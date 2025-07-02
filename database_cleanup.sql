-- Database Cleanup Script for CV Maker
-- This script removes redundant tables and optimizes the database structure

-- =====================================
-- STEP 1: Remove Redundant Tables
-- =====================================

-- Remove feedback table (redundant with contacts table)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS feedback;
SET FOREIGN_KEY_CHECKS = 1;

-- Remove cv_templates table (templates are hardcoded in frontend)
-- Uncomment the next line if you're sure you don't need dynamic templates
-- DROP TABLE IF EXISTS cv_templates;

-- =====================================
-- STEP 2: Add Performance Indexes
-- =====================================

-- Add indexes for better query performance
ALTER TABLE cvs 
ADD INDEX IF NOT EXISTS idx_user_template (user_id, selected_template),
ADD INDEX IF NOT EXISTS idx_user_created (user_id, created_at),
ADD INDEX IF NOT EXISTS idx_finalized (is_finalized);

ALTER TABLE work_experiences 
ADD INDEX IF NOT EXISTS idx_cv_dates (cv_id, start_date, end_date);

ALTER TABLE educations 
ADD INDEX IF NOT EXISTS idx_cv_dates (cv_id, start_date, end_date);

ALTER TABLE skills 
ADD INDEX IF NOT EXISTS idx_cv_name (cv_id, name);

ALTER TABLE interests 
ADD INDEX IF NOT EXISTS idx_cv_id (cv_id);

ALTER TABLE contacts 
ADD INDEX IF NOT EXISTS idx_email_created (email, created_at);

ALTER TABLE user_activities 
ADD INDEX IF NOT EXISTS idx_user_type (user_id, type),
ADD INDEX IF NOT EXISTS idx_created (created_at);

-- =====================================
-- STEP 3: Optimize Tables
-- =====================================

-- Optimize tables for better performance
OPTIMIZE TABLE users;
OPTIMIZE TABLE cvs;
OPTIMIZE TABLE work_experiences;
OPTIMIZE TABLE educations;
OPTIMIZE TABLE skills;
OPTIMIZE TABLE interests;
OPTIMIZE TABLE contacts;
OPTIMIZE TABLE user_profiles;
OPTIMIZE TABLE user_activities;

-- =====================================
-- VERIFICATION QUERIES
-- =====================================

-- Check table sizes after cleanup
SELECT 
    TABLE_NAME as 'Table',
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as 'Size (MB)',
    TABLE_ROWS as 'Rows'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME NOT IN ('cache', 'jobs', 'failed_jobs', 'personal_access_tokens')
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

-- Show all indexes
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    COLUMN_NAME
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME NOT IN ('cache', 'jobs', 'failed_jobs', 'personal_access_tokens')
ORDER BY TABLE_NAME, INDEX_NAME; 