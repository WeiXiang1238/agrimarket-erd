<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../models/ModelLoader.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? null;
$customerId = $_SESSION['customer_id'] ?? null;

$Review = ModelLoader::load('Review');
$VendorReview = ModelLoader::load('VendorReview');

action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'submit_product_review':
        if ($userRole !== 'customer') {
            echo json_encode(['success' => false, 'message' => 'Only customers can submit reviews']);
            exit;
        }
        $productId = (int)($_POST['product_id'] ?? 0);
        $orderId = (int)($_POST['order_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $comment = trim($_POST['comment'] ?? '');
        if (!$productId || !$orderId || !$rating) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }
        $reviewId = $Review->create([
            'product_id' => $productId,
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'is_verified_purchase' => 1,
            'is_approved' => 0
        ]);
        echo json_encode(['success' => true, 'review_id' => $reviewId]);
        break;
    case 'submit_vendor_review':
        if ($userRole !== 'customer') {
            echo json_encode(['success' => false, 'message' => 'Only customers can submit reviews']);
            exit;
        }
        $vendorId = (int)($_POST['vendor_id'] ?? 0);
        $orderId = (int)($_POST['order_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $comment = trim($_POST['comment'] ?? '');
        if (!$vendorId || !$orderId || !$rating) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }
        $reviewId = $VendorReview->create([
            'vendor_id' => $vendorId,
            'customer_id' => $customerId,
            'order_id' => $orderId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment,
            'is_verified_purchase' => 1,
            'is_approved' => 0
        ]);
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
    case 'moderate_review':
        if (!in_array($userRole, ['admin', 'staff'])) {
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