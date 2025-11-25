-- Migration: Add link_url column to provinces table
-- Run this if the link_url column doesn't exist

ALTER TABLE provinces
ADD COLUMN IF NOT EXISTS link_url VARCHAR(500);

