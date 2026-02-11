-- Update Admin Password
-- Generated: 2026-02-11 15:24:40
-- New Password: Admin123!

UPDATE users 
SET password_hash = '$2y$10$58vBWPHI9A9HgGZ41xqHm.gXclBJYGNiWlbTLRV2gh7fRS/5dmPpG' 
WHERE email = 'admin@tsuniversity.ng';

-- Verify the update
SELECT id, email, role, account_status 
FROM users 
WHERE email = 'admin@tsuniversity.ng';
