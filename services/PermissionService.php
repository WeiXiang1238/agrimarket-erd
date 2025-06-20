<?php

require_once __DIR__ . '/../Db_Connect.php';

/**
 * Permission Service
 * Handles all permission-related operations and user authorization
 */
class PermissionService
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
     * Get user permissions from database
     */
    public function getUserPermissions($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT p.permission_name 
                FROM permissions p
                JOIN role_permissions rp ON p.permission_id = rp.permission_id
                JOIN user_roles ur ON rp.role_id = ur.role_id
                WHERE ur.user_id = ? AND ur.is_active = 1 AND p.is_active = 1
            ");
            $stmt->execute([$userId]);
            $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Return as associative array for faster lookup
            return array_flip($permissions);
        } catch (Exception $e) {
            // Return empty array on error
            return [];
        }
    }
    
    /**
     * Get fallback permissions based on user role
     */
    public function getFallbackPermissions($role)
    {
        $permissions = [];
        
        switch ($role) {
            case 'admin':
                $permissions = [
                    'manage_users', 'manage_vendors', 'manage_products', 
                    'manage_orders', 'view_analytics', 'manage_system', 
                    'manage_staff', 'manage_promotions'
                ];
                break;
            case 'vendor':
                $permissions = [
                    'manage_products', 'manage_orders', 
                    'manage_inventory', 'view_reports'
                ];
                break;
            case 'staff':
                $permissions = ['customer_support', 'manage_orders'];
                break;
            case 'customer':
            default:
                $permissions = ['place_orders', 'view_orders'];
                break;
        }
        
        return array_flip($permissions);
    }
    
    /**
     * Get combined user permissions (database + fallback)
     */
    public function getEffectivePermissions($user)
    {
        if (!$user || !isset($user['user_id'])) {
            return [];
        }
        
        // Try to get permissions from database first
        $permissions = $this->getUserPermissions($user['user_id']);
        
        // If no permissions found in database, use fallback based on role
        if (empty($permissions)) {
            $role = $user['role'] ?? 'customer';
            $permissions = $this->getFallbackPermissions($role);
        }
        
        return $permissions;
    }
    
    /**
     * Check if user has specific permission
     */
    public function hasPermission($user, $permission)
    {
        $permissions = $this->getEffectivePermissions($user);
        return isset($permissions[$permission]);
    }
    
    /**
     * Check if user has any of the specified permissions
     */
    public function hasAnyPermission($user, $permissionList)
    {
        $permissions = $this->getEffectivePermissions($user);
        
        foreach ($permissionList as $permission) {
            if (isset($permissions[$permission])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get all available permissions
     */
    public function getAllPermissions()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT permission_id, permission_name, module, description 
                FROM permissions 
                WHERE is_active = 1 
                ORDER BY module, permission_name
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get all available roles
     */
    public function getAllRoles()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT role_id, role_name, description 
                FROM roles 
                WHERE is_active = 1 
                ORDER BY role_name
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get permissions for a specific role
     */
    public function getRolePermissions($roleId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.permission_name 
                FROM permissions p
                JOIN role_permissions rp ON p.permission_id = rp.permission_id
                WHERE rp.role_id = ? AND p.is_active = 1
            ");
            $stmt->execute([$roleId]);
            $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return array_flip($permissions);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get user roles
     */
    public function getUserRoles($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.role_id, r.role_name, r.description 
                FROM roles r
                JOIN user_roles ur ON r.role_id = ur.role_id
                WHERE ur.user_id = ? AND ur.is_active = 1 AND r.is_active = 1
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Check if user has specific role
     */
    public function hasRole($user, $roleName)
    {
        if (!$user || !isset($user['user_id'])) {
            return false;
        }
        
        // Check primary role field first
        if (isset($user['role']) && $user['role'] === $roleName) {
            return true;
        }
        
        // Check roles from user_roles table
        $roles = $this->getUserRoles($user['user_id']);
        foreach ($roles as $role) {
            if ($role['role_name'] === $roleName) {
                return true;
            }
        }
        
        return false;
    }
} 