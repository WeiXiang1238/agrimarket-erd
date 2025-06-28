<?php
// Debug page for order management issues
require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/OrderService.php';
require_once __DIR__ . '/../../services/OrderManagementService.php';

echo "<h1>Order Management Debug</h1>";

try {
    // Test database connection
    echo "<h2>1. Database Connection Test</h2>";
    global $host, $user, $pass, $dbname;
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    echo "✅ Database connection successful<br>";
    
    // Check total orders
    echo "<h2>2. Orders Table Check</h2>";
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM orders");
    $stmt->execute();
    $totalOrders = $stmt->fetch()['total'];
    echo "Total orders in database: " . $totalOrders . "<br>";
    
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM orders WHERE is_archive = 0");
    $stmt->execute();
    $activeOrders = $stmt->fetch()['total'];
    echo "Active orders (not archived): " . $activeOrders . "<br>";
    
    // Show sample orders
    if ($activeOrders > 0) {
        echo "<h3>Sample Orders:</h3>";
        $stmt = $db->prepare("
            SELECT o.order_id, o.status, o.order_date, v.business_name, c.customer_id, u.name as customer_name
            FROM orders o
            LEFT JOIN vendors v ON o.vendor_id = v.vendor_id
            LEFT JOIN customers c ON o.customer_id = c.customer_id
            LEFT JOIN users u ON c.user_id = u.user_id
            WHERE o.is_archive = 0
            LIMIT 5
        ");
        $stmt->execute();
        $sampleOrders = $stmt->fetchAll();
        
        echo "<table border='1'>";
        echo "<tr><th>Order ID</th><th>Status</th><th>Date</th><th>Vendor</th><th>Customer</th></tr>";
        foreach ($sampleOrders as $order) {
            echo "<tr>";
            echo "<td>" . $order['order_id'] . "</td>";
            echo "<td>" . $order['status'] . "</td>";
            echo "<td>" . $order['order_date'] . "</td>";
            echo "<td>" . ($order['business_name'] ?: 'N/A') . "</td>";
            echo "<td>" . ($order['customer_name'] ?: 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test AuthService
    echo "<h2>3. AuthService Test</h2>";
    session_start();
    $authService = new AuthService();
    
    if ($authService->isAuthenticated()) {
        echo "✅ User is authenticated<br>";
        
        $currentUser = $authService->getCurrentUser();
        echo "Current user: " . print_r($currentUser, true) . "<br>";
        
        $userRoles = $authService->getCurrentUserWithRoles();
        echo "User roles: " . print_r($userRoles, true) . "<br>";
        
        // Test OrderService
        echo "<h2>4. OrderService Test</h2>";
        $orderService = new OrderService();
        
        if ($userRoles['isAdmin']) {
            echo "Testing getAllOrders for admin...<br>";
            $result = $orderService->getAllOrders(1, 10);
            echo "Result: " . print_r($result, true) . "<br>";
        } else if ($userRoles['isCustomer']) {
            echo "Testing getCustomerOrderHistory for customer...<br>";
            if ($userRoles['customerId']) {
                $result = $orderService->getCustomerOrderHistory($userRoles['customerId'], 1, 10);
                echo "Result: " . print_r($result, true) . "<br>";
            } else {
                echo "❌ Customer ID is missing<br>";
            }
        } else if ($userRoles['isVendor']) {
            echo "Testing getVendorOrders for vendor...<br>";
            if ($userRoles['vendorId']) {
                $result = $orderService->getVendorOrders($userRoles['vendorId'], 1, 10);
                echo "Result: " . print_r($result, true) . "<br>";
            } else {
                echo "❌ Vendor ID is missing<br>";
            }
        }
        
    } else {
        echo "❌ User is not authenticated<br>";
    }
    
    // Test vendors
    echo "<h2>5. Vendors Table Check</h2>";
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM vendors WHERE is_archive = 0");
    $stmt->execute();
    $totalVendors = $stmt->fetch()['total'];
    echo "Total active vendors: " . $totalVendors . "<br>";
    
    if ($totalVendors > 0) {
        $stmt = $db->prepare("SELECT vendor_id, business_name FROM vendors WHERE is_archive = 0 LIMIT 5");
        $stmt->execute();
        $vendors = $stmt->fetchAll();
        
        echo "<h3>Sample Vendors:</h3>";
        echo "<ul>";
        foreach ($vendors as $vendor) {
            echo "<li>ID: " . $vendor['vendor_id'] . " - " . $vendor['business_name'] . "</li>";
        }
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}

echo "<br><a href='index.php'>← Back to Order Management</a>";
?> 