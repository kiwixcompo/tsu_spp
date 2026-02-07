-- TSU Faculties and Departments Data (Updated 2025)

-- This file contains the updated faculty and department structure for TSU
-- Used for populating dropdown menus and validation

-- Clear existing data first
DELETE FROM faculties_departments;

-- Faculty of Agriculture
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Agriculture', 'Agronomy'),
('Faculty of Agriculture', 'Animal Science'),
('Faculty of Agriculture', 'Crop Production'),
('Faculty of Agriculture', 'Forestry & Wildlife Conservation'),
('Faculty of Agriculture', 'Home Economics'),
('Faculty of Agriculture', 'Soil Science & Land Resources Management');

-- Faculty of Arts
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Arts', 'English & Literary Studies'),
('Faculty of Arts', 'Theatre & Film Studies'),
('Faculty of Arts', 'French'),
('Faculty of Arts', 'History'),
('Faculty of Arts', 'Arabic Studies'),
('Faculty of Arts', 'Languages & Linguistics');

-- Faculty of Communication & Media
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Communication & Media', 'Mass Communication');

-- Faculty of Education
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Education', 'Arts Education'),
('Faculty of Education', 'Educational Foundations'),
('Faculty of Education', 'Counselling, Educational Psychology and Human Development'),
('Faculty of Education', 'Science Education'),
('Faculty of Education', 'Human Kinetics & Physical Education'),
('Faculty of Education', 'Social Science Education'),
('Faculty of Education', 'Vocational & Technology Education'),
('Faculty of Education', 'Library & Information Science');

-- Faculty of Engineering
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Engineering', 'Agric & Bio-Resources Engineering'),
('Faculty of Engineering', 'Electrical/Electronics Engineering'),
('Faculty of Engineering', 'Civil Engineering'),
('Faculty of Engineering', 'Mechanical Engineering');

-- Faculty of Health Sciences
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Health Sciences', 'Environmental Health'),
('Faculty of Health Sciences', 'Public Health'),
('Faculty of Health Sciences', 'Nursing'),
('Faculty of Health Sciences', 'Medical Laboratory Science');

-- Faculty of Law
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Law', 'Public Law'),
('Faculty of Law', 'Private & Property Law');

-- Faculty of Management Sciences
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Management Sciences', 'Accounting'),
('Faculty of Management Sciences', 'Business Administration'),
('Faculty of Management Sciences', 'Public Administration'),
('Faculty of Management Sciences', 'Hospitality and Tourism Management');

-- Faculty of Science
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Science', 'Biological Sciences'),
('Faculty of Science', 'Chemical Sciences'),
('Faculty of Science', 'Mathematics and Statistics'),
('Faculty of Science', 'Physics');

-- Faculty of Computing & Artificial Intelligence
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Computing & Artificial Intelligence', 'Computer Science'),
('Faculty of Computing & Artificial Intelligence', 'Data Science and Artificial Intelligence'),
('Faculty of Computing & Artificial Intelligence', 'Information and Communication Technology'),
('Faculty of Computing & Artificial Intelligence', 'Software Engineering');

-- Faculty of Social Sciences
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Social Sciences', 'Economics'),
('Faculty of Social Sciences', 'Geography'),
('Faculty of Social Sciences', 'Political & International Relations'),
('Faculty of Social Sciences', 'Peace & Conflict Studies'),
('Faculty of Social Sciences', 'Sociology');

-- Faculty of Religion & Philosophy
INSERT INTO faculties_departments (faculty, department) VALUES
('Faculty of Religion & Philosophy', 'Islamic Studies'),
('Faculty of Religion & Philosophy', 'CRS');