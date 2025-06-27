<?php
session_start();
require_once __DIR__ . '/../../services/ProductService.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/ShoppingCartService.php';

// Check authentication - customers only
$authService = new AuthService();
$currentUser = $authService->getCurrentUser();

if (!$currentUser || $currentUser['role'] !== 'customer') {
    header('Location: /agrimarket-erd/v1/auth/login/');
    exit;
}

// Get customer ID
$customerId = $authService->getCustomerId($currentUser['user_id']);
if (!$customerId) {
    die('Customer profile not found');
}

$productService = new ProductService();
$cartService = new ShoppingCartService();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_to_cart':
            $productId = (int)($_POST['product_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 1);
            
            if ($productId <= 0 || $quantity <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
                exit;
            }
            
            $result = $cartService->addToCart($customerId, $productId, $quantity);
            echo json_encode($result);
            exit;
            
        case 'get_products':
            $page = (int)($_POST['page'] ?? 1);
            $limit = (int)($_POST['limit'] ?? 12);
            
            $filters = [
                'search' => $_POST['search'] ?? '',
                'category_id' => $_POST['category_id'] ?? '',
                'status' => 'active' // Only show active products to customers
            ];
            
            $result = $productService->getPaginatedProducts($page, $limit, $filters, 'customer');
            echo json_encode($result);
            exit;
            
        case 'get_product_details':
            $productId = (int)($_POST['product_id'] ?? 0);
            $result = $productService->getProductById($productId, 'customer');
            echo json_encode($result);
            exit;
            
        case 'get_cart_count':
            $result = $cartService->getCartItemCount($customerId);
            echo json_encode($result);
            exit;
    }
}

// Get initial data for page load
$page = (int)($_GET['page'] ?? 1);
$search = $_GET['search'] ?? '';
$categoryId = $_GET['category_id'] ?? '';

$filters = [
    'search' => $search,
    'category_id' => $categoryId,
    'status' => 'active'
];

// Get products
$productsData = $productService->getPaginatedProducts($page, 12, $filters, 'customer');
$products = $productsData['products'] ?? [];
$totalPages = $productsData['totalPages'] ?? 1;

// Get categories for filter
$categories = $productService->getCategories();

// Get cart item count
$cartCount = $cartService->getCartItemCount($customerId);
$cartItemCount = $cartCount['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Products - AgriMarket</title>
    <link rel="stylesheet" href="../components/main.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include '../components/sidebar.php'; ?>
        
        <main class="main-content">
            <?php 
            $pageTitle = 'Shop Products';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <div class="page-header">
                    <h1><i class="fas fa-shopping-bag"></i> Shop Products</h1>
                    <div class="header-actions">
                        <a href="/agrimarket-erd/v1/shopping-cart/" class="btn btn-primary">
                            <i class="fas fa-shopping-cart"></i> 
                            Cart (<span id="cartCount"><?php echo $cartItemCount; ?></span>)
                        </a>
                    </div>
                </div>

            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filter-group">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>" 
                                    <?php echo $categoryId == $category['category_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <button class="btn btn-secondary" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-section">
                <div id="productsGrid" class="products-grid">
                    <?php if (empty($products)): ?>
                        <div class="no-products">
                            <div class="no-products-icon">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <h3>No products found</h3>
                            <p>Try adjusting your search or filter criteria.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card" data-product-id="<?php echo $product['product_id']; ?>">
                                <div class="product-image">
                                    <img src="../../<?php echo !empty($product['image_path']) ? 
                                        htmlspecialchars($product['image_path']) : 
                                        '/agrimarket-erd/uploads/products/default-product.jpg'; ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    
                                    <?php if ($product['is_discounted']): ?>
                                        <div class="discount-badge">
                                            <?php echo $product['discount_percent']; ?>% OFF
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-actions">
                                        <button class="btn-icon btn-primary" 
                                                onclick="viewProductDetails(<?php echo $product['product_id']; ?>)"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-icon btn-success" 
                                                onclick="quickAddToCart(<?php echo $product['product_id']; ?>)"
                                                title="Quick Add to Cart"
                                                <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="product-info">
                                    <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="product-vendor">by <?php echo htmlspecialchars($product['vendor_name']); ?></p>
                                    <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                    
                                    <div class="product-pricing">
                                        <span class="current-price">RM <?php echo number_format($product['selling_price'], 2); ?></span>
                                        <?php if ($product['is_discounted']): ?>
                                            <span class="original-price">RM <?php echo number_format($product['base_price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="product-stock">
                                        <?php if ($product['stock_quantity'] > 0): ?>
                                            <span class="stock-available">
                                                <i class="fas fa-check-circle"></i>
                                                <?php echo $product['stock_quantity']; ?> in stock
                                            </span>
                                        <?php else: ?>
                                            <span class="stock-out">
                                                <i class="fas fa-times-circle"></i>
                                                Out of stock
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="product-actions-bottom">
                                        <button class="btn btn-primary btn-add-cart" 
                                                onclick="showAddToCartModal(<?php echo $product['product_id']; ?>)"
                                                <?php echo $product['stock_quantity'] <= 0 ? 'disabled' : ''; ?>>
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo urlencode($categoryId); ?>" 
                               class="page-link">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo urlencode($categoryId); ?>" 
                               class="page-link <?php echo $page === $i ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&category_id=<?php echo urlencode($categoryId); ?>" 
                               class="page-link">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Details Modal -->
            <div id="productDetailsModal" class="modal">
                <div class="modal-content modal-large">
                    <div class="modal-header">
                        <h2><i class="fas fa-cube"></i> Product Details</h2>
                        <button class="close-modal" onclick="closeProductDetails()">&times;</button>
                    </div>
                    <div class="modal-body" id="productDetailsContent">
                        <!-- Product details will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Add to Cart Modal -->
            <div id="addToCartModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><i class="fas fa-cart-plus"></i> Add to Cart</h2>
                        <button class="close-modal" onclick="closeAddToCart()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="addToCartContent">
                            <!-- Add to cart form will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html> 