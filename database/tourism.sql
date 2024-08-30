-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2024 at 07:58 AM
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
(9, 9, 1, '09054604916', 5, '2024-08-30 10:00:00', '2024-08-27', 1);

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

--
-- Dumping data for table `review_rating`
--

INSERT INTO `review_rating` (`id`, `tour_id`, `user_id`, `rating`, `review`, `date_created`) VALUES
(1, 1, 9, 4, 'qweqweqee', '2024-08-25 00:28:59'),
(3, 1, 9, 3, 'qweqweqee', '2024-08-25 00:29:27');

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
  `description` varchar(1000) NOT NULL,
  `status` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `user_id`, `img`, `title`, `address`, `latitude`, `longitude`, `type`, `description`, `status`, `date_created`) VALUES
(1, 9, 'buenos.jpg', 'Buenos Aires Mountain Resort', 'Barangay Ilijan, Bago City, Negros Occidental Philippines', ' 10.45456', '123.04783', 'Mountain Resort', 'Buenos Aires Mountain Resort is a nature-rich site that is located at the foot of Mount Kanlaon. It boasts an Olympic-size swimming pool, two kiddie pools and a standard-size pool. The resort’s cool c', '1', '2024-08-11 22:51:15'),
(7, 9, '1724504783_bantayanpark.jpg', 'Bantayan Park', 'Bantayan Park, Rizal Street, Bago City, Negros Occidental', '10.536883439109104', '122.83162734662949', 'Park', 'A place where you can chill, walk, run, bike, eat, and even make your tikt0k videos! The views in this park is good in any time of the day. In the morning, its a place for people to exercise or run. I', '1', '2024-08-24 21:06:23'),
(8, 9, '1724510289_balaynitanjuan.jpg', 'Balay ni Tan Juan', 'Rizal Street Barangay Poblacion, Bago City, Negros Occidental', '10.534373503857907', '122.83485882945081', 'Historical Landmark', 'If there is any structure that mirrors the resilient spirit of the people of Bago City in Negros Occidental, then it would be Balay ni Tan Juan, the ancestral home of the Negrense revolutionary hero G', '1', '2024-08-24 22:38:09'),
(9, 11, '1724569316_tanjuan.jpg', 'Tan Juan Monument', 'GRQP+297, Araneta Avenue, Bago City, Negros Occidental', '10.537537930388424', '122.83591358344103', 'Historical Landmark', 'The Gen. Juan Araneta monument at the heart of the city is not only a monument but the final resting place of Tan Juan?\r\nOn the occasion of his 50th death anniversary, a ceremony was held for Araneta’s reburial at the base of the monument built in his memory at the public plaza (Gen. Juan A. Araneta Park). ', '1', '2024-08-25 15:01:56');

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
(11, 'default.png', '', 'admin', '$2y$10$caZecj.nFNj0ZBuDLBxtUeL2Zqc2U/7Sp/.NefXxJalI9JMMa3s5y', 'admin@gmail.com', NULL, 'admin', '2024-08-12 12:40:06'),
(12, 'default.png', '', 'Nikolai1222', '$2y$10$91BlgV786pTrvoWgmArTWuzCTjchWR3F1EBXNqJQr.bovtqMpO/kG', 'niklapatan1@gmail.com', NULL, 'user', '2024-08-24 16:02:37'),
(13, 'default.png', '', 'nikolailapatan', '$2y$10$VEljE7PNENjSU3nVe8./v.KoESVt8yCE0hPaFXjou1OboaUmnQSAG', 'niklapatan1@gmail.com', NULL, 'user', '2024-08-27 01:25:09'),
(14, NULL, 'asdas', NULL, '$2y$10$KbcqVhkK1cZKA6HG8a3u5ubIwi1wQax0OhHxYEw28YNzyevm7lhyK', 'dasdas@ad', NULL, 'admin', '2024-08-28 16:05:30'),
(15, NULL, 'asd', NULL, '$2y$10$7nzPBdkyRsXHZf7xYQ5PjeM8ZeLLj9cUp4w1KhKPtBtGDOroNaiyq', 'asdasd@qweq', NULL, 'user', '2024-08-28 16:12:24'),
(16, NULL, 'dqwcewq', NULL, '$2y$10$uIOEno07GFLnjVyIe7bUN.Ii8iRyAZSj9N70.HwdG8wwOxPalugpa', 'qwexqwe@eqweqw', NULL, 'owner', '2024-08-28 16:13:02'),
(17, NULL, 'asdcqw', NULL, '$2y$10$m3aP7FjebSIgVN9Ul/I9iuzefiA6u9cdcQ22vQP8X2up1Ah0VNIum', 'qweqwe@12312', NULL, 'owner', '2024-08-28 16:15:09'),
(18, NULL, 'qwcewqe', NULL, '$2y$10$YT0.a6rp4Di/J2aBNNf8r.XBOIIm7VGoWstPnzSMUQVi7AoYlarl6', 'qwceqwe@QWE', NULL, 'owner', '2024-08-28 16:15:30'),
(19, NULL, 'asdcas', NULL, '$2y$10$GKZwabEaDeFGbbpofg3bx.8Q6Bw.c.Fd/nJhPtxoYDSX8eilaiyga', 'qweqw@qwe', NULL, 'admin', '2024-08-28 16:20:01'),
(20, 'default.png', '', 'asd', '$2y$10$RFFZIsO71C5YIFZaOE6Mset//Ty2oBXVTWpprzCYX9CRuwH86yzc2', 'nikolailapatan@qwe', NULL, 'user', '2024-08-29 17:03:03');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `Tours Review` (`tour_id`),
  ADD KEY `User Review` (`user_id`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Tour Added` (`user_id`);

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
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- AUTO_INCREMENT for table `review_rating`
--
ALTER TABLE `review_rating`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tours_image`
--
ALTER TABLE `tours_image`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
-- Constraints for table `tours`
--
ALTER TABLE `tours`
  ADD CONSTRAINT `Tour Added` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tours_image`
--
ALTER TABLE `tours_image`
  ADD CONSTRAINT `Tours Image` FOREIGN KEY (`tours_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
