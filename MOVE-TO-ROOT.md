# Move Project to Root Folder

This guide will help you move all code from `kmallxmas-laravel/` to the root folder so the project runs directly under `booths.khbevents.com`.

## 🎯 Goal

Move from:
```
/home/khbevents/booths.khbevents.com/kmallxmas-laravel/
```

To:
```
/home/khbevents/booths.khbevents.com/
```

## 📋 Pre-Migration Checklist

- [ ] Backup your current code
- [ ] Ensure you have SSH or cPanel File Manager access
- [ ] Note your current `.env` configuration
- [ ] Document any custom changes

## 🚀 Migration Steps

### Option 1: Using SSH (Recommended)

```bash
# Navigate to the parent directory
cd /home/khbevents/booths.khbevents.com

# Move all contents from kmallxmas-laravel to root
mv kmallxmas-laravel/* .
mv kmallxmas-laravel/.* . 2>/dev/null || true

# Remove the now-empty kmallxmas-laravel folder
rmdir kmallxmas-laravel

# Verify structure
ls -la
```

### Option 2: Using cPanel File Manager

1. **Open File Manager** in cPanel
2. **Navigate to** `booths.khbevents.com` folder
3. **Select all files** inside `kmallxmas-laravel` folder
4. **Cut** (Ctrl+X) all files
5. **Navigate back** to `booths.khbevents.com` root
6. **Paste** (Ctrl+V) all files
7. **Delete** the empty `kmallxmas-laravel` folder

### Option 3: Using Git (If you want to start fresh)

```bash
cd /home/khbevents/booths.khbevents.com

# Remove old structure
rm -rf kmallxmas-laravel

# Clone fresh from GitHub (if needed)
# git clone https://github.com/chamnabmeyinfo/khbevents-boothsystem-kmall.git .

# Or pull latest
git pull origin main
```

## ✅ Post-Migration Verification

After moving files, verify:

1. **Check structure:**
   ```bash
   ls -la
   # Should see: app/, bootstrap/, config/, database/, public/, routes/, etc.
   ```

2. **Verify key files exist:**
   - `.htaccess` (root)
   - `index.php` (root)
   - `composer.json`
   - `artisan`
   - `public/.htaccess`
   - `public/index.php`

3. **Check permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

4. **Test application:**
   - Visit: `https://booths.khbevents.com`
   - Should see login page

## 🔧 Update Document Root in cPanel

After migration, update document root:

1. **cPanel → Subdomains** (or **Domains**)
2. **Edit** `booths.khbevents.com`
3. **Set Document Root** to:
   ```
   /home/khbevents/booths.khbevents.com
   ```
   (NOT a subfolder - the root itself)

## 📝 Final Structure

After migration, your structure should be:

```
/home/khbevents/booths.khbevents.com/
├── .htaccess          ← Root .htaccess
├── index.php          ← Root index.php
├── .env               ← Environment config
├── .env.example
├── artisan
├── composer.json
├── app/
├── bootstrap/
├── config/
├── database/
├── public/            ← Public folder
│   ├── .htaccess
│   ├── index.php
│   └── images/
├── resources/
├── routes/
├── storage/
└── vendor/
```

## ⚠️ Important Notes

1. **Keep `.env` file** - Don't lose your configuration
2. **Preserve `storage/`** - Contains logs and sessions
3. **Maintain permissions** - Set storage to 755
4. **Update git** - If using git, the root is now the repo root

## 🐛 Troubleshooting

### Files not moving?
- Check file permissions
- Ensure you have write access to root folder
- Try using SSH instead of File Manager

### Application not working?
- Verify `.htaccess` exists in root
- Check `index.php` exists in root
- Verify document root is set correctly
- Check file permissions: `chmod -R 755 storage bootstrap/cache`

### 404 Errors?
- Verify document root points to root folder (not subfolder)
- Check `.htaccess` files exist
- Clear cache: `php artisan config:clear`

---

**After migration, update your deployment scripts and documentation!**
