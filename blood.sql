-- phpMyAdmin SQL Dump
-- version 5.2.1deb1ubuntu0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 22, 2024 at 09:30 PM
-- Server version: 8.0.35-0ubuntu0.23.04.1
-- PHP Version: 8.1.12-1ubuntu4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blood`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `donor_id` int NOT NULL,
  `status` enum('Pending','Accepted','Declined') DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `user_id`, `donor_id`, `status`, `created_at`) VALUES
(3, 7, 8, 'Accepted', '2024-07-22 13:04:17'),
(4, 7, 8, 'Accepted', '2024-07-22 15:26:39'),
(5, 7, 8, 'Accepted', '2024-07-22 15:34:46'),
(6, 7, 8, 'Accepted', '2024-07-22 15:37:30'),
(7, 7, 8, 'Accepted', '2024-07-22 15:40:20'),
(8, 7, 8, 'Accepted', '2024-07-22 15:43:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `age` int NOT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `bloodgroup` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `town` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `latitude` double(9,6) DEFAULT NULL,
  `longitude` double(9,6) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `age`, `sex`, `bloodgroup`, `mobile`, `email`, `town`, `state`, `latitude`, `longitude`, `role`) VALUES
(3, 'user1@gmail.com', '$2y$10$0vr6z5LGRDndhHyBA/9wjuF1bAmb2WiUAzuHTCyDqRqc42HGYh8Aq', 'Prabin neupane', 22, 'male', 'Apos', '9869837997', 'hari@gmail.com', 'chitwan', 'bagmati', 27.810694, 85.252589, 'donor'),
(4, 'user1@gmail.com', '$2y$10$slaKjqTg1ctNiuPqgvPeROCYoi3BDIYRWEzeaBh1LyYaeVg3fOfVq', 'Prabin neupane', 22, 'male', 'Apos', '9869837997', 'hari@gmail.com', 'chitwan', 'bagmati', 27.810694, 85.252589, 'donor'),
(5, 'user1@gmail.com', '$2y$10$I8Hi3eGpZim.6jidtg6UMOECHAkITxakQc/kqNPLG8TWiLBrs7HwC', 'Prabin neupane', 22, 'male', 'Aneg', '9869837997', 'prabin@gmail.com', 'chitwan', 'bagmati', 27.854413, 85.065821, 'donor'),
(6, 'user2@gmail.com', '$2y$10$zvzD4E5YUByIhZl/7fOJOeeoYvCt9g/vn799xLSJZmcoiMSqjdHz.', 'user2', 22, 'male', 'Apos', '9869837997', 'user2@gmail.com', 'chitwan', 'bagmati', 27.616173, 84.895533, 'user'),
(7, 'user2', '$2y$10$CBFI1WLanJlcN3tl8V58guQKbUnZuCrzQ.ZTy03Sr1xheFmqxEwh.', 'user2', 22, 'male', 'Apos', '9869837997', 'user@gmail.com', 'chitwan', 'bagmati', 28.367997, 84.977931, 'user'),
(8, 'Donor1', '$2y$10$oA04t7yq9DUkjHXrmz.n2ukDLXM2x/x9q/gNIHzWoi5MYxvoI96Zq', 'Donor', 22, 'male', 'ABpos', '9869837997', 'donor@gmail.com', 'chitwan', 'bagmati', 66.305565, 93.356731, 'donor'),
(9, 'user1@gmail.com', '$2y$10$xInNSUeueURyOICNF4qZuOdjb4r8O.9oVTkcXa6DQ8lAnM32l2V1m', 'user gmail', 22, 'other', 'ABpos', '9869837997', 'prabin@gmail.com', 'chitwan', 'bagmati', 27.689159, 85.161952, 'donor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
