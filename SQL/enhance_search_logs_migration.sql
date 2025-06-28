-- Migration: Enhance Search Logs Table for Better Analytics
-- Date: 2024-12-29
-- Description: Adds click_position and clicked_at columns to search_logs table

-- Add new columns for enhanced click tracking
ALTER TABLE search_logs 
ADD COLUMN click_position int(11) DEFAULT NULL COMMENT 'Position of clicked product in search results',
ADD COLUMN clicked_at timestamp NULL DEFAULT NULL COMMENT 'Timestamp when product was clicked';

-- Create index for better query performance on analytics
CREATE INDEX idx_search_logs_clicked_product ON search_logs(clicked_product_id);
CREATE INDEX idx_search_logs_keyword ON search_logs(keyword);
CREATE INDEX idx_search_logs_search_date ON search_logs(search_date);
CREATE INDEX idx_search_logs_user_id ON search_logs(user_id);

-- Update table comment
ALTER TABLE search_logs COMMENT = 'Enhanced search logs table for comprehensive analytics tracking';

-- Show the updated table structure
DESCRIBE search_logs; 