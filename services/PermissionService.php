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
        try {
            // Include database configuration
            require_once __DIR__ . '/../Db_Connect.php';
            
            // Use the global variables from Db_Connect.php
            global $host, $user, $pass, $dbname;
            
            // Ensure variables are strings, not arrays
            $host = is_array($host) ? 'localhost' : (string)$host;
            $user = is_array($user) ? 'root' : (string)$user;
            $pass = is_array($pass) ? '' : (string)$pass;
            $dbname = is_array($dbname) ? 'group_assignment' : (string)$dbname;
            
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
    
    /**
     * Get role details with permissions
     */
    public function getRoleDetails($roleId)
    {
        try {
            // Get role information
            $stmt = $this->db->prepare("
                SELECT role_id, role_name, description, is_active, created_at 
                FROM roles 
                WHERE role_id = ?
            ");
            $stmt->execute([$roleId]);
            $role = $stmt->fetch();
            
            if (!$role) {
                return null;
            }
            
            // Get role permissions with full permission details
            $stmt = $this->db->prepare("
                SELECT p.permission_id, p.permission_name, p.module, p.description
                FROM permissions p
                JOIN role_permissions rp ON p.permission_id = rp.permission_id
                WHERE rp.role_id = ? AND p.is_active = 1
                ORDER BY p.module, p.permission_name
            ");
            $stmt->execute([$roleId]);
            $role['permissions'] = $stmt->fetchAll();
            
            return $role;
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Update role permissions
     */
    public function updateRolePermissions($roleId, $permissionIds)
    {
        try {
            $this->db->beginTransaction();
            
            // First, remove all existing permissions for this role
            $stmt = $this->db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $stmt->execute([$roleId]);
            
            // Add new permissions
            if (!empty($permissionIds)) {
                $stmt = $this->db->prepare("
                    INSERT INTO role_permissions (role_id, permission_id) 
                    VALUES (?, ?)
                ");
                
                foreach ($permissionIds as $permissionId) {
                    $stmt->execute([$roleId, $permissionId]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Get all permissions grouped by module
     */
    public function getPermissionsByModule()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT permission_id, permission_name, module, description 
                FROM permissions 
                WHERE is_active = 1 
                ORDER BY module, permission_name
            ");
            $stmt->execute();
            $permissions = $stmt->fetchAll();
            
            // Group by module
            $grouped = [];
            foreach ($permissions as $permission) {
                $module = $permission['module'];
                if (!isset($grouped[$module])) {
                    $grouped[$module] = [];
                }
                $grouped[$module][] = $permission;
            }
            
            return $grouped;
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get roles with permission counts
     */
    public function getRolesWithPermissionCounts()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT r.role_id, r.role_name, r.description, r.is_active,
                       COUNT(rp.permission_id) as permission_count
                FROM roles r
                LEFT JOIN role_permissions rp ON r.role_id = rp.role_id
                WHERE r.is_active = 1
                GROUP BY r.role_id, r.role_name, r.description, r.is_active
                ORDER BY r.role_name
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
} 