-- phpMyAdmin SQL Dump
-- version 5.2.1deb1ubuntu0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 13, 2024 at 05:45 PM
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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `blood_type` enum('Apos','Aneg','Bpos','Bneg','Opos','Oneg','ABpos','ABneg') DEFAULT NULL,
  `accepted_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `password` varchar(100) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `age` int NOT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `blood_type` enum('Apos','Aneg','Bpos','Bneg','Opos','Oneg','ABpos','ABneg') DEFAULT NULL,
  `mobile` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `weight` int DEFAULT NULL,
  `state` varchar(100) NOT NULL,
  `latitude` double(9,6) DEFAULT NULL,
  `longitude` double(9,6) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `password`, `fullname`, `age`, `sex`, `blood_type`, `mobile`, `email`, `weight`, `state`, `latitude`, `longitude`, `role`, `reset_token`, `reset_token_expiry`, `otp`, `otp_expiry`, `status`) VALUES
(26, '$2y$10$lODVTTVYGdabw0He2IkOHuRK7t4U78cI.zNnrZ18j/bhCy2hh9./e', 'Accepted', 33, 'male', 'Apos', '9869837993', 'donor1@gmail.com', 55, 'bagmati', 27.688556, 85.346085, 'donor', '123a1ff5ad4de1c3ec45fcaf936330d1db66c19a79cec85d8a374432ea06c5d6', '2024-07-28 13:34:10', NULL, NULL, 'approved'),
(27, '$2y$10$zYz2//1zW1klPoNJ52G9e.vGrOFkFFDHOtwUFP.8WMuBFNPelWYze', 'donor3', 22, 'male', 'Apos', '9999999999', 'donor3@gmail.com', 55, 'bagmati', 27.583993, 85.182512, 'donor', NULL, NULL, NULL, NULL, 'approved'),
(29, '$2y$10$yCOkSXn/kW4N25ix.MUP8.ZWkRAWYfP0KIi8gkbk8KP7DR.OIWh/i', 'donor6', 22, 'female', 'Apos', '9869837998', 'donor6@gmail.com', 55, 'bagmati', 27.690679, 85.348719, 'donor', NULL, NULL, '749855', '2024-07-28 13:28:42', 'approved'),
(30, '$2y$10$IclvWKmMBmbr9RzEZEj79OEXfpVrD6S7GWnjMlw/r1ihdLdZ/1hLi', 'donor7', 44, 'female', 'Apos', '3443434343', 'donor7@gmail.com', 55, 'bagmati', 28.214725, 85.013636, 'donor', NULL, NULL, '310646', '2024-07-28 13:32:21', 'approved'),
(31, '$2y$10$VfKMYtAQ6VOn5Q5/O3.Ovuu5IBPJzTUF/tMgoo533fk1HXQD3h4Li', 'Donor 8', 33, 'male', 'Aneg', '9888226433', 'donor8@gmail.com', 55, 'bagmati', 27.642028, 84.486292, 'donor', NULL, NULL, NULL, NULL, 'approved'),
(33, '$2y$10$r4W7vBnK4y0xYqGzc/yhNOdDcuGMvV8Ag2rXUU3VufQrEk6Y3B3va', 'Prabin neupane', 44, 'male', 'Oneg', '5555555555', 'shyam@gmail.com', 55, 'bresrsert', 28.296981, 85.249842, 'donor', NULL, NULL, '639766', '2024-07-29 11:41:16', 'approved'),
(36, '$2y$10$eUzFNwLPpzU8H8s1ZBNj.uMLFDEhMmaB4pezEeRCzYCPHoWyEnmL.', 'Rijan Aryal', 19, 'male', 'Apos', '3589653214', 'asdf@gmail.com', 73, 'Bagmati', 27.664048, 85.343605, 'donor', NULL, NULL, NULL, NULL, 'approved'),
(39, '$2y$10$Kd670MMkVrCArcaMrc0dSOPuIX8gQ7TWzCzXfDw0VJj29pbgaDU4y', 'Prabin neupane', 33, 'male', 'Apos', '9999299292', 'pubgidws@gmail.com', 55, 'bagmati', 27.705270, 85.425624, 'donor', NULL, NULL, NULL, NULL, 'approved'),
(40, '$2y$10$kCe2XF6Bm5Qp3L3rhjo7jeLp0gf5KUh7XY6EahCLUmTmkuqI891bi', 'Blood Donor', 23, 'female', 'Oneg', '7767767777', 'pokhrelalina03@gmail.com', 55, 'bagmati', 27.680951, 85.310267, 'donor', NULL, NULL, '897071', '2024-08-01 20:48:22', 'approved'),
(66, '$2y$10$yH5oNfuZF8R1lbX.xRCZOuENag9Dnxjr0aXiUnQxblgludH5tjT/G', 'Prabin neupane', 33, 'male', 'Apos', '9869837997', 'prabeenneupane123@gmail.com', 55, 'bagmati', 27.666169, 85.321004, 'user', NULL, NULL, NULL, NULL, 'approved');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

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
