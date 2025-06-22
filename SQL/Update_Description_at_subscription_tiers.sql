-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 04:38 PM
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
(1, 'Bronze', 0.00, 15.00, 'Basic plan with 15% platform commission.', 0),
(2, 'Silver', 49.90, 11.00, 'Intermediate plan with 11% platform commission.', 0),
(3, 'Gold', 89.90, 7.00, 'Premium plan with 7% platform commission.', 0),
(4, 'Platinum', 119.90, 5.00, 'Ultimate plan with 5% platform commission.', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `subscription_tiers`
--
ALTER TABLE `subscription_tiers`
  ADD PRIMARY KEY (`tier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `subscription_tiers`
--
ALTER TABLE `subscription_tiers`
  MODIFY `tier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
