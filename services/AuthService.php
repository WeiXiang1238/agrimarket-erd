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
            SELECT u.user_id, u.name, u.email, u.role, u.profile_picture, u.last_login_at,
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
} 