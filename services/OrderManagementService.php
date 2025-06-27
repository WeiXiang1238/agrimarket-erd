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
            if ($userRoles['isCustomer']) {
                if (!$userRoles['customerId']) {
                    throw new Exception('Customer profile not found');
                }
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
                return $this->orderService->getVendorOrders(
                    $userRoles['vendorId'], 
                    $page, 
                    $limit, 
                    $status
                );
            }
            
            if ($userRoles['isAdmin']) {
                return $this->orderService->getAllOrders($page, $limit, $status);
            }

            return ['success' => true, 'orders' => [], 'total' => 0, 'totalPages' => 1];
            
        } catch (Exception $e) {
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
        $notes = $postData['notes'] ?? null;

        return $this->orderService->updateOrderStatus(
            $orderId, 
            $status, 
            $trackingNumber, 
            $deliveryDate, 
            $notes
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
}

?> 