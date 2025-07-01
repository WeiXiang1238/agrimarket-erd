<?php
// Set timezone to Asia/Kuala_Lumpur for Malaysian time
date_default_timezone_set('Asia/Kuala_Lumpur');

require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/PermissionService.php';
require_once __DIR__ . '/../../services/DashboardService.php';
require_once __DIR__ . '/../../services/NotificationService.php';
require_once __DIR__ . '/../../services/StaffService.php';
require_once __DIR__ . '/../../models/Staff.php';
require_once __DIR__ . '/../../models/StaffTask.php';

$authService = new AuthService();
$permissionService = new PermissionService();
$notificationService = new NotificationService();
$dashboardService = new DashboardService();

// Require authentication (any authenticated user can access)
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');

$currentUser = $authService->getCurrentUser();
$csrfToken = $authService->generateCSRFToken();

// Set page title for tracking using DashboardService
$pageTitle = $dashboardService->getDashboardTitle($currentUser);

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

// All dashboard functions moved to DashboardService for better separation of concerns



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



// Get staff data for staff dashboard
if ($currentUser['role'] == 'staff') {
    $tasks = $dashboardService->getStaffTasksData($currentUser['user_id']);
    $performance = $dashboardService->getStaffPerformanceData($currentUser['user_id']);
    $pendingTasks = array_filter($tasks, function($t) { return $t['status'] !== 'completed'; });
    $completedTasks = array_filter($tasks, function($t) { return $t['status'] === 'completed'; });
    $notifications = $notificationService->getUserNotifications($currentUser['user_id'], 5);
        }
        
// Rest of functions moved to DashboardService







?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $dashboardService->getDashboardTitle($currentUser); ?> - AgriMarket Solutions</title>
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
            $pageTitle = $dashboardService->getDashboardTitle($currentUser);
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <?php if ($currentUser['role'] == 'staff'): ?>
                    <?php
                    // Use DashboardService for staff data
                    $tasks = $dashboardService->getStaffTasksData($currentUser['user_id']);
                    $performance = $dashboardService->getStaffPerformanceData($currentUser['user_id']);
                    $pendingTasks = array_filter($tasks, function($t) { return $t['status'] !== 'completed'; });
                    $completedTasks = array_filter($tasks, function($t) { return $t['status'] === 'completed'; });
                    $unreadCount = count(array_filter($userNotifications, function($n) { return !$n['is_read']; }));
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
                            <h3><?php echo $dashboardService->getUserCount(); ?></h3>
                            <p>Total Users</p>
                            <?php $userChange = $dashboardService->getUserCountChange(); ?>
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
                            <h3><?php echo $dashboardService->getVendorCount(); ?></h3>
                            <p>Active Vendors</p>
                            <?php $vendorChange = $dashboardService->getVendorCountChange(); ?>
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
                            <h3><?php echo $dashboardService->getProductCount($currentUser); ?></h3>
                            <p><?php echo hasPermission('manage_products') ? 'Total Products' : 'My Products'; ?></p>
                            <?php $productChange = $dashboardService->getProductCountChange($currentUser); ?>
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
                            <h3><?php echo $dashboardService->getOrderCount($currentUser); ?></h3>
                            <p><?php echo hasPermission('manage_orders') ? 'Orders Today' : 'My Orders Today'; ?></p>
                            <?php $orderChange = $dashboardService->getOrderCountChange($currentUser); ?>
                            <span class="stat-change <?php echo $orderChange >= 0 ? 'positive' : 'negative'; ?>">
                                <?php echo ($orderChange >= 0 ? '+' : '') . $orderChange; ?>% from yesterday
                            </span>
                            <div class="total-orders" style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                Total Orders: <?php echo $dashboardService->getTotalOrderCount($currentUser); ?>
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
                            <h3><?php echo $dashboardService->getCartItemsCount($currentUser); ?></h3>
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
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $dashboardService->getCustomerPendingOrders($currentUser); ?></h3>
                            <p>Pending Orders</p>
                            <?php $pendingCount = $dashboardService->getCustomerPendingOrders($currentUser); ?>
                            <span class="stat-change <?php echo $pendingCount > 0 ? 'warning' : 'neutral'; ?>">
                                <?php echo $pendingCount > 0 ? 'Track progress' : 'No pending orders'; ?>
                            </span>
                            <div class="subscription-actions" style="margin-top: 0.5rem;">
                                <a href="/agrimarket-erd/v1/order-management/" class="btn-secondary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <i class="fas fa-eye"></i>
                                    Track Orders
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon orders">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $dashboardService->getCustomerOrderCount($currentUser); ?></h3>
                            <p>Total Orders</p>
                            <span class="stat-change positive">
                                RM <?php echo number_format($dashboardService->getCustomerTotalSpent($currentUser), 2); ?> total spent
                            </span>
                            <div class="total-orders" style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                This Month: RM <?php echo number_format($dashboardService->getCustomerMonthlySpent($currentUser), 2); ?>
                            </div>
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
                            <h3><?php echo $dashboardService->getVendorPendingOrders($currentUser); ?></h3>
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
                        <div class="stat-icon reports">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>RM <?php echo number_format($dashboardService->getVendorTotalRevenue($currentUser), 2); ?></h3>
                            <p>Total Revenue</p>
                            <span class="stat-change positive">From completed orders</span>
                            <div class="total-orders" style="margin-top: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                This Month: RM <?php echo number_format($dashboardService->getVendorMonthlyRevenue($currentUser), 2); ?>
                            </div>
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
                    <?php if ((hasPermission('view_analytics') || hasPermission('view_reports')) && $currentUser['role'] == 'admin'): ?>
                    <div class="stat-card">
                        <div class="stat-icon reports" style="background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 0.75rem; font-size: 1.5rem;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>RM <?php echo number_format($dashboardService->getRevenueAmount($currentUser), 2); ?></h3>
                            <p>Total Revenue</p>
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
                        <?php $subscriptionDetails = $dashboardService->getVendorSubscriptionDetails($currentUser); ?>
                        <?php if ($subscriptionDetails): ?>
                            <div class="stat-card">
                                <div class="stat-icon subscription" style="background: <?php echo $dashboardService->getSubscriptionTierColor($subscriptionDetails['tier_name']); ?>; color: white; display: flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 0.75rem; font-size: 1.5rem;">
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
                        <div class="card-content" style="padding: 1.5rem;">
                            <div class="activity-list">
                                <?php 
                                $recentActivities = $dashboardService->getRecentActivities($currentUser, $userPermissions);
                                if (!empty($recentActivities)):
                                    foreach ($recentActivities as $activity):
                                ?>
                                <div class="activity-item">
                                    <div class="activity-icon <?php echo $activity['type']; ?>">
                                        <i class="fas <?php echo $activity['icon']; ?>"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p><?php echo htmlspecialchars($activity['description']); ?></p>
                                        <span class="activity-time"><?php echo $dashboardService->getTimeAgo($activity['date']); ?></span>
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
                        <h1><?php echo $dashboardService->getDashboardTitle($currentUser); ?></h1>
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