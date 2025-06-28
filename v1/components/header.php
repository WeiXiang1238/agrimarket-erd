<?php
/**
 * Shared Header Component
 * Usage: include 'path/to/components/header.php';
 * 
 * Required variables:
 * - $currentUser: Array with user data (name, role, etc.)
 * - $pageTitle: String for the page title (optional, defaults to 'Dashboard')
 */

// Set default page title if not provided
$pageTitle = $pageTitle ?? 'Dashboard';
?>

<!-- Header CSS -->
<link rel="stylesheet" href="/agrimarket-erd/v1/components/header.css">

<!-- Header -->
<header class="header">
    <div class="header-left">
        <button class="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    </div>
    
    <div class="header-right">
        <div class="notifications" style="position: relative;">
            <button class="notification-btn" id="notificationBtn">
                <i class="fas fa-bell"></i>
                <?php if (!empty($unreadCount)): ?>
                    <span class="notification-badge"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </button>
            <div class="notification-dropdown-panel" id="notificationDropdown">
                <div class="dropdown-header">Notifications</div>
                <ul style="list-style: none; margin: 0; padding: 0;">
                    <?php if (empty($userNotifications)): ?>
                        <li class="empty" style="text-align: center; color: #94a3b8; padding: 1rem;">No notifications</li>
                    <?php else: ?>
                        <?php foreach ($userNotifications as $notif): ?>
                            <li 
                                class="<?php echo isset($notif['is_read']) && $notif['is_read'] ? 'read' : 'unread'; ?>"
                                <?php if (isset($notif['notification_id'])): ?>data-notif-id="<?php echo htmlspecialchars($notif['notification_id']); ?>"<?php endif; ?>
                            >
                                <span><?php echo htmlspecialchars($notif['message'] ?? ''); ?></span>
                                <div class="notif-time">
                                    <?php echo isset($notif['created_at']) ? date('M j, H:i', strtotime($notif['created_at'])) : ''; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <!-- Cart Icon - Only show for customers -->
        <?php 
        $userRolesList = explode(',', $currentUser['roles'] ?? '');
        if (in_array('customer', $userRolesList)): 
            // Load models for cart count
            require_once __DIR__ . '/../../models/ModelLoader.php';
            $ShoppingCart = ModelLoader::load('ShoppingCart');
            $Customer = ModelLoader::load('Customer');
            
            // Get customer ID and cart count
            $cartCount = 0;
            $customer = $Customer->findAll(['user_id' => $currentUser['user_id']]);
            if (!empty($customer)) {
                $customerId = $customer[0]['customer_id'];
                $cartItems = $ShoppingCart->findAll(['customer_id' => $customerId]);
                foreach ($cartItems as $item) {
                    $cartCount += $item['quantity'] ?? 0;
                }
            }
        ?>
        <div class="cart-icon" style="position: relative; margin-right: 1rem;">
            <a href="/agrimarket-erd/v1/shop/cart.php" class="cart-btn" id="cartBtn">
                <i class="fas fa-shopping-cart"></i>
                <?php if ($cartCount > 0): ?>
                    <span class="cart-badge"><?php echo $cartCount; ?></span>
                <?php endif; ?>
            </a>
        </div>
        <?php endif; ?>
        
        <div class="user-menu">
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($currentUser['profile_picture'] ?? 'data:image/svg+xml;base64,' . base64_encode('<svg width="40" height="40" viewBox="0 0 24 24" fill="#cbd5e1" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>')); ?>" 
                     alt="User Avatar" class="user-avatar" 
                     onerror="this.src='data:image/svg+xml;base64,<?php echo base64_encode('<svg width="40" height="40" viewBox="0 0 24 24" fill="#cbd5e1" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>'); ?>"/> 
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['name'] ?? 'Guest'); ?></span>
                    <span class="user-role"><?php echo ucfirst($currentUser['role'] ?? 'User'); ?></span>
                </div>
            </div>
            <div class="user-dropdown">
                <a href="/agrimarket-erd/v1/user-profile/"><i class="fas fa-user"></i> My Profile</a>
                <a href="#settings"><i class="fas fa-cog"></i> Settings</a>
                <a href="/agrimarket-erd/v1/auth/logout/"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</header>

<script>
// Header-specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle functionality
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (window.innerWidth <= 768) {
                sidebar?.classList.toggle('show');
                overlay?.classList.toggle('show');
            } else {
                sidebar?.classList.toggle('collapsed');
                // Also toggle expanded class on main content for better control
                mainContent?.classList.toggle('expanded');
            }
        });
    }

    // Close sidebar when clicking overlay (mobile)
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            document.querySelector('.sidebar')?.classList.remove('show');
            this.classList.remove('show');
        });
    }

    // User dropdown toggle
    const userInfo = document.querySelector('.user-info');
    if (userInfo) {
        userInfo.addEventListener('click', function() {
            document.querySelector('.user-dropdown')?.classList.toggle('show');
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-menu')) {
            document.querySelector('.user-dropdown')?.classList.remove('show');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var notifBtn = document.getElementById('notificationBtn');
    var notifDropdown = document.getElementById('notificationDropdown');

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('show');
        });

        notifDropdown.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent closing when clicking inside
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            notifDropdown.classList.remove('show');
        });
    }

    // Mark notification as read when clicked
    document.querySelectorAll('.notification-dropdown-panel li.unread').forEach(function(item) {
        item.addEventListener('click', function() {
            var notifId = this.getAttribute('data-notif-id');
            fetch('/agrimarket-erd/v1/notifications/mark-as-read.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'id=' + encodeURIComponent(notifId)
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    this.classList.remove('unread');
                    this.classList.add('read');
                    this.style.background = 'white';
                    this.style.fontWeight = 'normal';
                    // Optionally update the badge count
                    var badge = document.querySelector('.notification-badge');
                    if (badge) {
                        let count = parseInt(badge.textContent, 10);
                        if (count > 1) badge.textContent = count - 1;
                        else badge.remove();
                    }
                }
            });
        });
    });
});
</script> 