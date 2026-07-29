# Running the app from the Git repository (Option B)

Move `system.khbevents.com` from serving code out of the docroot to serving it
from the cPanel Git checkout, with the docroot pointed at `public/`.

**Today**

```
docroot = /home/khbevents/system.khbevents.com          <- Laravel root, hand-edited
          .env, vendor/, storage/, composer.json all inside the web root,
          hidden only by a RewriteRule in .htaccess
repo    = /home/khbevents/repositories/khbeventsystem   <- unused
```

**After**

```
docroot = /home/khbevents/repositories/khbeventsystem/public
app     = /home/khbevents/repositories/khbeventsystem   <- above the docroot
```

Deployment becomes **Update from Remote** in cPanel → Git Version Control.
`.env`, `vendor/`, `storage/` and `.git` end up above the docroot and are no
longer reachable over HTTP even if `.htaccess` breaks.

Nothing in the old docroot is modified until Phase 6. Rollback is one setting.

---

## Conventions

Set these once per shell session (cPanel → Terminal, or SSH):

```bash
REPO=/home/khbevents/repositories/khbeventsystem
LIVE=/home/khbevents/system.khbevents.com
PHP=/opt/cpanel/ea-php81/root/usr/bin/php
```

PHP 8.1 is deliberate: `public/.htaccess` sets `AddHandler
application/x-httpd-ea-php81`, and `composer.json` pins
`config.platform.php` to `8.1.0`. Confirm the binary exists:

```bash
$PHP -v
```

If that path is wrong, find it with `ls /opt/cpanel/ | grep ea-php`.

---

## Phase 0 — Pre-flight

### 0.1 Missing config files (blocker)

The `config/` directory is missing core Laravel files. Confirm on the server:

```bash
ls $LIVE/config/
```

Expected present: `app.php`, `auth.php`, `database.php`, `logging.php`,
`session.php`, `view.php`.

Known missing: **`filesystems.php`**, **`mail.php`**, `queue.php`, `cache.php`,
`services.php`.

This matters because:

- `Storage::disk('public')` is used in 10 places → needs `filesystems.php`
- `Mail::` is used in 9 places (the May 10 email automation) → needs `mail.php`
- **`php artisan storage:link` reads `config('filesystems.links')` and will
  fail without `filesystems.php`** — that is Phase 1.6 below

Laravel 10 does not merge framework defaults for missing config files. Restore
them from a matching Laravel 10 skeleton before continuing, and populate
`mail.php` from the `MAIL_*` keys already documented in
`env.system.khbevents.example`.

### 0.2 Take a rollback snapshot

```bash
cd /home/khbevents
tar -czf backup-live-$(date +%F-%H%M).tar.gz --exclude='*/vendor' --exclude='*/node_modules' system.khbevents.com
```

Also take a database dump from cPanel → phpMyAdmin → Export, or:

```bash
mysqldump -u khbevents_admaebooths -p khbevents_aebooths > ~/db-$(date +%F-%H%M).sql
```

### 0.3 Confirm the repo is current

cPanel → Git Version Control → **Update from Remote**. Then:

```bash
cd $REPO && git log --oneline -3 && git status --short
```

Should show `6725e31` on top and a clean tree.

---

## Phase 1 — Prepare the repository directory

Everything here happens in `$REPO`. The live site is untouched and stays up.

### 1.1 Dependencies

```bash
cd $REPO && $PHP /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader
```

If that composer path doesn't exist, try `which composer`, or copy
`$LIVE/vendor` across as a fallback:

```bash
cp -a $LIVE/vendor $REPO/vendor
```

### 1.2 Environment file

```bash
cp $LIVE/.env $REPO/.env
chmod 600 $REPO/.env
```

`.env` is gitignored, so `Update from Remote` will never overwrite it.

Add the `MAIL_*` and `QUEUE_CONNECTION` keys from
`env.system.khbevents.example` — the live `.env` predates the email work and
doesn't have them.

Confirm these are right for the new location:

```bash
grep -E '^(APP_ENV|APP_DEBUG|APP_URL)=' $REPO/.env
```

Expect `APP_ENV=production`, `APP_DEBUG=false`,
`APP_URL=https://system.khbevents.com`.

### 1.3 Storage tree

```bash
cp -a $LIVE/storage/app $REPO/storage/
mkdir -p $REPO/storage/framework/{sessions,views,cache} $REPO/storage/logs
```

`storage/app` holds `exports/` and `public/` — real data, copy it.
`framework/*` and `logs` are regenerable.

### 1.4 User uploads — the one that bites

`public/images/` is ~39 MB of live customer data and is **gitignored**, so
`Update from Remote` will never deliver it. Copy it explicitly:

```bash
cp -a $LIVE/public/images $REPO/public/
du -sh $REPO/public/images
```

Expect roughly:

| Directory | Size |
|---|---|
| `floor-plans` | 30 MB |
| `landing-pages` | 3.5 MB |
| `map.jpg` | 3.1 MB |
| `covers` | 912 KB |
| `avatars` | 796 KB |
| `booths` | 624 KB |
| `company` | 172 KB |

If this step is skipped, floor plans and client avatars disappear from the
live site after cutover.

### 1.5 Built frontend assets

`public/build/` is not tracked (0 files in git) — this is the cause of the
`Vite manifest not found` errors in `storage/logs/laravel.log`.

Simplest fix, since only two views use `@vite`:

```bash
cp -a $LIVE/public/build $REPO/public/ 2>/dev/null || echo "not present on live either"
```

If it isn't on live either, build locally (`npm ci && npm run build`) and
commit `public/build/` to the repo — 181 KB, three files. Then it arrives
with every future pull and this problem is permanently gone.

### 1.6 Storage symlink

```bash
cd $REPO && $PHP artisan storage:link
ls -la $REPO/public/storage
```

Must show a symlink to `../storage/app/public`.

**If this errors with `foreach() argument must be of type array|object, null
given`, `config/filesystems.php` is still missing — go back to Phase 0.1.**

### 1.7 Permissions

```bash
chmod -R 775 $REPO/storage $REPO/bootstrap/cache
chmod 600 $REPO/.env
find $REPO/public -type d -exec chmod 755 {} \;
```

---

## Phase 2 — Application state

```bash
cd $REPO
$PHP artisan migrate --force
$PHP artisan config:clear && $PHP artisan cache:clear && $PHP artisan view:clear
```

`migrate --force` matters: the log shows
`Table 'khbevents_aebooths.landing_page_*' not found`, so migrations are
behind on live.

**Do not run `config:cache` yet.** `DatabasePull`, `DatabasePush` and
`StoragePull` call `env()` directly (49 sites); once config is cached those
return `null` and the commands break. Leave config uncached until that's
fixed, or accept that those three console commands stop working.

---

## Phase 3 — Verify before switching

Still no change to the live site.

```bash
cd $REPO
$PHP artisan about
$PHP artisan route:list | head
```

Both should run without error. `artisan about` confirms the env, DB
connection and cache state.

Optional but recommended: create a throwaway subdomain
(e.g. `staging.khbevents.com`) pointed at `$REPO/public`, load it, log in,
open a floor plan. That exercises the real web path with zero risk to
production.

---

## Phase 4 — Switch the document root

cPanel → **Domains** → `system.khbevents.com` → **Manage** → Document Root:

```
/home/khbevents/repositories/khbeventsystem/public
```

Save. Takes effect immediately.

Confirm the PHP version for the domain (cPanel → MultiPHP Manager) is **8.1**,
matching the handler in `public/.htaccess`.

---

## Phase 5 — Verify live

Check in this order — each exercises a different subsystem:

1. `https://system.khbevents.com/` → redirects to `/login`
2. Log in → dashboard renders
3. Open a floor plan → **booth images and floor-plan images load** (proves 1.4)
4. Open a client profile → avatar loads, React dashboard renders (proves 1.5)
5. Create a test booking → confirms DB writes and migrations
6. Upload an image → confirms `storage/` permissions
7. Confirm secrets are unreachable:

```bash
curl -sI https://system.khbevents.com/.env        # expect 404
curl -sI https://system.khbevents.com/composer.json # expect 404
curl -sI https://system.khbevents.com/.git/config   # expect 404
```

All three must be 404 — that's the security win. Under the old layout they
were served whenever the rewrite failed.

Then watch for new errors:

```bash
tail -f $REPO/storage/logs/laravel.log
```

---

## Rollback

If anything is wrong, revert the document root in cPanel → Domains back to:

```
/home/khbevents/system.khbevents.com
```

The old tree was never modified, so this is instant and complete. Investigate
in `$REPO` at your leisure and re-attempt.

---

## After cutover

**New deploy flow:**

1. Commit and push to `main` on `github.com/chamnabmeyinfo/khbeventsystem`
2. cPanel → Git Version Control → **Update from Remote**
3. If dependencies changed: `cd $REPO && composer install --no-dev`
4. If migrations were added: `$PHP artisan migrate --force`

No `.cpanel.yml` needed — the checkout *is* the app.

**Never edit files directly in `$REPO`.** Any local modification makes
`Update from Remote` refuse to pull. If it happens:

```bash
cd $REPO && git status --short && git checkout -- <file>
```

**Files that live only on the server** and must survive — never commit them,
never delete them:

- `.env`
- `public/images/` (user uploads)
- `storage/app/` (exports, generated files)
- `vendor/`

**Old docroot cleanup:** once you've run on the new layout for a week or two
without issues, `/home/khbevents/system.khbevents.com` can be archived and
removed. Keep the tarball from Phase 0.2.

---

## Outstanding, not addressed here

- `public/seed-cpanel-settings.php` — removed in `6725e31`, so it disappears
  from the new docroot on cutover. **It is still live in the old docroot until
  then.** Delete it manually now: `rm $LIVE/public/seed-cpanel-settings.php`
- The database password in that file is in public git history and needs
  rotating regardless
- `config/mail.php` / `config/filesystems.php` (Phase 0.1)
- `auth()->id()` returns a username, not an integer — breaks activity logs and
  notifications
- `BookingService::createTimelineEntry()` writes non-existent columns
