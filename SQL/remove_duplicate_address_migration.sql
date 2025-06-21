-- Migration: Remove duplicate address column from customers table
-- Date: 2024-12-19
-- Purpose: Remove redundant address field since customer_addresses table handles all address data

-- Check if address column exists before dropping it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'customers' 
     AND COLUMN_NAME = 'address') > 0,
    'ALTER TABLE customers DROP COLUMN address;',
    'SELECT "Address column does not exist" as message;'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add comment to document the change
ALTER TABLE customers COMMENT = 'Customer business data - addresses handled by customer_addresses table';

-- Verify the change
SELECT 'Migration completed: address column removed from customers table' as status;

-- Show current customers table structure
DESCRIBE customers; 