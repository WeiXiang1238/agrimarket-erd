<?php

require_once __DIR__ . '/../Db_Connect.php';

/**
 * ShoppingCartService
 * Handles shopping cart operations and product comparison features
 */
class ShoppingCartService
{
    private $db;
    
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
    }
    
    /**
     * Add item to shopping cart
     */
    public function addToCart($customerId, $productId, $quantity = 1)
    {
        try {
            // Check if product exists and is available
            $stmt = $this->db->prepare("
                SELECT product_id, name, selling_price, stock_quantity, is_archive
                FROM products 
                WHERE product_id = ?
            ");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            
            if (!$product || $product['is_archive'] == 1) {
                return ['success' => false, 'message' => 'Product not found or unavailable'];
            }
            
            if ($product['stock_quantity'] < $quantity) {
                return ['success' => false, 'message' => 'Insufficient stock available'];
            }
            
            // Check if item already exists in cart
            $stmt = $this->db->prepare("
                SELECT cart_id, quantity FROM shopping_cart 
                WHERE customer_id = ? AND product_id = ?
            ");
            $stmt->execute([$customerId, $productId]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                $newQuantity = $existing['quantity'] + $quantity;
                
                // Check total quantity against stock
                if ($product['stock_quantity'] < $newQuantity) {
                    return ['success' => false, 'message' => 'Total quantity exceeds available stock'];
                }
                
                // Update quantity
                $stmt = $this->db->prepare("
                    UPDATE shopping_cart 
                    SET quantity = ?, updated_at = NOW()
                    WHERE cart_id = ?
                ");
                $stmt->execute([$newQuantity, $existing['cart_id']]);
                
                return ['success' => true, 'message' => 'Cart updated successfully'];
            } else {
                // Add new item
                $stmt = $this->db->prepare("
                    INSERT INTO shopping_cart (customer_id, product_id, quantity)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$customerId, $productId, $quantity]);
                
                return ['success' => true, 'message' => 'Item added to cart successfully'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to add item to cart: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update cart item quantity
     */
    public function updateCartItem($customerId, $cartId, $quantity)
    {
        try {
            if ($quantity <= 0) {
                return $this->removeFromCart($customerId, $cartId);
            }
            
            // Check if cart item belongs to customer and get product info
            $stmt = $this->db->prepare("
                SELECT sc.cart_id, sc.product_id, p.stock_quantity, p.name
                FROM shopping_cart sc
                LEFT JOIN products p ON sc.product_id = p.product_id
                WHERE sc.cart_id = ? AND sc.customer_id = ?
            ");
            $stmt->execute([$cartId, $customerId]);
            $cartItem = $stmt->fetch();
            
            if (!$cartItem) {
                return ['success' => false, 'message' => 'Cart item not found'];
            }
            
            if ($cartItem['stock_quantity'] < $quantity) {
                return ['success' => false, 'message' => 'Requested quantity exceeds available stock'];
            }
            
            $stmt = $this->db->prepare("
                UPDATE shopping_cart 
                SET quantity = ?, updated_at = NOW()
                WHERE cart_id = ?
            ");
            $stmt->execute([$quantity, $cartId]);
            
            return ['success' => true, 'message' => 'Cart updated successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to update cart: ' . $e->getMessage()];
        }
    }
    
    /**
     * Remove item from cart
     */
    public function removeFromCart($customerId, $cartId)
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM shopping_cart 
                WHERE cart_id = ? AND customer_id = ?
            ");
            $stmt->execute([$cartId, $customerId]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Item removed from cart'];
            } else {
                return ['success' => false, 'message' => 'Cart item not found'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to remove item from cart: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get customer's cart items
     */
    public function getCartItems($customerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    sc.cart_id,
                    sc.customer_id,
                    sc.product_id,
                    sc.quantity,
                    sc.added_at,
                    p.name as product_name,
                    p.description,
                    p.selling_price,
                    p.base_price,
                    p.stock_quantity,
                    p.image_path,
                    p.category,
                    p.packaging,
                    p.is_discounted,
                    p.discount_percent,
                    v.business_name as vendor_name,
                    v.vendor_id,
                    (sc.quantity * p.selling_price) as subtotal,
                    CASE 
                        WHEN p.stock_quantity >= sc.quantity THEN 'available'
                        ELSE 'out_of_stock'
                    END as availability_status
                FROM shopping_cart sc
                LEFT JOIN products p ON sc.product_id = p.product_id
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                WHERE sc.customer_id = ? AND p.is_archive = 0
                ORDER BY sc.added_at DESC
            ");
            
            $stmt->execute([$customerId]);
            $items = $stmt->fetchAll();
            
            // Calculate totals
            $totalItems = 0;
            $totalAmount = 0;
            $availableItems = 0;
            
            foreach ($items as &$item) {
                $totalItems += $item['quantity'];
                if ($item['availability_status'] === 'available') {
                    $totalAmount += $item['subtotal'];
                    $availableItems += $item['quantity'];
                }
                
                // Format image path
                if ($item['image_path'] && !str_starts_with($item['image_path'], 'http')) {
                    $item['image_url'] = '/agrimarket-erd/' . ltrim($item['image_path'], '/');
                } else {
                    $item['image_url'] = $item['image_path'] ?: '/uploads/products/default-product.jpg';
                }
            }
            
            return [
                'success' => true,
                'items' => $items,
                'summary' => [
                    'total_items' => $totalItems,
                    'available_items' => $availableItems,
                    'total_amount' => $totalAmount,
                    'shipping_cost' => 10.00, // Default shipping
                    'tax_amount' => $totalAmount * 0.06, // 6% tax
                    'final_amount' => $totalAmount + 10.00 + ($totalAmount * 0.06)
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch cart items: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get cart summary (totals only) for a customer
     */
    public function getCartSummary($customerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    sc.quantity,
                    p.selling_price,
                    CASE 
                        WHEN p.stock_quantity >= sc.quantity THEN 'available'
                        ELSE 'out_of_stock'
                    END as availability_status
                FROM shopping_cart sc
                LEFT JOIN products p ON sc.product_id = p.product_id
                WHERE sc.customer_id = ? AND p.is_archive = 0
            ");
            
            $stmt->execute([$customerId]);
            $items = $stmt->fetchAll();
            
            // Calculate totals (reusing logic from getCartItems)
            $totalItems = 0;
            $totalAmount = 0;
            $availableItems = 0;
            
            foreach ($items as $item) {
                $totalItems += $item['quantity'];
                if ($item['availability_status'] === 'available') {
                    $subtotal = $item['quantity'] * $item['selling_price'];
                    $totalAmount += $subtotal;
                    $availableItems += $item['quantity'];
                }
            }
            
            return [
                'success' => true,
                'summary' => [
                    'total_items' => $totalItems,
                    'available_items' => $availableItems,
                    'total_amount' => $totalAmount,
                    'shipping_cost' => 10.00, // Default shipping
                    'tax_amount' => $totalAmount * 0.06, // 6% tax
                    'final_amount' => $totalAmount + 10.00 + ($totalAmount * 0.06)
                ]
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to fetch cart summary: ' . $e->getMessage()];
        }
    }
    
    /**
     * Clear entire cart
     */
    public function clearCart($customerId)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM shopping_cart WHERE customer_id = ?");
            $stmt->execute([$customerId]);
            
            return ['success' => true, 'message' => 'Cart cleared successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to clear cart: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get cart item count for a customer
     */
    public function getCartItemCount($customerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COALESCE(SUM(quantity), 0) as total_items
                FROM shopping_cart sc
                LEFT JOIN products p ON sc.product_id = p.product_id
                WHERE sc.customer_id = ? AND p.is_archive = 0
            ");
            $stmt->execute([$customerId]);
            $result = $stmt->fetch();
            
            return [
                'success' => true,
                'count' => (int)$result['total_items']
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'count' => 0];
        }
    }
    
    /**
     * Compare products (Product Comparison Feature)
     */
    public function compareProducts($productIds)
    {
        try {
            if (empty($productIds) || count($productIds) < 2) {
                return ['success' => false, 'message' => 'At least 2 products required for comparison'];
            }
            
            if (count($productIds) > 5) {
                return ['success' => false, 'message' => 'Maximum 5 products can be compared at once'];
            }
            
            $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    v.business_name as vendor_name,
                    vu.email as vendor_email,
                    pc.name as category_name,
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(r.review_id) as review_count,
                    CASE 
                        WHEN p.stock_quantity > 0 THEN 'In Stock'
                        ELSE 'Out of Stock'
                    END as stock_status
                FROM products p
                LEFT JOIN vendors v ON p.vendor_id = v.vendor_id
                LEFT JOIN users vu ON v.user_id = vu.user_id
                LEFT JOIN product_categories pc ON p.category_id = pc.category_id
                LEFT JOIN reviews r ON p.product_id = r.product_id
                WHERE p.product_id IN ($placeholders) AND p.is_archive = 0
                GROUP BY p.product_id
                ORDER BY p.selling_price ASC
            ");
            
            $stmt->execute($productIds);
            $products = $stmt->fetchAll();
            
            if (count($products) < 2) {
                return ['success' => false, 'message' => 'Some products not found or unavailable'];
            }
            
            // Add comparison metrics
            $comparison = [
                'products' => $products,
                'comparison_metrics' => $this->generateComparisonMetrics($products)
            ];
            
            return ['success' => true, 'comparison' => $comparison];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Failed to compare products: ' . $e->getMessage()];
        }
    }
    
    /**
     * Add product to comparison list (stored in session)
     */
    public function addToComparison($productId)
    {
        if (!isset($_SESSION['product_comparison'])) {
            $_SESSION['product_comparison'] = [];
        }
        
        if (count($_SESSION['product_comparison']) >= 5) {
            return ['success' => false, 'message' => 'Maximum 5 products can be compared'];
        }
        
        if (!in_array($productId, $_SESSION['product_comparison'])) {
            $_SESSION['product_comparison'][] = $productId;
        }
        
        return [
            'success' => true,
            'message' => 'Product added to comparison',
            'comparison_count' => count($_SESSION['product_comparison'])
        ];
    }
    
    /**
     * Remove product from comparison list
     */
    public function removeFromComparison($productId)
    {
        if (isset($_SESSION['product_comparison'])) {
            $_SESSION['product_comparison'] = array_values(
                array_filter($_SESSION['product_comparison'], function($id) use ($productId) {
                    return $id != $productId;
                })
            );
        }
        
        return [
            'success' => true,
            'message' => 'Product removed from comparison',
            'comparison_count' => count($_SESSION['product_comparison'] ?? [])
        ];
    }
    
    /**
     * Get current comparison list
     */
    public function getComparisonList()
    {
        $productIds = $_SESSION['product_comparison'] ?? [];
        
        if (empty($productIds)) {
            return ['success' => true, 'products' => [], 'count' => 0];
        }
        
        return $this->compareProducts($productIds);
    }
    
    /**
     * Clear comparison list
     */
    public function clearComparison()
    {
        $_SESSION['product_comparison'] = [];
        return ['success' => true, 'message' => 'Comparison list cleared'];
    }
    
    // Helper methods
    
    private function generateComparisonMetrics($products)
    {
        if (empty($products) || count($products) < 2) {
            return [];
        }
        
        $prices = array_column($products, 'selling_price');
        $ratings = array_column($products, 'avg_rating');
        $stocks = array_column($products, 'stock_quantity');
        
        return [
            'price_range' => [
                'min' => min($prices),
                'max' => max($prices),
                'average' => array_sum($prices) / count($prices)
            ],
            'rating_range' => [
                'min' => min($ratings),
                'max' => max($ratings),
                'average' => array_sum($ratings) / count($ratings)
            ],
            'stock_range' => [
                'min' => min($stocks),
                'max' => max($stocks),
                'total' => array_sum($stocks)
            ],
            'best_value' => $this->findBestValue($products),
            'highest_rated' => $this->findHighestRated($products),
            'lowest_price' => $this->findLowestPrice($products)
        ];
    }
    
    private function findBestValue($products)
    {
        $bestProduct = null;
        $bestScore = 0;
        
        foreach ($products as $product) {
            if ($product['selling_price'] > 0) {
                // Simple value score: rating / price ratio
                $score = ($product['avg_rating'] ?: 1) / $product['selling_price'];
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestProduct = $product;
                }
            }
        }
        
        return $bestProduct;
    }
    
    private function findHighestRated($products)
    {
        return array_reduce($products, function($highest, $product) {
            return ($product['avg_rating'] > ($highest['avg_rating'] ?? 0)) ? $product : $highest;
        });
    }
    
    private function findLowestPrice($products)
    {
        return array_reduce($products, function($lowest, $product) {
            return ($product['selling_price'] < ($lowest['selling_price'] ?? PHP_FLOAT_MAX)) ? $product : $lowest;
        });
    }
}

?> 