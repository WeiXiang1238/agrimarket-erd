<?php

/**
 * Model Loader
 * Provides autoloading and factory pattern for model instantiation
 */
class ModelLoader
{
    private static $models = [];
    
    /**
     * Available model classes
     */
    private static $modelClasses = [
        // Core User Management
        'User' => 'User.php',
        'Role' => 'Role.php',
        'Permission' => 'Permission.php',
        'UserRole' => 'UserRole.php',
        'RolePermission' => 'RolePermission.php',
        
        // User Types
        'Customer' => 'Customer.php',
        'Vendor' => 'Vendor.php',
        'Staff' => 'Staff.php',
        'StaffTask' => 'StaffTask.php',
        
        // Product Management
        'Product' => 'Product.php',
        'ProductCategory' => 'ProductCategory.php',
        
        // Order Management
        'Order' => 'Order.php',
        'OrderItem' => 'OrderItem.php',
        'ShoppingCart' => 'ShoppingCart.php',
        
        // Customer Data
        'CustomerAddress' => 'CustomerAddress.php',
        
        // Payment System
        'PaymentMethod' => 'PaymentMethod.php',
        'Payment' => 'Payment.php',
        
        // Subscriptions
        'SubscriptionTier' => 'SubscriptionTier.php',
        'VendorSubscription' => 'VendorSubscription.php',
        
        // Reviews and Ratings
        'Review' => 'Review.php',
        'VendorReview' => 'VendorReview.php',
        
        // Notifications
        'Notification' => 'Notification.php',
        
        // Analytics and Tracking
        'SearchLog' => 'SearchLog.php',
        'PageVisit' => 'PageVisit.php',
        'AuditLog' => 'AuditLog.php'
    ];
    
    /**
     * Load and return model instance
     */
    public static function load($modelName)
    {
        if (!isset(self::$modelClasses[$modelName])) {
            throw new Exception("Model '{$modelName}' not found");
        }
        
        if (!isset(self::$models[$modelName])) {
            $filename = __DIR__ . '/' . self::$modelClasses[$modelName];
            
            if (!file_exists($filename)) {
                throw new Exception("Model file '{$filename}' not found");
            }
            
            require_once $filename;
            
            if (!class_exists($modelName)) {
                throw new Exception("Class '{$modelName}' not found in model file");
            }
            
            self::$models[$modelName] = new $modelName();
        }
        
        return self::$models[$modelName];
    }
    
    /**
     * Get available model names
     */
    public static function getAvailableModels()
    {
        return array_keys(self::$modelClasses);
    }
    
    /**
     * Get models by category
     */
    public static function getModelsByCategory()
    {
        return [
            'User Management' => ['User', 'Role', 'Permission', 'UserRole', 'RolePermission'],
            'User Types' => ['Customer', 'Vendor', 'Staff', 'StaffTask'],
            'Product Management' => ['Product', 'ProductCategory'],
            'Order Management' => ['Order', 'OrderItem', 'ShoppingCart'],
            'Customer Data' => ['CustomerAddress'],
            'Payment System' => ['PaymentMethod', 'Payment'],
            'Promotions and Discounts' => [],
            'Subscriptions' => ['SubscriptionTier', 'VendorSubscription'],
            'Reviews and Ratings' => ['Review', 'VendorReview'],
            'Notifications' => ['Notification'],
            'Vendor Settings' => [],
            'Analytics and Tracking' => ['SearchLog', 'PageVisit', 'AuditLog']
        ];
    }
    
    /**
     * Magic method to load models dynamically
     */
    public function __get($modelName)
    {
        return self::load($modelName);
    }
}

// Usage examples:
// $userModel = ModelLoader::load('User');
// $productModel = ModelLoader::load('Product');
// $orderModel = ModelLoader::load('Order');
// $categories = ModelLoader::getModelsByCategory(); 