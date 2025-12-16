-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20251104.8b43d270dd
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 28, 2025 at 04:42 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_camping_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `quantity` int DEFAULT '1',
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `item_id`, `booking_date`, `start_date`, `end_date`, `quantity`, `total_price`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2025-11-26', '2025-11-26', '2025-11-29', 1, 600000.00, 'cancelled', 'coba coba', '2025-11-26 03:54:08', '2025-11-26 04:01:46'),
(2, 2, 2, '2025-11-26', '2025-11-27', '2025-12-02', 1, 300000.00, 'cancelled', '', '2025-11-26 03:54:40', '2025-11-26 03:55:07'),
(3, 2, 1, '2025-11-26', '2025-11-28', '2025-12-06', 1, 1350000.00, 'completed', '', '2025-11-26 04:33:26', '2025-11-26 04:38:03'),
(4, 2, 2, '2025-11-26', '2025-11-28', '2025-11-30', 1, 150000.00, 'cancelled', '', '2025-11-26 04:35:29', '2025-11-26 04:40:49'),
(5, 2, 1, '2025-11-26', '2025-11-27', '2025-11-29', 2, 900000.00, 'cancelled', 'coba', '2025-11-26 04:40:05', '2025-11-26 04:45:31'),
(6, 2, 1, '2025-11-26', '2025-11-29', '2025-12-02', 3, 1800000.00, 'completed', '', '2025-11-26 04:45:51', '2025-11-28 11:29:56'),
(7, 3, 7, '2025-11-28', '2025-11-28', '2025-12-01', 1, 1000000.00, 'confirmed', 'booking ', '2025-11-28 12:08:20', '2025-11-28 14:40:45'),
(8, 3, 11, '2025-11-28', '2025-11-28', '2025-12-01', 1, 360000.00, 'pending', '', '2025-11-28 12:09:40', '2025-11-28 12:09:40'),
(9, 3, 9, '2025-11-28', '2025-11-29', '2025-11-30', 1, 90000.00, 'pending', '', '2025-11-28 15:24:39', '2025-11-28 15:24:39'),
(10, 3, 2, '2025-11-28', '2025-11-29', '2025-12-03', 3, 750000.00, 'pending', '', '2025-11-28 15:44:48', '2025-11-28 15:44:48');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_history`
--

CREATE TABLE `inventory_history` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity_before` int DEFAULT NULL,
  `quantity_after` int DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `tax` decimal(10,2) DEFAULT '0.00',
  `discount` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `status` enum('draft','sent','paid','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_per_day` decimal(10,2) NOT NULL,
  `quantity_available` int DEFAULT '1',
  `quantity_total` int DEFAULT '1',
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('available','unavailable') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `description`, `category`, `price_per_day`, `quantity_available`, `quantity_total`, `image_url`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Tenda Dome 4 Orang', 'Tenda berkualitas tinggi dengan kapasitas 4 orang, Tahan air', 'Tenda', 150000.00, 2, 5, 'assets/uploads/69269619725bd_1764136473.jpeg', 'available', '2025-11-26 03:53:48', '2025-11-26 10:31:42'),
(2, 'Sleeping Bag Premium', 'Sleeping bag premium dengan temperature rating hingga -10°C', 'Perlengkapan Tidur', 50000.00, 7, 10, 'assets/uploads/692696dc5bfd7_1764136668.jpg', 'available', '2025-11-26 03:53:48', '2025-11-28 15:44:48'),
(3, 'Matras Camping', 'Matras anti air dengan ketebalan 10cm, nyaman digunakan', 'Perlengkapan Tidur', 30000.00, 8, 8, 'assets/uploads/6926970b2ac82_1764136715.jpg', 'available', '2025-11-26 03:53:48', '2025-11-26 05:58:35'),
(4, 'Daypack 50L', 'Tas daypack berkapasitas 50 liter untuk hiking', 'Tas & Ransel', 75000.00, 6, 6, 'assets/uploads/6926972e80c09_1764136750.jpg', 'available', '2025-11-26 03:53:48', '2025-11-26 05:59:10'),
(5, 'Kompor Camping', 'Kompor portable untuk memasak di camping', 'Peralatan Masak', 40000.00, 4, 4, 'assets/uploads/6926976a36a5b_1764136810.jpg', 'available', '2025-11-26 03:53:48', '2025-11-26 06:00:10'),
(6, 'Lampu LED Camping', 'Lampu LED rechargeable dengan 3 mode cahaya', 'Penerangan', 60000.00, 7, 7, 'assets/uploads/6926979948fa7_1764136857.jpg', 'available', '2025-11-26 03:53:48', '2025-11-26 06:00:57'),
(7, 'Tenda Tunnel 6 Orang', 'Tenda besar berbentuk tunnel untuk kelompok hingga 6 orang, tahan angin dan hujan', 'Tenda', 250000.00, 4, 5, 'assets/uploads/692986bfe0069_1764329151.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 12:10:42'),
(8, 'Sleeping Bag Musim Panas', 'Sleeping bag ringan untuk suhu hangat, ideal untuk kemping musim kemarau', 'Perlengkapan Tidur', 40000.00, 12, 12, 'assets/uploads/692986ce5ec07_1764329166.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:26:06'),
(9, 'Matras Inflatable', 'Matras kempa yang bisa dikempiskan dan dikemas kecil, mudah dibawa', 'Perlengkapan Tidur', 45000.00, 9, 10, 'assets/uploads/692986eba8ca0_1764329195.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 15:24:39'),
(10, 'Tas Carrier 70L', 'Tas carrier besar dengan sistem sirkulasi udara, kapasitas 70 liter untuk ekspedisi', 'Tas & Ransel', 120000.00, 4, 4, 'assets/uploads/692986f7c3b0f_1764329207.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:26:47'),
(11, 'Set Peralatan Masak Outdoor', 'Set lengkap peralatan masak portable: panci, wajan, mangkuk, dan sendok', 'Peralatan Masak', 90000.00, 4, 5, 'assets/uploads/69298704eb6ba_1764329220.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 12:09:40'),
(12, 'Lentera Gantung Solar', 'Lentera gantung berbahan tahan lama dengan panel surya untuk pengisian daya', 'Penerangan', 70000.00, 6, 6, 'assets/uploads/6929871d630c0_1764329245.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:27:25'),
(13, 'Tenda Ultralight 2 Orang', 'Tenda ringan dan mudah dipasang untuk dua orang, ideal untuk backpacking', 'Tenda', 180000.00, 4, 4, 'assets/uploads/6929872bc5d0e_1764329259.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:27:39'),
(14, 'Kantong Tidur Anak', 'Sleeping bag lucu dan hangat untuk anak-anak usia 5–10 tahun', 'Perlengkapan Tidur', 35000.00, 8, 8, 'assets/uploads/692987391e635_1764329273.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:27:53'),
(15, 'Hydration Bladder 3L', 'Tempat air isi ulang kapasitas 3 liter untuk tas hiking', 'Aksesoris Hiking', 55000.00, 9, 9, 'assets/uploads/692987468e2e6_1764329286.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:28:06'),
(16, 'Senter Headlamp LED', 'Senter kepala dengan cahaya terang dan baterai tahan lama', 'Penerangan', 65000.00, 10, 10, 'assets/uploads/692987524b169_1764329298.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:28:18'),
(17, 'Pompa Inflator Manual', 'Pompa portable untuk mengisi matras atau peralatan kemping lainnya', 'Aksesoris Hiking', 25000.00, 7, 7, 'assets/uploads/69298761017b8_1764329313.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:28:33'),
(18, 'Fire Starter Kit', 'Alat pembuat api darurat yang tahan air dan angin', 'Survival', 30000.00, 15, 15, 'assets/uploads/6929876e5d182_1764329326.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:28:46'),
(19, 'Water Filter Portable', 'Filter air portable untuk menyaring air sungai atau danau agar aman diminum', 'Survival', 200000.00, 2, 2, 'assets/uploads/6929877ad4443_1764329338.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:28:58'),
(20, 'Tikar Camping Lipat', 'Tikar lipat anti air dan tahan lama untuk duduk atau alas tenda', 'Aksesoris Hiking', 20000.00, 20, 20, 'assets/uploads/69298786d054c_1764329350.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:29:10'),
(21, 'Cooking Stove Gas Mini', 'Kompor gas mini dengan desain ringkas dan efisien', 'Peralatan Masak', 50000.00, 6, 6, 'assets/uploads/69298798c8e2a_1764329368.jpg', 'available', '2025-11-28 11:18:05', '2025-11-28 11:29:28');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','completed','failed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `user_id`, `amount`, `payment_method`, `transaction_id`, `status`, `payment_date`, `created_at`) VALUES
(1, 1, 2, 600000.00, 'cash', 'TRX-20251126105415-2792', 'failed', '2025-11-26 03:54:15', '2025-11-26 03:54:15'),
(2, 2, 2, 300000.00, 'e_wallet', 'TRX-20251126105448-6484', 'failed', '2025-11-26 03:54:48', '2025-11-26 03:54:48'),
(3, 3, 2, 1350000.00, 'cash', 'TRX-20251126113333-9572', 'completed', '2025-11-26 04:33:33', '2025-11-26 04:33:33'),
(4, 4, 2, 150000.00, 'cash', 'TRX-20251126113710-3468', 'failed', '2025-11-26 04:37:10', '2025-11-26 04:37:10'),
(5, 5, 2, 900000.00, 'e_wallet', 'TRX-20251126114013-6916', 'failed', '2025-11-26 04:40:13', '2025-11-26 04:40:13'),
(6, 6, 2, 1800000.00, 'e_wallet', 'TRX-20251126114556-7548', 'completed', '2025-11-26 04:45:56', '2025-11-26 04:45:56'),
(7, 7, 3, 1000000.00, 'e_wallet', 'TRX-20251128190831-8587', 'completed', '2025-11-28 12:08:31', '2025-11-28 12:08:31'),
(8, 8, 3, 360000.00, 'e_wallet', 'TRX-20251128190948-9719', 'pending', '2025-11-28 12:09:48', '2025-11-28 12:09:48'),
(9, 9, 3, 90000.00, 'e_wallet', 'TRX-20251128222556-7048', 'pending', '2025-11-28 15:25:56', '2025-11-28 15:25:56'),
(10, 10, 3, 750000.00, 'e_wallet', 'TRX-20251128224456-7602', 'pending', '2025-11-28 15:44:56', '2025-11-28 15:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `booking_id` int NOT NULL,
  `user_id` int NOT NULL,
  `item_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@campingrental.com', '$2a$12$B8aq6zZ95ulGrgdp7BQc2.VxY7Cxn/6bL2xj6TK2TwYkEY1/t4G3W', 'Administrator', '081234567890', NULL, 'admin', 'active', '2025-11-25 22:37:39', '2025-11-25 22:40:00'),
(2, 'user1', 'user1@example.com', '$2a$12$x5IH8WDtpdzAslTi1ePXvOfg3.BHGPFQKz/vjOeEERrH407CJQ.iC', 'John Doe', '082234567890', NULL, 'user', 'active', '2025-11-25 22:37:39', '2025-11-25 23:03:04'),
(3, 'syahrul', 'syahrulumaediii@gmail.com', '$2y$10$RABayI61ZI53P90ViimxNeMW0EBe0oBXlfkGwhfL0wN9vhQSzpfYO', 'Syahrul Umaedi', '081223807456', NULL, 'user', 'active', '2025-11-28 12:01:51', '2025-11-28 12:01:51'),
(4, 'ika', 'ikamarlena@gmail.com', '$2y$10$ZFg.qfo0/Fs185g5ko3axOnv4XOUaWRE2R4tvc9Q4RTDBrLg6DRE.', 'Ika', '081223807456', NULL, 'user', 'active', '2025-11-28 12:05:59', '2025-11-28 12:05:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_item_id` (`item_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_start_date` (`start_date`);

--
-- Indexes for table `inventory_history`
--
ALTER TABLE `inventory_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_item_id` (`item_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_item_id` (`item_id`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `inventory_history`
--
ALTER TABLE `inventory_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_history`
--
ALTER TABLE `inventory_history`
  ADD CONSTRAINT `inventory_history_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
