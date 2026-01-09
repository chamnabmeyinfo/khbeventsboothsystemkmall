# cPanel Deployment Guide for KHB Events Booth System

This guide will help you deploy the Laravel application to cPanel at `booths.khbevents.com`.

## 📋 Prerequisites

1. Code pulled from GitHub to cPanel (already done ✅)
2. cPanel access with SSH or File Manager
3. PHP 8.1+ enabled
4. MySQL database created in cPanel
5. Composer installed (or use cPanel's PHP Selector)

## 🚀 Step-by-Step Deployment

### Step 1: Verify Directory Structure

Your code should be in one of these locations:
- `/home/khbevents/public_html/booths.khbevents.com/` (subdomain)
- `/home/khbevents/booths.khbevents.com/` (subdomain folder)
- `/home/khbevents/repositories/khbevents-boothsystem-kmall/` (if cloned to repositories)

**Important**: The document root should point to the `public` folder inside `kmallxmas-laravel`.

### Step 2: Set Document Root in cPanel

1. Go to **cPanel → Subdomains** (or **Domains**)
2. Find `booths.khbevents.com`
3. Edit the document root to point to:
   ```
   /home/khbevents/booths.khbevents.com/kmallxmas-laravel/public
   ```
   Or if your structure is different:
   ```
   /home/khbevents/public_html/booths/kmallxmas-laravel/public
   ```

### Step 3: Install Dependencies

**Option A: Using SSH (Recommended)**
```bash
cd /home/khbevents/booths.khbevents.com/kmallxmas-laravel
composer install --no-dev --optimize-autoloader
```

**Option B: Using cPanel Terminal**
- Go to cPanel → Terminal
- Run the same commands as Option A

**Option C: Using File Manager + PHP Selector**
- Use cPanel's PHP Selector to run composer
- Or upload `vendor` folder from local (not recommended)

### Step 4: Configure Environment File

1. In cPanel File Manager, navigate to `kmallxmas-laravel` folder
2. Copy `.env.example` to `.env` (if exists) or create new `.env`
3. Edit `.env` file with your production settings:

```env
APP_NAME="KHB Booths Booking System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://booths.khbevents.com
APP_TIMEZONE=Asia/Phnom_Penh

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_DRIVER=file

LOG_CHANNEL=stack
LOG_LEVEL=error
```

### Step 5: Generate Application Key

**Using SSH:**
```bash
cd /home/khbevents/booths.khbevents.com/kmallxmas-laravel
php artisan key:generate
```

**Using cPanel Terminal:**
- Same as above

### Step 6: Set File Permissions

**Using SSH:**
```bash
cd /home/khbevents/booths.khbevents.com/kmallxmas-laravel
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R khbevents:khbevents storage
chown -R khbevents:khbevents bootstrap/cache
```

**Using cPanel File Manager:**
1. Right-click `storage` folder → Change Permissions → 755
2. Right-click `bootstrap/cache` folder → Change Permissions → 755
3. Repeat for all subdirectories

### Step 7: Run Database Migrations

**Using SSH:**
```bash
cd /home/khbevents/booths.khbevents.com/kmallxmas-laravel
php artisan migrate --force
php artisan db:seed --force
```

### Step 8: Clear and Cache Configuration

**Using SSH:**
```bash
cd /home/khbevents/booths.khbevents.com/kmallxmas-laravel
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 9: Verify .htaccess Files

Ensure these files exist and are correct:

1. **`public/.htaccess`** - Should redirect all requests to `index.php`
2. **`.htaccess`** (root) - Should redirect to `public` folder (if document root is not set to public)

### Step 10: Test the Application

1. Visit `https://booths.khbevents.com`
2. You should see the login page
3. Default credentials:
   - Username: `admin`
   - Password: `password`
   - ⚠️ **Change immediately after first login!**

## 🔧 Alternative Setup: If Document Root Can't Be Changed

If you cannot change the document root to `public`, use this setup:

1. Keep document root as the main folder
2. The `.htaccess` in root will redirect to `public/index.php`
3. Ensure `index.php` in root exists (it should)

## 📝 Post-Deployment Checklist

- [ ] Document root points to `public` folder
- [ ] `.env` file configured with production settings
- [ ] `APP_KEY` generated
- [ ] File permissions set (storage: 755, bootstrap/cache: 755)
- [ ] Database migrations run
- [ ] Database seeded
- [ ] Configuration cached
- [ ] Application accessible via browser
- [ ] Login works
- [ ] Admin password changed
- [ ] SSL certificate installed (if using HTTPS)

## 🛠️ Troubleshooting

### Error: "500 Internal Server Error"
- Check file permissions (storage, bootstrap/cache)
- Check `.env` file exists and is configured
- Check error logs: `storage/logs/laravel.log`
- Verify PHP version (8.1+)

### Error: "No application encryption key"
- Run: `php artisan key:generate`

### Error: "Database connection failed"
- Verify database credentials in `.env`
- Check database exists in cPanel
- Verify database user has proper permissions

### Error: "Permission denied"
- Set correct file permissions
- Check ownership (should be your cPanel user)

### Pages show 404
- Verify document root is set to `public`
- Check `.htaccess` files exist
- Verify `mod_rewrite` is enabled

## 🔐 Security Recommendations

1. **Set `APP_DEBUG=false`** in production
2. **Change default admin password** immediately
3. **Use HTTPS** (SSL certificate)
4. **Restrict file permissions** (755 for folders, 644 for files)
5. **Keep `.env` secure** (already in .gitignore)
6. **Regular backups** of database and files
7. **Keep Laravel updated** for security patches

## 📞 Support

If you encounter issues:
1. Check `storage/logs/laravel.log` for errors
2. Check cPanel error logs
3. Verify all steps above are completed

---

**Last Updated**: 2026-01-15
