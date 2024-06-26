-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2024 at 01:47 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tdl`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type_id` tinyint(4) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`, `description`, `type_id`, `is_deleted`) VALUES
(23, 'Grocery', 'Grocery', 2, 0),
(24, 'Dress', 'Dress', 2, 0),
(25, 'Nov-2023', '', 1, 0),
(26, 'Dec-2023', '', 1, 0),
(27, 'Utilities', 'Utilities', 2, 0),
(28, 'Clovar', 'Clovar', 2, 0),
(29, 'FY Expense Reporting', 'FY Expense Reporting', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_monthly` tinyint(4) NOT NULL DEFAULT 0,
  `is_active` tinyint(4) NOT NULL DEFAULT 0,
  `category_list_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`category_id`, `name`, `description`, `is_monthly`, `is_active`, `category_list_id`) VALUES
(23, 'No-frills', '', 0, 1, 17),
(23, 'Walmart', '', 0, 1, 18),
(24, 'Hudson Bay', '', 0, 1, 19),
(24, 'Winners', '', 0, 1, 20),
(27, 'Internet', 'Fido', 1, 1, 21),
(27, 'Phone', '', 1, 1, 22),
(28, 'Monthly Fee', '', 1, 1, 23);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_column_list`
--

CREATE TABLE `report_column_list` (
  `category_list_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `orderb` int(11) NOT NULL,
  `report_column_list_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_entry`
--

CREATE TABLE `tbl_entry` (
  `entry_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `category_list_id` int(11) NOT NULL,
  `ref_no` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `hst_amt` float NOT NULL,
  `total_amt` float NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_entry`
--

INSERT INTO `tbl_entry` (`entry_id`, `period_id`, `category_list_id`, `ref_no`, `created_at`, `hst_amt`, `total_amt`, `description`, `is_deleted`, `updated_at`) VALUES
(9, 25, 17, '', '2023-12-02 03:25:48', 0, 35, '', 0, NULL),
(10, 25, 20, '', '2023-12-02 03:26:29', 16, 160, 'Cosctco', 1, '2023-12-04 03:08:42'),
(11, 26, 18, '', '2023-12-02 03:27:39', 1.5, 15, '', 0, NULL),
(12, 26, 20, '', '2023-12-04 01:23:21', 10, 100, '', 0, NULL),
(13, 26, 21, '', '2023-12-12 03:43:57', 5.65, 55, '', 0, NULL),
(14, 26, 20, '', '2023-12-14 02:57:42', 44, 888, '', 0, NULL),
(15, 26, 21, '', '2023-12-14 02:57:57', 11, 222, '', 0, NULL),
(16, 25, 22, 'Fido', '2023-12-15 00:55:32', 5, 50, '', 0, NULL),
(17, 25, 19, 'Winter', '2023-12-15 00:58:24', 10, 100, '', 0, NULL),
(18, 26, 22, '', '2023-12-23 01:32:45', 5, 50, '', 1, NULL),
(19, 26, 22, '', '2023-12-23 01:35:23', 5, 50, '', 1, NULL),
(20, 26, 23, '', '2023-12-23 01:41:29', 5, 55, '', 0, NULL),
(21, 25, 23, '', '2023-12-23 01:42:06', 5, 55, '', 0, NULL),
(22, 25, 21, '', '2023-12-23 01:43:08', 5, 60, '', 0, NULL),
(23, 26, 22, '', '2023-12-23 01:43:30', 10, 70, '', 0, NULL),
(24, 26, 17, '', '2023-12-25 17:49:27', 0, 31.5, '', 0, NULL),
(25, 26, 20, '', '2023-12-25 22:11:10', 50, 100, '', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'test', 'a@a.com', NULL, '$2y$10$6Bu74g4ZgDbDNcDwB/KMh.MFrf1Xk0vS06poEeYdLF8U.oghwMoDy', '6ZLutzExqfEWj8GwkPdD3S8DBtDIgYyqvXi1JM99U3quBpafMoxtAurtA5O3', '2023-04-26 06:32:27', '2023-04-26 06:32:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`category_list_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `report_column_list`
--
ALTER TABLE `report_column_list`
  ADD PRIMARY KEY (`report_column_list_id`);

--
-- Indexes for table `tbl_entry`
--
ALTER TABLE `tbl_entry`
  ADD PRIMARY KEY (`entry_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `category_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `report_column_list`
--
ALTER TABLE `report_column_list`
  MODIFY `report_column_list_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_entry`
--
ALTER TABLE `tbl_entry`
  MODIFY `entry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
