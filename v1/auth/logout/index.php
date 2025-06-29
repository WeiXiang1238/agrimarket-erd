<?php
require_once __DIR__ . '/../../../services/AuthService.php';

// Set page title for tracking
$pageTitle = 'Logout - AgriMarket Solutions';

// Include page tracking
require_once __DIR__ . '/../../../includes/page_tracking.php';

$authService = new AuthService();

// Handle logout
$result = $authService->logout();

// Redirect to login page with message
$redirectUrl = '/agrimarket-erd/v1/auth/login/';

if ($result['success']) {
    $redirectUrl .= '?message=' . urlencode('You have been logged out successfully.');
} else {
    $redirectUrl .= '?error=' . urlencode($result['message']);
}

header('Location: ' . $redirectUrl);
exit;
?> 