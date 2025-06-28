<?php
require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/PermissionService.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Vendor.php';
require_once __DIR__ . '/../../models/Staff.php';
require_once __DIR__ . '/../../services/NotificationService.php';

$authService = new AuthService();
$permissionService = new PermissionService();
$notificationService = new NotificationService();

// Require authentication
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');

$currentUser = $authService->getCurrentUser();
$csrfToken = $authService->generateCSRFToken();

// Get user permissions
$userPermissions = [];
if ($currentUser) {
    $userPermissions = $permissionService->getEffectivePermissions($currentUser);
}

// Get notifications for the current user
$userNotifications = [];
$unreadCount = 0;
if ($currentUser) {
    $userNotifications = $notificationService->getUserNotifications($currentUser['user_id'], 10);
    $unreadCount = 0;
    foreach ($userNotifications as $notif) {
        if (!$notif['is_read']) $unreadCount++;
    }
}

// Handle password change
$updateMessage = '';
$updateError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrfToken) {
        $updateError = 'Invalid request. Please try again.';
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation 1: All fields are required
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $updateError = 'All password fields are required.';
        }
        // Validation 2: New password must be at least 6 characters
        elseif (strlen($newPassword) < 6) {
            $updateError = 'New password must be at least 6 characters long.';
        }
        // Validation 3: New password and confirm password must match
        elseif ($newPassword !== $confirmPassword) {
            $updateError = 'New password and confirm password do not match.';
        }
        // Validation 4: New password cannot be same as current password
        elseif ($newPassword === $currentPassword) {
            $updateError = 'New password cannot be the same as your current password.';
        }
        else {
            $userModel = new User();
            $user = $userModel->find($currentUser['user_id']);
            
            // Validation 5: Current password must be correct
            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                if ($userModel->update($currentUser['user_id'], ['password' => $hashedPassword])) {
                    $updateMessage = 'Password changed successfully!';
                    
                    // Create notification for successful password change
                    require_once __DIR__ . '/../../models/Notification.php';
                    $notificationModel = new Notification();
                    $notificationData = [
                        'user_id' => $currentUser['user_id'],
                        'message' => 'Your password has been changed successfully!',
                        'type' => 'password_change',
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    $notificationModel->create($notificationData);
                } else {
                    $updateError = 'Failed to change password. Please try again.';
                }
            } else {
                $updateError = 'Current password is incorrect.';
            }
        }
    }
}

// Get additional user data based on role
$additionalData = [];
if ($currentUser['role'] === 'vendor') {
    $vendorModel = new Vendor();
    $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
    if (!empty($vendor)) {
        $additionalData = $vendor[0];
    }
} elseif ($currentUser['role'] === 'staff') {
    $staffModel = new Staff();
    $staff = $staffModel->findAll(['user_id' => $currentUser['user_id']]);
    if (!empty($staff)) {
        $additionalData = $staff[0];
    }
}

// Account Statistics
$statCreatedAt = $currentUser['created_at'] ?? null;
$statLastLogin = $currentUser['last_login'] ?? null;

// Get vendor subscription info if user is vendor
$vendorSubscriptionInfo = null;
if ($currentUser['role'] === 'vendor' && !empty($additionalData['vendor_id'])) {
    require_once __DIR__ . '/../../models/VendorSubscription.php';
    require_once __DIR__ . '/../../models/SubscriptionTier.php';
    $vendorSubModel = new VendorSubscription();
    $activeSub = $vendorSubModel->findAll(['vendor_id' => $additionalData['vendor_id'], 'is_active' => 1]);
    if (!empty($activeSub)) {
        $sub = $activeSub[0];
        $tierModel = new SubscriptionTier();
        $tier = $tierModel->find($sub['tier_id']);
        $vendorSubscriptionInfo = [
            'subscription' => $sub,
            'tier' => $tier
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - AgriMarket</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Include Shared CSS -->
    <link rel="stylesheet" href="/agrimarket-erd/v1/components/main.css">
    <link rel="stylesheet" href="/agrimarket-erd/v1/components/sidebar.css">
    <link rel="stylesheet" href="/agrimarket-erd/v1/components/header.css">
    
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Shared Sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Shared Header -->
            <?php 
            $pageTitle = 'Profile Settings';
            include '../components/header.php'; 
            ?>
            
            <!-- Settings Content -->
            <div class="profile-content">
                <div class="profile-header">
                    <h1>Profile Settings</h1>
                    <p>Manage your account security and preferences</p>
                </div>

                <?php if ($updateMessage): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($updateMessage); ?>
                    </div>
                <?php endif; ?>

                <?php if ($updateError): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($updateError); ?>
                    </div>
                <?php endif; ?>

                <div class="profile-grid">
                    <!-- Change Password -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h2><i class="fas fa-lock"></i> Change Password</h2>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                
                                <div class="form-group">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" 
                                           minlength="6" required>
                                    <small class="form-text">Minimum 6 characters</small>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>

                                <button type="submit" name="change_password" class="btn btn-warning">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Vendor Subscription Card (only for vendors) -->
                    <?php if ($vendorSubscriptionInfo): ?>
                    <div class="profile-card">
                        <div class="card-header">
                            <h2><i class="fas fa-crown"></i> Vendor Subscription</h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Subscription Tier</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($vendorSubscriptionInfo['tier']['name'] ?? ''); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($vendorSubscriptionInfo['tier']['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Monthly Fee</label>
                                <input type="text" class="form-control" value="<?php echo isset($vendorSubscriptionInfo['tier']['monthly_fee']) ? 'RM ' . number_format($vendorSubscriptionInfo['tier']['monthly_fee'], 2) : ''; ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Due Date</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($vendorSubscriptionInfo['subscription']['end_date'] ?? ''); ?>" readonly>
                            </div>
                            <div style="text-align: right; margin-top: 1.5rem;">
                                <a href="/agrimarket-erd/v1/subscription/subscription-plan.php?source=settings" class="btn btn-primary">
                                    <i class="fas fa-exchange-alt"></i> Change Plan
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Account Statistics -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h2><i class="fas fa-chart-bar"></i> Account Statistics</h2>
                        </div>
                        <div class="card-body">
                            <div class="stats-list">
                                <div class="stat-item">
                                    <div class="stat-label">Member Since</div>
                                    <div class="stat-value">
                                        <?php echo $statCreatedAt ? date('M j, Y', strtotime($statCreatedAt)) : 'N/A'; ?>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-label">Last Login</div>
                                    <div class="stat-value">
                                        <?php echo $statLastLogin ? date('M j, Y H:i', strtotime($statLastLogin)) : 'N/A'; ?>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-label">Account Status</div>
                                    <div class="stat-value">
                                        <span class="badge badge-success">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Handle form submissions
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 300);
                });
            }, 5000);
        });
    </script>
</body>
</html> 