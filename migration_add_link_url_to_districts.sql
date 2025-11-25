-- Migration: Add link_url column to districts table
-- Run this if you have an existing database without the link_url column

ALTER TABLE districts 
ADD COLUMN IF NOT EXISTS link_url VARCHAR(500);

