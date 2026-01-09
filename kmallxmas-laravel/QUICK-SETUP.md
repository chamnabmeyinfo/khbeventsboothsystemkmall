# Quick Setup Checklist for cPanel

Use this checklist after pulling code from GitHub to cPanel.

## ✅ Pre-Deployment Checklist

- [ ] Code pulled from GitHub to cPanel folder
- [ ] SSH access available (or cPanel Terminal)
- [ ] Database created in cPanel
- [ ] Database credentials noted

## 🚀 Quick Setup Steps

### 1. Navigate to Project Directory
```bash
cd /home/khbevents/booths.khbevents.com/kmallxmas-laravel
# OR wherever your code is located
```

### 2. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Create .env File
```bash
cp .env.example .env
# Then edit .env with your database credentials
```

### 4. Generate App Key
```bash
php artisan key:generate
```

### 5. Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
```

### 6. Run Migrations
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 7. Clear and Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Configure Document Root

**In cPanel:**
1. Go to **Subdomains** (or **Domains**)
2. Find `booths.khbevents.com`
3. Set Document Root to:
   ```
   /home/khbevents/booths.khbevents.com/kmallxmas-laravel/public
   ```

### 9. Test Application
- Visit: `https://booths.khbevents.com`
- Login with: `admin` / `password`
- **Change password immediately!**

## 🔧 One-Command Setup (if deploy script is executable)

```bash
chmod +x deploy-cpanel.sh
./deploy-cpanel.sh
```

## ⚠️ Important Notes

1. **Document Root MUST point to `public` folder**
2. **Set `APP_DEBUG=false` in production**
3. **Change default admin password**
4. **Verify file permissions** (storage: 755)

## 🐛 Common Issues

**500 Error:**
- Check permissions: `chmod -R 755 storage bootstrap/cache`
- Check `.env` file exists
- Check `APP_KEY` is set

**Database Error:**
- Verify database credentials in `.env`
- Check database exists in cPanel

**404 Errors:**
- Verify document root points to `public`
- Check `.htaccess` exists in `public` folder

---

**Need help?** Check `CPANEL-DEPLOYMENT.md` for detailed instructions.
