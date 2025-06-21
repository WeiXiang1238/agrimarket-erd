<?php

require_once __DIR__ . '/../Db_Connect.php';

/**
 * Customer Service
 * Contains all business logic for customer operations
 * 
 * Note: Based on actual database structure where customers table only contains:
 * - customer_id, user_id, phone, is_archive
 * - All personal data is in users table
 * - Addresses are in customer_addresses table
 */
class CustomerService
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
     * Get paginated customers with filtering
     */
    public function getPaginatedCustomers($page = 1, $limit = 10, $filters = [])
    {
        $offset = ($page - 1) * $limit;
        
        // Build query conditions
        $whereConditions = ['c.is_archive = 0']; // Only non-archived customers
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(u.name LIKE ? OR u.email LIKE ? OR c.phone LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        
        if (isset($filters['status']) && $filters['status'] !== '') {
            $whereConditions[] = "u.is_active = ?";
            $params[] = $filters['status'];
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countQuery = "
            SELECT COUNT(*) as total 
            FROM customers c 
            JOIN users u ON c.user_id = u.user_id 
            WHERE $whereClause AND u.is_archive = 0
        ";
        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get customers with basic info from users table
        $query = "
            SELECT 
                c.customer_id, 
                c.user_id,
                c.phone as customer_phone,
                c.is_archive,
                u.name as full_name, 
                u.email, 
                u.phone as user_phone,
                u.is_active as user_active,
                u.created_at as user_created_at,
                -- Calculate order stats
                COALESCE(order_stats.total_orders, 0) as total_orders,
                COALESCE(order_stats.total_spent, 0.00) as total_spent,
                -- Get primary address
                COALESCE(addr.street_address, 'No address') as primary_address
            FROM customers c
            JOIN users u ON c.user_id = u.user_id
            LEFT JOIN (
                SELECT 
                    customer_id,
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_spent
                FROM orders 
                WHERE is_archive = 0
                GROUP BY customer_id
            ) order_stats ON c.customer_id = order_stats.customer_id
            LEFT JOIN (
                SELECT 
                    customer_id,
                    CONCAT(street_address, ', ', city, ', ', state) as street_address
                FROM customer_addresses 
                WHERE is_default = 1
                LIMIT 1
            ) addr ON c.customer_id = addr.customer_id
            WHERE $whereClause AND u.is_archive = 0
            ORDER BY u.created_at DESC
            LIMIT $limit OFFSET $offset
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $customers = $stmt->fetchAll();
        
        return [
            'success' => true,
            'customers' => $customers,
            'total' => $total,
            'page' => $page,
            'totalPages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Register new customer with user account creation
     */
    public function registerCustomer($customerData)
    {
        try {
            // Validate required fields
            if (empty($customerData['name']) || empty($customerData['email']) || empty($customerData['password'])) {
                return ['success' => false, 'message' => 'Name, email, and password are required'];
            }
            
            // Check if email already exists
            if ($this->emailExists($customerData['email'])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            $this->db->beginTransaction();
            
            // Create user account
            $userData = [
                'name' => trim($customerData['name']),
                'email' => trim($customerData['email']),
                'phone' => $customerData['phone'] ?? null,
                'password' => $customerData['password'],
                'role' => 'customer'
            ];
            
            $userId = $this->createUserAccount($userData);
            if (!$userId) {
                throw new Exception("Failed to create user account");
            }
            
            // Create customer profile
            $stmt = $this->db->prepare("
                INSERT INTO customers (user_id, phone) 
                VALUES (?, ?)
            ");
            $stmt->execute([
                $userId,
                $customerData['phone'] ?? null
            ]);
            
            $customerId = $this->db->lastInsertId();
            
            $this->db->commit();
            
            return [
                'success' => true,
                'customer_id' => $customerId,
                'user_id' => $userId,
                'message' => 'Customer account created successfully'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Check if email already exists
     */
    private function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Create new customer with automatic user creation
     */
    public function createCustomer($customerData)
    {
        try {
            // Validate required fields
            if (empty($customerData['name']) || empty($customerData['email'])) {
                return ['success' => false, 'message' => 'Name and email are required'];
            }
            
            $this->db->beginTransaction();
            
            // First create the user account
            $userData = [
                'name' => trim($customerData['name']),
                'email' => trim($customerData['email']),
                'phone' => $customerData['phone'] ?? null,
                'password' => $this->generateSecurePassword(),
                'role' => 'customer'
            ];
            
            $userId = $this->createUserAccount($userData);
            if (!$userId) {
                throw new Exception("Failed to create user account");
            }
            
            // Create customer profile (minimal - just link to user)
            $stmt = $this->db->prepare("
                INSERT INTO customers (user_id, phone) 
                VALUES (?, ?)
            ");
            $stmt->execute([
                $userId,
                $customerData['phone'] ?? null
            ]);
            
            $customerId = $this->db->lastInsertId();
            
            // Create default address if provided
            if (!empty($customerData['address'])) {
                $this->createCustomerAddress($customerId, [
                    'address_type' => 'both',
                    'first_name' => explode(' ', $userData['name'])[0] ?? '',
                    'last_name' => substr($userData['name'], strpos($userData['name'], ' ') + 1) ?: '',
                    'street_address' => $customerData['address'],
                    'city' => $customerData['city'] ?? 'Unknown',
                    'state' => $customerData['state'] ?? 'Unknown', 
                    'postal_code' => $customerData['postal_code'] ?? '00000',
                    'country' => 'Malaysia',
                    'phone' => $customerData['phone'],
                    'is_default' => 1
                ]);
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'customer_id' => $customerId,
                'user_id' => $userId,
                'temp_password' => $userData['password'],
                'message' => 'Customer created successfully with user account'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to create customer: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update customer (only phone can be updated in customers table)
     */
    public function updateCustomer($customerId, $customerData)
    {
        try {
            $this->db->beginTransaction();
            
            // Update customer phone if provided
            if (isset($customerData['phone'])) {
                $stmt = $this->db->prepare("
                    UPDATE customers 
                    SET phone = ? 
                    WHERE customer_id = ? AND is_archive = 0
                ");
                $stmt->execute([$customerData['phone'], $customerId]);
            }
            
            // Update user data if provided
            if (isset($customerData['name']) || isset($customerData['email'])) {
                $userUpdateFields = [];
                $userParams = [];
                
                if (isset($customerData['name'])) {
                    $userUpdateFields[] = "name = ?";
                    $userParams[] = trim($customerData['name']);
                }
                if (isset($customerData['email'])) {
                    $userUpdateFields[] = "email = ?";
                    $userParams[] = trim($customerData['email']);
                }
                
                if (!empty($userUpdateFields)) {
                    $userParams[] = $customerId;
                    $stmt = $this->db->prepare("
                        UPDATE users u
                        JOIN customers c ON u.user_id = c.user_id
                        SET " . implode(', ', $userUpdateFields) . "
                        WHERE c.customer_id = ? AND c.is_archive = 0
                    ");
                    $stmt->execute($userParams);
                }
            }
            
            $this->db->commit();
            return ['success' => true, 'message' => 'Customer updated successfully'];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to update customer: ' . $e->getMessage()];
        }
    }
    
    /**
     * Toggle customer active status in users table
     */
    public function toggleCustomerStatus($customerId, $isActive, $adminId = null)
    {
        try {
            $this->db->beginTransaction();
            
            // Update is_active in users table for the customer
            $stmt = $this->db->prepare("
                UPDATE users u
                JOIN customers c ON u.user_id = c.user_id
                SET u.is_active = ? 
                WHERE c.customer_id = ?
            ");
            $result = $stmt->execute([$isActive, $customerId]);
            
            $this->db->commit();
            
            return [
                'success' => $result,
                'message' => $result ? 
                    ($isActive ? 'Customer activated successfully' : 'Customer deactivated successfully') :
                    'Failed to update customer status'
            ];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to update customer status: ' . $e->getMessage()];
        }
    }

    /**
     * Delete customer (soft delete)
     */
    public function deleteCustomer($customerId, $adminId = null)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE customers 
                SET is_archive = 1 
                WHERE customer_id = ?
            ");
            $stmt->execute([$customerId]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Customer deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Customer not found'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to delete customer: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get customer statistics
     */
    public function getCustomerStatistics()
    {
        try {
            $stats = [];
            
            // Total customers
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM customers c 
                JOIN users u ON c.user_id = u.user_id 
                WHERE c.is_archive = 0 AND u.is_archive = 0
            ");
            $stmt->execute();
            $stats['total_customers'] = $stmt->fetch()['total'];
            
            // Customers with orders
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT c.customer_id) as active 
                FROM customers c 
                JOIN orders o ON c.customer_id = o.customer_id 
                WHERE c.is_archive = 0 AND o.is_archive = 0
            ");
            $stmt->execute();
            $stats['active_customers'] = $stmt->fetch()['active'];
            
            // Total revenue from customers
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(o.total_amount), 0) as revenue 
                FROM customers c 
                JOIN orders o ON c.customer_id = o.customer_id 
                WHERE c.is_archive = 0 AND o.is_archive = 0
            ");
            $stmt->execute();
            $stats['total_revenue'] = $stmt->fetch()['revenue'];
            
            // Recent customers (last 30 days)
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as recent 
                FROM customers c 
                JOIN users u ON c.user_id = u.user_id 
                WHERE c.is_archive = 0 AND u.is_archive = 0 
                AND u.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ");
            $stmt->execute();
            $stats['recent_customers'] = $stmt->fetch()['recent'];
            
            return $stats;
            
        } catch (Exception $e) {
            return [
                'total_customers' => 0,
                'active_customers' => 0,
                'total_revenue' => 0,
                'recent_customers' => 0
            ];
        }
    }
    
    /**
     * Get customer details
     */
    public function getCustomerDetails($customerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.customer_id, 
                    c.user_id,
                    c.phone as customer_phone,
                    c.is_archive,
                    u.name, 
                    u.email, 
                    u.phone as user_phone,
                    u.is_active,
                    u.created_at
                FROM customers c
                JOIN users u ON c.user_id = u.user_id
                WHERE c.customer_id = ? AND c.is_archive = 0
            ");
            $stmt->execute([$customerId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Create customer address
     */
    private function createCustomerAddress($customerId, $addressData)
    {
        $stmt = $this->db->prepare("
            INSERT INTO customer_addresses (
                customer_id, address_type, first_name, last_name, 
                street_address, city, state, postal_code, country, 
                phone, is_default
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");
        
        return $stmt->execute([
            $customerId,
            $addressData['address_type'],
            $addressData['first_name'],
            $addressData['last_name'],
            $addressData['street_address'],
            $addressData['city'],
            $addressData['state'],
            $addressData['postal_code'],
            $addressData['country'],
            $addressData['phone'],
        ]);
    }
    
    /**
     * Create user account
     */
    private function createUserAccount($userData)
    {
        try {
            // Check if email already exists
            $checkStmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ? AND is_archive = 0");
            $checkStmt->execute([$userData['email']]);
            if ($checkStmt->rowCount() > 0) {
                throw new Exception("Email already exists");
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, phone, password, role, is_active, is_archive)
                VALUES (?, ?, ?, ?, ?, 1, 0)
            ");
            $stmt->execute([
                $userData['name'],
                $userData['email'],
                $userData['phone'],
                password_hash($userData['password'], PASSWORD_DEFAULT),
                $userData['role']
            ]);
            
            $userId = $this->db->lastInsertId();
            
            // Assign role in user_roles table
            $this->assignUserRole($userId, $userData['role']);
            
            return $userId;
        } catch (Exception $e) {
            throw new Exception("Failed to create user: " . $e->getMessage());
        }
    }

    /**
     * Assign role to user in user_roles table
     */
    private function assignUserRole($userId, $roleName)
    {
        try {
            // Get role ID
            $stmt = $this->db->prepare("SELECT role_id FROM roles WHERE role_name = ? AND is_active = 1");
            $stmt->execute([$roleName]);
            $role = $stmt->fetch();
            
            if (!$role) {
                throw new Exception("Role '$roleName' not found");
            }
            
            // Assign role to user
            $stmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id, is_active) 
                VALUES (?, ?, 1)
            ");
            $stmt->execute([$userId, $role['role_id']]);
            
        } catch (Exception $e) {
            throw new Exception("Failed to assign role: " . $e->getMessage());
        }
    }
    
    /**
     * Generate secure temporary password
     */
    private function generateSecurePassword($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
} 