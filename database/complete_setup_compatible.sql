-- ============================================================================
-- TSU STAFF PORTAL - COMPLETE DATABASE SETUP
-- Compatible with MySQL 5.5+ (uses utf8mb4_unicode_ci)
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ============================================================================
-- 1. USERS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_prefix` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `email_verified` tinyint(1) DEFAULT '0',
  `verification_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_expires` datetime DEFAULT NULL,
  `reset_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `account_status` enum('pending','active','suspended') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `profile_completion` int(11) DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email_verified` (`email_verified`),
  KEY `idx_account_status` (`account_status`),
  KEY `idx_verification_code` (`verification_code`),
  KEY `idx_reset_token` (`reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 2. PROFILES TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `staff_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `faculty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `professional_summary` text COLLATE utf8mb4_unicode_ci,
  `research_interests` text COLLATE utf8mb4_unicode_ci,
  `expertise_keywords` text COLLATE utf8mb4_unicode_ci,
  `profile_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_visibility` enum('public','private','university') COLLATE utf8mb4_unicode_ci DEFAULT 'public',
  `qr_code_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `profile_slug` (`profile_slug`),
  UNIQUE KEY `unique_staff_number` (`staff_number`),
  KEY `idx_faculty` (`faculty`),
  KEY `idx_department` (`department`),
  KEY `idx_profile_visibility` (`profile_visibility`),
  KEY `idx_staff_number` (`staff_number`),
  CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 3. EDUCATION TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `education` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `degree` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_of_study` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `institution` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_year` int(11) NOT NULL,
  `end_year` int(11) DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_on_profile` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_end_year` (`end_year`),
  CONSTRAINT `education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 4. EXPERIENCE TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `experience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `job_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organization` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_is_current` (`is_current`),
  CONSTRAINT `experience_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 5. PUBLICATIONS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `publications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publication_type` enum('journal','conference','book','chapter','thesis','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `authors` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `journal_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conference_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publication_year` int(11) NOT NULL,
  `volume` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pages` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `abstract` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_publication_year` (`publication_year`),
  KEY `idx_publication_type` (`publication_type`),
  CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 6. SKILLS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `skills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `skill_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proficiency_level` enum('beginner','intermediate','advanced','expert') COLLATE utf8mb4_unicode_ci DEFAULT 'intermediate',
  `years_of_experience` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. ACTIVITY LOGS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. FACULTIES AND DEPARTMENTS TABLE
-- ============================================================================
CREATE TABLE IF NOT EXISTS `faculties_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_faculty_department` (`faculty`,`department`),
  KEY `idx_faculty` (`faculty`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 9. INSERT DEFAULT ADMIN USER
-- Password: admin123 (CHANGE THIS IMMEDIATELY AFTER FIRST LOGIN!)
-- ============================================================================
INSERT INTO `users` (`email`, `email_prefix`, `password_hash`, `role`, `email_verified`, `account_status`, `profile_completion`, `created_at`) 
VALUES ('admin@tsuniversity.edu.ng', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 'active', 100, NOW())
ON DUPLICATE KEY UPDATE `role` = 'admin', `email_verified` = 1, `account_status` = 'active';

-- ============================================================================
-- 10. INSERT SAMPLE FACULTIES AND DEPARTMENTS
-- ============================================================================
INSERT INTO `faculties_departments` (`faculty`, `department`) VALUES
('Faculty of Agriculture', 'Agricultural Economics and Extension'),
('Faculty of Agriculture', 'Animal Production'),
('Faculty of Agriculture', 'Crop Production'),
('Faculty of Agriculture', 'Fisheries and Aquaculture'),
('Faculty of Agriculture', 'Forestry and Wildlife Management'),
('Faculty of Agriculture', 'Soil Science'),
('Faculty of Arts', 'English and Literary Studies'),
('Faculty of Arts', 'History and International Studies'),
('Faculty of Arts', 'Languages and Linguistics'),
('Faculty of Arts', 'Music'),
('Faculty of Arts', 'Theatre Arts'),
('Faculty of Education', 'Adult Education'),
('Faculty of Education', 'Educational Foundations'),
('Faculty of Education', 'Library and Information Science'),
('Faculty of Education', 'Science Education'),
('Faculty of Education', 'Vocational and Technical Education'),
('Faculty of Law', 'Private and Property Law'),
('Faculty of Law', 'Public Law'),
('Faculty of Management Sciences', 'Accounting'),
('Faculty of Management Sciences', 'Business Administration'),
('Faculty of Management Sciences', 'Public Administration'),
('Faculty of Science', 'Biochemistry'),
('Faculty of Science', 'Biological Sciences'),
('Faculty of Science', 'Chemistry'),
('Faculty of Science', 'Computer Science'),
('Faculty of Science', 'Mathematics'),
('Faculty of Science', 'Microbiology'),
('Faculty of Science', 'Physics'),
('Faculty of Social Sciences', 'Economics'),
('Faculty of Social Sciences', 'Geography'),
('Faculty of Social Sciences', 'Political Science'),
('Faculty of Social Sciences', 'Psychology'),
('Faculty of Social Sciences', 'Sociology')
ON DUPLICATE KEY UPDATE `faculty` = VALUES(`faculty`);

-- ============================================================================
-- SETUP COMPLETE!
-- ============================================================================
-- Next steps:
-- 1. Login with: admin@tsuniversity.edu.ng / admin123
-- 2. CHANGE THE ADMIN PASSWORD IMMEDIATELY!
-- 3. Create storage/qrcodes/ folder with 755 permissions
-- 4. Upload all application files
-- ============================================================================
