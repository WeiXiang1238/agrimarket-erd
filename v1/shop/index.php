<?php
session_start();
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../models/ModelLoader.php';

// Use AuthService to get the current user
$authService = new AuthService();
$currentUser = $authService->getCurrentUser();

if (!$currentUser) {
    echo '<div class="alert alert-danger">You must be logged in as a customer to view the shop.</div>';
    exit;
}

$roles = explode(',', $currentUser['roles'] ?? '');
if (!in_array('customer', $roles)) {
    echo '<div class="alert alert-danger">Only customers can access the shop.</div>';
    exit;
}


// --- Load models ---
$Product = ModelLoader::load('Product');
$ProductCategory = ModelLoader::load('ProductCategory');
$ProductImage = ModelLoader::load('ProductImage');
$ProductAttribute = ModelLoader::load('ProductAttribute');
$ShoppingCart = ModelLoader::load('ShoppingCart');
$Review = ModelLoader::load('Review');
$VendorReview = ModelLoader::load('VendorReview');

// --- Fetch products, categories, and cart ---
$categories = $ProductCategory->findAll(['is_active' => 1]);
$products = $Product->findAll(['is_archive' => 0]);
$cartItems = [];
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - AgriMarket Solutions</title>
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
            $pageTitle = 'User Management';
            include '../components/header.php'; 
            ?>
<!-- Product Listing -->
<section class="products-section" id="shop-section">
    <h2>Shop Products</h2>
    <div class="products-grid">
        <?php if (empty($products)): ?>
            <div class="no-products" style="color:#888;text-align:center;width:100%;padding:2rem;">No products available at the moment.</div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <?php
                    $images = $ProductImage->findAll(['product_id' => $product['product_id']]);
                    $primaryImage = $images[0]['image_path'] ?? 'https://via.placeholder.com/150';
                    $attributes = $ProductAttribute->findAll(['product_id' => $product['product_id']]);
                ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($primaryImage) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p class="price">RM <?= number_format($product['selling_price'], 2) ?></p>
                    <form class="add-to-cart-form" data-product-id="<?= $product['product_id'] ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                    <button class="compare-btn" data-product-id="<?= $product['product_id'] ?>">Compare</button>
                    <ul class="product-attributes">
                        <?php foreach ($attributes as $attr): ?>
                            <li><strong><?= htmlspecialchars($attr['attribute_name']) ?>:</strong> <?= htmlspecialchars($attr['attribute_value']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Cart Section -->
<section class="cart-section" id="cart-section">
    <h2>Your Cart</h2>
    <div id="cart-contents">
        <!-- Cart items will be loaded here via AJAX -->
    </div>
</section>

<!-- Product Comparison Section -->
<section class="compare-section" id="compare-section">
    <h2>Product Comparison</h2>
    <div id="compare-contents">
        <!-- Comparison table will be loaded here via JS -->
    </div>
</section>

<!-- Order History Section -->
<section class="order-history-section" id="order-history-section">
    <h2>Order History</h2>
    <div id="order-history-contents">
        <!-- Order history will be loaded here via AJAX -->
    </div>
</section>

<!-- Reviews Section -->
<section class="reviews-section" id="reviews-section">
    <h2>My Reviews</h2>
    <div id="reviews-contents">
        <!-- Reviews will be loaded here via AJAX -->
    </div>
</section>

<link rel="stylesheet" href="/agrimarket-erd/v1/shop/style.css">
<script src="/agrimarket-erd/v1/shop/cart.js"></script>
<script src="/agrimarket-erd/v1/shop/order.js"></script>
<script src="/agrimarket-erd/v1/shop/review.js"></script>
<script src="/agrimarket-erd/v1/shop/compare.js"></script> 