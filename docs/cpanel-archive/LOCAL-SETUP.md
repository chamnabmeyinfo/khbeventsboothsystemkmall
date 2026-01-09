# 🖥️ Local Development Setup

Quick guide to run the application locally.

## ✅ Quick Start

### Step 1: Update .env for Local Development
The `.env` file has been updated with local settings:
- `APP_ENV=local`
- `APP_DEBUG=true`
- `APP_URL=http://localhost:8000`
- Database: `khbevents_kmall` (update if different)

### Step 2: Update Database Credentials (if needed)
Edit `.env` and update these if your local database is different:
```env
DB_DATABASE=khbevents_kmall
DB_USERNAME=root
DB_PASSWORD=your_local_password
```

### Step 3: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 4: Start Development Server
```bash
php artisan serve
```

Then visit: **http://localhost:8000**

## 🔧 Alternative: Using XAMPP Apache

If you prefer using XAMPP Apache instead of `php artisan serve`:

### Option A: Configure XAMPP Virtual Host
1. Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
2. Add:
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/xampp/htdocs/KHB/khbevents/boothsystemv1/public"
       ServerName localhost
       <Directory "C:/xampp/htdocs/KHB/khbevents/boothsystemv1/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
3. Restart Apache
4. Visit: **http://localhost**

### Option B: Access via Project Path
Visit: **http://localhost/KHB/khbevents/boothsystemv1/public/**

## ⚠️ Important Notes

- **For Local:** Use `APP_ENV=local` and `APP_DEBUG=true`
- **For cPanel:** Use `APP_ENV=production` and `APP_DEBUG=false`
- Always clear cache after changing `.env` file

## 🐛 Troubleshooting

### Issue: "Not Found" error
- Make sure you're using `php artisan serve` (not XAMPP Apache on port 8000)
- Or configure XAMPP virtual host correctly
- Check that `vendor/` directory exists

### Issue: Database connection error
- Verify local database exists
- Check credentials in `.env`
- Make sure MySQL is running in XAMPP

### Issue: 500 error
- Check `storage/logs/laravel.log`
- Run: `php artisan config:clear`
- Verify file permissions

---

**Recommended:** Use `php artisan serve` for local development - it's simpler! 🚀
