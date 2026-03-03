# Deployment Fix for CollisionServiceProvider Error

## Problem

When deploying to production, you may encounter:

```
Class "NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider" not found
```

This happens because `nunomaduro/collision` is a dev-only dependency, but cached service provider files reference it.

## Solution

### Option 1: Clear Bootstrap Cache on Server (Recommended)

After deploying to production, run these commands on your server:

```bash
# Navigate to your project directory
cd /path/to/your/project

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Remove bootstrap cache files
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php

# Regenerate cache (this will create new cache files without dev dependencies)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Option 2: Regenerate Cache Files After Composer Install

Add this to your deployment script:

```bash
# Install dependencies (without dev packages)
composer install --no-dev --optimize-autoloader

# Clear and regenerate caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
rm -f bootstrap/cache/services.php bootstrap/cache/packages.php
php artisan config:cache
php artisan route:cache
```

### Option 3: Manual Fix (Quick Fix)

If you need an immediate fix, delete these files on the server:

- `bootstrap/cache/services.php`
- `bootstrap/cache/packages.php`

Then run:

```bash

```

## Prevention

The `bootstrap/cache/` directory is now in `.gitignore`, so these files will be regenerated on each server after deployment. This prevents the issue from happening again.

## Notes

- The bootstrap cache files are auto-generated and should not be committed to git
- They will be regenerated automatically when you run `php artisan config:cache` or `composer install`
- Always run `composer install --no-dev` on production servers

