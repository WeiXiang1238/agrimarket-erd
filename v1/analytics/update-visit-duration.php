<?php
/**
 * Update Visit Duration Endpoint
 * Handles AJAX requests to update visit duration when users leave pages
 */

session_start();
require_once __DIR__ . '/../../services/PageVisitService.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

try {
    $pageVisitService = new PageVisitService();
    
    // Get duration from request
    $duration = (int)($_POST['duration'] ?? 0);
    
    // Update the visit duration
    $result = $pageVisitService->updateVisitDuration();
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Visit duration updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No active visit found']);
    }
    
} catch (Exception $e) {
    error_log("Error updating visit duration: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating visit duration']);
}
?> 