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

// Handle profile update
$updateMessage = '';
$updateError = '';

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

// Fetch phone number from the correct table (after $additionalData is set)
$profilePhone = $currentUser['phone'] ?? '';
if ($currentUser['role'] === 'vendor' && !empty($additionalData['contact_number'])) {
    $profilePhone = $additionalData['contact_number'];
} elseif ($currentUser['role'] === 'customer') {
    require_once __DIR__ . '/../../models/Customer.php';
    $customerModel = new Customer();
    $customer = $customerModel->findAll(['user_id' => $currentUser['user_id']]);
    if (!empty($customer)) {
        $profilePhone = $customer[0]['phone'] ?? '';
    }
} elseif ($currentUser['role'] === 'staff') {
    require_once __DIR__ . '/../../models/Staff.php';
    $staffModel = new Staff();
    $staff = $staffModel->findAll(['user_id' => $currentUser['user_id']]);
    if (!empty($staff)) {
        $profilePhone = $staff[0]['phone'] ?? '';
    }
}

// Handle profile picture upload
$profilePicMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_profile_pic'])) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrfToken) {
        $profilePicMessage = 'Invalid request. Please try again.';
    } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['profile_picture']['tmp_name'];
        $fileName = basename($_FILES['profile_picture']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($fileExt, $allowed)) {
            $profilePicMessage = 'Only JPG, JPEG, PNG, and WEBP files are allowed.';
        } else {
            $newFileName = 'user_' . $currentUser['user_id'] . '_' . time() . '.' . $fileExt;
            $uploadDir = __DIR__ . '/../../Image/profile_pics/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $destPath = $uploadDir . $newFileName;
            
            // Initialize upload success flag
            $uploadSuccess = false;
            
            // Check if GD library is available for resizing
            if (!extension_loaded('gd')) {
                // GD library not available - upload without resizing
                $uploadSuccess = move_uploaded_file($fileTmp, $destPath);
                if (!$uploadSuccess) {
                    $profilePicMessage = 'Failed to upload image. GD library not available for processing.';
                } else {
                    $profilePicMessage = 'Profile picture uploaded! (Note: Image resizing not available - GD library required)';
                }
            } else {
                // Check if image needs resizing
                $imageInfo = getimagesize($fileTmp);
                if ($imageInfo === false) {
                    $profilePicMessage = 'Invalid image file.';
                } else {
                    $originalWidth = $imageInfo[0];
                    $originalHeight = $imageInfo[1];
                    $maxWidth = 375;
                    
                    // Only resize if width is greater than max width
                    if ($originalWidth > $maxWidth) {
                        // Calculate new dimensions maintaining aspect ratio
                        $ratio = $maxWidth / $originalWidth;
                        $newWidth = $maxWidth;
                        $newHeight = round($originalHeight * $ratio);
                        
                        // Create image resource based on file type
                        switch ($fileExt) {
                            case 'jpg':
                            case 'jpeg':
                                $sourceImage = imagecreatefromjpeg($fileTmp);
                                break;
                            case 'png':
                                $sourceImage = imagecreatefrompng($fileTmp);
                                break;
                            case 'webp':
                                $sourceImage = imagecreatefromwebp($fileTmp);
                                break;
                            default:
                                $sourceImage = imagecreatefromjpeg($fileTmp);
                        }
                        
                        if ($sourceImage) {
                            // Create new image with new dimensions
                            $newImage = imagecreatetruecolor($newWidth, $newHeight);
                            
                            // Preserve transparency for PNG and WebP
                            if ($fileExt === 'png' || $fileExt === 'webp') {
                                imagealphablending($newImage, false);
                                imagesavealpha($newImage, true);
                                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                                imagefill($newImage, 0, 0, $transparent);
                            }
                            
                            // Resize the image
                            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                            
                            // Save the resized image
                            switch ($fileExt) {
                                case 'jpg':
                                case 'jpeg':
                                    $uploadSuccess = imagejpeg($newImage, $destPath, 90);
                                    break;
                                case 'png':
                                    $uploadSuccess = imagepng($newImage, $destPath, 9);
                                    break;
                                case 'webp':
                                    $uploadSuccess = imagewebp($newImage, $destPath, 90);
                                    break;
                            }
                            
                            // Free memory
                            imagedestroy($sourceImage);
                            imagedestroy($newImage);
                            
                            if (!$uploadSuccess) {
                                $profilePicMessage = 'Failed to save resized image.';
                            }
                        } else {
                            $profilePicMessage = 'Failed to process image for resizing.';
                        }
                    } else {
                        // No resizing needed, move file as is
                        $uploadSuccess = move_uploaded_file($fileTmp, $destPath);
                        if (!$uploadSuccess) {
                            $profilePicMessage = 'Failed to move uploaded file.';
                        }
                    }
                }
            }
            
            if ($uploadSuccess) {
                // Save relative path to users table
                $relativePath = '/agrimarket-erd/Image/profile_pics/' . $newFileName;
                $userModel = new User();
                $updateResult = $userModel->update($currentUser['user_id'], ['profile_picture' => $relativePath]);
                
                if ($updateResult) {
                    $profilePicMessage = 'Profile picture updated!';
                    // Refresh current user data
                    $currentUser = (new User())->find($currentUser['user_id']);
                } else {
                    $profilePicMessage = 'Failed to update database.';
                }
            }
        }
    } else {
        $profilePicMessage = 'No file selected or upload error.';
    }
    
    // If it's an AJAX request, return JSON response
    if ($isAjax) {
        header('Content-Type: application/json');
        
        // Add debugging information
        $debugInfo = [];
        if (isset($_FILES['profile_picture'])) {
            $debugInfo['file_error'] = $_FILES['profile_picture']['error'];
            $debugInfo['file_size'] = $_FILES['profile_picture']['size'];
            $debugInfo['file_type'] = $_FILES['profile_picture']['type'];
            $debugInfo['file_name'] = $_FILES['profile_picture']['name'];
        }
        $debugInfo['upload_success'] = $uploadSuccess ?? 'not set';
        $debugInfo['upload_dir_exists'] = is_dir(__DIR__ . '/../../Image/profile_pics/');
        $debugInfo['upload_dir_writable'] = is_writable(__DIR__ . '/../../Image/profile_pics/');
        $debugInfo['gd_available'] = extension_loaded('gd');
        $debugInfo['profile_pic_message'] = $profilePicMessage;
        
        if (isset($imageInfo) && $imageInfo !== false) {
            $debugInfo['image_width'] = $imageInfo[0];
            $debugInfo['image_height'] = $imageInfo[1];
            $debugInfo['image_type'] = $imageInfo[2];
        }
        
        echo json_encode([
            'success' => empty($profilePicMessage) || strpos($profilePicMessage, 'updated!') !== false || strpos($profilePicMessage, 'uploaded!') !== false,
            'message' => $profilePicMessage,
            'profile_picture' => $currentUser['profile_picture'] ?? null,
            'debug' => $debugInfo
        ]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $userModel = new User();
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrfToken) {
        $updateError = 'Invalid request. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $address_updated = false;
        // Basic validation
        if (empty($name)) {
            $updateError = 'Name is required.';
        } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $updateError = 'Valid email is required.';
        } else {
            // Check if email is already taken by another user
            $existingUser = $userModel->findAll(['email' => $email]);
            if (!empty($existingUser) && $existingUser[0]['user_id'] != $currentUser['user_id']) {
                $updateError = 'Email is already taken by another user.';
            } else {
                // Update user profile
                $updateData = [
                    'name' => $name,
                    'email' => $email
                ];
                $userUpdate = $userModel->update($currentUser['user_id'], $updateData);
                // Phone update for vendor
                if ($currentUser['role'] === 'vendor') {
                    require_once __DIR__ . '/../../models/Vendor.php';
                    $vendorModel = new Vendor();
                    $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
                    if (!empty($vendor)) {
                        $vendorModel->update($vendor[0]['vendor_id'], [
                            'contact_number' => $phone,
                            'business_name' => trim($_POST['business_name'] ?? $vendor[0]['business_name'])
                        ]);
                    }
                }
                // Phone update for customer
                if ($currentUser['role'] === 'customer') {
                    require_once __DIR__ . '/../../models/Customer.php';
                    $customerModel = new Customer();
                    $customer = $customerModel->findAll(['user_id' => $currentUser['user_id']]);
                    if (!empty($customer)) {
                        $customerModel->update($customer[0]['customer_id'], ['phone' => $phone]);
                    }
                }
                // Phone update for staff
                if ($currentUser['role'] === 'staff') {
                    require_once __DIR__ . '/../../models/Staff.php';
                    $staffModel = new Staff();
                    $staff = $staffModel->findAll(['user_id' => $currentUser['user_id']]);
                    if (!empty($staff)) {
                        $staffModel->update($staff[0]['staff_id'], ['phone' => $phone]);
                    }
                }
                // Address update for vendor
                if ($currentUser['role'] === 'vendor') {
                    require_once __DIR__ . '/../../models/Vendor.php';
                    $vendorModel = new Vendor();
                    $vendor = $vendorModel->findAll(['user_id' => $currentUser['user_id']]);
                    if (!empty($vendor)) {
                        $address_updated = $vendorModel->update($vendor[0]['vendor_id'], ['address' => $address]);
                    }
                }
                // Address update for customer
                if ($currentUser['role'] === 'customer') {
                    require_once __DIR__ . '/../../models/Customer.php';
                    require_once __DIR__ . '/../../models/CustomerAddress.php';
                    $customerModel = new Customer();
                    $customer = $customerModel->findAll(['user_id' => $currentUser['user_id']]);
                    if (!empty($customer)) {
                        $addressModel = new CustomerAddress();
                        $addresses = $addressModel->findAll(['customer_id' => $customer[0]['customer_id'], 'is_default' => 1]);
                        if (!empty($addresses)) {
                            $addressId = $addresses[0]['address_id'];
                            $addressFields = [
                                'street_address' => trim($_POST['street_address'] ?? ''),
                                'street_address_2' => trim($_POST['street_address_2'] ?? ''),
                                'city' => trim($_POST['city'] ?? ''),
                                'state' => trim($_POST['state'] ?? ''),
                                'postal_code' => trim($_POST['postal_code'] ?? ''),
                                'country' => trim($_POST['country'] ?? '')
                            ];
                            $address_updated = $addressModel->update($addressId, $addressFields);
                        }
                    }
                }
                if ($userUpdate || $address_updated) {
                    $updateMessage = 'Profile updated successfully!';
                    // Refresh current user data
                    $currentUser = (new User())->find($currentUser['user_id']);
                } else {
                    $updateError = 'Failed to update profile. Please try again.';
                }
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $csrfToken) {
        $updateError = 'Invalid request. Please try again.';
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $updateError = 'All password fields are required.';
        } elseif ($newPassword !== $confirmPassword) {
            $updateError = 'New passwords do not match.';
        } elseif (strlen($newPassword) < 6) {
            $updateError = 'New password must be at least 6 characters long.';
        } else {
            $userModel = new User();
            $user = $userModel->find($currentUser['user_id']);
            
            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                if ($userModel->update($currentUser['user_id'], ['password' => $hashedPassword])) {
                    $updateMessage = 'Password changed successfully!';
                } else {
                    $updateError = 'Failed to change password. Please try again.';
                }
            } else {
                $updateError = 'Current password is incorrect.';
            }
        }
    }
}

// Address Info (for customer or vendor)
$addressInfo = '';
$customerAddressFields = [];
if ($currentUser['role'] === 'customer') {
    require_once __DIR__ . '/../../models/Customer.php';
    require_once __DIR__ . '/../../models/CustomerAddress.php';
    $customerModel = new Customer();
    $customer = $customerModel->findAll(['user_id' => $currentUser['user_id']]);
    if (!empty($customer)) {
        $addressModel = new CustomerAddress();
        $addresses = $addressModel->findAll(['customer_id' => $customer[0]['customer_id'], 'is_default' => 1]);
        if (!empty($addresses)) {
            $addr = $addresses[0];
            $addressInfo = $addr['street_address'] . ', ' . ($addr['street_address_2'] ? $addr['street_address_2'] . ', ' : '') . $addr['city'] . ', ' . $addr['state'] . ', ' . $addr['postal_code'] . ', ' . $addr['country'];
            $customerAddressFields = $addr;
        }
    }
} elseif ($currentUser['role'] === 'vendor' && !empty($additionalData['address'])) {
    $addressInfo = $additionalData['address'];
}

// Account statistics fields
$statCreatedAt = $currentUser['created_at'] ?? null;
$statLastLogin = $currentUser['last_login'] ?? ($currentUser['last_login_at'] ?? null);
if ($currentUser['role'] === 'vendor') {
    if (empty($statCreatedAt) && !empty($additionalData['registration_date'])) {
        $statCreatedAt = $additionalData['registration_date'];
    }
}
if ($currentUser['role'] === 'staff' && !empty($additionalData['created_at'])) {
    $statCreatedAt = $additionalData['created_at'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../components/main.css">
    <link rel="stylesheet" href="../dashboard/style.css">
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
            $pageTitle = 'User Profile';
            include '../components/header.php'; 
            ?>
            
            <!-- Profile Content -->
            <div class="profile-content">
                <div class="profile-header">
                    <h1>User Profile</h1>
                    <p>Manage your account information and settings</p>
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

                <!-- Profile picture -->
                <div class="profile-pic-section">
                    <div class="profile-pic-container">
                        <img src="<?php echo htmlspecialchars($currentUser['profile_picture'] ?? 'data:image/svg+xml;base64,' . base64_encode('<svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="60" cy="60" r="60" fill="#E2E8F0"/><circle cx="60" cy="45" r="20" fill="#9CA3AF"/><path d="M20 95C20 75 38 60 60 60C82 60 100 75 100 95" fill="#9CA3AF"/></svg>')); ?>" 
                             alt="Profile Picture" class="profile-pic-img">
                        <label for="profile_picture" class="profile-pic-upload-btn">
                            <i class="fas fa-camera"></i>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                        </label>
                    </div>
                    
                    <div class="profile-pic-actions">
                        <button type="button" class="upload-profile-pic-btn btn-disabled" disabled>
                            <i class="fas fa-upload"></i> Upload New
                        </button>
                    </div>
                    <div class="profile-pic-preview"></div>
                    <div class="profile-pic-message"></div>
                </div>

                <div class="profile-grid">
                    <!-- Profile Information -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h2><i class="fas fa-user"></i> Profile Information</h2>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($profilePhone); ?>">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">User ID</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($currentUser['user_id'] ?? ''); ?>" readonly>
                                </div>

                                <?php if ($currentUser['role'] === 'vendor' && !empty($additionalData)): ?>
                                <div class="form-group">
                                    <label class="form-label">Business Name</label>
                                    <input type="text" name="business_name" class="form-control" value="<?php echo htmlspecialchars($additionalData['business_name'] ?? ''); ?>">
                                </div>
                                <?php endif; ?>

                                <?php if ($currentUser['role'] === 'vendor'): ?>
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($addressInfo); ?></textarea>
                                </div>
                                <?php elseif ($currentUser['role'] === 'customer' && !empty($customerAddressFields)): ?>
                                <div class="form-group">
                                    <label class="form-label">Street Address</label>
                                    <input type="text" name="street_address" class="form-control" value="<?php echo htmlspecialchars($customerAddressFields['street_address'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Street Address 2</label>
                                    <input type="text" name="street_address_2" class="form-control" value="<?php echo htmlspecialchars($customerAddressFields['street_address_2'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($customerAddressFields['city'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" class="form-control" value="<?php echo htmlspecialchars($customerAddressFields['state'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control" value="<?php echo htmlspecialchars($customerAddressFields['postal_code'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Country</label>
                                    <input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($customerAddressFields['country'] ?? ''); ?>">
                                </div>
                                <?php elseif ($addressInfo): ?>
                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($addressInfo); ?></textarea>
                                </div>
                                <?php endif; ?>

                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Profile
                                </button>
                            </form>
                        </div>
                    </div>

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

                    <!-- Vendor Subscription Card (only for vendors) -->
                    <?php
                    if ($currentUser['role'] === 'vendor' && !empty($additionalData['vendor_id'])) {
                        require_once __DIR__ . '/../../models/VendorSubscription.php';
                        require_once __DIR__ . '/../../models/SubscriptionTier.php';
                        $vendorSubModel = new VendorSubscription();
                        $activeSub = $vendorSubModel->findAll(['vendor_id' => $additionalData['vendor_id'], 'is_active' => 1]);
                        if (!empty($activeSub)) {
                            $sub = $activeSub[0];
                            $tierModel = new SubscriptionTier();
                            $tier = $tierModel->find($sub['tier_id']);
                            ?>
                            <div class="profile-card">
                                <div class="card-header">
                                    <h2><i class="fas fa-crown"></i> Vendor Subscription</h2>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label">Subscription Tier</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($tier['name'] ?? ''); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" rows="2" readonly><?php echo htmlspecialchars($tier['description'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Monthly Fee</label>
                                        <input type="text" class="form-control" value="<?php echo isset($tier['monthly_fee']) ? 'RM ' . number_format($tier['monthly_fee'], 2) : ''; ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Due Date</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($sub['end_date'] ?? ''); ?>" readonly>
                                    </div>
                                    <div style="text-align: right; margin-top: 1.5rem;">
                                        <a href="/agrimarket-erd/v1/subscription/subscription-plan.php?source=profile" class="btn btn-primary">
                                            <i class="fas fa-exchange-alt"></i> Change Plan
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <!-- Security Settings -->
                    <div class="profile-card">
                        <div class="card-header">
                            <h2><i class="fas fa-shield-alt"></i> Security Settings</h2>
                        </div>
                        <div class="card-body">
                            <div class="security-options">
                                <div class="security-item">
                                    <div class="security-info">
                                        <h4>Two-Factor Authentication</h4>
                                        <p>Add an extra layer of security to your account</p>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">Enable</button>
                                </div>
                                
                                <div class="security-item">
                                    <div class="security-info">
                                        <h4>Login Notifications</h4>
                                        <p>Get notified when someone logs into your account</p>
                                    </div>
                                    <button class="btn btn-outline-primary btn-sm">Enable</button>
                                </div>
                                
                                <div class="security-item">
                                    <div class="security-info">
                                        <h4>Session Management</h4>
                                        <p>View and manage your active sessions</p>
                                    </div>
                                    <button class="btn btn-outline-secondary btn-sm">Manage</button>
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
            
            // Profile Picture Upload Functionality
            const profilePictureInput = document.getElementById('profile_picture');
            const profilePictureImg = document.querySelector('.profile-pic-img');
            const uploadButton = document.querySelector('.upload-profile-pic-btn');
            const previewContainer = document.querySelector('.profile-pic-preview');
            const messageContainer = document.querySelector('.profile-pic-message');
            
            let selectedFile = null;

            // Handle file selection with preview
            if (profilePictureInput) {
                profilePictureInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Validate file type
                        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            showMessage('Only JPG, JPEG, PNG, and WEBP files are allowed.', 'error');
                            return;
                        }

                        // Validate file size (max 5MB)
                        if (file.size > 5 * 1024 * 1024) {
                            showMessage('File size must be less than 5MB.', 'error');
                            return;
                        }

                        selectedFile = file;
                        
                        // Create preview
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (previewContainer) {
                                previewContainer.innerHTML = `
                                    <div class="preview-item">
                                        <img src="${e.target.result}" alt="Preview" class="preview-img">
                                        <div class="preview-info">
                                            <span class="preview-name">${file.name}</span>
                                            <span class="preview-size">${formatFileSize(file.size)}</span>
                                        </div>
                                        <button type="button" class="preview-remove" onclick="removePreview()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                `;
                                previewContainer.style.display = 'block';
                            }
                            
                            // Enable upload button
                            if (uploadButton) {
                                uploadButton.disabled = false;
                                uploadButton.classList.remove('btn-disabled');
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Handle upload button click
            if (uploadButton) {
                uploadButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (selectedFile) {
                        uploadProfilePicture(selectedFile);
                    } else {
                        showMessage('Please select a file first.', 'error');
                    }
                });
            }

            // Upload profile picture via AJAX
            function uploadProfilePicture(file) {
                const formData = new FormData();
                formData.append('profile_picture', file);
                formData.append('upload_profile_pic', '1');
                formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

                // Show loading state
                if (uploadButton) {
                    uploadButton.disabled = true;
                    uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
                }

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Log debug information
                    if (data.debug) {
                        console.log('Upload Debug Info:', data.debug);
                    }
                    
                    if (data.success) {
                        showMessage(data.message || 'Profile picture updated successfully!', 'success');
                        
                        // Update the profile picture display with the new path
                        if (data.profile_picture && profilePictureImg) {
                            profilePictureImg.src = data.profile_picture;
                        }
                        
                        // Update header avatar
                        const headerAvatar = document.querySelector('.user-avatar');
                        if (headerAvatar && data.profile_picture) {
                            headerAvatar.src = data.profile_picture;
                        }
                        
                        // Create notification
                        createProfilePictureNotification();
                        
                        // Clear the form
                        profilePictureInput.value = '';
                        selectedFile = null;
                        if (previewContainer) {
                            previewContainer.style.display = 'none';
                            previewContainer.innerHTML = '';
                        }
                    } else {
                        showMessage(data.message || 'Upload failed. Please try again.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Upload error:', error);
                    showMessage('Upload failed. Please try again.', 'error');
                })
                .finally(() => {
                    // Reset button state
                    if (uploadButton) {
                        uploadButton.disabled = false;
                        uploadButton.innerHTML = '<i class="fas fa-upload"></i> Upload New';
                    }
                });
            }

            // Show message
            function showMessage(message, type) {
                if (messageContainer) {
                    let icon = 'check-circle';
                    if (type === 'error') icon = 'exclamation-circle';
                    if (type === 'info') icon = 'info-circle';
                    
                    messageContainer.innerHTML = `
                        <div class="alert alert-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'}">
                            <i class="fas fa-${icon}"></i>
                            ${message}
                        </div>
                    `;
                    messageContainer.style.display = 'block';
                    
                    // Auto-hide success and info messages after 3 seconds
                    if (type === 'success' || type === 'info') {
                        setTimeout(() => {
                            messageContainer.style.display = 'none';
                        }, 3000);
                    }
                }
            }

            // Format file size
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Remove preview (global function for onclick)
            window.removePreview = function() {
                if (previewContainer) {
                    previewContainer.style.display = 'none';
                    previewContainer.innerHTML = '';
                }
                if (profilePictureInput) {
                    profilePictureInput.value = '';
                }
                selectedFile = null;
                if (uploadButton) {
                    uploadButton.disabled = true;
                    uploadButton.classList.add('btn-disabled');
                }
            };
            
            // Create profile picture notification
            function createProfilePictureNotification() {
                fetch('/agrimarket-erd/v1/notifications/create-profile-picture-notification.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'user_id=<?php echo $currentUser['user_id']; ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update notification count and list if available
                        const notificationBadge = document.querySelector('.notification-badge');
                        const notificationList = document.querySelector('.notification-dropdown-panel ul');
                        
                        if (notificationBadge) {
                            let currentCount = parseInt(notificationBadge.textContent) || 0;
                            notificationBadge.textContent = currentCount + 1;
                        }
                        
                        if (notificationList && data.notification) {
                            // Add new notification to the top of the list
                            const newNotification = document.createElement('li');
                            newNotification.className = 'unread';
                            newNotification.setAttribute('data-notif-id', data.notification.notification_id);
                            newNotification.innerHTML = `
                                <span>${data.notification.message}</span>
                                <div class="notif-time">Just now</div>
                            `;
                            
                            // Remove "No notifications" message if it exists
                            const emptyMessage = notificationList.querySelector('.empty');
                            if (emptyMessage) {
                                emptyMessage.remove();
                            }
                            
                            // Add new notification at the top
                            notificationList.insertBefore(newNotification, notificationList.firstChild);
                        }
                    }
                })
                .catch(error => {
                    console.error('Notification creation error:', error);
                });
            }
        });
    </script>
</body>
</html> 