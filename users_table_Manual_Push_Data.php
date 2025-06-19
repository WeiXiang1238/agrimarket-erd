<?php

//this function is for 1st time run only to create new data into databse.

// Include your DB connection file
ob_start(); // Prevent "Connected successfully" from displaying
include 'Db_Connect.php';
ob_end_clean(); // Clear the buffer

$name = 'Vendor A';
$email = 'vendorA@example.com';
$password = password_hash('123456', PASSWORD_DEFAULT); // Secure hashing
$role = 'admin';

// Validate role
$valid_roles = ['admin', 'vendor', 'customer', 'staff'];
if (!in_array($role, $valid_roles)) {
    die("⚠️ Error: Invalid role. Allowed roles are: " . implode(", ", $valid_roles));
}

// Check for duplicate email
$check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "⚠️ Email already exists: $email<br>";
} else {
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    if ($stmt->execute()) {
        echo "✅ New entry inserted for: $email<br>";
    } else {
        echo "❌ Error inserting user: " . $stmt->error . "<br>";
    }
    $stmt->close();
}


$check->close();
$conn->close();

echo "You can close this windows now..." . "<br>";

?>
