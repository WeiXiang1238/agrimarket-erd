<?php
/**
 * Event Tracking Endpoint
 * Handles AJAX requests to track custom events
 */

session_start();
require_once __DIR__ . '/../../services/PageVisitService.php';
require_once __DIR__ . '/../../services/AuthService.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['event'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid event data']);
        exit;
    }
    
    $eventName = $input['event'];
    $eventData = $input['data'] ?? [];
    $timestamp = $input['timestamp'] ?? time();
    
    // Get current user
    $authService = new AuthService();
    $currentUser = $authService->getCurrentUser();
    $userId = $currentUser ? $currentUser['user_id'] : null;
    
    // Get current page info
    $currentUrl = $_SERVER['HTTP_REFERER'] ?? $_SERVER['REQUEST_URI'] ?? '';
    
    // Create event title
    $eventTitle = "Event: $eventName";
    if (!empty($eventData)) {
        $eventTitle .= " - " . json_encode($eventData);
    }
    
    // Record the event as a page visit with special event type
    $pageVisitService = new PageVisitService();
    $result = $pageVisitService->recordPageVisit(
        $currentUrl,
        $eventTitle,
        $userId
    );
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Event tracked successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to track event']);
    }
    
} catch (Exception $e) {
    error_log("Error tracking event: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error tracking event']);
}
?> 