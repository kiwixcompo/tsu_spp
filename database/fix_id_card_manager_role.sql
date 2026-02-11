-- ============================================================================
-- Fix ID Card Manager Role
-- ============================================================================
-- This script updates the existing ID Card Manager user to have the correct role
-- Run this AFTER running migration 007_add_id_card_manager_role.sql
-- ============================================================================

-- Step 1: First, ensure the role enum includes 'id_card_manager'
-- (This should already be done by migration 007, but we'll verify)
ALTER TABLE users 
MODIFY COLUMN role ENUM('user', 'admin', 'id_card_manager') 
COLLATE utf8mb4_unicode_ci DEFAULT 'user';

-- Step 2: Update the ID Card Manager user to have the correct role
UPDATE users 
SET role = 'id_card_manager' 
WHERE email = 'idcards@tsuniversity.edu.ng';

-- Step 3: Verify the update
SELECT 
    id,
    email,
    role,
    account_status,
    email_verified
FROM users 
WHERE email LIKE 'idcards@%';

-- Expected result: role should be 'id_card_manager'
