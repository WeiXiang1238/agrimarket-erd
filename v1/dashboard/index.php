<?php
require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/PermissionService.php';

$authService = new AuthService();
$permissionService = new PermissionService();

// Require authentication (any authenticated user can access)
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');

$currentUser = $authService->getCurrentUser();
$csrfToken = $authService->generateCSRFToken();

// Get user permissions using PermissionService
$userPermissions = [];
if ($currentUser) {
    $userPermissions = $permissionService->getEffectivePermissions($currentUser);
}

// Helper function to check permissions
function hasPermission($permission) {
    global $userPermissions;
    return isset($userPermissions[$permission]);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getDashboardTitle($currentUser); ?> - AgriMarket Solutions</title>
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
                            <h3>1,234</h3>
                            <p>Total Users</p>
                            <span class="stat-change positive">+12% from last month</span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('manage_vendors')): ?>
                    <div class="stat-card">
                        <div class="stat-icon vendors">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="stat-info">
                            <h3>156</h3>
                            <p>Active Vendors</p>
                            <span class="stat-change positive">+8% from last month</span>
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
                            <h3><?php echo hasPermission('manage_products') ? '2,847' : '45'; ?></h3>
                            <p><?php echo hasPermission('manage_products') ? 'Total Products' : 'My Products'; ?></p>
                            <span class="stat-change positive">+15% from last month</span>
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
                            <h3><?php echo hasPermission('manage_orders') ? '892' : '12'; ?></h3>
                            <p><?php echo hasPermission('manage_orders') ? 'Orders Today' : 'My Orders'; ?></p>
                            <span class="stat-change <?php echo hasPermission('manage_orders') ? 'negative' : 'positive'; ?>">
                                <?php echo hasPermission('manage_orders') ? '-3% from yesterday' : '+2 this month'; ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Customer Stats -->
                    <?php if (hasPermission('place_orders')): ?>
                    <div class="stat-card">
                        <div class="stat-icon cart">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <h3>3</h3>
                            <p>Items in Cart</p>
                            <span class="stat-change neutral">Ready to checkout</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Content Sections -->
                <div class="content-grid">
                    <!-- Recent Activities -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3>Recent Activities</h3>
                            <button class="btn-secondary">View All</button>
                        </div>
                        <div class="card-content">
                            <div class="activity-list">
                                <?php if (hasPermission('manage_users')): ?>
                                <div class="activity-item">
                                    <div class="activity-icon new-user">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p><strong>New user registered:</strong> John Doe</p>
                                        <span class="activity-time">2 minutes ago</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('manage_orders') || hasPermission('view_orders')): ?>
                                <div class="activity-item">
                                    <div class="activity-icon new-order">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p><strong><?php echo hasPermission('manage_orders') ? 'New order placed:' : 'Order update:'; ?></strong> Order #12345</p>
                                        <span class="activity-time">5 minutes ago</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('manage_vendors')): ?>
                                <div class="activity-item">
                                    <div class="activity-icon vendor-approved">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p><strong>Vendor approved:</strong> Green Farm Co.</p>
                                        <span class="activity-time">1 hour ago</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('manage_products')): ?>
                                <div class="activity-item">
                                    <div class="activity-icon product-added">
                                        <i class="fas fa-plus-circle"></i>
                                    </div>
                                    <div class="activity-details">
                                        <p><strong>New product added:</strong> Organic Tomatoes</p>
                                        <span class="activity-time">2 hours ago</span>
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
                                <button class="action-btn primary">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Add New User</span>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('manage_products')): ?>
                                <button class="action-btn success">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Add Product</span>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('manage_vendors')): ?>
                                <button class="action-btn info">
                                    <i class="fas fa-store"></i>
                                    <span>Approve Vendor</span>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('place_orders')): ?>
                                <button class="action-btn warning">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span>Continue Shopping</span>
                                </button>
                                <?php endif; ?>
                                
                                <?php if (hasPermission('customer_support')): ?>
                                <button class="action-btn danger">
                                    <i class="fas fa-headset"></i>
                                    <span>Support Ticket</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
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
                
                // Handle different navigation types
                if (href.startsWith('/agrimarket-erd/v1/')) {
                    // External pages - navigate directly
                    window.location.href = href;
                } else if (href.startsWith('#')) {
                    // Internal sections - load content dynamically
                    loadDashboardSection(href.substring(1), sectionName);
                } else if (href.endsWith('.php')) {
                    // PHP pages - navigate to them
                    window.location.href = href;
                } else {
                    // Default behavior for other links
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
            loadComingSoon('Shop Products');
        }
        
        function loadCartSection() {
            loadComingSoon('Shopping Cart');
        }
        
        function loadAnalytics() {
            loadComingSoon('Analytics & Reports');
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
        document.querySelector('.notification-btn').addEventListener('click', function() {
            alert('Notification panel would open here');
        });
        
        // Quick action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.querySelector('span').textContent;
                alert(`${action} functionality would be implemented here`);
            });
        });
    </script>
</body>
</html> 