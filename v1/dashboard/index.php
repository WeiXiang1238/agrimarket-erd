<?php
// Set timezone to Asia/Kuala_Lumpur for Malaysian time
date_default_timezone_set('Asia/Kuala_Lumpur');

require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/PermissionService.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Vendor.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/SubscriptionTier.php';
require_once __DIR__ . '/../../models/VendorSubscription.php';
require_once __DIR__ . '/../../services/NotificationService.php';
require_once __DIR__ . '/../../models/Staff.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../services/StaffService.php';
require_once __DIR__ . '/../../models/StaffTask.php';

$authService = new AuthService();
$permissionService = new PermissionService();
$notificationService = new NotificationService();

// Require authentication (any authenticated user can access)
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');

$currentUser = $authService->getCurrentUser();
$csrfToken = $authService->generateCSRFToken();

// Set page title for tracking
$pageTitle = getDashboardTitle($currentUser);

// Include page tracking
require_once __DIR__ . '/../../includes/page_tracking.php';

// Get user permissions using PermissionService
$userPermissions = [];
if ($currentUser) {
    $userPermissions = $permissionService->getEffectivePermissions($currentUser);
}

// Get notifications for the current user
$userNotifications = [];
$unreadCount = 0;
if ($currentUser) {
    $userNotifications = $notificationService->getUserNotifications($currentUser['user_id'], 20);
    $unreadCount = 0;
    foreach ($userNotifications as $notif) {
        if (!$notif['is_read']) $unreadCount++;
    }
}

// Helper function to check permissions
function hasPermission($permission) {
    global $userPermissions;
    return isset($userPermissions[$permission]);
}

// Function to get product count for current user
function getProductCount($currentUser) {
    if (!$currentUser) return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Base query for all products
        $baseQuery = "
            SELECT COUNT(*) as product_count
            FROM products
            WHERE is_archive = 0
        ";
        
        // If user has manage_products permission (admin), return total count
        if (hasPermission('manage_products')) {
            $stmt = $db->prepare($baseQuery);
            $stmt->execute();
            return $stmt->fetch()['product_count'] ?? 0;
        }
        
        // If user is a vendor, get their vendor_id and count their products
        if ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($vendor)) {
                $vendorId = $vendor[0]['vendor_id'];
                $stmt = $db->prepare($baseQuery . " AND vendor_id = ?");
                $stmt->execute([$vendorId]);
                return $stmt->fetch()['product_count'] ?? 0;
            }
        }
        
        return 0;
        
    } catch (Exception $e) {
        error_log('Error getting product count: ' . $e->getMessage());
        return 0;
    }
}

// Function to get product count change percentage
function getProductCountChange($currentUser) {
    if (!$currentUser) return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Base query for last month's products
        $baseQuery = "
            SELECT COUNT(*) as product_count
            FROM products
            WHERE is_archive = 0
            AND created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH) ,'%Y-%m-01')
            AND created_at < DATE_FORMAT(NOW() ,'%Y-%m-01')
        ";
        
        // Base query for this month's products
        $thisMonthQuery = "
            SELECT COUNT(*) as product_count
            FROM products
            WHERE is_archive = 0
            AND created_at >= DATE_FORMAT(NOW() ,'%Y-%m-01')
        ";
        
        $whereClause = "";
        $params = [];
        
        // Add role-specific conditions
        if ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            if (!empty($vendor)) {
                $whereClause = " AND vendor_id = ?";
                $params[] = $vendor[0]['vendor_id'];
            }
        } elseif (!hasPermission('manage_products')) {
            return 0;
        }
        
        // Get last month's count
        $stmt = $db->prepare($baseQuery . $whereClause);
        $stmt->execute($params);
        $lastMonthCount = $stmt->fetch()['product_count'];
        
        // Get this month's count
        $stmt = $db->prepare($thisMonthQuery . $whereClause);
        $stmt->execute($params);
        $thisMonthCount = $stmt->fetch()['product_count'];
        
        if ($lastMonthCount > 0) {
            $percentChange = (($thisMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
            return round($percentChange);
        } else if ($thisMonthCount > 0) {
            return 100; // If last month was 0 and this month has products, return 100%
        }
        
        return 0;
        
    } catch (Exception $e) {
        error_log('Error getting product count change: ' . $e->getMessage());
        return 0;
    }
}

// Function to get order count for current user
function getOrderCount($currentUser) {
    if (!$currentUser) return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Base query for today's orders
        $baseQuery = "
            SELECT COUNT(*) as order_count
            FROM orders
            WHERE is_archive = 0
            AND DATE(order_date) = CURDATE()
        ";
        
        // If user has manage_orders permission (admin), return total count
        if (hasPermission('manage_orders')) {
            $stmt = $db->prepare($baseQuery);
            $stmt->execute();
            return $stmt->fetch()['order_count'] ?? 0;
        }
        
        // If user is a vendor, get their vendor_id and count their orders
        if ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($vendor)) {
                $vendorId = $vendor[0]['vendor_id'];
                $stmt = $db->prepare($baseQuery . " AND vendor_id = ?");
                $stmt->execute([$vendorId]);
                return $stmt->fetch()['order_count'] ?? 0;
            }
        }
        
        // If user is a customer, count their orders
        if ($currentUser['role'] == 'customer') {
            $stmt = $db->prepare($baseQuery . " AND customer_id = ?");
            $stmt->execute([$currentUser['user_id']]);
            return $stmt->fetch()['order_count'] ?? 0;
        }
        
        return 0;
        
    } catch (Exception $e) {
        error_log('Error getting order count: ' . $e->getMessage());
        return 0;
    }
}

// Function to get total order count for current user
function getTotalOrderCount($currentUser) {
    if (!$currentUser) return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Base query for all orders
        $baseQuery = "
            SELECT COUNT(*) as order_count
            FROM orders
            WHERE is_archive = 0
        ";
        
        // If user has manage_orders permission (admin), return total count
        if (hasPermission('manage_orders')) {
            $stmt = $db->prepare($baseQuery);
            $stmt->execute();
            return $stmt->fetch()['order_count'] ?? 0;
        }
        
        // If user is a vendor, get their vendor_id and count their orders
        if ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($vendor)) {
                $vendorId = $vendor[0]['vendor_id'];
                $stmt = $db->prepare($baseQuery . " AND vendor_id = ?");
                $stmt->execute([$vendorId]);
                return $stmt->fetch()['order_count'] ?? 0;
            }
        }
        
        // If user is a customer, count their orders
        if ($currentUser['role'] == 'customer') {
            $stmt = $db->prepare($baseQuery . " AND customer_id = ?");
            $stmt->execute([$currentUser['user_id']]);
            return $stmt->fetch()['order_count'] ?? 0;
        }
        
        return 0;
        
    } catch (Exception $e) {
        error_log('Error getting total order count: ' . $e->getMessage());
        return 0;
    }
}

// Function to get report count for current user
function getReportCount($currentUser) {
    if (!$currentUser) return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // If user has view_analytics permission (admin), get total analytics reports
        if (hasPermission('view_analytics')) {
            $stmt = $db->prepare("
                SELECT COUNT(DISTINCT report_type) as report_count
                FROM analytics_reports
                WHERE is_archive = 0
            ");
            $stmt->execute();
            return $stmt->fetch()['report_count'] ?? 0;
        }
        
        // If user has view_reports permission (vendor), get vendor-specific reports
        if (hasPermission('view_reports')) {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($vendor)) {
                $vendorId = $vendor[0]['vendor_id'];
                $stmt = $db->prepare("
                    SELECT COUNT(DISTINCT report_type) as report_count
                    FROM vendor_reports
                    WHERE vendor_id = ?
                    AND is_archive = 0
                ");
                $stmt->execute([$vendorId]);
                return $stmt->fetch()['report_count'] ?? 0;
            }
        }
        
        // For customers, get their available reports
        if ($currentUser['role'] == 'customer') {
            $stmt = $db->prepare("
                SELECT COUNT(DISTINCT report_type) as report_count
                FROM customer_reports
                WHERE is_archive = 0
                AND is_public = 1
            ");
            $stmt->execute();
            return $stmt->fetch()['report_count'] ?? 0;
        }
        
        return 0;
        
    } catch (Exception $e) {
        error_log('Error getting report count: ' . $e->getMessage());
        return 0;
    }
}

// Function to get vendor subscription details
function getVendorSubscriptionDetails($currentUser) {
    if (!$currentUser || $currentUser['role'] !== 'vendor') {
        return null;
    }
    
    $vendorModel = new Vendor();
    $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
    
    if (empty($vendor)) {
        return null;
    }
    
    $vendorId = $vendor[0]['vendor_id'];
    $subscriptionTierId = $vendor[0]['subscription_tier_id'] ?? $vendor[0]['tier_id'];
    
    // Get subscription tier details
    $subscriptionTierModel = new SubscriptionTier();
    $subscriptionTier = $subscriptionTierModel->find($subscriptionTierId);
    
    if (!$subscriptionTier) {
        return null;
    }
    
    // Get active subscription details
    $vendorSubscriptionModel = new VendorSubscription();
    $activeSubscription = $vendorSubscriptionModel->findAll([
        'vendor_id' => $vendorId,
        'is_active' => 1
    ]);
    
    $subscriptionDetails = [
        'tier_name' => $subscriptionTier['name'],
        'description' => $subscriptionTier['description'],
        'monthly_fee' => $subscriptionTier['monthly_fee'],
        'due_date' => null,
        'is_active' => false
    ];
    
    if (!empty($activeSubscription)) {
        $subscriptionDetails['due_date'] = $activeSubscription[0]['end_date'];
        $subscriptionDetails['is_active'] = true;
    }
    
    return $subscriptionDetails;
}

// Function to get subscription tier background color
function getSubscriptionTierColor($tierName) {
    $tierName = strtolower(trim($tierName));
    
    switch ($tierName) {
        case 'bronze':
            return 'linear-gradient(135deg, #cd7f32, #b8860b)'; // Bronze color
        case 'silver':
            return 'linear-gradient(135deg, #c0c0c0, #a8a8a8)'; // Silver color
        case 'gold':
            return 'linear-gradient(135deg, #ffd700, #ffb347)'; // Gold color
        case 'platinum':
            return 'linear-gradient(135deg, #667eea, #764ba2)'; // Fantasy purple gradient
        default:
            return 'linear-gradient(135deg,rgb(108, 126, 120),rgb(29, 88, 70))'; // Green (default)
    }
}

// Get dashboard title based on user role
function getDashboardTitle($user) {
    $role = $user['role'] ?? 'customer';
    switch ($role) {
        case 'admin': return 'Admin Dashboard';
        case 'vendor': return 'Vendor Dashboard';
        case 'staff': return 'Staff Dashboard';
        case 'customer': return 'Customer Dashboard';
        default: return 'Dashboard';
    }
}

// Handle task completion POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_task_id'])) {
    $staffService = new StaffService();
    $taskId = intval($_POST['complete_task_id']);
    if (isset($_POST['is_completed']) && $_POST['is_completed'] == '1') {
        $staffService->markTaskCompleted($taskId);
    } else {
        $staffService->markTaskNotCompleted($taskId);
    }
    // Redirect to avoid resubmission
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Function to get revenue amount for current user
function getRevenueAmount($currentUser) {
    if (!$currentUser) return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Base query for revenue calculation
        $baseQuery = "
            SELECT COALESCE(SUM(total_amount), 0) as total_revenue
            FROM orders 
            WHERE is_archive = 0 
            AND status = 'completed'
            AND order_date >= DATE_FORMAT(NOW() ,'%Y-%m-01')
        ";
        
        // If user has view_analytics permission (admin), return total platform revenue
        if (hasPermission('view_analytics')) {
            $stmt = $db->prepare($baseQuery);
            $stmt->execute();
            return $stmt->fetch()['total_revenue'] ?? 0;
        }
        
        // If user is a vendor, get their revenue
        if ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($vendor)) {
                $vendorId = $vendor[0]['vendor_id'];
                $stmt = $db->prepare($baseQuery . " AND vendor_id = ?");
                $stmt->execute([$vendorId]);
                return $stmt->fetch()['total_revenue'] ?? 0;
            }
        }
        
        return 0;
        
    } catch (Exception $e) {
        error_log('Error getting revenue amount: ' . $e->getMessage());
        return 0;
    }
}

// Function to get latest order information for current user
function getLatestOrderInfo($currentUser) {
    if (!$currentUser) return 'No orders today';
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // If user has view_analytics permission (admin), get latest platform order
        if (hasPermission('view_analytics')) {
            $stmt = $db->prepare("
                SELECT order_id, status, order_date
                FROM orders 
                WHERE is_archive = 0 
                AND order_date >= CURDATE()
                ORDER BY order_date DESC
                LIMIT 1
            ");
            $stmt->execute();
            $latestOrder = $stmt->fetch();
            
            if ($latestOrder) {
                $timeAgo = getTimeAgo($latestOrder['order_date']);
                return "Order #{$latestOrder['order_id']} - {$timeAgo}";
            }
            return 'No orders today';
        }
        
        // If user is a vendor, get their latest order
        if ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($vendor)) {
                $vendorId = $vendor[0]['vendor_id'];
                $stmt = $db->prepare("
                    SELECT order_id, status, order_date
                    FROM orders 
                    WHERE vendor_id = ? 
                    AND is_archive = 0 
                    AND order_date >= CURDATE()
                    ORDER BY order_date DESC
                    LIMIT 1
                ");
                $stmt->execute([$vendorId]);
                $latestOrder = $stmt->fetch();
                
                if ($latestOrder) {
                    $timeAgo = getTimeAgo($latestOrder['order_date']);
                    return "Order #{$latestOrder['order_id']} - {$timeAgo}";
                }
            }
        }
        
        // If user is a customer, get their latest order
        if ($currentUser['role'] == 'customer') {
            $customerModel = new Customer();
            $customer = $customerModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($customer)) {
                $customerId = $customer[0]['customer_id'];
                $stmt = $db->prepare("
                    SELECT order_id, status, order_date
                    FROM orders 
                    WHERE customer_id = ? 
                    AND is_archive = 0 
                    AND order_date >= CURDATE()
                    ORDER BY order_date DESC
                    LIMIT 1
                ");
                $stmt->execute([$customerId]);
                $latestOrder = $stmt->fetch();
                
                if ($latestOrder) {
                    $timeAgo = getTimeAgo($latestOrder['order_date']);
                    return "Order #{$latestOrder['order_id']} - {$timeAgo}";
                }
            }
        }
        
        return 'No orders today';
        
    } catch (Exception $e) {
        error_log('Error getting latest order info: ' . $e->getMessage());
        return 'No orders today';
    }
}

// Helper function to get time ago
function getTimeAgo($datetime) {
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

// Function to get latest order time for activity display
function getLatestOrderTime($currentUser) {
    if (!$currentUser) return 'No recent activity';
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get latest order date based on user role
        if (hasPermission('view_analytics')) {
            $stmt = $db->prepare("
                SELECT order_date
                FROM orders 
                WHERE is_archive = 0 
                ORDER BY order_date DESC
                LIMIT 1
            ");
            $stmt->execute();
            $latestOrder = $stmt->fetch();
        } elseif ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($vendor)) {
                $vendorId = $vendor[0]['vendor_id'];
                $stmt = $db->prepare("
                    SELECT order_date
                    FROM orders 
                    WHERE vendor_id = ? 
                    AND is_archive = 0 
                    ORDER BY order_date DESC
                    LIMIT 1
                ");
                $stmt->execute([$vendorId]);
                $latestOrder = $stmt->fetch();
            }
        } elseif ($currentUser['role'] == 'customer') {
            $customerModel = new Customer();
            $customer = $customerModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($customer)) {
                $customerId = $customer[0]['customer_id'];
                $stmt = $db->prepare("
                    SELECT order_date
                    FROM orders 
                    WHERE customer_id = ? 
                    AND is_archive = 0 
                    ORDER BY order_date DESC
                    LIMIT 1
                ");
                $stmt->execute([$customerId]);
                $latestOrder = $stmt->fetch();
            }
        }
        
        if (isset($latestOrder) && $latestOrder) {
            return getTimeAgo($latestOrder['order_date']);
        }
        
        return 'No recent activity';
        
    } catch (Exception $e) {
        error_log('Error getting latest order time: ' . $e->getMessage());
        return 'No recent activity';
    }
}

// Function to get latest product information for current user
function getLatestProductInfo($currentUser) {
    if (!$currentUser) return 'No products added';
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // If user has manage_products permission (admin), get latest platform product
        if (hasPermission('manage_products')) {
            $stmt = $db->prepare("
                SELECT product_id, name, created_at
                FROM products 
                WHERE is_archive = 0 
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $stmt->execute();
            $latestProduct = $stmt->fetch();
            
            if ($latestProduct) {
                return $latestProduct['name'];
            }
            return 'No products added';
        }
        
        // If user is a vendor, get their latest product
        if ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($vendor)) {
                $vendorId = $vendor[0]['vendor_id'];
                $stmt = $db->prepare("
                    SELECT product_id, name, created_at
                    FROM products 
                    WHERE vendor_id = ? 
                    AND is_archive = 0 
                    ORDER BY created_at DESC
                    LIMIT 1
                ");
                $stmt->execute([$vendorId]);
                $latestProduct = $stmt->fetch();
                
                if ($latestProduct) {
                    return $latestProduct['name'];
                }
            }
        }
        
        return 'No products added';
        
    } catch (Exception $e) {
        error_log('Error getting latest product info: ' . $e->getMessage());
        return 'No products added';
    }
}

// Function to get latest product time for activity display
function getLatestProductTime($currentUser) {
    if (!$currentUser) return 'No recent activity';
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get latest product date based on user role
        if (hasPermission('manage_products')) {
            $stmt = $db->prepare("
                SELECT created_at
                FROM products 
                WHERE is_archive = 0 
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $stmt->execute();
            $latestProduct = $stmt->fetch();
        } elseif ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            
            if (!empty($vendor)) {
                $vendorId = $vendor[0]['vendor_id'];
                $stmt = $db->prepare("
                    SELECT created_at
                    FROM products 
                    WHERE vendor_id = ? 
                    AND is_archive = 0 
                    ORDER BY created_at DESC
                    LIMIT 1
                ");
                $stmt->execute([$vendorId]);
                $latestProduct = $stmt->fetch();
            }
        }
        
        if (isset($latestProduct) && $latestProduct) {
            return getTimeAgo($latestProduct['created_at']);
        }
        
        return 'No recent activity';
        
    } catch (Exception $e) {
        error_log('Error getting latest product time: ' . $e->getMessage());
        return 'No recent activity';
    }
}

// Function to get cart items count for current user
function getCartItemsCount($currentUser) {
    if (!$currentUser) return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("
            SELECT COUNT(*) as item_count
            FROM shopping_cart_items sci
            JOIN shopping_carts sc ON sci.cart_id = sc.cart_id
            WHERE sc.user_id = ? AND sc.is_active = 1
        ");
        $stmt->execute([$currentUser['user_id']]);
        return $stmt->fetch()['item_count'] ?? 0;
        
    } catch (Exception $e) {
        error_log('Error getting cart items count: ' . $e->getMessage());
        return 0;
    }
}

// Function to get latest registered user
function getLatestRegisteredUser() {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("
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
                'time_ago' => getTimeAgo($user['created_at'])
            ];
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log('Error getting latest registered user: ' . $e->getMessage());
        return null;
    }
}

// Function to get latest approved vendor
function getLatestApprovedVendor() {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("
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
                'time_ago' => getTimeAgo($vendor['approved_at'])
            ];
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log('Error getting latest approved vendor: ' . $e->getMessage());
        return null;
    }
}

// Function to get vendor count change percentage
function getVendorCountChange() {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get current month's vendor count
        $stmt = $db->prepare("
            SELECT COUNT(*) as current_count
            FROM vendors
            WHERE is_approved = 1 
            AND is_archive = 0
            AND approved_at >= DATE_FORMAT(NOW() ,'%Y-%m-01')
        ");
        $stmt->execute();
        $currentCount = $stmt->fetch()['current_count'];
        
        // Get last month's vendor count
        $stmt = $db->prepare("
            SELECT COUNT(*) as last_count
            FROM vendors
            WHERE is_approved = 1 
            AND is_archive = 0
            AND approved_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH) ,'%Y-%m-01')
            AND approved_at < DATE_FORMAT(NOW() ,'%Y-%m-01')
        ");
        $stmt->execute();
        $lastCount = $stmt->fetch()['last_count'];
        
        if ($lastCount > 0) {
            $percentChange = (($currentCount - $lastCount) / $lastCount) * 100;
            return round($percentChange);
        } else if ($currentCount > 0) {
            return 100; // If last month was 0 and this month has vendors, return 100%
        }
        
        return 0;
        
    } catch (Exception $e) {
        error_log('Error getting vendor count change: ' . $e->getMessage());
        return 0;
    }
}

// Function to get total number of active vendors
function getVendorCount() {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("
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

// Function to get order count change percentage
function getOrderCountChange($currentUser) {
    if (!$currentUser) return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Base query for yesterday's orders
        $baseQuery = "
            SELECT COUNT(*) as order_count
            FROM orders
            WHERE is_archive = 0
            AND DATE(order_date) = ?
        ";
        
        $whereClause = "";
        $params = [];
        
        // Add role-specific conditions
        if ($currentUser['role'] == 'vendor') {
            $vendorModel = new Vendor();
            $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
            if (!empty($vendor)) {
                $whereClause = " AND vendor_id = ?";
                $params[] = $vendor[0]['vendor_id'];
            }
        } elseif ($currentUser['role'] == 'customer') {
            $whereClause = " AND customer_id = ?";
            $params[] = $currentUser['user_id'];
        } elseif (!hasPermission('manage_orders')) {
            return 0;
        }
        
        // Get yesterday's count
        $yesterdayDate = date('Y-m-d', strtotime('-1 day'));
        $yesterdayParams = array_merge([$yesterdayDate], $params);
        $stmt = $db->prepare($baseQuery . $whereClause);
        $stmt->execute($yesterdayParams);
        $yesterdayCount = $stmt->fetch()['order_count'];
        
        // Get today's count
        $todayDate = date('Y-m-d');
        $todayParams = array_merge([$todayDate], $params);
        $stmt = $db->prepare($baseQuery . $whereClause);
        $stmt->execute($todayParams);
        $todayCount = $stmt->fetch()['order_count'];
        
        if ($yesterdayCount > 0) {
            $percentChange = (($todayCount - $yesterdayCount) / $yesterdayCount) * 100;
            return round($percentChange);
        } else if ($todayCount > 0) {
            return 100; // If yesterday was 0 and today has orders, return 100%
        }
        
        return 0;
        
    } catch (Exception $e) {
        error_log('Error getting order count change: ' . $e->getMessage());
        return 0;
    }
}

// Function to get total user count
function getUserCount() {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("
            SELECT COUNT(*) as user_count
            FROM users
            WHERE is_archive = 0
        ");
        $stmt->execute();
        return $stmt->fetch()['user_count'] ?? 0;
        
    } catch (Exception $e) {
        error_log('Error getting user count: ' . $e->getMessage());
        return 0;
    }
}

// Function to get user count change percentage
function getUserCountChange() {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get current month's user count
        $stmt = $db->prepare("
            SELECT COUNT(*) as current_count
            FROM users
            WHERE is_archive = 0
            AND created_at >= DATE_FORMAT(NOW() ,'%Y-%m-01')
        ");
        $stmt->execute();
        $currentCount = $stmt->fetch()['current_count'];
        
        // Get last month's user count
        $stmt = $db->prepare("
            SELECT COUNT(*) as last_count
            FROM users
            WHERE is_archive = 0
            AND created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH) ,'%Y-%m-01')
            AND created_at < DATE_FORMAT(NOW() ,'%Y-%m-01')
        ");
        $stmt->execute();
        $lastCount = $stmt->fetch()['last_count'];
        
        if ($lastCount > 0) {
            $percentChange = (($currentCount - $lastCount) / $lastCount) * 100;
            return round($percentChange);
        } else if ($currentCount > 0) {
            return 100; // If last month was 0 and this month has users, return 100%
        }
        
        return 0;
        
    } catch (Exception $e) {
        error_log('Error getting user count change: ' . $e->getMessage());
        return 0;
    }
}

// Function to get recent activities
function getRecentActivities($currentUser, $limit = 5) {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $activities = [];
        
        // Get recent orders
        if (hasPermission('manage_orders') || hasPermission('view_orders')) {
            $orderQuery = "
                SELECT 
                    'order' as type,
                    o.order_id,
                    o.order_date as activity_date,
                    o.status,
                    CONCAT('Order #', o.order_id, ' - ', o.status) as description
                FROM orders o
                WHERE o.is_archive = 0
            ";
            
            if ($currentUser['role'] == 'vendor') {
                $vendorModel = new Vendor();
                $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
                if (!empty($vendor)) {
                    $orderQuery .= " AND o.vendor_id = " . $vendor[0]['vendor_id'];
                }
            } elseif ($currentUser['role'] == 'customer') {
                $orderQuery .= " AND o.customer_id = " . $currentUser['user_id'];
            }
            
            $orderQuery .= " ORDER BY o.order_date DESC LIMIT 5";
            
            $stmt = $db->prepare($orderQuery);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                $activities[] = [
                    'type' => 'order',
                    'icon' => 'fa-shopping-cart',
                    'description' => $row['description'],
                    'date' => $row['activity_date']
                ];
            }
        }
        
        // Get recent products (if applicable)
        if (hasPermission('manage_products')) {
            $productQuery = "
                SELECT 
                    'product' as type,
                    p.product_id,
                    p.name,
                    p.created_at as activity_date
                FROM products p
                WHERE p.is_archive = 0
            ";
            
            if ($currentUser['role'] == 'vendor') {
                $vendorModel = new Vendor();
                $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
                if (!empty($vendor)) {
                    $productQuery .= " AND p.vendor_id = " . $vendor[0]['vendor_id'];
                }
            }
            
            $productQuery .= " ORDER BY p.created_at DESC LIMIT 5";
            
            $stmt = $db->prepare($productQuery);
            $stmt->execute();
            while ($row = $stmt->fetch()) {
                $activities[] = [
                    'type' => 'product',
                    'icon' => 'fa-box',
                    'description' => "New product added: " . $row['name'],
                    'date' => $row['activity_date']
                ];
            }
        }
        
        // Get recent user registrations (if admin)
        if (hasPermission('manage_users')) {
            $stmt = $db->prepare("
                SELECT 
                    'user' as type,
                    user_id,
                    username,
                    created_at as activity_date
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
                    'description' => "New user registered: " . $row['username'],
                    'date' => $row['activity_date']
                ];
            }
        }
        
        // Sort all activities by date
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($activities, 0, $limit);
        
    } catch (Exception $e) {
        error_log('Error getting recent activities: ' . $e->getMessage());
        return [];
    }
}

// Function to get customer's total spent amount
function getCustomerTotalSpent($currentUser) {
    if (!$currentUser || $currentUser['role'] !== 'customer') return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) as total_spent
            FROM orders
            WHERE customer_id = ?
            AND status = 'completed'
            AND is_archive = 0
            AND order_date >= DATE_FORMAT(NOW() ,'%Y-%m-01')
        ");
        $stmt->execute([$currentUser['user_id']]);
        return $stmt->fetch()['total_spent'] ?? 0;
        
    } catch (Exception $e) {
        error_log('Error getting customer total spent: ' . $e->getMessage());
        return 0;
    }
}

// Function to get customer's order count
function getCustomerOrderCount($currentUser) {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $db->prepare("
            SELECT COUNT(*) as order_count
            FROM orders
            WHERE customer_id = ?
            AND is_archive = 0
        ");
        $stmt->execute([$currentUser['user_id']]);
        return $stmt->fetch()['order_count'] ?? 0;
        
    } catch (Exception $e) {
        error_log('Error getting customer order count: ' . $e->getMessage());
        return 0;
    }
}

// Function to get vendor's pending orders count
function getVendorPendingOrders($currentUser) {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get vendor_id from user_id
        $vendorModel = new Vendor();
        $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
        if (empty($vendor)) {
            return 0;
        }
        
        $stmt = $db->prepare("
            SELECT COUNT(*) as pending_count
            FROM orders
            WHERE vendor_id = ?
            AND status IN ('pending', 'processing')
            AND is_archive = 0
        ");
        $stmt->execute([$vendor[0]['vendor_id']]);
        return $stmt->fetch()['pending_count'] ?? 0;
        
    } catch (Exception $e) {
        error_log('Error getting vendor pending orders: ' . $e->getMessage());
        return 0;
    }
}

// Function to get vendor's total revenue
function getVendorTotalRevenue($currentUser) {
    if (!$currentUser || $currentUser['role'] !== 'vendor') return 0;
    
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get vendor_id from user_id
        $vendorModel = new Vendor();
        $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
        if (empty($vendor)) {
            return 0;
        }
        
        $stmt = $db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) as total_revenue
            FROM orders
            WHERE vendor_id = ?
            AND status = 'completed'
            AND is_archive = 0
            AND order_date >= DATE_FORMAT(NOW() ,'%Y-%m-01')
        ");
        $stmt->execute([$vendor[0]['vendor_id']]);
        return $stmt->fetch()['total_revenue'] ?? 0;
        
    } catch (Exception $e) {
        error_log('Error getting vendor total revenue: ' . $e->getMessage());
        return 0;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getDashboardTitle($currentUser); ?> - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../components/main.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Shared Sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Shared Header -->
            <?php 
            $pageTitle = getDashboardTitle($currentUser);
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <?php if ($currentUser['role'] == 'staff'): ?>
                    <?php
                    require_once __DIR__ . '/../../services/StaffService.php';
                    require_once __DIR__ . '/../../models/Notification.php';
                    $staffService = new StaffService();
                    $staffModel = new Staff();
                    $userModel = new User();
                    $notificationModel = new Notification();

                    // Get staff_id from user_id
                    $staff = $staffModel->findAll(['user_id' => $currentUser['user_id']]);
                    $staffId = $staff[0]['staff_id'] ?? null;

                    // Get tasks
                    $tasks = $staffId ? $staffService->getStaffTasks($staffId) : [];
                    $pendingTasks = array_filter($tasks, function($t) { return $t['status'] !== 'completed'; });
                    $completedTasks = array_filter($tasks, function($t) { return $t['status'] === 'completed'; });

                    // Get performance
                    $performance = $staffId ? $staffService->getStaffPerformance($staffId) : [];

                    // Get notifications
                    $notifications = $notificationModel->findAll(['user_id' => $currentUser['user_id'], 'is_read' => 0], 5);
                    $unreadCount = count($notifications);
                    ?>
                    <div class="dashboard-overview">
                        <div class="stats-grid" style="margin-bottom: 2rem;">
                            <div class="stat-card">
                                <div class="stat-icon" style="background: #6b7280;"><i class="fas fa-info-circle"></i></div>
                                <div class="stat-info">
                                    <h3><?php echo htmlspecialchars($currentUser['username']); ?></h3>
                                    <p>Role: Staff</p>
                                    <span class="stat-change neutral">Active</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon" style="background: #2563eb;"><i class="fas fa-list-check"></i></div>
                                <div class="stat-info">
                                    <h3><?php echo count($pendingTasks); ?></h3>
                                    <p>Assigned Tasks</p>
                                    <span class="stat-change <?php echo count($pendingTasks) > 0 ? 'warning' : 'positive'; ?>">
                                        <?php echo count($pendingTasks) > 0 ? 'Pending' : 'All Clear'; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon" style="background: #059669;"><i class="fas fa-bell"></i></div>
                                <div class="stat-info">
                                    <h3><?php echo $unreadCount; ?></h3>
                                    <p>Unread Notifications</p>
                                    <span class="stat-change <?php echo $unreadCount > 0 ? 'warning' : 'neutral'; ?>">
                                        <?php echo $unreadCount > 0 ? 'New Updates' : 'All Clear'; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon" style="background: #f59e0b;"><i class="fas fa-star"></i></div>
                                <div class="stat-info">
                                    <h3><?php echo $performance['tasks_completed'] ?? 0; ?></h3>
                                    <p>Tasks Completed</p>
                                    <span class="stat-change positive">
                                        <?php 
                                        $completionRate = isset($performance['total_tasks']) && $performance['total_tasks'] > 0 
                                            ? round(($performance['tasks_completed'] / $performance['total_tasks']) * 100) 
                                            : 0;
                                        echo $completionRate . '% Success Rate';
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- My Assigned Tasks Table -->
                        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); margin-bottom: 2rem; padding: 0 0 1.5rem 0; border: 1px solid #fbbf24;">
                            <h3 style="padding: 1rem 1.5rem 0.5rem 1.5rem; font-weight: 600;">My Assigned Tasks</h3>
                            <div style="overflow-x:auto;">
                                <table style="width:100%; border-collapse:collapse; background: #fffbeb;">
                                    <thead>
                                        <tr style="background: #fef3c7;">
                                            <th style="padding: 0.75rem; text-align:left;">Title</th>
                                            <th style="padding: 0.75rem; text-align:left;">Description</th>
                                            <th style="padding: 0.75rem; text-align:left;">Assigned Date</th>
                                            <th style="padding: 0.75rem; text-align:left;">Priority</th>
                                            <th style="padding: 0.75rem; text-align:left;">Status</th>
                                            <th style="padding: 0.75rem; text-align:left;">Due Date</th>
                                            <th style="padding: 0.75rem; text-align:left;">Completed Date</th>
                                            <th style="padding: 0.75rem; text-align:center;">Complete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($tasks && count($tasks) > 0): ?>
                                            <?php foreach ($tasks as $task): ?>
                                                <tr>
                                                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($task['title']); ?></td>
                                                    <td style="padding: 0.75rem;"><?php echo htmlspecialchars($task['description'] ?? ''); ?></td>
                                                    <td style="padding: 0.75rem;"><?php 
                                                        if ($task['assigned_date']) {
                                                            $date = new DateTime($task['assigned_date']);
                                                            echo $date->format('M j, Y h:i A');
                                                        } else {
                                                            echo '-';
                                                        }
                                                    ?></td>
                                                    <td style="padding: 0.75rem;"><?php echo ucfirst($task['priority']); ?></td>
                                                    <td style="padding: 0.75rem;"><?php echo ucfirst($task['status']); ?></td>
                                                    <td style="padding: 0.75rem;"><?php 
                                                        if ($task['due_date']) {
                                                            $date = new DateTime($task['due_date']);
                                                            echo $date->format('M j, Y h:i A');
                                                        } else {
                                                            echo '-';
                                                        }
                                                    ?></td>
                                                    <td style="padding: 0.75rem;">
                                                        <?php 
                                                        if ($task['completed_date']) {
                                                            $date = new DateTime($task['completed_date']);
                                                            echo $date->format('M j, Y h:i A');
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td style="padding: 0.75rem; text-align:center;">
                                                        <form method="POST" action="" style="margin:0;">
                                                            <input type="hidden" name="complete_task_id" value="<?php echo $task['task_id']; ?>">
                                                            <input type="checkbox" name="is_completed" value="1" <?php echo strtolower($task['status']) === 'completed' ? 'checked' : ''; ?> onchange="this.form.submit()">
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="8" style="padding: 0.75rem; text-align:center; color:#888;">No tasks assigned.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Recent Notifications Card -->
                        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); margin-bottom: 2rem; padding: 0 0 1.5rem 0;">
                            <h3 style="padding: 1rem 1.5rem 0.5rem 1.5rem; font-weight: 600;">Recent Notifications</h3>
                            <div style="padding: 0 1.5rem;">
                                <?php if ($notifications && count($notifications) > 0): ?>
                                    <ul style="list-style:none; margin:0; padding:0;">
                                        <?php foreach ($notifications as $note): ?>
                                            <li style="padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                                                <span style="font-weight:600;"><i class="fas fa-bell"></i> <?php echo htmlspecialchars($note['title'] ?? $note['message']); ?></span>
                                                <span style="float:right; color:#888; font-size:0.95em;"> <?php echo isset($note['created_at']) ? date('M j, Y g:i A', strtotime($note['created_at'])) : ''; ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div style="color:#888; padding: 0.75rem 0;">No new notifications.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Recent Activities Card -->
                        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.03); margin-bottom: 2rem; padding: 0 0 1.5rem 0;">
                            <div style="display:flex; align-items:center; justify-content:space-between; padding: 1rem 1.5rem 0.5rem 1.5rem;">
                                <h3 style="font-weight: 600;">Recent Activities</h3>
                                <a href="#" style="color: #2563eb; font-weight: 500; text-decoration: none;">View All</a>
                            </div>
                            <div style="padding: 0 1.5rem; color:#888;">No recent activities.</div>
                        </div>
                    </div>
                <?php else: ?>
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <!-- Debug: Show user role and permissions -->
                    <?php if (!hasPermission('manage_users') && !hasPermission('manage_vendors') && !hasPermission('manage_products') && !hasPermission('place_orders')): ?>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #6b7280;">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Welcome</h3>
                            <p>Role: <?php echo ucfirst($currentUser['role'] ?? 'Unknown'); ?></p>
                            <span class="stat-change neutral">Dashboard loading...</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Admin Stats -->
                    <?php if (hasPermission('manage_users')): ?>
                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo getUserCount(); ?></h3>
                            <p>Total Users</p>
                            <?php $userChange = getUserCountChange(); ?>
                            <span class="stat-change <?php echo $userChange >= 0 ? 'positive' : 'negative'; ?>">
                                <?php echo ($userChange >= 0 ? '+' : '') . $userChange; ?>% from last month
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('manage_vendors')): ?>
                    <div class="stat-card">
                        <div class="stat-icon vendors">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo getVendorCount(); ?></h3>
                            <p>Active Vendors</p>
                            <?php $vendorChange = getVendorCountChange(); ?>
                            <span class="stat-change <?php echo $vendorChange >= 0 ? 'positive' : 'negative'; ?>">
                                <?php echo ($vendorChange >= 0 ? '+' : '') . $vendorChange; ?>% from last month
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Product Stats -->
                    <?php if (hasPermission('manage_products') || hasPermission('manage_inventory')): ?>
                    <div class="stat-card">
                        <div class="stat-icon products">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo getProductCount($currentUser); ?></h3>
                            <p><?php echo hasPermission('manage_products') ? 'Total Products' : 'My Products'; ?></p>
                            <?php $productChange = getProductCountChange($currentUser); ?>
                            <span class="stat-change <?php echo $productChange >= 0 ? 'positive' : 'negative'; ?>">
                                <?php echo ($productChange >= 0 ? '+' : '') . $productChange; ?>% from last month
                            </span>
                            <div class="subscription-actions" style="margin-top: 0.5rem;">
                                <a href="/agrimarket-erd/v1/product-management/" class="btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-box"></i>
                                    Product Management
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Order Stats -->
                    <?php if (hasPermission('manage_orders') || hasPermission('view_orders')): ?>
                    <div class="stat-card">
                        <div class="stat-icon orders">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo getOrderCount($currentUser); ?></h3>
                            <p><?php echo hasPermission('manage_orders') ? 'Orders Today' : 'My Orders Today'; ?></p>
                            <?php $orderChange = getOrderCountChange($currentUser); ?>
                            <span class="stat-change <?php echo $orderChange >= 0 ? 'positive' : 'negative'; ?>">
                                <?php echo ($orderChange >= 0 ? '+' : '') . $orderChange; ?>% from yesterday
                            </span>
                            <div class="total-orders" style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                Total Orders: <?php echo getTotalOrderCount($currentUser); ?>
                            </div>
                            <div class="subscription-actions" style="margin-top: 0.5rem;">
                                <a href="/agrimarket-erd/v1/order-management/" class="btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-calculator"></i>
                                    Order Management
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Customer Stats -->
                    <?php if ($currentUser['role'] == 'customer'): ?>
                    <div class="stat-card">
                        <div class="stat-icon cart">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo getCartItemsCount($currentUser); ?></h3>
                            <p>Items in Cart</p>
                            <span class="stat-change neutral">Ready to checkout</span>
                            <div class="subscription-actions" style="margin-top: 0.5rem;">
                                <a href="/agrimarket-erd/v1/shopping-cart/" class="btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-shopping-cart"></i>
                                    View Cart
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon orders">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo getCustomerOrderCount($currentUser); ?></h3>
                            <p>Total Orders</p>
                            <span class="stat-change neutral">
                                RM <?php echo number_format(getCustomerTotalSpent($currentUser), 2); ?> spent
                            </span>
                            <div class="subscription-actions" style="margin-top: 0.5rem;">
                                <a href="/agrimarket-erd/v1/order-management/" class="btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-list"></i>
                                    View Orders
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Vendor Stats -->
                    <?php if ($currentUser['role'] == 'vendor'): ?>
                    <div class="stat-card">
                        <div class="stat-icon orders">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo getVendorPendingOrders($currentUser); ?></h3>
                            <p>Pending Orders</p>
                            <span class="stat-change warning">Needs attention</span>
                            <div class="subscription-actions" style="margin-top: 0.5rem;">
                                <a href="/agrimarket-erd/v1/order-management/" class="btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-tasks"></i>
                                    Process Orders
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>RM <?php echo number_format(getVendorTotalRevenue($currentUser), 2); ?></h3>
                            <p>Total Revenue</p>
                            <span class="stat-change positive">From completed orders</span>
                            <div class="subscription-actions" style="margin-top: 0.5rem;">
                                <a href="/agrimarket-erd/v1/analytics/" class="btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-chart-line"></i>
                                    View Analytics
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Report Stats -->
                    <?php if (hasPermission('view_analytics') || hasPermission('view_reports') || $currentUser['role'] == 'vendor' || $currentUser['role'] == 'admin'): ?>
                    <div class="stat-card">
                        <div class="stat-icon reports" style="background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 0.75rem; font-size: 1.5rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>RM <?php echo number_format(getRevenueAmount($currentUser), 2); ?></h3>
                            <p><?php echo hasPermission('view_analytics') ? 'Total Revenue' : 'My Revenue'; ?></p>
                            <div class="subscription-actions" style="margin-top: 0.5rem;">
                                <a href="/agrimarket-erd/v1/analytics/" class="btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-chart-line"></i>
                                    View Analytics
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    
                    <!-- Subscription Tier Card for Vendors -->
                    <?php if ($currentUser['role'] == 'vendor'): ?>
                        <?php $subscriptionDetails = getVendorSubscriptionDetails($currentUser); ?>
                        <?php if ($subscriptionDetails): ?>
                            <div class="stat-card">
                                <div class="stat-icon subscription" style="background: <?php echo getSubscriptionTierColor($subscriptionDetails['tier_name']); ?>; color: white; display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 0.75rem; font-size: 1.5rem;">
                                    <i class="fas fa-gem"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo htmlspecialchars($subscriptionDetails['tier_name']) . ' Tier'; ?></h3>
                                    <p><?php echo htmlspecialchars($subscriptionDetails['description']); ?></p>
                                    <span class="stat-change <?php echo $subscriptionDetails['is_active'] ? 'positive' : 'negative'; ?>">
                                        RM <?php echo number_format($subscriptionDetails['monthly_fee'], 2); ?> / month
                                        <?php if ($subscriptionDetails['due_date']): ?>
                                            • Due: <?php echo date('M j', strtotime($subscriptionDetails['due_date'])); ?>
                                        <?php endif; ?>
                                    </span>
                                    <div class="subscription-actions" style="margin-top: 0.5rem;">
                                        <a href="/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard" class="btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <i class="fas fa-edit"></i>
                                            Change Plan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Debug: Show if subscription details are not found -->
                            <div class="stat-card">
                                <div class="stat-icon subscription" style="background: linear-gradient(135deg, #6b7280, #4b5563); color: white; display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 0.75rem; font-size: 1.5rem;">
                                    <i class="fas fa-gem"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>No Subscription</h3>
                                    <p>Subscription details not found</p>
                                    <span class="stat-change neutral">Contact support</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>


                </div>
                
                <!-- Content Sections -->
                <div class="content-grid">
                    <!-- Recent Activities -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3>Recent Activities</h3>
                        </div>
                        <div class="card-content">
                            <div class="activity-list">
                                <?php 
                                $recentActivities = getRecentActivities($currentUser);
                                if (!empty($recentActivities)):
                                    foreach ($recentActivities as $activity):
                                ?>
                                <div class="activity-item">
                                    <div class="activity-icon <?php echo $activity['type']; ?>">
                                        <i class="fas <?php echo $activity['icon']; ?>"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p><?php echo htmlspecialchars($activity['description']); ?></p>
                                        <span class="activity-time"><?php echo getTimeAgo($activity['date']); ?></span>
                                    </div>
                                </div>
                                <?php 
                                    endforeach;
                                else:
                                ?>
                                <div class="activity-item">
                                    <div class="activity-details">
                                        <p>No recent activities</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3>Quick Actions</h3>
                        </div>
                        <div class="card-content">
                            <div class="quick-actions">
                                <?php if (hasPermission('manage_users')): ?>
                                <button class="action-btn primary" onclick="window.location.href='/agrimarket-erd/v1/user-management/'">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Add New User</span>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('manage_products')): ?>
                                <button class="action-btn success" onclick="window.location.href='/agrimarket-erd/v1/product-management/'">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Add Product</span>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('place_orders')): ?>
                                <button class="action-btn warning" onclick="window.location.href='/agrimarket-erd/v1/products/'">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Continue Shopping</span>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('customer_support')): ?>
                                <button class="action-btn danger" onclick="window.location.href='/agrimarket-erd/v1/support/'">
                                    <i class="fas fa-headset"></i>
                                    <span>Support Ticket</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script>
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (window.innerWidth > 768) {
                // Desktop - reset mobile classes
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                // Mobile - reset desktop classes
                sidebar.classList.remove('collapsed');
            }
        });
        
        // Navigation active state
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
               
                e.preventDefault();
                // Remove active class from all items
                document.querySelectorAll('.sidebar-nav li').forEach(item => {
                    item.classList.remove('active');
                });
                // Add active class to clicked item
                this.parentElement.classList.add('active');
                // Load content for the selected section
                const href = this.getAttribute('href');
                const sectionName = this.querySelector('span').textContent;
                if (href.startsWith('/agrimarket-erd/v1/')) {
                    window.location.href = href;
                } else if (href.startsWith('#')) {
                    loadDashboardSection(href.substring(1), sectionName);
                } else if (href.endsWith('.php')) {
                    window.location.href = href;
                } else {
                    console.log('Loading section:', href);
                }
            });
        });
        
        // Function to load dashboard sections dynamically
        function loadDashboardSection(section, sectionName) {
            const contentArea = document.querySelector('.dashboard-content');
            
            // Show loading state
            contentArea.innerHTML = `
                <div class="loading-container" style="text-align: center; padding: 60px 20px;">
                    <div class="loading-spinner" style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3b82f6; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <p style="margin-top: 20px; color: #6b7280;">Loading ${sectionName}...</p>
                </div>
                <style>
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            `;
            
            // Simulate loading delay and then load appropriate content
            setTimeout(() => {
                console.log('Section:', section);
                switch(section) {
                    case 'dashboard':
                        loadDashboardHome();
                        break;
                    case 'vendors':
                        loadVendorManagement();
                        break;
                    case 'staff':
                        loadStaffManagement();
                        break;
                    case 'products':
                        loadProductManagement();
                        break;
                    case 'inventory':
                        loadInventoryManagement();
                        break;
                    case 'orders':
                        loadOrderManagement();
                        break;
                    case 'shop':
                        loadShopSection();
                        break;
                    case 'cart':
                        loadCartSection();
                        break;
                    case 'analytics':
                        loadAnalytics();
                        break;
                    case 'support':
                        loadCustomerSupport();
                        break;
                    case 'promotions':
                        loadPromotions();
                        break;
                    case 'settings':
                        loadSystemSettings();
                        break;
                    default:
                        loadComingSoon(sectionName);
                }
            }, 500);
        }
        
        // Function to load dashboard home content
        function loadDashboardHome() {
            const contentArea = document.querySelector('.dashboard-content');
            contentArea.innerHTML = `
                <!-- Dashboard Home Content (current content) -->
                <div class="dashboard-overview">
                    <div class="welcome-section">
                        <h1><?php echo getDashboardTitle($currentUser); ?></h1>
                        <p>Welcome back, <?php echo htmlspecialchars($currentUser['name']); ?>! Here's what's happening with your account.</p>
                    </div>
                    
                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <!-- Your existing stats cards would go here -->
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Dashboard</h3>
                                <p>Overview of your activities</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content Grid -->
                    <div class="content-grid">
                        <!-- Recent Activities -->
                        <div class="content-card">
                            <div class="card-header">
                                <h3>Recent Activities</h3>
                                <button class="btn-secondary">View All</button>
                            </div>
                            <div class="card-content">
                                <div class="activity-list">
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="activity-details">
                                            <p>Welcome to your dashboard!</p>
                                            <span class="activity-time">Just now</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="content-card">
                            <div class="card-header">
                                <h3>Quick Actions</h3>
                            </div>
                            <div class="card-content">
                                <div class="quick-actions">
                                    <button class="action-btn primary" onclick="alert('Quick action functionality')">
                                        <i class="fas fa-plus"></i>
                                        <span>Quick Action</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Function to load coming soon content for unimplemented sections
        function loadComingSoon(sectionName) {
            const contentArea = document.querySelector('.dashboard-content');
            contentArea.innerHTML = `
                <div class="coming-soon-container" style="text-align: center; padding: 80px 20px;">
                    <div class="coming-soon-icon" style="font-size: 64px; color: #d1d5db; margin-bottom: 24px;">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h2 style="color: #374151; margin-bottom: 16px;">${sectionName} Coming Soon</h2>
                    <p style="color: #6b7280; font-size: 18px; max-width: 500px; margin: 0 auto 32px;">
                        We're working hard to bring you this feature. It will be available in a future update.
                    </p>
                    <button class="btn btn-primary" onclick="loadDashboardSection('dashboard', 'Dashboard')" style="padding: 12px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-size: 16px; cursor: pointer;">
                        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                        Back to Dashboard
                    </button>
                </div>
            `;
        }
        
        // Specific content loaders for different sections
        function loadVendorManagement() {
            loadComingSoon('Vendor Management');
        }
        
        function loadStaffManagement() {
            loadComingSoon('Staff Management');
        }
        
        function loadProductManagement() {
            loadComingSoon('Product Management');
        }
        
        function loadInventoryManagement() {
            loadComingSoon('Inventory Management');
        }
        
        function loadOrderManagement() {
            loadComingSoon('Order Management');
        }
        
        function loadShopSection() {
            const contentArea = document.querySelector('.dashboard-content');
            contentArea.innerHTML = `
                <div style="text-align:center;padding:60px 20px;">
                    <div class="loading-spinner" style="display:inline-block;width:40px;height:40px;border:4px solid #f3f3f3;border-top:4px solid #3b82f6;border-radius:50%;animation:spin 1s linear infinite;"></div>
                    <p style="margin-top:20px;color:#6b7280;">Loading Shop Products...</p>
                </div>
            `;
            fetch('/agrimarket-erd/v1/shop/partial.php')
                .then(res => res.text())
                .then(html => {
                    contentArea.innerHTML = html;
                });
            return;
        }
        
        function loadCartSection() {
          
        }
        
        function loadAnalytics() {
            
        }
        
        function loadCustomerSupport() {
            loadComingSoon('Customer Support');
        }
        
        function loadPromotions() {
            loadComingSoon('Promotions');
        }
        
        function loadSystemSettings() {
            loadComingSoon('System Settings');
        }
        
        // Notification functionality
        // document.querySelector('.notification-btn').addEventListener('click', function() {
        //     alert('Notification panel would open here');
        // });
        
        // Quick action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.querySelector('span').textContent;
                alert(`${action} functionality would be implemented here`);
            });
        });
        
        const shopLink = document.getElementById('sidebar-shop-link');
        if (shopLink) {
            shopLink.addEventListener('click', function(e) {
                e.preventDefault();
                loadShopSection();
                return false;
            });
        }
        
    </script>
    <script src="/agrimarket-erd/v1/components/page_tracking.js"></script>
</body>
</html> 