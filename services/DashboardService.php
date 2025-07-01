<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/AnalyticsService.php';
require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/OrderService.php';
require_once __DIR__ . '/ProductService.php';
require_once __DIR__ . '/VendorService.php';
require_once __DIR__ . '/ShoppingCartService.php';
require_once __DIR__ . '/../models/Vendor.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/SubscriptionTier.php';
require_once __DIR__ . '/../models/VendorSubscription.php';

/**
 * DashboardService
 * Consolidates all dashboard functionality by reusing existing service methods
 * Follows the reuse-first strategy
 */
class DashboardService
{
    private $db;
    private $analyticsService;
    private $userService;
    private $orderService;
    private $productService;
    private $vendorService;
    private $cartService;

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

        // Initialize services for reuse
        $this->analyticsService = new AnalyticsService();
        $this->userService = new UserService();
        $this->orderService = new OrderService();
        $this->productService = new ProductService();
        $this->vendorService = new VendorService();
        $this->cartService = new ShoppingCartService();
    }

    /**
     * Get comprehensive dashboard data for any user role
     */
    public function getDashboardData($currentUser, $userPermissions = [])
    {
        $userRole = $currentUser['role'] ?? 'customer';
        $userId = $currentUser['user_id'];
        
        $dashboardData = [
            'user' => $currentUser,
            'role' => $userRole,
            'stats' => [],
            'activities' => [],
            'notifications' => [],
            'quick_actions' => []
        ];

        try {
            // Get role-specific statistics
            switch ($userRole) {
                case 'admin':
                    $dashboardData['stats'] = $this->getAdminStats($userPermissions);
                    break;
                case 'vendor':
                    $dashboardData['stats'] = $this->getVendorStats($userId);
                    break;
                case 'customer':
                    $dashboardData['stats'] = $this->getCustomerStats($userId);
                    break;
                case 'staff':
                    $dashboardData['stats'] = $this->getStaffStats($userId);
                    break;
                default:
                    $dashboardData['stats'] = [];
            }

            // Get recent activities
            $dashboardData['activities'] = $this->getRecentActivities($currentUser, $userPermissions);

            return $dashboardData;

        } catch (Exception $e) {
            error_log('Error getting dashboard data: ' . $e->getMessage());
            return $dashboardData;
        }
    }

    /**
     * Get product count for current user (reuses ProductService)
     */
    public function getProductCount($currentUser)
    {
        if (!$currentUser) return 0;
        
        try {
            $userRole = $currentUser['role'];
            $userId = $currentUser['user_id'];
            
            $productStats = $this->productService->getProductStatistics($userRole, $userId);
            return $productStats['total_products'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting product count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get order count for current user (reuses OrderService)
     */
    public function getOrderCount($currentUser)
    {
        if (!$currentUser) return 0;
        
        try {
            $userRole = $currentUser['role'];
            $userId = $currentUser['user_id'];
            
            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                $orderStats = $this->orderService->getOrderStats($userRole, $userId, $vendorId);
            } else {
                $orderStats = $this->orderService->getOrderStats($userRole, $userId);
            }
            
            return $orderStats['today_orders'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting order count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total order count for current user (reuses OrderService)
     */
    public function getTotalOrderCount($currentUser)
    {
        if (!$currentUser) return 0;
        
        try {
            $userRole = $currentUser['role'];
            $userId = $currentUser['user_id'];
            
            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                $orderStats = $this->orderService->getOrderStats($userRole, $userId, $vendorId);
            } else {
                $orderStats = $this->orderService->getOrderStats($userRole, $userId);
            }
            
            return $orderStats['total_orders'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting total order count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get user count (reuses UserService)
     */
    public function getUserCount()
    {
        try {
            $userStats = $this->userService->getUserStatistics();
            return $userStats['total_users'] ?? 0;
        } catch (Exception $e) {
            error_log('Error getting user count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get vendor count
     */
    public function getVendorCount()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as vendor_count
                FROM vendors
                WHERE is_archive = 0
            ");
            $stmt->execute();
            return $stmt->fetch()['vendor_count'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting vendor count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get cart items count (reuses ShoppingCartService)
     */
    public function getCartItemsCount($currentUser)
    {
        if (!$currentUser) return 0;
        
        try {
            // Get customer_id from user_id
            $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = ? AND is_archive = 0");
            $stmt->execute([$currentUser['user_id']]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                return 0; // No customer profile found
            }
            
            $cartCount = $this->cartService->getCartItemCount($customer['customer_id']);
            return $cartCount['success'] ? $cartCount['count'] : 0;
        } catch (Exception $e) {
            error_log('Error getting cart items count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get revenue amount for current user
     */
    public function getRevenueAmount($currentUser)
    {
        if (!$currentUser) return 0;
        
        try {
            $userRole = $currentUser['role'];
            $userId = $currentUser['user_id'];
            
            $whereClause = "o.is_archive = 0 AND o.status = 'completed' AND o.order_date >= DATE_FORMAT(NOW() ,'%Y-%m-01')";
            $params = [];

            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $whereClause .= " AND o.vendor_id = ?";
                    $params[] = $vendorId;
                }
            }

            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(o.total_amount), 0) as total_revenue
                FROM orders o
                WHERE $whereClause
            ");
            $stmt->execute($params);
            return $stmt->fetch()['total_revenue'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting revenue amount: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get vendor subscription details
     */
    public function getVendorSubscriptionDetails($currentUser)
    {
        if (!$currentUser || $currentUser['role'] !== 'vendor') {
            return null;
        }
        
        try {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (empty($vendor)) {
                return null;
            }
            
            $vendorId = $vendor[0]['vendor_id'];
            $subscriptionTierId = $vendor[0]['subscription_tier_id'] ?? $vendor[0]['tier_id'];
            
            $subscriptionTierModel = new SubscriptionTier();
            $subscriptionTier = $subscriptionTierModel->find($subscriptionTierId);
            
            if (!$subscriptionTier) {
                return null;
            }
            
            $vendorSubscriptionModel = new VendorSubscription();
            $activeSubscription = $vendorSubscriptionModel->findAll([
                'vendor_id' => $vendorId,
                'is_active' => 1
            ]);
            
            return [
                'tier_name' => $subscriptionTier['name'],
                'description' => $subscriptionTier['description'],
                'monthly_fee' => $subscriptionTier['monthly_fee'],
                'due_date' => !empty($activeSubscription) ? $activeSubscription[0]['end_date'] : null,
                'is_active' => !empty($activeSubscription)
            ];
            
        } catch (Exception $e) {
            error_log('Error getting vendor subscription details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get percentage change calculations
     */
    public function getProductCountChange($currentUser)
    {
        return $this->calculateProductChange($currentUser);
    }

    public function getOrderCountChange($currentUser)
    {
        return $this->calculateOrderChange($currentUser);
    }

    public function getUserCountChange()
    {
        try {
            $userStats = $this->userService->getUserStatistics();
            return $this->calculatePercentageChange(
                $userStats['current_month_users'] ?? 0,
                $userStats['last_month_users'] ?? 0
            );
        } catch (Exception $e) {
            error_log('Error getting user count change: ' . $e->getMessage());
            return 0;
        }
    }

    public function getVendorCountChange()
    {
        return $this->calculateVendorChange();
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities($currentUser, $userPermissions = [], $limit = 5)
    {
        $activities = [];

        try {
            $userRole = $currentUser['role'];
            $userId = $currentUser['user_id'];

            // Get order activities - allow for all roles that can have orders
            if ($this->hasOrderPermissions($userPermissions, $userRole) || in_array($userRole, ['vendor', 'customer', 'admin'])) {
                $orderActivities = $this->getOrderActivities($currentUser, $userPermissions);
                $activities = array_merge($activities, $orderActivities);
            }

            // Get product activities - allow for vendors and admins
            if ($this->hasProductPermissions($userPermissions, $userRole) || in_array($userRole, ['vendor', 'admin'])) {
                $productActivities = $this->getProductActivities($currentUser, $userPermissions);
                $activities = array_merge($activities, $productActivities);
            }

            // Get user activities (admin only or with manage_users permission)
            if (isset($userPermissions['manage_users']) || in_array('manage_users', $userPermissions) || $userRole === 'admin') {
                $userActivities = $this->getUserActivities();
                $activities = array_merge($activities, $userActivities);
            }

            // If no activities found yet, provide some default activities based on role
            if (empty($activities)) {
                $activities = $this->getDefaultActivities($currentUser);
            }

            // Sort activities by date
            usort($activities, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            return array_slice($activities, 0, $limit);

        } catch (Exception $e) {
            error_log('Error getting recent activities: ' . $e->getMessage());
            return $this->getDefaultActivities($currentUser);
        }
    }

    /**
     * Get latest order information
     */
    public function getLatestOrderInfo($currentUser)
    {
        try {
            $userRole = $currentUser['role'];
            $userId = $currentUser['user_id'];
            
            $whereClause = "o.is_archive = 0 AND DATE(o.order_date) = CURDATE()";
            $params = [];

            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $whereClause .= " AND o.vendor_id = ?";
                    $params[] = $vendorId;
                }
            } elseif ($userRole === 'customer') {
                $whereClause .= " AND o.customer_id = ?";
                $params[] = $userId;
            }

            $stmt = $this->db->prepare("
                SELECT o.order_id, o.status, o.order_date
                FROM orders o
                WHERE $whereClause
                ORDER BY o.order_date DESC
                LIMIT 1
            ");
            $stmt->execute($params);
            $latestOrder = $stmt->fetch();

            if ($latestOrder) {
                return "Order #{$latestOrder['order_id']} - " . $this->getTimeAgo($latestOrder['order_date']);
            }

            return 'No orders today';

        } catch (Exception $e) {
            error_log('Error getting latest order info: ' . $e->getMessage());
            return 'No orders today';
        }
    }

    /**
     * Get latest product information
     */
    public function getLatestProductInfo($currentUser)
    {
        try {
            $userRole = $currentUser['role'];
            $userId = $currentUser['user_id'];
            
            $whereClause = "p.is_archive = 0";
            $params = [];

            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $whereClause .= " AND p.vendor_id = ?";
                    $params[] = $vendorId;
                }
            }

            $stmt = $this->db->prepare("
                SELECT p.product_id, p.name, p.created_at
                FROM products p
                WHERE $whereClause
                ORDER BY p.created_at DESC
                LIMIT 1
            ");
            $stmt->execute($params);
            $latestProduct = $stmt->fetch();

            if ($latestProduct) {
                return $latestProduct['name'];
            }

            return 'No products added';

        } catch (Exception $e) {
            error_log('Error getting latest product info: ' . $e->getMessage());
            return 'No products added';
        }
    }

    // Helper methods for staff dashboard
    public function getStaffTasksData($userId)
    {
        try {
            require_once __DIR__ . '/StaffService.php';
            require_once __DIR__ . '/../models/Staff.php';
            
            $staffService = new StaffService();
            $staffModel = new Staff();
            
            $staff = $staffModel->findAll(['user_id' => $userId]);
            $staffId = $staff[0]['staff_id'] ?? null;
            
            if (!$staffId) {
                return [];
            }
            
            return $staffService->getStaffTasks($staffId);
            
        } catch (Exception $e) {
            error_log('Error getting staff tasks: ' . $e->getMessage());
            return [];
        }
    }

    public function getStaffPerformanceData($userId)
    {
        try {
            require_once __DIR__ . '/StaffService.php';
            require_once __DIR__ . '/../models/Staff.php';
            
            $staffService = new StaffService();
            $staffModel = new Staff();
            
            $staff = $staffModel->findAll(['user_id' => $userId]);
            $staffId = $staff[0]['staff_id'] ?? null;
            
            if (!$staffId) {
                return [];
            }
            
            return $staffService->getStaffPerformance($staffId);
            
        } catch (Exception $e) {
            error_log('Error getting staff performance: ' . $e->getMessage());
            return [];
        }
    }

    // Customer-specific methods
    public function getCustomerTotalSpent($currentUser)
    {
        if (!$currentUser || $currentUser['role'] !== 'customer') return 0;
        
        try {
            // Get customer_id from user_id
            $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = ? AND is_archive = 0");
            $stmt->execute([$currentUser['user_id']]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                return 0; // No customer profile found
            }
            
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(total_amount), 0) as total_spent
                FROM orders
                WHERE customer_id = ?
                AND status = 'Delivered'
                AND is_archive = 0
            ");
            $stmt->execute([$customer['customer_id']]);
            return $stmt->fetch()['total_spent'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting customer total spent: ' . $e->getMessage());
            return 0;
        }
    }

    public function getCustomerOrderCount($currentUser)
    {
        try {
            // Get customer_id from user_id
            $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = ? AND is_archive = 0");
            $stmt->execute([$currentUser['user_id']]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                return 0; // No customer profile found
            }
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as order_count
                FROM orders
                WHERE customer_id = ?
                AND is_archive = 0
            ");
            $stmt->execute([$customer['customer_id']]);
            return $stmt->fetch()['order_count'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting customer order count: ' . $e->getMessage());
            return 0;
        }
    }

    public function getCustomerTodayOrders($currentUser)
    {
        if (!$currentUser || $currentUser['role'] !== 'customer') return 0;
        
        try {
            // Get customer_id from user_id
            $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = ? AND is_archive = 0");
            $stmt->execute([$currentUser['user_id']]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                return 0; // No customer profile found
            }
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as today_orders
                FROM orders
                WHERE customer_id = ?
                AND DATE(order_date) = CURDATE()
                AND is_archive = 0
            ");
            $stmt->execute([$customer['customer_id']]);
            return $stmt->fetch()['today_orders'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting customer today orders: ' . $e->getMessage());
            return 0;
        }
    }

    public function getCustomerPendingOrders($currentUser)
    {
        if (!$currentUser || $currentUser['role'] !== 'customer') return 0;
        
        try {
            // Get customer_id from user_id
            $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = ? AND is_archive = 0");
            $stmt->execute([$currentUser['user_id']]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                return 0; // No customer profile found
            }
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as pending_orders
                FROM orders
                WHERE customer_id = ?
                AND status IN ('Pending', 'Processing', 'Confirmed')
                AND is_archive = 0
            ");
            $stmt->execute([$customer['customer_id']]);
            return $stmt->fetch()['pending_orders'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting customer pending orders: ' . $e->getMessage());
            return 0;
        }
    }

    public function getCustomerFavoriteVendor($currentUser)
    {
        if (!$currentUser || $currentUser['role'] !== 'customer') return null;
        
        try {
            // Get customer_id from user_id
            $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = ? AND is_archive = 0");
            $stmt->execute([$currentUser['user_id']]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                return null; // No customer profile found
            }
            
            $stmt = $this->db->prepare("
                SELECT v.business_name, v.vendor_id, COUNT(*) as order_count
                FROM orders o
                JOIN vendors v ON o.vendor_id = v.vendor_id
                WHERE o.customer_id = ?
                AND o.is_archive = 0
                AND v.is_archive = 0
                GROUP BY v.vendor_id, v.business_name
                ORDER BY order_count DESC
                LIMIT 1
            ");
            $stmt->execute([$customer['customer_id']]);
            $result = $stmt->fetch();
            
            return $result ? [
                'business_name' => $result['business_name'],
                'order_count' => $result['order_count']
            ] : null;
            
        } catch (Exception $e) {
            error_log('Error getting customer favorite vendor: ' . $e->getMessage());
            return null;
        }
    }

    public function getCustomerMonthlySpent($currentUser)
    {
        if (!$currentUser || $currentUser['role'] !== 'customer') return 0;
        
        try {
            // Get customer_id from user_id
            $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = ? AND is_archive = 0");
            $stmt->execute([$currentUser['user_id']]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                return 0; // No customer profile found
            }
            
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(total_amount), 0) as monthly_spent
                FROM orders
                WHERE customer_id = ?
                AND status = 'Delivered'
                AND is_archive = 0
                AND order_date >= DATE_FORMAT(NOW() ,'%Y-m-01')
            ");
            $stmt->execute([$customer['customer_id']]);
            return $stmt->fetch()['monthly_spent'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting customer monthly spent: ' . $e->getMessage());
            return 0;
        }
    }

    // Vendor-specific methods
    public function getVendorPendingOrders($currentUser)
    {
        try {
            $vendorId = $this->getVendorIdByUserId($currentUser['user_id']);
            if (!$vendorId) return 0;
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as pending_count
                FROM orders
                WHERE vendor_id = ?
                AND status IN ('Pending', 'Processing')
                AND is_archive = 0
            ");
            $stmt->execute([$vendorId]);
            return $stmt->fetch()['pending_count'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting vendor pending orders: ' . $e->getMessage());
            return 0;
        }
    }

    public function getVendorTotalRevenue($currentUser)
    {
        if (!$currentUser || $currentUser['role'] !== 'vendor') return 0;
        
        try {
            $vendorId = $this->getVendorIdByUserId($currentUser['user_id']);
            if (!$vendorId) return 0;
            
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(total_amount), 0) as total_revenue
                FROM orders
                WHERE vendor_id = ?
                AND status = 'Delivered'
                AND is_archive = 0
            ");
            $stmt->execute([$vendorId]);
            return $stmt->fetch()['total_revenue'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting vendor total revenue: ' . $e->getMessage());
            return 0;
        }
    }

    public function getVendorMonthlyRevenue($currentUser)
    {
        if (!$currentUser || $currentUser['role'] !== 'vendor') return 0;
        
        try {
            $vendorId = $this->getVendorIdByUserId($currentUser['user_id']);
            if (!$vendorId) return 0;
            
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(total_amount), 0) as monthly_revenue
                FROM orders
                WHERE vendor_id = ?
                AND status = 'Delivered'
                AND is_archive = 0
                AND order_date >= DATE_FORMAT(NOW() ,'%Y-%m-01')
            ");
            $stmt->execute([$vendorId]);
            return $stmt->fetch()['monthly_revenue'] ?? 0;
            
        } catch (Exception $e) {
            error_log('Error getting vendor monthly revenue: ' . $e->getMessage());
            return 0;
        }
    }

    // Private helper methods
    private function getVendorIdByUserId($userId)
    {
        try {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $userId]);
            return !empty($vendor) ? $vendor[0]['vendor_id'] : null;
        } catch (Exception $e) {
            error_log('Error getting vendor ID: ' . $e->getMessage());
            return null;
        }
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100);
        } elseif ($current > 0) {
            return 100;
        }
        return 0;
    }

    private function calculateProductChange($currentUser)
    {
        try {
            $whereClause = "is_archive = 0";
            $params = [];

            if ($currentUser['role'] === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($currentUser['user_id']);
                if ($vendorId) {
                    $whereClause .= " AND vendor_id = ?";
                    $params[] = $vendorId;
                }
            }

            // This month
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as current_count
                FROM products
                WHERE $whereClause AND created_at >= DATE_FORMAT(NOW() ,'%Y-%m-01')
            ");
            $stmt->execute($params);
            $currentCount = $stmt->fetch()['current_count'];

            // Last month
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as last_count
                FROM products
                WHERE $whereClause 
                AND created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH) ,'%Y-%m-01')
                AND created_at < DATE_FORMAT(NOW() ,'%Y-%m-01')
            ");
            $stmt->execute($params);
            $lastCount = $stmt->fetch()['last_count'];

            return $this->calculatePercentageChange($currentCount, $lastCount);

        } catch (Exception $e) {
            error_log('Error calculating product change: ' . $e->getMessage());
            return 0;
        }
    }

    private function calculateOrderChange($currentUser)
    {
        try {
            $whereClause = "is_archive = 0 AND DATE(order_date) = ?";
            $params = [];

            if ($currentUser['role'] === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($currentUser['user_id']);
                if ($vendorId) {
                    $whereClause .= " AND vendor_id = ?";
                    $params[] = $vendorId;
                }
            } elseif ($currentUser['role'] === 'customer') {
                // Get customer_id from user_id
                $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = ? AND is_archive = 0");
                $stmt->execute([$currentUser['user_id']]);
                $customer = $stmt->fetch();
                
                if ($customer) {
                    $whereClause .= " AND customer_id = ?";
                    $params[] = $customer['customer_id'];
                }
            }

            // Yesterday
            $yesterdayParams = array_merge([date('Y-m-d', strtotime('-1 day'))], $params);
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM orders WHERE $whereClause");
            $stmt->execute($yesterdayParams);
            $yesterdayCount = $stmt->fetch()['count'];

            // Today
            $todayParams = array_merge([date('Y-m-d')], $params);
            $stmt->execute($todayParams);
            $todayCount = $stmt->fetch()['count'];

            return $this->calculatePercentageChange($todayCount, $yesterdayCount);

        } catch (Exception $e) {
            error_log('Error calculating order change: ' . $e->getMessage());
            return 0;
        }
    }

    private function calculateVendorChange()
    {
        try {
            // Current month
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as current_count
                FROM vendors
                WHERE is_approved = 1 AND is_archive = 0
                AND approved_at >= DATE_FORMAT(NOW() ,'%Y-%m-01')
            ");
            $stmt->execute();
            $currentCount = $stmt->fetch()['current_count'];

            // Last month
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as last_count
                FROM vendors
                WHERE is_approved = 1 AND is_archive = 0
                AND approved_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH) ,'%Y-%m-01')
                AND approved_at < DATE_FORMAT(NOW() ,'%Y-%m-01')
            ");
            $stmt->execute();
            $lastCount = $stmt->fetch()['last_count'];

            return $this->calculatePercentageChange($currentCount, $lastCount);

        } catch (Exception $e) {
            error_log('Error calculating vendor change: ' . $e->getMessage());
            return 0;
        }
    }

    private function getOrderActivities($currentUser, $userPermissions)
    {
        $activities = [];
        try {
            $userRole = $currentUser['role'];
            $userId = $currentUser['user_id'];
            
            $whereClause = "o.is_archive = 0";
            $params = [];

            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $whereClause .= " AND o.vendor_id = ?";
                    $params[] = $vendorId;
                }
            } elseif ($userRole === 'customer') {
                $whereClause .= " AND o.customer_id = ?";
                $params[] = $userId;
            }

            $stmt = $this->db->prepare("
                SELECT o.order_id, o.status, o.order_date
                FROM orders o
                WHERE $whereClause
                ORDER BY o.order_date DESC
                LIMIT 5
            ");
            $stmt->execute($params);
            
            while ($row = $stmt->fetch()) {
                $activities[] = [
                    'type' => 'order',
                    'icon' => 'fa-shopping-cart',
                    'description' => "Order #{$row['order_id']} - {$row['status']}",
                    'date' => $row['order_date']
                ];
            }

        } catch (Exception $e) {
            error_log('Error getting order activities: ' . $e->getMessage());
        }

        return $activities;
    }

    private function getProductActivities($currentUser, $userPermissions)
    {
        $activities = [];
        try {
            $userRole = $currentUser['role'];
            $userId = $currentUser['user_id'];
            
            $whereClause = "p.is_archive = 0";
            $params = [];

            if ($userRole === 'vendor') {
                $vendorId = $this->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $whereClause .= " AND p.vendor_id = ?";
                    $params[] = $vendorId;
                }
            }

            $stmt = $this->db->prepare("
                SELECT p.product_id, p.name, p.created_at
                FROM products p
                WHERE $whereClause
                ORDER BY p.created_at DESC
                LIMIT 5
            ");
            $stmt->execute($params);
            
            while ($row = $stmt->fetch()) {
                $activities[] = [
                    'type' => 'product',
                    'icon' => 'fa-box',
                    'description' => "New product added: {$row['name']}",
                    'date' => $row['created_at']
                ];
            }

        } catch (Exception $e) {
            error_log('Error getting product activities: ' . $e->getMessage());
        }

        return $activities;
    }

    private function getUserActivities()
    {
        $activities = [];
        try {
            $stmt = $this->db->prepare("
                SELECT user_id, username, created_at
                FROM users
                WHERE is_archive = 0
                ORDER BY created_at DESC
                LIMIT 5
            ");
            $stmt->execute();
            
            while ($row = $stmt->fetch()) {
                $activities[] = [
                    'type' => 'user',
                    'icon' => 'fa-user-plus',
                    'description' => "New user registered: {$row['username']}",
                    'date' => $row['created_at']
                ];
            }

        } catch (Exception $e) {
            error_log('Error getting user activities: ' . $e->getMessage());
        }

        return $activities;
    }

    private function hasOrderPermissions($userPermissions, $userRole)
    {
        return isset($userPermissions['manage_orders']) || 
               in_array('manage_orders', $userPermissions) ||
               isset($userPermissions['view_orders']) ||
               in_array('view_orders', $userPermissions) ||
               in_array($userRole, ['vendor', 'customer']);
    }

    private function hasProductPermissions($userPermissions, $userRole)
    {
        return isset($userPermissions['manage_products']) || 
               in_array('manage_products', $userPermissions) ||
               in_array($userRole, ['vendor']);
    }

    private function getDefaultActivities($currentUser)
    {
        $userRole = $currentUser['role'];
        $activities = [];
        
        switch ($userRole) {
            case 'admin':
                $activities[] = [
                    'type' => 'welcome',
                    'icon' => 'fa-crown',
                    'description' => 'Welcome to your admin dashboard!',
                    'date' => date('Y-m-d H:i:s')
                ];
                break;
            case 'vendor':
                $activities[] = [
                    'type' => 'welcome',
                    'icon' => 'fa-store',
                    'description' => 'Welcome to your vendor dashboard! Start by adding products.',
                    'date' => date('Y-m-d H:i:s')
                ];
                break;
            case 'customer':
                $activities[] = [
                    'type' => 'welcome',
                    'icon' => 'fa-shopping-bag',
                    'description' => 'Welcome! Start shopping for fresh produce.',
                    'date' => date('Y-m-d H:i:s')
                ];
                break;
            case 'staff':
                $activities[] = [
                    'type' => 'welcome',
                    'icon' => 'fa-user-tie',
                    'description' => 'Welcome to your staff dashboard! Check your assigned tasks.',
                    'date' => date('Y-m-d H:i:s')
                ];
                break;
            default:
                $activities[] = [
                    'type' => 'welcome',
                    'icon' => 'fa-user',
                    'description' => 'Welcome to AgriMarket Solutions!',
                    'date' => date('Y-m-d H:i:s')
                ];
        }
        
        return $activities;
    }

    public function getTimeAgo($datetime)
    {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' min ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } else {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        }
    }

    public function getSubscriptionTierColor($tierName)
    {
        $tierName = strtolower(trim($tierName));
        
        switch ($tierName) {
            case 'bronze':
                return 'linear-gradient(135deg, #cd7f32, #b8860b)';
            case 'silver':
                return 'linear-gradient(135deg, #c0c0c0, #a8a8a8)';
            case 'gold':
                return 'linear-gradient(135deg, #ffd700, #ffb347)';
            case 'platinum':
                return 'linear-gradient(135deg, #667eea, #764ba2)';
            default:
                return 'linear-gradient(135deg,rgb(108, 126, 120),rgb(29, 88, 70))';
        }
    }

    public function getDashboardTitle($user)
    {
        $role = $user['role'] ?? 'customer';
        switch ($role) {
            case 'admin': return 'Admin Dashboard';
            case 'vendor': return 'Vendor Dashboard';
            case 'staff': return 'Staff Dashboard';
            case 'customer': return 'Customer Dashboard';
            default: return 'Dashboard';
        }
    }

    public function getLatestRegisteredUser()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT username, created_at
                FROM users
                WHERE is_archive = 0
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $stmt->execute();
            $user = $stmt->fetch();
            
            if ($user) {
                return [
                    'username' => $user['username'],
                    'time_ago' => $this->getTimeAgo($user['created_at'])
                ];
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log('Error getting latest registered user: ' . $e->getMessage());
            return null;
        }
    }

    public function getLatestApprovedVendor()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT v.business_name, v.approved_at
                FROM vendors v
                WHERE v.is_approved = 1 AND v.is_archive = 0
                ORDER BY v.approved_at DESC
                LIMIT 1
            ");
            $stmt->execute();
            $vendor = $stmt->fetch();
            
            if ($vendor) {
                return [
                    'business_name' => $vendor['business_name'],
                    'time_ago' => $this->getTimeAgo($vendor['approved_at'])
                ];
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log('Error getting latest approved vendor: ' . $e->getMessage());
            return null;
        }
    }
} 