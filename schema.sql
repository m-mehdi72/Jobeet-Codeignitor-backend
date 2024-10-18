-- -- Drop tables if they already exist to start fresh
-- DROP TABLE IF EXISTS jobs;
-- DROP TABLE IF EXISTS affiliates;
-- DROP TABLE IF EXISTS categories;
-- DROP TABLE IF EXISTS admin;

-- Create the `admin_settings` table
CREATE TABLE admin_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(100) NOT NULL UNIQUE,
    value VARCHAR(255) NOT NULL,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


-- Create the `categories` table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uc_category_name UNIQUE (name)
);

-- Create the `affiliates` table
CREATE TABLE affiliates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    site_url VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    is_active BOOLEAN NOT NULL DEFAULT FALSE,
    created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uc_affiliate_token UNIQUE (token)
);

-- Create the `jobs` table
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    company VARCHAR(100) NOT NULL,
    type ENUM('full-time', 'part-time', 'freelance') NOT NULL,
    logo VARCHAR(255),
    url VARCHAR(255),
    position VARCHAR(100) NOT NULL,
    location VARCHAR(100),
    description TEXT NOT NULL,
    how_to_apply TEXT NOT NULL,
    public BOOLEAN NOT NULL DEFAULT TRUE,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    status ENUM('active', 'expired') NOT NULL DEFAULT 'active',
    created_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_on DATE NOT NULL,
    CONSTRAINT fk_jobs_categories FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    CONSTRAINT uc_jobs_token UNIQUE (token)
);

-- Add indexes for optimized querying
CREATE INDEX idx_jobs_category ON jobs (category_id);
CREATE INDEX idx_jobs_token ON jobs (token);

