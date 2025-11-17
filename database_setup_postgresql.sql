-- PostgreSQL Database Setup Script for Internal Education Worker Report System
-- Run this script in pgAdmin or PostgreSQL command line

-- Create database (run this separately if needed)
-- CREATE DATABASE "edu-pro";

-- Connect to the database
-- \c "edu-pro";

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    user_id SERIAL PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reports table for storing uploaded reports
CREATE TABLE IF NOT EXISTS reports (
    report_id SERIAL PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(500) NOT NULL,
    user_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Admin users table
CREATE TABLE IF NOT EXISTS admins (
    admin_id SERIAL PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a default admin user (password: admin123 - change this!)
-- You can generate a new hash using: echo password_hash('your_password', PASSWORD_DEFAULT);
-- Password hash for 'admin123'
INSERT INTO admins (username, password) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON CONFLICT (username) DO NOTHING;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_reports_category ON reports(category);
CREATE INDEX IF NOT EXISTS idx_reports_created_at ON reports(created_at);
CREATE INDEX IF NOT EXISTS idx_admins_username ON admins(username);

-- Note: The password hash above is for 'admin123'
-- To change the password, generate a new hash and update this script

