-- Database Migration Script
-- This script adds missing columns and tables to make the existing database compatible with the enhanced services

-- Add missing columns to users table
ALTER TABLE `users` 
ADD COLUMN `phone` varchar(20) DEFAULT NULL AFTER `email`,
ADD COLUMN `profile_picture` varchar(255) DEFAULT NULL AFTER `role`,
ADD COLUMN `is_active` tinyint(1) DEFAULT 1 AFTER `profile_picture`,
ADD COLUMN `email_verified_at` timestamp NULL DEFAULT NULL AFTER `is_active`,
ADD COLUMN `phone_verified_at` timestamp NULL DEFAULT NULL AFTER `email_verified_at`,
ADD COLUMN `last_login_at` timestamp NULL DEFAULT NULL AFTER `phone_verified_at`,
ADD COLUMN `remember_token` varchar(100) DEFAULT NULL AFTER `last_login_at`,
ADD COLUMN `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() AFTER `created_at`;

-- Create roles table if not exists
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default roles
INSERT IGNORE INTO `roles` (`role_id`, `role_name`, `display_name`, `description`) VALUES
(1, 'admin', 'Administrator', 'Full system access'),
(2, 'vendor', 'Vendor', 'Can manage products and orders'),
(3, 'customer', 'Customer', 'Can browse and purchase products'),
(4, 'staff', 'Staff', 'Can assist with operations');

-- Create permissions table if not exists
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(100) NOT NULL,
  `display_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `permission_name` (`permission_name`),
  KEY `module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default permissions
INSERT IGNORE INTO `permissions` (`permission_id`, `permission_name`, `display_name`, `module`) VALUES
(1, 'manage_users', 'Manage Users', 'user_management'),
(2, 'manage_products', 'Manage Products', 'product_management'),
(3, 'manage_orders', 'Manage Orders', 'order_management'),
(4, 'view_analytics', 'View Analytics', 'analytics'),
(5, 'manage_system', 'Manage System', 'system'),
(6, 'manage_vendors', 'Manage Vendors', 'vendor_management'),
(7, 'manage_customers', 'Manage Customers', 'customer_management'),
(8, 'manage_staff', 'Manage Staff', 'staff_management'),
(9, 'place_orders', 'Place Orders', 'shopping'),
(10, 'view_orders', 'View Orders', 'shopping'),
(11, 'manage_inventory', 'Manage Inventory', 'inventory'),
(12, 'view_reports', 'View Reports', 'reporting'),
(13, 'manage_promotions', 'Manage Promotions', 'marketing'),
(14, 'customer_support', 'Customer Support', 'support');

-- Create role_permissions table if not exists
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`role_permission_id`),
  UNIQUE KEY `role_permission_unique` (`role_id`, `permission_id`),
  KEY `role_id` (`role_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Assign permissions to roles
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
-- Admin permissions (all)
(1, 1), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 13),
-- Vendor permissions  
(2, 2), (2, 3), (2, 11), (2, 12),
-- Customer permissions
(3, 9), (3, 10),
-- Staff permissions
(4, 14);

-- Create user_roles table if not exists
CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`user_role_id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Migrate existing user roles to new system
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`, `is_active`)
SELECT 
    u.user_id,
    CASE 
        WHEN u.role = 'admin' THEN 1
        WHEN u.role = 'vendor' THEN 2
        WHEN u.role = 'customer' THEN 3
        WHEN u.role = 'staff' THEN 4
        ELSE 3
    END as role_id,
    1 as is_active
FROM users u
WHERE u.user_id NOT IN (SELECT ur.user_id FROM user_roles ur WHERE ur.user_id = u.user_id);

-- Update all existing users to be active by default
UPDATE `users` SET `is_active` = 1 WHERE `is_active` IS NULL; 