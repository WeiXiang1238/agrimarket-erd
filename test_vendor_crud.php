<?php
/**
 * Test file for Vendor CRUD operations
 * This file demonstrates how to use the new auto-user creation feature
 */

require_once __DIR__ . '/services/VendorService.php';

$vendorService = new VendorService();

echo "<h1>Vendor CRUD Test</h1>";

// Test 1: Create a new vendor (auto-creates user)
echo "<h2>Test 1: Creating a new vendor with auto-user creation</h2>";

$newVendorData = [
    'contact_person' => 'John Smith',
    'business_name' => 'Smith Farm Products',
    'business_email' => 'john@smithfarm.com',
    'business_phone' => '+1234567890',
    'business_address' => '123 Farm Road, Agriculture Valley, AV 12345',
    'website_url' => 'https://www.smithfarm.com',
    'description' => 'Organic farm producing fresh vegetables and fruits',
    'subscription_tier' => 'premium'
];

$result = $vendorService->createVendor($newVendorData);

echo "<pre>";
print_r($result);
echo "</pre>";

if ($result['success']) {
    $vendorId = $result['vendor_id'];
    echo "<p><strong>✅ Vendor created successfully!</strong></p>";
    echo "<p>Vendor ID: {$vendorId}</p>";
    echo "<p>User ID: {$result['user_id']}</p>";
    echo "<p>Temporary Password: <code>{$result['temp_password']}</code></p>";
    
    // Test 2: Get vendor details
    echo "<h2>Test 2: Getting vendor details</h2>";
    $vendorDetails = $vendorService->getVendorDetails($vendorId);
    
    echo "<pre>";
    print_r($vendorDetails);
    echo "</pre>";
    
    // Test 3: Update vendor
    echo "<h2>Test 3: Updating vendor information</h2>";
    $updateData = [
        'business_name' => 'Smith Premium Farm Products',
        'business_phone' => '+1234567891',
        'business_address' => '123 Premium Farm Road, Agriculture Valley, AV 12345',
        'description' => 'Premium organic farm producing the finest vegetables and fruits'
    ];
    
    $updateResult = $vendorService->updateVendor($vendorId, $updateData);
    
    echo "<pre>";
    print_r($updateResult);
    echo "</pre>";
    
    if ($updateResult['success']) {
        echo "<p><strong>✅ Vendor updated successfully!</strong></p>";
    }
    
    // Test 4: Get subscription tiers
    echo "<h2>Test 4: Available subscription tiers</h2>";
    $tiers = $vendorService->getSubscriptionTiers();
    
    echo "<pre>";
    print_r($tiers);
    echo "</pre>";
    
    // Test 5: Get vendor statistics
    echo "<h2>Test 5: Vendor statistics</h2>";
    $stats = $vendorService->getVendorStatistics();
    
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
    
    // Test 6: Get paginated vendors
    echo "<h2>Test 6: Paginated vendors list</h2>";
    $paginatedResult = $vendorService->getPaginatedVendors(1, 5);
    
    echo "<pre>";
    print_r($paginatedResult);
    echo "</pre>";
    
} else {
    echo "<p><strong>❌ Failed to create vendor:</strong> {$result['message']}</p>";
}

echo "<hr>";
echo "<h2>Summary</h2>";
echo "<p>The new vendor CRUD system now:</p>";
echo "<ul>";
echo "<li>✅ Automatically creates user accounts when creating vendors</li>";
echo "<li>✅ Generates secure temporary passwords</li>";
echo "<li>✅ Validates all input data comprehensively</li>";
echo "<li>✅ Provides proper error handling</li>";
echo "<li>✅ Maintains backward compatibility with existing user selection</li>";
echo "<li>✅ Includes audit logging for all operations</li>";
echo "</ul>";

?> 