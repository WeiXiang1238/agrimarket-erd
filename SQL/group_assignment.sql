-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 04, 2025 at 08:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `group_assignment`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `user_id`, `action`, `table_name`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'Vendor deactivated', 'vendors', 4, NULL, NULL, NULL, NULL, '2025-06-21 14:56:15'),
(2, 1, 'Vendor activated', 'vendors', 4, NULL, NULL, NULL, NULL, '2025-06-21 14:56:18'),
(3, 1, 'Vendor deactivated', 'vendors', 4, NULL, NULL, NULL, NULL, '2025-06-21 14:59:17'),
(4, 1, 'Vendor activated', 'vendors', 4, NULL, NULL, NULL, NULL, '2025-06-21 15:00:43'),
(5, 6, 'restock', 'products', 1, '{\"stock_quantity\":99}', '{\"stock_quantity\":100,\"quantity_changed\":1,\"reason\":\"Test restock\",\"notes\":\"Testing restock functionality\"}', 'unknown', 'unknown', '2025-07-02 12:32:12'),
(6, 1, 'restock', 'products', 2, '{\"stock_quantity\":1}', '{\"stock_quantity\":2,\"quantity_changed\":1,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 13:54:41'),
(7, 1, 'reduce', 'products', 2, '{\"stock_quantity\":2}', '{\"stock_quantity\":1,\"quantity_changed\":1,\"reason\":\"damaged\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 13:55:05'),
(8, 1, 'restock', 'products', 1, '{\"stock_quantity\":100}', '{\"stock_quantity\":101,\"quantity_changed\":1,\"reason\":\"Test bulk restock\",\"notes\":\"Test bulk restock\"}', 'unknown', 'unknown', '2025-07-02 13:58:26'),
(9, 1, 'restock', 'products', 2, '{\"stock_quantity\":1}', '{\"stock_quantity\":2,\"quantity_changed\":1,\"reason\":\"Test bulk restock\",\"notes\":\"Test bulk restock\"}', 'unknown', 'unknown', '2025-07-02 13:58:26'),
(10, 1, 'restock', 'products', 2, '{\"stock_quantity\":2}', '{\"stock_quantity\":3,\"quantity_changed\":\"1\",\"reason\":\"Inventory adjustment\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:01:43'),
(11, 1, 'restock', 'products', 1, '{\"stock_quantity\":101}', '{\"stock_quantity\":102,\"quantity_changed\":\"1\",\"reason\":\"Inventory adjustment\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:01:43'),
(12, 1, 'restock', 'products', 2, '{\"stock_quantity\":3}', '{\"stock_quantity\":4,\"quantity_changed\":1,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:04:24'),
(13, 1, 'reduce', 'products', 2, '{\"stock_quantity\":4}', '{\"stock_quantity\":3,\"quantity_changed\":1,\"reason\":\"damaged\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:04:31'),
(14, 1, 'restock', 'products', 2, '{\"stock_quantity\":3}', '{\"stock_quantity\":4,\"quantity_changed\":1,\"reason\":\"Supplier delivery\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:04:42'),
(15, 1, 'restock', 'products', 1, '{\"stock_quantity\":102}', '{\"stock_quantity\":103,\"quantity_changed\":1,\"reason\":\"Supplier delivery\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:04:42'),
(16, 1, 'reduce', 'products', 2, '{\"stock_quantity\":4}', '{\"stock_quantity\":3,\"quantity_changed\":1,\"reason\":\"damaged\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:04:57'),
(17, 1, 'reduce', 'products', 1, '{\"stock_quantity\":103}', '{\"stock_quantity\":102,\"quantity_changed\":1,\"reason\":\"expired\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:05:04'),
(18, 1, 'reduce', 'products', 1, '{\"stock_quantity\":102}', '{\"stock_quantity\":5,\"quantity_changed\":97,\"reason\":\"Test low stock notification\",\"notes\":\"Testing low stock notification system\"}', 'unknown', 'unknown', '2025-07-02 14:08:12'),
(19, 1, 'restock', 'products', 1, '{\"stock_quantity\":5}', '{\"stock_quantity\":20,\"quantity_changed\":15,\"reason\":\"Test stock recovery notification\",\"notes\":\"Testing stock recovery notification system\"}', 'unknown', 'unknown', '2025-07-02 14:08:12'),
(20, 1, 'restock', 'products', 2, '{\"stock_quantity\":3}', '{\"stock_quantity\":4,\"quantity_changed\":1,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:10:32'),
(21, 1, 'restock', 'products', 2, '{\"stock_quantity\":4}', '{\"stock_quantity\":5,\"quantity_changed\":1,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:10:35'),
(22, 1, 'restock', 'products', 2, '{\"stock_quantity\":5}', '{\"stock_quantity\":6,\"quantity_changed\":1,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:10:37'),
(23, 1, 'restock', 'products', 2, '{\"stock_quantity\":6}', '{\"stock_quantity\":117,\"quantity_changed\":111,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:10:39'),
(24, 1, 'reduce', 'products', 2, '{\"stock_quantity\":117}', '{\"stock_quantity\":7,\"quantity_changed\":110,\"reason\":\"damaged\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:10:52'),
(25, 1, 'restock', 'products', 2, '{\"stock_quantity\":7}', '{\"stock_quantity\":18,\"quantity_changed\":11,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:11:09'),
(26, 1, 'restock', 'products', 2, '{\"stock_quantity\":18}', '{\"stock_quantity\":29,\"quantity_changed\":11,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:11:20'),
(27, 1, 'reduce', 'products', 2, '{\"stock_quantity\":29}', '{\"stock_quantity\":10,\"quantity_changed\":19,\"reason\":\"damaged\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:11:30'),
(28, 1, 'restock', 'products', 2, '{\"stock_quantity\":10}', '{\"stock_quantity\":11,\"quantity_changed\":1,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:11:42'),
(29, 1, 'reduce', 'products', 2, '{\"stock_quantity\":11}', '{\"stock_quantity\":0,\"quantity_changed\":11,\"reason\":\"damaged\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:12:04'),
(30, 1, 'restock', 'products', 2, '{\"stock_quantity\":0}', '{\"stock_quantity\":1,\"quantity_changed\":1,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:12:12'),
(31, 1, 'restock', 'products', 2, '{\"stock_quantity\":1}', '{\"stock_quantity\":21,\"quantity_changed\":20,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:12:18'),
(32, 1, 'restock', 'products', 1, '{\"stock_quantity\":5}', '{\"stock_quantity\":11,\"quantity_changed\":6,\"reason\":\"Manual restock\",\"notes\":\"\"}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '2025-07-02 14:19:23');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_archive` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Customer business data - addresses handled by customer_addresses table';

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `user_id`, `phone`, `is_archive`) VALUES
(1, 9, '+60123456789', 0),
(2, 11, '+60123456789', 0),
(3, 12, '1234567890', 0),
(4, 13, '1234567890', 0),
(5, 16, '+60123456789', 0),
(6, 17, '+60123456789', 0),
(7, 19, '123123123', 0);

-- --------------------------------------------------------

--
-- Table structure for table `customer_addresses`
--

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

--
-- Dumping data for table `customer_addresses`
--

INSERT INTO `customer_addresses` (`address_id`, `customer_id`, `address_type`, `first_name`, `last_name`, `company`, `street_address`, `street_address_2`, `city`, `state`, `postal_code`, `country`, `phone`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 7, 'both', 'adfs', 'dfs', NULL, 'afdasdf', NULL, 'a', 'a', '21000', 'Malaysia', '123123123', 1, '2025-06-21 14:58:09', '2025-06-21 14:58:09');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `type` enum('order','promo','alert') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `message`, `is_read`, `type`, `created_at`) VALUES
(1, 17, 'Your order has been placed successfully. You will receive updates on your order status.', 1, 'order', '2025-06-29 07:42:45'),
(2, 6, 'You have received a new order #4 with total amount $210.44', 0, 'order', '2025-06-29 07:42:45'),
(3, 1, 'Test notification - Product Management page loaded!', 1, NULL, '2025-06-29 07:50:45'),
(4, 17, 'Your order has been placed successfully. You will receive updates on your order status.', 1, 'order', '2025-07-01 13:00:58'),
(5, 6, 'You have received a new order #5 with total amount $32.27', 0, 'order', '2025-07-01 13:00:58'),
(6, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-01 13:30:36'),
(7, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-01 13:30:37'),
(8, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-01 13:30:38'),
(9, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-01 13:34:18'),
(10, 17, 'Your order has been delivered successfully Tracking number: asdasd', 1, 'order', '2025-07-01 13:34:51'),
(11, 17, 'Your order has been delivered successfully Tracking number: ·123456', 1, 'order', '2025-07-01 13:35:00'),
(12, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-01 13:35:13'),
(13, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-01 13:39:04'),
(14, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 2', 0, '', '2025-07-02 13:54:41'),
(15, 6, 'Stock for your product \'123\' has been reduced by 1 units. New stock level: 1', 0, '', '2025-07-02 13:55:05'),
(16, 6, 'Your product \'adf1\' has been restocked with 1 units. New stock level: 101', 0, '', '2025-07-02 13:58:26'),
(17, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 2', 0, '', '2025-07-02 13:58:26'),
(18, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 3', 0, '', '2025-07-02 14:01:43'),
(19, 6, 'Your product \'adf1\' has been restocked with 1 units. New stock level: 102', 0, '', '2025-07-02 14:01:43'),
(20, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 4', 0, '', '2025-07-02 14:04:24'),
(21, 6, 'Stock for your product \'123\' has been reduced by 1 units. New stock level: 3', 0, '', '2025-07-02 14:04:31'),
(22, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 4', 0, '', '2025-07-02 14:04:42'),
(23, 6, 'Your product \'adf1\' has been restocked with 1 units. New stock level: 103', 0, '', '2025-07-02 14:04:42'),
(24, 6, 'Stock for your product \'123\' has been reduced by 1 units. New stock level: 3', 0, '', '2025-07-02 14:04:57'),
(25, 6, 'Stock for your product \'adf1\' has been reduced by 1 units. New stock level: 102', 0, '', '2025-07-02 14:05:04'),
(26, 6, 'Stock for your product \'adf1\' has been reduced by 97 units. New stock level: 5', 0, '', '2025-07-02 14:08:12'),
(27, 6, 'Your product \'adf1\' is running low on stock. Current stock: 5 (Threshold: 10)', 0, '', '2025-07-02 14:08:12'),
(28, 1, 'Product \'adf1\' (Vendor: Gold Tier Test Business) is running low on stock. Current stock: 5 (Threshold: 10)', 1, '', '2025-07-02 14:08:12'),
(29, 2, 'Product \'adf1\' (Vendor: Gold Tier Test Business) is running low on stock. Current stock: 5 (Threshold: 10)', 0, '', '2025-07-02 14:08:12'),
(30, 6, 'Your product \'adf1\' has been restocked with 15 units. New stock level: 20', 0, '', '2025-07-02 14:08:12'),
(31, 6, 'Your product \'adf1\' stock has recovered. Current stock: 20', 0, '', '2025-07-02 14:08:12'),
(32, 1, 'Product \'adf1\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 20', 1, '', '2025-07-02 14:08:12'),
(33, 2, 'Product \'adf1\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 20', 0, '', '2025-07-02 14:08:12'),
(34, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 4', 0, '', '2025-07-02 14:10:32'),
(35, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 5', 0, '', '2025-07-02 14:10:35'),
(36, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 6', 0, '', '2025-07-02 14:10:37'),
(37, 6, 'Your product \'123\' has been restocked with 111 units. New stock level: 117', 0, '', '2025-07-02 14:10:39'),
(38, 6, 'Your product \'123\' stock has recovered. Current stock: 117', 0, '', '2025-07-02 14:10:39'),
(39, 1, 'Product \'123\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 117', 1, '', '2025-07-02 14:10:39'),
(40, 2, 'Product \'123\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 117', 0, '', '2025-07-02 14:10:39'),
(41, 6, 'Stock for your product \'123\' has been reduced by 110 units. New stock level: 7', 0, '', '2025-07-02 14:10:52'),
(42, 6, 'Your product \'123\' is running low on stock. Current stock: 7 (Threshold: 10)', 0, '', '2025-07-02 14:10:52'),
(43, 1, 'Product \'123\' (Vendor: Gold Tier Test Business) is running low on stock. Current stock: 7 (Threshold: 10)', 1, '', '2025-07-02 14:10:52'),
(44, 2, 'Product \'123\' (Vendor: Gold Tier Test Business) is running low on stock. Current stock: 7 (Threshold: 10)', 0, '', '2025-07-02 14:10:52'),
(45, 6, 'Your product \'123\' has been restocked with 11 units. New stock level: 18', 0, '', '2025-07-02 14:11:09'),
(46, 6, 'Your product \'123\' stock has recovered. Current stock: 18', 0, '', '2025-07-02 14:11:09'),
(47, 1, 'Product \'123\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 18', 1, '', '2025-07-02 14:11:09'),
(48, 2, 'Product \'123\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 18', 0, '', '2025-07-02 14:11:09'),
(49, 6, 'Your product \'123\' has been restocked with 11 units. New stock level: 29', 0, '', '2025-07-02 14:11:20'),
(50, 6, 'Stock for your product \'123\' has been reduced by 19 units. New stock level: 10', 0, '', '2025-07-02 14:11:30'),
(51, 6, 'Your product \'123\' is running low on stock. Current stock: 10 (Threshold: 10)', 0, '', '2025-07-02 14:11:30'),
(52, 1, 'Product \'123\' (Vendor: Gold Tier Test Business) is running low on stock. Current stock: 10 (Threshold: 10)', 1, '', '2025-07-02 14:11:30'),
(53, 2, 'Product \'123\' (Vendor: Gold Tier Test Business) is running low on stock. Current stock: 10 (Threshold: 10)', 0, '', '2025-07-02 14:11:30'),
(54, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 11', 0, '', '2025-07-02 14:11:42'),
(55, 6, 'Your product \'123\' stock has recovered. Current stock: 11', 0, '', '2025-07-02 14:11:42'),
(56, 1, 'Product \'123\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 11', 1, '', '2025-07-02 14:11:42'),
(57, 2, 'Product \'123\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 11', 0, '', '2025-07-02 14:11:42'),
(58, 6, 'Stock for your product \'123\' has been reduced by 11 units. New stock level: 0', 0, '', '2025-07-02 14:12:04'),
(59, 6, 'Your product \'123\' is now out of stock. Please restock immediately.', 0, '', '2025-07-02 14:12:04'),
(60, 1, 'Product \'123\' (Vendor: Gold Tier Test Business) is now out of stock. Vendor needs to restock immediately.', 1, '', '2025-07-02 14:12:04'),
(61, 2, 'Product \'123\' (Vendor: Gold Tier Test Business) is now out of stock. Vendor needs to restock immediately.', 0, '', '2025-07-02 14:12:04'),
(62, 6, 'Your product \'123\' has been restocked with 1 units. New stock level: 1', 0, '', '2025-07-02 14:12:12'),
(63, 6, 'Your product \'123\' has been restocked with 20 units. New stock level: 21', 0, '', '2025-07-02 14:12:18'),
(64, 6, 'Your product \'123\' stock has recovered. Current stock: 21', 0, '', '2025-07-02 14:12:18'),
(65, 1, 'Product \'123\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 21', 1, '', '2025-07-02 14:12:18'),
(66, 2, 'Product \'123\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 21', 0, '', '2025-07-02 14:12:18'),
(67, 6, 'Your product \'adf1\' is running low on stock after a customer order. Current stock: 5 (Threshold: 10). Consider restocking soon.', 0, '', '2025-07-02 14:15:37'),
(68, 1, 'Product \'adf1\' (Vendor: Gold Tier Test Business) is running low on stock after a customer order. Current stock: 5 (Threshold: 10).', 1, '', '2025-07-02 14:15:37'),
(69, 2, 'Product \'adf1\' (Vendor: Gold Tier Test Business) is running low on stock after a customer order. Current stock: 5 (Threshold: 10).', 0, '', '2025-07-02 14:15:37'),
(70, 9, 'Your order has been placed successfully. You will receive updates on your order status.', 0, 'order', '2025-07-02 14:15:37'),
(71, 6, 'You have received a new order #6 with total amount $344.06', 0, 'order', '2025-07-02 14:15:37'),
(72, 6, 'Your product \'adf1\' has been restocked with 6 units. New stock level: 11', 0, '', '2025-07-02 14:19:23'),
(73, 6, 'Your product \'adf1\' stock has recovered. Current stock: 11', 0, '', '2025-07-02 14:19:23'),
(74, 1, 'Product \'adf1\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 11', 1, '', '2025-07-02 14:19:23'),
(75, 2, 'Product \'adf1\' (Vendor: Gold Tier Test Business) stock has recovered. Current stock: 11', 0, '', '2025-07-02 14:19:23'),
(76, 6, 'Your product \'adf1\' is running low on stock after a customer order. Current stock: 9 (Threshold: 10). Consider restocking soon.', 0, '', '2025-07-02 14:24:22'),
(77, 1, 'Product \'adf1\' (Vendor: Gold Tier Test Business) is running low on stock after a customer order. Current stock: 9 (Threshold: 10).', 1, '', '2025-07-02 14:24:22'),
(78, 2, 'Product \'adf1\' (Vendor: Gold Tier Test Business) is running low on stock after a customer order. Current stock: 9 (Threshold: 10).', 0, '', '2025-07-02 14:24:22'),
(79, 9, 'Your order has been placed successfully. You will receive updates on your order status.', 0, 'order', '2025-07-02 14:24:22'),
(80, 6, 'You have received a new order #7 with total amount $54.54', 0, 'order', '2025-07-02 14:24:22'),
(81, 17, 'Your order has been placed successfully. You will receive updates on your order status.', 1, 'order', '2025-07-02 14:25:01'),
(82, 6, 'You have received a new order #8 with total amount $77.87', 0, 'order', '2025-07-02 14:25:01'),
(83, 17, 'Your order has been placed successfully. You will receive updates on your order status.', 1, 'order', '2025-07-02 14:28:25'),
(84, 6, 'You have received a new order #9 with total amount $11.06', 0, 'order', '2025-07-02 14:28:25'),
(85, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-02 15:01:48'),
(86, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-02 15:01:49'),
(87, 1, 'Most searched products report has been successfully exported for 30 days timeframe.', 0, '', '2025-07-04 05:29:07'),
(88, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:30:20'),
(89, 6, 'Your subscription plan has been changed to Silver successfully.', 0, NULL, '2025-07-04 05:40:34'),
(90, 6, 'Your subscription plan has been changed to Gold successfully.', 0, NULL, '2025-07-04 05:40:36'),
(91, 6, 'Your subscription plan has been changed to Platinum successfully.', 0, NULL, '2025-07-04 05:41:05'),
(92, 6, 'Your subscription plan has been changed to Silver successfully.', 0, NULL, '2025-07-04 05:41:16'),
(93, 6, 'Your subscription plan has been changed to Gold successfully.', 0, NULL, '2025-07-04 05:41:18'),
(94, 6, 'Your subscription plan has been changed to Gold successfully.', 0, NULL, '2025-07-04 05:41:29'),
(95, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:43:59'),
(96, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:46:32'),
(97, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:46:56'),
(98, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:46:57'),
(99, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:47:12'),
(100, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:51:07'),
(101, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:51:08'),
(102, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:51:13'),
(103, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:51:42'),
(104, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:53:28'),
(105, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:53:48'),
(106, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:54:02'),
(107, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:54:32'),
(108, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:54:45'),
(109, 6, 'Sales report report has been successfully exported for monthly timeframe.', 0, '', '2025-07-04 05:55:34'),
(110, 6, 'Sales report report has been successfully exported for monthly timeframe.', 0, '', '2025-07-04 05:58:14'),
(111, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:58:16'),
(112, 6, 'Sales report report has been successfully exported for monthly timeframe.', 0, '', '2025-07-04 05:58:18'),
(113, 6, 'Most ordered products report has been successfully exported for 30 days timeframe.', 0, '', '2025-07-04 05:58:27'),
(114, 6, 'Most ordered products report has been successfully exported for 30 days timeframe.', 0, '', '2025-07-04 05:58:41'),
(115, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:59:04'),
(116, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:59:05'),
(117, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:59:05'),
(118, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:59:05'),
(119, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:59:06'),
(120, 6, '1 products are running low on stock. Consider restocking soon.', 0, '', '2025-07-04 05:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `total_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `promo_code` varchar(50) DEFAULT NULL,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 10.00,
  `final_amount` decimal(10,2) NOT NULL,
  `platform_fee` decimal(10,2) DEFAULT 0.00,
  `vendor_earnings` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'Unpaid',
  `tracking_number` varchar(100) DEFAULT NULL,
  `delivered_at` date NOT NULL,
  `cancel_reason` text NOT NULL,
  `is_archive` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `vendor_id`, `order_date`, `status`, `total_amount`, `discount_amount`, `promo_code`, `shipping_fee`, `final_amount`, `platform_fee`, `vendor_earnings`, `payment_method`, `payment_status`, `tracking_number`, `delivered_at`, `cancel_reason`, `is_archive`) VALUES
(1, 6, 5, '2025-06-28 08:09:56', 'Processing', 21.01, 0.00, NULL, 10.00, 32.27, 0.00, 0.00, 'Cash on Delivery', 'Paid', 'TRK123456789', '2025-06-28', '', 0),
(2, 6, 5, '2025-06-28 09:25:38', 'Delivered', 189.09, 0.00, NULL, 10.00, 210.44, 0.00, 0.00, 'Cash on Delivery', 'Paid', '1231', '2025-06-28', 'Admin Cancellation: admin_policy_violation | Notes: Test cancellation by admin for system testing', 0),
(3, 6, 5, '2025-06-28 12:10:49', 'Pending', 32.01, 0.00, NULL, 10.00, 43.93, 0.00, 0.00, 'Cash on Delivery', 'Paid', NULL, '0000-00-00', '', 0),
(4, 6, 5, '2025-06-29 15:42:45', 'Pending', 189.09, 0.00, NULL, 10.00, 210.44, 0.00, 0.00, 'Debit Card', 'Paid', NULL, '0000-00-00', '', 0),
(5, 6, 5, '2025-07-01 21:00:58', 'Delivered', 21.01, 0.00, NULL, 10.00, 32.27, 0.00, 0.00, 'Bank Transfer', 'Paid', '·123456', '2025-07-01', '', 0),
(6, 1, 5, '2025-07-02 22:15:37', 'Pending', 315.15, 0.00, NULL, 10.00, 344.06, 0.00, 0.00, NULL, 'Unpaid', NULL, '0000-00-00', '', 0),
(7, 1, 5, '2025-07-02 22:24:22', 'Pending', 42.02, 0.00, NULL, 10.00, 54.54, 0.00, 0.00, NULL, 'Unpaid', NULL, '0000-00-00', '', 0),
(8, 6, 5, '2025-07-02 22:25:01', 'Pending', 64.03, 0.00, NULL, 10.00, 77.87, 0.00, 0.00, 'Credit Card', 'Paid', NULL, '0000-00-00', '', 0),
(9, 6, 5, '2025-07-02 22:28:25', 'Pending', 1.00, 0.00, NULL, 10.00, 11.06, 0.00, 0.00, 'Debit Card', 'Paid', NULL, '0000-00-00', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price_at_purchase`, `subtotal`) VALUES
(1, 1, 1, 1, 21.01, 21.01),
(2, 2, 1, 9, 21.01, 189.09),
(3, 3, 1, 1, 21.01, 21.01),
(4, 3, 2, 11, 1.00, 11.00),
(5, 4, 1, 9, 21.01, 189.09),
(6, 5, 1, 1, 21.01, 21.01),
(7, 6, 1, 15, 21.01, 315.15),
(8, 7, 1, 2, 21.01, 42.02),
(9, 8, 1, 3, 21.01, 63.03),
(10, 8, 2, 1, 1.00, 1.00),
(11, 9, 2, 1, 1.00, 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `page_visits`
--

CREATE TABLE `page_visits` (
  `visit_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `page_url` varchar(500) NOT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `referrer_url` varchar(500) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `visit_duration` int(11) DEFAULT NULL,
  `visit_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_visits`
--

INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(1, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 06:22:00'),
(2, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:00'),
(3, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:00'),
(4, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:00'),
(5, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:01'),
(6, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:01'),
(7, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:01'),
(8, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:01'),
(9, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:18'),
(10, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 06:22:19'),
(11, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:19'),
(12, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:19'),
(13, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:19'),
(14, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:19'),
(15, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:19'),
(16, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:22:19'),
(17, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 14, '2025-06-29 06:22:49'),
(18, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:03'),
(19, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 10, '2025-06-29 06:23:04'),
(20, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(21, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(22, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(23, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(24, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(25, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(26, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(27, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(28, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(29, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(30, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(31, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:04'),
(32, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:07'),
(33, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:09'),
(34, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:12'),
(35, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 06:23:25'),
(36, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-06-29 06:23:33'),
(37, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:23:37'),
(38, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 60, '2025-06-29 06:24:03'),
(39, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 06:25:03'),
(40, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 06:26:53'),
(41, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:26:53'),
(42, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:26:54'),
(43, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:26:54'),
(44, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:26:54'),
(45, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:00'),
(46, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 10, '2025-06-29 06:27:00'),
(47, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:02'),
(48, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:02'),
(49, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:25'),
(50, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 06:27:25'),
(51, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:29'),
(52, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 06:27:29'),
(53, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:30'),
(54, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:30'),
(55, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:59'),
(56, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 9, '2025-06-29 06:27:59'),
(57, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:59'),
(58, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 14, '2025-06-29 06:27:59'),
(59, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:59'),
(60, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:59'),
(61, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:59'),
(62, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:27:59'),
(63, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:28:00'),
(64, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:28:02'),
(65, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:28:03'),
(66, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:28:07'),
(67, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 06:28:18'),
(68, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 195, '2025-06-29 06:28:29'),
(69, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2424, '2025-06-29 06:28:30'),
(70, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:28:59'),
(71, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:29:30'),
(72, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:29:59'),
(73, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:31:44'),
(74, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 15, '2025-06-29 06:31:44'),
(75, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:31:44'),
(76, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:31:45'),
(77, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:31:45'),
(78, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:31:45'),
(79, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:31:45'),
(80, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:31:45'),
(81, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:31:47'),
(82, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 25, '2025-06-29 06:32:14'),
(83, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:32:30'),
(84, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 268, '2025-06-29 06:32:45'),
(85, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:34:30'),
(86, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: time_on_page - {\"seconds\":600}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:37:30'),
(87, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:19'),
(88, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 06:43:19'),
(89, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:20'),
(90, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:20'),
(91, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:20'),
(92, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:20'),
(93, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:20'),
(94, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:20'),
(95, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:22'),
(96, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-06-29 06:43:49'),
(97, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:55'),
(98, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 19, '2025-06-29 06:43:55'),
(99, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:55'),
(100, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:55'),
(101, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:55'),
(102, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:55'),
(103, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:55'),
(104, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:55'),
(105, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:43:58'),
(106, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:44:11'),
(107, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-06-29 06:44:17'),
(108, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 249, '2025-06-29 06:44:25'),
(109, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:44:55'),
(110, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:45:55'),
(111, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:48:34'),
(112, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 8, '2025-06-29 06:48:34'),
(113, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:48:35'),
(114, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:48:35'),
(115, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:48:35'),
(116, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:48:35'),
(117, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:48:35'),
(118, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:48:35'),
(119, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 8, '2025-06-29 06:49:04'),
(120, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:12'),
(121, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 06:49:12'),
(122, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:12'),
(123, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:12'),
(124, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:12'),
(125, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:12'),
(126, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:12'),
(127, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:12'),
(128, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:15'),
(129, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 06:49:16'),
(130, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(131, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(132, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(133, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(134, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(135, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(136, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(137, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(138, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(139, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(140, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(141, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(142, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:49:16'),
(143, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-06-29 06:49:46'),
(144, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 23, '2025-06-29 06:50:16'),
(145, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:51:40'),
(146, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 8, '2025-06-29 06:51:40'),
(147, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:51:41'),
(148, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:51:41'),
(149, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:51:41'),
(150, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:51:41'),
(151, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:51:41'),
(152, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:51:41'),
(153, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 06:52:11'),
(154, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 36, '2025-06-29 06:52:41');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(155, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:53:18'),
(156, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 06:53:18'),
(157, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:53:18'),
(158, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:53:18'),
(159, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:53:18'),
(160, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:53:18'),
(161, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:53:18'),
(162, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:53:18'),
(163, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-06-29 06:53:48'),
(164, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 83, '2025-06-29 06:54:19'),
(165, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:55:30'),
(166, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:55:42'),
(167, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 11, '2025-06-29 06:55:43'),
(168, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:55:43'),
(169, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:55:43'),
(170, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:55:43'),
(171, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:55:43'),
(172, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:55:43'),
(173, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:55:43'),
(174, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:56:04'),
(175, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 06:56:04'),
(176, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:56:04'),
(177, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:56:04'),
(178, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:56:04'),
(179, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:56:04'),
(180, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:56:04'),
(181, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:56:04'),
(182, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 12, '2025-06-29 06:56:34'),
(183, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 111, '2025-06-29 06:57:05'),
(184, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:58:30'),
(185, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:58:56'),
(186, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 06:58:56'),
(187, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:58:56'),
(188, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:58:57'),
(189, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:58:57'),
(190, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:58:57'),
(191, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:58:57'),
(192, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:58:57'),
(193, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:00'),
(194, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 06:59:00'),
(195, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:00'),
(196, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:00'),
(197, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:00'),
(198, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:00'),
(199, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:00'),
(200, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:00'),
(201, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:03'),
(202, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 06:59:03'),
(203, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:03'),
(204, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:04'),
(205, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:04'),
(206, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:04'),
(207, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:04'),
(208, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:04'),
(209, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:08'),
(210, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:08'),
(211, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 06:59:09'),
(212, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 49, '2025-06-29 06:59:33'),
(213, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:00:03'),
(214, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:04'),
(215, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-06-29 07:02:04'),
(216, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:05'),
(217, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:05'),
(218, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:05'),
(219, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:05'),
(220, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:05'),
(221, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:05'),
(222, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 17, '2025-06-29 07:02:35'),
(223, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:52'),
(224, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 07:02:52'),
(225, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:52'),
(226, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:52'),
(227, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:52'),
(228, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:53'),
(229, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:53'),
(230, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:02:53'),
(231, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:06'),
(232, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-06-29 07:03:06'),
(233, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:06'),
(234, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:06'),
(235, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:06'),
(236, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:06'),
(237, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:06'),
(238, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:06'),
(239, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:11'),
(240, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-06-29 07:03:11'),
(241, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:11'),
(242, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:11'),
(243, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:11'),
(244, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:12'),
(245, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:12'),
(246, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:12'),
(247, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:20'),
(248, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-06-29 07:03:20'),
(249, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:20'),
(250, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:20'),
(251, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:20'),
(252, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:20'),
(253, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:20'),
(254, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:20'),
(255, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:26'),
(256, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 07:03:26'),
(257, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:26'),
(258, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:26'),
(259, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:27'),
(260, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:27'),
(261, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:27'),
(262, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:27'),
(263, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:03:36'),
(264, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:03:51'),
(265, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 07:03:52'),
(266, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:52'),
(267, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:52'),
(268, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:52'),
(269, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:52'),
(270, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:52'),
(271, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:52'),
(272, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:03:54'),
(273, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:04:01'),
(274, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 07:04:02'),
(275, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:04:02'),
(276, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:04:02'),
(277, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:04:02'),
(278, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:04:02'),
(279, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:04:02'),
(280, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:04:02'),
(281, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:04:05'),
(282, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-06-29 07:04:31'),
(283, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 07:05:38'),
(284, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:05:52'),
(285, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:07:13'),
(286, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-06-29 07:07:13'),
(287, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:07:13'),
(288, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:07:13'),
(289, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:07:13'),
(290, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:07:13'),
(291, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:07:13'),
(292, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:07:13'),
(293, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 26, '2025-06-29 07:07:43'),
(294, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 54, '2025-06-29 07:08:14'),
(295, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:21'),
(296, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 9, '2025-06-29 07:11:21'),
(297, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:21'),
(298, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:22'),
(299, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:22'),
(300, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:22'),
(301, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:22'),
(302, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:22'),
(303, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:37'),
(304, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 17, '2025-06-29 07:11:37'),
(305, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:37'),
(306, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:37'),
(307, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:37'),
(308, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:38'),
(309, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:38'),
(310, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:46');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(311, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:48'),
(312, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:49'),
(313, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:51'),
(314, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:11:51'),
(315, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:12:04'),
(316, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 23, '2025-06-29 07:12:07'),
(317, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:30'),
(318, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 07:12:30'),
(319, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:31'),
(320, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:31'),
(321, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:31'),
(322, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:31'),
(323, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:31'),
(324, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:37'),
(325, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 07:12:37'),
(326, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:37'),
(327, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:37'),
(328, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:37'),
(329, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:37'),
(330, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:37'),
(331, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:12:42'),
(332, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 07:13:27'),
(333, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:13:28'),
(334, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 07:13:35'),
(335, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:13:37'),
(336, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:13:37'),
(337, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:13:38'),
(338, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-06-29 07:14:05'),
(339, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-06-29 07:14:09'),
(340, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 30, '2025-06-29 07:14:35'),
(341, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 59, '2025-06-29 07:15:11'),
(342, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 313, '2025-06-29 07:15:35'),
(343, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:16:10'),
(344, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/vendors/', 'Find Vendors - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:16:12'),
(345, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shop/', 'Shop - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:16:14'),
(346, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shop/', 'Shop - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shop/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:16:16'),
(347, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shop/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:16:16'),
(348, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 07:16:16'),
(349, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:16:18'),
(350, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 84, '2025-06-29 07:16:49'),
(351, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:17:19'),
(352, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:18:13'),
(353, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:18:14'),
(354, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:18:15'),
(355, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:18:16'),
(356, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 124, '2025-06-29 07:18:47'),
(357, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:19:17'),
(358, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:19:30'),
(359, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:20:30'),
(360, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:20:49'),
(361, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:20:49'),
(362, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:20:49'),
(363, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:20:49'),
(364, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:20:49'),
(365, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:20:49'),
(366, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:20:49'),
(367, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:20:51'),
(368, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:21:15'),
(369, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 65, '2025-06-29 07:21:19'),
(370, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 43, '2025-06-29 07:21:48'),
(371, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:21:49'),
(372, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:22:18'),
(373, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 60, '2025-06-29 07:22:49'),
(374, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:24:02'),
(375, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:24:13'),
(376, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 87, '2025-06-29 07:24:44'),
(377, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:25:14'),
(378, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-06-29 07:26:30'),
(379, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:26:36'),
(380, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:26:36'),
(381, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1380, '2025-06-29 07:27:07'),
(382, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:27:37'),
(383, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:27:53'),
(384, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:27:54'),
(385, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:27:55'),
(386, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:27:55'),
(387, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 10, '2025-06-29 07:28:26'),
(388, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:28:36'),
(389, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 07:29:07'),
(390, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:29:30'),
(391, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:29:57'),
(392, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 18, '2025-06-29 07:30:28'),
(393, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:30:47'),
(394, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 13, '2025-06-29 07:31:48'),
(395, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:32:01'),
(396, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:32:30'),
(397, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 28, '2025-06-29 07:32:32'),
(398, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:33:00'),
(399, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 07:33:31'),
(400, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 35, '2025-06-29 07:35:13'),
(401, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:37:05'),
(402, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Event: time_on_page - {\"seconds\":600}', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:37:30'),
(403, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 07:37:35'),
(404, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 07:40:25'),
(405, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:40:32'),
(406, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:40:33'),
(407, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 65, '2025-06-29 07:41:04'),
(408, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:41:34'),
(409, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:42:09'),
(410, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:42:33'),
(411, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:42:47'),
(412, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:42:54'),
(413, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:42:58'),
(414, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:43:03'),
(415, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:43:03'),
(416, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shop/', 'Shop - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:43:04'),
(417, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shop/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/shop/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:43:14'),
(418, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shop/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/shop/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:43:15'),
(419, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/shop/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/shop/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:43:15'),
(420, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/vendors/', 'Find Vendors - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shop/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:43:30'),
(421, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:43:30'),
(422, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shop/cart.php', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:46:19'),
(423, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:19'),
(424, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:20'),
(425, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-06-29 07:46:20'),
(426, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:20'),
(427, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:20'),
(428, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:20'),
(429, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:20'),
(430, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:20'),
(431, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/order-management/index.php', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:21'),
(432, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/order-management/index.php', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 07:46:27'),
(433, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:29'),
(434, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:29'),
(435, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:30'),
(436, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:30'),
(437, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:30'),
(438, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:31'),
(439, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:31'),
(440, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:31'),
(441, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:31'),
(442, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:32'),
(443, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:32'),
(444, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:33'),
(445, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:33'),
(446, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:36'),
(447, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 07:46:36'),
(448, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/order-management/index.php', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:37'),
(449, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:51'),
(450, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:46:51'),
(451, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 07:46:55'),
(452, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:46:56'),
(453, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:11'),
(454, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 07:47:11'),
(455, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 07:47:24'),
(456, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:25'),
(457, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:26');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(458, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:26'),
(459, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/vendors/', 'Find Vendors - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:26'),
(460, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:26'),
(461, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:39'),
(462, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:40'),
(463, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:40'),
(464, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:47:40'),
(465, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:41'),
(466, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:41'),
(467, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:47:41'),
(468, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/vendors/', 'Find Vendors - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:42'),
(469, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:42'),
(470, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:44'),
(471, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 07:47:44'),
(472, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:46'),
(473, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:46'),
(474, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:46'),
(475, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:46'),
(476, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:47'),
(477, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:47:51'),
(478, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:48:09'),
(479, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:48:09'),
(480, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:48:48'),
(481, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:49:10'),
(482, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:49:11'),
(483, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:49:12'),
(484, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:49:16'),
(485, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:49:17'),
(486, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:49:18'),
(487, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:49:18'),
(488, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:49:18'),
(489, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:02'),
(490, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:07'),
(491, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:50:07'),
(492, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 07:50:42'),
(493, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:42'),
(494, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:50:44'),
(495, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:44'),
(496, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:45'),
(497, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 07:50:45'),
(498, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:46'),
(499, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:46'),
(500, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:46'),
(501, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:46'),
(502, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:46'),
(503, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:46'),
(504, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:46'),
(505, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:46'),
(506, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:46'),
(507, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:49'),
(508, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-06-29 07:50:52'),
(509, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:52'),
(510, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:50:53'),
(511, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 338, '2025-06-29 07:51:10'),
(512, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 19, '2025-06-29 07:51:15'),
(513, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:51:40'),
(514, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 18, '2025-06-29 07:51:40'),
(515, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:53:30'),
(516, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 193, '2025-06-29 07:53:37'),
(517, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/product-management/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:55:30'),
(518, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:30'),
(519, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:50'),
(520, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:50'),
(521, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:56:51'),
(522, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:51'),
(523, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:51'),
(524, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:51'),
(525, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:51'),
(526, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:51'),
(527, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:56:54'),
(528, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:54'),
(529, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:54'),
(530, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:54'),
(531, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:54'),
(532, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:54'),
(533, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:54'),
(534, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:54'),
(535, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:54'),
(536, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:55'),
(537, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:55'),
(538, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:55'),
(539, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-profile/settings.php', 'Profile Settings - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:56'),
(540, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:56:59'),
(541, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 07:57:00'),
(542, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:07'),
(543, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:57:07'),
(544, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:08'),
(545, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:57:08'),
(546, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/vendors/', 'Find Vendors - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:09'),
(547, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:10'),
(548, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/vendors/', 'Find Vendors - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:10'),
(549, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 07:57:12'),
(550, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:12'),
(551, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:12'),
(552, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:12'),
(553, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:12'),
(554, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:12'),
(555, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:12'),
(556, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:12'),
(557, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:12'),
(558, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 18, '2025-06-29 07:57:30'),
(559, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 860, '2025-06-29 07:57:41'),
(560, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 07:57:48'),
(561, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:49'),
(562, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:49'),
(563, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:49'),
(564, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:49'),
(565, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:50'),
(566, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:50'),
(567, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 07:57:50'),
(568, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:52'),
(569, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:52'),
(570, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:52'),
(571, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:52'),
(572, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:52'),
(573, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:53'),
(574, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:53'),
(575, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:53'),
(576, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:53'),
(577, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:57:53'),
(578, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:11'),
(579, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:12'),
(580, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:14'),
(581, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:14'),
(582, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:15'),
(583, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:15'),
(584, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:16'),
(585, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-06-29 07:58:16'),
(586, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:16'),
(587, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:16'),
(588, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:16'),
(589, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:16'),
(590, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:16'),
(591, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:16'),
(592, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:58:17'),
(593, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 07:58:24'),
(594, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 105, '2025-06-29 07:58:55'),
(595, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:59:25'),
(596, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 07:59:30'),
(597, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:00:30'),
(598, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 08:01:48'),
(599, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:48'),
(600, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:48'),
(601, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:48'),
(602, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:48'),
(603, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:51');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(604, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 11, '2025-06-29 08:01:52'),
(605, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:52'),
(606, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:52'),
(607, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:52'),
(608, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:52'),
(609, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:01:52'),
(610, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 8, '2025-06-29 08:02:21'),
(611, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:02:29'),
(612, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:30'),
(613, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:30'),
(614, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:30'),
(615, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:31'),
(616, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:32'),
(617, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:33'),
(618, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:02:34'),
(619, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(620, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(621, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(622, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(623, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(624, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(625, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(626, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(627, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(628, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(629, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(630, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(631, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(632, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:34'),
(633, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:35'),
(634, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:36'),
(635, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 08:02:36'),
(636, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:36'),
(637, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:36'),
(638, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:36'),
(639, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:36'),
(640, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:38'),
(641, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 08:02:40'),
(642, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:40'),
(643, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:40'),
(644, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:40'),
(645, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:40'),
(646, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:02:40'),
(647, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 57, '2025-06-29 08:03:28'),
(648, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:04:25'),
(649, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 24, '2025-06-29 08:04:55'),
(650, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 17, '2025-06-29 08:05:26'),
(651, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:05:43'),
(652, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:05:44'),
(653, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:05:45'),
(654, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:05:45'),
(655, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 08:06:16'),
(656, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:06:26'),
(657, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:06:27'),
(658, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 08:07:23'),
(659, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:25'),
(660, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:07:25'),
(661, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:25'),
(662, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:26'),
(663, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:07:26'),
(664, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:27'),
(665, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:27'),
(666, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:27'),
(667, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:27'),
(668, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:27'),
(669, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:07:27'),
(670, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:27'),
(671, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:28'),
(672, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:28'),
(673, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:28'),
(674, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:28'),
(675, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:29'),
(676, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:29'),
(677, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:29'),
(678, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:29'),
(679, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:29'),
(680, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:29'),
(681, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:07:29'),
(682, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:29'),
(683, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:30'),
(684, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/vendors/', 'Event: time_on_page - {\"seconds\":600}', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:30'),
(685, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:30'),
(686, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:30'),
(687, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:30'),
(688, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:07:30'),
(689, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:30'),
(690, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:31'),
(691, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-06-29 08:07:31'),
(692, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:31'),
(693, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:31'),
(694, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:31'),
(695, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:31'),
(696, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(697, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(698, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(699, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(700, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(701, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(702, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(703, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(704, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(705, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(706, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:32'),
(707, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(708, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(709, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(710, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(711, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(712, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(713, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(714, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(715, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(716, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(717, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(718, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(719, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(720, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(721, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(722, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(723, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(724, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:07:33'),
(725, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 10, '2025-06-29 08:08:01'),
(726, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:11'),
(727, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 13, '2025-06-29 08:08:12'),
(728, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:12'),
(729, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:12'),
(730, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:12'),
(731, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:12'),
(732, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:12'),
(733, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:27'),
(734, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:28'),
(735, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:28'),
(736, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:29'),
(737, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:54'),
(738, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:08:55'),
(739, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:55'),
(740, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:55'),
(741, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:55'),
(742, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:55'),
(743, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:55'),
(744, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:55'),
(745, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:55'),
(746, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:55'),
(747, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:56'),
(748, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:58'),
(749, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 08:08:58'),
(750, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:58'),
(751, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:58'),
(752, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:58'),
(753, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:58');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(754, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(755, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(756, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(757, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(758, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(759, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(760, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(761, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(762, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(763, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(764, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:08:59'),
(765, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:02'),
(766, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:05'),
(767, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:05'),
(768, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:05'),
(769, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:05'),
(770, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:05'),
(771, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:05'),
(772, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:06'),
(773, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:07'),
(774, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:09:07'),
(775, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:08'),
(776, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:09:31'),
(777, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 08:09:32'),
(778, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:34'),
(779, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 08:09:35'),
(780, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:09:37'),
(781, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:38'),
(782, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:39'),
(783, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:09:39'),
(784, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:40'),
(785, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:09:41'),
(786, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:42'),
(787, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:43'),
(788, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 08:09:46'),
(789, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:09:49'),
(790, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:07'),
(791, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 13, '2025-06-29 08:10:37'),
(792, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:50'),
(793, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-06-29 08:10:51'),
(794, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:51'),
(795, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/role-permission-management/', 'Role & Permission Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:53'),
(796, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 19, '2025-06-29 08:10:54'),
(797, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(798, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(799, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(800, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(801, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(802, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(803, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(804, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(805, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(806, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(807, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(808, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(809, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(810, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(811, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:54'),
(812, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/role-permission-management/?action=get_role_details&role_id=1', 'Role & Permission Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:55'),
(813, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:59'),
(814, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:59'),
(815, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:59'),
(816, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:59'),
(817, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:59'),
(818, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:10:59'),
(819, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:11:20'),
(820, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:11:22'),
(821, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:11:23'),
(822, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:11:24'),
(823, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:11:50'),
(824, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:11:51'),
(825, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:12:01'),
(826, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:12:03'),
(827, 17, '5o3cbnum5bf7mt7rhlnf68sbss', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/vendors/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:12:03'),
(828, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 18, '2025-06-29 08:12:21'),
(829, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:12:34'),
(830, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 269, '2025-06-29 08:12:52'),
(831, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:13:04'),
(832, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:14:30'),
(833, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:14:30'),
(834, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:17:21'),
(835, 17, '5o3cbnum5bf7mt7rhlnf68sbss', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:17:30'),
(836, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-06-29 08:17:51'),
(837, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:17:56'),
(838, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:24'),
(839, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:26'),
(840, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:18:26'),
(841, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:27'),
(842, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-06-29 08:18:27'),
(843, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:27'),
(844, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:27'),
(845, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:27'),
(846, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:27'),
(847, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:27'),
(848, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:27'),
(849, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:31'),
(850, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:18:31'),
(851, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:41'),
(852, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:41'),
(853, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:41'),
(854, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:42'),
(855, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/user-management/', 'User Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:18:42'),
(856, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:43'),
(857, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/customer-management/', 'Customer Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:43'),
(858, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/customer-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:18:43'),
(859, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:44'),
(860, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:44'),
(861, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:44'),
(862, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:44'),
(863, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:47'),
(864, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:47'),
(865, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:47'),
(866, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:47'),
(867, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:47'),
(868, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:47'),
(869, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:47'),
(870, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:48'),
(871, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:48'),
(872, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:48'),
(873, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:49'),
(874, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:49'),
(875, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:49'),
(876, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-06-29 08:18:50'),
(877, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:50'),
(878, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-06-29 08:18:50'),
(879, 1, '3ra0llsj1oqifk2e2bq0vk7fdu', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-06-29 08:19:20'),
(880, NULL, '5agn0e1oqbhsvh3nevsfj2qh3r', '/agrimarket-erd/v1/subscription/subscription-plan.php', 'Subscription Plans - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/subscription/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-07-01 12:30:49'),
(881, NULL, '5agn0e1oqbhsvh3nevsfj2qh3r', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/subscription/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:30:49'),
(882, NULL, '5agn0e1oqbhsvh3nevsfj2qh3r', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:30:55'),
(883, NULL, '5agn0e1oqbhsvh3nevsfj2qh3r', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:30:59'),
(884, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:30:59'),
(885, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1202, '2025-07-01 12:31:35'),
(886, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:32:05'),
(887, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:33:22'),
(888, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:36:22'),
(889, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":600}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:41:22'),
(890, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:51:40'),
(891, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:51:42'),
(892, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:51:45'),
(893, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:51:46'),
(894, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:51:46'),
(895, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 12:51:53'),
(896, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 9, '2025-07-01 12:51:54'),
(897, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:51:54'),
(898, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:51:54');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(899, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:51:54'),
(900, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:51:54'),
(901, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:52:04'),
(902, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 205, '2025-07-01 12:52:34'),
(903, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:53:04'),
(904, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:54:22'),
(905, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:56:38'),
(906, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:57:04'),
(907, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 11, '2025-07-01 12:57:35'),
(908, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-07-01 12:58:50'),
(909, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:58:55'),
(910, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:58:56'),
(911, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:59:15'),
(912, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:59:16'),
(913, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:59:19'),
(914, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:59:37'),
(915, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:59:38'),
(916, NULL, '4bjlunfgv17f5esucu6dgkmt9h', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 11, '2025-07-01 12:59:46'),
(917, NULL, '4bjlunfgv17f5esucu6dgkmt9h', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:59:57'),
(918, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 12:59:57'),
(919, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:00:03'),
(920, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:00:05'),
(921, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 68, '2025-07-01 13:00:13'),
(922, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-07-01 13:00:35'),
(923, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:00:38'),
(924, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:00:43'),
(925, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:00:52'),
(926, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:01:00'),
(927, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:01:03'),
(928, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 170, '2025-07-01 13:01:34'),
(929, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:02:04'),
(930, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:03:22'),
(931, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 60, '2025-07-01 13:03:22'),
(932, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:04:22'),
(933, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:04:24'),
(934, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:04:26'),
(935, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:04:27'),
(936, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:04:29'),
(937, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 89, '2025-07-01 13:04:53'),
(938, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-07-01 13:04:59'),
(939, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:05:04'),
(940, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:05:06'),
(941, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:05:23'),
(942, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 11, '2025-07-01 13:05:36'),
(943, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 16, '2025-07-01 13:06:06'),
(944, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 28, '2025-07-01 13:07:06'),
(945, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:34'),
(946, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:35'),
(947, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:37'),
(948, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:38'),
(949, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:39'),
(950, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-07-01 13:07:39'),
(951, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:43'),
(952, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"2\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:43'),
(953, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:43'),
(954, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:43'),
(955, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:45'),
(956, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:46'),
(957, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:50'),
(958, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 13, '2025-07-01 13:07:50'),
(959, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:50'),
(960, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:50'),
(961, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(962, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(963, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(964, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(965, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(966, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(967, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(968, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(969, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(970, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:07:51'),
(971, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:08:00'),
(972, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:08:03'),
(973, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 471, '2025-07-01 13:08:22'),
(974, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 42, '2025-07-01 13:08:33'),
(975, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:09:03'),
(976, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-07-01 13:09:25'),
(977, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 27, '2025-07-01 13:10:13'),
(978, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:40'),
(979, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:45'),
(980, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:46'),
(981, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-07-01 13:10:46'),
(982, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:47'),
(983, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:48'),
(984, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:48'),
(985, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:48'),
(986, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:49'),
(987, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:49'),
(988, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:49'),
(989, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:49'),
(990, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"1\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:50'),
(991, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:50'),
(992, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:50'),
(993, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:50'),
(994, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:53'),
(995, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:56'),
(996, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:56'),
(997, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:56'),
(998, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:58'),
(999, 17, 'uebeq155bqu27t014nq1837a4q', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:10:58'),
(1000, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:11:22'),
(1001, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1369, '2025-07-01 13:11:29'),
(1002, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:11:59'),
(1003, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:13:22'),
(1004, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:16:22'),
(1005, NULL, 's8k2fojdtse95oafrf6ebfn9s1', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 4, '2025-07-01 13:16:30'),
(1006, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:16:34'),
(1007, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 9, '2025-07-01 13:16:34'),
(1008, NULL, 's8k2fojdtse95oafrf6ebfn9s1', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 5, '2025-07-01 13:17:00'),
(1009, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 8, '2025-07-01 13:17:04'),
(1010, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:05'),
(1011, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/vendor-management/', 'Vendor Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:08'),
(1012, NULL, 's8k2fojdtse95oafrf6ebfn9s1', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:18'),
(1013, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:18'),
(1014, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Subscription Plans - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:26'),
(1015, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:27'),
(1016, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:27'),
(1017, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:27'),
(1018, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:27'),
(1019, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:27'),
(1020, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:27'),
(1021, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/subscription/subscription-plan.php?source=dashboard', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:27'),
(1022, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:17:28'),
(1023, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 50, '2025-07-01 13:17:34'),
(1024, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 11, '2025-07-01 13:17:58'),
(1025, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 734, '2025-07-01 13:18:35'),
(1026, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 546, '2025-07-01 13:19:22'),
(1027, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:20:22'),
(1028, 17, 'uebeq155bqu27t014nq1837a4q', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":600}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:21:22'),
(1029, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:22:22'),
(1030, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:23:22'),
(1031, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Event: time_on_page - {\"seconds\":600}', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:27:22'),
(1032, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":600}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:28:22'),
(1033, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:34'),
(1034, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 2, '2025-07-01 13:30:34'),
(1035, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:36'),
(1036, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:37'),
(1037, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:37'),
(1038, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:37'),
(1039, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:37'),
(1040, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:30:37'),
(1041, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:38'),
(1042, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:38'),
(1043, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:38');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(1044, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:38'),
(1045, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:30:38'),
(1046, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 9, '2025-07-01 13:30:39'),
(1047, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:39'),
(1048, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:39'),
(1049, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:30:39'),
(1050, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 186, '2025-07-01 13:31:09'),
(1051, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:31:39'),
(1052, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:33:22'),
(1053, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:18'),
(1054, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:34:18'),
(1055, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:18'),
(1056, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:18'),
(1057, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:18'),
(1058, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:19'),
(1059, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:22'),
(1060, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:34:43'),
(1061, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:44'),
(1062, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:44'),
(1063, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 6, '2025-07-01 13:34:45'),
(1064, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:45'),
(1065, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:45'),
(1066, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:45'),
(1067, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:34:51'),
(1068, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:34:51'),
(1069, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 8, '2025-07-01 13:34:52'),
(1070, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:00'),
(1071, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:00'),
(1072, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:35:02'),
(1073, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 4, '2025-07-01 13:35:05'),
(1074, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:07'),
(1075, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:09'),
(1076, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:13'),
(1077, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 2, '2025-07-01 13:35:13'),
(1078, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:13'),
(1079, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:13'),
(1080, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:13'),
(1081, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:15'),
(1082, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 4, '2025-07-01 13:35:46'),
(1083, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:35:50'),
(1084, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 67, '2025-07-01 13:36:21'),
(1085, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:36:51'),
(1086, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:37:28'),
(1087, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:38:00'),
(1088, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 15, '2025-07-01 13:38:31'),
(1089, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/dashboard/', 'Vendor Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:38:46'),
(1090, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:00'),
(1091, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:01'),
(1092, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:02'),
(1093, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:39:02'),
(1094, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:03'),
(1095, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:04'),
(1096, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 11, '2025-07-01 13:39:04'),
(1097, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:04'),
(1098, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:05'),
(1099, 6, 'r1hjvith8ls3f98f53bu4hr5vh', '/agrimarket-erd/v1/analytics/', 'Vendor Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:05'),
(1100, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/vendor-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:15'),
(1101, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:39:15'),
(1102, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:15'),
(1103, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:16'),
(1104, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-07-01 13:39:16'),
(1105, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:16'),
(1106, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:16'),
(1107, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:16'),
(1108, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:16'),
(1109, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:39:35'),
(1110, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 8, '2025-07-01 13:39:46'),
(1111, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-07-01 13:39:57'),
(1112, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:40:05'),
(1113, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:40:11'),
(1114, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-07-01 13:40:16'),
(1115, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:40:17'),
(1116, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 109, '2025-07-01 13:41:17'),
(1117, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:41:22'),
(1118, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:06'),
(1119, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:43:06'),
(1120, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:06'),
(1121, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:06'),
(1122, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:06'),
(1123, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:06'),
(1124, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:08'),
(1125, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-01 13:43:08'),
(1126, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:08'),
(1127, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:09'),
(1128, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:09'),
(1129, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:43:09'),
(1130, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 10, '2025-07-01 13:43:38'),
(1131, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 104, '2025-07-01 13:44:09'),
(1132, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:44:22'),
(1133, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:45:22'),
(1134, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:45:53'),
(1135, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-07-01 13:45:53'),
(1136, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:45:53'),
(1137, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:45:53'),
(1138, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:45:53'),
(1139, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:45:53'),
(1140, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-07-01 13:46:23'),
(1141, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:46:29'),
(1142, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-07-01 13:46:29'),
(1143, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:46:29'),
(1144, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:46:29'),
(1145, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:46:29'),
(1146, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:46:29'),
(1147, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1413, '2025-07-01 13:46:59'),
(1148, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:47:29'),
(1149, 6, 'r1hjvith8ls3f98f53bu4hr5vh', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":600}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:49:22'),
(1150, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:49:22'),
(1151, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:52:22'),
(1152, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', 'http://localhost/agrimarket-erd/v1/analytics/', 'Event: time_on_page - {\"seconds\":600}', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 13:57:22'),
(1153, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 14:10:32'),
(1154, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-07-01 14:10:32'),
(1155, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 14:10:32'),
(1156, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 14:10:32'),
(1157, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 14:10:32'),
(1158, 1, 'v15miu0p7ht9i3f8fqtp6fq5vn', '/agrimarket-erd/v1/analytics/', 'Reports & Analytics', 'http://localhost/agrimarket-erd/v1/analytics/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-01 14:10:32'),
(1159, NULL, 'l66dsh9vfm6fvasdt10n2ho32r', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-07-02 10:24:51'),
(1160, NULL, 'l66dsh9vfm6fvasdt10n2ho32r', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:24:58'),
(1161, NULL, 'l66dsh9vfm6fvasdt10n2ho32r', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 10:25:01'),
(1162, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:02'),
(1163, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:04'),
(1164, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 19, '2025-07-02 10:25:04'),
(1165, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 12, '2025-07-02 10:25:24'),
(1166, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:24'),
(1167, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:24'),
(1168, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:24'),
(1169, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:24'),
(1170, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:24'),
(1171, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:24'),
(1172, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:24'),
(1173, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:24'),
(1174, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:25:34'),
(1175, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 509, '2025-07-02 10:26:37'),
(1176, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:28:20'),
(1177, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":300}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:31:20'),
(1178, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:35:06'),
(1179, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-07-02 10:35:06'),
(1180, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:35:06'),
(1181, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 16, '2025-07-02 10:35:11'),
(1182, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:35:11'),
(1183, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:35:11'),
(1184, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:35:26'),
(1185, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 29, '2025-07-02 10:35:30'),
(1186, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 36, '2025-07-02 10:35:59'),
(1187, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:36:07'),
(1188, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 10, '2025-07-02 10:39:20'),
(1189, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:30'),
(1190, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 19, '2025-07-02 10:39:30');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(1191, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:31'),
(1192, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:36'),
(1193, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:36'),
(1194, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:36'),
(1195, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:47'),
(1196, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:48'),
(1197, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:48'),
(1198, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:52'),
(1199, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:52'),
(1200, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:52'),
(1201, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-07-02 10:39:55'),
(1202, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:56'),
(1203, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:56'),
(1204, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 13, '2025-07-02 10:39:58'),
(1205, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:58'),
(1206, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:58'),
(1207, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:59'),
(1208, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:59'),
(1209, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:39:59'),
(1210, 1, 'p49s9sip3eijagac8oig182m8f', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:00'),
(1211, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:02'),
(1212, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:06'),
(1213, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:06'),
(1214, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:06'),
(1215, 1, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/auth/logout/', 'Logout - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:11'),
(1216, NULL, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/auth/login/?message=You+have+been+logged+out+successfully.', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:12'),
(1217, NULL, 'p49s9sip3eijagac8oig182m8f', '/agrimarket-erd/v1/auth/login/?message=You+have+been+logged+out+successfully.', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/?message=You+have+been+logged+out+successfully.', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:18'),
(1218, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/?message=You+have+been+logged+out+successfully.', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:18'),
(1219, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:23'),
(1220, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 10:40:24'),
(1221, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:25'),
(1222, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:25'),
(1223, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/role-permission-management/', 'Role & Permission Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:25'),
(1224, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:26'),
(1225, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-07-02 10:40:26'),
(1226, NULL, 'd57ed0u9lv1ec7bshvukefago7', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 5, '2025-07-02 10:40:42'),
(1227, NULL, 'd57ed0u9lv1ec7bshvukefago7', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:47'),
(1228, NULL, 'd57ed0u9lv1ec7bshvukefago7', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:40:49'),
(1229, NULL, 'd57ed0u9lv1ec7bshvukefago7', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:16'),
(1230, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:16'),
(1231, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-07-02 10:41:22'),
(1232, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:22'),
(1233, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:22'),
(1234, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:22'),
(1235, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:22'),
(1236, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:22'),
(1237, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:22'),
(1238, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:22'),
(1239, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:22'),
(1240, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:22'),
(1241, 1, 'nnfhmvmugie00cfd7vhfjddrop', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 39, '2025-07-02 10:41:26'),
(1242, 1, 'nnfhmvmugie00cfd7vhfjddrop', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:41:56'),
(1243, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:06'),
(1244, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-07-02 10:42:13'),
(1245, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:13'),
(1246, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/staff-management/', 'Staff Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:13'),
(1247, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:16'),
(1248, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:18'),
(1249, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 23, '2025-07-02 10:42:19'),
(1250, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:19'),
(1251, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:20'),
(1252, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:20'),
(1253, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:20'),
(1254, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:20'),
(1255, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:20'),
(1256, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1257, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1258, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1259, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1260, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1261, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1262, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1263, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1264, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1265, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1266, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1267, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1268, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1269, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1270, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1271, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1272, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:21'),
(1273, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=vendor&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:27'),
(1274, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=vendor&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:28'),
(1275, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:28'),
(1276, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=vendor&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:31'),
(1277, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:31'),
(1278, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:32'),
(1279, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=vendor&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:33'),
(1280, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:33'),
(1281, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=vendor&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:33'),
(1282, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=vendor&page=1&limit=10&search=&status=pending&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:40'),
(1283, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:47'),
(1284, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 10:42:50'),
(1285, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:51'),
(1286, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:53'),
(1287, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:54'),
(1288, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:56'),
(1289, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:57'),
(1290, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 10:42:58'),
(1291, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:42:59'),
(1292, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:43:00'),
(1293, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 10:43:02'),
(1294, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:43:03'),
(1295, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 28, '2025-07-02 10:44:12'),
(1296, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 120, '2025-07-02 10:47:02'),
(1297, 1, 'nnfhmvmugie00cfd7vhfjddrop', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 119, '2025-07-02 10:47:19'),
(1298, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:48:19'),
(1299, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/dashboard/', 'Staff Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:02'),
(1300, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:03'),
(1301, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 3, '2025-07-02 10:49:03'),
(1302, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:03'),
(1303, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:04'),
(1304, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_25_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:04'),
(1305, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:04'),
(1306, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_50_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:04'),
(1307, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:04'),
(1308, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:04'),
(1309, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:04'),
(1310, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_75_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:04'),
(1311, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:06'),
(1312, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_reviews&type=product&page=1&limit=10&search=&status=&rating=', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:07'),
(1313, 21, 'bdgc863pff44nfselvqg2ad7ui', '/agrimarket-erd/v1/review-management/?action=get_stats', 'Review Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6, '2025-07-02 10:49:07'),
(1314, 21, 'bdgc863pff44nfselvqg2ad7ui', 'http://localhost/agrimarket-erd/v1/review-management/', 'Event: scroll_100_percent', 'http://localhost/agrimarket-erd/v1/review-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 10:49:07'),
(1315, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 60, '2025-07-02 12:07:58'),
(1316, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:08:43'),
(1317, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/staff-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:08:44'),
(1318, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:08:45'),
(1319, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:08:45'),
(1320, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:08:57'),
(1321, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:08:57'),
(1322, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:08:58'),
(1323, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 277, '2025-07-02 12:08:58'),
(1324, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:08:58'),
(1325, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:24'),
(1326, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:24'),
(1327, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:24'),
(1328, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:28'),
(1329, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:30'),
(1330, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:31'),
(1331, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:31');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(1332, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:31'),
(1333, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:32'),
(1334, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:32'),
(1335, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:33'),
(1336, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:33'),
(1337, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:35'),
(1338, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 6070, '2025-07-02 12:13:35'),
(1339, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:35'),
(1340, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:36'),
(1341, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:38'),
(1342, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:48'),
(1343, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:53'),
(1344, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:13:54'),
(1345, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:14:02'),
(1346, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:14:31'),
(1347, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:14:50'),
(1348, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:14:50'),
(1349, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:14:50'),
(1350, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:14:55'),
(1351, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:17:14'),
(1352, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:17:14'),
(1353, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:17:14'),
(1354, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:17:21'),
(1355, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:18:23'),
(1356, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:20:14'),
(1357, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:20:14'),
(1358, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:20:14'),
(1359, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:20:18'),
(1360, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:25:17'),
(1361, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:25:17'),
(1362, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:25:17'),
(1363, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:25:22'),
(1364, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 12:25:30'),
(1365, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:35'),
(1366, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:35'),
(1367, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:35'),
(1368, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:37'),
(1369, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:37'),
(1370, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:37'),
(1371, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:41'),
(1372, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:41'),
(1373, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/role-permission-management/', 'Role & Permission Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:44'),
(1374, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:45'),
(1375, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 2, '2025-07-02 13:54:45'),
(1376, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:47'),
(1377, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 631, '2025-07-02 13:54:47'),
(1378, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:54:47'),
(1379, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:55:05'),
(1380, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:55:05'),
(1381, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 13:55:24'),
(1382, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:01:43'),
(1383, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:01:43'),
(1384, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:01:54'),
(1385, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:18'),
(1386, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:18'),
(1387, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:18'),
(1388, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:24'),
(1389, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:24'),
(1390, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:31'),
(1391, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:31'),
(1392, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:42'),
(1393, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:43'),
(1394, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:57'),
(1395, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:04:57'),
(1396, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:04'),
(1397, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:04'),
(1398, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:06'),
(1399, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:07'),
(1400, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:07'),
(1401, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:07'),
(1402, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:16'),
(1403, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/role-permission-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:16'),
(1404, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/product-management/', 'Product Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:16'),
(1405, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:18'),
(1406, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 825, '2025-07-02 14:05:18'),
(1407, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:05:18'),
(1408, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:29'),
(1409, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:29'),
(1410, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:29'),
(1411, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:32'),
(1412, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:32'),
(1413, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:32'),
(1414, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:35'),
(1415, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:35'),
(1416, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:35'),
(1417, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:37'),
(1418, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:37'),
(1419, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:37'),
(1420, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:39'),
(1421, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:39'),
(1422, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:39'),
(1423, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:52'),
(1424, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:52'),
(1425, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:10:52'),
(1426, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:09'),
(1427, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:09'),
(1428, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:09'),
(1429, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:20'),
(1430, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:20'),
(1431, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:20'),
(1432, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:30'),
(1433, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:30'),
(1434, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:30'),
(1435, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:42'),
(1436, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:42'),
(1437, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:42'),
(1438, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:49'),
(1439, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:49'),
(1440, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:11:49'),
(1441, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:04'),
(1442, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:04'),
(1443, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:04'),
(1444, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:06'),
(1445, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:06'),
(1446, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:06'),
(1447, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:12'),
(1448, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:12'),
(1449, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:13'),
(1450, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:14'),
(1451, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:14'),
(1452, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:15'),
(1453, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:18'),
(1454, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:18'),
(1455, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:18'),
(1456, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/product-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:19'),
(1457, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:19'),
(1458, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:12:19'),
(1459, NULL, '2sg953a1v5j5sbkgpfdvgreeds', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 10, '2025-07-02 14:18:46'),
(1460, NULL, '2sg953a1v5j5sbkgpfdvgreeds', '/agrimarket-erd/v1/auth/login/', 'Login - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 14:18:56'),
(1461, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/auth/login/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:18:57'),
(1462, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/dashboard/', 'Admin Dashboard', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:01'),
(1463, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:03'),
(1464, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:07'),
(1465, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:10'),
(1466, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:18'),
(1467, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:18'),
(1468, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:18'),
(1469, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:23'),
(1470, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:23');
INSERT INTO `page_visits` (`visit_id`, `user_id`, `session_id`, `page_url`, `page_title`, `referrer_url`, `user_agent`, `ip_address`, `device_type`, `browser`, `visit_duration`, `visit_date`) VALUES
(1471, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:23'),
(1472, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:27'),
(1473, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:27'),
(1474, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:27'),
(1475, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:31'),
(1476, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:31'),
(1477, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:31'),
(1478, 17, 'gpnlmp3l64e98gr58tchlelafa', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:19:40'),
(1479, 17, 'gpnlmp3l64e98gr58tchlelafa', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":60}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 48, '2025-07-02 14:20:11'),
(1480, 17, 'gpnlmp3l64e98gr58tchlelafa', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Event: time_on_page - {\"seconds\":120}', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 89, '2025-07-02 14:23:19'),
(1481, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:24:48'),
(1482, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:25:03'),
(1483, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:25:08'),
(1484, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:25:08'),
(1485, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:25:08'),
(1486, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:25:19'),
(1487, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:25:20'),
(1488, 1, 'nnfhmvmugie00cfd7vhfjddrop', '/agrimarket-erd/v1/inventory-management/', 'Inventory Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/inventory-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:25:20'),
(1489, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/user-profile/', 'User Profile - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:25:23'),
(1490, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:25:24'),
(1491, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:26:05'),
(1492, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:26:06'),
(1493, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 14:26:20'),
(1494, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:26:22'),
(1495, 17, 'gpnlmp3l64e98gr58tchlelafa', 'http://localhost/agrimarket-erd/v1/order-management/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 7, '2025-07-02 14:26:53'),
(1496, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:27:00'),
(1497, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 61, '2025-07-02 14:27:15'),
(1498, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/user-profile/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:28:14'),
(1499, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:28:16'),
(1500, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 4, '2025-07-02 14:28:16'),
(1501, 17, 'gpnlmp3l64e98gr58tchlelafa', 'http://localhost/agrimarket-erd/v1/products/', 'Event: product_click - {\"product_id\":\"2\"}', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:28:18'),
(1502, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:28:18'),
(1503, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:28:19'),
(1504, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:28:19'),
(1505, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/shopping-cart/', 'Shopping Cart - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:28:20'),
(1506, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/order-management/', 'Order Management - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/shopping-cart/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 14:28:27'),
(1507, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:28:38'),
(1508, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 14:28:45'),
(1509, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/products/', 'Shop Products - AgriMarket Solutions', 'http://localhost/agrimarket-erd/v1/products/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 1, '2025-07-02 14:28:46'),
(1510, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/order-management/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:28:47'),
(1511, 17, 'gpnlmp3l64e98gr58tchlelafa', '/agrimarket-erd/v1/dashboard/', 'Customer Dashboard', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:29:23'),
(1512, 17, 'gpnlmp3l64e98gr58tchlelafa', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Event: time_on_page - {\"seconds\":30}', 'http://localhost/agrimarket-erd/v1/dashboard/', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '::1', 'Desktop', 'Chrome', 0, '2025-07-02 14:29:53');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

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

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method_id`, `amount`, `currency`, `transaction_id`, `reference_number`, `status`, `gateway_response`, `failure_reason`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 32.27, 'MYR', 'PAY_685f32d4b28e8', 'REF-20250628-2D4B2345', 'completed', '{\"txn_id\":\"PAY_685f32d4b28e8\",\"status\":\"success\"}', NULL, '2025-06-28 00:09:56', '2025-06-28 00:09:56', '2025-06-28 00:09:56'),
(2, 2, 5, 210.44, 'MYR', 'PAY_685f4492c2955', 'REF-20250628-492C2855', 'completed', '{\"txn_id\":\"PAY_685f4492c2955\",\"status\":\"success\"}', NULL, '2025-06-28 01:25:38', '2025-06-28 01:25:38', '2025-06-28 01:25:38'),
(3, 3, 5, 43.93, 'MYR', 'PAY_685f6b490f951', 'REF-20250628-B490F6FF', 'completed', '{\"txn_id\":\"PAY_685f6b490f951\",\"status\":\"success\"}', NULL, '2025-06-28 04:10:49', '2025-06-28 04:10:49', '2025-06-28 04:10:49'),
(4, 4, 2, 213.59, 'MYR', 'CC_6860ee75421d2', 'REF-20250629-E7541F16', 'completed', '{\"status\":\"success\",\"card_last4\":\"1111\",\"processed_at\":\"2025-06-29 09:42:45\"}', NULL, '2025-06-29 07:42:45', '2025-06-29 07:42:45', '2025-06-29 07:42:45'),
(5, 5, 3, 32.27, 'MYR', 'FPX_6863dc0abbd7f', 'REF-20250701-C0ABBBA2', 'completed', '{\"bank_code\":\"MAYBANK\",\"fpx_id\":\"FPX_6863dc0abbd7f\",\"status\":\"success\"}', NULL, '2025-07-01 13:00:58', '2025-07-01 13:00:58', '2025-07-01 13:00:58'),
(6, 8, 1, 79.82, 'MYR', 'CC_6865413d3b296', 'REF-20250702-13D3AECB', 'completed', '{\"status\":\"success\",\"card_last4\":\"1111\",\"processed_at\":\"2025-07-02 16:25:01\"}', NULL, '2025-07-02 14:25:01', '2025-07-02 14:25:01', '2025-07-02 14:25:01'),
(7, 9, 2, 11.23, 'MYR', 'CC_68654209d83e9', 'REF-20250702-209D8307', 'completed', '{\"status\":\"success\",\"card_last4\":\"1111\",\"processed_at\":\"2025-07-02 16:28:25\"}', NULL, '2025-07-02 14:28:25', '2025-07-02 14:28:25', '2025-07-02 14:28:25');

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

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

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`payment_method_id`, `name`, `code`, `description`, `is_active`, `processing_fee_percent`, `min_amount`, `max_amount`, `sort_order`, `created_at`) VALUES
(1, 'Credit Card', 'CREDIT_CARD', 'Visa, MasterCard, American Express', 1, 2.50, 0.00, NULL, 1, '2025-06-20 13:45:30'),
(2, 'Debit Card', 'DEBIT_CARD', 'Bank debit cards', 1, 1.50, 0.00, NULL, 2, '2025-06-20 13:45:30'),
(3, 'Bank Transfer', 'BANK_TRANSFER', 'Direct bank transfer', 1, 0.00, 0.00, NULL, 3, '2025-06-20 13:45:30'),
(4, 'Mobile Payment', 'MOBILE_PAYMENT', 'Mobile wallet payments', 1, 1.00, 0.00, NULL, 4, '2025-06-20 13:45:30'),
(5, 'Cash on Delivery', 'COD', 'Pay when you receive', 1, 0.00, 0.00, NULL, 5, '2025-06-20 13:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `display_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`, `display_name`, `description`, `module`, `is_active`, `created_at`) VALUES
(1, 'manage_users', 'Manage Users', NULL, 'user_management', 1, '2025-06-20 13:52:25'),
(2, 'manage_products', 'Manage Products', NULL, 'product_management', 1, '2025-06-20 13:52:25'),
(3, 'manage_orders', 'Manage Orders', NULL, 'order_management', 1, '2025-06-20 13:52:25'),
(4, 'view_analytics', 'View Analytics', NULL, 'analytics', 1, '2025-06-20 13:52:25'),
(5, 'manage_system', 'Manage System', NULL, 'system', 1, '2025-06-20 13:52:25'),
(6, 'manage_vendors', 'Manage Vendors', NULL, 'vendor_management', 1, '2025-06-20 13:52:25'),
(7, 'manage_customers', 'Manage Customers', NULL, 'customer_management', 1, '2025-06-20 13:52:25'),
(8, 'manage_staff', 'Manage Staff', NULL, 'staff_management', 1, '2025-06-20 13:52:25'),
(9, 'place_orders', 'Place Orders', NULL, 'shopping', 1, '2025-06-20 13:52:25'),
(10, 'view_orders', 'View Orders', NULL, 'shopping', 1, '2025-06-20 13:52:25'),
(11, 'manage_inventory', 'Manage Inventory', NULL, 'inventory', 1, '2025-06-20 13:52:25'),
(12, 'view_reports', 'View Reports', NULL, 'reporting', 1, '2025-06-20 13:52:25'),
(13, 'manage_promotions', 'Manage Promotions', NULL, 'marketing', 1, '2025-06-20 13:52:25'),
(14, 'customer_support', 'Customer Support', NULL, 'support', 1, '2025-06-20 13:52:25');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `packaging` varchar(100) DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `is_discounted` tinyint(1) DEFAULT 0,
  `discount_percent` decimal(5,2) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_archive` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `vendor_id`, `name`, `description`, `category`, `packaging`, `base_price`, `selling_price`, `stock_quantity`, `is_discounted`, `discount_percent`, `image_path`, `is_archive`) VALUES
(1, 5, 'adf1', 'adfasdfasfd1', 'Aquaculture', 'asdf1', 12.01, 21.01, 6, 0, NULL, 'uploads/products/product_685e99a2228d1_1751030178.png', 0),
(2, 5, '123', '1', 'Aquaculture', 'asdf1', 2.00, 1.00, 19, 0, NULL, 'uploads/products/product_685f63318e4d6_1751081777.png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_category_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `name`, `slug`, `description`, `parent_category_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Livestock', 'livestock', 'Cattle, poultry, hogs and other farm animals', NULL, 1, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(2, 'Crops', 'crops', 'Corn, soybeans, hay and other agricultural crops', NULL, 2, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(3, 'Forestry Products', 'forestry-products', 'Almonds, walnuts and other edible forest products', NULL, 3, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(4, 'Dairy Products', 'dairy-products', 'Milk and dairy-based products', NULL, 4, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(5, 'Aquaculture', 'aquaculture', 'Fish farming and aquatic products', NULL, 5, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(6, 'Miscellaneous', 'miscellaneous', 'Honey and other agricultural products', NULL, 6, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(7, 'Cattle', 'cattle', 'Beef cattle, dairy cattle', 1, 1, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(8, 'Poultry', 'poultry', 'Chickens, ducks, turkeys', 1, 2, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(9, 'Pigs', 'pigs', 'Hogs and swine', 1, 3, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(10, 'Grains', 'grains', 'Corn, wheat, rice', 2, 1, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(11, 'Vegetables', 'vegetables', 'Fresh vegetables', 2, 2, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30'),
(12, 'Fruits', 'fruits', 'Fresh fruits', 2, 3, 1, '2025-06-20 13:45:30', '2025-06-20 13:45:30');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `pros` text DEFAULT NULL,
  `cons` text DEFAULT NULL,
  `is_verified_purchase` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_approved` tinyint(1) DEFAULT 0,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `helpful_count` int(11) DEFAULT 0,
  `is_archive` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `customer_id`, `order_id`, `rating`, `title`, `comment`, `pros`, `cons`, `is_verified_purchase`, `created_at`, `updated_at`, `is_approved`, `approved_by`, `approved_at`, `helpful_count`, `is_archive`) VALUES
(2, 1, 6, 2, 5, '1', '213', '1233', '3333', 1, '2025-06-28 13:03:27', '2025-06-28 05:03:37', 1, 1, '2025-06-27 23:03:37', 0, 0),
(3, 1, 6, 2, 5, '1', '1', '1', '1', 1, '2025-06-29 15:46:33', '2025-07-02 10:49:06', 1, 21, '2025-07-02 04:49:06', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `display_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrator', 'Full system access', 1, '2025-06-20 13:52:25', '2025-06-20 13:52:25'),
(2, 'vendor', 'Vendor', 'Can manage products and orders', 1, '2025-06-20 13:52:25', '2025-06-20 13:52:25'),
(3, 'customer', 'Customer', 'Can browse and purchase products', 1, '2025-06-20 13:52:25', '2025-06-20 13:52:25'),
(4, 'staff', 'Staff', 'Can assist with operations', 1, '2025-06-20 13:52:25', '2025-06-20 13:52:25');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_permission_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_permission_id`, `role_id`, `permission_id`, `granted_at`) VALUES
(12, 3, 9, '2025-06-20 13:52:25'),
(13, 3, 10, '2025-06-20 13:52:25'),
(14, 4, 14, '2025-06-20 13:52:25'),
(16, 1, 4, '2025-06-28 00:36:01'),
(17, 1, 7, '2025-06-28 00:36:01'),
(18, 1, 11, '2025-06-28 00:36:01'),
(19, 1, 13, '2025-06-28 00:36:01'),
(20, 1, 3, '2025-06-28 00:36:01'),
(21, 1, 2, '2025-06-28 00:36:01'),
(22, 1, 12, '2025-06-28 00:36:01'),
(23, 1, 8, '2025-06-28 00:36:01'),
(24, 1, 5, '2025-06-28 00:36:01'),
(25, 1, 1, '2025-06-28 00:36:01'),
(26, 1, 6, '2025-06-28 00:36:01'),
(27, 2, 11, '2025-06-28 00:36:34'),
(28, 2, 3, '2025-06-28 00:36:34'),
(29, 2, 2, '2025-06-28 00:36:34'),
(30, 2, 12, '2025-06-28 00:36:34');

-- --------------------------------------------------------

--
-- Table structure for table `search_logs`
--

CREATE TABLE `search_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `keyword` varchar(255) NOT NULL COMMENT 'Search keyword or term',
  `search_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `click_position` int(11) DEFAULT NULL COMMENT 'Position of clicked product in search results',
  `clicked_at` timestamp NULL DEFAULT NULL COMMENT 'Timestamp when product was clicked',
  `filters` text DEFAULT NULL COMMENT 'JSON string of applied search filters',
  `results_count` int(11) DEFAULT 0 COMMENT 'Number of search results returned',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP address of the user',
  `user_agent` text DEFAULT NULL COMMENT 'Browser user agent string',
  `session_id` varchar(100) DEFAULT NULL COMMENT 'Session ID for tracking user sessions',
  `clicked_product_id` int(11) DEFAULT NULL COMMENT 'ID of product clicked from search results',
  `clicked_vendor_id` int(11) DEFAULT NULL COMMENT 'ID of vendor clicked from vendor search results'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Comprehensive search logs table for product and vendor analytics tracking';

--
-- Dumping data for table `search_logs`
--

INSERT INTO `search_logs` (`log_id`, `user_id`, `keyword`, `search_date`, `click_position`, `clicked_at`, `filters`, `results_count`, `ip_address`, `user_agent`, `session_id`, `clicked_product_id`, `clicked_vendor_id`) VALUES
(1, 3, 'fresh onions', '2025-05-28 20:48:48', 2, '2025-05-28 20:52:06', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.253', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3085', 1, NULL),
(2, 1, 'fresh vegetables', '2025-05-28 20:48:48', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.79', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1161', NULL, NULL),
(3, NULL, 'carrots', '2025-05-28 20:48:48', 1, '2025-05-28 20:51:38', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.91', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8291', 1, NULL),
(4, 5, 'tomatoes', '2025-05-28 20:48:48', 4, '2025-05-28 20:51:55', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.221', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7640', 1, NULL),
(6, 1, 'corn', '2025-05-28 20:49:01', 4, '2025-05-28 20:50:30', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.80', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4646', 1, NULL),
(7, 9, 'fruits', '2025-05-28 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.48', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9130', NULL, NULL),
(8, 9, 'potatoes', '2025-05-28 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.162', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2863', NULL, NULL),
(9, 2, 'fresh lettuce', '2025-05-28 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.134', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2948', NULL, NULL),
(10, 16, 'fresh carrots', '2025-05-28 20:49:01', 4, '2025-05-28 20:49:43', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.100', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9097', 2, NULL),
(11, 1, 'corn', '2025-05-28 20:49:01', 2, '2025-05-28 20:53:04', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.77', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3559', 2, NULL),
(12, 2, 'potatoes', '2025-05-28 20:49:01', 1, '2025-05-28 20:52:50', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.125', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4773', 1, NULL),
(13, 1, 'tomato', '2025-05-28 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.126', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8300', NULL, NULL),
(14, 9, 'sweet corn', '2025-05-28 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.206', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7124', NULL, NULL),
(15, 16, 'tomatoes', '2025-05-28 20:49:01', 1, '2025-05-28 20:51:09', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.128', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9586', 2, NULL),
(16, 16, 'organic vegetables', '2025-05-28 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.37', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1695', NULL, NULL),
(17, NULL, 'organic tomato', '2025-05-29 20:49:01', 4, '2025-05-29 20:51:07', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.83', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4699', 1, NULL),
(18, 1, 'lettuce', '2025-05-29 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.134', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5445', NULL, NULL),
(19, 9, 'tomato', '2025-05-29 20:49:01', 3, '2025-05-29 20:53:33', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.7', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5787', 1, NULL),
(20, 1, 'fresh potatoes', '2025-05-29 20:49:01', 1, '2025-05-29 20:52:21', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.95', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9478', 1, NULL),
(21, NULL, 'organic fruits', '2025-05-29 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.212', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4596', NULL, NULL),
(22, NULL, 'onions', '2025-05-29 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.241', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9664', NULL, NULL),
(23, 16, 'farm fresh', '2025-05-29 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.232', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7277', NULL, NULL),
(24, NULL, 'farm fresh', '2025-05-29 20:49:01', 5, '2025-05-29 20:51:17', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.131', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8105', 2, NULL),
(25, 2, 'corn', '2025-05-29 20:49:01', 3, '2025-05-29 20:51:35', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.80', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9584', 2, NULL),
(26, NULL, 'fresh lettuce', '2025-05-29 20:49:01', 4, '2025-05-29 20:51:23', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.222', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9237', 2, NULL),
(27, 11, 'fruits', '2025-05-29 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.181', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5412', NULL, NULL),
(28, 16, 'organic corn', '2025-05-29 20:49:01', 2, '2025-05-29 20:52:11', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.244', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3507', 2, NULL),
(29, 9, 'vegetables', '2025-05-29 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.251', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8569', NULL, NULL),
(30, 11, 'organic potatoes', '2025-05-29 20:49:01', 1, '2025-05-29 20:52:33', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.111', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7397', 2, NULL),
(31, 9, 'fresh fruits', '2025-05-29 20:49:01', 1, '2025-05-29 20:50:46', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.230', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7816', 1, NULL),
(32, 2, 'local produce', '2025-05-30 20:49:01', 1, '2025-05-30 20:51:19', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.91', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9503', 2, NULL),
(33, 2, 'vegetables', '2025-05-30 20:49:01', 5, '2025-05-30 20:50:38', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.17', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5808', 1, NULL),
(34, 2, 'fruits', '2025-05-30 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.2', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6695', NULL, NULL),
(35, 2, 'corn', '2025-05-30 20:49:01', 2, '2025-05-30 20:51:13', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.109', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3092', 1, NULL),
(36, 11, 'fresh onions', '2025-05-30 20:49:01', 1, '2025-05-30 20:50:55', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.79', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6310', 1, NULL),
(37, NULL, 'corn', '2025-05-30 20:49:01', 1, '2025-05-30 20:50:40', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.6', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2391', 1, NULL),
(38, NULL, 'carrots', '2025-05-30 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.196', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2065', NULL, NULL),
(39, NULL, 'organic vegetables', '2025-05-30 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.249', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1626', NULL, NULL),
(40, 11, 'fresh carrots', '2025-05-30 20:49:01', 2, '2025-05-30 20:53:34', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.4', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9273', 1, NULL),
(41, NULL, 'fresh onions', '2025-05-30 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.253', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4839', NULL, NULL),
(42, 9, 'fruits', '2025-05-30 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.226', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6499', NULL, NULL),
(43, 16, 'organic fruits', '2025-05-30 20:49:01', 4, '2025-05-30 20:53:16', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.128', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1846', 1, NULL),
(44, 2, 'tomato', '2025-05-30 20:49:01', 3, '2025-05-30 20:49:39', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.154', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5920', 1, NULL),
(45, 9, 'fresh lettuce', '2025-05-31 20:49:01', 1, '2025-05-31 20:53:28', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.99', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4875', 2, NULL),
(46, 9, 'organic potatoes', '2025-05-31 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.152', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4241', NULL, NULL),
(47, 1, 'potatoes', '2025-05-31 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.150', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7979', NULL, NULL),
(48, 11, 'organic tomato', '2025-05-31 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.221', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4708', NULL, NULL),
(49, NULL, 'seasonal vegetables', '2025-06-01 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.48', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9045', NULL, NULL),
(50, NULL, 'local produce', '2025-06-01 20:49:01', 3, '2025-06-01 20:53:58', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.11', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2541', 1, NULL),
(51, 16, 'salad', '2025-06-01 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.28', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9543', NULL, NULL),
(52, 11, 'organic potatoes', '2025-06-01 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.213', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3950', NULL, NULL),
(53, 16, 'fresh corn', '2025-06-01 20:49:01', 1, '2025-06-01 20:51:36', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.52', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1435', 2, NULL),
(54, 16, 'salad', '2025-06-02 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.255', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1361', NULL, NULL),
(55, 1, 'organic fruits', '2025-06-02 20:49:01', 1, '2025-06-02 20:49:34', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.148', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9441', 1, NULL),
(56, 1, 'carrots', '2025-06-02 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.90', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5241', NULL, NULL),
(57, 9, 'organic corn', '2025-06-02 20:49:01', 1, '2025-06-02 20:51:08', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.66', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1764', 1, NULL),
(58, 11, 'seasonal vegetables', '2025-06-02 20:49:01', 2, '2025-06-02 20:50:23', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.121', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4087', 1, NULL),
(59, 2, 'farm fresh', '2025-06-02 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.20', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5470', NULL, NULL),
(60, 9, 'fresh lettuce', '2025-06-02 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.212', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4695', NULL, NULL),
(61, 16, 'organic tomato', '2025-06-02 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.188', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5414', NULL, NULL),
(62, NULL, 'fresh lettuce', '2025-06-02 20:49:01', 3, '2025-06-02 20:51:17', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.121', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9872', 1, NULL),
(63, NULL, 'tomatoes', '2025-06-02 20:49:01', 1, '2025-06-02 20:49:29', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.86', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6609', 1, NULL),
(64, NULL, 'carrots', '2025-06-02 20:49:01', 1, '2025-06-02 20:51:17', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.211', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9492', 2, NULL),
(65, 11, 'tomatoes', '2025-06-02 20:49:01', 1, '2025-06-02 20:51:11', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.250', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7932', 2, NULL),
(66, 9, 'tomato', '2025-06-02 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.3', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3149', NULL, NULL),
(67, 1, 'vegetables', '2025-06-03 20:49:01', 1, '2025-06-03 20:52:43', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.66', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8814', 1, NULL),
(68, 2, 'tomato', '2025-06-03 20:49:01', 4, '2025-06-03 20:53:24', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.174', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7976', 1, NULL),
(69, 2, 'lettuce', '2025-06-03 20:49:01', 2, '2025-06-03 20:51:42', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.64', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9591', 1, NULL),
(70, 16, 'fresh lettuce', '2025-06-03 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.29', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1107', NULL, NULL),
(71, 1, 'fresh fruits', '2025-06-03 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.67', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3250', NULL, NULL),
(72, 11, 'lettuce', '2025-06-04 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.5', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5717', NULL, NULL),
(73, 11, 'organic potatoes', '2025-06-04 20:49:01', 1, '2025-06-04 20:49:08', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.198', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2657', 2, NULL),
(74, 16, 'fruits', '2025-06-04 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.254', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8841', NULL, NULL),
(75, 1, 'lettuce', '2025-06-04 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.110', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8145', NULL, NULL),
(76, 2, 'organic lettuce', '2025-06-04 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.231', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6358', NULL, NULL),
(77, 9, 'carrots', '2025-06-04 20:49:01', 2, '2025-06-04 20:52:29', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.192', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7106', 1, NULL),
(78, 9, 'potatoes', '2025-06-04 20:49:01', 2, '2025-06-04 20:49:02', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.156', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4736', 1, NULL),
(79, 2, 'organic onions', '2025-06-04 20:49:01', 2, '2025-06-04 20:52:04', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.218', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3579', 2, NULL),
(80, 2, 'fresh vegetables', '2025-06-04 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.35', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2082', NULL, NULL),
(81, 16, 'farm fresh', '2025-06-04 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.76', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7185', NULL, NULL),
(82, 11, 'corn', '2025-06-04 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.45', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9015', NULL, NULL),
(83, 9, 'organic tomato', '2025-06-04 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.89', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2689', NULL, NULL),
(84, 11, 'local produce', '2025-06-04 20:49:01', 3, '2025-06-04 20:53:41', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.138', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5724', 2, NULL),
(85, 2, 'vegetables', '2025-06-05 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.12', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9909', NULL, NULL),
(86, 16, 'salad', '2025-06-05 20:49:01', 3, '2025-06-05 20:53:26', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.221', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2879', 2, NULL),
(87, NULL, 'organic corn', '2025-06-05 20:49:01', 4, '2025-06-05 20:53:31', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.112', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4432', 2, NULL),
(88, 16, 'farm fresh', '2025-06-05 20:49:01', 2, '2025-06-05 20:50:59', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.172', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3660', 1, NULL),
(89, 1, 'potatoes', '2025-06-05 20:49:01', 1, '2025-06-05 20:54:00', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.123', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6320', 2, NULL),
(90, 11, 'fresh onions', '2025-06-05 20:49:01', 2, '2025-06-05 20:53:18', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.62', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5843', 2, NULL),
(91, 2, 'organic lettuce', '2025-06-05 20:49:01', 2, '2025-06-05 20:53:15', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.233', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1585', 2, NULL),
(92, 9, 'fresh vegetables', '2025-06-05 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.128', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9435', NULL, NULL),
(93, NULL, 'fresh corn', '2025-06-05 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.213', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8207', NULL, NULL),
(94, 1, 'organic corn', '2025-06-05 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.231', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3761', NULL, NULL),
(95, 9, 'seasonal vegetables', '2025-06-05 20:49:01', 3, '2025-06-05 20:51:41', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.137', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6209', 1, NULL),
(96, 11, 'farm fresh', '2025-06-05 20:49:01', 3, '2025-06-05 20:49:44', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.45', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6512', 2, NULL),
(97, 2, 'fresh potatoes', '2025-06-05 20:49:01', 1, '2025-06-05 20:50:31', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.185', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9885', 1, NULL),
(98, 1, 'salad', '2025-06-05 20:49:01', 4, '2025-06-05 20:49:48', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.244', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5461', 2, NULL),
(99, 2, 'farm fresh', '2025-06-05 20:49:01', 2, '2025-06-05 20:49:51', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.40', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5412', 1, NULL),
(100, 2, 'fresh fruits', '2025-06-06 20:49:01', 1, '2025-06-06 20:52:41', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.46', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8840', 1, NULL),
(101, 9, 'fresh corn', '2025-06-06 20:49:01', 4, '2025-06-06 20:52:13', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.169', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1134', 2, NULL),
(102, NULL, 'seasonal vegetables', '2025-06-06 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.194', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2053', NULL, NULL),
(103, 16, 'fresh vegetables', '2025-06-06 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.89', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3307', NULL, NULL),
(104, 1, 'organic vegetables', '2025-06-06 20:49:01', 4, '2025-06-06 20:50:24', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.96', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6897', 1, NULL),
(105, 9, 'farm fresh', '2025-06-06 20:49:01', 1, '2025-06-06 20:51:06', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.229', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7954', 1, NULL),
(106, 11, 'organic vegetables', '2025-06-06 20:49:01', 3, '2025-06-06 20:49:27', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.69', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6125', 1, NULL),
(107, 9, 'tomatoes', '2025-06-06 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.177', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2600', NULL, NULL),
(108, 9, 'organic lettuce', '2025-06-07 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.122', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4768', NULL, NULL),
(109, 9, 'vegetables', '2025-06-07 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.129', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2673', NULL, NULL),
(110, 11, 'fruits', '2025-06-07 20:49:01', 3, '2025-06-07 20:52:22', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.50', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9257', 2, NULL),
(111, 9, 'seasonal vegetables', '2025-06-07 20:49:01', 4, '2025-06-07 20:50:44', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.222', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3481', 1, NULL),
(112, 11, 'fresh vegetables', '2025-06-07 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.202', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5628', NULL, NULL),
(113, 11, 'sweet corn', '2025-06-07 20:49:01', 5, '2025-06-07 20:50:24', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.235', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2788', 2, NULL),
(114, NULL, 'local produce', '2025-06-07 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.11', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9036', NULL, NULL),
(115, 11, 'fresh vegetables', '2025-06-07 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.232', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2732', NULL, NULL),
(116, NULL, 'fresh onions', '2025-06-07 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.194', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6369', NULL, NULL),
(117, 11, 'organic potatoes', '2025-06-07 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.115', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5043', NULL, NULL),
(118, 11, 'lettuce', '2025-06-07 20:49:01', 2, '2025-06-07 20:52:22', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.144', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4465', 2, NULL),
(119, 11, 'fresh potatoes', '2025-06-07 20:49:01', 4, '2025-06-07 20:52:11', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.233', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4074', 2, NULL),
(120, NULL, 'tomatoes', '2025-06-07 20:49:01', 5, '2025-06-07 20:52:31', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.57', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8734', 1, NULL),
(121, 16, 'tomatoes', '2025-06-08 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.232', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6706', NULL, NULL),
(122, 1, 'carrots', '2025-06-08 20:49:01', 5, '2025-06-08 20:52:10', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.189', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5766', 2, NULL),
(123, 1, 'farm fresh', '2025-06-08 20:49:01', 1, '2025-06-08 20:51:29', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.62', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7049', 2, NULL),
(124, 1, 'seasonal vegetables', '2025-06-08 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.117', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5359', NULL, NULL),
(125, 2, 'fresh corn', '2025-06-08 20:49:01', 4, '2025-06-08 20:51:54', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.17', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7571', 1, NULL),
(126, 2, 'vegetables', '2025-06-09 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.204', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3309', NULL, NULL),
(127, 1, 'tomato', '2025-06-09 20:49:01', 1, '2025-06-09 20:51:10', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.215', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5620', 1, NULL),
(128, 11, 'potatoes', '2025-06-09 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.160', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7802', NULL, NULL),
(129, 2, 'fresh vegetables', '2025-06-09 20:49:01', 2, '2025-06-09 20:53:49', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.198', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3540', 2, NULL),
(130, 2, 'organic potatoes', '2025-06-09 20:49:01', 2, '2025-06-09 20:50:06', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.187', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1214', 1, NULL),
(131, 1, 'local produce', '2025-06-09 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.225', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8703', NULL, NULL),
(132, 16, 'fruits', '2025-06-10 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.247', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4502', NULL, NULL),
(133, 9, 'onions', '2025-06-10 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.124', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7934', NULL, NULL),
(134, 11, 'fresh potatoes', '2025-06-10 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.31', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9989', NULL, NULL),
(135, 2, 'tomato', '2025-06-10 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.97', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2510', NULL, NULL),
(136, 11, 'farm fresh', '2025-06-10 20:49:01', 3, '2025-06-10 20:50:44', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.226', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4858', 1, NULL),
(137, 9, 'fresh potatoes', '2025-06-10 20:49:01', 2, '2025-06-10 20:52:22', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.31', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7209', 1, NULL),
(138, 9, 'sweet corn', '2025-06-11 20:49:01', 2, '2025-06-11 20:49:08', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.102', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8204', 2, NULL),
(139, 2, 'fresh lettuce', '2025-06-11 20:49:01', 1, '2025-06-11 20:49:39', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.16', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6691', 2, NULL),
(140, 2, 'local produce', '2025-06-11 20:49:01', 1, '2025-06-11 20:49:46', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.168', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9691', 2, NULL),
(141, 9, 'organic tomato', '2025-06-11 20:49:01', 2, '2025-06-11 20:51:05', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.85', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5509', 1, NULL),
(142, 11, 'organic lettuce', '2025-06-11 20:49:01', 1, '2025-06-11 20:53:48', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.12', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1965', 1, NULL),
(143, 16, 'potatoes', '2025-06-11 20:49:01', 3, '2025-06-11 20:50:37', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.117', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3642', 2, NULL),
(144, NULL, 'local produce', '2025-06-11 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.48', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7668', NULL, NULL),
(145, 9, 'organic corn', '2025-06-12 20:49:01', 5, '2025-06-12 20:50:32', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.145', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1957', 2, NULL),
(146, 9, 'fresh onions', '2025-06-12 20:49:01', 1, '2025-06-12 20:53:11', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.38', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4005', 1, NULL),
(147, 9, 'onions', '2025-06-12 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.200', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1565', NULL, NULL),
(148, 16, 'potatoes', '2025-06-12 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.89', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3557', NULL, NULL),
(149, 11, 'vegetables', '2025-06-12 20:49:01', 2, '2025-06-12 20:49:32', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.197', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9967', 2, NULL),
(150, 2, 'organic onions', '2025-06-12 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.136', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7159', NULL, NULL),
(151, 2, 'fresh carrots', '2025-06-12 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.234', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4296', NULL, NULL),
(152, 16, 'fresh corn', '2025-06-12 20:49:01', 4, '2025-06-12 20:51:37', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.168', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9015', 2, NULL),
(153, 1, 'lettuce', '2025-06-12 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.151', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7166', NULL, NULL),
(154, NULL, 'carrots', '2025-06-13 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.227', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8728', NULL, NULL),
(155, 11, 'corn', '2025-06-13 20:49:01', 2, '2025-06-13 20:49:21', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.232', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7149', 2, NULL),
(156, 9, 'local produce', '2025-06-13 20:49:01', 3, '2025-06-13 20:51:24', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.72', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1668', 2, NULL),
(157, 9, 'organic vegetables', '2025-06-13 20:49:01', 1, '2025-06-13 20:53:34', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.220', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9482', 1, NULL),
(158, 2, 'corn', '2025-06-13 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.155', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2617', NULL, NULL),
(159, 1, 'organic lettuce', '2025-06-14 20:49:01', 2, '2025-06-14 20:52:43', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.200', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7709', 2, NULL),
(160, 9, 'organic vegetables', '2025-06-14 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.115', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7698', NULL, NULL),
(161, NULL, 'onions', '2025-06-14 20:49:01', 1, '2025-06-14 20:49:16', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.66', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8224', 1, NULL),
(162, 16, 'fresh corn', '2025-06-14 20:49:01', 3, '2025-06-14 20:50:28', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.67', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1244', 1, NULL),
(163, 16, 'fresh onions', '2025-06-14 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.224', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9932', NULL, NULL),
(164, 9, 'organic onions', '2025-06-14 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.22', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6032', NULL, NULL),
(165, 1, 'fresh vegetables', '2025-06-14 20:49:01', 3, '2025-06-14 20:50:35', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.135', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6248', 2, NULL),
(166, 2, 'tomatoes', '2025-06-14 20:49:01', 2, '2025-06-14 20:52:12', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.229', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8372', 2, NULL),
(167, NULL, 'sweet corn', '2025-06-14 20:49:01', 2, '2025-06-14 20:50:23', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.141', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8675', 1, NULL),
(168, 1, 'potatoes', '2025-06-14 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.44', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2494', NULL, NULL),
(169, 2, 'fresh corn', '2025-06-14 20:49:01', 2, '2025-06-14 20:51:26', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.203', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2996', 1, NULL),
(170, NULL, 'fresh carrots', '2025-06-14 20:49:01', 3, '2025-06-14 20:51:07', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.14', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8114', 1, NULL),
(171, 11, 'organic onions', '2025-06-14 20:49:01', 3, '2025-06-14 20:52:19', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.60', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3506', 1, NULL),
(172, 2, 'vegetables', '2025-06-14 20:49:01', 4, '2025-06-14 20:50:37', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.114', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4649', 2, NULL),
(173, 1, 'lettuce', '2025-06-14 20:49:01', 1, '2025-06-14 20:52:44', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.66', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4563', 2, NULL),
(174, 2, 'local produce', '2025-06-15 20:49:01', 4, '2025-06-15 20:51:18', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.34', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4734', 2, NULL),
(175, 2, 'onions', '2025-06-15 20:49:01', 4, '2025-06-15 20:49:50', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.57', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2439', 2, NULL),
(176, NULL, 'organic fruits', '2025-06-15 20:49:01', 4, '2025-06-15 20:52:46', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.33', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8146', 1, NULL),
(177, NULL, 'carrots', '2025-06-15 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.216', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9087', NULL, NULL),
(178, 2, 'tomatoes', '2025-06-15 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.189', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5789', NULL, NULL),
(179, 11, 'fresh carrots', '2025-06-15 20:49:01', 1, '2025-06-15 20:53:59', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.14', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9534', 1, NULL),
(180, NULL, 'fresh vegetables', '2025-06-15 20:49:01', 1, '2025-06-15 20:52:47', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.234', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5714', 1, NULL),
(181, 2, 'seasonal vegetables', '2025-06-15 20:49:01', 5, '2025-06-15 20:51:13', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.111', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6995', 1, NULL),
(182, 1, 'carrots', '2025-06-15 20:49:01', 3, '2025-06-15 20:50:44', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.137', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4140', 2, NULL),
(183, 11, 'fresh vegetables', '2025-06-15 20:49:01', 4, '2025-06-15 20:51:21', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.191', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3873', 2, NULL),
(184, 1, 'tomatoes', '2025-06-15 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.226', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7187', NULL, NULL),
(185, 1, 'organic lettuce', '2025-06-16 20:49:01', 2, '2025-06-16 20:50:08', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.39', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6381', 1, NULL),
(186, 9, 'vegetables', '2025-06-16 20:49:01', 5, '2025-06-16 20:50:05', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.119', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7708', 1, NULL),
(187, 16, 'fresh vegetables', '2025-06-16 20:49:01', 2, '2025-06-16 20:53:20', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.90', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5613', 2, NULL),
(188, 11, 'vegetables', '2025-06-16 20:49:01', 1, '2025-06-16 20:52:15', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.104', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1621', 2, NULL),
(189, 1, 'organic tomato', '2025-06-16 20:49:01', 1, '2025-06-16 20:53:19', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.80', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1973', 1, NULL),
(190, 9, 'potatoes', '2025-06-16 20:49:01', 2, '2025-06-16 20:49:41', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.84', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2358', 2, NULL),
(191, 11, 'lettuce', '2025-06-16 20:49:01', 5, '2025-06-16 20:52:27', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.142', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3348', 1, NULL),
(192, 11, 'vegetables', '2025-06-16 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.71', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5127', NULL, NULL),
(193, NULL, 'sweet corn', '2025-06-16 20:49:01', 5, '2025-06-16 20:53:06', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.156', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7297', 1, NULL),
(194, 16, 'organic onions', '2025-06-16 20:49:01', 5, '2025-06-16 20:53:43', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.57', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4912', 2, NULL),
(195, 1, 'seasonal vegetables', '2025-06-17 20:49:01', 2, '2025-06-17 20:52:21', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.171', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7120', 2, NULL),
(196, 16, 'tomatoes', '2025-06-17 20:49:01', 4, '2025-06-17 20:49:44', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.120', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1604', 2, NULL),
(197, NULL, 'seasonal vegetables', '2025-06-17 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.170', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1728', NULL, NULL),
(198, 9, 'carrots', '2025-06-17 20:49:01', 1, '2025-06-17 20:53:17', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.111', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5172', 2, NULL),
(199, 11, 'seasonal vegetables', '2025-06-17 20:49:01', 2, '2025-06-17 20:52:41', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.132', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1812', 1, NULL),
(200, 16, 'organic potatoes', '2025-06-18 20:49:01', 3, '2025-06-18 20:49:07', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.18', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8283', 2, NULL),
(201, NULL, 'organic potatoes', '2025-06-18 20:49:01', 3, '2025-06-18 20:53:18', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.49', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8099', 1, NULL),
(202, 1, 'onions', '2025-06-18 20:49:01', 2, '2025-06-18 20:51:04', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.107', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5511', 1, NULL),
(203, 2, 'organic potatoes', '2025-06-18 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.48', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3022', NULL, NULL),
(204, NULL, 'farm fresh', '2025-06-19 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.85', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5486', NULL, NULL),
(205, 16, 'organic vegetables', '2025-06-19 20:49:01', 1, '2025-06-19 20:51:58', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.166', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4388', 1, NULL),
(206, NULL, 'fresh vegetables', '2025-06-19 20:49:01', 4, '2025-06-19 20:53:12', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.122', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8852', 1, NULL),
(207, 11, 'fresh carrots', '2025-06-19 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.27', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4010', NULL, NULL),
(208, NULL, 'organic tomato', '2025-06-19 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.185', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7801', NULL, NULL),
(209, 11, 'vegetables', '2025-06-19 20:49:01', 1, '2025-06-19 20:49:49', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.74', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9702', 1, NULL),
(210, 16, 'potatoes', '2025-06-19 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.206', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1642', NULL, NULL),
(211, 1, 'fresh vegetables', '2025-06-19 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.17', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6494', NULL, NULL),
(212, 1, 'organic tomato', '2025-06-19 20:49:01', 1, '2025-06-19 20:52:55', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.164', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7827', 1, NULL),
(213, 2, 'fresh corn', '2025-06-19 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.68', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7110', NULL, NULL),
(214, NULL, 'fruits', '2025-06-19 20:49:01', 3, '2025-06-19 20:50:28', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.140', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9915', 2, NULL),
(215, 1, 'fruits', '2025-06-19 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.198', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5071', NULL, NULL),
(216, 11, 'fruits', '2025-06-19 20:49:01', 1, '2025-06-19 20:49:28', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.130', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6315', 1, NULL),
(217, NULL, 'fresh potatoes', '2025-06-20 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.36', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7846', NULL, NULL),
(218, 16, 'fresh lettuce', '2025-06-20 20:49:01', 2, '2025-06-20 20:50:26', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.51', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9370', 1, NULL),
(219, 1, 'organic fruits', '2025-06-20 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.130', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1839', NULL, NULL),
(220, 2, 'carrots', '2025-06-20 20:49:01', 4, '2025-06-20 20:51:30', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.118', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2142', 2, NULL),
(221, NULL, 'corn', '2025-06-20 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.210', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1229', NULL, NULL),
(222, NULL, 'onions', '2025-06-20 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.67', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8161', NULL, NULL),
(223, NULL, 'organic potatoes', '2025-06-21 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.203', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7971', NULL, NULL),
(224, 2, 'organic corn', '2025-06-21 20:49:01', 1, '2025-06-21 20:50:35', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.243', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6080', 1, NULL),
(225, 16, 'salad', '2025-06-21 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.109', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2450', NULL, NULL),
(226, 1, 'salad', '2025-06-21 20:49:01', 2, '2025-06-21 20:50:03', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.178', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1865', 1, NULL),
(227, 11, 'tomatoes', '2025-06-21 20:49:01', 1, '2025-06-21 20:49:41', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.194', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8946', 1, NULL),
(228, 11, 'fresh vegetables', '2025-06-21 20:49:01', 5, '2025-06-21 20:49:35', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.207', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3072', 2, NULL),
(229, NULL, 'potatoes', '2025-06-21 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.79', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6672', NULL, NULL),
(230, 2, 'fresh corn', '2025-06-21 20:49:01', 1, '2025-06-21 20:53:15', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.28', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1414', 1, NULL),
(231, 16, 'fresh vegetables', '2025-06-21 20:49:01', 1, '2025-06-21 20:51:31', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.110', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6600', 2, NULL),
(232, 11, 'tomatoes', '2025-06-21 20:49:01', 4, '2025-06-21 20:52:43', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.214', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9921', 1, NULL),
(233, 1, 'organic carrots', '2025-06-21 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.50', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3759', NULL, NULL),
(234, NULL, 'corn', '2025-06-21 20:49:01', 2, '2025-06-21 20:49:45', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.175', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6177', 2, NULL),
(235, 9, 'farm fresh', '2025-06-21 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.1', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4944', NULL, NULL),
(236, 1, 'fresh corn', '2025-06-21 20:49:01', 1, '2025-06-21 20:51:46', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.90', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5508', 1, NULL),
(237, NULL, 'fresh potatoes', '2025-06-21 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.239', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4288', NULL, NULL),
(238, 16, 'corn', '2025-06-22 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.228', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3217', NULL, NULL),
(239, 9, 'fresh lettuce', '2025-06-22 20:49:01', 4, '2025-06-22 20:51:28', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.209', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6654', 1, NULL),
(240, 9, 'organic vegetables', '2025-06-22 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.62', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1044', NULL, NULL),
(241, 1, 'onions', '2025-06-22 20:49:01', 3, '2025-06-22 20:49:33', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.197', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7109', 1, NULL),
(242, 16, 'organic fruits', '2025-06-22 20:49:01', 1, '2025-06-22 20:49:10', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.78', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5830', 1, NULL),
(243, 16, 'vegetables', '2025-06-22 20:49:01', 5, '2025-06-22 20:52:26', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.30', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3801', 2, NULL),
(244, NULL, 'fresh potatoes', '2025-06-22 20:49:01', 2, '2025-06-22 20:50:39', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.149', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7340', 2, NULL),
(245, 16, 'farm fresh', '2025-06-22 20:49:01', 1, '2025-06-22 20:52:48', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.3', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8720', 2, NULL),
(246, 2, 'carrots', '2025-06-22 20:49:01', 3, '2025-06-22 20:52:45', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.177', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5285', 1, NULL),
(247, 11, 'tomato', '2025-06-22 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.83', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5001', NULL, NULL),
(248, 1, 'tomato', '2025-06-22 20:49:01', 3, '2025-06-22 20:49:16', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.53', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4457', 1, NULL);
INSERT INTO `search_logs` (`log_id`, `user_id`, `keyword`, `search_date`, `click_position`, `clicked_at`, `filters`, `results_count`, `ip_address`, `user_agent`, `session_id`, `clicked_product_id`, `clicked_vendor_id`) VALUES
(249, 2, 'fresh potatoes', '2025-06-22 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.179', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8954', NULL, NULL),
(250, NULL, 'fruits', '2025-06-22 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.31', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8577', NULL, NULL),
(251, 16, 'local produce', '2025-06-22 20:49:01', 2, '2025-06-22 20:49:53', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.204', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8379', 2, NULL),
(252, NULL, 'fresh lettuce', '2025-06-22 20:49:01', 3, '2025-06-22 20:53:19', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.61', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5888', 2, NULL),
(253, 11, 'onions', '2025-06-23 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.244', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4249', NULL, NULL),
(254, 1, 'fresh vegetables', '2025-06-23 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.217', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9446', NULL, NULL),
(255, 2, 'lettuce', '2025-06-23 20:49:01', 5, '2025-06-23 20:51:18', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.94', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9204', 2, NULL),
(256, 9, 'organic tomato', '2025-06-23 20:49:01', 1, '2025-06-23 20:51:55', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.31', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7643', 1, NULL),
(257, 11, 'carrots', '2025-06-23 20:49:01', 1, '2025-06-23 20:50:38', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.32', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4614', 1, NULL),
(258, 9, 'organic potatoes', '2025-06-23 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.188', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5540', NULL, NULL),
(259, 1, 'fresh fruits', '2025-06-23 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.188', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2963', NULL, NULL),
(260, 11, 'corn', '2025-06-23 20:49:01', 4, '2025-06-23 20:50:57', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.86', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5880', 2, NULL),
(261, NULL, 'corn', '2025-06-23 20:49:01', 2, '2025-06-23 20:51:21', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.55', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9727', 2, NULL),
(262, 9, 'fresh corn', '2025-06-24 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.221', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5375', NULL, NULL),
(263, 1, 'potatoes', '2025-06-24 20:49:01', 3, '2025-06-24 20:49:23', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.27', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1277', 2, NULL),
(264, 9, 'organic lettuce', '2025-06-24 20:49:01', 2, '2025-06-24 20:51:13', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.75', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3128', 2, NULL),
(265, 11, 'vegetables', '2025-06-25 20:49:01', 2, '2025-06-25 20:51:42', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.83', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8368', 2, NULL),
(266, 16, 'fresh fruits', '2025-06-25 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.225', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8457', NULL, NULL),
(267, 11, 'organic onions', '2025-06-25 20:49:01', 1, '2025-06-25 20:52:18', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.197', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5740', 1, NULL),
(268, 16, 'onions', '2025-06-25 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.87', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3749', NULL, NULL),
(269, 2, 'sweet corn', '2025-06-25 20:49:01', 1, '2025-06-25 20:51:58', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.245', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2244', 2, NULL),
(270, 2, 'organic onions', '2025-06-25 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.205', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9140', NULL, NULL),
(271, 16, 'fresh onions', '2025-06-25 20:49:01', 1, '2025-06-25 20:53:21', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.211', 'Mozilla/5.0 (Demo Browser)', 'demo_session_9663', 2, NULL),
(272, 11, 'fresh vegetables', '2025-06-25 20:49:01', 1, '2025-06-25 20:54:00', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.183', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4998', 2, NULL),
(273, 16, 'fresh onions', '2025-06-25 20:49:01', 1, '2025-06-25 20:53:53', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.132', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5526', 1, NULL),
(274, 11, 'organic lettuce', '2025-06-25 20:49:01', 2, '2025-06-25 20:53:00', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.129', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8767', 1, NULL),
(275, NULL, 'farm fresh', '2025-06-25 20:49:01', 2, '2025-06-25 20:50:40', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.124', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2149', 2, NULL),
(276, 2, 'organic corn', '2025-06-25 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.151', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7630', NULL, NULL),
(277, 16, 'seasonal vegetables', '2025-06-25 20:49:01', 3, '2025-06-25 20:49:23', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.197', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6226', 1, NULL),
(278, 16, 'onions', '2025-06-26 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.195', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1135', NULL, NULL),
(279, 9, 'fresh fruits', '2025-06-26 20:49:01', 2, '2025-06-26 20:52:26', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.193', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8543', 1, NULL),
(280, 9, 'vegetables', '2025-06-26 20:49:01', 1, '2025-06-26 20:52:08', '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.41', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2238', 2, NULL),
(281, 9, 'fresh lettuce', '2025-06-26 20:49:01', 2, '2025-06-26 20:49:51', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.7', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5764', 1, NULL),
(282, 16, 'fresh vegetables', '2025-06-26 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.78', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7086', NULL, NULL),
(283, 16, 'fresh vegetables', '2025-06-26 20:49:01', 3, '2025-06-26 20:51:14', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.139', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8919', 1, NULL),
(284, 16, 'fresh vegetables', '2025-06-26 20:49:01', 1, '2025-06-26 20:53:44', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.173', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6492', 1, NULL),
(285, 11, 'organic tomato', '2025-06-26 20:49:01', 1, '2025-06-26 20:51:17', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.111', 'Mozilla/5.0 (Demo Browser)', 'demo_session_6617', 1, NULL),
(286, 16, 'organic tomato', '2025-06-26 20:49:01', 2, '2025-06-26 20:51:36', '{\"category_id\":\"\",\"search_type\":\"product\"}', 8, '192.168.1.209', 'Mozilla/5.0 (Demo Browser)', 'demo_session_1817', 1, NULL),
(287, 11, 'local produce', '2025-06-26 20:49:01', 1, '2025-06-26 20:53:20', '{\"category_id\":\"\",\"search_type\":\"product\"}', 4, '192.168.1.251', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3153', 2, NULL),
(288, NULL, 'organic potatoes', '2025-06-26 20:49:01', 4, '2025-06-26 20:53:41', '{\"category_id\":\"\",\"search_type\":\"product\"}', 6, '192.168.1.185', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2806', 1, NULL),
(289, 16, 'fruits', '2025-06-26 20:49:01', 1, '2025-06-26 20:49:57', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.74', 'Mozilla/5.0 (Demo Browser)', 'demo_session_5179', 2, NULL),
(290, 16, 'fruits', '2025-06-26 20:49:01', 1, '2025-06-26 20:53:54', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '192.168.1.40', 'Mozilla/5.0 (Demo Browser)', 'demo_session_7789', 2, NULL),
(291, 1, 'fresh onions', '2025-06-26 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.240', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2474', NULL, NULL),
(292, 16, 'onions', '2025-06-26 20:49:01', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 3, '192.168.1.51', 'Mozilla/5.0 (Demo Browser)', 'demo_session_2044', NULL, NULL),
(293, 11, 'organic onions', '2025-06-27 20:49:02', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.169', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8337', NULL, NULL),
(294, 16, 'fresh onions', '2025-06-27 20:49:02', 1, '2025-06-27 20:51:20', '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '192.168.1.209', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4560', 1, NULL),
(295, NULL, 'fresh onions', '2025-06-27 20:49:02', 1, '2025-06-27 20:52:30', '{\"category_id\":\"\",\"search_type\":\"product\"}', 2, '192.168.1.151', 'Mozilla/5.0 (Demo Browser)', 'demo_session_8319', 1, NULL),
(296, 9, 'salad', '2025-06-27 20:49:02', 2, '2025-06-27 20:53:18', '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.193', 'Mozilla/5.0 (Demo Browser)', 'demo_session_3755', 2, NULL),
(297, 16, 'corn', '2025-06-27 20:49:02', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 7, '192.168.1.150', 'Mozilla/5.0 (Demo Browser)', 'demo_session_4551', NULL, NULL),
(298, NULL, 'test tomato search', '2025-06-28 04:51:08', 1, '2025-06-27 20:51:08', '{\"category_id\":\"\",\"search_type\":\"product\"}', 5, '127.0.0.1', 'Test Browser/1.0', 'test_session_1751086268', 1, NULL),
(299, NULL, 'test onions 1751086505', '2025-06-28 04:55:05', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 0, '', '', '3988bd8ea16e1bf79359a9859569093b', NULL, NULL),
(300, 17, 'a', '2025-06-28 04:56:59', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '4oomuk6t5rpms7shb7fg5b80u7', NULL, NULL),
(301, 17, 'v', '2025-06-28 04:57:44', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 0, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '4oomuk6t5rpms7shb7fg5b80u7', NULL, NULL),
(302, 17, 's', '2025-06-28 04:57:45', NULL, NULL, '{\"category_id\":\"\",\"search_type\":\"product\"}', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '4oomuk6t5rpms7shb7fg5b80u7', NULL, NULL),
(303, 17, 'test', '2025-06-29 05:01:39', NULL, NULL, '{\"subscription_tier\":\"\",\"search_type\":\"vendor\"}', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '5o3cbnum5bf7mt7rhlnf68sbss', NULL, NULL),
(304, 17, 'test', '2025-06-29 05:19:14', 1, '2025-06-28 23:20:04', '{\"subscription_tier\":\"\",\"search_type\":\"vendor\",\"has_search_term\":true}', 3, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36', '5o3cbnum5bf7mt7rhlnf68sbss', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `hire_date` date NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `performance_rating` decimal(3,2) DEFAULT NULL,
  `status` enum('active','inactive','terminated') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_archive` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `user_id`, `email`, `employee_id`, `department`, `position`, `hire_date`, `salary`, `phone`, `emergency_contact`, `address`, `performance_rating`, `status`, `created_at`, `updated_at`, `is_archive`) VALUES
(1, 21, '', 'EMP0001', 'Research & Development', 'Assistant', '2025-06-28', 13231.00, '123123123123', '123', '123123123', 0.00, 'active', '2025-06-27 03:34:30', '2025-07-02 10:39:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `staff_tasks`
--

CREATE TABLE `staff_tasks` (
  `task_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `task_description` text DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `due_date` date DEFAULT NULL,
  `assigned_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_date` timestamp NULL DEFAULT NULL,
  `is_archive` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff_tasks`
--

INSERT INTO `staff_tasks` (`task_id`, `staff_id`, `task_title`, `task_description`, `priority`, `status`, `due_date`, `assigned_date`, `completed_date`, `is_archive`, `created_at`, `updated_at`) VALUES
(1, 1, 'adsf', 'adsf', 'low', 'completed', '2025-07-26', '2025-07-02 10:39:47', '2025-07-02 10:43:02', 0, '2025-07-02 10:39:47', '2025-07-02 10:43:02');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_tiers`
--

CREATE TABLE `subscription_tiers` (
  `tier_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `monthly_fee` decimal(10,2) NOT NULL,
  `commission_percent` decimal(5,2) NOT NULL,
  `description` text DEFAULT NULL,
  `is_archive` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription_tiers`
--

INSERT INTO `subscription_tiers` (`tier_id`, `name`, `monthly_fee`, `commission_percent`, `description`, `is_archive`) VALUES
(1, 'Bronze', 0.00, 15.00, 'Basic plan with 15% commission.', 0),
(2, 'Silver', 49.90, 11.00, 'Intermediate plan with 11% commission.', 0),
(3, 'Gold', 89.90, 7.00, 'Premium plan with 7% commission.', 0),
(4, 'Platinum', 119.90, 5.00, 'Ultimate plan with 5% commission.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','vendor','customer','staff') DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `is_archive` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password`, `role`, `profile_picture`, `is_active`, `email_verified_at`, `phone_verified_at`, `last_login_at`, `remember_token`, `is_archive`, `created_at`, `updated_at`) VALUES
(1, 'admin1', 'admin1@agrimarket.com', NULL, '$2y$10$VDeO8MI09SDjMVWzk9tmruK.n1EHpSKrBCVazRCZycdFiGjsHffpC', 'admin', NULL, 1, NULL, NULL, '2025-07-04 05:31:05', NULL, 0, '2025-06-19 14:39:41', '2025-07-04 05:31:05'),
(2, 'admin2', 'admin2@agrimarket.com', NULL, '$2y$10$kl4SktklyYQdRxYqq7PLeuFja.77aVWkPdwMXb44gJjuwLnOhpxyi', 'admin', NULL, 1, NULL, NULL, NULL, NULL, 0, '2025-06-19 14:39:53', '2025-06-20 13:50:04'),
(3, 'Vendor A', 'vendorA@example.com', NULL, '$2y$10$TSDsfclxAVwtdOII6l83g.MjsGl8wb6cbWOqNOP5s1sGpQxGq9MwK', 'vendor', NULL, 1, NULL, NULL, NULL, NULL, 0, '2025-06-19 14:40:30', '2025-06-21 14:44:43'),
(5, 'John Smith', 'john@smithfarm.com', NULL, '$2y$10$xInG4K4NR5NtrFy0lauEwe4eqYKHdpYwV3Ae84CiyA75R679Ioyd.', 'vendor', NULL, 1, NULL, NULL, NULL, NULL, 0, '2025-06-21 11:19:22', '2025-06-21 15:00:43'),
(6, 'Test User Gold', 'gold@testbusiness.com', NULL, '$2y$10$VDeO8MI09SDjMVWzk9tmruK.n1EHpSKrBCVazRCZycdFiGjsHffpC', 'vendor', NULL, 1, NULL, NULL, '2025-07-04 05:30:17', NULL, 0, '2025-06-21 11:31:21', '2025-07-04 05:30:17'),
(7, 'Vendor Test 2', 'vendor2@example.com', NULL, '$2y$10$xR1FOYBRZD9L5vAnDIdLg.m/pfx0VbQCoJCNb3ddJDYx/C8FFPeOO', 'vendor', NULL, 1, NULL, NULL, NULL, NULL, 0, '2025-06-21 11:33:22', '2025-06-21 11:33:22'),
(8, 'test', 'test@gmail.com', '+60123456789', '$2y$10$qNJsIKGvt6c.bS5.xJHXkuFDUSAEjqiLEz47lTXJ12uNZq7PSoifq', 'customer', NULL, 1, NULL, NULL, '2025-06-21 14:44:41', NULL, 0, '2025-06-21 12:36:33', '2025-06-21 14:44:41'),
(9, 'C1', 'c1@gmail.com', '+60123456789', '$2y$10$Fnv0TIMu/H3ZZP6s8oupRO4AmnrnufeSRl5H/o23FGhB2TdI4eT6y', 'customer', NULL, 1, NULL, NULL, NULL, NULL, 0, '2025-06-21 14:15:28', '2025-06-21 14:15:28'),
(10, 'v1', 'v1@gmail.com', NULL, '$2y$10$SFmLeJoJkCcwv.JpBghNyuFWvfOJ7JJyrl1YceAtYxT5I6N2zpufm', 'vendor', NULL, 1, NULL, NULL, NULL, NULL, 0, '2025-06-21 14:16:42', '2025-06-21 14:16:42'),
(11, 'c2', 'c2@gmail.com', '+60123456789', '$2y$10$YONe9Fazgbq7f1XmmnFSEuDNTeztLFMjs/zyrFkcRq9MfppyBYWGG', 'customer', NULL, 1, NULL, NULL, NULL, NULL, 0, '2025-06-21 14:19:34', '2025-06-21 14:19:34'),
(12, 'Test Customer 1750515760', 'testcustomer1750515760@example.com', '1234567890', '$2y$10$cAblZAbyPJJyVHf0WkzZNevvEaYbLJd0ADrFDi/3wK1MzszwRKzom', 'customer', NULL, 1, NULL, NULL, NULL, NULL, 0, '2025-06-21 14:22:40', '2025-06-21 14:22:40'),
(13, 'Test Customer Fixed 1750515957', 'testcustomerfixed1750515957@example.com', '1234567890', '$2y$10$hoZ99UuOSfpzdojFXxNmMOzDEaPp2KQRDJ7sBT0zsG3gw0SPL7v9.', 'customer', NULL, 1, NULL, NULL, '2025-06-21 14:25:57', NULL, 0, '2025-06-21 14:25:57', '2025-06-21 14:25:57'),
(14, 'Test Vendor Fixed 1750515957', 'testvendorfixed1750515957@example.com', '1234567890', '$2y$10$5Ma6.Pi8tsjt4AqoWGs/t.NptpDvzAyudUAGm9ffeU/zwAJ3HfC0O', 'vendor', NULL, 1, NULL, NULL, '2025-06-21 14:25:57', NULL, 0, '2025-06-21 14:25:57', '2025-06-21 14:25:57'),
(16, 'c3', 'c3@gmail.com', '+60123456789', '$2y$10$YONe9Fazgbq7f1XmmnFSEuDNTeztLFMjs/zyrFkcRq9MfppyBYWGG', 'customer', NULL, 1, NULL, NULL, '2025-06-21 14:42:24', NULL, 0, '2025-06-21 14:35:17', '2025-06-21 14:42:41'),
(17, 'c4', 'c4@gmail.com', '+60123456789', '$2y$10$UjacUzyKFBmjUEFiP2PoFORxT3tCHq68iz3m09lvgbL28H9c7JmRy', 'customer', NULL, 1, NULL, NULL, '2025-07-02 15:21:26', NULL, 0, '2025-06-21 14:47:16', '2025-07-02 15:21:26'),
(18, 'v2', 'v2@gmail.com', '+60123456789', '$2y$10$qcKGtO9NMPllEkf/p0e39eI886UOL0CBKiWqjCP/F5mjAoD4Ihira', 'vendor', NULL, 1, NULL, NULL, '2025-06-21 14:47:57', NULL, 0, '2025-06-21 14:47:52', '2025-06-21 14:47:57'),
(19, 'adfs', 'weixiang1238@gmail.com', '123123123', '$2y$10$1z3nOPwxroFPBqkIErnT/ezoWqCNsIuW3ujSrCv/X2IyYXYWwyFr2', 'customer', NULL, 1, NULL, NULL, NULL, NULL, 0, '2025-06-21 14:58:09', '2025-06-21 15:01:03'),
(21, 'd123123', 'driver41@gmail.com', '123123123123', '$2y$10$VDeO8MI09SDjMVWzk9tmruK.n1EHpSKrBCVazRCZycdFiGjsHffpC', 'staff', NULL, 1, NULL, NULL, '2025-07-04 05:30:00', NULL, 0, '2025-06-27 03:34:30', '2025-07-04 05:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_role_id`, `user_id`, `role_id`, `assigned_at`, `is_active`) VALUES
(1, 1, 1, '2025-06-20 13:52:26', 1),
(2, 2, 1, '2025-06-20 13:52:26', 1),
(3, 3, 2, '2025-06-20 13:52:26', 1),
(4, 8, 3, '2025-06-21 12:36:33', 1),
(5, 11, 3, '2025-06-21 14:19:34', 1),
(6, 12, 3, '2025-06-21 14:22:40', 1),
(7, 13, 3, '2025-06-21 14:25:57', 1),
(8, 14, 2, '2025-06-21 14:25:57', 1),
(9, 16, 3, '2025-06-21 14:35:17', 1),
(10, 17, 3, '2025-06-21 14:47:16', 1),
(11, 18, 2, '2025-06-21 14:47:52', 1),
(12, 19, 3, '2025-06-21 14:58:09', 1),
(14, 21, 4, '2025-06-27 03:34:30', 1),
(15, 6, 2, '2025-06-28 02:36:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `subscription_tier_id` int(11) NOT NULL,
  `registration_date` date NOT NULL DEFAULT current_timestamp(),
  `is_archive` tinyint(1) DEFAULT 0,
  `tier_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendor_id`, `user_id`, `business_name`, `contact_number`, `address`, `website_url`, `description`, `subscription_tier_id`, `registration_date`, `is_archive`, `tier_id`) VALUES
(3, 3, 'vendor A', '012-3456789', 'Lot 1, Jalan Satu, Kedah', NULL, NULL, 1, '2025-06-19', 0, 1),
(4, 5, 'Smith Premium Farm Products', '+1234567891', '123 Premium Farm Road, Agriculture Valley, AV 12345', 'https://www.smithfarm.com', 'Premium organic farm producing the finest vegetables and fruits', 1, '2025-06-21', 0, 1),
(5, 6, 'Gold Tier Test Business', '+1234567890', '123 Gold Street, Test City, TC 12345', NULL, NULL, 3, '2025-06-21', 0, 3),
(6, 7, 'test vendor 2', '+60123456789', 'aslgdfj asdgaj sdlgkj ', 'https://asdfadsf.com', 'asdjkfhaj dfa;dlsfj hadf adf', 2, '2025-06-21', 0, 1),
(7, 10, 'V2', '+60123456789', 'asgsdf asdf ag agagda', 'https://asdfadsf.com', 'adfasdgs asdg asdg', 1, '2025-06-21', 0, 1),
(8, 14, 'Test Business Fixed', '1234567890', '123 Test St, Test City, Test State', 'https://testfixed.com', 'Test business description fixed', 1, '2025-06-21', 0, 1),
(9, 18, 'V2', '+60123456789', 'adsfasdfasdf', 'https://asdfadsf.com', 'asdfasdfadsf', 1, '2025-06-21', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_reviews`
--

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

--
-- Dumping data for table `vendor_reviews`
--

INSERT INTO `vendor_reviews` (`vendor_review_id`, `vendor_id`, `customer_id`, `order_id`, `rating`, `title`, `comment`, `pros`, `cons`, `is_verified_purchase`, `is_approved`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(2, 5, 6, 2, 5, '1', '2', NULL, NULL, 1, 1, 1, '2025-06-27 21:24:01', '2025-06-28 03:19:27', '2025-06-28 03:24:01'),
(3, 5, 6, 2, 5, '1', '2', NULL, NULL, 1, 0, NULL, NULL, '2025-06-28 03:38:51', '2025-06-28 03:38:51'),
(4, 5, 6, 2, 5, 'a', 'a', 'd', 'd', 1, 0, NULL, NULL, '2025-06-29 07:46:25', '2025-06-29 07:46:25');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_subscriptions`
--

CREATE TABLE `vendor_subscriptions` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `tier_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_subscriptions`
--

INSERT INTO `vendor_subscriptions` (`id`, `vendor_id`, `tier_id`, `start_date`, `end_date`, `payment_amount`, `is_active`) VALUES
(2, 3, 1, '2025-06-19', '2025-07-19', 0.00, 1),
(3, 5, 2, '2025-07-04', '2025-08-04', 49.90, 0),
(4, 5, 3, '2025-07-04', '2025-08-04', 89.90, 0),
(5, 5, 4, '2025-07-04', '2025-08-04', 119.90, 0),
(6, 5, 2, '2025-07-04', '2025-08-04', 49.90, 0),
(7, 5, 3, '2025-07-04', '2025-08-04', 89.90, 0),
(8, 5, 3, '2025-07-04', '2025-08-04', 89.90, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `table_name` (`table_name`),
  ADD KEY `action` (`action`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `is_default` (`is_default`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `page_visits`
--
ALTER TABLE `page_visits`
  ADD PRIMARY KEY (`visit_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `visit_date` (`visit_date`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `payment_method_id` (`payment_method_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_method_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`),
  ADD KEY `module` (`module`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_category_id` (`parent_category_id`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_permission_id`),
  ADD UNIQUE KEY `role_permission_unique` (`role_id`,`permission_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `search_logs`
--
ALTER TABLE `search_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_search_logs_clicked_product` (`clicked_product_id`),
  ADD KEY `idx_search_logs_keyword` (`keyword`),
  ADD KEY `idx_search_logs_search_date` (`search_date`),
  ADD KEY `idx_search_logs_user_id` (`user_id`),
  ADD KEY `idx_search_logs_session_id` (`session_id`),
  ADD KEY `idx_search_logs_clicked_vendor` (`clicked_vendor_id`);

--
-- Indexes for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `customer_product_unique` (`customer_id`,`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department` (`department`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `staff_tasks`
--
ALTER TABLE `staff_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `status` (`status`),
  ADD KEY `priority` (`priority`),
  ADD KEY `due_date` (`due_date`);

--
-- Indexes for table `subscription_tiers`
--
ALTER TABLE `subscription_tiers`
  ADD PRIMARY KEY (`tier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_role_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendor_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tier_id` (`tier_id`),
  ADD KEY `vendors_ibfk_2` (`subscription_tier_id`);

--
-- Indexes for table `vendor_reviews`
--
ALTER TABLE `vendor_reviews`
  ADD PRIMARY KEY (`vendor_review_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `is_approved` (`is_approved`);

--
-- Indexes for table `vendor_subscriptions`
--
ALTER TABLE `vendor_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `tier_id` (`tier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `page_visits`
--
ALTER TABLE `page_visits`
  MODIFY `visit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1513;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `role_permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `search_logs`
--
ALTER TABLE `search_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_tasks`
--
ALTER TABLE `staff_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subscription_tiers`
--
ALTER TABLE `subscription_tiers`
  MODIFY `tier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `user_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vendor_reviews`
--
ALTER TABLE `vendor_reviews`
  MODIFY `vendor_review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vendor_subscriptions`
--
ALTER TABLE `vendor_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `page_visits`
--
ALTER TABLE `page_visits`
  ADD CONSTRAINT `page_visits_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`payment_method_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD CONSTRAINT `product_categories_ibfk_1` FOREIGN KEY (`parent_category_id`) REFERENCES `product_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE;

--
-- Constraints for table `search_logs`
--
ALTER TABLE `search_logs`
  ADD CONSTRAINT `search_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `shopping_cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shopping_cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_tasks`
--
ALTER TABLE `staff_tasks`
  ADD CONSTRAINT `staff_tasks_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE CASCADE;

--
-- Constraints for table `vendors`
--
ALTER TABLE `vendors`
  ADD CONSTRAINT `vendors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendors_ibfk_2` FOREIGN KEY (`subscription_tier_id`) REFERENCES `subscription_tiers` (`tier_id`);

--
-- Constraints for table `vendor_reviews`
--
ALTER TABLE `vendor_reviews`
  ADD CONSTRAINT `vendor_reviews_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`vendor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_reviews_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vendor_reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
