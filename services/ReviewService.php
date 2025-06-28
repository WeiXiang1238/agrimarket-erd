<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/../models/ModelLoader.php';

/**
 * Review Service
 * Handles complex review operations, analytics, and moderation
 */
class ReviewService
{
    private $db;
    private $Review;
    private $VendorReview;
    private $Product;
    private $Vendor;
    private $Customer;

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
        
        $this->Review = ModelLoader::load('Review');
        $this->VendorReview = ModelLoader::load('VendorReview');
        $this->Product = ModelLoader::load('Product');
        $this->Vendor = ModelLoader::load('Vendor');
        $this->Customer = ModelLoader::load('Customer');
    }

    /**
     * Get reviews with pagination and filtering
     */
    public function getReviews($filters = [], $page = 1, $limit = 10)
    {
        try {
            $type = $filters['type'] ?? 'product'; // 'product' or 'vendor'
            $status = $filters['status'] ?? ''; // 'pending', 'approved', 'rejected'
            $search = $filters['search'] ?? '';
            $productId = $filters['product_id'] ?? null;
            $vendorId = $filters['vendor_id'] ?? null;
            $customerId = $filters['customer_id'] ?? null;
            $rating = $filters['rating'] ?? null;

            $offset = ($page - 1) * $limit;
            $whereConditions = [];
            $params = [];

            if ($type === 'product') {
                $baseQuery = "
                    SELECT 
                        r.review_id,
                        r.product_id,
                        r.customer_id,
                        r.order_id,
                        r.rating,
                        r.title,
                        r.comment,
                        r.pros,
                        r.cons,
                        r.is_verified_purchase,
                        r.is_approved,
                        r.approved_by,
                        r.approved_at,
                        r.helpful_count,
                        r.created_at,
                        p.name as product_name,
                        p.image_path as product_image,
                        cu.name as customer_name,
                        v.business_name as vendor_name,
                        approver.name as approved_by_name,
                        'product' as review_type
                    FROM reviews r
                    LEFT JOIN products p ON r.product_id = p.product_id
                    LEFT JOIN customers c ON r.customer_id = c.customer_id
                    LEFT JOIN users cu ON c.user_id = cu.user_id
                    LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                    LEFT JOIN users approver ON r.approved_by = approver.user_id
                    WHERE 1=1
                ";
                $countQuery = "SELECT COUNT(*) as total FROM reviews r WHERE 1=1";
            } else {
                $baseQuery = "
                    SELECT 
                        vr.vendor_review_id as review_id,
                        vr.vendor_id,
                        vr.customer_id,
                        vr.order_id,
                        vr.rating,
                        vr.title,
                        vr.comment,
                        vr.pros,
                        vr.cons,
                        vr.is_verified_purchase,
                        vr.is_approved,
                        vr.approved_by,
                        vr.approved_at,
                        0 as helpful_count,
                        vr.created_at,
                        v.business_name as vendor_name,
                        cu.name as customer_name,
                        approver.name as approved_by_name,
                        'vendor' as review_type
                    FROM vendor_reviews vr
                    LEFT JOIN vendors v ON vr.vendor_id = v.vendor_id
                    LEFT JOIN customers c ON vr.customer_id = c.customer_id
                    LEFT JOIN users cu ON c.user_id = cu.user_id
                    LEFT JOIN users approver ON vr.approved_by = approver.user_id
                    WHERE 1=1
                ";
                $countQuery = "SELECT COUNT(*) as total FROM vendor_reviews vr WHERE 1=1";
            }

            // Add filtering conditions
            if ($status !== '') {
                if ($status === 'pending') {
                    $whereConditions[] = "is_approved = 0 AND (approved_at IS NULL OR approved_at = '')";
                    $countQuery .= " AND is_approved = 0 AND (approved_at IS NULL OR approved_at = '')";
                } elseif ($status === 'approved') {
                    $whereConditions[] = "is_approved = 1";
                    $countQuery .= " AND is_approved = 1";
                } elseif ($status === 'rejected') {
                    $whereConditions[] = "is_approved = 0 AND approved_at IS NOT NULL AND approved_at != ''";
                    $countQuery .= " AND is_approved = 0 AND approved_at IS NOT NULL AND approved_at != ''";
                }
            }

            if ($search) {
                $whereConditions[] = "(title LIKE ? OR comment LIKE ? OR pros LIKE ? OR cons LIKE ?)";
                $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
                $countQuery .= " AND (title LIKE ? OR comment LIKE ? OR pros LIKE ? OR cons LIKE ?)";
            }

            if ($productId && $type === 'product') {
                $whereConditions[] = "r.product_id = ?";
                $params[] = $productId;
                $countQuery .= " AND product_id = ?";
            }

            if ($vendorId) {
                if ($type === 'product') {
                    $whereConditions[] = "v.vendor_id = ?";
                } else {
                    $whereConditions[] = "vr.vendor_id = ?";
                }
                $params[] = $vendorId;
                $countQuery .= " AND vendor_id = ?";
            }

            if ($customerId) {
                $prefix = $type === 'product' ? 'r' : 'vr';
                $whereConditions[] = "{$prefix}.customer_id = ?";
                $params[] = $customerId;
                $countQuery .= " AND customer_id = ?";
            }

            if ($rating) {
                $prefix = $type === 'product' ? 'r' : 'vr';
                $whereConditions[] = "{$prefix}.rating = ?";
                $params[] = $rating;
                $countQuery .= " AND rating = ?";
            }

            // Build final query
            if (!empty($whereConditions)) {
                $baseQuery .= " AND " . implode(" AND ", $whereConditions);
            }
            // Use direct integer values for LIMIT and OFFSET to avoid PDO string binding issues
            $limit = intval($limit);
            $offset = intval($offset);
            $baseQuery .= " ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";

            // Get total count
            $countParams = $search ? ["%$search%", "%$search%", "%$search%", "%$search%"] : [];
            if ($productId && $type === 'product') $countParams[] = $productId;
            if ($vendorId) $countParams[] = $vendorId;
            if ($customerId) $countParams[] = $customerId;
            if ($rating) $countParams[] = $rating;

            $stmt = $this->db->prepare($countQuery);
            if (!empty($countParams)) {
                $stmt->execute($countParams);
            } else {
                $stmt->execute();
            }
            $total = $stmt->fetch()['total'];

            // Get reviews (no need to add limit/offset to params now)
            $stmt = $this->db->prepare($baseQuery);
            $stmt->execute($params);
            $reviews = $stmt->fetchAll();

            return [
                'success' => true,
                'reviews' => $reviews,
                'total' => $total,
                'page' => $page,
                'totalPages' => ceil($total / $limit)
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch reviews: ' . $e->getMessage()];
        }
    }

    /**
     * Get review statistics
     */
    public function getReviewStats($userRole = null, $userId = null)
    {
        try {
            $stats = [
                'total_reviews' => 0,
                'pending_reviews' => 0,
                'approved_reviews' => 0,
                'average_rating' => 0,
                'total_product_reviews' => 0,
                'total_vendor_reviews' => 0,
                'verified_reviews' => 0,
                'reviews_this_month' => 0
            ];

            // Base queries for product reviews
            $productStatsQuery = "
                SELECT 
                    COUNT(*) as total_product_reviews,
                    COUNT(CASE WHEN is_approved = 0 THEN 1 END) as pending_product_reviews,
                    COUNT(CASE WHEN is_approved = 1 THEN 1 END) as approved_product_reviews,
                    COUNT(CASE WHEN is_verified_purchase = 1 THEN 1 END) as verified_product_reviews,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN 1 END) as product_reviews_this_month,
                    AVG(CASE WHEN is_approved = 1 THEN rating END) as avg_product_rating
                FROM reviews 
                WHERE 1=1
            ";

            // Base queries for vendor reviews
            $vendorStatsQuery = "
                SELECT 
                    COUNT(*) as total_vendor_reviews,
                    COUNT(CASE WHEN is_approved = 0 THEN 1 END) as pending_vendor_reviews,
                    COUNT(CASE WHEN is_approved = 1 THEN 1 END) as approved_vendor_reviews,
                    COUNT(CASE WHEN is_verified_purchase = 1 THEN 1 END) as verified_vendor_reviews,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN 1 END) as vendor_reviews_this_month,
                    AVG(CASE WHEN is_approved = 1 THEN rating END) as avg_vendor_rating
                FROM vendor_reviews
            ";

            // Add user-specific filtering for vendors
            if ($userRole === 'vendor' && $userId) {
                $vendorId = $this->getVendorIdByUserId($userId);
                if ($vendorId) {
                    $productStatsQuery .= " AND product_id IN (SELECT product_id FROM products WHERE vendor_id = ?)";
                    $vendorStatsQuery .= " AND vendor_id = ?";
                    
                    $stmt = $this->db->prepare($productStatsQuery);
                    $stmt->execute([$vendorId]);
                    $productStats = $stmt->fetch();
                    
                    $stmt = $this->db->prepare($vendorStatsQuery);
                    $stmt->execute([$vendorId]);
                    $vendorStats = $stmt->fetch();
                } else {
                    return $stats; // No vendor profile, return zero stats
                }
            } else {
                $stmt = $this->db->prepare($productStatsQuery);
                $stmt->execute();
                $productStats = $stmt->fetch();
                
                $stmt = $this->db->prepare($vendorStatsQuery);
                $stmt->execute();
                $vendorStats = $stmt->fetch();
            }

            // Combine statistics
            $stats['total_product_reviews'] = intval($productStats['total_product_reviews']);
            $stats['total_vendor_reviews'] = intval($vendorStats['total_vendor_reviews']);
            $stats['total_reviews'] = $stats['total_product_reviews'] + $stats['total_vendor_reviews'];
            
            $stats['pending_reviews'] = intval($productStats['pending_product_reviews']) + intval($vendorStats['pending_vendor_reviews']);
            $stats['approved_reviews'] = intval($productStats['approved_product_reviews']) + intval($vendorStats['approved_vendor_reviews']);
            $stats['verified_reviews'] = intval($productStats['verified_product_reviews']) + intval($vendorStats['verified_vendor_reviews']);
            $stats['reviews_this_month'] = intval($productStats['product_reviews_this_month']) + intval($vendorStats['vendor_reviews_this_month']);

            // Calculate average rating (weighted)
            $productAvg = floatval($productStats['avg_product_rating'] ?? 0);
            $vendorAvg = floatval($vendorStats['avg_vendor_rating'] ?? 0);
            $totalApproved = $stats['approved_reviews'];
            
            if ($totalApproved > 0) {
                $productWeight = $stats['approved_reviews'] > 0 ? intval($productStats['approved_product_reviews']) / $totalApproved : 0;
                $vendorWeight = $stats['approved_reviews'] > 0 ? intval($vendorStats['approved_vendor_reviews']) / $totalApproved : 0;
                $stats['average_rating'] = round(($productAvg * $productWeight) + ($vendorAvg * $vendorWeight), 2);
            }

            return $stats;

        } catch (Exception $e) {
            return $stats; // Return empty stats on error
        }
    }

    /**
     * Moderate review (approve/reject)
     */
    public function moderateReview($reviewId, $type, $action, $moderatorId)
    {
        try {
            $isApproved = $action === 'approve' ? 1 : 0;
            $data = [
                'is_approved' => $isApproved,
                'approved_by' => $moderatorId,
                'approved_at' => date('Y-m-d H:i:s')
            ];

            if ($type === 'product') {
                $result = $this->Review->update($reviewId, $data);
            } else {
                $result = $this->VendorReview->update($reviewId, $data);
            }

            $actionText = $action === 'approve' ? 'approved' : 'rejected';
            return ['success' => $result !== false, 'message' => $result !== false ? "Review {$actionText} successfully" : 'Failed to moderate review'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error moderating review: ' . $e->getMessage()];
        }
    }

    /**
     * Bulk moderate reviews
     */
    public function bulkModerateReviews($reviewIds, $type, $action, $moderatorId)
    {
        try {
            $isApproved = $action === 'approve' ? 1 : 0;
            $successCount = 0;
            $totalCount = count($reviewIds);

            foreach ($reviewIds as $reviewId) {
                $data = [
                    'is_approved' => $isApproved,
                    'approved_by' => $moderatorId,
                    'approved_at' => date('Y-m-d H:i:s')
                ];

                $result = false;
                if ($type === 'product') {
                    $result = $this->Review->update($reviewId, $data);
                } else {
                    $result = $this->VendorReview->update($reviewId, $data);
                }

                if ($result !== false) {
                    $successCount++;
                }
            }

            return [
                'success' => $successCount > 0,
                'message' => "Successfully moderated {$successCount} out of {$totalCount} reviews",
                'processed' => $successCount,
                'total' => $totalCount
            ];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error in bulk moderation: ' . $e->getMessage()];
        }
    }

    /**
     * Get review details
     */
    public function getReviewDetails($reviewId, $type)
    {
        try {
            if ($type === 'product') {
                $query = "
                    SELECT 
                        r.*,
                        p.name as product_name,
                        p.image_path as product_image,
                        cu.name as customer_name,
                        cu.email as customer_email,
                        v.business_name as vendor_name,
                        CONCAT('ORD-', o.order_id) as order_number,
                        approver.name as approved_by_name
                    FROM reviews r
                    LEFT JOIN products p ON r.product_id = p.product_id
                    LEFT JOIN customers c ON r.customer_id = c.customer_id
                    LEFT JOIN users cu ON c.user_id = cu.user_id
                    LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                    LEFT JOIN orders o ON r.order_id = o.order_id
                    LEFT JOIN users approver ON r.approved_by = approver.user_id
                    WHERE r.review_id = ?
                ";
            } else {
                $query = "
                    SELECT 
                        vr.*,
                        v.business_name as vendor_name,
                        cu.name as customer_name,
                        cu.email as customer_email,
                        CONCAT('ORD-', o.order_id) as order_number,
                        approver.name as approved_by_name
                    FROM vendor_reviews vr
                    LEFT JOIN vendors v ON vr.vendor_id = v.vendor_id
                    LEFT JOIN customers c ON vr.customer_id = c.customer_id
                    LEFT JOIN users cu ON c.user_id = cu.user_id
                    LEFT JOIN orders o ON vr.order_id = o.order_id
                    LEFT JOIN users approver ON vr.approved_by = approver.user_id
                    WHERE vr.vendor_review_id = ?
                ";
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute([$reviewId]);
            $review = $stmt->fetch();

            return $review ? ['success' => true, 'review' => $review] : ['success' => false, 'message' => 'Review not found'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error fetching review details: ' . $e->getMessage()];
        }
    }

    /**
     * Get rating distribution for a product or vendor
     */
    public function getRatingDistribution($entityId, $type)
    {
        try {
            if ($type === 'product') {
                $query = "
                    SELECT 
                        rating,
                        COUNT(*) as count,
                        (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM reviews WHERE product_id = ? AND is_approved = 1)) as percentage
                    FROM reviews 
                    WHERE product_id = ? AND is_approved = 1
                    GROUP BY rating 
                    ORDER BY rating DESC
                ";
                $params = [$entityId, $entityId];
            } else {
                $query = "
                    SELECT 
                        rating,
                        COUNT(*) as count,
                        (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM vendor_reviews WHERE vendor_id = ? AND is_approved = 1)) as percentage
                    FROM vendor_reviews 
                    WHERE vendor_id = ? AND is_approved = 1
                    GROUP BY rating 
                    ORDER BY rating DESC
                ";
                $params = [$entityId, $entityId];
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $distribution = $stmt->fetchAll();

            // Fill in missing ratings with 0
            $fullDistribution = [];
            for ($i = 5; $i >= 1; $i--) {
                $found = false;
                foreach ($distribution as $rating) {
                    if ($rating['rating'] == $i) {
                        $fullDistribution[] = [
                            'rating' => $i,
                            'count' => intval($rating['count']),
                            'percentage' => round(floatval($rating['percentage']), 1)
                        ];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $fullDistribution[] = [
                        'rating' => $i,
                        'count' => 0,
                        'percentage' => 0
                    ];
                }
            }

            return ['success' => true, 'distribution' => $fullDistribution];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error getting rating distribution: ' . $e->getMessage()];
        }
    }

    /**
     * Helper method to get vendor ID by user ID
     */
    private function getVendorIdByUserId($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT vendor_id FROM vendors WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            return $result ? $result['vendor_id'] : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Delete review (hard delete)
     */
    public function deleteReview($reviewId, $type)
    {
        try {
            if ($type === 'product') {
                // Hard delete for product reviews
                $stmt = $this->db->prepare("DELETE FROM reviews WHERE review_id = ?");
                $result = $stmt->execute([$reviewId]);
            } else {
                // Hard delete for vendor reviews
                $stmt = $this->db->prepare("DELETE FROM vendor_reviews WHERE vendor_review_id = ?");
                $result = $stmt->execute([$reviewId]);
            }

            return ['success' => $result, 'message' => $result ? 'Review deleted successfully' : 'Failed to delete review'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error deleting review: ' . $e->getMessage()];
        }
    }
}

?> 