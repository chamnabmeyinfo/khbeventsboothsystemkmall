# Quick Start - Direct Domain Access

## 🎯 For booths.khbevents.com

Your code is now configured to run directly under `booths.khbevents.com`!

## ✅ What's Configured

1. **Root `.htaccess`** - Routes all requests to `public/` folder
2. **Root `index.php`** - Bootstraps Laravel correctly
3. **Public `.htaccess`** - Handles Laravel routing
4. **Auto URL Detection** - Automatically detects `booths.khbevents.com`

## 🚀 Quick Setup (3 Steps)

### 1. Set Document Root in cPanel

**cPanel → Subdomains → Edit booths.khbevents.com**

Set Document Root to:

```
/home/khbevents/booths.khbevents.com
```

_(NOT the public folder - the code handles that automatically)_

### 2. Run Setup Commands

```bash
cd /home/khbevents/booths.khbevents.com
composer install --no-dev --optimize-autoloader
cp .env.example .env
# Edit .env with your database credentials
php artisan key:generate
chmod -R 755 storage bootstrap/cache
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Verify Setup

Visit: `https://booths.khbevents.com/verify-setup.php?password=khbevents2026`

This will check all your settings and show what needs to be fixed.

**⚠️ Delete `verify-setup.php` after verification for security!**

## 📚 Documentation

- **`DIRECT-DOMAIN-SETUP.md`** - Detailed setup guide
- **`CPANEL-DEPLOYMENT.md`** - Full deployment guide
- **`QUICK-SETUP.md`** - Quick checklist

## 🎉 That's It!

After completing the steps above, visit:
**https://booths.khbevents.com**

Default login:

- Username: `admin`
- Password: `password`

**⚠️ Change the password immediately!**

---

**Need help?** Check the troubleshooting section in `DIRECT-DOMAIN-SETUP.md`
