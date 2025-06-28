-- Staff Table Migration
-- This script updates the existing staffs table to include all necessary fields for staff management

-- First, let's rename the existing table to backup
RENAME TABLE IF EXISTS `staffs` TO `staffs_backup`;

-- Create the new comprehensive staff table
CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `hire_date` date NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `performance_rating` decimal(3,2) DEFAULT NULL,
  `status` enum('active','inactive','terminated') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_archive` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY `employee_id` (`employee_id`),
  KEY `user_id` (`user_id`),
  KEY `manager_id` (`manager_id`),
  KEY `department` (`department`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Migrate existing data from backup table
INSERT INTO `staff` (`staff_id`, `user_id`, `employee_id`, `department`, `position`, `hire_date`, `status`, `is_archive`)
SELECT 
    s.staff_id,
    s.user_id,
    CONCAT('EMP', LPAD(s.staff_id, 4, '0')) as employee_id,
    'General' as department,
    COALESCE(s.position, 'Staff Member') as position,
    CURDATE() as hire_date,
    'active' as status,
    s.is_archive
FROM `staffs_backup` s;

-- Update the auto increment
ALTER TABLE `staff` AUTO_INCREMENT = (SELECT MAX(staff_id) + 1 FROM `staff`);

-- Add some sample staff data for testing
INSERT INTO `staff` (`user_id`, `employee_id`, `department`, `position`, `hire_date`, `salary`, `phone`, `status`) VALUES
(1, 'EMP0001', 'IT', 'System Administrator', '2024-01-15', 5000.00, '012-3456789', 'active'),
(2, 'EMP0002', 'HR', 'HR Manager', '2024-02-01', 4500.00, '012-3456790', 'active');

-- Create staff_tasks table if it doesn't exist with proper structure
CREATE TABLE IF NOT EXISTS `staff_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `assigned_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `completed_date` DATETIME NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`task_id`),
  KEY `staff_id` (`staff_id`),
  KEY `status` (`status`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key constraints
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_manager_fk` FOREIGN KEY (`manager_id`) REFERENCES `staff` (`staff_id`) ON DELETE SET NULL;

ALTER TABLE `staff_tasks`
  ADD CONSTRAINT `staff_tasks_staff_fk` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE;

-- You can drop the backup table after confirming everything works
-- DROP TABLE `staffs_backup`;

-- Modify completed_date column to use DATETIME instead of TIMESTAMP
ALTER TABLE `staff_tasks` 
MODIFY COLUMN `completed_date` DATETIME NULL DEFAULT NULL;

-- Reset any existing completed dates that might be wrong
UPDATE `staff_tasks` 
SET `completed_date` = NULL 
WHERE `completed_date` = '2025-06-28 08:00:00';

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `