<?php
session_start();

// Check if vendor_id is set in session, if not redirect to login
if (!isset($_SESSION['vendor_id'])) {
    header("Location: login.php");
    exit;
}
$vendor_id = $_SESSION['vendor_id'];

// Include your DB connection file
ob_start(); // Prevent "Connected successfully" from displaying
include 'Db_Connect.php';
ob_end_clean(); // Clear the buffer

// Assuming vendor_id is stored in session
// $vendor_id = $_SESSION['vendor_id'] ; ?? 3; //temporary for testing

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
        .card { 
            display: inline-block; 
            width: 220px; 
            padding: 1rem; 
            margin: 1rem; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            background: #f9f9f9; 
        }
        .card h3 { margin-top: 0; color: #333; }
        a:link{
            color: rgb(155, 161, 165);
            text-decoration: underline;
        }
        a:hover{
            color: rgb(9, 147, 240);
        }
        .logout-button {
            position: fixed;
            top: 20px;
            right: 30px;
            z-index: 999;
            background-color:rgb(10, 111, 170);
            padding: 8px 12px;
            border-radius: 5px;
        }
        .logout-button a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .logout-button a:hover {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <!-- Logout Button -->
    <div class="logout-button">
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Summary Cards -->
    <div style="display: flex; flex-wrap: wrap; align-items: flex-start;">
        <div class="card" onclick="location.href='product_list.php';" style="cursor: pointer;">
            <h3>🛒 Products</h3>
            <p>Total products: <?= $productCount ?></p>
        </div>
        <div class="card" onlick="location.href='order_list.php';" style="cursor: pointer;">
            <h3>📦 Orders</h3>
            <p>Total orders: <?= $orderCount ?></p>
        </div>
        <div class="card" onclick="location.href='earnings.php';" style="cursor: pointer;">
            <h3>💰 Earnings</h3>
            <p>Total earninig: RM <?= number_format($earnings, 2) ?></p>
        </div>
        <div class="card" style="width: auto;">
            <h3>🔑 Tier</h3>
            <li>Tier: <?= $subscription['name'] ?? 'N/A' ?></li>
            <li>Monthly Fee: RM <?= number_format($subscription['monthly_fee'] ?? 0, 2) ?></li>
            <li>Active From: <?= $subscription['start_date'] ?? '-' ?> to <?= $subscription['end_date'] ?? '-' ?></li>
            <li><a href="subscription_plan.php">Change Subscription?</a></li>
        </div>
    </div>



    <?php include 'footer.php'; ?>
</body>
</html>
