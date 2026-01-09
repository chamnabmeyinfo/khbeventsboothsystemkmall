# Quick Reference - Your Database Configuration

## 🔑 Your Database Credentials

```
Host:     localhost
Port:     3306
Database: khbevents_aebooths
Username: khbevents_admaebooths
Password: ASDasd12345$$$%%%
```

## 📝 .env File Configuration

Your `.env` file should contain:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=khbevents_aebooths
DB_USERNAME=khbevents_admaebooths
DB_PASSWORD=ASDasd12345$$$%%%
```

## ✅ Quick Setup Steps

1. **Verify in cPanel:**
   - Database exists: `khbevents_aebooths`
   - User exists: `khbevents_admaebooths`
   - User has ALL PRIVILEGES on database

2. **Generate APP_KEY:**
   ```bash
   php artisan key:generate
   ```

3. **Test Connection:**
   ```bash
   php artisan db:show
   ```

4. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

## 🔧 Common Commands

```bash
# Test database connection
php artisan db:show

# Run migrations
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# View Laravel logs
tail -f storage/logs/laravel.log
```

## ⚠️ Important Notes

- Password contains special characters (`$`, `%`) - should work as-is in `.env`
- If connection fails, try `DB_HOST=127.0.0.1` instead of `localhost`
- Never commit `.env` file to version control

---

For detailed instructions, see [YOUR-CPANEL-SETUP.md](./YOUR-CPANEL-SETUP.md)
