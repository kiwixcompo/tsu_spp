-- Add blood_group column to profiles table
ALTER TABLE profiles 
ADD COLUMN blood_group VARCHAR(5) DEFAULT NULL AFTER designation;

-- Update comment
ALTER TABLE profiles 
MODIFY COLUMN blood_group VARCHAR(5) DEFAULT NULL COMMENT 'Blood group (A+, A-, B+, B-, AB+, AB-, O+, O-)';
