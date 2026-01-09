# 🔧 Fix 404 Error - Step by Step Guide

## Understanding the Problem

A 404 error means the server can't find the requested resource. For Laravel on cPanel, this usually means:
- Document root is not pointing to `public/` directory
- `.htaccess` file is missing or misconfigured
- mod_rewrite is not enabled

## Step-by-Step Fix

### Step 1: Check Directory Structure

**On cPanel Terminal or SSH:**
```bash
cd ~/booths.khbevents.com
ls -la
```

**You should see:**
- `app/`
- `public/`
- `routes/`
- `vendor/`
- `.env`
- etc.

**Check if `public/` directory exists:**
```bash
ls -la public/
```

**Should see:**
- `index.php`
- `.htaccess`
- `images/`

### Step 2: Check Document Root

**In cPanel:**
1. Go to **Subdomains** (under Domains)
2. Find `booths.khbevents.com`
3. Check the **Document Root** path

**It should be:**
```
/home/khbevents/booths.khbevents.com/public
```

**NOT:**
```
/home/khbevents/booths.khbevents.com
```

### Step 3: Fix Document Root

**Option A: Change in cPanel (Recommended)**

1. Go to **Subdomains** in cPanel
2. Click **Manage** next to `booths.khbevents.com`
3. Change **Document Root** to:
   ```
   public_html/booths.khbevents.com/public
   ```
   OR
   ```
   /home/khbevents/booths.khbevents.com/public
   ```
4. Click **Change**

**Option B: Create Symbolic Link (Alternative)**

If you can't change document root:
```bash
cd ~/public_html
ln -s ~/booths.khbevents.com/public booths.khbevents.com
```

### Step 4: Verify .htaccess File

**Check if `.htaccess` exists in `public/` directory:**
```bash
cd ~/booths.khbevents.com/public
ls -la .htaccess
```

**If missing, create it:**
```bash
cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
```

### Step 5: Check mod_rewrite is Enabled

**Create a test file:**
```bash
cd ~/booths.khbevents.com/public
echo "<?php phpinfo(); ?>" > test.php
```

**Visit:** `https://booths.khbevents.com/test.php`

**Look for:** `mod_rewrite` in the output

**If not enabled, contact your hosting provider or enable in cPanel.**

### Step 6: Check File Permissions

```bash
cd ~/booths.khbevents.com
chmod -R 755 public
chmod 644 public/.htaccess
chmod 644 public/index.php
```

### Step 7: Test Direct Access

**Try accessing directly:**
```
https://booths.khbevents.com/index.php
```

**If this works but root doesn't:**
- Document root issue (go back to Step 3)

**If this doesn't work:**
- Check `public/index.php` exists
- Check PHP is working

### Step 8: Check PHP Version

**In cPanel:**
1. Go to **Select PHP Version**
2. Select **PHP 8.1** or higher
3. Enable extensions:
   - `mod_rewrite`
   - `openssl`
   - `pdo`
   - `mbstring`
   - `tokenizer`
   - `xml`
   - `ctype`
   - `json`

### Step 9: Verify index.php

**Check if `public/index.php` exists and has content:**
```bash
cd ~/booths.khbevents.com/public
head -20 index.php
```

**Should see Laravel bootstrap code.**

### Step 10: Check Error Logs

**Check Apache error log:**
```bash
tail -n 50 /home/khbevents/logs/error_log
```

**Check Laravel log:**
```bash
tail -n 50 ~/booths.khbevents.com/storage/logs/laravel.log
```

## Quick Diagnostic Script

**Run this to check everything:**
```bash
cd ~/booths.khbevents.com

echo "=== Checking Directory Structure ==="
ls -la | grep -E "app|public|vendor|routes"

echo "=== Checking public/ directory ==="
ls -la public/ | head -10

echo "=== Checking .htaccess ==="
test -f public/.htaccess && echo "✓ .htaccess exists" || echo "✗ .htaccess missing"

echo "=== Checking index.php ==="
test -f public/index.php && echo "✓ index.php exists" || echo "✗ index.php missing"

echo "=== Checking .env ==="
test -f .env && echo "✓ .env exists" || echo "✗ .env missing"

echo "=== Checking vendor/ ==="
test -d vendor && echo "✓ vendor/ exists" || echo "✗ vendor/ missing - run: composer install"
```

## Most Common Solution

**90% of the time, the issue is:**

Document root should be:
```
/home/khbevents/booths.khbevents.com/public
```

**NOT:**
```
/home/khbevents/booths.khbevents.com
```

## After Fixing

1. **Clear browser cache**
2. **Try again:** `https://booths.khbevents.com`
3. **If still 404, check error logs**

---

**Start with Step 1 and work through each step!** 🔍
