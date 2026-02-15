-- ============================================================================
-- UPDATE SCRIPT: Add profile_views column to profiles table
-- Date: February 15, 2026
-- Purpose: Track the number of times a profile has been viewed
-- ============================================================================

-- Add profile_views column to profiles table
ALTER TABLE `profiles` 
ADD COLUMN `profile_views` INT(11) NOT NULL DEFAULT 0 
AFTER `qr_code_path`;

-- Add index for better performance when sorting by views
ALTER TABLE `profiles` 
ADD INDEX `idx_profile_views` (`profile_views`);

-- ============================================================================
-- VERIFICATION QUERY
-- Run this after the update to verify the changes
-- ============================================================================
-- SHOW COLUMNS FROM profiles LIKE 'profile_views';
-- SELECT id, first_name, last_name, profile_views FROM profiles LIMIT 5;
