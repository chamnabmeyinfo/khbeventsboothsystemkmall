# 🚀 Deploy Code & Database Updates from Localhost to Live Server

## ⚠️ IMPORTANT: Before You Start

1. **Commit all changes locally** and push to Git
2. **Backup live database** (via cPanel phpMyAdmin)
3. **Test locally** to ensure everything works

---

## Step 1: Push Code Changes to Git (On Localhost)

```bash
# On your local machine
git add .
git commit -m "Update: Add new features and migrations"
git push origin main
```

---

## Step 2: Pull Code on Live Server

You're already on the live server. Run these commands:

```bash
# Navigate to your project directory
cd ~/system.khbevents.com

# Pull latest code from Git
git pull origin main

# If you get conflicts, resolve them first
```

---

## Step 3: Update Dependencies (If Needed)

```bash
# Install/update Composer dependencies
/opt/alt/php82/usr/bin/php composer.phar install --no-dev --optimize-autoloader

# Or if composer is in PATH:
composer install --no-dev --optimize-autoloader
```

---

## Step 4: Clear Caches

```bash
# Clear all caches
/opt/alt/php82/usr/bin/php artisan config:clear
/opt/alt/php82/usr/bin/php artisan cache:clear
/opt/alt/php82/usr/bin/php artisan route:clear
/opt/alt/php82/usr/bin/php artisan view:clear
```

---

## Step 5: Run Database Migrations

**This is the most important step** - it will update your database schema:

```bash
# Check migration status first
/opt/alt/php82/usr/bin/php artisan migrate:status

# Run migrations (with --force for production)
/opt/alt/php82/usr/bin/php artisan migrate --force

# If you see "Nothing to migrate", all migrations are up to date
```

---

## Step 6: Optimize for Production

```bash
# Cache configuration for better performance
/opt/alt/php82/usr/bin/php artisan config:cache

# Cache routes for better performance
/opt/alt/php82/usr/bin/php artisan route:cache

# Cache views for better performance
/opt/alt/php82/usr/bin/php artisan view:cache
```

---

## Step 7: Verify Deployment

1. **Check your website** - Visit `https://system.khbevents.com`
2. **Test key features** - Login, check floor plans, etc.
3. **Check error logs** if something doesn't work:
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## 🔄 If You Need to Sync Data (Not Just Schema)

**⚠️ WARNING**: Only do this if you want to **overwrite live data** with local data!

### Option A: Export Local Database

1. On localhost, open phpMyAdmin
2. Export your database with DROP statements
3. Upload to cPanel
4. Import via phpMyAdmin (see `database/EXPORT_LOCAL_TO_LIVE_CPANEL_GUIDE.md`)

### Option B: Use Command Line (SSH)

```bash
# On localhost, export database
mysqldump -u root -p khbeventskmallxmas > local_export.sql

# Upload to server (via SCP or cPanel File Manager)
# Then on live server:
mysql -u khbevents_admaebooths -p khbevents_aebooths < local_export.sql
```

---

## 🐛 Troubleshooting

### Error: "Nothing to migrate"

- ✅ **This is OK!** It means all migrations are already applied
- Your database is up to date

### Error: "Table already exists"

- The migration has guards, but if you still get this:
  ```bash
  # Check which migrations have run
  /opt/alt/php82/usr/bin/php artisan migrate:status
  ```

### Error: "Class not found"

- Clear caches and re-run:
  ```bash
  /opt/alt/php82/usr/bin/php artisan config:clear
  /opt/alt/php82/usr/bin/php composer.phar dump-autoload
  ```

### Error: "Permission denied"

- Check file permissions:
  ```bash
  chmod -R 755 storage bootstrap/cache
  chown -R khbevents:khbevents storage bootstrap/cache
  ```

---

## ✅ Quick Deployment Checklist

- [ ] Committed and pushed code to Git
- [ ] Backed up live database
- [ ] Pulled latest code on live server
- [ ] Cleared caches
- [ ] Ran migrations
- [ ] Optimized for production
- [ ] Tested website functionality
- [ ] Checked error logs

---

## 📝 Notes

- **Migrations only update schema** (tables, columns, indexes)
- **Migrations do NOT sync data** - they only create/modify structure
- **To sync data**, you need to export/import the database separately
- **Always backup** before running migrations on production
