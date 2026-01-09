# 🔧 Fix Missing vendor/ Directory

## Problem Identified

Your diagnostic shows:
- ✓ Directory structure OK
- ✓ .htaccess exists
- ✓ index.php exists
- ✗ **vendor/ directory is MISSING**

**This is why you're getting 404 errors!** Laravel can't load without the `vendor/` directory.

## Solution: Install Dependencies

### Step 1: Check Current Status

Run these commands on cPanel Terminal:

```bash
cd ~/booths.khbevents.com

# Check if .env exists
test -f .env && echo "✓ .env exists" || echo "✗ .env MISSING"

# Check if composer.json exists
test -f composer.json && echo "✓ composer.json exists" || echo "✗ composer.json MISSING"

# Check vendor directory
test -d vendor && echo "✓ vendor/ exists" || echo "✗ vendor/ MISSING"
```

### Step 2: Install Dependencies

**Option A: Using cPanel Terminal (Recommended)**

```bash
cd ~/booths.khbevents.com

# Find PHP 8.1+ path
which php
# OR
/opt/cpanel/ea-php81/root/usr/bin/php --version

# Install dependencies (use full PHP path if needed)
composer install --no-dev --optimize-autoloader

# OR if composer command not found, use full path:
/opt/cpanel/ea-php81/root/usr/bin/php /usr/local/bin/composer install --no-dev --optimize-autoloader
```

**Option B: If Composer Not Available**

1. **Download Composer:**
   ```bash
   cd ~
   curl -sS https://getcomposer.org/installer | php
   ```

2. **Install dependencies:**
   ```bash
   cd ~/booths.khbevents.com
   php ~/composer.phar install --no-dev --optimize-autoloader
   ```

### Step 3: Verify Installation

After running `composer install`, check:

```bash
cd ~/booths.khbevents.com
ls -la vendor/ | head -10
```

**You should see:**
- `vendor/autoload.php`
- `vendor/composer/`
- `vendor/laravel/`
- etc.

### Step 4: Check Document Root (Also Important!)

While installing dependencies, also check your **Document Root** in cPanel:

1. Go to **Subdomains** in cPanel
2. Find `booths.khbevents.com`
3. **Document Root should be:**
   ```
   /home/khbevents/booths.khbevents.com/public
   ```
   **NOT:**
   ```
   /home/khbevents/booths.khbevents.com
   ```

### Step 5: Set Permissions

After installing dependencies:

```bash
cd ~/booths.khbevents.com
chmod -R 755 storage bootstrap/cache
chmod -R 755 vendor
```

### Step 6: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Step 7: Test Again

1. **Clear browser cache** (Ctrl+F5)
2. **Visit:** `https://booths.khbevents.com`
3. **Should work now!**

## Troubleshooting Composer Installation

### Issue: "composer: command not found"

**Solution:**
```bash
# Use full path to composer
/usr/local/bin/composer install --no-dev --optimize-autoloader

# OR use PHP directly
php /usr/local/bin/composer install --no-dev --optimize-autoloader
```

### Issue: "PHP version too low"

**Solution:**
```bash
# Use PHP 8.1+ explicitly
/opt/cpanel/ea-php81/root/usr/bin/php /usr/local/bin/composer install --no-dev --optimize-autoloader
```

### Issue: "Memory limit exhausted"

**Solution:**
```bash
# Increase memory limit
php -d memory_limit=512M /usr/local/bin/composer install --no-dev --optimize-autoloader
```

### Issue: "Permission denied"

**Solution:**
```bash
# Check ownership
ls -la ~/booths.khbevents.com | grep vendor

# Fix ownership if needed
chown -R khbevents:khbevents ~/booths.khbevents.com/vendor
```

## Complete Installation Command

**Run this complete command:**

```bash
cd ~/booths.khbevents.com && \
/opt/cpanel/ea-php81/root/usr/bin/php /usr/local/bin/composer install --no-dev --optimize-autoloader && \
chmod -R 755 storage bootstrap/cache vendor && \
php artisan config:clear && \
php artisan cache:clear && \
echo "✓ Installation complete!"
```

## After Installation

1. **Verify vendor/ exists:**
   ```bash
   ls -la vendor/ | head -5
   ```

2. **Test the site:**
   - Visit: `https://booths.khbevents.com`
   - Should load now!

---

**The missing vendor/ directory is the main issue. Install dependencies and it should work!** 🚀
