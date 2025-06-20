<?php
require_once __DIR__ . '/../../../services/AuthService.php';

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