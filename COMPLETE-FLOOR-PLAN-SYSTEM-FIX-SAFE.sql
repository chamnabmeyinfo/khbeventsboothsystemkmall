-- ============================================
-- COMPLETE FLOOR PLAN SYSTEM FIX - SAFE VERSION
-- ============================================
-- Run this script in phpMyAdmin or MySQL client
-- Checks if columns exist before adding them (idempotent)
-- Safe to run multiple times without errors

-- ============================================
-- PART 1: FIX BOOKINGS TABLE - Add Project Tracking (Safe)
-- ============================================

-- Step 1: Add event_id to book table (only if it doesn't exist)
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'book'
    AND COLUMN_NAME = 'event_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `book` ADD COLUMN `event_id` BIGINT UNSIGNED NULL AFTER `id`',
    'SELECT "✅ Column event_id already exists in book table" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Add floor_plan_id to book table (only if it doesn't exist)
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'book'
    AND COLUMN_NAME = 'floor_plan_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `book` ADD COLUMN `floor_plan_id` BIGINT UNSIGNED NULL AFTER `event_id`',
    'SELECT "✅ Column floor_plan_id already exists in book table" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Add idx_event_id index (only if it doesn't exist)
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'book'
    AND INDEX_NAME = 'idx_event_id'
);

SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `book` ADD INDEX `idx_event_id` (`event_id`)',
    'SELECT "✅ Index idx_event_id already exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 4: Add idx_floor_plan_id index (only if it doesn't exist)
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'book'
    AND INDEX_NAME = 'idx_floor_plan_id'
);

SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `book` ADD INDEX `idx_floor_plan_id` (`floor_plan_id`)',
    'SELECT "✅ Index idx_floor_plan_id already exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================
-- PART 2: BACKFILL BOOKING DATA
-- ============================================
-- Note: Backfilling is done via PHP artisan command for better reliability
-- Run: php artisan bookings:backfill-project-data
-- Or use the stored procedure below if you prefer SQL

-- ============================================
-- PART 3: FIX CANVAS SETTINGS - Make Floor Plan Specific (Safe)
-- ============================================

-- Step 5: Add floor_plan_id to canvas_settings (only if it doesn't exist)
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'canvas_settings'
    AND COLUMN_NAME = 'floor_plan_id'
);

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `canvas_settings` ADD COLUMN `floor_plan_id` BIGINT UNSIGNED NULL AFTER `id`',
    'SELECT "✅ Column floor_plan_id already exists in canvas_settings table" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 6: Add idx_floor_plan_id index to canvas_settings (only if it doesn't exist)
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'canvas_settings'
    AND INDEX_NAME = 'idx_floor_plan_id'
);

SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `canvas_settings` ADD INDEX `idx_floor_plan_id` (`floor_plan_id`)',
    'SELECT "✅ Index idx_floor_plan_id already exists in canvas_settings" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 7: Clean up canvas_settings - remove invalid data
UPDATE `canvas_settings` 
SET `floorplan_image` = NULL 
WHERE `floorplan_image` LIKE '%asset%' OR `floorplan_image` LIKE '%{{%';

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check if columns were added successfully
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'book'
AND COLUMN_NAME IN ('event_id', 'floor_plan_id')
ORDER BY COLUMN_NAME;

-- Check how many bookings need backfilling
SELECT COUNT(*) as bookings_needing_backfill
FROM `book`
WHERE event_id IS NULL OR floor_plan_id IS NULL;

-- Check floor plans with their images
SELECT 
    fp.id,
    fp.name,
    fp.project_name,
    fp.event_id,
    fp.floor_image,
    fp.canvas_width,
    fp.canvas_height,
    COUNT(b.id) as booth_count
FROM `floor_plans` fp
LEFT JOIN `booth` b ON b.floor_plan_id = fp.id
GROUP BY fp.id
ORDER BY fp.id;

-- ============================================
-- NEXT STEPS
-- ============================================
-- After running this script:
-- 1. Run: php artisan bookings:backfill-project-data
--    OR use the stored procedure in COMPLETE-FLOOR-PLAN-SYSTEM-FIX.sql
-- 2. Verify bookings have event_id and floor_plan_id populated
-- 3. Test zone creation for new floor plans
-- 4. Test floor plan image upload/display
