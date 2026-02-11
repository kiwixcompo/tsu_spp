-- ============================================================================
-- Create ID Card Manager User
-- ============================================================================
-- Email: idcards@tsuniversity.edu.ng
-- Password: IDCard@2026!
-- Role: id_card_manager
-- ============================================================================

-- First, ensure the role exists (run migration if not already done)
-- SOURCE database/migrations/007_add_id_card_manager_role.sql;

-- Create ID Card Manager user
INSERT INTO users (email, email_prefix, password_hash, email_verified, account_status, role, created_at)
VALUES (
    'idcards@tsuniversity.edu.ng',
    'idcards',
    '$2y$10$KXVBDEeOqdbNsJeM5ItvGe7BPlTQiDopyFtcxZumRmidKcB3vR7rm',
    1,
    'active',
    'id_card_manager',
    NOW()
);

-- Get the user ID
SET @user_id = LAST_INSERT_ID();

-- Create profile for ID Card Manager
INSERT INTO profiles (
    user_id,
    staff_number,
    title,
    first_name,
    last_name,
    designation,
    faculty,
    department,
    profile_visibility,
    profile_slug,
    created_at
)
VALUES (
    @user_id,
    'TSU/ICM/001',
    'Mr.',
    'ID Card',
    'Manager',
    'ID Card Manager',
    'Administration',
    'ICT',
    'private',
    'id-card-manager',
    NOW()
);

-- Verify creation
SELECT 
    u.id,
    u.email,
    u.role,
    u.account_status,
    p.staff_number,
    CONCAT(p.first_name, ' ', p.last_name) as name
FROM users u
INNER JOIN profiles p ON u.id = p.user_id
WHERE u.email = 'idcards@tsuniversity.edu.ng';
