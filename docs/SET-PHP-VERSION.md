# 🔧 Set PHP Version via .htaccess

## Overview

You can set the PHP version for your Laravel project using `.htaccess` in the `public/` directory. This ensures the web server uses the correct PHP version.

## ✅ Solution: Add PHP Version to .htaccess

The `.htaccess` file in `public/` directory has been updated to use **PHP 8.3**.

### Current Configuration

The `public/.htaccess` file now includes:

```apache
# Set PHP version to 8.3 for this project
<IfModule mod_php.c>
    AddHandler application/x-httpd-php83 .php
</IfModule>
```

## 📋 How It Works

1. **Web Server (Apache):** Uses PHP 8.3 when serving requests
2. **CLI (Terminal):** Still uses default PHP (7.3), so you need to use full path:
   ```bash
   /opt/cpanel/ea-php83/root/usr/bin/php artisan ...
   ```

## 🔄 After Updating .htaccess on cPanel

After pulling the updated code from GitHub:

1. **Verify .htaccess is updated:**
   ```bash
   cd ~/booths.khbevents.com/public
   head -5 .htaccess
   ```

2. **Test PHP version:**
   - Create test file: `echo "<?php phpinfo(); ?>" > public/test.php`
   - Visit: `https://booths.khbevents.com/test.php`
   - Look for: "PHP Version 8.3.x"
   - **Delete test file after:** `rm public/test.php`

## ⚙️ Alternative PHP Versions

If you need a different PHP version, change the handler:

**For PHP 8.1:**
```apache
AddHandler application/x-httpd-php81 .php
```

**For PHP 8.2:**
```apache
AddHandler application/x-httpd-php82 .php
```

**For PHP 8.3:**
```apache
AddHandler application/x-httpd-php83 .php
```

**For PHP 8.4:**
```apache
AddHandler application/x-httpd-php84 .php
```

## 🎯 Important Notes

1. **Web Server vs CLI:**
   - `.htaccess` only affects the **web server** (Apache)
   - **CLI/Terminal** still uses default PHP
   - For CLI commands, use: `/opt/cpanel/ea-php83/root/usr/bin/php artisan ...`

2. **cPanel PHP Selector:**
   - You can also set PHP version in cPanel > **Select PHP Version**
   - But `.htaccess` takes precedence for that directory

3. **Both Methods:**
   - Setting in cPanel: Applies to entire subdomain
   - Setting in `.htaccess`: Applies only to that directory (more specific)

## ✅ Verification

After updating `.htaccess`, verify:

1. **Pull latest code:**
   ```bash
   cd ~/booths.khbevents.com
   git pull origin main
   ```

2. **Check .htaccess:**
   ```bash
   head -5 public/.htaccess
   ```

3. **Test PHP version:**
   - Visit: `https://booths.khbevents.com/test.php` (if you create it)
   - Should show PHP 8.3.x

---

**The `.htaccess` file is now configured to use PHP 8.3!** 🚀
