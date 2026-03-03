# Monitoring & Production Readiness

**Application:** KHB-BMS  
**Production URL:** https://system.khbevents.com

This document covers uptime monitoring, error logging, and production environment checks for Month 1. Use it with [GO-LIVE-PROCEDURE.md](GO-LIVE-PROCEDURE.md) and [RESTORE-PROCEDURE.md](RESTORE-PROCEDURE.md).

---

## 1. Uptime monitoring and alerting

**Goal:** Know when https://system.khbevents.com is down and get notified.

**Options:**

| Option                 | What it does                                       | Setup                                                                                           |
| ---------------------- | -------------------------------------------------- | ----------------------------------------------------------------------------------------------- |
| **UptimeRobot** (free) | Hits your URL every 5 min; email/SMS when it fails | Sign up at uptimerobot.com, add monitor for `https://system.khbevents.com`, set alert contacts. |
| **Pingdom**            | Similar; paid plans offer more intervals and SLA   | Add HTTPS check, set notifications.                                                             |
| **cPanel / host**      | Some hosts offer “uptime” or “status” in the panel | Check your host’s docs; enable and add your email.                                              |

**Recommendation:** Add at least one external monitor (e.g. UptimeRobot) so you get email/SMS if the site goes down, even when the server is still up.

**Alert contacts:** Use an email (and optionally SMS) that is checked regularly (e.g. chamnab@antelite.digital or a shared ops address).

**Done when (checklist):** Monitor added for `https://system.khbevents.com`, alert contact set, one test alert received. ☐

---

## 2. Error logging and log location

**Where Laravel writes logs:**

- **Path:** `storage/logs/laravel.log` (or `laravel-YYYY-MM-DD.log` if you use the `daily` channel).
- **View on server:** SSH or cPanel File Manager → project root → `storage/logs/`. Tail with `tail -f storage/logs/laravel.log` if you have SSH.

**Production settings** (in `.env`):

- `APP_DEBUG=false` — never `true` in production.
- `APP_ENV=production`
- `LOG_CHANNEL=daily` (recommended) so logs rotate by day and don’t fill the disk.
- `LOG_LEVEL=error` or `warning` — avoid `debug` in production.

**Checking for errors:** Open the latest file in `storage/logs/` and search for `ERROR`, `CRITICAL`, or stack traces. Use this when users report issues or after deploying.

**Done when (checklist):** Production uses `LOG_CHANNEL=daily` and `LOG_LEVEL=error` or `warning`; you know where logs are and treat `ERROR`/`CRITICAL` as critical. ☐

---

## 3. Production .env checklist

Before go-live and after any env change, confirm:

| Variable                | Expected (production)                              |
| ----------------------- | -------------------------------------------------- |
| `APP_ENV`               | `production`                                       |
| `APP_DEBUG`             | `false`                                            |
| `APP_URL`               | `https://system.khbevents.com`                     |
| `LOG_CHANNEL`           | `daily` (or `stack` if you prefer)                 |
| `LOG_LEVEL`             | `error` or `warning`                               |
| `DB_*`                  | Real production DB credentials                     |
| `SESSION_SECURE_COOKIE` | `true` (so session cookie is only sent over HTTPS) |

Optional for later:

- `CACHE_DRIVER`, `SESSION_DRIVER` — `redis` if available; otherwise `file` is fine.
- `QUEUE_CONNECTION` — `database` or `redis` if you use queues.

**Done when (checklist):** All rows in the table above are set correctly on the production server. ☐

---

## 4. Server health (CPU, memory, disk)

- **cPanel:** Use **Metrics** or **Resource Usage** to see CPU, memory, and disk. Set a calendar reminder to check monthly (or use host alerts if they offer them).
- **Disk:** Ensure enough free space (e.g. &gt;500 MB). Logs and backups can grow; rotation (e.g. `daily` logs) and backup retention help.
- **Alerts:** If your host can alert on high CPU, low disk, or high memory, enable them and point them to the same contact as uptime alerts.

**Done when (checklist):** You know where to see CPU/memory/disk (cPanel Metrics or host dashboard); calendar reminder set for monthly check (or host alerts enabled). ☐

---

## 5. Quick reference

| Need to…                  | Action                                                                                           |
| ------------------------- | ------------------------------------------------------------------------------------------------ |
| Know if the site is down  | Use UptimeRobot (or similar) and ensure alerts are on.                                           |
| Find why something failed | Check `storage/logs/laravel.log` (or latest `laravel-*.log`).                                    |
| Harden production         | Set `APP_DEBUG=false`, `APP_ENV=production`, `SESSION_SECURE_COOKIE=true`, and sensible `LOG_*`. |
| Verify server resources   | cPanel Metrics / Resource Usage; consider host-side alerts.                                      |

---

_Last updated: January 2026_
