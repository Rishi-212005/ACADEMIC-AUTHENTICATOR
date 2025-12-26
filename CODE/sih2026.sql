-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 24, 2025 at 10:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sih2026`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password_hash`, `created_at`) VALUES
(1, 'nyaswanth81@gmail.com', '$2y$10$s5iey9.rXcJKXdMDM.6mVOMNGVxzfE6.7/bbAnbXTkSCIamza1ITC', '2025-09-15 17:42:18'),
(4, 'admin@SIH', '$2y$10$mqayj.Dw1aTb2JrmvqTUJOwF5d4wPNdhv.Y0gpfWAvCS2l/y89HZi', '2025-09-15 17:59:51'),
(1110, 'admin@test.com', '$2y$10$8gR4NLEJg/rfKRFO33SIquJwNE3GJ.dUAxaXLdjrQIYOU12zxMLUG', '2025-09-18 04:35:07');

-- --------------------------------------------------------

--
-- Table structure for table `admin_otps`
--

CREATE TABLE `admin_otps` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_otps`
--

INSERT INTO `admin_otps` (`id`, `admin_id`, `code`, `expires_at`, `used`, `created_at`) VALUES
(1, 1, '282784', '2025-09-15 20:12:25', 1, '2025-09-15 18:02:25'),
(2, 1, '727793', '2025-09-15 20:13:23', 0, '2025-09-15 18:03:23'),
(3, 1, '733515', '2025-09-15 20:22:46', 0, '2025-09-15 18:12:46'),
(4, 1, '241831', '2025-09-15 20:23:39', 1, '2025-09-15 18:13:39'),
(5, 1, '919736', '2025-09-15 20:26:34', 1, '2025-09-15 18:16:34'),
(6, 1, '405560', '2025-09-15 20:30:31', 1, '2025-09-15 18:20:31'),
(7, 1, '568759', '2025-09-15 20:36:03', 1, '2025-09-15 18:26:03'),
(8, 1, '392281', '2025-09-15 20:46:09', 1, '2025-09-15 18:36:09'),
(9, 1, '695425', '2025-09-15 20:49:54', 1, '2025-09-15 18:39:54'),
(10, 1, '746523', '2025-09-15 20:56:44', 1, '2025-09-15 18:46:44'),
(11, 1, '648307', '2025-09-15 21:19:24', 1, '2025-09-15 19:09:24'),
(12, 1, '427048', '2025-09-15 21:19:31', 0, '2025-09-15 19:09:31'),
(13, 1, '321383', '2025-09-15 21:32:42', 1, '2025-09-15 19:22:42'),
(14, 1, '955397', '2025-09-15 21:40:00', 1, '2025-09-15 19:30:00'),
(15, 1, '541201', '2025-09-16 05:57:04', 1, '2025-09-16 03:47:04'),
(16, 1, '198551', '2025-09-16 12:39:18', 1, '2025-09-16 10:29:18'),
(17, 1, '876376', '2025-09-16 13:09:45', 1, '2025-09-16 10:59:45'),
(18, 1, '479784', '2025-09-16 13:14:00', 1, '2025-09-16 11:04:00'),
(19, 1, '241991', '2025-09-16 13:48:37', 1, '2025-09-16 11:38:37'),
(20, 1, '825347', '2025-09-16 15:32:32', 1, '2025-09-16 13:22:32'),
(21, 1, '884691', '2025-09-16 16:10:45', 1, '2025-09-16 14:00:45'),
(22, 1, '157781', '2025-09-16 16:21:17', 1, '2025-09-16 14:11:17'),
(23, 1, '568061', '2025-09-16 16:28:08', 1, '2025-09-16 14:18:08'),
(24, 1, '311585', '2025-09-16 17:19:04', 1, '2025-09-16 15:09:04'),
(25, 1, '456737', '2025-09-16 17:58:21', 1, '2025-09-16 15:48:21'),
(26, 1, '941329', '2025-09-18 06:22:04', 1, '2025-09-18 04:12:04'),
(27, 1, '130744', '2025-09-18 06:47:02', 1, '2025-09-18 04:37:02'),
(28, 1, '198851', '2025-09-18 07:12:34', 1, '2025-09-18 05:02:34'),
(29, 1, '657772', '2025-09-18 08:29:52', 1, '2025-09-18 06:19:52'),
(30, 1, '912793', '2025-09-18 14:54:04', 1, '2025-09-18 12:44:04'),
(31, 1, '388928', '2025-09-18 17:20:01', 1, '2025-09-18 15:10:01'),
(32, 1, '698110', '2025-09-18 17:24:40', 1, '2025-09-18 15:14:40'),
(33, 1, '984102', '2025-09-20 08:55:50', 1, '2025-09-20 06:45:50');

-- --------------------------------------------------------

--
-- Stand-in structure for view `certificates`
-- (See below for the actual view)
--
CREATE TABLE `certificates` (
`id` bigint(20) unsigned
,`institution_id` int(10) unsigned
,`student_name` varchar(255)
,`hall_ticket_no` varchar(64)
,`certificate_no` varchar(64)
,`branch` varchar(128)
,`exam_type` varchar(64)
,`exam_month_year` varchar(32)
,`total_marks` int(11)
,`total_credits` decimal(6,2)
,`sgpa` decimal(4,2)
,`cgpa` decimal(4,2)
,`date_of_issue` date
,`file_hash` char(64)
,`original_file_path` varchar(512)
,`status` varchar(32)
);

-- --------------------------------------------------------

--
-- Table structure for table `institutions`
--

CREATE TABLE `institutions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('institute','organization') NOT NULL,
  `email` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `public_key` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`id`, `name`, `type`, `email`, `website`, `contact_name`, `phone`, `address`, `status`, `public_key`, `created_at`) VALUES
(1, 'MOHAN BABU', 'institute', 'sairishikumar.2005@gmail.com', NULL, NULL, '+919392069522', NULL, 'approved', NULL, '2025-09-18 10:12:50'),
(3, 'JNTUACEA', 'institute', 'tetana2282@dotxan.com', NULL, NULL, '+919392069522', NULL, 'approved', NULL, '2025-09-18 12:58:08');

-- --------------------------------------------------------

--
-- Table structure for table `ocr_extracted_data`
--

CREATE TABLE `ocr_extracted_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `extracted_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`extracted_data`)),
  `confidence` decimal(3,2) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ocr_saved_details`
--

CREATE TABLE `ocr_saved_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institution_id` int(10) UNSIGNED NOT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `hall_ticket_no` varchar(64) DEFAULT NULL,
  `certificate_no` varchar(64) DEFAULT NULL,
  `branch` varchar(128) DEFAULT NULL,
  `exam_type` varchar(64) DEFAULT NULL,
  `exam_month_year` varchar(32) DEFAULT NULL,
  `total_marks` int(11) DEFAULT NULL,
  `total_credits` decimal(6,2) DEFAULT NULL,
  `sgpa` decimal(4,2) DEFAULT NULL,
  `cgpa` decimal(4,2) DEFAULT NULL,
  `date_of_issue` date DEFAULT NULL,
  `file_hash` char(64) DEFAULT NULL,
  `original_file_path` varchar(512) DEFAULT NULL,
  `status` varchar(32) NOT NULL DEFAULT 'pending',
  `file_name` varchar(255) GENERATED ALWAYS AS (substring_index(`original_file_path`,'/',-1)) STORED,
  `confidence` decimal(4,3) DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `college` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `medium` varchar(64) DEFAULT NULL,
  `pass_status` varchar(64) DEFAULT NULL,
  `aggregate` varchar(64) DEFAULT NULL,
  `achievement` varchar(255) DEFAULT NULL,
  `raw_extracted_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_extracted_fields`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

CREATE TABLE `otps` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otps`
--

INSERT INTO `otps` (`id`, `user_id`, `code`, `expires_at`, `used`, `created_at`) VALUES
(11, 4, '530523', '2025-09-15 21:34:32', 1, '2025-09-15 19:24:32'),
(12, 5, '919307', '2025-09-16 05:58:24', 1, '2025-09-16 03:48:24'),
(13, 6, '664708', '2025-09-16 12:41:07', 1, '2025-09-16 10:31:07'),
(20, 9, '981617', '2025-09-16 13:49:58', 1, '2025-09-16 11:39:58'),
(21, 9, '710899', '2025-09-16 13:54:53', 1, '2025-09-16 11:44:53'),
(22, 9, '645114', '2025-09-16 14:02:12', 1, '2025-09-16 11:52:12'),
(23, 9, '467802', '2025-09-16 14:03:47', 1, '2025-09-16 11:53:47'),
(26, 6, '351103', '2025-09-16 14:11:23', 0, '2025-09-16 12:01:23'),
(27, 6, '506966', '2025-09-16 14:12:45', 1, '2025-09-16 12:02:45'),
(29, 11, '559997', '2025-09-16 18:17:32', 0, '2025-09-16 16:07:32'),
(30, 15, '249753', '2025-09-18 08:31:45', 0, '2025-09-18 06:21:45'),
(31, 15, '695582', '2025-09-18 08:33:25', 1, '2025-09-18 06:23:25'),
(32, 15, '308398', '2025-09-18 11:33:30', 0, '2025-09-18 09:23:30'),
(33, 15, '847372', '2025-09-18 11:40:08', 1, '2025-09-18 09:30:08'),
(34, 15, '616128', '2025-09-18 13:11:02', 1, '2025-09-18 11:01:02'),
(35, 23, '722848', '2025-09-18 14:56:29', 1, '2025-09-18 12:46:29'),
(36, 23, '546355', '2025-09-18 15:29:17', 1, '2025-09-18 13:19:17'),
(37, 24, '936391', '2025-09-18 17:27:47', 1, '2025-09-18 15:17:47'),
(38, 24, '678867', '2025-09-18 17:37:34', 1, '2025-09-18 15:27:34'),
(39, 24, '577529', '2025-09-18 17:47:00', 1, '2025-09-18 15:37:00'),
(40, 15, '727509', '2025-09-19 11:18:20', 1, '2025-09-19 09:08:20'),
(41, 15, '870077', '2025-09-19 13:40:07', 0, '2025-09-19 11:30:07'),
(42, 24, '622576', '2025-09-19 13:42:03', 0, '2025-09-19 11:32:03'),
(43, 24, '883983', '2025-09-19 13:42:57', 1, '2025-09-19 11:32:57'),
(44, 15, '911324', '2025-09-20 06:43:24', 1, '2025-09-20 04:33:24'),
(45, 15, '122311', '2025-09-20 07:48:03', 1, '2025-09-20 05:38:03'),
(46, 15, '865670', '2025-09-20 08:12:33', 1, '2025-09-20 06:02:33'),
(47, 15, '874086', '2025-09-20 08:14:12', 1, '2025-09-20 06:04:12'),
(48, 15, '592832', '2025-09-20 08:50:50', 1, '2025-09-20 06:40:50'),
(49, 15, '646261', '2025-09-20 11:18:28', 1, '2025-09-20 09:08:28'),
(50, 15, '884043', '2025-09-20 13:34:03', 1, '2025-09-20 11:24:03'),
(51, 15, '452113', '2025-09-20 14:46:53', 1, '2025-09-20 12:36:53'),
(52, 15, '868431', '2025-09-21 09:10:22', 1, '2025-09-21 07:00:22'),
(53, 24, '911641', '2025-09-21 12:11:54', 1, '2025-09-21 10:01:54'),
(54, 24, '151222', '2025-09-21 13:11:01', 1, '2025-09-21 11:01:01'),
(55, 24, '115450', '2025-09-21 14:18:58', 1, '2025-09-21 12:08:58'),
(56, 24, '632968', '2025-09-22 15:04:02', 1, '2025-09-22 12:54:02'),
(57, 24, '245940', '2025-09-22 17:24:35', 1, '2025-09-22 15:14:35'),
(58, 24, '377933', '2025-09-23 14:32:14', 1, '2025-09-23 12:22:14');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `type` enum('organization','institution') NOT NULL,
  `name` varchar(255) NOT NULL,
  `org_type` varchar(100) DEFAULT NULL,
  `inst_type` varchar(100) DEFAULT NULL,
  `inst_code` varchar(100) DEFAULT NULL,
  `inst_university` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `type`, `name`, `org_type`, `inst_type`, `inst_code`, `inst_university`, `email`, `phone`, `website`, `address_line1`, `address_line2`, `city`, `state`, `district`, `pincode`, `country`, `password_hash`, `document_path`, `status`, `created_at`) VALUES
(4, 'organization', 'SBI', 'government', '', '', '', 'nyaswanthyadav81@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$7AYW1BatNG75hG.FMpFgiegdTqITYrR0cQJ/A6K8L6xwmC7O/XND.', 'uploads/1757964110_1-2.pdf', 'approved', '2025-09-15 19:21:50'),
(5, 'organization', 'ACER', 'private', '', '', '', 'chiranjeevibathula06@gmail.com', '+919392069522', 'http://localhost/SIH-2026/registration.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$vkjTxeXtNuQNZnQ54DPI7u03wCy8qzXMhViL/hixp9LlEw3.gqEL2', 'uploads/1757994395_1-1.pdf', 'approved', '2025-09-16 03:46:35'),
(6, 'organization', 'pookie enterprisers', 'psu', '', '', '', 'shashidarm984@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', 'jntua', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$2iyMLQdoAowyOVhN/Pj/wOv6tgCobZqS1VYs.M2rqfoOLwcDt4exa', 'uploads/1758018527_1-1.pdf', 'approved', '2025-09-16 10:28:47'),
(9, 'institution', 'JNTUACEA', '', 'college', '515001', '', '23001a0520@jntua.ac.in', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$G9JLD6lkmbMd4qbaQ/OhD.hTAS8ZOO3HjcigYFtUE6mrOju0F6SJK', 'uploads/1758022688_1-2.pdf', 'approved', '2025-09-16 11:38:08'),
(11, 'institution', 'VIT', '', 'university', '11111111111', '', 'pookie@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'eeeeeeeeeeeee', '515001', 'India', '$2y$10$lvkeatnTw4juUt5l3GTiou9u/oQckWWEtnOldYhoJHjRb4YDtzAdW', 'uploads/1758037820_1-2.pdf', 'approved', '2025-09-16 15:50:20'),
(12, 'institution', '111111111', '', 'college', '11111111111', '', 'pookie2@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$AKy.9pjxOJ1SAzBVG6GaJ.5Na7uXGPIUxYSaa2LeHOzT/yyVn1b1O', 'uploads/1758039225_1-1.pdf', 'rejected', '2025-09-16 16:13:45'),
(14, 'institution', 'JNTUA', '', 'university', 'wwwwwww', '', 'pokie@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'eeeeeeeeeeeee', '515001', 'India', '$2y$10$C3b7wHJU3IFOI.Xjq6wlFeYmnTZpoUBkpkDSI1CQ35Fk/lT2l.mRe', 'uploads/1758039882_1-2.pdf', 'approved', '2025-09-16 16:24:42'),
(16, 'institution', 'veltech', '', 'polytechnic', '8999999', '', 'rishi@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', 'jntua', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$IWNxA7PG.II.SBJT2qeAuu43k3ljsuZnUsWakabb55cXz622fqyvC', 'uploads/1758169788_1-1.pdf', 'rejected', '2025-09-18 04:29:48'),
(17, 'institution', 'Test University', NULL, NULL, NULL, NULL, 'test@university.com', '+1234567890', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'approved', '2025-09-18 05:27:31'),
(18, 'organization', 'Test Company', NULL, NULL, NULL, NULL, 'test@company.com', '+1234567891', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'approved', '2025-09-18 05:27:31'),
(19, 'institution', 'Sample College', NULL, NULL, NULL, NULL, 'sample@college.com', '+1234567892', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'approved', '2025-09-18 05:27:31'),
(20, 'organization', 'Demo Corp', NULL, NULL, NULL, NULL, 'demo@corp.com', '+1234567893', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'approved', '2025-09-18 05:27:31'),
(21, 'institution', 'Future Institute', NULL, NULL, NULL, NULL, 'future@institute.com', '+1234567894', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'approved', '2025-09-18 05:27:32'),
(22, 'institution', 'MOHAN BABU', '', 'college', '11111111111', '', 'sairishikumar.2005@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', 'jntua', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$UWR7rmJXRlNwqCBzpc7heOISUaGkSOrinz52kpWlsRAntuFfnSNR.', 'uploads/1758176364_1-1.pdf', 'approved', '2025-09-18 06:19:24'),
(23, 'organization', 'YASH BUSSINESS', 'employer', '', '', '', 'p@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$NYIVkRatt8dpcesST3Cj6OKeG3jJBnYxaf2F5DMuqAkodWa6PU89C', 'uploads/1758178782_1-2.pdf', 'approved', '2025-09-18 06:59:42'),
(24, 'organization', 'jhgdeyg', 'government', '', '', '', 'wkeuhwefjwfeg@gmail.com', '+919392069522', 'http://localhost/SIH-2026/registration.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', 'eq', 'PUTTUR', 'jharkhand', 'gwegwuieghwqjejg', '515001', 'India', '$2y$10$kBDFq2Fszmr/tF4rct8idekJR6yvoJu/wBZeHq/EO7Mg2vwr6js2y', 'uploads/1758178979_1-2.pdf', 'approved', '2025-09-18 07:02:59'),
(25, 'institution', 'JNTUACEA', '', 'college', '515001', '', 'tetana2282@dotxan.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'eeeeeeeeeeeee', '515001', 'India', '$2y$10$JUSOfUmTrUgTsWPwVxf84ehDTPSadWEkXyaTyTCC3/RZXtStmNWJi', 'uploads/1758199427_1-2.pdf', 'approved', '2025-09-18 12:43:47'),
(26, 'institution', 'JNTUACEA', '', 'university', '8999999', '', 'nyaswanth81@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$Jr3LGep78OpoMOi0pH5fF.4QjHRj/ZPdENam90U/9r/CnAqNlcQJK', 'uploads/1758208181_Screenshot_2025-01-23_213903.png', 'approved', '2025-09-18 15:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `type` enum('organization','institution') NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `type`, `name`, `email`, `phone`, `password_hash`, `created_at`) VALUES
(4, 'organization', 'SBI', 'nyaswanthyadav81@gmail.com', '+919392069522', '$2y$10$7AYW1BatNG75hG.FMpFgiegdTqITYrR0cQJ/A6K8L6xwmC7O/XND.', '2025-09-15 19:23:57'),
(5, 'organization', 'ACER', 'chiranjeevibathula06@gmail.com', '+919392069522', '$2y$10$vkjTxeXtNuQNZnQ54DPI7u03wCy8qzXMhViL/hixp9LlEw3.gqEL2', '2025-09-16 03:47:41'),
(6, 'organization', 'pookie enterprisers', 'shashidarm984@gmail.com', '+919392069522', '$2y$10$2iyMLQdoAowyOVhN/Pj/wOv6tgCobZqS1VYs.M2rqfoOLwcDt4exa', '2025-09-16 10:30:00'),
(9, 'institution', 'JNTUACEA', '23001a0520@jntua.ac.in', '+919392069522', '$2y$10$G9JLD6lkmbMd4qbaQ/OhD.hTAS8ZOO3HjcigYFtUE6mrOju0F6SJK', '2025-09-16 11:39:25'),
(11, 'institution', 'VIT', 'pookie@gmail.com', '+919392069522', '$2y$10$lvkeatnTw4juUt5l3GTiou9u/oQckWWEtnOldYhoJHjRb4YDtzAdW', '2025-09-16 16:07:18'),
(12, 'institution', 'JNTUA', 'pokie@gmail.com', '+919392069522', '$2y$10$C3b7wHJU3IFOI.Xjq6wlFeYmnTZpoUBkpkDSI1CQ35Fk/lT2l.mRe', '2025-09-16 16:36:23'),
(13, 'organization', 'YASH BUSSINESS', 'nyanth81@gmail.com', '+919392069522', '$2y$10$yL0lHcgPCxaLO9RDEI3gW.PbsFOumJcuZ5zydC5Qup6M7gVLzeNjG', '2025-09-18 05:21:24'),
(14, 'institution', 'Future Institute', 'future@institute.com', '+1234567894', '', '2025-09-18 05:28:25'),
(15, 'institution', 'MOHAN BABU', 'sairishikumar.2005@gmail.com', '+919392069522', '$2y$10$UWR7rmJXRlNwqCBzpc7heOISUaGkSOrinz52kpWlsRAntuFfnSNR.', '2025-09-18 06:20:54'),
(16, 'institution', 'Test University', 'test@university.com', '+1234567890', '', '2025-09-18 06:55:49'),
(17, 'organization', 'Test Company', 'test@company.com', '+1234567891', '', '2025-09-18 06:56:08'),
(18, 'organization', 'YASH BUSSINESS', 'p@gmail.com', '+919392069522', '$2y$10$NYIVkRatt8dpcesST3Cj6OKeG3jJBnYxaf2F5DMuqAkodWa6PU89C', '2025-09-18 07:00:17'),
(19, 'organization', 'Demo Corp', 'demo@corp.com', '+1234567893', '', '2025-09-18 07:01:01'),
(21, 'institution', 'Sample College', 'sample@college.com', '+1234567892', '', '2025-09-18 07:01:15'),
(22, 'organization', 'jhgdeyg', 'wkeuhwefjwfeg@gmail.com', '+919392069522', '$2y$10$kBDFq2Fszmr/tF4rct8idekJR6yvoJu/wBZeHq/EO7Mg2vwr6js2y', '2025-09-18 07:03:33'),
(23, 'institution', 'JNTUACEA', 'tetana2282@dotxan.com', '+919392069522', '$2y$10$JUSOfUmTrUgTsWPwVxf84ehDTPSadWEkXyaTyTCC3/RZXtStmNWJi', '2025-09-18 12:45:59'),
(24, 'institution', 'JNTUACEA', 'nyaswanth81@gmail.com', '+919392069522', '$2y$10$Jr3LGep78OpoMOi0pH5fF.4QjHRj/ZPdENam90U/9r/CnAqNlcQJK', '2025-09-18 15:11:12');

-- --------------------------------------------------------

--
-- Table structure for table `verifications`
--

CREATE TABLE `verifications` (
  `id` bigint(20) NOT NULL,
  `certificate_id` bigint(20) DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `verifier_type` enum('institution','admin','external') DEFAULT 'institution',
  `status` enum('valid','suspect','invalid','manual_review') NOT NULL,
  `confidence` float DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `txid` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `certificates`
--
DROP TABLE IF EXISTS `certificates`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `certificates`  AS SELECT `ocr_saved_details`.`id` AS `id`, `ocr_saved_details`.`institution_id` AS `institution_id`, `ocr_saved_details`.`student_name` AS `student_name`, `ocr_saved_details`.`hall_ticket_no` AS `hall_ticket_no`, `ocr_saved_details`.`certificate_no` AS `certificate_no`, `ocr_saved_details`.`branch` AS `branch`, `ocr_saved_details`.`exam_type` AS `exam_type`, `ocr_saved_details`.`exam_month_year` AS `exam_month_year`, `ocr_saved_details`.`total_marks` AS `total_marks`, `ocr_saved_details`.`total_credits` AS `total_credits`, `ocr_saved_details`.`sgpa` AS `sgpa`, `ocr_saved_details`.`cgpa` AS `cgpa`, `ocr_saved_details`.`date_of_issue` AS `date_of_issue`, `ocr_saved_details`.`file_hash` AS `file_hash`, `ocr_saved_details`.`original_file_path` AS `original_file_path`, `ocr_saved_details`.`status` AS `status` FROM `ocr_saved_details`WITH LOCAL CHECK OPTION  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `admin_otps`
--
ALTER TABLE `admin_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ocr_extracted_data`
--
ALTER TABLE `ocr_extracted_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `ocr_saved_details`
--
ALTER TABLE `ocr_saved_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_institution_id` (`institution_id`),
  ADD KEY `idx_hall_ticket_no` (`hall_ticket_no`),
  ADD KEY `idx_certificate_no` (`certificate_no`);

--
-- Indexes for table `otps`
--
ALTER TABLE `otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `verifications`
--
ALTER TABLE `verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `certificate_id` (`certificate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2691;

--
-- AUTO_INCREMENT for table `admin_otps`
--
ALTER TABLE `admin_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `institutions`
--
ALTER TABLE `institutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ocr_extracted_data`
--
ALTER TABLE `ocr_extracted_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ocr_saved_details`
--
ALTER TABLE `ocr_saved_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `verifications`
--
ALTER TABLE `verifications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_otps`
--
ALTER TABLE `admin_otps`
  ADD CONSTRAINT `admin_otps_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ocr_extracted_data`
--
ALTER TABLE `ocr_extracted_data`
  ADD CONSTRAINT `ocr_extracted_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `otps`
--
ALTER TABLE `otps`
  ADD CONSTRAINT `otps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verifications`
--
ALTER TABLE `verifications`
  ADD CONSTRAINT `verifications_ibfk_1` FOREIGN KEY (`certificate_id`) REFERENCES `certificates` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
