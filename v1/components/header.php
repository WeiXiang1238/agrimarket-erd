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
        <div class="notifications">
            <button class="notification-btn">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
        </div>
        
        <div class="user-menu">
            <div class="user-info">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 24 24' fill='%23cbd5e1'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E" alt="User Avatar" class="user-avatar" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\' viewBox=\'0 0 24 24\' fill=\'%23cbd5e1\'%3E%3Cpath d=\'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\'/%3E%3C/svg%3E'"/> 
                <div class="user-details">
                    <span class="user-name"><?php echo htmlspecialchars($currentUser['name'] ?? 'Guest'); ?></span>
                    <span class="user-role"><?php echo ucfirst($currentUser['role'] ?? 'User'); ?></span>
                </div>
            </div>
            <div class="user-dropdown">
                <a href="#profile"><i class="fas fa-user"></i> My Profile</a>
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
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (window.innerWidth <= 768) {
                sidebar?.classList.toggle('show');
                overlay?.classList.toggle('show');
            } else {
                sidebar?.classList.toggle('collapsed');
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