-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2024 at 05:15 AM
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
-- Database: `tourism`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking_table`
--

CREATE TABLE `booking_table` (
  `book_id` int(50) NOT NULL,
  `tourist_id` int(50) NOT NULL,
  `spot_id` int(50) NOT NULL,
  `booking_created` timestamp(6) NULL DEFAULT current_timestamp(6),
  `booking_sched` timestamp(6) NULL DEFAULT NULL,
  `status` varchar(10) NOT NULL COMMENT '0 = pending, 1 = accepted and 2 = cancelled\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cost`
--

CREATE TABLE `cost` (
  `id` int(50) NOT NULL,
  `tourist_id` int(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `price` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_table`
--

CREATE TABLE `feedback_table` (
  `feedback_id` int(50) NOT NULL,
  `tourist_id` int(50) NOT NULL,
  `spot_id` int(50) NOT NULL,
  `feedback` varchar(200) DEFAULT NULL,
  `stars_rating` int(50) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recommendation`
--

CREATE TABLE `recommendation` (
  `recom_id` int(50) NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `beach` int(50) DEFAULT NULL,
  `pools` int(50) DEFAULT NULL,
  `campsite` int(50) DEFAULT NULL,
  `falls` int(50) DEFAULT NULL,
  `historical` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `status` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `user_id`, `title`, `address`, `type`, `description`, `status`, `date_created`) VALUES
(1, 9, 'Buenos Aires Mountain Resort', 'Barangay Ilijan, Bago City, Negros Occidental Philippines', '', 'Buenos Aires Mountain Resort is a nature-rich site that is located at the foot of Mount Kanlaon. It boasts an Olympic-size swimming pool, two kiddie pools and a standard-size pool. The resort’s cool c', '1', '2024-08-11 22:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `tours_image`
--

CREATE TABLE `tours_image` (
  `id` int(50) NOT NULL,
  `tours_id` int(50) NOT NULL,
  `img` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(200) DEFAULT NULL,
  `lastname` varchar(200) DEFAULT NULL,
  `profile_picture` varchar(200) DEFAULT NULL,
  `username` varchar(200) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `role` varchar(200) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `profile_picture`, `username`, `password`, `email`, `phone_number`, `role`, `date_created`) VALUES
(9, 'demo', 'demo', 'default.jpg', 'demo123', '$2y$10$uYNw6ScQPDMUlWQpZuFmwuXkW6ZSnXrXD5y1wtmMxPE1G8wdbLjqm', 'demo@gmail.com', NULL, 'user', '2024-08-12 12:40:06'),
(10, 'crez', 'mustre', 'default.jpg', 'crez', '$2y$10$nKj6EyzaG/a7Vsl09IXOmuxt/nfJdf21c4e7pTgvSPhhFRliNmWTm', 'crez@gmail.com', NULL, 'admin', '2024-08-12 12:40:06'),
(11, NULL, NULL, 'default.jpg', 'admin', '$2y$10$caZecj.nFNj0ZBuDLBxtUeL2Zqc2U/7Sp/.NefXxJalI9JMMa3s5y', 'admin@gmail.com', NULL, 'admin', '2024-08-12 12:40:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking_table`
--
ALTER TABLE `booking_table`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `tourist_id` (`tourist_id`,`spot_id`);

--
-- Indexes for table `cost`
--
ALTER TABLE `cost`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tourist_id` (`tourist_id`);

--
-- Indexes for table `feedback_table`
--
ALTER TABLE `feedback_table`
  ADD PRIMARY KEY (`feedback_id`),
  ADD UNIQUE KEY `tourist_id` (`tourist_id`,`spot_id`);

--
-- Indexes for table `recommendation`
--
ALTER TABLE `recommendation`
  ADD PRIMARY KEY (`recom_id`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `tours_image`
--
ALTER TABLE `tours_image`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tours_id` (`tours_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking_table`
--
ALTER TABLE `booking_table`
  MODIFY `book_id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost`
--
ALTER TABLE `cost`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_table`
--
ALTER TABLE `feedback_table`
  MODIFY `feedback_id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recommendation`
--
ALTER TABLE `recommendation`
  MODIFY `recom_id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tours_image`
--
ALTER TABLE `tours_image`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
