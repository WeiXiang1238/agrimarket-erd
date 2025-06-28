<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/OrderService.php';
require_once __DIR__ . '/AuthService.php';

/**
 * OrderManagementService
 * Handles order management operations and data processing for the frontend
 */
class OrderManagementService
{
    private $db;
    private $orderService;
    private $authService;
    
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
        
        $this->orderService = new OrderService();
        $this->authService = new AuthService();
    }

    /**
     * Initialize order management page data
     */
    public function initializeOrderManagement()
    {
        // Get authenticated user with roles
        $userRoles = $this->authService->getCurrentUserWithRoles();
        
        if (!$userRoles) {
            return [
                'success' => false,
                'redirect' => '/agrimarket-erd/v1/auth/login/',
                'message' => 'Authentication required'
            ];
        }

        return [
            'success' => true,
            'userRoles' => $userRoles,
            'pageTitle' => $userRoles['isCustomer'] ? 'My Orders' : 'Order Management'
        ];
    }

    /**
     * Get orders based on user role and filters
     */
    public function getOrdersForUser($userRoles, $page = 1, $status = null, $limit = 10)
    {
        try {
            error_log("OrderManagementService: Getting orders for user roles: " . print_r($userRoles, true));
            
            if ($userRoles['isCustomer']) {
                if (!$userRoles['customerId']) {
                    throw new Exception('Customer profile not found');
                }
                error_log("OrderManagementService: Fetching customer orders for customer_id: " . $userRoles['customerId']);
                return $this->orderService->getCustomerOrderHistory(
                    $userRoles['customerId'], 
                    $page, 
                    $limit, 
                    $status
                );
            } 
            
            if ($userRoles['isVendor']) {
                if (!$userRoles['vendorId']) {
                    throw new Exception('Vendor profile not found');
                }
                error_log("OrderManagementService: Fetching vendor orders for vendor_id: " . $userRoles['vendorId']);
                return $this->orderService->getVendorOrders(
                    $userRoles['vendorId'], 
                    $page, 
                    $limit, 
                    $status
                );
            }
            
            if ($userRoles['isAdmin']) {
                error_log("OrderManagementService: Fetching ALL orders for admin");
                $result = $this->orderService->getAllOrders($page, $limit, $status);
                error_log("OrderManagementService: Admin orders result: " . print_r($result, true));
                return $result;
            }

            error_log("OrderManagementService: No matching role found, returning empty result");
            return ['success' => true, 'orders' => [], 'total' => 0, 'totalPages' => 1];
            
        } catch (Exception $e) {
            error_log("OrderManagementService: Exception in getOrdersForUser: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'Failed to fetch orders: ' . $e->getMessage(),
                'orders' => [],
                'total' => 0,
                'totalPages' => 1
            ];
        }
    }

    /**
     * Handle AJAX order management actions
     */
    public function handleOrderAction($action, $postData, $userRoles)
    {
        try {
            switch ($action) {
                case 'update_status':
                    return $this->handleUpdateStatus($postData, $userRoles);
                    
                case 'cancel_order':
                    return $this->handleCancelOrder($postData, $userRoles);
                    
                case 'reorder':
                    return $this->handleReorder($postData, $userRoles);
                    
                case 'track_order':
                    return $this->handleTrackOrder($postData, $userRoles);
                    
                case 'get_order_details':
                    return $this->handleGetOrderDetails($postData, $userRoles);
                    
                case 'get_order_analytics':
                    return $this->handleGetOrderAnalytics($postData, $userRoles);
                    
                case 'admin_cancel_order':
                    return $this->handleAdminCancelOrder($postData, $userRoles);
                    
                case 'bulk_update_status':
                    return $this->handleBulkUpdateStatus($postData, $userRoles);
                    
                case 'export_orders':
                    return $this->handleExportOrders($postData, $userRoles);
                    
                case 'generate_report':
                    return $this->handleGenerateReport($postData, $userRoles);
                    
                default:
                    return ['success' => false, 'message' => 'Invalid action'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Action failed: ' . $e->getMessage()];
        }
    }

    private function handleUpdateStatus($postData, $userRoles)
    {
        if (!$userRoles['isVendor'] && !$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $orderId = (int)($postData['order_id'] ?? 0);
        $status = $postData['status'] ?? '';
        $trackingNumber = $postData['tracking_number'] ?? null;
        $deliveryDate = $postData['delivery_date'] ?? null;
        // Notes removed since database doesn't have this column

        return $this->orderService->updateOrderStatus(
            $orderId, 
            $status, 
            $trackingNumber, 
            $deliveryDate
        );
    }

    private function handleCancelOrder($postData, $userRoles)
    {
        if (!$userRoles['isCustomer']) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $orderId = (int)($postData['order_id'] ?? 0);
        $reason = $postData['reason'] ?? 'Customer request';

        return $this->orderService->cancelOrder($orderId, $userRoles['customerId'], $reason);
    }

    private function handleReorder($postData, $userRoles)
    {
        if (!$userRoles['isCustomer']) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $orderId = (int)($postData['order_id'] ?? 0);

        return $this->orderService->reorder($userRoles['customerId'], $orderId);
    }

    private function handleTrackOrder($postData, $userRoles)
    {
        $orderId = (int)($postData['order_id'] ?? 0);
        $customerFilter = $userRoles['isCustomer'] ? $userRoles['customerId'] : null;

        return $this->orderService->trackOrder($orderId, $customerFilter);
    }

    private function handleGetOrderDetails($postData, $userRoles)
    {
        $orderId = (int)($postData['order_id'] ?? 0);
        $customerFilter = $userRoles['isCustomer'] ? $userRoles['customerId'] : null;

        return $this->orderService->getOrderDetails($orderId, $customerFilter);
    }

    /**
     * Get order status options for filtering
     */
    public function getOrderStatusOptions()
    {
        return [
            '' => 'All Statuses',
            'Pending' => 'Pending',
            'Confirmed' => 'Confirmed', 
            'Processing' => 'Processing',
            'Shipped' => 'Shipped',
            'Delivered' => 'Delivered',
            'Cancelled' => 'Cancelled'
        ];
    }

    /**
     * Validate and sanitize pagination parameters
     */
    public function validatePaginationParams($page, $status)
    {
        $page = max(1, (int)($page ?? 1));
        $status = in_array($status, array_keys($this->getOrderStatusOptions())) ? $status : '';
        
        return ['page' => $page, 'status' => $status];
    }

    /**
     * Get vendors for admin filter dropdown
     */
    public function getVendorsForFilter()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT v.vendor_id, v.business_name 
                FROM vendors v 
                INNER JOIN orders o ON v.vendor_id = o.vendor_id 
                WHERE v.is_archive = 0 
                ORDER BY v.business_name
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error fetching vendors for filter: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Handle order analytics request (admin/vendor only)
     */
    private function handleGetOrderAnalytics($postData, $userRoles)
    {
        if (!$userRoles['isAdmin'] && !$userRoles['isVendor']) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $orderId = (int)($postData['order_id'] ?? 0);
        
        if (!$orderId) {
            return ['success' => false, 'message' => 'Invalid order ID'];
        }

        try {
            // Get order details first
            $orderResult = $this->orderService->getOrderDetails($orderId);
            if (!$orderResult['success']) {
                return $orderResult;
            }

            $order = $orderResult['order'];
            
            // Check vendor access
            if ($userRoles['isVendor'] && $order['vendor_id'] != $userRoles['vendorId']) {
                return ['success' => false, 'message' => 'Unauthorized access to this order'];
            }

            // Generate analytics data
            $analytics = $this->generateOrderAnalytics($order);
            
            return [
                'success' => true,
                'analytics' => $analytics
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to generate analytics: ' . $e->getMessage()];
        }
    }

    /**
     * Handle admin cancel order request (admin only)
     */
    private function handleAdminCancelOrder($postData, $userRoles)
    {
        if (!$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Admin access required'];
        }

        $orderId = (int)($postData['order_id'] ?? 0);
        $cancelReason = $postData['cancel_reason'] ?? '';
        $adminNotes = $postData['admin_notes'] ?? '';
        $notifyCustomer = isset($postData['notify_customer']);
        $notifyVendor = isset($postData['notify_vendor']);

        if (!$orderId || !$cancelReason) {
            return ['success' => false, 'message' => 'Order ID and cancellation reason are required'];
        }

        try {
            $this->db->beginTransaction();

            // Get order details first
            $orderResult = $this->orderService->getOrderDetails($orderId);
            if (!$orderResult['success']) {
                $this->db->rollBack();
                return $orderResult;
            }

            $order = $orderResult['order'];

            // Check if order can be cancelled
            if ($order['status'] === 'Cancelled') {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Order is already cancelled'];
            }

            if ($order['status'] === 'Delivered') {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Cannot cancel delivered orders'];
            }

            // Prepare cancellation reason with admin notes
            $fullReason = "Admin Cancellation: " . $cancelReason;
            if ($adminNotes) {
                $fullReason .= " | Notes: " . $adminNotes;
            }

            // Update order status to cancelled
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET status = 'Cancelled', cancel_reason = ?
                WHERE order_id = ? AND is_archive = 0
            ");
            $stmt->execute([$fullReason, $orderId]);

            if ($stmt->rowCount() === 0) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Order not found or could not be updated'];
            }

            // Restore product stock
            $stmt = $this->db->prepare("
                SELECT oi.product_id, oi.quantity
                FROM order_items oi
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll();

            foreach ($items as $item) {
                $stmt = $this->db->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity + ?
                    WHERE product_id = ?
                ");
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }

            // TODO: Send notifications if requested
            if ($notifyCustomer) {
                $this->sendCancellationNotification($order, 'customer', $cancelReason);
            }
            
            if ($notifyVendor) {
                $this->sendCancellationNotification($order, 'vendor', $cancelReason);
            }

            $this->db->commit();

            return [
                'success' => true, 
                'message' => 'Order cancelled successfully by admin',
                'order_id' => $orderId
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Failed to cancel order: ' . $e->getMessage()];
        }
    }

    /**
     * Generate analytics data for an order
     */
    private function generateOrderAnalytics($order)
    {
        $analytics = [
            'order_id' => $order['order_id'],
            'order_summary' => [
                'status' => $order['status'],
                'total_amount' => $order['final_amount'],
                'items_count' => count($order['items'] ?? []),
                'order_date' => $order['order_date']
            ]
        ];

        // Calculate processing time
        $orderDate = new DateTime($order['order_date']);
        $currentDate = new DateTime();
        $daysDiff = $currentDate->diff($orderDate)->days;
        
        $analytics['processing_time'] = $daysDiff . ' days since placed';

        // Order timeline
        $analytics['timeline'] = $this->generateOrderTimeline($order);

        // Customer info (if available)
        if (isset($order['customer_name'])) {
            $analytics['customer_info'] = [
                'name' => $order['customer_name'],
                'email' => $order['customer_email'] ?? 'N/A'
            ];
        }

        // Vendor info
        if (isset($order['vendor_name'])) {
            $analytics['vendor_info'] = [
                'name' => $order['vendor_name'],
                'contact' => $order['vendor_phone'] ?? 'N/A'
            ];
        }

        // Calculate revenue breakdown
        $analytics['revenue_breakdown'] = [
            'subtotal' => $order['total_amount'] ?? 0,
            'shipping' => $order['shipping_fee'] ?? 0,
            'final_amount' => $order['final_amount'] ?? 0
        ];

        // Order performance metrics
        $analytics['performance'] = [
            'customer_rating' => 'N/A', // Would come from reviews table
            'vendor_response_time' => $this->calculateVendorResponseTime($order),
            'delivery_performance' => $this->calculateDeliveryPerformance($order)
        ];

        return $analytics;
    }

    /**
     * Generate order timeline for analytics
     */
    private function generateOrderTimeline($order)
    {
        $timeline = [];
        
        // Order placed
        $timeline[] = [
            'date' => date('M j, Y g:i A', strtotime($order['order_date'])),
            'event' => 'Order Placed',
            'status' => 'completed'
        ];

        // Status-based timeline
        $statuses = ['Confirmed', 'Processing', 'Shipped', 'Delivered'];
        $currentStatus = $order['status'];
        
        foreach ($statuses as $status) {
            $isCompleted = $this->isStatusCompleted($currentStatus, $status);
            $timeline[] = [
                'date' => $isCompleted ? 'Completed' : 'Pending',
                'event' => $status,
                'status' => $isCompleted ? 'completed' : 'pending'
            ];
        }

        return $timeline;
    }

    /**
     * Check if a status has been completed
     */
    private function isStatusCompleted($currentStatus, $checkStatus)
    {
        $statusOrder = ['Pending', 'Confirmed', 'Processing', 'Shipped', 'Delivered'];
        $currentIndex = array_search($currentStatus, $statusOrder);
        $checkIndex = array_search($checkStatus, $statusOrder);
        
        return $currentIndex !== false && $checkIndex !== false && $currentIndex >= $checkIndex;
    }

    /**
     * Calculate vendor response time (placeholder)
     */
    private function calculateVendorResponseTime($order)
    {
        // This would calculate actual response time based on order status changes
        // For now, return a placeholder
        return 'Within 24 hours';
    }

    /**
     * Calculate delivery performance (placeholder)
     */
    private function calculateDeliveryPerformance($order)
    {
        if ($order['status'] === 'Delivered' && isset($order['delivered_at'])) {
            return 'On time';
        }
        return 'In progress';
    }

    /**
     * Send cancellation notification (placeholder)
     */
    private function sendCancellationNotification($order, $recipient, $reason)
    {
        // TODO: Implement actual notification system
        // This would integrate with NotificationService
        error_log("Cancellation notification sent to $recipient for order {$order['order_id']}: $reason");
    }

    /**
     * Handle bulk update status request (admin only)
     */
    private function handleBulkUpdateStatus($postData, $userRoles)
    {
        if (!$userRoles['isAdmin']) {
            return ['success' => false, 'message' => 'Unauthorized - Admin access required'];
        }

        $orderIds = $postData['order_ids'] ?? '';
        $status = $postData['bulk_status'] ?? '';
        $notes = $postData['bulk_notes'] ?? '';

        if (!$orderIds || !$status) {
            return ['success' => false, 'message' => 'Order IDs and status are required'];
        }

        // Parse order IDs
        $orderIdArray = array_map('intval', explode(',', $orderIds));
        $orderIdArray = array_filter($orderIdArray); // Remove invalid IDs

        if (empty($orderIdArray)) {
            return ['success' => false, 'message' => 'No valid order IDs provided'];
        }

        try {
            $this->db->beginTransaction();
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($orderIdArray as $orderId) {
                $result = $this->orderService->updateOrderStatus($orderId, $status);
                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Order #$orderId: " . $result['message'];
                }
            }

            $this->db->commit();

            $message = "$successCount orders updated successfully";
            if ($errorCount > 0) {
                $message .= ", $errorCount failed";
            }

            return [
                'success' => true,
                'message' => $message,
                'updated_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Bulk update failed: ' . $e->getMessage()];
        }
    }

    /**
     * Handle export orders request (admin/vendor)
     */
    private function handleExportOrders($postData, $userRoles)
    {
        if (!$userRoles['isAdmin'] && !$userRoles['isVendor']) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $orderIds = $postData['order_ids'] ?? '';
        
        try {
            // If specific order IDs provided, use them; otherwise export all accessible orders
            if ($orderIds) {
                $orderIdArray = array_map('intval', explode(',', $orderIds));
                $orders = $this->getOrdersByIds($orderIdArray, $userRoles);
            } else {
                // Export all orders based on user role
                $ordersResult = $this->getOrdersForUser($userRoles, 1, null, 1000); // Get up to 1000 orders
                $orders = $ordersResult['orders'] ?? [];
            }

            if (empty($orders)) {
                return ['success' => false, 'message' => 'No orders found to export'];
            }

            // Generate CSV content
            $csvContent = $this->generateOrdersCSV($orders);
            
            // Set headers for CSV download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="orders_export_' . date('Y-m-d_H-i-s') . '.csv"');
            header('Content-Length: ' . strlen($csvContent));
            
            echo $csvContent;
            exit;

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Export failed: ' . $e->getMessage()];
        }
    }

    /**
     * Handle generate report request (admin/vendor)
     */
    private function handleGenerateReport($postData, $userRoles)
    {
        if (!$userRoles['isAdmin'] && !$userRoles['isVendor']) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        $orderIds = $postData['order_ids'] ?? '';
        
        try {
            // Parse order IDs
            $orderIdArray = array_map('intval', explode(',', $orderIds));
            $orders = $this->getOrdersByIds($orderIdArray, $userRoles);

            if (empty($orders)) {
                return ['success' => false, 'message' => 'No orders found for report'];
            }

            // Generate report data
            $report = $this->generateOrderReport($orders);
            
            return [
                'success' => true,
                'message' => 'Report generated successfully',
                'report' => $report
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Report generation failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get orders by specific IDs (with role-based access control)
     */
    private function getOrdersByIds($orderIds, $userRoles)
    {
        if (empty($orderIds)) {
            return [];
        }

        $placeholders = str_repeat('?,', count($orderIds) - 1) . '?';
        $whereConditions = ["o.order_id IN ($placeholders)", 'o.is_archive = 0'];
        $params = $orderIds;

        // Add role-based filters
        if ($userRoles['isVendor']) {
            $whereConditions[] = 'o.vendor_id = ?';
            $params[] = $userRoles['vendorId'];
        }

        $whereClause = implode(' AND ', $whereConditions);

        $stmt = $this->db->prepare("
            SELECT 
                o.*,
                v.business_name as vendor_name,
                c.customer_id,
                u.name as customer_name,
                u.email as customer_email,
                COUNT(oi.order_item_id) as item_count,
                GROUP_CONCAT(
                    CONCAT(p.name, ' (x', oi.quantity, ')') 
                    SEPARATOR ', '
                ) as items_summary
            FROM orders o
            LEFT JOIN vendors v ON o.vendor_id = v.vendor_id
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            LEFT JOIN users u ON c.user_id = u.user_id
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            LEFT JOIN products p ON oi.product_id = p.product_id
            WHERE $whereClause
            GROUP BY o.order_id
            ORDER BY o.order_date DESC
        ");

        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Generate CSV content for orders
     */
    private function generateOrdersCSV($orders)
    {
        $csv = [];
        
        // CSV Headers
        $csv[] = [
            'Order ID',
            'Order Date',
            'Customer Name',
            'Customer Email', 
            'Vendor Name',
            'Status',
            'Payment Status',
            'Total Amount',
            'Shipping Fee',
            'Final Amount',
            'Items Summary',
            'Tracking Number'
        ];

        // CSV Data
        foreach ($orders as $order) {
            $csv[] = [
                $order['order_id'],
                $order['order_date'],
                $order['customer_name'] ?? 'N/A',
                $order['customer_email'] ?? 'N/A',
                $order['vendor_name'] ?? 'N/A',
                $order['status'],
                $order['payment_status'] ?? 'N/A',
                $order['total_amount'],
                $order['shipping_fee'] ?? '0.00',
                $order['final_amount'],
                $order['items_summary'] ?? 'N/A',
                $order['tracking_number'] ?? 'N/A'
            ];
        }

        // Convert to CSV string
        $output = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Generate order report
     */
    private function generateOrderReport($orders)
    {
        $report = [
            'summary' => [
                'total_orders' => count($orders),
                'total_revenue' => 0,
                'average_order_value' => 0,
                'status_breakdown' => []
            ],
            'details' => []
        ];

        $statusCounts = [];
        $totalRevenue = 0;

        foreach ($orders as $order) {
            // Calculate totals
            $totalRevenue += floatval($order['final_amount']);
            
            // Count statuses
            $status = $order['status'];
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
            
            // Add to details
            $report['details'][] = [
                'order_id' => $order['order_id'],
                'date' => $order['order_date'],
                'customer' => $order['customer_name'] ?? 'N/A',
                'vendor' => $order['vendor_name'] ?? 'N/A',
                'status' => $order['status'],
                'amount' => $order['final_amount'],
                'items' => $order['item_count'] ?? 0
            ];
        }

        $report['summary']['total_revenue'] = $totalRevenue;
        $report['summary']['average_order_value'] = count($orders) > 0 ? $totalRevenue / count($orders) : 0;
        $report['summary']['status_breakdown'] = $statusCounts;

        return $report;
    }
}

?> 