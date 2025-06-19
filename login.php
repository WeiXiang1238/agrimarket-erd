<?php
session_start();
$message = "";

// Process form on POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    ob_start(); // Prevent "Connected successfully" from displaying
    include 'Db_Connect.php';
    ob_end_clean(); // Clear the buffer

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Fetch user by email
    $stmt = $conn->prepare("SELECT user_id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_name'] = $user['name'];

            // If vendor, get vendor_id
            if ($user['role'] === 'vendor') {
                $vendorStmt = $conn->prepare("SELECT vendor_id FROM vendors WHERE user_id = ?");
                $vendorStmt->bind_param("i", $user['user_id']);
                $vendorStmt->execute();
                $vendorResult = $vendorStmt->get_result();
                if ($vendorRow = $vendorResult->fetch_assoc()) {
                    $_SESSION['vendor_id'] = $vendorRow['vendor_id'];
                }
                $vendorStmt->close();
            }

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'vendor') {
                header("Location: dashboard_vendor.php");
            } else {
                header("Location: user_dashboard.php"); // fallback
            }
            exit;
        } else {
            $message = "❌ Invalid password.";
        }
    } else {
        $message = "❌ Invalid email.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- Basic Login Form UI -->
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        /* Flex container to control layout */
        .container {
            position: absolute;
            top: 12%;
            left: 75%;
            width: 430px;
            height: auto;
            border: 0px solid #73AD21;
        }
        .login-box {
            max-width: 400px; margin: auto;  background: white; padding: 30px;
            border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input[type=email], input[type=password] {
            width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type=submit] {
            background: #3498db; color: white; padding: 10px 15px;
            border: none; border-radius: 4px; cursor: pointer;
        }
        a:link{
            color: rgb(155, 161, 165);
            text-decoration: underline;
        }
        a:hover{
            color: rgb(9, 147, 240);
        }
        .logo{
            position: absolute;
            top: 10%;
            left: 35%;
            width: 100px;
            height: 100px;
            border: 0px solid #73AD21;
        }
        body {
            background-image: url('Image/Login/background.webp');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="Image/Login/Sow Smarter, Grow Faster, Sell Better.jpeg" alt="AgriMarket Logo" style="width: 500px; height: auto;">
    </div>

    <div style="text-align: center;color:white;">
        <h2><i>Sow Smarter, Grow Faster, Sell Better <sub><small>by AgriMarket Solutions</small></sub></i></h2>
        <h2 style="text-align: right;margin-right:20%;">Login</h2>

    </div>
    <div class="container">
        <div class="login-box">
            <?php if ($message): ?>
                <p style="color:red;"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <label>Email:</label><br>
                <input type="email" name="email" required placeholder="email"><br><br>

                <label>Password:</label><br>
                <input type="password" name="password" required placeholder="password"><br><br>

                <button type="submit">Login</button>
            </form>

            <div style="text-align:right;">
                <a href="register.php">Create New Account</a>
            </div>
        </div>
    </div>
</body>
</html>
