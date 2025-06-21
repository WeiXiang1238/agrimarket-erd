<?php
require_once __DIR__ . '/../../../services/UserService.php';
require_once __DIR__ . '/../../../services/AuthService.php';

$userService = new UserService();
$authService = new AuthService();

// Redirect if already authenticated
if ($authService->isAuthenticated()) {
    header('Location: ' . $authService->getUserDashboardUrl());
    exit;
}

$error = '';
$success = '';

// Handle forgot password form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!$authService->validateCSRFToken($csrfToken)) {
        $error = 'Invalid security token. Please try again.';
    } elseif (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if email exists
        if ($userService->emailExists($email)) {
            // In a real application, you would:
            // 1. Generate a unique reset token
            // 2. Store it in the database with an expiration time
            // 3. Send an email with a reset link
            // For this demo, we'll just show a success message
            $success = 'If an account with that email exists, a password reset link has been sent to your email address. Please check your inbox and follow the instructions to reset your password.';
        } else {
            // Don't reveal whether the email exists or not for security
            $success = 'If an account with that email exists, a password reset link has been sent to your email address. Please check your inbox and follow the instructions to reset your password.';
        }
    }
}

$csrfToken = $authService->generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../../components/main.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <div class="forgot-password-header">
                <div class="logo">
                    <i class="fas fa-seedling"></i>
                    <h1>AgriMarket</h1>
                </div>
                <h2>Reset Your Password</h2>
                <p>Enter your email address and we'll send you a link to reset your password</p>
                
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$success): ?>
            <form class="forgot-password-form" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Enter your email address"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required
                        >
                    </div>
                    <small class="help-text">We'll send a password reset link to this email</small>
                </div>
                
                <button type="submit" class="reset-btn">
                    <i class="fas fa-paper-plane"></i>
                    Send Reset Link
                </button>
            </form>
            <?php endif; ?>
            
            <div class="forgot-password-footer">
                <div class="back-to-login">
                    <a href="../login/">
                        <i class="fas fa-arrow-left"></i>
                        Back to Login
                    </a>
                </div>
                
                <div class="help-text-footer">
                    <p>Don't have an account? 
                        <a href="../register/">Create Account</a>
                    </p>
                </div>
                
                <div class="info-box">
                    <h4>Password Reset Information:</h4>
                    <ul>
                        <li><i class="fas fa-clock"></i> Reset links expire in 1 hour</li>
                        <li><i class="fas fa-shield-alt"></i> Links can only be used once</li>
                        <li><i class="fas fa-envelope"></i> Check your spam folder if needed</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="background-overlay">
            <div class="bg-shape shape-1"></div>
            <div class="bg-shape shape-2"></div>
            <div class="bg-shape shape-3"></div>
        </div>
    </div>
    
    <script>
        // Auto-hide alerts after 8 seconds (longer for success message)
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const delay = alert.classList.contains('alert-success') ? 10000 : 5000;
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, delay);
            });
        });
        
        // Form validation
        document.querySelector('.forgot-password-form')?.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            
            if (!email) {
                e.preventDefault();
                alert('Please enter your email address.');
                return;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
            
            // Show loading state
            const button = this.querySelector('.reset-btn');
            button.classList.add('loading');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        });
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        // Auto-focus email input
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            if (emailInput && !emailInput.value) {
                emailInput.focus();
            }
        });
    </script>
</body>
</html> 