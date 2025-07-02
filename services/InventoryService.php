<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/AuditLog.php';
require_once __DIR__ . '/NotificationService.php';

/**
 * InventoryService
 * Handles inventory management operations including restock and reduce stock
 */
class InventoryService
{
    private $db;
    private $notificationService;
    
    public function __construct()
    {
        global $conn;
        if (!$conn || $conn->connect_error) {
            throw new Exception('Database connection failed');
        }
        $this->db = $conn;
        $this->notificationService = new NotificationService();
    }
    
    /**
     * Restock a product
     */
    public function restockProduct($productId, $quantity, $userId, $reason = 'Manual restock', $notes = '')
    {
        try {
            $this->db->begin_transaction();
            
            // Get product details
            $stmt = $this->db->prepare("
                SELECT p.*, v.business_name as vendor_name, v.user_id as vendor_user_id
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE p.product_id = ? AND p.is_archive = 0
            ");
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            
            if (!$product) {
                throw new Exception('Product not found');
            }
            
            // Validate quantity
            if ($quantity <= 0) {
                throw new Exception('Restock quantity must be greater than 0');
            }
            
            if ($quantity > 999999) {
                throw new Exception('Restock quantity cannot exceed 999,999');
            }
            
            // Get current stock
            $currentStock = $product['stock_quantity'];
            $newStock = $currentStock + $quantity;
            
            // Update product stock
            $stmt = $this->db->prepare("
                UPDATE products 
                SET stock_quantity = ?
                WHERE product_id = ?
            ");
            $stmt->bind_param('ii', $newStock, $productId);
            $stmt->execute();
            
            if ($stmt->affected_rows === 0) {
                throw new Exception('Failed to update product stock');
            }
            
            // Log the restock operation
            $this->logInventoryOperation(
                $productId,
                'restock',
                $quantity,
                $currentStock,
                $newStock,
                $userId,
                $reason,
                $notes
            );
            
            // Create notification for vendor if restock was done by admin
            if ($product['vendor_user_id'] && $product['vendor_user_id'] != $userId) {
                $this->notificationService->createNotification(
                    $product['vendor_user_id'],
                    'Product Restocked',
                    "Your product '{$product['name']}' has been restocked with {$quantity} units. New stock level: {$newStock}",
                    'inventory'
                );
            }
            
            // Check for stock recovery notification
            $this->checkStockRecoveryNotification($product, $currentStock, $newStock);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => "Product restocked successfully. New stock level: {$newStock}",
                'previous_stock' => $currentStock,
                'new_stock' => $newStock,
                'restocked_quantity' => $quantity
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to restock product: ' . $e->getMessage()];
        }
    }
    
    /**
     * Reduce stock for a product
     */
    public function reduceStock($productId, $quantity, $userId, $reason = 'Manual reduction', $notes = '')
    {
        try {
            $this->db->begin_transaction();
            
            // Get product details
            $stmt = $this->db->prepare("
                SELECT p.*, v.business_name as vendor_name, v.user_id as vendor_user_id
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE p.product_id = ? AND p.is_archive = 0
            ");
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            
            if (!$product) {
                throw new Exception('Product not found');
            }
            
            // Validate quantity
            if ($quantity <= 0) {
                throw new Exception('Reduction quantity must be greater than 0');
            }
            
            if ($quantity > $product['stock_quantity']) {
                throw new Exception('Reduction quantity cannot exceed current stock level');
            }
            
            // Get current stock
            $currentStock = $product['stock_quantity'];
            $newStock = $currentStock - $quantity;
            
            // Update product stock
            $stmt = $this->db->prepare("
                UPDATE products 
                SET stock_quantity = ?
                WHERE product_id = ?
            ");
            $stmt->bind_param('ii', $newStock, $productId);
            $stmt->execute();
            
            if ($stmt->affected_rows === 0) {
                throw new Exception('Failed to update product stock');
            }
            
            // Log the stock reduction operation
            $this->logInventoryOperation(
                $productId,
                'reduce',
                $quantity,
                $currentStock,
                $newStock,
                $userId,
                $reason,
                $notes
            );
            
            // Create notification for vendor if reduction was done by admin
            if ($product['vendor_user_id'] && $product['vendor_user_id'] != $userId) {
                $this->notificationService->createNotification(
                    $product['vendor_user_id'],
                    'Stock Reduced',
                    "Stock for your product '{$product['name']}' has been reduced by {$quantity} units. New stock level: {$newStock}",
                    'inventory'
                );
            }
            
            // Check for low stock notification
            $this->checkLowStockNotification($product, $currentStock, $newStock);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => "Stock reduced successfully. New stock level: {$newStock}",
                'previous_stock' => $currentStock,
                'new_stock' => $newStock,
                'reduced_quantity' => $quantity
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to reduce stock: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get inventory history for a product
     */
    public function getInventoryHistory($productId, $limit = 50, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    al.*,
                    u.name as user_name,
                    u.email as user_email
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.user_id
                WHERE al.table_name = 'products' 
                AND al.record_id = ?
                AND al.action IN ('restock', 'reduce')
                ORDER BY al.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->bind_param('iii', $productId, $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $history = $result->fetch_all(MYSQLI_ASSOC);
            
            // Process the history to extract details from JSON
            foreach ($history as &$item) {
                $oldValues = json_decode($item['old_values'], true) ?: [];
                $newValues = json_decode($item['new_values'], true) ?: [];
                
                $item['previous_stock'] = $oldValues['stock_quantity'] ?? 0;
                $item['new_stock'] = $newValues['stock_quantity'] ?? 0;
                $item['quantity_changed'] = $newValues['quantity_changed'] ?? 0;
                $item['reason'] = $newValues['reason'] ?? 'N/A';
                $item['notes'] = $newValues['notes'] ?? '';
            }
            
            return $history;
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get low stock products for a vendor
     */
    public function getLowStockProducts($vendorId = null, $threshold = 10)
    {
        try {
            $whereClause = "WHERE p.is_archive = 0 AND p.stock_quantity <= ?";
            $params = [$threshold];
            $types = 'i';
            
            if ($vendorId) {
                $whereClause .= " AND p.vendor_id = ?";
                $params[] = $vendorId;
                $types .= 'i';
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    v.business_name as vendor_name
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                {$whereClause}
                ORDER BY p.stock_quantity ASC
            ");
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get out of stock products for a vendor
     */
    public function getOutOfStockProducts($vendorId = null)
    {
        try {
            $whereClause = "WHERE p.is_archive = 0 AND p.stock_quantity = 0";
            $params = [];
            $types = '';
            
            if ($vendorId) {
                $whereClause .= " AND p.vendor_id = ?";
                $params[] = $vendorId;
                $types = 'i';
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    v.business_name as vendor_name
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                {$whereClause}
                ORDER BY p.name ASC
            ");
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_all(MYSQLI_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get inventory statistics
     */
    public function getInventoryStats($vendorId = null)
    {
        try {
            $whereClause = "WHERE p.is_archive = 0";
            $params = [];
            $types = '';
            
            if ($vendorId) {
                $whereClause .= " AND p.vendor_id = ?";
                $params[] = $vendorId;
                $types = 'i';
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_products,
                    SUM(CASE WHEN stock_quantity > 0 THEN 1 ELSE 0 END) as in_stock_products,
                    SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock_products,
                    SUM(CASE WHEN stock_quantity <= 10 AND stock_quantity > 0 THEN 1 ELSE 0 END) as low_stock_products,
                    SUM(stock_quantity) as total_stock_quantity,
                    AVG(stock_quantity) as avg_stock_quantity
                FROM products p
                {$whereClause}
            ");
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            
            return $result->fetch_assoc();
            
        } catch (Exception $e) {
            return [
                'total_products' => 0,
                'in_stock_products' => 0,
                'out_of_stock_products' => 0,
                'low_stock_products' => 0,
                'total_stock_quantity' => 0,
                'avg_stock_quantity' => 0
            ];
        }
    }
    
    /**
     * Log inventory operation to audit log
     */
    private function logInventoryOperation($productId, $action, $quantity, $previousStock, $newStock, $userId, $reason, $notes)
    {
        try {
            $oldValues = json_encode([
                'stock_quantity' => $previousStock
            ]);
            
            $newValues = json_encode([
                'stock_quantity' => $newStock,
                'quantity_changed' => $quantity,
                'reason' => $reason,
                'notes' => $notes
            ]);
            
            $stmt = $this->db->prepare("
                INSERT INTO audit_logs (
                    table_name, record_id, action, user_id, old_values, new_values, ip_address, user_agent
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            $tableName = 'products';
            $stmt->bind_param('sissssss', 
                $tableName, $productId, $action, $userId, $oldValues, $newValues, $ipAddress, $userAgent
            );
            $stmt->execute();
            
        } catch (Exception $e) {
            // Log error but don't fail the main operation
            error_log('Failed to log inventory operation: ' . $e->getMessage());
        }
    }
    
    /**
     * Bulk restock multiple products
     */
    public function bulkRestock($products, $userId, $reason = 'Bulk restock')
    {
        try {
            $this->db->begin_transaction();
            
            $results = [];
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($products as $productData) {
                $productId = $productData['product_id'];
                $quantity = $productData['quantity'];
                $notes = $productData['notes'] ?? '';
                
                $result = $this->restockProduct($productId, $quantity, $userId, $reason, $notes);
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                
                $results[] = [
                    'product_id' => $productId,
                    'result' => $result
                ];
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => "Bulk restock completed. Success: {$successCount}, Errors: {$errorCount}",
                'results' => $results,
                'success_count' => $successCount,
                'error_count' => $errorCount
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Bulk restock failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Bulk reduce stock for multiple products
     */
    public function bulkReduceStock($products, $userId, $reason = 'Bulk reduction')
    {
        try {
            $this->db->begin_transaction();
            
            $results = [];
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($products as $productData) {
                $productId = $productData['product_id'];
                $quantity = $productData['quantity'];
                $notes = $productData['notes'] ?? '';
                
                $result = $this->reduceStock($productId, $quantity, $userId, $reason, $notes);
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                
                $results[] = [
                    'product_id' => $productId,
                    'result' => $result
                ];
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => "Bulk reduction completed. Success: {$successCount}, Errors: {$errorCount}",
                'results' => $results,
                'success_count' => $successCount,
                'error_count' => $errorCount
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Bulk reduction failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Check if stock has recovered from low stock and send notification
     */
    private function checkStockRecoveryNotification($product, $previousStock, $newStock)
    {
        try {
            $lowStockThreshold = 10; // Default threshold
            
            // Check if stock has recovered from low stock
            $wasBelowThreshold = $previousStock <= $lowStockThreshold && $previousStock > 0;
            $isNowAboveThreshold = $newStock > $lowStockThreshold;
            
            if ($wasBelowThreshold && $isNowAboveThreshold) {
                // Stock has recovered from low stock - send notification
                $this->sendStockRecoveryNotification($product, $newStock);
            }
            
        } catch (Exception $e) {
            error_log('Error checking stock recovery notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if stock has become low and send notification
     */
    private function checkLowStockNotification($product, $previousStock, $newStock)
    {
        try {
            $lowStockThreshold = 10; // Default threshold
            
            // Check if stock has crossed the low stock threshold
            $wasAboveThreshold = $previousStock > $lowStockThreshold;
            $isNowBelowThreshold = $newStock <= $lowStockThreshold && $newStock > 0;
            
            if ($wasAboveThreshold && $isNowBelowThreshold) {
                // Stock has just become low - send notification
                $this->sendLowStockNotification($product, $newStock, $lowStockThreshold);
            } elseif ($newStock == 0) {
                // Stock has become zero - send out of stock notification
                $this->sendOutOfStockNotification($product);
            }
            
        } catch (Exception $e) {
            error_log('Error checking low stock notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send low stock notification
     */
    private function sendLowStockNotification($product, $currentStock, $threshold)
    {
        try {
            // Notify vendor
            if ($product['vendor_user_id']) {
                $this->notificationService->createNotification(
                    $product['vendor_user_id'],
                    'Low Stock Alert',
                    "Your product '{$product['name']}' is running low on stock. Current stock: {$currentStock} (Threshold: {$threshold})",
                    'inventory'
                );
            }
            
            // Notify admin users
            $this->notifyAdminsLowStock($product, $currentStock, $threshold);
            
        } catch (Exception $e) {
            error_log('Error sending low stock notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send stock recovery notification
     */
    private function sendStockRecoveryNotification($product, $currentStock)
    {
        try {
            // Notify vendor
            if ($product['vendor_user_id']) {
                $this->notificationService->createNotification(
                    $product['vendor_user_id'],
                    'Stock Recovered',
                    "Your product '{$product['name']}' stock has recovered. Current stock: {$currentStock}",
                    'inventory'
                );
            }
            
            // Notify admin users
            $this->notifyAdminsStockRecovery($product, $currentStock);
            
        } catch (Exception $e) {
            error_log('Error sending stock recovery notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send out of stock notification
     */
    private function sendOutOfStockNotification($product)
    {
        try {
            // Notify vendor
            if ($product['vendor_user_id']) {
                $this->notificationService->createNotification(
                    $product['vendor_user_id'],
                    'Out of Stock Alert',
                    "Your product '{$product['name']}' is now out of stock. Please restock immediately.",
                    'inventory'
                );
            }
            
            // Notify admin users
            $this->notifyAdminsOutOfStock($product);
            
        } catch (Exception $e) {
            error_log('Error sending out of stock notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Notify admin users about low stock
     */
    private function notifyAdminsLowStock($product, $currentStock, $threshold)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT u.user_id, u.name, u.email
                FROM users u
                JOIN user_roles ur ON u.user_id = ur.user_id
                JOIN roles r ON ur.role_id = r.role_id
                WHERE r.role_name = 'admin' 
                AND u.is_active = 1 
                AND ur.is_active = 1
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            $admins = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach ($admins as $admin) {
                $this->notificationService->createNotification(
                    $admin['user_id'],
                    'Low Stock Alert - Admin',
                    "Product '{$product['name']}' (Vendor: {$product['vendor_name']}) is running low on stock. Current stock: {$currentStock} (Threshold: {$threshold})",
                    'inventory'
                );
            }
            
        } catch (Exception $e) {
            error_log('Error notifying admins about low stock: ' . $e->getMessage());
        }
    }
    
    /**
     * Notify admin users about stock recovery
     */
    private function notifyAdminsStockRecovery($product, $currentStock)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT u.user_id, u.name, u.email
                FROM users u
                JOIN user_roles ur ON u.user_id = ur.user_id
                JOIN roles r ON ur.role_id = r.role_id
                WHERE r.role_name = 'admin' 
                AND u.is_active = 1 
                AND ur.is_active = 1
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            $admins = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach ($admins as $admin) {
                $this->notificationService->createNotification(
                    $admin['user_id'],
                    'Stock Recovered - Admin',
                    "Product '{$product['name']}' (Vendor: {$product['vendor_name']}) stock has recovered. Current stock: {$currentStock}",
                    'inventory'
                );
            }
            
        } catch (Exception $e) {
            error_log('Error notifying admins about stock recovery: ' . $e->getMessage());
        }
    }
    
    /**
     * Notify admin users about out of stock
     */
    private function notifyAdminsOutOfStock($product)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT u.user_id, u.name, u.email
                FROM users u
                JOIN user_roles ur ON u.user_id = ur.user_id
                JOIN roles r ON ur.role_id = r.role_id
                WHERE r.role_name = 'admin' 
                AND u.is_active = 1 
                AND ur.is_active = 1
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            $admins = $result->fetch_all(MYSQLI_ASSOC);
            
            foreach ($admins as $admin) {
                $this->notificationService->createNotification(
                    $admin['user_id'],
                    'Out of Stock Alert - Admin',
                    "Product '{$product['name']}' (Vendor: {$product['vendor_name']}) is now out of stock. Vendor needs to restock immediately.",
                    'inventory'
                );
            }
            
        } catch (Exception $e) {
            error_log('Error notifying admins about out of stock: ' . $e->getMessage());
        }
    }
}
?> 