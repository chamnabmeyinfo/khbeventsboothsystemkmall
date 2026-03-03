# Why https://system.khbevents.com/ Doesn't Load – Diagnostics

Run these on the server (SSH or cPanel Terminal) and use the results below.

---

## 1. Run this block and save the output

```bash
cd /home/khbevents/system.khbevents.com

echo "========== 1. PHP version (cli) =========="
/usr/local/bin/ea-php81 -v

echo "========== 2. .env exists? =========="
test -f .env && echo "YES" || echo "NO - CREATE .env"

echo "========== 3. APP_KEY set? =========="
grep "^APP_KEY=" .env 2>/dev/null | head -1

echo "========== 4. vendor/ exists? =========="
test -d vendor && echo "YES" || echo "NO - RUN: /usr/local/bin/ea-php81 /usr/local/bin/composer install --no-dev"

echo "========== 5. Document root (see cPanel Domains) =========="
echo "Must be: /home/khbevents/system.khbevents.com/public"
echo "Current dir for comparison: $(pwd)/public"

echo "========== 6. Last Laravel error (from log) =========="
if [ -f storage/logs/laravel.log ]; then
  tail -80 storage/logs/laravel.log
else
  echo "No laravel.log found"
fi

echo "========== 7. Maintenance mode? =========="
test -f storage/framework/maintenance.php && echo "YES - REMOVE: rm storage/framework/maintenance.php" || echo "No"

echo "========== 8. storage/bootstrap writable? =========="
ls -la storage/framework 2>/dev/null || echo "storage/framework missing or not readable"
ls -la bootstrap/cache 2>/dev/null || echo "bootstrap/cache missing or not readable"
```

---

## 2. Turn on Laravel error display

In `.env` set:

```
APP_DEBUG=true
```

Then run:

```bash
/usr/local/bin/ea-php81 artisan config:clear
```

Reload https://system.khbevents.com/ in the browser.  
**Copy the full error message** you see (and the first lines of the stack trace). That tells us the exact cause.

---

## 3. Typical causes and fixes

| Symptom                    | Cause                                      | Fix                                                                        |
| -------------------------- | ------------------------------------------ | -------------------------------------------------------------------------- |
| White/blank page           | PHP error before output, or wrong doc root | Set APP_DEBUG=true, ensure doc root = `.../public`                         |
| HTTP 500                   | PHP fatal, DB connection, or missing class | See error in browser when APP_DEBUG=true, or in `storage/logs/laravel.log` |
| "Unable to handle request" | Laravel crash (e.g. env, cache, DB)        | Check log (section 1.6 above), fix DB_PASSWORD quoting, clear cache        |
| Endless loading / timeout  | PHP 7.3 for web, or stuck DB/script        | Set domain to **ea-php81** in cPanel → MultiPHP Manager                    |
| Document root wrong        | Domain points to project root not `public` | cPanel → Domains → document root = `.../system.khbevents.com/public`       |

---

## 4. Quick reset (often fixes 500 after deploy)

```bash
cd /home/khbevents/system.khbevents.com

# Remove bad caches
rm -f bootstrap/cache/services.php bootstrap/cache/packages.php bootstrap/cache/config.php

# Ensure DB password is quoted in .env: DB_PASSWORD="yourpass"
# Then:
/usr/local/bin/ea-php81 artisan config:clear
/usr/local/bin/ea-php81 artisan cache:clear
/usr/local/bin/ea-php81 artisan view:clear
/usr/local/bin/ea-php81 artisan config:cache

chmod -R 775 storage bootstrap/cache
```

---

## 5. One-line PHP test (is the web server using PHP 8.1?)

Create a file in `public/`:

```bash
echo '<?php echo "PHP " . PHP_VERSION;' > /home/khbevents/system.khbevents.com/public/phpinfo_test.php
```

Open in browser: **https://system.khbevents.com/phpinfo_test.php**

- If you see `PHP 8.1.x` or `8.2.x` → web PHP is OK.
- If you see `PHP 7.3.x` or an error → in cPanel set the domain to **ea-php81**.

**Delete the test file when done:**

```bash
rm /home/khbevents/system.khbevents.com/public/phpinfo_test.php
```

---

## 6. “Works on localhost but not online” checklist

If the app works locally but fails or behaves wrongly online, check these in order.

| Check             | What to do                                                                                                                                                                                                                    |
| ----------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **APP_URL**       | In production `.env` set `APP_URL=https://system.khbevents.com` (no trailing slash). Wrong or missing APP_URL makes `url()` and `asset()` point to localhost. After changing: `php artisan config:clear` then `config:cache`. |
| **HTTPS**         | The app forces HTTPS in production when APP_URL starts with `https://`. Ensure APP_URL uses `https://` and the site is reached via HTTPS.                                                                                     |
| **Document root** | Must be the `public` folder (e.g. `.../system.khbevents.com/public`). If it points to the project root, you get 500 or “Unable to handle request”.                                                                            |
| **PHP version**   | Project `public/.htaccess` forces **PHP 8.1** via `AddHandler application/x-httpd-ea-php81 .php`. If your host uses different handler names, see section 7.                                                                   |
| **Cache**         | After any .env or config change, run the “Quick reset” in section 4 (config:clear, cache:clear, view:clear, config:cache).                                                                                                    |
| **Logs**          | Check `storage/logs/laravel.log` on the server; the last entries usually explain 500s or “works locally but not online” (e.g. DB connection, wrong path, missing env).                                                        |

**Verify APP_URL on the server:**

```bash
cd /home/khbevents/system.khbevents.com
grep "^APP_URL=" .env
# Must show: APP_URL=https://system.khbevents.com
```

---

## 7. PHP version in `public/.htaccess`

The app sets PHP 8.1 in `public/.htaccess` for cPanel (EasyApache 4):

```apache
<IfModule mime_module>
    AddHandler application/x-httpd-ea-php81 .php
</IfModule>
```

- **PHP 8.2:** change `ea-php81` to `ea-php82`.
- **Different host:** some use `application/x-httpd-php81` or LSAPI names. Check your host’s “PHP version in .htaccess” docs or ask support for the exact `AddHandler` line.
- **XAMPP/local:** this block is ignored if the handler isn’t defined, so local dev is unaffected.
