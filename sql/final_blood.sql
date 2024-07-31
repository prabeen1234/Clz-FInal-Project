-- phpMyAdmin SQL Dump
-- version 5.2.1deb1ubuntu0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 28, 2024 at 12:18 PM
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
  `blood_type` enum('Apos','Aneg','Bpos','Bneg','Opos','Oneg','ABpos','ABneg') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `user_id`, `donor_id`, `status`, `created_at`, `latitude`, `longitude`, `blood_type`) VALUES
(81, 24, 25, 'Declined', '2024-07-27 14:06:51', 27.583086, 85.258082, 'Apos'),
(92, 24, 25, 'Declined', '2024-07-27 17:37:19', 27.838402, 85.562810, 'Apos'),
(93, 24, 25, 'Declined', '2024-07-27 17:49:14', 27.838402, 85.562810, 'Apos'),
(94, 24, 26, 'Accepted', '2024-07-28 02:42:45', 27.838402, 85.562810, 'Apos'),
(95, 24, 25, 'Accepted', '2024-07-28 02:49:58', 27.838402, 85.562810, 'Apos'),
(96, 24, 26, 'Accepted', '2024-07-28 03:41:55', 27.721524, 85.361710, 'Apos'),
(97, 24, 25, 'Accepted', '2024-07-28 03:48:34', 27.721524, 85.361710, 'Apos'),
(98, 24, 25, 'Pending', '2024-07-28 05:35:02', 27.721524, 85.361710, 'Apos'),
(99, 24, 26, 'Pending', '2024-07-28 05:35:23', 27.721524, 85.361710, 'Apos'),
(100, 24, 30, 'Accepted', '2024-07-28 05:46:40', 27.647420, 84.414954, 'Apos');

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
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `password`, `fullname`, `age`, `sex`, `blood_type`, `mobile`, `email`, `weight`, `state`, `latitude`, `longitude`, `role`) VALUES
(24, '$2y$10$Gu1/Qz62wyoKgCXphCGh8u1P5wlBqxqvH2/NAeQK9oovQvkzRaMHC', 'User 1', 33, 'male', 'Apos', '9822222222', 'user1@gmail.com', 55, 'bagmati', 27.647420, 84.414954, 'user'),
(25, '$2y$10$suqBEeZYh38.Q4usknlcHeFoTf6B9obL9NQ1T8juvwaHC/L38mGl.', 'Prabin neupane', 22, 'male', 'Apos', '9811111111', 'donor2@gmail.com', 55, 'bagmati', 27.688431, 85.346128, 'donor'),
(26, '$2y$10$AwEFrDoE8BI9JtvdBvLDKeq7A3d3J0f2petNrsvFLKFYnXlbq0GbW', 'Blood Donor', 22, 'male', 'Apos', '9869837993', 'donor1@gmail.com', 55, 'bagmati', 27.676978, 85.317153, 'donor'),
(27, '$2y$10$zYz2//1zW1klPoNJ52G9e.vGrOFkFFDHOtwUFP.8WMuBFNPelWYze', 'donor3', 22, 'male', 'Apos', '9999999999', 'donor3@gmail.com', 55, 'bagmati', 27.637162, 85.326747, 'donor'),
(28, '$2y$10$3uesCSux9HZCCadLa4Qsrec9Muqi1KLiG08BM9xexoWAo3qd/tt0y', 'donor5', 33, 'male', 'Apos', '9869837999', 'donor5@gmail.com', 55, 'bagmati', 27.666356, 85.326747, 'donor'),
(29, '$2y$10$yCOkSXn/kW4N25ix.MUP8.ZWkRAWYfP0KIi8gkbk8KP7DR.OIWh/i', 'donor6', 22, 'female', 'Apos', '9869837998', 'donor6@gmail.com', 55, 'bagmati', 27.690679, 85.348719, 'donor'),
(30, '$2y$10$IclvWKmMBmbr9RzEZEj79OEXfpVrD6S7GWnjMlw/r1ihdLdZ/1hLi', 'donor7', 44, 'female', 'Apos', '3443434343', 'donor7@gmail.com', 55, 'bagmati', 28.214725, 85.013636, 'donor'),
(31, '$2y$10$VfKMYtAQ6VOn5Q5/O3.Ovuu5IBPJzTUF/tMgoo533fk1HXQD3h4Li', 'Donor 8', 33, 'male', 'Aneg', '9888226433', 'donor8@gmail.com', 55, 'bagmati', 27.642028, 84.486292, 'donor');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
