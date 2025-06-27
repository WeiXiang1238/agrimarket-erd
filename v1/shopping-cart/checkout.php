<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/OrderService.php';
require_once __DIR__ . '/../../services/PaymentService.php';
require_once __DIR__ . '/../../services/ShoppingCartService.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // Check authentication
    $authService = new AuthService();
    $currentUser = $authService->getCurrentUser();
    
    if (!$currentUser || $currentUser['role'] !== 'customer') {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized. Customer access required.']);
        exit;
    }
    
    // Get customer ID
    $customerId = $authService->getCustomerId($currentUser['user_id']);
    if (!$customerId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Customer profile not found']);
        exit;
    }
    
    // Get and validate input data
    $shippingName = trim($_POST['shipping_name'] ?? '');
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $shippingCity = trim($_POST['shipping_city'] ?? '');
    $shippingPostal = trim($_POST['shipping_postal'] ?? '');
    $paymentMethodId = (int)($_POST['payment_method'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    
    // Validate required fields
    $errors = [];
    if (empty($shippingName)) $errors[] = 'Shipping name is required';
    if (empty($shippingAddress)) $errors[] = 'Shipping address is required';
    if (empty($shippingCity)) $errors[] = 'Shipping city is required';
    if (empty($shippingPostal)) $errors[] = 'Shipping postal code is required';
    if ($paymentMethodId <= 0) $errors[] = 'Payment method is required';
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    // Format shipping address
    $fullShippingAddress = [
        'name' => $shippingName,
        'address' => $shippingAddress,
        'city' => $shippingCity,
        'postal_code' => $shippingPostal
    ];
    
    // Initialize services
    $orderService = new OrderService();
    $paymentService = new PaymentService();
    $cartService = new ShoppingCartService();
    
    // Validate cart before checkout
    $cartData = $cartService->getCartItems($customerId);
    if (!$cartData['success'] || empty($cartData['items'])) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit;
    }
    
    $cartSummary = $cartService->getCartSummary($customerId);
    if (!$cartSummary['success']) {
        echo json_encode(['success' => false, 'message' => 'Unable to calculate cart total']);
        exit;
    }
    
    // Check if all items are available
    $unavailableItems = [];
    foreach ($cartData['items'] as $item) {
        if ($item['availability_status'] !== 'available') {
            $unavailableItems[] = $item['product_name'];
        }
    }
    
    if (!empty($unavailableItems)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Some items are no longer available: ' . implode(', ', $unavailableItems)
        ]);
        exit;
    }
    
    // Validate payment method
    $paymentMethods = $paymentService->getAvailablePaymentMethods($cartSummary['summary']['final_amount']);
    if (!$paymentMethods['success']) {
        echo json_encode(['success' => false, 'message' => 'Unable to load payment methods']);
        exit;
    }
    
    $validPaymentMethod = false;
    foreach ($paymentMethods['payment_methods'] as $method) {
        if ($method['payment_method_id'] == $paymentMethodId) {
            $validPaymentMethod = $method;
            break;
        }
    }
    
    if (!$validPaymentMethod) {
        echo json_encode(['success' => false, 'message' => 'Invalid payment method selected']);
        exit;
    }
    
    // Create order from cart
    $orderResult = $orderService->createOrderFromCart(
        $customerId,
        json_encode($fullShippingAddress),
        json_encode($fullShippingAddress), // Use same for billing
        $paymentMethodId,
        $notes
    );
    
    if (!$orderResult['success']) {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to create order: ' . $orderResult['message']
        ]);
        exit;
    }
    
    $orders = $orderResult['orders'];
    $processedPayments = [];
    $failedPayments = [];
    
    // Process payment for each order
    foreach ($orders as $order) {
        // Prepare payment data based on payment method
        $paymentData = [];
        
        // For demo purposes, we'll use mock data
        // In production, you'd collect real payment details
        switch (strtolower($validPaymentMethod['code'])) {
            case 'credit_card':
            case 'debit_card':
                $paymentData = [
                    'card_number' => '4111111111111111', // Mock card
                    'cvv' => '123',
                    'expiry_month' => '12',
                    'expiry_year' => '2025'
                ];
                break;
            case 'fpx':
            case 'bank_transfer':
                $paymentData = [
                    'bank_code' => 'MAYBANK'
                ];
                break;
            default:
                $paymentData = [];
        }
        
        $paymentResult = $paymentService->processPayment(
            $order['order_id'],
            $paymentMethodId,
            $order['total_amount'],
            $paymentData
        );
        
        if ($paymentResult['success']) {
            $processedPayments[] = [
                'order_id' => $order['order_id'],
                'payment_id' => $paymentResult['payment_id'],
                'reference_number' => $paymentResult['reference_number'],
                'transaction_id' => $paymentResult['transaction_id'] ?? null
            ];
        } else {
            $failedPayments[] = [
                'order_id' => $order['order_id'],
                'error' => $paymentResult['message']
            ];
        }
    }
    
    // Determine overall success
    if (count($processedPayments) > 0 && count($failedPayments) === 0) {
        // All payments successful
        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully!',
            'orders' => $orders,
            'payments' => $processedPayments,
            'redirect' => '/agrimarket-erd/v1/order-management/'
        ]);
    } elseif (count($processedPayments) > 0 && count($failedPayments) > 0) {
        // Partial success
        echo json_encode([
            'success' => true,
            'message' => 'Some orders processed successfully, but some payments failed.',
            'orders' => $orders,
            'successful_payments' => $processedPayments,
            'failed_payments' => $failedPayments,
            'redirect' => '/agrimarket-erd/v1/order-management/'
        ]);
    } else {
        // All payments failed
        echo json_encode([
            'success' => false,
            'message' => 'Payment processing failed for all orders.',
            'orders' => $orders,
            'failed_payments' => $failedPayments
        ]);
    }
    
} catch (Exception $e) {
    error_log('Checkout error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred during checkout. Please try again.'
    ]);
}
?> 