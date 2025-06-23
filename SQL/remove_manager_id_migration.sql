-- Remove manager_id column from staff table
ALTER TABLE staff DROP COLUMN IF EXISTS manager_id;

-- Add email column to staff table if it doesn't exist
ALTER TABLE staff ADD COLUMN IF NOT EXISTS email VARCHAR(255) AFTER user_id;

-- Update the email column to get email from users table
UPDATE staff s 
JOIN users u ON s.user_id = u.user_id 
SET s.email = u.email 
WHERE s.email IS NULL OR s.email = '';

-- Make email column NOT NULL after populating it
ALTER TABLE staff MODIFY COLUMN email VARCHAR(255) NOT NULL; 