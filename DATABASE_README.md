# AgriMarket Solutions - Database Documentation

## Overview
This document provides comprehensive information about the database structure and implementation for AgriMarket Solutions, an e-commerce platform designed for agricultural products.

## Database Architecture

### Role-Based Access Control System

The system now includes a comprehensive role control mechanism:

#### Core Role Tables
- **`roles`** - System roles (admin, vendor, customer, staff)
- **`permissions`** - Granular permissions for different actions
- **`role_permissions`** - Maps permissions to roles
- **`user_roles`** - Maps users to roles (supports multiple roles per user)

#### Default Roles and Permissions

**Admin Role:**
- user_management
- customer_management  
- vendor_management
- staff_management
- view_reports
- review_moderation
- notification_management

**Vendor Role:**
- product_management
- order_management
- vendor_reports
- subscription_management

**Customer Role:**
- place_orders
- write_reviews

**Staff Role:**
- task_management

### Enhanced Database Schema

#### User Management
- **`users`** - Core user accounts with role assignment
- **`user_roles`** - Maps users to multiple roles
- **`customers`** - Customer-specific profile data
- **`vendors`** - Vendor business information and settings
- **`staff`** - Staff employment details

#### Product Catalog
- **`product_categories`** - Hierarchical category system with slugs
- **`products`** - Product information with enhanced tracking
- **`product_images`** - Multiple images per product
- **`product_attributes`** - Flexible product specifications

#### Order Management
- **`orders`** - Order transactions with enhanced tracking
- **`order_items`** - Individual order line items
- **`shopping_cart`** - Persistent shopping cart
- **`customer_addresses`** - Multiple shipping/billing addresses

#### Payment System
- **`payment_methods`** - Available payment options
- **`payments`** - Payment transaction records

#### Reviews and Ratings
- **`reviews`** - Product reviews with moderation
- **`vendor_reviews`** - Separate vendor rating system

#### Analytics and Tracking
- **`search_logs`** - Search behavior tracking
- **`page_visits`** - User behavior analytics
- **`audit_logs`** - System change tracking

#### Notifications
- **`notifications`** - User notifications system
- **`notification_settings`** - User notification preferences

#### Business Settings
- **`vendor_settings`** - Vendor-specific business configuration
- **`customer_preferences`** - Customer favorites and preferences

## Model Architecture

### Simplified Model Structure

Models now contain only table structure and basic properties:

```php
class User extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [...];
    protected $columns = [...];
    protected $relationships = [...];
}
```

### Service Layer

All business logic has been moved to service classes:

- **`UserService`** - User authentication, registration, role management
- **`ProductService`** - Product catalog operations
- **`OrderService`** - Order processing and management
- **`CustomerService`** - Customer profile and cart management
- **`VendorService`** - Vendor dashboard and operations
- **`NotificationService`** - Notification management
- **`AnalyticsService`** - Analytics and reporting

### Model Loader

The `ModelLoader` class provides automatic model loading:

```php
// Load models
$userModel = ModelLoader::load('User');
$productModel = ModelLoader::load('Product');

// Get available models
$models = ModelLoader::getAvailableModels();
```

## Installation Instructions

### 1. Database Setup
```bash
# Import the enhanced database structure
mysql -u username -p database_name < SQL/agrimarket_enhanced.sql
```

### 2. Database Configuration
Update `Db_Connect.php` with your database credentials:

```php
$host = 'localhost';
$dbname = 'agrimarket_solutions';
$username = 'your_username';
$password = 'your_password';
```

### 3. Model Usage
```php
// Include the model loader
require_once 'models/ModelLoader.php';

// Use service for business operations
require_once 'services/UserService.php';
$userService = new UserService();

// Authenticate user
$result = $userService->authenticate($email, $password);

// Check permissions
$hasPermission = $userService->hasPermission($userId, 'product_management');
```

## Key Features

### Role-Based Security
- Granular permission system
- Multiple roles per user
- Easy permission checking
- Audit trail for changes

### Enhanced Product Management
- Hierarchical categories
- Multiple product images
- Flexible attributes
- Stock tracking
- Sales analytics

### Advanced Order Processing
- Multi-step order workflow
- Payment integration
- Shipping tracking
- Order history

### Analytics and Reporting
- Search behavior tracking
- Page visit analytics
- Sales reporting
- User engagement metrics

### Notification System
- Multi-type notifications
- User preferences
- Automated alerts
- System notifications

## Security Features

- Password hashing using PHP's `password_hash()`
- Prepared statements for SQL injection prevention
- Soft delete functionality
- Role-based access control
- Session management
- Input validation and sanitization
- Audit logging for sensitive operations

## Performance Optimizations

- Database indexing on frequently queried columns
- Pagination for large datasets
- Optimized queries with appropriate JOINs
- Connection pooling via singleton pattern
- Caching for frequently accessed data

## API Usage Examples

### User Authentication
```php
$userService = new UserService();

// Register new user
$result = $userService->register([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'securepassword',
    'role' => 'customer'
]);

// Login user
$result = $userService->authenticate('john@example.com', 'securepassword');

// Check permissions
$canManageProducts = $userService->hasPermission($userId, 'product_management');
```

### Product Management
```php
$productService = new ProductService();

// Search products
$products = $productService->searchProducts('organic tomatoes', [
    'category' => 'vegetables',
    'min_price' => 10,
    'max_price' => 50
]);

// Get product details
$product = $productService->getProductDetails($productId);
```

### Order Processing
```php
$orderService = new OrderService();

// Create order from cart
$result = $orderService->createOrderFromCart($customerId, $shippingAddress);

// Update order status
$orderService->updateOrderStatus($orderId, 'shipped', $trackingNumber);
```

## Support and Maintenance

### Regular Maintenance Tasks
- Clean up old notifications (30+ days)
- Archive completed orders (yearly)
- Update search indexes
- Backup database regularly
- Monitor performance metrics

### Troubleshooting
- Check database connection in `Db_Connect.php`
- Verify table permissions
- Review error logs for issues
- Test role assignments
- Validate data integrity

## Development Guidelines

### Adding New Features
1. Create model with table structure only
2. Implement business logic in service layer
3. Add appropriate permissions
4. Update role assignments
5. Add proper error handling
6. Include audit logging

### Best Practices
- Keep models simple (structure only)
- Use services for business logic
- Implement proper error handling
- Follow naming conventions
- Document all methods
- Use prepared statements
- Validate all inputs

## Database Schema Diagram

The complete database schema includes 25+ tables with proper relationships and constraints. Key relationships:

- Users → User_Roles → Roles → Role_Permissions → Permissions
- Users → Customers/Vendors/Staff (inheritance)
- Vendors → Products → Order_Items → Orders → Customers
- Products → Reviews ← Customers
- Users → Notifications
- Orders → Payments ← Payment_Methods

This structure supports a complete e-commerce platform with advanced role management, analytics, and business intelligence capabilities. 