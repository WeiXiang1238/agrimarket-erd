<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/../models/ModelLoader.php';

/**
 * Analytics Service
 * Provides comprehensive analytics and reporting functionality
 */
class AnalyticsService
{
    private $db;
    private $SearchLog;
    private $PageVisit;
    private $AuditLog;
    private $Product;
    private $Vendor;
    private $Order;

    public function __construct()
    {
        global $host, $user, $pass, $dbname;
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
        
        $this->SearchLog = ModelLoader::load('SearchLog');
        $this->PageVisit = ModelLoader::load('PageVisit');
        $this->AuditLog = ModelLoader::load('AuditLog');
        $this->Product = ModelLoader::load('Product');
        $this->Vendor = ModelLoader::load('Vendor');
        $this->Order = ModelLoader::load('Order');
    }

    /**
     * Get most searched products with enhanced analytics
     */
    public function getMostSearchedProducts($limit = 10, $timeframe = '30 days')
    {
        try {
            $timeCondition = $this->getTimeCondition($timeframe, 'sl.search_date');
            $limit = (int)$limit; // Ensure integer for security
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.product_id,
                    p.name as product_name,
                    p.category,
                    p.selling_price as price,
                    p.stock_quantity,
                    v.business_name as vendor_name,
                    COUNT(sl.log_id) as search_count,
                    COUNT(DISTINCT sl.user_id) as unique_searchers,
                    COUNT(sl.clicked_product_id) as clicks,
                    ROUND((COUNT(sl.clicked_product_id) * 100.0 / COUNT(sl.log_id)), 2) as click_through_rate,
                    AVG(sl.click_position) as avg_click_position,
                    AVG(sl.results_count) as avg_results_shown,
                    COUNT(DISTINCT sl.session_id) as unique_sessions,
                    MAX(sl.search_date) as last_searched,
                    GROUP_CONCAT(DISTINCT sl.keyword ORDER BY sl.search_date DESC) as recent_keywords
                FROM search_logs sl
                INNER JOIN products p ON sl.clicked_product_id = p.product_id
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE sl.clicked_product_id IS NOT NULL 
                AND p.is_archive = 0 
                AND $timeCondition
                GROUP BY p.product_id
                ORDER BY search_count DESC
                LIMIT $limit
            ");
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting most searched products: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get most searched keywords/terms
     */
    public function getMostSearchedKeywords($limit = 20, $timeframe = '30 days')
    {
        try {
            $timeCondition = $this->getTimeCondition($timeframe, 'search_date');
            $limit = (int)$limit; // Ensure integer for security
            
            $stmt = $this->db->prepare("
                SELECT 
                    keyword,
                    COUNT(*) as search_count,
                    COUNT(DISTINCT user_id) as unique_searchers,
                    COUNT(DISTINCT session_id) as unique_sessions,
                    COUNT(clicked_product_id) as total_clicks,
                    ROUND((COUNT(clicked_product_id) * 100.0 / COUNT(*)), 2) as click_through_rate,
                    AVG(results_count) as avg_results_count,
                    MAX(search_date) as last_searched,
                    MIN(search_date) as first_searched
                FROM search_logs 
                WHERE keyword IS NOT NULL 
                AND keyword != ''
                AND $timeCondition
                GROUP BY keyword
                ORDER BY search_count DESC
                LIMIT $limit
            ");
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting most searched keywords: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get search trends by category
     */
    public function getSearchTrendsByCategory($timeframe = '30 days')
    {
        try {
            $timeCondition = $this->getTimeCondition($timeframe, 'sl.search_date');
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.category,
                    COUNT(sl.log_id) as search_count,
                    COUNT(DISTINCT sl.user_id) as unique_searchers,
                    COUNT(sl.clicked_product_id) as clicks,
                    ROUND((COUNT(sl.clicked_product_id) * 100.0 / COUNT(sl.log_id)), 2) as click_through_rate,
                    COUNT(DISTINCT sl.keyword) as unique_keywords
                FROM search_logs sl
                INNER JOIN products p ON sl.clicked_product_id = p.product_id
                WHERE sl.clicked_product_id IS NOT NULL 
                AND p.is_archive = 0 
                AND $timeCondition
                GROUP BY p.category
                ORDER BY search_count DESC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting search trends by category: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get most searched vendors
     */
    public function getMostSearchedVendors($limit = 10, $timeframe = '30 days')
    {
        try {
            $timeCondition = $this->getTimeCondition($timeframe, 'sl.search_date');
            $limit = (int)$limit; // Ensure integer for security
            
            $stmt = $this->db->prepare("
                SELECT 
                    v.vendor_id,
                    v.business_name,
                    v.contact_email,
                    COUNT(sl.log_id) as search_count,
                    COUNT(DISTINCT sl.user_id) as unique_searchers,
                    COUNT(sl.clicked_product_id) as product_clicks,
                    AVG(sl.results_count) as avg_results_per_search
                FROM search_logs sl
                INNER JOIN products p ON sl.clicked_product_id = p.product_id
                INNER JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE sl.clicked_product_id IS NOT NULL 
                AND v.is_archive = 0 
                AND $timeCondition
                GROUP BY v.vendor_id
                ORDER BY search_count DESC
                LIMIT $limit
            ");
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting most searched vendors: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get most visited product pages
     */
    public function getMostVisitedProductPages($limit = 10, $timeframe = '30 days')
    {
        try {
            $timeCondition = $this->getTimeCondition($timeframe, 'pv.visit_date');
            $limit = (int)$limit; // Ensure integer for security
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.product_id,
                    p.name as product_name,
                    p.category,
                    p.selling_price as price,
                    v.business_name as vendor_name,
                    COUNT(pv.visit_id) as visit_count,
                    COUNT(DISTINCT pv.user_id) as unique_visitors,
                    COUNT(DISTINCT pv.session_id) as unique_sessions,
                    AVG(pv.visit_duration) as avg_visit_duration,
                    COUNT(CASE WHEN pv.user_id IS NOT NULL THEN 1 END) as logged_in_visits,
                    COUNT(CASE WHEN pv.user_id IS NULL THEN 1 END) as anonymous_visits
                FROM page_visits pv
                INNER JOIN products p ON pv.page_url LIKE CONCAT('%product%', p.product_id, '%')
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE p.is_archive = 0 
                AND $timeCondition
                GROUP BY p.product_id
                ORDER BY visit_count DESC
                LIMIT $limit
            ");
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting most visited product pages: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get most popularly ordered products
     */
    public function getMostOrderedProducts($limit = 10, $timeframe = '30 days')
    {
        try {
            $timeCondition = $this->getTimeCondition($timeframe, 'o.order_date');
            $limit = (int)$limit; // Ensure integer for security
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.product_id,
                    p.name as product_name,
                    p.category,
                    p.selling_price as price,
                    v.business_name as vendor_name,
                    COUNT(oi.order_item_id) as order_count,
                    SUM(oi.quantity) as total_quantity_sold,
                    SUM(oi.price * oi.quantity) as total_revenue,
                    COUNT(DISTINCT o.customer_id) as unique_customers,
                    AVG(oi.quantity) as avg_quantity_per_order,
                    AVG(oi.price) as avg_price_per_unit
                FROM order_items oi
                INNER JOIN orders o ON oi.order_id = o.order_id
                INNER JOIN products p ON oi.product_id = p.product_id
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE o.is_archive = 0 
                AND p.is_archive = 0 
                AND o.status != 'Cancelled'
                AND $timeCondition
                GROUP BY p.product_id
                ORDER BY total_quantity_sold DESC
                LIMIT $limit
            ");
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Error getting most ordered products: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get sales reports by timeframe
     */
    public function getSalesReport($timeframe = 'monthly', $startDate = null, $endDate = null)
    {
        try {
            $groupBy = $this->getGroupByFormat($timeframe);
            $dateCondition = '';
            $params = [];
            
            if ($startDate && $endDate) {
                $dateCondition = 'AND o.order_date BETWEEN ? AND ?';
                $params = [$startDate, $endDate];
            } else {
                $dateCondition = $this->getTimeCondition($this->getDefaultTimeRange($timeframe), 'o.order_date');
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    $groupBy as period,
                    COUNT(o.order_id) as total_orders,
                    SUM(o.final_amount) as total_revenue,
                    AVG(o.final_amount) as avg_order_value,
                    COUNT(DISTINCT o.customer_id) as unique_customers,
                    COUNT(DISTINCT o.vendor_id) as active_vendors,
                    SUM(CASE WHEN o.status = 'Delivered' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN o.status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    ROUND((SUM(CASE WHEN o.status = 'Delivered' THEN 1 ELSE 0 END) * 100.0 / COUNT(o.order_id)), 2) as success_rate
                FROM orders o
                WHERE o.is_archive = 0 
                $dateCondition
                GROUP BY $groupBy
                ORDER BY period DESC
                LIMIT 12
            ");
            
            $stmt->execute($params);
            return array_reverse($stmt->fetchAll());
            
        } catch (Exception $e) {
            error_log("Error generating sales report: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get comprehensive analytics dashboard data
     */
    public function getDashboardAnalytics($userRole = 'admin', $userId = null, $vendorId = null)
    {
        try {
            $analytics = [
                'overview' => $this->getAnalyticsOverview($userRole, $userId, $vendorId),
                'search_trends' => $this->getSearchTrends($userRole, $vendorId),
                'page_visit_trends' => $this->getPageVisitTrends($userRole, $vendorId),
                'sales_trends' => $this->getSalesTrends($userRole, $vendorId),
                'top_performing' => [
                    'products' => $this->getMostOrderedProducts(5, '30 days'),
                    'searched_products' => $this->getMostSearchedProducts(5, '30 days'),
                    'visited_pages' => $this->getMostVisitedProductPages(5, '30 days')
                ]
            ];

            // Add vendor-specific data if applicable
            if ($userRole === 'vendor' && $vendorId) {
                $analytics['vendor_performance'] = $this->getVendorPerformance($vendorId);
            }

            return $analytics;
            
        } catch (Exception $e) {
            error_log("Error getting dashboard analytics: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get analytics overview stats
     */
    private function getAnalyticsOverview($userRole, $userId, $vendorId)
    {
        try {
            $vendorCondition = '';
            $params = [];
            
            if ($userRole === 'vendor' && $vendorId) {
                $vendorCondition = 'AND p.vendor_id = ?';
                $params[] = $vendorId;
            }
            
            // Get search stats
            $searchStmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_searches,
                    COUNT(DISTINCT user_id) as unique_searchers,
                    COUNT(clicked_product_id) as total_clicks,
                    COUNT(CASE WHEN search_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as searches_this_week
                FROM search_logs sl
                WHERE search_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                " . ($vendorCondition ? "AND clicked_product_id IN (SELECT product_id FROM products p WHERE p.is_archive = 0 $vendorCondition)" : "")
            );
            $searchStmt->execute($params);
            $searchStats = $searchStmt->fetch();

            // Get page visit stats
            $visitStmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_visits,
                    COUNT(DISTINCT user_id) as unique_visitors,
                    COUNT(DISTINCT session_id) as unique_sessions,
                    AVG(visit_duration) as avg_duration,
                    COUNT(CASE WHEN visit_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as visits_this_week
                FROM page_visits pv
                WHERE visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                " . ($vendorCondition ? "AND page_url LIKE '%product%' AND EXISTS (SELECT 1 FROM products p WHERE pv.page_url LIKE CONCAT('%', p.product_id, '%') AND p.is_archive = 0 $vendorCondition)" : "")
            );
            $visitStmt->execute($params);
            $visitStats = $visitStmt->fetch();

            // Get order stats
            $orderCondition = '';
            if ($userRole === 'vendor' && $vendorId) {
                $orderCondition = 'AND vendor_id = ?';
            }
            $orderStmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_orders,
                    SUM(final_amount) as total_revenue,
                    AVG(final_amount) as avg_order_value,
                    COUNT(DISTINCT customer_id) as unique_customers,
                    COUNT(CASE WHEN order_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as orders_this_week
                FROM orders 
                WHERE is_archive = 0 
                AND order_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                AND status != 'Cancelled'
                $orderCondition
            ");
            $orderStmt->execute($params);
            $orderStats = $orderStmt->fetch();

            return [
                'searches' => $searchStats,
                'visits' => $visitStats,
                'orders' => $orderStats
            ];
            
        } catch (Exception $e) {
            return [
                'searches' => ['total_searches' => 0, 'unique_searchers' => 0, 'total_clicks' => 0, 'searches_this_week' => 0],
                'visits' => ['total_visits' => 0, 'unique_visitors' => 0, 'unique_sessions' => 0, 'avg_duration' => 0, 'visits_this_week' => 0],
                'orders' => ['total_orders' => 0, 'total_revenue' => 0, 'avg_order_value' => 0, 'unique_customers' => 0, 'orders_this_week' => 0]
            ];
        }
    }

    /**
     * Get search trends over time
     */
    private function getSearchTrends($userRole, $vendorId)
    {
        try {
            $vendorCondition = '';
            $params = [];
            
            if ($userRole === 'vendor' && $vendorId) {
                $vendorCondition = 'AND clicked_product_id IN (SELECT product_id FROM products WHERE vendor_id = ? AND is_archive = 0)';
                $params[] = $vendorId;
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(search_date) as date,
                    COUNT(*) as search_count,
                    COUNT(DISTINCT user_id) as unique_searchers,
                    COUNT(clicked_product_id) as clicks
                FROM search_logs 
                WHERE search_date >= DATE_SUB(NOW(), INTERVAL 14 DAY)
                $vendorCondition
                GROUP BY DATE(search_date)
                ORDER BY date ASC
            ");
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get page visit trends over time
     */
    private function getPageVisitTrends($userRole, $vendorId)
    {
        try {
            $vendorCondition = '';
            $params = [];
            
            if ($userRole === 'vendor' && $vendorId) {
                $vendorCondition = 'AND page_url LIKE "%product%" AND EXISTS (SELECT 1 FROM products p WHERE page_url LIKE CONCAT("%", p.product_id, "%") AND p.vendor_id = ? AND p.is_archive = 0)';
                $params[] = $vendorId;
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(visit_date) as date,
                    COUNT(*) as visit_count,
                    COUNT(DISTINCT user_id) as unique_visitors,
                    COUNT(DISTINCT session_id) as unique_sessions
                FROM page_visits 
                WHERE visit_date >= DATE_SUB(NOW(), INTERVAL 14 DAY)
                $vendorCondition
                GROUP BY DATE(visit_date)
                ORDER BY date ASC
            ");
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get sales trends over time
     */
    private function getSalesTrends($userRole, $vendorId)
    {
        try {
            $vendorCondition = '';
            $params = [];
            
            if ($userRole === 'vendor' && $vendorId) {
                $vendorCondition = 'AND vendor_id = ?';
                $params[] = $vendorId;
            }
            
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(order_date) as date,
                    COUNT(*) as order_count,
                    SUM(final_amount) as revenue,
                    AVG(final_amount) as avg_order_value
                FROM orders 
                WHERE is_archive = 0 
                AND order_date >= DATE_SUB(NOW(), INTERVAL 14 DAY)
                AND status != 'Cancelled'
                $vendorCondition
                GROUP BY DATE(order_date)
                ORDER BY date ASC
            ");
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get vendor-specific performance data
     */
    private function getVendorPerformance($vendorId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(DISTINCT p.product_id) as total_products,
                    COUNT(DISTINCT o.order_id) as total_orders,
                    SUM(o.final_amount) as total_revenue,
                    AVG(r.rating) as avg_rating,
                    COUNT(r.review_id) as total_reviews,
                    COUNT(CASE WHEN o.status = 'Delivered' THEN 1 END) as delivered_orders,
                    COUNT(CASE WHEN o.status = 'Cancelled' THEN 1 END) as cancelled_orders
                FROM vendors v
                LEFT JOIN products p ON v.vendor_id = p.vendor_id AND p.is_archive = 0
                LEFT JOIN orders o ON v.vendor_id = o.vendor_id AND o.is_archive = 0
                LEFT JOIN reviews r ON p.product_id = r.product_id AND r.is_approved = 1
                WHERE v.vendor_id = ?
                GROUP BY v.vendor_id
            ");
            $stmt->execute([$vendorId]);
            return $stmt->fetch();
            
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Helper method to get time condition SQL
     */
    private function getTimeCondition($timeframe, $dateColumn)
    {
        switch ($timeframe) {
            case '7 days':
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case '30 days':
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case '90 days':
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
            case '1 year':
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            default:
                return "$dateColumn >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
    }

    /**
     * Helper method to get GROUP BY format for different timeframes
     */
    private function getGroupByFormat($timeframe)
    {
        switch ($timeframe) {
            case 'daily':
                return "DATE(o.order_date)";
            case 'weekly':
                return "YEARWEEK(o.order_date)";
            case 'monthly':
                return "DATE_FORMAT(o.order_date, '%Y-%m')";
            case 'quarterly':
                return "CONCAT(YEAR(o.order_date), '-Q', QUARTER(o.order_date))";
            case 'annually':
                return "YEAR(o.order_date)";
            default:
                return "DATE_FORMAT(o.order_date, '%Y-%m')";
        }
    }

    /**
     * Helper method to get default time range for different report timeframes
     */
    private function getDefaultTimeRange($timeframe)
    {
        switch ($timeframe) {
            case 'daily':
                return '30 days';
            case 'weekly':
                return '90 days';
            case 'monthly':
                return '1 year';
            case 'quarterly':
                return '2 years';
            case 'annually':
                return '5 years';
            default:
                return '1 year';
        }
    }

    /**
     * Export analytics data to CSV
     */
    public function exportAnalyticsData($reportType, $timeframe = '30 days', $userRole = 'admin', $vendorId = null)
    {
        try {
            $data = [];
            $filename = '';
            
            switch ($reportType) {
                case 'most_searched_products':
                    $data = $this->getMostSearchedProducts(100, $timeframe);
                    $filename = 'most_searched_products_' . str_replace(' ', '_', $timeframe);
                    break;
                case 'most_visited_pages':
                    $data = $this->getMostVisitedProductPages(100, $timeframe);
                    $filename = 'most_visited_pages_' . str_replace(' ', '_', $timeframe);
                    break;
                case 'most_ordered_products':
                    $data = $this->getMostOrderedProducts(100, $timeframe);
                    $filename = 'most_ordered_products_' . str_replace(' ', '_', $timeframe);
                    break;
                case 'sales_report':
                    $data = $this->getSalesReport('daily', null, null);
                    $filename = 'sales_report_' . str_replace(' ', '_', $timeframe);
                    break;
                default:
                    throw new Exception('Invalid report type');
            }
            
            if (empty($data)) {
                throw new Exception('No data available for export');
            }
            
            // Generate CSV content
            $csvContent = $this->generateCSVContent($data);
            
            return [
                'success' => true,
                'filename' => $filename . '_' . date('Y-m-d') . '.csv',
                'content' => $csvContent
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate CSV content from data array
     */
    private function generateCSVContent($data)
    {
        if (empty($data)) {
            return '';
        }
        
        $output = fopen('php://temp', 'r+');
        
        // Write headers
        fputcsv($output, array_keys($data[0]));
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $csvContent;
    }
}

?> 