-- Additional Content Tables for CRUD Management
-- Run this after the main database_setup_postgresql.sql

-- Teacher Colleges table
CREATE TABLE IF NOT EXISTS teacher_colleges (
    college_id SERIAL PRIMARY KEY,
    college_name VARCHAR(255) NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    date_created DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Internal Workers table
CREATE TABLE IF NOT EXISTS internal_workers (
    worker_id SERIAL PRIMARY KEY,
    worker_name VARCHAR(255) NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    date_created DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Districts table
CREATE TABLE IF NOT EXISTS districts (
    district_id SERIAL PRIMARY KEY,
    district_name VARCHAR(255) NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    link_url VARCHAR(500),
    date_created DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Provinces table
CREATE TABLE IF NOT EXISTS provinces (
    province_id SERIAL PRIMARY KEY,
    province_name VARCHAR(255) NOT NULL,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    link_url VARCHAR(500),
    date_created DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- CPD Content table
CREATE TABLE IF NOT EXISTS cpd_content (
    cpd_id SERIAL PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    content_type VARCHAR(100) DEFAULT 'resource', -- resource, program, certificate
    file_path VARCHAR(500),
    link_url VARCHAR(500),
    date_created DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_teacher_colleges_date ON teacher_colleges(date_created DESC);
CREATE INDEX IF NOT EXISTS idx_teacher_colleges_active ON teacher_colleges(is_active);
CREATE INDEX IF NOT EXISTS idx_internal_workers_date ON internal_workers(date_created DESC);
CREATE INDEX IF NOT EXISTS idx_internal_workers_active ON internal_workers(is_active);
CREATE INDEX IF NOT EXISTS idx_districts_date ON districts(date_created DESC);
CREATE INDEX IF NOT EXISTS idx_districts_active ON districts(is_active);
CREATE INDEX IF NOT EXISTS idx_provinces_date ON provinces(date_created DESC);
CREATE INDEX IF NOT EXISTS idx_provinces_active ON provinces(is_active);
CREATE INDEX IF NOT EXISTS idx_cpd_date ON cpd_content(date_created DESC);
CREATE INDEX IF NOT EXISTS idx_cpd_active ON cpd_content(is_active);
CREATE INDEX IF NOT EXISTS idx_cpd_type ON cpd_content(content_type);

-- Navigation Menu Items table
CREATE TABLE IF NOT EXISTS menu_items (
    menu_id SERIAL PRIMARY KEY,
    menu_text VARCHAR(255) NOT NULL,
    menu_icon VARCHAR(50),
    menu_url VARCHAR(500) NOT NULL,
    menu_row VARCHAR(20) DEFAULT 'top', -- 'top' or 'bottom'
    display_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Create indexes for menu items
CREATE INDEX IF NOT EXISTS idx_menu_row ON menu_items(menu_row);
CREATE INDEX IF NOT EXISTS idx_menu_order ON menu_items(display_order);
CREATE INDEX IF NOT EXISTS idx_menu_active ON menu_items(is_active);

