-- Migration: Add video_url, document_path, and category fields to activities table
-- This allows activities to support videos, documents, and categorization

-- Add video_url field for video links (YouTube, Vimeo, etc.)
ALTER TABLE activities 
ADD COLUMN IF NOT EXISTS video_url VARCHAR(500);

-- Add document_path field for uploaded documents
ALTER TABLE activities 
ADD COLUMN IF NOT EXISTS document_path VARCHAR(500);

-- Add category field for categorizing activities (e.g., "ພະແນກບໍລິຫານ")
ALTER TABLE activities 
ADD COLUMN IF NOT EXISTS category VARCHAR(200);

-- Create index for category for better filtering performance
CREATE INDEX IF NOT EXISTS idx_activities_category ON activities(category);

-- Create index for date_created if not exists
CREATE INDEX IF NOT EXISTS idx_activities_date_created ON activities(date_created DESC);

