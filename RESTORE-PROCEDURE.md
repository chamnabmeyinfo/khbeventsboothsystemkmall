# Backup & Restore Procedure (JetBackup / cPanel)

**Application:** KHB-BMS  
**Production URL:** https://system.khbevents.com  
**Backup solution:** JetBackup (via cPanel)

This document describes how backups are used and how to restore when needed. Keep it next to [GO-LIVE-PROCEDURE.md](GO-LIVE-PROCEDURE.md).

---

## 1. What gets backed up (confirm with your host)

Typical JetBackup on cPanel includes:

- **Files:** Home directory (includes the Laravel project, `public/`, `storage/`, `.env`, etc.)
- **Database:** MySQL/MariaDB databases tied to the account

**Action:** In cPanel, open JetBackup (or the backup tool your host provides) and confirm:

- Backup frequency (e.g. daily).
- Retention (e.g. last 30 days, or 30 daily + 12 monthly).
- Whether backups are stored off-server (e.g. in the host’s backup datacentre).

Note the answers in your runbook or in the table below.

| Item         | Your setup           |
| ------------ | -------------------- |
| Frequency    | _e.g. Daily_         |
| Retention    | _e.g. 30 days daily_ |
| Off-site?    | _Yes / No_           |
| Includes DB? | _Yes / No_           |

### 1.1 Backup confirmation checklist (mark when done)

Use this to satisfy the Month 1 checklist items for backup.

| #   | Action                                                                         | Done |
| --- | ------------------------------------------------------------------------------ | ---- |
| 1   | Confirmed automated daily backups (DB + files, compressed) in cPanel/JetBackup | ☐    |
| 2   | Set or confirmed retention (e.g. 30 days daily, 12 months monthly)             | ☐    |
| 3   | Confirmed off-site or alternate location with host (or noted “Not available”)  | ☐    |
| 4   | Ran one test restore (Option A or B in Section 3) and verified login/data      | ☐    |
| 5   | Set a calendar reminder for “monthly test restore”                             | ☐    |

---

## 2. Restore procedure (full or partial)

### 2.1 Restore entire account (files + database)

Use when the server or the whole site is broken.

1. Log in to **cPanel**.
2. Open **JetBackup** (or **Backup**, **Restore**, or similar).
3. Choose **Restore** and pick a **restore point** (date/time).
4. Select **Full Account** (or equivalent) and start the restore.
5. Wait for the job to finish (host-dependent; can take minutes to hours).
6. Run Laravel post-restore steps (see **Section 2.3**).
7. Test https://system.khbevents.com (login, key flows).

### 2.2 Restore only files (e.g. after bad deploy)

Use when code/files are wrong but the database is fine.

1. In **JetBackup** (or backup/restore), choose **File Restore** (or **Home Directory**).
2. Pick the restore point (e.g. just before the bad deploy).
3. Restore the **project directory** (path where the Laravel app lives, e.g. `public_html` or subdomain docroot).
4. **Do not** overwrite `.env` with an old copy if it contains different DB credentials or URLs. Prefer restoring everything else and keeping the current `.env`, or restore `.env` only if you know it’s correct for this environment.
5. Run **Section 2.3** (clear caches, check permissions).
6. Test the site.

### 2.3 Restore only the database

Use when the DB was corrupted or a bad migration ran.

1. In **JetBackup**, choose **Database Restore** (or **MySQL Restore**).
2. Pick the database used by the app and a restore point.
3. Run the restore. The tool may create a new DB or overwrite the existing one; follow the host’s UI.
4. If the DB name or user changed, update `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and run:

   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

5. Test the site (login, bookings, etc.).

### 2.4 Post-restore (Laravel)

After any restore, from the **project root** on the server run:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
```

Ensure permissions are correct:

```bash
chmod -R 775 storage bootstrap/cache
```

Then verify https://system.khbevents.com.

---

## 3. Monthly backup verification (test restore)

**Goal:** Once a month, prove that backups are usable.

1. **Option A — Restore to a subdomain or test URL:**  
   Restore files + DB to a test subdomain (e.g. `test.system.khbevents.com` or a separate test account). Point that subdomain’s document root at the restored app, and use a copy of `.env` with the restored DB. Open the test URL and run through login and one or two key flows.

2. **Option B — Restore DB only to a temporary DB:**  
   Restore the DB to a new database name (e.g. `khb_restore_test`). Point a temporary `.env` or a test site at this DB and load the app; confirm you can log in and see data.

3. **Record:** Note the date and result (e.g. “Restored to test subdomain; login and booking list OK”) in the **Monthly test restore log** below. If something fails, fix backup/retention/restore steps and try again.

**Monthly test restore log**

| Month           | Date         | Result (e.g. Restored to test DB; login OK) |
| --------------- | ------------ | ------------------------------------------- |
| _e.g. Feb 2026_ | _YYYY-MM-DD_ | _Pass / Fail + notes_                       |
|                 |              |                                             |

---

## 4. Retention and off-site

- **Retention:** Align with your policy (e.g. 30 days daily, 12 months monthly). Configure in JetBackup or ask your host.
- **Off-site:** If JetBackup (or the host) copies backups to another location, document that. If not, consider an extra copy (e.g. DB dump + critical files to cloud storage) if you need disaster recovery beyond the server.

---

## 5. Quick reference

| Need to…                    | See                                                                                                |
| --------------------------- | -------------------------------------------------------------------------------------------------- |
| Roll back a bad code deploy | [GO-LIVE-PROCEDURE.md](GO-LIVE-PROCEDURE.md) § Rollback; or Section 2.2 here (restore files only). |
| Restore the whole site      | Section 2.1.                                                                                       |
| Restore only the DB         | Section 2.3.                                                                                       |
| Prove backups work          | Section 3 (monthly test restore).                                                                  |

---

_Last updated: January 2026_
