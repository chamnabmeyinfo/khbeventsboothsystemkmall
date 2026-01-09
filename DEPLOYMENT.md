# Deployment Guide - KHB Booths Booking System

This guide explains how to deploy the application to cPanel with dynamic configuration.

## 🚀 Quick Deployment Steps

### 1. Upload Files to cPanel

Upload all files to your cPanel hosting:
- Upload to: `public_html/` or your subdomain directory
- Make sure all files are uploaded (including hidden files like `.env.example`)

### 2. Run Configuration Setup

#### Option A: Via Browser (Recommended)
1. Navigate to: `http://yourdomain.com/setup-config.php`
2. Review the detected configuration
3. Click "Generate .env File"
4. Review and edit the generated `.env` file if needed

#### Option B: Via Command Line (SSH)
```bash
php setup-config.php
```

### 3. Configure Database

Edit the `.env` file and update database credentials:
```env
DB_HOST=localhost
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Set Up Database

```bash
# Run migrations
php artisan migrate

# Seed initial data (optional)
php artisan db:seed
```

### 6. Set Permissions

```bash
# Set storage and cache permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Security

**IMPORTANT:** Delete the setup script after configuration:
```bash
rm setup-config.php
```

## 📁 Directory Structure for cPanel

### Option 1: Document Root = public_html
```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── index.php
│   └── .htaccess
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── artisan
```

**Configure .htaccess in public_html:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Option 2: Document Root = public_html/public (Recommended)
```
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          <- Set as Document Root
│   ├── index.php
│   └── .htaccess
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── artisan
```

## 🔧 Dynamic Configuration Features

The `app.php` file automatically:

1. **Detects Environment**
   - Localhost: `localhost`, `127.0.0.1`, `.local`, `.test`, `.dev`
   - Production: Everything else

2. **Sets Dynamic Paths**
   - Automatically finds Laravel root directory
   - Detects public directory (public or public_html)

3. **Configures URLs**
   - Detects protocol (http/https)
   - Handles subdirectories automatically
   - Handles custom ports

4. **Database Configuration**
   - Uses environment variables from .env
   - Falls back to sensible defaults

## 📝 Environment Variables

The application uses these environment variables (auto-configured in `app.php`):

```env
APP_NAME="KHB Booths Booking System"
APP_ENV=production
APP_KEY=                    # Generate with: php artisan key:generate
APP_DEBUG=false             # Set to false in production
APP_URL=http://yourdomain.com
APP_TIMEZONE=Asia/Phnom_Penh

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

SESSION_DRIVER=file
CACHE_DRIVER=file
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## 🔒 Security Checklist

- [ ] Delete `setup-config.php` after setup
- [ ] Set `APP_DEBUG=false` in production
- [ ] Set proper file permissions (storage, cache)
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL
- [ ] Keep Laravel and dependencies updated
- [ ] Don't commit `.env` file to version control

## 🐛 Troubleshooting

### Issue: 500 Internal Server Error
- Check file permissions: `chmod -R 775 storage bootstrap/cache`
- Check `.env` file exists and has correct values
- Check error logs: `storage/logs/laravel.log`

### Issue: Database Connection Error
- Verify database credentials in `.env`
- Check database exists and user has permissions
- Verify database host (might be `localhost` or IP address)

### Issue: Routes Not Working
- Ensure `.htaccess` file exists in `public/` directory
- Check `mod_rewrite` is enabled in Apache
- Verify Document Root points to `public/` directory

### Issue: Assets Not Loading
- Check `APP_URL` in `.env` matches your domain
- Verify asset paths in views
- Clear cache: `php artisan cache:clear`

## 📞 Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check Apache error logs
3. Review configuration in `setup-config.php` output

---

**Built with ❤️ using Laravel**

