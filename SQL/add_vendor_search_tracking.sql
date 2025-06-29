-- Migration: Add Vendor Search Tracking to Search Logs
-- Date: 2024-12-29
-- Description: Adds vendor-specific search tracking fields to enable direct vendor search analytics

-- Add vendor tracking field to search_logs table
ALTER TABLE search_logs 
ADD COLUMN clicked_vendor_id int(11) DEFAULT NULL COMMENT 'ID of vendor clicked from vendor search results';

-- Add foreign key constraint for clicked_vendor_id (optional)
-- Note: Uncomment the line below if you want foreign key constraint
-- ALTER TABLE search_logs ADD CONSTRAINT fk_search_logs_vendor FOREIGN KEY (clicked_vendor_id) REFERENCES vendors(vendor_id) ON DELETE SET NULL;

-- Create index for better query performance on vendor analytics
CREATE INDEX idx_search_logs_clicked_vendor ON search_logs(clicked_vendor_id);

-- Update table comment to reflect vendor tracking capability
ALTER TABLE search_logs COMMENT = 'Comprehensive search logs table for product and vendor analytics tracking';

-- Show the updated table structure
DESCRIBE search_logs;

-- Test query to show vendor search analytics capability
SELECT 'Vendor search tracking added successfully' as status; 