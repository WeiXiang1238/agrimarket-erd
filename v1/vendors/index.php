<?php
session_start();
require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/VendorService.php';
require_once __DIR__ . '/../../models/ModelLoader.php';

// Set page title for tracking
$pageTitle = 'Find Vendors - AgriMarket Solutions';

// Include page tracking
require_once __DIR__ . '/../../includes/page_tracking.php';

// Initialize services
$authService = new AuthService();
$vendorService = new VendorService();

// Get current user (optional for vendor directory)
$currentUser = $authService->getCurrentUser();
$customerId = null;

if ($currentUser && $currentUser['role'] === 'customer') {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $stmt = $db->prepare("SELECT customer_id FROM customers WHERE user_id = ? AND is_archive = 0");
        $stmt->execute([$currentUser['user_id']]);
        $customer = $stmt->fetch();
        $customerId = $customer ? $customer['customer_id'] : null;
    } catch (Exception $e) {
        error_log("Error getting customer ID: " . $e->getMessage());
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'search_vendors':
                $page = (int)($_POST['page'] ?? 1);  
                $limit = (int)($_POST['limit'] ?? 12);
                $searchTerm = $_POST['search'] ?? '';
                $subscriptionTier = $_POST['subscription_tier'] ?? '';
                
                $filters = [
                    'search' => $searchTerm,
                    'subscription_tier' => $subscriptionTier,
                    'status' => '1' // Only active vendors
                ];
                
                $result = $vendorService->getPaginatedVendors($page, $limit, $filters);
                
                // Log vendor search/visit (always log, not just when there's a search term)
                if ($result['success']) {
                    try {
                        $searchLogModel = ModelLoader::load('SearchLog');
                        
                        $searchLogData = [
                            'user_id' => $currentUser['user_id'] ?? null,
                            'keyword' => $searchTerm ?: 'vendor_browse', // Use 'vendor_browse' if no search term
                            'filters' => json_encode([
                                'subscription_tier' => $subscriptionTier,
                                'search_type' => 'vendor',
                                'has_search_term' => !empty($searchTerm)
                            ]),
                            'results_count' => $result['total'] ?? 0,
                            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                            'session_id' => session_id()
                        ];
                        
                        $searchLogId = $searchLogModel->create($searchLogData);
                        $result['search_log_id'] = $searchLogId;
                    } catch (Exception $e) {
                        error_log("Error logging vendor search: " . $e->getMessage());
                    }
                }
                
                echo json_encode($result);
                break;
                
            case 'track_vendor_click':
                $searchLogId = (int)($_POST['search_log_id'] ?? 0);
                $vendorId = (int)($_POST['vendor_id'] ?? 0);
                $clickPosition = (int)($_POST['click_position'] ?? 0);
                
                if ($searchLogId > 0 && $vendorId > 0) {
                    try {
                        $searchLogModel = ModelLoader::load('SearchLog');
                        
                        $updateData = [
                            'clicked_vendor_id' => $vendorId,
                            'click_position' => $clickPosition,
                            'clicked_at' => date('Y-m-d H:i:s')
                        ];
                        
                        $searchLogModel->update($searchLogId, $updateData);
                        echo json_encode(['success' => true, 'message' => 'Vendor click tracked']);
                    } catch (Exception $e) {
                        error_log("Error tracking vendor click: " . $e->getMessage());
                        echo json_encode(['success' => false, 'message' => 'Failed to track click']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
                }
                break;
                
            case 'get_vendor_products':
                $vendorId = (int)($_POST['vendor_id'] ?? 0);
                $page = (int)($_POST['page'] ?? 1);
                $limit = (int)($_POST['limit'] ?? 8);
                
                if ($vendorId > 0) {
                    require_once __DIR__ . '/../../services/ProductService.php';
                    $productService = new ProductService();
                    
                    $filters = [
                        'vendor_id' => $vendorId,
                        'status' => 'active'
                    ];
                    
                    $result = $productService->getPaginatedProducts($page, $limit, $filters, 'customer');
                    echo json_encode($result);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid vendor ID']);
                }
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Get initial data for page load
$page = (int)($_GET['page'] ?? 1);
$search = $_GET['search'] ?? '';
$subscriptionTier = $_GET['subscription_tier'] ?? '';

$filters = [
    'search' => $search,
    'subscription_tier' => $subscriptionTier,
    'status' => '1'
];

// Get vendors
$vendorsData = $vendorService->getPaginatedVendors($page, 12, $filters);
$vendors = $vendorsData['vendors'] ?? [];
$totalPages = $vendorsData['totalPages'] ?? 1;

// Get subscription tiers for filter
$subscriptionTiers = $vendorService->getSubscriptionTiers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Vendors | AgriMarket</title>
    <link rel="stylesheet" href="/agrimarket-erd/v1/components/main.css">
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include '../components/sidebar.php'; ?>
        
        <main class="main-content">
            <?php 
            $pageTitle = 'Find Vendors';
            include '../components/header.php'; 
            ?>
            
            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2><i class="fas fa-store-alt"></i> Find Vendors</h2>
                            <p>Discover trusted agricultural vendors and suppliers</p>
                        </div>
                        <a href="/agrimarket-erd/v1/products/" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i> Browse Products
                        </a>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="controls-section">
                    <div class="controls-left">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search vendors..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        
                        <select id="tierFilter">
                            <option value="">All Tiers</option>
                            <?php foreach ($subscriptionTiers as $tier): ?>
                                <option value="<?php echo $tier['value']; ?>" 
                                        <?php echo $subscriptionTier === $tier['value'] ? 'selected' : ''; ?>>
                                    <?php echo $tier['label']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <button class="btn btn-secondary" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Vendors Grid -->
                <div class="content-card">
                    <div id="vendorsGrid" class="vendors-grid">
                        <!-- Vendors will be loaded here -->
                    </div>
                    
                    <!-- Pagination -->
                    <div id="pagination" class="pagination">
                        <!-- Pagination will be loaded here -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
    <script>
        // Initialize with server data
        const initialVendors = <?php echo json_encode($vendors); ?>;
        const initialTotalPages = <?php echo $totalPages; ?>;
        const initialPage = <?php echo $page; ?>;
        
        document.addEventListener('DOMContentLoaded', function() {
            displayVendors(initialVendors);
            updatePagination(initialPage, initialTotalPages);
        });
    </script>
    <script src="/agrimarket-erd/v1/components/page_tracking.js"></script>
</body>
</html> 