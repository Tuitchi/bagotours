-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2024 at 04:44 AM
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
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `url`, `type`, `created_at`, `is_read`) VALUES
(11, 11, ' visits Tan Juan Monument', 'dashboard', 'visits', '2024-09-23 19:02:07', 1),
(12, 11, ' visits Buenos Aires Mountain Resort', 'dashboard', 'visits', '2024-09-23 19:02:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `qrcode`
--

CREATE TABLE `qrcode` (
  `id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `qr_code_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qrcode`
--

INSERT INTO `qrcode` (`id`, `tour_id`, `title`, `qr_code_path`, `created_at`, `updated_at`) VALUES
(16, 1, 'Buenos Aires Mountain Resort', '../upload/QRcodes/Buenos Aires Mountain Resort.png', '2024-09-23 03:04:03', '2024-09-23 03:04:03'),
(17, 7, 'Bantayan Park', '../upload/QRcodes/Bantayan Park.png', '2024-09-23 03:04:18', '2024-09-23 03:04:18'),
(18, 8, 'Balay ni Tan Juan', '../upload/QRcodes/Balay ni Tan Juan.png', '2024-09-23 03:04:20', '2024-09-23 03:04:20'),
(19, 9, 'Tan Juan Monument', '../upload/QRcodes/Tan Juan Monument.png', '2024-09-23 03:04:23', '2024-09-23 03:04:23');

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
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(50) NOT NULL,
  `file` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `file`, `type`) VALUES
(1, 'websiteIcon.png', 'Tab Icon');

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
  `proof` varchar(50) NOT NULL,
  `proof_image` varchar(150) NOT NULL,
  `status` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `expiry` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `user_id`, `img`, `title`, `address`, `latitude`, `longitude`, `type`, `description`, `proof`, `proof_image`, `status`, `date_created`, `expiry`) VALUES
(1, 11, 'buenos.jpg', 'Buenos Aires Mountain Resort', 'Barangay Ilijan, Bago City, Negros Occidental Philippines', ' 10.45456', '123.04783', 'Mountain Resort', 'Buenos Aires Mountain Resort is a nature-rich site that is located at the foot of Mount Kanlaon. It boasts an Olympic-size swimming pool, two kiddie pools and a standard-size pool. The resort’s cool c', '', '', '1', '2024-08-11 22:51:15', NULL),
(7, 11, '1724504783_bantayanpark.jpg', 'Bantayan Park', 'Bantayan Park, Rizal Street, Bago City, Negros Occidental', '10.536883439109104', '122.83162734662949', 'Park', 'A place where you can chill, walk, run, bike, eat, and even make your tikt0k videos! The views in this park is good in any time of the day. In the morning, its a place for people to exercise or run. I', '', '', '1', '2024-08-24 21:06:23', NULL),
(8, 11, '1724510289_balaynitanjuan.jpg', 'Balay ni Tan Juan', 'Rizal Street Barangay Poblacion, Bago City, Negros Occidental', '10.534373503857907', '122.83485882945081', 'Historical Landmark', 'If there is any structure that mirrors the resilient spirit of the people of Bago City in Negros Occidental, then it would be Balay ni Tan Juan, the ancestral home of the Negrense revolutionary hero G', '', '', '1', '2024-08-24 22:38:09', NULL),
(9, 11, '1724569316_tanjuan.jpg', 'Tan Juan Monument', 'GRQP+297, Araneta Avenue, Bago City, Negros Occidental', '10.537537930388424', '122.83591358344103', 'Historical Landmark', 'The Gen. Juan Araneta monument at the heart of the city is not only a monument but the final resting place of Tan Juan?\r\nOn the occasion of his 50th death anniversary, a ceremony was held for Araneta’s reburial at the base of the monument built in his memory at the public plaza (Gen. Juan A. Araneta Park). ', '', '', '1', '2024-08-25 15:01:56', NULL);

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
  `home_address` varchar(200) NOT NULL,
  `role` varchar(200) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `profile_picture`, `name`, `username`, `password`, `email`, `phone_number`, `home_address`, `role`, `date_created`) VALUES
(10, 'default.png', 'Crez Rowell Dion Mustre', 'crez', '$2y$10$nKj6EyzaG/a7Vsl09IXOmuxt/nfJdf21c4e7pTgvSPhhFRliNmWTm', 'crez@gmail.com', '09054604913', '', 'user', '2024-08-12 04:40:06'),
(11, 'default.png', '', 'admin', '$2y$10$caZecj.nFNj0ZBuDLBxtUeL2Zqc2U/7Sp/.NefXxJalI9JMMa3s5y', 'admin@gmail.com', NULL, '', 'admin', '2024-08-12 04:40:06'),
(26, 'default.png', 'Nikolai Lapatan', 'user', '$2y$10$0d7xcJ3m/6PV..9We.xmFO891KsOu1lhNPHjJ/pxSrCx0WcdckFAi', 'user@gmail.com', '09054604916', 'Purok Daisy, Barangay Calumangan, Bago City', 'user', '2024-09-23 18:12:42'),
(27, 'default.png', 'Nikolai Lapatan', 'nonbago', '$2y$10$7bLMbsREYUDOysa3.kxDHekQ1zblkr4r1BtRVIDcnJn3ZLA5ck8Q6', 'nonbago@gmail.com', NULL, 'Barangay Kukak, Talisay City, Negros Occidental', 'user', '2024-09-23 18:34:51');

-- --------------------------------------------------------

--
-- Table structure for table `visit_records`
--

CREATE TABLE `visit_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `visit_time` datetime DEFAULT current_timestamp(),
  `city_residence` enum('Bago City','Non-Bago City') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visit_records`
--

INSERT INTO `visit_records` (`id`, `user_id`, `tour_id`, `visit_time`, `city_residence`) VALUES
(8, 26, 9, '2024-09-24 02:33:31', 'Bago City'),
(9, 27, 9, '2024-09-24 02:35:09', 'Non-Bago City'),
(10, 11, 9, '2024-09-24 03:02:07', 'Non-Bago City'),
(11, 11, 1, '2024-09-24 03:02:17', 'Non-Bago City');

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `User Notification` (`user_id`);

--
-- Indexes for table `qrcode`
--
ALTER TABLE `qrcode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`);

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
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `visit_records`
--
ALTER TABLE `visit_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `recommendation`
--
ALTER TABLE `recommendation`
  MODIFY `recom_id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_rating`
--
ALTER TABLE `review_rating`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tours_image`
--
ALTER TABLE `tours_image`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `visit_records`
--
ALTER TABLE `visit_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `User Notification` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `qrcode`
--
ALTER TABLE `qrcode`
  ADD CONSTRAINT `qrcode_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

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

--
-- Constraints for table `visit_records`
--
ALTER TABLE `visit_records`
  ADD CONSTRAINT `visit_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `visit_records_ibfk_2` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
