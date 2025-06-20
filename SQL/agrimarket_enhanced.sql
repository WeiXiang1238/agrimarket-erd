-- Enhanced AgriMarket Database Schema with Role Control
-- This file includes all tables needed for the complete AgriMarket e-commerce system

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: agrimarket_solutions

-- Role Control System Tables

-- Roles table for system roles
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default roles
INSERT INTO `roles` (`role_id`, `role_name`, `description`, `is_active`) VALUES
(1, 'admin', 'System Administrator', 1),
(2, 'vendor', 'Product Vendor/Seller', 1),
(3, 'customer', 'Product Buyer/Customer', 1),
(4, 'staff', 'Platform Staff Member', 1);

-- Permissions table for granular permissions
CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default permissions
INSERT INTO `permissions` (`permission_id`, `permission_name`, `module`, `description`, `is_active`) VALUES
(1, 'user_management', 'admin', 'Manage system users', 1),
(2, 'product_management', 'vendor', 'Manage products', 1),
(3, 'order_management', 'vendor', 'Manage orders', 1),
(4, 'customer_management', 'admin', 'Manage customers', 1),
(5, 'vendor_management', 'admin', 'Manage vendors', 1),
(6, 'staff_management', 'admin', 'Manage staff members', 1),
(7, 'view_reports', 'admin', 'View system reports', 1),
(8, 'review_moderation', 'admin', 'Moderate reviews', 1),
(9, 'place_orders', 'customer', 'Place orders', 1),
(10, 'write_reviews', 'customer', 'Write product reviews', 1),
(11, 'vendor_reports', 'vendor', 'View vendor reports', 1),
(12, 'subscription_management', 'vendor', 'Manage subscriptions', 1),
(13, 'notification_management', 'admin', 'Manage notifications', 1),
(14, 'task_management', 'staff', 'Manage assigned tasks', 1);

-- Role permissions mapping
CREATE TABLE `role_permissions` (
  `role_permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`role_permission_id`),
  UNIQUE KEY `role_permission_unique` (`role_id`, `permission_id`),
  KEY `role_id` (`role_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Assign permissions to roles
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
-- Admin permissions
(1, 1), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 13),
-- Vendor permissions  
(2, 2), (2, 3), (2, 11), (2, 12),
-- Customer permissions
(3, 9), (3, 10),
-- Staff permissions
(4, 14);

-- User roles mapping (for multiple roles per user if needed)
CREATE TABLE `user_roles` (
  `user_role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Enhanced Product Categories table
CREATE TABLE `product_categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_category_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert enhanced agricultural categories
INSERT INTO `product_categories` (`category_id`, `name`, `slug`, `description`, `parent_category_id`, `sort_order`, `is_active`) VALUES
(1, 'Livestock', 'livestock', 'Cattle, poultry, hogs and other farm animals', NULL, 1, 1),
(2, 'Crops', 'crops', 'Corn, soybeans, hay and other agricultural crops', NULL, 2, 1),
(3, 'Forestry Products', 'forestry-products', 'Almonds, walnuts and other edible forest products', NULL, 3, 1),
(4, 'Dairy Products', 'dairy-products', 'Milk and dairy-based products', NULL, 4, 1),
(5, 'Aquaculture', 'aquaculture', 'Fish farming and aquatic products', NULL, 5, 1),
(6, 'Miscellaneous', 'miscellaneous', 'Honey and other agricultural products', NULL, 6, 1),
(7, 'Cattle', 'cattle', 'Beef cattle, dairy cattle', 1, 1, 1),
(8, 'Poultry', 'poultry', 'Chickens, ducks, turkeys', 1, 2, 1),
(9, 'Pigs', 'pigs', 'Hogs and swine', 1, 3, 1),
(10, 'Grains', 'grains', 'Corn, wheat, rice', 2, 1, 1),
(11, 'Vegetables', 'vegetables', 'Fresh vegetables', 2, 2, 1),
(12, 'Fruits', 'fruits', 'Fresh fruits', 2, 3, 1);

-- Shopping Cart table
CREATE TABLE `shopping_cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Customer addresses for shipping
CREATE TABLE `customer_addresses` (
  `address_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `address_type` enum('shipping','billing','both') DEFAULT 'shipping',
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `street_address` varchar(255) NOT NULL,
  `street_address_2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(100) DEFAULT 'Malaysia',
  `phone` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Payment methods
CREATE TABLE `payment_methods` (
  `payment_method_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `processing_fee_percent` decimal(5,2) DEFAULT 0.00,
  `min_amount` decimal(10,2) DEFAULT 0.00,
  `max_amount` decimal(10,2) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `payment_methods` (`payment_method_id`, `name`, `code`, `description`, `processing_fee_percent`, `sort_order`) VALUES
(1, 'Credit Card', 'CREDIT_CARD', 'Visa, MasterCard, American Express', 2.50, 1),
(2, 'Debit Card', 'DEBIT_CARD', 'Bank debit cards', 1.50, 2),
(3, 'Bank Transfer', 'BANK_TRANSFER', 'Direct bank transfer', 0.00, 3),
(4, 'Mobile Payment', 'MOBILE_PAYMENT', 'Mobile wallet payments', 1.00, 4),
(5, 'Cash on Delivery', 'COD', 'Pay when you receive', 0.00, 5);

-- Payment transactions
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'MYR',
  `transaction_id` varchar(100) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','cancelled','refunded') DEFAULT 'pending',
  `gateway_response` text DEFAULT NULL,
  `failure_reason` text DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Product images
CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  `image_size` int(11) DEFAULT NULL,
  `image_type` varchar(50) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Vendor reviews (separate from product reviews)
CREATE TABLE `vendor_reviews` (
  `vendor_review_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `pros` text DEFAULT NULL,
  `cons` text DEFAULT NULL,
  `is_verified_purchase` tinyint(1) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Page visit tracking for analytics
CREATE TABLE `page_visits` (
  `visit_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `page_url` varchar(500) NOT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `referrer_url` varchar(500) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `visit_duration` int(11) DEFAULT NULL,
  `visit_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Enhanced notification settings
CREATE TABLE `notification_settings` (
  `setting_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_updates` tinyint(1) DEFAULT 1,
  `promotional_emails` tinyint(1) DEFAULT 1,
  `low_stock_alerts` tinyint(1) DEFAULT 1,
  `sms_notifications` tinyint(1) DEFAULT 0,
  `push_notifications` tinyint(1) DEFAULT 1,
  `newsletter_subscription` tinyint(1) DEFAULT 1,
  `price_alerts` tinyint(1) DEFAULT 0,
  `new_product_alerts` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Customer preferences/favorites with enhanced tracking
CREATE TABLE `customer_preferences` (
  `preference_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `preference_type` enum('favorite_product','favorite_vendor','favorite_category','wishlist','compare') NOT NULL,
  `notes` text DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Product attributes for flexible product specifications
CREATE TABLE `product_attributes` (
  `attribute_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attribute_name` varchar(100) NOT NULL,
  `attribute_value` text NOT NULL,
  `attribute_type` enum('text','number','date','boolean','select') DEFAULT 'text',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Vendor business settings
CREATE TABLE `vendor_settings` (
  `setting_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `business_license` varchar(100) DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `return_policy` text DEFAULT NULL,
  `shipping_policy` text DEFAULT NULL,
  `minimum_order_amount` decimal(10,2) DEFAULT 0.00,
  `free_shipping_threshold` decimal(10,2) DEFAULT NULL,
  `processing_time_days` int(11) DEFAULT 3,
  `auto_accept_orders` tinyint(1) DEFAULT 0,
  `vacation_mode` tinyint(1) DEFAULT 0,
  `vacation_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- System audit log for tracking changes
CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) NOT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes and constraints for new tables

-- Roles table
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

-- Permissions table
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`),
  ADD KEY `module` (`module`);



-- User roles table
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_role_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

-- Product categories
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_category_id` (`parent_category_id`),
  ADD KEY `is_active` (`is_active`);

-- Shopping cart
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `customer_product_unique` (`customer_id`, `product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

-- Customer addresses
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `is_default` (`is_default`);

-- Payment methods
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_method_id`),
  ADD UNIQUE KEY `code` (`code`);

-- Payments
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `payment_method_id` (`payment_method_id`),
  ADD KEY `status` (`status`);

-- Product images
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `is_primary` (`is_primary`);

-- Vendor reviews
ALTER TABLE `vendor_reviews`
  ADD PRIMARY KEY (`vendor_review_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `is_approved` (`is_approved`);

-- Page visits
ALTER TABLE `page_visits`
  ADD PRIMARY KEY (`visit_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `visit_date` (`visit_date`);

-- Notification settings
ALTER TABLE `notification_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

-- Customer preferences
ALTER TABLE `customer_preferences`
  ADD PRIMARY KEY (`preference_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `preference_type` (`preference_type`);

-- Product attributes
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`attribute_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `attribute_name` (`attribute_name`);

-- Vendor settings
ALTER TABLE `vendor_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `vendor_id` (`vendor_id`);

-- Audit logs
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_name` (`table_name`),
  ADD KEY `action` (`action`),
  ADD KEY `created_at` (`created_at`);

-- AUTO_INCREMENT settings
ALTER TABLE `roles` MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `permissions` MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `role_permissions` MODIFY `role_permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
ALTER TABLE `user_roles` MODIFY `user_role_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `product_categories` MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `shopping_cart` MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `customer_addresses` MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `payment_methods` MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `payments` MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `product_images` MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vendor_reviews` MODIFY `vendor_review_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `page_visits` MODIFY `visit_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `notification_settings` MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `customer_preferences` MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `product_attributes` MODIFY `attribute_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vendor_settings` MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `audit_logs` MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

-- Foreign key constraints
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`parent_category_id`) REFERENCES `product_categories` (`category_id`) ON DELETE SET NULL;

ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `shopping_cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shopping_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`payment_method_id`);

ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

ALTER TABLE `vendor_reviews`
  ADD CONSTRAINT `vendor_reviews_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE SET NULL;

ALTER TABLE `page_visits`
  ADD CONSTRAINT `page_visits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `notification_settings`
  ADD CONSTRAINT `notification_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `customer_preferences`
  ADD CONSTRAINT `customer_preferences_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_preferences_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_preferences_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_preferences_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`) ON DELETE CASCADE;

ALTER TABLE `product_attributes`
  ADD CONSTRAINT `product_attributes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

ALTER TABLE `vendor_settings`
  ADD CONSTRAINT `vendor_settings_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE;

ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */; 