-- Add display_years column to education table
-- This allows users to control whether their year ranges are displayed in public profile

ALTER TABLE education 
ADD COLUMN display_years BOOLEAN DEFAULT FALSE AFTER description;

-- Add index for efficient filtering
ALTER TABLE education ADD INDEX idx_display_years (display_years);

-- Update existing records to maintain current behavior (show years by default for visibility)
-- Uncomment if you want existing records to show years:
-- UPDATE education SET display_years = TRUE;
