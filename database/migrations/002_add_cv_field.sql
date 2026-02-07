-- Add CV file field to profiles table
ALTER TABLE profiles ADD COLUMN cv_file VARCHAR(255) NULL AFTER profile_photo;