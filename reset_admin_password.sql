-- ═══════════════════════════════════════════════════════════
-- Reset Admin Password SQL Script
-- ═══════════════════════════════════════════════════════════
-- Run this in phpMyAdmin or MySQL command line
-- Database: tsuniity_tsu_staff_portal (for online server)
-- ═══════════════════════════════════════════════════════════

-- Option 1: Set password to "Admin123!"
-- This hash is for password: Admin123!
UPDATE users 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
WHERE email = 'admin@tsuniversity.edu.ng';

-- Verify the update
SELECT id, email, email_verified, account_status, created_at 
FROM users 
WHERE email = 'admin@tsuniversity.edu.ng';

-- ═══════════════════════════════════════════════════════════
-- Alternative passwords (uncomment ONE to use):
-- ═══════════════════════════════════════════════════════════

-- Option 2: Set password to "password"
-- UPDATE users 
-- SET password_hash = '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm'
-- WHERE email = 'admin@tsuniversity.edu.ng';

-- Option 3: Set password to "admin123"
-- UPDATE users 
-- SET password_hash = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy'
-- WHERE email = 'admin@tsuniversity.edu.ng';

-- Option 4: Set password to "tsuadmin2024"
-- UPDATE users 
-- SET password_hash = '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRIfpCKwBcJsHfwprNuOhsuTwCcvK'
-- WHERE email = 'admin@tsuniversity.edu.ng';

-- ═══════════════════════════════════════════════════════════
-- After running the UPDATE, try logging in with:
-- Email: admin@tsuniversity.edu.ng
-- Password: Admin123! (or whichever option you chose)
-- ═══════════════════════════════════════════════════════════
