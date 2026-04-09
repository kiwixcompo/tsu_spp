-- Migration: Replace flat units_offices with Directorates → Units hierarchy
-- For non-teaching staff

CREATE TABLE IF NOT EXISTS directorates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS directorate_units (
    id INT AUTO_INCREMENT PRIMARY KEY,
    directorate_id INT NOT NULL,
    unit_name VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (directorate_id) REFERENCES directorates(id) ON DELETE CASCADE,
    INDEX idx_directorate_id (directorate_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed directorates and their units
INSERT INTO directorates (name, display_order) VALUES
('Directorate of Academic Planning', 1),
('Directorate of SERVICOM', 2),
('Directorate of Endowment', 3),
('Directorate of Advancement', 4),
('Directorate of Quality Assurance', 5),
('Directorate of Legal Services', 6),
('Directorate of Works', 7),
('Directorate of Physical Planning and Development', 8),
('Directorate of Internal Audit', 9),
('Directorate of Information and Protocol', 10),
('Directorate of Information Communication Technology (ICT)', 11),
('Directorate of Narcotics', 12),
('Directorate of Institute for Peace Studies and Conflict Resolution', 13),
('Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 14),
('Directorate of International Collaboration and Linkages', 15),
('Directorate of Research and Development', 16),
('Directorate of Institute for Distance Education and Lifelong Learning', 17),
('Directorate of SANDWICH', 18),
('Directorate of Career Development and Employability', 19),
('Directorate of School of Basic Studies', 20),
('Directorate of Gen. T. Y. Danjuma Preliminary Studies, Takum', 21),
('Directorate of Central Administration and Council Matters', 22),
('Directorate of Sports', 23),
('Directorate of Diploma Unit', 24),
('Directorate of Procurement', 25),
('Directorate of Establishment Unit', 26),
('Directorate of Academic Affairs Division', 27);

-- Seed units per directorate
INSERT INTO directorate_units (directorate_id, unit_name, display_order)
SELECT d.id, u.unit_name, u.display_order FROM directorates d
JOIN (
    SELECT 'Directorate of Academic Planning' AS dir, 'Secretariat' AS unit_name, 1 AS display_order UNION ALL
    SELECT 'Directorate of Academic Planning', 'Academic Unit and Planning', 2 UNION ALL
    SELECT 'Directorate of Academic Planning', 'Data information Center', 3 UNION ALL
    SELECT 'Directorate of Academic Planning', 'Affiliation', 4 UNION ALL

    SELECT 'Directorate of SERVICOM', 'SERVICOM', 1 UNION ALL

    SELECT 'Directorate of Endowment', 'Endowment', 1 UNION ALL

    SELECT 'Directorate of Advancement', 'Advancement', 1 UNION ALL

    SELECT 'Directorate of Quality Assurance', 'Quality Assurance', 1 UNION ALL

    SELECT 'Directorate of Legal Services', 'Head of Litigation', 1 UNION ALL
    SELECT 'Directorate of Legal Services', 'Head of Library', 2 UNION ALL

    SELECT 'Directorate of Works', 'Electrical/Mechanical', 1 UNION ALL
    SELECT 'Directorate of Works', 'Building', 2 UNION ALL
    SELECT 'Directorate of Works', 'Transport', 3 UNION ALL
    SELECT 'Directorate of Works', 'Directorate', 4 UNION ALL

    SELECT 'Directorate of Physical Planning and Development', 'Physical Planning', 1 UNION ALL

    SELECT 'Directorate of Internal Audit', 'Internal Audit', 1 UNION ALL

    SELECT 'Directorate of Information and Protocol', 'Information and Protocol', 1 UNION ALL

    SELECT 'Directorate of Information Communication Technology (ICT)', 'Web/Software Development', 1 UNION ALL
    SELECT 'Directorate of Information Communication Technology (ICT)', 'Networking', 2 UNION ALL
    SELECT 'Directorate of Information Communication Technology (ICT)', 'Welfare', 3 UNION ALL
    SELECT 'Directorate of Information Communication Technology (ICT)', 'Business', 4 UNION ALL
    SELECT 'Directorate of Information Communication Technology (ICT)', 'Training', 5 UNION ALL

    SELECT 'Directorate of Narcotics', 'Narcotics', 1 UNION ALL

    SELECT 'Directorate of Institute for Peace Studies and Conflict Resolution', 'Peace Studies and Conflict Resolution', 1 UNION ALL

    SELECT 'Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 'Hair Dressing Saloon', 1 UNION ALL
    SELECT 'Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 'Shoe and Leather Work', 2 UNION ALL
    SELECT 'Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 'I.C.T', 3 UNION ALL
    SELECT 'Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 'Printing Press', 4 UNION ALL
    SELECT 'Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 'Restaurant', 5 UNION ALL
    SELECT 'Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 'Cosmetic and Soap Making', 6 UNION ALL
    SELECT 'Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 'Mushroom Farm', 7 UNION ALL
    SELECT 'Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 'Poultry', 8 UNION ALL
    SELECT 'Directorate of Institute for Entrepreneurship Skill Acquisition, Innovation, Research and Digitalization', 'Fashion Design', 9 UNION ALL

    SELECT 'Directorate of International Collaboration and Linkages', 'International Collaboration and Linkages', 1 UNION ALL

    SELECT 'Directorate of Research and Development', 'Research and Development', 1 UNION ALL

    SELECT 'Directorate of Institute for Distance Education and Lifelong Learning', 'Distance Education and Lifelong Learning', 1 UNION ALL

    SELECT 'Directorate of SANDWICH', 'SANDWICH Programme', 1 UNION ALL

    SELECT 'Directorate of Career Development and Employability', 'Career Development and Employability', 1 UNION ALL

    SELECT 'Directorate of School of Basic Studies', 'IJMB', 1 UNION ALL

    SELECT 'Directorate of Gen. T. Y. Danjuma Preliminary Studies, Takum', 'Gen. T. Y. Danjuma Preliminary Studies', 1 UNION ALL

    SELECT 'Directorate of Central Administration and Council Matters', 'Central Administration and Council Matters', 1 UNION ALL

    SELECT 'Directorate of Sports', 'Sports', 1 UNION ALL

    SELECT 'Directorate of Diploma Unit', 'Diploma Programme', 1 UNION ALL

    SELECT 'Directorate of Procurement', 'Procurement', 1 UNION ALL

    SELECT 'Directorate of Establishment Unit', 'Establishment', 1 UNION ALL

    SELECT 'Directorate of Academic Affairs Division', 'Secretariat Office', 1 UNION ALL
    SELECT 'Directorate of Academic Affairs Division', 'Admission/Result Office', 2 UNION ALL
    SELECT 'Directorate of Academic Affairs Division', 'NYSC Desk Office', 3 UNION ALL
    SELECT 'Directorate of Academic Affairs Division', 'Senate and Ceremonial Office', 4 UNION ALL
    SELECT 'Directorate of Academic Affairs Division', 'Exam and Record Office', 5 UNION ALL
    SELECT 'Directorate of Academic Affairs Division', 'Student Record Office', 6
) u ON d.name = u.dir;

-- Add directorate column to profiles table for non-teaching staff
ALTER TABLE profiles
  ADD COLUMN directorate VARCHAR(255) DEFAULT NULL AFTER unit;
