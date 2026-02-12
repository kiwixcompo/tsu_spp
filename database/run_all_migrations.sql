-- ============================================================================
-- RUN ALL MIGRATIONS - TSU Staff Portal
-- ============================================================================
-- This script runs all necessary migrations in the correct order
-- Run this in phpMyAdmin to update your database structure
-- ============================================================================

-- ============================================================================
-- MIGRATION 005: Add Staff Type and Unit
-- ============================================================================

-- Add staff_type column
ALTER TABLE profiles 
ADD COLUMN IF NOT EXISTS staff_type ENUM('teaching', 'non-teaching') 
COLLATE utf8mb4_unicode_ci DEFAULT NULL 
AFTER department;

-- Add unit column
ALTER TABLE profiles 
ADD COLUMN IF NOT EXISTS unit VARCHAR(255) 
COLLATE utf8mb4_unicode_ci DEFAULT NULL 
AFTER staff_type;

-- Add profile_visibility column
ALTER TABLE profiles 
ADD COLUMN IF NOT EXISTS profile_visibility ENUM('public', 'private', 'university') 
COLLATE utf8mb4_unicode_ci DEFAULT 'public' 
AFTER profile_slug;

-- Add blood_group column (if not exists from migration 004)
ALTER TABLE profiles 
ADD COLUMN IF NOT EXISTS blood_group VARCHAR(10) 
COLLATE utf8mb4_unicode_ci DEFAULT NULL 
AFTER office_phone;

-- Create units_offices table if not exists
CREATE TABLE IF NOT EXISTS `units_offices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('unit','office','directorate') COLLATE utf8mb4_unicode_ci DEFAULT 'unit',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert units/offices data
INSERT IGNORE INTO units_offices (name, type) VALUES
('Academic Planning Unit', 'unit'),
('Admissions Office', 'office'),
('Alumni Relations Office', 'office'),
('Audit Unit', 'unit'),
('Bursary Department', 'office'),
('Career Services Unit', 'unit'),
('Central Administration', 'office'),
('Counseling Services Unit', 'unit'),
('Directorate of ICT', 'directorate'),
('Directorate of Physical Planning', 'directorate'),
('Directorate of Works and Services', 'directorate'),
('Examinations and Records Office', 'office'),
('Facilities Management Unit', 'unit'),
('Finance Office', 'office'),
('Health Services Unit', 'unit'),
('Human Resources Department', 'office'),
('Information Technology Unit', 'unit'),
('Internal Audit Unit', 'unit'),
('Legal Unit', 'unit'),
('Library Services', 'office'),
('Maintenance Unit', 'unit'),
('Medical Centre', 'office'),
('Procurement Unit', 'unit'),
('Protocol Unit', 'unit'),
('Public Relations Office', 'office'),
('Publications Unit', 'unit'),
('Quality Assurance Unit', 'unit'),
('Registry Department', 'office'),
('Research and Innovation Office', 'office'),
('Safety and Security Unit', 'unit'),
('Scholarship Office', 'office'),
('Sports and Recreation Unit', 'unit'),
('Student Affairs Office', 'office'),
('Teaching Practice Unit', 'unit'),
('Transport Unit', 'unit'),
('University Clinic', 'office'),
('University Press', 'office'),
('Vice Chancellor Office', 'office'),
('Welfare Services Unit', 'unit'),
('Works Department', 'office');

-- ============================================================================
-- MIGRATION 006: Add Staff Number Unique Constraint
-- ============================================================================

-- Add unique constraint to staff_number if not exists
-- First check if constraint exists, if not add it
SET @constraint_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'profiles' 
    AND CONSTRAINT_NAME = 'unique_staff_number'
);

SET @sql = IF(@constraint_exists = 0,
    'ALTER TABLE profiles ADD CONSTRAINT unique_staff_number UNIQUE (staff_number)',
    'SELECT "Constraint already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- MIGRATION 007: Add ID Card Manager Role
-- ============================================================================

-- Modify users table to add new role
ALTER TABLE users 
MODIFY COLUMN role ENUM('user', 'admin', 'id_card_manager') 
COLLATE utf8mb4_unicode_ci DEFAULT 'user';

-- Create id_card_print_logs table to track printing activities
CREATE TABLE IF NOT EXISTS `id_card_print_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User who printed the card',
  `profile_id` int(11) NOT NULL COMMENT 'Profile whose card was printed',
  `print_type` enum('single','bulk') COLLATE utf8mb4_unicode_ci DEFAULT 'single',
  `print_format` enum('pdf','preview') COLLATE utf8mb4_unicode_ci DEFAULT 'pdf',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_profile_id` (`profile_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `id_card_print_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `id_card_print_logs_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create id_card_settings table for customization
CREATE TABLE IF NOT EXISTS `id_card_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('text','number','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `description` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT IGNORE INTO id_card_settings (setting_key, setting_value, setting_type, description) VALUES
('card_template_version', '1.0', 'text', 'Current ID card template version'),
('enable_bulk_printing', '1', 'boolean', 'Allow bulk printing of ID cards'),
('max_bulk_print_count', '50', 'number', 'Maximum number of cards in bulk print'),
('require_approval', '0', 'boolean', 'Require admin approval before printing'),
('watermark_enabled', '0', 'boolean', 'Add watermark to ID cards');

-- ============================================================================
-- VERIFICATION
-- ============================================================================

-- Show updated table structure
SELECT 'Profiles table structure:' as info;
DESCRIBE profiles;

SELECT 'Users role enum:' as info;
SHOW COLUMNS FROM users LIKE 'role';

SELECT 'New tables created:' as info;
SHOW TABLES LIKE 'id_card%';
SHOW TABLES LIKE 'units_offices';

SELECT 'Migration completed successfully!' as status;


-- ============================================================================
-- SEED FACULTIES AND DEPARTMENTS
-- ============================================================================

-- Insert faculties and departments data
INSERT IGNORE INTO faculties_departments (faculty_name, department_name) VALUES
-- Faculty of Agriculture
('Faculty of Agriculture', 'Agricultural Economics and Extension'),
('Faculty of Agriculture', 'Animal Science'),
('Faculty of Agriculture', 'Crop Science'),
('Faculty of Agriculture', 'Fisheries and Aquaculture'),
('Faculty of Agriculture', 'Soil Science'),

-- Faculty of Arts
('Faculty of Arts', 'English and Literary Studies'),
('Faculty of Arts', 'History and International Studies'),
('Faculty of Arts', 'Linguistics and Nigerian Languages'),
('Faculty of Arts', 'Music'),
('Faculty of Arts', 'Theatre Arts'),

-- Faculty of Education
('Faculty of Education', 'Adult Education'),
('Faculty of Education', 'Educational Foundations'),
('Faculty of Education', 'Educational Management'),
('Faculty of Education', 'Guidance and Counselling'),
('Faculty of Education', 'Library and Information Science'),
('Faculty of Education', 'Science Education'),

-- Faculty of Engineering
('Faculty of Engineering', 'Agricultural and Bioresources Engineering'),
('Faculty of Engineering', 'Civil Engineering'),
('Faculty of Engineering', 'Electrical/Electronic Engineering'),
('Faculty of Engineering', 'Mechanical Engineering'),

-- Faculty of Environmental Sciences
('Faculty of Environmental Sciences', 'Architecture'),
('Faculty of Environmental Sciences', 'Estate Management'),
('Faculty of Environmental Sciences', 'Quantity Surveying'),
('Faculty of Environmental Sciences', 'Surveying and Geoinformatics'),
('Faculty of Environmental Sciences', 'Urban and Regional Planning'),

-- Faculty of Law
('Faculty of Law', 'Business Law'),
('Faculty of Law', 'International Law'),
('Faculty of Law', 'Private and Property Law'),
('Faculty of Law', 'Public Law'),

-- Faculty of Management Sciences
('Faculty of Management Sciences', 'Accounting'),
('Faculty of Management Sciences', 'Banking and Finance'),
('Faculty of Management Sciences', 'Business Administration'),
('Faculty of Management Sciences', 'Marketing'),
('Faculty of Management Sciences', 'Public Administration'),

-- Faculty of Science
('Faculty of Science', 'Biochemistry'),
('Faculty of Science', 'Biological Sciences'),
('Faculty of Science', 'Chemistry'),
('Faculty of Science', 'Computer Science'),
('Faculty of Science', 'Geology'),
('Faculty of Science', 'Mathematics'),
('Faculty of Science', 'Microbiology'),
('Faculty of Science', 'Physics'),
('Faculty of Science', 'Statistics'),

-- Faculty of Social Sciences
('Faculty of Social Sciences', 'Economics'),
('Faculty of Social Sciences', 'Geography'),
('Faculty of Social Sciences', 'Mass Communication'),
('Faculty of Social Sciences', 'Political Science'),
('Faculty of Social Sciences', 'Psychology'),
('Faculty of Social Sciences', 'Sociology and Anthropology');

SELECT 'Faculties and departments seeded successfully!' as status;
