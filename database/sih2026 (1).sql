-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2025 at 02:32 PM
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
(33, 1, '984102', '2025-09-20 08:55:50', 1, '2025-09-20 06:45:50'),
(34, 1, '389893', '2025-09-24 10:43:33', 1, '2025-09-24 08:33:33'),
(35, 1, '208830', '2025-09-24 19:02:39', 1, '2025-09-24 16:52:39'),
(36, 1, '682538', '2025-12-25 05:20:57', 1, '2025-12-25 04:10:57'),
(37, 1, '856500', '2025-12-25 08:16:00', 1, '2025-12-25 07:06:00'),
(38, 1, '648198', '2025-12-25 09:43:35', 1, '2025-12-25 08:33:35'),
(39, 1, '421717', '2025-12-25 10:28:40', 1, '2025-12-25 09:18:40'),
(40, 1, '297240', '2025-12-25 12:36:17', 1, '2025-12-25 11:26:17');

-- --------------------------------------------------------

--
-- Stand-in structure for view `certificates`
-- (See below for the actual view)
-- NOTE: This is a phpMyAdmin export artifact for view structure
-- Commented out since view creation is disabled due to permissions
--
-- CREATE TABLE `certificates` (
-- `id` bigint(20) unsigned
-- ,`institution_id` int(10) unsigned
-- ,`student_name` varchar(255)
-- ,`hall_ticket_no` varchar(64)
-- ,`certificate_no` varchar(64)
-- ,`branch` varchar(128)
-- ,`exam_type` varchar(64)
-- ,`exam_month_year` varchar(32)
-- ,`total_marks` int(11)
-- ,`total_credits` decimal(6,2)
-- ,`sgpa` decimal(4,2)
-- ,`cgpa` decimal(4,2)
-- ,`date_of_issue` date
-- ,`file_hash` char(64)
-- ,`original_file_path` varchar(512)
-- ,`status` varchar(32)
-- );

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

--
-- Dumping data for table `ocr_saved_details`
--

INSERT INTO `ocr_saved_details` (`id`, `institution_id`, `student_name`, `hall_ticket_no`, `certificate_no`, `branch`, `exam_type`, `exam_month_year`, `total_marks`, `total_credits`, `sgpa`, `cgpa`, `date_of_issue`, `file_hash`, `original_file_path`, `status`, `confidence`, `university`, `college`, `course`, `medium`, `pass_status`, `aggregate`, `achievement`, `raw_extracted_fields`, `created_at`, `updated_at`) VALUES
(54, 24, 'N YASWANTH', '23001A0516', 'J23425201', 'COMPUTER SCIENCE AND ENGINEERING', NULL, NULL, NULL, NULL, 8.59, 8.59, NULL, '46fcfd9f3bbddc34dd95266a01759b04c84202968d5276cbd57bcabe09ed0a3c', 'uploads/ocr_images/WhatsApp Image 2025-12-25 at 9.50.05 AM.jpeg', 'pending', 0.830, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', NULL, NULL, 'PASSED', 'Eight Two Three', NULL, '{\"Student Name\":\"N YASWANTH\",\"Roll Number\":\"23001A0516\",\"Hall Ticket No\":\"23001A0516\",\"Seena No\":null,\"Examination Date\":\"JUNEJULY 2024\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"College Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"CGPA\":\"8.59\",\"CGPA (Scale 10)\":null,\"SGPA\":\"8.59\",\"Result\":\"PASSED\",\"Grade\":null,\"Achievement\":null,\"SerialNo\":\"J23425201\",\"AggregateInWords\":\"Eight Two Three\"}', '2025-12-25 11:53:49', '2025-12-25 11:53:49'),
(55, 24, 'Student 1', '23001A0501', 'J2300001', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 740, 23.66, 9.26, 8.22, '2024-09-23', '0ec0f72a6b3d8e04f3726dc419825cf57ec284313dc88d562bd3368746d5238a', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'PASSED', 'First Class', '', '{\"id\":\"1\",\"institution_id\":\"23\",\"student_name\":\"Student 1\",\"hall_ticket_no\":\"23001A0501\",\"certificate_no\":\"J2300001\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"740\",\"total_credits\":\"23.66\",\"sgpa\":\"9.26\",\"cgpa\":\"8.22\",\"date_of_issue\":\"2024-09-23\",\"file_hash\":\"88ec722e9d4ff058c7dbdb3f863f7fc5ccbaf69409d1a8a2d76e22a7777b48a3\",\"original_file_path\":\"uploads\\/ocr_images\\/student_1.jpg\",\"status\":\"pending\",\"confidence\":\"0.906\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"PASSED\",\"aggregate\":\"First Class\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497221\",\"updated_at\":\"2025-12-25 04:55:09.497225\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17'),
(56, 24, 'Student 2', '23002A0502', 'J2300002', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 776, 18.62, 8.79, 7.21, '2024-04-27', '05d536691c8341c6923edc0a12a8a99b2ebdb59f6789ce822f4d5cdba9a540dc', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'PASSED', 'First Class', '', '{\"id\":\"2\",\"institution_id\":\"30\",\"student_name\":\"Student 2\",\"hall_ticket_no\":\"23002A0502\",\"certificate_no\":\"J2300002\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"776\",\"total_credits\":\"18.62\",\"sgpa\":\"8.79\",\"cgpa\":\"7.21\",\"date_of_issue\":\"2024-04-27\",\"file_hash\":\"ac9b14b1c9f0c775857e99708008038d48b2fc7c077ca4c8b06438c056771f83\",\"original_file_path\":\"uploads\\/ocr_images\\/student_2.jpg\",\"status\":\"approved\",\"confidence\":\"0.766\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"PASSED\",\"aggregate\":\"First Class\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497246\",\"updated_at\":\"2025-12-25 04:55:09.497247\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17'),
(57, 24, 'Student 3', '23003A0503', 'J2300003', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 899, 20.44, 7.59, 8.75, '2024-10-04', '85c68f8c6d9dd7fd136b78ff660b260bc8509940f98a796e3edaa7ee59b12472', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'FAILED', 'Second Class', '', '{\"id\":\"3\",\"institution_id\":\"27\",\"student_name\":\"Student 3\",\"hall_ticket_no\":\"23003A0503\",\"certificate_no\":\"J2300003\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"899\",\"total_credits\":\"20.44\",\"sgpa\":\"7.59\",\"cgpa\":\"8.75\",\"date_of_issue\":\"2024-10-04\",\"file_hash\":\"d282c8f613f9e76de286c4345ac0c8263252098fb57392c57367a07c84632cd1\",\"original_file_path\":\"uploads\\/ocr_images\\/student_3.jpg\",\"status\":\"approved\",\"confidence\":\"0.873\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"FAILED\",\"aggregate\":\"Second Class\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497263\",\"updated_at\":\"2025-12-25 04:55:09.497263\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17'),
(58, 24, 'Student 4', '23004A0504', 'J2300004', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 836, 18.56, 6.58, 7.86, '2024-12-16', '79e5db4fa1bf75197c6bd93be721147b435af45bab8328ec152d584f6450227f', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'FAILED', 'Distinction', '', '{\"id\":\"4\",\"institution_id\":\"21\",\"student_name\":\"Student 4\",\"hall_ticket_no\":\"23004A0504\",\"certificate_no\":\"J2300004\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"836\",\"total_credits\":\"18.56\",\"sgpa\":\"6.58\",\"cgpa\":\"7.86\",\"date_of_issue\":\"2024-12-16\",\"file_hash\":\"871246a40b0d634ad87f78f886320de739e18992d93157590619f2e591040758\",\"original_file_path\":\"uploads\\/ocr_images\\/student_4.jpg\",\"status\":\"rejected\",\"confidence\":\"0.897\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"FAILED\",\"aggregate\":\"Distinction\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497292\",\"updated_at\":\"2025-12-25 04:55:09.497292\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17'),
(59, 24, 'Student 5', '23005A0505', 'J2300005', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 670, 22.17, 8.37, 7.41, '2024-10-22', 'e09d092a1f2a100e28cc49eb36aea463df3a6d6033f1028302190ef67965a9d7', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'FAILED', 'Second Class', '', '{\"id\":\"5\",\"institution_id\":\"20\",\"student_name\":\"Student 5\",\"hall_ticket_no\":\"23005A0505\",\"certificate_no\":\"J2300005\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"670\",\"total_credits\":\"22.17\",\"sgpa\":\"8.37\",\"cgpa\":\"7.41\",\"date_of_issue\":\"2024-10-22\",\"file_hash\":\"580650bbb05d8ff6899e8129dcd3cd4fa56e069a069b7b5e984584b25f6bc64e\",\"original_file_path\":\"uploads\\/ocr_images\\/student_5.jpg\",\"status\":\"rejected\",\"confidence\":\"0.739\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"FAILED\",\"aggregate\":\"Second Class\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497306\",\"updated_at\":\"2025-12-25 04:55:09.497306\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17'),
(60, 24, 'Student 6', '23006A0506', 'J2300006', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 808, 18.27, 8.04, 8.37, '2024-12-04', '5fcd4184d018ff4110bb741c367448bb6ffc78a493c0a39f8b1efe397eb83da9', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'FAILED', 'First Class', '', '{\"id\":\"6\",\"institution_id\":\"24\",\"student_name\":\"Student 6\",\"hall_ticket_no\":\"23006A0506\",\"certificate_no\":\"J2300006\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"808\",\"total_credits\":\"18.27\",\"sgpa\":\"8.04\",\"cgpa\":\"8.37\",\"date_of_issue\":\"2024-12-04\",\"file_hash\":\"6fd15a32c72a23f89bc768a3c7f293bc852860114e1492a8154ca13da491beeb\",\"original_file_path\":\"uploads\\/ocr_images\\/student_6.jpg\",\"status\":\"approved\",\"confidence\":\"0.869\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"FAILED\",\"aggregate\":\"First Class\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497319\",\"updated_at\":\"2025-12-25 04:55:09.497319\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17'),
(61, 24, 'Student 7', '23007A0507', 'J2300007', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 866, 18.89, 7.04, 7.25, '2024-02-07', 'edd71b6be60dca1fb054151811599a0b4f8a254358d41a40f5bf9ad217d2f95d', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'FAILED', 'Second Class', '', '{\"id\":\"7\",\"institution_id\":\"27\",\"student_name\":\"Student 7\",\"hall_ticket_no\":\"23007A0507\",\"certificate_no\":\"J2300007\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"866\",\"total_credits\":\"18.89\",\"sgpa\":\"7.04\",\"cgpa\":\"7.25\",\"date_of_issue\":\"2024-02-07\",\"file_hash\":\"95fcb07bbd069d46c402731713a2e985de6bc7e1c9c90f6dcdb67c2f4a2cc8d1\",\"original_file_path\":\"uploads\\/ocr_images\\/student_7.jpg\",\"status\":\"rejected\",\"confidence\":\"0.892\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"FAILED\",\"aggregate\":\"Second Class\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497334\",\"updated_at\":\"2025-12-25 04:55:09.497334\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17'),
(62, 24, 'Student 8', '23008A0508', 'J2300008', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 610, 23.85, 8.47, 9.20, '2024-09-06', '6e6c76c2efbe533979ad9c1a19005729a869162a51075d04208053e63384a24c', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'PASSED', 'Second Class', '', '{\"id\":\"8\",\"institution_id\":\"24\",\"student_name\":\"Student 8\",\"hall_ticket_no\":\"23008A0508\",\"certificate_no\":\"J2300008\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"610\",\"total_credits\":\"23.85\",\"sgpa\":\"8.47\",\"cgpa\":\"9.2\",\"date_of_issue\":\"2024-09-06\",\"file_hash\":\"deac769dceb33c3f785a811b8fc17dc488288f1e241de17f8cc7a1381f01ac52\",\"original_file_path\":\"uploads\\/ocr_images\\/student_8.jpg\",\"status\":\"pending\",\"confidence\":\"0.919\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"PASSED\",\"aggregate\":\"Second Class\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497352\",\"updated_at\":\"2025-12-25 04:55:09.497353\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17'),
(63, 24, 'Student 9', '23009A0509', 'J2300009', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 802, 21.73, 9.31, 7.76, '2024-10-11', '2c05284a60e3729de6ff968d51bfd6ce0b0174ec151394c0aef55db183e17dca', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'PASSED', 'First Class', '', '{\"id\":\"9\",\"institution_id\":\"20\",\"student_name\":\"Student 9\",\"hall_ticket_no\":\"23009A0509\",\"certificate_no\":\"J2300009\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"802\",\"total_credits\":\"21.73\",\"sgpa\":\"9.31\",\"cgpa\":\"7.76\",\"date_of_issue\":\"2024-10-11\",\"file_hash\":\"d5c55348f2cd529366680124acc33e3b0951a3a088a05d16d63b649788666a19\",\"original_file_path\":\"uploads\\/ocr_images\\/student_9.jpg\",\"status\":\"pending\",\"confidence\":\"0.784\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"PASSED\",\"aggregate\":\"First Class\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497367\",\"updated_at\":\"2025-12-25 04:55:09.497367\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17'),
(64, 24, 'Student 10', '23010A0510', 'J2300010', 'COMPUTER SCIENCE AND ENGINEERING', 'REGULAR', 'JUNE 2024', 790, 18.54, 7.93, 6.62, '2024-09-01', '51442ad6009c0c7d945109e5a02c7e54cbccc5f4e1964bdaeeb58d234f6f26a0', 'bulk_upload/1766665337_ocr_saved_details_sample_10_students.csv', 'pending', NULL, 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'SEES COLLEGE OF ENGINEERING', 'B.Tech', 'ENGLISH', 'FAILED', 'Second Class', '', '{\"id\":\"10\",\"institution_id\":\"27\",\"student_name\":\"Student 10\",\"hall_ticket_no\":\"23010A0510\",\"certificate_no\":\"J2300010\",\"branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"exam_type\":\"REGULAR\",\"exam_month_year\":\"JUNE 2024\",\"total_marks\":\"790\",\"total_credits\":\"18.54\",\"sgpa\":\"7.93\",\"cgpa\":\"6.62\",\"date_of_issue\":\"2024-09-01\",\"file_hash\":\"6e68ccc7755949c8eb354974d1369b943c3d73bd972608b0f76379a3f9b6a0aa\",\"original_file_path\":\"uploads\\/ocr_images\\/student_10.jpg\",\"status\":\"rejected\",\"confidence\":\"0.856\",\"university\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"college\":\"SEES COLLEGE OF ENGINEERING\",\"course\":\"B.Tech\",\"medium\":\"ENGLISH\",\"pass_status\":\"FAILED\",\"aggregate\":\"Second Class\",\"achievement\":\"\",\"raw_extracted_fields\":\"{\\\"sample\\\":\\\"ocr raw json\\\"}\",\"created_at\":\"2025-12-25 04:55:09.497382\",\"updated_at\":\"2025-12-25 04:55:09.497382\"}', '2025-12-25 12:22:17', '2025-12-25 12:22:17');

-- --------------------------------------------------------

--
-- Table structure for table `organization_validations`
--

CREATE TABLE `organization_validations` (
  `id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `hall_ticket_no` varchar(64) DEFAULT NULL,
  `certificate_no` varchar(64) DEFAULT NULL,
  `college_name` varchar(255) DEFAULT NULL,
  `university_name` varchar(255) DEFAULT NULL,
  `branch` varchar(128) DEFAULT NULL,
  `cgpa` decimal(4,2) DEFAULT NULL,
  `sgpa` decimal(4,2) DEFAULT NULL,
  `pass_status` varchar(64) DEFAULT NULL,
  `aggregate` varchar(255) DEFAULT NULL,
  `confidence` decimal(5,2) DEFAULT NULL,
  `validation_score` decimal(5,2) DEFAULT NULL,
  `validation_status` varchar(20) DEFAULT NULL,
  `extracted_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extracted_fields`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organization_validations`
--

INSERT INTO `organization_validations` (`id`, `organization_id`, `student_name`, `hall_ticket_no`, `certificate_no`, `college_name`, `university_name`, `branch`, `cgpa`, `sgpa`, `pass_status`, `aggregate`, `confidence`, `validation_score`, `validation_status`, `extracted_fields`, `created_at`) VALUES
(2, 25, 'N YASWANTH', '23001A0516', 'J23425201', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 8.59, 8.59, 'PASSED', 'Eight Two Three', 83.24, 100.00, 'VALID', '{\"Student Name\":\"N YASWANTH\",\"Roll Number\":\"23001A0516\",\"Hall Ticket No\":\"23001A0516\",\"Seena No\":null,\"Examination Date\":\"JUNEJULY 2024\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"College Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"CGPA\":\"8.59\",\"CGPA (Scale 10)\":null,\"SGPA\":\"8.59\",\"Result\":\"PASSED\",\"Grade\":null,\"Achievement\":null,\"SerialNo\":\"J23425201\",\"AggregateInWords\":\"Eight Two Three\"}', '2025-12-25 12:17:58'),
(4, 25, 'Student 2', '23002A0502', 'J2300002', 'SEES COLLEGE OF ENGINEERING', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 7.21, 8.79, 'PASSED', 'First Class', NULL, 100.00, 'VALID', '{\"Student Name\":\"Student 2\",\"Hall Ticket No\":\"23002A0502\",\"SerialNo\":\"J2300002\",\"College Name\":\"SEES COLLEGE OF ENGINEERING\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"Exam Month Year\":\"JUNE 2024\",\"Course\":\"B.Tech\",\"Medium\":\"ENGLISH\",\"Total Marks\":776,\"Total Credits\":18.62,\"CGPA\":7.21,\"SGPA\":8.79,\"Result\":\"PASSED\",\"AggregateInWords\":\"First Class\",\"Achievement\":\"\",\"IssueDate\":\"2024-04-27\"}', '2025-12-25 12:30:55'),
(5, 25, 'Student 3', '23003A0503', 'J2300003', 'SEES COLLEGE OF ENGINEERING', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 8.75, 7.59, 'FAILED', 'Second Class', NULL, 100.00, 'VALID', '{\"Student Name\":\"Student 3\",\"Hall Ticket No\":\"23003A0503\",\"SerialNo\":\"J2300003\",\"College Name\":\"SEES COLLEGE OF ENGINEERING\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"Exam Month Year\":\"JUNE 2024\",\"Course\":\"B.Tech\",\"Medium\":\"ENGLISH\",\"Total Marks\":899,\"Total Credits\":20.44,\"CGPA\":8.75,\"SGPA\":7.59,\"Result\":\"FAILED\",\"AggregateInWords\":\"Second Class\",\"Achievement\":\"\",\"IssueDate\":\"2024-10-04\"}', '2025-12-25 12:30:55'),
(6, 25, 'Student 4', '23004A0504', 'J2300004', 'SEES COLLEGE OF ENGINEERING', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 7.86, 6.58, 'FAILED', 'Distinction', NULL, 100.00, 'VALID', '{\"Student Name\":\"Student 4\",\"Hall Ticket No\":\"23004A0504\",\"SerialNo\":\"J2300004\",\"College Name\":\"SEES COLLEGE OF ENGINEERING\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"Exam Month Year\":\"JUNE 2024\",\"Course\":\"B.Tech\",\"Medium\":\"ENGLISH\",\"Total Marks\":836,\"Total Credits\":18.56,\"CGPA\":7.86,\"SGPA\":6.58,\"Result\":\"FAILED\",\"AggregateInWords\":\"Distinction\",\"Achievement\":\"\",\"IssueDate\":\"2024-12-16\"}', '2025-12-25 12:30:55'),
(7, 25, 'Student 5', '23005A0505', 'J2300005', 'SEES COLLEGE OF ENGINEERING', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 7.41, 8.37, 'FAILED', 'Second Class', NULL, 100.00, 'VALID', '{\"Student Name\":\"Student 5\",\"Hall Ticket No\":\"23005A0505\",\"SerialNo\":\"J2300005\",\"College Name\":\"SEES COLLEGE OF ENGINEERING\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"Exam Month Year\":\"JUNE 2024\",\"Course\":\"B.Tech\",\"Medium\":\"ENGLISH\",\"Total Marks\":670,\"Total Credits\":22.17,\"CGPA\":7.41,\"SGPA\":8.37,\"Result\":\"FAILED\",\"AggregateInWords\":\"Second Class\",\"Achievement\":\"\",\"IssueDate\":\"2024-10-22\"}', '2025-12-25 12:30:55'),
(8, 25, 'Student 6', '23006A0506', 'J2300006', 'SEES COLLEGE OF ENGINEERING', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 8.37, 8.04, 'FAILED', 'First Class', NULL, 100.00, 'VALID', '{\"Student Name\":\"Student 6\",\"Hall Ticket No\":\"23006A0506\",\"SerialNo\":\"J2300006\",\"College Name\":\"SEES COLLEGE OF ENGINEERING\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"Exam Month Year\":\"JUNE 2024\",\"Course\":\"B.Tech\",\"Medium\":\"ENGLISH\",\"Total Marks\":808,\"Total Credits\":18.27,\"CGPA\":8.37,\"SGPA\":8.04,\"Result\":\"FAILED\",\"AggregateInWords\":\"First Class\",\"Achievement\":\"\",\"IssueDate\":\"2024-12-04\"}', '2025-12-25 12:30:55'),
(9, 25, 'Student 7', '23007A0507', 'J2300007', 'SEES COLLEGE OF ENGINEERING', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 7.25, 7.04, 'FAILED', 'Second Class', NULL, 100.00, 'VALID', '{\"Student Name\":\"Student 7\",\"Hall Ticket No\":\"23007A0507\",\"SerialNo\":\"J2300007\",\"College Name\":\"SEES COLLEGE OF ENGINEERING\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"Exam Month Year\":\"JUNE 2024\",\"Course\":\"B.Tech\",\"Medium\":\"ENGLISH\",\"Total Marks\":866,\"Total Credits\":18.89,\"CGPA\":7.25,\"SGPA\":7.04,\"Result\":\"FAILED\",\"AggregateInWords\":\"Second Class\",\"Achievement\":\"\",\"IssueDate\":\"2024-02-07\"}', '2025-12-25 12:30:55'),
(10, 25, 'Student 8', '23008A0508', 'J2300008', 'SEES COLLEGE OF ENGINEERING', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 9.20, 8.47, 'PASSED', 'Second Class', NULL, 99.25, 'VALID', '{\"Student Name\":\"Student 8\",\"Hall Ticket No\":\"23008A0508\",\"SerialNo\":\"J2300008\",\"College Name\":\"SEES COLLEGE OF ENGINEERING\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"Exam Month Year\":\"JUNE 2024\",\"Course\":\"B.Tech\",\"Medium\":\"ENGLISH\",\"Total Marks\":610,\"Total Credits\":23.85,\"CGPA\":9.2,\"SGPA\":8.47,\"Result\":\"PASSED\",\"AggregateInWords\":\"Second Class\",\"Achievement\":\"\",\"IssueDate\":\"2024-09-06\"}', '2025-12-25 12:30:55'),
(11, 25, 'Student 9', '23009A0509', 'J2300009', 'SEES COLLEGE OF ENGINEERING', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 7.76, 9.31, 'PASSED', 'First Class', NULL, 100.00, 'VALID', '{\"Student Name\":\"Student 9\",\"Hall Ticket No\":\"23009A0509\",\"SerialNo\":\"J2300009\",\"College Name\":\"SEES COLLEGE OF ENGINEERING\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"Exam Month Year\":\"JUNE 2024\",\"Course\":\"B.Tech\",\"Medium\":\"ENGLISH\",\"Total Marks\":802,\"Total Credits\":21.73,\"CGPA\":7.76,\"SGPA\":9.31,\"Result\":\"PASSED\",\"AggregateInWords\":\"First Class\",\"Achievement\":\"\",\"IssueDate\":\"2024-10-11\"}', '2025-12-25 12:30:55'),
(12, 25, 'Student 10', '23010A0510', 'J2300010', 'SEES COLLEGE OF ENGINEERING', 'JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR', 'COMPUTER SCIENCE AND ENGINEERING', 6.62, 7.93, 'FAILED', 'Second Class', NULL, 100.00, 'VALID', '{\"Student Name\":\"Student 10\",\"Hall Ticket No\":\"23010A0510\",\"SerialNo\":\"J2300010\",\"College Name\":\"SEES COLLEGE OF ENGINEERING\",\"University Name\":\"JAWAHARLAL NEHRU TECHNOLOGICAL UNIVERSITY ANANTAPUR\",\"Branch\":\"COMPUTER SCIENCE AND ENGINEERING\",\"Exam Month Year\":\"JUNE 2024\",\"Course\":\"B.Tech\",\"Medium\":\"ENGLISH\",\"Total Marks\":790,\"Total Credits\":18.54,\"CGPA\":6.62,\"SGPA\":7.93,\"Result\":\"FAILED\",\"AggregateInWords\":\"Second Class\",\"Achievement\":\"\",\"IssueDate\":\"2024-09-01\"}', '2025-12-25 12:30:55');

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
(58, 24, '377933', '2025-09-23 14:32:14', 1, '2025-09-23 12:22:14'),
(59, 15, '195754', '2025-09-24 10:56:29', 1, '2025-09-24 08:46:29'),
(60, 15, '104480', '2025-09-24 11:47:18', 1, '2025-09-24 09:37:18'),
(61, 24, '370181', '2025-09-24 18:44:31', 1, '2025-09-24 16:34:31'),
(62, 25, '898224', '2025-09-24 19:04:28', 1, '2025-09-24 16:54:28'),
(63, 24, '487915', '2025-09-24 19:23:00', 1, '2025-09-24 17:13:00'),
(64, 25, '923171', '2025-09-24 19:25:22', 1, '2025-09-24 17:15:22'),
(65, 24, '306198', '2025-09-24 19:39:00', 1, '2025-09-24 17:29:00'),
(66, 25, '314144', '2025-09-24 19:45:28', 1, '2025-09-24 17:35:28'),
(67, 24, '397961', '2025-09-24 19:50:46', 1, '2025-09-24 17:40:46'),
(68, 25, '983463', '2025-09-24 20:16:55', 1, '2025-09-24 18:06:55'),
(69, 25, '310148', '2025-12-25 05:23:09', 1, '2025-12-25 04:13:09'),
(70, 24, '727345', '2025-12-25 05:24:52', 1, '2025-12-25 04:14:52'),
(71, 24, '641338', '2025-12-25 06:48:57', 1, '2025-12-25 05:38:57'),
(72, 25, '573400', '2025-12-25 08:17:37', 1, '2025-12-25 07:07:37'),
(73, 24, '804742', '2025-12-25 12:48:31', 1, '2025-12-25 11:38:31'),
(74, 25, '510179', '2025-12-25 12:51:58', 1, '2025-12-25 11:41:58'),
(75, 24, '167128', '2025-12-25 13:02:23', 1, '2025-12-25 11:52:23'),
(76, 25, '796013', '2025-12-25 13:05:14', 1, '2025-12-25 11:55:14'),
(77, 24, '274009', '2025-12-25 13:31:49', 1, '2025-12-25 12:21:49'),
(78, 25, '216299', '2025-12-25 13:33:01', 1, '2025-12-25 12:23:01'),
(79, 24, '230465', '2025-12-25 13:38:31', 1, '2025-12-25 12:28:31'),
(80, 25, '863947', '2025-12-25 13:40:26', 1, '2025-12-25 12:30:26');

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
(26, 'institution', 'JNTUACEA', '', 'university', '8999999', '', 'nyaswanth81@gmail.com', '+919392069522', 'http://127.0.0.1:5500/registration1.html', 'N YASWANTH, ELLORA BOYS HOSTEL,JNTU COLLEGE, ANANTAPUR', '', 'PUTTUR', 'jharkhand', 'TIRUPATI', '515001', 'India', '$2y$10$Jr3LGep78OpoMOi0pH5fF.4QjHRj/ZPdENam90U/9r/CnAqNlcQJK', 'uploads/1758208181_Screenshot_2025-01-23_213903.png', 'approved', '2025-09-18 15:09:41'),
(27, 'organization', 'rrrrrrrrrrrrrrr', 'government', '', '', '', 'nyaswanthyadav81@gmail.com', '9392069527', 'http://localhost/SIH-2025/register.php', 'puttur', '', 'rrrrrrrrrrr', 'jharkhand', 'rrrrrrrrrr', '123456', 'India', '$2y$10$tWTPHo3rDjHG2c6GqYjWJeWU3rH9tmqvyQ4/U/VK.2CcXPmO2SNMS', 'uploads/1758732727_2-1_page-0001.jpg', 'approved', '2025-09-24 16:52:07'),
(28, 'institution', 'IIT TPT', '', 'university', '1234567', '', 'r@gmail.com', '1234567890', 'http://localhost/yash/SIH-2025/register.php', '12345Y', '23456', 'QWERTY', 'jharkhand', '1234', '123456', 'India', '$2y$10$c9ATXkp5N5Fq8VMwRAMmH.BA05hVDTSvj5kVClFoa5QXK/D1Q8mSq', 'uploads/1766662203_23001A0538_SATHWIK.pdf', 'approved', '2025-12-25 11:30:03');

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
(24, 'institution', 'JNTUACEA', 'nyaswanth81@gmail.com', '+919392069522', '$2y$10$Jr3LGep78OpoMOi0pH5fF.4QjHRj/ZPdENam90U/9r/CnAqNlcQJK', '2025-09-18 15:11:12'),
(25, 'organization', 'rrrrrrrrrrrrrrr', 'nyaswanthyadav81@gmail.com', '9392069527', '$2y$10$tWTPHo3rDjHG2c6GqYjWJeWU3rH9tmqvyQ4/U/VK.2CcXPmO2SNMS', '2025-09-24 16:53:59'),
(26, 'institution', 'IIT TPT', 'r@gmail.com', '1234567890', '$2y$10$c9ATXkp5N5Fq8VMwRAMmH.BA05hVDTSvj5kVClFoa5QXK/D1Q8mSq', '2025-12-25 11:30:26');

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
-- NOTE: View creation commented out due to permission restrictions
-- The view is simply a SELECT from ocr_saved_details table
-- If you need the view, you can create it manually with proper permissions:
-- CREATE VIEW `certificates` AS 
-- SELECT `id`, `institution_id`, `student_name`, `hall_ticket_no`, `certificate_no`, 
--        `branch`, `exam_type`, `exam_month_year`, `total_marks`, `total_credits`, 
--        `sgpa`, `cgpa`, `date_of_issue`, `file_hash`, `original_file_path`, `status` 
-- FROM `ocr_saved_details`;
--
-- DROP TABLE IF EXISTS `certificates`;

-- CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `certificates`  AS SELECT `ocr_saved_details`.`id` AS `id`, `ocr_saved_details`.`institution_id` AS `institution_id`, `ocr_saved_details`.`student_name` AS `student_name`, `ocr_saved_details`.`hall_ticket_no` AS `hall_ticket_no`, `ocr_saved_details`.`certificate_no` AS `certificate_no`, `ocr_saved_details`.`branch` AS `branch`, `ocr_saved_details`.`exam_type` AS `exam_type`, `ocr_saved_details`.`exam_month_year` AS `exam_month_year`, `ocr_saved_details`.`total_marks` AS `total_marks`, `ocr_saved_details`.`total_credits` AS `total_credits`, `ocr_saved_details`.`sgpa` AS `sgpa`, `ocr_saved_details`.`cgpa` AS `cgpa`, `ocr_saved_details`.`date_of_issue` AS `date_of_issue`, `ocr_saved_details`.`file_hash` AS `file_hash`, `ocr_saved_details`.`original_file_path` AS `original_file_path`, `ocr_saved_details`.`status` AS `status` FROM `ocr_saved_details` WITH LOCAL CHECK OPTION;

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
-- Indexes for table `organization_validations`
--
ALTER TABLE `organization_validations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `hall_ticket_no` (`hall_ticket_no`),
  ADD KEY `certificate_no` (`certificate_no`),
  ADD KEY `created_at` (`created_at`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3594;

--
-- AUTO_INCREMENT for table `admin_otps`
--
ALTER TABLE `admin_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `organization_validations`
--
ALTER TABLE `organization_validations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
-- Constraints for table `organization_validations`
--
ALTER TABLE `organization_validations`
  ADD CONSTRAINT `organization_validations_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `otps`
--
ALTER TABLE `otps`
  ADD CONSTRAINT `otps_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
