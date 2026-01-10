# 📁 Project Structure - Floor Plan Management System

**Last Updated:** January 2026

---

## ✅ **KEPT FILES (Essential Only)**

### **SQL Scripts (Reference Only):**
- ✅ `COMPLETE-FLOOR-PLAN-SYSTEM-FIX-SAFE.sql` - Safe SQL script with column existence checks (idempotent)
  - Can be run manually if needed
  - Checks for existing columns before adding
  - Safe to run multiple times

### **Database:**
- ✅ `database/migrations/` - All Laravel migrations (essential - part of system)
- ✅ `database/export_real_database/khbeventskmallxmas.sql` - Database export (backup reference)

### **Documentation (Essential):**
- ✅ `README.md` - Main project readme
- ✅ `COMPANY-MANAGEMENT-ARCHITECTURE.md` - Complete system architecture documentation
- ✅ `docs/DATABASE-STRUCTURE.md` - Database structure reference
- ✅ `docs/SYSTEM-WIDE-AUDIT-REPORT.md` - System audit report

---

## 🗑️ **REMOVED FILES (Redundant/Temporary)**

### **SQL Files Removed:**
- ❌ `BOOTH-NUMBER-UNIQUENESS-FIX.sql` - Already migrated
- ❌ `ZONE-SETTINGS-FLOOR-PLAN-FIX.sql` - Already migrated
- ❌ `FLOOR-PLAN-SETUP.sql` - Initial setup, already done
- ❌ `FLOOR-PLAN-SETUP-SIMPLE.sql` - Initial setup, already done
- ❌ `FLOOR-PLAN-SETUP-MANUAL.sql` - Initial setup, already done
- ❌ `COMPLETE-FLOOR-PLAN-SYSTEM-FIX-SIMPLE.sql` - Redundant (SAFE version kept)
- ❌ `COMPLETE-FLOOR-PLAN-SYSTEM-FIX.sql` - Redundant (SAFE version kept)
- ❌ `QUICK-FIX-BOOKING-BACKFILL.sql` - Use PHP command instead

### **Markdown Files Removed:**
- ❌ `ARCHITECTURE-CONFIRMATION.md` - Redundant (info in COMPANY-MANAGEMENT-ARCHITECTURE.md)
- ❌ `FIX-INSTRUCTIONS.md` - Temporary fix instructions, already implemented
- ❌ `FLOOR-PLAN-INDEPENDENCE-FIXES.md` - Already implemented
- ❌ `FLOOR-PLAN-SYSTEM-COMPLETE-FIX.md` - Temporary fix doc, already implemented
- ❌ `FLOOR-PLAN-UPGRADE-PLAN.md` - Already implemented
- ❌ `MULTI-PROJECT-ARCHITECTURE-UNDERSTANDING.md` - Redundant
- ❌ `ZONE-INDEPENDENCE-FIXES.md` - Already implemented
- ❌ `docs/cpanel-archive/` - Old deployment docs (entire folder removed)

---

## 📊 **CURRENT SYSTEM FEATURES**

### **✅ Multi-Project Support:**
- Floor plans belong to projects/events (via `event_id`)
- Bookings track which project they belong to
- Reports can filter by project

### **✅ Multi-Floor-Plan Support:**
- Each project can have multiple floor plans
- Each floor plan is completely independent
- Same booth numbers can exist in different floor plans

### **✅ Complete Data Storage:**
- **Floor Plan Images:** `floor_plans.floor_image` (unique per floor plan)
- **Canvas Size:** `floor_plans.canvas_width`, `canvas_height`
- **Zone Settings:** `zone_settings` (with `floor_plan_id`)
- **Booth Positions:** `booth.position_x`, `position_y`
- **Booth Appearance:** All properties in `booth` table
- **Booking Project:** `book.event_id`, `floor_plan_id`

### **✅ System Flexibility:**
- Easy to add new projects
- Easy to add new floor plans per project
- Easy to query data per project/floor plan
- Easy to generate project-specific reports

---

## 🔧 **HOW TO USE**

### **If you need to fix database schema manually:**
1. Use `COMPLETE-FLOOR-PLAN-SYSTEM-FIX-SAFE.sql` in phpMyAdmin
2. Or run migrations: `php artisan migrate`

### **If you need to backfill booking data:**
```bash
php artisan bookings:backfill-project-data
```

### **If you need to create a new floor plan:**
1. Go to Floor Plans → Create New
2. Enter name, project, etc.
3. Go to Booths → Select floor plan
4. Add zones, booths, upload image
5. Everything is automatically stored per floor plan

---

## 📚 **DOCUMENTATION**

- **Architecture:** See `COMPANY-MANAGEMENT-ARCHITECTURE.md`
- **Database:** See `docs/DATABASE-STRUCTURE.md`
- **Audit:** See `docs/SYSTEM-WIDE-AUDIT-REPORT.md`
- **Main:** See `README.md`

---

**Project is now clean and organized!** 🎉
