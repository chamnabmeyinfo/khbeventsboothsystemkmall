# 📋 Copying .env to cPanel - Complete Guide

## ✅ Yes, Copy Your .env File - But Update These First!

You should copy your `.env` file to cPanel, but **you need to update a few values** for production.

## 🔄 What to Update Before Copying

### 1. **APP_URL** ⚠️ MUST UPDATE
```env
# Change from:
APP_URL=https://yourdomain.com

# To your actual domain:
APP_URL=https://your-actual-domain.com
```

### 2. **APP_KEY** ⚠️ MUST GENERATE
```env
# Currently empty - you need to generate this on cPanel:
APP_KEY=

# After uploading, run on cPanel:
php artisan key:generate
```

### 3. **Mail Configuration** (Optional but Recommended)
```env
# Current (for local development):
MAIL_HOST=mailhog
MAIL_PORT=1025

# Update for production (use your cPanel email settings):
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="KHB Booths Booking System"
```

## ❌ What to Remove (Not Needed on Production)

### GitHub Configuration Section
Remove these lines (they're only for local Git operations):
```env
# GitHub Configuration
GITHUB_USERNAME=your_github_username
GITHUB_TOKEN=your_github_token_here
GITHUB_REPO_URL=https://github.com/yourusername/yourrepo.git
GITHUB_USE_SSH=false
```

## ✅ What's Already Correct (Don't Change)

These are already configured correctly for cPanel:
- ✅ Database credentials (already set)
- ✅ `APP_ENV=production`
- ✅ `APP_DEBUG=false`
- ✅ `DB_HOST=localhost`
- ✅ Session, Cache, and Logging settings

## 📝 Complete Production .env Template

Here's what your production `.env` should look like:

```env
# Laravel Application Configuration
APP_NAME="KHB Booths Booking System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-actual-domain.com
APP_TIMEZONE=Asia/Phnom_Penh

# Database Configuration for cPanel
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=khbevents_aebooths
DB_USERNAME=khbevents_admaebooths
DB_PASSWORD="ASDasd12345$$$%%%"

# Session Configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Cache Configuration
CACHE_DRIVER=file
CACHE_PREFIX=

# Queue Configuration
QUEUE_CONNECTION=sync

# Logging Configuration
LOG_CHANNEL=stack
LOG_LEVEL=error
LOG_DEPRECATIONS_CHANNEL=null

# Mail Configuration (Update with your cPanel email settings)
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="KHB Booths Booking System"

# Additional Configuration
BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
```

## 🚀 Step-by-Step Process

### Step 1: Edit .env Locally
1. Open your `.env` file
2. Update `APP_URL` with your actual domain
3. Remove the GitHub configuration section
4. (Optional) Update mail configuration

### Step 2: Upload to cPanel
1. Upload the `.env` file to your project root on cPanel
2. Make sure it's in the same directory as `artisan`, `composer.json`, etc.

### Step 3: Generate APP_KEY on cPanel
After uploading, connect to your cPanel via SSH or use Terminal in cPanel:

```bash
cd /path/to/your/project
php artisan key:generate
```

This will automatically update `APP_KEY` in your `.env` file.

### Step 4: Verify File Permissions
Make sure `.env` has correct permissions:
- File permissions: `644` or `600` (more secure)
- Should be readable by web server, but not writable by others

### Step 5: Test Connection
```bash
php artisan db:show
```

### Step 6: Run Migrations
```bash
php artisan migrate
```

## 🔐 Security Checklist

Before going live, ensure:

- [ ] `APP_DEBUG=false` (already set ✅)
- [ ] `APP_ENV=production` (already set ✅)
- [ ] `APP_KEY` is generated (do this after upload)
- [ ] `.env` file permissions are secure (644 or 600)
- [ ] GitHub credentials removed (not needed on server)
- [ ] Database password is secure (already set ✅)
- [ ] `APP_URL` matches your actual domain

## 📍 Where to Upload .env in cPanel

Upload `.env` to your project root directory. This is typically:
- `public_html/your-project/` (if in subdirectory)
- `public_html/` (if in root)

The `.env` file should be in the same directory as:
- `artisan`
- `composer.json`
- `app/` folder
- `config/` folder

## ⚠️ Important Notes

1. **Never commit `.env` to Git** - It's already in `.gitignore` ✅
2. **Keep a backup** - Save a copy of your production `.env` securely
3. **APP_KEY is unique** - Each environment needs its own key
4. **Test locally first** - Make sure everything works before going live

## 🆘 Quick Reference

**Must Update:**
- `APP_URL` → Your actual domain
- `APP_KEY` → Generate after upload (`php artisan key:generate`)

**Must Remove:**
- GitHub configuration section

**Optional but Recommended:**
- Mail configuration (for email functionality)

**Already Correct:**
- Database credentials ✅
- Environment settings ✅
- Most other configurations ✅

---

**Ready to deploy?** Follow the steps above and you'll be all set! 🚀
