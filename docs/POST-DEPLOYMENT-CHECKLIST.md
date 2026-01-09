# ✅ Post-Deployment Checklist

## 🔍 Step 1: Verify Site is Accessible

1. **Visit your site:**
   ```
   https://booths.khbevents.com
   ```

2. **Check for errors:**
   - ✅ Site loads without 500 error
   - ✅ No blank white page
   - ✅ No database connection errors

## 🔐 Step 2: Test Login

1. **Go to login page:**
   ```
   https://booths.khbevents.com/login
   ```

2. **Test with your credentials:**
   - Enter username and password
   - Verify login works
   - Check if you're redirected to dashboard

## 🗄️ Step 3: Verify Database Connection

If you see database errors, check:

1. **Verify .env file has correct credentials:**
   ```bash
   cd ~/booths.khbevents.com
   cat .env | grep DB_
   ```

2. **Test database connection:**
   ```bash
   php artisan tinker
   # Then type: DB::connection()->getDatabaseName();
   # Should show: khbevents_aebooths
   ```

## 🔧 Step 4: Check File Permissions

If you see permission errors:

```bash
cd ~/booths.khbevents.com
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## 🧹 Step 5: Clear All Caches (if needed)

If you see old data or errors:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

## 📋 Step 6: Test Key Features

1. **Dashboard:**
   - ✅ Can access dashboard after login
   - ✅ Statistics display correctly

2. **Booths:**
   - ✅ Can view booths list
   - ✅ Can create/edit booths (if admin)

3. **Bookings:**
   - ✅ Can create bookings
   - ✅ Can view bookings

## 🐛 Troubleshooting Common Issues

### Issue: 500 Internal Server Error

**Check Laravel logs:**
```bash
cd ~/booths.khbevents.com
tail -n 50 storage/logs/laravel.log
```

**Common fixes:**
- Check `.env` file exists and has correct values
- Verify `APP_KEY` is set
- Check file permissions
- Ensure `vendor/` directory exists

### Issue: Database Connection Failed

**Check:**
1. Database credentials in `.env`
2. Database user has proper permissions
3. Database exists in phpMyAdmin

### Issue: Page Not Found (404)

**Fix:**
```bash
php artisan route:clear
php artisan config:clear
```

### Issue: Assets Not Loading (CSS/JS)

**Check:**
- Verify `public/` directory is accessible
- Check `.htaccess` in `public/` directory exists
- Clear browser cache

## ✅ Success Indicators

Your deployment is successful if:
- ✅ Site loads at `https://booths.khbevents.com`
- ✅ Login page works
- ✅ Can log in successfully
- ✅ Dashboard displays correctly
- ✅ No errors in browser console
- ✅ Database queries work

## 🎉 Next Steps After Verification

Once everything works:
1. **Test all features** thoroughly
2. **Monitor logs** for any issues:
   ```bash
   tail -f storage/logs/laravel.log
   ```
3. **Set up backups** (database and files)
4. **Document any custom configurations**

---

**If everything works, your deployment is complete!** 🚀
