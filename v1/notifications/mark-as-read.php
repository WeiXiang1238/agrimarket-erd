<?php
require_once __DIR__ . '/../../services/NotificationService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $notifId = (int)$_POST['id'];
    $notificationService = new NotificationService();
    $notificationService->markAsRead($notifId);
    echo json_encode(['success' => true]);
    exit;
}
echo json_encode(['success' => false]); 