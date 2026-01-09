# Root Folder Deployment Guide

This guide is for deploying the application when all code is in the root folder (not in a subdirectory).

## 📁 Directory Structure

After moving files to root, your structure should be:

```
/home/khbevents/booths.khbevents.com/
├── .htaccess          ← Root .htaccess (routes to public/)
├── index.php          ← Root index.php (bootstraps Laravel)
├── .env               ← Environment configuration
├── .env.example
├── artisan            ← Laravel CLI
├── composer.json
├── app/               ← Application code
├── bootstrap/         ← Bootstrap files
├── config/            ← Configuration files
├── database/          ← Migrations and seeders
├── public/            ← Public assets (document root can point here OR root)
│   ├── .htaccess     ← Laravel routing
│   ├── index.php     ← Laravel entry point
│   └── images/
├── resources/         ← Views and assets
├── routes/            ← Route definitions
├── storage/           ← Logs, cache, sessions
└── vendor/            ← Composer dependencies
```

## 🚀 Quick Setup

### 1. Set Document Root in cPanel

**Option A: Point to Root (Recommended)**
```
/home/khbevents/booths.khbevents.com
```
The root `.htaccess` will automatically route to `public/`

**Option B: Point to Public Folder**
```
/home/khbevents/booths.khbevents.com/public
```
Traditional Laravel setup

### 2. Run Setup Commands

```bash
cd /home/khbevents/booths.khbevents.com

# Install dependencies
composer install --no-dev --optimize-autoloader

# Create .env file
cp .env.example .env
# Edit .env with your database credentials

# Generate app key
php artisan key:generate

# Set permissions
chmod -R 755 storage bootstrap/cache

# Run migrations
php artisan migrate --force
php artisan db:seed --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Test Application

Visit: `https://booths.khbevents.com`

Default login:
- Username: `admin`
- Password: `password`

**⚠️ Change password immediately!**

## ✅ Verification Checklist

- [ ] Document root set correctly
- [ ] `.env` file exists and configured
- [ ] `APP_KEY` generated
- [ ] File permissions set (storage: 755)
- [ ] Database migrations completed
- [ ] Application accessible
- [ ] Login works
- [ ] Admin password changed

## 🔧 How It Works

### Root .htaccess
- Routes all non-file requests to `public/` folder
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

## 🐛 Troubleshooting

### 500 Internal Server Error
- Check file permissions: `chmod -R 755 storage bootstrap/cache`
- Check `.env` file exists
- Check `APP_KEY` is set
- View logs: `storage/logs/laravel.log`

### Assets Not Loading
- Verify `.htaccess` files exist (root and public)
- Check `mod_rewrite` is enabled
- Clear cache: `php artisan cache:clear`

### Routes Return 404
- Verify `.htaccess` files exist
- Check `mod_rewrite` is enabled
- Clear route cache: `php artisan route:clear && php artisan route:cache`

### Database Connection Error
- Verify database credentials in `.env`
- Check database exists in cPanel
- Verify database user permissions

---

**Last Updated:** 2026-01-15
