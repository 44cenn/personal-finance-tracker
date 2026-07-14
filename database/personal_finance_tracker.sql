-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2026 at 06:24 PM
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
-- Database: `personal_finance_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `type`, `user_id`, `created_at`) VALUES
(32, 'Gaji Pokok', 'income', NULL, '2026-07-07 08:21:54'),
(33, 'Tunjangan', 'income', NULL, '2026-07-07 08:21:54'),
(34, 'Bonus Tahunan', 'income', NULL, '2026-07-07 08:21:54'),
(35, 'Proyek Freelance', 'income', NULL, '2026-07-07 08:21:54'),
(36, 'Pendapatan Usaha', 'income', NULL, '2026-07-07 08:21:54'),
(37, 'Penjualan Aset', 'income', NULL, '2026-07-07 08:21:54'),
(38, 'Dividen Saham', 'income', NULL, '2026-07-07 08:21:54'),
(39, 'Bunga Tabungan', 'income', NULL, '2026-07-07 08:21:54'),
(40, 'Hadiah/Pemberian', 'income', NULL, '2026-07-07 08:21:54'),
(41, 'Lainnya (Pemasukan)', 'income', NULL, '2026-07-07 08:21:54'),
(42, 'Bahan Makanan Pokok', 'expense', NULL, '2026-07-07 08:21:54'),
(43, 'Buah & Sayuran', 'expense', NULL, '2026-07-07 08:21:54'),
(44, 'Daging & Ikan', 'expense', NULL, '2026-07-07 08:21:54'),
(45, 'Cemilan & Minuman', 'expense', NULL, '2026-07-07 08:21:54'),
(46, 'Makan Siang Kantor', 'expense', NULL, '2026-07-07 08:21:54'),
(47, 'Restoran & Kafe', 'expense', NULL, '2026-07-07 08:21:54'),
(48, 'Bensin/BBM', 'expense', NULL, '2026-07-07 08:21:54'),
(49, 'Transportasi Online', 'expense', NULL, '2026-07-07 08:21:54'),
(50, 'Parkir & Tol', 'expense', NULL, '2026-07-07 08:21:54'),
(51, 'Servis Kendaraan', 'expense', NULL, '2026-07-07 08:21:54'),
(52, 'Listrik & Air', 'expense', NULL, '2026-07-07 08:21:54'),
(53, 'Internet & TV Kabel', 'expense', NULL, '2026-07-07 08:21:54'),
(54, 'Cicilan Rumah/Sewa', 'expense', NULL, '2026-07-07 08:21:54'),
(55, 'Cicilan Kendaraan', 'expense', NULL, '2026-07-07 08:21:54'),
(56, 'Gas & Air Galon', 'expense', NULL, '2026-07-07 08:21:54'),
(57, 'Perlengkapan Mandi & Cuci', 'expense', NULL, '2026-07-07 08:21:54'),
(58, 'Langganan Streaming', 'expense', NULL, '2026-07-07 08:21:54'),
(59, 'Bioskop & Konser', 'expense', NULL, '2026-07-07 08:21:54'),
(60, 'Hobi', 'expense', NULL, '2026-07-07 08:21:54'),
(61, 'Obat & Vitamin', 'expense', NULL, '2026-07-07 08:21:54'),
(62, 'Konsultasi Dokter', 'expense', NULL, '2026-07-07 08:21:54'),
(63, 'Asuransi Kesehatan', 'expense', NULL, '2026-07-07 08:21:54'),
(64, 'Pendidikan Anak (SPP)', 'expense', NULL, '2026-07-07 08:21:54'),
(65, 'Buku & Alat Tulis', 'expense', NULL, '2026-07-07 08:21:54'),
(66, 'Kursus & Pelatihan', 'expense', NULL, '2026-07-07 08:21:54'),
(67, 'Pakaian & Sepatu', 'expense', NULL, '2026-07-07 08:21:54'),
(68, 'Perawatan Diri (Skincare, dll)', 'expense', NULL, '2026-07-07 08:21:54'),
(69, 'Donasi & Zakat', 'expense', NULL, '2026-07-07 08:21:54'),
(70, 'Hadiah untuk Orang Lain', 'expense', NULL, '2026-07-07 08:21:54'),
(71, 'Lainnya (Pengeluaran)', 'expense', NULL, '2026-07-07 08:21:54');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `category_id`, `type`, `amount`, `description`, `transaction_date`, `created_at`) VALUES
(1, 23, 32, 'income', 5000000.00, 'UMR', '2026-07-08', '2026-07-07 17:31:29'),
(2, 25, 38, 'income', 100000.00, '', '2026-07-08', '2026-07-08 03:24:47'),
(3, 25, 48, 'expense', 40000.00, 'kepingin hemat', '2026-06-08', '2026-07-08 03:25:53'),
(4, 25, 63, 'expense', 1000.00, '', '2026-07-08', '2026-07-08 03:26:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT 'assets/images/avatars/default.png',
  `theme` varchar(50) NOT NULL DEFAULT 'default',
  `currency` varchar(10) NOT NULL DEFAULT 'IDR',
  `language` varchar(10) NOT NULL DEFAULT 'id',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `profile_picture`, `theme`, `currency`, `language`, `created_at`) VALUES
(1, 'laut', 'laut@gmail.com', '$2y$10$R5srusWNRrKPYgFffjIAsuVhSMnVdOZtJkepNCdyXfNCPOxMZC8qK', 'admin', 'assets/images/avatars/avatar2.png', 'default', 'IDR', 'id', '2026-07-07 08:14:05'),
(2, 'admin', 'admin@gmail.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'admin', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(3, 'Citra Lestari', 'citra.l@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(4, 'Agus Wijaya', 'agus.w@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(5, 'Dewi Anggraini', 'dewi.a@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(6, 'Eko Prasetyo', 'eko.p@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(7, 'Fitriani', 'fitriani@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(8, 'Gunawan', 'gunawan@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(9, 'Herlina', 'herlina@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(10, 'Indra Maulana', 'indra.m@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(11, 'Joko Susilo', 'joko.s@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(12, 'Kartika Sari', 'kartika.s@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(13, 'Lia Puspita', 'lia.p@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(14, 'Muhammad Ali', 'm.ali@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(15, 'Nurhayati', 'nurhayati@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(16, 'Oscar Daniel', 'oscar.d@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(17, 'Putri Wulandari', 'putri.w@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(18, 'Rahmat Hidayat', 'rahmat.h@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(19, 'Siti Aminah', 'siti.a@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(20, 'Teguh Firmansyah', 'teguh.f@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(21, 'Utari Dewi', 'utari.d@example.com', '$2y$10$VODkS2dC.dF/aJ.9i.U/UuL8kY7zV6u5T4r.oPq9R8sT7uV6wX5y', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'id', '2026-07-07 08:28:06'),
(23, 'acen', 'acen@gmail.com', '$2y$10$5eeo4IaME72dRylpaz8.T.dBobK21zv.WFE41vOGG6LwR6UFEC14G', 'user', 'uploads/profile_pictures/user23-6a56415a9c7bc9.96582108.jpg', 'golden', 'IDR', 'en', '2026-07-07 16:41:04'),
(24, 'raja', 'raja@gmail.com', '$2y$10$DFnlPLr3sCEDn8I4rmmWfeMMdjMc06k4MFS1Yl1FBsuSdYDGf8dYu', 'user', 'assets/images/avatars/default.png', 'golden', 'IDR', 'id', '2026-07-08 03:18:58'),
(25, 'Raj', 'raj@gmail.com', '$2y$10$AAP8JBPI1ZUo9slSI8Nv9.1Aaorf3.vswUQEd3XSruN1tdmsvbDvy', 'user', 'assets/images/avatars/default.png', 'default', 'IDR', 'en', '2026-07-08 03:22:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categories_users` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
