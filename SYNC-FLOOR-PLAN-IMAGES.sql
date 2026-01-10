-- ============================================
-- SYNC FLOOR PLAN IMAGES TO CANVAS SETTINGS
-- ============================================
-- This script syncs floor_plans.floor_image to canvas_settings.floorplan_image
-- Run this to fix any mismatches between floor plans and canvas settings

-- Sync canvas_settings.floorplan_image with floor_plans.floor_image
UPDATE canvas_settings cs
INNER JOIN floor_plans fp ON fp.id = cs.floor_plan_id
SET cs.floorplan_image = fp.floor_image
WHERE fp.floor_image IS NOT NULL
AND fp.floor_image != '';

-- Clear canvas_settings.floorplan_image for floor plans that have no image
UPDATE canvas_settings cs
INNER JOIN floor_plans fp ON fp.id = cs.floor_plan_id
SET cs.floorplan_image = NULL
WHERE fp.floor_image IS NULL OR fp.floor_image = '';

-- Verify the sync
SELECT 
    cs.id,
    cs.floor_plan_id,
    fp.name as floor_plan_name,
    fp.floor_image as floor_plans_image,
    cs.floorplan_image as canvas_settings_image,
    CASE 
        WHEN fp.floor_image = cs.floorplan_image THEN '✅ Synced'
        WHEN fp.floor_image IS NULL AND cs.floorplan_image IS NULL THEN '✅ Both NULL'
        ELSE '⚠️ Mismatch'
    END as status
FROM canvas_settings cs
LEFT JOIN floor_plans fp ON fp.id = cs.floor_plan_id
WHERE cs.floor_plan_id IS NOT NULL
ORDER BY cs.floor_plan_id;
