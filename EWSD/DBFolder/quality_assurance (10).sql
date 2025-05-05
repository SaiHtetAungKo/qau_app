-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 05, 2025 at 07:02 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quality_assurance`
--

-- --------------------------------------------------------

--
-- Table structure for table `annoucement`
--

CREATE TABLE `annoucement` (
  `announce_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `announce_title` varchar(225) NOT NULL,
  `description` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `annoucement`
--

INSERT INTO `annoucement` (`announce_id`, `department_id`, `announce_title`, `description`) VALUES
(1, 1, 'title', 'title'),
(2, 1, 'title ', 'title'),
(3, 1, 'ets', 'ets'),
(4, 1, 'Closure Date Ending Soon', 'Dear all, \r\n Closure Date will be end soon.');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) DEFAULT NULL,
  `department_location` text NOT NULL,
  `status` varchar(250) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `department_location`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Department of Physics', 'Building A, Room 203', 'Active', '2025-02-28 07:21:12', '2025-05-03 13:43:50'),
(2, 'Department of Chemistry', 'Building B, Room 101', 'Active', '2025-04-05 13:56:52', '2025-05-03 14:09:25'),
(3, 'Department of Biology', 'Science Block, Room 305', 'Active', '2025-04-05 16:05:43', '2025-05-03 13:59:16'),
(4, 'Department of Environmental Science', 'Eco Hall, Room 110', 'Active', '2025-04-05 16:06:33', '2025-04-05 16:31:55'),
(5, 'Department of Geology', 'Earth Science Center, Room 204', 'Closed', '2025-04-05 16:07:36', '2025-05-03 14:09:42'),
(6, 'Department of English', 'Building C, Room 201', 'Deactivated', '2025-05-03 13:56:59', '2025-05-03 14:07:48');

-- --------------------------------------------------------

--
-- Table structure for table `ideas`
--

CREATE TABLE `ideas` (
  `idea_id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `requestIdea_id` int(11) DEFAULT NULL,
  `SubCategoryID` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `img_path` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `anonymousSubmission` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ideas`
--

INSERT INTO `ideas` (`idea_id`, `userID`, `requestIdea_id`, `SubCategoryID`, `title`, `description`, `img_path`, `status`, `anonymousSubmission`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Test Title 1', 'Test Description 1', NULL, 'active', 1, '2025-03-05 07:00:21', '2025-05-04 04:56:00'),
(2, 3, 2, 2, 'Test Title 2', 'Test Description 2', NULL, 'active', 0, '2025-03-05 07:00:21', '2025-05-04 04:56:42'),
(3, 2, 1, 3, 'Test Title 3', 'Test Description 3', NULL, 'active', 0, '2025-03-05 07:00:21', '2025-05-04 16:52:07'),
(4, 4, 2, 4, 'Test Title 4', 'Test Description 4', NULL, 'active', 0, '2025-03-05 07:00:21', '2025-05-04 04:56:41'),
(5, 5, 1, 5, 'Test Title 5', 'Test Description 5', NULL, 'active', 0, '2025-03-05 07:00:21', '2025-05-04 04:56:40'),
(6, 6, 2, 6, 'Test Title 6', 'Test Description 6', NULL, 'hide', 0, '2025-03-05 07:00:21', '2025-05-04 17:03:44'),
(7, 7, 1, 7, 'Test Title 7', 'Test Description 7', NULL, 'hide', 0, '2025-03-05 07:00:21', '2025-05-04 04:56:25'),
(8, 8, 2, 8, 'Test Title 8', 'Test Description 8', NULL, 'hide', 1, '2025-03-05 07:00:21', '2025-05-04 17:03:02'),
(9, 8, 1, 9, 'Test Title 9', 'Test Description 9', NULL, 'test9', 1, '2025-03-05 07:00:21', '2025-03-08 07:05:55'),
(10, 8, 2, 10, 'Test Title 10', 'Test Description 2', NULL, 'hide', 1, '2025-03-05 07:00:21', '2025-05-04 17:03:12'),
(11, 1, NULL, 1, 'Toilet Cleaning', 'the smell are bad', 'Images/1746091111_niggerFAM.jpg', 'pending', 0, '2025-05-01 09:18:31', '2025-05-01 09:18:31'),
(12, 1, NULL, 9, 'wifi issues', 'wifi issue', NULL, 'pending', 1, '2025-05-01 15:10:34', '2025-05-01 15:10:34'),
(13, 1, NULL, 9, 'wifi issues', 'wifi issue', NULL, 'pending', 0, '2025-05-01 15:10:46', '2025-05-01 15:10:46'),
(14, 1, NULL, 9, 'wifi issues', 'wifi issue', NULL, 'pending', 0, '2025-05-01 15:10:58', '2025-05-01 15:10:58'),
(15, 1, NULL, 9, 'wifi ', 'wifi', NULL, 'pending', 0, '2025-05-01 15:13:08', '2025-05-01 15:13:08'),
(16, 1, NULL, 1, 'rest', 'rest', NULL, 'pending', 0, '2025-05-01 15:16:55', '2025-05-01 15:16:55'),
(17, 1, NULL, 9, 'wifi issues', 'wifi', NULL, 'pending', 0, '2025-05-01 15:17:28', '2025-05-01 15:17:28'),
(18, 1, NULL, 9, 'wifi issues', 'wifi wifi', NULL, 'pending', 0, '2025-05-01 15:21:24', '2025-05-01 15:21:24'),
(19, 1, NULL, 7, 'wifi issues', 'WIFI', NULL, 'pending', 0, '2025-05-01 15:24:04', '2025-05-01 15:24:04'),
(20, 1, NULL, 15, 'CLASS', 'CLASS', NULL, 'pending', 0, '2025-05-01 15:25:36', '2025-05-01 15:25:36'),
(21, 1, NULL, 20, 'Id card', 'card issue', NULL, 'pending', 0, '2025-05-01 15:27:08', '2025-05-01 15:27:08'),
(32, 1, 5, 1, 'cleaning', 'pleaseclean', NULL, 'pending', 0, '2025-05-03 07:34:23', '2025-05-03 07:34:23'),
(33, 1, 5, 6, 'fire aware', 'pls aware', NULL, 'active', 0, '2025-05-03 07:39:43', '2025-05-04 17:04:44'),
(34, 19, 6, 9, 'wifi issues', 'wifi issue', NULL, 'active', 0, '2025-05-04 17:05:44', '2025-05-04 17:05:44');

-- --------------------------------------------------------

--
-- Table structure for table `idea_comment`
--

CREATE TABLE `idea_comment` (
  `ideacommentID` int(11) NOT NULL,
  `ideacommentText` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `idea_id` int(11) DEFAULT NULL,
  `requestIdea_id` int(11) DEFAULT NULL,
  `anonymousSubmission` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `idea_comment`
--

INSERT INTO `idea_comment` (`ideacommentID`, `ideacommentText`, `user_id`, `idea_id`, `requestIdea_id`, `anonymousSubmission`, `created_at`, `updated_at`) VALUES
(1, 'Test comment 1', 2, 8, 1, 1, '2025-03-08 13:36:07', '2025-03-08 13:36:45'),
(2, 'Test comment 2', 1, 8, 1, 1, '2025-03-08 13:36:53', '2025-03-08 13:37:15'),
(3, 'Test comment 3', 4, 8, 1, 1, '2025-03-08 13:36:53', '2025-03-08 14:12:43'),
(4, 'April 24', 6, 10, NULL, 0, '2025-04-24 13:45:00', '2025-04-24 13:45:00'),
(5, 'Hello World', 6, 10, NULL, 1, '2025-04-24 13:45:30', '2025-04-24 13:45:30'),
(6, 'April 25 Hello World !', 5, 10, NULL, 0, '2025-04-25 02:08:17', '2025-04-25 02:08:17'),
(7, '\'fpeokg\';//rpe \"', 5, 10, NULL, 0, '2025-04-25 02:08:36', '2025-04-25 02:08:36'),
(8, 'Test Test', 5, 10, NULL, 0, '2025-04-27 02:27:26', '2025-04-27 02:27:26'),
(9, 'Test', 5, 10, NULL, 1, '2025-04-27 10:40:48', '2025-04-27 10:40:48'),
(10, 'Yes', 10, 0, NULL, 1, '2025-05-01 04:50:00', '2025-05-01 04:50:00'),
(11, 'yes', 10, 0, NULL, 1, '2025-05-01 04:50:06', '2025-05-01 04:50:06'),
(12, 'yees', 10, 0, NULL, 0, '2025-05-01 04:50:09', '2025-05-01 04:50:09'),
(13, 'hi', 11, 8, NULL, 1, '2025-05-01 11:37:05', '2025-05-01 11:37:05'),
(14, 'hi', 11, 8, NULL, 0, '2025-05-01 11:37:09', '2025-05-01 11:37:09'),
(15, 'hi', 11, 8, NULL, 0, '2025-05-01 11:37:34', '2025-05-01 11:37:34'),
(16, 'agree', 6, 33, NULL, 0, '2025-05-03 03:12:17', '2025-05-03 03:12:17'),
(17, 'agresee', 6, 33, NULL, 0, '2025-05-03 03:13:59', '2025-05-03 03:13:59'),
(18, 'agree', 20, 34, NULL, 0, '2025-05-04 12:37:09', '2025-05-04 12:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `idea_vote`
--

CREATE TABLE `idea_vote` (
  `ideavoteID` int(11) NOT NULL,
  `idea_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `votetype` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `idea_vote`
--

INSERT INTO `idea_vote` (`ideavoteID`, `idea_id`, `user_id`, `votetype`, `created_at`, `updated_at`) VALUES
(1, 8, 1, 1, '2025-03-08 13:42:20', '2025-03-08 13:42:35'),
(2, 8, 2, 1, '2025-03-08 13:42:20', '2025-03-08 13:42:45'),
(3, 8, 3, 1, '2025-03-08 13:42:20', '2025-03-08 13:42:52'),
(4, 8, 4, 1, '2025-03-08 13:42:20', '2025-03-08 13:42:56'),
(6, 8, 6, 1, '2025-03-08 13:42:20', '2025-03-08 13:43:07'),
(7, 7, 6, 1, '2025-03-08 13:42:20', '2025-03-08 13:44:01'),
(8, 7, 5, 1, '2025-03-08 13:42:20', '2025-03-08 13:44:15'),
(9, 7, 4, 1, '2025-03-08 13:42:20', '2025-03-08 13:44:22'),
(10, 6, 4, 1, '2025-03-08 13:42:20', '2025-03-08 13:44:28'),
(11, 6, 3, 1, '2025-03-08 13:42:20', '2025-03-08 13:44:47'),
(12, 5, 3, 1, '2025-03-08 13:42:20', '2025-03-08 13:44:55'),
(13, 7, 4, 2, '2025-03-08 13:42:20', '2025-03-08 13:49:55'),
(14, 6, 3, 2, '2025-03-08 13:42:20', '2025-03-08 13:50:06'),
(15, 5, 3, 2, '2025-03-08 13:42:20', '2025-03-08 13:50:11'),
(16, 8, 6, 2, '2025-03-08 13:42:20', '2025-03-08 13:50:25'),
(28, 8, 11, 1, '2025-05-01 09:16:33', '2025-05-01 09:16:33'),
(29, 0, 10, 1, '2025-05-01 09:19:48', '2025-05-01 09:19:48'),
(32, 0, 11, 1, '2025-05-01 09:22:26', '2025-05-01 09:22:26');

-- --------------------------------------------------------

--
-- Table structure for table `maincategory`
--

CREATE TABLE `maincategory` (
  `MainCategoryID` int(11) NOT NULL,
  `MainCategoryTitle` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maincategory`
--

INSERT INTO `maincategory` (`MainCategoryID`, `MainCategoryTitle`, `Description`, `Status`, `created_at`, `updated_at`) VALUES
(1, 'Cleaning & Maintenance', 'Cleaning services for various areas', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(2, 'Safety', 'Ensuring safety measures in place', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(3, 'IT & Technology Support', 'Technical support for IT-related issues', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(4, 'Facilities & Infrastructure', 'Maintenance and infrastructure support', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(5, 'Security & Access Control', 'Managing security and access protocols', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(6, 'Parking & Transportation', 'Parking and transportation-related concerns', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(7, 'Meeting Room & Conference Management', 'Handling meeting room logistics', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(8, 'HR & Staff Development', 'Employee growth and HR support', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(9, 'Student Support Services', 'Providing academic and student support', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(10, 'Communication & Feedback', 'Internal communication and feedback channels', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(11, 'Research & Development', 'Advancing academic and industry research', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(12, 'Library & Learning Resources', 'Providing access to books and study spaces', 'Active', '2025-03-08 12:22:54', '2025-03-08 12:22:54'),
(13, 'Sustainability', 'Efforts for energy saving and budget management', 'inactive', '2025-03-08 12:22:54', '2025-05-04 16:50:36'),
(16, 'function', 'new category', 'Status ', '2025-05-04 16:51:08', '2025-05-04 16:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `request_ideas`
--

CREATE TABLE `request_ideas` (
  `requestIdea_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `closure_date` date NOT NULL,
  `final_closure_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_ideas`
--

INSERT INTO `request_ideas` (`requestIdea_id`, `title`, `description`, `closure_date`, `final_closure_date`, `created_at`, `updated_at`) VALUES
(1, 'Test-1', 'test-1', '2025-03-03', '2025-04-30', '2025-03-03 07:12:01', '2025-04-24 14:40:51'),
(2, 'Test -2', 'test-2', '2025-04-03', '2025-04-30', '2025-03-03 11:00:22', '2025-03-03 11:00:22'),
(3, 'Test-3', 'test-3', '2025-03-31', '2025-04-30', '2025-03-09 08:48:35', '2025-03-09 08:48:35'),
(4, 'Test For Closure and Final Closure Date', 'Test For Closure and Final Closure Date', '2025-04-30', '2025-05-01', '2025-04-27 07:08:06', '2025-04-27 07:08:06'),
(5, 'WIll be close soon on 5 30', 'close soon', '2025-05-30', '2025-05-31', '2025-05-03 07:33:04', '2025-05-03 07:33:04'),
(6, 'deadline ended', 'ended', '2025-05-29', '2025-05-31', '2025-05-03 13:48:40', '2025-05-04 16:54:34');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_type` varchar(255) DEFAULT NULL,
  `role_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_type`, `role_description`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'none', '2025-02-28 07:15:54', '2025-02-28 07:15:54'),
(2, 'QA Manager', 'none', '2025-02-28 07:16:30', '2025-02-28 07:16:30'),
(3, 'QA coordinator', 'none', '2025-02-28 07:16:55', '2025-02-28 07:16:55'),
(4, 'Staff', 'none', '2025-02-28 07:17:09', '2025-02-28 07:17:09');

-- --------------------------------------------------------

--
-- Table structure for table `subcategory`
--

CREATE TABLE `subcategory` (
  `SubCategoryID` int(11) NOT NULL,
  `MainCategoryID` int(11) DEFAULT NULL,
  `SubCategoryTitle` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subcategory`
--

INSERT INTO `subcategory` (`SubCategoryID`, `MainCategoryID`, `SubCategoryTitle`, `Description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Restroom Cleaning', 'Regular restroom cleaning services', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(2, 1, 'Classroom Cleaning', 'Cleaning and maintaining classrooms', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(3, 1, 'Office Cleaning', 'Workplace and office area cleaning', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(4, 1, 'Hallways & Staircase Cleaning', 'Cleaning staircases and hallways', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(5, 1, 'Outdoor Cleaning & Gardening', 'Outdoor spaces and gardening maintenance', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(6, 2, 'Fire Safety & Extinguishers', 'Fire safety measures and extinguisher maintenance', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(7, 2, 'First Aid & Medical Support', 'Emergency first aid and medical support', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(8, 2, 'Emergency Exits & Lighting', 'Ensuring clear exits and emergency lighting', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(9, 3, 'Internet & Wi-Fi Issues', 'Technical issues related to Wi-Fi and internet', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(10, 3, 'Hardware Requests', 'Requests for IT hardware and devices', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(11, 3, 'Software Updates & Installation', 'Software updates and installations', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(12, 3, 'System Access & Password Issues', 'Login and password-related support', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(13, 3, 'Online Learning Platforms', 'Support for online education platforms', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(14, 3, 'Data Security & Privacy', 'Security and privacy concerns in IT', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(15, 4, 'Classroom Equipment', 'Maintenance of classroom equipment', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(16, 4, 'Office Space & Workstations', 'Managing workstations and office space', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(17, 4, 'Lighting & Power Outages', 'Handling power-related concerns', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(18, 4, 'Restroom Maintenance', 'Maintenance of restrooms and washrooms', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(19, 4, 'Furniture Requests & Repairs', 'Requests and repairs for furniture', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(20, 5, 'ID Card & Badge System', 'Management of ID cards and badges', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(21, 5, 'Unauthorized Access Prevention', 'Preventing unauthorized access', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(22, 5, 'Lost & Found Management', 'Handling lost and found items', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(23, 6, 'Parking Issues', 'Concerns related to parking spaces', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(24, 6, 'Ferry Transportation', 'Transportation assistance and ferry services', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(25, 7, 'Meeting Room Equipment', 'Technical equipment for meeting rooms', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(26, 7, 'Meeting Room Logistics & Setup', 'Arrangements for meeting rooms', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(27, 7, 'Meeting Room Reservations', 'Booking and scheduling of meeting rooms', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(28, 8, 'Training & Skill Development', 'Employee training and skill enhancement', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(29, 8, 'Performance Reviews & Promotions', 'Evaluation and promotion processes', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(30, 8, 'Workplace Culture & Engagement', 'Improving work environment and culture', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(31, 9, 'Education Enhancement', 'Support for improving education quality', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(32, 9, 'Academic Advising', 'Providing academic counseling and guidance', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(33, 9, 'Promotion Services', 'Services related to student promotions', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(34, 9, 'Classroom Technology Improvements', 'Enhancing classroom tech facilities', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(35, 9, 'E-learning & Digital', 'Online learning and digital education support', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(36, 10, 'Internal Announcements & Updates', 'Official internal updates and notices', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(37, 10, 'Anonymous Suggestion Box', 'Platform for anonymous suggestions', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(38, 10, 'Meeting & Town Hall Sessions', 'Town hall meetings and discussions', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(39, 10, 'Staff-Management Communication', 'Facilitating communication with management', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(40, 11, 'Lab Equipment & Maintenance', 'Maintenance of research lab equipment', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(41, 11, 'Funding & Grant Applications', 'Applying for research grants and funding', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(42, 11, 'Collaboration with Industry', 'Partnerships with industry leaders', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(43, 11, 'Access to Research Databases', 'Providing access to research materials', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(44, 12, 'Digital & Physical Book Additional', 'Expanding book resources', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(45, 12, 'Library Study Spaces', 'Improving study spaces in libraries', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(46, 12, 'Library Services', 'Managing and enhancing library services', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(47, 13, 'Energy Saving', 'Initiatives to reduce energy consumption', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(48, 13, 'Budget Saving & Management', 'Strategies for financial sustainability', '2025-03-08 12:23:14', '2025-03-08 12:23:14'),
(51, 16, 'SubCat', 'SubDes', '2025-05-04 16:51:08', '2025-05-04 16:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `user_phone` varchar(255) DEFAULT NULL,
  `user_password` varchar(255) DEFAULT NULL,
  `user_profile` text NOT NULL,
  `account_status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `department_id`, `user_name`, `user_email`, `user_phone`, `user_password`, `user_profile`, `account_status`, `created_at`, `updated_at`) VALUES
(2, 2, 1, 'John', 'john@gmail.com', '+95 9671564181', '$2y$10$1gjlE.6TiqoxKz.xMqdUQeleSyu7AFMfuJ3CdFC3OpeUEkdQSmtfy', 'user_images/_toji.jpg', 'active', '2025-03-01 18:19:38', '2025-04-05 14:57:27'),
(17, 2, 1, 'bob', 'bob@gmail.com', '0564556456', '$2y$10$XhY95ruYBgLkwwoN5D.BO.eoqje3XzH8kLHmt4xowX62aBplAzhqe', 'user_images/_image.png', 'active', '2025-05-04 16:38:58', '2025-05-04 16:38:58'),
(18, 3, 1, 'kitty', 'harrynyinyi183@gmail.com', '014564578', '$2y$10$4Hu.LB/4dNnCa3kpuyjAmOhHJ41i9hgsp/mmcINfAosLjOG7gbXPe', 'user_images/_kitty.jpg', 'active', '2025-05-04 16:40:32', '2025-05-04 16:40:32'),
(19, 4, 1, 'Spider', 'harrynyinyi184@gmail.com', '09265645', '$2y$10$VY6cMDmferlJYDGu65GxqO.VnxUmPXO2d9Rvl6TjeVgsMDWUAVqs2', 'user_images/_png-transparent-spider-man-heroes-download-with-transparent-background-free-thumbnail.png', 'active', '2025-05-04 16:42:39', '2025-05-04 17:02:28'),
(20, 4, 1, 'perry', 'perry@gmail.com', '089677566', '$2y$10$YxyGANH5t5JJ8.lIPOtrmuQU5MTHFJO1cIVJDV4iZO9ZtEkJZlGwC', 'user_images/_pic1.jpg', 'active', '2025-05-04 16:59:15', '2025-05-04 16:59:15'),
(100, 1, 3, 'Alvin', 'alvin@gmail.com', '0912345678', '$2b$12$3H7HyhOjV3GshZrBZ0qMquVRtJ09rhkTJ/Cf0jA2iTNGIOp0ivHgK\n', '', 'active', '2025-05-05 04:41:06', '2025-05-05 04:42:26'),
(101, 1, 1, 'James', 'jame@gmail.com', '99888112223', '$2y$10$HkJghzn51Ai8b0Cj4b9vdOS3kEF8lPOXNjox67umCXOg6d.GlORkq', 'user_images/_cg.png', 'active', '2025-05-05 04:51:03', '2025-05-05 04:57:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `annoucement`
--
ALTER TABLE `annoucement`
  ADD PRIMARY KEY (`announce_id`),
  ADD KEY `departments` (`department_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `ideas`
--
ALTER TABLE `ideas`
  ADD PRIMARY KEY (`idea_id`);

--
-- Indexes for table `idea_comment`
--
ALTER TABLE `idea_comment`
  ADD PRIMARY KEY (`ideacommentID`),
  ADD KEY `idea_comment_ibfk_2` (`idea_id`),
  ADD KEY `idea_comment_ibfk_3` (`requestIdea_id`),
  ADD KEY `idea_comment_ibfk_1` (`user_id`);

--
-- Indexes for table `idea_vote`
--
ALTER TABLE `idea_vote`
  ADD PRIMARY KEY (`ideavoteID`),
  ADD KEY `idea_vote_ibfk_1` (`idea_id`),
  ADD KEY `idea_vote_ibfk_2` (`user_id`);

--
-- Indexes for table `maincategory`
--
ALTER TABLE `maincategory`
  ADD PRIMARY KEY (`MainCategoryID`);

--
-- Indexes for table `request_ideas`
--
ALTER TABLE `request_ideas`
  ADD PRIMARY KEY (`requestIdea_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `subcategory`
--
ALTER TABLE `subcategory`
  ADD PRIMARY KEY (`SubCategoryID`),
  ADD KEY `MainCategoryID` (`MainCategoryID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `annoucement`
--
ALTER TABLE `annoucement`
  MODIFY `announce_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ideas`
--
ALTER TABLE `ideas`
  MODIFY `idea_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `idea_comment`
--
ALTER TABLE `idea_comment`
  MODIFY `ideacommentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `idea_vote`
--
ALTER TABLE `idea_vote`
  MODIFY `ideavoteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `maincategory`
--
ALTER TABLE `maincategory`
  MODIFY `MainCategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `request_ideas`
--
ALTER TABLE `request_ideas`
  MODIFY `requestIdea_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subcategory`
--
ALTER TABLE `subcategory`
  MODIFY `SubCategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subcategory`
--
ALTER TABLE `subcategory`
  ADD CONSTRAINT `subcategory_ibfk_1` FOREIGN KEY (`MainCategoryID`) REFERENCES `maincategory` (`MainCategoryID`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
