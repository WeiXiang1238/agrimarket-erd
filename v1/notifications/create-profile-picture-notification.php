<?php
require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../models/Notification.php';

// Set JSON header
header('Content-Type: application/json');

// Check if it's an AJAX request
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Check if user is authenticated
$authService = new AuthService();
$currentUser = $authService->getCurrentUser();

if (!$currentUser) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Get user ID from request
$userId = $_POST['user_id'] ?? $currentUser['user_id'];

// Validate user ID matches current user
if ($userId != $currentUser['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    // Create notification
    $notificationModel = new Notification();
    
    $notificationData = [
        'user_id' => $userId,
        'message' => 'Your profile picture has been updated successfully!',
        'type' => 'profile_update',
        'is_read' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $notificationId = $notificationModel->create($notificationData);
    
    if ($notificationId) {
        // Get the created notification for response
        $notification = $notificationModel->find($notificationId);
        
        echo json_encode([
            'success' => true,
            'message' => 'Notification created successfully',
            'notification' => $notification
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create notification'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error creating notification: ' . $e->getMessage()
    ]);
}
?> 