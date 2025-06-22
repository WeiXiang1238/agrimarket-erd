-- Create staff_tasks table for task assignment functionality
CREATE TABLE IF NOT EXISTS `staff_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`task_id`),
  KEY `staff_id` (`staff_id`),
  KEY `status` (`status`),
  KEY `priority` (`priority`),
  KEY `due_date` (`due_date`),
  CONSTRAINT `staff_tasks_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`staff_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some sample tasks for testing
INSERT INTO `staff_tasks` (`staff_id`, `task_title`, `task_description`, `priority`, `status`, `due_date`) VALUES
(1, 'Complete quarterly report', 'Prepare and submit the Q4 financial report', 'high', 'pending', DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
(1, 'Review employee performance', 'Conduct annual performance reviews for team members', 'medium', 'in_progress', DATE_ADD(CURDATE(), INTERVAL 14 DAY)),
(2, 'Update system documentation', 'Update technical documentation for the new features', 'low', 'completed', DATE_SUB(CURDATE(), INTERVAL 3 DAY)),
(3, 'Customer support training', 'Conduct training session for new customer support staff', 'medium', 'pending', DATE_ADD(CURDATE(), INTERVAL 5 DAY)); 