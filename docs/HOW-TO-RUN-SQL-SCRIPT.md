# 📋 How to Run the SQL Script in phpMyAdmin

## ⚠️ Error: "No database selected"

If you get this error, you need to select your database first.

## ✅ Solution: Two Methods

### Method 1: Select Database in phpMyAdmin (Recommended)

1. **Open phpMyAdmin** (usually at http://localhost/phpmyadmin)

2. **Select your database** from the left sidebar:
   - Look for `khbeventskmallxmas` (or your database name)
   - Click on it to select it
   - The database name should appear in the top navigation

3. **Click on the SQL tab** (at the top)

4. **Copy and paste the SQL** from `CREATE-TABLES-MANUAL.sql`

5. **Click "Go"** to execute

### Method 2: Use Updated SQL File (Easier)

I've updated the SQL file to automatically select the database. Just:

1. **Open phpMyAdmin**
2. **Click on the SQL tab** (you don't need to select database first)
3. **Copy and paste the ENTIRE SQL** from `CREATE-TABLES-MANUAL.sql` (it now includes `USE database_name;` at the top)
4. **Click "Go"**

## 📝 Step-by-Step Instructions

### Step 1: Open phpMyAdmin
- Go to: http://localhost/phpmyadmin
- Login if required (usually no password for XAMPP)

### Step 2: Select Database (Method 1) OR Use SQL with USE statement (Method 2)

**If using Method 1:**
- Click on `khbeventskmallxmas` in the left sidebar
- Click on **SQL** tab at the top

**If using Method 2:**
- Just click on **SQL** tab (the SQL file now includes `USE` statement)

### Step 3: Paste SQL
- Open `CREATE-TABLES-MANUAL.sql` file
- Copy **ALL** the SQL (Ctrl+A, then Ctrl+C)
- Paste into the SQL text area (Ctrl+V)

### Step 4: Execute
- Click the **"Go"** button at the bottom
- Wait for success message

## ✅ Expected Result

You should see:
```
# MySQL returned an empty result set (i.e. zero rows).
```

Or success messages for each table creation.

## 🔍 Verify Tables Were Created

After running the SQL, check if tables exist:

1. In phpMyAdmin, click on your database name in the left sidebar
2. You should see these new tables:
   - ✅ `notifications`
   - ✅ `payments`
   - ✅ `messages`
   - ✅ `roles`
   - ✅ `permissions`
   - ✅ `role_permissions`
   - ✅ `activity_logs`
   - ✅ `email_templates`
   - ✅ `migrations` (if it didn't exist)

## ⚠️ If You Get Foreign Key Errors

If you get errors about foreign keys, it means the base tables don't exist. Make sure these tables exist first:
- `user`
- `client`
- `book`

If they don't exist, you'll need to create them first or temporarily disable foreign key checks:

```sql
SET FOREIGN_KEY_CHECKS=0;
-- Paste your SQL here
SET FOREIGN_KEY_CHECKS=1;
```

## 🎯 Quick Alternative: Run Tables One by One

If the full script fails, you can run each `CREATE TABLE` statement individually:

1. Copy just one `CREATE TABLE` statement
2. Paste and run it
3. If successful, move to the next one
4. This helps identify which table is causing issues

---

**The updated SQL file now includes `USE database_name;` at the top, so you don't need to manually select the database!**
