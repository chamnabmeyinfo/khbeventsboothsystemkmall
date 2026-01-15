@echo off
REM Critical fix deployment - Windows Local Test
REM Run this to test locally before production

echo ================================================
echo CRITICAL FIX - LOCAL TESTING
echo ================================================
echo.

echo [1/3] Checking git status...
git status
echo.

echo [2/3] Running Laravel cache clear...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo.

echo [3/3] Testing database connection...
php artisan migrate:status
echo.

echo ================================================
echo LOCAL TEST ENVIRONMENT READY
echo ================================================
echo.
echo NEXT STEPS:
echo 1. Go to: http://localhost/KHB/khbevents/boothsystemv1/booths?view=canvas
echo 2. Create test booths (A01, A02, A03)
echo 3. Book booth A02
echo 4. Try to delete Zone A
echo 5. Verify A02 is SKIPPED with warning
echo.
echo ================================================

pause
