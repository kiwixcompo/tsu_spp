-- Add staff_type and unit columns to profiles table
ALTER TABLE profiles 
ADD COLUMN staff_type ENUM('teaching', 'non-teaching') DEFAULT 'teaching' AFTER designation,
ADD COLUMN unit VARCHAR(255) DEFAULT NULL AFTER staff_type;

-- Add comment
ALTER TABLE profiles 
MODIFY COLUMN staff_type ENUM('teaching', 'non-teaching') DEFAULT 'teaching' COMMENT 'Type of staff: teaching or non-teaching',
MODIFY COLUMN unit VARCHAR(255) DEFAULT NULL COMMENT 'Unit/Office/Directorate for non-teaching staff';

-- Update existing records to have staff_type as teaching (default)
UPDATE profiles SET staff_type = 'teaching' WHERE staff_type IS NULL;
