-- Add role column to users table
ALTER TABLE users 
ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER account_status;

-- Update admin user to have admin role
UPDATE users 
SET role = 'admin' 
WHERE email = 'admin@tsuniversity.edu.ng';

-- Add index for role column
ALTER TABLE users ADD INDEX idx_role (role);
