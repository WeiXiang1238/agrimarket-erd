<?php
session_start();
require_once __DIR__ . '/../../services/OrderManagementService.php';
require_once __DIR__ . '/../../services/NotificationService.php';

// Set page title for tracking
$pageTitle = 'Order Management - AgriMarket Solutions';

// Include page tracking
require_once __DIR__ . '/../../includes/page_tracking.php';

// Initialize order management service
$orderMgmtService = new OrderManagementService();
$notificationService = new NotificationService();

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

// Set currentUser for sidebar
$currentUser = $userRoles['user'];

// Get notifications for the current user
$userNotifications = [];
$unreadCount = 0;
if ($currentUser) {
    $userNotifications = $notificationService->getUserNotifications($currentUser['user_id'], 10);
    $unreadCount = 0;
    foreach ($userNotifications as $notif) {
        if (!$notif['is_read']) $unreadCount++;
    }
    
    // Add a test notification for order management (remove this in production)
    // if (empty($userNotifications)) {
    //     $notificationService->createNotification(
    //         $currentUser['user_id'],
    //         'Order Management Test',
    //         'Order management notifications are working correctly!',
    //         'order'
    //     );
    // }
}

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

// Debug user roles
error_log("User Roles Debug: " . print_r($userRoles, true));

// Debug: Check total orders in database
try {
    global $host, $user, $pass, $dbname;
    $debugDb = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $debugStmt = $debugDb->prepare("SELECT COUNT(*) as total FROM orders WHERE is_archive = 0");
    $debugStmt->execute();
    $totalOrdersInDb = $debugStmt->fetch()['total'];
    error_log("Total orders in database: " . $totalOrdersInDb);
} catch (Exception $e) {
    error_log("Debug query error: " . $e->getMessage());
}

// Get orders for current user
$ordersData = $orderMgmtService->getOrdersForUser($userRoles, $page, $status ?: null);
$orders = $ordersData['orders'] ?? [];
$totalPages = $ordersData['totalPages'] ?? 1;

// Debug orders data
error_log("Orders Data Debug: " . print_r($ordersData, true));
error_log("Orders Count: " . count($orders));

// Get status options for filter
$statusOptions = $orderMgmtService->getOrderStatusOptions();

// Get vendors for filter (admin only)
$vendors = [];
if ($userRoles['isAdmin']) {
    $vendors = $orderMgmtService->getVendorsForFilter();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - AgriMarket Solutions</title>
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
            $pageTitle = $userRoles['isCustomer'] ? 'My Orders' : 'Order Management';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2>
                                <i class="fas fa-shopping-cart"></i> 
                                <?php echo htmlspecialchars($pageTitle); ?>
                            </h2>
                            <p><?php echo $userRoles['isCustomer'] ? 'Track and manage your orders' : 'Manage customer orders and fulfillment'; ?></p>
                        </div>
                        <?php if ($userRoles['isCustomer']): ?>
                            <a href="/agrimarket-erd/v1/products/" class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i> Continue Shopping
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            <!-- Order Filters -->
            <div class="controls-section">
                <div class="controls-left">
                    <label for="statusFilter" class="form-label">Filter by Status:</label>
                    <select id="statusFilter" class="form-control" onchange="filterOrders()">
                        <?php foreach ($statusOptions as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>" 
                                    <?php echo $status === $value ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <?php if ($userRoles['isAdmin']): ?>
                        <label for="vendorFilter" class="form-label">Filter by Vendor:</label>
                        <select id="vendorFilter" class="form-control" onchange="filterOrders()">
                            <option value="">All Vendors</option>
                            <?php foreach ($vendors as $vendor): ?>
                                <option value="<?php echo htmlspecialchars($vendor['vendor_id']); ?>">
                                    <?php echo htmlspecialchars($vendor['business_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label for="dateRangeFilter" class="form-label">Date Range:</label>
                        <select id="dateRangeFilter" class="form-control" onchange="filterOrders()">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    <?php endif; ?>
                </div>
                
                <?php if ($userRoles['isAdmin']): ?>
                    <div class="controls-right">
                        <button class="btn btn-primary" onclick="exportOrders()">
                            <i class="fas fa-download"></i> Export Orders
                        </button>
                        <button class="btn btn-secondary" onclick="toggleBulkActions()">
                            <i class="fas fa-tasks"></i> Bulk Actions
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (empty($orders)): ?>
                <div class="content-card">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-clipboard-list text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-muted mb-3">No orders found</h3>
                        <p class="text-muted mb-4">
                            <?php if ($userRoles['isCustomer']): ?>
                                You haven't placed any orders yet. Start shopping to see your order history here.
                                <?php if ($userRoles['customerId'] === null): ?>
                                    <br><strong>Note:</strong> Customer profile not found. Please contact support.
                                <?php endif; ?>
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
                </div>
            <?php else: ?>
                <div class="orders-grid">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card" data-order-id="<?php echo $order['order_id']; ?>">
                            <?php if ($userRoles['isAdmin']): ?>
                                <div class="order-checkbox">
                                    <input type="checkbox" class="order-select" value="<?php echo $order['order_id']; ?>" onchange="updateSelectedCount()">
                                </div>
                            <?php endif; ?>
                            
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Order #<?php echo $order['order_id']; ?></h3>
                                    <p class="order-date"><?php echo date('M j, Y g:i A', strtotime($order['order_date'])); ?></p>
                                    <p class="vendor-name">from <?php echo htmlspecialchars($order['vendor_name']); ?></p>
                                    
                                    <?php if ($userRoles['isAdmin'] && isset($order['customer_name'])): ?>
                                        <p class="customer-name">Customer: <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                    <span class="payment-status payment-<?php echo strtolower($order['payment_status']); ?>">
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                    
                                    <?php if ($userRoles['isAdmin']): ?>
                                        <div class="admin-badges">
                                            <span class="order-id-badge">#<?php echo $order['order_id']; ?></span>
                                        </div>
                                    <?php endif; ?>
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
                                    <?php if (($order['shipping_fee'] ?? 0) > 0): ?>
                                        <div class="shipping-cost">
                                            <span class="label">Shipping:</span>
                                            <span class="amount">RM <?php echo number_format($order['shipping_fee'], 2); ?></span>
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
                                <button class="btn btn-secondary" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                
                                <button class="btn btn-secondary" onclick="trackOrder(<?php echo $order['order_id']; ?>)">
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
                                        <button class="btn btn-success" onclick="openReviewModal(<?php echo $order['order_id']; ?>, '<?php echo htmlspecialchars($order['vendor_name']); ?>')">
                                            <i class="fas fa-star"></i> Write Review
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($userRoles['isVendor'] || $userRoles['isAdmin']): ?>
                                    <button class="btn btn-primary" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>)">
                                        <i class="fas fa-edit"></i> Update Status
                                    </button>
                                <?php endif; ?>

                                <?php if ($userRoles['isAdmin']): ?>
                                    <button class="btn btn-info" onclick="viewCustomerProfile(<?php echo $order['customer_id'] ?? 0; ?>)" title="View Customer Profile">
                                        <i class="fas fa-user"></i>
                                    </button>
                                    
                                    <button class="btn btn-warning" onclick="viewVendorProfile(<?php echo $order['vendor_id'] ?? 0; ?>)" title="View Vendor Profile">
                                        <i class="fas fa-store"></i>
                                    </button>
                                    
                                    <button class="btn btn-secondary" onclick="viewOrderAnalytics(<?php echo $order['order_id']; ?>)" title="Order Analytics">
                                        <i class="fas fa-chart-bar"></i>
                                    </button>
                                    
                                    <?php if (in_array($order['status'], ['Pending', 'Confirmed'])): ?>
                                        <button class="btn btn-danger" onclick="adminCancelOrder(<?php echo $order['order_id']; ?>)" title="Admin Cancel">
                                            <i class="fas fa-ban"></i> Admin Cancel
                                        </button>
                                    <?php endif; ?>
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
                               class="btn <?php echo $page === $i ? 'btn-primary' : 'btn-light'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Order Details Modal -->
            <div id="orderDetailsModal" class="modal">
                <div class="modal-content large-modal">
                    <div class="modal-header">
                        <h3><i class="fas fa-clipboard-list"></i> Order Details</h3>
                        <button class="modal-close" onclick="closeOrderDetails()">&times;</button>
                    </div>
                    <div class="modal-body" id="orderDetailsContent">
                        <!-- Order details will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Order Tracking Modal -->
            <div id="trackingModal" class="modal">
                <div class="modal-content large-modal">
                    <div class="modal-header">
                        <h3><i class="fas fa-map-marker-alt"></i> Order Tracking</h3>
                        <button class="modal-close" onclick="closeTracking()">&times;</button>
                    </div>
                    <div class="modal-body" id="trackingContent">
                        <!-- Tracking content will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Update Status Modal (for vendors/admins) -->
            <?php if ($userRoles['isVendor'] || $userRoles['isAdmin']): ?>
                <div id="updateStatusModal" class="modal">
                    <div class="modal-content medium-modal">
                        <div class="modal-header">
                            <h3><i class="fas fa-edit"></i> Update Order Status</h3>
                            <button class="modal-close" onclick="closeUpdateStatus()">&times;</button>
                        </div>
                        <div class="modal-body">
                                <form id="updateStatusForm">
                                    <input type="hidden" id="updateOrderId" name="order_id">
                                    
                                    <div class="form-group">
                                        <label class="form-label">Status:</label>
                                        <select name="status" class="form-control" required>
                                            <option value="Pending">Pending</option>
                                            <option value="Confirmed">Confirmed</option>
                                            <option value="Processing">Processing</option>
                                            <option value="Shipped">Shipped</option>
                                            <option value="Delivered">Delivered</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Tracking Number (optional):</label>
                                        <input type="text" name="tracking_number" class="form-control" placeholder="Enter tracking number">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Delivery Date (optional):</label>
                                        <input type="date" name="delivery_date" class="form-control">
                                    </div>

                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">Update Order</button>
                                        <button type="button" class="btn btn-secondary" onclick="closeUpdateStatus()">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($userRoles['isAdmin']): ?>
                <!-- Admin Bulk Actions Panel -->
                <div id="bulkActionsPanel" class="bulk-actions-panel" style="display: none;">
                    <div class="bulk-actions-header">
                        <label>
                            <input type="checkbox" id="selectAllOrders"> Select All
                        </label>
                        <span id="selectedCount">0 orders selected</span>
                    </div>
                    <div class="bulk-actions-buttons">
                        <button class="btn btn-primary" onclick="bulkUpdateStatus()">
                            <i class="fas fa-edit"></i> Update Status
                        </button>
                        <button class="btn btn-warning" onclick="bulkExport()">
                            <i class="fas fa-file-export"></i> Export Selected
                        </button>
                        <button class="btn btn-info" onclick="bulkGenerateReport()">
                            <i class="fas fa-chart-line"></i> Generate Report
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Admin-specific modals -->
            <?php if ($userRoles['isAdmin']): ?>
                <!-- Bulk Status Update Modal -->
                <div id="bulkStatusModal" class="modal">
                    <div class="modal-content medium-modal">
                        <div class="modal-header">
                            <h3><i class="fas fa-tasks"></i> Bulk Update Status</h3>
                            <button class="modal-close" onclick="closeBulkStatusModal()">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="bulkStatusForm">
                                <div class="form-group">
                                    <label class="form-label">New Status:</label>
                                    <select name="bulk_status" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Confirmed">Confirmed</option>
                                        <option value="Processing">Processing</option>
                                        <option value="Shipped">Shipped</option>
                                        <option value="Delivered">Delivered</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Update Note (optional):</label>
                                    <textarea name="bulk_notes" class="form-control" rows="3" placeholder="Add a note for this bulk update..."></textarea>
                                </div>
                                
                                <div id="selectedOrdersList" class="selected-orders-list">
                                    <!-- Selected orders will be listed here -->
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Update Selected Orders</button>
                                    <button type="button" class="btn btn-secondary" onclick="closeBulkStatusModal()">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order Analytics Modal -->
                <div id="orderAnalyticsModal" class="modal">
                    <div class="modal-content large-modal">
                        <div class="modal-header">
                            <h3><i class="fas fa-chart-bar"></i> Order Analytics</h3>
                            <button class="modal-close" onclick="closeOrderAnalyticsModal()">&times;</button>
                        </div>
                        <div class="modal-body" id="orderAnalyticsContent">
                            <!-- Analytics content will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Admin Cancel Order Modal -->
                <div id="adminCancelModal" class="modal">
                    <div class="modal-content medium-modal">
                        <div class="modal-header">
                            <h3><i class="fas fa-ban"></i> Admin Cancel Order</h3>
                            <button class="modal-close" onclick="closeAdminCancelModal()">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="adminCancelForm">
                                <input type="hidden" id="adminCancelOrderId" name="order_id">
                                
                                <div class="form-group">
                                    <label class="form-label">Cancellation Reason:</label>
                                    <select name="cancel_reason" class="form-control" required>
                                        <option value="">Select Reason</option>
                                        <option value="admin_policy_violation">Policy Violation</option>
                                        <option value="admin_fraud_detected">Fraud Detected</option>
                                        <option value="admin_vendor_issue">Vendor Issue</option>
                                        <option value="admin_system_error">System Error</option>
                                        <option value="admin_customer_request">Customer Service Request</option>
                                        <option value="admin_other">Other Administrative Reason</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Internal Notes:</label>
                                    <textarea name="admin_notes" class="form-control" rows="4" placeholder="Add internal notes for this cancellation..." required></textarea>
                                </div>

                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="notify_customer" checked> 
                                        Send notification to customer
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="notify_vendor" checked> 
                                        Send notification to vendor
                                    </label>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-danger">Cancel Order</button>
                                    <button type="button" class="btn btn-secondary" onclick="closeAdminCancelModal()">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Customer Review Modal -->
            <?php if ($userRoles['isCustomer']): ?>
            <div id="reviewModal" class="modal">
                <div class="modal-content large-modal">
                    <div class="modal-header">
                        <h3><i class="fas fa-star"></i> Write Your Review</h3>
                        <button class="modal-close" onclick="closeReviewModal()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="order-review-info">
                            <h4>Order: <span id="orderDisplayText"></span></h4>
                            <p>Vendor: <span id="vendorNameDisplay"></span></p>
                        </div>
                        
                        <!-- Review Type Tabs -->
                        <div class="review-tabs">
                            <button type="button" class="review-tab-btn active" data-tab="vendor" onclick="switchReviewTab('vendor')">
                                <i class="fas fa-store"></i> Review Vendor
                            </button>
                            <button type="button" class="review-tab-btn" data-tab="product" onclick="switchReviewTab('product')">
                                <i class="fas fa-box"></i> Review Products
                            </button>
                        </div>
                        
                        <!-- Vendor Review Tab -->
                        <div id="vendorReviewTab" class="review-tab-content active">
                            <form id="vendorReviewForm">
                                <input type="hidden" id="reviewOrderId" name="order_id">
                                <input type="hidden" id="reviewVendorId" name="vendor_id">
                                
                                <div class="form-group">
                                    <label>Vendor Rating *</label>
                                    <div class="rating-input">
                                        <div class="stars vendor-stars" data-rating="0">
                                            <i class="rating-star far fa-star" data-rating="1"></i>
                                            <i class="rating-star far fa-star" data-rating="2"></i>
                                            <i class="rating-star far fa-star" data-rating="3"></i>
                                            <i class="rating-star far fa-star" data-rating="4"></i>
                                            <i class="rating-star far fa-star" data-rating="5"></i>
                                        </div>
                                        <span class="rating-text">Rate vendor service</span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Review Title (optional)</label>
                                    <input type="text" id="vendorReviewTitle" placeholder="Summarize your vendor experience">
                                </div>
                                
                                <div class="form-group">
                                    <label>Your Review *</label>
                                    <textarea id="vendorReviewComment" rows="4" placeholder="Share your experience with this vendor - delivery, communication, service quality..."></textarea>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>What did they do well? (optional)</label>
                                        <textarea id="vendorReviewPros" rows="2" placeholder="Strengths..."></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Areas for improvement? (optional)</label>
                                        <textarea id="vendorReviewCons" rows="2" placeholder="Suggestions..."></textarea>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="button" class="btn btn-secondary" onclick="closeReviewModal()">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="submitVendorReview()">
                                        <i class="fas fa-paper-plane"></i> Submit Vendor Review
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Product Reviews Tab -->
                        <div id="productReviewTab" class="review-tab-content">
                            <div class="products-to-review">
                                <div id="productsList">
                                    <!-- Products will be loaded here -->
                                    <div class="loading">
                                        <i class="fas fa-spinner fa-spin"></i> Loading products...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
    
    <?php if ($userRoles['isCustomer']): ?>
    <script>
    // Customer review functionality
    let currentReviewOrderId = null;
    let currentReviewVendorId = null;
    let orderProducts = [];
    let activeReviewTab = 'vendor';

    function openReviewModal(orderId, vendorName) {
        currentReviewOrderId = orderId;
        document.getElementById('reviewOrderId').value = orderId;
        document.getElementById('vendorNameDisplay').textContent = vendorName;
        document.getElementById('orderDisplayText').textContent = `#${orderId}`;
        
        // Show modal first
        document.getElementById('reviewModal').style.display = 'block';
        
        // Wait for DOM to be ready, then initialize tabs and load data
        setTimeout(() => {
            // Reset forms and tabs
            resetReviewForms();
            switchReviewTab('vendor');
            
            // Get order details including vendor ID and products
            getOrderDetailsForReview(orderId);
        }, 100);
    }

    async function getOrderDetailsForReview(orderId) {
        try {
            const formData = new FormData();
            formData.append('action', 'get_order_details');
            formData.append('order_id', orderId);
            
            const response = await fetch('index.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            console.log('Order details response:', data);
            
            if (data.success && data.order) {
                currentReviewVendorId = data.order.vendor_id;
                document.getElementById('reviewVendorId').value = data.order.vendor_id;
                orderProducts = data.order.items || [];
                
                console.log('Order products loaded:', orderProducts);
                
                // Load products for review
                loadProductsForReview();
            } else {
                console.error('Failed to load order details:', data.message || 'Unknown error');
                orderProducts = [];
                loadProductsForReview();
            }
        } catch (error) {
            console.error('Error getting order details:', error);
            orderProducts = [];
            loadProductsForReview();
        }
    }

    function loadProductsForReview() {
        const productsList = document.getElementById('productsList');
        
        console.log('Loading products for review:', orderProducts);
        console.log('Number of products:', orderProducts.length);
        if (orderProducts.length > 0) {
            console.log('First product structure:', orderProducts[0]);
        }
        
        if (!productsList) {
            console.error('Products list element not found');
            return;
        }
        
        if (orderProducts.length === 0) {
            productsList.innerHTML = '<div class="no-products" style="text-align: center; padding: 2rem; color: #6b7280;"><i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i><h4>No products found</h4><p>This order doesn\'t contain any products to review.</p></div>';
            return;
        }
        
        productsList.innerHTML = orderProducts.map(product => `
            <div class="product-review-item" data-product-id="${product.product_id}">
                <div class="product-header">
                    <div class="product-info">
                        <h5>${escapeHtml(product.product_name || product.name || 'Unknown Product')}</h5>
                        <p>Quantity: ${product.quantity || 'N/A'} | Price: RM ${parseFloat(product.price_at_purchase || 0).toFixed(2)}</p>
                    </div>
                    <div class="product-actions">
                        <button type="button" class="btn btn-primary btn-sm" onclick="startProductReview(${product.product_id})">
                            <i class="fas fa-star"></i> Review Product
                        </button>
                    </div>
                </div>
                <div class="product-review-form" id="productForm_${product.product_id}">
                    <div class="form-group">
                        <label>Product Rating *</label>
                        <div class="rating-input">
                            <div class="stars product-stars" data-rating="0" data-product-id="${product.product_id}">
                                <i class="rating-star far fa-star" data-rating="1"></i>
                                <i class="rating-star far fa-star" data-rating="2"></i>
                                <i class="rating-star far fa-star" data-rating="3"></i>
                                <i class="rating-star far fa-star" data-rating="4"></i>
                                <i class="rating-star far fa-star" data-rating="5"></i>
                            </div>
                            <span class="rating-text">Rate this product</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Review Title (optional)</label>
                        <input type="text" id="productTitle_${product.product_id}" placeholder="Summarize your product experience">
                    </div>
                    
                    <div class="form-group">
                        <label>Product Review *</label>
                        <textarea id="productComment_${product.product_id}" rows="3" placeholder="How was the product quality, freshness, packaging, etc?"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>What you liked (optional)</label>
                            <textarea id="productPros_${product.product_id}" rows="2" placeholder="Positives..."></textarea>
                        </div>
                        <div class="form-group">
                            <label>Areas for improvement (optional)</label>
                            <textarea id="productCons_${product.product_id}" rows="2" placeholder="Suggestions..."></textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="cancelProductReview(${product.product_id})">Cancel</button>
                        <button type="button" class="btn btn-success btn-sm" onclick="submitProductReview(${product.product_id})">
                            <i class="fas fa-paper-plane"></i> Submit Product Review
                        </button>
                    </div>
                </div>
                <div class="product-review-submitted" id="productSubmitted_${product.product_id}" style="display: none;">
                    <i class="fas fa-check-circle"></i>
                    <h6>Review Submitted!</h6>
                    <p>Your product review has been submitted and is pending approval.</p>
                </div>
            </div>
        `).join('');
    }

    function switchReviewTab(tab) {
        console.log('Switching to tab:', tab);
        activeReviewTab = tab;
        
        // Update tab buttons with error checking
        const tabButtons = document.querySelectorAll('.review-tab-btn');
        console.log('Found tab buttons:', tabButtons.length);
        if (tabButtons.length > 0) {
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
            });
            
            const activeTabBtn = document.querySelector(`[data-tab="${tab}"]`);
            console.log('Active tab button found:', activeTabBtn);
            if (activeTabBtn) {
                activeTabBtn.classList.add('active');
            }
        }
        
        // Update tab content with error checking
        const tabContents = document.querySelectorAll('.review-tab-content');
        console.log('Found tab contents:', tabContents.length);
        if (tabContents.length > 0) {
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            const activeTabContent = document.getElementById(`${tab}ReviewTab`);
            console.log('Active tab content found:', activeTabContent);
            if (activeTabContent) {
                activeTabContent.classList.add('active');
                console.log('Added active class to:', `${tab}ReviewTab`);
            }
        }
        
        // Load products when switching to products tab
        if (tab === 'product') {
            if (orderProducts.length > 0) {
                loadProductsForReview();
            } else {
                // Show loading or empty state
                const productsList = document.getElementById('productsList');
                if (productsList) {
                    productsList.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading products...</div>';
                }
            }
        }
    }

    function startProductReview(productId) {
        const form = document.getElementById(`productForm_${productId}`);
        form.classList.add('expanded');
        
        // Initialize star rating for this product
        initializeProductStarRating(productId);
        
        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    function initializeProductStarRating(productId) {
        const stars = document.querySelector(`.product-stars[data-product-id="${productId}"]`);
        if (stars) {
            // Ensure the data-rating is set to 0 initially
            stars.dataset.rating = '0';
            console.log(`Initialized rating for product ${productId}:`, stars.dataset.rating);
        }
    }

    function cancelProductReview(productId) {
        const form = document.getElementById(`productForm_${productId}`);
        form.classList.remove('expanded');
        resetProductForm(productId);
    }

    function resetProductForm(productId) {
        // Reset rating
        const stars = document.querySelector(`.product-stars[data-product-id="${productId}"]`);
        if (stars) {
            stars.querySelectorAll('.rating-star').forEach(star => {
                star.classList.remove('fas');
                star.classList.add('far');
                star.style.color = '#d1d5db';
            });
            stars.dataset.rating = '0';
            stars.parentElement.querySelector('.rating-text').textContent = 'Rate this product';
        }
        
        // Reset form fields
        document.getElementById(`productTitle_${productId}`).value = '';
        document.getElementById(`productComment_${productId}`).value = '';
        document.getElementById(`productPros_${productId}`).value = '';
        document.getElementById(`productCons_${productId}`).value = '';
    }

    async function submitProductReview(productId) {
        const stars = document.querySelector(`.product-stars[data-product-id="${productId}"]`);
        const rating = parseInt(stars ? stars.dataset.rating : '0');
        const title = document.getElementById(`productTitle_${productId}`).value.trim();
        const comment = document.getElementById(`productComment_${productId}`).value.trim();
        const pros = document.getElementById(`productPros_${productId}`).value.trim();
        const cons = document.getElementById(`productCons_${productId}`).value.trim();
        
        console.log('Product review submission data:');
        console.log('Product ID:', productId);
        console.log('Order ID:', currentReviewOrderId);
        console.log('Stars element found:', stars);
        console.log('Stars dataset.rating:', stars ? stars.dataset.rating : 'null');
        console.log('Rating parsed:', rating);
        console.log('Title:', title);
        console.log('Comment:', comment);
        
        if (rating === 0) {
            alert('Please select a rating for this product');
            return;
        }
        
        if (!comment) {
            alert('Please write a review comment');
            return;
        }
        
        if (!currentReviewOrderId) {
            alert('Error: Order ID not found. Please try reopening the review modal.');
            return;
        }
        
        if (!productId) {
            alert('Error: Product ID not found');
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'submit_product_review');
            formData.append('product_id', productId);
            formData.append('order_id', currentReviewOrderId);
            formData.append('rating', rating);
            formData.append('title', title);
            formData.append('comment', comment);
            formData.append('pros', pros);
            formData.append('cons', cons);
            
            console.log('FormData contents:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            const response = await fetch('../shop/review.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Hide form and show success message
                document.getElementById(`productForm_${productId}`).style.display = 'none';
                document.getElementById(`productSubmitted_${productId}`).style.display = 'block';
                
                // Mark item as reviewed
                const item = document.querySelector(`[data-product-id="${productId}"]`);
                item.classList.add('reviewed');
                
                alert('Product review submitted successfully! It will be visible after approval.');
            } else {
                alert(data.message || 'Failed to submit product review');
            }
        } catch (error) {
            console.error('Error submitting product review:', error);
            alert('Error submitting product review');
        }
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').style.display = 'none';
        resetReviewForms();
    }

    function resetReviewForms() {
        // Reset vendor form
        resetVendorForm();
        
        // Reset product forms
        orderProducts.forEach(product => {
            resetProductForm(product.product_id);
        });
        
        // Reset tabs
        switchReviewTab('vendor');
    }

    function resetVendorForm() {
        // Reset vendor rating
        const vendorStars = document.querySelector('.vendor-stars');
        if (vendorStars) {
            vendorStars.querySelectorAll('.rating-star').forEach(star => {
                star.classList.remove('fas');
                star.classList.add('far');
                star.style.color = '#d1d5db';
            });
            vendorStars.dataset.rating = '0';
            vendorStars.parentElement.querySelector('.rating-text').textContent = 'Rate vendor service';
        }
        
        // Reset vendor form fields
        document.getElementById('vendorReviewTitle').value = '';
        document.getElementById('vendorReviewComment').value = '';
        document.getElementById('vendorReviewPros').value = '';
        document.getElementById('vendorReviewCons').value = '';
    }

    async function submitVendorReview() {
        const rating = parseInt(document.querySelector('.vendor-stars').dataset.rating);
        const title = document.getElementById('vendorReviewTitle').value.trim();
        const comment = document.getElementById('vendorReviewComment').value.trim();
        const pros = document.getElementById('vendorReviewPros').value.trim();
        const cons = document.getElementById('vendorReviewCons').value.trim();
        
        if (rating === 0) {
            alert('Please select a vendor rating');
            return;
        }
        
        if (!comment) {
            alert('Please write a vendor review comment');
            return;
        }
        
        if (!currentReviewVendorId) {
            alert('Error: Vendor ID not found');
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('action', 'submit_vendor_review');
            formData.append('vendor_id', currentReviewVendorId);
            formData.append('order_id', currentReviewOrderId);
            formData.append('rating', rating);
            formData.append('title', title);
            formData.append('comment', comment);
            formData.append('pros', pros);
            formData.append('cons', cons);
            
            const response = await fetch('../shop/review.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                alert('Vendor review submitted successfully! It will be visible after approval.');
                closeReviewModal();
            } else {
                alert(data.message || 'Failed to submit vendor review');
            }
        } catch (error) {
            console.error('Error submitting vendor review:', error);
            alert('Error submitting vendor review');
        }
    }

    // Helper function for HTML escaping
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Star rating functionality
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('rating-star')) {
            const rating = parseInt(e.target.dataset.rating);
            const container = e.target.closest('.stars');
            const stars = container.querySelectorAll('.rating-star');
            
            console.log('Star clicked - Rating:', rating, 'Container:', container);
            
            // Update visual stars
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
            
            // Update dataset and text
            container.dataset.rating = rating;
            console.log('Updated container dataset.rating to:', container.dataset.rating);
            const ratingText = container.parentElement.querySelector('.rating-text');
            if (ratingText) {
                const isVendor = container.classList.contains('vendor-stars');
                const isProduct = container.classList.contains('product-stars');
                
                if (isVendor) {
                    ratingText.textContent = `${rating} star${rating !== 1 ? 's' : ''} - vendor service`;
                } else if (isProduct) {
                    ratingText.textContent = `${rating} star${rating !== 1 ? 's' : ''} - product quality`;
                } else {
                    ratingText.textContent = `${rating} star${rating !== 1 ? 's' : ''}`;
                }
            }
        }
    });

    // Star hover effects
    document.addEventListener('mouseover', function(e) {
        if (e.target.classList.contains('rating-star')) {
            const rating = parseInt(e.target.dataset.rating);
            const container = e.target.closest('.stars');
            const stars = container.querySelectorAll('.rating-star');
            
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.style.color = '#f59e0b';
                } else {
                    star.style.color = '#d1d5db';
                }
            });
        }
    });

    document.addEventListener('mouseout', function(e) {
        if (e.target.classList.contains('rating-star')) {
            const container = e.target.closest('.stars');
            const currentRating = parseInt(container.dataset.rating);
            const stars = container.querySelectorAll('.rating-star');
            
            stars.forEach((star, index) => {
                if (index < currentRating) {
                    star.style.color = '#f59e0b';
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.style.color = '#d1d5db';
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
        }
    });
    </script>
    <?php endif; ?>
    <script src="/agrimarket-erd/v1/components/page_tracking.js"></script>
</body>
</html> 