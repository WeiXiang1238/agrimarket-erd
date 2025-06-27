<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../models/ModelLoader.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? null) !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$customerId = $_SESSION['customer_id'] ?? null;
$userId = $_SESSION['user_id'];

$ShoppingCart = ModelLoader::load('ShoppingCart');
$Order = ModelLoader::load('Order');
$OrderItem = ModelLoader::load('OrderItem');
$Payment = ModelLoader::load('Payment');
$CustomerAddress = ModelLoader::load('CustomerAddress');
$PaymentMethod = ModelLoader::load('PaymentMethod');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'place_order':
        // Place order from session cart
        $cart = $_SESSION['cart'] ?? [];
        $addressId = (int)($_POST['address_id'] ?? 0);
        $paymentMethodId = (int)($_POST['payment_method_id'] ?? 0);
        if (!$cart || !$addressId || !$paymentMethodId) {
            echo json_encode(['success' => false, 'message' => 'Missing cart, address, or payment method']);
            exit;
        }
        $address = $CustomerAddress->find($addressId);
        $paymentMethod = $PaymentMethod->find($paymentMethodId);
        if (!$address || !$paymentMethod) {
            echo json_encode(['success' => false, 'message' => 'Invalid address or payment method']);
            exit;
        }
        // Calculate totals
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        $orderData = [
            'customer_id' => $customerId,
            'vendor_id' => 1, // TODO: support multi-vendor
            'order_number' => uniqid('ORD'),
            'total_amount' => $total,
            'final_amount' => $total,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $paymentMethod['name'],
            'shipping_address' => json_encode($address),
            'order_date' => date('Y-m-d H:i:s')
        ];
        $orderId = $Order->create($orderData);
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Failed to create order']);
            exit;
        }
        // Create order items
        foreach ($cart as $item) {
            $OrderItem->create([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price_at_purchase' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity']
            ]);
        }
        // Create payment record
        $Payment->create([
            'order_id' => $orderId,
            'payment_method_id' => $paymentMethodId,
            'amount' => $total,
            'currency' => 'MYR',
            'status' => 'pending',
            'processed_at' => null
        ]);
        // Clear cart
        $_SESSION['cart'] = [];
        echo json_encode(['success' => true, 'order_id' => $orderId]);
        break;
    case 'get_history':
        // Get order history for this customer
        $orders = $Order->findAll(['customer_id' => $customerId]);
        echo json_encode(['success' => true, 'orders' => $orders]);
        break;
    case 'get_tracking':
        $orderId = (int)($_GET['order_id'] ?? 0);
        $order = $Order->find($orderId);
        if (!$order || $order['customer_id'] != $customerId) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            exit;
        }
        echo json_encode(['success' => true, 'status' => $order['status'], 'tracking_number' => $order['tracking_number']]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
} 