<?php

require_once __DIR__ . '/../Db_Connect.php';

/**
 * Vendor Service
 * Contains all business logic for vendor operations
 */
class VendorService
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
     * Get paginated vendors with filtering
     */
    public function getPaginatedVendors($page = 1, $limit = 10, $filters = [])
    {
        $offset = ($page - 1) * $limit;
        
        // Build query conditions
        $whereConditions = ['v.is_archive = 0']; // Only non-archived vendors
        $params = [];
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(v.business_name LIKE ? OR u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        
        if (!empty($filters['verification_status'])) {
            // Since all vendors are considered 'verified', only filter if not 'verified'
            if ($filters['verification_status'] !== 'verified') {
                $whereConditions[] = "1 = 0"; // No results for non-verified status
            }
        }
        
        if (!empty($filters['subscription_tier'])) {
            $whereConditions[] = "st.name = ?";
            $params[] = $filters['subscription_tier'];
        }
        
        if (!empty($filters['business_type'])) {
            // Business type filter is not applicable with current schema
            // Adding a condition that always returns true to maintain compatibility
            $whereConditions[] = "1 = 1";
        }
        
        if (isset($filters['status']) && $filters['status'] !== '') {
            $whereConditions[] = "u.is_active = ?";
            $params[] = $filters['status'];
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        // Get total count
        $countQuery = "
            SELECT COUNT(*) as total 
            FROM vendors v 
            JOIN users u ON v.user_id = u.user_id 
            LEFT JOIN subscription_tiers st ON v.subscription_tier_id = st.tier_id
            WHERE $whereClause
        ";
        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        
        // Get vendors
        $query = "
            SELECT 
                v.vendor_id, v.business_name, v.contact_number, v.address,
                v.website_url, v.description, v.subscription_tier_id, 
                v.registration_date as created_at, v.tier_id, v.is_archive,
                u.name as contact_name, u.email as user_email, u.is_active as user_active,
                st.name as subscription_tier,
                'verified' as verification_status,
                'Not specified' as business_type,
                0.00 as total_sales,
                0 as rating,
                0 as total_reviews,
                u.is_active as is_active
            FROM vendors v
            JOIN users u ON v.user_id = u.user_id
            LEFT JOIN subscription_tiers st ON v.subscription_tier_id = st.tier_id
            WHERE $whereClause
            ORDER BY v.registration_date DESC
            LIMIT $limit OFFSET $offset
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $vendors = $stmt->fetchAll();
        
        return [
            'success' => true,
            'vendors' => $vendors,
            'total' => $total,
            'page' => $page,
            'totalPages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Register new vendor with user account creation (for public registration)
     */
    public function registerVendor($vendorData)
    {
        try {
            // Comprehensive validation for registration
            $validation = $this->validateVendorRegistrationData($vendorData);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Check if email already exists
            if ($this->emailExists($vendorData['business_email'])) {
                return ['success' => false, 'message' => 'Email already exists'];
            }
            
            $this->db->beginTransaction();
            
            // Create user account
            $userData = [
                'name' => $vendorData['contact_person'],
                'email' => $vendorData['business_email'],
                'password' => $vendorData['password'],
                'role' => 'vendor',
                'phone' => $vendorData['business_phone'] ?? null
            ];
            
            $userId = $this->createUserAccount($userData);
            if (!$userId) {
                throw new Exception("Failed to create user account");
            }
            
            // Create vendor profile
            $stmt = $this->db->prepare("
                INSERT INTO vendors (
                    user_id, business_name, contact_number, address, 
                    website_url, description, subscription_tier_id, tier_id, registration_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                trim($vendorData['business_name']),
                $vendorData['business_phone'],
                $vendorData['business_address'],
                $vendorData['website_url'] ?? null,
                $vendorData['description'] ?? null,
                $this->getSubscriptionTierId($vendorData['subscription_tier'] ?? 'basic'),
                1  // Default tier ID
            ]);
            
            $vendorId = $this->db->lastInsertId();
            
            $this->db->commit();
            
            return [
                'success' => true,
                'vendor_id' => $vendorId,
                'user_id' => $userId,
                'message' => 'Vendor account created successfully'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Create new vendor with automatic user creation
     */
    public function createVendor($vendorData)
    {
        try {
            // Comprehensive validation
            $validation = $this->validateVendorData($vendorData, false);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            $this->db->beginTransaction();
            
            // First create the user account
            $userData = [
                'name' => $vendorData['contact_person'] ?? $vendorData['business_name'],
                'email' => $vendorData['business_email'] ?? $vendorData['email'],
                'password' => $this->generateSecurePassword(),
                'role' => 'vendor'
            ];
            
            $userId = $this->createUserAccount($userData);
            if (!$userId) {
                throw new Exception("Failed to create user account");
            }
            
            // Create vendor profile
            $stmt = $this->db->prepare("
                INSERT INTO vendors (
                    user_id, business_name, contact_number, address, 
                    website_url, description, subscription_tier_id, tier_id, registration_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                trim($vendorData['business_name']),
                $vendorData['business_phone'] ?? $vendorData['contact_number'] ?? '',
                $vendorData['business_address'] ?? $vendorData['address'] ?? '',
                $vendorData['website_url'] ?? null,
                $vendorData['description'] ?? null,
                $this->getSubscriptionTierId($vendorData['subscription_tier'] ?? 'basic'),
                1  // Default tier ID
            ]);
            
            $vendorId = $this->db->lastInsertId();
            
            $this->db->commit();
            
            return [
                'success' => true,
                'vendor_id' => $vendorId,
                'user_id' => $userId,
                'temp_password' => $userData['password'], // Return temp password for admin
                'message' => 'Vendor created successfully with user account'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to create vendor: ' . $e->getMessage()];
        }
    }
    
    /**
     * Create vendor from existing user (for backward compatibility)
     */
    public function createVendorFromUser($vendorData)
    {
        try {
            // Comprehensive validation
            $validation = $this->validateVendorData($vendorData, false);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Check if user already has a vendor profile
            if ($this->userHasVendorProfile($vendorData['user_id'])) {
                return ['success' => false, 'message' => 'User already has a vendor profile'];
            }
            
            $this->db->beginTransaction();
            
            // Create vendor
            $stmt = $this->db->prepare("
                INSERT INTO vendors (
                    user_id, business_name, contact_number, address, 
                    website_url, description, subscription_tier_id, tier_id, registration_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $vendorData['user_id'],
                trim($vendorData['business_name']),
                $vendorData['business_phone'] ?? $vendorData['contact_number'] ?? '',
                $vendorData['business_address'] ?? $vendorData['address'] ?? '',
                $vendorData['website_url'] ?? null,
                $vendorData['description'] ?? null,
                $this->getSubscriptionTierId($vendorData['subscription_tier'] ?? 'basic'),
                1  // Default tier ID
            ]);
            
            $vendorId = $this->db->lastInsertId();
            
            // Update user role to vendor if not already
            $this->updateUserRole($vendorData['user_id'], 'vendor');
            
            $this->db->commit();
            
            return [
                'success' => true,
                'vendor_id' => $vendorId,
                'message' => 'Vendor created successfully'
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to create vendor: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update vendor
     */
    public function updateVendor($vendorId, $vendorData)
    {
        try {
            // Comprehensive validation
            $validation = $this->validateVendorData($vendorData, true);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Remove sensitive fields and map to actual database columns
            $validFields = ['business_name', 'contact_number', 'address', 'website_url', 'description'];
            $mappedData = [];
            
            if (isset($vendorData['business_name'])) {
                $mappedData['business_name'] = trim($vendorData['business_name']);
            }
            if (isset($vendorData['business_phone']) || isset($vendorData['contact_number'])) {
                $mappedData['contact_number'] = trim($vendorData['business_phone'] ?? $vendorData['contact_number']);
            }
            if (isset($vendorData['business_address']) || isset($vendorData['address'])) {
                $mappedData['address'] = trim($vendorData['business_address'] ?? $vendorData['address']);
            }
            if (isset($vendorData['website_url'])) {
                $mappedData['website_url'] = trim($vendorData['website_url']) ?: null;
            }
            if (isset($vendorData['description'])) {
                $mappedData['description'] = trim($vendorData['description']) ?: null;
            }
            
            if (empty($mappedData)) {
                return ['success' => false, 'message' => 'No valid fields to update'];
            }
            
            $fields = [];
            $values = [];
            
            foreach ($mappedData as $field => $value) {
                $fields[] = "$field = ?";
                $values[] = $value;
            }
            
            $values[] = $vendorId;
            
            $stmt = $this->db->prepare("
                UPDATE vendors 
                SET " . implode(', ', $fields) . "
                WHERE vendor_id = ?
            ");
            $result = $stmt->execute($values);
            
            return [
                'success' => $result,
                'message' => $result ? 'Vendor updated successfully' : 'Failed to update vendor'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update vendor: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update vendor verification status
     */
    public function updateVerificationStatus($vendorId, $status, $adminId = null)
    {
        $validStatuses = ['pending', 'verified', 'rejected'];
        
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid verification status'];
        }
        
        // Since the current schema doesn't support verification status,
        // we'll simulate success but log the action
        try {
            // Log the status change if admin ID provided
            if ($adminId) {
                $this->logVendorAction($vendorId, $adminId, "Verification status changed to: $status");
            }
            
            return [
                'success' => true,
                'message' => 'Verification status updated successfully (simulated - current schema limitation)'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update verification status: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update vendor subscription tier
     */
    public function updateSubscriptionTier($vendorId, $tier, $adminId = null)
    {
        $validTiers = $this->getValidSubscriptionTiers();
        
        if (!in_array($tier, $validTiers)) {
            return ['success' => false, 'message' => 'Invalid subscription tier'];
        }
        
        try {
            // Get the tier ID from subscription_tiers table
            $tierStmt = $this->db->prepare("SELECT tier_id FROM subscription_tiers WHERE name = ?");
            $tierStmt->execute([$tier]);
            $tierRow = $tierStmt->fetch();
            
            if (!$tierRow) {
                return ['success' => false, 'message' => 'Subscription tier not found'];
            }
            
            $stmt = $this->db->prepare("
                UPDATE vendors 
                SET subscription_tier_id = ?
                WHERE vendor_id = ?
            ");
            $result = $stmt->execute([$tierRow['tier_id'], $vendorId]);
            
            // Log the tier change if admin ID provided
            if ($adminId && $result) {
                $this->logVendorAction($vendorId, $adminId, "Subscription tier changed to: $tier");
            }
            
            return [
                'success' => $result,
                'message' => $result ? 'Subscription tier updated successfully' : 'Failed to update subscription tier'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update subscription tier: ' . $e->getMessage()];
        }
    }
    
    /**
     * Toggle vendor active status in users table
     */
    public function toggleVendorStatus($vendorId, $isActive, $adminId = null)
    {
        try {
            $this->db->beginTransaction();
            
            // Update is_active in users table for the vendor
            $stmt = $this->db->prepare("
                UPDATE users u
                JOIN vendors v ON u.user_id = v.user_id
                SET u.is_active = ? 
                WHERE v.vendor_id = ?
            ");
            $result = $stmt->execute([$isActive, $vendorId]);
            
            if ($result && $adminId) {
                $action = $isActive ? "Vendor activated" : "Vendor deactivated";
                $this->logVendorAction($vendorId, $adminId, $action);
            }
            
            $this->db->commit();
            
            return [
                'success' => $result,
                'message' => $result ? 
                    ($isActive ? 'Vendor activated successfully' : 'Vendor deactivated successfully') :
                    'Failed to update vendor status'
            ];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to update vendor status: ' . $e->getMessage()];
        }
    }

    /**
     * Toggle vendor archive status
     */
    public function toggleVendorArchive($vendorId, $isArchive, $adminId = null)
    {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                UPDATE vendors 
                SET is_archive = ? 
                WHERE vendor_id = ?
            ");
            $result = $stmt->execute([$isArchive, $vendorId]);
            
            if ($result && $adminId) {
                $action = $isArchive ? "Vendor archived" : "Vendor unarchived";
                $this->logVendorAction($vendorId, $adminId, $action);
            }
            
            $this->db->commit();
            
            return [
                'success' => $result,
                'message' => $result ? 
                    ($isArchive ? 'Vendor archived successfully' : 'Vendor restored successfully') :
                    'Failed to update vendor status'
            ];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to update vendor status: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete vendor (soft delete)
     */
    public function deleteVendor($vendorId, $adminId = null)
    {
        try {
            $this->db->beginTransaction();
            
            // Soft delete vendor
            $stmt = $this->db->prepare("
                UPDATE vendors 
                SET is_archive = 1 
                WHERE vendor_id = ?
            ");
            $result = $stmt->execute([$vendorId]);
            
            if ($result && $adminId) {
                $this->logVendorAction($vendorId, $adminId, "Vendor deleted");
            }
            
            $this->db->commit();
            
            return [
                'success' => $result,
                'message' => $result ? 'Vendor deleted successfully' : 'Failed to delete vendor'
            ];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to delete vendor: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get vendor statistics
     */
    public function getVendorStatistics()
    {
        try {
            $query = "
                SELECT 
                    COUNT(*) as total_vendors,
                    COUNT(*) as verified_vendors,
                    0 as pending_vendors,
                    0 as rejected_vendors,
                    COUNT(CASE WHEN st.name = 'basic' THEN 1 END) as basic_tier,
                    COUNT(CASE WHEN st.name = 'premium' THEN 1 END) as premium_tier,
                    COUNT(CASE WHEN st.name = 'enterprise' THEN 1 END) as enterprise_tier,
                    0 as average_rating,
                    0.00 as total_platform_sales
                FROM vendors v
                LEFT JOIN subscription_tiers st ON v.subscription_tier_id = st.tier_id
                WHERE v.is_archive = 0
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            return [
                'total_vendors' => 0,
                'verified_vendors' => 0,
                'pending_vendors' => 0,
                'rejected_vendors' => 0,
                'basic_tier' => 0,
                'premium_tier' => 0,
                'enterprise_tier' => 0,
                'average_rating' => 0,
                'total_platform_sales' => 0
            ];
        }
    }
    
    /**
     * Get vendor details
     */
    public function getVendorDetails($vendorId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    v.*, u.name as contact_name, u.email as user_email,
                    u.is_active as user_active,
                    (SELECT COUNT(*) FROM products WHERE vendor_id = v.vendor_id AND is_archive = 0) as product_count,
                    (SELECT COUNT(*) FROM orders WHERE vendor_id = v.vendor_id) as order_count
                FROM vendors v
                JOIN users u ON v.user_id = u.user_id
                WHERE v.vendor_id = ? AND v.is_archive = 0
            ");
            $stmt->execute([$vendorId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Get vendor by ID (for products page filtering)
     */
    public function getVendorById($vendorId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    v.vendor_id, v.business_name, v.contact_number, v.address,
                    v.website_url, v.description, v.subscription_tier_id,
                    u.name as contact_name, u.email as user_email,
                    st.name as subscription_tier
                FROM vendors v
                JOIN users u ON v.user_id = u.user_id
                LEFT JOIN subscription_tiers st ON v.subscription_tier_id = st.tier_id
                WHERE v.vendor_id = ? AND v.is_archive = 0
            ");
            $stmt->execute([$vendorId]);
            $vendor = $stmt->fetch();
            
            if ($vendor) {
                return [
                    'success' => true,
                    'vendor' => $vendor
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Vendor not found'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error fetching vendor: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get available users for vendor creation (users without vendor profiles)
     */
    public function getAvailableUsers()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT u.user_id, u.name, u.email, u.role
                FROM users u
                LEFT JOIN vendors v ON u.user_id = v.user_id AND v.is_archive = 0
                WHERE v.user_id IS NULL AND u.is_archive = 0 AND u.is_active = 1
                ORDER BY u.name ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get vendor business types
     */
    public function getBusinessTypes()
    {
        return [
            ['value' => 'farm', 'label' => 'Farm'],
            ['value' => 'cooperative', 'label' => 'Cooperative'],
            ['value' => 'distributor', 'label' => 'Distributor'],
            ['value' => 'retailer', 'label' => 'Retailer'],
            ['value' => 'processor', 'label' => 'Processor'],
            ['value' => 'supplier', 'label' => 'Supplier'],
            ['value' => 'other', 'label' => 'Other']
        ];
    }
    
    /**
     * Get subscription tiers
     */
    public function getSubscriptionTiers()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT name as value, name as label, description 
                FROM subscription_tiers 
                ORDER BY tier_id
            ");
            $stmt->execute();
            $tiers = $stmt->fetchAll();
            
            if (empty($tiers)) {
                // Fallback to default tiers if none in database
                return [
                    ['value' => 'basic', 'label' => 'Basic', 'description' => 'Up to 50 products'],
                    ['value' => 'premium', 'label' => 'Premium', 'description' => 'Up to 200 products'],
                    ['value' => 'enterprise', 'label' => 'Enterprise', 'description' => 'Unlimited products']
                ];
            }
            
            return $tiers;
        } catch (Exception $e) {
            return [
                ['value' => 'basic', 'label' => 'Basic', 'description' => 'Up to 50 products'],
                ['value' => 'premium', 'label' => 'Premium', 'description' => 'Up to 200 products'],
                ['value' => 'enterprise', 'label' => 'Enterprise', 'description' => 'Unlimited products']
            ];
        }
    }
    
    /**
     * Get verification statuses
     */
    public function getVerificationStatuses()
    {
        return [
            ['value' => 'pending', 'label' => 'Pending', 'class' => 'warning'],
            ['value' => 'verified', 'label' => 'Verified', 'class' => 'success'],
            ['value' => 'rejected', 'label' => 'Rejected', 'class' => 'danger']
        ];
    }
    
    /**
     * Check if user has vendor profile
     */
    private function userHasVendorProfile($userId)
    {
        $stmt = $this->db->prepare("
            SELECT vendor_id FROM vendors 
            WHERE user_id = ? AND is_archive = 0
        ");
        $stmt->execute([$userId]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Update user role
     */
    private function updateUserRole($userId, $role)
    {
        $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE user_id = ?");
        return $stmt->execute([$role, $userId]);
    }
    
    /**
     * Log vendor action for audit trail
     */
    private function logVendorAction($vendorId, $adminId, $action)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO audit_logs (table_name, record_id, action, user_id, created_at)
                VALUES ('vendors', ?, ?, ?, NOW())
            ");
            $stmt->execute([$vendorId, $action, $adminId]);
        } catch (Exception $e) {
            // Log error but don't fail the main operation
            error_log("Failed to log vendor action: " . $e->getMessage());
        }
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
                INSERT INTO users (name, email, phone, password, role, is_active, is_archive, created_at)
                VALUES (?, ?, ?, ?, ?, 1, 0, NOW())
            ");
            $stmt->execute([
                $userData['name'],
                $userData['email'],
                $userData['phone'] ?? null,
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
    
    /**
     * Get subscription tier ID by name
     */
    private function getSubscriptionTierId($tierName)
    {
        try {
            $stmt = $this->db->prepare("SELECT tier_id FROM subscription_tiers WHERE name = ?");
            $stmt->execute([$tierName]);
            $result = $stmt->fetch();
            return $result ? $result['tier_id'] : 1; // Default to tier ID 1 if not found
        } catch (Exception $e) {
            return 1; // Default tier
        }
    }
    
    /**
     * Get valid subscription tier names from database
     */
    private function getValidSubscriptionTiers()
    {
        try {
            $stmt = $this->db->prepare("SELECT name FROM subscription_tiers ORDER BY tier_id");
            $stmt->execute();
            $tiers = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $tiers ?: ['Bronze']; // Fallback to Bronze if no tiers found
        } catch (Exception $e) {
            return ['Bronze', 'Silver', 'Gold', 'Platinum']; // Fallback tiers
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
     * Validation for vendor registration (public registration form)
     */
    private function validateVendorRegistrationData($vendorData)
    {
        $errors = [];
        
        // Validate Contact Person Name
        $contactPerson = trim($vendorData['contact_person'] ?? '');
        if (empty($contactPerson)) {
            $errors[] = 'Contact person name is required';
        } elseif (strlen($contactPerson) < 2 || strlen($contactPerson) > 100) {
            $errors[] = 'Contact person name must be between 2 and 100 characters';
        }
        
        // Validate Business Name
        $businessName = trim($vendorData['business_name'] ?? '');
        if (empty($businessName)) {
            $errors[] = 'Business name is required';
        } elseif (strlen($businessName) < 2 || strlen($businessName) > 100) {
            $errors[] = 'Business name must be between 2 and 100 characters';
        }
        
        // Validate Business Email
        $businessEmail = trim($vendorData['business_email'] ?? '');
        if (empty($businessEmail)) {
            $errors[] = 'Business email is required';
        } elseif (!filter_var($businessEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid business email address';
        }
        
        // Validate Business Phone
        $businessPhone = trim($vendorData['business_phone'] ?? '');
        if (empty($businessPhone)) {
            $errors[] = 'Business phone is required';
        } elseif (!preg_match('/^[+]?[0-9\s\-\(\)]{7,20}$/', $businessPhone)) {
            $errors[] = 'Please enter a valid phone number';
        }
        
        // Validate Business Address
        $businessAddress = trim($vendorData['business_address'] ?? '');
        if (empty($businessAddress)) {
            $errors[] = 'Business address is required';
        } elseif (strlen($businessAddress) < 10) {
            $errors[] = 'Business address must be at least 10 characters';
        }
        
        // Validate Password
        $password = $vendorData['password'] ?? '';
        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long';
        }
        
        if (!empty($errors)) {
            return ['valid' => false, 'message' => implode('. ', $errors)];
        }
        
        return ['valid' => true];
    }

    /**
     * Enhanced vendor data validation for auto-user creation
     */
    private function validateVendorData($vendorData, $isUpdate = false)
    {
        $errors = [];
        
        // Validate User ID (only for existing user flow)
        if (!$isUpdate && isset($vendorData['user_id']) && !empty($vendorData['user_id'])) {
            if (!is_numeric($vendorData['user_id'])) {
                $errors[] = 'Valid user selection is required';
            }
        }
        
        // Validate Contact Person Name (required for auto-user creation)
        if (!$isUpdate && empty($vendorData['user_id'])) {
            $contactPerson = trim($vendorData['contact_person'] ?? '');
            if (empty($contactPerson)) {
                $errors[] = 'Contact person name is required';
            } elseif (strlen($contactPerson) < 2) {
                $errors[] = 'Contact person name must be at least 2 characters';
            } elseif (strlen($contactPerson) > 100) {
                $errors[] = 'Contact person name must be less than 100 characters';
            }
        }
        
        // Validate Business Name
        $businessName = trim($vendorData['business_name'] ?? '');
        if (empty($businessName)) {
            $errors[] = 'Business name is required';
        } elseif (strlen($businessName) < 2) {
            $errors[] = 'Business name must be at least 2 characters';
        } elseif (strlen($businessName) > 100) {
            $errors[] = 'Business name must be less than 100 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9\s\-\._&\']+$/', $businessName)) {
            $errors[] = 'Business name contains invalid characters';
        }
        
        // Validate Business Email (required for auto-user creation)
        $businessEmail = trim($vendorData['business_email'] ?? $vendorData['email'] ?? '');
        if (!$isUpdate && empty($vendorData['user_id'])) {
            if (empty($businessEmail)) {
                $errors[] = 'Business email is required';
            }
        }
        
        if (!empty($businessEmail)) {
            if (!filter_var($businessEmail, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid business email address';
            } elseif (strlen($businessEmail) > 100) {
                $errors[] = 'Business email must be less than 100 characters';
            }
        }
        
        // Validate Contact Number
        $contactNumber = trim($vendorData['business_phone'] ?? $vendorData['contact_number'] ?? '');
        if (empty($contactNumber)) {
            $errors[] = 'Contact number is required';
        } elseif (!preg_match('/^[+]?[0-9\s\-\(\)]{7,20}$/', $contactNumber)) {
            $errors[] = 'Please enter a valid phone number (7-20 digits)';
        }
        
        // Validate Address
        $address = trim($vendorData['business_address'] ?? $vendorData['address'] ?? '');
        if (empty($address)) {
            $errors[] = 'Business address is required';
        } elseif (strlen($address) < 10) {
            $errors[] = 'Address must be at least 10 characters long';
        } elseif (strlen($address) > 500) {
            $errors[] = 'Address must be less than 500 characters';
        }
        
        // Validate Website URL (optional)
        $websiteUrl = trim($vendorData['website_url'] ?? '');
        if (!empty($websiteUrl)) {
            if (!filter_var($websiteUrl, FILTER_VALIDATE_URL)) {
                $errors[] = 'Please enter a valid website URL';
            } elseif (strlen($websiteUrl) > 255) {
                $errors[] = 'Website URL must be less than 255 characters';
            } elseif (!preg_match('/^https?:\/\//', $websiteUrl)) {
                $errors[] = 'Website URL must start with http:// or https://';
            }
        }
        
        // Validate Description (optional)
        $description = trim($vendorData['description'] ?? '');
        if (!empty($description) && strlen($description) > 1000) {
            $errors[] = 'Business description must be less than 1000 characters';
        }
        
        // Validate Subscription Tier (optional)
        $subscriptionTier = $vendorData['subscription_tier'] ?? '';
        if (!empty($subscriptionTier)) {
            $validTiers = $this->getValidSubscriptionTiers();
            if (!in_array($subscriptionTier, $validTiers)) {
                $errors[] = 'Invalid subscription tier selected';
            }
        }
        
        return [
            'valid' => empty($errors),
            'message' => empty($errors) ? 'Validation passed' : implode('. ', $errors)
        ];
    }
} 