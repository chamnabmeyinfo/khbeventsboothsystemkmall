# Server Setup After Fresh Pull (system.khbevents.com)

If you **deleted old code and pulled the latest** from Git, the server has no `.env` and no `vendor/`. Follow these steps to fix the **500 Internal Server Error**.

Use **cPanel Terminal** or **SSH**. All commands run from the **project root**:
`/home/khbevents/system.khbevents.com`

---

## Step 1: Document root (cPanel)

The site must point to the **`public`** folder, not the project root.

- **cPanel → Domains → Domains →** select `system.khbevents.com` → **Manage**
- Set **Document Root** to:
  ```
  /home/khbevents/system.khbevents.com/public
  ```
- Save. If it already points to `.../public`, skip this.

---

## Step 2: Create `.env`

**Option A — Copy from repo:**  
If the project has `env.laravel.example`, run:

```bash
cd /home/khbevents/system.khbevents.com
cp env.laravel.example .env
```

Then edit `.env` and set `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and `APP_URL`.

**Option B — Create by hand:**  
Create the file `/home/khbevents/system.khbevents.com/.env` with at least these (replace placeholders with your real values):

```ini
APP_NAME="KHB Booths Booking System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://system.khbevents.com
APP_TIMEZONE=Asia/Phnom_Penh

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=file
SESSION_LIFETIME=120
LOG_CHANNEL=stack
LOG_LEVEL=error
```

- Use the **same database name, user, and password** you had before (from cPanel → MySQL® Databases or your host’s MySQL panel).
- Leave `APP_KEY=` empty for now; Step 4 will generate it.

---

## Step 3: Install PHP dependencies

```bash
cd /home/khbevents/system.khbevents.com
composer install --no-dev --optimize-autoloader
```

If `composer` is not in PATH, use the full path your host gives (e.g. `php /path/to/composer install --no-dev --optimize-autoloader`).

---

## Step 4: Generate application key

```bash
cd /home/khbevents/system.khbevents.com
php artisan key:generate --force
```

This fills `APP_KEY` in `.env`.

---

## Step 5: Avoid Collision/cache 500 error

Remove old bootstrap cache so Laravel does not load missing dev packages:

```bash
cd /home/khbevents/system.khbevents.com
rm -f bootstrap/cache/services.php bootstrap/cache/packages.php bootstrap/cache/config.php
```

---

## Step 6: Rebuild caches (optional but recommended)

```bash
cd /home/khbevents/system.khbevents.com
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If any command fails, run first:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

then repeat the cache commands above.

---

## Step 7: Permissions

Ensure the web server can write to `storage` and `bootstrap/cache`:

```bash
cd /home/khbevents/system.khbevents.com
chmod -R 775 storage bootstrap/cache
```

On cPanel, the user is often your account (e.g. `khbevents`). If you see “permission denied” in logs or cache:

```bash
chown -R khbevents:khbevents storage bootstrap/cache
```

Use the user your host says runs PHP for this domain.

---

## Step 8: Run migrations (if the database was recreated)

Only if this is a **new** or **empty** database:

```bash
cd /home/khbevents/system.khbevents.com
php artisan migrate --force
```

If you **restored an existing database**, do **not** run `migrate` unless you know you need new migrations.

---

## Quick checklist

| Step | Action                                                       |
| ---- | ------------------------------------------------------------ |
| 1    | Document root = `.../system.khbevents.com/public`            |
| 2    | `.env` exists with APP*\*, DB*\_, SESSION\_\_, LOG\_\*       |
| 3    | `composer install --no-dev --optimize-autoloader`            |
| 4    | `php artisan key:generate --force`                           |
| 5    | Delete `bootstrap/cache/services.php` and `packages.php`     |
| 6    | `php artisan config:cache` (and route/view cache if desired) |
| 7    | `chmod -R 775 storage bootstrap/cache`                       |
| 8    | `php artisan migrate --force` only if DB is new/empty        |

Then open **https://system.khbevents.com/** again.

---

## If you still get 500

1. **Turn on errors temporarily**  
   In `.env` set:

   ```ini
   APP_DEBUG=true
   ```

   Reload the page and check the full error message (then set `APP_DEBUG=false` again).

2. **Check the log**

   ```bash
   tail -50 /home/khbevents/system.khbevents.com/storage/logs/laravel.log
   ```

3. **Ensure `storage` and `bootstrap/cache` exist**
   ```bash
   mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

See also [GO-LIVE-PROCEDURE.md](GO-LIVE-PROCEDURE.md) and [DEPLOYMENT-FIX.md](DEPLOYMENT-FIX.md).
