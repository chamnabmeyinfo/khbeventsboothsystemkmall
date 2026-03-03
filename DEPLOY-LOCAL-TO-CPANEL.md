# Deploy Local Development to cPanel

**Goal:** Get the KHB Booths Booking System from your Windows/XAMPP machine to **https://system.khbevents.com** on cPanel.

**Production URL:** https://system.khbevents.com  
**Project path:** `/home/khbevents/system.khbevents.com`  
**Document root:** `/home/khbevents/system.khbevents.com/public`

---

## Overview

| Phase | Where                    | What you do                                |
| ----- | ------------------------ | ------------------------------------------ |
| **A** | Local PC                 | Prepare and push code (or pack for upload) |
| **B** | cPanel (first time only) | Domain, PHP, database                      |
| **C** | cPanel / FTP / Git       | Put code on the server                     |
| **D** | Server (Terminal/SSH)    | .env, Composer, Laravel setup              |
| **E** | Browser                  | Verify the site                            |

---

## Part A — On your local machine

### A1. Test and commit

```powershell
cd c:\xampp\htdocs\KHB\khbevents\boothsystemv1

# Optional: run local deploy check
.\deploy-windows-local.bat

# Commit and push (if using Git)
git add .
git status
git commit -m "Deploy: sync to production"
git push origin main
```

- If you **don’t use Git**, make sure the folder is the version you want and you’ll upload it in Part C (FTP/File Manager).

### A2. Know what not to upload

Do **not** copy to the server (or overwrite on the server):

- `.env` — create/keep it only on the server with production values.
- `node_modules/`, `vendor/` — install on the server with Composer.
- `storage/logs/*.log`, `storage/framework/*` — let the app recreate them.
- `.git/` — only needed if you use Git on the server.

---

## Part B — cPanel (first-time setup)

Do this once per site. If the domain and DB already exist, skip to Part C.

### B1. Domain and document root

1. **cPanel → Domains → Domains** → select `system.khbevents.com` → **Manage**.
2. Set **Document Root** to:
   ```
   /home/khbevents/system.khbevents.com/public
   ```
3. Save.

### B2. PHP version

1. **cPanel → MultiPHP Manager**.
2. Select `system.khbevents.com`.
3. Choose **ea-php81** (or 8.2).  
   (The project’s `public/.htaccess` also forces PHP 8.1 via `.htaccess`.)

### B3. Database

1. **cPanel → MySQL® Databases**.
2. Create a database (e.g. `khbevents_aebooths`).
3. Create a user and a strong password.
4. Add the user to the database with **All Privileges**.
5. Note: **DB_DATABASE**, **DB_USERNAME**, **DB_PASSWORD** — you’ll put these in `.env` in Part D.

### B4. SSL (recommended)

- **cPanel → SSL/TLS** or **Let’s Encrypt**.
- Install or auto-renew a certificate for `system.khbevents.com` so the site can use `https://`.

---

## Part C — Get the code onto the server

Use **one** of the two ways below.

### Option 1 — Git (recommended if you already use it)

**Prereq:** Git is available in cPanel Terminal (or SSH), and the repo is already cloned, or you can clone it.

**First time (clone):**

```bash
cd /home/khbevents
git clone https://github.com/chamnabmeyinfo/khbeventsboothsystemkmall.git system.khbevents.com
cd system.khbevents.com
```

**Later (update):**

```bash
cd /home/khbevents/system.khbevents.com
git pull origin main
```

Then go to **Part D**.

---

### Option 2 — FTP or cPanel File Manager (no Git on server)

1. **Zip the project on your PC** (from the project root, e.g. `boothsystemv1`):
   - Include: `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `artisan`, `composer.json`, `composer.lock`, `env.laravel.example`.
   - Exclude: `vendor/`, `node_modules/`, `.env`, `.git/`, `storage/logs/*.log`.

2. **Upload:**
   - **FTP:** Upload the zip to `/home/khbevents/` (or the folder that will contain the site), then in cPanel File Manager go there and **Extract**.
   - **File Manager:** Go to `/home/khbevents/`, upload the zip, then **Extract**.

3. **Folder name:** After extract, the app must sit at `/home/khbevents/system.khbevents.com/`.  
   If you extracted to something like `boothsystemv1`, rename it to `system.khbevents.com` or move its contents into that folder.

4. Then go to **Part D**.

---

## Part D — On the server (Terminal or SSH)

All commands below are from the **project root**:

```bash
cd /home/khbevents/system.khbevents.com
```

Use the PHP your host provides for CLI (often `php` or `/usr/local/bin/ea-php81`). If `php` doesn’t run 8.1, use:

```bash
/usr/local/bin/ea-php81
```

Replace `php` in the examples with that path if needed (e.g. `/usr/local/bin/ea-php81 artisan ...`).

### D1. Create or keep `.env`

**First deploy:**

```bash
cp env.laravel.example .env
# or: cp env.system.khbevents.example .env
```

**If you already have a working `.env` on the server,** do not overwrite it.

Edit `.env` (cPanel **File Manager → Edit** or `nano .env` in Terminal). Set at least:

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
DB_DATABASE=khbevents_aebooths
DB_USERNAME=khbevents_admaebooths
DB_PASSWORD="your_password_here"

SESSION_DRIVER=file
SESSION_LIFETIME=120
LOG_CHANNEL=stack
LOG_LEVEL=error
```

- Use the DB name, user, and password from **B3**.
- Put `DB_PASSWORD` in double quotes if it contains `$`, `%`, or `#`.

### D2. Application key

```bash
php artisan key:generate --force
```

(This fills `APP_KEY` in `.env`.)

### D3. Composer (no dev dependencies)

```bash
composer install --no-dev --optimize-autoloader
```

If `composer` is not in PATH:

```bash
/usr/local/bin/ea-php81 /usr/local/bin/composer install --no-dev --optimize-autoloader
```

Adjust paths to match your host (cPanel often documents “PHP and Composer” paths).

### D4. Clear old caches, then rebuild

```bash
rm -f bootstrap/cache/services.php bootstrap/cache/packages.php bootstrap/cache/config.php
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### D5. Permissions

```bash
chmod -R 775 storage bootstrap/cache
```

On cPanel, the PHP process often runs as your user. If you see permission errors on logs/cache:

```bash
chown -R khbevents:khbevents storage bootstrap/cache
```

Use the username your host uses for this account.

### D6. Migrations (only when needed)

- **New or empty database:** run migrations:
  ```bash
  php artisan migrate --force
  ```
- **Existing DB that was restored or already in use:** skip unless you intentionally added new migrations. If in doubt, run:
  ```bash
  php artisan migrate:status
  ```
  then run `migrate --force` only if there are pending migrations.

### D7. Storage link (if the app uses `public/storage`)

```bash
php artisan storage:link
```

---

## Part E — Verify

1. Open **https://system.khbevents.com** in a browser.
2. Check:
   - Page loads (no 500, no blank).
   - Login and main flows work.
   - CSS/JS and images load (no broken layout, no mixed-content warnings).

If you get a 500 or blank page, use **SERVER-DIAGNOSTIC-500.md** (enable `APP_DEBUG=true` temporarily, check `storage/logs/laravel.log`, document root, and PHP version).

---

## Quick checklist (paste and tick)

```
Part A — Local
[ ] Code committed (or zipped without .env, vendor, .git)
[ ] Pushed to Git (if using Git deploy)

Part B — cPanel (first time)
[ ] Document root = /home/khbevents/system.khbevents.com/public
[ ] PHP = ea-php81 (MultiPHP Manager)
[ ] MySQL database khbevents_aebooths + user khbevents_admaebooths, credentials in .env
[ ] SSL for system.khbevents.com (Let's Encrypt / AutoSSL)

Part C — Code on server
[ ] Git: cloned or pulled in /home/khbevents/system.khbevents.com
  OR
[ ] FTP/File Manager: zip extracted to that path, no .env overwritten

Part D — Server terminal
[ ] .env created/edited (APP_ENV=production, APP_DEBUG=false, APP_URL, DB_*)
[ ] php artisan key:generate --force
[ ] composer install --no-dev --optimize-autoloader
[ ] Bootstrap cache files removed, then config/cache/route/view cache rebuilt
[ ] chmod -R 775 storage bootstrap/cache
[ ] php artisan migrate --force (only if DB is new or has pending migrations)
[ ] php artisan storage:link (if app uses public/storage)

Part E — Verify
[ ] https://system.khbevents.com loads and login works
```

---

## Later deploys (code-only updates)

1. **Local:** commit and push (or zip changed files).
2. **Server:**
   - **Git:** `cd /home/khbevents/system.khbevents.com && git pull origin main`
   - **FTP:** upload only changed files; do **not** overwrite `.env`.
3. **Server:** run the same commands as in **D3–D5** (composer, clear caches, rebuild, permissions). If you added migrations, run **D6** as well.

---

## Checklist (system.khbevents.com)

```
Part A — Local
[ ] Code committed and pushed (or zipped)

Part B — cPanel
[ ] Subdomain system.khbevents.com exists
[ ] Document root = /home/khbevents/system.khbevents.com/public
[ ] PHP = ea-php81 (MultiPHP Manager)
[ ] Database: khbevents_aebooths / khbevents_admaebooths
[ ] SSL for system.khbevents.com (Let's Encrypt / AutoSSL)

Part C — Code on server
[ ] Code in /home/khbevents/system.khbevents.com (clone or extract)

Part D — Terminal (from /home/khbevents/system.khbevents.com)
[ ] .env with APP_URL=https://system.khbevents.com, DB_* (khbevents_aebooths, khbevents_admaebooths)
[ ] php artisan key:generate --force
[ ] composer install --no-dev --optimize-autoloader
[ ] rm -f bootstrap/cache/*.php && artisan config:cache route:cache view:cache
[ ] chmod -R 775 storage bootstrap/cache
[ ] php artisan migrate --force (if DB new)
[ ] php artisan storage:link

Part E — Verify
[ ] https://system.khbevents.com loads and login works
```

---

Detailed “every deploy” and rollback steps: **GO-LIVE-PROCEDURE.md**.  
Fresh pull / 500 recovery: **SERVER-SETUP-AFTER-FRESH-PULL.md** and **SERVER-DIAGNOSTIC-500.md** (path `/home/khbevents/system.khbevents.com`, URL `https://system.khbevents.com`).
