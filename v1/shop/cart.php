<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../models/ModelLoader.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? null) !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$Product = ModelLoader::load('Product');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    case 'add':
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $product = $Product->find($productId);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        // Add or update cart
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'price' => $product['selling_price'],
                'quantity' => $quantity
            ];
        }
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        break;
    case 'update':
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        break;
    case 'remove':
        $productId = (int)($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$productId]);
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        break;
    case 'get':
    default:
        echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
        break;
} 