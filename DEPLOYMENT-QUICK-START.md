# 🚀 Quick Deployment Guide

## ✅ Step 1: Code is Pushed to GitHub

Your code has been pushed to:
```
https://github.com/chamnabmeyinfo/khbeventsboothsystemkmall.git
```

## 📋 Step 2: Set Up Git in cPanel

1. **Log in to cPanel**
2. **Find "Git Version Control"** (under Software section)
3. **Click "Create"**
4. **Enter these details:**
   - **Repository URL:** `https://github.com/chamnabmeyinfo/khbeventsboothsystemkmall.git`
   - **Repository Path:** `/home/khbevents/booths.khbevents.com`
   - **Branch:** `main`
5. **Click "Create"**

## ⚙️ Step 3: Configure .env on cPanel

After cloning, use **cPanel File Manager** or **Terminal**:

1. **Navigate to:** `booths.khbevents.com/`
2. **Copy .env.example to .env:**
   ```bash
   cp .env.example .env
   ```

3. **Edit .env** with your cPanel database:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://booths.khbevents.com
   
   DB_HOST=localhost
   DB_DATABASE=khbevents_aebooths
   DB_USERNAME=khbevents_admaebooths
   DB_PASSWORD="your_password"
   ```

4. **Generate app key:**
   ```bash
   php artisan key:generate
   ```

## 📦 Step 4: Install Dependencies

In **cPanel Terminal** or **SSH**:

```bash
cd ~/booths.khbevents.com
composer install --no-dev --optimize-autoloader
```

## 🔧 Step 5: Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
```

## 🗄️ Step 6: Run Migrations

```bash
php artisan migrate --force
```

## 🧹 Step 7: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## ✅ Step 8: Test

Visit: **https://booths.khbevents.com**

---

**For detailed instructions, see:** `docs/CPANEL-GIT-DEPLOYMENT.md`
