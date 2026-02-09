# Deployment troubleshooting: 500 Internal Server Error on live (system.khbevents.com)

When you push code to the live site and see:

**"Oops! An Error Occurred — The server returned a 500 Internal Server Error"**

that is Laravel’s **generic production error page**. The real cause is hidden because `APP_DEBUG` is (correctly) `false` on production. You must find the actual error to fix it.

---

## 1. Find the real error (do this first)

### Option A: Laravel log (recommended)

On the server, open:

- **Path:** `/home/khbevents/system.khbevents.com/storage/logs/laravel.log`

View the last entries (newest at bottom):

```bash
tail -100 /home/khbevents/system.khbevents.com/storage/logs/laravel.log
```

Or download `storage/logs/laravel.log` via cPanel File Manager / SFTP and open the end of the file. The stack trace and message will tell you the exact error (e.g. missing `.env`, wrong permissions, missing class, database connection).

### Option B: Temporarily enable debug (use only to debug, then turn off)

1. On the server, edit `.env` in the project root.
2. Set:
   - `APP_DEBUG=true`
   - `APP_ENV=local` (optional, for more verbose errors)
3. Reload the site in the browser. You should see the real error page.
4. **Immediately** set `APP_DEBUG=false` and `APP_ENV=production` again and save. Do not leave debug on in production.

### Option C: PHP / cPanel error log

In cPanel: **Errors** or **Error Log** (or **Metrics → Errors**). The log path might be something like `~/logs/error.log` or shown in the interface. Check the time of your request for the corresponding PHP/Laravel error.

---

## 2. Common causes and fixes

After a **code push** to live, the most likely causes are:

| Cause | What to check | Fix |
|-------|----------------|-----|
| **Missing or wrong `.env`** | On server: does `.env` exist in project root? Is `APP_KEY` set? | Create `.env` from `.env.example`, set `APP_KEY` (run `php artisan key:generate` on server or copy from a safe backup). Set `APP_DEBUG=false`, `APP_ENV=production`, and correct `APP_URL`, DB_* for production. |
| **Storage not writable** | Laravel needs to write to `storage/` and `bootstrap/cache/`. | On server: `chmod -R 775 storage bootstrap/cache` and ensure the web server user (e.g. `nobody`, `apache`, or your cPanel user) owns or can write to those dirs. Some hosts need `chmod 777` for `storage` and `bootstrap/cache` if ownership can’t be changed. |
| **Vendor / autoload missing** | After push, `vendor/` might be missing or out of date (e.g. if you don’t deploy `vendor/` or don’t run Composer on server). | On server in project root: `composer install --no-dev --optimize-autoloader`. If PHP CLI is different from web PHP, use the same PHP version the site uses (e.g. `php81 composer install ...`). |
| **Cached config from old code** | Old cached config/routes can reference removed or changed code. | On server: `php artisan config:clear`, `php artisan cache:clear`, `php artisan route:clear`, `php artisan view:clear`. Then reload the site. |
| **PHP version** | Laravel 10 requires PHP 8.1+. | In cPanel, set the domain or app to use PHP 8.1 or 8.2 (e.g. **Select PHP Version** or **MultiPHP Manager**). |
| **Document root** | Site must run from the `public` folder. | Document root must be `/home/khbevents/system.khbevents.com/public` (see [CPANEL-REMOTE-FILE-MANAGER.md](CPANEL-REMOTE-FILE-MANAGER.md)). |
| **Database** | Wrong credentials or DB not reachable from web server. | In `.env` on server: check `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`. Test from server (e.g. `php artisan tinker` then `DB::connection()->getPdo();`). |

| **"Spatie\LaravelIgnition\IgnitionServiceProvider" not found** | Bootstrap cache was built on a machine with dev dependencies (e.g. your PC) and deployed to the server. Production uses `composer install --no-dev`, so Ignition is not installed. | **Do not use `php artisan`**—the app fails to boot. Delete the cache files by hand (SSH or File Manager): `rm -f bootstrap/cache/config.php bootstrap/cache/services.php bootstrap/cache/packages.php`. Then reload the site; Laravel will regenerate them from the server’s `vendor/` only. Set `APP_DEBUG=false` after fixing. |

---

## 3. After each deploy (recommended)

Run on the server from the project root:

```bash
cd /home/khbevents/system.khbevents.com
composer install --no-dev --optimize-autoloader
php artisan clear-compiled
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
# Optional: re-cache for production performance
# php artisan config:cache
# php artisan route:cache
```

**Important:** `php artisan clear-compiled` removes `bootstrap/cache/services.php` and `packages.php` so they are rebuilt from the server’s installed packages only (no dev-only packages like Ignition). If you deploy via SFTP and upload your local `bootstrap/cache/`, always run `clear-compiled` on the server after deploy.

Ensure:

- `.env` exists and has correct production values (and is **not** overwritten by your deploy).
- `storage` and `bootstrap/cache` are writable by the web server.

---

## 4. Summary

1. **Get the real error** from `storage/logs/laravel.log` (or temporarily `APP_DEBUG=true`, then turn off).
2. **Fix that error** using the table above (env, permissions, vendor, cache, PHP version, document root, DB).
3. **After every deploy**, run the clear commands (and `composer install` if you don’t deploy `vendor/`).

If you paste the **exact error message and stack trace** from `laravel.log` (with any sensitive lines removed), you can target the fix precisely.
