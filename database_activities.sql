-- Activities table for storing activities/meetings with images
CREATE TABLE IF NOT EXISTS activities (
    activity_id SERIAL PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    image_path VARCHAR(500),
    date_created DATE DEFAULT CURRENT_DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_activities_date ON activities(date_created DESC);
CREATE INDEX IF NOT EXISTS idx_activities_active ON activities(is_active);

