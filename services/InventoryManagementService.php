<?php

require_once __DIR__ . '/InventoryService.php';
require_once __DIR__ . '/ProductService.php';
require_once __DIR__ . '/AuthService.php';

/**
 * InventoryManagementService
 * Controller for inventory management operations
 */
class InventoryManagementService
{
    private $inventoryService;
    private $productService;
    private $authService;
    
    public function __construct()
    {
        $this->inventoryService = new InventoryService();
        $this->productService = new ProductService();
        $this->authService = new AuthService();
    }
    
    /**
     * Handle inventory management requests
     */
    public function handleRequest($postData, $userRoles)
    {
        try {
            $action = $postData['action'] ?? '';
            
            switch ($action) {
                case 'restock':
                    return $this->handleRestock($postData, $userRoles);
                    
                case 'reduce_stock':
                    return $this->handleReduceStock($postData, $userRoles);
                    
                case 'get_inventory_history':
                    return $this->handleGetInventoryHistory($postData, $userRoles);
                    
                case 'get_low_stock_products':
                    return $this->handleGetLowStockProducts($postData, $userRoles);
                    
                case 'get_out_of_stock_products':
                    return $this->handleGetOutOfStockProducts($postData, $userRoles);
                    
                case 'get_inventory_stats':
                    return $this->handleGetInventoryStats($postData, $userRoles);
                    
                case 'bulk_restock':
                    return $this->handleBulkRestock($postData, $userRoles);
                    
                case 'bulk_reduce_stock':
                    return $this->handleBulkReduceStock($postData, $userRoles);
                    
                case 'get_products_for_inventory':
                    return $this->handleGetProductsForInventory($postData, $userRoles);
                    
                case 'get_categories':
                    return $this->handleGetCategories($postData, $userRoles);
                    
                case 'refresh_notifications':
                    return $this->handleRefreshNotifications($postData, $userRoles);
                    
                default:
                    return ['success' => false, 'message' => 'Invalid action'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Action failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Handle restock request
     */
    private function handleRestock($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        $productId = (int)($postData['product_id'] ?? 0);
        $quantity = (int)($postData['quantity'] ?? 0);
        $reason = $postData['reason'] ?? 'Manual restock';
        $notes = $postData['notes'] ?? '';
        
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product ID'];
        }
        
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Quantity must be greater than 0'];
        }
        
        // Check if vendor can modify this product
        if ($userRoles['isVendor'] && !$userRoles['isAdmin']) {
            $productResult = $this->productService->getProductById($productId, 'vendor', $userRoles['user']['user_id']);
            if (!$productResult['success']) {
                return ['success' => false, 'message' => 'Product not found or access denied'];
            }
        }
        
        return $this->inventoryService->restockProduct(
            $productId, 
            $quantity, 
            $userRoles['user']['user_id'], 
            $reason, 
            $notes
        );
    }
    
    /**
     * Handle reduce stock request
     */
    private function handleReduceStock($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        $productId = (int)($postData['product_id'] ?? 0);
        $quantity = (int)($postData['quantity'] ?? 0);
        $reason = $postData['reason'] ?? 'Manual reduction';
        $notes = $postData['notes'] ?? '';
        
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product ID'];
        }
        
        if ($quantity <= 0) {
            return ['success' => false, 'message' => 'Quantity must be greater than 0'];
        }
        
        // Check if vendor can modify this product
        if ($userRoles['isVendor'] && !$userRoles['isAdmin']) {
            $productResult = $this->productService->getProductById($productId, 'vendor', $userRoles['user']['user_id']);
            if (!$productResult['success']) {
                return ['success' => false, 'message' => 'Product not found or access denied'];
            }
        }
        
        return $this->inventoryService->reduceStock(
            $productId, 
            $quantity, 
            $userRoles['user']['user_id'], 
            $reason, 
            $notes
        );
    }
    
    /**
     * Handle get inventory history request
     */
    private function handleGetInventoryHistory($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        $productId = (int)($postData['product_id'] ?? 0);
        $limit = (int)($postData['limit'] ?? 50);
        $offset = (int)($postData['offset'] ?? 0);
        
        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product ID'];
        }
        
        // Check if vendor can access this product's history
        if ($userRoles['isVendor'] && !$userRoles['isAdmin']) {
            $productResult = $this->productService->getProductById($productId, 'vendor', $userRoles['user']['user_id']);
            if (!$productResult['success']) {
                return ['success' => false, 'message' => 'Product not found or access denied'];
            }
        }
        
        $history = $this->inventoryService->getInventoryHistory($productId, $limit, $offset);
        
        return [
            'success' => true,
            'history' => $history,
            'count' => count($history)
        ];
    }
    
    /**
     * Handle get low stock products request
     */
    private function handleGetLowStockProducts($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        $threshold = (int)($postData['threshold'] ?? 10);
        $vendorId = null;
        
        // If vendor, only show their products
        if ($userRoles['isVendor'] && !$userRoles['isAdmin']) {
            $vendorId = $this->productService->getVendorIdByUserId($userRoles['user']['user_id']);
            if (!$vendorId) {
                return ['success' => false, 'message' => 'Vendor profile not found'];
            }
        }
        
        $products = $this->inventoryService->getLowStockProducts($vendorId, $threshold);
        
        return [
            'success' => true,
            'products' => $products,
            'count' => count($products),
            'threshold' => $threshold
        ];
    }
    
    /**
     * Handle get out of stock products request
     */
    private function handleGetOutOfStockProducts($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        $vendorId = null;
        
        // If vendor, only show their products
        if ($userRoles['isVendor'] && !$userRoles['isAdmin']) {
            $vendorId = $this->productService->getVendorIdByUserId($userRoles['user']['user_id']);
            if (!$vendorId) {
                return ['success' => false, 'message' => 'Vendor profile not found'];
            }
        }
        
        $products = $this->inventoryService->getOutOfStockProducts($vendorId);
        
        return [
            'success' => true,
            'products' => $products,
            'count' => count($products)
        ];
    }
    
    /**
     * Handle get inventory stats request
     */
    private function handleGetInventoryStats($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        $vendorId = null;
        
        // If vendor, only show their stats
        if ($userRoles['isVendor'] && !$userRoles['isAdmin']) {
            $vendorId = $this->productService->getVendorIdByUserId($userRoles['user']['user_id']);
            if (!$vendorId) {
                return ['success' => false, 'message' => 'Vendor profile not found'];
            }
        }
        
        $stats = $this->inventoryService->getInventoryStats($vendorId);
        
        return [
            'success' => true,
            'stats' => $stats
        ];
    }
    
    /**
     * Handle bulk restock request
     */
    private function handleBulkRestock($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        $products = $postData['products'] ?? '';
        $reason = $postData['reason'] ?? 'Bulk restock';
        
        // Decode JSON if it's a string
        if (is_string($products)) {
            $products = json_decode($products, true);
        }
        
        if (empty($products) || !is_array($products)) {
            return ['success' => false, 'message' => 'No products provided for bulk restock'];
        }
        
        // Validate products data
        foreach ($products as $product) {
            if (!isset($product['product_id']) || !isset($product['quantity'])) {
                return ['success' => false, 'message' => 'Invalid product data format'];
            }
            
            if ((int)$product['product_id'] <= 0 || (int)$product['quantity'] <= 0) {
                return ['success' => false, 'message' => 'Invalid product ID or quantity'];
            }
        }
        
        // Check if vendor can modify these products
        if ($userRoles['isVendor'] && !$userRoles['isAdmin']) {
            foreach ($products as $product) {
                $productResult = $this->productService->getProductById($product['product_id'], 'vendor', $userRoles['user']['user_id']);
                if (!$productResult['success']) {
                    return ['success' => false, 'message' => 'Access denied to one or more products'];
                }
            }
        }
        
        return $this->inventoryService->bulkRestock($products, $userRoles['user']['user_id'], $reason);
    }
    
    /**
     * Handle bulk reduce stock request
     */
    private function handleBulkReduceStock($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        $products = $postData['products'] ?? '';
        $reason = $postData['reason'] ?? 'Bulk reduction';
        
        // Decode JSON if it's a string
        if (is_string($products)) {
            $products = json_decode($products, true);
        }
        
        if (empty($products) || !is_array($products)) {
            return ['success' => false, 'message' => 'No products provided for bulk reduction'];
        }
        
        // Validate products data
        foreach ($products as $product) {
            if (!isset($product['product_id']) || !isset($product['quantity'])) {
                return ['success' => false, 'message' => 'Invalid product data format'];
            }
            
            if ((int)$product['product_id'] <= 0 || (int)$product['quantity'] <= 0) {
                return ['success' => false, 'message' => 'Invalid product ID or quantity'];
            }
        }
        
        // Check if vendor can modify these products
        if ($userRoles['isVendor'] && !$userRoles['isAdmin']) {
            foreach ($products as $product) {
                $productResult = $this->productService->getProductById($product['product_id'], 'vendor', $userRoles['user']['user_id']);
                if (!$productResult['success']) {
                    return ['success' => false, 'message' => 'Access denied to one or more products'];
                }
            }
        }
        
        return $this->inventoryService->bulkReduceStock($products, $userRoles['user']['user_id'], $reason);
    }
    
    /**
     * Handle get products for inventory management
     */
    private function handleGetProductsForInventory($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        $filters = [
            'search' => $postData['search'] ?? '',
            'category' => $postData['category'] ?? '',
            'status' => $postData['status'] ?? '',
            'page' => (int)($postData['page'] ?? 1),
            'limit' => (int)($postData['limit'] ?? 20)
        ];
        
        // If vendor, only show their products
        if ($userRoles['isVendor'] && !$userRoles['isAdmin']) {
            $vendorId = $this->productService->getVendorIdByUserId($userRoles['user']['user_id']);
            if (!$vendorId) {
                return ['success' => false, 'message' => 'Vendor profile not found'];
            }
            $filters['vendor_id'] = $vendorId;
        }
        
        $result = $this->productService->getPaginatedProducts(
            $filters['page'], 
            $filters['limit'], 
            $filters, 
            $userRoles['isVendor'] ? 'vendor' : 'admin', 
            $userRoles['user']['user_id']
        );
        
        return $result;
    }
    
    /**
     * Handle get categories request
     */
    private function handleGetCategories($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Vendor or Admin access required'];
        }
        
        try {
            $categories = $this->productService->getCategories();
            return [
                'success' => true,
                'categories' => $categories
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch categories: ' . $e->getMessage()];
        }
    }
    
    /**
     * Handle refresh notifications request
     */
    private function handleRefreshNotifications($postData, $userRoles)
    {
        try {
            require_once __DIR__ . '/NotificationService.php';
            $notificationService = new NotificationService();
            
            $unreadCount = $notificationService->getUnreadCount($userRoles['user']['user_id']);
            
            return [
                'success' => true,
                'unreadCount' => $unreadCount
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to refresh notifications: ' . $e->getMessage()];
        }
    }
}
?> 