<!-- Sidebar CSS -->
<link rel="stylesheet" href="/agrimarket-erd/v1/components/sidebar.css">

<?php
// Ensure session is started and get current user if not already set
if (!isset($_SESSION)) {
    session_start();
}

// Get current user if not already set
if (!isset($currentUser) && isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../services/AuthService.php';
    $authService = new AuthService();
    $currentUser = $authService->getCurrentUser();
}

// Include PermissionService if not already included
if (!class_exists('PermissionService')) {
    require_once __DIR__ . '/../../services/PermissionService.php';
}

// Get user permissions using PermissionService
$userPermissions = [];
if (isset($currentUser)) {
    try {
        $permissionService = new PermissionService();
        $userPermissions = $permissionService->getEffectivePermissions($currentUser);
    } catch (Exception $e) {
        // Fallback to basic role-based permissions if PermissionService fails
        $role = $currentUser['role'] ?? 'customer';
        switch ($role) {
            case 'admin':
                $userPermissions = ['manage_users', 'manage_vendors', 'manage_products', 'manage_orders', 'view_analytics', 'manage_system', 'manage_staff', 'manage_promotions'];
                break;
            case 'vendor':
                $userPermissions = ['manage_products', 'manage_orders', 'manage_inventory', 'view_reports'];
                break;
            case 'staff':
                $userPermissions = ['customer_support', 'manage_orders'];
                break;
            case 'customer':
            default:
                $userPermissions = ['place_orders', 'view_orders'];
                break;
        }
        $userPermissions = array_flip($userPermissions);
    }
}

function hasSidebarPermission($permission) {
    global $userPermissions, $currentUser;
    // Check permission or fallback to role check for admin
    return isset($userPermissions[$permission]) || 
           ($permission === 'manage_users' && ($currentUser['role'] ?? '') === 'admin') ||
           // Allow customers to see their orders
           ($permission === 'view_orders' && ($currentUser['role'] ?? '') === 'customer');
}
?>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-seedling"></i>
            <h2>AgriMarket</h2>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['REQUEST_URI'], '/dashboard/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/dashboard/">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- User Profile (All Users) -->
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/user-profile/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/user-profile/">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li>

            <!-- Admin Only Sections -->
            <?php if (hasSidebarPermission('manage_users') || ($currentUser['role'] ?? '') === 'admin'): ?>
            <li class="<?php echo (strpos($_SERVER['REQUEST_URI'], '/user-management/') !== false) ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/user-management/">
                    <i class="fas fa-users"></i>
                    <span>User Management</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (hasSidebarPermission('manage_vendors')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/vendor-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/vendor-management/">
                    <i class="fas fa-store"></i>
                    <span>Vendor Management</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (hasSidebarPermission('manage_customers')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/customer-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/customer-management/">
                    <i class="fas fa-user-friends"></i>
                    <span>Customer Management</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (hasSidebarPermission('manage_staff')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/staff-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/staff-management/">
                    <i class="fas fa-users-cog"></i>
                    <span>Staff Management</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (hasSidebarPermission('manage_system') || ($currentUser['role'] ?? '') === 'admin'): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/role-permission-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/role-permission-management/">
                    <i class="fas fa-shield-alt"></i>
                    <span>Role & Permissions</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Product Management -->
            <?php if (hasSidebarPermission('manage_products') || ($currentUser['role'] ?? '') === 'admin' || ($currentUser['role'] ?? '') === 'vendor'): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/product-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/product-management/">
                    <i class="fas fa-cube"></i>
                    <span>Product Management</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Inventory Management -->
            <?php if (hasSidebarPermission('manage_inventory') || ($currentUser['role'] ?? '') === 'admin' || ($currentUser['role'] ?? '') === 'vendor'): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/inventory-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/inventory-management/">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory Management</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Order Management / My Orders -->
            <?php if (hasSidebarPermission('manage_orders') || hasSidebarPermission('view_orders') || ($currentUser['role'] ?? '') === 'customer'): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/order-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/order-management/">
                    <i class="fas fa-clipboard-list"></i>
                    <span><?php echo (($currentUser['role'] ?? '') === 'customer') ? 'My Orders' : 'Order Management'; ?></span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Shopping (Customers) -->
            <?php if (hasSidebarPermission('place_orders') || ($currentUser['role'] ?? '') === 'customer'): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/products/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/products/">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Shop Products</span>
                </a>
            </li>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/vendors/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/vendors/">
                    <i class="fas fa-store-alt"></i>
                    <span>Find Vendors</span>
                </a>
            </li>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/shopping-cart/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/shopping-cart/">
                <i class="fas fa-shopping-cart"></i>
                    <span>Shopping Cart</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Analytics & Reports -->
            <?php if (hasSidebarPermission('view_analytics') || hasSidebarPermission('view_reports')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/analytics/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/analytics/">
                    <i class="fas fa-chart-bar"></i>
                    <span><?php echo hasSidebarPermission('view_analytics') ? 'Analytics' : 'Reports'; ?></span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Review Management -->
            <?php if (hasSidebarPermission('manage_reviews') || ($currentUser['role'] ?? '') === 'admin' || ($currentUser['role'] ?? '') === 'staff'): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/review-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/review-management/">
                    <i class="fas fa-star"></i>
                    <span>Review Management</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay"></div> 