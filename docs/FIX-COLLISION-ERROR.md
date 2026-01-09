# 🔧 Fix Collision Service Provider Error

## Problem

Error: `Class "NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider" not found`

**Cause:** Collision is a dev dependency that was cached in bootstrap cache, but it's not installed in production (because we used `--no-dev`).

## ✅ Solution: Clear Bootstrap Cache

**Run these commands on cPanel Terminal:**

```bash
cd ~/booths.khbevents.com

# Remove cached service provider files
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/services.php

# Clear all caches
/opt/cpanel/ea-php83/root/usr/bin/php artisan config:clear
/opt/cpanel/ea-php83/root/usr/bin/php artisan cache:clear
/opt/cpanel/ea-php83/root/usr/bin/php artisan route:clear
/opt/cpanel/ea-php83/root/usr/bin/php artisan view:clear
```

## Alternative: Rebuild Cache

If the above doesn't work, rebuild the cache:

```bash
cd ~/booths.khbevents.com

# Remove all cache files
rm -f bootstrap/cache/*.php

# Rebuild (this will skip dev dependencies)
/opt/cpanel/ea-php83/root/usr/bin/php artisan config:cache
/opt/cpanel/ea-php83/root/usr/bin/php artisan route:cache
```

## Complete Fix Command

**Run this all at once:**

```bash
cd ~/booths.khbevents.com && \
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php && \
/opt/cpanel/ea-php83/root/usr/bin/php artisan config:clear && \
/opt/cpanel/ea-php83/root/usr/bin/php artisan cache:clear && \
/opt/cpanel/ea-php83/root/usr/bin/php artisan route:clear && \
/opt/cpanel/ea-php83/root/usr/bin/php artisan view:clear && \
echo "✓ Cache cleared! Now test: https://booths.khbevents.com"
```

## After Fixing

1. **Test the site:** `https://booths.khbevents.com`
2. **Should work now!**

---

**The cached service providers need to be cleared to remove dev dependencies!** 🧹
