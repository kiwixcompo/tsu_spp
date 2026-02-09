-- ============================================
-- BLOOD GROUP COLUMN MIGRATION
-- Run this SQL in phpMyAdmin to add blood group support
-- ============================================

-- Add blood_group column to profiles table
ALTER TABLE profiles 
ADD COLUMN blood_group VARCHAR(5) DEFAULT NULL AFTER designation;

-- Verify the column was added
DESCRIBE profiles;

-- ============================================
-- DONE! Now you can:
-- 1. Create/edit profiles with blood group
-- 2. Generate ID cards with blood group displayed
-- ============================================
