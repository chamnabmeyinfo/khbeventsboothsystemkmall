# 🔧 Fix Local Development - Complete Guide

## ⚠️ Current Issue

Your `.env` file has **cPanel production** credentials, but you're running **locally**.

## ✅ Solution: Update .env File

### Step 1: Open `.env` File
Edit: `C:\xampp\htdocs\KHB\khbevents\boothsystemv1\.env`

### Step 2: Update These Values

**Change from (cPanel):**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://booths.khbevents.com

DB_HOST=localhost
DB_DATABASE=khbevents_aebooths
DB_USERNAME=khbevents_admaebooths
DB_PASSWORD="ASDasd12345$$%%%"
```

**To (Local):**
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_HOST=127.0.0.1
DB_DATABASE=khbevents_kmall
DB_USERNAME=root
DB_PASSWORD=
```

**Note:** 
- Update `DB_DATABASE` to match your **local database name**
- `DB_PASSWORD` is usually empty for XAMPP, or use your MySQL password

### Step 3: Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 4: Verify Database Exists
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Check if database `khbevents_kmall` exists
3. If not, create it or update `.env` with your actual database name

### Step 5: Test
1. Start server: `php artisan serve`
2. Visit: http://localhost:8000
3. Try to login

## 🎯 Quick Copy-Paste for .env

Replace these lines in your `.env`:

```env
APP_NAME="KHB Booths Booking System"
APP_ENV=local
APP_KEY=base64:UnO17uy4PSghuEfwM7dMITkZreLT+W9lasKeVtMJxmA=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Phnom_Penh

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=khbevents_kmall
DB_USERNAME=root
DB_PASSWORD=
```

## ✅ After Fixing

1. Clear cache: `php artisan config:clear`
2. Test database: `php artisan db:show`
3. Start server: `php artisan serve`
4. Visit: http://localhost:8000

---

**The app.php file will also auto-detect localhost and override settings, but updating .env is the proper way!**
