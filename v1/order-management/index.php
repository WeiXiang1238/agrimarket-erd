<?php
session_start();
require_once __DIR__ . '/../../services/OrderManagementService.php';

// Initialize order management service
$orderMgmtService = new OrderManagementService();

// Initialize page data
$initResult = $orderMgmtService->initializeOrderManagement();

if (!$initResult['success']) {
    if (isset($initResult['redirect'])) {
        header('Location: ' . $initResult['redirect']);
        exit;
    }
    die($initResult['message']);
}

$userRoles = $initResult['userRoles'];
$pageTitle = $initResult['pageTitle'];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $result = $orderMgmtService->handleOrderAction($action, $_POST, $userRoles);
    
    echo json_encode($result);
    exit;
}

// Get pagination parameters
$params = $orderMgmtService->validatePaginationParams($_GET['page'] ?? 1, $_GET['status'] ?? '');
$page = $params['page'];
$status = $params['status'];

// Get orders for current user
$ordersData = $orderMgmtService->getOrdersForUser($userRoles, $page, $status ?: null);
$orders = $ordersData['orders'] ?? [];
$totalPages = $ordersData['totalPages'] ?? 1;

// Get status options for filter
$statusOptions = $orderMgmtService->getOrderStatusOptions();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - AgriMarket</title>
    <link rel="stylesheet" href="../components/main.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include '../components/sidebar.php'; ?>
        
        <main class="main-content">
            <?php 
            $pageTitle = $userRoles['isCustomer'] ? 'My Orders' : 'Order Management';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <div class="page-header">
                    <h1>
                        <i class="fas fa-shopping-cart"></i> 
                        <?php echo htmlspecialchars($pageTitle); ?>
                    </h1>
                    <div class="page-actions">
                        <?php if ($userRoles['isCustomer']): ?>
                            <a href="/agrimarket-erd/v1/shopping-cart/" class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i> Continue Shopping
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            <!-- Order Filters -->
            <div class="filters-section">
                <div class="filter-group">
                    <label>Status:</label>
                    <select id="statusFilter" onchange="filterOrders()">
                        <?php foreach ($statusOptions as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>" 
                                    <?php echo $status === $value ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if (empty($orders)): ?>
                <div class="empty-orders">
                    <div class="empty-orders-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>No orders found</h3>
                    <p>
                        <?php if ($userRoles['isCustomer']): ?>
                            You haven't placed any orders yet. Start shopping to see your order history here.
                        <?php else: ?>
                            No orders match the current filters.
                        <?php endif; ?>
                    </p>
                    <?php if ($userRoles['isCustomer']): ?>
                        <a href="/agrimarket-erd/v1/products/" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Browse Products
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="orders-grid">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card" data-order-id="<?php echo $order['order_id']; ?>">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Order #<?php echo $order['order_id']; ?></h3>
                                    <p class="order-date"><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                    <p class="vendor-name">from <?php echo htmlspecialchars($order['vendor_name']); ?></p>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                    <span class="payment-status payment-<?php echo strtolower($order['payment_status']); ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="order-details">
                                <div class="order-items">
                                    <p><strong><?php echo $order['item_count']; ?> item(s):</strong></p>
                                    <p class="items-summary"><?php echo htmlspecialchars($order['items_summary']); ?></p>
                                </div>
                                
                                <div class="order-totals">
                                    <div class="total-amount">
                                        <span class="label">Total:</span>
                                        <span class="amount">RM <?php echo number_format($order['final_amount'], 2); ?></span>
                                    </div>
                                    <?php if ($order['shipping_cost'] > 0): ?>
                                        <div class="shipping-cost">
                                            <span class="label">Shipping:</span>
                                            <span class="amount">RM <?php echo number_format($order['shipping_cost'], 2); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($order['tracking_number']): ?>
                                <div class="tracking-info">
                                    <i class="fas fa-truck"></i>
                                    <span>Tracking: <?php echo htmlspecialchars($order['tracking_number']); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="order-actions">
                                <button class="btn btn-outline" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                
                                <button class="btn btn-outline" onclick="trackOrder(<?php echo $order['order_id']; ?>)">
                                    <i class="fas fa-map-marker-alt"></i> Track Order
                                </button>

                                <?php if ($userRoles['isCustomer']): ?>
                                    <?php if (in_array($order['status'], ['Pending', 'Confirmed'])): ?>
                                        <button class="btn btn-danger" onclick="cancelOrder(<?php echo $order['order_id']; ?>)">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($order['status'] === 'Delivered'): ?>
                                        <button class="btn btn-primary" onclick="reorderItems(<?php echo $order['order_id']; ?>)">
                                            <i class="fas fa-redo"></i> Reorder
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($userRoles['isVendor'] || $userRoles['isAdmin']): ?>
                                    <button class="btn btn-primary" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>)">
                                        <i class="fas fa-edit"></i> Update Status
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status); ?>" 
                               class="page-link <?php echo $page === $i ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Order Details Modal -->
            <div id="orderDetailsModal" class="modal">
                <div class="modal-content modal-large">
                    <div class="modal-header">
                        <h2><i class="fas fa-clipboard-list"></i> Order Details</h2>
                        <button class="close-modal" onclick="closeOrderDetails()">&times;</button>
                    </div>
                    <div class="modal-body" id="orderDetailsContent">
                        <!-- Order details will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Order Tracking Modal -->
            <div id="trackingModal" class="modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2><i class="fas fa-map-marker-alt"></i> Order Tracking</h2>
                        <button class="close-modal" onclick="closeTracking()">&times;</button>
                    </div>
                    <div class="modal-body" id="trackingContent">
                        <!-- Tracking content will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Update Status Modal (for vendors/admins) -->
            <?php if ($userRoles['isVendor'] || $userRoles['isAdmin']): ?>
                <div id="updateStatusModal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2><i class="fas fa-edit"></i> Update Order Status</h2>
                            <button class="close-modal" onclick="closeUpdateStatus()">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="updateStatusForm">
                                <input type="hidden" id="updateOrderId" name="order_id">
                                
                                <div class="form-group">
                                    <label>Status:</label>
                                    <select name="status" required>
                                        <option value="Pending">Pending</option>
                                        <option value="Confirmed">Confirmed</option>
                                        <option value="Processing">Processing</option>
                                        <option value="Shipped">Shipped</option>
                                        <option value="Delivered">Delivered</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tracking Number (optional):</label>
                                    <input type="text" name="tracking_number" placeholder="Enter tracking number">
                                </div>

                                <div class="form-group">
                                    <label>Delivery Date (optional):</label>
                                    <input type="date" name="delivery_date">
                                </div>

                                <div class="form-group">
                                    <label>Notes (optional):</label>
                                    <textarea name="notes" rows="3" placeholder="Add any notes..."></textarea>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Update Order</button>
                                    <button type="button" class="btn btn-secondary" onclick="closeUpdateStatus()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script src="script.js"></script>
</body>
</html> 