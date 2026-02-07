-- TSU Staff Profile Portal - Simple Database Setup
-- This version avoids MySQL index length issues

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS tsu_staff_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE tsu_staff_portal;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS memberships;
DROP TABLE IF EXISTS awards;
DROP TABLE IF EXISTS certifications;
DROP TABLE IF EXISTS skills;
DROP TABLE IF EXISTS publications;
DROP TABLE IF EXISTS experience;
DROP TABLE IF EXISTS education;
DROP TABLE IF EXISTS profiles;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(191) UNIQUE NOT NULL,
    email_prefix VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    verification_code VARCHAR(6),
    email_verified BOOLEAN DEFAULT FALSE,
    verification_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME,
    account_status ENUM('pending', 'active', 'suspended') DEFAULT 'pending',
    profile_completion INT DEFAULT 0
);

-- Profiles table
CREATE TABLE profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    title ENUM('Prof.', 'Dr.', 'Mr.', 'Mrs.', 'Ms.', 'Engr.', 'Arc.') NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    display_name VARCHAR(191),
    staff_id VARCHAR(50) UNIQUE,
    profile_photo VARCHAR(191),
    faculty VARCHAR(191),
    department VARCHAR(191),
    designation VARCHAR(191),
    office_location VARCHAR(100),
    office_phone VARCHAR(20),
    professional_summary TEXT,
    research_interests TEXT,
    expertise_keywords TEXT,
    profile_visibility ENUM('public', 'university', 'private') DEFAULT 'public',
    allow_contact BOOLEAN DEFAULT TRUE,
    profile_slug VARCHAR(191) UNIQUE,
    profile_views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Education table
CREATE TABLE education (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    degree_type ENUM('PhD', 'M.Sc', 'M.A', 'M.Ed', 'M.Tech', 'MBA', 'B.Sc', 'B.A', 'B.Ed', 'B.Tech', 'HND', 'OND', 'Others') NOT NULL,
    field_of_study VARCHAR(191) NOT NULL,
    institution VARCHAR(191) NOT NULL,
    country VARCHAR(100),
    start_year YEAR,
    end_year YEAR,
    is_current BOOLEAN DEFAULT FALSE,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Experience table
CREATE TABLE experience (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    job_title VARCHAR(191) NOT NULL,
    organization VARCHAR(191) NOT NULL,
    location VARCHAR(191),
    start_date DATE,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Publications table
CREATE TABLE publications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    publication_type ENUM('journal', 'conference', 'book', 'chapter', 'report', 'thesis', 'other') NOT NULL,
    title VARCHAR(500) NOT NULL,
    authors TEXT,
    journal_conference_name VARCHAR(500),
    publisher VARCHAR(191),
    year YEAR,
    volume VARCHAR(50),
    issue VARCHAR(50),
    pages VARCHAR(50),
    doi VARCHAR(191),
    url VARCHAR(500),
    abstract TEXT,
    citation_count INT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Skills table
CREATE TABLE skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    skill_category ENUM('technical', 'research', 'teaching', 'administrative', 'language', 'software', 'other') DEFAULT 'other',
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert') DEFAULT 'intermediate',
    years_experience INT,
    endorsed_count INT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Certifications table
CREATE TABLE certifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    certification_name VARCHAR(191) NOT NULL,
    issuing_organization VARCHAR(191) NOT NULL,
    issue_date DATE,
    expiry_date DATE,
    credential_id VARCHAR(100),
    credential_url VARCHAR(500),
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Awards table
CREATE TABLE awards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    award_title VARCHAR(191) NOT NULL,
    issuing_organization VARCHAR(191) NOT NULL,
    year YEAR,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Professional memberships table
CREATE TABLE memberships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    organization_name VARCHAR(191) NOT NULL,
    membership_type VARCHAR(100),
    position_held VARCHAR(191),
    start_year YEAR,
    end_year YEAR,
    is_current BOOLEAN DEFAULT TRUE,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Activity logs table
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert sample data
-- Admin user (password: Admin123!)
INSERT INTO users (email, email_prefix, password_hash, email_verified, account_status) VALUES 
('admin@tsuniversity.edu.ng', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, 'active');

-- Admin profile
INSERT INTO profiles (user_id, title, first_name, last_name, faculty, department, designation, profile_slug, professional_summary) VALUES 
(1, 'Dr.', 'System', 'Administrator', 'Administration', 'IT Department', 'System Administrator', 'admin', 'System administrator for TSU Staff Portal');

-- Sample user (password: User123!)
INSERT INTO users (email, email_prefix, password_hash, email_verified, account_status) VALUES 
('john.doe@tsuniversity.edu.ng', 'john.doe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, 'active');

-- Sample profile
INSERT INTO profiles (user_id, title, first_name, last_name, faculty, department, designation, profile_slug, professional_summary) VALUES 
(2, 'Dr.', 'John', 'Doe', 'Faculty of Sciences', 'B.Sc. Computer Science', 'Senior Lecturer', 'john-doe', 'Experienced computer science lecturer with expertise in software engineering and artificial intelligence.');

-- Sample education
INSERT INTO education (user_id, degree_type, field_of_study, institution, country, start_year, end_year) VALUES 
(2, 'PhD', 'Computer Science', 'University of Lagos', 'Nigeria', 2015, 2019),
(2, 'M.Sc', 'Computer Science', 'Ahmadu Bello University', 'Nigeria', 2012, 2014);

-- Sample experience
INSERT INTO experience (user_id, job_title, organization, location, start_date, is_current, description) VALUES 
(2, 'Senior Lecturer', 'Taraba State University', 'Jalingo, Taraba State', '2020-01-01', TRUE, 'Teaching undergraduate and postgraduate courses in computer science.');

-- Sample skills
INSERT INTO skills (user_id, skill_name, skill_category, proficiency_level, years_experience) VALUES 
(2, 'Python Programming', 'technical', 'expert', 8),
(2, 'Machine Learning', 'research', 'advanced', 5);

COMMIT;