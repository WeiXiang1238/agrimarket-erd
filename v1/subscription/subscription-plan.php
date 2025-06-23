<?php
require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/PermissionService.php';
require_once __DIR__ . '/../../models/Vendor.php';
require_once __DIR__ . '/../../models/SubscriptionTier.php';
require_once __DIR__ . '/../../models/VendorSubscription.php';
require_once __DIR__ . '/../../services/NotificationService.php';

$authService = new AuthService();
$permissionService = new PermissionService();

// Require authentication
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');

$currentUser = $authService->getCurrentUser();
$csrfToken = $authService->generateCSRFToken();

// Check if user is a vendor
if ($currentUser['role'] !== 'vendor') {
    header('Location: /agrimarket-erd/v1/dashboard/');
    exit;
}

// Get user permissions
$userPermissions = $permissionService->getEffectivePermissions($currentUser);

// Helper function to check permissions
function hasPermission($permission) {
    global $userPermissions;
    return isset($userPermissions[$permission]);
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
        'is_active' => false,
        'vendor_id' => $vendorId,
        'tier_id' => $subscriptionTierId
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
            return 'linear-gradient(135deg, rgb(108, 126, 120), rgb(29, 88, 70))'; // Green (default)
    }
}

// Get current subscription details
$currentSubscription = getVendorSubscriptionDetails($currentUser);

// Get all available subscription tiers
$subscriptionTierModel = new SubscriptionTier();
$allTiers = $subscriptionTierModel->findAll();

// Handle form submission for plan change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_plan'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrfToken) {
        $error = "Invalid request. Please try again.";
    } else {
        $newTierId = $_POST['tier_id'] ?? null;
        
        if ($newTierId && $currentSubscription) {
            // Update vendor's subscription tier in vendors table
            $vendorModel = new Vendor();
            $updateResult = $vendorModel->update($currentSubscription['vendor_id'], [
                'subscription_tier_id' => $newTierId,
                'tier_id' => $newTierId
            ]);
            
            if ($updateResult) {
                // 1. Deactivate old vendor_subscriptions
                $vendorSubscriptionModel = new VendorSubscription();
                $vendorSubscriptionModel->updateByConditions(
                    ['vendor_id' => $currentSubscription['vendor_id'], 'is_active' => 1],
                    ['is_active' => 0]
                );
                // 2. Insert new active vendor_subscription
                $today = date('Y-m-d');
                $endDate = date('Y-m-d', strtotime('+1 month', strtotime($today)));
                // Get new tier fee
                $subscriptionTierModel = new SubscriptionTier();
                $newTier = $subscriptionTierModel->find($newTierId);
                $paymentAmount = $newTier ? $newTier['monthly_fee'] : 0;
                $vendorSubscriptionModel->create([
                    'vendor_id' => $currentSubscription['vendor_id'],
                    'tier_id' => $newTierId,
                    'start_date' => $today,
                    'end_date' => $endDate,
                    'payment_amount' => $paymentAmount,
                    'is_active' => 1
                ]);
                $success = "Subscription plan updated successfully!";
                // Refresh subscription details
                $currentSubscription = getVendorSubscriptionDetails($currentUser);
                
                // Send notification to user
                $notificationService = new NotificationService();
                $notificationService->sendToUser(
                    $currentUser['user_id'],
                    'Your subscription plan has been changed to ' . htmlspecialchars($newTier['name']) . ' successfully.'
                );
            } else {
                $error = "Failed to update subscription plan. Please try again.";
            }
        } else {
            $error = "Invalid tier selection.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Plans - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../components/main.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="subscription-container">
        <!-- Back Button -->
        <a href="/agrimarket-erd/v1/dashboard/" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
        
        <!-- Page Header -->
        <div class="page-header">
            <h1>Subscription Plans</h1>
            <p>Choose the perfect plan for your business needs</p>
        </div>
        
        <!-- Alert Messages -->
        <?php if (isset($success)): ?>
        <div class="alert success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <!-- Current Plan Section -->
        <?php if ($currentSubscription): ?>
        <div class="current-plan-section">
            <div class="current-plan-header">
                <div class="current-plan-icon" style="background: <?php echo getSubscriptionTierColor($currentSubscription['tier_name']); ?>;">
                    <i class="fas fa-gem"></i>
                </div>
                <div class="current-plan-info">
                    <h2><?php echo htmlspecialchars($currentSubscription['tier_name']); ?> Plan</h2>
                    <p><?php echo htmlspecialchars($currentSubscription['description']); ?></p>
                </div>
            </div>
            
            <div class="plan-details">
                <div class="plan-detail">
                    <h4>Monthly Fee</h4>
                    <p>RM <?php echo number_format($currentSubscription['monthly_fee'], 2); ?></p>
                </div>
                <div class="plan-detail">
                    <h4>Status</h4>
                    <p><?php echo $currentSubscription['is_active'] ? 'Active' : 'Inactive'; ?></p>
                </div>
                <?php if ($currentSubscription['due_date']): ?>
                <div class="plan-detail">
                    <h4>Due Date</h4>
                    <p><?php echo date('M j, Y', strtotime($currentSubscription['due_date'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Available Plans -->
        <div class="plans-grid">
            <?php foreach ($allTiers as $tier): ?>
            <?php 
            $isCurrentPlan = $currentSubscription && $currentSubscription['tier_id'] == $tier['tier_id'];
            $isUpgrade = $currentSubscription && $tier['monthly_fee'] > $currentSubscription['monthly_fee'];
            $isDowngrade = $currentSubscription && $tier['monthly_fee'] < $currentSubscription['monthly_fee'];
            ?>
            <div class="plan-card <?php echo $isCurrentPlan ? 'current' : ''; ?>">
                <div class="plan-header">
                    <div class="plan-icon" style="background: <?php echo getSubscriptionTierColor($tier['name']); ?>;">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3 class="plan-name"><?php echo htmlspecialchars($tier['name']); ?></h3>
                    <div class="plan-price">
                        <span class="currency">RM</span>
                        <?php echo number_format($tier['monthly_fee'], 2); ?>
                        <span style="font-size: 1rem; color: #64748b;">/month</span>
                    </div>
                    <p class="plan-description"><?php echo htmlspecialchars($tier['description']); ?></p>
                </div>
                
                <form method="POST" style="margin-top: auto;">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="tier_id" value="<?php echo $tier['tier_id']; ?>">
                    
                    <?php if ($isCurrentPlan): ?>
                    <button type="button" class="plan-action current" disabled>
                        <i class="fas fa-check"></i>
                        Current Plan
                    </button>
                    <?php else: ?>
                    <button type="submit" name="change_plan" class="plan-action <?php echo $isUpgrade ? 'upgrade' : ($isDowngrade ? 'downgrade' : 'upgrade'); ?>">
                        <i class="fas fa-<?php echo $isUpgrade ? 'arrow-up' : ($isDowngrade ? 'arrow-down' : 'check'); ?>"></i>
                        <?php echo $isUpgrade ? 'Upgrade' : ($isDowngrade ? 'Downgrade' : 'Select'); ?> Plan
                    </button>
                    <?php endif; ?>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script>
        // Add confirmation for plan changes
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('button[type="submit"]');
                if (button && button.textContent.includes('Downgrade')) {
                    if (!confirm('Are you sure you want to downgrade your plan? This may affect your current features.')) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
</body>
</html> 