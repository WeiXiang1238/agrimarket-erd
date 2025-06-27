<!-- Sidebar CSS -->
<link rel="stylesheet" href="/agrimarket-erd/v1/components/sidebar.css">

<?php
// Include PermissionService if not already included
if (!class_exists('PermissionService')) {
    require_once __DIR__ . '/../../services/PermissionService.php';
}

// Get user permissions using PermissionService
$userPermissions = [];
if (isset($currentUser)) {
    $permissionService = new PermissionService();
    $userPermissions = $permissionService->getEffectivePermissions($currentUser);
}

function hasSidebarPermission($permission) {
    global $userPermissions, $currentUser;
    // Check permission or fallback to role check for admin
    return isset($userPermissions[$permission]) || 
           ($permission === 'manage_users' && ($currentUser['role'] ?? '') === 'admin');
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
            
            <!-- Product Management -->
            <?php if (hasSidebarPermission('manage_products') || ($currentUser['role'] ?? '') === 'admin' || ($currentUser['role'] ?? '') === 'vendor'): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/product-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/product-management/">
                    <i class="fas fa-cube"></i>
                    <span>Product Management</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Inventory Management (Vendors) -->
            <?php if (hasSidebarPermission('manage_inventory')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/inventory/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/inventory/">
                    <i class="fas fa-warehouse"></i>
                    <span>Inventory</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Order Management -->
            <?php if (hasSidebarPermission('manage_orders') || hasSidebarPermission('view_orders')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/order-management/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/order-management/">
                    <i class="fas fa-clipboard-list"></i>
                    <span><?php echo hasSidebarPermission('manage_orders') ? 'Order Management' : 'My Orders'; ?></span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Shopping (Customers) -->
            <?php if (hasSidebarPermission('place_orders')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/products/') !== false || strpos($_SERVER['REQUEST_URI'], '/shop/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/products/">
                    <i class="fas fa-shopping-bag"></i>
                    <span>Shop Products</span>
                </a>
            </li>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/shop/') !== false ? 'active' : ''; ?>">
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/shop/') !== false && basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/shop/" id="sidebar-shop-link">
                    <i class="fas fa-store"></i>
                    <span>Shop Products</span>
                </a>
            </li>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/shopping-cart/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/shopping-cart/">
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' && strpos($_SERVER['REQUEST_URI'], '/shop/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/shop/cart.php" id="sidebar-cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Shopping Cart</span>
                </a>
            </li>
            <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' && strpos($_SERVER['REQUEST_URI'], '/shop/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/shop/productcomparison.php" id="sidebar-product-comparison-link">
                    <i class="fas fa-product-comparison"></i>
                    <span>Product Comparison</span>
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
            
            <!-- Customer Support (Staff) -->
            <?php if (hasSidebarPermission('customer_support')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/support/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/support/">
                    <i class="fas fa-headset"></i>
                    <span>Customer Support</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Promotions (Admin) -->
            <?php if (hasSidebarPermission('manage_promotions')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/promotions/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/promotions/">
                    <i class="fas fa-tags"></i>
                    <span>Promotions</span>
                </a>
            </li>
            <?php endif; ?>
            
            <!-- System Settings (Admin) -->
            <?php if (hasSidebarPermission('manage_system')): ?>
            <li class="<?php echo strpos($_SERVER['REQUEST_URI'], '/settings/') !== false ? 'active' : ''; ?>">
                <a href="/agrimarket-erd/v1/settings/">
                    <i class="fas fa-cog"></i>
                    <span>System Settings</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>

<!-- Mobile Sidebar Overlay -->
<div class="sidebar-overlay"></div> 