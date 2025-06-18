<?php

//this function is for 1st time run only to create new data into databse.

// Include your DB connection file
ob_start(); // Prevent "Connected successfully" from displaying
include 'Db_Connect.php';
ob_end_clean(); // Clear the buffer

$vendorId = 3;
$amount = 0.00;
$subcribeTierId = 1;
$startDate = date("Y-m-d");
$endDate = date("Y-m-d", strtotime("+1 month"));

// Check if this vendor already has a subscription
$check = $conn->prepare("
    SELECT id FROM vendor_subscriptions
    WHERE vendor_id = ? AND is_active = TRUE
");
$check->bind_param("i", $vendorId);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "⚠️ Vendor ID $vendorId already has an active subscription.<br>";
} else {
    // Get monthly fee from subscription_tiers
    $feeStmt = $conn->prepare("SELECT monthly_fee FROM subscription_tiers WHERE tier_id = ?");
    $feeStmt->bind_param("i", $subcribeTierId);
    $feeStmt->execute();
    $feeStmt->bind_result($monthly_fee);
    

    if ($feeStmt->fetch()) {
        $amount = $monthly_fee;

        // ✅ Important: Free result before next query
        $feeStmt->close();

        $stmt = $conn->prepare("
            INSERT INTO vendor_subscriptions 
                (vendor_id, tier_id, start_date, end_date, payment_amount)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissd", $vendorId, $subcribeTierId, $startDate, $endDate, $amount);

        if ($stmt->execute()) {
            echo "✅ Vendor subscription inserted for vendor ID $vendorId.<br>";
        } else {
            echo "❌ Error inserting vendor subscription: " . $stmt->error . "<br>";
        }

        $stmt->close();
    } else {
        echo "❌ Tier ID $subcribeTierId not found.<br>";
    }

    
}


$check->close();
$conn->close();

echo "You can close this windows now..." . "<br>";

?>
