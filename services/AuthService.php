<?php

require_once __DIR__ . '/../models/ModelLoader.php';

/**
 * Authentication Service
 * Handles user authentication, authorization, and session management
 */
class AuthService
{
    private $userModel;
    private $roleModel;
    private $userRoleModel;
    private $permissionService;
    
    public function __construct()
    {
        $this->userModel = ModelLoader::load('User');
        $this->roleModel = ModelLoader::load('Role');
        $this->userRoleModel = ModelLoader::load('UserRole');
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Authenticate user login
     */
    public function login($email, $password, $rememberMe = false)
    {
        try {
            $db = $this->userModel->getDb();
            
            // Get user with active status
            $stmt = $db->prepare("
                SELECT u.*, 
                       GROUP_CONCAT(r.role_name) as roles
                FROM users u
                LEFT JOIN user_roles ur ON u.user_id = ur.user_id AND ur.is_active = 1
                LEFT JOIN roles r ON ur.role_id = r.role_id AND r.is_active = 1
                WHERE u.email = ? AND u.is_active = 1 AND u.is_archive = 0
                GROUP BY u.user_id
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }
            error_log('user password: ' . print_r($user['password'], true));
            error_log('user password: ' . print_r($password, true));
            error_log('Password verification result: ' . var_export(password_verify($password, $user['password']), true));
            
            // Verify password
            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }
            
            // Check if user has any active roles
            if (empty($user['roles'])) {
                return [
                    'success' => false,
                    'message' => 'Your account does not have sufficient permissions'
                ];
            }
            
            // Create session
            $this->createSession($user);
            
            // Update last login
            $this->updateLastLogin($user['user_id']);
            
            // Handle remember me
            if ($rememberMe) {
                $this->setRememberMeToken($user['user_id']);
            }
            
            // Remove sensitive data
            unset($user['password'], $user['remember_token']);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'redirect' => $this->getRedirectUrl($user['roles'])
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Logout user
     */
    public function logout()
    {
        try {
            // Clear remember me token if exists
            if (isset($_SESSION['user_id'])) {
                $this->clearRememberMeToken($_SESSION['user_id']);
            }
            
            // Clear remember me cookie
            if (isset($_COOKIE['remember_token'])) {
                setcookie('remember_token', '', time() - 3600, '/');
                setcookie('remember_user', '', time() - 3600, '/');
            }
            
            // Destroy session
            session_destroy();
            
            return [
                'success' => true,
                'message' => 'Logged out successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated()
    {
        // Check session
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_email'])) {
            return true;
        }
        
        // Check remember me token
        if (isset($_COOKIE['remember_token']) && isset($_COOKIE['remember_user'])) {
            return $this->validateRememberMeToken($_COOKIE['remember_user'], $_COOKIE['remember_token']);
        }
        
        return false;
    }
    
    /**
     * Get current authenticated user
     */
    public function getCurrentUser()
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return null;
        }
        
        $db = $this->userModel->getDb();
        $stmt = $db->prepare("
            SELECT u.user_id, u.name, u.email, u.role, u.profile_picture, u.last_login_at, u.created_at,
                   GROUP_CONCAT(r.role_name) as roles,
                   GROUP_CONCAT(DISTINCT p.permission_name) as permissions
            FROM users u
            LEFT JOIN user_roles ur ON u.user_id = ur.user_id AND ur.is_active = 1
            LEFT JOIN roles r ON ur.role_id = r.role_id AND r.is_active = 1
            LEFT JOIN role_permissions rp ON r.role_id = rp.role_id
            LEFT JOIN permissions p ON rp.permission_id = p.permission_id AND p.is_active = 1
            WHERE u.user_id = ? AND u.is_active = 1 AND u.is_archive = 0
            GROUP BY u.user_id
        ");
        $stmt->execute([$userId]);
        
        return $stmt->fetch();
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole($roleName)
    {
        $user = $this->getCurrentUser();
        if (!$user || !$user['roles']) {
            return false;
        }
        
        $roles = explode(',', $user['roles']);
        return in_array($roleName, $roles);
    }
    
    /**
     * Get PermissionService instance
     */
    private function getPermissionService()
    {
        if (!isset($this->permissionService)) {
            require_once __DIR__ . '/PermissionService.php';
            $this->permissionService = new PermissionService();
        }
        return $this->permissionService;
    }
    
    /**
     * Check if user has specific permission
     */
    public function hasPermission($permissionName)
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return false;
        }
        
        $permissionService = $this->getPermissionService();
        return $permissionService->hasPermission($user, $permissionName);
    }
    
    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            $roles = [$roles];
        }
        
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Require authentication (redirect if not authenticated)
     */
    public function requireAuth($redirectUrl = '/agrimarket-erd/v1/auth/login/')
    {
        if (!$this->isAuthenticated()) {
            header("Location: $redirectUrl");
            exit;
        }
    }
    
    /**
     * Require specific role (redirect if not authorized)
     */
    public function requireRole($roleName, $redirectUrl = '/unauthorized.php')
    {
        $this->requireAuth();
        
        if (!$this->hasRole($roleName)) {
            header("Location: $redirectUrl");
            exit;
        }
    }
    
    /**
     * Require specific permission (redirect if not authorized)
     */
    public function requirePermission($permissionName, $redirectUrl = '/unauthorized.php')
    {
        $this->requireAuth();
        
        if (!$this->hasPermission($permissionName)) {
            header("Location: $redirectUrl");
            exit;
        }
    }
    
    /**
     * Generate CSRF token
     */
    public function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token
     */
    public function validateCSRFToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Create user session
     */
    private function createSession($user)
    {
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_roles'] = $user['roles'];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId)
    {
        $db = $this->userModel->getDb();
        $stmt = $db->prepare("UPDATE users SET last_login_at = NOW() WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
    
    /**
     * Set remember me token
     */
    private function setRememberMeToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        
        // Store hashed token in database
        $db = $this->userModel->getDb();
        $stmt = $db->prepare("UPDATE users SET remember_token = ? WHERE user_id = ?");
        $stmt->execute([$hashedToken, $userId]);
        
        // Set cookie (30 days)
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
        setcookie('remember_user', $userId, time() + (30 * 24 * 60 * 60), '/', '', false, true);
    }
    
    /**
     * Validate remember me token
     */
    private function validateRememberMeToken($userId, $token)
    {
        $db = $this->userModel->getDb();
        $stmt = $db->prepare("SELECT remember_token FROM users WHERE user_id = ? AND is_active = 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($token, $user['remember_token'])) {
            // Recreate session
            $stmt = $db->prepare("
                SELECT u.*, GROUP_CONCAT(r.role_name) as roles
                FROM users u
                LEFT JOIN user_roles ur ON u.user_id = ur.user_id AND ur.is_active = 1
                LEFT JOIN roles r ON ur.role_id = r.role_id AND r.is_active = 1
                WHERE u.user_id = ?
                GROUP BY u.user_id
            ");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch();
            
            if ($userData) {
                $this->createSession($userData);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Clear remember me token
     */
    private function clearRememberMeToken($userId)
    {
        $db = $this->userModel->getDb();
        $stmt = $db->prepare("UPDATE users SET remember_token = NULL WHERE user_id = ?");
        $stmt->execute([$userId]);
    }
    
    /**
     * Get redirect URL based on user roles
     */
    private function getRedirectUrl($roles)
    {
        // All authenticated users now use the unified dashboard
        return '/agrimarket-erd/v1/dashboard/';
    }
    
    /**
     * Check session timeout
     */
    public function checkSessionTimeout($timeoutMinutes = 30)
    {
        if (isset($_SESSION['last_activity'])) {
            $inactive = time() - $_SESSION['last_activity'];
            
            if ($inactive >= ($timeoutMinutes * 60)) {
                $this->logout();
                return false;
            }
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Get user's dashboard URL
     */
    public function getUserDashboardUrl()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return '/agrimarket-erd/v1/auth/login/';
        }
        
        return $this->getRedirectUrl($user['roles']);
    }

    /**
     * Get current user with extended role information
     */
    public function getCurrentUserWithRoles()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return null;
        }

        // Get roles from user_roles table (comma-separated string)
        $userRoles = [];
        if (isset($user['roles']) && !empty($user['roles'])) {
            $userRoles = explode(',', $user['roles']);
        }

        $roleData = [
            'user' => $user,
            'isCustomer' => in_array('customer', $userRoles) || $user['role'] === 'customer',
            'isVendor' => in_array('vendor', $userRoles) || $user['role'] === 'vendor', 
            'isAdmin' => in_array('admin', $userRoles) || $user['role'] === 'admin',
            'isStaff' => in_array('staff', $userRoles) || $user['role'] === 'staff',
            'customerId' => null,
            'vendorId' => null,
            'staffId' => null
        ];

        // Get specific role IDs
        try {
            $db = $this->userModel->getDb();
            
            if ($roleData['isCustomer']) {
                $stmt = $db->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                $customer = $stmt->fetch();
                $roleData['customerId'] = $customer['customer_id'] ?? null;
                
                // Auto-create customer profile if missing
                if ($roleData['customerId'] === null) {
                    $roleData['customerId'] = $this->createMissingCustomerProfile($user['user_id']);
                }
            }

            if ($roleData['isVendor']) {
                $stmt = $db->prepare("SELECT vendor_id FROM vendors WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                $vendor = $stmt->fetch();
                $roleData['vendorId'] = $vendor['vendor_id'] ?? null;
            }

            if ($roleData['isStaff']) {
                $stmt = $db->prepare("SELECT staff_id FROM staff WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                $staff = $stmt->fetch();
                $roleData['staffId'] = $staff['staff_id'] ?? null;
            }
        } catch (Exception $e) {
            error_log("Error fetching role data: " . $e->getMessage());
        }

        return $roleData;
    }

    /**
     * Create missing customer profile for a user
     */
    private function createMissingCustomerProfile($userId)
    {
        try {
            $db = $this->userModel->getDb();
            
            // Get user details
            $stmt = $db->prepare("SELECT phone FROM users WHERE user_id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            
            // Create customer profile
            $stmt = $db->prepare("
                INSERT INTO customers (user_id, phone) 
                VALUES (?, ?)
            ");
            $stmt->execute([$userId, $user['phone'] ?? null]);
            
            $customerId = $db->lastInsertId();
            error_log("Auto-created customer profile: customer_id=$customerId for user_id=$userId");
            
            return $customerId;
        } catch (Exception $e) {
            error_log("Failed to create customer profile: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get customer ID for a user
     */
    public function getCustomerId($userId)
    {
        try {
            $db = $this->userModel->getDb();
            $stmt = $db->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
            $stmt->execute([$userId]);
            $customer = $stmt->fetch();
            return $customer['customer_id'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get vendor ID for a user
     */
    public function getVendorId($userId)
    {
        try {
            $db = $this->userModel->getDb();
            $stmt = $db->prepare("SELECT vendor_id FROM vendors WHERE user_id = ?");
            $stmt->execute([$userId]);
            $vendor = $stmt->fetch();
            return $vendor['vendor_id'] ?? null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Utility: Create missing customer profiles for all users with role='customer'
     * This can be called once to fix existing data
     */
    public static function createMissingCustomerProfiles()
    {
        try {
            global $host, $user, $pass, $dbname;
            $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Find users with role='customer' who don't have customer profiles
            $stmt = $db->prepare("
                SELECT u.user_id, u.name, u.email, u.phone
                FROM users u
                LEFT JOIN customers c ON u.user_id = c.user_id
                WHERE u.role = 'customer' 
                AND u.is_archive = 0 
                AND c.customer_id IS NULL
            ");
            $stmt->execute();
            $missingCustomers = $stmt->fetchAll();
            
            $created = 0;
            foreach ($missingCustomers as $user) {
                $stmt = $db->prepare("
                    INSERT INTO customers (user_id, phone) 
                    VALUES (?, ?)
                ");
                if ($stmt->execute([$user['user_id'], $user['phone']])) {
                    $created++;
                    error_log("Created customer profile for user: {$user['name']} (ID: {$user['user_id']})");
                }
            }
            
            return [
                'success' => true,
                'message' => "Created $created customer profiles out of " . count($missingCustomers) . " missing profiles",
                'created' => $created,
                'total_missing' => count($missingCustomers)
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create customer profiles: ' . $e->getMessage()
            ];
        }
    }
} 