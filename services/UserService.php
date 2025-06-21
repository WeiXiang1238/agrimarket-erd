<?php

require_once __DIR__ . '/../Db_Connect.php';

/**
 * User Service
 * Contains all business logic for user operations
 */
class UserService
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
     * Authenticate user login
     */
    public function authenticate($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Remove password from returned data for security
            unset($user['password']);
            
            // Update last login
            $this->updateLastLogin($user['user_id']);
            
            return ['success' => true, 'user' => $user];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
    
    /**
     * Register new user
     */
    public function register($userData)
    {
        $db = $this->userModel->getDb();
        
        try {
            $db->beginTransaction();
            
            // Check if email already exists
            if ($this->emailExists($userData['email'])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            // Hash password
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Create user
            $stmt = $db->prepare("
                INSERT INTO users (name, email, phone, password, role, is_active) 
                VALUES (?, ?, ?, ?, ?, 1)
            ");
            $stmt->execute([
                $userData['name'],
                $userData['email'],
                $userData['phone'] ?? null,
                $userData['password'],
                $userData['role']
            ]);
            
            $userId = $db->lastInsertId();
            
            // Assign role
            $roleId = $this->getRoleIdByName($userData['role']);
            if ($roleId) {
                $this->assignRole($userId, $roleId);
            }
            
            $db->commit();
            
            return ['success' => true, 'user_id' => $userId, 'message' => 'User registered successfully'];
            
        } catch (Exception $e) {
            $db->rollback();
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword)
    {
        $db = $this->userModel->getDb();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }
    
    /**
     * Get users by role
     */
    public function getUsersByRole($role)
    {
        $db = $this->userModel->getDb();
        $stmt = $db->prepare("
            SELECT user_id, name, email, role, created_at 
            FROM users 
            WHERE role = ? AND is_archive = 0
            ORDER BY name ASC
        ");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $profileData)
    {
        $db = $this->userModel->getDb();
        
        // Remove sensitive fields
        unset($profileData['password'], $profileData['user_id']);
        
        $fields = [];
        $values = [];
        
        foreach ($profileData as $field => $value) {
            $fields[] = "$field = ?";
            $values[] = $value;
        }
        
        $values[] = $userId;
        
        $stmt = $db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = ?");
        return $stmt->execute($values);
    }
    
    /**
     * Get user with roles and permissions
     */
    public function getUserWithPermissions($userId)
    {
        $db = $this->userModel->getDb();
        
        // Get user data
        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ? AND is_archive = 0");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) return null;
        
        // Remove password for security
        unset($user['password']);
        
        // Get user roles
        $stmt = $db->prepare("
            SELECT r.role_name, r.description 
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.role_id
            WHERE ur.user_id = ? AND ur.is_active = 1
        ");
        $stmt->execute([$userId]);
        $user['roles'] = $stmt->fetchAll();
        
        // Get user permissions
        $stmt = $db->prepare("
            SELECT DISTINCT p.permission_name, p.module, p.description
            FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.permission_id
            WHERE ur.user_id = ? AND ur.is_active = 1 AND p.is_active = 1
        ");
        $stmt->execute([$userId]);
        $user['permissions'] = $stmt->fetchAll();
        
        return $user;
    }
    
    /**
     * Check if user has permission
     */
    public function hasPermission($userId, $permissionName)
    {
        $db = $this->userModel->getDb();
        
        $stmt = $db->prepare("
            SELECT COUNT(*) 
            FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.permission_id
            WHERE ur.user_id = ? AND p.permission_name = ? 
            AND ur.is_active = 1 AND p.is_active = 1
        ");
        $stmt->execute([$userId, $permissionName]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Assign role to user
     */
    public function assignRole($userId, $roleId)
    {
        $db = $this->userModel->getDb();
        
        // Check if role assignment already exists
        $stmt = $db->prepare("SELECT user_role_id FROM user_roles WHERE user_id = ? AND role_id = ?");
        $stmt->execute([$userId, $roleId]);
        
        if ($stmt->rowCount() > 0) {
            // Update existing assignment to active
            $stmt = $db->prepare("UPDATE user_roles SET is_active = 1 WHERE user_id = ? AND role_id = ?");
            return $stmt->execute([$userId, $roleId]);
        } else {
            // Create new role assignment
            $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id, is_active) VALUES (?, ?, 1)");
            return $stmt->execute([$userId, $roleId]);
        }
    }
    
    /**
     * Remove role from user
     */
    public function removeRole($userId, $roleId)
    {
        $db = $this->userModel->getDb();
        $stmt = $db->prepare("UPDATE user_roles SET is_active = 0 WHERE user_id = ? AND role_id = ?");
        return $stmt->execute([$userId, $roleId]);
    }
    
    /**
     * Get role ID by name
     */
    private function getRoleIdByName($roleName)
    {
        $db = $this->userModel->getDb();
        $stmt = $db->prepare("SELECT role_id FROM roles WHERE role_name = ? AND is_active = 1");
        $stmt->execute([$roleName]);
        $result = $stmt->fetch();
        return $result ? $result['role_id'] : null;
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId)
    {
        $stmt = $this->db->prepare("UPDATE users SET last_login_at = NOW() WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
    
    /**
     * Search users
     */
    public function searchUsers($searchTerm, $limit = 20)
    {
        $db = $this->userModel->getDb();
        $searchTerm = "%{$searchTerm}%";
        
        $stmt = $db->prepare("
            SELECT user_id, name, email, role, created_at 
            FROM users 
            WHERE (name LIKE ? OR email LIKE ?) AND is_archive = 0
            ORDER BY name ASC
            LIMIT ?
        ");
        $stmt->execute([$searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get paginated users with filtering
     */
    public function getPaginatedUsers($page = 1, $limit = 10, $filters = [])
    {
        $offset = ($page - 1) * $limit;
        
        // Build query conditions
        $whereConditions = ['1=1']; // Always true condition
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        
        if (!empty($filters['role'])) {
            $whereConditions[] = "u.role = ?";
            $params[] = $filters['role'];
        }
        
        if (isset($filters['status']) && $filters['status'] !== '') {
            $whereConditions[] = "u.is_active = ?";
            $params[] = $filters['status'];
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM users u WHERE $whereClause";
        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get users
        $query = "
            SELECT u.user_id, u.name, u.email, u.role, u.is_active, u.created_at, u.last_login_at
            FROM users u
            WHERE $whereClause
            ORDER BY u.created_at DESC
            LIMIT $limit OFFSET $offset
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $users = $stmt->fetchAll();
        
        return [
            'success' => true,
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'totalPages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Create new user
     */
    public function createUser($userData)
    {
        try {
            // Comprehensive validation
            $validation = $this->validateUserData($userData, false);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Check if email exists
            if ($this->emailExists($userData['email'])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            $this->db->beginTransaction();
            
            // Create user
            $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, role, is_active, created_at) 
                VALUES (?, ?, ?, ?, 1, NOW())
            ");
            $stmt->execute([
                trim($userData['name']),
                trim($userData['email']),
                $hashedPassword,
                $userData['role'] ?? 'customer'
            ]);
            
            $userId = $this->db->lastInsertId();
            
            $this->db->commit();
            return ['success' => true, 'message' => 'User created successfully', 'user_id' => $userId];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Failed to create user: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update existing user
     */
    public function updateUser($userId, $userData)
    {
        try {
            // Comprehensive validation
            $validation = $this->validateUserData($userData, true);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Check if email exists (excluding current user)
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
            $stmt->execute([trim($userData['email']), $userId]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            $this->db->beginTransaction();
            
            // Update user
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE user_id = ?");
            $stmt->execute([
                trim($userData['name']),
                trim($userData['email']),
                $userData['role'] ?? 'customer',
                $userId
            ]);
            
            $this->db->commit();
            return ['success' => true, 'message' => 'User updated successfully'];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Failed to update user: ' . $e->getMessage()];
        }
    }
    
    /**
     * Toggle user active status
     */
    public function toggleUserStatus($userId, $status, $currentUserId)
    {
        if ($userId == $currentUserId) {
            return ['success' => false, 'message' => 'Cannot change your own account status'];
        }
        
        try {
            $stmt = $this->db->prepare("UPDATE users SET is_active = ? WHERE user_id = ?");
            $result = $stmt->execute([$status, $userId]);
            
            return [
                'success' => $result,
                'message' => $result ? 'User status updated successfully' : 'Failed to update user status'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update user status: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete user (hard delete for now)
     */
    public function deleteUser($userId, $currentUserId)
    {
        if ($userId == $currentUserId) {
            return ['success' => false, 'message' => 'Cannot delete your own account'];
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            $result = $stmt->execute([$userId]);
            
            return [
                'success' => $result,
                'message' => $result ? 'User deleted successfully' : 'Failed to delete user'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to delete user: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get user statistics
     */
    public function getUserStatistics()
    {
        try {
            $query = "
                SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_users,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_users,
                    SUM(CASE WHEN role = 'vendor' THEN 1 ELSE 0 END) as vendor_users,
                    SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) as staff_users,
                    SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) as customer_users
                FROM users 
                WHERE 1=1
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return [
                'total_users' => 0,
                'active_users' => 0,
                'inactive_users' => 0,
                'admin_users' => 0,
                'vendor_users' => 0,
                'staff_users' => 0,
                'customer_users' => 0
            ];
        }
    }
    
    /**
     * Get available roles
     */
    public function getAvailableRoles()
    {
        return [
            ['role_name' => 'admin', 'description' => 'Administrator'],
            ['role_name' => 'vendor', 'description' => 'Vendor'],
            ['role_name' => 'staff', 'description' => 'Staff Member'],
            ['role_name' => 'customer', 'description' => 'Customer']
        ];
    }
    
    /**
     * Comprehensive user data validation
     */
    private function validateUserData($userData, $isUpdate = false)
    {
        $errors = [];
        
        // Validate Name
        $name = trim($userData['name'] ?? '');
        if (empty($name)) {
            $errors[] = 'Name is required';
        } elseif (strlen($name) < 2) {
            $errors[] = 'Name must be at least 2 characters long';
        } elseif (strlen($name) > 100) {
            $errors[] = 'Name must be less than 100 characters';
        } elseif (!preg_match("/^[a-zA-Z\s\-\.']+$/", $name)) {
            $errors[] = 'Name contains invalid characters (only letters, spaces, hyphens, periods, and apostrophes allowed)';
        }
        
        // Validate Email
        $email = trim($userData['email'] ?? '');
        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
        } elseif (strlen($email) > 100) {
            $errors[] = 'Email must be less than 100 characters';
        }
        
        // Validate Password (required for new users)
        if (!$isUpdate) {
            $password = $userData['password'] ?? '';
            if (empty($password)) {
                $errors[] = 'Password is required';
            } elseif (strlen($password) < 8) {
                $errors[] = 'Password must be at least 8 characters long';
            } elseif (strlen($password) > 255) {
                $errors[] = 'Password must be less than 255 characters';
            } elseif (!preg_match('/(?=.*[a-z])/', $password)) {
                $errors[] = 'Password must contain at least one lowercase letter';
            } elseif (!preg_match('/(?=.*[A-Z])/', $password)) {
                $errors[] = 'Password must contain at least one uppercase letter';
            } elseif (!preg_match('/(?=.*\d)/', $password)) {
                $errors[] = 'Password must contain at least one number';
            } elseif (!preg_match('/(?=.*[@$!%*?&])/', $password)) {
                $errors[] = 'Password must contain at least one special character (@$!%*?&)';
            }
        }
        
        // Validate Role
        $role = $userData['role'] ?? '';
        if (empty($role)) {
            $errors[] = 'User role is required';
        } else {
            $validRoles = ['admin', 'vendor', 'staff', 'customer'];
            if (!in_array($role, $validRoles)) {
                $errors[] = 'Invalid user role selected';
            }
        }
        
        return [
            'valid' => empty($errors),
            'message' => empty($errors) ? 'Validation passed' : implode('. ', $errors)
        ];
    }
} 