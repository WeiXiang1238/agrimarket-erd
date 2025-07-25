<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/NotificationService.php';
require_once __DIR__ . '/InventoryService.php';

/**
 * OrderService
 * Handles order management, tracking, customer order history, and reordering features
 */
class OrderService
{
    private $db;
    private $notificationService;
    private $inventoryService;
    
    public function __construct()
    {
        global $conn;
        if (!$conn || $conn->connect_error) {
            throw new Exception('Database connection failed');
        }
        $this->db = $conn;
        
        $this->notificationService = new NotificationService();
        $this->inventoryService = new InventoryService();
    }
    
    /**
     * Create new order from shopping cart
     */
    public function createOrderFromCart($customerId, $shippingAddress, $billingAddress = null, $paymentMethodId = null, $notes = null)
    {
        try {
            $this->db->begin_transaction();
            
            // Get customer cart items
            $cartItems = $this->getCartItems($customerId);
            if (empty($cartItems)) {
                throw new Exception('Shopping cart is empty');
            }
            
            // Group cart items by vendor
            $ordersByVendor = [];
            foreach ($cartItems as $item) {
                $vendorId = $item['vendor_id'];
                if (!isset($ordersByVendor[$vendorId])) {
                    $ordersByVendor[$vendorId] = [];
                }
                $ordersByVendor[$vendorId][] = $item;
            }
            
            $createdOrders = [];
            
            // Create separate orders for each vendor
            foreach ($ordersByVendor as $vendorId => $items) {
                $orderNumber = $this->generateOrderNumber();
                $totalAmount = 0;
                $shippingCost = 10.00; // Default shipping cost
                $taxAmount = 0;
                
                // Calculate totals
                foreach ($items as $item) {
                    $subtotal = $item['selling_price'] * $item['quantity'];
                    $totalAmount += $subtotal;
                    $taxAmount += $subtotal * 0.06; // 6% tax
                }
                
                $finalAmount = $totalAmount + $shippingCost + $taxAmount;
                
                // Create order - simplified for existing database structure
                $stmt = $this->db->prepare("
                    INSERT INTO orders (
                        customer_id, vendor_id, order_date, status, total_amount,
                        shipping_fee, final_amount, payment_status
                    ) VALUES (?, ?, NOW(), 'Pending', ?, ?, ?, 'Unpaid')
                ");
                
                $stmt->bind_param('idddd', $customerId, $vendorId, $totalAmount, $shippingCost, $finalAmount);
                $stmt->execute();
                
                $orderId = $this->db->insert_id;
                
                // Add order items
                foreach ($items as $item) {
                    $subtotal = $item['selling_price'] * $item['quantity'];
                    
                    $stmt = $this->db->prepare("
                        INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, subtotal)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->bind_param('iiddd', $orderId, $item['product_id'], $item['quantity'], 
                        $item['selling_price'], $subtotal);
                    $stmt->execute();
                    
                    // Update product stock
                    $this->updateProductStock($item['product_id'], $item['quantity']);
                }
                
                $createdOrders[] = [
                    'order_id' => $orderId,
                    'order_number' => 'ORD-' . $orderId,
                    'vendor_id' => $vendorId,
                    'total_amount' => $finalAmount
                ];
            }
            
            // Clear shopping cart
            $this->clearCart($customerId);
            
            $this->db->commit();
            
            // Create notifications for customer and vendors
            try {
                // Get customer user ID for notification
                $stmt = $this->db->prepare("SELECT user_id FROM customers WHERE customer_id = ?");
                $stmt->bind_param('i', $customerId);
                $stmt->execute();
                $result = $stmt->get_result();
                $customerData = $result->fetch_assoc();
                $customerUserId = $customerData ? $customerData['user_id'] : null;
                
                // Create notification for customer
                if ($customerUserId) {
                    $this->notificationService->createNotification(
                        $customerUserId, 
                        'Order Placed Successfully', 
                        'Your order has been placed successfully. You will receive updates on your order status.',
                        'order'
                    );
                }
                
                // Create notifications for vendors
                foreach ($createdOrders as $order) {
                    $vendorId = $order['vendor_id'];
                    
                    // Get vendor user ID
                    $stmt = $this->db->prepare("SELECT user_id FROM vendors WHERE vendor_id = ?");
                    $stmt->bind_param('i', $vendorId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $vendorData = $result->fetch_assoc();
                    $vendorUserId = $vendorData ? $vendorData['user_id'] : null;
                    
                    if ($vendorUserId) {
                        $this->notificationService->createNotification(
                            $vendorUserId,
                            'New Order Received',
                            'You have received a new order #' . $order['order_id'] . ' with total amount $' . number_format($order['total_amount'], 2),
                            'order'
                        );
                    }
                }
            } catch (Exception $e) {
                error_log('Error creating order notifications: ' . $e->getMessage());
                // Don't fail the order creation if notifications fail
            }
            
            return [
                'success' => true,
                'message' => 'Orders created successfully',
                'orders' => $createdOrders
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to create order: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get customer order history with pagination
     */
    public function getCustomerOrderHistory($customerId, $page = 1, $limit = 10, $status = null)
    {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = ['o.customer_id = ?', 'o.is_archive = 0'];
            $params = [$customerId];
            
            if ($status) {
                $whereConditions[] = 'o.status = ?';
                $params[] = $status;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            // Get total count
            $countStmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM orders o 
                WHERE $whereClause
            ");
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $countStmt->bind_param($types, ...$params);
            }
            $countStmt->execute();
            $result = $countStmt->get_result();
            $totalData = $result ? $result->fetch_assoc() : null;
            $total = $totalData ? $totalData['total'] : 0;
            
            $sql = "
                SELECT 
                    o.*,
                    v.business_name AS vendor_name,
                    COUNT(oi.order_item_id) AS item_count,
                    GROUP_CONCAT(CONCAT(p.name,' (x',oi.quantity,')') SEPARATOR ', ') AS items_summary
                FROM orders o
                LEFT JOIN vendors  v  ON o.vendor_id = v.vendor_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                LEFT JOIN products p     ON oi.product_id = p.product_id
                WHERE $whereClause
                GROUP BY o.order_id
                ORDER BY o.order_date DESC
                LIMIT ? OFFSET ?";

            $stmt = $this->db->prepare($sql);

            // Prepare types string for bind_param
            $types = str_repeat('s', count($params)) . 'ii';
            $bindParams = $params;
            $bindParams[] = (int)$limit;
            $bindParams[] = (int)$offset;
            $stmt->bind_param($types, ...$bindParams);
            $stmt->execute();
            $result = $stmt->get_result();
            $orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            
            return [
                'success' => true,
                'orders' => $orders,
                'total' => $total,
                'page' => $page,
                'totalPages' => ceil($total / $limit)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch order history: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get order details by ID
     */
    public function getOrderDetails($orderId, $customerId = null)
    {
        try {
            $whereConditions = ['o.order_id = ?', 'o.is_archive = 0'];
            $params = [$orderId];
            
            if ($customerId) {
                $whereConditions[] = 'o.customer_id = ?';
                $params[] = $customerId;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            $stmt = $this->db->prepare("
                SELECT 
                    o.*,
                    v.business_name as vendor_name,
                    v.contact_number as vendor_phone,
                    vu.email as vendor_email,
                    c.customer_id,
                    u.name as customer_name,
                    u.email as customer_email
                FROM orders o
                LEFT JOIN vendors v ON o.vendor_id = v.vendor_id
                LEFT JOIN users vu ON v.user_id = vu.user_id
                LEFT JOIN customers c ON o.customer_id = c.customer_id
                LEFT JOIN users u ON c.user_id = u.user_id
                WHERE $whereClause
            ");
            
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result ? $result->fetch_assoc() : null;
            
            if (!$order) {
                return ['success' => false, 'message' => 'Order not found'];
            }
            
            // Get order items
            $stmt = $this->db->prepare("
                SELECT 
                    oi.*,
                    p.name as product_name,
                    p.image_path,
                    p.stock_quantity,
                    v.business_name as vendor_name
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.product_id
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE oi.order_id = ?
            ");
            
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $order['items'] = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            
            return ['success' => true, 'order' => $order];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch order details: ' . $e->getMessage()];
        }
    }
    
    /**
     * Track order status and delivery
     */
    public function trackOrder($orderId, $customerId = null)
    {
        try {
            $whereConditions = ['o.order_id = ?', 'o.is_archive = 0'];
            $params = [$orderId];
            
            if ($customerId) {
                $whereConditions[] = 'o.customer_id = ?';
                $params[] = $customerId;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            $stmt = $this->db->prepare("
                SELECT 
                    o.order_id,
                    o.status,
                    o.tracking_number,
                    o.delivered_at,
                    o.order_date,
                    o.final_amount,
                    v.business_name as vendor_name,
                    COUNT(oi.order_item_id) as item_count
                FROM orders o
                LEFT JOIN vendors v ON o.vendor_id = v.vendor_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                WHERE $whereClause
                GROUP BY o.order_id
            ");
            
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $tracking = $result ? $result->fetch_assoc() : null;
            
            if (!$tracking) {
                return ['success' => false, 'message' => 'Order not found'];
            }
            
            // Generate tracking timeline
            $tracking['timeline'] = $this->generateTrackingTimeline($tracking['status']);
            
            return ['success' => true, 'tracking' => $tracking];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to track order: ' . $e->getMessage()];
        }
    }
    
    /**
     * Reorder from previous order
     */
    public function reorder($customerId, $orderId)
    {
        try {
            // Get original order items
            $stmt = $this->db->prepare("
                SELECT 
                    oi.product_id,
                    oi.quantity,
                    p.stock_quantity,
                    p.selling_price,
                    p.is_archive
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.product_id
                LEFT JOIN orders o ON oi.order_id = o.order_id
                WHERE o.order_id = ? AND o.customer_id = ? AND o.is_archive = 0
            ");
            
            $stmt->bind_param('ii', $orderId, $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            
            if (empty($items)) {
                return ['success' => false, 'message' => 'Original order not found'];
            }
            
            $addedItems = [];
            $unavailableItems = [];
            
            foreach ($items as $item) {
                if ($item['is_archive'] == 1) {
                    $unavailableItems[] = 'Product no longer available';
                    continue;
                }
                
                if ($item['stock_quantity'] < $item['quantity']) {
                    $unavailableItems[] = 'Insufficient stock for some items';
                    continue;
                }
                
                // Add to cart
                $result = $this->addToCart($customerId, $item['product_id'], $item['quantity']);
                if ($result['success']) {
                    $addedItems[] = $item['product_id'];
                }
            }
            
            $message = count($addedItems) . ' items added to cart';
            if (!empty($unavailableItems)) {
                $message .= '. Some items were unavailable: ' . implode(', ', array_unique($unavailableItems));
            }
            
            return [
                'success' => true,
                'message' => $message,
                'added_items' => count($addedItems),
                'unavailable_items' => count($unavailableItems)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to reorder: ' . $e->getMessage()];
        }
    }
    
    // Helper methods
    private function getCartItems($customerId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                sc.cart_id,
                sc.product_id,
                sc.quantity,
                p.name,
                p.selling_price,
                p.stock_quantity,
                p.vendor_id,
                p.is_archive
            FROM shopping_cart sc
            LEFT JOIN products p ON sc.product_id = p.product_id
            WHERE sc.customer_id = ? AND p.is_archive = 0
        ");
        $stmt->bind_param('i', $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
    
    private function updateProductStock($productId, $quantity)
    {
        try {
            // Get current product information before updating
            $stmt = $this->db->prepare("
                SELECT p.*, v.business_name as vendor_name, v.user_id as vendor_user_id
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE p.product_id = ?
            ");
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            
            if (!$product) {
                throw new Exception('Product not found');
            }
            
            $previousStock = $product['stock_quantity'];
            $newStock = $previousStock - $quantity;
            
            // Update the stock
            $stmt = $this->db->prepare("
                UPDATE products 
                SET stock_quantity = stock_quantity - ?
                WHERE product_id = ?
            ");
            $stmt->bind_param('ii', $quantity, $productId);
            $stmt->execute();
            
            // Check for low stock notifications after order
            $this->checkLowStockAfterOrder($product, $previousStock, $newStock);
            
        } catch (Exception $e) {
            error_log('Error updating product stock: ' . $e->getMessage());
            // Continue with order processing even if notification fails
        }
    }
    
    private function clearCart($customerId)
    {
        $stmt = $this->db->prepare("DELETE FROM shopping_cart WHERE customer_id = ?");
        $stmt->bind_param('i', $customerId);
        $stmt->execute();
    }
    
    private function addToCart($customerId, $productId, $quantity)
    {
        // Check if item already exists in cart
        $stmt = $this->db->prepare("
            SELECT cart_id, quantity FROM shopping_cart 
            WHERE customer_id = ? AND product_id = ?
        ");
        $stmt->bind_param('ii', $customerId, $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result->fetch_assoc();
        
        if ($existing) {
            // Update quantity
            $stmt = $this->db->prepare("
                UPDATE shopping_cart 
                SET quantity = quantity + ?, updated_at = NOW()
                WHERE cart_id = ?
            ");
            $stmt->bind_param('ii', $quantity, $existing['cart_id']);
            $stmt->execute();
        } else {
            // Add new item
            $stmt = $this->db->prepare("
                INSERT INTO shopping_cart (customer_id, product_id, quantity)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param('iii', $customerId, $productId, $quantity);
            $stmt->execute();
        }
        
        return ['success' => true];
    }
    
    private function generateTrackingTimeline($currentStatus)
    {
        $statuses = [
            'Pending' => ['label' => 'Order Placed', 'completed' => true],
            'Confirmed' => ['label' => 'Order Confirmed', 'completed' => false],
            'Processing' => ['label' => 'Processing', 'completed' => false],
            'Shipped' => ['label' => 'Shipped', 'completed' => false],
            'Delivered' => ['label' => 'Delivered', 'completed' => false]
        ];
        
        $statusKeys = array_keys($statuses);
        $currentIndex = array_search($currentStatus, $statusKeys);
        if ($currentIndex === false) $currentIndex = 0;
        
        $timeline = [];
        $index = 0;
        foreach ($statuses as $status => $info) {
            $timeline[] = [
                'status' => $status,
                'label' => $info['label'],
                'completed' => $index <= $currentIndex,
                'current' => $status === $currentStatus
            ];
            $index++;
        }
        
        return $timeline;
    }
    
    /**
     * Get order statistics for dashboard
     */
    public function getOrderStats($userRole = 'customer', $userId = null, $vendorId = null)
    {
        try {
            $whereConditions = ['is_archive = 0'];
            $params = [];
            
            if ($userRole === 'customer' && $userId) {
                // Get customer_id from user_id
                $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
                $stmt->bind_param('i', $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $customer = $result->fetch_assoc();
                if ($customer) {
                    $whereConditions[] = 'customer_id = ?';
                    $params[] = $customer['customer_id'];
                }
            } elseif ($userRole === 'vendor' && $vendorId) {
                $whereConditions[] = 'vendor_id = ?';
                $params[] = $vendorId;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                    SUM(CASE WHEN DATE(order_date) = CURDATE() THEN 1 ELSE 0 END) as today_orders,
                    COALESCE(SUM(final_amount), 0) as total_revenue
                FROM orders 
                WHERE $whereClause
            ");
            
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
            
        } catch (Exception $e) {
            return [
                'total_orders' => 0,
                'pending_orders' => 0,
                'processing_orders' => 0,
                'delivered_orders' => 0,
                'today_orders' => 0,
                'total_revenue' => 0
            ];
        }
    }

    /**
     * Update order status (for vendors/admins)
     */
    public function updateOrderStatus($orderId, $status, $trackingNumber = null, $deliveryDate = null)
    {
        try {
            $updateFields = ['status = ?'];
            $params = [$status];
            
            if ($trackingNumber) {
                $updateFields[] = 'tracking_number = ?';
                $params[] = $trackingNumber;
            }
            
            // Use delivered_at instead of delivery_date, and set it when status is Delivered or when deliveryDate is provided
            if ($deliveryDate) {
                $updateFields[] = 'delivered_at = ?';
                $params[] = $deliveryDate;
            } elseif ($status === 'Delivered') {
                $updateFields[] = 'delivered_at = CURDATE()';
            }
            
            // Skip notes since the column doesn't exist in the database
            // If you need notes functionality, you'll need to add the column first
            
            $params[] = $orderId;
            
            $updateClause = implode(', ', $updateFields);
            
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET $updateClause
                WHERE order_id = ? AND is_archive = 0
            ");
            
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                // Create notification for customer about status update
                try {
                    // Get order details to find customer
                    $stmt = $this->db->prepare("
                        SELECT o.customer_id, c.user_id, u.name as customer_name
                        FROM orders o
                        LEFT JOIN customers c ON o.customer_id = c.customer_id
                        LEFT JOIN users u ON c.user_id = u.user_id
                        WHERE o.order_id = ?
                    ");
                    $stmt->bind_param('i', $orderId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $orderInfo = $result ? $result->fetch_assoc() : null;
                    
                    if ($orderInfo && $orderInfo['user_id']) {
                        $statusMessages = [
                            'Pending' => 'Your order is pending confirmation',
                            'Confirmed' => 'Your order has been confirmed and is being processed',
                            'Processing' => 'Your order is being processed and prepared for shipping',
                            'Shipped' => 'Your order has been shipped and is on its way',
                            'Delivered' => 'Your order has been delivered successfully',
                            'Cancelled' => 'Your order has been cancelled'
                        ];
                        
                        $message = $statusMessages[$status] ?? "Your order status has been updated to: $status";
                        
                        if ($trackingNumber) {
                            $message .= " Tracking number: $trackingNumber";
                        }
                        
                        $this->notificationService->createNotification(
                            $orderInfo['user_id'],
                            'Order Status Updated',
                            $message,
                            'order'
                        );
                    }
                } catch (Exception $e) {
                    error_log('Error creating status update notification: ' . $e->getMessage());
                    // Don't fail the status update if notification fails
                }
                
                return ['success' => true, 'message' => 'Order status updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Order not found or no changes made'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update order status: ' . $e->getMessage()];
        }
    }

    /**
     * Cancel order (for customers)
     */
    public function cancelOrder($orderId, $customerId, $reason = 'Customer request')
    {
        try {
            $this->db->begin_transaction();
            
            // Check if order can be cancelled
            $stmt = $this->db->prepare("
                SELECT order_id, status, customer_id 
                FROM orders 
                WHERE order_id = ? AND customer_id = ? AND is_archive = 0
            ");
            $stmt->bind_param('ii', $orderId, $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result ? $result->fetch_assoc() : null;
            
            if (!$order) {
                throw new Exception('Order not found');
            }
            
            if (!in_array($order['status'], ['Pending', 'Confirmed'])) {
                throw new Exception('Order cannot be cancelled at this stage');
            }
            
            // Update order status to cancelled
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET status = 'Cancelled', cancel_reason = ?
                WHERE order_id = ?
            ");
            $stmt->bind_param('si', $reason, $orderId);
            $stmt->execute();
            
            // Restore product stock
            $stmt = $this->db->prepare("
                SELECT oi.product_id, oi.quantity
                FROM order_items oi
                WHERE oi.order_id = ?
            ");
            $stmt->bind_param('i', $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            
            foreach ($items as $item) {
                $stmt = $this->db->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity + ?
                    WHERE product_id = ?
                ");
                $stmt->bind_param('ii', $item['quantity'], $item['product_id']);
                $stmt->execute();
            }
            
            $this->db->commit();
            
            // Create notification for vendor about order cancellation
            try {
                // Get order details to find vendor
                $stmt = $this->db->prepare("
                    SELECT o.vendor_id, v.user_id, v.business_name
                    FROM orders o
                    LEFT JOIN vendors v ON o.vendor_id = v.vendor_id
                    WHERE o.order_id = ?
                ");
                $stmt->bind_param('i', $orderId);
                $stmt->execute();
                $result = $stmt->get_result();
                $orderInfo = $result ? $result->fetch_assoc() : null;
                
                if ($orderInfo && $orderInfo['user_id']) {
                    $this->notificationService->createNotification(
                        $orderInfo['user_id'],
                        'Order Cancelled',
                        "Order #$orderId has been cancelled by the customer. Reason: $reason",
                        'order'
                    );
                }
            } catch (Exception $e) {
                error_log('Error creating cancellation notification: ' . $e->getMessage());
                // Don't fail the cancellation if notification fails
            }
            
            return ['success' => true, 'message' => 'Order cancelled successfully'];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => 'Failed to cancel order: ' . $e->getMessage()];
        }
    }

    /**
     * Get vendor orders (for vendor dashboard)
     */
    public function getVendorOrders($vendorId, $page = 1, $limit = 10, $status = null)
    {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = ['o.vendor_id = ?', 'o.is_archive = 0'];
            $params = [$vendorId];
            
            if ($status) {
                $whereConditions[] = 'o.status = ?';
                $params[] = $status;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            // Get total count
            $countStmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM orders o 
                WHERE $whereClause
            ");
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $countStmt->bind_param($types, ...$params);
            }
            $countStmt->execute();
            $result = $countStmt->get_result();
            $totalData = $result ? $result->fetch_assoc() : null;
            $total = $totalData ? $totalData['total'] : 0;
            
            // Get orders - create separate params array for main query
            $mainQueryParams = $params; // Copy original params
            $mainQueryParams[] = (int)$limit;
            $mainQueryParams[] = (int)$offset;
            
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
                LIMIT ? OFFSET ?
            ");
            
            // Prepare types string for bind_param
            $types = str_repeat('s', count($params)) . 'ii';
            $bindParams = $params;
            $bindParams[] = (int)$limit;
            $bindParams[] = (int)$offset;
            $stmt->bind_param($types, ...$bindParams);
            $stmt->execute();
            $result = $stmt->get_result();
            $orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            
            return [
                'success' => true,
                'orders' => $orders,
                'total' => $total,
                'page' => $page,
                'totalPages' => ceil($total / $limit)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch vendor orders: ' . $e->getMessage()];
        }
    }

    /**
     * Get all orders (for admin dashboard)
     */
    public function getAllOrders($page = 1, $limit = 10, $status = null)
    {
        try {
            $offset = ($page - 1) * $limit;
            $whereConditions = ['o.is_archive = 0'];
            $params = [];
            
            if ($status) {
                $whereConditions[] = 'o.status = ?';
                $params[] = $status;
            }
            
            $whereClause = implode(' AND ', $whereConditions);
            
            // Get total count
            $countStmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM orders o 
                WHERE $whereClause
            ");
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $countStmt->bind_param($types, ...$params);
            }
            $countStmt->execute();
            $result = $countStmt->get_result();
            $totalData = $result ? $result->fetch_assoc() : null;
            $total = $totalData ? $totalData['total'] : 0;
            
            // Get orders - create separate params array for main query
            $mainQueryParams = $params; // Copy original params
            $mainQueryParams[] = (int)$limit;
            $mainQueryParams[] = (int)$offset;
            
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
                LIMIT ? OFFSET ?
            ");
            
            // Prepare types string for bind_param
            $types = str_repeat('s', count($params)) . 'ii';
            $bindParams = $params;
            $bindParams[] = (int)$limit;
            $bindParams[] = (int)$offset;
            $stmt->bind_param($types, ...$bindParams);
            $stmt->execute();
            $result = $stmt->get_result();
            $orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            
            return [
                'success' => true,
                'orders' => $orders,
                'total' => $total,
                'page' => $page,
                'totalPages' => ceil($total / $limit)
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch orders: ' . $e->getMessage()];
        }
    }
    
    /**
     * Check for low stock notifications after customer order
     */
    private function checkLowStockAfterOrder($product, $previousStock, $newStock)
    {
        try {
            $lowStockThreshold = 10; // Default threshold
            
            // Check if stock has crossed the low stock threshold
            $wasAboveThreshold = $previousStock > $lowStockThreshold;
            $isNowBelowThreshold = $newStock <= $lowStockThreshold && $newStock > 0;
            
            if ($wasAboveThreshold && $isNowBelowThreshold) {
                // Stock has just become low due to customer order - send notification
                $this->sendLowStockNotificationAfterOrder($product, $newStock, $lowStockThreshold);
            } elseif ($newStock == 0) {
                // Stock has become zero due to customer order - send out of stock notification
                $this->sendOutOfStockNotificationAfterOrder($product);
            }
            
        } catch (Exception $e) {
            error_log('Error checking low stock notification after order: ' . $e->getMessage());
        }
    }
    
    /**
     * Send low stock notification after customer order
     */
    private function sendLowStockNotificationAfterOrder($product, $currentStock, $threshold)
    {
        try {
            // Notify vendor
            if ($product['vendor_user_id']) {
                $this->notificationService->createNotification(
                    $product['vendor_user_id'],
                    'Low Stock Alert - Customer Order',
                    "Your product '{$product['name']}' is running low on stock after a customer order. Current stock: {$currentStock} (Threshold: {$threshold}). Consider restocking soon.",
                    'inventory'
                );
            }
            
            // Notify admin users
            $this->notifyAdminsLowStockAfterOrder($product, $currentStock, $threshold);
            
        } catch (Exception $e) {
            error_log('Error sending low stock notification after order: ' . $e->getMessage());
        }
    }
    
    /**
     * Send out of stock notification after customer order
     */
    private function sendOutOfStockNotificationAfterOrder($product)
    {
        try {
            // Notify vendor
            if ($product['vendor_user_id']) {
                $this->notificationService->createNotification(
                    $product['vendor_user_id'],
                    'Out of Stock Alert - Customer Order',
                    "Your product '{$product['name']}' is now out of stock after a customer order. Please restock immediately to avoid losing sales.",
                    'inventory'
                );
            }
            
            // Notify admin users
            $this->notifyAdminsOutOfStockAfterOrder($product);
            
        } catch (Exception $e) {
            error_log('Error sending out of stock notification after order: ' . $e->getMessage());
        }
    }
    
    /**
     * Notify admin users about low stock after customer order
     */
    private function notifyAdminsLowStockAfterOrder($product, $currentStock, $threshold)
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
                    'Low Stock Alert - Customer Order (Admin)',
                    "Product '{$product['name']}' (Vendor: {$product['vendor_name']}) is running low on stock after a customer order. Current stock: {$currentStock} (Threshold: {$threshold}).",
                    'inventory'
                );
            }
            
        } catch (Exception $e) {
            error_log('Error notifying admins about low stock after order: ' . $e->getMessage());
        }
    }
    
    /**
     * Notify admin users about out of stock after customer order
     */
    private function notifyAdminsOutOfStockAfterOrder($product)
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
                    'Out of Stock Alert - Customer Order (Admin)',
                    "Product '{$product['name']}' (Vendor: {$product['vendor_name']}) is now out of stock after a customer order. Vendor needs to restock immediately.",
                    'inventory'
                );
            }
            
        } catch (Exception $e) {
            error_log('Error notifying admins about out of stock after order: ' . $e->getMessage());
        }
    }
}

?> 