-- TSU Staff Profile Portal Database Schema

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_prefix VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    verification_code VARCHAR(6),
    email_verified BOOLEAN DEFAULT FALSE,
    verification_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME,
    account_status ENUM('pending', 'active', 'suspended') DEFAULT 'pending',
    profile_completion INT DEFAULT 0,
    INDEX idx_email (email),
    INDEX idx_status (account_status)
);

-- Profiles table
CREATE TABLE profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    -- Basic Information
    title ENUM('Prof.', 'Dr.', 'Mr.', 'Mrs.', 'Ms.', 'Engr.', 'Arc.') NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    display_name VARCHAR(255),
    staff_id VARCHAR(50) UNIQUE,
    profile_photo VARCHAR(255),
    
    -- Professional Information
    faculty VARCHAR(255),
    department VARCHAR(255),
    designation VARCHAR(255),
    office_location VARCHAR(100),
    office_phone VARCHAR(20),
    
    -- Bio & Summary
    professional_summary TEXT,
    research_interests TEXT,
    expertise_keywords TEXT,
    
    -- Profile Settings
    profile_visibility ENUM('public', 'university', 'private') DEFAULT 'public',
    allow_contact BOOLEAN DEFAULT TRUE,
    profile_slug VARCHAR(255) UNIQUE,
    profile_views INT DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FULLTEXT(first_name, last_name, professional_summary, research_interests, expertise_keywords),
    INDEX idx_faculty_dept (faculty, department),
    INDEX idx_slug (profile_slug)
);

-- Education table
CREATE TABLE education (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    degree_type ENUM('PhD', 'M.Sc', 'M.A', 'M.Ed', 'M.Tech', 'MBA', 'B.Sc', 'B.A', 'B.Ed', 'B.Tech', 'HND', 'OND', 'Others') NOT NULL,
    field_of_study VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    country VARCHAR(100),
    start_year YEAR,
    end_year YEAR,
    is_current BOOLEAN DEFAULT FALSE,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_education (user_id)
);

-- Experience table
CREATE TABLE experience (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    organization VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    start_date DATE,
    end_date DATE,
    is_current BOOLEAN DEFAULT FALSE,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_experience (user_id)
);

-- Publications table
CREATE TABLE publications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    publication_type ENUM('journal', 'conference', 'book', 'chapter', 'report', 'thesis', 'other') NOT NULL,
    title VARCHAR(500) NOT NULL,
    authors TEXT,
    journal_conference_name VARCHAR(500),
    publisher VARCHAR(255),
    year YEAR,
    volume VARCHAR(50),
    issue VARCHAR(50),
    pages VARCHAR(50),
    doi VARCHAR(255),
    url VARCHAR(500),
    abstract TEXT,
    citation_count INT DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FULLTEXT(title, authors, abstract),
    INDEX idx_user_publications (user_id),
    INDEX idx_year (year)
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
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_skills (user_id),
    INDEX idx_skill_category (skill_category)
);

-- Certifications table
CREATE TABLE certifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    certification_name VARCHAR(255) NOT NULL,
    issuing_organization VARCHAR(255) NOT NULL,
    issue_date DATE,
    expiry_date DATE,
    credential_id VARCHAR(100),
    credential_url VARCHAR(500),
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_certifications (user_id)
);

-- Awards table
CREATE TABLE awards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    award_title VARCHAR(255) NOT NULL,
    issuing_organization VARCHAR(255) NOT NULL,
    year YEAR,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_awards (user_id)
);

-- Professional memberships table
CREATE TABLE memberships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    organization_name VARCHAR(255) NOT NULL,
    membership_type VARCHAR(100),
    position_held VARCHAR(255),
    start_year YEAR,
    end_year YEAR,
    is_current BOOLEAN DEFAULT TRUE,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_memberships (user_id)
);

-- Activity logs table
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_activity (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
);