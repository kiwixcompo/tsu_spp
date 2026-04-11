-- Migration: Add Faculties (as directorates) with Departments + Deanery (as units)
-- Run this AFTER 008_add_directorates_units.sql

INSERT INTO directorates (name, display_order) VALUES
('Faculty of Agriculture', 28),
('Faculty of Arts', 29),
('Faculty of Communication & Media', 30),
('Faculty of Education', 31),
('Faculty of Engineering', 32),
('Faculty of Health Sciences', 33),
('Faculty of Law', 34),
('Faculty of Management Sciences', 35),
('Faculty of Science', 36),
('Faculty of Computing & Artificial Intelligence', 37),
('Faculty of Social Sciences', 38),
('Faculty of Religion & Philosophy', 39);

INSERT INTO directorate_units (directorate_id, unit_name, display_order)
SELECT d.id, u.unit_name, u.display_order FROM directorates d
JOIN (
    SELECT 'Faculty of Agriculture' AS dir, 'Deanery' AS unit_name, 1 AS display_order UNION ALL
    SELECT 'Faculty of Agriculture', 'Department of Agric Extension & Economics', 2 UNION ALL
    SELECT 'Faculty of Agriculture', 'Department of Agronomy', 3 UNION ALL
    SELECT 'Faculty of Agriculture', 'Department of Animal Science', 4 UNION ALL
    SELECT 'Faculty of Agriculture', 'Department of Crop Production', 5 UNION ALL
    SELECT 'Faculty of Agriculture', 'Department of Forestry & Wildlife Conservation', 6 UNION ALL
    SELECT 'Faculty of Agriculture', 'Department of Home Economics', 7 UNION ALL
    SELECT 'Faculty of Agriculture', 'Department of Soil Science & Land Resources Mgmt', 8 UNION ALL

    SELECT 'Faculty of Arts', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Arts', 'Department of English & Literary Studies', 2 UNION ALL
    SELECT 'Faculty of Arts', 'Department of Theatre & Film Studies', 3 UNION ALL
    SELECT 'Faculty of Arts', 'Department of French', 4 UNION ALL
    SELECT 'Faculty of Arts', 'Department of History', 5 UNION ALL
    SELECT 'Faculty of Arts', 'Department of Arabic Studies', 6 UNION ALL
    SELECT 'Faculty of Arts', 'Department of Languages & Linguistics', 7 UNION ALL

    SELECT 'Faculty of Communication & Media', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Communication & Media', 'Department of Mass Communication', 2 UNION ALL

    SELECT 'Faculty of Education', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Education', 'Department of Arts Education', 2 UNION ALL
    SELECT 'Faculty of Education', 'Department of Educational Foundations', 3 UNION ALL
    SELECT 'Faculty of Education', 'Department of Counselling, Educational Psychology and Human Development', 4 UNION ALL
    SELECT 'Faculty of Education', 'Department of Science Education', 5 UNION ALL
    SELECT 'Faculty of Education', 'Department of Human Kinetics & Physical Education', 6 UNION ALL
    SELECT 'Faculty of Education', 'Department of Social Science Education', 7 UNION ALL
    SELECT 'Faculty of Education', 'Department of Vocational & Technology Education', 8 UNION ALL
    SELECT 'Faculty of Education', 'Department of Library & Info Science', 9 UNION ALL

    SELECT 'Faculty of Engineering', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Engineering', 'Department of Agric & Bio-Resources Engineering', 2 UNION ALL
    SELECT 'Faculty of Engineering', 'Department of Electrical/Electronics Engineering', 3 UNION ALL
    SELECT 'Faculty of Engineering', 'Department of Civil Engineering', 4 UNION ALL
    SELECT 'Faculty of Engineering', 'Department of Mechanical Engineering', 5 UNION ALL

    SELECT 'Faculty of Health Sciences', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Health Sciences', 'Department of Environmental Health', 2 UNION ALL
    SELECT 'Faculty of Health Sciences', 'Department of Public Health', 3 UNION ALL
    SELECT 'Faculty of Health Sciences', 'Department of Nursing', 4 UNION ALL
    SELECT 'Faculty of Health Sciences', 'Department of Medical Lab Science', 5 UNION ALL

    SELECT 'Faculty of Law', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Law', 'Department of Public Law', 2 UNION ALL
    SELECT 'Faculty of Law', 'Department of Private & Property Law', 3 UNION ALL

    SELECT 'Faculty of Management Sciences', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Management Sciences', 'Department of Accounting', 2 UNION ALL
    SELECT 'Faculty of Management Sciences', 'Department of Business Administration', 3 UNION ALL
    SELECT 'Faculty of Management Sciences', 'Department of Public Administration', 4 UNION ALL
    SELECT 'Faculty of Management Sciences', 'Department of Hospitality and Tourism Management', 5 UNION ALL

    SELECT 'Faculty of Science', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Science', 'Department of Biological Sciences', 2 UNION ALL
    SELECT 'Faculty of Science', 'Department of Chemical Sciences', 3 UNION ALL
    SELECT 'Faculty of Science', 'Department of Mathematics and Statistics', 4 UNION ALL
    SELECT 'Faculty of Science', 'Department of Physics', 5 UNION ALL

    SELECT 'Faculty of Computing & Artificial Intelligence', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Computing & Artificial Intelligence', 'Department of Computer Science', 2 UNION ALL
    SELECT 'Faculty of Computing & Artificial Intelligence', 'Department of Data Science and Artificial Intelligence', 3 UNION ALL
    SELECT 'Faculty of Computing & Artificial Intelligence', 'Department of Information and Communication Technology', 4 UNION ALL
    SELECT 'Faculty of Computing & Artificial Intelligence', 'Department of Software Engineering', 5 UNION ALL

    SELECT 'Faculty of Social Sciences', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Social Sciences', 'Department of Economics', 2 UNION ALL
    SELECT 'Faculty of Social Sciences', 'Department of Geography', 3 UNION ALL
    SELECT 'Faculty of Social Sciences', 'Department of Political & International Relations', 4 UNION ALL
    SELECT 'Faculty of Social Sciences', 'Department of Peace & Conflict Studies', 5 UNION ALL
    SELECT 'Faculty of Social Sciences', 'Department of Sociology', 6 UNION ALL

    SELECT 'Faculty of Religion & Philosophy', 'Deanery', 1 UNION ALL
    SELECT 'Faculty of Religion & Philosophy', 'Department of Islamic Studies', 2 UNION ALL
    SELECT 'Faculty of Religion & Philosophy', 'Department of Christian Religious Studies', 3
) u ON d.name = u.dir;
