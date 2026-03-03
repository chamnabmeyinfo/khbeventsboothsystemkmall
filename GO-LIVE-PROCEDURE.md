# Go-Live & Deployment Procedure

**Application:** KHB-BMS (KHB Events Business Management System)  
**Production URL:** https://system.khbevents.com  
**Hosting:** cPanel (with JetBackup for backups)

This document is the single runbook for deploying changes and recovering from issues. See also [DEPLOYMENT-FIX.md](DEPLOYMENT-FIX.md) for the Collision/cache fix and [RESTORE-PROCEDURE.md](RESTORE-PROCEDURE.md) for backup/restore.

---

## 1. Pre-deployment checklist (run before every deploy)

Check these **before** uploading or pulling new code:

| Step | Action                                                                                                                                                          | Pass? |
| ---- | --------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----- |
| 1    | **Database:** From server or SSH, run `php artisan migrate:status` — confirm no pending migrations that could break the app, or plan to run them during deploy. | ☐     |
| 2    | **Disk space:** In cPanel → Metrics or Disk Usage, confirm enough free space (e.g. &gt;500 MB).                                                                 | ☐     |
| 3    | **Env:** Production `.env` has `APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL=https://system.khbevents.com`, and correct DB credentials.             | ☐     |
| 4    | **Backup:** Confirm a recent JetBackup (or backup) exists. See [RESTORE-PROCEDURE.md](RESTORE-PROCEDURE.md).                                                    | ☐     |

If any check fails, fix it before deploying.

---

## 2. Deployment steps (cPanel / FTP or Git)

**Option A — Upload via FTP or cPanel File Manager**

1. Upload (or overwrite) only the files that changed. Avoid overwriting:
   - `.env` (keep production values)
   - `storage/` and `bootstrap/cache/` (let the app recreate as needed; or preserve and fix permissions)
2. If you upload the whole project, **do not** overwrite `.env` with a dev copy.
3. Go to **Section 3** and run the post-deploy commands (via SSH or cPanel Terminal).

**Option B — Git on server**

1. SSH into the server, `cd` to the project root.
2. `git pull origin main` (or your branch).
3. Run the post-deploy commands in **Section 3**.

**Option C — One-off “critical fix” (no new migrations)**

Use the same post-deploy as Section 3; often only cache clear is needed. See [DEPLOYMENT-FIX.md](DEPLOYMENT-FIX.md) if you hit the Collision/cache error.

---

## 3. Post-deployment commands (run after every deploy)

Run these from the **project root** on the server (cPanel Terminal or SSH):

```bash
# Install/refresh dependencies (use --no-dev on production)
composer install --no-dev --optimize-autoloader

# Clear and regenerate caches (avoids Collision/dev dependency issues)
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
rm -f bootstrap/cache/services.php bootstrap/cache/packages.php
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If you changed the database schema:

```bash
php artisan migrate --force
```

**File permissions** (if you see “permission denied” on logs or cache):

```bash
chmod -R 775 storage bootstrap/cache
chown -R <web-server-user>:<web-server-user> storage bootstrap/cache
```

Replace `<web-server-user>` with the user that runs PHP (e.g. `nobody`, or your cPanel user). Your host’s docs describe the correct user.

---

## 4. Rollback procedure

If the new deployment causes errors or downtime:

1. **Revert code**
   - **Git:** `git log -3` to find the last good commit, then `git reset --hard <commit-hash>` and run the post-deploy commands (Section 3). If you already ran new migrations, you may need to run `php artisan migrate:rollback` first (only if safe for your data).
   - **FTP/File Manager:** Restore the previous set of files from your local backup or from a JetBackup “files only” restore for the project directory. Then run the post-deploy commands from Section 3.
2. **If the database was migrated:** Prefer to fix forward (new migration that fixes the issue) rather than rollback migrations that have run in production, unless you have a tested rollback plan.
3. **Verify:** Open https://system.khbevents.com and test login and key flows.
4. **Post-incident:** Note what went wrong and update this doc or your deploy steps so it doesn’t repeat.

For **full server/files/DB restore** from JetBackup, see [RESTORE-PROCEDURE.md](RESTORE-PROCEDURE.md).

---

## 5. SSL and DNS

- **SSL:** Handled in cPanel (e.g. AutoSSL / Let’s Encrypt). Ensure the certificate is valid and auto-renewal is on.
- **DNS:** The domain for https://system.khbevents.com should point to this server. Changes are done in the DNS provider or cPanel Zone Editor, not in the app.

---

## 6. Quick reference

| Item                     | Where                                                                                  |
| ------------------------ | -------------------------------------------------------------------------------------- |
| Production URL           | https://system.khbevents.com                                                           |
| Collision / cache issues | [DEPLOYMENT-FIX.md](DEPLOYMENT-FIX.md)                                                 |
| Backup & restore         | [RESTORE-PROCEDURE.md](RESTORE-PROCEDURE.md)                                           |
| Monitoring & alerts      | [MONITORING.md](MONITORING.md)                                                         |
| Security verification    | [SECURITY-VERIFICATION.md](SECURITY-VERIFICATION.md) (SQL/XSS audit)                   |
| Local pre-deploy test    | Run `deploy-windows-local.bat` before pushing (clears cache, checks `migrate:status`). |

---

_Last updated: January 2026_
