# How to Test Floor Plan Data Isolation

## Quick Verification Test (5 minutes)

Follow these steps to confirm that deleting zones in one floor plan does NOT affect other floor plans:

---

## Step 1: Create Two Test Floor Plans

1. Go to **Floor Plans** menu
2. Click **Create New Floor Plan**
3. Create first floor plan:
   - Name: `Test Event A`
   - Set as active
4. Create second floor plan:
   - Name: `Test Event B`
   - Set as active

---

## Step 2: Add Zone "A" to Both Floor Plans

### In Test Event A:
1. Click "View Booths" on Test Event A
2. Switch to Canvas View
3. Click "Create Zone" button
4. Create Zone with:
   - Zone Name: `A`
   - From: `1`
   - To: `5`
   - Click "Create Zone"
5. You should now see booths: A01, A02, A03, A04, A05

### In Test Event B:
1. Go back to Floor Plans list
2. Click "View Booths" on Test Event B
3. Switch to Canvas View
4. Click "Create Zone" button
5. Create Zone with:
   - Zone Name: `A`
   - From: `1`
   - To: `3`
   - Click "Create Zone"
6. You should now see booths: A01, A02, A03

**Current State:**
- Test Event A has Zone A with booths: A01, A02, A03, A04, A05 (5 booths)
- Test Event B has Zone A with booths: A01, A02, A03 (3 booths)

---

## Step 3: Delete Zone "A" from Test Event A

1. Make sure you're viewing Test Event A (check floor plan selector)
2. In Canvas View, find the "Delete" button under Zone A controls
3. Click the Delete button (trash icon)
4. Select "Delete All" tab
5. Check the confirmation checkbox
6. Click "Delete Booths" button
7. Confirm the deletion

**Expected Result:**
- ✅ Message: "5 booth(s) deleted successfully from Zone A in Test Event A"
- ✅ Zone A disappears from Test Event A canvas

---

## Step 4: Verify Test Event B is NOT Affected

1. Go back to Floor Plans list
2. Click "View Booths" on Test Event B
3. Switch to Canvas View

**Expected Result:**
- ✅ Zone A still exists in Test Event B
- ✅ All 3 booths still exist: A01, A02, A03
- ✅ Nothing was deleted from Test Event B

---

## Step 5: Verify in Database (Optional)

If you have database access, run this query:

```sql
-- Check booths in both floor plans
SELECT 
    fp.name as floor_plan_name,
    b.booth_number,
    b.floor_plan_id
FROM booth b
JOIN floor_plans fp ON b.floor_plan_id = fp.id
WHERE b.booth_number LIKE 'A%'
AND fp.name IN ('Test Event A', 'Test Event B')
ORDER BY fp.name, b.booth_number;
```

**Expected Result:**
```
floor_plan_name | booth_number | floor_plan_id
----------------|--------------|---------------
Test Event B    | A01          | 2
Test Event B    | A02          | 2
Test Event B    | A03          | 2
```

(No rows for Test Event A because we deleted them)

---

## Step 6: Cleanup Test Data

1. Go to Floor Plans list
2. Delete "Test Event A" floor plan
3. Delete "Test Event B" floor plan

---

## Advanced Test: Zone Settings Isolation

If you want to verify zone settings are also isolated:

### Step 1: Create Same Zone in Both Floor Plans
1. Create Zone "B" in Test Event A (booths B01-B05)
2. Create Zone "B" in Test Event B (booths B01-B05)

### Step 2: Customize Zone B in Test Event A
1. In Test Event A canvas
2. Click Zone B to select it
3. Change zone settings:
   - Background color: Red
   - Border color: Blue
   - Price: 1000

### Step 3: Check Zone B in Test Event B
1. Switch to Test Event B
2. Click Zone B to select it
3. Verify:
   - ✅ Background color is DEFAULT (not red)
   - ✅ Border color is DEFAULT (not blue)
   - ✅ Price is DEFAULT (not 1000)

**Result:** Zone settings are completely independent!

---

## What This Test Proves

✅ **Booth Isolation:**
- Booth numbers can be the same across floor plans
- Deleting booths in one floor plan doesn't affect others

✅ **Zone Isolation:**
- Zone names can be the same across floor plans
- Zone settings are independent per floor plan

✅ **Data Safety:**
- No way to accidentally delete data from other floor plans
- Each floor plan is a completely separate project

---

## Troubleshooting

### Issue: "Floor plan ID is required" error
**Solution:** Make sure you've selected a floor plan from the dropdown at the top of the page.

### Issue: Can't see the Delete button
**Solution:** 
1. Make sure you're in Canvas View (not List View)
2. Make sure you have permission to edit canvas (admin/owner)
3. Check that "Delete" checkbox is enabled in zone controls

### Issue: All booths deleted from both floor plans
**Solution:** This should NOT happen with the new code. If it does:
1. Check your Laravel log: `storage/logs/laravel.log`
2. Verify the migration was run: Check database for composite unique key on `booth` table
3. Contact developer for assistance

---

## Test Result Sheet

Use this checklist to verify:

- [ ] Created two test floor plans
- [ ] Created Zone A in both floor plans
- [ ] Verified different number of booths in each (5 in A, 3 in B)
- [ ] Deleted Zone A from Test Event A
- [ ] Verified Zone A still exists in Test Event B
- [ ] Verified all booths still exist in Test Event B
- [ ] Cleaned up test data

**If all checkboxes pass: ✅ Floor Plan Isolation is working correctly!**

---

**Test Duration:** ~5 minutes  
**Required Access:** Admin or Owner role  
**Risk Level:** Low (test data only)
