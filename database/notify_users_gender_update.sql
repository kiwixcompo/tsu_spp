-- Script to identify users who need to update their gender field
-- This query will help send targeted emails to users without gender information

SELECT 
    u.id,
    u.email,
    CONCAT(p.first_name, ' ', p.last_name) as full_name,
    p.gender
FROM users u
INNER JOIN profiles p ON u.id = p.user_id
WHERE p.gender IS NULL
  AND u.account_status = 'active'
  AND u.email_verified = 1
ORDER BY u.created_at DESC;

-- Count of users needing update
SELECT COUNT(*) as users_needing_update
FROM users u
INNER JOIN profiles p ON u.id = p.user_id
WHERE p.gender IS NULL
  AND u.account_status = 'active';
