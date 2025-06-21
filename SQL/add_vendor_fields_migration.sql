-- Migration: Add website_url and description fields to vendors table
-- Date: Current
-- Purpose: Enable saving website URL and business description for vendors

-- Add website_url column
ALTER TABLE `vendors` 
ADD COLUMN `website_url` VARCHAR(255) NULL 
AFTER `address`;

-- Add description column  
ALTER TABLE `vendors` 
ADD COLUMN `description` TEXT NULL 
AFTER `website_url`;

-- Update existing records to have NULL values (they will remain NULL by default)

-- Verify the changes
DESCRIBE `vendors`; 