# 🚀 cPanel Git Deployment Guide

## Overview
This guide explains how to deploy the cleaned-up project to cPanel using Git version control.

## 📋 Prerequisites

1. ✅ Code is pushed to GitHub: `https://github.com/chamnabmeyinfo/khbeventsboothsystemkmall.git`
2. ✅ cPanel has Git Version Control feature enabled
3. ✅ Subdomain: `booths.khbevents.com` is set up in cPanel

## 🔧 Step 1: Push Code to GitHub (Local)

```bash
# Stage all changes
git add -A

# Commit changes
git commit -m "Clean up project: Remove non-essential files, use standard Laravel config"

# Push to GitHub
git push origin main
```

## 🔧 Step 2: Set Up Git in cPanel

### 2.1 Access Git Version Control

1. Log in to **cPanel**
2. Navigate to **Git Version Control** (under "Software" section)
3. Click **Create** or **Manage**

### 2.2 Clone Repository

**Repository URL:**
```
https://github.com/chamnabmeyinfo/khbeventsboothsystemkmall.git
```

**Repository Path:**
```
/home/khbevents/booths.khbevents.com
```

**Branch:**
```
main
```

**Click "Create"**

## 🔧 Step 3: Configure .env on cPanel

After cloning, SSH into your server or use cPanel File Manager:

1. Navigate to: `booths.khbevents.com/`
2. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

3. Edit `.env` with your cPanel database credentials:
   ```env
   APP_NAME="KHB Booths Booking System"
   APP_ENV=production
   APP_KEY=base64:YOUR_APP_KEY_HERE
   APP_DEBUG=false
   APP_URL=https://booths.khbevents.com
   APP_TIMEZONE=Asia/Phnom_Penh

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=khbevents_aebooths
   DB_USERNAME=khbevents_admaebooths
   DB_PASSWORD="your_cpanel_db_password"
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

## 🔧 Step 4: Install Dependencies

SSH into your server or use cPanel Terminal:

```bash
cd ~/booths.khbevents.com
composer install --no-dev --optimize-autoloader
```

**Note:** Make sure PHP version is 8.1+ in cPanel.

## 🔧 Step 5: Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## 🔧 Step 6: Run Migrations

```bash
php artisan migrate --force
```

## 🔧 Step 7: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 🔧 Step 8: Set Up Auto-Pull (Optional)

In cPanel Git Version Control:

1. Go to your repository
2. Enable **Auto-Deploy** or set up a **Webhook**
3. Or manually pull updates when needed:
   ```bash
   cd ~/booths.khbevents.com
   git pull origin main
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan config:clear
   php artisan cache:clear
   ```

## ✅ Verification

1. Visit: `https://booths.khbevents.com`
2. Test login functionality
3. Check that all features work correctly

## 🔄 Updating Code (After Initial Setup)

### Option 1: Manual Pull (Recommended)

```bash
cd ~/booths.khbevents.com
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
```

### Option 2: Use cPanel Git Interface

1. Go to **Git Version Control** in cPanel
2. Click **Pull or Deploy** on your repository
3. Run the commands above via SSH/Terminal

## ⚠️ Important Notes

1. **Never commit `.env` file** - It's in `.gitignore`
2. **Always backup database** before running migrations
3. **Test in staging first** if possible
4. **Keep `.env` secure** - Don't share credentials

## 🐛 Troubleshooting

### Issue: "Permission denied"
```bash
chmod -R 755 storage bootstrap/cache
chown -R khbevents:khbevents storage bootstrap/cache
```

### Issue: "Composer not found"
- Use full path: `/opt/cpanel/ea-php81/root/usr/bin/php /usr/local/bin/composer install`

### Issue: "Database connection failed"
- Verify `.env` has correct cPanel database credentials
- Check database user has proper permissions

### Issue: "500 Internal Server Error"
- Check `storage/logs/laravel.log`
- Verify file permissions
- Ensure `vendor/` directory exists

---

**Your project is now ready for cPanel deployment!** 🎉
