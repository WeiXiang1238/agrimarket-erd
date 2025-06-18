<?php
// Start session and include DB connection if needed
if (session_status() == PHP_SESSION_NONE) session_start();

$vendor_name = 'to the Vendor Dashboard'; // Default fallback

$_SESSION['vendor_id'] = 1; //temporary
// echo "Vendor ID from session: " . ($_SESSION['vendor_id'] ?? 'Not Set') . "<br>"; //debug line

// Only run DB query if vendor_id is available
if (isset($_SESSION['vendor_id'])) {
    ob_start(); // Prevent "Connected successfully" from displaying
    include 'Db_Connect.php'; // Optional if already connected earlier
    ob_end_clean(); // Clear the buffer

    // Assuming $vendor_id is already stored in session
    $vendor_id = $_SESSION['vendor_id']?? 1;

    $vendorQuery = $conn->prepare("
        SELECT v.business_name 
        FROM vendors v 
        WHERE v.vendor_id = ?
    ");
    $vendorQuery->bind_param("i", $vendor_id);
    $vendorQuery->execute();
    $vendorResult = $vendorQuery->get_result();
    $vendorRow = $vendorResult->fetch_assoc();

    if ($vendorRow) {
        $vendor_name = "back, " . $vendorRow['business_name'];
    } 

    $vendorQuery->close();
    $conn->close();
}
?>
<header>
    <h1>Welcome <?= htmlspecialchars($vendor_name) ?></h1>
</header>
