<?php
session_start();
require_once __DIR__ . '/../../models/ModelLoader.php';
require_once __DIR__ . '/../../services/PermissionService.php';

// --- Auth: Only allow logged-in customers ---
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: /agrimarket-erd/v1/auth/login/');
    exit;
}
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? null;
if ($userRole !== 'customer') {
    header('Location: /agrimarket-erd/v1/dashboard/');
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
$products = $Product->findAll(['status' => 'active']);
$cartItems = [];
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartItems = $_SESSION['cart'];
}

// --- HTML ---
?>
<!DOCTYPE html>
<html>
<head>
    <title>AgriMarket Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-left">
            <a href="index.php" class="nav-logo">AgriMarket</a>
            <a href="index.php">Home</a>
            <a href="#cart-section">Cart</a>
            <a href="#compare-section">Product Comparison</a>
        </div>
        <div class="nav-right">
            <a href="/agrimarket-erd/logout.php">Logout</a>
        </div>
    </nav>

    <!-- Product Listing -->
    <section class="products-section" id="shop-section">
        <h2>Shop Products</h2>
        <div class="products-grid">
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

    <script src="cart.js"></script>
    <script src="order.js"></script>
    <script src="review.js"></script>
    <script src="compare.js"></script>
</body>
</html> 