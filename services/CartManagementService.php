<?php

require_once __DIR__ . '/../Db_Connect.php';
require_once __DIR__ . '/ShoppingCartService.php';
require_once __DIR__ . '/AuthService.php';
require_once __DIR__ . '/PaymentService.php';

/**
 * CartManagementService
 * Handles cart management operations and data processing for the frontend
 */
class CartManagementService
{
    private $db;
    private $cartService;
    private $authService;
    private $paymentService;
    
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
        
        $this->cartService = new ShoppingCartService();
        $this->authService = new AuthService();
        $this->paymentService = new PaymentService();
    }

    /**
     * Initialize shopping cart page data
     */
    public function initializeShoppingCart()
    {
        // Get authenticated user
        $currentUser = $this->authService->getCurrentUser();
        
        if (!$currentUser || $currentUser['role'] !== 'customer') {
            return [
                'success' => false,
                'redirect' => '/agrimarket-erd/v1/auth/login/',
                'message' => 'Customer authentication required'
            ];
        }

        // Get customer ID
        $customerId = $this->authService->getCustomerId($currentUser['user_id']);
        
        if (!$customerId) {
            return [
                'success' => false,
                'message' => 'Customer profile not found'
            ];
        }

        return [
            'success' => true,
            'customerId' => $customerId,
            'currentUser' => $currentUser
        ];
    }

    /**
     * Get complete cart data for display
     */
    public function getCartPageData($customerId)
    {
        try {
            // Get cart items
            $cartData = $this->cartService->getCartItems($customerId);
            $cartItems = $cartData['items'] ?? [];
            $cartSummary = $cartData['summary'] ?? [];

            // Get comparison list
            $comparisonData = $this->cartService->getComparisonList();
            $comparisonProducts = $comparisonData['products'] ?? [];

            // Get payment methods
            $paymentMethodsData = $this->paymentService->getAvailablePaymentMethods(
                $cartSummary['final_amount'] ?? 0
            );
            $paymentMethods = $paymentMethodsData['payment_methods'] ?? [];

            return [
                'success' => true,
                'cartItems' => $cartItems,
                'cartSummary' => $cartSummary,
                'comparisonProducts' => $comparisonProducts,
                'paymentMethods' => $paymentMethods,
                'isEmpty' => empty($cartItems)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load cart data: ' . $e->getMessage(),
                'cartItems' => [],
                'cartSummary' => [],
                'comparisonProducts' => [],
                'paymentMethods' => [],
                'isEmpty' => true
            ];
        }
    }

    /**
     * Handle AJAX cart actions
     */
    public function handleCartAction($action, $postData, $customerId)
    {
        try {
            switch ($action) {
                case 'update_quantity':
                    return $this->handleUpdateQuantity($postData, $customerId);
                    
                case 'remove_item':
                    return $this->handleRemoveItem($postData, $customerId);
                    
                case 'clear_cart':
                    return $this->handleClearCart($customerId);
                    
                case 'add_to_comparison':
                    return $this->handleAddToComparison($postData);
                    
                case 'remove_from_comparison':
                    return $this->handleRemoveFromComparison($postData);
                    
                case 'clear_comparison':
                    return $this->handleClearComparison();
                    
                default:
                    return ['success' => false, 'message' => 'Invalid action'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Action failed: ' . $e->getMessage()];
        }
    }

    private function handleUpdateQuantity($postData, $customerId)
    {
        $cartId = (int)($postData['cart_id'] ?? 0);
        $quantity = (int)($postData['quantity'] ?? 1);

        if ($cartId <= 0 || $quantity < 0) {
            return ['success' => false, 'message' => 'Invalid parameters'];
        }

        return $this->cartService->updateCartItem($customerId, $cartId, $quantity);
    }

    private function handleRemoveItem($postData, $customerId)
    {
        $cartId = (int)($postData['cart_id'] ?? 0);

        if ($cartId <= 0) {
            return ['success' => false, 'message' => 'Invalid cart item'];
        }

        return $this->cartService->removeFromCart($customerId, $cartId);
    }

    private function handleClearCart($customerId)
    {
        return $this->cartService->clearCart($customerId);
    }

    private function handleAddToComparison($postData)
    {
        $productId = (int)($postData['product_id'] ?? 0);

        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product'];
        }

        return $this->cartService->addToComparison($productId);
    }

    private function handleRemoveFromComparison($postData)
    {
        $productId = (int)($postData['product_id'] ?? 0);

        if ($productId <= 0) {
            return ['success' => false, 'message' => 'Invalid product'];
        }

        return $this->cartService->removeFromComparison($productId);
    }

    private function handleClearComparison()
    {
        return $this->cartService->clearComparison();
    }

    /**
     * Validate cart state and return warnings
     */
    public function validateCartState($cartItems)
    {
        $warnings = [];
        $outOfStockItems = 0;

        foreach ($cartItems as $item) {
            if ($item['availability_status'] !== 'available') {
                $outOfStockItems++;
            }
            
            if ($item['quantity'] > $item['stock_quantity']) {
                $warnings[] = "Quantity for {$item['product_name']} exceeds available stock";
            }
        }

        if ($outOfStockItems > 0) {
            $warnings[] = "$outOfStockItems item(s) are currently out of stock";
        }

        return $warnings;
    }

    /**
     * Get formatted cart summary for display
     */
    public function getFormattedCartSummary($cartSummary)
    {
        return [
            'subtotal' => number_format($cartSummary['total_amount'] ?? 0, 2),
            'shipping' => number_format($cartSummary['shipping_cost'] ?? 0, 2),
            'tax' => number_format($cartSummary['tax_amount'] ?? 0, 2),
            'total' => number_format($cartSummary['final_amount'] ?? 0, 2),
            'itemCount' => $cartSummary['available_items'] ?? 0
        ];
    }

    /**
     * Check if cart is valid for checkout
     */
    public function isCartValidForCheckout($cartItems, $cartSummary)
    {
        if (empty($cartItems)) {
            return ['valid' => false, 'message' => 'Cart is empty'];
        }

        $availableItems = 0;
        foreach ($cartItems as $item) {
            if ($item['availability_status'] === 'available') {
                $availableItems++;
            }
        }

        if ($availableItems === 0) {
            return ['valid' => false, 'message' => 'No available items in cart'];
        }

        if (($cartSummary['final_amount'] ?? 0) <= 0) {
            return ['valid' => false, 'message' => 'Invalid cart total'];
        }

        return ['valid' => true, 'message' => 'Cart is ready for checkout'];
    }
}

?> 