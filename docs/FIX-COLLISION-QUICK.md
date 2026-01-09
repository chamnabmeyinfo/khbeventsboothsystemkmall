# ⚡ Quick Fix: Collision Service Provider Error

## Problem

After `composer install`, you get:
```
Class "NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider" not found
```

## ✅ Quick Fix

**Run this on cPanel Terminal:**

```bash
cd ~/floorplan.khbevents.com

# Remove cached service provider files (this fixes it!)
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/services.php

# Now clear cache - should work now!
/opt/cpanel/ea-php83/root/usr/bin/php artisan config:clear
/opt/cpanel/ea-php83/root/usr/bin/php artisan cache:clear
```

## Complete Command (Run All at Once)

```bash
cd ~/floorplan.khbevents.com && \
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php && \
/opt/cpanel/ea-php83/root/usr/bin/php artisan config:clear && \
/opt/cpanel/ea-php83/root/usr/bin/php artisan cache:clear && \
/opt/cpanel/ea-php83/root/usr/bin/php artisan route:clear && \
/opt/cpanel/ea-php83/root/usr/bin/php artisan view:clear && \
echo "✓ Fixed! Now test: https://floorplan.khbevents.com"
```

## What This Does

- Removes cached service provider files that reference Collision (dev dependency)
- Clears all Laravel caches
- Laravel will rebuild cache without dev dependencies

---

**After this, everything should work!** ✅
