# 🚀 cPanel Deployment Checklist

Complete checklist to deploy your Laravel application to cPanel hosting.

## 📋 Pre-Deployment Checklist

### 1. Local Preparation
- [ ] All code is committed to Git
- [ ] All dependencies are installed (`composer install --no-dev`)
- [ ] `.env.example` file is up to date
- [ ] All sensitive files are in `.gitignore`
- [ ] Test application locally to ensure it works

### 2. cPanel Server Requirements
- [ ] PHP >= 8.1 (check in cPanel > Select PHP Version)
- [ ] MySQL 5.7+ or MariaDB (check in cPanel > MySQL Databases)
- [ ] Composer installed (check via SSH: `composer --version`)
- [ ] Required PHP extensions enabled:
  - [ ] OpenSSL
  - [ ] PDO
  - [ ] PDO_MySQL
  - [ ] Mbstring
  - [ ] Tokenizer
  - [ ] XML
  - [ ] Ctype
  - [ ] JSON
  - [ ] Fileinfo

## 📤 Upload Files to cPanel

### Step 1: Upload Project Files
1. [ ] Upload all project files to cPanel (via FTP, File Manager, or Git)
2. [ ] Ensure files are in the correct directory:
   - For subdomain: `public_html/subdomain/`
   - For main domain: `public_html/`
   - For subdirectory: `public_html/your-folder/`

### Step 2: Verify File Structure
Ensure these directories exist:
- [ ] `app/`
- [ ] `bootstrap/`
- [ ] `config/`
- [ ] `database/`
- [ ] `public/`
- [ ] `resources/`
- [ ] `routes/`
- [ ] `storage/`
- [ ] `vendor/` (or will be created with composer install)

## 🔧 Configuration Steps

### Step 3: Create .env File
1. [ ] Copy `.env.example` to `.env`
2. [ ] Update database credentials:
   ```env
   DB_HOST=localhost
   DB_DATABASE=your_cpanel_username_boothsystem_db
   DB_USERNAME=your_cpanel_username_boothsystem_user
   DB_PASSWORD=your_database_password
   ```
3. [ ] Update `APP_URL` with your actual domain
4. [ ] Set `APP_ENV=production`
5. [ ] Set `APP_DEBUG=false`

### Step 4: Set File Permissions
Via SSH or File Manager, set permissions:
- [ ] `storage/` directory: `755` or `775`
- [ ] `bootstrap/cache/` directory: `755` or `775`
- [ ] `.env` file: `644` or `600` (more secure)

**Via SSH:**
```bash
chmod -R 755 storage bootstrap/cache
chmod 644 .env
```

### Step 5: Install Dependencies
Via SSH in project root:
```bash
composer install --no-dev --optimize-autoloader
```

- [ ] Dependencies installed successfully
- [ ] No errors during installation

### Step 6: Generate Application Key
Via SSH:
```bash
php artisan key:generate
```

- [ ] `APP_KEY` generated in `.env` file

### Step 7: Create Storage Link (if needed)
Via SSH:
```bash
php artisan storage:link
```

- [ ] Storage link created (if using public storage)

## 🗄️ Database Setup

### Step 8: Create Database in cPanel
1. [ ] Log into cPanel
2. [ ] Go to **MySQL Databases**
3. [ ] Create database: `boothsystem_db` → Full name: `username_boothsystem_db`
4. [ ] Create user: `boothsystem_user` → Full name: `username_boothsystem_user`
5. [ ] Assign user to database with **ALL PRIVILEGES**

### Step 9: Configure Database in .env
- [ ] Database credentials updated in `.env`
- [ ] Test connection: `php artisan db:show`

### Step 10: Run Migrations
Via SSH:
```bash
php artisan migrate
```

- [ ] Migrations run successfully
- [ ] All tables created

### Step 11: Seed Database (Optional)
Via SSH:
```bash
php artisan db:seed
```

- [ ] Database seeded (if needed)

## 🌐 Server Configuration

### Step 12: Configure .htaccess
- [ ] Root `.htaccess` file exists and is correct
- [ ] `public/.htaccess` file exists and is correct

### Step 13: Set Document Root (if needed)
If deploying to subdirectory:
- [ ] Document root points to `public/` folder
- [ ] Or `.htaccess` redirects correctly

### Step 14: Configure PHP Version
- [ ] PHP version set to 8.1 or higher (cPanel > Select PHP Version)
- [ ] Required extensions enabled

## ✅ Testing & Verification

### Step 15: Test Application
1. [ ] Visit your domain in browser
2. [ ] Application loads without errors
3. [ ] Login page accessible
4. [ ] Can log in successfully
5. [ ] Database operations work
6. [ ] No 500 errors in browser
7. [ ] Check Laravel logs: `storage/logs/laravel.log` (no critical errors)

### Step 16: Security Checks
- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] `.env` file not accessible via browser
- [ ] Sensitive files in `.gitignore`
- [ ] File permissions are secure

## 🔍 Troubleshooting

### Common Issues:

**500 Internal Server Error:**
- Check `storage/logs/laravel.log`
- Verify file permissions
- Check `.env` file exists and is configured
- Verify `APP_KEY` is set

**Database Connection Error:**
- Verify database credentials in `.env`
- Check database user has ALL PRIVILEGES
- Test connection: `php artisan db:show`

**Permission Denied:**
- Set storage permissions: `chmod -R 755 storage`
- Set bootstrap/cache permissions: `chmod -R 755 bootstrap/cache`

**Composer Not Found:**
- Install Composer via cPanel or SSH
- Or upload `vendor/` folder manually

## 📝 Post-Deployment

### Step 17: Optimize Application
Via SSH:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- [ ] Configuration cached
- [ ] Routes cached
- [ ] Views cached

### Step 18: Set Up Backups
- [ ] Configure database backups in cPanel
- [ ] Set up file backups (optional)

### Step 19: Monitor Logs
- [ ] Check `storage/logs/laravel.log` regularly
- [ ] Monitor for errors

## 🎉 Deployment Complete!

Once all items are checked:
- [ ] Application is live and accessible
- [ ] All features working correctly
- [ ] No errors in logs
- [ ] Security measures in place

---

**Need Help?** Check the troubleshooting section or review Laravel logs.
