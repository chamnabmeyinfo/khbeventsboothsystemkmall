# 🚨 PHASE 0: CRITICAL FIX DEPLOYMENT CHECKLIST

**Date:** 2026-01-15  
**Status:** 🔒 Ready for Deployment  
**Priority:** P0 - CRITICAL (Must Deploy Before Any Other Work)

---

## 📋 PRE-DEPLOYMENT CHECKLIST

### ✅ Step 1: Verify Fix is in Code (5 minutes)

The critical booking protection fix is **ALREADY IN YOUR CODE** at:
- **File:** `app/Http/Controllers/BoothController.php`
- **Function:** `deleteBoothsInZone()` (Lines 1819-2021)
- **Changes:** Booking protection + floor plan isolation

**Verify these features exist:**
- [ ] `force_delete_booked` parameter in validation (Line 1837)
- [ ] Booking check: `if ($booth->bookid && !$forceDelete)` (Lines 1858, 1910, 1969)
- [ ] Booking update logic before deletion (Lines 1870-1887)
- [ ] `bookedBooths[]` array tracking (Lines 1860-1865)
- [ ] HTTP 206 status for partial deletion
- [ ] Floor plan filtering: `->where('floor_plan_id', $floorPlanId)`

### ✅ Step 2: Local Testing (15 minutes)

**IMPORTANT:** Test locally FIRST before production deployment!

#### Test Case 1: Protected Deletion (Default)
1. Go to: `http://localhost/KHB/khbevents/boothsystemv1/booths?view=canvas`
2. Create test booths: A01, A02, A03
3. Book booth A02 (assign to a client)
4. Try to delete entire Zone A
5. **Expected Result:**
   - A01 deleted ✅
   - A02 SKIPPED (warning shown) ✅
   - A03 deleted ✅
   - Message: "2 booth(s) deleted. WARNING: 1 booth with active booking was SKIPPED"

#### Test Case 2: Floor Plan Isolation
1. Create 2 floor plans: "Event 2026" and "Event 2027"
2. In "Event 2026": Create booths A01, A02
3. In "Event 2027": Create booths A01, A02
4. Delete Zone A in "Event 2026"
5. **Expected Result:**
   - "Event 2026" booths A01, A02 deleted ✅
   - "Event 2027" booths A01, A02 UNTOUCHED ✅

#### Test Case 3: Booking Data Integrity
1. Create booth B01, book it to "Test Client"
2. Check database: `book` table has entry with `boothid = "[X]"`
3. Try to delete B01
4. **Expected Result:**
   - B01 NOT deleted ✅
   - Booking still exists in database ✅
   - Client can still see booking ✅

---

## 🚀 DEPLOYMENT STEPS

### Step 1: Backup Production Database (CRITICAL!) 

**On your production server:**

```bash
# SSH to production
ssh username@floorplan.khbevents.com

# Navigate to project
cd ~/floorplan.khbevents.com

# Create backup directory if not exists
mkdir -p backups

# Backup database
mysqldump -u your_db_user -p khbevents > backups/backup_before_critical_fix_$(date +%Y%m%d_%H%M%S).sql

# Verify backup was created
ls -lh backups/backup_before_critical_fix_*

# Note the filename for potential rollback
```

**⚠️ STOP! Do NOT proceed until backup is complete and verified!**

---

### Step 2: Commit and Push Changes

**On your local machine:**

```bash
cd c:\xampp\htdocs\KHB\khbevents\boothsystemv1

# Check current status
git status

# Add the critical fix
git add app/Http/Controllers/BoothController.php

# Commit with clear message
git commit -m "CRITICAL FIX: Protect booking data when deleting booths

- Add booking protection to deleteBoothsInZone() function
- Skip booked booths by default (safe mode)
- Add force_delete_booked parameter for controlled deletion
- Update book.boothid JSON when force deleting booked booths
- Add floor_plan_id filtering for proper isolation
- Return HTTP 206 with warnings when booths are skipped
- Prevent data loss in production environment

Fixes: Data loss vulnerability
Priority: P0 - Critical
Tested: Local environment verified"

# Push to remote
git push origin main
```

---

### Step 3: Deploy to Production

**On production server:**

```bash
# Navigate to project
cd ~/floorplan.khbevents.com

# Pull latest changes
git pull origin main

# Clear all caches (IMPORTANT!)
/opt/alt/php82/usr/bin/php artisan config:clear
/opt/alt/php82/usr/bin/php artisan cache:clear
/opt/alt/php82/usr/bin/php artisan route:clear
/opt/alt/php82/usr/bin/php artisan view:clear

# Optional: Restart web server (if needed)
# For cPanel/LiteSpeed:
# touch tmp/restart.txt
```

---

### Step 4: Production Verification (CRITICAL!)

**Test on live site:** `https://floorplan.khbevents.com/booths`

#### Quick Smoke Test (5 minutes):

1. **Create Test Booth:**
   - Go to canvas view
   - Create booth "TEST-01" in a test zone
   - **Do NOT use real booths with bookings for testing!**

2. **Book the Test Booth:**
   - Assign "TEST-01" to a test client
   - Confirm booking is saved

3. **Try to Delete:**
   - Go to Zone management
   - Try to delete the test zone containing "TEST-01"
   - **Expected:** Warning message showing TEST-01 was skipped

4. **Verify Booking:**
   - Check bookings list
   - Verify TEST-01 booking still exists
   - Client should still see booking

5. **Cleanup:**
   - Clear the booking from TEST-01
   - Delete the test booth
   - Delete test client if created

---

### Step 5: Monitor Production (24 hours)

**Check Laravel logs:**

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Or check recent errors
tail -100 storage/logs/laravel.log | grep -i error
```

**Monitor for:**
- ✅ No database errors
- ✅ No booking data loss reports
- ✅ Deletion functions working properly
- ✅ Warning messages displaying correctly

---

## 🔍 VERIFICATION QUERIES

**Run these on production database to verify data integrity:**

### Check for Orphaned Bookings:
```sql
-- This should return 0 rows
SELECT 
    b.id as booth_id,
    b.booth_number,
    b.bookid,
    bk.id as booking_exists
FROM booth b
LEFT JOIN book bk ON b.bookid = bk.id
WHERE b.bookid IS NOT NULL
AND bk.id IS NULL;
```

### Check Booking References:
```sql
-- All bookings should have valid JSON in boothid
SELECT 
    id,
    boothid,
    clientid,
    floor_plan_id
FROM book
WHERE boothid IS NULL 
   OR boothid = '' 
   OR boothid = '[]';
-- Should be empty or only show intentionally empty bookings
```

### Check Floor Plan Isolation:
```sql
-- All booths and their bookings should be in same floor plan
SELECT 
    b.id,
    b.booth_number,
    b.floor_plan_id as booth_floor_plan,
    bk.floor_plan_id as booking_floor_plan
FROM booth b
INNER JOIN book bk ON b.bookid = bk.id
WHERE b.floor_plan_id != bk.floor_plan_id;
-- Should return 0 rows
```

---

## ⚠️ ROLLBACK PROCEDURE (If Issues Occur)

### Emergency Rollback Steps:

```bash
# 1. Restore database backup
mysql -u your_db_user -p khbevents < backups/backup_before_critical_fix_TIMESTAMP.sql

# 2. Revert code changes
git revert HEAD
git push origin main

# 3. Pull reverted code on production
cd ~/floorplan.khbevents.com
git pull origin main

# 4. Clear caches
/opt/alt/php82/usr/bin/php artisan config:clear
/opt/alt/php82/usr/bin/php artisan cache:clear
/opt/alt/php82/usr/bin/php artisan route:clear
/opt/alt/php82/usr/bin/php artisan view:clear

# 5. Verify rollback
# Test that old functionality works
```

---

## 📊 SUCCESS CRITERIA

Deployment is successful when:

- [x] Code deployed to production
- [x] All caches cleared
- [x] Test booth deletion works
- [x] Booked booths are protected (skipped)
- [x] Warning messages display correctly
- [x] Database queries show no orphaned data
- [x] No errors in Laravel logs
- [x] No user reports of issues
- [x] System monitored for 24 hours

---

## 📞 TROUBLESHOOTING

### Issue: Booked booths are still being deleted

**Check:**
1. Verify `force_delete_booked` is NOT being sent from frontend
2. Check `resources/views/booths/index.blade.php` - should NOT include this parameter
3. Review JavaScript console for errors

**Fix:**
- Frontend should never send `force_delete_booked: true` by default
- Only admin panel should have option for force delete

---

### Issue: Warning messages not showing

**Check:**
1. Frontend JavaScript handling of HTTP 206 response
2. Check browser console for errors
3. Verify `booked_booths_skipped` array is being displayed

**Fix:**
- Update frontend to handle `response.booked_booths_skipped`
- Display warning modal to user

---

### Issue: Database errors after deployment

**Check:**
1. Laravel logs: `storage/logs/laravel.log`
2. MySQL error log
3. Check database connection

**Fix:**
- Rollback immediately (see Emergency Rollback above)
- Investigate error
- Fix and redeploy

---

## ✅ POST-DEPLOYMENT CHECKLIST

After 24 hours of monitoring:

- [ ] No booking data loss reported
- [ ] No database integrity errors
- [ ] Users can delete available booths normally
- [ ] Users see warnings when trying to delete booked booths
- [ ] Floor plan isolation working correctly
- [ ] No performance degradation
- [ ] Laravel logs clean (no errors)
- [ ] Database queries return 0 orphaned records

---

## 🎯 NEXT STEPS AFTER DEPLOYMENT

Once Phase 0 is deployed and stable (24 hours monitoring):

✅ **Proceed to Option A: Quick Wins**

1. Multiple booth images (6h)
2. Booking timeline (6h)
3. Quick filters (4h)
4. Financial dashboard (8h)
5. Payment reminders (6h)

**Total: 30 hours of high-impact features**

---

## 📝 DEPLOYMENT LOG

**Fill this out during deployment:**

| Step | Time | Status | Notes |
|------|------|--------|-------|
| Backup created | _____ | ⬜ | Filename: _____________ |
| Code committed | _____ | ⬜ | Commit hash: __________ |
| Code pushed | _____ | ⬜ | Branch: main |
| Production pulled | _____ | ⬜ | |
| Caches cleared | _____ | ⬜ | |
| Test 1: Protected deletion | _____ | ⬜ | Result: ______________ |
| Test 2: Floor plan isolation | _____ | ⬜ | Result: ______________ |
| Test 3: Booking integrity | _____ | ⬜ | Result: ______________ |
| DB queries verified | _____ | ⬜ | Orphaned records: _____ |
| 24h monitoring complete | _____ | ⬜ | Issues found: _________ |

---

**STATUS:** 🚀 **READY FOR DEPLOYMENT**

**Deployed By:** __________________  
**Deployment Date:** __________________  
**Verified By:** __________________  
**Sign-off Date:** __________________

---

**IMPORTANT:** Do NOT proceed with Quick Wins (Option A) until this Phase 0 is deployed and verified!
