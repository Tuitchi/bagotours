-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 23, 2024 at 07:33 PM
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
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `tours_id` int(50) NOT NULL,
  `phone_number` varchar(150) NOT NULL,
  `people` int(50) NOT NULL,
  `date_sched` datetime NOT NULL,
  `date_created` date NOT NULL DEFAULT current_timestamp(),
  `status` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `tours_id`, `phone_number`, `people`, `date_sched`, `date_created`, `status`) VALUES
(1, 9, 1, '9054604916', 10, '2024-08-29 06:00:00', '0000-00-00', 0),
(3, 9, 1, '9054604916', 10, '2024-08-29 06:00:00', '2024-08-24', 0),
(4, 9, 1, '9054604916', 10, '2024-08-29 06:00:00', '2024-08-24', 0),
(6, 9, 1, 'asd123123', 123, '2024-09-03 00:33:00', '2024-08-24', 0),
(7, 9, 1, '9054604916', 123, '2024-08-28 00:57:00', '2024-08-24', 0);

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
-- Table structure for table `inquiry`
--

CREATE TABLE `inquiry` (
  `id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` varchar(500) NOT NULL,
  `status` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
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
-- Table structure for table `review_rating`
--

CREATE TABLE `review_rating` (
  `id` int(50) NOT NULL,
  `tour_id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `rating` int(50) NOT NULL,
  `review` varchar(500) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `img` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `status` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `user_id`, `img`, `title`, `address`, `latitude`, `longitude`, `type`, `description`, `status`, `date_created`) VALUES
(1, 9, 'buenos.jpg', 'Buenos Aires Mountain Resort', 'Barangay Ilijan, Bago City, Negros Occidental Philippines', ' 10.45456', '123.04783', 'resort', 'Buenos Aires Mountain Resort is a nature-rich site that is located at the foot of Mount Kanlaon. It boasts an Olympic-size swimming pool, two kiddie pools and a standard-size pool. The resort’s cool c', '1', '2024-08-11 22:51:15');

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
  `profile_picture` varchar(200) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
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

INSERT INTO `users` (`id`, `profile_picture`, `name`, `username`, `password`, `email`, `phone_number`, `role`, `date_created`) VALUES
(9, 'default.png', '', 'demo123', '$2y$10$uYNw6ScQPDMUlWQpZuFmwuXkW6ZSnXrXD5y1wtmMxPE1G8wdbLjqm', 'demo@gmail.com', NULL, 'user', '2024-08-12 12:40:06'),
(10, 'default.png', '', 'crez', '$2y$10$nKj6EyzaG/a7Vsl09IXOmuxt/nfJdf21c4e7pTgvSPhhFRliNmWTm', 'crez@gmail.com', NULL, 'admin', '2024-08-12 12:40:06'),
(11, 'default.png', '', 'admin', '$2y$10$caZecj.nFNj0ZBuDLBxtUeL2Zqc2U/7Sp/.NefXxJalI9JMMa3s5y', 'admin@gmail.com', NULL, 'admin', '2024-08-12 12:40:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `User Book` (`user_id`),
  ADD KEY `Tours Book` (`tours_id`);

--
-- Indexes for table `cost`
--
ALTER TABLE `cost`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tourist_id` (`tourist_id`);

--
-- Indexes for table `inquiry`
--
ALTER TABLE `inquiry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `User Inquiry` (`user_id`);

--
-- Indexes for table `recommendation`
--
ALTER TABLE `recommendation`
  ADD PRIMARY KEY (`recom_id`);

--
-- Indexes for table `review_rating`
--
ALTER TABLE `review_rating`
  ADD KEY `Tours Review` (`tour_id`),
  ADD KEY `User Review` (`user_id`);

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
  ADD KEY `Tours Image` (`tours_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cost`
--
ALTER TABLE `cost`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry`
--
ALTER TABLE `inquiry`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

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

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `Tours Book` FOREIGN KEY (`tours_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `User Book` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inquiry`
--
ALTER TABLE `inquiry`
  ADD CONSTRAINT `User Inquiry` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `review_rating`
--
ALTER TABLE `review_rating`
  ADD CONSTRAINT `Tours Review` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `User Review` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tours_image`
--
ALTER TABLE `tours_image`
  ADD CONSTRAINT `Tours Image` FOREIGN KEY (`tours_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
