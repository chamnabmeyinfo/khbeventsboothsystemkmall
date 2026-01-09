# 🔧 Fix Local Development - Quick Guide

## Problem
Getting "Not Found" error when accessing `localhost:8000`

## ✅ Solution

### Method 1: Use Laravel's Built-in Server (Recommended)

1. **Open terminal in project folder:**
   ```bash
   cd C:\xampp\htdocs\KHB\khbevents\boothsystemv1
   ```

2. **Update .env file manually:**
   Open `.env` and change:
   ```env
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8000
   
   DB_HOST=127.0.0.1
   DB_DATABASE=khbevents_kmall
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Clear cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

4. **Start Laravel server:**
   ```bash
   php artisan serve
   ```

5. **Visit:** http://localhost:8000

### Method 2: Use XAMPP Apache

If you want to use XAMPP Apache instead:

1. **Update .env:**
   ```env
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost
   ```

2. **Access via:**
   - http://localhost/KHB/khbevents/boothsystemv1/public/
   
   OR configure virtual host to point to `public/` folder

## ⚠️ Important

**Port 8000 with Apache error means:**
- You're trying to access via XAMPP Apache on port 8000
- But Laravel's `php artisan serve` should be used for port 8000
- OR configure XAMPP to use port 80, not 8000

## 🚀 Quick Fix

**Just run this:**
```bash
cd C:\xampp\htdocs\KHB\khbevents\boothsystemv1
php artisan serve
```

Then visit: **http://localhost:8000**

---

**The issue:** You're accessing via XAMPP Apache, but should use `php artisan serve` for port 8000.
