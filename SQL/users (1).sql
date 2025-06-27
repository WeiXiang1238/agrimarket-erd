-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2025 at 11:48 AM
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
(1, 'admin1', 'admin1@agrimarket.com', NULL, '$2y$10$VDeO8MI09SDjMVWzk9tmruK.n1EHpSKrBCVazRCZycdFiGjsHffpC', 'admin', NULL, 1, NULL, NULL, '2025-06-27 06:17:23', NULL, 0, '2025-06-19 14:39:41', '2025-06-27 06:17:23'),
(2, 'admin2', 'admin2@agrimarket.com', NULL, '$2y$10$kl4SktklyYQdRxYqq7PLeuFja.77aVWkPdwMXb44gJjuwLnOhpxyi', 'admin', NULL, 1, NULL, NULL, '2025-06-22 06:17:30', NULL, 0, '2025-06-19 14:39:53', '2025-06-22 06:17:30'),
(3, 'Vendor A', 'vendorA@example.com', '', '$2y$10$dMrfPIxkrAyt0lGVOORatO/.idXwIkPKaDkwz4pM2ZKcZBS4jiw42', 'vendor', '/Image/profile_pics/user_3_1751016593.png', 1, NULL, NULL, '2025-06-27 09:35:02', NULL, 0, '2025-06-19 14:40:30', '2025-06-27 09:35:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
