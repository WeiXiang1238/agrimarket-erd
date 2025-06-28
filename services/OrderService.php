<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Payment.php';

/**
 * OrderService
 * Handles order management, tracking, customer order history, and reordering features
 */
class OrderService
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
     * Create new order from shopping cart
     */
    public function createOrderFromCart($customerId, $shippingAddress, $billingAddress = null, $paymentMethodId = null, $notes = null)
    {
        try {
            $this->db->beginTransaction();
            
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
                
                $stmt->execute([
                    $customerId, $vendorId, $totalAmount, $shippingCost, $finalAmount
                ]);
                
                $orderId = $this->db->lastInsertId();
                
                // Add order items
                foreach ($items as $item) {
                    $subtotal = $item['selling_price'] * $item['quantity'];
                    
                    $stmt = $this->db->prepare("
                        INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase, subtotal)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $orderId, $item['product_id'], $item['quantity'], 
                        $item['selling_price'], $subtotal
                    ]);
                    
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
            
            return [
                'success' => true,
                'message' => 'Orders created successfully',
                'orders' => $createdOrders
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
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
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
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

            /* bind WHERE-clause params first */
            $index = 1;
            foreach ($params as $val) {
                $stmt->bindValue($index++, $val);        // default string binding is fine
            }

            /* bind LIMIT / OFFSET with correct type */
            $stmt->bindValue($index++, (int)$limit,  PDO::PARAM_INT);
            $stmt->bindValue($index++, (int)$offset, PDO::PARAM_INT);

            $stmt->execute();
$orders = $stmt->fetchAll();
            
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
            
            $stmt->execute($params);
            $order = $stmt->fetch();
            
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
            
            $stmt->execute([$orderId]);
            $order['items'] = $stmt->fetchAll();
            
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
            
            $stmt->execute($params);
            $tracking = $stmt->fetch();
            
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
            
            $stmt->execute([$orderId, $customerId]);
            $items = $stmt->fetchAll();
            
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
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }
    
    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
    
    private function updateProductStock($productId, $quantity)
    {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET stock_quantity = stock_quantity - ?
            WHERE product_id = ?
        ");
        $stmt->execute([$quantity, $productId]);
    }
    
    private function clearCart($customerId)
    {
        $stmt = $this->db->prepare("DELETE FROM shopping_cart WHERE customer_id = ?");
        $stmt->execute([$customerId]);
    }
    
    private function addToCart($customerId, $productId, $quantity)
    {
        // Check if item already exists in cart
        $stmt = $this->db->prepare("
            SELECT cart_id, quantity FROM shopping_cart 
            WHERE customer_id = ? AND product_id = ?
        ");
        $stmt->execute([$customerId, $productId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update quantity
            $stmt = $this->db->prepare("
                UPDATE shopping_cart 
                SET quantity = quantity + ?, updated_at = NOW()
                WHERE cart_id = ?
            ");
            $stmt->execute([$quantity, $existing['cart_id']]);
        } else {
            // Add new item
            $stmt = $this->db->prepare("
                INSERT INTO shopping_cart (customer_id, product_id, quantity)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$customerId, $productId, $quantity]);
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
                $stmt->execute([$userId]);
                $customer = $stmt->fetch();
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
            
            $stmt->execute($params);
            return $stmt->fetch();
            
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
            
            $stmt->execute($params);
            
            if ($stmt->rowCount() > 0) {
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
            $this->db->beginTransaction();
            
            // Check if order can be cancelled
            $stmt = $this->db->prepare("
                SELECT order_id, status, customer_id 
                FROM orders 
                WHERE order_id = ? AND customer_id = ? AND is_archive = 0
            ");
            $stmt->execute([$orderId, $customerId]);
            $order = $stmt->fetch();
            
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
            $stmt->execute([$reason, $orderId]);
            
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
            
            $this->db->commit();
            
            return ['success' => true, 'message' => 'Order cancelled successfully'];
            
        } catch (Exception $e) {
            $this->db->rollBack();
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
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
            // Get orders - create separate params array for main query
            $mainQueryParams = $params; // Copy original params
            $mainQueryParams[] = (int)$limit;
            $mainQueryParams[] = (int)$offset;
            
            $stmt = $this->db->prepare("
                SELECT 
                    o.*,
                    c.customer_id,
                    u.name as customer_name,
                    u.email as customer_email,
                    COUNT(oi.order_item_id) as item_count,
                    GROUP_CONCAT(
                        CONCAT(p.name, ' (x', oi.quantity, ')') 
                        SEPARATOR ', '
                    ) as items_summary
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.customer_id
                LEFT JOIN users u ON c.user_id = u.user_id
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                LEFT JOIN products p ON oi.product_id = p.product_id
                WHERE $whereClause
                GROUP BY o.order_id
                ORDER BY o.order_date DESC
                LIMIT ? OFFSET ?
            ");
            
            // Bind WHERE clause parameters first
            $index = 1;
            foreach ($params as $val) {
                $stmt->bindValue($index++, $val);
            }
            
            // Bind LIMIT and OFFSET with correct type
            $stmt->bindValue($index++, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue($index++, (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $orders = $stmt->fetchAll();
            
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
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
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
            
            // Bind WHERE clause parameters first
            $index = 1;
            foreach ($params as $val) {
                $stmt->bindValue($index++, $val);
            }
            
            // Bind LIMIT and OFFSET with correct type
            $stmt->bindValue($index++, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue($index++, (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $orders = $stmt->fetchAll();
            
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
}

?> 