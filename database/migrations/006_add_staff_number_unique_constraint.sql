-- Add unique constraint to staff_number in profiles table
-- This ensures no two staff members can have the same staff number (e.g., TSU/SP/300)
-- However, TSU/SP/300 and TSU/JP/300 are different and both allowed

-- First, check if there are any duplicate staff numbers and report them
SELECT staff_number, COUNT(*) as count 
FROM profiles 
WHERE staff_number IS NOT NULL AND staff_number != ''
GROUP BY staff_number 
HAVING count > 1;

-- If duplicates exist, you'll need to manually resolve them before running the next command
-- You can update duplicate staff numbers like this:
-- UPDATE profiles SET staff_number = 'TSU/SP/301' WHERE id = <duplicate_id>;

-- Add unique constraint (only run this after resolving any duplicates)
ALTER TABLE profiles 
ADD UNIQUE KEY unique_staff_number (staff_number);

-- Note: This constraint ensures that:
-- - TSU/SP/300 can only exist once
-- - TSU/JP/300 can only exist once
-- - But both TSU/SP/300 and TSU/JP/300 can coexist (different prefixes)
