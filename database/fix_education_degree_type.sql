-- Fix education table to ensure degree_type column exists and migrate any old data
-- This script handles the migration from 'degree' to 'degree_type' if needed

-- Check if the old 'degree' column exists and migrate data
-- Note: This is a safety script in case some installations have the old schema

-- First, check the current structure
SHOW COLUMNS FROM education LIKE 'degree%';

-- If you see a 'degree' column (not degree_type), run this:
-- ALTER TABLE education CHANGE COLUMN degree degree_type ENUM('PhD', 'M.Sc', 'M.A', 'M.Ed', 'M.Tech', 'MBA', 'B.Sc', 'B.A', 'B.Ed', 'B.Tech', 'HND', 'OND', 'Others') NOT NULL;

-- If degree_type already exists, this script is not needed
-- The application now correctly uses 'degree_type' everywhere
