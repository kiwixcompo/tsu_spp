-- Add Directorate of Career Development & Employability Services
-- This directorate will appear in the units/offices dropdown for non-teaching staff

INSERT INTO units_offices (name)
VALUES ('Directorate of Career Development & Employability Services');

-- Verify the insertion
SELECT * FROM units_offices WHERE name = 'Directorate of Career Development & Employability Services';

-- Success message
SELECT 'Directorate of Career Development & Employability Services added successfully!' AS message;
