<?php
// Only display errors for development - disable for production
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/InventoryManagementService.php';
require_once __DIR__ . '/../../services/ProductService.php';
require_once __DIR__ . '/../../services/NotificationService.php';

// Set page title for tracking
$pageTitle = 'Inventory Management - AgriMarket Solutions';

// Include page tracking
require_once __DIR__ . '/../../includes/page_tracking.php';

$authService = new AuthService();
$inventoryService = new InventoryManagementService();
$productService = new ProductService();
$notificationService = new NotificationService();

// Require authentication and vendor/admin access
$authService->requireAuth('/agrimarket-erd/v1/auth/login/');

$currentUser = $authService->getCurrentUser();
$userRoles = $authService->getCurrentUserWithRoles();

// Check if user has vendor or admin access
if (!$userRoles || (!$userRoles['isVendor'] && !$userRoles['isAdmin'])) {
    header('Location: /agrimarket-erd/v1/dashboard/');
    exit();
}
$csrfToken = $authService->generateCSRFToken();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Clear any output buffer to prevent HTML before JSON
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Content-Type: application/json');
    
    if (!$authService->validateCSRFToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
    
    try {
        // Add debugging
        error_log('Inventory request: ' . json_encode($_POST));
        
        $response = $inventoryService->handleRequest($_POST, $userRoles);
        
        // Add debugging
        error_log('Inventory response: ' . json_encode($response));
        
        echo json_encode($response);
        exit;
    } catch (Exception $e) {
        error_log('Inventory error: ' . $e->getMessage());
        error_log('Inventory error trace: ' . $e->getTraceAsString());
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}

// Get initial data
$stats = $inventoryService->handleRequest(['action' => 'get_inventory_stats'], $userRoles);
$lowStockProducts = $inventoryService->handleRequest(['action' => 'get_low_stock_products'], $userRoles);
$outOfStockProducts = $inventoryService->handleRequest(['action' => 'get_out_of_stock_products'], $userRoles);

// Get notifications for the current user
$userNotifications = [];
$unreadCount = 0;
if ($currentUser) {
    $userNotifications = $notificationService->getUserNotifications($currentUser['user_id'], 10);
    $unreadCount = $notificationService->getUnreadCount($currentUser['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - AgriMarket Solutions</title>
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
            $pageTitle = 'Inventory Management';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2>Inventory Management</h2>
                            <p>Manage your product inventory, restock items, and track stock levels</p>
                        </div>
                        <button class="btn btn-primary" onclick="openRestockModal()">
                            <i class="fas fa-plus"></i>
                            Restock Product
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon total">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['success'] ? $stats['stats']['total_products'] : 0; ?></h3>
                            <p>Total Products</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon active">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['success'] ? $stats['stats']['in_stock_products'] : 0; ?></h3>
                            <p>In Stock</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['success'] ? $stats['stats']['low_stock_products'] : 0; ?></h3>
                            <p>Low Stock</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon recent">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['success'] ? $stats['stats']['out_of_stock_products'] : 0; ?></h3>
                            <p>Out of Stock</p>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="controls-section">
                    <div class="controls-left">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search products...">
                        </div>
                        <select id="categoryFilter">
                            <option value="">All Categories</option>
                        </select>
                        <select id="statusFilter">
                            <option value="">All Status</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                    <div class="controls-right">
                        <button class="btn btn-warning" onclick="openReduceStockModal()">
                            <i class="fas fa-minus"></i> Reduce Stock
                        </button>
                        <button class="btn btn-info" onclick="openBulkRestockModal()">
                            <i class="fas fa-layer-group"></i> Bulk Restock
                        </button>
                        <button class="btn btn-secondary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- Alerts Section -->
                <?php if (($lowStockProducts['success'] && !empty($lowStockProducts['products'])) || ($outOfStockProducts['success'] && !empty($outOfStockProducts['products']))): ?>
                <div class="alerts-section">
                    <!-- Low Stock Alerts -->
                    <?php if ($lowStockProducts['success'] && !empty($lowStockProducts['products'])): ?>
                    <div class="alert alert-warning">
                        <h4><i class="fas fa-exclamation-triangle"></i> Low Stock Alert</h4>
                        <p><?php echo count($lowStockProducts['products']); ?> products are running low on stock.</p>
                        <button class="btn btn-sm btn-warning" onclick="showLowStockProducts()">View Details</button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Out of Stock Alerts -->
                    <?php if ($outOfStockProducts['success'] && !empty($outOfStockProducts['products'])): ?>
                    <div class="alert alert-danger">
                        <h4><i class="fas fa-times-circle"></i> Out of Stock Alert</h4>
                        <p><?php echo count($outOfStockProducts['products']); ?> products are out of stock.</p>
                        <button class="btn btn-sm btn-danger" onclick="showOutOfStockProducts()">View Details</button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Inventory Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h3>Product Inventory</h3>
                        <div class="table-controls">
                            <select id="limitSelect" onchange="loadInventory()">
                                <option value="10">10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-content">
                        <div id="inventoryTableContent">
                            <div class="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Loading inventory data...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Restock Modal -->
    <div id="restockModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Restock Product</h3>
                <span class="close" onclick="closeRestockModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="restockForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="action" value="restock">
                    
                    <div class="form-group">
                        <label>Product *</label>
                        <select id="restockProductId" name="product_id" required>
                            <option value="">Select a product</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Current Stock</label>
                        <input type="number" id="currentStock" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Quantity to Add *</label>
                        <input type="number" id="restockQuantity" name="quantity" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Reason</label>
                        <select id="restockReason" name="reason">
                            <option value="Manual restock">Manual restock</option>
                            <option value="Supplier delivery">Supplier delivery</option>
                            <option value="Return from customer">Return from customer</option>
                            <option value="Inventory adjustment">Inventory adjustment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea id="restockNotes" name="notes" rows="3" placeholder="Optional notes about this restock"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRestockModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitRestock()">Restock Product</button>
            </div>
        </div>
    </div>

    <!-- Reduce Stock Modal -->
    <div id="reduceStockModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-minus"></i> Reduce Stock</h3>
                <span class="close" onclick="closeReduceStockModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="reduceStockForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="action" value="reduce_stock">
                    
                    <div class="form-group">
                        <label>Product *</label>
                        <select id="reduceProductId" name="product_id" required>
                            <option value="">Select a product</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Current Stock</label>
                        <input type="number" id="reduceCurrentStock" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Quantity to Reduce *</label>
                        <input type="number" id="reduceQuantity" name="quantity" min="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Reason *</label>
                        <select id="reduceReason" name="reason" required>
                            <option value="">Select a reason</option>
                            <option value="damaged">Damaged</option>
                            <option value="expired">Expired</option>
                            <option value="returned">Returned</option>
                            <option value="adjustment">Stock Adjustment</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea id="reduceNotes" name="notes" rows="3" placeholder="Optional notes about this reduction"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeReduceStockModal()">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitReduceStock()">Reduce Stock</button>
            </div>
        </div>
    </div>

    <!-- Bulk Restock Modal -->
    <div id="bulkRestockModal" class="modal">
        <div class="modal-content large">
            <div class="modal-header">
                <h3><i class="fas fa-layer-group"></i> Bulk Restock</h3>
                <span class="close" onclick="closeBulkRestockModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="bulk-restock-container">
                    <div class="form-group">
                        <label>Add Products to Bulk Restock</label>
                        <select id="bulkProductSelect" onchange="addBulkProduct()">
                            <option value="">Select a product to add</option>
                        </select>
                    </div>
                    
                    <div class="bulk-products-list" id="bulkProductsList">
                        <!-- Products will be added here -->
                    </div>
                    
                    <div class="form-group">
                        <label>Bulk Restock Reason</label>
                        <select id="bulkRestockReason" name="reason">
                            <option value="Bulk restock">Bulk restock</option>
                            <option value="Supplier delivery">Supplier delivery</option>
                            <option value="Inventory adjustment">Inventory adjustment</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="bulk-controls">
                        <button type="button" class="btn btn-secondary" onclick="clearBulkProducts()">Clear All</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeBulkRestockModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkRestock()">Bulk Restock</button>
            </div>
        </div>
    </div>

    <script>
        // Global CSRF token for AJAX requests
        const CSRF_TOKEN = '<?php echo $csrfToken; ?>';
    </script>
    <script src="script.js"></script>
</body>
</html> 