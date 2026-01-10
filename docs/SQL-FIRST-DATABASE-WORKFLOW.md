# 📊 SQL-First Database Workflow

**Last Updated:** January 2026

---

## 🎯 **Overview**

This document describes the **SQL-First Database Workflow** for the KHB Events Booth System. This approach uses a single SQL file as the **source of truth** for the database schema and data, making it easy to keep code and database in perfect sync.

### **Key Benefits:**

✅ **Single Source of Truth**: One SQL file (`database/export_real_database/khbeventskmallxmas.sql`) contains everything  
✅ **Easy Import/Export**: Import directly to phpMyAdmin or use Laravel commands  
✅ **Code-Database Sync**: Keep database changes in sync with code changes  
✅ **Quick Development**: Edit SQL file directly, then import  
✅ **Version Control**: Track database changes in Git alongside code  

---

## 📁 **SQL File Location**

```
database/
└── export_real_database/
    └── khbeventskmallxmas.sql  ← Your source of truth SQL file
```

**File Format:** Standard phpMyAdmin SQL dump format  
**Character Set:** UTF-8 (utf8mb4)  
**Engine:** InnoDB (preferred) / MyISAM  

---

## 🔄 **Workflow Options**

### **Option 1: SQL File → Database (Recommended for Development)**

**When to use:** Starting fresh, importing changes, syncing from code

```bash
# 1. Edit SQL file directly (in your editor)
# Edit: database/export_real_database/khbeventskmallxmas.sql

# 2. Import SQL file to database
php artisan db:import

# 3. Verify sync
php artisan db:sync
```

**Alternative (Manual):**
1. Open phpMyAdmin: `http://localhost:8000/phpmyadmin/`
2. Select database: `khbeventskmallxmas`
3. Click **Import** tab
4. Choose file: `database/export_real_database/khbeventskmallxmas.sql`
5. Click **Go**

---

### **Option 2: Database → SQL File (Backup/Export)**

**When to use:** After making changes in phpMyAdmin, before deploying, creating backups

```bash
# Export current database to SQL file
php artisan db:export

# Or export specific tables only
php artisan db:export --tables=floor_plans,booth,book

# Or export structure only (no data)
php artisan db:export --no-data
```

**Alternative (Manual):**
1. Open phpMyAdmin: `http://localhost:8000/phpmyadmin/`
2. Select database: `khbeventskmallxmas`
3. Click **Export** tab
4. Choose method: **Quick** or **Custom**
5. Format: **SQL**
6. Click **Go**
7. Save file as: `database/export_real_database/khbeventskmallxmas.sql`

---

### **Option 3: Check Sync Status**

**When to use:** Before committing changes, verifying deployment, troubleshooting

```bash
# Check if SQL file and database are in sync
php artisan db:sync

# Show detailed differences
php artisan db:sync --diff
```

---

## 🛠️ **Available Commands**

### **1. Export Database**

```bash
php artisan db:export [options]
```

**Options:**
- `--file=path/to/file.sql` - Export to specific file (default: `database/export_real_database/khbeventskmallxmas.sql`)
- `--no-data` - Export structure only (no data)
- `--tables=table1,table2` - Export specific tables only
- `--force` - Overwrite existing file without confirmation

**Examples:**
```bash
# Export to default location
php artisan db:export

# Export to custom location
php artisan db:export --file=/path/to/backup.sql

# Export structure only
php artisan db:export --no-data

# Export specific tables
php artisan db:export --tables=floor_plans,booth,book
```

---

### **2. Import Database**

```bash
php artisan db:import [options]
```

**Options:**
- `--file=path/to/file.sql` - Import from specific file (default: `database/export_real_database/khbeventskmallxmas.sql`)
- `--force` - Skip confirmation prompts
- `--backup` - Create backup before import

**Examples:**
```bash
# Import from default location
php artisan db:import

# Import from custom file
php artisan db:import --file=/path/to/backup.sql

# Import with backup
php artisan db:import --backup

# Import without confirmation
php artisan db:import --force
```

---

### **3. Sync Check**

```bash
php artisan db:sync [options]
```

**Options:**
- `--file=path/to/file.sql` - Compare with specific file (default: `database/export_real_database/khbeventskmallxmas.sql`)
- `--diff` - Show detailed differences

**Examples:**
```bash
# Check sync status
php artisan db:sync

# Show detailed differences
php artisan db:sync --diff

# Compare with custom file
php artisan db:sync --file=/path/to/other.sql
```

---

### **4. Database Inspection**

```bash
php artisan db:inspect [options]
```

**Options:**
- `--table=table_name` - Inspect specific table
- `--tables` - List all tables

**Examples:**
```bash
# Show database overview
php artisan db:inspect

# List all tables
php artisan db:inspect --tables

# Inspect specific table
php artisan db:inspect --table=floor_plans
```

---

## 📋 **Development Workflow Examples**

### **Example 1: Adding a New Column**

```bash
# Method A: Edit SQL file directly
# 1. Edit: database/export_real_database/khbeventskmallxmas.sql
#    Add: ALTER TABLE `booth` ADD COLUMN `new_column` VARCHAR(255) NULL;

# 2. Import to database
php artisan db:import

# 3. Update Model (if needed)
# Edit: app/Models/Booth.php

# Method B: Use phpMyAdmin
# 1. Add column in phpMyAdmin
# 2. Export to SQL file
php artisan db:export
```

---

### **Example 2: Creating a New Table**

```bash
# 1. Edit SQL file: database/export_real_database/khbeventskmallxmas.sql
#    Add CREATE TABLE statement

# 2. Import to database
php artisan db:import

# 3. Create Model
php artisan make:model NewModel

# 4. Verify
php artisan db:sync
```

---

### **Example 3: Deploying to Production**

```bash
# 1. Export latest database from development
php artisan db:export

# 2. Commit SQL file to Git
git add database/export_real_database/khbeventskmallxmas.sql
git commit -m "Update database schema"

# 3. Push to repository
git push

# 4. On production server:
git pull
php artisan db:import --backup  # Creates backup first
```

---

### **Example 4: Testing Database Changes**

```bash
# 1. Create backup before testing
php artisan db:export --file=database/export_real_database/backup_$(date +%Y%m%d_%H%M%S).sql

# 2. Make changes in phpMyAdmin or edit SQL file

# 3. Test changes
php artisan db:sync  # Verify sync

# 4. If something breaks, restore backup
php artisan db:import --file=database/export_real_database/backup_YYYYMMDD_HHMMSS.sql
```

---

## 🔍 **Troubleshooting**

### **Issue: Command not found**

```bash
# Make sure you're in the project root
cd c:\xampp\htdocs\KHB\khbevents\boothsystemv1

# Clear cache
php artisan config:clear
php artisan cache:clear
```

---

### **Issue: mysqldump/mysql command not found**

The commands will fall back to Laravel-based export/import (slower but works).

**For better performance, install MySQL client:**
- XAMPP includes it: `C:\xampp\mysql\bin\mysqldump.exe`
- Or use phpMyAdmin import/export directly

---

### **Issue: Import fails with syntax errors**

**Solution 1: Import via phpMyAdmin**
- phpMyAdmin handles syntax better
- Go to phpMyAdmin → Import → Choose file → Go

**Solution 2: Check SQL file encoding**
- Ensure file is UTF-8 encoded
- Remove BOM if present

**Solution 3: Split large SQL file**
- If file is too large, split into chunks
- Import one chunk at a time

---

### **Issue: Database out of sync**

```bash
# 1. Check current sync status
php artisan db:sync --diff

# 2. Decide which is correct (database or SQL file)
#    - If database is correct: php artisan db:export
#    - If SQL file is correct: php artisan db:import

# 3. Verify sync
php artisan db:sync
```

---

### **Issue: Foreign key constraints fail**

```bash
# Import with foreign key checks disabled
# Edit SQL file, add at the top:
SET FOREIGN_KEY_CHECKS = 0;

# At the end:
SET FOREIGN_KEY_CHECKS = 1;
```

---

## ✅ **Best Practices**

### **1. Always Backup Before Import**
```bash
php artisan db:import --backup
```

### **2. Verify Sync Before Committing**
```bash
php artisan db:sync
```

### **3. Keep SQL File Updated**
- Export after making changes in phpMyAdmin
- Commit SQL file changes to Git with code changes

### **4. Use Descriptive Commit Messages**
```bash
git commit -m "Add floor_plan_id to bookings table [DB Change]"
```

### **5. Test Import/Export Regularly**
- Test on development before production
- Verify structure matches expectations

---

## 🔄 **Integration with Laravel Migrations**

**Hybrid Approach (Recommended):**

1. **Development**: Use SQL file for rapid development
2. **Migrations**: Generate migrations from SQL file when stable
3. **Production**: Use migrations for deployment

**Generate Migration from SQL File:**

```bash
# 1. Export current database
php artisan db:export

# 2. Compare with previous version (Git diff)
git diff database/export_real_database/khbeventskmallxmas.sql

# 3. Create migration manually based on changes
php artisan make:migration your_migration_name
```

---

## 📚 **Related Documentation**

- **Database Structure**: See `docs/DATABASE-STRUCTURE.md`
- **System Architecture**: See `COMPANY-MANAGEMENT-ARCHITECTURE.md`
- **Project Structure**: See `PROJECT-STRUCTURE.md`

---

## 💡 **Quick Reference**

```bash
# Export database to SQL file
php artisan db:export

# Import SQL file to database
php artisan db:import

# Check if in sync
php artisan db:sync

# Inspect database
php artisan db:inspect
```

---

**🎉 Happy Database Management!**
