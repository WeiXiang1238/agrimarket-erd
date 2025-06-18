<?php
session_start();

// Include your DB connection file
ob_start(); // Prevent "Connected successfully" from displaying
include 'Db_Connect.php';
ob_end_clean(); // Clear the buffer

// Assuming vendor_id is stored in session
$vendor_id = $_SESSION['vendor_id'] ?? 1;

// Get vendor summary data
$productCount = $conn->query("SELECT COUNT(*) AS total FROM products WHERE vendor_id = $vendor_id AND is_archive = 0")->fetch_assoc()['total'];
$orderCount = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE vendor_id = $vendor_id")->fetch_assoc()['total'];
$earnings = $conn->query("SELECT SUM(vendor_earnings) AS total FROM orders WHERE vendor_id = $vendor_id")->fetch_assoc()['total'] ?? 0.00;

// Get subscription info
$subscription = $conn->query("
    SELECT st.name, st.monthly_fee, vs.start_date, vs.end_date 
    FROM vendor_subscriptions vs 
    JOIN subscription_tiers st ON vs.tier_id = st.tier_id 
    WHERE vs.vendor_id = $vendor_id AND vs.is_active = 1
    LIMIT 1
")->fetch_assoc();

// // Get vendor name
// $vendorQuery = $conn->prepare("
//     SELECT u.name 
//     FROM vendors v 
//     JOIN users u ON v.user_id = u.user_id 
//     WHERE v.vendor_id = ?
// ");
// $vendorQuery->bind_param("i", $vendor_id);
// $vendorQuery->execute();
// $vendorResult = $vendorQuery->get_result();
// $vendorRow = $vendorResult->fetch_assoc();
// $vendor_name = "back, " . $vendorRow['name'] ?? 'to Vendor Dashboard';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Dashboard</title>
    <style>
        body { font-family: Arial; margin: 2rem; }
        .card { display: inline-block; width: 220px; padding: 1rem; margin: 1rem; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; }
        .card h3 { margin-top: 0; color: #333; }
    </style>
</head>
<body>

<!-- To prevent any potential HTML injection if the name contains special characters (e.g., <, &, etc.). -->
<!-- <h1>Welcome <?= htmlspecialchars($vendor_name) ?></h1> -->
<?php include 'header.php'; ?>

<!-- Summary Cards -->
<div class="card"><h3>🛒 Products</h3><p><?= $productCount ?></p></div>
<div class="card"><h3>📦 Orders</h3><p><?= $orderCount ?></p></div>
<div class="card"><h3>💰 Earnings</h3><p>RM <?= number_format($earnings, 2) ?></p></div>
<div class="card"><h3>🔑 Tier</h3><p><?= $subscription['name'] ?? 'N/A' ?></p></div>

<!-- Subscription Info -->
<h2>📄 Subscription Details</h2>
<ul>
    <li>Tier: <?= $subscription['name'] ?? 'N/A' ?></li>
    <li>Monthly Fee: RM <?= number_format($subscription['monthly_fee'] ?? 0, 2) ?></li>
    <li>Active From: <?= $subscription['start_date'] ?? '-' ?> to <?= $subscription['end_date'] ?? '-' ?></li>
</ul>

<!-- Optional: Add latest orders or product table here -->

</body>
</html>
