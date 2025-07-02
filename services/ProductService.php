<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/ProductCategory.php';
require_once __DIR__ . '/../models/ProductImage.php';
require_once __DIR__ . '/../models/ProductAttribute.php';
require_once __DIR__ . '/../models/Vendor.php';

/**
 * ProductService
 * Handles product management operations with mysqli
 * Updated to match actual database schema
 */
class ProductService
{
    private $db;
    
    public function __construct()
    {
        global $conn;
        if (!$conn || $conn->connect_error) {
            throw new Exception('Database connection failed');
        }
        $this->db = $conn;
    }
    
    /**
     * Helper function to execute mysqli queries with parameters
     */
    private function executeQuery($query, $params = [], $types = '')
    {
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        
        if (!empty($params)) {
            if (empty($types)) {
                // Auto-detect types
                $types = '';
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } else {
                        $types .= 's';
                    }
                }
            }
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
    
    /**
     * Get vendor ID for a user
     */
    public function getVendorIdByUserId($userId)
    {
        try {
            error_log('Looking up vendor ID for user ID: ' . $userId);
            $result = $this->executeQuery(
                "SELECT vendor_id FROM vendors WHERE user_id = ? AND is_archive = 0", 
                [$userId]
            );
            $vendorData = $result->fetch_assoc();
            error_log('Vendor lookup result: ' . print_r($vendorData, true));
            return $vendorData ? $vendorData['vendor_id'] : null;
        } catch (Exception $e) {
            error_log('Error fetching vendor data: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get paginated products with filters and role-based access
     */
    public function getPaginatedProducts($page = 1, $limit = 10, $filters = [], $userRole = 'admin', $userId = null)
    {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = ['p.is_archive = 0'];
            $params = [];
            
            // Role-based filtering - vendors can only see their own products
            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $whereConditions[] = 'p.vendor_id = ?';
                    $params[] = $vendorId;
                } else {
                    // Vendor profile not found, show no products
                    $whereConditions[] = '1 = 0';
                }
            }
            
            // Search filter
            if (!empty($filters['search'])) {
                $whereConditions[] = '(p.name LIKE ? OR p.description LIKE ?)';
                $searchTerm = '%' . $filters['search'] . '%';
                $params = array_merge($params, [$searchTerm, $searchTerm]);
            }
            
            // Category filter (using category string field)
            if (!empty($filters['category_id'])) {
                // Convert category_id to category name for filtering
                $categoryResult = $this->executeQuery(
                    "SELECT name FROM product_categories WHERE category_id = ?", 
                    [$filters['category_id']]
                );
                $categoryData = $categoryResult->fetch_assoc();
                if ($categoryData) {
                    $whereConditions[] = 'p.category = ?';
                    $params[] = $categoryData['name'];
                }
            } elseif (!empty($filters['category'])) {
                // Direct category name filter
                $whereConditions[] = 'p.category = ?';
                $params[] = $filters['category'];
            }
            
            // Vendor filter (only for admin)
            if (!empty($filters['vendor_id']) && $userRole !== 'vendor') {
                $whereConditions[] = 'p.vendor_id = ?';
                $params[] = $filters['vendor_id'];
            }
            
            // Status filter - using stock_quantity for inventory status
            if (!empty($filters['status'])) {
                if ($filters['status'] === 'active' || $filters['status'] === 'in_stock') {
                    $whereConditions[] = 'p.stock_quantity > 0';
                } elseif ($filters['status'] === 'out_of_stock') {
                    $whereConditions[] = 'p.stock_quantity = 0';
                } elseif ($filters['status'] === 'low_stock') {
                    $whereConditions[] = 'p.stock_quantity > 0 AND p.stock_quantity <= 10';
                }
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM products p WHERE {$whereClause}";
            $result = $this->executeQuery($countQuery, $params);
            $totalRow = $result->fetch_assoc();
            $total = $totalRow['total'];
            
            // Get products
            $query = "
                SELECT 
                    p.*,
                    v.business_name as vendor_name,
                    p.category as category_name,
                    CASE 
                        WHEN p.stock_quantity > 0 THEN 'active'
                        ELSE 'out_of_stock'
                    END as status,
                    p.base_price as unit_price,
                    0 as featured,
                    0 as organic_certified,
                    '' as sku,
                    NULL as weight,
                    NULL as dimensions,
                    NULL as expiry_date,
                    NULL as origin_location,
                    NULL as quantity_per_unit,
                    1 as minimum_order_quantity,
                    0 as review_count,
                    0 as avg_rating
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE {$whereClause}
                ORDER BY p.product_id DESC
                LIMIT ? OFFSET ?
            ";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $result = $this->executeQuery($query, $params);
            $products = $result->fetch_all(MYSQLI_ASSOC);
            
            return [
                'success' => true,
                'products' => $products,
                'total' => $total,
                'page' => $page,
                'totalPages' => ceil($total / $limit)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch products: ' . $e->getMessage()];
        }
    }
    
    /**
     * Handle image upload
     */
    private function handleImageUpload($fileData)
    {
        if (!isset($fileData) || $fileData['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Image upload failed', 'path' => null];
        }
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($fileData['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.'];
        }
        
        // Validate file size (5MB max)
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if ($fileData['size'] > $maxFileSize) {
            return ['success' => false, 'message' => 'File size too large. Maximum allowed size is 5MB.'];
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $fileExtension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        $fileName = uniqid('product_') . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadDir . $fileName;
        $relativePath = 'uploads/products/' . $fileName;
        
        // Move uploaded file
        if (move_uploaded_file($fileData['tmp_name'], $filePath)) {
            return ['success' => true, 'message' => 'Image uploaded successfully', 'path' => $relativePath];
        } else {
            return ['success' => false, 'message' => 'Failed to save uploaded image', 'path' => null];
        }
    }

    /**
     * Create new product
     */
    public function createProduct($productData, $userRole = 'admin', $userId = null)
    {
        try {
            error_log('createProduct called with userRole: ' . $userRole . ', userId: ' . $userId);
            
            // For vendors, automatically set vendor_id to their own vendor profile
            $vendorId = intval($productData['vendor_id'] ?? 0);
            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                error_log('Vendor ID resolved: ' . $vendorId);
                if (!$vendorId) {
                    return ['success' => false, 'message' => 'Vendor profile not found'];
                }
            }
            
            // Validate product data
            $validation = $this->validateProductData($productData);
            error_log('Validation result: ' . print_r($validation, true));
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Handle image upload if provided
            $imagePath = null;
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                error_log('Processing image upload');
                $uploadResult = $this->handleImageUpload($_FILES['product_image']);
                error_log('Image upload result: ' . print_r($uploadResult, true));
                if (!$uploadResult['success']) {
                    return ['success' => false, 'message' => $uploadResult['message']];
                }
                $imagePath = $uploadResult['path'];
            }
            
            // Get category name from category_id
            $categoryName = null;
            if (!empty($productData['category_id'])) {
                $categoryResult = $this->executeQuery(
                    "SELECT name FROM product_categories WHERE category_id = ?", 
                    [$productData['category_id']]
                );
                $categoryData = $categoryResult->fetch_assoc();
                if ($categoryData) {
                    $categoryName = $categoryData['name'];
                }
            }
            
            // Prepare data for insertion (matching actual database schema)
            $insertParams = [
                $vendorId,
                trim($productData['name']),
                trim($productData['description']) ?: null,
                $categoryName,
                trim($productData['packaging'] ?? '') ?: null,
                floatval($productData['unit_price']),  // maps to base_price
                floatval($productData['selling_price']),
                intval($productData['stock_quantity'] ?? 0),
                intval($productData['is_discounted'] ?? 0),
                !empty($productData['discount_percent']) ? floatval($productData['discount_percent']) : null,
                $imagePath
            ];
            
            error_log('Insert parameters: ' . print_r($insertParams, true));
            
            // Insert product (using actual database columns)
            $stmt = $this->db->prepare("
                INSERT INTO products (
                    vendor_id, name, description, category, packaging, 
                    base_price, selling_price, stock_quantity, is_discounted, 
                    discount_percent, image_path
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            if ($stmt) {
                $types = 'issssddiids';
                $stmt->bind_param($types, ...$insertParams);
                $result = $stmt->execute();
                
                if ($result) {
                    $productId = $this->db->insert_id;
                    error_log('Product created successfully with ID: ' . $productId);
                    
                    // Create notification for successful product creation
                    $this->createProductNotification($userId, $productData['name'], $productId);
                    
                    return [
                        'success' => true, 
                        'message' => 'Product created successfully', 
                        'product_id' => $productId
                    ];
                } else {
                    error_log('Failed to execute insert: ' . $stmt->error);
                    return ['success' => false, 'message' => 'Failed to create product: ' . $stmt->error];
                }
            } else {
                error_log('Failed to prepare statement: ' . $this->db->error);
                return ['success' => false, 'message' => 'Failed to prepare statement: ' . $this->db->error];
            }
            
        } catch (Exception $e) {
            error_log('Exception in createProduct: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create product: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update existing product
     */
    public function updateProduct($productId, $productData)
    {
        try {
            // Validate product data
            $validation = $this->validateProductData($productData);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Handle image upload if provided
            $imagePath = null;
            $updateImagePath = false;
            
            // Check if a new image is being uploaded
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadResult = $this->handleImageUpload($_FILES['product_image']);
                if (!$uploadResult['success']) {
                    return ['success' => false, 'message' => $uploadResult['message']];
                }
                $imagePath = $uploadResult['path'];
                $updateImagePath = true;
            }
            // Check if existing image should be removed (when remove_image flag is set)
            elseif (isset($productData['remove_image']) && $productData['remove_image'] === '1') {
                $imagePath = null; // Set to null to remove the image
                $updateImagePath = true;
            }
            
            // Get category name from category_id
            $categoryName = null;
            if (!empty($productData['category_id'])) {
                $categoryResult = $this->executeQuery(
                    "SELECT name FROM product_categories WHERE category_id = ?", 
                    [$productData['category_id']]
                );
                $categoryData = $categoryResult->fetch_assoc();
                if ($categoryData) {
                    $categoryName = $categoryData['name'];
                }
            }
            
            // Prepare data for update (matching actual database schema)
            if ($updateImagePath) {
                $updateParams = [
                    trim($productData['name']),
                    trim($productData['description']) ?: null,
                    $categoryName,
                    trim($productData['packaging'] ?? '') ?: null,
                    floatval($productData['unit_price']),  // maps to base_price
                    floatval($productData['selling_price']),
                    intval($productData['stock_quantity'] ?? 0),
                    intval($productData['is_discounted'] ?? 0),
                    !empty($productData['discount_percent']) ? floatval($productData['discount_percent']) : null,
                    $imagePath,
                    $productId
                ];
            } else {
                $updateParams = [
                    trim($productData['name']),
                    trim($productData['description']) ?: null,
                    $categoryName,
                    trim($productData['packaging'] ?? '') ?: null,
                    floatval($productData['unit_price']),  // maps to base_price
                    floatval($productData['selling_price']),
                    intval($productData['stock_quantity'] ?? 0),
                    intval($productData['is_discounted'] ?? 0),
                    !empty($productData['discount_percent']) ? floatval($productData['discount_percent']) : null,
                    $productId
                ];
            }
            
            // Update product (using actual database columns)
            if ($updateImagePath) {
                $stmt = $this->db->prepare("
                    UPDATE products SET
                        name = ?, description = ?, category = ?, packaging = ?, 
                        base_price = ?, selling_price = ?, stock_quantity = ?, 
                        is_discounted = ?, discount_percent = ?, image_path = ?
                    WHERE product_id = ? AND is_archive = 0
                ");
                $types = 'ssssddiiisi';
            } else {
                $stmt = $this->db->prepare("
                    UPDATE products SET
                        name = ?, description = ?, category = ?, packaging = ?, 
                        base_price = ?, selling_price = ?, stock_quantity = ?, 
                        is_discounted = ?, discount_percent = ?
                    WHERE product_id = ? AND is_archive = 0
                ");
                $types = 'ssssddiiid';
            }
            
            if ($stmt) {
                $stmt->bind_param($types, ...$updateParams);
                $result = $stmt->execute();
                
                if ($result && $stmt->affected_rows > 0) {
                    return ['success' => true, 'message' => 'Product updated successfully'];
                } else {
                    return ['success' => false, 'message' => 'Product not found or no changes made'];
                }
            } else {
                return ['success' => false, 'message' => 'Failed to prepare statement: ' . $this->db->error];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update product: ' . $e->getMessage()];
        }
    }
    
    /**
     * Toggle product status (using stock_quantity as status indicator)
     */
    public function toggleProductStatus($productId, $status)
    {
        try {
            // Map status to stock_quantity changes
            $stockQuantity = null;
            if ($status === 'active') {
                // Set to 1 if currently 0, otherwise keep current value
                $currentResult = $this->executeQuery(
                    "SELECT stock_quantity FROM products WHERE product_id = ? AND is_archive = 0", 
                    [$productId]
                );
                $currentData = $currentResult->fetch_assoc();
                $stockQuantity = $currentData && $currentData['stock_quantity'] == 0 ? 1 : $currentData['stock_quantity'];
            } elseif ($status === 'out_of_stock') {
                $stockQuantity = 0;
            } else {
                return ['success' => false, 'message' => 'Invalid status for current database schema'];
            }
            
            $stmt = $this->db->prepare("
                UPDATE products 
                SET stock_quantity = ?
                WHERE product_id = ? AND is_archive = 0
            ");
            
            if ($stmt) {
                $stmt->bind_param('ii', $stockQuantity, $productId);
                $result = $stmt->execute();
                
                if ($result && $stmt->affected_rows > 0) {
                    $statusText = $status === 'active' ? 'activated' : 'marked as out of stock';
                    return ['success' => true, 'message' => "Product {$statusText} successfully"];
                } else {
                    return ['success' => false, 'message' => 'Product not found'];
                }
            } else {
                return ['success' => false, 'message' => 'Failed to prepare statement: ' . $this->db->error];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update product status: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete product (soft delete)
     */
    public function deleteProduct($productId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET is_archive = 1
                WHERE product_id = ? AND is_archive = 0
            ");
            
            if ($stmt) {
                $stmt->bind_param('i', $productId);
                $result = $stmt->execute();
                
                if ($result && $stmt->affected_rows > 0) {
                    return ['success' => true, 'message' => 'Product deleted successfully'];
                } else {
                    return ['success' => false, 'message' => 'Product not found'];
                }
            } else {
                return ['success' => false, 'message' => 'Failed to prepare statement: ' . $this->db->error];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to delete product: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get product statistics with role-based filtering
     */
    public function getProductStatistics($userRole = 'admin', $userId = null)
    {
        try {
            $statsWhere = "WHERE is_archive = 0";
            $statsParams = [];
            
            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $statsWhere .= " AND vendor_id = ?";
                    $statsParams[] = $vendorId;
                } else {
                    // No vendor profile, show zero stats
                    $statsWhere .= " AND 1 = 0";
                }
            }
            
            $result = $this->executeQuery("
                SELECT 
                    COUNT(*) as total_products,
                    SUM(CASE WHEN stock_quantity > 0 THEN 1 ELSE 0 END) as active_products,
                    SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock_products,
                    0 as featured_products
                FROM products 
                {$statsWhere}
            ", $statsParams);
            
            return $result->fetch_assoc();
            
        } catch (Exception $e) {
            return [
                'total_products' => 0,
                'active_products' => 0,
                'out_of_stock_products' => 0,
                'featured_products' => 0
            ];
        }
    }
    
    /**
     * Get all categories for dropdown
     */
    public function getCategories()
    {
        try {
            $result = $this->executeQuery("
                SELECT category_id, name, parent_category_id
                FROM product_categories 
                WHERE is_active = 1
                ORDER BY parent_category_id ASC, name ASC
            ");
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get all vendors for dropdown
     */
    public function getVendors()
    {
        try {
            $result = $this->executeQuery("
                SELECT v.vendor_id, v.business_name, u.name as contact_name
                FROM vendors v
                JOIN users u ON v.user_id = u.user_id
                WHERE v.is_archive = 0
                ORDER BY v.business_name ASC
            ");
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get single product details for editing
     */
    public function getProductById($productId, $userRole = 'admin', $userId = null)
    {
        try {
            $whereConditions = ['p.product_id = ?', 'p.is_archive = 0'];
            $params = [$productId];
            
            // Role-based filtering - vendors can only see their own products
            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $whereConditions[] = 'p.vendor_id = ?';
                    $params[] = $vendorId;
                } else {
                    // Vendor profile not found, show no products
                    $whereConditions[] = '1 = 0';
                }
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            $query = "
                SELECT 
                    p.*,
                    v.business_name as vendor_name,
                    pc.category_id,
                    pc.name as category_name,
                    CASE 
                        WHEN p.stock_quantity > 0 THEN 'active'
                        ELSE 'out_of_stock'
                    END as status
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                LEFT JOIN product_categories pc ON pc.name = p.category
                WHERE {$whereClause}
                LIMIT 1
            ";
            
            $result = $this->executeQuery($query, $params);
            $product = $result->fetch_assoc();
            
            if ($product) {
                return ['success' => true, 'product' => $product];
            } else {
                return ['success' => false, 'message' => 'Product not found or access denied'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch product: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validate product data
     */
    private function validateProductData($data)
    {
        if (empty($data['name'])) {
            return ['valid' => false, 'message' => 'Product name is required'];
        }
        
        if (empty($data['unit_price']) || $data['unit_price'] <= 0) {
            return ['valid' => false, 'message' => 'Valid unit price is required'];
        }
        
        if (empty($data['selling_price']) || $data['selling_price'] <= 0) {
            return ['valid' => false, 'message' => 'Valid selling price is required'];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Check if SKU exists (not used in current schema but kept for compatibility)
     */
    private function skuExists($sku)
    {
        // SKU not implemented in current database schema
        return false;
    }
    
    /**
     * Check if SKU exists for other products (not used in current schema but kept for compatibility)
     */
    private function skuExistsForOtherProduct($sku, $productId)
    {
        // SKU not implemented in current database schema
        return false;
    }
    
    /**
     * Create notification for successful product creation
     */
    private function createProductNotification($userId, $productName, $productId)
    {
        try {
            $message = "Product '{$productName}' has been created successfully!";
            
            $stmt = $this->db->prepare("
                INSERT INTO notifications (
                    user_id, 
                    message, 
                    is_read, 
                    type, 
                    created_at
                ) VALUES (?, ?, 0, 'alert', NOW())
            ");
            
            $stmt->bind_param('is', $userId, $message);
            
            $result = $stmt->execute();
            if ($result) {
                error_log('Notification created successfully for product: ' . $productName);
            } else {
                error_log('Failed to create notification: ' . $stmt->error);
            }
            
        } catch (Exception $e) {
            error_log('Error creating product notification: ' . $e->getMessage());
        }
    }
}

?> 