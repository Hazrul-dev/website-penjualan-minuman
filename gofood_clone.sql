-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2025 at 05:19 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gofood_clone`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_large` decimal(10,2) NOT NULL,
  `price_medium` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(22, 1, 'Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.', 0, '2025-02-24 19:44:38'),
(23, 7, 'Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.', 0, '2025-02-25 05:52:53'),
(24, 7, 'Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.', 0, '2025-02-25 05:56:43'),
(25, 7, 'Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.', 0, '2025-02-25 13:32:07'),
(26, 1, 'Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.', 0, '2025-02-25 14:30:27'),
(27, 1, 'Pembelian sudah dikonfirmasi. Minuman sedang dalam pengantaran.', 0, '2025-02-26 03:53:25'),
(28, 7, 'Proses minuman gagal untuk pengiriman.', 0, '2025-02-26 03:58:42');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `menu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `size`, `quantity`, `total_price`, `order_date`, `status`, `payment_status`, `menu_id`) VALUES
(70, 1, 1, 'large', 1, '15000.00', '2025-02-25 01:31:01', 'pending', 'pending', 0),
(71, 1, 1, 'large', 1, '15000.00', '2025-02-25 01:47:35', 'pending', 'pending', 0),
(72, 1, 1, 'medium', 1, '12000.00', '2025-02-25 01:52:00', 'pending', 'pending', 0),
(73, 1, 1, 'large', 1, '15000.00', '2025-02-25 02:25:25', 'pending', 'pending', 0),
(74, 1, 1, 'medium', 1, '12000.00', '2025-02-25 02:43:14', 'pending', 'pending', 0),
(75, 7, 1, 'large', 1, '15000.00', '2025-02-25 12:49:56', 'pending', 'pending', 0),
(76, 7, 1, 'large', 2, '30000.00', '2025-02-25 12:50:53', 'pending', 'pending', 0),
(77, 7, 1, 'medium', 1, '12000.00', '2025-02-25 12:55:01', 'pending', 'pending', 0),
(78, 7, 1, 'large', 1, '15000.00', '2025-02-25 20:30:32', 'pending', 'pending', 0),
(79, 1, 2, 'medium', 1, '12000.00', '2025-02-25 21:24:47', 'pending', 'pending', 0),
(80, 1, 6, 'large', 1, '17000.00', '2025-02-25 21:27:32', 'pending', 'pending', 0),
(81, 1, 6, 'large', 1, '17000.00', '2025-02-25 21:28:37', 'pending', 'pending', 0),
(82, 1, 6, 'large', 2, '34000.00', '2025-02-26 10:25:08', 'pending', 'pending', 0),
(83, 1, 2, 'large', 1, '15000.00', '2025-02-26 10:44:00', 'pending', 'pending', 0),
(84, 7, 6, 'large', 3, '51000.00', '2025-02-26 10:57:00', 'pending', 'pending', 0);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `method` enum('BANK_BRI','BANK_MANDIRI','DANA','SHOPEEPAY','OVO','GOPAY') DEFAULT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `account_number` varchar(20) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `npwp` varchar(20) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `delivery_fee` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `method`, `bank`, `amount`, `account_number`, `full_name`, `npwp`, `phone_number`, `delivery_location`, `status`, `payment_date`, `user_id`, `delivery_fee`) VALUES
(44, 74, 'BANK_BRI', 'BRI', '22000.00', '245642147874125', 'Alwi Darmansyah', '1207232306020009', '083191795965', 'Medan Tuntungan', 'approved', '2025-02-24 19:43:40', 1, '10000.00'),
(45, 76, 'BANK_BRI', 'BRI', '40000.00', '245642147874125', 'Rohid Adriano Limbong', '1207232306020009', '081378972224', 'Medan Tuntungan', 'approved', '2025-02-25 05:52:13', 7, '10000.00'),
(46, 77, 'BANK_MANDIRI', 'Mandiri', '16000.00', '2456421478741', 'Rohid Adriano Limbong', '1207232306020009', '083191795965', 'Medan Perjuangan', 'approved', '2025-02-25 05:55:40', 7, '4000.00'),
(47, 78, 'DANA', NULL, '15000.00', NULL, NULL, NULL, '083191795965', 'Medan Deli', 'approved', '2025-02-25 13:31:21', 7, '10000.00'),
(48, 81, 'BANK_MANDIRI', 'Mandiri', '24000.00', '2456421478741', 'Rohid Adriano Limbong', '1207232306020009', '083191795965', 'Medan Barat', 'approved', '2025-02-25 14:29:28', 1, '7000.00'),
(49, 82, 'BANK_BRI', 'BRI', '44000.00', '145248745214535', 'Amelia Riski', '1207230205820009', '083191795965', 'Medan Tuntungan', 'approved', '2025-02-26 03:40:14', 1, '10000.00'),
(50, 84, 'SHOPEEPAY', NULL, '61000.00', NULL, NULL, NULL, '083191795965', 'Medan Deli', 'rejected', '2025-02-26 03:58:24', 7, '10000.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `price_large` decimal(10,2) NOT NULL,
  `price_medium` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `price_large`, `price_medium`, `image`) VALUES
(1, 'Mango Smoothies', 'Rasakan kesegaran sempurna dari Mango Smoothies, minuman es yang creamy dan menyegarkan dengan perpaduan Mangga segar dan susu yang lembut. Manis alami dengan sentuhan asam yang menyegarkan, cocok untuk menemani harimu! Nikmati setiap tegukan yang creamy, dingin, dan penuh rasa! 🥤✨', '55000.00', '15000.00', '12000.00', '67bdcb9d72a0e.jpg'),
(2, 'Avocado Smoothies', 'Nikmati kelezatan Avocado Smoothies, perpaduan sempurna antara alpukat matang, susu segar, dan sentuhan madu alami yang menghasilkan tekstur creamy dan rasa yang kaya. Cocok untuk menemani aktivitasmu, Avocado Smoothies siap menyegarkan hari dengan rasa nikmat dan manfaat sehat dalam satu gelas! 🥑🥤✨', '0.00', '15000.00', '12000.00', '67bdcc2ea051d.jpg'),
(3, 'Pokat Kocok', 'Rasakan kesegaran Pokat Kocok, minuman khas dengan alpukat matang yang dikocok sempurna, menghasilkan tekstur lembut dan creamy. Cocok untuk dinikmati kapan saja, Pokat Kocok siap menyegarkan harimu dengan kelezatan alami alpukat! 🥤💚', '0.00', '17000.00', '14000.00', '67bdcd34157b2.jpg'),
(4, 'Durian Smoothies', 'Nikmati kelembutan Durian Smoothies, perpaduan durian montong asli dengan susu segar yang menghasilkan rasa manis legit dan tekstur creamy yang menggoda. Setiap tegukan menghadirkan aroma khas durian dengan sensasi segar yang memanjakan lidah. Wajib coba bagi pecinta durian!💛', '0.00', '17000.00', '14000.00', '67bdcd840c6b2.jpg'),
(5, 'Dark Chocolate Float', 'Rasakan kenikmatan Dark Chocolate Smoothies, perpaduan sempurna antara cokelat hitam premium dan susu segar yang menghasilkan rasa pahit-manis khas dan tekstur creamy yang memanjakan. Kaya akan antioksidan, minuman ini tidak hanya lezat tetapi juga menyehatkan. Pilihan sempurna untuk pecinta cokelat sejati! 🥤🍫💛', '0.00', '15000.00', '12000.00', '67bdced37b267.jpg'),
(6, 'Sunkist Smoothies', 'Nikmati kesegaran Sunkist Smoothies, perpaduan jeruk sunkist segar dengan yogurt dan madu yang menghasilkan rasa manis-asam yang menyegarkan. Kaya akan vitamin C, minuman ini siap meningkatkan energi dan menyegarkan harimu. Cocok untuk pelepas dahaga dan menjaga imun tubuh! 🥤🍊💛', '0.00', '17000.00', '14000.00', '67bdcf37a7b52.jpg'),
(7, 'Dragon Smoothies', 'Nikmati kesegaran Dragon Smoothies, perpaduan buah naga segar dengan yogurt dan madu yang menghasilkan tekstur creamy serta rasa manis alami yang menyegarkan. Kaya akan serat, antioksidan, dan vitamin, minuman ini tidak hanya lezat tetapi juga menyehatkan. Pilihan sempurna untuk gaya hidup sehat dan penuh energi! 🥤💖', '0.00', '15000.00', '12000.00', '67bdcf96013ce.jpg'),
(8, 'Strawberry Smoothies', 'Rasakan kesegaran sempurna dari Stroberi Smoothies, minuman es yang creamy dan menyegarkan dengan perpaduan Stroberi segar dan susu yang lembut. Manis alami dengan sentuhan asam yang menyegarkan, cocok untuk menemani harimu! Nikmati setiap tegukan yang creamy, dingin, dan penuh rasa! 🥤✨', '0.00', '15000.00', '12000.00', '67bdcfd414b62.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `shop_settings`
--

CREATE TABLE `shop_settings` (
  `id` int(11) NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `shop_address` text NOT NULL,
  `shop_image` varchar(255) DEFAULT NULL,
  `bank_bri` varchar(255) DEFAULT NULL,
  `bank_mandiri` varchar(255) DEFAULT NULL,
  `dana` varchar(255) DEFAULT NULL,
  `shopeepay` varchar(255) DEFAULT NULL,
  `gopay` varchar(255) DEFAULT NULL,
  `ovo` varchar(255) DEFAULT NULL,
  `opening_hours` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shop_settings`
--

INSERT INTO `shop_settings` (`id`, `shop_name`, `shop_address`, `shop_image`, `bank_bri`, `bank_mandiri`, `dana`, `shopeepay`, `gopay`, `ovo`, `opening_hours`, `created_at`, `updated_at`) VALUES
(1, 'FLOAT SMOOTHIES MEDAN', 'Gg. tengah No.15C\r\nJalan Setia Budi, Tanjung Sari, Kota Medan', 'float.jpg', '532801036648536 An. Dwi Citra Tarcilia B', '1050017742002 An. DWI CITRA TARCILIA B', '0812-9993-6647', '0812-9993-6647', '0812-9993-6647', '0812-9993-6647', '08.00 WIB - 22.00 WIB', '2025-02-07 17:40:51', '2025-02-07 17:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') NOT NULL,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `status`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'approved'),
(2, 'hazrul', '$2y$10$dT3RetOUC4zXVxhkbmjBlO9OIhnLZ8pGpOqC7W8ALUGJmIkILv8QS', 'customer', 'pending'),
(7, 'user1', '$2y$10$dpgsjGHueGZ/HbGMs1n0M.7j1H8rImboAYeGKasAkM2xm1nnGN2Vi', 'customer', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `username` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `profile_picture`, `location`, `email`, `phone`, `username`) VALUES
(1, 1, '67bddc249e3cd.jpg', 'Medan', 'test@gmail.com', '08123456789', 'testuser'),
(2, 1, '67bddc249e3cd.jpg', 'Medan', 'test@gmail.com', '08123456789', 'testuser'),
(3, 1, '67bddc249e3cd.jpg', 'Medan', 'test@gmail.com', '08123456789', 'testuser'),
(4, 1, '67bddc249e3cd.jpg', 'Medan', 'test@gmail.com', '08123456789', 'testuser'),
(5, 2, '67a459ea37c05.jpg', 'Medan Baru', 'hazrulan23@gmail.com', '082289280632', 'hazrul'),
(6, 1, '67bddc249e3cd.jpg', 'Medan', 'test@gmail.com', '08123456789', 'testuser'),
(7, 2, '67a459ea37c05.jpg', 'Medan Baru', 'hazrulan23@gmail.com', '082289280632', 'hazrul'),
(8, 2, '67a459ea37c05.jpg', 'Medan Baru', 'hazrulan23@gmail.com', '082289280632', 'hazrul'),
(9, 1, '67bddc249e3cd.jpg', 'Medan', 'test@gmail.com', '08123456789', 'testuser'),
(10, 1, '67bddc249e3cd.jpg', 'Medan', 'test@gmail.com', '08123456789', 'testuser'),
(11, 7, '67be0a0f1dd69.jpg', 'Binjai', 'usr1@gmail.com', '083191795870', 'Pembeli1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_settings`
--
ALTER TABLE `shop_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `shop_settings`
--
ALTER TABLE `shop_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
