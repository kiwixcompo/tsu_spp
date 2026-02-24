-- Create Nominal Role User
-- This script creates the nominal_role user account for staff list management
-- Email: nominalrole@tsuniversity.edu.ng
-- Password: Password@123

-- Insert the user with hashed password
INSERT INTO users (email, email_prefix, password_hash, role, account_status, email_verified, created_at, updated_at)
VALUES (
    'nominalrole@tsuniversity.edu.ng',
    'nominalrole',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Password@123
    'nominal_role',
    'active',
    1,
    NOW(),
    NOW()
);

-- Get the user ID
SET @user_id = LAST_INSERT_ID();

-- Create a basic profile for the nominal role user
INSERT INTO profiles (
    user_id,
    staff_number,
    staff_type,
    title,
    first_name,
    last_name,
    gender,
    designation,
    blood_group,
    profile_visibility,
    profile_slug,
    created_at,
    updated_at
)
VALUES (
    @user_id,
    'TSU/SP/NOMINAL',
    'non-teaching',
    'Mr.',
    'Nominal',
    'Role',
    'Prefer not to say',
    'Staff List Manager',
    'O+',
    'private',
    'nominal-role-manager',
    NOW(),
    NOW()
);

-- Verify the user was created
SELECT 
    u.id,
    u.email,
    u.role,
    u.account_status,
    u.email_verified,
    p.first_name,
    p.last_name,
    p.designation
FROM users u
LEFT JOIN profiles p ON u.id = p.user_id
WHERE u.email = 'nominalrole@tsuniversity.edu.ng';

-- Success message
SELECT 'Nominal Role user created successfully!' AS message,
       'Email: nominalrole@tsuniversity.edu.ng' AS email,
       'Password: Password@123' AS password,
       'Role: nominal_role' AS role;
