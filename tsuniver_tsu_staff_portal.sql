-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 12, 2026 at 01:45 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 8.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tsuniver_tsu_staff_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'user_login', '{\"user_id\":1}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 15:24:21'),
(2, 1, 'user_logout', '[]', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 15:27:29'),
(3, 1, 'user_login', '{\"user_id\":1}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 15:27:45'),
(4, 1, 'user_logout', '[]', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 15:50:51'),
(5, 3, 'user_login', '{\"user_id\":3}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 15:53:35'),
(6, 3, 'user_logout', '[]', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:03:47'),
(7, 3, 'user_login', '{\"user_id\":3}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:05:08'),
(8, 3, 'user_logout', '[]', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:05:18'),
(9, 1, 'user_login', '{\"user_id\":1}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:05:52'),
(10, 1, 'user_logout', '[]', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:06:00'),
(11, 3, 'user_login', '{\"user_id\":3}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:06:24'),
(12, 3, 'user_logout', '[]', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:06:30'),
(13, 3, 'user_login', '{\"user_id\":3}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:09:05'),
(14, 3, 'user_logout', '[]', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:16:02'),
(15, 3, 'user_login', '{\"user_id\":3}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:16:21'),
(16, 3, 'user_login', '{\"user_id\":3}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:33:03'),
(17, 3, 'user_login', '{\"user_id\":3}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:37:43'),
(18, 3, 'user_login', '{\"user_id\":3}', '105.117.0.40', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-11 16:41:57'),
(19, NULL, 'user_registered', '{\"email\":\"social@tsuniversity.edu.ng\"}', '98.97.77.148', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 10:48:33'),
(20, 4, 'email_verified', '{\"user_id\":4}', '98.97.77.148', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 10:49:47'),
(21, 4, 'profile_created', '{\"profile_id\":3}', '98.97.77.148', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 11:00:07'),
(22, 4, 'account_activated', '{\"user_id\":4}', '98.97.77.148', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 11:00:07'),
(23, 4, 'user_logout', '[]', '98.97.77.148', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 12:24:11'),
(24, 4, 'user_login', '{\"user_id\":4}', '98.97.77.148', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 12:24:37');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `degree` varchar(100) NOT NULL,
  `field_of_study` varchar(255) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_year` int(11) NOT NULL,
  `end_year` int(11) DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `display_on_profile` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `experience`
--

CREATE TABLE `experience` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `organization` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculties_departments`
--

CREATE TABLE `faculties_departments` (
  `id` int(11) NOT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculties_departments`
--

INSERT INTO `faculties_departments` (`id`, `faculty_name`, `department_name`, `created_at`) VALUES
(1, 'Faculty of Agriculture', 'Agricultural Economics and Extension', '2026-02-12 11:22:29'),
(2, 'Faculty of Agriculture', 'Animal Science', '2026-02-12 11:22:29'),
(3, 'Faculty of Agriculture', 'Crop Science', '2026-02-12 11:22:29'),
(4, 'Faculty of Agriculture', 'Fisheries and Aquaculture', '2026-02-12 11:22:29'),
(5, 'Faculty of Agriculture', 'Soil Science', '2026-02-12 11:22:29'),
(6, 'Faculty of Arts', 'English and Literary Studies', '2026-02-12 11:22:29'),
(7, 'Faculty of Arts', 'History and International Studies', '2026-02-12 11:22:29'),
(8, 'Faculty of Arts', 'Linguistics and Nigerian Languages', '2026-02-12 11:22:29'),
(9, 'Faculty of Arts', 'Music', '2026-02-12 11:22:29'),
(10, 'Faculty of Arts', 'Theatre Arts', '2026-02-12 11:22:29'),
(11, 'Faculty of Education', 'Adult Education', '2026-02-12 11:22:29'),
(12, 'Faculty of Education', 'Educational Foundations', '2026-02-12 11:22:29'),
(13, 'Faculty of Education', 'Educational Management', '2026-02-12 11:22:29'),
(14, 'Faculty of Education', 'Guidance and Counselling', '2026-02-12 11:22:29'),
(15, 'Faculty of Education', 'Library and Information Science', '2026-02-12 11:22:29'),
(16, 'Faculty of Education', 'Science Education', '2026-02-12 11:22:29'),
(17, 'Faculty of Engineering', 'Agricultural and Bioresources Engineering', '2026-02-12 11:22:29'),
(18, 'Faculty of Engineering', 'Civil Engineering', '2026-02-12 11:22:29'),
(19, 'Faculty of Engineering', 'Electrical/Electronic Engineering', '2026-02-12 11:22:29'),
(20, 'Faculty of Engineering', 'Mechanical Engineering', '2026-02-12 11:22:29'),
(21, 'Faculty of Environmental Sciences', 'Architecture', '2026-02-12 11:22:29'),
(22, 'Faculty of Environmental Sciences', 'Estate Management', '2026-02-12 11:22:29'),
(23, 'Faculty of Environmental Sciences', 'Quantity Surveying', '2026-02-12 11:22:29'),
(24, 'Faculty of Environmental Sciences', 'Surveying and Geoinformatics', '2026-02-12 11:22:29'),
(25, 'Faculty of Environmental Sciences', 'Urban and Regional Planning', '2026-02-12 11:22:29'),
(26, 'Faculty of Law', 'Business Law', '2026-02-12 11:22:29'),
(27, 'Faculty of Law', 'International Law', '2026-02-12 11:22:29'),
(28, 'Faculty of Law', 'Private and Property Law', '2026-02-12 11:22:29'),
(29, 'Faculty of Law', 'Public Law', '2026-02-12 11:22:29'),
(30, 'Faculty of Management Sciences', 'Accounting', '2026-02-12 11:22:29'),
(31, 'Faculty of Management Sciences', 'Banking and Finance', '2026-02-12 11:22:29'),
(32, 'Faculty of Management Sciences', 'Business Administration', '2026-02-12 11:22:29'),
(33, 'Faculty of Management Sciences', 'Marketing', '2026-02-12 11:22:29'),
(34, 'Faculty of Management Sciences', 'Public Administration', '2026-02-12 11:22:29'),
(35, 'Faculty of Science', 'Biochemistry', '2026-02-12 11:22:29'),
(36, 'Faculty of Science', 'Biological Sciences', '2026-02-12 11:22:29'),
(37, 'Faculty of Science', 'Chemistry', '2026-02-12 11:22:29'),
(38, 'Faculty of Science', 'Computer Science', '2026-02-12 11:22:29'),
(39, 'Faculty of Science', 'Geology', '2026-02-12 11:22:29'),
(40, 'Faculty of Science', 'Mathematics', '2026-02-12 11:22:29'),
(41, 'Faculty of Science', 'Microbiology', '2026-02-12 11:22:29'),
(42, 'Faculty of Science', 'Physics', '2026-02-12 11:22:29'),
(43, 'Faculty of Science', 'Statistics', '2026-02-12 11:22:29'),
(44, 'Faculty of Social Sciences', 'Economics', '2026-02-12 11:22:29'),
(45, 'Faculty of Social Sciences', 'Geography', '2026-02-12 11:22:29'),
(46, 'Faculty of Social Sciences', 'Mass Communication', '2026-02-12 11:22:29'),
(47, 'Faculty of Social Sciences', 'Political Science', '2026-02-12 11:22:29'),
(48, 'Faculty of Social Sciences', 'Psychology', '2026-02-12 11:22:29'),
(49, 'Faculty of Social Sciences', 'Sociology and Anthropology', '2026-02-12 11:22:29');

-- --------------------------------------------------------

--
-- Table structure for table `id_card_print_logs`
--

CREATE TABLE `id_card_print_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'User who printed the card',
  `profile_id` int(11) NOT NULL COMMENT 'Profile whose card was printed',
  `print_type` enum('single','bulk') DEFAULT 'single',
  `print_format` enum('pdf','preview') DEFAULT 'pdf',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `id_card_settings`
--

CREATE TABLE `id_card_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('text','number','boolean','json') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `id_card_settings`
--

INSERT INTO `id_card_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES
(1, 'card_template_version', '1.0', 'text', 'Current ID card template version', '2026-02-11 16:36:49'),
(2, 'enable_bulk_printing', '1', 'boolean', 'Allow bulk printing of ID cards', '2026-02-11 16:36:49'),
(3, 'max_bulk_print_count', '50', 'number', 'Maximum number of cards in bulk print', '2026-02-11 16:36:49'),
(4, 'require_approval', '0', 'boolean', 'Require admin approval before printing', '2026-02-11 16:36:49'),
(5, 'watermark_enabled', '0', 'boolean', 'Add watermark to ID cards', '2026-02-11 16:36:49');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `staff_number` varchar(50) DEFAULT NULL,
  `title` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `staff_type` enum('teaching','non-teaching') DEFAULT 'teaching' COMMENT 'Type of staff: teaching or non-teaching',
  `unit` varchar(255) DEFAULT NULL COMMENT 'Unit/Office/Directorate for non-teaching staff',
  `faculty` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `office_location` varchar(100) DEFAULT NULL,
  `office_phone` varchar(20) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `professional_summary` text DEFAULT NULL,
  `research_interests` text DEFAULT NULL,
  `expertise_keywords` text DEFAULT NULL,
  `profile_slug` varchar(255) NOT NULL,
  `profile_visibility` enum('public','private','university') DEFAULT 'public',
  `qr_code_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `staff_number`, `title`, `first_name`, `middle_name`, `last_name`, `designation`, `staff_type`, `unit`, `faculty`, `department`, `office_location`, `office_phone`, `blood_group`, `profile_photo`, `cv_file`, `professional_summary`, `research_interests`, `expertise_keywords`, `profile_slug`, `profile_visibility`, `qr_code_path`, `created_at`, `updated_at`) VALUES
(2, 3, 'TSU/ICM/001', 'Mr.', 'ID Card', NULL, 'Manager', 'ID Card Manager', 'teaching', NULL, 'Administration', 'ICT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'id-card-manager', 'private', NULL, '2026-02-11 15:52:06', '2026-02-11 15:52:06'),
(3, 4, 'TSU/SP/562', 'Dr.', 'Benson', 'Harrison', 'Lee', 'Senior Lecturer', 'teaching', NULL, 'Faculty of Agriculture', 'Agricultural Economics and Extension', '', '', 'A+', 'uploads/profiles/profile_4_1770894005.png', NULL, '', '', '', 'benson-lee', 'public', 'qr_4_1770894007.png', '2026-02-12 11:00:06', '2026-02-12 11:00:07');

-- --------------------------------------------------------

--
-- Table structure for table `publications`
--

CREATE TABLE `publications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `publication_type` enum('journal','conference','book','chapter','thesis','other') NOT NULL,
  `authors` text NOT NULL,
  `journal_name` varchar(255) DEFAULT NULL,
  `conference_name` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `publication_year` int(11) NOT NULL,
  `volume` varchar(50) DEFAULT NULL,
  `issue` varchar(50) DEFAULT NULL,
  `pages` varchar(50) DEFAULT NULL,
  `doi` varchar(255) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `proficiency_level` enum('beginner','intermediate','advanced','expert') DEFAULT 'intermediate',
  `years_of_experience` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units_offices`
--

CREATE TABLE `units_offices` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('unit','office','directorate') DEFAULT 'unit',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units_offices`
--

INSERT INTO `units_offices` (`id`, `name`, `type`, `created_at`) VALUES
(1, 'Academic Planning Unit', 'unit', '2026-02-12 10:56:55'),
(2, 'Admissions Office', 'office', '2026-02-12 10:56:55'),
(3, 'Alumni Relations Office', 'office', '2026-02-12 10:56:55'),
(4, 'Audit Unit', 'unit', '2026-02-12 10:56:55'),
(5, 'Bursary Department', 'office', '2026-02-12 10:56:55'),
(6, 'Career Services Unit', 'unit', '2026-02-12 10:56:55'),
(7, 'Central Administration', 'office', '2026-02-12 10:56:55'),
(8, 'Counseling Services Unit', 'unit', '2026-02-12 10:56:55'),
(9, 'Directorate of ICT', 'directorate', '2026-02-12 10:56:55'),
(10, 'Directorate of Physical Planning', 'directorate', '2026-02-12 10:56:55'),
(11, 'Directorate of Works and Services', 'directorate', '2026-02-12 10:56:55'),
(12, 'Examinations and Records Office', 'office', '2026-02-12 10:56:55'),
(13, 'Facilities Management Unit', 'unit', '2026-02-12 10:56:55'),
(14, 'Finance Office', 'office', '2026-02-12 10:56:55'),
(15, 'Health Services Unit', 'unit', '2026-02-12 10:56:55'),
(16, 'Human Resources Department', 'office', '2026-02-12 10:56:55'),
(17, 'Information Technology Unit', 'unit', '2026-02-12 10:56:55'),
(18, 'Internal Audit Unit', 'unit', '2026-02-12 10:56:55'),
(19, 'Legal Unit', 'unit', '2026-02-12 10:56:55'),
(20, 'Library Services', 'office', '2026-02-12 10:56:55'),
(21, 'Maintenance Unit', 'unit', '2026-02-12 10:56:55'),
(22, 'Medical Centre', 'office', '2026-02-12 10:56:55'),
(23, 'Procurement Unit', 'unit', '2026-02-12 10:56:55'),
(24, 'Protocol Unit', 'unit', '2026-02-12 10:56:55'),
(25, 'Public Relations Office', 'office', '2026-02-12 10:56:55'),
(26, 'Publications Unit', 'unit', '2026-02-12 10:56:55'),
(27, 'Quality Assurance Unit', 'unit', '2026-02-12 10:56:55'),
(28, 'Registry Department', 'office', '2026-02-12 10:56:55'),
(29, 'Research and Innovation Office', 'office', '2026-02-12 10:56:55'),
(30, 'Safety and Security Unit', 'unit', '2026-02-12 10:56:55'),
(31, 'Scholarship Office', 'office', '2026-02-12 10:56:55'),
(32, 'Sports and Recreation Unit', 'unit', '2026-02-12 10:56:55'),
(33, 'Student Affairs Office', 'office', '2026-02-12 10:56:55'),
(34, 'Teaching Practice Unit', 'unit', '2026-02-12 10:56:55'),
(35, 'Transport Unit', 'unit', '2026-02-12 10:56:55'),
(36, 'University Clinic', 'office', '2026-02-12 10:56:55'),
(37, 'University Press', 'office', '2026-02-12 10:56:55'),
(38, 'Vice Chancellor Office', 'office', '2026-02-12 10:56:55'),
(39, 'Welfare Services Unit', 'unit', '2026-02-12 10:56:55'),
(40, 'Works Department', 'office', '2026-02-12 10:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_prefix` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin','id_card_manager') DEFAULT 'user',
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_code` varchar(10) DEFAULT NULL,
  `verification_expires` datetime DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `account_status` enum('pending','active','suspended') DEFAULT 'pending',
  `profile_completion` int(11) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `email_prefix`, `password_hash`, `role`, `email_verified`, `verification_code`, `verification_expires`, `reset_token`, `reset_token_expires`, `account_status`, `profile_completion`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin@tsuniversity.edu.ng', 'admin', '$2y$12$bfoFvLoACxSBME3rV6EtcuNPKsnjQRZ/jAWfqljqvb56BlXx2jVpa', 'admin', 1, NULL, NULL, NULL, NULL, 'active', 100, '2026-02-11 17:05:52', '2026-02-11 15:07:31', '2026-02-11 16:05:52'),
(3, 'idcards@tsuniversity.edu.ng', 'idcards', '$2y$12$oOh5QWXymAvbqTQfJbIcG.bZaL9J.o/KtdtT2O5W7SsXhtRGmDXu6', 'id_card_manager', 1, NULL, NULL, NULL, NULL, 'active', 0, '2026-02-11 17:41:57', '2026-02-11 15:52:06', '2026-02-11 16:41:57'),
(4, 'social@tsuniversity.edu.ng', 'social', '$2y$10$8CSYxL.jAEqHjCKXXzZ7qeKLf9jfO/xakcYTviXe3hEITB0bnoaPO', 'user', 1, NULL, NULL, NULL, NULL, 'active', 33, '2026-02-12 13:24:37', '2026-02-12 10:48:32', '2026-02-12 12:24:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_end_year` (`end_year`);

--
-- Indexes for table `experience`
--
ALTER TABLE `experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_is_current` (`is_current`);

--
-- Indexes for table `faculties_departments`
--
ALTER TABLE `faculties_departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_faculty` (`faculty_name`),
  ADD KEY `idx_department` (`department_name`);

--
-- Indexes for table `id_card_print_logs`
--
ALTER TABLE `id_card_print_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_profile_id` (`profile_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `id_card_settings`
--
ALTER TABLE `id_card_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `profile_slug` (`profile_slug`),
  ADD UNIQUE KEY `unique_staff_number` (`staff_number`),
  ADD KEY `idx_faculty` (`faculty`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_profile_visibility` (`profile_visibility`),
  ADD KEY `idx_staff_number` (`staff_number`);

--
-- Indexes for table `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_publication_year` (`publication_year`),
  ADD KEY `idx_publication_type` (`publication_type`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `units_offices`
--
ALTER TABLE `units_offices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email_verified` (`email_verified`),
  ADD KEY `idx_account_status` (`account_status`),
  ADD KEY `idx_verification_code` (`verification_code`),
  ADD KEY `idx_reset_token` (`reset_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `experience`
--
ALTER TABLE `experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculties_departments`
--
ALTER TABLE `faculties_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `id_card_print_logs`
--
ALTER TABLE `id_card_print_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `id_card_settings`
--
ALTER TABLE `id_card_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units_offices`
--
ALTER TABLE `units_offices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `experience`
--
ALTER TABLE `experience`
  ADD CONSTRAINT `experience_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `id_card_print_logs`
--
ALTER TABLE `id_card_print_logs`
  ADD CONSTRAINT `id_card_print_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `id_card_print_logs_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `publications`
--
ALTER TABLE `publications`
  ADD CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
