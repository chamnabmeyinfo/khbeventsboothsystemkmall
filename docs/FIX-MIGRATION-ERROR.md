# 🔧 Fix: "Array to string conversion" Migration Error

## 🔴 Problem

When running `php artisan migrate`, you get this error:

```
ErrorException: Array to string conversion
at vendor\laravel\framework\src\Illuminate\Database\Schema\Builder.php:163
```

This happens because Laravel's database configuration cache has a corrupted prefix value.

## ✅ Solution

### Step 1: Clear All Caches

Run these commands **in order**:

```bash
cd C:\xampp\htdocs\KHB\khbevents\boothsystemv1

# Clear configuration cache
php artisan config:clear

# Clear application cache
php artisan cache:clear

# Clear route cache (if exists)
php artisan route:clear

# Clear view cache
php artisan view:clear
```

### Step 2: Verify .env File

Make sure your `.env` file **does NOT** have a `DB_PREFIX` line, or if it does, it should be:

```env
DB_PREFIX=
```

Or simply remove the line entirely (empty prefix is default).

### Step 3: Try Migration Again

After clearing caches, try again:

```bash
php artisan migrate --force
```

### Step 4: If Still Not Working

If the error persists, manually delete the config cache file:

```bash
# On Windows
del bootstrap\cache\config.php

# Or on Linux/Mac
rm bootstrap/cache/config.php
```

Then try migration again.

## 🔍 Root Cause

This error occurs when:
1. Configuration cache is corrupted
2. `DB_PREFIX` environment variable is set to an array (should be string or empty)
3. Cached database configuration has wrong prefix format

## ✅ After Fix

Once migrations run successfully, all tables will be created:
- ✅ `notifications`
- ✅ `payments`
- ✅ `messages`
- ✅ `roles`
- ✅ `permissions`
- ✅ `role_permissions`
- ✅ `activity_logs`
- ✅ `email_templates`
- ✅ `user` table will have `role_id` column

## 🎯 Quick Fix Command (All-in-One)

Run this single command block:

```bash
cd C:\xampp\htdocs\KHB\khbevents\boothsystemv1 && php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear && php artisan migrate --force
```

This will:
1. Clear all caches
2. Run migrations
3. Create all missing tables

---

**Note:** The `config/database.php` file has been updated to ensure prefix is always a string.
