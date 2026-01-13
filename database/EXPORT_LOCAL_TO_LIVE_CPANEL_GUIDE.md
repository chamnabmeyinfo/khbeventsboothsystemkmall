# Export Local Database and Force Import to Live cPanel phpMyAdmin

## ⚠️ IMPORTANT WARNINGS

1. **BACKUP LIVE DATABASE FIRST!** - Always backup your live database before importing
2. **This will OVERWRITE all data** in your live database
3. **Test on staging first** if possible
4. **Verify your local database** is correct before exporting

---

## Step 1: Export from Local Database

### Option A: Using phpMyAdmin (Local)
1. Open `http://localhost/phpmyadmin` (or your local phpMyAdmin)
2. Select your database
3. Click **"Export"** tab
4. Choose export method:
   - **Quick**: Default settings (recommended)
   - **Custom**: More control
5. **Custom Export Settings** (if using Custom):
   - ✅ **Format**: SQL
   - ✅ **Structure**: Check all
   - ✅ **Data**: Check all
   - ✅ **Add DROP TABLE / DROP VIEW**: **CHECK THIS** (forces overwrite)
   - ✅ **Add IF NOT EXISTS**: Uncheck (we want to overwrite)
   - ✅ **Add CREATE PROCEDURE / FUNCTION**: Check
   - ✅ **Add CREATE TRIGGER**: Check
   - ✅ **Add AUTO_INCREMENT value**: Check
   - ✅ **Enclose table and field names with backquotes**: Check
6. Click **"Go"** to download SQL file

### Option B: Using Command Line (MySQL/MariaDB)
```bash
# Export entire database
mysqldump -u root -p your_database_name > local_export.sql

# Export with DROP statements (force overwrite)
mysqldump -u root -p --add-drop-table --add-drop-database your_database_name > local_export.sql

# Export with all options for force overwrite
mysqldump -u root -p \
  --add-drop-table \
  --add-drop-database \
  --add-drop-trigger \
  --routines \
  --triggers \
  --single-transaction \
  your_database_name > local_export.sql
```

---

## Step 2: Prepare SQL File for Import

### Add Force Overwrite Statements (Optional but Recommended)

Add these lines at the beginning of your SQL file:

```sql
-- Disable foreign key checks (prevents errors during import)
SET FOREIGN_KEY_CHECKS = 0;

-- Disable unique checks (faster import)
SET UNIQUE_CHECKS = 0;

-- Disable autocommit (faster import)
SET AUTOCOMMIT = 0;

-- Your exported SQL here...

-- Re-enable at the end
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;
SET AUTOCOMMIT = 1;
COMMIT;
```

---

## Step 3: Import to Live cPanel phpMyAdmin

### Method 1: Import Tab (Recommended for Large Files)

1. **Login to cPanel**
2. **Open phpMyAdmin** (usually in "Databases" section)
3. **Select your live database**
4. **Click "Import" tab**
5. **Choose File**: Select your exported SQL file
6. **Important Settings**:
   - ✅ **Format**: SQL
   - ✅ **Partial import**: **UNCHECK** (import everything)
   - ✅ **Allow interruption**: Check (optional, for large files)
   - ✅ **SQL compatibility mode**: **NONE** (or match your MySQL version)
   - ✅ **Format**: SQL
7. **Click "Go"**
8. **Wait for completion** - Large databases may take several minutes

### Method 2: SQL Tab (For Smaller Files or Manual Execution)

1. **Login to cPanel**
2. **Open phpMyAdmin**
3. **Select your live database**
4. **Click "SQL" tab**
5. **Open your SQL file** in a text editor
6. **Copy and paste** the SQL content
7. **Click "Go"**

### Method 3: Upload via File Manager (For Very Large Files)

1. **Login to cPanel**
2. **Open "File Manager"**
3. **Navigate to a safe directory** (e.g., `/home/username/tmp/`)
4. **Upload your SQL file**
5. **Go back to phpMyAdmin**
6. **Click "Import" tab**
7. **Choose "Browse your computer"** or select from uploaded location
8. **Click "Go"**

---

## Step 4: Verify Import

After import completes:

1. **Check for errors** - phpMyAdmin will show any errors
2. **Verify table count**:
   ```sql
   SELECT COUNT(*) as table_count FROM information_schema.tables 
   WHERE table_schema = 'your_database_name';
   ```
3. **Check a few tables** to ensure data imported correctly
4. **Test your application** to ensure everything works

---

## Troubleshooting

### Error: "File too large"
- **Solution**: Increase upload limits in phpMyAdmin or use command line
- **cPanel**: Increase `upload_max_filesize` and `post_max_size` in PHP settings
- **Alternative**: Split SQL file or use command line import

### Error: "Timeout"
- **Solution**: Increase execution time in phpMyAdmin settings
- **Alternative**: Use command line import (no timeout)

### Error: "Access denied"
- **Solution**: Check database user permissions in cPanel
- Ensure user has CREATE, DROP, ALTER privileges

### Error: "Duplicate entry"
- **Solution**: This means DROP statements didn't work
- Add `DROP TABLE IF EXISTS` statements manually
- Or use the prepared SQL with DROP statements

### Import stops partway
- **Solution**: Enable "Partial import" in phpMyAdmin
- Or import in smaller chunks
- Check error log for specific issues

---

## Command Line Import (Alternative Method)

If phpMyAdmin has issues, use SSH:

```bash
# Connect via SSH to your cPanel server
ssh username@your-server.com

# Navigate to where SQL file is uploaded
cd ~/tmp/

# Import to database
mysql -u cpanel_username -p cpanel_database_name < local_export.sql

# Or with force options
mysql -u cpanel_username -p \
  --force \
  cpanel_database_name < local_export.sql
```

---

## Best Practices

1. ✅ **Always backup live database first**
2. ✅ **Test import on staging** if available
3. ✅ **Export with DROP statements** for clean import
4. ✅ **Verify local database** is correct before exporting
5. ✅ **Check file size** - large files may need special handling
6. ✅ **Import during low-traffic hours**
7. ✅ **Notify users** if live site will be affected
8. ✅ **Test thoroughly** after import

---

## Quick Checklist

- [ ] Backup live database
- [ ] Export local database with DROP statements
- [ ] Verify export file is complete
- [ ] Upload to cPanel (if needed)
- [ ] Import via phpMyAdmin
- [ ] Check for errors
- [ ] Verify data integrity
- [ ] Test application
- [ ] Monitor for issues
