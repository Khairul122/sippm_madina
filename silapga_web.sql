-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 09, 2026 at 07:48 PM
-- Server version: 10.4.34-MariaDB
-- PHP Version: 7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `silapga_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `actor_type` varchar(255) NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `rejection_reason` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `title`, `description`, `actor_type`, `actor_id`, `date`, `location`, `status`, `rejection_reason`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Gotong Royong Bersih Desa', 'Kegiatan gotong royong membersihkan saluran air dan fasilitas umum bersama warga.', 'opd', 1, '2026-07-18', 'Balai Desa', 'draft', NULL, NULL, '2026-07-21 07:06:13', '2026-07-21 07:06:13'),
(2, 'Musyawarah Perencanaan Pembangunan Kecamatan', 'Musrenbang tingkat kecamatan membahas prioritas pembangunan tahun berikutnya.', 'kecamatan', 1, '2026-07-20', 'Aula Kantor Camat', 'draft', NULL, NULL, '2026-07-21 07:06:13', '2026-07-21 07:06:13'),
(3, 'Kebersihan', '<p>Kebersihan</p>', 'kecamatan', 20, '2026-07-29', 'Mesjid Agung Syahrun Nur', 'draft', NULL, NULL, '2026-07-30 05:10:45', '2026-07-31 02:28:40'),
(4, 'Sosialisasi Pencegahan Stunting', '<p>Penyuluhan mengenai pencegahan stunting kepada msyarakat</p>', 'kecamatan', 1, '2026-07-29', 'Aula Kantor Camat', 'draft', NULL, NULL, '2026-08-04 13:41:22', '2026-08-04 13:41:22'),
(5, 'Penanaman Pohon', '<p>Kegiatan penghijauan bersama masyarakat dan instansi terkait.</p>', 'opd', 1, '2026-08-07', 'Ruang hijau terbuka', 'draft', NULL, NULL, '2026-08-04 13:48:04', '2026-08-04 13:48:36');

-- --------------------------------------------------------

--
-- Table structure for table `activity_documentations`
--

CREATE TABLE `activity_documentations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_documentations`
--

INSERT INTO `activity_documentations` (`id`, `activity_id`, `file_path`, `caption`, `created_at`, `updated_at`) VALUES
(1, 3, 'activity-documentations/R7SxslZy0ZubAkHS6dg1gs5cB0j1DzTeSkaeqVIE.png', NULL, '2026-07-30 05:10:45', '2026-07-30 05:10:45');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES
(1, NULL, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintSubmitted', 'complaint', 1, NULL, '{\"status\": \"diajukan\", \"ticket_number\": \"PGD-2026-000001\"}', '127.0.0.1', '2026-07-21 07:06:12'),
(2, NULL, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintSubmitted', 'complaint', 2, NULL, '{\"status\": \"diajukan\", \"ticket_number\": \"PGD-2026-000002\"}', '127.0.0.1', '2026-07-21 07:06:13'),
(3, 8, 'login_failed', 'user', 8, NULL, '{\"email\": \"admin@gmail.com\"}', '127.0.0.1', '2026-07-21 07:31:05'),
(4, 2, 'login', 'user', 2, NULL, '{\"guard\": \"web\"}', '127.0.0.1', '2026-07-21 07:32:45'),
(5, 2, 'logout', 'user', 2, NULL, '{\"guard\": \"web\"}', '127.0.0.1', '2026-07-21 07:44:21'),
(6, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.69.176.141', '2026-07-21 07:58:54'),
(7, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.69.166.100', '2026-07-30 04:48:41'),
(8, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintVerified', 'complaint', 2, '{\"status\":\"diajukan\"}', '{\"status\":\"diverifikasi\",\"is_valid\":true,\"rejection_reason\":null}', '172.69.166.100', '2026-07-30 04:49:21'),
(9, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDisposed', 'complaint', 2, '{\"status\":\"diverifikasi\"}', '{\"status\":\"diproses\",\"disposed_to_type\":\"camat\",\"disposed_to_id\":20}', '104.23.175.32', '2026-07-30 04:49:57'),
(10, 2, 'user_created', 'user', 9, NULL, '{\"name\":\"CAMAT PANYABUNGAN\",\"email\":\"camatpanyabungan@gmail.com\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"20\",\"phone\":\"081377241610\",\"nik\":\"12030457000\"}', '172.68.164.138', '2026-07-30 05:01:41'),
(11, NULL, 'login_failed', 'user', NULL, NULL, '{\"email\":\"fatimahzahro1008@gmail.com\"}', '162.158.88.103', '2026-07-30 05:01:59'),
(12, NULL, 'login_failed', 'user', NULL, NULL, '{\"email\":\"fatimahzahro1008@gmail.com\"}', '162.158.88.103', '2026-07-30 05:02:04'),
(13, 9, 'login_failed', 'user', 9, NULL, '{\"email\":\"camatpanyabungan@gmail.com\"}', '172.69.176.140', '2026-07-30 05:06:46'),
(14, 9, 'login_failed', 'user', 9, NULL, '{\"email\":\"camatpanyabungan@gmail.com\"}', '172.68.164.138', '2026-07-30 05:07:32'),
(15, 2, 'user_updated', 'user', 9, '{\"name\":\"CAMAT PANYABUNGAN\",\"email\":\"camatpanyabungan@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":20,\"phone\":\"081377241610\",\"nik\":\"12030457000\"}', '{\"name\":\"CAMAT PANYABUNGAN\",\"email\":\"camatpanyabungan@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":\"20\",\"phone\":\"081377241610\",\"nik\":\"12030457000\"}', '172.69.166.100', '2026-07-30 05:08:16'),
(16, 9, 'login', 'user', 9, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-07-30 05:08:33'),
(17, 9, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintHandled', 'complaint', 2, '{\"status\":\"diproses\"}', '{\"status\":\"ditindaklanjuti\"}', '172.68.164.138', '2026-07-30 05:09:11'),
(18, 9, 'logout', 'user', 9, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-07-30 05:11:07'),
(19, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintResolved', 'complaint', 2, '{\"status\":\"ditindaklanjuti\"}', '{\"status\":\"selesai\",\"response_text\":\"Lampu sudah diperbaiki dan diganti dan diperbaiki\"}', '172.69.166.101', '2026-07-30 05:11:49'),
(20, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '172.68.164.138', '2026-07-30 05:12:25'),
(21, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintVerified', 'complaint', 1, '{\"status\":\"diajukan\"}', '{\"status\":\"diverifikasi\",\"is_valid\":true,\"rejection_reason\":null}', '172.68.164.138', '2026-07-30 05:14:53'),
(22, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDisposed', 'complaint', 1, '{\"status\":\"diverifikasi\"}', '{\"status\":\"diproses\",\"disposed_to_type\":\"opd\",\"disposed_to_id\":29}', '172.68.164.138', '2026-07-30 05:15:15'),
(23, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDispositionCancelled', 'complaint', 1, '{\"status\":\"diproses\"}', '{\"status\":\"diverifikasi\",\"cancelled_target_type\":\"opd\",\"cancelled_target_id\":29}', '172.68.164.138', '2026-07-30 05:15:28'),
(24, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ActivityPublished', 'activity', 3, '{\"status\":\"diverifikasi\"}', '{\"status\":\"dipublikasikan\"}', '172.68.164.138', '2026-07-30 05:16:44'),
(25, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-07-30 05:16:57'),
(26, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.69.166.100', '2026-07-30 05:25:34'),
(27, NULL, 'login_failed', 'user', NULL, NULL, '{\"email\":\"jhdjjaduyaduahd@gmail.com\"}', '172.70.189.32', '2026-07-30 06:26:50'),
(28, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.68.164.138', '2026-07-30 06:47:44'),
(29, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-07-30 10:51:17'),
(30, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.68.164.138', '2026-07-30 10:52:26'),
(31, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-07-30 10:53:04'),
(32, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '162.158.108.157', '2026-07-30 12:43:48'),
(33, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-07-30 13:09:16'),
(34, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '162.158.189.252', '2026-07-30 13:10:00'),
(35, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '172.70.189.32', '2026-07-30 13:10:48'),
(36, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '172.70.189.31', '2026-07-30 13:11:07'),
(37, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.23.175.33', '2026-07-30 13:41:35'),
(38, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.23.175.33', '2026-07-30 13:42:37'),
(39, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.69.166.101', '2026-07-30 13:57:11'),
(40, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.69.166.100', '2026-07-30 13:57:37'),
(41, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '108.162.226.8', '2026-07-30 14:09:06'),
(42, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '108.162.226.9', '2026-07-30 14:09:47'),
(43, 4, 'logout', 'user', 4, NULL, '{\"guard\":\"web\"}', '162.158.170.52', '2026-07-30 14:16:22'),
(44, 5, 'login', 'user', 5, NULL, '{\"guard\":\"web\"}', '172.69.166.101', '2026-07-30 14:16:54'),
(45, 5, 'logout', 'user', 5, NULL, '{\"guard\":\"web\"}', '172.69.166.100', '2026-07-30 14:35:05'),
(46, 6, 'login', 'user', 6, NULL, '{\"guard\":\"web\"}', '162.158.162.161', '2026-07-30 14:35:54'),
(47, 6, 'logout', 'user', 6, NULL, '{\"guard\":\"web\"}', '172.70.208.157', '2026-07-30 14:39:38'),
(48, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.208.156', '2026-07-30 14:40:00'),
(49, 8, 'login_failed', 'user', 8, NULL, '{\"email\":\"admin@gmail.com\"}', '172.70.208.156', '2026-07-30 21:18:21'),
(50, 8, 'login_failed', 'user', 8, NULL, '{\"email\":\"admin@gmail.com\"}', '172.71.124.232', '2026-07-30 21:19:14'),
(51, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.92.141', '2026-07-30 21:20:32'),
(52, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.92.140', '2026-07-30 21:21:12'),
(53, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.71.124.233', '2026-07-31 01:50:17'),
(54, NULL, 'login_failed', 'user', NULL, NULL, '{\"email\":\"fatimahzahro1008@gmail.com\"}', '162.158.108.157', '2026-07-31 02:23:15'),
(55, NULL, 'login_failed', 'user', NULL, NULL, '{\"email\":\"fatimahzahro1008@gmail.com\"}', '162.158.108.156', '2026-07-31 02:23:24'),
(56, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '162.158.170.52', '2026-07-31 02:25:39'),
(57, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '162.158.107.69', '2026-07-31 02:29:47'),
(58, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '104.22.66.145', '2026-07-31 02:31:09'),
(59, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.69.176.141', '2026-07-31 02:32:24'),
(60, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.68.164.138', '2026-07-31 02:37:26'),
(61, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.69.166.101', '2026-07-31 02:46:58'),
(62, 2, 'user_created', 'user', 10, NULL, '{\"name\":\"CAMAT BATAHAN\",\"email\":\"camatbatahan@gmail.com\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"40\",\"phone\":\"0000\",\"nik\":\"00000\"}', '172.70.208.157', '2026-07-31 02:48:41'),
(63, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.23.175.33', '2026-07-31 02:56:27'),
(64, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '162.158.88.103', '2026-07-31 02:59:15'),
(65, 2, 'user_created', 'user', 11, NULL, '{\"name\":\"Kecamatan Batang Natal\",\"email\":\"camatbatangnatal@gmail.com\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"37\",\"phone\":\"00000\",\"nik\":\"000001\"}', '104.23.175.33', '2026-07-31 02:59:59'),
(66, 2, 'user_updated', 'user', 9, '{\"name\":\"CAMAT PANYABUNGAN\",\"email\":\"camatpanyabungan@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":20,\"phone\":\"081377241610\",\"nik\":\"12030457000\"}', '{\"name\":\"CAMAT PANYABUNGAN\",\"email\":\"kec-panyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":\"20\",\"phone\":\"081377241610\",\"nik\":\"12030457000\"}', '104.23.175.33', '2026-07-31 03:07:03'),
(67, 2, 'user_created', 'user', 12, NULL, '{\"name\":\"camat22\",\"email\":\"camat22@gmail.com\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"40\",\"phone\":\"081240907080\",\"nik\":\"121409080709070\"}', '172.68.164.138', '2026-07-31 03:09:10'),
(68, 2, 'user_updated', 'user', 12, '{\"name\":\"camat22\",\"email\":\"camat22@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":40,\"phone\":\"081240907080\",\"nik\":\"121409080709070\"}', '{\"name\":\"camat22\",\"email\":\"camat22@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":\"40\",\"phone\":\"081240907081\",\"nik\":\"121409080709070\"}', '172.68.164.138', '2026-07-31 03:09:33'),
(69, 2, 'user_deactivated', 'user', 12, '{\"is_active\":true}', '{\"is_active\":false}', '172.68.164.138', '2026-07-31 03:09:59'),
(70, 2, 'user_activated', 'user', 12, '{\"is_active\":false}', '{\"is_active\":true}', '172.68.164.138', '2026-07-31 03:10:09'),
(71, 2, 'user_deleted', 'user', 12, '{\"name\":\"camat22\",\"email\":\"camat22@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":40,\"phone\":\"081240907081\",\"nik\":\"121409080709070\"}', '[]', '172.68.164.138', '2026-07-31 03:10:16'),
(72, 2, 'user_created', 'user', 13, NULL, '{\"name\":\"Kecamatan Panyabungan Utara\",\"email\":\"kec-pybutara@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"23\",\"phone\":\"0000000\",\"nik\":\"00002\"}', '172.68.164.139', '2026-07-31 03:10:32'),
(73, 2, 'user_created', 'user', 14, NULL, '{\"name\":\"Kecamatan Panyabungan Barat\",\"email\":\"kec-pybbarat@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"22\",\"phone\":\"000000\",\"nik\":\"00003\"}', '162.158.108.157', '2026-07-31 03:14:35'),
(74, 2, 'user_created', 'user', 15, NULL, '{\"name\":\"Kecamatan Panyabungan Selatan\",\"email\":\"kec-pybselatan@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"30\",\"phone\":\"00000004\",\"nik\":\"000005\"}', '162.158.162.160', '2026-07-31 03:18:31'),
(75, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '162.158.170.53', '2026-07-31 03:20:33'),
(76, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '162.158.170.52', '2026-07-31 03:20:51'),
(77, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-07-31 03:21:59'),
(78, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-07-31 03:22:19'),
(79, 4, 'logout', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-07-31 03:22:33'),
(80, 2, 'user_created', 'user', 16, NULL, '{\"name\":\"Kecamatan Panyabungan Timur\",\"email\":\"kec-pybtimur@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"21\",\"phone\":\"000004\",\"nik\":\"0000006\"}', '172.69.166.100', '2026-07-31 03:22:48'),
(81, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '162.158.108.157', '2026-07-31 03:24:24'),
(82, 2, 'user_created', 'user', 17, NULL, '{\"name\":\"Kecamatan Siabu\",\"email\":\"kec-siabu@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"25\",\"phone\":\"00000005\",\"nik\":\"000007\"}', '172.70.208.156', '2026-07-31 03:25:15'),
(83, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.69.166.101', '2026-07-31 03:25:47'),
(84, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.69.176.141', '2026-07-31 03:26:20'),
(85, 5, 'login', 'user', 5, NULL, '{\"guard\":\"web\"}', '172.69.176.140', '2026-07-31 03:26:53'),
(86, 2, 'user_updated', 'user', 10, '{\"name\":\"CAMAT BATAHAN\",\"email\":\"camatbatahan@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":40,\"phone\":\"0000\",\"nik\":\"00000\"}', '{\"name\":\"Kecamatan Batahan\",\"email\":\"kec-batahan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":\"40\",\"phone\":\"0000\",\"nik\":\"00000\"}', '104.22.66.145', '2026-07-31 03:27:52'),
(87, 2, 'user_created', 'user', 18, NULL, '{\"name\":\"Kecamatan Bukit Malintang\",\"email\":\"kec-malintang@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"24\",\"phone\":\"00000006\",\"nik\":\"00008\"}', '172.70.208.156', '2026-07-31 03:27:58'),
(88, 2, 'user_updated', 'user', 9, '{\"name\":\"CAMAT PANYABUNGAN\",\"email\":\"kec-panyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":20,\"phone\":\"081377241610\",\"nik\":\"12030457000\"}', '{\"name\":\"Kecamatan Panyabungan\",\"email\":\"kec-panyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":\"20\",\"phone\":\"000008\",\"nik\":\"000009\"}', '104.22.66.144', '2026-07-31 03:29:23'),
(89, 5, 'logout', 'user', 5, NULL, '{\"guard\":\"web\"}', '172.69.176.141', '2026-07-31 03:29:32'),
(90, 2, 'user_updated', 'user', 9, '{\"name\":\"Kecamatan Panyabungan\",\"email\":\"kec-panyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":20,\"phone\":\"000008\",\"nik\":\"000009\"}', '{\"name\":\"Kecamatan Panyabungan\",\"email\":\"kec-panyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":\"20\",\"phone\":\"000008\",\"nik\":\"000009\"}', '104.22.66.144', '2026-07-31 03:30:11'),
(91, 2, 'user_created', 'user', 19, NULL, '{\"name\":\"Kecamatan Naga Juang\",\"email\":\"kec-nagajuang@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"26\",\"phone\":\"000007\",\"nik\":\"0000010\"}', '172.69.176.140', '2026-07-31 03:30:15'),
(92, 2, 'user_updated', 'user', 10, '{\"name\":\"Kecamatan Batahan\",\"email\":\"kec-batahan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":40,\"phone\":\"0000\",\"nik\":\"00000\"}', '{\"name\":\"Kecamatan Batahan\",\"email\":\"kec-batahan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":\"40\",\"phone\":\"0000\",\"nik\":\"00000\"}', '104.22.66.145', '2026-07-31 03:30:36'),
(93, 2, 'user_created', 'user', 20, NULL, '{\"name\":\"Kecamtan Huta Bargot\",\"email\":\"kec-hutabargot@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"27\",\"phone\":\"000009\",\"nik\":\"0000011\"}', '172.71.124.233', '2026-07-31 03:32:42'),
(94, 2, 'user_created', 'user', 21, NULL, '{\"name\":\"Kecamatan Puncak Sorik Marapi\",\"email\":\"kec-puncaksm@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"28\",\"phone\":\"0000010\",\"nik\":\"000000012\"}', '172.71.124.233', '2026-07-31 03:34:37'),
(95, 2, 'user_created', 'user', 22, NULL, '{\"name\":\"Kecamatan Lembah Sorik Marapi\",\"email\":\"kec-lembahsm@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"28\",\"phone\":\"0000012\",\"nik\":\"00000013\"}', '172.69.166.100', '2026-07-31 03:36:06'),
(96, 2, 'user_created', 'user', 23, NULL, '{\"name\":\"Kecamatan Tambangan\",\"email\":\"kec-tambangan@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"41\",\"phone\":\"0000013\",\"nik\":\"0000014\"}', '104.23.175.32', '2026-07-31 03:40:22'),
(97, 2, 'user_created', 'user', 24, NULL, '{\"name\":\"Kecamatan Kotanopan\",\"email\":\"kec-kotanopan@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"31\",\"phone\":\"0000014\",\"nik\":\"0000015\"}', '172.69.166.101', '2026-07-31 03:42:34'),
(98, 2, 'user_created', 'user', 25, NULL, '{\"name\":\"Kecamatan Muarasipongi\",\"email\":\"kec-muarasipongi@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"38\",\"phone\":\"0000015\",\"nik\":\"0000016\"}', '104.23.175.32', '2026-07-31 03:44:06'),
(99, 2, 'user_created', 'user', 26, NULL, '{\"name\":\"Kecamatan Pakantan\",\"email\":\"kec-pakantan@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"39\",\"phone\":\"0000016\",\"nik\":\"00000017\"}', '104.22.66.144', '2026-07-31 03:45:30'),
(100, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.23.175.33', '2026-07-31 03:46:31'),
(101, 2, 'user_created', 'user', 27, NULL, '{\"name\":\"Kecamatan Ulu Pungkut\",\"email\":\"kec-ulupungkut@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"42\",\"phone\":\"0000017\",\"nik\":\"00000018\"}', '162.158.162.160', '2026-07-31 03:47:05'),
(102, 2, 'user_created', 'user', 28, NULL, '{\"name\":\"Kecamatan Batang Natal\",\"email\":\"kec-batangnatal@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"37\",\"phone\":\"0000018\",\"nik\":\"0000019\"}', '108.162.226.8', '2026-07-31 03:48:30'),
(103, 2, 'user_created', 'user', 29, NULL, '{\"name\":\"Kecamartan Lingga Bayu\",\"email\":\"kec-linggabayu@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"35\",\"phone\":\"0000019\",\"nik\":\"00000020\"}', '162.158.189.253', '2026-07-31 03:50:03'),
(104, 2, 'user_created', 'user', 30, NULL, '{\"name\":\"Kecamatan Ranto Baek\",\"email\":\"kec-rantobaek@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"36\",\"phone\":\"0000020\",\"nik\":\"00000021\"}', '172.69.176.141', '2026-07-31 03:51:56'),
(105, 2, 'user_created', 'user', 31, NULL, '{\"name\":\"Kecamatan Natal\",\"email\":\"kec-natal@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"32\",\"phone\":\"0000021\",\"nik\":\"00000022\"}', '172.69.166.100', '2026-07-31 03:53:44'),
(106, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '162.158.108.156', '2026-07-31 03:54:17'),
(107, 2, 'user_created', 'user', 32, NULL, '{\"name\":\"Kecamatan Sinunukan\",\"email\":\"kec-sinunukan@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"33\",\"phone\":\"0000022\",\"nik\":\"00000023\"}', '104.23.175.32', '2026-07-31 03:58:00'),
(108, 2, 'user_created', 'user', 33, NULL, '{\"name\":\"Kecamatan Muara Batang Gadis\",\"email\":\"kec-mbg@mail.madina.go.id\",\"role\":\"camat\",\"opd_id\":null,\"kecamatan_id\":\"34\",\"phone\":\"0000023\",\"nik\":\"000000024\"}', '162.158.162.161', '2026-07-31 04:02:09'),
(109, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDisposed', 'complaint', 1, '{\"status\":\"diverifikasi\"}', '{\"status\":\"diproses\",\"disposed_to_type\":\"opd\",\"disposed_to_id\":7}', '172.70.92.141', '2026-07-31 04:07:23'),
(110, 2, 'user_created', 'user', 34, NULL, '{\"name\":\"SEKRETARIAT DAERAH KABUPATEN\",\"email\":\"setda@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"4\",\"kecamatan_id\":null,\"phone\":\"000002\",\"nik\":\"00001\"}', '162.158.88.102', '2026-07-31 04:16:35'),
(111, 2, 'user_created', 'user', 35, NULL, '{\"name\":\"INSPEKTORAT DAERAH KABUPATEN\",\"email\":\"inspektorat@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"32\",\"kecamatan_id\":null,\"phone\":\"000003\",\"nik\":\"000002\"}', '104.23.175.33', '2026-07-31 04:22:17'),
(112, 2, 'user_created', 'user', 36, NULL, '{\"name\":\"DINAS PENDIDIKAN DAN KEBUDAYAAN\",\"email\":\"disdik@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"5\",\"kecamatan_id\":null,\"phone\":\"0000011\",\"nik\":\"00004\"}', '172.71.124.233', '2026-07-31 04:27:24'),
(113, 2, 'user_created', 'user', 37, NULL, '{\"name\":\"DINAS KESEHATAN\",\"email\":\"dinkes@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"6\",\"kecamatan_id\":null,\"phone\":\"000021\",\"nik\":\"0000022\"}', '162.158.170.53', '2026-07-31 04:30:36'),
(114, 2, 'user_created', 'user', 38, NULL, '{\"name\":\"DINAS PEKERJAAN UMUM DAN PENATAAN RUANG\",\"email\":\"pupr@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"7\",\"kecamatan_id\":null,\"phone\":\"0000033\",\"nik\":\"000006\"}', '172.69.166.101', '2026-07-31 04:34:15'),
(115, 2, 'user_created', 'user', 39, NULL, '{\"name\":\"DINAS PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN SERTA PERTANAHAN\",\"email\":\"perkimtan@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"8\",\"kecamatan_id\":null,\"phone\":\"0000032\",\"nik\":\"0000012\"}', '172.70.208.156', '2026-07-31 04:36:34'),
(116, 2, 'user_deleted', 'user', 11, '{\"name\":\"Kecamatan Batang Natal\",\"email\":\"camatbatangnatal@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":37,\"phone\":\"00000\",\"nik\":\"000001\"}', '[]', '172.71.124.232', '2026-07-31 04:37:13'),
(117, 2, 'user_created', 'user', 40, NULL, '{\"name\":\"SATUAN POLISI PAMONG PRAJA DAN PEMADAM KEBAKARAN\",\"email\":\"satpol@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"9\",\"kecamatan_id\":null,\"phone\":\"000034\",\"nik\":\"0000050\"}', '104.23.175.33', '2026-07-31 04:38:40'),
(118, 2, 'user_created', 'user', 41, NULL, '{\"name\":\"DINAS SOSIAL, PEMBERDAYAAN PEREMPUAN DAN PERLIDUNGAN ANAK\",\"email\":\"dinsosp3a@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"10\",\"kecamatan_id\":null,\"phone\":\"0000035\",\"nik\":\"000060\"}', '104.22.66.145', '2026-07-31 04:40:12'),
(119, 2, 'user_created', 'user', 42, NULL, '{\"name\":\"DINAS KOPERASI, USAHA KECIL DAN MENENGAH\",\"email\":\"diskopukm@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"11\",\"kecamatan_id\":null,\"phone\":\"0000036\",\"nik\":\"0000061\"}', '172.69.166.101', '2026-07-31 04:41:30'),
(120, 2, 'user_created', 'user', 43, NULL, '{\"name\":\"DINAS TENAGA KERJA\",\"email\":\"disnaker@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"12\",\"kecamatan_id\":null,\"phone\":\"0000038\",\"nik\":\"000062\"}', '104.23.175.33', '2026-07-31 04:43:12'),
(121, 2, 'user_created', 'user', 44, NULL, '{\"name\":\"DINAS PENGENDALIAN PENDUDUK DAN KELUARGA BERENCANA\",\"email\":\"disp2kb@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"13\",\"kecamatan_id\":null,\"phone\":\"0000048\",\"nik\":\"000070\"}', '172.69.166.101', '2026-07-31 04:45:08'),
(122, 2, 'user_created', 'user', 45, NULL, '{\"name\":\"DINAS KETAHANAN PANGAN\",\"email\":\"distapang@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"14\",\"kecamatan_id\":null,\"phone\":\"0000067\",\"nik\":\"0000072\"}', '104.22.66.229', '2026-07-31 04:47:14'),
(123, 2, 'user_created', 'user', 46, NULL, '{\"name\":\"DINAS PERTANIAN\",\"email\":\"distan@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"15\",\"kecamatan_id\":null,\"phone\":\"0000056\",\"nik\":\"000077\"}', '162.158.108.157', '2026-07-31 04:48:41'),
(124, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-07-31 04:50:21'),
(125, 2, 'user_created', 'user', 47, NULL, '{\"name\":\"distan@mail.madina.go.id\",\"email\":\"dlh@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"16\",\"kecamatan_id\":null,\"phone\":\"00000123\",\"nik\":\"0000057\"}', '104.22.66.4', '2026-07-31 04:52:22'),
(126, 2, 'user_created', 'user', 48, NULL, '{\"name\":\"DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL\",\"email\":\"disdukcapil@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"17\",\"kecamatan_id\":null,\"phone\":\"000080\",\"nik\":\"0000099\"}', '162.158.162.160', '2026-07-31 04:54:55'),
(127, 2, 'user_created', 'user', 49, NULL, '{\"name\":\"DINAS PEMBERDAYAAN MASYARAKAT DAN DESA\",\"email\":\"dpmd@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"18\",\"kecamatan_id\":null,\"phone\":\"000097\",\"nik\":\"00078\"}', '172.70.208.157', '2026-07-31 04:56:05'),
(128, 2, 'user_created', 'user', 50, NULL, '{\"name\":\"DINAS PERHUBUNGAN\",\"email\":\"dishub@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"19\",\"kecamatan_id\":null,\"phone\":\"00021\",\"nik\":\"0089\"}', '172.69.176.141', '2026-07-31 04:57:36'),
(129, 2, 'user_created', 'user', 51, NULL, '{\"name\":\"DINAS PERPUSTAKAAN DAN KEARSIPAN\",\"email\":\"disperpus@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"20\",\"kecamatan_id\":null,\"phone\":\"00987\",\"nik\":\"000567\"}', '172.68.164.138', '2026-07-31 04:58:38'),
(130, 2, 'user_created', 'user', 52, NULL, '{\"name\":\"DINAS KOMUNIKASI DAN INFORMATIKA\",\"email\":\"diskominfo@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"3\",\"kecamatan_id\":null,\"phone\":\"00879\",\"nik\":\"000165\"}', '172.68.242.88', '2026-07-31 05:00:03'),
(131, 2, 'user_created', 'user', 53, NULL, '{\"name\":\"DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU\",\"email\":\"dpmptsp@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"21\",\"kecamatan_id\":null,\"phone\":\"000983\",\"nik\":\"00132\"}', '162.158.88.102', '2026-07-31 05:01:34'),
(132, 2, 'user_created', 'user', 54, NULL, '{\"name\":\"DINAS PEMUDA DAN OLAHRAGA\",\"email\":\"dispora@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"22\",\"kecamatan_id\":null,\"phone\":\"000408\",\"nik\":\"00609\"}', '172.69.176.140', '2026-07-31 05:02:52'),
(133, 2, 'user_created', 'user', 55, NULL, '{\"name\":\"DINAS PERIKANAN\",\"email\":\"perikanan@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"23\",\"kecamatan_id\":null,\"phone\":\"000980\",\"nik\":\"00101\"}', '172.68.164.138', '2026-07-31 05:04:09'),
(134, 2, 'user_created', 'user', 56, NULL, '{\"name\":\"DINAS PARIWISATA\",\"email\":\"pariwisata@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"24\",\"kecamatan_id\":null,\"phone\":\"000707\",\"nik\":\"000909\"}', '162.158.88.102', '2026-07-31 05:05:10'),
(135, 2, 'user_created', 'user', 57, NULL, '{\"name\":\"DINAS PERDAGANGAN\",\"email\":\"disdag@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"29\",\"kecamatan_id\":null,\"phone\":\"00606\",\"nik\":\"000505\"}', '104.23.175.32', '2026-07-31 05:06:08'),
(136, 2, 'user_updated', 'user', 57, '{\"name\":\"DINAS PERDAGANGAN\",\"email\":\"disdag@mail.madina.go.id\",\"is_active\":true,\"opd_id\":29,\"kecamatan_id\":null,\"phone\":\"00606\",\"nik\":\"000505\"}', '{\"name\":\"DINAS PERDAGANGAN\",\"email\":\"disdag@mail.madina.go.id\",\"is_active\":true,\"opd_id\":\"25\",\"kecamatan_id\":null,\"phone\":\"00606\",\"nik\":\"000505\"}', '104.23.175.33', '2026-07-31 05:07:31'),
(137, 2, 'user_created', 'user', 58, NULL, '{\"name\":\"BADAN PERENCANAAN PEMBANGUNAN, RISET DAN INOVASI DAERAH\",\"email\":\"bapperida@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"26\",\"kecamatan_id\":null,\"phone\":\"00303\",\"nik\":\"0000202\"}', '162.158.108.156', '2026-07-31 05:08:43'),
(138, 2, 'user_created', 'user', 59, NULL, '{\"name\":\"BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH\",\"email\":\"bpkad@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"27\",\"kecamatan_id\":null,\"phone\":\"00429\",\"nik\":\"000917\"}', '104.23.175.32', '2026-07-31 05:09:51'),
(139, 2, 'user_created', 'user', 60, NULL, '{\"name\":\"BADAN PENDAPATAN DAERAH\",\"email\":\"bapenda@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"28\",\"kecamatan_id\":null,\"phone\":\"00915\",\"nik\":\"000518\"}', '104.22.66.239', '2026-07-31 05:10:59'),
(140, 2, 'user_created', 'user', 61, NULL, '{\"name\":\"BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA\",\"email\":\"bkpsdm@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"29\",\"kecamatan_id\":null,\"phone\":\"00689\",\"nik\":\"000543\"}', '162.158.162.161', '2026-07-31 05:12:01'),
(141, 2, 'user_created', 'user', 62, NULL, '{\"name\":\"BADAN PENANGGULANGAN BENCANA DAERAH\",\"email\":\"bpbd@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"30\",\"kecamatan_id\":null,\"phone\":\"000876\",\"nik\":\"000999\"}', '162.158.88.102', '2026-07-31 05:13:24'),
(142, 2, 'user_created', 'user', 63, NULL, '{\"name\":\"BADAN KESATUAN BANGSA DAN POLITIK\",\"email\":\"kesbang@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"31\",\"kecamatan_id\":null,\"phone\":\"006056\",\"nik\":\"0087564\"}', '172.70.92.140', '2026-07-31 05:16:07'),
(143, 61, 'login', 'user', 61, NULL, '{\"guard\":\"web\"}', '108.162.226.9', '2026-07-31 05:16:41'),
(144, 61, 'logout', 'user', 61, NULL, '{\"guard\":\"web\"}', '108.162.226.8', '2026-07-31 05:17:10'),
(145, 58, 'login', 'user', 58, NULL, '{\"guard\":\"web\"}', '108.162.226.9', '2026-07-31 05:17:23'),
(146, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-07-31 07:31:26'),
(147, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.69.176.141', '2026-08-01 08:22:11'),
(148, 8, 'login_failed', 'user', 8, NULL, '{\"email\":\"admin@gmail.com\"}', '104.22.66.239', '2026-08-03 08:24:17'),
(149, NULL, 'login_failed', 'user', NULL, NULL, '{\"email\":\"admin@ppid.madina.go.id\"}', '104.22.66.239', '2026-08-03 08:24:22'),
(150, NULL, 'login_failed', 'user', NULL, NULL, '{\"email\":\"admin@ppid.madina.go.id\"}', '104.22.66.239', '2026-08-03 08:24:35'),
(151, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.208.156', '2026-08-03 10:50:37'),
(152, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '172.71.124.82', '2026-08-04 01:36:50'),
(153, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.23.175.199', '2026-08-04 01:42:32'),
(154, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '172.71.124.83', '2026-08-04 01:46:30'),
(155, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.23.175.198', '2026-08-04 01:47:44'),
(156, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '172.70.142.149', '2026-08-04 02:00:59'),
(157, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.71.124.232', '2026-08-04 02:14:11'),
(158, 2, 'login_failed', 'user', 2, NULL, '{\"email\":\"kominfo@gmail.com\"}', '162.158.162.161', '2026-08-04 02:16:54'),
(159, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.69.176.140', '2026-08-04 02:17:19'),
(160, 2, 'user_created', 'user', 64, NULL, '{\"name\":\"SEKRETARIAT DPRD\",\"email\":\"setwan@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"35\",\"kecamatan_id\":null,\"phone\":\"00003\",\"nik\":\"00029\"}', '104.22.66.239', '2026-08-04 02:28:26'),
(161, 2, 'user_created', 'user', 65, NULL, '{\"name\":\"BAGIAN HUKUM\",\"email\":\"bagianhukum@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"37\",\"kecamatan_id\":null,\"phone\":\"00000\",\"nik\":null}', '172.68.164.138', '2026-08-04 02:35:30'),
(162, 2, 'user_updated', 'user', 65, '{\"name\":\"BAGIAN HUKUM\",\"email\":\"bagianhukum@mail.madina.go.id\",\"is_active\":true,\"opd_id\":37,\"kecamatan_id\":null,\"phone\":\"00000\",\"nik\":null}', '{\"name\":\"BAGIAN HUKUM\",\"email\":\"bagianhukum@mail.madina.go.id\",\"is_active\":true,\"opd_id\":\"37\",\"kecamatan_id\":null,\"phone\":\"00000\",\"nik\":\"000030\"}', '172.71.124.233', '2026-08-04 02:36:31'),
(163, 2, 'user_created', 'user', 66, NULL, '{\"name\":\"BAGIAN ADMINISTRASI PEMBANGUNAN\",\"email\":\"bagianadministrasipembangunan@mail.go.id\",\"role\":\"opd\",\"opd_id\":\"40\",\"kecamatan_id\":null,\"phone\":\"0000034\",\"nik\":\"000066\"}', '172.71.124.232', '2026-08-04 02:40:21'),
(164, 2, 'user_updated', 'user', 66, '{\"name\":\"BAGIAN ADMINISTRASI PEMBANGUNAN\",\"email\":\"bagianadministrasipembangunan@mail.go.id\",\"is_active\":true,\"opd_id\":40,\"kecamatan_id\":null,\"phone\":\"0000034\",\"nik\":\"000066\"}', '{\"name\":\"BAGIAN ADMINISTRASI PEMBANGUNAN\",\"email\":\"bagianadministrasipembangunan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":\"40\",\"kecamatan_id\":null,\"phone\":\"0000034\",\"nik\":\"000066\"}', '172.68.242.88', '2026-08-04 02:41:14'),
(165, 2, 'user_created', 'user', 67, NULL, '{\"name\":\"BAGIAN KESEJAHTERAAN RAKYAT\",\"email\":\"bagiankesejahteraanrakyat@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"38\",\"kecamatan_id\":null,\"phone\":\"00000045\",\"nik\":\"000089\"}', '162.158.162.160', '2026-08-04 02:43:09'),
(166, 2, 'user_created', 'user', 68, NULL, '{\"name\":\"BAGIAN ORGANISASI\",\"email\":\"bagianorganisasi@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"43\",\"kecamatan_id\":null,\"phone\":\"0000045\",\"nik\":\"00089\"}', '162.158.108.157', '2026-08-04 02:44:45'),
(167, 2, 'user_created', 'user', 69, NULL, '{\"name\":\"BAGIAN PENGADAAN BARANG DAN JASA\",\"email\":\"bagianpengadaanbarangdanjasa@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"41\",\"kecamatan_id\":null,\"phone\":\"0000055\",\"nik\":\"000078\"}', '172.71.124.232', '2026-08-04 02:46:20'),
(168, 2, 'user_created', 'user', 70, NULL, '{\"name\":\"BAGIAN PEREKONOMIAN DAN SUNMBER DAYA ALAM\",\"email\":\"bagianperekonomiandansumberdayaalam@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"39\",\"kecamatan_id\":null,\"phone\":\"00000044\",\"nik\":\"000099\"}', '162.158.88.102', '2026-08-04 02:47:59'),
(169, 1, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintSubmitted', 'complaint', 3, NULL, '{\"ticket_number\":\"PGD-2026-000003\",\"status\":\"diajukan\"}', '104.22.66.239', '2026-08-04 02:48:39'),
(170, 2, 'user_created', 'user', 71, NULL, '{\"name\":\"BAGIAN TATA PEMERINTAHAN\",\"email\":\"bagiantatapemerintahan@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"36\",\"kecamatan_id\":null,\"phone\":\"0000077\",\"nik\":\"0000077\"}', '172.71.124.232', '2026-08-04 02:50:17'),
(171, 2, 'user_created', 'user', 72, NULL, '{\"name\":\"BAGIAN UMUM\",\"email\":\"bagianumum@mail.madina.go.id\",\"role\":\"opd\",\"opd_id\":\"42\",\"kecamatan_id\":null,\"phone\":\"000044\",\"nik\":\"00088\"}', '172.71.124.232', '2026-08-04 02:51:48'),
(172, 2, 'user_deleted', 'user', 47, '{\"name\":\"distan@mail.madina.go.id\",\"email\":\"dlh@mail.madina.go.id\",\"is_active\":true,\"opd_id\":16,\"kecamatan_id\":null,\"phone\":\"00000123\",\"nik\":\"0000057\"}', '[]', '172.70.142.149', '2026-08-04 02:56:48'),
(173, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 02:58:49'),
(174, 2, 'user_created', 'user', 73, NULL, '{\"name\":\"RUMAH SAKIT UMUM DAERAH PANYABUNGAN\",\"email\":\"rsudpanyabungan@mail.com\",\"role\":\"opd\",\"opd_id\":\"33\",\"kecamatan_id\":null,\"phone\":\"000\",\"nik\":\"000\"}', '172.70.142.149', '2026-08-04 03:06:40'),
(175, 2, 'user_created', 'user', 74, NULL, '{\"name\":\"RUMAH SAKIT UMUM DAERAH HUSNI THAMRIN NATAL\",\"email\":\"rshusnithamrin@mail.com\",\"role\":\"opd\",\"opd_id\":\"34\",\"kecamatan_id\":null,\"phone\":\"00\",\"nik\":\"0\"}', '172.68.164.139', '2026-08-04 03:08:39'),
(176, 2, 'user_updated', 'user', 74, '{\"name\":\"RUMAH SAKIT UMUM DAERAH HUSNI THAMRIN NATAL\",\"email\":\"rshusnithamrin@mail.com\",\"is_active\":true,\"opd_id\":34,\"kecamatan_id\":null,\"phone\":\"00\",\"nik\":\"0\"}', '{\"name\":\"RUMAH SAKIT UMUM DAERAH HUSNI THAMRIN NATAL\",\"email\":\"rshusnithamrin@mail.madina.go.id\",\"is_active\":true,\"opd_id\":\"34\",\"kecamatan_id\":null,\"phone\":\"00\",\"nik\":\"0\"}', '162.158.108.157', '2026-08-04 03:11:49'),
(177, 2, 'user_updated', 'user', 73, '{\"name\":\"RUMAH SAKIT UMUM DAERAH PANYABUNGAN\",\"email\":\"rsudpanyabungan@mail.com\",\"is_active\":true,\"opd_id\":33,\"kecamatan_id\":null,\"phone\":\"000\",\"nik\":\"000\"}', '{\"name\":\"RUMAH SAKIT UMUM DAERAH PANYABUNGAN\",\"email\":\"rsudpanyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":\"33\",\"kecamatan_id\":null,\"phone\":\"000\",\"nik\":\"000\"}', '162.158.108.157', '2026-08-04 03:12:12'),
(178, 2, 'user_updated', 'user', 73, '{\"name\":\"RUMAH SAKIT UMUM DAERAH PANYABUNGAN\",\"email\":\"rsudpanyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":33,\"kecamatan_id\":null,\"phone\":\"000\",\"nik\":\"000\"}', '{\"name\":\"RUMAH SAKIT UMUM DAERAH PANYABUNGAN\",\"email\":\"rsudpanyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":\"33\",\"kecamatan_id\":null,\"phone\":\"000\",\"nik\":\"000\"}', '162.158.108.157', '2026-08-04 03:12:13'),
(179, 2, 'user_updated', 'user', 73, '{\"name\":\"RUMAH SAKIT UMUM DAERAH PANYABUNGAN\",\"email\":\"rsudpanyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":33,\"kecamatan_id\":null,\"phone\":\"000\",\"nik\":\"000\"}', '{\"name\":\"RUMAH SAKIT UMUM DAERAH PANYABUNGAN\",\"email\":\"rsudpanyabungan@mail.madina.go.id\",\"is_active\":true,\"opd_id\":\"33\",\"kecamatan_id\":null,\"phone\":\"000\",\"nik\":\"000\"}', '162.158.108.157', '2026-08-04 03:12:14'),
(180, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '162.158.189.253', '2026-08-04 03:33:25'),
(181, 2, 'user_updated', 'user', 70, '{\"name\":\"BAGIAN PEREKONOMIAN DAN SUNMBER DAYA ALAM\",\"email\":\"bagianperekonomiandansumberdayaalam@mail.madina.go.id\",\"is_active\":true,\"opd_id\":39,\"kecamatan_id\":null,\"phone\":\"00000044\",\"nik\":\"000099\"}', '{\"name\":\"BAGIAN PEREKONOMIAN DAN SUNMBER DAYA ALAM\",\"email\":\"bagianperekonomiandansumberdayaalam@mail.madina.go.id\",\"is_active\":true,\"opd_id\":\"39\",\"kecamatan_id\":null,\"phone\":\"00000044\",\"nik\":\"000099\"}', '162.158.108.156', '2026-08-04 04:05:15'),
(182, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.71.124.233', '2026-08-04 05:02:20'),
(183, NULL, 'login_failed', 'user', NULL, NULL, '{\"email\":\"fatimahzahro@gmail.com\"}', '104.22.66.239', '2026-08-04 05:25:29'),
(184, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.68.242.89', '2026-08-04 05:25:43'),
(185, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.23.175.219', '2026-08-04 05:53:27'),
(186, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 06:40:41'),
(187, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 06:41:06'),
(188, 4, 'logout', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.70.189.32', '2026-08-04 06:56:10'),
(189, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.70.208.157', '2026-08-04 07:01:49'),
(190, 4, 'logout', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.70.208.157', '2026-08-04 07:16:51'),
(191, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.208.157', '2026-08-04 07:17:14'),
(192, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.142.149', '2026-08-04 07:20:05'),
(193, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.70.142.148', '2026-08-04 07:20:34'),
(194, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.70.142.148', '2026-08-04 07:21:00'),
(195, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.142.149', '2026-08-04 07:21:21'),
(196, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintVerified', 'complaint', 3, '{\"status\":\"diajukan\"}', '{\"status\":\"diverifikasi\",\"is_valid\":true,\"rejection_reason\":null}', '172.70.142.149', '2026-08-04 07:23:07'),
(197, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDisposed', 'complaint', 3, '{\"status\":\"diverifikasi\"}', '{\"status\":\"diproses\",\"disposed_to_type\":\"opd\",\"disposed_to_id\":16}', '172.70.142.148', '2026-08-04 07:23:37'),
(198, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.142.148', '2026-08-04 07:24:03'),
(199, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.70.142.148', '2026-08-04 07:24:21'),
(200, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '162.158.88.103', '2026-08-04 07:29:18'),
(201, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '162.158.88.102', '2026-08-04 07:29:32'),
(202, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 07:31:44'),
(203, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 07:32:06'),
(204, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 07:37:29'),
(205, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 07:37:44'),
(206, 4, 'logout', 'user', 4, NULL, '{\"guard\":\"web\"}', '162.158.88.102', '2026-08-04 07:39:14'),
(207, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 07:39:41'),
(208, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '162.158.88.103', '2026-08-04 07:42:38'),
(209, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.69.176.141', '2026-08-04 07:43:00'),
(210, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '162.158.162.160', '2026-08-04 12:40:58'),
(211, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '162.158.162.160', '2026-08-04 12:41:42'),
(212, 5, 'login', 'user', 5, NULL, '{\"guard\":\"web\"}', '162.158.88.103', '2026-08-04 12:42:53'),
(213, 5, 'logout', 'user', 5, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 12:44:09'),
(214, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 12:44:38'),
(215, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.68.164.139', '2026-08-04 12:56:08'),
(216, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.69.176.141', '2026-08-04 13:01:11'),
(217, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '162.158.189.252', '2026-08-04 13:02:34'),
(218, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '162.158.88.103', '2026-08-04 13:28:38'),
(219, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.71.124.233', '2026-08-04 13:35:31'),
(220, 4, 'logout', 'user', 4, NULL, '{\"guard\":\"web\"}', '104.23.175.217', '2026-08-04 13:43:55'),
(221, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '104.23.175.216', '2026-08-04 13:44:55'),
(222, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.70.208.157', '2026-08-04 13:48:59'),
(223, 1, 'login_failed', 'user', 1, NULL, '{\"email\":\"masyarakat@gmail.com\"}', '104.22.66.239', '2026-08-04 13:50:48'),
(224, 1, 'login_failed', 'user', 1, NULL, '{\"email\":\"masyarakat@gmail.com\"}', '104.22.66.239', '2026-08-04 13:50:53'),
(225, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 13:51:20'),
(226, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 13:51:22'),
(227, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 13:51:40'),
(228, 1, 'login', 'user', 1, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-04 13:53:02'),
(229, 1, 'logout', 'user', 1, NULL, '{\"guard\":\"web\"}', '172.70.208.157', '2026-08-04 13:57:38'),
(230, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.68.242.88', '2026-08-04 13:57:58'),
(231, 4, 'logout', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.70.208.157', '2026-08-04 13:58:56'),
(232, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.70.208.156', '2026-08-04 13:59:07'),
(233, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.70.208.156', '2026-08-04 14:03:11'),
(234, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.70.142.148', '2026-08-04 14:03:37'),
(235, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '162.158.162.160', '2026-08-05 01:35:39'),
(236, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.23.175.33', '2026-08-05 01:51:44'),
(237, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDispositionCancelled', 'complaint', 3, '{\"status\":\"diproses\"}', '{\"status\":\"diverifikasi\",\"cancelled_target_type\":\"opd\",\"cancelled_target_id\":16}', '162.158.189.252', '2026-08-05 01:52:54'),
(238, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.68.242.88', '2026-08-05 01:57:52'),
(239, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.23.175.33', '2026-08-05 02:07:55'),
(240, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDispositionCancelled', 'complaint', 1, '{\"status\":\"diproses\"}', '{\"status\":\"diverifikasi\",\"cancelled_target_type\":\"opd\",\"cancelled_target_id\":7}', '172.71.124.233', '2026-08-05 02:08:36'),
(241, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDisposed', 'complaint', 3, '{\"status\":\"diverifikasi\"}', '{\"status\":\"diproses\",\"disposed_to_type\":\"opd\",\"disposed_to_id\":44}', '172.69.176.141', '2026-08-05 02:11:22'),
(242, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.142.148', '2026-08-05 02:15:25'),
(243, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.70.142.149', '2026-08-05 02:15:54'),
(244, 2, 'user_updated', 'user', 3, '{\"name\":\"Opd Demo\",\"email\":\"opd@gmail.com\",\"is_active\":true,\"opd_id\":1,\"kecamatan_id\":null,\"phone\":null,\"nik\":null}', '{\"name\":\"Opd Demo\",\"email\":\"opd@gmail.com\",\"is_active\":true,\"opd_id\":\"44\",\"kecamatan_id\":null,\"phone\":null,\"nik\":null}', '104.23.175.32', '2026-08-05 02:17:03'),
(245, 2, 'user_updated', 'user', 4, '{\"name\":\"Camat Demo\",\"email\":\"camat@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":1,\"phone\":null,\"nik\":null}', '{\"name\":\"Camat Demo\",\"email\":\"camat@gmail.com\",\"is_active\":true,\"opd_id\":null,\"kecamatan_id\":\"43\",\"phone\":null,\"nik\":null}', '104.23.175.33', '2026-08-05 02:17:38'),
(246, 3, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintHandled', 'complaint', 3, '{\"status\":\"diproses\"}', '{\"status\":\"ditindaklanjuti\"}', '104.22.66.239', '2026-08-05 02:48:40'),
(247, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-05 02:58:23'),
(248, 2, 'login_failed', 'user', 2, NULL, '{\"email\":\"kominfo@gmail.com\"}', '104.22.66.239', '2026-08-05 02:58:45'),
(249, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '104.22.66.239', '2026-08-05 02:59:06'),
(250, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.92.140', '2026-08-05 03:12:37'),
(251, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.70.92.140', '2026-08-05 03:12:58'),
(252, 4, 'logout', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.70.92.140', '2026-08-05 03:13:24'),
(253, 2, 'login', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.92.140', '2026-08-05 03:13:39'),
(254, 2, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDisposed', 'complaint', 1, '{\"status\":\"diverifikasi\"}', '{\"status\":\"diproses\",\"disposed_to_type\":\"camat\",\"disposed_to_id\":43}', '172.70.92.141', '2026-08-05 03:14:17'),
(255, 2, 'logout', 'user', 2, NULL, '{\"guard\":\"web\"}', '172.70.92.141', '2026-08-05 03:14:48'),
(256, 4, 'login', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.70.92.141', '2026-08-05 03:15:03'),
(257, 4, 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintHandled', 'complaint', 1, '{\"status\":\"diproses\"}', '{\"status\":\"ditindaklanjuti\"}', '162.158.108.156', '2026-08-05 03:36:11'),
(258, 4, 'logout', 'user', 4, NULL, '{\"guard\":\"web\"}', '172.70.142.148', '2026-08-05 03:38:50'),
(259, 3, 'login', 'user', 3, NULL, '{\"guard\":\"web\"}', '172.69.176.141', '2026-08-06 07:00:58'),
(260, 3, 'logout', 'user', 3, NULL, '{\"guard\":\"web\"}', '104.23.175.32', '2026-08-06 07:03:42');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('sippm-madina-cache-011233c5865cb49a73be8317a95e7c92', 'i:1;', 1785896213),
('sippm-madina-cache-011233c5865cb49a73be8317a95e7c92:timer', 'i:1785896213;', 1785896213),
('sippm-madina-cache-0b4003041fad1af4b4edf945c0f8fb68', 'i:1;', 1785421073),
('sippm-madina-cache-0b4003041fad1af4b4edf945c0f8fb68:timer', 'i:1785421073;', 1785421073),
('sippm-madina-cache-15685ee34dde731ac69772d16b5c3420', 'i:1;', 1785464798),
('sippm-madina-cache-15685ee34dde731ac69772d16b5c3420:timer', 'i:1785464798;', 1785464798),
('sippm-madina-cache-167eebcbcd7c4d10b1c355285d8d3a9b', 'i:1;', 1785468472),
('sippm-madina-cache-167eebcbcd7c4d10b1c355285d8d3a9b:timer', 'i:1785468472;', 1785468472),
('sippm-madina-cache-17ee2e6418b44b9d61608efb3011ad81', 'i:1;', 1785408843),
('sippm-madina-cache-17ee2e6418b44b9d61608efb3011ad81:timer', 'i:1785408843;', 1785408843),
('sippm-madina-cache-19b277234c5b887d97a4585b6d4a0ffe', 'i:1;', 1785899679),
('sippm-madina-cache-19b277234c5b887d97a4585b6d4a0ffe:timer', 'i:1785899679;', 1785899679),
('sippm-madina-cache-1e9056edb4abebebc7785dae82a33429', 'i:1;', 1785899762),
('sippm-madina-cache-1e9056edb4abebebc7785dae82a33429:timer', 'i:1785899762;', 1785899762),
('sippm-madina-cache-2265869334a8aa68cfca13f4469bf6e2', 'i:1;', 1785809873),
('sippm-madina-cache-2265869334a8aa68cfca13f4469bf6e2:timer', 'i:1785809873;', 1785809873),
('sippm-madina-cache-2913155be5adc1c59218fa531e483ded', 'i:1;', 1785388172),
('sippm-madina-cache-2913155be5adc1c59218fa531e483ded:timer', 'i:1785388172;', 1785388172),
('sippm-madina-cache-2d2cf6fda4d7ea5f4ce32076f1d5868e', 'i:1;', 1785808049),
('sippm-madina-cache-2d2cf6fda4d7ea5f4ce32076f1d5868e:timer', 'i:1785808049;', 1785808049),
('sippm-madina-cache-2db5440b39e53bd2c46df1e816c211c9', 'i:1;', 1785388065),
('sippm-madina-cache-2db5440b39e53bd2c46df1e816c211c9:timer', 'i:1785388065;', 1785388065),
('sippm-madina-cache-343e6d37020d419455bfe8a39bee74f6', 'i:1;', 1785809711),
('sippm-madina-cache-343e6d37020d419455bfe8a39bee74f6:timer', 'i:1785809711;', 1785809711),
('sippm-madina-cache-3828bc38023db9c4746f3f82df39d394', 'i:1;', 1785468323),
('sippm-madina-cache-3828bc38023db9c4746f3f82df39d394:timer', 'i:1785468323;', 1785468323),
('sippm-madina-cache-412cbcd1f478c5807c6470dc8f753cff', 'i:1;', 1785851154),
('sippm-madina-cache-412cbcd1f478c5807c6470dc8f753cff:timer', 'i:1785851154;', 1785851154),
('sippm-madina-cache-4359fa4c99688ed2f9065ef71e1d67a3', 'i:1;', 1785475103),
('sippm-madina-cache-4359fa4c99688ed2f9065ef71e1d67a3:timer', 'i:1785475103;', 1785475103),
('sippm-madina-cache-4a6e5c9bd2ef467cd137a523f5b3d2f2', 'i:1;', 1785807469),
('sippm-madina-cache-4a6e5c9bd2ef467cd137a523f5b3d2f2:timer', 'i:1785807469;', 1785807469),
('sippm-madina-cache-4ae6dd84da18b6a28a43e3f8c080f2b6', 'i:1;', 1785388405),
('sippm-madina-cache-4ae6dd84da18b6a28a43e3f8c080f2b6:timer', 'i:1785388405;', 1785388405),
('sippm-madina-cache-4afaa1a17445352a6198ed4b2fbe3053', 'i:1;', 1785847432),
('sippm-madina-cache-4afaa1a17445352a6198ed4b2fbe3053:timer', 'i:1785847432;', 1785847432),
('sippm-madina-cache-4d8a6a58b4e3c8f2a3d60dbb49ddb8b7', 'i:2;', 1785745522),
('sippm-madina-cache-4d8a6a58b4e3c8f2a3d60dbb49ddb8b7:timer', 'i:1785745522;', 1785745522),
('sippm-madina-cache-4e3859a5919ac37492b03609a3a35422', 'i:1;', 1785446414),
('sippm-madina-cache-4e3859a5919ac37492b03609a3a35422:timer', 'i:1785446414;', 1785446414),
('sippm-madina-cache-52db490018884a178788e50bad610ae8', 'i:1;', 1785464664),
('sippm-madina-cache-52db490018884a178788e50bad610ae8:timer', 'i:1785464664;', 1785464664),
('sippm-madina-cache-569fc28fd71c9432a3da4811ec32c19f', 'i:1;', 1785852276),
('sippm-madina-cache-569fc28fd71c9432a3da4811ec32c19f:timer', 'i:1785852276;', 1785852276),
('sippm-madina-cache-5717d48b442a3cecb5682962b5f48b4a', 'i:1;', 1785822867),
('sippm-madina-cache-5717d48b442a3cecb5682962b5f48b4a:timer', 'i:1785822867;', 1785822867),
('sippm-madina-cache-5bf805d62219b478be628611f15cd187', 'i:1;', 1785850590),
('sippm-madina-cache-5bf805d62219b478be628611f15cd187:timer', 'i:1785850590;', 1785850590),
('sippm-madina-cache-5f7192266503e71ed701646738519f0c', 'i:1;', 1785417126),
('sippm-madina-cache-5f7192266503e71ed701646738519f0c:timer', 'i:1785417126;', 1785417126),
('sippm-madina-cache-66ac589d5aa332b1c0ce9a74742e9f2d', 'i:1;', 1785745516),
('sippm-madina-cache-66ac589d5aa332b1c0ce9a74742e9f2d:timer', 'i:1785745516;', 1785745516),
('sippm-madina-cache-680aa83e0065b33036407609f0eed6b3', 'i:1;', 1785999717),
('sippm-madina-cache-680aa83e0065b33036407609f0eed6b3:timer', 'i:1785999717;', 1785999717),
('sippm-madina-cache-6d5fb8f614f686b89934c5673ed49164', 'i:2;', 1785898784),
('sippm-madina-cache-6d5fb8f614f686b89934c5673ed49164:timer', 'i:1785898784;', 1785898784),
('sippm-madina-cache-7176a9b24dfde4c1ac021e59abc77baa', 'i:1;', 1785466077),
('sippm-madina-cache-7176a9b24dfde4c1ac021e59abc77baa:timer', 'i:1785466077;', 1785466077),
('sippm-madina-cache-73ffb65126068c3e5a1eedb854055749', 'i:1;', 1785848613),
('sippm-madina-cache-73ffb65126068c3e5a1eedb854055749:timer', 'i:1785848613;', 1785848613),
('sippm-madina-cache-76dc492161d4234e4a6b8fe99660e80c', 'i:1;', 1785821188),
('sippm-madina-cache-76dc492161d4234e4a6b8fe99660e80c:timer', 'i:1785821188;', 1785821188),
('sippm-madina-cache-78c84b44e67e049e14286c137c74b487', 'i:1;', 1785851641),
('sippm-madina-cache-78c84b44e67e049e14286c137c74b487:timer', 'i:1785851641;', 1785851641),
('sippm-madina-cache-860c7b8197ba79a276f767bf8db22f5d', 'i:1;', 1785827894),
('sippm-madina-cache-860c7b8197ba79a276f767bf8db22f5d:timer', 'i:1785827894;', 1785827894),
('sippm-madina-cache-88f3aad145f7b0665e7732ff1762a185', 'i:1;', 1785422214),
('sippm-madina-cache-88f3aad145f7b0665e7732ff1762a185:timer', 'i:1785422214;', 1785422214),
('sippm-madina-cache-929d6caf354d58af745179a7d53b3568', 'i:1;', 1785572591),
('sippm-madina-cache-929d6caf354d58af745179a7d53b3568:timer', 'i:1785572591;', 1785572591),
('sippm-madina-cache-95a2a2c64df674b0368975c082f56573', 'i:1;', 1785754296),
('sippm-madina-cache-95a2a2c64df674b0368975c082f56573:timer', 'i:1785754296;', 1785754296),
('sippm-madina-cache-95f14c38e0122c855a849469f0b7290d', 'i:1;', 1785475060),
('sippm-madina-cache-95f14c38e0122c855a849469f0b7290d:timer', 'i:1785475060;', 1785475060),
('sippm-madina-cache-98c1ca615adb86b127a127736200a28d', 'i:1;', 1785829123),
('sippm-madina-cache-98c1ca615adb86b127a127736200a28d:timer', 'i:1785829123;', 1785829123),
('sippm-madina-cache-9e4b1ae4e705447f92ef999f9c6a8757', 'i:1;', 1785462677),
('sippm-madina-cache-9e4b1ae4e705447f92ef999f9c6a8757:timer', 'i:1785462677;', 1785462677),
('sippm-madina-cache-9edceb663e1a1c81eb10d19e8b1b9e3f', 'i:1;', 1785466815),
('sippm-madina-cache-9edceb663e1a1c81eb10d19e8b1b9e3f:timer', 'i:1785466815;', 1785466815),
('sippm-madina-cache-a1735746d2a0be4d75ca4d91a36506a1', 'i:1;', 1785847318),
('sippm-madina-cache-a1735746d2a0be4d75ca4d91a36506a1:timer', 'i:1785847318;', 1785847318),
('sippm-madina-cache-a2b00bc20d7d4a2ccb388cd07c2ba5d6', 'i:1;', 1785465129),
('sippm-madina-cache-a2b00bc20d7d4a2ccb388cd07c2ba5d6:timer', 'i:1785465129;', 1785465129),
('sippm-madina-cache-a3d1b79cb0817e962a88cb0a465ca265', 'i:1;', 1785386980),
('sippm-madina-cache-a3d1b79cb0817e962a88cb0a465ca265:timer', 'i:1785386980;', 1785386980),
('sippm-madina-cache-a48027fc6a07337bab78a1317468291e', 'i:1;', 1785388111),
('sippm-madina-cache-a48027fc6a07337bab78a1317468291e:timer', 'i:1785388111;', 1785388111),
('sippm-madina-cache-ae26a4f5bf44a2250f8f37237ebee691', 'i:1;', 1785446360),
('sippm-madina-cache-ae26a4f5bf44a2250f8f37237ebee691:timer', 'i:1785446360;', 1785446360),
('sippm-madina-cache-b09d8149b5c0feb43cba5077578897cf', 'i:1;', 1785394123),
('sippm-madina-cache-b09d8149b5c0feb43cba5077578897cf:timer', 'i:1785394123;', 1785394123),
('sippm-madina-cache-b5abf86ed7658592014a099f15d42970', 'i:1;', 1785828632),
('sippm-madina-cache-b5abf86ed7658592014a099f15d42970:timer', 'i:1785828632;', 1785828632),
('sippm-madina-cache-b84b067047477edd23515d083dad05c2', 'i:2;', 1785387778),
('sippm-madina-cache-b84b067047477edd23515d083dad05c2:timer', 'i:1785387778;', 1785387778),
('sippm-madina-cache-b8cc3d25400973c00c3c9eec5b9b265b', 'i:1;', 1785821202),
('sippm-madina-cache-b8cc3d25400973c00c3c9eec5b9b265b:timer', 'i:1785821202;', 1785821202),
('sippm-madina-cache-bab0994912a2975a0a18017eb0d40369', 'i:1;', 1785828785),
('sippm-madina-cache-bab0994912a2975a0a18017eb0d40369:timer', 'i:1785828785;', 1785828785),
('sippm-madina-cache-bab42ab59ddfe21f1c32320813becdaf', 'i:1;', 1785446492),
('sippm-madina-cache-bab42ab59ddfe21f1c32320813becdaf:timer', 'i:1785446492;', 1785446492),
('sippm-madina-cache-c34fe629c2bf0dcf4631fcc722f0e6ad', 'i:1;', 1785464655),
('sippm-madina-cache-c34fe629c2bf0dcf4631fcc722f0e6ad:timer', 'i:1785464655;', 1785464655),
('sippm-madina-cache-c3e6ba4601644f89cbeeebe6dcf281d6', 'i:1;', 1785828321),
('sippm-madina-cache-c3e6ba4601644f89cbeeebe6dcf281d6:timer', 'i:1785828321;', 1785828321),
('sippm-madina-cache-c58dce8bc0b080c9986ee8aac16636f4', 'i:1;', 1785465046),
('sippm-madina-cache-c58dce8bc0b080c9986ee8aac16636f4:timer', 'i:1785465046;', 1785465046),
('sippm-madina-cache-c7b9127f7719432c002df3e4749ef728', 'i:1;', 1785470116),
('sippm-madina-cache-c7b9127f7719432c002df3e4749ef728:timer', 'i:1785470116;', 1785470116),
('sippm-madina-cache-cec3c3880ec02087b7ebf37047e67688', 'i:1;', 1785419917),
('sippm-madina-cache-cec3c3880ec02087b7ebf37047e67688:timer', 'i:1785419917;', 1785419917),
('sippm-madina-cache-d03fe5665e3a6193640dc55edc1f391b', 'i:1;', 1785809899),
('sippm-madina-cache-d03fe5665e3a6193640dc55edc1f391b:timer', 'i:1785809899;', 1785809899),
('sippm-madina-cache-d16488a79159acf451548ddf4bb94995', 'i:1;', 1785417060),
('sippm-madina-cache-d16488a79159acf451548ddf4bb94995:timer', 'i:1785417060;', 1785417060),
('sippm-madina-cache-d4f6e42e5502d87f82aad5d6ec0afec1', 'i:1;', 1785852007),
('sippm-madina-cache-d4f6e42e5502d87f82aad5d6ec0afec1:timer', 'i:1785852007;', 1785852007),
('sippm-madina-cache-d8dfbde7cf57d1a3ab0bc39c383ca8a2', 'i:1;', 1785468198),
('sippm-madina-cache-d8dfbde7cf57d1a3ab0bc39c383ca8a2:timer', 'i:1785468198;', 1785468198),
('sippm-madina-cache-dashboard.kinerja', 'a:5:{s:12:\"targetLabels\";a:2:{i:0;s:5:\"Camat\";i:1;s:3:\"Opd\";}s:12:\"targetTotals\";a:2:{i:0;i:1;i:1;i:2;}s:14:\"resolutionRate\";d:33.33;s:15:\"totalComplaints\";i:3;s:18:\"resolvedComplaints\";i:1;}', 1785828171),
('sippm-madina-cache-dashboard.statistik', 'a:3:{s:18:\"complaintsByStatus\";a:6:{s:8:\"diajukan\";i:0;s:12:\"diverifikasi\";i:0;s:7:\"ditolak\";i:0;s:8:\"diproses\";i:2;s:15:\"ditindaklanjuti\";i:0;s:7:\"selesai\";i:1;}s:20:\"complaintsByCategory\";a:3:{s:13:\"Infrastruktur\";i:1;s:23:\"Kebersihan & Lingkungan\";i:1;s:16:\"Pelayanan Publik\";i:1;}s:18:\"activitiesByStatus\";a:4:{s:5:\"draft\";i:4;s:12:\"diverifikasi\";i:0;s:14:\"dipublikasikan\";i:0;s:7:\"ditolak\";i:0;}}', 1785851202),
('sippm-madina-cache-dd57a5df14bfaa4a86777efb27ce8fea', 'i:1;', 1785848227),
('sippm-madina-cache-dd57a5df14bfaa4a86777efb27ce8fea:timer', 'i:1785848227;', 1785848227),
('sippm-madina-cache-df3234fd95af82c13f6288d325739345', 'i:1;', 1785392869),
('sippm-madina-cache-df3234fd95af82c13f6288d325739345:timer', 'i:1785392869;', 1785392869),
('sippm-madina-cache-e08884d79e26d659042916698fa4038c', 'i:1;', 1785893799),
('sippm-madina-cache-e08884d79e26d659042916698fa4038c:timer', 'i:1785893799;', 1785893799),
('sippm-madina-cache-e381cc715ef183cb8fbde8fb005df8cd', 'i:1;', 1785828140),
('sippm-madina-cache-e381cc715ef183cb8fbde8fb005df8cd:timer', 'i:1785828140;', 1785828140),
('sippm-madina-cache-e8f14d7370d706dbd1bef8e0e3a6d0e4', 'i:1;', 1785826968),
('sippm-madina-cache-e8f14d7370d706dbd1bef8e0e3a6d0e4:timer', 'i:1785826968;', 1785826968),
('sippm-madina-cache-ed228ce61246aedf1a3e71606ee09119', 'i:1;', 1785829440),
('sippm-madina-cache-ed228ce61246aedf1a3e71606ee09119:timer', 'i:1785829440;', 1785829440),
('sippm-madina-cache-ef8ebc9fdddc73bf6473af12fbd63fc8', 'i:1;', 1785468111),
('sippm-madina-cache-ef8ebc9fdddc73bf6473af12fbd63fc8:timer', 'i:1785468111;', 1785468111),
('sippm-madina-cache-f13ba4654013eb2ece6e2b5afb0c8806', 'i:1;', 1785808919),
('sippm-madina-cache-f13ba4654013eb2ece6e2b5afb0c8806:timer', 'i:1785808919;', 1785808919),
('sippm-madina-cache-f1ebb3f543bfb9e405617132afe00a2c', 'i:1;', 1785851937),
('sippm-madina-cache-f1ebb3f543bfb9e405617132afe00a2c:timer', 'i:1785851937;', 1785851937),
('sippm-madina-cache-f3a27ca68c16df5bad928c7623270632', 'i:1;', 1785895735),
('sippm-madina-cache-f3a27ca68c16df5bad928c7623270632:timer', 'i:1785895735;', 1785895735),
('sippm-madina-cache-f82b0372d6d8956c5df9139a75339605', 'i:1;', 1785899637),
('sippm-madina-cache-f82b0372d6d8956c5df9139a75339605:timer', 'i:1785899637;', 1785899637),
('sippm-madina-cache-fbc33727e9d477f7f3332ef74100fe1f', 'i:1;', 1785420647),
('sippm-madina-cache-fbc33727e9d477f7f3332ef74100fe1f:timer', 'i:1785420647;', 1785420647),
('sippm-madina-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:14:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:16:\"registrasi_login\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:7:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;i:7;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:14:\"buat_pengaduan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:20:\"verifikasi_pengaduan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:19:\"disposisi_pengaduan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:19:\"menangani_pengaduan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:3;i:1;i:4;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:22:\"kirim_hasil_penanganan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:3;i:1;i:4;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:19:\"menjawab_masyarakat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:14:\"input_kegiatan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:3;i:1;i:4;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:29:\"verifikasi_publikasi_kegiatan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:17:\"melihat_statistik\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:2;i:1;i:3;i:2;i:4;i:3;i:5;i:4;i:6;i:5;i:7;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:18:\"monitoring_kinerja\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:2;i:1;i:5;i:2;i:6;i:3;i:7;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:22:\"lihat_laporan_kegiatan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:6:{i:0;i:2;i:1;i:3;i:2;i:4;i:3;i:5;i:4;i:6;i:5;i:7;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"kelola_pengguna\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:15:\"lihat_audit_log\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}}s:5:\"roles\";a:7:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:10:\"masyarakat\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"kominfo\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:3:\"opd\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:5:\"camat\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:6:\"bupati\";s:1:\"c\";s:3:\"web\";}i:5;a:3:{s:1:\"a\";i:6;s:1:\"b\";s:12:\"wakil_bupati\";s:1:\"c\";s:3:\"web\";}i:6;a:3:{s:1:\"a\";i:7;s:1:\"b\";s:5:\"sekda\";s:1:\"c\";s:3:\"web\";}}}', 1785982658);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `target_type` varchar(255) NOT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'diajukan',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `ticket_number`, `user_id`, `title`, `description`, `category`, `target_type`, `target_id`, `status`, `latitude`, `longitude`, `rejection_reason`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'PGD-2026-000001', 1, 'Jalan Rusak di Depan Kantor Desa', 'Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.', 'Infrastruktur', 'opd', 1, 'ditindaklanjuti', '0.5333000', '99.4167000', NULL, NULL, '2026-07-21 07:06:05', '2026-08-05 03:36:11'),
(2, 'PGD-2026-000002', 1, 'Lampu Jalan Mati di Simpang Tiga', 'Lampu penerangan jalan umum mati sejak seminggu lalu sehingga kawasan gelap dan rawan.', 'Pelayanan Publik', 'camat', 1, 'selesai', '0.5401000', '99.4210000', NULL, NULL, '2026-07-21 07:06:13', '2026-07-30 05:11:49'),
(3, 'PGD-2026-000003', 1, 'Sampah Menumpuk Di Depan Sekolah', '<p>Sampah menumpuk di depan sekolah Madrasah syariful Majelis hingga ke jalan raya dan bau tidak enak menyeruak ke sekolah maupun di jalan raya.</p>', 'Kebersihan & Lingkungan', 'opd', 40, 'ditindaklanjuti', '0.6722828', '99.7039392', NULL, NULL, '2026-08-04 02:48:39', '2026-08-05 02:48:40');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_attachments`
--

CREATE TABLE `complaint_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `complaint_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_categories`
--

CREATE TABLE `complaint_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaint_categories`
--

INSERT INTO `complaint_categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Infrastruktur', 'infrastruktur', '2026-07-21 07:05:55', '2026-07-21 07:05:55'),
(2, 'Pelayanan Publik', 'pelayanan-publik', '2026-07-21 07:05:55', '2026-07-21 07:05:55'),
(3, 'Kebersihan & Lingkungan', 'kebersihan-lingkungan', '2026-07-21 07:05:55', '2026-07-21 07:05:55'),
(4, 'Kesehatan', 'kesehatan', '2026-07-21 07:05:55', '2026-07-21 07:05:55'),
(5, 'Pendidikan', 'pendidikan', '2026-07-21 07:05:55', '2026-07-21 07:05:55'),
(6, 'Keamanan & Ketertiban', 'keamanan-ketertiban', '2026-07-21 07:05:55', '2026-07-21 07:05:55'),
(7, 'Sosial & Kesejahteraan', 'sosial-kesejahteraan', '2026-07-21 07:05:55', '2026-07-21 07:05:55'),
(8, 'Lain-lain', 'lain-lain', '2026-07-21 07:05:55', '2026-07-21 07:05:55');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_handlings`
--

CREATE TABLE `complaint_handlings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `complaint_id` bigint(20) UNSIGNED NOT NULL,
  `disposition_id` bigint(20) UNSIGNED DEFAULT NULL,
  `handled_by` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaint_handlings`
--

INSERT INTO `complaint_handlings` (`id`, `complaint_id`, `disposition_id`, `handled_by`, `description`, `attachment_path`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 9, '<p>Lampu sudah diperbaiki dan diganti</p>', 'complaint-handlings/aIATeWd73nn0SJag7tL7FrUYY5dJCDISqJQ8TTuo.png', '2026-07-30 05:09:11', '2026-07-30 05:09:11'),
(2, 3, 5, 3, '<p>Penanganan telah dilaksanakan sesuai hasil pemeriksaan di lapangan.</p>', NULL, '2026-08-05 02:48:40', '2026-08-05 02:48:40'),
(3, 1, 6, 4, '<p>Tim telah melakukan survei lapangan dan menangani permasalahan.</p>', NULL, '2026-08-05 03:36:11', '2026-08-05 03:36:11');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_responses`
--

CREATE TABLE `complaint_responses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `complaint_id` bigint(20) UNSIGNED NOT NULL,
  `response_text` text NOT NULL,
  `responded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaint_responses`
--

INSERT INTO `complaint_responses` (`id`, `complaint_id`, `response_text`, `responded_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Lampu sudah diperbaiki dan diganti dan diperbaiki', 2, '2026-07-30 05:11:49', '2026-07-30 05:11:49');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_status_histories`
--

CREATE TABLE `complaint_status_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `complaint_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `changed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaint_status_histories`
--

INSERT INTO `complaint_status_histories` (`id`, `complaint_id`, `status`, `note`, `changed_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'diajukan', 'Pengaduan diajukan oleh masyarakat.', 1, '2026-07-21 07:06:05', '2026-07-21 07:06:05'),
(2, 2, 'diajukan', 'Pengaduan diajukan oleh masyarakat.', 1, '2026-07-21 07:06:13', '2026-07-21 07:06:13'),
(3, 2, 'diverifikasi', NULL, 2, '2026-07-30 04:49:21', '2026-07-30 04:49:21'),
(4, 2, 'diproses', 'Tolong tindak lanjuti', 2, '2026-07-30 04:49:57', '2026-07-30 04:49:57'),
(5, 2, 'ditindaklanjuti', '<p>Lampu sudah diperbaiki dan diganti</p>', 9, '2026-07-30 05:09:11', '2026-07-30 05:09:11'),
(6, 2, 'selesai', 'Lampu sudah diperbaiki dan diganti dan diperbaiki', 2, '2026-07-30 05:11:49', '2026-07-30 05:11:49'),
(7, 1, 'diverifikasi', NULL, 2, '2026-07-30 05:14:53', '2026-07-30 05:14:53'),
(8, 1, 'diproses', 'Silahkan Tindk Lanjuti', 2, '2026-07-30 05:15:15', '2026-07-30 05:15:15'),
(9, 1, 'diverifikasi', 'Disposisi dibatalkan.', 2, '2026-07-30 05:15:28', '2026-07-30 05:15:28'),
(10, 1, 'diproses', 'tindak lanjuti', 2, '2026-07-31 04:07:23', '2026-07-31 04:07:23'),
(11, 3, 'diajukan', 'Pengaduan diajukan oleh masyarakat.', 1, '2026-08-04 02:48:39', '2026-08-04 02:48:39'),
(12, 3, 'diverifikasi', NULL, 2, '2026-08-04 07:23:07', '2026-08-04 07:23:07'),
(13, 3, 'diproses', NULL, 2, '2026-08-04 07:23:37', '2026-08-04 07:23:37'),
(14, 3, 'diverifikasi', 'Disposisi dibatalkan.', 2, '2026-08-05 01:52:54', '2026-08-05 01:52:54'),
(15, 1, 'diverifikasi', 'Disposisi dibatalkan.', 2, '2026-08-05 02:08:36', '2026-08-05 02:08:36'),
(16, 3, 'diproses', NULL, 2, '2026-08-05 02:11:22', '2026-08-05 02:11:22'),
(17, 3, 'ditindaklanjuti', '<p>Penanganan telah dilaksanakan sesuai hasil pemeriksaan di lapangan.</p>', 3, '2026-08-05 02:48:40', '2026-08-05 02:48:40'),
(18, 1, 'diproses', NULL, 2, '2026-08-05 03:14:17', '2026-08-05 03:14:17'),
(19, 1, 'ditindaklanjuti', '<p>Tim telah melakukan survei lapangan dan menangani permasalahan.</p>', 4, '2026-08-05 03:36:11', '2026-08-05 03:36:11');

-- --------------------------------------------------------

--
-- Table structure for table `desas`
--

CREATE TABLE `desas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kecamatan_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `desas`
--

INSERT INTO `desas` (`id`, `kecamatan_id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(9, 20, 'Dalan Lidang', NULL, '2025-10-29 16:47:16', '2025-10-29 16:58:41'),
(11, 20, 'Sipolu-Polu', NULL, '2025-10-29 16:59:26', '2025-10-29 16:59:26'),
(12, 20, 'Pidoli Lombang', NULL, '2025-10-29 16:59:40', '2025-10-29 16:59:40'),
(13, 20, 'Pidoli Dolok', NULL, '2025-10-29 16:59:50', '2025-10-29 16:59:50'),
(14, 20, 'Kayu Jati', NULL, '2025-10-29 17:00:03', '2025-10-29 17:00:31'),
(15, 20, 'Aek Mata', NULL, '2025-10-29 17:02:06', '2025-10-29 17:02:06'),
(16, 20, 'Kota Siantar', NULL, '2025-10-29 17:02:35', '2025-10-29 17:02:35'),
(17, 20, 'Ipar Bondar', NULL, '2025-10-29 17:02:58', '2025-10-29 17:02:58'),
(18, 20, 'Parbangunan', NULL, '2025-10-29 17:03:20', '2025-10-29 17:03:20'),
(19, 20, 'Pagaran Tonga', NULL, '2025-10-30 20:16:48', '2025-10-30 20:16:48'),
(20, 20, 'Salam Bue', NULL, '2025-10-30 20:17:29', '2025-10-30 20:17:29'),
(21, 20, 'Adian Jior', NULL, '2025-10-30 20:20:45', '2025-10-30 20:20:45'),
(22, 20, 'Aek Banir', NULL, '2025-10-30 20:21:16', '2025-10-30 20:21:16'),
(23, 20, 'Darussalam', NULL, '2025-10-30 20:21:51', '2025-10-30 20:21:51'),
(24, 31, 'Batahan', NULL, '2025-10-30 20:22:17', '2025-10-30 20:22:17'),
(25, 20, 'Gunung Barani', NULL, '2025-10-30 20:22:22', '2025-10-30 20:22:22'),
(26, 31, 'Botung', NULL, '2025-10-30 20:22:33', '2025-10-30 20:22:33'),
(27, 20, 'Gunung Manaon', NULL, '2025-10-30 20:22:50', '2025-10-30 20:22:50'),
(30, 20, 'Gunung Tua Julu', NULL, '2025-10-30 20:23:47', '2025-10-30 20:23:47'),
(31, 20, 'Gunung Tua Jae', NULL, '2025-10-30 20:24:49', '2025-10-30 20:24:49'),
(32, 31, 'Gading Bain', NULL, '2025-10-30 20:24:52', '2025-10-30 20:24:52'),
(33, 31, 'Gunung Tua Ms', NULL, '2025-10-30 20:25:40', '2025-10-30 20:25:40'),
(34, 20, 'Gunung Tua Tonga', NULL, '2025-10-30 20:25:49', '2025-10-30 20:25:49'),
(35, 20, 'Huta Lombang Lubis', NULL, '2025-10-30 20:26:26', '2025-10-30 20:26:26'),
(37, 20, 'Kampung Padang', NULL, '2025-10-30 20:27:12', '2025-10-30 20:27:12'),
(38, 20, 'Lumban Pasir', NULL, '2025-10-30 20:27:34', '2025-10-30 20:27:34'),
(39, 20, 'Manyabar', NULL, '2025-10-30 20:28:01', '2025-10-30 20:28:01'),
(40, 31, 'Gunung Tua SM', NULL, '2025-10-30 20:28:13', '2025-10-30 20:28:13'),
(41, 20, 'Manyabar Jae', NULL, '2025-10-30 20:28:18', '2025-10-30 20:28:18'),
(42, 20, 'Panggorengan', NULL, '2025-10-30 20:28:47', '2025-10-30 20:28:47'),
(43, 31, 'Husor Tolang', NULL, '2025-10-30 20:29:22', '2025-10-30 20:29:22'),
(44, 31, 'Huta Baringin TB', NULL, '2025-10-30 20:29:40', '2025-10-30 20:29:40'),
(45, 20, 'Panyabungan Jae', NULL, '2025-10-30 20:29:40', '2025-10-30 20:29:40'),
(46, 31, 'Huta Dangka', NULL, '2025-10-30 20:29:56', '2025-10-30 20:29:56'),
(47, 20, 'Panyabungan Julu', NULL, '2025-10-30 20:30:02', '2025-10-30 20:30:02'),
(48, 31, 'Huta Padang  Sm', NULL, '2025-10-30 20:30:15', '2025-10-30 20:30:15'),
(49, 20, 'Panyabungan Tonga', NULL, '2025-10-30 20:30:21', '2025-10-30 20:30:21'),
(50, 31, 'Huta Puli', NULL, '2025-10-30 20:30:32', '2025-10-30 20:30:32'),
(51, 31, 'Huta Pungkut Jae', NULL, '2025-10-30 20:32:32', '2025-10-30 20:33:23'),
(52, 20, 'Sarak Matua', NULL, '2025-10-30 20:32:41', '2025-10-30 20:32:41'),
(53, 20, 'Sigalapang Julu', NULL, '2025-10-30 20:33:11', '2025-10-30 20:33:11'),
(54, 20, 'Sipapaga', NULL, '2025-10-30 20:34:00', '2025-10-30 20:34:00'),
(55, 20, 'Siobon Jae', NULL, '2025-10-30 20:34:27', '2025-10-30 20:34:27'),
(56, 20, 'Siobon Julu', NULL, '2025-10-30 20:34:42', '2025-10-30 20:34:42'),
(57, 31, 'Huta Pungkut Julu', NULL, '2025-10-30 20:34:59', '2025-10-30 20:34:59'),
(58, 20, 'Saba Jambu', NULL, '2025-10-30 20:35:10', '2025-10-30 20:35:10'),
(59, 20, 'Sopo Batu', NULL, '2025-10-30 20:35:33', '2025-10-30 20:35:33'),
(60, 31, 'Huta Pungkut Tonga', NULL, '2025-10-30 20:57:29', '2025-10-30 20:57:29'),
(62, 31, 'Huta Rimbaru Sm', NULL, '2025-10-30 20:58:34', '2025-10-30 20:58:34'),
(63, 31, 'Manambin', NULL, '2025-10-30 20:58:59', '2025-10-30 20:58:59'),
(64, 31, 'Muara Botung', NULL, '2025-10-30 20:59:34', '2025-10-30 20:59:34'),
(65, 31, 'Muara Potan', NULL, '2025-10-30 21:00:03', '2025-10-30 21:00:03'),
(66, 31, 'Muara Pungkut', NULL, '2025-10-30 21:00:31', '2025-10-30 21:00:31'),
(67, 31, 'Muara Siambak', NULL, '2025-10-30 21:01:00', '2025-10-30 21:01:00'),
(68, 31, 'Padang Bulan', NULL, '2025-10-30 21:01:24', '2025-10-30 21:01:24'),
(69, 31, 'Pagar Gunung', NULL, '2025-10-30 21:01:48', '2025-10-30 21:01:48'),
(70, 31, 'Patialo', NULL, '2025-10-30 21:02:10', '2025-10-30 21:02:10'),
(71, 31, 'Saba Dolok', NULL, '2025-10-30 21:02:31', '2025-10-30 21:02:31'),
(72, 31, 'Sibio Bio', NULL, '2025-10-30 21:03:02', '2025-10-30 21:03:02'),
(73, 31, 'Simandolan', NULL, '2025-10-30 21:03:32', '2025-10-30 21:03:32'),
(74, 31, 'Simpang Tolang Jae', NULL, '2025-10-30 21:04:26', '2025-10-30 21:04:26'),
(76, 31, 'Simpang Tolang Julu', NULL, '2025-10-30 21:04:46', '2025-10-30 21:04:46'),
(77, 31, 'Singengu Jae', NULL, '2025-10-30 21:05:25', '2025-10-30 21:05:25'),
(78, 31, 'Singengu Julu', NULL, '2025-10-30 21:05:38', '2025-10-30 21:05:38'),
(79, 31, 'Sopo Sorik', NULL, '2025-10-30 21:06:13', '2025-10-30 21:06:13'),
(80, 31, 'Tambang Bustak', NULL, '2025-10-30 21:06:57', '2025-10-30 21:06:57'),
(81, 31, 'Tobang', NULL, '2025-10-30 21:07:25', '2025-10-30 21:07:25'),
(82, 31, 'Ujung Marisi', NULL, '2025-10-30 21:07:54', '2025-10-30 21:07:54'),
(83, 31, 'Usor Tolang', NULL, '2025-10-30 21:08:31', '2025-10-30 21:08:31'),
(84, 25, 'Aek Mual', NULL, '2025-10-30 21:11:20', '2025-10-30 21:11:20'),
(85, 25, 'Bonan Dolok', NULL, '2025-10-30 21:11:45', '2025-10-30 21:11:45'),
(86, 25, 'Huraba', NULL, '2025-10-30 21:12:03', '2025-10-30 21:12:03'),
(87, 25, 'Huraba II', NULL, '2025-10-30 21:12:19', '2025-10-30 21:12:19'),
(88, 25, 'Huta Baringin', NULL, '2025-10-30 21:13:26', '2025-10-30 21:13:26'),
(89, 25, 'Huta Godang Muda', NULL, '2025-10-30 21:13:53', '2025-10-30 21:13:53'),
(90, 25, 'Huta Puli', NULL, '2025-10-30 21:14:14', '2025-10-30 21:14:14'),
(91, 25, 'Huta Raja', NULL, '2025-10-30 21:14:34', '2025-10-30 21:14:34'),
(92, 25, 'Lumban Dolok', NULL, '2025-10-30 21:14:57', '2025-10-30 21:14:57'),
(93, 25, 'Lumban Pinasa', NULL, '2025-10-30 21:15:15', '2025-10-30 21:15:15'),
(94, 25, 'Muara Batang Angkola', NULL, '2025-10-30 21:15:46', '2025-10-30 21:15:46'),
(95, 25, 'Pintu Padang Jae', NULL, '2025-10-30 21:16:31', '2025-10-30 21:16:31'),
(96, 25, 'Pintu Padang Julu', NULL, '2025-10-30 21:16:43', '2025-10-30 21:16:43'),
(97, 25, 'Sibaruang', NULL, '2025-10-30 21:17:13', '2025-10-30 21:17:13'),
(98, 25, 'Sihepeng', NULL, '2025-10-30 21:17:33', '2025-10-30 21:17:33'),
(99, 25, 'Sihepeng Sada', NULL, '2025-10-30 21:18:18', '2025-10-30 21:18:18'),
(100, 25, 'Sihepeng Dua', NULL, '2025-10-30 21:18:54', '2025-10-30 21:18:54'),
(101, 25, 'Sihepeng Tolu', NULL, '2025-10-30 21:19:19', '2025-10-30 21:19:19'),
(102, 25, 'Sihepeng Opat', NULL, '2025-10-30 21:19:34', '2025-10-30 21:19:34'),
(103, 25, 'Sihepeng Lima', NULL, '2025-10-30 21:19:51', '2025-10-30 21:19:51'),
(104, 25, 'Simaninggir', NULL, '2025-10-30 21:20:15', '2025-10-30 21:20:15'),
(105, 25, 'Sinonoan', NULL, '2025-10-30 21:21:01', '2025-10-30 21:21:01'),
(106, 25, 'Tangga Bosi', NULL, '2025-10-30 21:21:33', '2025-10-30 21:21:33'),
(107, 25, 'Tangga Bosi II', NULL, '2025-10-30 21:21:49', '2025-10-30 21:21:49'),
(108, 25, 'Tangga Bosi III', NULL, '2025-10-30 21:22:03', '2025-10-30 21:22:03'),
(109, 25, 'Tanjung Sialang', NULL, '2025-10-30 21:22:40', '2025-10-30 21:22:40'),
(110, 25, 'Siabu', NULL, '2025-10-30 21:23:02', '2025-10-30 21:23:02'),
(111, 25, 'Simanggambat', NULL, '2025-10-30 21:23:27', '2025-10-30 21:23:27'),
(112, 20, 'Panyabungan I', NULL, '2025-10-30 21:25:01', '2025-10-30 21:25:01'),
(113, 20, 'Panyabungan II', NULL, '2025-10-30 21:25:15', '2025-10-30 21:25:15'),
(114, 20, 'Panyabungan III', NULL, '2025-10-30 21:25:24', '2025-10-30 21:25:24'),
(115, 20, 'Pasar Hilir', NULL, '2025-10-30 21:25:50', '2025-10-30 21:25:50'),
(116, 31, 'Tamiang', NULL, '2025-10-30 21:26:53', '2025-10-30 21:26:53'),
(117, 31, 'Pasar Kotanopan', NULL, '2025-10-30 21:27:19', '2025-10-30 21:27:19'),
(118, 37, 'Aek Baru Jae', NULL, '2025-10-30 21:29:17', '2025-10-30 21:29:17'),
(119, 37, 'Aek Baru Julu', NULL, '2025-10-30 21:29:32', '2025-10-30 21:29:32'),
(120, 37, 'Aek Guo', NULL, '2025-10-30 21:30:06', '2025-10-30 21:30:06'),
(121, 37, 'Aek Holbung', NULL, '2025-10-30 21:30:27', '2025-10-30 21:30:27'),
(122, 37, 'Aek Manggis', NULL, '2025-10-30 21:30:51', '2025-10-30 21:30:51'),
(123, 37, 'Aek Nabara', NULL, '2025-10-30 21:31:08', '2025-10-30 21:31:08'),
(124, 37, 'Aek Nangali', NULL, '2025-10-30 21:31:37', '2025-10-30 21:31:37'),
(125, 37, 'Ampung  Julu', NULL, '2025-10-30 21:32:13', '2025-10-30 21:32:13'),
(126, 37, 'Ampung  Padang', NULL, '2025-10-30 21:32:30', '2025-10-30 21:32:30'),
(127, 37, 'Ampung  Siala', NULL, '2025-10-30 21:32:48', '2025-10-30 21:32:48'),
(128, 37, 'Bangkelang', NULL, '2025-10-30 21:33:25', '2025-10-30 21:33:25'),
(129, 37, 'Banjar Malayu', NULL, '2025-10-30 21:34:09', '2025-10-30 21:34:09'),
(130, 37, 'Batu Madinding', NULL, '2025-10-30 21:34:39', '2025-10-30 21:34:39'),
(131, 37, 'Bulu Soma', NULL, '2025-10-30 21:35:18', '2025-10-30 21:35:18'),
(132, 37, 'Guo Batu', NULL, '2025-10-30 21:35:42', '2025-10-30 21:35:42'),
(133, 37, 'Hadangkahan', NULL, '2025-10-30 21:36:29', '2025-10-30 21:36:29'),
(134, 37, 'Hatupangan', NULL, '2025-10-30 21:36:54', '2025-10-30 21:36:54'),
(135, 37, 'Huta Lobu', NULL, '2025-10-30 21:37:52', '2025-10-30 21:37:52'),
(136, 37, 'Jambur Baru', NULL, '2025-10-30 21:38:26', '2025-10-30 21:38:26'),
(137, 37, 'Lubuk Bandar Panjang', NULL, '2025-10-30 21:39:02', '2025-10-30 21:39:02'),
(138, 37, 'Lubuk Samboa', NULL, '2025-10-30 21:39:26', '2025-10-30 21:39:26'),
(139, 37, 'Muara Parlampungan', NULL, '2025-10-30 21:40:03', '2025-10-30 21:40:03'),
(140, 37, 'Rantobi', NULL, '2025-10-30 21:40:41', '2025-10-30 21:40:41'),
(141, 37, 'Rao Rao', NULL, '2025-10-30 21:40:55', '2025-10-30 21:40:55'),
(142, 37, 'Simanguntong', NULL, '2025-10-30 21:41:22', '2025-10-30 21:41:22'),
(143, 37, 'Sipogu', NULL, '2025-10-30 21:41:52', '2025-10-30 21:41:52'),
(144, 37, 'Sopotinjak', NULL, '2025-10-30 21:42:17', '2025-10-30 21:42:17'),
(145, 37, 'Tarlola', NULL, '2025-10-30 21:42:38', '2025-10-30 21:42:38'),
(146, 37, 'Tombang Kaluang', NULL, '2025-10-30 21:43:47', '2025-10-30 21:43:47'),
(147, 37, 'Tor Naincat', NULL, '2025-10-30 21:44:27', '2025-10-30 21:44:27'),
(148, 37, 'Muara Soma', NULL, '2025-10-30 21:44:48', '2025-10-30 21:44:48'),
(149, 32, 'Balimbing', NULL, '2025-10-30 21:45:55', '2025-10-30 21:45:55'),
(150, 32, 'Bintuas', NULL, '2025-10-30 21:47:13', '2025-10-30 21:47:13'),
(151, 32, 'Bonda Kase', NULL, '2025-10-30 21:47:44', '2025-10-30 21:47:44'),
(152, 32, 'Buburan', NULL, '2025-10-30 21:48:10', '2025-10-30 21:48:10'),
(153, 32, 'Kampung Sawah', NULL, '2025-10-30 21:48:43', '2025-10-30 21:48:43'),
(154, 32, 'Kun Kun', NULL, '2025-10-30 21:49:06', '2025-10-30 21:49:06'),
(155, 32, 'Panggautan', NULL, '2025-10-30 21:49:30', '2025-10-30 21:49:30'),
(156, 32, 'Pardamean Baru', NULL, '2025-10-30 21:50:01', '2025-10-30 21:50:01'),
(157, 32, 'Pasar III Natal', NULL, '2025-10-30 21:50:37', '2025-10-30 21:50:37'),
(158, 32, 'Pasar V  Natal', NULL, '2025-10-30 21:50:53', '2025-10-30 21:50:53'),
(159, 32, 'Pasar VI  Natal', NULL, '2025-10-30 21:51:04', '2025-10-30 21:51:04'),
(160, 32, 'Patiluban Hilir', NULL, '2025-10-30 21:51:51', '2025-10-30 21:51:51'),
(161, 32, 'Patiluban Mudik', NULL, '2025-10-30 21:52:20', '2025-10-30 21:52:20'),
(162, 32, 'Perkebunan Patiluban', NULL, '2025-10-30 21:52:55', '2025-10-30 21:52:55'),
(163, 32, 'Rukun Jaya', NULL, '2025-10-30 21:53:15', '2025-10-30 21:53:15'),
(164, 32, 'Sasaran', NULL, '2025-10-30 21:53:32', '2025-10-30 21:53:32'),
(165, 32, 'Setia Karya', NULL, '2025-10-30 21:53:52', '2025-10-30 21:53:52'),
(166, 32, 'Sikara Kara', NULL, '2025-10-30 21:54:07', '2025-10-30 21:54:07'),
(167, 32, 'Sikara Kara II', NULL, '2025-10-30 21:54:33', '2025-10-30 21:54:33'),
(168, 32, 'Sikara Kara III', NULL, '2025-10-30 21:54:44', '2025-10-30 21:54:44'),
(169, 32, 'Sikara Kara IV', NULL, '2025-10-30 21:54:56', '2025-10-30 21:54:56'),
(170, 32, 'Sinunukan V', NULL, '2025-10-30 21:55:29', '2025-10-30 21:55:29'),
(171, 32, 'Suka Maju', NULL, '2025-10-30 21:55:53', '2025-10-30 21:55:53'),
(172, 32, 'Sundutan Tigo', NULL, '2025-10-30 21:56:24', '2025-10-30 21:56:24'),
(173, 32, 'Taluk', NULL, '2025-10-30 21:56:43', '2025-10-30 21:56:43'),
(174, 32, 'Tegal Sari', NULL, '2025-10-30 21:57:17', '2025-10-30 21:57:17'),
(175, 32, 'Tunas Karya', NULL, '2025-10-30 21:57:46', '2025-10-30 21:57:46'),
(176, 32, 'Pasar I Natal', NULL, '2025-10-30 21:58:13', '2025-10-30 21:58:13'),
(177, 32, 'Pasar II Natal', NULL, '2025-10-30 21:58:32', '2025-10-30 21:58:32'),
(178, 40, 'Banjur Aur', NULL, '2025-10-31 00:16:49', '2025-10-31 00:16:49'),
(180, 40, 'Batahan II', NULL, '2025-10-31 00:17:34', '2025-10-31 00:17:34'),
(181, 40, 'Batahan III', NULL, '2025-10-31 00:17:50', '2025-10-31 00:17:50'),
(182, 40, 'Batahan IV', NULL, '2025-10-31 00:18:02', '2025-10-31 00:18:02'),
(183, 40, 'Batu Sondat', NULL, '2025-10-31 00:18:37', '2025-10-31 00:18:37'),
(184, 40, 'Bintungan Bejangkar', NULL, '2025-10-31 00:19:26', '2025-10-31 00:19:26'),
(185, 40, 'Kampung Kapas I', NULL, '2025-10-31 00:19:55', '2025-10-31 00:19:55'),
(186, 40, 'Kampung Kapas II', NULL, '2025-10-31 00:20:08', '2025-10-31 00:20:08'),
(187, 40, 'Kuala Batahan', NULL, '2025-10-31 00:20:36', '2025-10-31 00:20:36'),
(188, 40, 'Kubangan Pandan Sari', NULL, '2025-10-31 00:21:18', '2025-10-31 00:21:18'),
(189, 40, 'Kubangan Tompek', NULL, '2025-10-31 00:21:50', '2025-10-31 00:21:50'),
(190, 40, 'Muara Pertemuan', NULL, '2025-10-31 00:22:26', '2025-10-31 00:22:26'),
(191, 40, 'Pasar Batahan', NULL, '2025-10-31 00:22:50', '2025-10-31 00:22:50'),
(193, 40, 'Sari Kenanga Batahan', NULL, '2025-10-31 00:24:24', '2025-10-31 00:24:24'),
(194, 40, 'Pulai Tamang', NULL, '2025-10-31 00:24:48', '2025-10-31 00:24:48'),
(195, 40, 'Wono Sari', NULL, '2025-10-31 00:25:06', '2025-10-31 00:25:06'),
(196, 24, 'Bange', NULL, '2025-10-31 00:25:31', '2025-10-31 00:25:31'),
(197, 24, 'Bange Nauli', NULL, '2025-10-31 00:25:54', '2025-10-31 00:25:54'),
(198, 24, 'Huta Bangun', NULL, '2025-10-31 00:26:18', '2025-10-31 00:26:18'),
(199, 24, 'Huta Bangun Jae', NULL, '2025-10-31 00:26:30', '2025-10-31 00:26:30'),
(200, 24, 'Janji Matogu', NULL, '2025-10-31 00:27:19', '2025-10-31 00:27:19'),
(201, 24, 'Lambou Darul Ihsan', NULL, '2025-10-31 00:27:57', '2025-10-31 00:27:57'),
(202, 24, 'Malintang', NULL, '2025-10-31 00:28:15', '2025-10-31 00:28:15'),
(203, 24, 'Malintang Jae', NULL, '2025-10-31 00:28:26', '2025-10-31 00:28:26'),
(204, 24, 'Malintang Julu', NULL, '2025-10-31 00:28:37', '2025-10-31 00:28:37'),
(206, 24, 'Sidojadi', NULL, '2025-10-31 00:29:18', '2025-10-31 00:29:18'),
(207, 24, 'Pasar Baru Malintang', NULL, '2025-10-31 00:30:00', '2025-10-31 00:30:00'),
(208, 27, 'Bangun Sejati', NULL, '2025-10-31 00:30:35', '2025-10-31 00:30:35'),
(209, 27, 'Binanga', NULL, '2025-10-31 00:31:00', '2025-10-31 00:31:00'),
(210, 27, 'Huta Bargot Dolok', NULL, '2025-10-31 00:31:22', '2025-10-31 00:31:22'),
(211, 27, 'Huta Bargot Lombang', NULL, '2025-10-31 00:31:43', '2025-10-31 00:31:43'),
(212, 27, 'Huta Bargot Nauli', NULL, '2025-10-31 00:32:02', '2025-10-31 00:32:02'),
(213, 27, 'Huta Bargot Setia', NULL, '2025-10-31 00:32:18', '2025-10-31 00:32:18'),
(214, 27, 'Hutanaingkan', NULL, '2025-10-31 00:33:02', '2025-10-31 00:33:02'),
(215, 27, 'Hutarimbaru', NULL, '2025-10-31 00:33:31', '2025-10-31 00:33:31'),
(216, 27, 'Kumpulan Setia', NULL, '2025-10-31 00:33:53', '2025-10-31 00:33:53'),
(217, 27, 'Mondan', NULL, '2025-10-31 00:34:10', '2025-10-31 00:34:10'),
(218, 27, 'Pasar Huta Bargot', NULL, '2025-10-31 00:34:40', '2025-10-31 00:34:40'),
(219, 27, 'Saba Padang', NULL, '2025-10-31 00:34:59', '2025-10-31 00:34:59'),
(220, 27, 'Sayur Maincat', NULL, '2025-10-31 00:35:18', '2025-10-31 00:35:18'),
(221, 27, 'Simalagi', NULL, '2025-10-31 00:35:36', '2025-10-31 00:35:36'),
(222, 28, 'Aek Marian Mg', NULL, '2025-10-31 00:36:18', '2025-10-31 00:36:18'),
(223, 28, 'Bangun Purba', NULL, '2025-10-31 00:36:37', '2025-10-31 00:36:37'),
(224, 28, 'Maga Dolok', NULL, '2025-10-31 00:36:58', '2025-10-31 00:36:58'),
(225, 28, 'Maga Lombang', NULL, '2025-10-31 00:37:14', '2025-10-31 00:37:14'),
(226, 28, 'Pangkat', NULL, '2025-10-31 00:37:32', '2025-10-31 00:37:32'),
(227, 28, 'Purba Baru', NULL, '2025-10-31 00:37:51', '2025-10-31 00:37:51'),
(228, 28, 'Purba Lamo', NULL, '2025-10-31 00:38:12', '2025-10-31 00:38:12'),
(229, 28, 'Sian Tona', NULL, '2025-10-31 00:38:31', '2025-10-31 00:38:31'),
(230, 28, 'Pasar Maga', NULL, '2025-10-31 00:38:53', '2025-10-31 00:38:53'),
(231, 35, 'Aek Garingging', NULL, '2025-10-31 00:39:22', '2025-10-31 00:39:22'),
(232, 35, 'Aek Manyuruk', NULL, '2025-10-31 00:39:49', '2025-10-31 00:39:49'),
(233, 35, 'Bandar Limabung', NULL, '2025-10-31 00:40:04', '2025-10-31 00:40:04'),
(234, 35, 'Banjar Naga', NULL, '2025-10-31 00:40:32', '2025-10-31 00:40:32'),
(235, 35, 'Batu Gajah', NULL, '2025-10-31 00:40:46', '2025-10-31 00:40:46'),
(236, 35, 'Bonca Baiyon', NULL, '2025-10-31 00:41:28', '2025-10-31 00:41:28'),
(237, 35, 'Kampung Baru', NULL, '2025-10-31 00:41:52', '2025-10-31 00:41:52'),
(238, 35, 'Lancat', NULL, '2025-10-31 00:42:11', '2025-10-31 00:42:11'),
(239, 35, 'Lobung', NULL, '2025-10-31 00:42:26', '2025-10-31 00:42:26'),
(240, 35, 'Pangkalan', NULL, '2025-10-31 00:42:44', '2025-10-31 00:42:44'),
(241, 35, 'Perkebunan Simpang Gambir', NULL, '2025-10-31 00:43:12', '2025-10-31 00:43:12'),
(242, 35, 'Sikumbu', NULL, '2025-10-31 00:43:31', '2025-10-31 00:43:31'),
(243, 35, 'Simpang Bajole', NULL, '2025-10-31 00:43:53', '2025-10-31 00:43:53'),
(244, 35, 'Simpang Duku', NULL, '2025-10-31 00:44:12', '2025-10-31 00:44:12'),
(245, 35, 'Simpang Durian', NULL, '2025-10-31 00:44:34', '2025-10-31 00:44:34'),
(246, 35, 'Simpang Koje', NULL, '2025-10-31 00:44:51', '2025-10-31 00:44:51'),
(247, 35, 'Ulu Torusan', NULL, '2025-10-31 00:45:16', '2025-10-31 00:45:16'),
(248, 35, 'Simpang Gambir', NULL, '2025-10-31 00:45:34', '2025-10-31 00:45:34'),
(249, 35, 'Tapus', NULL, '2025-10-31 00:45:53', '2025-10-31 00:45:53'),
(250, 34, 'Batu Mundam', NULL, '2025-10-31 00:46:18', '2025-10-31 00:46:18'),
(251, 34, 'Huta Imbaru', NULL, '2025-10-31 00:46:46', '2025-10-31 00:46:46'),
(252, 34, 'Lubuk Kapundung', NULL, '2025-10-31 00:47:16', '2025-10-31 00:47:16'),
(254, 34, 'Lubuk Kapundung II', NULL, '2025-10-31 00:47:51', '2025-10-31 00:47:51'),
(255, 34, 'Manuncang', NULL, '2025-10-31 00:48:51', '2025-10-31 00:48:51'),
(256, 34, 'Panunggulan', NULL, '2025-10-31 00:49:13', '2025-10-31 00:49:13'),
(257, 34, 'Pasar I Singkuang', NULL, '2025-10-31 00:49:40', '2025-10-31 00:49:40'),
(258, 34, 'Pasar II Singkuang', NULL, '2025-10-31 00:49:52', '2025-10-31 00:49:52'),
(259, 34, 'Rantau Panjang', NULL, '2025-10-31 00:50:12', '2025-10-31 00:50:12'),
(260, 34, 'Sale Baru', NULL, '2025-10-31 00:50:35', '2025-10-31 00:50:35'),
(261, 34, 'Sikapas', NULL, '2025-10-31 00:50:48', '2025-10-31 00:50:48'),
(262, 34, 'Suka Makmur', NULL, '2025-10-31 00:51:08', '2025-10-31 00:51:08'),
(263, 34, 'Tabuyung', NULL, '2025-10-31 00:51:29', '2025-10-31 00:51:29'),
(264, 34, 'Tagilang Julu', NULL, '2025-10-31 00:51:50', '2025-10-31 00:51:50'),
(265, 38, 'Aek Botung', NULL, '2025-10-31 00:52:21', '2025-10-31 00:52:21'),
(266, 38, 'Bandar Panjang', NULL, '2025-10-31 00:52:41', '2025-10-31 00:52:41'),
(267, 38, 'Bandar Panjang  Tuo', NULL, '2025-10-31 00:53:27', '2025-10-31 00:53:27'),
(268, 38, 'Kampung Pinang', NULL, '2025-10-31 00:53:47', '2025-10-31 00:53:47'),
(269, 38, 'Koto Baringin', NULL, '2025-10-31 00:54:12', '2025-10-31 00:54:12'),
(270, 38, 'Koto Baru', NULL, '2025-10-31 00:54:30', '2025-10-31 00:54:30'),
(271, 38, 'Limau Manis', NULL, '2025-10-31 00:54:48', '2025-10-31 00:54:48'),
(272, 38, 'Muara Kumpulan', NULL, '2025-10-31 00:55:11', '2025-10-31 00:55:11'),
(273, 38, 'Ranjo Batu', NULL, '2025-10-31 00:55:36', '2025-10-31 00:55:36'),
(274, 38, 'Sibinail', NULL, '2025-10-31 00:55:56', '2025-10-31 00:55:56'),
(275, 38, 'Simpang Mandepo', NULL, '2025-10-31 00:56:18', '2025-10-31 00:56:18'),
(276, 38, 'Tamiang Mudo', NULL, '2025-10-31 00:56:46', '2025-10-31 00:56:46'),
(277, 38, 'Tanjung Alai', NULL, '2025-10-31 00:57:05', '2025-10-31 00:57:05'),
(278, 38, 'Tanjung Larangan', NULL, '2025-10-31 00:57:21', '2025-10-31 00:57:21'),
(279, 38, 'Tanjung Medan', NULL, '2025-10-31 00:57:40', '2025-10-31 00:57:40'),
(280, 38, 'Pasar Muara Sipongi', NULL, '2025-10-31 00:58:03', '2025-10-31 00:58:03'),
(288, 39, 'Huta Gambir', NULL, '2025-10-31 01:01:17', '2025-10-31 01:01:17'),
(289, 39, 'Huta Julu', NULL, '2025-10-31 01:01:35', '2025-10-31 01:01:35'),
(290, 39, 'Huta Lancat', NULL, '2025-10-31 01:01:56', '2025-10-31 01:01:56'),
(291, 39, 'Huta Padang', NULL, '2025-10-31 01:02:10', '2025-10-31 01:02:10'),
(292, 39, 'Huta Toras', NULL, '2025-10-31 01:02:36', '2025-10-31 01:02:36'),
(293, 39, 'Pakantan Dolok', NULL, '2025-10-31 01:02:57', '2025-10-31 01:02:57'),
(294, 39, 'Pakantan Lombang', NULL, '2025-10-31 01:03:16', '2025-10-31 01:03:16'),
(295, 39, 'Silogun', NULL, '2025-10-31 01:03:40', '2025-10-31 01:03:40'),
(296, 22, 'Barbaran Jae', NULL, '2025-10-31 01:04:15', '2025-10-31 01:04:15'),
(297, 22, 'Barbaran Julu', NULL, '2025-10-31 01:04:31', '2025-10-31 01:04:31'),
(298, 22, 'Batang Gadis', NULL, '2025-10-31 01:04:53', '2025-10-31 01:04:53'),
(299, 22, 'Batang Gadis Jae', NULL, '2025-10-31 01:05:11', '2025-10-31 01:05:11'),
(300, 22, 'Huta Tonga Barbaran', NULL, '2025-10-31 01:05:45', '2025-10-31 01:05:45'),
(301, 22, 'Hutabaringin', NULL, '2025-10-31 01:06:10', '2025-10-31 01:06:10'),
(302, 22, 'Runding', NULL, '2025-10-31 01:06:23', '2025-10-31 01:06:23'),
(303, 22, 'Saba Jior', NULL, '2025-10-31 01:06:35', '2025-10-31 01:06:35'),
(304, 22, 'Sirambas', NULL, '2025-10-31 01:06:48', '2025-10-31 01:06:48'),
(305, 22, 'Longat', NULL, '2025-10-31 01:07:08', '2025-10-31 01:07:08'),
(306, 30, 'Aek Ngali', NULL, '2025-10-31 01:07:46', '2025-10-31 01:07:46'),
(307, 30, 'Hayu Raja', NULL, '2025-10-31 01:08:08', '2025-10-31 01:08:08'),
(308, 30, 'Huta Julu', NULL, '2025-10-31 01:08:29', '2025-10-31 01:08:29'),
(309, 30, 'Huta Raja Huta Julu', NULL, '2025-10-31 01:08:56', '2025-10-31 01:08:56'),
(310, 30, 'Hutaimbaru', NULL, '2025-10-31 01:09:22', '2025-10-31 01:09:22'),
(311, 30, 'Kayu Laut', NULL, '2025-10-31 01:09:39', '2025-10-31 01:09:39'),
(312, 30, 'Lumban Dolok', NULL, '2025-10-31 01:10:03', '2025-10-31 01:10:03'),
(313, 30, 'Pagaran Gala Gala', NULL, '2025-10-31 01:10:18', '2025-10-31 01:10:18'),
(314, 30, 'Roburan Dolok', NULL, '2025-10-31 01:10:43', '2025-10-31 01:10:43'),
(315, 30, 'Roburan Lombang', NULL, '2025-10-31 01:10:59', '2025-10-31 01:10:59'),
(316, 30, 'Tano Bato', NULL, '2025-10-31 01:11:25', '2025-10-31 01:11:25'),
(317, 21, 'Aek Nabara', NULL, '2025-10-31 01:12:01', '2025-10-31 01:12:01'),
(318, 21, 'Banjar Lancat', NULL, '2025-10-31 01:12:51', '2025-10-31 01:12:51'),
(319, 21, 'Huta Bangun', NULL, '2025-10-31 01:13:14', '2025-10-31 01:13:14'),
(320, 21, 'Huta Tinggi', NULL, '2025-10-31 01:13:39', '2025-10-31 01:13:39'),
(322, 21, 'Hutaimbaru', NULL, '2025-10-31 01:15:50', '2025-10-31 01:15:50'),
(323, 21, 'Pagur', NULL, '2025-10-31 01:16:12', '2025-10-31 01:16:12'),
(324, 21, 'Padang Laru', NULL, '2025-10-31 01:16:40', '2025-10-31 01:16:40'),
(325, 21, 'Pardomuan', NULL, '2025-10-31 01:17:11', '2025-10-31 01:17:11'),
(326, 21, 'Parmompang', NULL, '2025-10-31 01:17:32', '2025-10-31 01:17:32'),
(327, 21, 'Ranto Natas', NULL, '2025-10-31 01:18:00', '2025-10-31 01:18:00'),
(328, 21, 'Tanjung Julu', NULL, '2025-10-31 01:18:30', '2025-10-31 01:18:30'),
(329, 21, 'Tanjung', NULL, '2025-10-31 01:18:43', '2025-10-31 01:18:43'),
(330, 21, 'Tebing Tinggi', NULL, '2025-10-31 01:19:12', '2025-10-31 01:19:12'),
(331, 21, 'Gunung Baringin', NULL, '2025-10-31 01:19:38', '2025-10-31 01:19:38'),
(332, 23, 'Baringin Jaya', NULL, '2025-10-31 01:20:08', '2025-10-31 01:20:08'),
(333, 23, 'Huta Dame', NULL, '2025-10-31 01:20:45', '2025-10-31 01:20:45'),
(334, 23, 'Jambur Padang Matinggi', NULL, '2025-10-31 01:21:16', '2025-10-31 01:21:16'),
(335, 23, 'Kampung Baru', NULL, '2025-10-31 01:21:30', '2025-10-31 01:21:30'),
(336, 23, 'Mompang Julu', NULL, '2025-10-31 01:21:53', '2025-10-31 01:21:53'),
(337, 23, 'Rumbio', NULL, '2025-10-31 01:22:05', '2025-10-31 01:22:05'),
(339, 23, 'Simanondong', NULL, '2025-10-31 01:22:38', '2025-10-31 01:22:38'),
(340, 23, 'Sopo Sorik', NULL, '2025-10-31 01:23:31', '2025-10-31 01:23:31'),
(341, 23, 'Sukaramai', NULL, '2025-10-31 01:24:10', '2025-10-31 01:24:10'),
(342, 23, 'Tanjung Mompang', NULL, '2025-10-31 01:24:25', '2025-10-31 01:24:25'),
(343, 23, 'Tor Banua Raja', NULL, '2025-10-31 01:24:48', '2025-10-31 01:24:48'),
(344, 23, 'Mompang Jae', NULL, '2025-10-31 01:25:13', '2025-10-31 01:25:13'),
(345, 29, 'Handel', NULL, '2025-10-31 01:25:56', '2025-10-31 01:25:56'),
(346, 29, 'Huta Baringin', NULL, '2025-10-31 01:26:17', '2025-10-31 01:26:17'),
(347, 29, 'Huta Baringin Julu', NULL, '2025-10-31 01:26:29', '2025-10-31 01:26:29'),
(348, 29, 'Huta Baru', NULL, '2025-10-31 01:26:54', '2025-10-31 01:26:54'),
(349, 29, 'Huta Lombang', NULL, '2025-10-31 01:27:22', '2025-10-31 01:27:22'),
(350, 29, 'Huta Namale', NULL, '2025-10-31 01:27:45', '2025-10-31 01:27:45'),
(351, 29, 'Huta Tinggi', NULL, '2025-10-31 01:28:01', '2025-10-31 01:28:01'),
(352, 29, 'Purba Julu', NULL, '2025-10-31 01:28:17', '2025-10-31 01:28:17'),
(353, 29, 'Sibanggor Jae', NULL, '2025-10-31 01:28:36', '2025-10-31 01:28:36'),
(354, 29, 'Sibanggor Julu', NULL, '2025-10-31 01:28:48', '2025-10-31 01:28:48'),
(356, 29, 'Sibanggor Tonga', NULL, '2025-10-31 01:29:12', '2025-10-31 01:29:12'),
(357, 36, 'Bangun Saroha', NULL, '2025-10-31 01:30:42', '2025-10-31 01:30:42'),
(358, 36, 'Banjar Maga', NULL, '2025-10-31 01:31:02', '2025-10-31 01:31:02'),
(359, 36, 'Gonting', NULL, '2025-10-31 01:31:18', '2025-10-31 01:31:18'),
(360, 36, 'Gunung Godang', NULL, '2025-10-31 01:31:40', '2025-10-31 01:31:40'),
(361, 36, 'Huta Baringin', NULL, '2025-10-31 01:32:01', '2025-10-31 01:32:01'),
(362, 36, 'Huta Nauli', NULL, '2025-10-31 01:32:18', '2025-10-31 01:32:18'),
(363, 36, 'Huta Raja', NULL, '2025-10-31 01:32:29', '2025-10-31 01:32:29'),
(364, 36, 'Lubuk Kancah', NULL, '2025-10-31 01:32:48', '2025-10-31 01:32:48'),
(365, 36, 'Manisak', NULL, '2025-10-31 01:33:06', '2025-10-31 01:33:06'),
(366, 36, 'Padang Silojongon', NULL, '2025-10-31 01:33:29', '2025-10-31 01:33:29'),
(367, 36, 'Ranto Nalinjang', NULL, '2025-10-31 01:33:50', '2025-10-31 01:33:50'),
(368, 36, 'Ranto Panjang', NULL, '2025-10-31 01:34:16', '2025-10-31 01:34:16'),
(369, 36, 'Sampuran', NULL, '2025-10-31 01:34:34', '2025-10-31 01:34:34'),
(370, 36, 'Simaninggir', NULL, '2025-10-31 01:34:52', '2025-10-31 01:34:52'),
(371, 36, 'Simpang Talap', NULL, '2025-10-31 01:35:10', '2025-10-31 01:35:10'),
(372, 36, 'Tandikek', NULL, '2025-10-31 01:35:26', '2025-10-31 01:35:26'),
(373, 36, 'Torusan Duo Sepakat', NULL, '2025-10-31 01:35:58', '2025-10-31 01:35:58'),
(374, 36, 'Torusan Muara Bangko', NULL, '2025-10-31 01:36:20', '2025-10-31 01:36:20'),
(375, 33, 'Airapa', NULL, '2025-10-31 01:36:55', '2025-10-31 01:36:55'),
(376, 33, 'Banjar Aur Utara', NULL, '2025-10-31 01:37:21', '2025-10-31 01:37:21'),
(377, 33, 'Bintungan Bejangkar Baru', NULL, '2025-10-31 01:38:15', '2025-10-31 01:38:15'),
(378, 33, 'Kampung Kapas II', NULL, '2025-10-31 01:38:42', '2025-10-31 01:38:42'),
(379, 33, 'Pasir Putih', NULL, '2025-10-31 01:39:04', '2025-10-31 01:39:04'),
(380, 33, 'Sido Makmur', NULL, '2025-10-31 01:39:27', '2025-10-31 01:39:27'),
(381, 33, 'Sinunukan I', NULL, '2025-10-31 01:39:48', '2025-10-31 01:39:48'),
(382, 33, 'Sinunukan II', NULL, '2025-10-31 01:39:58', '2025-10-31 01:39:58'),
(383, 33, 'Sinunukan III', NULL, '2025-10-31 01:40:08', '2025-10-31 01:40:08'),
(384, 33, 'Sinunukan IV', NULL, '2025-10-31 01:40:19', '2025-10-31 01:40:19'),
(386, 33, 'Sinunukan VI', NULL, '2025-10-31 01:40:46', '2025-10-31 01:40:46'),
(387, 33, 'Suka Damai', NULL, '2025-10-31 01:41:27', '2025-10-31 01:41:27'),
(388, 33, 'Suka Damai II', NULL, '2025-10-31 01:41:40', '2025-10-31 01:41:40'),
(389, 33, 'Widoderen', NULL, '2025-10-31 01:42:01', '2025-10-31 01:42:01'),
(390, 41, 'Angin Barat', NULL, '2025-10-31 01:43:12', '2025-10-31 01:43:12'),
(391, 41, 'Huta Tonga AB', NULL, '2025-10-31 01:43:39', '2025-10-31 01:43:39'),
(392, 41, 'Laru Baringin', NULL, '2025-10-31 01:44:03', '2025-10-31 01:44:03'),
(393, 41, 'Laru Bolak', NULL, '2025-10-31 01:44:23', '2025-10-31 01:44:23'),
(394, 41, 'Laru Dolok', NULL, '2025-10-31 01:44:36', '2025-10-31 01:44:36'),
(395, 41, 'Lumban Pasir', NULL, '2025-10-31 01:45:04', '2025-10-31 01:45:04'),
(396, 41, 'Muara Mais', NULL, '2025-10-31 01:45:24', '2025-10-31 01:45:24'),
(397, 41, 'Muara Mais Jambur', NULL, '2025-10-31 01:45:45', '2025-10-31 01:45:45'),
(398, 41, 'Padang Sanggar', NULL, '2025-10-31 01:46:10', '2025-10-31 01:46:10'),
(399, 41, 'Panjaringan', NULL, '2025-10-31 01:46:36', '2025-10-31 01:46:36'),
(400, 41, 'Pasar Laru', NULL, '2025-10-31 01:46:56', '2025-10-31 01:46:56'),
(401, 41, 'Pastap', NULL, '2025-10-31 01:47:16', '2025-10-31 01:47:16'),
(402, 41, 'Pastap Julu', NULL, '2025-10-31 01:47:31', '2025-10-31 01:47:31'),
(403, 41, 'Rao Rao Dolok', NULL, '2025-10-31 01:47:55', '2025-10-31 01:47:55'),
(404, 41, 'Rao Rao Lombang', NULL, '2025-10-31 01:48:12', '2025-10-31 01:48:12'),
(405, 41, 'Simangambat TB', NULL, '2025-10-31 01:48:31', '2025-10-31 01:48:31'),
(406, 41, 'Tambangan Jae', NULL, '2025-10-31 01:48:51', '2025-10-31 01:48:51'),
(407, 41, 'Tambangan Pasoman', NULL, '2025-10-31 01:49:07', '2025-10-31 01:49:07'),
(408, 41, 'Tambangan Tonga', NULL, '2025-10-31 01:49:19', '2025-10-31 01:49:19'),
(409, 41, 'Laru Lombang', NULL, '2025-10-31 01:49:36', '2025-10-31 01:49:36'),
(410, 42, 'Alahan Kae', NULL, '2025-10-31 01:50:09', '2025-10-31 01:50:09'),
(411, 42, 'Habincaran', NULL, '2025-10-31 01:50:21', '2025-10-31 01:50:21'),
(412, 42, 'Huta Padang Up', NULL, '2025-10-31 01:50:49', '2025-10-31 01:50:49'),
(413, 42, 'Huta Rimbaru Up', NULL, '2025-10-31 01:51:17', '2025-10-31 01:51:17'),
(414, 42, 'Muara Saladi', NULL, '2025-10-31 01:51:34', '2025-10-31 01:51:34'),
(415, 42, 'Patahajang', NULL, '2025-10-31 01:51:52', '2025-10-31 01:51:52'),
(416, 42, 'Simpang Banyak Jae', NULL, '2025-10-31 01:52:12', '2025-10-31 01:52:12'),
(417, 42, 'Simpang Banyak Julu', NULL, '2025-10-31 01:52:23', '2025-10-31 01:52:23'),
(418, 42, 'Simpang Duhu Lombang', NULL, '2025-10-31 01:52:42', '2025-10-31 01:52:42'),
(419, 42, 'Simpang Duku Dolok', NULL, '2025-10-31 01:52:58', '2025-10-31 01:52:58'),
(420, 42, 'Simpang Pining', NULL, '2025-10-31 01:53:19', '2025-10-31 01:53:19'),
(421, 42, 'Tolang', NULL, '2025-10-31 01:53:32', '2025-10-31 01:53:32'),
(422, 42, 'Huta Godang', NULL, '2025-10-31 01:53:48', '2025-10-31 01:53:48'),
(423, 21, 'Sirangkap', NULL, '2025-10-31 02:06:13', '2025-10-31 02:06:13'),
(424, 26, 'Banua Rakyat', NULL, '2025-10-31 02:10:17', '2025-10-31 02:10:17'),
(425, 26, 'Banua Simanosor', NULL, '2025-10-31 02:10:40', '2025-10-31 02:10:40'),
(426, 26, 'Humbang I', NULL, '2025-10-31 02:11:02', '2025-10-31 02:11:02'),
(427, 26, 'Sayur Matua', NULL, '2025-10-31 02:11:28', '2025-10-31 02:11:28'),
(428, 26, 'Tambiski', NULL, '2025-10-31 02:11:48', '2025-10-31 02:11:48'),
(429, 26, 'Tambiski Nauli', NULL, '2025-10-31 02:12:02', '2025-10-31 02:12:02'),
(430, 26, 'Tarutung Panjang', NULL, '2025-10-31 02:12:20', '2025-10-31 02:12:20'),
(432, 40, 'Pasar Baru Batahan', NULL, '2025-11-02 21:09:29', '2025-11-02 21:09:29'),
(433, 40, 'Batahan I', NULL, '2025-11-02 21:18:39', '2025-11-02 21:18:39'),
(434, 32, 'Si Kara -Kara I', NULL, '2025-11-02 21:27:01', '2025-11-02 21:27:01');

-- --------------------------------------------------------

--
-- Table structure for table `dispositions`
--

CREATE TABLE `dispositions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `complaint_id` bigint(20) UNSIGNED NOT NULL,
  `disposed_to_type` varchar(255) NOT NULL,
  `disposed_to_id` bigint(20) UNSIGNED NOT NULL,
  `disposed_by` bigint(20) UNSIGNED NOT NULL,
  `note` text DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `cancel_note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dispositions`
--

INSERT INTO `dispositions` (`id`, `complaint_id`, `disposed_to_type`, `disposed_to_id`, `disposed_by`, `note`, `cancelled_at`, `cancelled_by`, `cancel_note`, `created_at`, `updated_at`) VALUES
(1, 2, 'camat', 20, 2, 'Tolong tindak lanjuti', NULL, NULL, NULL, '2026-07-30 04:49:57', '2026-07-30 04:49:57'),
(2, 1, 'opd', 29, 2, 'Silahkan Tindk Lanjuti', '2026-07-30 05:15:28', 2, NULL, '2026-07-30 05:15:15', '2026-07-30 05:15:28'),
(3, 1, 'opd', 7, 2, 'tindak lanjuti', '2026-08-05 02:08:36', 2, NULL, '2026-07-31 04:07:22', '2026-08-05 02:08:36'),
(4, 3, 'opd', 16, 2, NULL, '2026-08-05 01:52:54', 2, NULL, '2026-08-04 07:23:37', '2026-08-05 01:52:54'),
(5, 3, 'opd', 44, 2, NULL, NULL, NULL, NULL, '2026-08-05 02:11:22', '2026-08-05 02:11:22'),
(6, 1, 'camat', 43, 2, NULL, NULL, NULL, NULL, '2026-08-05 03:14:17', '2026-08-05 03:14:17');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"8405a5a1-bcc8-4170-8433-c1c3bc4e63e1\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintSubmitted\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:57:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintSubmitted\\\":1:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:1;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:1;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Jalan Rusak di Depan Kantor Desa\\\";s:11:\\\"description\\\";s:100:\\\"Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.\\\";s:8:\\\"category\\\";s:13:\\\"Infrastruktur\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIAJUKAN\\\";s:8:\\\"latitude\\\";d:0.5333;s:9:\\\"longitude\\\";d:99.4167;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1784617572,\"delay\":null}', 0, NULL, 1784617572, 1784617572),
(2, 'default', '{\"uuid\":\"7b5ca9b5-c0ed-4b80-a346-2cf4ede8c2db\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintSubmitted\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:57:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintSubmitted\\\":1:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:2;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:2;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Lampu Jalan Mati di Simpang Tiga\\\";s:11:\\\"description\\\";s:86:\\\"Lampu penerangan jalan umum mati sejak seminggu lalu sehingga kawasan gelap dan rawan.\\\";s:8:\\\"category\\\";s:16:\\\"Pelayanan Publik\\\";s:10:\\\"targetType\\\";E:50:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:CAMAT\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIAJUKAN\\\";s:8:\\\"latitude\\\";d:0.5401;s:9:\\\"longitude\\\";d:99.421;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:13.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:13.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1784617573,\"delay\":null}', 0, NULL, 1784617573, 1784617573),
(3, 'default', '{\"uuid\":\"e6f659c2-c808-40f3-9572-dd8eac838529\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintVerified\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintVerified\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:2;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:2;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Lampu Jalan Mati di Simpang Tiga\\\";s:11:\\\"description\\\";s:86:\\\"Lampu penerangan jalan umum mati sejak seminggu lalu sehingga kawasan gelap dan rawan.\\\";s:8:\\\"category\\\";s:16:\\\"Pelayanan Publik\\\";s:10:\\\"targetType\\\";E:50:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:CAMAT\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";s:8:\\\"latitude\\\";d:0.5401;s:9:\\\"longitude\\\";d:99.421;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:13.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:13.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:7:\\\"isValid\\\";b:1;s:15:\\\"rejectionReason\\\";N;s:14:\\\"previousStatus\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIAJUKAN\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785386961,\"delay\":null}', 0, NULL, 1785386961, 1785386961),
(4, 'default', '{\"uuid\":\"a7022b99-ace4-4f1d-a0c1-bdf36e3c1b61\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:2;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:2;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Lampu Jalan Mati di Simpang Tiga\\\";s:11:\\\"description\\\";s:86:\\\"Lampu penerangan jalan umum mati sejak seminggu lalu sehingga kawasan gelap dan rawan.\\\";s:8:\\\"category\\\";s:16:\\\"Pelayanan Publik\\\";s:10:\\\"targetType\\\";E:50:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:CAMAT\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";s:8:\\\"latitude\\\";d:0.5401;s:9:\\\"longitude\\\";d:99.421;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:13.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-30 11:49:21.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"disposedToType\\\";r:12;s:12:\\\"disposedToId\\\";i:20;s:14:\\\"previousStatus\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785386997,\"delay\":null}', 0, NULL, 1785386997, 1785386997),
(5, 'default', '{\"uuid\":\"a9cb0a9f-75fa-49a7-954d-5007082d9895\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintHandled\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:55:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintHandled\\\":2:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:2;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:2;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Lampu Jalan Mati di Simpang Tiga\\\";s:11:\\\"description\\\";s:86:\\\"Lampu penerangan jalan umum mati sejak seminggu lalu sehingga kawasan gelap dan rawan.\\\";s:8:\\\"category\\\";s:16:\\\"Pelayanan Publik\\\";s:10:\\\"targetType\\\";E:50:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:CAMAT\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:65:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DITINDAKLANJUTI\\\";s:8:\\\"latitude\\\";d:0.5401;s:9:\\\"longitude\\\";d:99.421;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:13.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-30 11:49:57.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"previousStatus\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785388151,\"delay\":null}', 0, NULL, 1785388151, 1785388151),
(6, 'default', '{\"uuid\":\"9700da78-347f-4c11-a214-3a12c658dd8d\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintResolved\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintResolved\\\":3:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:2;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:2;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Lampu Jalan Mati di Simpang Tiga\\\";s:11:\\\"description\\\";s:86:\\\"Lampu penerangan jalan umum mati sejak seminggu lalu sehingga kawasan gelap dan rawan.\\\";s:8:\\\"category\\\";s:16:\\\"Pelayanan Publik\\\";s:10:\\\"targetType\\\";E:50:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:CAMAT\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:57:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:SELESAI\\\";s:8:\\\"latitude\\\";d:0.5401;s:9:\\\"longitude\\\";d:99.421;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:13.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-30 12:09:11.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:12:\\\"responseText\\\";s:49:\\\"Lampu sudah diperbaiki dan diganti dan diperbaiki\\\";s:14:\\\"previousStatus\\\";E:65:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DITINDAKLANJUTI\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785388309,\"delay\":null}', 0, NULL, 1785388309, 1785388309),
(7, 'default', '{\"uuid\":\"416dc414-0b44-4e66-8d8c-d777d78503bd\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintVerified\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintVerified\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:1;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:1;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Jalan Rusak di Depan Kantor Desa\\\";s:11:\\\"description\\\";s:100:\\\"Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.\\\";s:8:\\\"category\\\";s:13:\\\"Infrastruktur\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";s:8:\\\"latitude\\\";d:0.5333;s:9:\\\"longitude\\\";d:99.4167;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:7:\\\"isValid\\\";b:1;s:15:\\\"rejectionReason\\\";N;s:14:\\\"previousStatus\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIAJUKAN\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785388493,\"delay\":null}', 0, NULL, 1785388493, 1785388493),
(8, 'default', '{\"uuid\":\"426623fd-cb9e-4da2-b3f8-073697ca5d8e\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:1;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:1;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Jalan Rusak di Depan Kantor Desa\\\";s:11:\\\"description\\\";s:100:\\\"Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.\\\";s:8:\\\"category\\\";s:13:\\\"Infrastruktur\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";s:8:\\\"latitude\\\";d:0.5333;s:9:\\\"longitude\\\";d:99.4167;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-30 12:14:53.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"disposedToType\\\";r:12;s:12:\\\"disposedToId\\\";i:29;s:14:\\\"previousStatus\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785388515,\"delay\":null}', 0, NULL, 1785388515, 1785388515),
(9, 'default', '{\"uuid\":\"89a7eebc-3968-4859-a878-d8898b3f1774\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDispositionCancelled\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:68:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDispositionCancelled\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:1;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:1;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Jalan Rusak di Depan Kantor Desa\\\";s:11:\\\"description\\\";s:100:\\\"Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.\\\";s:8:\\\"category\\\";s:13:\\\"Infrastruktur\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";s:8:\\\"latitude\\\";d:0.5333;s:9:\\\"longitude\\\";d:99.4167;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-30 12:15:15.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:19:\\\"cancelledTargetType\\\";r:12;s:17:\\\"cancelledTargetId\\\";i:29;s:14:\\\"previousStatus\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785388528,\"delay\":null}', 0, NULL, 1785388528, 1785388528),
(10, 'default', '{\"uuid\":\"e3b8d591-9227-41a9-a72c-018d24029e12\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ActivityPublished\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ActivityPublished\\\":2:{s:8:\\\"activity\\\";O:37:\\\"App\\\\Domain\\\\Activity\\\\Entities\\\\Activity\\\":12:{s:2:\\\"id\\\";i:3;s:5:\\\"title\\\";s:10:\\\"Kebersihan\\\";s:11:\\\"description\\\";s:17:\\\"<p>Kebersihan<\\/p>\\\";s:9:\\\"actorType\\\";s:9:\\\"kecamatan\\\";s:7:\\\"actorId\\\";i:20;s:4:\\\"date\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-29 12:16:44.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:8:\\\"location\\\";s:24:\\\"Mesjid Agung Syahrun Nur\\\";s:6:\\\"status\\\";E:62:\\\"App\\\\Domain\\\\Activity\\\\ValueObjects\\\\ActivityStatus:DIPUBLIKASIKAN\\\";s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-30 12:10:45.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-30 12:16:31.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"previousStatus\\\";E:60:\\\"App\\\\Domain\\\\Activity\\\\ValueObjects\\\\ActivityStatus:DIVERIFIKASI\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785388604,\"delay\":null}', 0, NULL, 1785388604, 1785388604),
(11, 'default', '{\"uuid\":\"9cf9bee3-e611-426e-a252-f0009014eeed\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:1;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:1;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Jalan Rusak di Depan Kantor Desa\\\";s:11:\\\"description\\\";s:100:\\\"Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.\\\";s:8:\\\"category\\\";s:13:\\\"Infrastruktur\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";s:8:\\\"latitude\\\";d:0.5333;s:9:\\\"longitude\\\";d:99.4167;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-30 12:15:28.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"disposedToType\\\";r:12;s:12:\\\"disposedToId\\\";i:7;s:14:\\\"previousStatus\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785470843,\"delay\":null}', 0, NULL, 1785470843, 1785470843),
(12, 'default', '{\"uuid\":\"375d2a48-3c30-423a-9134-0b4b3a1c12af\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintSubmitted\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:57:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintSubmitted\\\":1:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:3;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:3;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Sampah Menumpuk Di Depan Sekolah\\\";s:11:\\\"description\\\";s:148:\\\"<p>Sampah menumpuk di depan sekolah Madrasah syariful Majelis hingga ke jalan raya dan bau tidak enak menyeruak ke sekolah maupun di jalan raya.<\\/p>\\\";s:8:\\\"category\\\";s:23:\\\"Kebersihan & Lingkungan\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:40;s:6:\\\"status\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIAJUKAN\\\";s:8:\\\"latitude\\\";d:0.6722828;s:9:\\\"longitude\\\";d:99.7039392;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 09:48:39.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 09:48:39.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785811719,\"delay\":null}', 0, NULL, 1785811719, 1785811719),
(13, 'default', '{\"uuid\":\"6ccae160-7a78-44bd-becb-23b74189f25c\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintVerified\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintVerified\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:3;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:3;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Sampah Menumpuk Di Depan Sekolah\\\";s:11:\\\"description\\\";s:148:\\\"<p>Sampah menumpuk di depan sekolah Madrasah syariful Majelis hingga ke jalan raya dan bau tidak enak menyeruak ke sekolah maupun di jalan raya.<\\/p>\\\";s:8:\\\"category\\\";s:23:\\\"Kebersihan & Lingkungan\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:40;s:6:\\\"status\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";s:8:\\\"latitude\\\";d:0.6722828;s:9:\\\"longitude\\\";d:99.7039392;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 09:48:39.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 09:48:39.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:7:\\\"isValid\\\";b:1;s:15:\\\"rejectionReason\\\";N;s:14:\\\"previousStatus\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIAJUKAN\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785828187,\"delay\":null}', 0, NULL, 1785828187, 1785828187),
(14, 'default', '{\"uuid\":\"b0f130f3-9020-4bb4-8bc2-5c629c1f6e51\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:3;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:3;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Sampah Menumpuk Di Depan Sekolah\\\";s:11:\\\"description\\\";s:148:\\\"<p>Sampah menumpuk di depan sekolah Madrasah syariful Majelis hingga ke jalan raya dan bau tidak enak menyeruak ke sekolah maupun di jalan raya.<\\/p>\\\";s:8:\\\"category\\\";s:23:\\\"Kebersihan & Lingkungan\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:40;s:6:\\\"status\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";s:8:\\\"latitude\\\";d:0.6722828;s:9:\\\"longitude\\\";d:99.7039392;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 09:48:39.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 14:23:07.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"disposedToType\\\";r:12;s:12:\\\"disposedToId\\\";i:16;s:14:\\\"previousStatus\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785828217,\"delay\":null}', 0, NULL, 1785828217, 1785828217),
(15, 'default', '{\"uuid\":\"f317189a-33ae-4153-acfc-26b2c888cece\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDispositionCancelled\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:68:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDispositionCancelled\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:3;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:3;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Sampah Menumpuk Di Depan Sekolah\\\";s:11:\\\"description\\\";s:148:\\\"<p>Sampah menumpuk di depan sekolah Madrasah syariful Majelis hingga ke jalan raya dan bau tidak enak menyeruak ke sekolah maupun di jalan raya.<\\/p>\\\";s:8:\\\"category\\\";s:23:\\\"Kebersihan & Lingkungan\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:40;s:6:\\\"status\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";s:8:\\\"latitude\\\";d:0.6722828;s:9:\\\"longitude\\\";d:99.7039392;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 09:48:39.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 14:23:37.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:19:\\\"cancelledTargetType\\\";r:12;s:17:\\\"cancelledTargetId\\\";i:16;s:14:\\\"previousStatus\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785894774,\"delay\":null}', 0, NULL, 1785894774, 1785894774),
(16, 'default', '{\"uuid\":\"216589d9-5f29-4726-83a7-c425555d20a1\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDispositionCancelled\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:68:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDispositionCancelled\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:1;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:1;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Jalan Rusak di Depan Kantor Desa\\\";s:11:\\\"description\\\";s:100:\\\"Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.\\\";s:8:\\\"category\\\";s:13:\\\"Infrastruktur\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";s:8:\\\"latitude\\\";d:0.5333;s:9:\\\"longitude\\\";d:99.4167;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-31 11:07:23.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:19:\\\"cancelledTargetType\\\";r:12;s:17:\\\"cancelledTargetId\\\";i:7;s:14:\\\"previousStatus\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785895716,\"delay\":null}', 0, NULL, 1785895716, 1785895716),
(17, 'default', '{\"uuid\":\"10cdad48-148a-41c9-979b-8894fbd6b361\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:3;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:3;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Sampah Menumpuk Di Depan Sekolah\\\";s:11:\\\"description\\\";s:148:\\\"<p>Sampah menumpuk di depan sekolah Madrasah syariful Majelis hingga ke jalan raya dan bau tidak enak menyeruak ke sekolah maupun di jalan raya.<\\/p>\\\";s:8:\\\"category\\\";s:23:\\\"Kebersihan & Lingkungan\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:40;s:6:\\\"status\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";s:8:\\\"latitude\\\";d:0.6722828;s:9:\\\"longitude\\\";d:99.7039392;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 09:48:39.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-05 08:52:54.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"disposedToType\\\";r:12;s:12:\\\"disposedToId\\\";i:44;s:14:\\\"previousStatus\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785895882,\"delay\":null}', 0, NULL, 1785895882, 1785895882),
(18, 'default', '{\"uuid\":\"745be6c9-d77b-47c3-99c7-96632849e63c\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintHandled\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:55:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintHandled\\\":2:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:3;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:3;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Sampah Menumpuk Di Depan Sekolah\\\";s:11:\\\"description\\\";s:148:\\\"<p>Sampah menumpuk di depan sekolah Madrasah syariful Majelis hingga ke jalan raya dan bau tidak enak menyeruak ke sekolah maupun di jalan raya.<\\/p>\\\";s:8:\\\"category\\\";s:23:\\\"Kebersihan & Lingkungan\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:40;s:6:\\\"status\\\";E:65:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DITINDAKLANJUTI\\\";s:8:\\\"latitude\\\";d:0.6722828;s:9:\\\"longitude\\\";d:99.7039392;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-04 09:48:39.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-05 09:11:22.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"previousStatus\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785898120,\"delay\":null}', 0, NULL, 1785898120, 1785898120);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(19, 'default', '{\"uuid\":\"cb4394cd-971c-4ab5-8945-c447de170005\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:56:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintDisposed\\\":4:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:1;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:1;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Jalan Rusak di Depan Kantor Desa\\\";s:11:\\\"description\\\";s:100:\\\"Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.\\\";s:8:\\\"category\\\";s:13:\\\"Infrastruktur\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";s:8:\\\"latitude\\\";d:0.5333;s:9:\\\"longitude\\\";d:99.4167;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-05 09:08:36.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"disposedToType\\\";E:50:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:CAMAT\\\";s:12:\\\"disposedToId\\\";i:43;s:14:\\\"previousStatus\\\";E:62:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIVERIFIKASI\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785899657,\"delay\":null}', 0, NULL, 1785899657, 1785899657),
(20, 'default', '{\"uuid\":\"db083b1c-1011-4235-ae7a-eb1d62b1dee7\",\"displayName\":\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintHandled\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":18:{s:5:\\\"event\\\";O:55:\\\"App\\\\Infrastructure\\\\Broadcasting\\\\Events\\\\ComplaintHandled\\\":2:{s:9:\\\"complaint\\\";O:39:\\\"App\\\\Domain\\\\Complaint\\\\Entities\\\\Complaint\\\":15:{s:2:\\\"id\\\";i:1;s:12:\\\"ticketNumber\\\";O:46:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\\":2:{s:52:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000year\\\";i:2026;s:56:\\\"\\u0000App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TicketNumber\\u0000sequence\\\";i:1;}s:6:\\\"userId\\\";i:1;s:5:\\\"title\\\";s:32:\\\"Jalan Rusak di Depan Kantor Desa\\\";s:11:\\\"description\\\";s:100:\\\"Jalan berlubang cukup dalam dan membahayakan pengendara motor, sudah terjadi sejak musim hujan lalu.\\\";s:8:\\\"category\\\";s:13:\\\"Infrastruktur\\\";s:10:\\\"targetType\\\";E:48:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\TargetType:OPD\\\";s:8:\\\"targetId\\\";i:1;s:6:\\\"status\\\";E:65:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DITINDAKLANJUTI\\\";s:8:\\\"latitude\\\";d:0.5333;s:9:\\\"longitude\\\";d:99.4167;s:15:\\\"rejectionReason\\\";N;s:9:\\\"createdAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-07-21 14:06:05.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"updatedAt\\\";O:17:\\\"DateTimeImmutable\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-08-05 10:14:17.000000\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"Asia\\/Jakarta\\\";}s:9:\\\"deletedAt\\\";N;}s:14:\\\"previousStatus\\\";E:58:\\\"App\\\\Domain\\\\Complaint\\\\ValueObjects\\\\ComplaintStatus:DIPROSES\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:13:\\\"debounceOwner\\\";s:0:\\\"\\\";s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1785900971,\"delay\":null}', 0, NULL, 1785900971, 1785900971);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kecamatans`
--

CREATE TABLE `kecamatans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kecamatans`
--

INSERT INTO `kecamatans` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(20, 'Panyabungan', 'PYB', '2025-10-29 07:27:45', '2025-10-29 16:48:39'),
(21, 'Panyabungan Timur', 'PAT', '2025-10-29 16:46:34', '2025-10-29 16:49:01'),
(22, 'Panyabungan Barat', 'PAB', '2025-10-29 16:46:43', '2025-10-29 16:47:53'),
(23, 'Panyabungan Utara', 'PAU', '2025-10-29 16:49:23', '2025-10-29 16:49:23'),
(24, 'Bukit Malintang', 'BML', '2025-10-29 16:49:57', '2025-10-29 16:49:57'),
(25, 'Siabu', 'SAB', '2025-10-29 16:50:15', '2025-10-29 16:50:15'),
(26, 'Naga Juang', 'NGJ', '2025-10-29 16:50:35', '2025-10-29 16:50:35'),
(27, 'Hutabargot', 'HBG', '2025-10-29 16:51:10', '2025-10-29 16:51:10'),
(28, 'Lembah Sorik Marapi', 'LSM', '2025-10-29 16:51:30', '2025-10-29 16:51:30'),
(29, 'Puncak Sorik Marapi', 'PSM', '2025-10-29 16:51:46', '2025-10-29 16:51:46'),
(30, 'Panyabungan Selatan', 'PAS', '2025-10-29 16:52:02', '2025-10-29 16:52:02'),
(31, 'Kotanopan', 'KTN', '2025-10-29 16:52:14', '2025-10-29 16:52:14'),
(32, 'Natal', 'NTL', '2025-10-29 16:53:03', '2025-10-29 16:53:03'),
(33, 'Sinunukan', 'SNN', '2025-10-29 16:53:15', '2025-10-29 16:53:15'),
(34, 'Muara Batang Gadis', 'MBG', '2025-10-29 16:53:30', '2025-10-29 16:53:30'),
(35, 'Lingga Bayu', 'LGB', '2025-10-29 16:53:50', '2025-10-29 16:53:50'),
(36, 'Ranto Baek', 'RTB', '2025-10-29 16:54:08', '2025-10-29 16:54:08'),
(37, 'Batang Natal', 'BTN', '2025-10-29 16:54:24', '2025-10-29 16:54:24'),
(38, 'Muara Sipongi', 'MSP', '2025-10-29 16:54:55', '2025-10-29 16:54:55'),
(39, 'Pakantan', 'PKT', '2025-10-29 16:55:11', '2025-10-29 16:55:11'),
(40, 'Batahan', 'BTH', '2025-10-29 16:56:12', '2025-10-29 16:56:12'),
(41, 'Tambangan', 'TMB', '2025-10-29 16:56:42', '2025-10-29 16:56:42'),
(42, 'Ulu Pungkut', 'UPK', '2025-10-29 16:56:53', '2025-10-29 16:56:53'),
(43, 'Camat Demo 1', 'CD1', '2026-08-05 02:10:20', '2026-08-05 02:10:20');

-- --------------------------------------------------------

--
-- Table structure for table `manual_books`
--

CREATE TABLE `manual_books` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manual_books`
--

INSERT INTO `manual_books` (`id`, `file_path`, `original_name`, `file_size`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 'manual-books/4zBR7Nh1MiZXOynXg2CRLIXlvdmnpH2UWLhC3peX.pdf', 'MANUAL BOOK APLIKASI SIPAPA MADINA(Masyarakat).pdf', 1090915, 2, '2026-08-05 01:36:02', '2026-08-05 01:36:02');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_07_05_074550_create_permission_tables', 1),
(5, '2026_07_05_074551_create_opds_table', 1),
(6, '2026_07_05_074552_create_kecamatans_table', 1),
(7, '2026_07_05_074553_add_sippm_fields_to_users_table', 1),
(8, '2026_07_05_074554_create_complaints_table', 1),
(9, '2026_07_05_074555_create_complaint_attachments_table', 1),
(10, '2026_07_05_074556_create_complaint_status_histories_table', 1),
(11, '2026_07_05_074557_create_dispositions_table', 1),
(12, '2026_07_05_074558_create_complaint_handlings_table', 1),
(13, '2026_07_05_074559_create_complaint_responses_table', 1),
(14, '2026_07_05_074600_create_activities_table', 1),
(15, '2026_07_05_074601_create_activity_documentations_table', 1),
(16, '2026_07_05_074602_create_notifications_table', 1),
(17, '2026_07_05_074603_create_audit_logs_table', 1),
(18, '2026_07_05_081508_create_personal_access_tokens_table', 1),
(19, '2026_07_05_155055_create_desas_table', 1),
(20, '2026_07_09_200000_create_ttd_signatures_table', 1),
(21, '2026_07_09_210000_widen_nip_column_in_ttd_signatures_table', 1),
(22, '2026_07_09_220000_add_avatar_path_to_users_table', 1),
(23, '2026_07_09_230000_create_manual_books_table', 1),
(24, '2026_07_21_140000_create_complaint_categories_table', 1),
(25, '2026_07_30_100000_create_site_settings_table', 2),
(26, '2026_07_30_100100_add_cancellation_to_dispositions_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'user', 1),
(2, 'user', 2),
(2, 'user', 8),
(3, 'user', 3),
(3, 'user', 34),
(3, 'user', 35),
(3, 'user', 36),
(3, 'user', 37),
(3, 'user', 38),
(3, 'user', 39),
(3, 'user', 40),
(3, 'user', 41),
(3, 'user', 42),
(3, 'user', 43),
(3, 'user', 44),
(3, 'user', 45),
(3, 'user', 46),
(3, 'user', 48),
(3, 'user', 49),
(3, 'user', 50),
(3, 'user', 51),
(3, 'user', 52),
(3, 'user', 53),
(3, 'user', 54),
(3, 'user', 55),
(3, 'user', 56),
(3, 'user', 57),
(3, 'user', 58),
(3, 'user', 59),
(3, 'user', 60),
(3, 'user', 61),
(3, 'user', 62),
(3, 'user', 63),
(3, 'user', 64),
(3, 'user', 65),
(3, 'user', 66),
(3, 'user', 67),
(3, 'user', 68),
(3, 'user', 69),
(3, 'user', 70),
(3, 'user', 71),
(3, 'user', 72),
(3, 'user', 73),
(3, 'user', 74),
(4, 'user', 4),
(4, 'user', 9),
(4, 'user', 10),
(4, 'user', 13),
(4, 'user', 14),
(4, 'user', 15),
(4, 'user', 16),
(4, 'user', 17),
(4, 'user', 18),
(4, 'user', 19),
(4, 'user', 20),
(4, 'user', 21),
(4, 'user', 22),
(4, 'user', 23),
(4, 'user', 24),
(4, 'user', 25),
(4, 'user', 26),
(4, 'user', 27),
(4, 'user', 28),
(4, 'user', 29),
(4, 'user', 30),
(4, 'user', 31),
(4, 'user', 32),
(4, 'user', 33),
(5, 'user', 5),
(6, 'user', 6),
(7, 'user', 7);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `data`, `created_at`, `updated_at`) VALUES
(1, 2, 'Pengaduan baru masuk', 'Pengaduan PGD-2026-000001 telah diajukan dan menunggu verifikasi.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintSubmitted', 1, NULL, '2026-07-21 07:06:13', '2026-07-30 04:51:46'),
(2, 8, 'Pengaduan baru masuk', 'Pengaduan PGD-2026-000001 telah diajukan dan menunggu verifikasi.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintSubmitted', 0, NULL, '2026-07-21 07:06:13', '2026-07-21 07:06:13'),
(3, 2, 'Pengaduan baru masuk', 'Pengaduan PGD-2026-000002 telah diajukan dan menunggu verifikasi.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintSubmitted', 1, NULL, '2026-07-21 07:06:13', '2026-08-04 04:49:36'),
(4, 8, 'Pengaduan baru masuk', 'Pengaduan PGD-2026-000002 telah diajukan dan menunggu verifikasi.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintSubmitted', 0, NULL, '2026-07-21 07:06:13', '2026-07-21 07:06:13'),
(5, 1, 'Pengaduan Anda telah diverifikasi', 'Pengaduan PGD-2026-000002 telah diverifikasi dan akan didisposisikan.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintVerified', 0, NULL, '2026-07-30 04:49:21', '2026-07-30 04:49:21'),
(6, 2, 'Pengaduan telah ditindaklanjuti', 'Pengaduan PGD-2026-000002 telah ditindaklanjuti oleh unit terkait.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintHandled', 1, NULL, '2026-07-30 05:09:11', '2026-08-04 04:49:24'),
(7, 8, 'Pengaduan telah ditindaklanjuti', 'Pengaduan PGD-2026-000002 telah ditindaklanjuti oleh unit terkait.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintHandled', 0, NULL, '2026-07-30 05:09:11', '2026-07-30 05:09:11'),
(8, 1, 'Pengaduan Anda telah selesai', 'Pengaduan PGD-2026-000002 telah dijawab resmi oleh Kominfo.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintResolved', 0, NULL, '2026-07-30 05:11:49', '2026-07-30 05:11:49'),
(9, 1, 'Pengaduan Anda telah diverifikasi', 'Pengaduan PGD-2026-000001 telah diverifikasi dan akan didisposisikan.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintVerified', 0, NULL, '2026-07-30 05:14:53', '2026-07-30 05:14:53'),
(10, 2, 'Kegiatan dipublikasikan', 'Kegiatan \"Kebersihan\" telah dipublikasikan ke feed publik.', 'App\\Infrastructure\\Broadcasting\\Events\\ActivityPublished', 1, NULL, '2026-07-30 05:16:44', '2026-07-31 03:45:40'),
(11, 8, 'Kegiatan dipublikasikan', 'Kegiatan \"Kebersihan\" telah dipublikasikan ke feed publik.', 'App\\Infrastructure\\Broadcasting\\Events\\ActivityPublished', 0, NULL, '2026-07-30 05:16:44', '2026-07-30 05:16:44'),
(12, 2, 'Pengaduan baru masuk', 'Pengaduan PGD-2026-000003 telah diajukan dan menunggu verifikasi.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintSubmitted', 1, NULL, '2026-08-04 02:48:39', '2026-08-04 03:11:40'),
(13, 8, 'Pengaduan baru masuk', 'Pengaduan PGD-2026-000003 telah diajukan dan menunggu verifikasi.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintSubmitted', 0, NULL, '2026-08-04 02:48:39', '2026-08-04 02:48:39'),
(14, 1, 'Pengaduan Anda telah diverifikasi', 'Pengaduan PGD-2026-000003 telah diverifikasi dan akan didisposisikan.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintVerified', 0, NULL, '2026-08-04 07:23:07', '2026-08-04 07:23:07'),
(15, 38, 'Disposisi pengaduan dibatalkan', 'Disposisi pengaduan PGD-2026-000001 ke unit Anda telah dibatalkan oleh Kominfo.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDispositionCancelled', 0, NULL, '2026-08-05 02:08:36', '2026-08-05 02:08:36'),
(16, 2, 'Pengaduan telah ditindaklanjuti', 'Pengaduan PGD-2026-000003 telah ditindaklanjuti oleh unit terkait.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintHandled', 1, NULL, '2026-08-05 02:48:40', '2026-08-05 03:14:28'),
(17, 8, 'Pengaduan telah ditindaklanjuti', 'Pengaduan PGD-2026-000003 telah ditindaklanjuti oleh unit terkait.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintHandled', 0, NULL, '2026-08-05 02:48:40', '2026-08-05 02:48:40'),
(18, 4, 'Pengaduan baru didisposisikan', 'Pengaduan PGD-2026-000001 didisposisikan ke unit Anda.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintDisposed', 0, NULL, '2026-08-05 03:14:17', '2026-08-05 03:14:17'),
(19, 2, 'Pengaduan telah ditindaklanjuti', 'Pengaduan PGD-2026-000001 telah ditindaklanjuti oleh unit terkait.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintHandled', 0, NULL, '2026-08-05 03:36:11', '2026-08-05 03:36:11'),
(20, 8, 'Pengaduan telah ditindaklanjuti', 'Pengaduan PGD-2026-000001 telah ditindaklanjuti oleh unit terkait.', 'App\\Infrastructure\\Broadcasting\\Events\\ComplaintHandled', 0, NULL, '2026-08-05 03:36:11', '2026-08-05 03:36:11');

-- --------------------------------------------------------

--
-- Table structure for table `opds`
--

CREATE TABLE `opds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opds`
--

INSERT INTO `opds` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(3, 'DINAS KOMUNIKASI DAN INFORMATIKA', 'DISKOMINFO', '2025-10-29 18:16:57', '2025-10-29 20:43:52'),
(4, 'SEKRETARIAT DAERAH KABUPATEN', 'SETDA', '2025-10-29 20:43:11', '2025-10-29 20:43:11'),
(5, 'DINAS PENDIDIKAN DAN KEBUDAYAAN', 'DISDIKBUD', '2025-10-29 20:46:44', '2025-10-29 20:46:44'),
(6, 'DINAS KESEHATAN', 'DINKES', '2025-10-29 20:47:15', '2025-10-29 20:47:15'),
(7, 'DINAS PEKERJAAN UMUM DAN PENATAAN RUANG', 'DISPUPR', '2025-10-29 20:47:31', '2025-10-29 20:47:31'),
(8, 'DINAS PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN SERTA PERTANAHAN', 'DISPERKIM', '2025-10-29 20:48:26', '2025-10-29 20:48:26'),
(9, 'SATUAN POLISI PAMONG PRAJA DAN PEMADAM KEBAKARAN', 'SATPOLPP', '2025-10-29 20:48:49', '2025-10-29 20:48:49'),
(10, 'DINAS SOSIAL, PEMBERDAYAAN PEREMPUAN DAN PERLIDUNGAN ANAK', 'DINSOSPPPA', '2025-10-29 20:49:05', '2025-10-29 20:49:05'),
(11, 'DINAS KOPERASI, USAHA KECIL DAN MENENGAH', 'DISKOPUKM', '2025-10-29 20:49:18', '2025-10-29 20:49:18'),
(12, 'DINAS TENAGA KERJA', 'DISNAKER', '2025-10-29 20:49:29', '2025-10-29 20:49:29'),
(13, 'DINAS PENGENDALIAN PENDUDUK DAN KELUARGA BERENCANA', 'DPPKB', '2025-10-29 20:49:40', '2025-10-29 20:49:40'),
(14, 'DINAS KETAHANAN PANGAN', 'DKP', '2025-10-29 20:49:53', '2025-10-29 20:49:53'),
(15, 'DINAS PERTANIAN', 'DISTAN', '2025-10-29 20:50:04', '2025-10-29 20:50:04'),
(16, 'DINAS LINGKUNGAN HIDUP', 'DLH', '2025-10-29 20:50:15', '2025-10-29 20:50:15'),
(17, 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL', 'DISDUKCAPIL', '2025-10-29 20:50:24', '2025-10-29 20:50:24'),
(18, 'DINAS PEMBERDAYAAN MASYARAKAT DAN DESA', 'DPMD', '2025-10-29 20:50:38', '2025-10-29 20:50:38'),
(19, 'DINAS PERHUBUNGAN', 'DISHUB', '2025-10-29 20:50:48', '2025-10-29 20:50:48'),
(20, 'DINAS PERPUSTAKAAN DAN KEARSIPAN', 'DISPUSTAKA', '2025-10-29 20:51:00', '2025-10-29 20:51:00'),
(21, 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU', 'DPMPTSP', '2025-10-29 20:51:25', '2025-10-29 20:51:25'),
(22, 'DINAS PEMUDA DAN OLAHRAGA', 'DISPORA', '2025-10-29 20:51:47', '2025-10-29 20:51:47'),
(23, 'DINAS PERIKANAN', 'DISKAN', '2025-10-29 20:52:12', '2025-10-29 20:52:12'),
(24, 'DINAS PARIWISATA', 'DISPAR', '2025-10-29 20:52:24', '2025-10-29 20:52:24'),
(25, 'DINAS PERDAGANGAN', 'DISDAG', '2025-10-29 20:52:34', '2025-10-29 20:52:34'),
(26, 'BADAN PERENCANAAN PEMBANGUNAN, RISET DAN INOVASI DAERAH', 'BAPPERIDA', '2025-10-29 20:52:47', '2025-10-29 20:52:47'),
(27, 'BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH', 'BPKAD', '2025-10-29 20:52:56', '2025-10-29 20:52:56'),
(28, 'BADAN PENDAPATAN DAERAH', 'BAPENDA', '2025-10-29 20:53:12', '2025-10-29 20:53:12'),
(29, 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA', 'BKPSDM', '2025-10-29 20:53:23', '2025-10-29 20:53:23'),
(30, 'BADAN PENANGGULANGAN BENCANA DAERAH', 'BPBD', '2025-10-29 20:53:34', '2025-10-29 20:53:34'),
(31, 'BADAN KESATUAN BANGSA DAN POLITIK', 'KESBANGPOL', '2025-10-29 20:53:45', '2025-10-29 20:53:45'),
(32, 'INSPEKTORAT DAERAH KABUPATEN', 'INSPEKTORAT', '2025-10-29 20:56:14', '2025-10-29 20:56:14'),
(33, 'RUMAH SAKIT UMUM DAERAH PANYABUNGAN', 'RSUDP', '2025-10-30 20:59:50', '2025-10-30 20:59:50'),
(34, 'RUMAH SAKIT UMUM DAERAH HUSNI THAMRIN NATAL', 'RSUDN', '2025-10-30 21:00:28', '2025-10-30 21:00:28'),
(35, 'SEKRETARIAT DPRD', 'SETWAN', '2025-10-30 21:01:14', '2026-08-04 02:28:24'),
(36, 'BAGIAN TATA PEMERINTAHAN', 'TAPEM', '2026-07-31 03:20:22', '2026-07-31 03:20:22'),
(37, 'BAGIAN HUKUM', 'HUKUM', '2026-07-31 03:20:49', '2026-07-31 03:20:49'),
(38, 'BAGIAN KESEJAHTERAAN RAKYAT', 'KESRA', '2026-07-31 03:21:27', '2026-07-31 03:21:27'),
(39, 'BAGIAN PEREKONOMIAN DAN SUMBER DAYA ALAM', 'PEREKONOMIAN', '2026-07-31 03:22:16', '2026-07-31 03:22:37'),
(40, 'BAGIAN ADMINISTRASI PEMBANGUNAN', 'ADPEM', '2026-07-31 03:23:49', '2026-07-31 03:23:49'),
(41, 'BAGIAN PENGADAAN BARANG DAN JASA', 'BPBJ', '2026-07-31 03:24:34', '2026-07-31 03:24:34'),
(42, 'BAGIAN UMUM', 'UMUM', '2026-07-31 03:24:48', '2026-07-31 03:24:48'),
(43, 'BAGIAN ORGANISASI', 'ORTA', '2026-07-31 03:25:03', '2026-07-31 03:25:03'),
(44, 'OPD DEMO', 'OPD', '2026-08-05 02:09:47', '2026-08-05 02:09:47');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'registrasi_login', 'web', '2026-07-21 07:05:53', '2026-07-21 07:05:53'),
(2, 'buat_pengaduan', 'web', '2026-07-21 07:05:53', '2026-07-21 07:05:53'),
(3, 'verifikasi_pengaduan', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(4, 'disposisi_pengaduan', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(5, 'menangani_pengaduan', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(6, 'kirim_hasil_penanganan', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(7, 'menjawab_masyarakat', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(8, 'input_kegiatan', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(9, 'verifikasi_publikasi_kegiatan', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(10, 'melihat_statistik', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(11, 'monitoring_kinerja', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(12, 'lihat_laporan_kegiatan', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(13, 'kelola_pengguna', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54'),
(14, 'lihat_audit_log', 'web', '2026-07-21 07:05:54', '2026-07-21 07:05:54');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'masyarakat', 'web', '2026-07-21 07:05:52', '2026-07-21 07:05:52'),
(2, 'kominfo', 'web', '2026-07-21 07:05:52', '2026-07-21 07:05:52'),
(3, 'opd', 'web', '2026-07-21 07:05:52', '2026-07-21 07:05:52'),
(4, 'camat', 'web', '2026-07-21 07:05:52', '2026-07-21 07:05:52'),
(5, 'bupati', 'web', '2026-07-21 07:05:52', '2026-07-21 07:05:52'),
(6, 'wakil_bupati', 'web', '2026-07-21 07:05:52', '2026-07-21 07:05:52'),
(7, 'sekda', 'web', '2026-07-21 07:05:52', '2026-07-21 07:05:52');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(2, 1),
(3, 2),
(4, 2),
(5, 3),
(5, 4),
(6, 3),
(6, 4),
(7, 2),
(8, 3),
(8, 4),
(9, 2),
(10, 2),
(10, 3),
(10, 4),
(10, 5),
(10, 6),
(10, 7),
(11, 2),
(11, 5),
(11, 6),
(11, 7),
(12, 2),
(12, 3),
(12, 4),
(12, 5),
(12, 6),
(12, 7),
(13, 2),
(14, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('01GG6Zd564JkYw4o1MyU7rl67FNmPe5l4IClOG1D', NULL, '104.23.175.33', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', 'eyJfdG9rZW4iOiJxMEg1Ym92S1kzRTFLTndqb21ZYmJGZUE3SDR5MnlhQ1JIRXBqYlYxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786100964),
('0gFRhKWCjg0DyCKn2hfllR2J4dzEiR5pINdRsx55', NULL, '172.71.124.80', 'Mozilla/5.0', 'eyJfdG9rZW4iOiJFaGFCOTh3R3JXR3UxY3o2RDJ0N0xQRnVvVlNLMHVnTEVmTlZUOTZ4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786065282),
('0N3WGckvBGx6vkkeE7Ei4FD10OLG1G0uYrAb9rcf', NULL, '162.158.162.160', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', 'eyJfdG9rZW4iOiJtbkpPM01RMm9GYkNWaVlqOUN2SFFzNmUxU3FIbURib01XM1ZRTnlzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786268348),
('4uk3PR9WVVTHnbJNZ5X7rk2eCR6m4HezxizgC78s', NULL, '162.158.41.167', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'eyJfdG9rZW4iOiI3TzE1NUZscjc1ckw4NXllM1M2NVdacWRXTGxnTFpNRk84alhrd3puIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786100973),
('7wPUD2D4jpeK1zDc1f9JrP4xONgwRfGnp4iTzXJf', NULL, '172.68.164.139', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJIVjFDd095UnFLVFNLTDA3bUxNdDdPR0tJUmYxRmtoc0xiR0Eyb1FSIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786279263),
('8lV858MxNsCuYhJ6KXsulDdJplLAFhNpTUDsH0iR', NULL, '172.68.164.156', 'python-requests/2.34.2', 'eyJfdG9rZW4iOiJ3bDZHelE2d1RPMUpKS1pYR1NZbk9PeUFUZzZDUzRPblpQUWw1RVFiIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786045144),
('aFz2DVuIWsDGH2WiDKOGGLYXP2xx9sp1HdJNxuYL', NULL, '104.23.175.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJlT0VTcUNtTml5S1kzbHg2TkVhWWlNQkxoMFYyY0t6NVI4YzBVNm0yIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL3NpbGFwZ2F3YXQubWFkaW5hLmdvLmlkIiwicm91dGUiOm51bGx9fQ==', 1785999823),
('CQHds9sQSlhVyRtYG4cbQIPRj6v1UVxaFBj5xE1H', NULL, '172.70.142.149', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJwbWJtM2pUbmZHWEhMU3ZDS3hsbjVzaEV5Nk9MVnJLdDhUT0lXelZaIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786089698),
('GH8a9fzzEiEdHqTcemManzdgw4nZTc3raHiKRTxf', NULL, '172.70.142.149', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJ5VnFlWG5SdkVaNld4ZE03ckd4blU5dmIwSHA4QlpicHczQ2dsejRWIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786276790),
('nYaAhmB3aMFwghRfnecFTMcSIC7BF7n9TU7eVQfl', NULL, '104.22.66.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJtdjY1M0J5NmFOUmVpSUZESDhHam9kMGgwcDQ1MHR2c0RpSzJOd1ZRIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786279660),
('oXkunU7DRFUkU7hDNrUVB0xXNuES7IN0keVqw4ht', NULL, '162.158.243.231', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', 'eyJfdG9rZW4iOiJWbHJTclh5WVBJelp6RTBlTHFJaGtMaDQ0bXpHejFjTzR4aGNZaXJCIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786100964),
('RMrpbCiHOz92K1FWsQxTkzibDGY2ZciE77c7h0u2', NULL, '172.69.166.117', 'Mozilla/5.0', 'eyJfdG9rZW4iOiJQeEthMUlHWjR1Nm1OMkJmbTVheWVQUXhqQnJOTHFEQnpEZThZSnN1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786065274),
('T1PVopMtZ8KRdFAX9lo6xU1lpNi1RKAlvPjcfa1p', NULL, '108.162.245.64', 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)', 'eyJfdG9rZW4iOiJsNHd1WHNjNjVKQkhEME8wMUJQMUYyZzduTGh6b21QVU5yU21EUHQzIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786100966),
('uAqBRLqnyliSYOxBGurdlHjMIr6xyrQlUlIoPbDj', NULL, '104.22.66.239', 'Mozilla/5.0 (iPad; CPU OS 26_5_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/151.0.7922.57 Mobile/15E148 Safari/604.1', 'eyJfdG9rZW4iOiJJandmamZUWFZoWkNBc3Qxc1gzYWJvRzJpb0Yya0hhWE54Q281U2JPIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1785989606),
('wqhS04oYhGWtOpEG4tzMw1G4kp1y1YclNtbTEnfD', NULL, '172.70.208.157', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Mobile Safari/537.36', 'eyJfdG9rZW4iOiJIdTBKOTBVM2FIU0JsUjVTUzJsV1JGVVFJVmM3TUdOdUdybEdDRUl6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786251074),
('xVgYFS6oHXjTGMFLgo0Q7y83GiRp9vzCy8bTWOsM', NULL, '104.22.24.170', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 (compatible; meta-externalagent/1.1 (+https://developers.facebook.com/docs/sharing/webmasters/crawler))', 'eyJfdG9rZW4iOiJ2Y0ZsdnZ3b0dQeDJiSnlxbzFYdng0alFVWDlFRXVnU2pvWm9ZemhWIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9zaWxhcGdhd2F0Lm1hZGluYS5nby5pZCIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1786211224);

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hero_image_path` varchar(255) DEFAULT NULL,
  `hero_caption` varchar(255) DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `hero_image_path`, `hero_caption`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'site/j0easStE94D0ZkW7iFQveY1d3mjQgF8XBo436opN.png', NULL, 2, '2026-07-30 05:05:03', '2026-07-30 10:53:19');

-- --------------------------------------------------------

--
-- Table structure for table `ttd_signatures`
--

CREATE TABLE `ttd_signatures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_penandatangan` varchar(255) NOT NULL,
  `jabatan_penandatangan` varchar(255) NOT NULL,
  `pangkat` varchar(255) DEFAULT NULL,
  `nip` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ttd_signatures`
--

INSERT INTO `ttd_signatures` (`id`, `nama_penandatangan`, `jabatan_penandatangan`, `pangkat`, `nip`, `created_at`, `updated_at`) VALUES
(1, 'MUHAMMAD SYAIL LUBIS, ST, M.M.', '<p>KEPALA DINAS KOMUNIKASI </p><p><span style=\"color: rgb(0, 0, 0);\">Plt.</span>DAN INFORMASI </p><p>KABUPATEN MANDAILING NATAL</p>', 'PEMBINA', '19793019 200502 1 002', '2026-07-31 03:40:07', '2026-07-31 09:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `nik` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `consent_at` timestamp NULL DEFAULT NULL,
  `opd_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kecamatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `nik`, `phone`, `is_active`, `consent_at`, `opd_id`, `kecamatan_id`, `avatar_path`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Masyarakat Demo', 'masyarakat@gmail.com', '1305010101010001', NULL, 1, '2026-07-21 07:05:57', NULL, NULL, 'avatars/ar6SYVzYPiqkCcBDGnTLeOd4ZyoM8RahC1smwoIj.jpg', '2026-07-21 07:05:57', '$2y$12$vfJV0we717Nu4cvEGeh/R.EbZhBjHq5FserEWVS6qbEssAsoqGJ.O', NULL, '2026-07-21 07:05:57', '2026-07-30 13:28:24'),
(2, 'Kominfo Demo', 'kominfo@gmail.com', NULL, NULL, 1, '2026-07-21 07:05:58', NULL, NULL, 'avatars/eYTrd8JowM2uarZh8Uyd21qctNpyD5SPOVRYaXkv.jpg', '2026-07-21 07:05:58', '$2y$12$0zGYn4PAdwtLlIMkLVVraumVcIKtx1NBpSOx/BuAXkDNRbpbeveTa', NULL, '2026-07-21 07:05:58', '2026-07-30 04:51:27'),
(3, 'Opd Demo', 'opd@gmail.com', NULL, NULL, 1, '2026-07-21 07:05:59', 44, NULL, NULL, '2026-07-21 07:05:59', '$2y$12$EOAxCwu0btbRffG5viGr2uyqT0LH7z2HrcAZ5PQ6t5WG2B4HaBefO', NULL, '2026-07-21 07:05:59', '2026-08-05 02:17:03'),
(4, 'Camat Demo', 'camat@gmail.com', NULL, NULL, 1, '2026-07-21 07:06:00', NULL, 43, NULL, '2026-07-21 07:06:00', '$2y$12$O2xjouEKowz3cJIgJuMA2O7kc6261nQO.mYim0ztxmZhCN2NbLM8G', NULL, '2026-07-21 07:06:00', '2026-08-05 02:17:38'),
(5, 'Bupati Demo', 'bupati@gmail.com', NULL, NULL, 1, '2026-07-21 07:06:02', NULL, NULL, NULL, '2026-07-21 07:06:02', '$2y$12$HBdG.ud9Juc4TUl4DUx9RuipoF.V4UhiH.0fwAfrhHR7.NFOqYVp6', NULL, '2026-07-21 07:06:02', '2026-07-21 07:06:02'),
(6, 'Wakil Bupati Demo', 'wakil_bupati@gmail.com', NULL, NULL, 1, '2026-07-21 07:06:03', NULL, NULL, NULL, '2026-07-21 07:06:03', '$2y$12$iPFlrBBUBsslSMAVTKzGKeFOR.LmX3N11Fl93.AGrXqG8iC.8EAKa', NULL, '2026-07-21 07:06:03', '2026-07-21 07:06:03'),
(7, 'Sekda Demo', 'sekda@gmail.com', NULL, NULL, 1, '2026-07-21 07:06:04', NULL, NULL, NULL, '2026-07-21 07:06:04', '$2y$12$JsiqOuaaXkpu20sj/rnfROuTGtCqiUhb/7QnoxXzLSYFWnRbrcMfW', NULL, '2026-07-21 07:06:04', '2026-07-21 07:06:04'),
(8, 'Administrator Kominfo', 'admin@gmail.com', NULL, NULL, 1, '2026-07-21 07:06:04', NULL, NULL, NULL, '2026-07-21 07:06:04', '$2y$12$RlJeC/PnSNL1EggYaIVhEOMI0cvAQH6ELF2UmoWHox3okgmEr7Ski', NULL, '2026-07-21 07:06:04', '2026-07-21 07:06:04'),
(9, 'Kecamatan Panyabungan', 'kec-panyabungan@mail.madina.go.id', '000009', '000008', 1, NULL, NULL, 20, NULL, NULL, '$2y$12$Sf0q/LvTZI1hAyO068KoQOMUoFgJgf6SF./K1WWWNG67WrHLNvMGG', NULL, '2026-07-30 05:01:41', '2026-07-31 03:30:11'),
(10, 'Kecamatan Batahan', 'kec-batahan@mail.madina.go.id', '00000', '0000', 1, NULL, NULL, 40, NULL, NULL, '$2y$12$BeSakPb8aG1Z0mNW397gxeH/CRpgkRtbLCl798SDzDF4qCGH3HJfy', NULL, '2026-07-31 02:48:41', '2026-07-31 03:30:36'),
(13, 'Kecamatan Panyabungan Utara', 'kec-pybutara@mail.madina.go.id', '00002', '0000000', 1, NULL, NULL, 23, NULL, NULL, '$2y$12$5kh4cho2lqcVJpsiExpMoOQaDe1XdkG6CL/yJvXN4qPV/aPK0D3ZG', NULL, '2026-07-31 03:10:32', '2026-07-31 03:10:32'),
(14, 'Kecamatan Panyabungan Barat', 'kec-pybbarat@mail.madina.go.id', '00003', '000000', 1, NULL, NULL, 22, NULL, NULL, '$2y$12$a0x6rkrNoj689jRNIvzxMulw4d.nddKGybOmtp5/qFPhrG6H2PhCC', NULL, '2026-07-31 03:14:35', '2026-07-31 03:14:35'),
(15, 'Kecamatan Panyabungan Selatan', 'kec-pybselatan@mail.madina.go.id', '000005', '00000004', 1, NULL, NULL, 30, NULL, NULL, '$2y$12$PZ3eYJCAVkooJBoRfA.aZu8vABD2Cr8GzRId7MLmahoiPJzOB0kVW', NULL, '2026-07-31 03:18:31', '2026-07-31 03:18:31'),
(16, 'Kecamatan Panyabungan Timur', 'kec-pybtimur@mail.madina.go.id', '0000006', '000004', 1, NULL, NULL, 21, NULL, NULL, '$2y$12$zPcOpNO7oMOrKuG/sEyBgOCZRbPTqQ8tDO9ZVuvz/qeplC3VwAYJu', NULL, '2026-07-31 03:22:48', '2026-07-31 03:22:48'),
(17, 'Kecamatan Siabu', 'kec-siabu@mail.madina.go.id', '000007', '00000005', 1, NULL, NULL, 25, NULL, NULL, '$2y$12$Akz5xoMwtQwDXxPxxy6yiOHXkVG8fzKXYMjJb8eOesH80yPV0QaHK', NULL, '2026-07-31 03:25:15', '2026-07-31 03:25:15'),
(18, 'Kecamatan Bukit Malintang', 'kec-malintang@mail.madina.go.id', '00008', '00000006', 1, NULL, NULL, 24, NULL, NULL, '$2y$12$6Exg4.bYGYOcJIFSJ2EkiOAn6eHe2yNE892PX6Z5QPVvAN4wUnwly', NULL, '2026-07-31 03:27:58', '2026-07-31 03:27:58'),
(19, 'Kecamatan Naga Juang', 'kec-nagajuang@mail.madina.go.id', '0000010', '000007', 1, NULL, NULL, 26, NULL, NULL, '$2y$12$5i.ZD4AyMskYeiLSJwnc3et1/vIGR.cLkpR697qysXBYIPJcsvCjC', NULL, '2026-07-31 03:30:15', '2026-07-31 03:30:15'),
(20, 'Kecamtan Huta Bargot', 'kec-hutabargot@mail.madina.go.id', '0000011', '000009', 1, NULL, NULL, 27, NULL, NULL, '$2y$12$rPWAHWSk/XUScFy8t389FOOALQZTrxgej9zxSfJPHAQE288koKpO2', NULL, '2026-07-31 03:32:42', '2026-07-31 03:32:42'),
(21, 'Kecamatan Puncak Sorik Marapi', 'kec-puncaksm@mail.madina.go.id', '000000012', '0000010', 1, NULL, NULL, 28, NULL, NULL, '$2y$12$AIRs4q8ucf2FthGODpx6cuJkDVQpl5R3rOsGRUlu9Q5/gT6iVoxPS', NULL, '2026-07-31 03:34:37', '2026-07-31 03:34:37'),
(22, 'Kecamatan Lembah Sorik Marapi', 'kec-lembahsm@mail.madina.go.id', '00000013', '0000012', 1, NULL, NULL, 28, NULL, NULL, '$2y$12$LTTsOPgtunV2etuVKHT5D.Wdw3gwk1JiKSWTCIa7TLOHUzA7SUe8u', NULL, '2026-07-31 03:36:06', '2026-07-31 03:36:06'),
(23, 'Kecamatan Tambangan', 'kec-tambangan@mail.madina.go.id', '0000014', '0000013', 1, NULL, NULL, 41, NULL, NULL, '$2y$12$O.W3Qy48FVhAVHrAQu2P2ecKb0ECE8Ps8ILvroITcMED86KpXZoNS', NULL, '2026-07-31 03:40:22', '2026-07-31 03:40:22'),
(24, 'Kecamatan Kotanopan', 'kec-kotanopan@mail.madina.go.id', '0000015', '0000014', 1, NULL, NULL, 31, NULL, NULL, '$2y$12$W524y1l.VlhtEOLnFC8UW.cgNTqqPlbe2exqnaKhdF88iLr77vAfO', NULL, '2026-07-31 03:42:34', '2026-07-31 03:42:34'),
(25, 'Kecamatan Muarasipongi', 'kec-muarasipongi@mail.madina.go.id', '0000016', '0000015', 1, NULL, NULL, 38, NULL, NULL, '$2y$12$CdKAqgmzJkPKuLhvZcSt9Oj30wP7C8Awiwknnk4ZTXNUejzPlO2b2', NULL, '2026-07-31 03:44:06', '2026-07-31 03:44:06'),
(26, 'Kecamatan Pakantan', 'kec-pakantan@mail.madina.go.id', '00000017', '0000016', 1, NULL, NULL, 39, NULL, NULL, '$2y$12$aWoNv9Zr.DUoVkhPG0u69eQdKjZZPF3kzVQDhZriZ1pJM/nkvq7WK', NULL, '2026-07-31 03:45:30', '2026-07-31 03:45:30'),
(27, 'Kecamatan Ulu Pungkut', 'kec-ulupungkut@mail.madina.go.id', '00000018', '0000017', 1, NULL, NULL, 42, NULL, NULL, '$2y$12$hCsJvfP4E.x9j5vJUax9WO2X9eSPgwrcnxcFX.r6/2IkHJxDmBtVy', NULL, '2026-07-31 03:47:05', '2026-07-31 03:47:05'),
(28, 'Kecamatan Batang Natal', 'kec-batangnatal@mail.madina.go.id', '0000019', '0000018', 1, NULL, NULL, 37, NULL, NULL, '$2y$12$YV4gX32SsxG10h0bUUGc4u/zdQ6MrcWuJhl9wT8jykzxb0Rtmq1k2', NULL, '2026-07-31 03:48:30', '2026-07-31 03:48:30'),
(29, 'Kecamartan Lingga Bayu', 'kec-linggabayu@mail.madina.go.id', '00000020', '0000019', 1, NULL, NULL, 35, NULL, NULL, '$2y$12$/7/011Vcx6YEhHJx8fqjTuH1YF8AL6Uz4c22QlWh70wwRXdjqwMBO', NULL, '2026-07-31 03:50:03', '2026-07-31 03:50:03'),
(30, 'Kecamatan Ranto Baek', 'kec-rantobaek@mail.madina.go.id', '00000021', '0000020', 1, NULL, NULL, 36, NULL, NULL, '$2y$12$0WqQPk.JYhyUWQh.V0e1Ue8FfrFtB0X8ukoiXDPjMNbYve/9O2/Gy', NULL, '2026-07-31 03:51:56', '2026-07-31 03:51:56'),
(31, 'Kecamatan Natal', 'kec-natal@mail.madina.go.id', '00000022', '0000021', 1, NULL, NULL, 32, NULL, NULL, '$2y$12$pLufEX4F5.lLBMvZvWu4M.DQMscwULwmdp/R1Yx.4jk14wGsRcrjS', NULL, '2026-07-31 03:53:44', '2026-07-31 03:53:44'),
(32, 'Kecamatan Sinunukan', 'kec-sinunukan@mail.madina.go.id', '00000023', '0000022', 1, NULL, NULL, 33, NULL, NULL, '$2y$12$GLdG/.6tWT.t3kH2CMy1Q.6wKWk0aJh1w8/miEdJFofvIl4SDaXGG', NULL, '2026-07-31 03:58:00', '2026-07-31 03:58:00'),
(33, 'Kecamatan Muara Batang Gadis', 'kec-mbg@mail.madina.go.id', '000000024', '0000023', 1, NULL, NULL, 34, NULL, NULL, '$2y$12$qPmU5p5jVLPcX0p.Q5V/qOq1LHTTesK.7HDGQVsgTBZXA80A7..0W', NULL, '2026-07-31 04:02:09', '2026-07-31 04:02:09'),
(34, 'SEKRETARIAT DAERAH KABUPATEN', 'setda@mail.madina.go.id', '00001', '000002', 1, NULL, 4, NULL, NULL, NULL, '$2y$12$uMLdDrP4r.9NIFIhqZpJgeWvVyt.wN6pr4ZZ9/eaLlgBVCqAP/fXm', NULL, '2026-07-31 04:16:35', '2026-07-31 04:16:35'),
(35, 'INSPEKTORAT DAERAH KABUPATEN', 'inspektorat@mail.madina.go.id', '000002', '000003', 1, NULL, 32, NULL, NULL, NULL, '$2y$12$glBrOGiX7Txk9UPbJ/Ar3eb.WiCwjIjFmgZvpI8BSknjLa4j/UDyq', NULL, '2026-07-31 04:22:17', '2026-07-31 04:22:17'),
(36, 'DINAS PENDIDIKAN DAN KEBUDAYAAN', 'disdik@mail.madina.go.id', '00004', '0000011', 1, NULL, 5, NULL, NULL, NULL, '$2y$12$wMcO.l177A3Y8vTpGfv6r.d9vYc397gF72gYGsS.VyO6DVM7scz5G', NULL, '2026-07-31 04:27:24', '2026-07-31 04:27:24'),
(37, 'DINAS KESEHATAN', 'dinkes@mail.madina.go.id', '0000022', '000021', 1, NULL, 6, NULL, NULL, NULL, '$2y$12$NYYx9fOK/yd.BWFz1r./Zes0PJFjEIaXL4P4nLq/cDQI71TVGWcOi', NULL, '2026-07-31 04:30:36', '2026-07-31 04:30:36'),
(38, 'DINAS PEKERJAAN UMUM DAN PENATAAN RUANG', 'pupr@mail.madina.go.id', '000006', '0000033', 1, NULL, 7, NULL, NULL, NULL, '$2y$12$e3YY1ZGhecvigrnxddtC5eTEDr94ZVEnyfjjhcjuKUY899fyNFtwq', NULL, '2026-07-31 04:34:15', '2026-07-31 04:34:15'),
(39, 'DINAS PERUMAHAN RAKYAT DAN KAWASAN PERMUKIMAN SERTA PERTANAHAN', 'perkimtan@mail.madina.go.id', '0000012', '0000032', 1, NULL, 8, NULL, NULL, NULL, '$2y$12$AqLMMI0PFzdKWxuemXFrAO9zD6EksNATK.xBpOQmLuQT/bjjqTY0S', NULL, '2026-07-31 04:36:34', '2026-07-31 04:36:34'),
(40, 'SATUAN POLISI PAMONG PRAJA DAN PEMADAM KEBAKARAN', 'satpol@mail.madina.go.id', '0000050', '000034', 1, NULL, 9, NULL, NULL, NULL, '$2y$12$oIHZ4uSbw0jMRK2b2mZyG.n7XypQKpqyLI9Gb6kyAd2Cz/dzXXgG.', NULL, '2026-07-31 04:38:40', '2026-07-31 04:38:40'),
(41, 'DINAS SOSIAL, PEMBERDAYAAN PEREMPUAN DAN PERLIDUNGAN ANAK', 'dinsosp3a@mail.madina.go.id', '000060', '0000035', 1, NULL, 10, NULL, NULL, NULL, '$2y$12$t0.qIfbZyTCCDrpTZKu.pucwqpvTcSXQ4.31lFEEp4DK26wq2BteW', NULL, '2026-07-31 04:40:12', '2026-07-31 04:40:12'),
(42, 'DINAS KOPERASI, USAHA KECIL DAN MENENGAH', 'diskopukm@mail.madina.go.id', '0000061', '0000036', 1, NULL, 11, NULL, NULL, NULL, '$2y$12$T2j6F4IOLs91BBBRcVee4emZgKreGgA5tdOGy8HrW8bZO25izGQt6', NULL, '2026-07-31 04:41:30', '2026-07-31 04:41:30'),
(43, 'DINAS TENAGA KERJA', 'disnaker@mail.madina.go.id', '000062', '0000038', 1, NULL, 12, NULL, NULL, NULL, '$2y$12$Qx2sRFRDBodYlAEDBGSXkOxcEElhEcaLKOd3atROB7fYAQILk8yMG', NULL, '2026-07-31 04:43:12', '2026-07-31 04:43:12'),
(44, 'DINAS PENGENDALIAN PENDUDUK DAN KELUARGA BERENCANA', 'disp2kb@mail.madina.go.id', '000070', '0000048', 1, NULL, 13, NULL, NULL, NULL, '$2y$12$h7TP2FNehrCpDY3pq4MksOf1cpd1aiNMtC6/uE/Yf3xgyL93Szjke', NULL, '2026-07-31 04:45:08', '2026-07-31 04:45:08'),
(45, 'DINAS KETAHANAN PANGAN', 'distapang@mail.madina.go.id', '0000072', '0000067', 1, NULL, 14, NULL, NULL, NULL, '$2y$12$fNEmpkHkdQsL2dU9FAqgOOYDrCBDyrmIMB3sY7ETasgthCZas5y66', NULL, '2026-07-31 04:47:14', '2026-07-31 04:47:14'),
(46, 'DINAS PERTANIAN', 'distan@mail.madina.go.id', '000077', '0000056', 1, NULL, 15, NULL, NULL, NULL, '$2y$12$SIvAnFaWdbqBjQ7e8FzR7eg.G1hTQCPNmEHGPhGYT9igbEGjDVfHy', NULL, '2026-07-31 04:48:41', '2026-07-31 04:48:41'),
(48, 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL', 'disdukcapil@mail.madina.go.id', '0000099', '000080', 1, NULL, 17, NULL, NULL, NULL, '$2y$12$vfP9u0C0.cgsulRjjYHQ8..mHbgHObuKii/l8Pid/YbyyAzg2yNHi', NULL, '2026-07-31 04:54:55', '2026-07-31 04:54:55'),
(49, 'DINAS PEMBERDAYAAN MASYARAKAT DAN DESA', 'dpmd@mail.madina.go.id', '00078', '000097', 1, NULL, 18, NULL, NULL, NULL, '$2y$12$GTQUEEHwhsJzwhL7aq9UhO4ohnih8UOuBqBzNnnOkwakKpTWrLrem', NULL, '2026-07-31 04:56:05', '2026-07-31 04:56:05'),
(50, 'DINAS PERHUBUNGAN', 'dishub@mail.madina.go.id', '0089', '00021', 1, NULL, 19, NULL, NULL, NULL, '$2y$12$dlYLGQcvB6uUPcWXppmR7OrXbS0uYKI.mX9YMSD5AN8ljpPWEmgBG', NULL, '2026-07-31 04:57:36', '2026-07-31 04:57:36'),
(51, 'DINAS PERPUSTAKAAN DAN KEARSIPAN', 'disperpus@mail.madina.go.id', '000567', '00987', 1, NULL, 20, NULL, NULL, NULL, '$2y$12$zC.ho3SGAeZvgC4rPEDHrONXzlij2IKSUyTerI7gDrO0HpP0doDUm', NULL, '2026-07-31 04:58:38', '2026-07-31 04:58:38'),
(52, 'DINAS KOMUNIKASI DAN INFORMATIKA', 'diskominfo@mail.madina.go.id', '000165', '00879', 1, NULL, 3, NULL, NULL, NULL, '$2y$12$vGeYe3toi1yMJdz1Kc4NR.vraEOPME8ngJGzGMyia/J7yoUtKCPTa', NULL, '2026-07-31 05:00:03', '2026-07-31 05:00:03'),
(53, 'DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU', 'dpmptsp@mail.madina.go.id', '00132', '000983', 1, NULL, 21, NULL, NULL, NULL, '$2y$12$EA4g.Gm2w/4ldSQjK.iW.OYbtSaN7FKc.Q0jOKti/DNasaWaCyzHG', NULL, '2026-07-31 05:01:34', '2026-07-31 05:01:34'),
(54, 'DINAS PEMUDA DAN OLAHRAGA', 'dispora@mail.madina.go.id', '00609', '000408', 1, NULL, 22, NULL, NULL, NULL, '$2y$12$14HNOm9.iOOiWpPszpcQxeAmLNoCxz8vJvrDMauFmDfhpjgiQJYf6', NULL, '2026-07-31 05:02:52', '2026-07-31 05:02:52'),
(55, 'DINAS PERIKANAN', 'perikanan@mail.madina.go.id', '00101', '000980', 1, NULL, 23, NULL, NULL, NULL, '$2y$12$61Z9RtkjVZJ9osKs5P0eu.iJbyaMI1XtzcQAPqroTttmuS3MP.0wO', NULL, '2026-07-31 05:04:09', '2026-07-31 05:04:09'),
(56, 'DINAS PARIWISATA', 'pariwisata@mail.madina.go.id', '000909', '000707', 1, NULL, 24, NULL, NULL, NULL, '$2y$12$09pRrIuV22JGfxvKMzDPyOGN9sDm0tjTGmwDjCfcsg5IqzLijMSeu', NULL, '2026-07-31 05:05:10', '2026-07-31 05:05:10'),
(57, 'DINAS PERDAGANGAN', 'disdag@mail.madina.go.id', '000505', '00606', 1, NULL, 25, NULL, NULL, NULL, '$2y$12$kn9acnJPnLZ1RjHrjuk1COJNWjwp0.uBwHy37LOVPVG3/VCcgHk5C', NULL, '2026-07-31 05:06:08', '2026-07-31 05:07:31'),
(58, 'BADAN PERENCANAAN PEMBANGUNAN, RISET DAN INOVASI DAERAH', 'bapperida@mail.madina.go.id', '0000202', '00303', 1, NULL, 26, NULL, NULL, NULL, '$2y$12$FaGvOVQg4HgGOS1MAHxhgOSZgrM/.rdW3HXAdNWTTAAz9nuW/uvm6', NULL, '2026-07-31 05:08:43', '2026-07-31 05:08:43'),
(59, 'BADAN PENGELOLAAN KEUANGAN DAN ASET DAERAH', 'bpkad@mail.madina.go.id', '000917', '00429', 1, NULL, 27, NULL, NULL, NULL, '$2y$12$tABAgwJ/I66qel9sDz0cf.HBpVJPBbBtNLd2W7o6Vg.3/jbOUOK7G', NULL, '2026-07-31 05:09:51', '2026-07-31 05:09:51'),
(60, 'BADAN PENDAPATAN DAERAH', 'bapenda@mail.madina.go.id', '000518', '00915', 1, NULL, 28, NULL, NULL, NULL, '$2y$12$Qph8YU1muN88vblLHmvPOeqZ0uF/28khYeXz2qwqrBSeAVFnXL98G', NULL, '2026-07-31 05:10:59', '2026-07-31 05:10:59'),
(61, 'BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA', 'bkpsdm@mail.madina.go.id', '000543', '00689', 1, NULL, 29, NULL, NULL, NULL, '$2y$12$4PlKAHOhyv2mwiBGiioac.MP62kbRzVYDSUmgQBe8xqkdy/0RoZFK', NULL, '2026-07-31 05:12:01', '2026-07-31 05:12:01'),
(62, 'BADAN PENANGGULANGAN BENCANA DAERAH', 'bpbd@mail.madina.go.id', '000999', '000876', 1, NULL, 30, NULL, NULL, NULL, '$2y$12$Oit2MkC.dnf99nXVEcNILephV85jl16XVMeROzq/ioouctpKNAeVC', NULL, '2026-07-31 05:13:24', '2026-07-31 05:13:24'),
(63, 'BADAN KESATUAN BANGSA DAN POLITIK', 'kesbang@mail.madina.go.id', '0087564', '006056', 1, NULL, 31, NULL, NULL, NULL, '$2y$12$UV7.0vRbG0/rISHBjI//D.MjzchF2EFWgo0Shp65dAhSNsDJOkbFC', NULL, '2026-07-31 05:16:07', '2026-07-31 05:16:07'),
(64, 'SEKRETARIAT DPRD', 'setwan@mail.madina.go.id', '00029', '00003', 1, NULL, 35, NULL, NULL, NULL, '$2y$12$7KeBjYHKzQLL.NJ2XtnhjuNS0hcLbkg6AovUQTPoaWalCCkQFtduu', NULL, '2026-08-04 02:28:26', '2026-08-04 02:28:26'),
(65, 'BAGIAN HUKUM', 'bagianhukum@mail.madina.go.id', '000030', '00000', 1, NULL, 37, NULL, NULL, NULL, '$2y$12$5H1reTBMKBtxPTfCXj8G/u8n5UWnxsXdpEi8Ku7SPsv6N5slJ/LXy', NULL, '2026-08-04 02:35:30', '2026-08-04 02:36:31'),
(66, 'BAGIAN ADMINISTRASI PEMBANGUNAN', 'bagianadministrasipembangunan@mail.madina.go.id', '000066', '0000034', 1, NULL, 40, NULL, NULL, NULL, '$2y$12$DiweC5j.kpjSuBvBCUJX6.OQ8TikyUBkKELjTht5Jka5EsFiy2Dau', NULL, '2026-08-04 02:40:21', '2026-08-04 02:41:14'),
(67, 'BAGIAN KESEJAHTERAAN RAKYAT', 'bagiankesejahteraanrakyat@mail.madina.go.id', '000089', '00000045', 1, NULL, 38, NULL, NULL, NULL, '$2y$12$ZZfqL5U6DpZBxo5x7TBuMOBPCxK94uAFI0n40ORoeL731vlep31qa', NULL, '2026-08-04 02:43:09', '2026-08-04 02:43:09'),
(68, 'BAGIAN ORGANISASI', 'bagianorganisasi@mail.madina.go.id', '00089', '0000045', 1, NULL, 43, NULL, NULL, NULL, '$2y$12$5mbCQYOWaBp6cHLk6I5PEO79IcRCxel8zGTLQFYQYFgThUOrMpLri', NULL, '2026-08-04 02:44:45', '2026-08-04 02:44:45'),
(69, 'BAGIAN PENGADAAN BARANG DAN JASA', 'bagianpengadaanbarangdanjasa@mail.madina.go.id', '000078', '0000055', 1, NULL, 41, NULL, NULL, NULL, '$2y$12$EwcXeuq4.qHR0olc.ZaL4.DsChLQmFW6eahkshpWT7Iy33Z0G8UOG', NULL, '2026-08-04 02:46:20', '2026-08-04 02:46:20'),
(70, 'BAGIAN PEREKONOMIAN DAN SUNMBER DAYA ALAM', 'bagianperekonomiandansumberdayaalam@mail.madina.go.id', '000099', '00000044', 1, NULL, 39, NULL, NULL, NULL, '$2y$12$zLGavYLyt3IpcywNSL/tmOsarLOU8WG6pGj.1tJcE.UQ.MPJpnOnC', NULL, '2026-08-04 02:47:59', '2026-08-04 02:47:59'),
(71, 'BAGIAN TATA PEMERINTAHAN', 'bagiantatapemerintahan@mail.madina.go.id', '0000077', '0000077', 1, NULL, 36, NULL, NULL, NULL, '$2y$12$viHtfBUUJ3aKPGZZu0BrCuB93DzjHPewJevwZyfQU8cRilcaNexBq', NULL, '2026-08-04 02:50:17', '2026-08-04 02:50:17'),
(72, 'BAGIAN UMUM', 'bagianumum@mail.madina.go.id', '00088', '000044', 1, NULL, 42, NULL, NULL, NULL, '$2y$12$zjYGDGXEAA.NyUgmVWJmyeyo6o9PUyMo/yxBGERgkExTEa./fypk2', NULL, '2026-08-04 02:51:48', '2026-08-04 02:51:48'),
(73, 'RUMAH SAKIT UMUM DAERAH PANYABUNGAN', 'rsudpanyabungan@mail.madina.go.id', '000', '000', 1, NULL, 33, NULL, NULL, NULL, '$2y$12$9Nry9YJtQRUl2SRHuQvUHey08mNQ0/.ZtnC6LOAkElMkc.Cin4HxO', NULL, '2026-08-04 03:06:40', '2026-08-04 03:12:12'),
(74, 'RUMAH SAKIT UMUM DAERAH HUSNI THAMRIN NATAL', 'rshusnithamrin@mail.madina.go.id', '0', '00', 1, NULL, 34, NULL, NULL, NULL, '$2y$12$8dVH76rq4L.EzjWp5vUpEe1QgVZpZ9aBtoMIYTpu5MMa.Y5d1cEOK', NULL, '2026-08-04 03:08:39', '2026-08-04 03:11:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activities_actor_type_actor_id_index` (`actor_type`,`actor_id`),
  ADD KEY `activities_status_index` (`status`);

--
-- Indexes for table `activity_documentations`
--
ALTER TABLE `activity_documentations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_documentations_activity_id_foreign` (`activity_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `complaints_ticket_number_unique` (`ticket_number`),
  ADD KEY `complaints_user_id_foreign` (`user_id`),
  ADD KEY `complaints_status_index` (`status`),
  ADD KEY `complaints_category_index` (`category`),
  ADD KEY `complaints_target_type_target_id_index` (`target_type`,`target_id`);

--
-- Indexes for table `complaint_attachments`
--
ALTER TABLE `complaint_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_attachments_complaint_id_foreign` (`complaint_id`);

--
-- Indexes for table `complaint_categories`
--
ALTER TABLE `complaint_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `complaint_categories_name_unique` (`name`),
  ADD UNIQUE KEY `complaint_categories_slug_unique` (`slug`);

--
-- Indexes for table `complaint_handlings`
--
ALTER TABLE `complaint_handlings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_handlings_complaint_id_foreign` (`complaint_id`),
  ADD KEY `complaint_handlings_disposition_id_foreign` (`disposition_id`),
  ADD KEY `complaint_handlings_handled_by_foreign` (`handled_by`);

--
-- Indexes for table `complaint_responses`
--
ALTER TABLE `complaint_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_responses_complaint_id_foreign` (`complaint_id`),
  ADD KEY `complaint_responses_responded_by_foreign` (`responded_by`);

--
-- Indexes for table `complaint_status_histories`
--
ALTER TABLE `complaint_status_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_status_histories_complaint_id_foreign` (`complaint_id`),
  ADD KEY `complaint_status_histories_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `desas`
--
ALTER TABLE `desas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `desas_kecamatan_id_name_unique` (`kecamatan_id`,`name`);

--
-- Indexes for table `dispositions`
--
ALTER TABLE `dispositions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dispositions_complaint_id_foreign` (`complaint_id`),
  ADD KEY `dispositions_disposed_by_foreign` (`disposed_by`),
  ADD KEY `dispositions_disposed_to_type_disposed_to_id_index` (`disposed_to_type`,`disposed_to_id`),
  ADD KEY `dispositions_cancelled_by_foreign` (`cancelled_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kecamatans`
--
ALTER TABLE `kecamatans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kecamatans_code_unique` (`code`);

--
-- Indexes for table `manual_books`
--
ALTER TABLE `manual_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manual_books_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`);

--
-- Indexes for table `opds`
--
ALTER TABLE `opds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `opds_code_unique` (`code`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_settings_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `ttd_signatures`
--
ALTER TABLE `ttd_signatures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_nik_unique` (`nik`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `users_opd_id_foreign` (`opd_id`),
  ADD KEY `users_kecamatan_id_foreign` (`kecamatan_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `activity_documentations`
--
ALTER TABLE `activity_documentations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=261;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `complaint_attachments`
--
ALTER TABLE `complaint_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaint_categories`
--
ALTER TABLE `complaint_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `complaint_handlings`
--
ALTER TABLE `complaint_handlings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `complaint_responses`
--
ALTER TABLE `complaint_responses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `complaint_status_histories`
--
ALTER TABLE `complaint_status_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `desas`
--
ALTER TABLE `desas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=435;

--
-- AUTO_INCREMENT for table `dispositions`
--
ALTER TABLE `dispositions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `kecamatans`
--
ALTER TABLE `kecamatans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `manual_books`
--
ALTER TABLE `manual_books`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `opds`
--
ALTER TABLE `opds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ttd_signatures`
--
ALTER TABLE `ttd_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_documentations`
--
ALTER TABLE `activity_documentations`
  ADD CONSTRAINT `activity_documentations_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaint_attachments`
--
ALTER TABLE `complaint_attachments`
  ADD CONSTRAINT `complaint_attachments_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaint_handlings`
--
ALTER TABLE `complaint_handlings`
  ADD CONSTRAINT `complaint_handlings_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaint_handlings_disposition_id_foreign` FOREIGN KEY (`disposition_id`) REFERENCES `dispositions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `complaint_handlings_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaint_responses`
--
ALTER TABLE `complaint_responses`
  ADD CONSTRAINT `complaint_responses_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaint_responses_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaint_status_histories`
--
ALTER TABLE `complaint_status_histories`
  ADD CONSTRAINT `complaint_status_histories_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaint_status_histories_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `desas`
--
ALTER TABLE `desas`
  ADD CONSTRAINT `desas_kecamatan_id_foreign` FOREIGN KEY (`kecamatan_id`) REFERENCES `kecamatans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dispositions`
--
ALTER TABLE `dispositions`
  ADD CONSTRAINT `dispositions_cancelled_by_foreign` FOREIGN KEY (`cancelled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dispositions_complaint_id_foreign` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dispositions_disposed_by_foreign` FOREIGN KEY (`disposed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `manual_books`
--
ALTER TABLE `manual_books`
  ADD CONSTRAINT `manual_books_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD CONSTRAINT `site_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
