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

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'customer';
    $agree = isset($_POST['agree_terms']);
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    // Validate CSRF token
    if (!$authService->validateCSRFToken($csrfToken)) {
        $error = 'Invalid security token. Please try again.';
    } elseif (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Password confirmation does not match.';
    } elseif (!$agree) {
        $error = 'You must agree to the terms and conditions.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Attempt registration
        $userData = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'role' => $role
        ];
        
        $result = $userService->register($userData);
        
        if ($result['success']) {
            $success = $result['message'] . ' You can now log in with your credentials.';
        } else {
            $error = $result['message'];
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
    <title>Register - AgriMarket Solutions</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="logo">
                    <i class="fas fa-seedling"></i>
                    <h1>AgriMarket</h1>
                </div>
                <p>Join our community of farmers and agricultural vendors</p>
                
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
            
            <form class="register-form" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                placeholder="Enter your full name"
                                value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                required
                            >
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                placeholder="Enter your email"
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                required
                            >
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-group">
                            <i class="fas fa-phone"></i>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                placeholder="Enter your phone number"
                                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                            >
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Account Type *</label>
                        <div class="input-group">
                            <i class="fas fa-user-tag"></i>
                            <select id="role" name="role" required>
                                <option value="customer" <?php echo ($_POST['role'] ?? 'customer') === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                <option value="vendor" <?php echo ($_POST['role'] ?? '') === 'vendor' ? 'selected' : ''; ?>>Vendor</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="Enter your password"
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                        <small class="help-text">Minimum 6 characters</small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                placeholder="Confirm your password"
                                required
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye" id="confirmPasswordToggleIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="agree_terms" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="#" onclick="showTerms()">Terms & Conditions</a> and <a href="#" onclick="showPrivacy()">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>
            
            <div class="register-footer">
                <p>Already have an account? 
                    <a href="../login/">Sign In</a>
                </p>
                
                <div class="account-types">
                    <h4>Account Types:</h4>
                    <div class="types">
                        <div class="type-info">
                            <i class="fas fa-user"></i>
                            <span>Customer - Browse and buy agricultural products</span>
                        </div>
                        <div class="type-info">
                            <i class="fas fa-store"></i>
                            <span>Vendor - Sell your agricultural products</span>
                        </div>
                    </div>
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
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId + 'ToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
        
        // Form validation
        document.querySelector('.register-form').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const agreeTerms = document.querySelector('input[name="agree_terms"]').checked;
            
            if (!name || !email || !password || !confirmPassword) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long.');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password confirmation does not match.');
                return;
            }
            
            if (!agreeTerms) {
                e.preventDefault();
                alert('You must agree to the terms and conditions.');
                return;
            }
        });
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        function showTerms() {
            alert('Terms & Conditions\n\nPlease read our terms and conditions carefully before using our services.');
        }
        
        function showPrivacy() {
            alert('Privacy Policy\n\nWe respect your privacy and are committed to protecting your personal information.');
        }
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthIndicator = document.querySelector('.password-strength');
            
            if (password.length === 0) {
                if (strengthIndicator) strengthIndicator.remove();
                return;
            }
            
            let strength = 0;
            let strengthText = '';
            let strengthClass = '';
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            switch (strength) {
                case 0:
                case 1:
                    strengthText = 'Very Weak';
                    strengthClass = 'very-weak';
                    break;
                case 2:
                    strengthText = 'Weak';
                    strengthClass = 'weak';
                    break;
                case 3:
                    strengthText = 'Fair';
                    strengthClass = 'fair';
                    break;
                case 4:
                    strengthText = 'Good';
                    strengthClass = 'good';
                    break;
                case 5:
                    strengthText = 'Strong';
                    strengthClass = 'strong';
                    break;
            }
            
            if (!document.querySelector('.password-strength')) {
                const indicator = document.createElement('div');
                indicator.className = 'password-strength';
                this.parentNode.parentNode.appendChild(indicator);
            }
            
            const indicator = document.querySelector('.password-strength');
            indicator.className = `password-strength ${strengthClass}`;
            indicator.textContent = `Password Strength: ${strengthText}`;
        });
    </script>
</body>
</html> 