<?php
require_once __DIR__ . '/../Db_Connect.php';

class NotificationService {
    private $db;

    public function __construct() {
        global $conn;
        if (!$conn || $conn->connect_error) {
            throw new Exception('Database connection failed');
        }
        $this->db = $conn;
    }

    // Add a notification for a user
    public function sendToUser($userId, $message) {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, message, is_read, created_at) VALUES (?, ?, 0, NOW())");
        $stmt->bind_param('is', $userId, $message);
        return $stmt->execute();
    }

    // Create a notification with more details
    public function createNotification($userId, $title, $message, $type = 'order') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO notifications (
                    user_id, 
                    message, 
                    is_read, 
                    type, 
                    created_at
                ) VALUES (?, ?, 0, ?, NOW())
            ");
            $stmt->bind_param('iss', $userId, $message, $type);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error creating notification: ' . $e->getMessage());
            return false;
        }
    }

    // Fetch notifications for a user (most recent first)
    public function getUserNotifications($userId, $limit = 20) {
        $limit = (int)$limit; // Ensure it's an integer
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param('ii', $userId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Mark a notification as read
    public function markAsRead($notificationId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ?");
        $stmt->bind_param('i', $notificationId);
        return $stmt->execute();
    }
    
    // Get unread notification count for a user
    public function getUnreadCount($userId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    // Mark all notifications as read for a user
    public function markAllAsRead($userId) {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        return $stmt->execute();
    }
} 