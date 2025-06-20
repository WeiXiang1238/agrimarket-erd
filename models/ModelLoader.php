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
        
        // Product Management
        'Product' => 'Product.php',
        'ProductCategory' => 'ProductCategory.php',
        'ProductImage' => 'ProductImage.php',
        'ProductAttribute' => 'ProductAttribute.php',
        
        // Order Management
        'Order' => 'Order.php',
        'OrderItem' => 'OrderItem.php',
        'ShoppingCart' => 'ShoppingCart.php',
        
        // Customer Data
        'CustomerAddress' => 'CustomerAddress.php',
        'CustomerPreference' => 'CustomerPreference.php',
        
        // Payment System
        'PaymentMethod' => 'PaymentMethod.php',
        'Payment' => 'Payment.php',
        
        // Reviews and Ratings
        'Review' => 'Review.php',
        'VendorReview' => 'VendorReview.php',
        
        // Notifications
        'Notification' => 'Notification.php',
        'NotificationSetting' => 'NotificationSetting.php',
        
        // Vendor Settings
        'VendorSetting' => 'VendorSetting.php',
        
        // Analytics and Tracking
        'Analytics' => 'Analytics.php',
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
            'User Types' => ['Customer', 'Vendor', 'Staff'],
            'Product Management' => ['Product', 'ProductCategory', 'ProductImage', 'ProductAttribute'],
            'Order Management' => ['Order', 'OrderItem', 'ShoppingCart'],
            'Customer Data' => ['CustomerAddress', 'CustomerPreference'],
            'Payment System' => ['PaymentMethod', 'Payment'],
            'Reviews and Ratings' => ['Review', 'VendorReview'],
            'Notifications' => ['Notification', 'NotificationSetting'],
            'Vendor Settings' => ['VendorSetting'],
            'Analytics and Tracking' => ['Analytics', 'SearchLog', 'PageVisit', 'AuditLog']
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

?> 