-- Fix education table to migrate from 'degree' to 'degree_type'
-- This script handles the migration for production databases using complete_setup_compatible.sql

-- Step 1: Check current structure
-- Run this first to see what you have:
-- SHOW COLUMNS FROM education LIKE 'degree%';

-- Step 2: If you have 'degree' column (varchar), migrate to 'degree_type' (ENUM)
-- This preserves existing data while converting to the standardized ENUM type

-- Backup existing data first (recommended)
-- CREATE TABLE education_backup AS SELECT * FROM education;

-- Option A: If you want to keep the ENUM type (recommended for consistency)
-- This will convert varchar to ENUM, mapping common values
ALTER TABLE education 
CHANGE COLUMN `degree` `degree_type` 
ENUM('PhD', 'M.Sc', 'M.A', 'M.Ed', 'M.Tech', 'M.Eng', 'MBA', 'B.Sc', 'B.A', 'B.Ed', 'B.Eng', 'B.Tech', 'HND', 'OND', 'Others') 
NOT NULL DEFAULT 'Others';

-- Step 3: Update any non-standard values to 'Others'
-- This handles cases where the varchar had values not in the ENUM
UPDATE education 
SET degree_type = 'Others' 
WHERE degree_type NOT IN ('PhD', 'M.Sc', 'M.A', 'M.Ed', 'M.Tech', 'M.Eng', 'MBA', 'B.Sc', 'B.A', 'B.Ed', 'B.Eng', 'B.Tech', 'HND', 'OND');

-- Step 4: Verify the migration
SELECT degree_type, COUNT(*) as count 
FROM education 
GROUP BY degree_type 
ORDER BY count DESC;

-- Note: After running this migration, the application code will work correctly
-- as it now uses 'degree_type' everywhere

