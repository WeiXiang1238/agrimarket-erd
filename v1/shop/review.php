<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../models/ModelLoader.php';
require_once __DIR__ . '/../../services/AuthService.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$authService = new AuthService();
$userWithRoles = $authService->getCurrentUserWithRoles();

if (!$userWithRoles) {
    echo json_encode(['success' => false, 'message' => 'Authentication failed']);
    exit;
}

$userId = $_SESSION['user_id'];
$isCustomer = $userWithRoles['isCustomer'];
$customerId = $userWithRoles['customerId'];
$userRole = $_SESSION['user_role'] ?? null; // For backward compatibility

$Review = ModelLoader::load('Review');
$VendorReview = ModelLoader::load('VendorReview');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'submit_product_review':
        if (!$isCustomer || !$customerId) {
            echo json_encode(['success' => false, 'message' => 'Only customers can submit reviews']);
            exit;
        }
        $productId = (int)($_POST['product_id'] ?? 0);
        $orderId = (int)($_POST['order_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $comment = trim($_POST['comment'] ?? '');
        $pros = trim($_POST['pros'] ?? '');
        $cons = trim($_POST['cons'] ?? '');
        
        // Debug logging
        error_log("Product Review Submission - Product ID: $productId, Order ID: $orderId, Rating: $rating, Comment: '$comment'");
        
        if (!$productId || !$orderId || !$rating || !$comment) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields: product_id, order_id, rating, and comment are required']);
            exit;
        }
        $reviewData = [
            'product_id' => $productId,
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'is_verified_purchase' => 1,
            'is_approved' => 0
        ];
        
        // Add pros and cons if provided
        if ($pros) {
            $reviewData['pros'] = $pros;
        }
        if ($cons) {
            $reviewData['cons'] = $cons;
        }
        
        $reviewId = $Review->create($reviewData);
        echo json_encode(['success' => true, 'review_id' => $reviewId]);
        break;
    case 'submit_vendor_review':
        if (!$isCustomer || !$customerId) {
            echo json_encode(['success' => false, 'message' => 'Only customers can submit reviews']);
            exit;
        }
        $vendorId = (int)($_POST['vendor_id'] ?? 0);
        $orderId = (int)($_POST['order_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $comment = trim($_POST['comment'] ?? '');
        $pros = trim($_POST['pros'] ?? '');
        $cons = trim($_POST['cons'] ?? '');
        
        // Debug logging
        error_log("Vendor Review Submission - Vendor ID: $vendorId, Order ID: $orderId, Rating: $rating, Comment: '$comment'");
        
        if (!$vendorId || !$orderId || !$rating || !$comment) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields: vendor_id, order_id, rating, and comment are required']);
            exit;
        }
        
        $reviewData = [
            'vendor_id' => $vendorId,
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'is_verified_purchase' => 1,
            'is_approved' => 0
        ];
        
        // Add pros and cons if provided
        if ($pros) {
            $reviewData['pros'] = $pros;
        }
        if ($cons) {
            $reviewData['cons'] = $cons;
        }
        
        $reviewId = $VendorReview->create($reviewData);
        echo json_encode(['success' => true, 'review_id' => $reviewId]);
        break;
    case 'get_product_reviews':
        $productId = (int)($_GET['product_id'] ?? 0);
        $reviews = $Review->findAll(['product_id' => $productId, 'is_approved' => 1]);
        echo json_encode(['success' => true, 'reviews' => $reviews]);
        break;
    case 'get_vendor_reviews':
        $vendorId = (int)($_GET['vendor_id'] ?? 0);
        $reviews = $VendorReview->findAll(['vendor_id' => $vendorId, 'is_approved' => 1]);
        echo json_encode(['success' => true, 'reviews' => $reviews]);
        break;
    case 'get_customer_reviews':
        if (!$isCustomer || !$customerId) {
            echo json_encode(['success' => false, 'message' => 'Only customers can view their reviews']);
            exit;
        }
        
        try {
            // Get customer's product reviews
            $productReviews = $Review->findAll(['customer_id' => $customerId]);
            
            // Get customer's vendor reviews  
            $vendorReviews = $VendorReview->findAll(['customer_id' => $customerId]);
            
            // Combine and format reviews
            $allReviews = [];
            
            // Add product reviews
            foreach ($productReviews as $review) {
                $review['review_type'] = 'product';
                $review['product_name'] = $review['product_name'] ?? 'Unknown Product';
                $allReviews[] = $review;
            }
            
            // Add vendor reviews
            foreach ($vendorReviews as $review) {
                $review['review_type'] = 'vendor';
                $review['vendor_name'] = $review['vendor_name'] ?? 'Unknown Vendor';
                $allReviews[] = $review;
            }
            
            // Sort by created_at descending
            usort($allReviews, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            echo json_encode(['success' => true, 'reviews' => $allReviews]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error loading reviews: ' . $e->getMessage()]);
        }
        break;
    case 'moderate_review':
        if (!$userWithRoles['isAdmin'] && !$userWithRoles['isStaff']) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        $reviewId = (int)($_POST['review_id'] ?? 0);
        $approve = (int)($_POST['approve'] ?? 0);
        $type = $_POST['type'] ?? 'product';
        if ($type === 'product') {
            $Review->update($reviewId, [
                'is_approved' => $approve,
                'approved_by' => $userId,
                'approved_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $VendorReview->update($reviewId, [
                'is_approved' => $approve,
                'approved_by' => $userId,
                'approved_at' => date('Y-m-d H:i:s')
            ]);
        }
        echo json_encode(['success' => true]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
} 