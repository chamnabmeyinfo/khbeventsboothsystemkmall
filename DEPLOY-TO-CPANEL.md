# 🚀 Deploy to cPanel - Step by Step Guide

Complete guide to deploy your Laravel application to cPanel hosting.

## 📦 Step 1: Prepare Files Locally

### 1.1 Install Production Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 1.2 Verify All Files
- [ ] All code committed to Git
- [ ] `.env.example` is ready
- [ ] No sensitive data in code

## 📤 Step 2: Upload to cPanel

### Option A: Via Git (Recommended)
1. SSH into your cPanel server
2. Navigate to your domain directory:
   ```bash
   cd ~/public_html
   # or for subdomain:
   cd ~/public_html/subdomain
   ```
3. Clone your repository:
   ```bash
   git clone https://github.com/chamnabmeyinfo/khbeventsboothsystemkmall.git .
   ```

### Option B: Via FTP/File Manager
1. Upload all files to cPanel
2. Ensure complete file structure is uploaded
3. Verify all directories are present

## 🔧 Step 3: Configure Environment

### 3.1 Create .env File
```bash
cp .env.example .env
```

### 3.2 Edit .env File
Update these values:
```env
APP_NAME="KHB Booths Booking System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=khbevents_aebooths
DB_USERNAME=khbevents_admaebooths
DB_PASSWORD="ASDasd12345$$$%%%"
```

## 🔑 Step 4: Generate Application Key

```bash
php artisan key:generate
```

This will automatically update `APP_KEY` in your `.env` file.

## 📁 Step 5: Set File Permissions

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

## 📦 Step 6: Install Dependencies (if not done)

```bash
composer install --no-dev --optimize-autoloader
```

## 🗄️ Step 7: Database Setup

### 7.1 Create Database in cPanel
1. Log into cPanel
2. Go to **MySQL Databases**
3. Create database and user
4. Assign user to database with **ALL PRIVILEGES**

### 7.2 Test Database Connection
```bash
php artisan db:show
```

### 7.3 Run Migrations
```bash
php artisan migrate
```

## ✅ Step 8: Verify Configuration

### 8.1 Test Application
Visit your domain in browser - should load without errors.

### 8.2 Check Logs
```bash
tail -f storage/logs/laravel.log
```

## 🚀 Step 9: Optimize (Optional but Recommended)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔍 Quick Troubleshooting

### Issue: 500 Error
```bash
# Check logs
tail storage/logs/laravel.log

# Fix permissions
chmod -R 755 storage bootstrap/cache

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Issue: Database Connection Failed
- Verify credentials in `.env`
- Check user has ALL PRIVILEGES
- Test: `php artisan db:show`

### Issue: Permission Denied
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## 📋 Quick Command Reference

```bash
# Generate key
php artisan key:generate

# Run migrations
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check database
php artisan db:show

# View logs
tail -f storage/logs/laravel.log
```

## ✅ Deployment Checklist

- [ ] Files uploaded to cPanel
- [ ] `.env` file created and configured
- [ ] `APP_KEY` generated
- [ ] File permissions set correctly
- [ ] Dependencies installed
- [ ] Database created and configured
- [ ] Migrations run successfully
- [ ] Application accessible in browser
- [ ] No errors in logs
- [ ] Application optimized (cached)

---

**Your application should now be live!** 🎉
