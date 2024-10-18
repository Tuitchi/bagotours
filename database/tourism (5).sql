-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2024 at 06:21 PM
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
  `tour_id` int(50) NOT NULL,
  `phone_number` varchar(150) NOT NULL,
  `people` int(50) NOT NULL,
  `date_sched` date NOT NULL,
  `date_created` date NOT NULL DEFAULT current_timestamp(),
  `status` int(10) NOT NULL COMMENT '0 = Pending, 1 = Confirmed, 2 = Cancelled, 3 = Ongoing, 4 = Completed',
  `is_review` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `tour_id`, `phone_number`, `people`, `date_sched`, `date_created`, `status`, `is_review`) VALUES
(48, 10, 1, '09054604916', 12, '2024-10-07', '2024-10-08', 4, 1),
(49, 10, 1, '09054604916', 12, '2024-10-07', '2024-10-08', 0, 1);

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
  `tour_id` int(50) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `tour_id`, `message`, `url`, `type`, `created_at`, `is_read`) VALUES
(11, 11, 0, ' visits Tan Juan Monument', 'dashboard', 'visits', '2024-09-23 19:02:07', 1),
(12, 11, 0, ' visits Buenos Aires Mountain Resort', 'dashboard', 'visits', '2024-09-23 19:02:17', 1),
(14, 11, 0, 'Someone upgrade there account, please check the application form.', 'pending.php?view=true&id=25', 'booking', '2024-09-24 03:27:12', 1),
(15, 29, 0, 'user user visits Buenos Aires Mountain Resort', 'dashboard', 'visits', '2024-09-24 03:38:59', 0),
(16, 29, 0, 'user user visits Buenos Aires Mountain Resort', 'dashboard', 'visits', '2024-09-24 03:40:34', 0),
(17, 11, 0, 'Someone upgrade there account, please check the application form.', 'pending.php?view=true&id=26', 'booking', '2024-09-27 16:23:02', 1),
(19, 11, 0, 'Someone booked a tour.', 'view_booking.php?user_id=10&booking_id=26', 'booking', '2024-10-02 13:49:39', 1),
(48, 10, 56, 'You can Register as an owner again.', 'form.php', 'Upgrade cancelled', '2024-10-05 04:04:13', 1),
(49, 11, 57, 'Someone upgraded their account, please check the application form.', 'pending.php?view=true&id=57', 'upgrade', '2024-10-06 11:36:55', 1),
(67, 10, 1, 'Buenos Aires Mountain Resort has accepted your reservation.', 'booking', 'booking', '2024-10-06 18:54:04', 1),
(68, 11, 1, 'Crez Rowell Dion Mustre Will arrive tomorrow at Buenos Aires Mountain Resort', 'notifyUser', 'booking', '2024-10-07 06:22:14', 1),
(86, 11, 1, 'Crez Rowell Dion Mustre booked in Buenos Aires Mountain Resort.', 'view_booking.php?user_id=10&booking_id=45', 'booking', '2024-10-07 06:49:20', 1),
(87, 11, 8, 'Crez Rowell Dion Mustre booked in Balay ni Tan Juan.', 'booking.php?id=46', 'booking', '2024-10-07 08:16:30', 1),
(88, 10, 8, 'Balay ni Tan Juan has declined your reservation.', 'booking', 'booking', '2024-10-07 08:37:31', 1),
(89, 11, 1, 'Crez Rowell Dion Mustre booked in Buenos Aires Mountain Resort.', 'booking.php?id=47', 'booking', '2024-10-07 08:48:02', 1),
(90, 10, 1, 'Buenos Aires Mountain Resort has declined your reservation.', 'booking', 'booking', '2024-10-07 08:48:14', 1),
(91, 11, 1, 'Crez Rowell Dion Mustre booked in Buenos Aires Mountain Resort.', 'booking.php?id=48', 'booking', '2024-10-07 08:50:11', 1),
(92, 10, 1, 'Buenos Aires Mountain Resort has declined your reservation.', 'booking', 'booking', '2024-10-07 08:50:22', 1),
(93, 11, 1, 'Crez Rowell Dion Mustre Will arrive tomorrow at Buenos Aires Mountain Resort', '../php/phpmailer.php?id=48', 'booking', '2024-10-07 11:21:58', 1),
(94, 11, 1, 'Crez Rowell Dion Mustre will arrive today at Buenos Aires Mountain Resort', 'booking?complete=true&id=48', 'booking', '2024-10-07 12:18:25', 1),
(95, 10, 1, 'Buenos Aires Mountain Resort has accepted your reservation.', 'booking', 'booking', '2024-10-07 12:41:50', 0),
(96, 10, 1, 'Buenos Aires Mountain Resort has declined your reservation.', 'booking', 'booking', '2024-10-07 12:41:53', 0),
(97, 10, 1, 'Buenos Aires Mountain Resort has accepted your reservation.', 'booking', 'booking', '2024-10-07 12:56:45', 0),
(98, 10, 1, 'Buenos Aires Mountain Resort has declined your reservation.', 'booking', 'booking', '2024-10-07 13:03:27', 0),
(99, 10, 1, 'Buenos Aires Mountain Resort has declined your reservation.', 'booking', 'booking', '2024-10-07 13:03:31', 0),
(100, 10, 1, 'Buenos Aires Mountain Resort has declined your reservation.', 'booking', 'booking', '2024-10-07 13:03:33', 0),
(101, 10, 1, 'Buenos Aires Mountain Resort has accepted your reservation.', 'booking', 'booking', '2024-10-07 13:03:42', 0),
(102, 10, 1, 'Buenos Aires Mountain Resort has declined your reservation.', 'booking', 'booking', '2024-10-07 13:03:45', 0),
(103, 10, 1, 'Buenos Aires Mountain Resort has accepted your reservation.', 'booking', 'booking', '2024-10-07 13:03:52', 0),
(104, 10, 1, 'Buenos Aires Mountain Resort has accepted your reservation.', 'booking', 'booking', '2024-10-07 13:23:38', 0),
(105, 10, 1, 'Buenos Aires Mountain Resort has declined your reservation.', 'booking', 'booking', '2024-10-07 13:24:23', 0),
(106, 10, 1, 'Buenos Aires Mountain Resort has declined your reservation.', 'booking', 'booking', '2024-10-07 13:25:16', 1),
(107, 11, 1, ' visits Buenos Aires Mountain Resort', 'dashboard', 'visits', '2024-10-09 12:04:37', 0),
(108, 10, 8, 'Crez Rowell Dion Mustre visits Balay ni Tan Juan', 'dashboard', 'visits', '2024-10-09 12:18:28', 0),
(109, 10, 1, 'Crez Rowell Dion Mustre visits Buenos Aires Mountain Resort', 'dashboard', 'visits', '2024-10-09 12:18:32', 0);

-- --------------------------------------------------------

--
-- Table structure for table `proof`
--

CREATE TABLE `proof` (
  `id` int(50) NOT NULL,
  `tour_id` int(50) NOT NULL,
  `proof` varchar(200) NOT NULL,
  `proof_image` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proof`
--

INSERT INTO `proof` (`id`, `tour_id`, `proof`, `proof_image`) VALUES
(23, 57, 'Business permit', '1728214615_df5dbb9b-2824-488e-86d8-85fb32085dba.jfif'),
(24, 57, 'Business permit', '1728214615_f6bb4a56-eb93-4db9-a3ff-503ecefafa50.jfif'),
(25, 57, 'Business permit', '1728214615_461622702_498746622982089_8489791430061758106_n.jpg');

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
(7, 1, 10, 2, 'its so nice', '2024-10-08 00:14:52');

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
  `img` varchar(255) NOT NULL,
  `title` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `status` varchar(50) NOT NULL COMMENT ' 0 = Pending, 1 = Active, 2 = Cancelled, 3 = Inactive.',
  `bookable` tinyint(1) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `expiry` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `user_id`, `img`, `title`, `address`, `latitude`, `longitude`, `type`, `description`, `status`, `bookable`, `date_created`, `expiry`) VALUES
(1, 11, 'buenos.jpg', 'Buenos Aires Mountain Resort', 'Barangay Ilijan, Bago City, Negros Occidental Philippines', ' 10.45456', '123.04783', 'Mountain Resort', 'Buenos Aires Mountain Resort is a nature-rich site that is located at the foot of Mount Kanlaon. It boasts an Olympic-size swimming pool, two kiddie pools and a standard-size pool. The resort’s cool c', '1', 1, '2024-08-11 22:51:15', NULL),
(7, 11, '1724504783_bantayanpark.jpg', 'Bantayan Park', 'Bantayan Park, Rizal Street, Bago City, Negros Occidental', '10.536883439109104', '122.83162734662949', 'Park', 'A place where you can chill, walk, run, bike, eat, and even make your tikt0k videos! The views in this park is good in any time of the day. In the morning, its a place for people to exercise or run. I', '1', 0, '2024-08-24 21:06:23', NULL),
(8, 11, '1724510289_balaynitanjuan.jpg', 'Balay ni Tan Juan', 'Rizal Street Barangay Poblacion, Bago City, Negros Occidental', '10.534373503857907', '122.83485882945081', 'Historical Landmark', 'If there is any structure that mirrors the resilient spirit of the people of Bago City in Negros Occidental, then it would be Balay ni Tan Juan, the ancestral home of the Negrense revolutionary hero G', '1', 1, '2024-08-24 22:38:09', NULL),
(9, 11, '1724569316_tanjuan.jpg', 'Tan Juan Monument', 'GRQP+297, Araneta Avenue, Bago City, Negros Occidental', '10.537537930388424', '122.83591358344103', 'Historical Landmark', 'The Gen. Juan Araneta monument at the heart of the city is not only a monument but the final resting place of Tan Juan?On the occasion of his 50th death anniversary, a ceremony was held for Araneta’s reburial at the base of the monument built in his memory at the public plaza (Gen. Juan A. Araneta Park). ', '3', 0, '2024-08-25 15:01:56', NULL),
(57, 10, '1728214615_461622702_498746622982089_8489791430061758106_n.jpg', 'qweqw', 'Balatong, Ilijan, 6101, Bago City, Negros Occidental, Philippines', '10.522230131485728', '122.94159887981709', 'Campsite', 'qweqwe', '2', 0, '2024-10-06 19:36:55', '2024-10-14');

-- --------------------------------------------------------

--
-- Table structure for table `tours_image`
--

CREATE TABLE `tours_image` (
  `id` int(50) NOT NULL,
  `tour_id` int(50) NOT NULL,
  `img` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours_image`
--

INSERT INTO `tours_image` (`id`, `tour_id`, `img`) VALUES
(40, 57, '1728214615_df5dbb9b-2824-488e-86d8-85fb32085dba.jfif'),
(41, 57, '1728214615_f6bb4a56-eb93-4db9-a3ff-503ecefafa50.jfif'),
(42, 57, '1728214615_461622702_498746622982089_8489791430061758106_n.jpg');

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
  `device_id` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `profile_picture`, `name`, `username`, `password`, `email`, `phone_number`, `home_address`, `role`, `device_id`, `date_created`) VALUES
(10, 'default.png', 'Crez Rowell Dion Mustre', 'crez', '$2y$10$nKj6EyzaG/a7Vsl09IXOmuxt/nfJdf21c4e7pTgvSPhhFRliNmWTm', 'crez@gmail.com', '09054604913', '', 'user', '0908be040727c251ad7d9d409cfdb1e6', '2024-08-12 04:40:06'),
(11, 'default.png', '', 'admin', '$2y$10$caZecj.nFNj0ZBuDLBxtUeL2Zqc2U/7Sp/.NefXxJalI9JMMa3s5y', 'admin@gmail.com', NULL, '', 'admin', '8992d1b8b6e8ec5f0975eba68ed8d306', '2024-08-12 04:40:06'),
(26, 'default.png', 'Nikolai Lapatan', 'user', '$2y$10$0d7xcJ3m/6PV..9We.xmFO891KsOu1lhNPHjJ/pxSrCx0WcdckFAi', 'user@gmail.com', '09054604916', 'Purok Daisy, Barangay Calumangan, Bago City', 'user', '', '2024-09-23 18:12:42'),
(27, 'default.png', 'Nikolai Lapatan', 'nonbago', '$2y$10$7bLMbsREYUDOysa3.kxDHekQ1zblkr4r1BtRVIDcnJn3ZLA5ck8Q6', 'nonbago@gmail.com', NULL, 'Barangay Kukak, Talisay City, Negros Occidental', 'user', '', '2024-09-23 18:34:51'),
(28, 'default.png', 'owner owner', 'owner', '$2y$10$Z7G01WznGDLJSqOWkMGzae.T08fRh.LIcrfr.yZir.NwCv32dwHNy', 'owner@gmail.com', NULL, 'Barangay Gwe, Talisay City, Negros Occidental', 'owner', '', '2024-09-24 03:26:01'),
(29, 'default.png', 'user user', 'user1', '$2y$10$5vnCO/cp1P5YKfF5HpU1muAl.ogMv8R1lZNg4SkNb4dgSfUGdtM.u', 'user1@gmail.com', NULL, 'Purok Daisy, Barangay Calumangan, Bago City', 'user', '', '2024-09-24 03:38:59'),
(30, 'default.png', 'Nikolai Lapatan', 'Bago123', '$2y$10$P7/65skfeWbY0FNN7mQ/W.BEJyEZR934E8WHYf2UhOgO2BrIEnnRC', 'chelester@bcc.com', NULL, 'Purok Daisy Calumangan', 'user', '', '2024-09-29 12:47:36'),
(31, 'default.png', 'Nikolai Lapatan', 'demo123', '$2y$10$OxgP7Uar/qmuuGkEOpXTu.VBd7yVgsUieOJyr.L9ln4nY28lYQjum', 'chelester@bcc.com2', NULL, 'Barangay Kukak, Talisay C123ty, Negros Occidental', 'user', '', '2024-09-29 12:49:44');

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
(11, 11, 1, '2024-09-24 03:02:17', 'Non-Bago City'),
(12, 29, 1, '2024-09-23 11:38:59', 'Bago City'),
(13, 29, 1, '2024-09-24 11:40:34', 'Bago City'),
(14, 10, 1, '2024-09-28 01:12:35', 'Non-Bago City'),
(15, 11, 8, '2024-10-09 20:04:16', 'Non-Bago City'),
(16, 11, 1, '2024-10-09 20:04:37', 'Non-Bago City'),
(17, 10, 8, '2024-10-09 20:18:28', 'Non-Bago City'),
(18, 10, 1, '2024-10-09 20:18:32', 'Non-Bago City');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `User Book` (`user_id`),
  ADD KEY `Tours Book` (`tour_id`);

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
-- Indexes for table `proof`
--
ALTER TABLE `proof`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Tour's proof` (`tour_id`);

--
-- Indexes for table `qrcode`
--
ALTER TABLE `qrcode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tour_id_2` (`tour_id`),
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
  ADD KEY `Tours Image` (`tour_id`);

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
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `proof`
--
ALTER TABLE `proof`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `recommendation`
--
ALTER TABLE `recommendation`
  MODIFY `recom_id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_rating`
--
ALTER TABLE `review_rating`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `tours_image`
--
ALTER TABLE `tours_image`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `visit_records`
--
ALTER TABLE `visit_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `Tours Book` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
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
-- Constraints for table `proof`
--
ALTER TABLE `proof`
  ADD CONSTRAINT `Tour's proof` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `Tours Image` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
