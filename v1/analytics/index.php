<?php
session_start();
require_once __DIR__ . '/../../Db_Connect.php';
require_once __DIR__ . '/../../services/AnalyticsService.php';
require_once __DIR__ . '/../../services/AuthService.php';
require_once __DIR__ . '/../../services/PermissionService.php';
require_once __DIR__ . '/../../services/NotificationService.php';

// Initialize services
$authService = new AuthService();
$permissionService = new PermissionService();
$analyticsService = new AnalyticsService();
$notificationService = new NotificationService();

// Check authentication
if (!$authService->isAuthenticated()) {
    header('Location: /agrimarket-erd/v1/auth/login/');
    exit;
}

// Get current user
$currentUser = $authService->getCurrentUser();

// Get notifications for the current user
$userNotifications = [];
$unreadCount = 0;
if ($currentUser) {
    $userNotifications = $notificationService->getUserNotifications($currentUser['user_id'], 10);
    $unreadCount = 0;
    foreach ($userNotifications as $notif) {
        if (!$notif['is_read']) $unreadCount++;
    }
    
    // Add a test notification for analytics (remove this in production)
    // if (empty($userNotifications)) {
    //     $notificationService->createNotification(
    //         $currentUser['user_id'],
    //         'Analytics Test',
    //         'Analytics notifications are working correctly!',
    //         'analytics'
    //     );
    // }
}

// Check permissions
if (!$permissionService->hasPermission($currentUser, 'view_analytics') && 
    !$permissionService->hasPermission($currentUser, 'view_reports')) {
    header('Location: /agrimarket-erd/v1/dashboard/');
    exit;
}

// Get user role and vendor information
$userRole = $currentUser['role'];
$vendorId = null;
if ($userRole === 'vendor') {
    try {
        global $host, $user, $pass, $dbname;
        $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $stmt = $db->prepare("SELECT vendor_id FROM vendors WHERE user_id = ? AND is_archive = 0");
        $stmt->execute([$currentUser['user_id']]);
        $vendor = $stmt->fetch();
        $vendorId = $vendor ? $vendor['vendor_id'] : null;
    } catch (Exception $e) {
        error_log("Error getting vendor ID: " . $e->getMessage());
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'get_dashboard_analytics':
                $analytics = $analyticsService->getDashboardAnalytics($userRole, $currentUser['user_id'], $vendorId);
                echo json_encode(['success' => true, 'data' => $analytics]);
                break;
                
            case 'get_most_searched_products':
                $limit = (int)($_POST['limit'] ?? 10);
                $timeframe = $_POST['timeframe'] ?? '30 days';
                $data = $analyticsService->getMostSearchedProducts($limit, $timeframe);
                echo json_encode(['success' => true, 'data' => $data]);
                break;
                
            case 'get_most_searched_vendors':
                $limit = (int)($_POST['limit'] ?? 10);
                $timeframe = $_POST['timeframe'] ?? '30 days';
                $data = $analyticsService->getMostSearchedVendors($limit, $timeframe);
                echo json_encode(['success' => true, 'data' => $data]);
                break;
                
            case 'get_most_searched_keywords':
                $limit = (int)($_POST['limit'] ?? 20);
                $timeframe = $_POST['timeframe'] ?? '30 days';
                $data = $analyticsService->getMostSearchedKeywords($limit, $timeframe);
                echo json_encode(['success' => true, 'data' => $data]);
                break;
                
            case 'get_search_trends_by_category':
                $timeframe = $_POST['timeframe'] ?? '30 days';
                $data = $analyticsService->getSearchTrendsByCategory($timeframe);
                echo json_encode(['success' => true, 'data' => $data]);
                break;
                
            case 'get_most_visited_pages':
                $limit = (int)($_POST['limit'] ?? 10);
                $timeframe = $_POST['timeframe'] ?? '30 days';
                $data = $analyticsService->getMostVisitedProductPages($limit, $timeframe);
                echo json_encode(['success' => true, 'data' => $data]);
                break;
                
            case 'get_most_ordered_products':
                $limit = (int)($_POST['limit'] ?? 10);
                $timeframe = $_POST['timeframe'] ?? '30 days';
                $data = $analyticsService->getMostOrderedProducts($limit, $timeframe);
                echo json_encode(['success' => true, 'data' => $data]);
                break;
                
            case 'get_sales_report':
                $timeframe = $_POST['timeframe'] ?? 'monthly';
                $startDate = $_POST['start_date'] ?? null;
                $endDate = $_POST['end_date'] ?? null;
                $data = $analyticsService->getSalesReport($timeframe, $startDate, $endDate);
                echo json_encode(['success' => true, 'data' => $data]);
                break;
                
            case 'export_data':
                $reportType = $_POST['report_type'] ?? '';
                $timeframe = $_POST['timeframe'] ?? '30 days';
                $result = $analyticsService->exportAnalyticsData($reportType, $timeframe, $userRole, $vendorId, $currentUser['user_id']);
                
                if ($result['success']) {
                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                    echo $result['content'];
                    exit;
                } else {
                    echo json_encode($result);
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

// Get initial dashboard data
$dashboardAnalytics = $analyticsService->getDashboardAnalytics($userRole, $currentUser['user_id'], $vendorId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $userRole === 'vendor' ? 'Vendor Analytics' : 'Reports & Analytics'; ?> | AgriMarket</title>
    <link rel="stylesheet" href="/agrimarket-erd/v1/components/main.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../dashboard/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <?php include '../components/sidebar.php'; ?>
        
        <main class="main-content">
            <?php 
            $pageTitle = $userRole === 'vendor' ? 'Vendor Analytics' : 'Reports & Analytics';
            include '../components/header.php'; 
            ?>
            
                        <div class="dashboard-content">
                <!-- Page Header with Gradient -->
                <div class="page-header-gradient">
                    <div class="page-header">
                        <div>
                            <h2>
                                <i class="fas fa-chart-bar"></i> 
                                <?php echo $userRole === 'vendor' ? 'Vendor Analytics' : 'Reports & Analytics'; ?>
                            </h2>
                            <p><?php echo $userRole === 'vendor' ? 'Monitor your business performance and insights' : 'Comprehensive analytics and reporting dashboard'; ?></p>
                        </div>
                        <div class="header-actions">
                            <div class="timeframe-selector">
                                <label for="globalTimeframe">Timeframe:</label>
                                <select id="globalTimeframe" onchange="updateGlobalTimeframe()">
                                    <option value="7 days">Last 7 days</option>
                                    <option value="30 days" selected>Last 30 days</option>
                                    <option value="90 days">Last 90 days</option>
                                    <option value="1 year">Last year</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" onclick="refreshAllData()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

            <!-- Overview Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon searches">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalSearches"><?php echo number_format($dashboardAnalytics['overview']['searches']['total_searches'] ?? 0); ?></h3>
                        <p>Total Searches (30 days)</p>
                        <span class="stat-change" id="searchesChange">
                            <?php echo number_format($dashboardAnalytics['overview']['searches']['searches_this_week'] ?? 0); ?> this week
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon visits">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalVisits"><?php echo number_format($dashboardAnalytics['overview']['visits']['total_visits'] ?? 0); ?></h3>
                        <p>Page Visits (30 days)</p>
                        <span class="stat-change" id="visitsChange">
                            <?php echo number_format($dashboardAnalytics['overview']['visits']['visits_this_week'] ?? 0); ?> this week
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalOrders"><?php echo number_format($dashboardAnalytics['overview']['orders']['total_orders'] ?? 0); ?></h3>
                        <p>Orders (30 days)</p>
                        <span class="stat-change" id="ordersChange">
                            <?php echo number_format($dashboardAnalytics['overview']['orders']['orders_this_week'] ?? 0); ?> this week
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="totalRevenue">$<?php echo number_format($dashboardAnalytics['overview']['orders']['total_revenue'] ?? 0, 2); ?></h3>
                        <p>Revenue (30 days)</p>
                        <span class="stat-change" id="revenueChange">
                            Avg: $<?php echo number_format($dashboardAnalytics['overview']['orders']['avg_order_value'] ?? 0, 2); ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Search Trends</h3>
                        <div class="chart-controls">
                            <button class="btn btn-sm" onclick="exportChart('search_trends')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    <canvas id="searchTrendsChart"></canvas>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Page Visit Trends</h3>
                        <div class="chart-controls">
                            <button class="btn btn-sm" onclick="exportChart('page_visits')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    <canvas id="pageVisitTrendsChart"></canvas>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Sales Trends</h3>
                        <div class="chart-controls">
                            <button class="btn btn-sm" onclick="exportChart('sales_trends')">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    </div>
                    <canvas id="salesTrendsChart"></canvas>
                </div>
            </div>

            <!-- Reports Tabs -->
            <div class="reports-section">
                <div class="tabs-container">
                    <div class="tabs-nav">
                        <button class="tab-btn active" onclick="showTab('searched-products')">
                            <i class="fas fa-search"></i> Most Searched Products
                        </button>
                        <?php if ($userRole === 'admin'): ?>
                        <button class="tab-btn" onclick="showTab('searched-vendors')">
                            <i class="fas fa-store"></i> Most Searched Vendors
                        </button>
                        <?php endif; ?>
                        <button class="tab-btn" onclick="showTab('visited-pages')">
                            <i class="fas fa-eye"></i> Most Visited Pages
                        </button>
                        <button class="tab-btn" onclick="showTab('ordered-products')">
                            <i class="fas fa-shopping-cart"></i> Most Ordered Products
                        </button>
                        <button class="tab-btn" onclick="showTab('sales-report')">
                            <i class="fas fa-chart-line"></i> Sales Reports
                        </button>
                    </div>

                    <!-- Most Searched Products Tab -->
                    <div id="searched-products" class="tab-content active">
                        <div class="report-header">
                            <h3>Most Searched Products</h3>
                            <div class="report-controls">
                                <select id="searchedProductsTimeframe" onchange="loadMostSearchedProducts()">
                                    <option value="7 days">Last 7 days</option>
                                    <option value="30 days" selected>Last 30 days</option>
                                    <option value="90 days">Last 90 days</option>
                                    <option value="1 year">Last year</option>
                                </select>
                                <button class="btn btn-primary" onclick="exportReport('most_searched_products')">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </div>
                        </div>
                        <div class="report-table-container">
                            <table id="searchedProductsTable" class="data-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Vendor</th>
                                        <th>Search Count</th>
                                        <th>Unique Searchers</th>
                                        <th>Clicks</th>
                                        <th>CTR %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Most Searched Vendors Tab -->
                    <?php if ($userRole === 'admin'): ?>
                    <div id="searched-vendors" class="tab-content">
                        <div class="report-header">
                            <h3>Most Searched Vendors</h3>
                            <div class="report-controls">
                                <select id="searchedVendorsTimeframe" onchange="loadMostSearchedVendors()">
                                    <option value="7 days">Last 7 days</option>
                                    <option value="30 days" selected>Last 30 days</option>
                                    <option value="90 days">Last 90 days</option>
                                    <option value="1 year">Last year</option>
                                </select>
                                <button class="btn btn-primary" onclick="exportReport('most_searched_vendors')">
                                    <i class="fas fa-download"></i> Export CSV
                                </button>
                            </div>
                        </div>
                        <div class="report-table-container">
                            <table id="searchedVendorsTable" class="data-table">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Email</th>
                                        <th>Search Count</th>
                                        <th>Unique Searchers</th>
                                        <th>Product Clicks</th>
                                        <th>Avg Results</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- More tabs would be here -->
                </div>
            </div>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
    <script>
        // Initialize page data
        const userRole = '<?php echo $userRole; ?>';
        const initialAnalytics = <?php echo json_encode($dashboardAnalytics); ?>;
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            loadMostSearchedProducts();
            if (userRole === 'admin') {
                loadMostSearchedVendors();
            }
            // Only load data for tabs that exist
            if (document.getElementById('visitedPagesTable')) {
                loadMostVisitedPages();
            }
            if (document.getElementById('orderedProductsTable')) {
                loadMostOrderedProducts();
            }
            if (document.getElementById('salesReportTable')) {
                loadSalesReport();
            }
        });
    </script>
</body>
</html>
