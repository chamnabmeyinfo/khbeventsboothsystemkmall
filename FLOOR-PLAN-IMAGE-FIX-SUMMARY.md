# 🔧 Floor Plan Image Conflict Fix - Summary

**Issue Reported:** Uploading images via booth editor (`/booths?floor_plan_id=4`) or edit page (`/floor-plans/1/edit`) causes one of them to go blank. Requires re-uploading repeatedly.

**Root Causes Identified:**
1. ✅ Invalid data in `canvas_settings` (Blade template strings, full URLs) - **CLEANED**
2. ✅ `loadCanvasSettings()` was clearing image when `settings.floorplan_image` was null - **FIXED**
3. ✅ `saveCanvasSettingsToDatabase()` was saving `floorplan_image` from JavaScript - **FIXED**
4. ✅ `FloorPlanController::update()` might clear `floor_image` if not explicitly preserved - **FIXED**
5. ✅ Initialization order: `loadCanvasSettings()` might override Blade-loaded image - **FIXED**

---

## ✅ **Fixes Applied**

### **1. SettingsController (`getCanvasSettings`)**
- ✅ ALWAYS uses `floor_plans.floor_image` as source of truth
- ✅ Always syncs `canvas_settings.floorplan_image` with `floor_plans.floor_image`
- ✅ Cleans invalid data (Blade templates, full URLs) automatically
- ✅ Never returns null if floor plan has an image

### **2. SettingsController (`saveCanvasSettings`)**
- ✅ NO LONGER saves `floorplan_image` from JavaScript
- ✅ Only the upload endpoint (`BoothController::uploadFloorplan`) handles `floorplan_image`
- ✅ Prevents JavaScript from overwriting with wrong values

### **3. JavaScript (`loadCanvasSettings`)**
- ✅ NO LONGER clears image if `settings.floorplan_image` is null
- ✅ Prioritizes image already loaded from Blade template
- ✅ Only updates if we have a valid new image

### **4. JavaScript (`saveCanvasSettingsToDatabase`)**
- ✅ NO LONGER saves `floorplan_image` (removed from code)
- ✅ Only saves zoom/pan/grid settings
- ✅ Image path is managed exclusively in `floor_plans.floor_image`

### **5. FloorPlanController (`update`)**
- ✅ ALWAYS preserves `floor_image` unless explicitly uploading new one
- ✅ Refreshes floor plan from database before syncing canvas_settings
- ✅ Always syncs canvas_settings with floor_plans.floor_image after update

### **6. JavaScript Initialization**
- ✅ Sets `self.floorplanImage` and `self.floorPlanImageUrl` from Blade template
- ✅ Ensures canvas background is set BEFORE calling `loadCanvasSettings()`
- ✅ Delays `loadCanvasSettings()` call until after image is loaded
- ✅ Prevents `loadCanvasSettings()` from clearing existing image

---

## 🔍 **Current Database State**

✅ **Floor Plan 1 (Kmall):**
- `floor_image`: `images/floor-plans/1768059162_floor_plan_1.jpg`
- `canvas_width`: 6250
- `canvas_height`: 3125

✅ **Floor Plan 4 (Phnom Penh Shopping Festival):**
- `floor_image`: `images/floor-plans/1768060061_floor_plan_4.jpg`
- `canvas_width`: 6963
- `canvas_height`: 4924

✅ **Floor Plan 5 (Koh Norea):**
- `floor_image`: NULL
- `canvas_width`: 1200
- `canvas_height`: 800

---

## 🧪 **Testing Steps**

1. **Test Upload via Booth Editor:**
   - Go to: `http://localhost:8000/booths?floor_plan_id=1`
   - Upload image for Kmall
   - Verify image shows in canvas
   - Check database: `SELECT floor_image FROM floor_plans WHERE id = 1;`

2. **Test Upload via Booth Editor (Different Floor Plan):**
   - Go to: `http://localhost:8000/booths?floor_plan_id=4`
   - Upload image for Phnom Penh
   - Verify image shows in canvas
   - Check database: `SELECT floor_image FROM floor_plans WHERE id = 4;`

3. **Test Switching Between Floor Plans:**
   - Switch to Kmall: Select from dropdown or go to `/booths?floor_plan_id=1`
   - Verify Kmall's image loads correctly
   - Switch to Phnom Penh: Select from dropdown or go to `/booths?floor_plan_id=4`
   - Verify Phnom Penh's image loads correctly
   - Switch back to Kmall
   - Verify Kmall's image still loads correctly (should NOT be blank)

4. **Test Edit Page:**
   - Go to: `http://localhost:8000/floor-plans/1/edit`
   - Verify Kmall's image shows in preview
   - Save form (without uploading image)
   - Verify image is still there after save
   - Go back to booth editor
   - Verify image still loads correctly

---

## ⚠️ **If Issue Persists**

If images still go blank after these fixes, check:

1. **Database State:**
   ```sql
   SELECT id, name, floor_image FROM floor_plans ORDER BY id;
   ```

2. **Canvas Settings State:**
   ```sql
   SELECT id, floor_plan_id, floorplan_image FROM canvas_settings WHERE floor_plan_id IS NOT NULL;
   ```

3. **File Existence:**
   ```bash
   ls -la public/images/floor-plans/
   ```

4. **Browser Console:**
   - Open browser DevTools (F12)
   - Check Console tab for JavaScript errors
   - Check Network tab for failed image loads
   - Look for 404 errors on image URLs

5. **Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## 🎯 **Expected Behavior**

✅ **After Fix:**
- Each floor plan's image is stored separately with unique names
- Uploading via booth editor saves to correct floor plan
- Switching between floor plans loads correct image
- Edit page shows correct image preview
- Saving edit form (without image) preserves existing image
- No conflicts between upload methods
- Images persist correctly per floor plan ID

---

**Status:** ✅ All fixes applied. Ready for testing.
