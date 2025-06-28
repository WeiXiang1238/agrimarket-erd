<?php
session_start();
require_once '../../Db_Connect.php';
require_once '../../services/AuthService.php';
require_once '../../services/ReviewService.php';
require_once '../../models/ModelLoader.php';

$authService = new AuthService();
$reviewService = new ReviewService();

// Check authentication and permissions
$authService->requireAuth();
$currentUser = $authService->getCurrentUser();

// Check if user has permission to manage reviews
if (!$authService->hasPermission('manage_reviews') && !$authService->hasRole('admin') && !$authService->hasRole('staff')) {
    header('Location: ../dashboard/');
    exit;
}

// Get user roles for conditional features
$userRoles = [
    'isAdmin' => $authService->hasRole('admin'),
    'isStaff' => $authService->hasRole('staff'),
    'isVendor' => $authService->hasRole('vendor')
];

$userId = $currentUser['user_id'];
$userRole = $authService->hasRole('admin') ? 'admin' : ($authService->hasRole('staff') ? 'staff' : 'vendor');

// Handle AJAX requests
if (isset($_GET['action']) || isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_GET['action'] ?? $_POST['action'];
    
    switch ($action) {
        case 'get_reviews':
            $filters = [
                'type' => $_GET['type'] ?? 'product',
                'status' => $_GET['status'] ?? '',
                'search' => $_GET['search'] ?? '',
                'rating' => $_GET['rating'] ?? null,
                'vendor_id' => $_GET['vendor_id'] ?? null,
                'customer_id' => $_GET['customer_id'] ?? null
            ];
            
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 10);
            
            // Filter by vendor for vendor users
            if ($userRole === 'vendor') {
                $vendorId = $reviewService->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $filters['vendor_id'] = $vendorId;
                }
            }
            
            $result = $reviewService->getReviews($filters, $page, $limit);
            echo json_encode($result);
            exit;
            
        case 'get_review_details':
            $reviewId = intval($_GET['review_id'] ?? 0);
            $type = $_GET['type'] ?? 'product';
            
            $result = $reviewService->getReviewDetails($reviewId, $type);
            echo json_encode($result);
            exit;
            
        case 'moderate_review':
            if (!($userRoles['isAdmin'] ?? false) && !($userRoles['isStaff'] ?? false)) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            
            $reviewId = intval($_POST['review_id'] ?? 0);
            $type = $_POST['type'] ?? 'product';
            $moderateAction = $_POST['moderate_action'] ?? 'approve';
            
            $result = $reviewService->moderateReview($reviewId, $type, $moderateAction, $userId);
            echo json_encode($result);
            exit;
            
        case 'bulk_moderate':
            if (!($userRoles['isAdmin'] ?? false) && !($userRoles['isStaff'] ?? false)) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            
            $reviewIds = $_POST['review_ids'] ?? [];
            $type = $_POST['type'] ?? 'product';
            $moderateAction = $_POST['moderate_action'] ?? 'approve';
            
            $result = $reviewService->bulkModerateReviews($reviewIds, $type, $moderateAction, $userId);
            echo json_encode($result);
            exit;
            
        case 'delete_review':
            if (!($userRoles['isAdmin'] ?? false)) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            
            $reviewId = intval($_POST['review_id'] ?? 0);
            $type = $_POST['type'] ?? 'product';
            
            $result = $reviewService->deleteReview($reviewId, $type);
            echo json_encode($result);
            exit;
            
        case 'get_stats':
            $result = $reviewService->getReviewStats($userRole, $userId);
            echo json_encode(['success' => true, 'stats' => $result]);
            exit;
            
        case 'get_rating_distribution':
            $entityId = intval($_GET['entity_id'] ?? 0);
            $type = $_GET['type'] ?? 'product';
            
            $result = $reviewService->getRatingDistribution($entityId, $type);
            echo json_encode($result);
            exit;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            exit;
    }
}

// Get initial statistics
$stats = $reviewService->getReviewStats($userRole, $userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Management - AgriMarket</title>
    <link rel="stylesheet" href="../components/main.css">
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Include Shared Sidebar -->
        <?php include '../components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Shared Header -->
            <?php 
            $pageTitle = 'Review Management';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2>Review Management</h2>
                            <p>Manage customer reviews and ratings for products and vendors</p>
                        </div>
                        <?php if (($userRoles['isAdmin'] ?? false) || ($userRoles['isStaff'] ?? false)): ?>
                        <div class="header-actions">
                            <button class="btn btn-warning" onclick="showPendingReviews()">
                                <i class="fas fa-clock"></i>
                                Pending Reviews
                            </button>
                            <button class="btn btn-primary" onclick="openBulkModerationModal()">
                                <i class="fas fa-tasks"></i>
                                Bulk Actions
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon reviews bg-primary">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="totalReviews"><?php echo $stats['total_reviews']; ?></h3>
                            <p>Total Reviews</p>
                            <span class="stat-change neutral">All time</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon pending bg-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="pendingReviews"><?php echo $stats['pending_reviews']; ?></h3>
                            <p>Pending Approval</p>
                            <span class="stat-change <?php echo $stats['pending_reviews'] > 0 ? 'negative' : 'neutral'; ?>">
                                Requires attention
                            </span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon approved bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="approvedReviews"><?php echo $stats['approved_reviews']; ?></h3>
                            <p>Approved Reviews</p>
                            <span class="stat-change positive">Published</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon rating bg-info">
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="averageRating"><?php echo number_format($stats['average_rating'], 1); ?></h3>
                            <p>Average Rating</p>
                            <span class="stat-change neutral">Out of 5.0</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon verified bg-secondary">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="verifiedReviews"><?php echo $stats['verified_reviews']; ?></h3>
                            <p>Verified Reviews</p>
                            <span class="stat-change positive">Authentic purchases</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon recent bg-purple">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="recentReviews"><?php echo $stats['reviews_this_month']; ?></h3>
                            <p>This Month</p>
                            <span class="stat-change neutral">New reviews</span>
                        </div>
                    </div>
                </div>

                <!-- Review Type Tabs -->
                <div class="review-tabs">
                    <button class="tab-btn active" data-type="product" onclick="switchReviewType('product')">
                        <i class="fas fa-box"></i>
                        Product Reviews
                    </button>
                    <button class="tab-btn" data-type="vendor" onclick="switchReviewType('vendor')">
                        <i class="fas fa-store"></i>
                        Vendor Reviews
                    </button>
                </div>

                <!-- Controls Section -->
                <div class="controls-section">
                    <div class="controls-left">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search reviews...">
                        </div>
                        <select id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <select id="ratingFilter">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    <div class="controls-right">
                        <div class="table-controls">
                            <span>Show</span>
                            <select id="limitSelect">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span>entries</span>
                        </div>
                    </div>
                </div>

                <!-- Reviews Content Card -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 id="reviewsTitle">Product Reviews</h3>
                        <?php if (($userRoles['isAdmin'] ?? false) || ($userRoles['isStaff'] ?? false)): ?>
                        <div class="bulk-actions" id="bulkActions" style="display: none;">
                            <label class="bulk-select-all">
                                <input type="checkbox" id="selectAllReviews"> Select All
                            </label>
                            <span id="selectedCount">0 reviews selected</span>
                            <div class="bulk-actions-buttons">
                                <button class="btn btn-success btn-sm" onclick="bulkModerate('approve')">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="bulkModerate('reject')">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                                <?php if ($userRoles['isAdmin'] ?? false): ?>
                                <button class="btn btn-danger btn-sm" onclick="bulkDelete()">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-content">
                        <div class="table-container">
                            <div id="reviewsLoading" class="loading-indicator">
                                <i class="fas fa-spinner fa-spin"></i>
                                Loading reviews...
                            </div>
                            <table class="management-table" id="reviewsTable" style="display: none;">
                                <thead>
                                    <tr>
                                        <?php if (($userRoles['isAdmin'] ?? false) || ($userRoles['isStaff'] ?? false)): ?>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <?php endif; ?>
                                        <th>Customer</th>
                                        <th>Product/Vendor</th>
                                        <th>Rating</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Verified</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="reviewsTableBody">
                                    <!-- Reviews will be loaded here -->
                                </tbody>
                            </table>
                            <div id="noReviews" class="no-data" style="display: none;">
                                <i class="fas fa-star"></i>
                                <p>No reviews found</p>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="pagination" id="reviewsPagination">
                            <!-- Pagination will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Review Details Modal -->
    <div id="reviewDetailsModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <h3><i class="fas fa-star"></i> Review Details</h3>
                <button class="modal-close" onclick="closeReviewDetails()">&times;</button>
            </div>
            <div class="modal-body" id="reviewDetailsContent">
                <!-- Review details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Bulk Moderation Modal -->
    <?php if (($userRoles['isAdmin'] ?? false) || ($userRoles['isStaff'] ?? false)): ?>
    <div id="bulkModerationModal" class="modal">
        <div class="modal-content medium-modal">
            <div class="modal-header">
                <h3><i class="fas fa-tasks"></i> Bulk Review Moderation</h3>
                <button class="modal-close" onclick="closeBulkModerationModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="bulkModerationForm">
                    <div class="form-group">
                        <label class="form-label">Action:</label>
                        <select name="bulk_action" class="form-control" required>
                            <option value="">Select Action</option>
                            <option value="approve">Approve Selected Reviews</option>
                            <option value="reject">Reject Selected Reviews</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Notes (optional):</label>
                        <textarea name="bulk_notes" class="form-control" rows="3" placeholder="Add notes for this bulk action..."></textarea>
                    </div>
                    
                    <div id="selectedReviewsList" class="selected-reviews-list">
                        <!-- Selected reviews will be listed here -->
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Execute Bulk Action</button>
                        <button type="button" class="btn btn-secondary" onclick="closeBulkModerationModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Rating Distribution Modal -->
    <div id="ratingDistributionModal" class="modal">
        <div class="modal-content medium-modal">
            <div class="modal-header">
                <h3><i class="fas fa-chart-bar"></i> Rating Distribution</h3>
                <button class="modal-close" onclick="closeRatingDistribution()">&times;</button>
            </div>
            <div class="modal-body" id="ratingDistributionContent">
                <!-- Rating distribution will be loaded here -->
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html> 