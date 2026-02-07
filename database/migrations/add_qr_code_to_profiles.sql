-- Add QR code field to profiles table
-- Run this migration to add QR code support

ALTER TABLE profiles 
ADD COLUMN qr_code_path VARCHAR(255) NULL AFTER profile_slug,
ADD INDEX idx_qr_code (qr_code_path);

-- Update existing profiles to generate QR codes
-- This will be done programmatically through the ProfileController
