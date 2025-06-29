<?php
/**
 * Page Visit Tracking Include
 * 
 * Include this file at the top of any page to automatically track page visits.
 * Usage: require_once __DIR__ . '/../includes/page_tracking.php';
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/../services/PageVisitService.php';
require_once __DIR__ . '/../services/AuthService.php';

// Initialize services
$pageVisitService = new PageVisitService();
$authService = new AuthService();

// Get current page information
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
$currentTitle = $pageTitle ?? null; // Set $pageTitle before including this file
$currentUser = $authService->getCurrentUser();
$userId = $currentUser ? $currentUser['user_id'] : null;

// Skip tracking for certain conditions
$skipTracking = false;

// Skip if it's a bot/crawler
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($userAgent, 'bot') !== false || 
        strpos($userAgent, 'crawler') !== false || 
        strpos($userAgent, 'spider') !== false) {
        $skipTracking = true;
    }
}

// Skip if it's an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $skipTracking = true;
}

// Skip if it's a resource file (images, CSS, JS, etc.)
$resourceExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];
$pathInfo = pathinfo($currentUrl);
if (isset($pathInfo['extension']) && in_array(strtolower($pathInfo['extension']), $resourceExtensions)) {
    $skipTracking = true;
}

// Skip if it's an admin/backend page that shouldn't be tracked
$skipPaths = ['/admin/', '/api/', '/cron/', '/backup/'];
foreach ($skipPaths as $skipPath) {
    if (strpos($currentUrl, $skipPath) !== false) {
        $skipTracking = true;
        break;
    }
}

// Record the page visit if not skipped
if (!$skipTracking) {
    $pageVisitService->recordPageVisit($currentUrl, $currentTitle, $userId);
}

// Function to manually track page visits (for specific pages)
function trackPageVisit($pageUrl = null, $pageTitle = null, $userId = null) {
    global $pageVisitService, $authService;
    
    if (!$pageUrl) {
        $pageUrl = $_SERVER['REQUEST_URI'] ?? '';
    }
    
    if (!$pageTitle) {
        $pageTitle = $GLOBALS['pageTitle'] ?? null;
    }
    
    if (!$userId) {
        $currentUser = $authService->getCurrentUser();
        $userId = $currentUser ? $currentUser['user_id'] : null;
    }
    
    return $pageVisitService->recordPageVisit($pageUrl, $pageTitle, $userId);
}

// Function to update visit duration (call this when user leaves page)
function updateVisitDuration() {
    global $pageVisitService;
    return $pageVisitService->updateVisitDuration();
}
?> 