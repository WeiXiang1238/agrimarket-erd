<?php
session_start();
require_once __DIR__ . '/../../services/CartManagementService.php';

// Set page title for tracking
$pageTitle = 'Shopping Cart - AgriMarket Solutions';

// Include page tracking
require_once __DIR__ . '/../../includes/page_tracking.php';

// Initialize cart management service
$cartMgmtService = new CartManagementService();

// Initialize page data
$initResult = $cartMgmtService->initializeShoppingCart();

if (!$initResult['success']) {
    if (isset($initResult['redirect'])) {
        header('Location: ' . $initResult['redirect']);
        exit;
    }
    die($initResult['message']);
}

$customerId = $initResult['customerId'];
$currentUser = $initResult['currentUser'];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $result = $cartMgmtService->handleCartAction($action, $_POST, $customerId);
    
    echo json_encode($result);
    exit;
}

// Get cart page data
$cartPageData = $cartMgmtService->getCartPageData($customerId);
$cartItems = $cartPageData['cartItems'] ?? [];
error_log("Cart items value: " . var_export($cartItems, true));
error_log("Cart items: " . print_r($cartItems, true));
$cartSummary = $cartPageData['cartSummary'] ?? [];
$comparisonProducts = $cartPageData['comparisonProducts'] ?? [];
$paymentMethods = $cartPageData['paymentMethods'] ?? [];
$isEmpty = $cartPageData['isEmpty'] ?? true;

// Get cart validation warnings
$warnings = $cartMgmtService->validateCartState($cartItems);

// Get formatted cart summary
$formattedSummary = $cartMgmtService->getFormattedCartSummary($cartSummary);

// Check if cart is valid for checkout
$checkoutValidation = $cartMgmtService->isCartValidForCheckout($cartItems, $cartSummary);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - AgriMarket Solutions</title>
    <link rel="stylesheet" href="../components/main.css">
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include '../components/sidebar.php'; ?>
        
        <main class="main-content">
            <?php 
            $pageTitle = 'Shopping Cart';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2><i class="fas fa-shopping-cart"></i> Shopping Cart</h2>
                            <p>Review and manage your selected items</p>
                        </div>
                        <div class="cart-actions">
                            <button class="btn btn-secondary" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Clear Cart
                            </button>
                            <button class="btn btn-primary" onclick="showComparison()">
                                <i class="fas fa-balance-scale"></i> Compare (<?php echo count($comparisonProducts); ?>)
                            </button>
                        </div>
                    </div>
                </div>

            <?php if ($isEmpty): ?>
                <div class="content-card">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-muted mb-3">Your cart is empty</h3>
                        <p class="text-muted mb-4">Add some products to get started with your shopping experience.</p>
                        <a href="/agrimarket-erd/v1/products/" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Browse Products
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Cart Validation Warnings -->
                <?php if (!empty($warnings)): ?>
                    <div class="cart-warnings">
                        <?php foreach ($warnings as $warning): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?php echo htmlspecialchars($warning); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Checkout Validation -->
                <?php if (!$checkoutValidation['valid']): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i>
                        <?php echo htmlspecialchars($checkoutValidation['message']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="cart-content">
                    <div class="cart-items">
                        <div class="cart-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr class="cart-item" data-cart-id="<?php echo $item['cart_id']; ?>">
                                            <td class="product-info">
                                                <div class="product-image">
                                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                                </div>
                                                <div class="product-details">
                                                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                                    <p class="vendor">by <?php echo htmlspecialchars($item['vendor_name']); ?></p>
                                                    <p class="category"><?php echo htmlspecialchars($item['category']); ?></p>
                                                    <?php if ($item['availability_status'] !== 'available'): ?>
                                                        <span class="status-badge status-out-of-stock">Out of Stock</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="price">
                                                <span class="current-price">RM <?php echo number_format($item['selling_price'], 2); ?></span>
                                                <?php if ($item['is_discounted']): ?>
                                                    <span class="original-price">RM <?php echo number_format($item['base_price'], 2); ?></span>
                                                    <span class="discount-badge"><?php echo $item['discount_percent']; ?>% OFF</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="quantity">
                                                <div class="quantity-controls">
                                                    <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)" 
                                                            <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>-</button>
                                                    <input type="number" value="<?php echo $item['quantity']; ?>" 
                                                           min="1" max="<?php echo $item['stock_quantity']; ?>"
                                                           onchange="updateQuantity(<?php echo $item['cart_id']; ?>, this.value)">
                                                    <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)"
                                                            <?php echo $item['quantity'] >= $item['stock_quantity'] ? 'disabled' : ''; ?>>+</button>
                                                </div>
                                                <small>Max: <?php echo $item['stock_quantity']; ?></small>
                                            </td>
                                            <td class="subtotal">
                                                <strong>RM <?php echo number_format($item['subtotal'], 2); ?></strong>
                                            </td>
                                            <td class="actions">
                                                <button class="btn-icon btn-primary" 
                                                        onclick="addToComparison(<?php echo $item['product_id']; ?>)"
                                                        title="Add to Comparison">
                                                    <i class="fas fa-balance-scale"></i>
                                                </button>
                                                <button class="btn-icon btn-danger" 
                                                        onclick="removeItem(<?php echo $item['cart_id']; ?>)"
                                                        title="Remove Item">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="cart-summary">
                        <div class="summary-card">
                            <h3>Order Summary</h3>
                            <div class="summary-row">
                                <span>Subtotal (<?php echo $formattedSummary['itemCount']; ?> items)</span>
                                <span>RM <?php echo $formattedSummary['subtotal']; ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span>RM <?php echo $formattedSummary['shipping']; ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Tax (6%)</span>
                                <span>RM <?php echo $formattedSummary['tax']; ?></span>
                            </div>
                            <div class="summary-row total">
                                <span><strong>Total</strong></span>
                                <span><strong>RM <?php echo $formattedSummary['total']; ?></strong></span>
                            </div>
                            
                            <div class="checkout-actions">
                                <button class="btn btn-primary btn-large" 
                                        onclick="proceedToCheckout()" 
                                        <?php echo !$checkoutValidation['valid'] ? 'disabled title="' . htmlspecialchars($checkoutValidation['message']) . '"' : ''; ?>>
                                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                                </button>
                                <a href="/agrimarket-erd/v1/products/" class="btn btn-secondary">
                                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Product Comparison Modal -->
            <div id="comparisonModal" class="modal">
                <div class="modal-dialog large-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><i class="fas fa-balance-scale"></i> Product Comparison</h3>
                            <button class="modal-close" onclick="closeComparison()">&times;</button>
                        </div>
                        <div class="modal-body" id="comparisonContent">
                            <!-- Comparison content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Checkout Modal -->
            <div id="checkoutModal" class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3><i class="fas fa-credit-card"></i> Checkout</h3>
                            <button class="modal-close" onclick="closeCheckout()">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="checkoutForm">
                                <div class="checkout-step">
                                    <h3>Shipping Address</h3>
                                    <div class="form-group">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="shipping_name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Address</label>
                                        <textarea name="shipping_address" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">City</label>
                                            <input type="text" name="shipping_city" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Postal Code</label>
                                            <input type="text" name="shipping_postal" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="checkout-step">
                                    <h3>Payment Method</h3>
                                    <div class="payment-methods">
                                        <?php if (!empty($paymentMethods)): ?>
                                            <?php foreach ($paymentMethods as $method): ?>
                                                <label class="payment-method">
                                                    <input type="radio" name="payment_method" value="<?php echo $method['payment_method_id']; ?>" required>
                                                    <div class="method-info">
                                                        <strong><?php echo htmlspecialchars($method['name']); ?></strong>
                                                        <p><?php echo htmlspecialchars($method['description']); ?></p>
                                                        <?php if ($method['processing_fee_percent'] > 0): ?>
                                                            <small>Processing fee: <?php echo $method['processing_fee_percent']; ?>%</small>
                                                        <?php endif; ?>
                                                    </div>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="checkout-total">
                                    <h4>Total: RM <?php echo $formattedSummary['total']; ?></h4>
                                </div>

                                <div class="checkout-actions">
                                    <button type="submit" class="btn btn-primary btn-large">
                                        <i class="fas fa-credit-card"></i> Place Order
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="closeCheckout()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
    <script src="/agrimarket-erd/v1/components/page_tracking.js"></script>
</body>
</html> 