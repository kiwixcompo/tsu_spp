-- Verify that the Directorate of Career Development & Employability Services exists

SELECT * FROM units_offices 
WHERE name LIKE '%Career Development%' 
   OR name LIKE '%Employability%';

-- Show all units/offices for reference
SELECT id, name FROM units_offices ORDER BY name;
