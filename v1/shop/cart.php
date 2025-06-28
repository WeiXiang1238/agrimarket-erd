<?php
session_start();
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../models/ModelLoader.php';

// Use AuthService to get the current user
$authService = new AuthService();
$currentUser = $authService->getCurrentUser();

if (!$currentUser) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['action']) || isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in as a customer to access the cart.']);
    } else {
        echo '<div class="alert alert-danger">You must be logged in as a customer to access the cart.</div>';
    }
    exit;
}

$roles = explode(',', $currentUser['roles'] ?? '');
if (!in_array('customer', $roles)) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['action']) || isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'Only customers can access the cart.']);
    } else {
        echo '<div class="alert alert-danger">Only customers can access the cart.</div>';
    }
    exit;
}

// Load models
$Product = ModelLoader::load('Product');
$ShoppingCart = ModelLoader::load('ShoppingCart');
$Customer = ModelLoader::load('Customer');

// Get customer ID
$customer = $Customer->findAll(['user_id' => $currentUser['user_id']]);
$customerId = $customer[0]['customer_id'] ?? null;

if (!$customerId) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['action']) || isset($_POST['action'])) {
        echo json_encode(['success' => false, 'message' => 'Customer profile not found.']);
    } else {
        echo '<div class="alert alert-danger">Customer profile not found.</div>';
    }
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Handle AJAX requests - check for action parameter or X-Requested-With header
if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) || isset($_GET['action']) || isset($_POST['action'])) && !empty($action)) {
    // Debug: Log all AJAX requests
    error_log("AJAX request received: action=$action, method=" . $_SERVER['REQUEST_METHOD']);
    
    switch ($action) {
        case 'test':
            echo json_encode(['success' => true, 'message' => 'AJAX is working']);
            break;
            
        case 'test_update':
            // Simple test for cart update
            $testProductId = 1;
            $testQuantity = 2;
            
            // Test database connection
            try {
                $testCart = $ShoppingCart->findAll(['customer_id' => $customerId, 'product_id' => $testProductId]);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Database connection working',
                    'test_cart_items' => count($testCart),
                    'customer_id' => $customerId
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }
            break;
            
        case 'add':
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = max(1, (int)($_POST['quantity'] ?? 1));
            
            // Validate product
            $product = $Product->find($productId);
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }
            
            // Check stock availability
            if ($product['stock_quantity'] < $quantity) {
                echo json_encode(['success' => false, 'message' => 'Insufficient stock available']);
                exit;
            }
            
            // Check if item already exists in cart
            $existingCartItem = $ShoppingCart->findAll([
                'customer_id' => $customerId,
                'product_id' => $productId
            ]);
            
            if (!empty($existingCartItem)) {
                // Update existing cart item
                $newQuantity = $existingCartItem[0]['quantity'] + $quantity;
                $ShoppingCart->update($existingCartItem[0]['cart_id'], ['quantity' => $newQuantity]);
            } else {
                // Add new cart item
                $ShoppingCart->create([
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                    'quantity' => $quantity
                ]);
            }
            
            // Get updated cart
            $cartItems = getCartItems($customerId);
            echo json_encode(['success' => true, 'cart' => $cartItems]);
            break;
            
        case 'update':
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = max(1, (int)($_POST['quantity'] ?? 1));
            
            // Debug logging
            error_log("Cart update request: product_id=$productId, quantity=$quantity, customer_id=$customerId");
            
            // Find cart item
            $cartItem = $ShoppingCart->findAll([
                'customer_id' => $customerId,
                'product_id' => $productId
            ]);
            
            error_log("Found cart items: " . count($cartItem));
            
            if (!empty($cartItem)) {
                $cartId = $cartItem[0]['cart_id'];
                error_log("Updating cart item ID: $cartId");
                
                // Check stock availability
                $product = $Product->find($productId);
                if ($product && $product['stock_quantity'] < $quantity) {
                    error_log("Insufficient stock: requested=$quantity, available=" . $product['stock_quantity']);
                    echo json_encode(['success' => false, 'message' => 'Insufficient stock available']);
                    exit;
                }
                
                // Update quantity
                try {
                    $updateResult = $ShoppingCart->update($cartId, ['quantity' => $quantity]);
                    error_log("Cart update result: " . ($updateResult ? 'success' : 'failed'));
                    
                    if (!$updateResult) {
                        echo json_encode(['success' => false, 'message' => 'Failed to update cart item']);
                        exit;
                    }
                } catch (Exception $e) {
                    error_log("Cart update exception: " . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                    exit;
                }
            } else {
                error_log("Cart item not found for product_id=$productId, customer_id=$customerId");
                echo json_encode(['success' => false, 'message' => 'Cart item not found']);
                exit;
            }
            
            // Get updated cart
            $cartItems = getCartItems($customerId);
            error_log("Updated cart items count: " . count($cartItems));
            echo json_encode(['success' => true, 'cart' => $cartItems]);
            break;
            
        case 'remove':
            $productId = (int)($_POST['product_id'] ?? 0);
            
            // Find and delete cart item
            $cartItem = $ShoppingCart->findAll([
                'customer_id' => $customerId,
                'product_id' => $productId
            ]);
            
            if (!empty($cartItem)) {
                $ShoppingCart->hardDelete($cartItem[0]['cart_id']);
            }
            
            // Get updated cart
            $cartItems = getCartItems($customerId);
            echo json_encode(['success' => true, 'cart' => $cartItems]);
            break;
            
        case 'clear_cart':
            // Delete all cart items for this customer
            $cartItems = $ShoppingCart->findAll(['customer_id' => $customerId]);
            foreach ($cartItems as $item) {
                $ShoppingCart->hardDelete($item['cart_id']);
            }
            
            echo json_encode(['success' => true, 'cart' => []]);
            break;
            
        case 'get':
        default:
            $cartItems = getCartItems($customerId);
            echo json_encode(['success' => true, 'cart' => $cartItems]);
            break;
    }
    exit;
}

/**
 * Get cart items with product details
 */
function getCartItems($customerId) {
    global $ShoppingCart, $Product;
    
    $cartItems = $ShoppingCart->findAll(['customer_id' => $customerId]);
    $formattedCart = [];
    
    // Debug: Log the raw cart items
    error_log("Raw cart items: " . json_encode($cartItems));
    
    foreach ($cartItems as $item) {
        $product = $Product->find($item['product_id']);
        
        // Debug: Log if product was found
        if ($product) {
            error_log("Product found for ID {$item['product_id']}");
            error_log("Product data: " . json_encode($product));
            error_log("Selling price raw: " . $product['selling_price']);
            error_log("Selling price type: " . gettype($product['selling_price']));
            
            // Ensure price is a number
            $price = floatval($product['selling_price']);
            error_log("Price after floatval: " . $price);
            
            $formattedCart[$item['product_id']] = [
                'product_id' => (int)$item['product_id'],
                'name' => $product['name'],
                'price' => $price,
                'quantity' => (int)$item['quantity'],
                'stock_quantity' => (int)$product['stock_quantity'],
                'sku' => $product['sku'] ?? 'N/A'
            ];
            
            // Debug: Log formatted item
            error_log("Formatted cart item: " . json_encode($formattedCart[$item['product_id']]));
        } else {
            error_log("Product NOT found for ID {$item['product_id']}");
        }
    }
    
    // Debug: Log final formatted cart
    error_log("Final formatted cart: " . json_encode($formattedCart));
    
    return $formattedCart;
}

// Load cart items for display
$cartItems = getCartItems($customerId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../components/main.css">
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Shared Sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Shared Header -->
            <?php 
            $pageTitle = 'Cart';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <div class="cart-page">

                        <div class="cart-actions">
                            <div>
                            <a href="/agrimarket-erd/v1/shop/" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                            </div>
                            <div class="cart-actions" id="cart-actions"> </div>
                            <div>
                            <button class="btn btn-secondary" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Clear Cart
                            </button>
                            </div>
                        </div>
                    
                    <div id="cart-contents">
                        <!-- Cart items will be loaded here via AJAX -->
                    </div>
                    
                    <div class="cart-actions" id="cart-actions" >
                       
                        <div id="cart-actions-middle"> </div>
                        <div class="cart-actions-right">
                            <button class="btn btn-success btn-lg" onclick="proceedToCheckout()">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Pass cart data to JavaScript
        window.cartData = <?php echo json_encode($cartItems); ?>;
        console.log('=== PHP TO JS DEBUG ===');
        console.log('Initial cart data from PHP:', window.cartData);
        console.log('Cart data type:', typeof window.cartData);
        console.log('Cart data keys:', Object.keys(window.cartData));
        
        // Debug each item
        if (window.cartData && Object.keys(window.cartData).length > 0) {
            Object.values(window.cartData).forEach((item, index) => {
                console.log(`Item ${index + 1} from PHP:`, item);
                console.log(`Item ${index + 1} price type:`, typeof item.price);
                console.log(`Item ${index + 1} price value:`, item.price);
            });
        }
    </script>
    <script src="/agrimarket-erd/v1/shop/cart.js"></script>
</body>
</html>