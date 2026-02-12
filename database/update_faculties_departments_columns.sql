-- ============================================================================
-- UPDATE SCRIPT: Fix faculties_departments table column names
-- Date: February 12, 2026
-- Purpose: Rename columns to match application code expectations
-- ============================================================================

-- Rename faculty_name to faculty
ALTER TABLE `faculties_departments` 
CHANGE COLUMN `faculty_name` `faculty` VARCHAR(255) NOT NULL;

-- Rename department_name to department
ALTER TABLE `faculties_departments` 
CHANGE COLUMN `department_name` `department` VARCHAR(255) NOT NULL;

-- Drop old indexes if they exist
ALTER TABLE `faculties_departments` 
DROP INDEX IF EXISTS `idx_faculty`,
DROP INDEX IF EXISTS `idx_department`;

-- Add new indexes with correct column names
ALTER TABLE `faculties_departments` 
ADD INDEX `idx_faculty` (`faculty`),
ADD INDEX `idx_department` (`department`);

-- Add unique constraint to prevent duplicate faculty-department combinations
ALTER TABLE `faculties_departments` 
ADD UNIQUE KEY `unique_faculty_department` (`faculty`, `department`);

-- ============================================================================
-- VERIFICATION QUERY
-- Run this after the update to verify the changes
-- ============================================================================
-- SHOW COLUMNS FROM faculties_departments;
-- SELECT * FROM faculties_departments LIMIT 5;
