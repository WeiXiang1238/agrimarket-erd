<?php

require_once __DIR__ . '/../Db_Connect.php';

/**
 * Staff Service
 * Contains all business logic for staff operations
 */
class StaffService
{
    private $db;
    
    public function __construct()
    {
        global $host, $user, $pass, $dbname;
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get paginated staff with filters
     */
    public function getPaginatedStaff($page = 1, $limit = 10, $filters = [])
    {
        $db = $this->db;
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $whereConditions = ['s.is_archive = 0'];
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(u.name LIKE ? OR s.employee_id LIKE ? OR s.department LIKE ? OR s.position LIKE ? OR u.email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['department'])) {
            $whereConditions[] = "s.department = ?";
            $params[] = $filters['department'];
        }
        
        if (!empty($filters['status'])) {
            $whereConditions[] = "s.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['position'])) {
            $whereConditions[] = "s.position = ?";
            $params[] = $filters['position'];
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countStmt = $db->prepare("
            SELECT COUNT(*) as total
            FROM staff s
            JOIN users u ON s.user_id = u.user_id
            WHERE $whereClause
        ");
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get staff data
        $stmt = $db->prepare("
            SELECT 
                s.staff_id,
                s.employee_id,
                s.department,
                s.position,
                s.hire_date,
                s.salary,
                s.phone,
                s.performance_rating,
                s.status,
                s.created_at,
                u.user_id,
                u.name,
                u.email,
                u.role
            FROM staff s
            JOIN users u ON s.user_id = u.user_id
            WHERE $whereClause
            ORDER BY s.created_at DESC
            LIMIT $limit OFFSET $offset
        ");
        
        $stmt->execute($params);
        $staff = $stmt->fetchAll();
        
        return [
            'success' => true,
            'staff' => $staff,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total_records' => $total,
                'limit' => $limit
            ]
        ];
    }
    
    /**
     * Create new staff member
     */
    public function createStaff($staffData)
    {
        $db = $this->db;
        
        try {
            $db->beginTransaction();
            
            // Validate required fields
            $requiredFields = ['name', 'email', 'department', 'position', 'hire_date'];
            foreach ($requiredFields as $field) {
                if (empty($staffData[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }
            
            // Check if email already exists in users table
            if ($this->emailExists($staffData['email'])) {
                throw new Exception("Email already exists");
            }
            
            // Create user first
            $userData = [
                'name' => $staffData['name'],
                'email' => $staffData['email'],
                'phone' => $staffData['phone'] ?? null,
                'role' => 'staff'
            ];
            
            $userInfo = $this->createUser($userData);
            if (!$userInfo['user_id']) {
                throw new Exception("Failed to create user account");
            }
            
            // Auto-generate employee ID
            $employeeId = $this->generateEmployeeId();
            
            // Insert staff record
            $stmt = $db->prepare("
                INSERT INTO staff (
                    user_id, employee_id, department, position, hire_date, 
                    salary, phone, emergency_contact, address, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userInfo['user_id'],
                $employeeId,
                $staffData['department'],
                $staffData['position'],
                $staffData['hire_date'],
                $staffData['salary'] ?? null,
                $staffData['phone'] ?? null,
                $staffData['emergency_contact'] ?? null,
                $staffData['address'] ?? null,
                $staffData['status'] ?? 'active'
            ]);
            
            $staffId = $db->lastInsertId();
            
            $db->commit();
            
            return ['success' => true, 'staff_id' => $staffId, 'employee_id' => $employeeId, 'message' => 'Staff member created successfully', 'password' => $userInfo['password']];
            
        } catch (Exception $e) {
            $db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Update staff member
     */
    public function updateStaff($staffData)
    {
        $db = $this->db;
        
        try {
            $db->beginTransaction();
            
            // Validate staff ID
            if (empty($staffData['staff_id'])) {
                throw new Exception("Staff ID is required");
            }

            // Get existing staff data to find the user_id
            $existingStaff = $this->getStaffById($staffData['staff_id']);
            if (!$existingStaff) {
                throw new Exception("Staff member not found");
            }
            $userId = $existingStaff['user_id'];

            // 1. Update the users table
            $userStmt = $db->prepare("
                UPDATE users SET
                    name = ?,
                    email = ?,
                    phone = ?,
                    updated_at = NOW()
                WHERE user_id = ?
            ");
            $userStmt->execute([
                $staffData['name'],
                $staffData['email'],
                $staffData['phone'] ?? null,
                $userId
            ]);

            // 2. Update the staff table
            $staffStmt = $db->prepare("
                UPDATE staff SET
                    department = ?,
                    position = ?,
                    hire_date = ?,
                    salary = ?,
                    phone = ?,
                    emergency_contact = ?,
                    address = ?,
                    status = ?,
                    performance_rating = ?,
                    updated_at = NOW()
                WHERE staff_id = ?
            ");
            
            $staffStmt->execute([
                $staffData['department'],
                $staffData['position'],
                $staffData['hire_date'],
                $staffData['salary'] ?? null,
                $staffData['phone'] ?? null,
                $staffData['emergency_contact'] ?? null,
                $staffData['address'] ?? null,
                $staffData['status'] ?? 'active',
                $staffData['performance_rating'] ?? null,
                $staffData['staff_id']
            ]);
            
            $db->commit();
            return ['success' => true, 'message' => 'Staff member updated successfully'];
            
        } catch (Exception $e) {
            $db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get staff details
     */
    public function getStaffDetails($staffId)
    {
        $db = $this->db;
        
        $stmt = $db->prepare("
            SELECT 
                s.*,
                u.name,
                u.email,
                u.role
            FROM staff s
            JOIN users u ON s.user_id = u.user_id
            WHERE s.staff_id = ? AND s.is_archive = 0
        ");
        $stmt->execute([$staffId]);
        
        return $stmt->fetch();
    }
    
    /**
     * Delete staff member
     */
    public function deleteStaff($staffId)
    {
        $db = $this->db;
        
        try {
            // Check if staff exists (including archived ones)
            $stmt = $db->prepare("
                SELECT s.staff_id, u.name
                FROM staff s
                JOIN users u ON s.user_id = u.user_id
                WHERE s.staff_id = ?
            ");
            $stmt->execute([$staffId]);
            $existingStaff = $stmt->fetch();
            
            if (!$existingStaff) {
                throw new Exception("Staff member not found");
            }
            
            // Check if already archived
            $stmt = $db->prepare("SELECT is_archive FROM staff WHERE staff_id = ?");
            $stmt->execute([$staffId]);
            $staffRow = $stmt->fetch();
            
            if ($staffRow && $staffRow['is_archive']) {
                throw new Exception("Staff member has already been deleted");
            }
            
            // Soft delete by setting is_archive = 1
            $stmt = $db->prepare("UPDATE staff SET is_archive = 1, updated_at = NOW() WHERE staff_id = ?");
            $stmt->execute([$staffId]);
            
            return ['success' => true, 'message' => 'Staff member deleted successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Toggle staff status
     */
    public function toggleStaffStatus($staffId, $status, $currentUserId)
    {
        $db = $this->db;
        
        try {
            $validStatuses = ['active', 'inactive', 'terminated'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid status");
            }
            
            $stmt = $db->prepare("UPDATE staff SET status = ? WHERE staff_id = ?");
            $stmt->execute([$status, $staffId]);
            
            // Log the action
            $this->logStaffAction($staffId, 'status_change', $currentUserId, ['status' => $status]);
            
            return ['success' => true, 'message' => 'Staff status updated successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get staff statistics
     */
    public function getStaffStatistics()
    {
        $db = $this->db;
        
        try {
            // Total staff
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM staff WHERE is_archive = 0");
            $stmt->execute();
            $totalStaff = $stmt->fetch()['total'];
            
            // Staff by status
            $stmt = $db->prepare("
                SELECT status, COUNT(*) as count 
                FROM staff 
                WHERE is_archive = 0 
                GROUP BY status
            ");
            $stmt->execute();
            $statusStats = $stmt->fetchAll();
            
            // Staff by department
            $stmt = $db->prepare("
                SELECT department, COUNT(*) as count 
                FROM staff 
                WHERE is_archive = 0 
                GROUP BY department
            ");
            $stmt->execute();
            $departmentStats = $stmt->fetchAll();
            
            // Average salary
            $stmt = $db->prepare("
                SELECT AVG(salary) as avg_salary 
                FROM staff 
                WHERE is_archive = 0 AND salary IS NOT NULL
            ");
            $stmt->execute();
            $avgSalary = $stmt->fetch()['avg_salary'];
            
            // Recent hires (last 30 days)
            $stmt = $db->prepare("
                SELECT COUNT(*) as recent_hires 
                FROM staff 
                WHERE is_archive = 0 AND hire_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            $stmt->execute();
            $recentHires = $stmt->fetch()['recent_hires'];
            
            return [
                'total_staff' => $totalStaff,
                'status_stats' => $statusStats,
                'department_stats' => $departmentStats,
                'avg_salary' => round($avgSalary ?? 0, 2),
                'recent_hires' => $recentHires
            ];
            
        } catch (Exception $e) {
            return [
                'total_staff' => 0,
                'status_stats' => [],
                'department_stats' => [],
                'avg_salary' => 0,
                'recent_hires' => 0
            ];
        }
    }
    
    /**
     * Get available departments
     */
    public function getDepartments()
    {
        // Return predefined departments instead of querying the database
        return [
            'IT',
            'HR',
            'Finance',
            'Marketing',
            'Sales',
            'Operations',
            'Customer Service',
            'Product Management',
            'Quality Assurance',
            'Research & Development',
            'Legal',
            'Administration'
        ];
    }
    
    /**
     * Get available positions
     */
    public function getPositions()
    {
        // Return predefined positions instead of querying the database
        return [
            'Manager',
            'Senior Manager',
            'Director',
            'VP',
            'CEO',
            'CTO',
            'CFO',
            'Developer',
            'Senior Developer',
            'Lead Developer',
            'Architect',
            'Analyst',
            'Senior Analyst',
            'Specialist',
            'Coordinator',
            'Assistant',
            'Executive',
            'Consultant',
            'Intern',
            'Trainee'
        ];
    }
    
    /**
     * Get available users for staff creation
     */
    public function getAvailableUsers()
    {
        $db = $this->db;
        
        $stmt = $db->prepare("
            SELECT u.user_id, u.name, u.email, u.role
            FROM users u
            LEFT JOIN staff s ON u.user_id = s.user_id
            WHERE u.is_archive = 0 
            AND s.staff_id IS NULL
            AND u.role != 'admin'
            ORDER BY u.name ASC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get available managers
     */
    public function getAvailableManagers()
    {
        $db = $this->db;
        
        $stmt = $db->prepare("
            SELECT s.staff_id, u.name, s.department, s.position
            FROM staff s
            JOIN users u ON s.user_id = u.user_id
            WHERE s.is_archive = 0 
            AND s.status = 'active'
            ORDER BY u.name ASC
        ");
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Check if employee ID exists
     */
    private function employeeIdExists($employeeId)
    {
        $stmt = $this->db->prepare("SELECT staff_id FROM staff WHERE employee_id = ? AND is_archive = 0");
        $stmt->execute([$employeeId]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Check if user is already a staff member
     */
    private function userIsStaff($userId)
    {
        $stmt = $this->db->prepare("SELECT staff_id FROM staff WHERE user_id = ? AND is_archive = 0");
        $stmt->execute([$userId]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Update user role
     */
    private function updateUserRole($userId, $role)
    {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE user_id = ?");
        $stmt->execute([$role, $userId]);
    }
    
    /**
     * Log staff actions
     */
    private function logStaffAction($staffId, $action, $currentUserId, $details = [])
    {
        // This would typically log to an audit log table
        // For now, we'll just return true
        return true;
    }
    
    /**
     * Generate next employee ID
     */
    public function generateEmployeeId()
    {
        $db = $this->db;
        
        // Get the last employee ID
        $stmt = $db->prepare("
            SELECT employee_id 
            FROM staff 
            WHERE employee_id LIKE 'EMP%' 
            ORDER BY CAST(SUBSTRING(employee_id, 4) AS UNSIGNED) DESC 
            LIMIT 1
        ");
        $stmt->execute();
        $lastEmployee = $stmt->fetch();
        
        if ($lastEmployee) {
            // Extract the number part and increment
            $lastNumber = intval(substr($lastEmployee['employee_id'], 3));
            $nextNumber = $lastNumber + 1;
        } else {
            // Start with 1 if no existing employees
            $nextNumber = 1;
        }
        
        // Format as EMP0001, EMP0002, etc.
        return 'EMP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Check if email exists in users table
     */
    private function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ? AND is_archive = 0");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Create a new user
     */
    private function createUser($userData)
    {
        try {
            // Generate a random password
            $password = bin2hex(random_bytes(8)); // 16 character random password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, phone, password, role, is_active) 
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                $userData['name'],
                $userData['email'],
                $userData['phone'] ?? null,
                $hashedPassword,
                $userData['role']
            ]);
            
            $userId = $this->db->lastInsertId();
            
            // Assign role if needed
            if ($userData['role'] === 'staff') {
                $this->assignStaffRole($userId);
            }
            
            return ['user_id' => $userId, 'password' => $password];
        } catch (Exception $e) {
            // Re-throw the exception with a more informative message
            throw new Exception("Database error while creating user: " . $e->getMessage());
        }
    }
    
    /**
     * Assign staff role to user
     */
    private function assignStaffRole($userId)
    {
        try {
            // Get staff role ID
            $stmt = $this->db->prepare("SELECT role_id FROM roles WHERE role_name = 'staff' AND is_active = 1");
            $stmt->execute();
            $role = $stmt->fetch();
            
            if ($role) {
                $stmt = $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id, is_active) 
                    VALUES (?, ?, 1)
                ");
                $stmt->execute([$userId, $role['role_id']]);
            }
        } catch (Exception $e) {
            // Ignore role assignment errors
        }
    }
    
    /**
     * Get staff by ID
     */
    public function getStaffById($staffId)
    {
        $db = $this->db;
        
        $stmt = $db->prepare("
            SELECT 
                s.staff_id,
                s.employee_id,
                s.department,
                s.position,
                s.hire_date,
                s.salary,
                s.phone,
                s.emergency_contact,
                s.address,
                s.performance_rating,
                s.status,
                s.created_at,
                u.user_id,
                u.name,
                u.email,
                u.role
            FROM staff s
            JOIN users u ON s.user_id = u.user_id
            WHERE s.staff_id = ? AND s.is_archive = 0
        ");
        
        $stmt->execute([$staffId]);
        return $stmt->fetch();
    }
    
    /**
     * Assign task to staff member
     */
    public function assignTask($taskData)
    {
        $db = $this->db;
        
        try {
            // Validate required fields
            if (empty($taskData['staff_id']) || empty($taskData['task_title'])) {
                throw new Exception("Staff ID and task title are required");
            }
            
            // Check if staff exists
            $staff = $this->getStaffById($taskData['staff_id']);
            if (!$staff) {
                throw new Exception("Staff member not found");
            }
            
            $stmt = $db->prepare("
                INSERT INTO staff_tasks (
                    staff_id, task_title, task_description, priority, 
                    status, due_date, assigned_date
                ) VALUES (?, ?, ?, ?, 'pending', ?, NOW())
            ");
            
            $stmt->execute([
                $taskData['staff_id'],
                $taskData['task_title'],
                $taskData['task_description'] ?? null,
                $taskData['priority'] ?? 'medium',
                $taskData['due_date'] ?? null
            ]);
            
            $taskId = $db->lastInsertId();
            
            return ['success' => true, 'task_id' => $taskId, 'message' => 'Task assigned successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get staff tasks
     */
    public function getStaffTasks($staffId)
    {
        $db = $this->db;
        
        $stmt = $db->prepare("
            SELECT 
                task_id,
                task_title,
                task_description,
                priority,
                status,
                due_date,
                assigned_date,
                completed_date
            FROM staff_tasks
            WHERE staff_id = ? AND is_archive = 0
            ORDER BY assigned_date DESC
        ");
        
        $stmt->execute([$staffId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get staff performance data
     */
    public function getStaffPerformance($staffId)
    {
        $db = $this->db;
        
        try {
            // Get tasks completed count
            $stmt = $db->prepare("
                SELECT COUNT(*) as tasks_completed
                FROM staff_tasks
                WHERE staff_id = ? AND status = 'completed' AND is_archive = 0
            ");
            $stmt->execute([$staffId]);
            $tasksCompleted = $stmt->fetch()['tasks_completed'];
            
            // Get last activity (last page visit or task completion)
            $stmt = $db->prepare("
                SELECT 
                    COALESCE(
                        (SELECT MAX(visit_date) FROM page_visits WHERE user_id = s.user_id),
                        (SELECT MAX(completed_date) FROM staff_tasks WHERE staff_id = ? AND completed_date IS NOT NULL),
                        s.created_at
                    ) as last_activity
                FROM staff s
                WHERE s.staff_id = ?
            ");
            $stmt->execute([$staffId, $staffId]);
            $lastActivity = $stmt->fetch()['last_activity'];
            
            // Get performance rating
            $stmt = $db->prepare("
                SELECT performance_rating
                FROM staff
                WHERE staff_id = ?
            ");
            $stmt->execute([$staffId]);
            $performanceRating = $stmt->fetch()['performance_rating'];
            
            return [
                'tasks_completed' => $tasksCompleted,
                'last_activity' => $lastActivity ? date('M j, Y g:i A', strtotime($lastActivity)) : null,
                'performance_rating' => $performanceRating
            ];
            
        } catch (Exception $e) {
            return [
                'tasks_completed' => 0,
                'last_activity' => null,
                'performance_rating' => null
            ];
        }
    }
}

?> 