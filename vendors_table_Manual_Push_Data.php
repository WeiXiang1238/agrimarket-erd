<?php

//this function is for 1st time run only to create new data into databse.

// Include your DB connection file
ob_start(); // Prevent "Connected successfully" from displaying
include 'Db_Connect.php';
ob_end_clean(); // Clear the buffer

$userId = 3;
$name = 'vendor A';
$contact = '012-3456789';
$address = 'Lot 1, Jalan Satu, Kedah'; 
$subcribeTierId = 1;
// $regDate = NOW();

// Check for duplicate userId
$check = $conn->prepare("SELECT user_id FROM vendors WHERE user_id = ?");
$check->bind_param("i", $userId);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "⚠️ User Id already exists: $userId<br>";
} else {
    // Insert new vendor
    $stmt = $conn->prepare("INSERT INTO vendors (user_id, business_name, contact_number, address, subscription_tier_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $userId, $name, $contact, $address, $subcribeTierId);
    if ($stmt->execute()) {
        echo "✅ New entry inserted for: <br> user_id = $userId<br> vendor name = $name<br>";
    } else {
        echo "❌ Error inserting user: " . $stmt->error . "<br>";
    }
    $stmt->close();
}


$check->close();
$conn->close();

echo "You can close this windows now..." . "<br>";

?>
