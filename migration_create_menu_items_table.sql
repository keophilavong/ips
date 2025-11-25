-- Migration: Create menu_items table
-- Run this to create the menu_items table if it doesn't exist

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

