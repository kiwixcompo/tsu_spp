-- Add gender field to profiles table
-- This allows users to specify their gender

ALTER TABLE `profiles` 
ADD COLUMN `gender` ENUM('Male', 'Female', 'Other', 'Prefer not to say') NULL 
AFTER `blood_group`;

-- Add index for faster filtering
ALTER TABLE `profiles` ADD INDEX `idx_gender` (`gender`);

-- Update users table to support nominal_role
ALTER TABLE `users` 
MODIFY COLUMN `role` ENUM('user', 'admin', 'id_card_manager', 'nominal_role') DEFAULT 'user';

-- Create nominal role user
INSERT INTO `users` (`email`, `password`, `role`, `account_status`, `email_verified`, `created_at`) 
VALUES (
    'nominalrole@tsuniversity.edu.ng',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Password@123
    'nominal_role',
    'active',
    1,
    NOW()
);

-- Create profile for nominal role user
INSERT INTO `profiles` (`user_id`, `first_name`, `last_name`, `designation`, `profile_slug`, `created_at`)
SELECT 
    id,
    'Nominal',
    'Role',
    'Staff List Manager',
    'nominal-role',
    NOW()
FROM `users` 
WHERE `email` = 'nominalrole@tsuniversity.edu.ng';
