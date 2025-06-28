-- Complete Search Logs Migration
-- Date: 2024-12-29
-- Description: Adds all missing fields to search_logs table for comprehensive analytics

-- Add all missing columns for complete search analytics
ALTER TABLE search_logs 
ADD COLUMN filters text DEFAULT NULL COMMENT 'JSON string of applied search filters',
ADD COLUMN results_count int(11) DEFAULT 0 COMMENT 'Number of search results returned',
ADD COLUMN ip_address varchar(45) DEFAULT NULL COMMENT 'IP address of the user',
ADD COLUMN user_agent text DEFAULT NULL COMMENT 'Browser user agent string',
ADD COLUMN session_id varchar(100) DEFAULT NULL COMMENT 'Session ID for tracking user sessions',
ADD COLUMN clicked_product_id int(11) DEFAULT NULL COMMENT 'ID of product clicked from search results',
ADD COLUMN search_duration int(11) DEFAULT NULL COMMENT 'Search duration in milliseconds';

-- Modify keyword field to match model (increase length)
ALTER TABLE search_logs 
MODIFY COLUMN keyword varchar(255) NOT NULL COMMENT 'Search keyword or term';

-- Add foreign key constraint for clicked_product_id (if products table exists)
-- Note: Uncomment the line below if you want foreign key constraint
-- ALTER TABLE search_logs ADD CONSTRAINT fk_search_logs_product FOREIGN KEY (clicked_product_id) REFERENCES products(product_id) ON DELETE SET NULL;

-- Create indexes for better query performance
CREATE INDEX idx_search_logs_clicked_product ON search_logs(clicked_product_id);
CREATE INDEX idx_search_logs_keyword ON search_logs(keyword);
CREATE INDEX idx_search_logs_search_date ON search_logs(search_date);
CREATE INDEX idx_search_logs_user_id ON search_logs(user_id);
CREATE INDEX idx_search_logs_session_id ON search_logs(session_id);

-- Update table comment
ALTER TABLE search_logs COMMENT = 'Comprehensive search logs table for detailed analytics tracking';

-- Show the updated table structure
DESCRIBE search_logs;

-- Test query to show all fields are working
SELECT 'Migration completed successfully. Table structure updated.' as status; 