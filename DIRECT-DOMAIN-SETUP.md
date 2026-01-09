# Direct Domain Setup - booths.khbevents.com

This guide explains how to set up the application to run directly under `booths.khbevents.com` without requiring the document root to point to the `public` folder.

## 🎯 Setup Overview

When the document root is set to the main project folder (not `public`), the application uses:
- Root `.htaccess` to redirect requests to `public/`
- Root `index.php` to bootstrap Laravel
- Automatic URL detection for `booths.khbevents.com`

## 📋 Prerequisites

- Code pulled from GitHub to cPanel
- Domain `booths.khbevents.com` configured in cPanel
- Database created
- SSH or cPanel Terminal access

## 🚀 Step-by-Step Setup

### Step 1: Configure Document Root in cPanel

**Option A: Set to Main Folder (Recommended for Direct Access)**
1. Go to **cPanel → Subdomains** (or **Domains**)
2. Find `booths.khbevents.com`
3. Set Document Root to:
   ```
   /home/khbevents/booths.khbevents.com/kmallxmas-laravel
   ```
   (NOT the public folder)

**Option B: Set to Public Folder (Alternative)**
If you prefer traditional Laravel setup:
```
/home/khbevents/booths.khbevents.com/kmallxmas-laravel/public
```

Both options work! The code is configured to handle both scenarios.

### Step 2: Install Dependencies

```bash
cd /home/khbevents/booths.khbevents.com/kmallxmas-laravel
composer install --no-dev --optimize-autoloader
```

### Step 3: Create .env File

```bash
cp .env.example .env
```

Edit `.env` and ensure:
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
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Set File Permissions

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Step 6: Run Database Migrations

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Step 7: Cache Configuration

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 8: Test the Application

Visit: `https://booths.khbevents.com`

You should see the login page.

## 🔧 How It Works

### Root .htaccess
- Redirects all requests to `public/` folder
- Handles static files in root if they exist
- Preserves query strings and POST data

### Root index.php
- Detects if request is for a root file (serves directly)
- Routes all other requests to `public/index.php`
- Maintains proper server context for Laravel

### Public .htaccess
- Handles Laravel routing
- Processes all application requests
- Serves static assets from public folder

## ✅ Verification Checklist

After setup, verify:

- [ ] Document root configured correctly
- [ ] `.env` file exists with correct `APP_URL`
- [ ] `APP_KEY` generated
- [ ] File permissions set (storage: 755)
- [ ] Database migrations completed
- [ ] Application loads at `https://booths.khbevents.com`
- [ ] Login page displays correctly
- [ ] Assets (CSS/JS/images) load properly
- [ ] Routes work (try navigating)
- [ ] Can login with default credentials

## 🐛 Troubleshooting

### Assets Not Loading (404 for CSS/JS)

**Problem:** Assets return 404 errors

**Solution:**
1. Check that `public/.htaccess` exists
2. Verify `mod_rewrite` is enabled
3. Clear cache: `php artisan cache:clear`
4. Check asset paths in browser DevTools

### 500 Internal Server Error

**Problem:** Server error when accessing the site

**Solution:**
1. Check `storage/logs/laravel.log`
2. Verify file permissions: `chmod -R 755 storage bootstrap/cache`
3. Check `.env` file exists and is configured
4. Verify `APP_KEY` is set

### Routes Return 404

**Problem:** All routes return 404 except homepage

**Solution:**
1. Verify `.htaccess` files exist (root and public)
2. Check `mod_rewrite` is enabled
3. Clear route cache: `php artisan route:clear && php artisan route:cache`
4. Verify document root is correct

### Database Connection Error

**Problem:** Cannot connect to database

**Solution:**
1. Verify database credentials in `.env`
2. Check database exists in cPanel
3. Verify database user has proper permissions
4. Test connection: `php artisan tinker` then `DB::connection()->getPdo();`

## 🔐 Security Notes

1. **Set `APP_DEBUG=false`** in production
2. **Change default admin password** immediately
3. **Use HTTPS** (SSL certificate)
4. **Restrict file permissions** (755 for folders, 644 for files)
5. **Keep `.env` secure** (already in .gitignore)

## 📝 File Structure

```
/home/khbevents/booths.khbevents.com/kmallxmas-laravel/
├── .htaccess          ← Routes to public/
├── index.php          ← Bootstraps Laravel
├── app/
├── bootstrap/
├── config/
├── database/
├── public/            ← Laravel public folder
│   ├── .htaccess     ← Laravel routing
│   ├── index.php     ← Laravel entry point
│   └── images/
├── resources/
├── routes/
├── storage/
├── vendor/
└── .env               ← Configuration
```

## 🎉 Quick Setup Script

You can also use the automated deployment script:

```bash
chmod +x deploy-cpanel.sh
./deploy-cpanel.sh
```

Then configure the document root in cPanel as described in Step 1.

---

**Last Updated:** 2026-01-15
