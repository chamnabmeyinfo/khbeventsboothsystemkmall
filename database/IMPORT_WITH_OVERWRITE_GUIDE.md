# SQL Import with Force Overwrite Guide

## Method 1: Using phpMyAdmin Import Settings

### Steps:
1. Open phpMyAdmin
2. Select your database
3. Click on **"Import"** tab
4. Choose your SQL file
5. **Important Settings:**
   - ✅ Check **"Allow interruption of import"** (optional)
   - ✅ Check **"Partial import"** if you want to continue on errors
   - ⚠️ **"SQL compatibility mode"**: Choose "NONE" or "MYSQL40" depending on your MySQL version
6. Click **"Go"**

### For Force Overwrite:
- Use the `FORCE_OVERWRITE_AFFILIATE_COLUMN.sql` script
- This script will drop and recreate the column
- **WARNING**: This deletes existing data in the column

## Method 2: Direct SQL Execution (Recommended)

### Steps:
1. Open phpMyAdmin
2. Select your database
3. Click on **"SQL"** tab
4. Copy and paste the SQL script
5. Click **"Go"**

### If you get errors:
- **"Duplicate column"** → Column already exists, use DROP first
- **"Duplicate key"** → Index already exists, use DROP INDEX first
- **"Access denied"** → Use simpler scripts without information_schema checks

## Method 3: Command Line (MySQL/MariaDB)

```bash
# Connect to MySQL
mysql -u root -p

# Select database
USE your_database_name;

# Source the SQL file
SOURCE /path/to/FORCE_OVERWRITE_AFFILIATE_COLUMN.sql;

# Or directly
mysql -u root -p your_database_name < FORCE_OVERWRITE_AFFILIATE_COLUMN.sql
```

## Available SQL Scripts

### 1. FORCE_OVERWRITE_AFFILIATE_COLUMN.sql
- **Purpose**: Drop and recreate column (deletes data)
- **Use when**: You want to start fresh
- **Warning**: ⚠️ Deletes all existing affiliate_user_id values

### 2. MODIFY_OR_ADD_AFFILIATE_COLUMN.sql
- **Purpose**: Modify existing or add new (preserves data)
- **Use when**: Column exists but you want to update definition
- **Safe**: ✅ Preserves existing data

### 3. ADD_AFFILIATE_COLUMN_SIMPLE_NO_CHECK.sql
- **Purpose**: Simple add (no permission checks needed)
- **Use when**: Column doesn't exist yet
- **Safe**: ✅ Will error if column exists (that's OK)

## Troubleshooting

### Error: "Duplicate column name"
- **Solution**: Use `FORCE_OVERWRITE_AFFILIATE_COLUMN.sql` to drop first

### Error: "Access denied for information_schema"
- **Solution**: Use `ADD_AFFILIATE_COLUMN_SIMPLE_NO_CHECK.sql` (no checks)

### Error: "Table doesn't exist"
- **Solution**: Make sure you're in the correct database

### Import stops partway
- **Solution**: Enable "Partial import" in phpMyAdmin settings
- Or run SQL statements one at a time manually
