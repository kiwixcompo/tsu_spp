-- Add staff_number column to profiles table
ALTER TABLE profiles 
ADD COLUMN staff_number VARCHAR(50) DEFAULT NULL AFTER user_id,
ADD UNIQUE KEY unique_staff_number (staff_number);

-- Add index for faster lookups
CREATE INDEX idx_staff_number ON profiles(staff_number);
