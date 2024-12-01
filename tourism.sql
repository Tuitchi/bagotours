-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2024 at 02:24 PM
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
-- Table structure for table `accommodations`
--

CREATE TABLE `accommodations` (
  `id` int(11) NOT NULL,
  `tour_id` int(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(100) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `total_units` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `accommodations`
--

INSERT INTO `accommodations` (`id`, `tour_id`, `name`, `description`, `type`, `cost`, `capacity`, `total_units`) VALUES
(1, 1, 'Large Cottage', '20 per cottage.', 'Cottage', 0.00, 20, 10),
(2, 1, 'Large', 'Capable of 50 person per Large Cottage\r\n', 'Cottage', 0.00, 50, 4);

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `tour_id` int(50) NOT NULL,
  `people` int(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(10) NOT NULL COMMENT '0 = Pending, 1 = Confirmed, 2 = Cancelled, 3 = Ongoing, 4 = Completed',
  `is_review` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_accommodations`
--

CREATE TABLE `booking_accommodations` (
  `booking_id` int(11) NOT NULL,
  `accommodation_id` int(11) NOT NULL,
  `units_reserved` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_code` bigint(20) DEFAULT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_description` text DEFAULT NULL,
  `event_date_start` datetime NOT NULL,
  `event_date_end` datetime NOT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `organizer_name` varchar(255) DEFAULT NULL,
  `organizer_contact` varchar(255) DEFAULT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `ticket_price` decimal(10,2) DEFAULT NULL,
  `ticket_type` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `registration_deadline` datetime DEFAULT NULL,
  `event_image` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `sponsor` varchar(255) DEFAULT NULL,
  `status` enum('upcoming','postponed','completed') DEFAULT 'upcoming',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees`
--

CREATE TABLE `fees` (
  `id` int(11) NOT NULL,
  `tour_id` int(11) DEFAULT NULL,
  `fee_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `fees`
--

INSERT INTO `fees` (`id`, `tour_id`, `fee_type`, `amount`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Entrance Fee', 30.00, 'Per Head', '2024-11-29 20:04:58', '2024-11-29 20:04:58');

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
  `tour_id` bigint(20) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(1000) NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`) VALUES
(28, 10, '191dc688efff00692c8b0ca6b592f76b', '2024-11-18 01:27:40');

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
-- Table structure for table `review_rating`
--

CREATE TABLE `review_rating` (
  `id` int(50) NOT NULL,
  `tour_id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `rating` int(50) NOT NULL,
  `review` varchar(500) NOT NULL,
  `img` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_rating`
--

INSERT INTO `review_rating` (`id`, `tour_id`, `user_id`, `rating`, `review`, `img`, `date_created`, `date_updated`) VALUES
(20, 1, 10, 5, 'asdasdas', '', '2024-11-19 09:21:59', NULL),
(21, 9, 10, 5, 'qweqwdqw', '', '2024-11-19 09:25:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `file` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `name`, `file`, `type`) VALUES
(1, 'icon', 'websiteIcon.png', 'Tab Icon');

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
  `description` mediumtext NOT NULL,
  `price_title` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `proof_title` varchar(255) NOT NULL,
  `proof_image` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL COMMENT ' 0 = Pending, 1 = Active, 2 = Cancelled, 3 = Inactive.',
  `bookable` tinyint(1) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `expiry` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `user_id`, `img`, `title`, `address`, `latitude`, `longitude`, `type`, `description`, `price_title`, `price`, `proof_title`, `proof_image`, `status`, `bookable`, `date_created`, `expiry`) VALUES
(1, 11, 'buenos.jpg,1731977068_247405_1731320525931_1326041679_31461205_7070072_n.jpg,1731977068_buenos-aires-mountain.jpg', 'Buenos Aires Mountain Resort', 'Barangay Ilijan, Bago City, Negros Occidental Philippines', ' 10.45456', '123.04783', 'Beach Resort', 'Located in Barangay Ilijan, 29 kilometers east of Bago City, the resort was a stopover of President Quezon on his way to Zamboanguita in Negros Oriental during his escape from Japanese forces in World War II. It is owned and operated by the City Government of Bago. Ideal for family vacations, the resort has four swimming pools, a training center, a pavilion, cottages and a hostel.', '', 0, '', '', '1', 1, '2024-08-11 22:51:15', NULL),
(7, 11, '1724504783_bantayanpark.jpg', 'Bantayan Park', 'Bantayan Park, Rizal Street, Bago City, Negros Occidental', '10.536883439109104', '122.83162734662949', 'Park', 'A place where you can chill, walk, run, bike, eat, and even make your tikt0k videos! The views in this park is good in any time of the day. In the morning, its a place for people to exercise or run. I', '', 0, '', '', '1', 0, '2024-08-24 21:06:23', NULL),
(8, 11, '1724510289_balaynitanjuan.jpg', 'Balay ni Tan Juan', 'Rizal Street Barangay Poblacion, Bago City, Negros Occidental', '10.534373503857907', '122.83485882945081', 'Historical Landmark', 'If there is any structure that mirrors the resilient spirit of the people of Bago City in Negros Occidental, then it would be Balay ni Tan Juan, the ancestral home of the Negrense revolutionary hero G', '', 0, '', '', '1', 1, '2024-08-24 22:38:09', NULL),
(9, 11, '1724569316_tanjuan.jpg', 'Tan Juan Monument', 'GRQP+297, Araneta Avenue, Bago City, Negros Occidental', '10.537537930388424', '122.83591358344103', 'Historical Landmark', 'The Gen. Juan Araneta monument at the heart of the city is not only a monument but the final resting place of Tan Juan?On the occasion of his 50th death anniversary, a ceremony was held for Araneta’s reburial at the base of the monument built in his memory at the public plaza (Gen. Juan A. Araneta Park). ', '', 0, '', '', '1', 0, '2024-08-25 15:01:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `profile_picture` varchar(200) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `gender` varchar(150) NOT NULL,
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

INSERT INTO `users` (`id`, `profile_picture`, `name`, `gender`, `username`, `password`, `email`, `phone_number`, `home_address`, `role`, `device_id`, `date_created`) VALUES
(10, 'default.png', 'Crez Rowell Dion Mustre', '', 'crez', '$2y$10$m2yshWj1y4jBULgEvH.rSOBt6eghfkMKqv/VLG33VeLb3oysTY65W', 'niklapatan@gmail.com', '09054604913', '', 'user', '0908be040727c251ad7d9d409cfdb1e6', '2024-08-12 04:40:06'),
(11, 'default.png', '', '', 'admin', '$2y$10$caZecj.nFNj0ZBuDLBxtUeL2Zqc2U/7Sp/.NefXxJalI9JMMa3s5y', 'admin@gmail.com', NULL, '', 'admin', '8992d1b8b6e8ec5f0975eba68ed8d306', '2024-08-12 04:40:06'),
(26, 'default.png', 'user nik', '', 'user', '$2y$10$WRGnAfRfBO/gySLdbhDDdO04L0wsTGa2l0IDzGMmweVufQBblWbR.', 'user@gmail.com', '09054604916', 'Bago City, Negros Occidental, Philippines', 'user', '3ed7e38e38d58e1ecd3a6417c54f87c3', '2024-09-23 18:12:42'),
(27, 'default.png', 'Nikolai Lapatan', '', 'nonbago', '$2y$10$7bLMbsREYUDOysa3.kxDHekQ1zblkr4r1BtRVIDcnJn3ZLA5ck8Q6', 'nonbago@gmail.com', NULL, 'Barangay Kukak, Talisay City, Negros Occidental', 'user', '', '2024-09-23 18:34:51'),
(28, 'default.png', 'owner owner', '', 'owner', '$2y$10$Z7G01WznGDLJSqOWkMGzae.T08fRh.LIcrfr.yZir.NwCv32dwHNy', 'owner@gmail.com', NULL, 'Barangay Gwe, Talisay City, Negros Occidental', 'user', '0d930107bae984a115472005837fa5ac', '2024-09-24 03:26:01'),
(29, 'default.png', 'user user', '', 'user1', '$2y$10$5vnCO/cp1P5YKfF5HpU1muAl.ogMv8R1lZNg4SkNb4dgSfUGdtM.u', 'user1@gmail.com', NULL, 'Purok Daisy, Barangay Calumangan, Bago City', 'user', '', '2024-09-24 03:38:59'),
(30, 'default.png', 'Nikolai Lapatan', '', 'Bago123', '$2y$10$P7/65skfeWbY0FNN7mQ/W.BEJyEZR934E8WHYf2UhOgO2BrIEnnRC', 'chelester@bcc.com', NULL, 'Purok Daisy Calumangan', 'user', '', '2024-09-29 12:47:36'),
(31, 'default.png', 'Nikolai Lapatan', '', 'demo123', '$2y$10$OxgP7Uar/qmuuGkEOpXTu.VBd7yVgsUieOJyr.L9ln4nY28lYQjum', 'chelester@bcc.com2', NULL, 'Barangay Kukak, Talisay C123ty, Negros Occidental', 'user', '', '2024-09-29 12:49:44'),
(32, 'default.png', 'Nikolai Lapatan', '', 'nikolai1234', '$2y$10$U4WnpEUgTfIzLN6loxtzxugnSMrp/uI79EBZKvZivCBVYpsQguNF6', 'niklapatan1@gmail.com', NULL, 'Purok Daisy Calumangan', 'user', 'e9b9c8e4366b26541f3975a94f3b5ee9', '2024-11-01 16:10:00'),
(33, 'default.png', 'Nikolai Lapatan', '', 'niko', '$2y$10$j76fOkk/mNuaAt2eVoILcOIFcnh1HWSrk/en1Simmh3T72zoy49f.', 'lapatan20@gmail.com', NULL, 'Purok Daisy Calumangan Bago City', 'user', '846033da672aa388a077fe3bb25f650e', '2024-11-09 14:07:04'),
(34, 'default.png', 'Niko Lapatan', '', 'nikolai1222', '$2y$10$zZi/rptbWVTzUPSsao7st.VQ9CQMGWJ1ukUu4dv1yth6IzyThOv1.', 'test@gmail.com', NULL, 'Purok Daisy Calumangan Bago City', 'user', '13971e8b109ccc5e5cefe1579870936d', '2024-11-17 12:03:59');

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
(18, 10, 1, '2024-10-09 20:18:32', 'Non-Bago City'),
(19, 10, 1, '2024-11-15 01:29:57', 'Non-Bago City'),
(20, 11, 1, '2024-11-15 01:33:14', 'Non-Bago City');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Tour_accommodation` (`tour_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `User Book` (`user_id`),
  ADD KEY `Tours Book` (`tour_id`);

--
-- Indexes for table `booking_accommodations`
--
ALTER TABLE `booking_accommodations`
  ADD PRIMARY KEY (`booking_id`,`accommodation_id`),
  ADD KEY `accommodation_id` (`accommodation_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `fees`
--
ALTER TABLE `fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Tour_fees` (`tour_id`);

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
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `qrcode`
--
ALTER TABLE `qrcode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tour_id_2` (`tour_id`),
  ADD KEY `tour_id` (`tour_id`);

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
-- AUTO_INCREMENT for table `accommodations`
--
ALTER TABLE `accommodations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `fees`
--
ALTER TABLE `fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inquiry`
--
ALTER TABLE `inquiry`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `review_rating`
--
ALTER TABLE `review_rating`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `visit_records`
--
ALTER TABLE `visit_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD CONSTRAINT `Tour_accommodation` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `Tours Book` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `User Book` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `booking_accommodations`
--
ALTER TABLE `booking_accommodations`
  ADD CONSTRAINT `booking_accommodations_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`),
  ADD CONSTRAINT `booking_accommodations_ibfk_2` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations` (`id`);

--
-- Constraints for table `fees`
--
ALTER TABLE `fees`
  ADD CONSTRAINT `Tour_fees` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `visit_records`
--
ALTER TABLE `visit_records`
  ADD CONSTRAINT `visit_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `visit_records_ibfk_2` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
