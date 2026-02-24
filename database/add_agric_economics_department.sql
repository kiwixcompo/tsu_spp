-- Add Agric Economics & Extension department to Faculty of Agriculture
-- This department was missing from the initial setup

INSERT INTO `faculties_departments` (`faculty`, `department`, `created_at`) 
VALUES ('Faculty of Agriculture', 'Agric Economics & Extension', NOW())
ON DUPLICATE KEY UPDATE `department` = 'Agric Economics & Extension';
