# Your cPanel Database Configuration Guide

## ✅ Your Database Credentials

I've configured your `.env` file with the following database credentials:

```
Database Name: khbevents_aebooths
Database User: khbevents_admaebooths
Database Host: localhost
Database Port: 3306
```

## 📋 Step-by-Step Setup in cPanel

### Step 1: Verify Database Exists

1. Log into your **cPanel** account
2. Navigate to **MySQL Databases** (under "Databases" section)
3. Verify that the database `khbevents_aebooths` exists
4. If it doesn't exist, create it:
   - Enter database name: `aebooths` (cPanel will add the prefix automatically)
   - Click **Create Database**

### Step 2: Verify Database User Exists

1. In the **MySQL Databases** section, scroll down to **MySQL Users**
2. Verify that the user `khbevents_admaebooths` exists
3. If it doesn't exist, create it:
   - Enter username: `admaebooths` (cPanel will add the prefix automatically)
   - Enter password: `ASDasd12345$$$%%%`
   - Click **Create User**

### Step 3: Assign User to Database

1. Scroll down to **Add User To Database**
2. Select user: `khbevents_admaebooths`
3. Select database: `khbevents_aebooths`
4. Click **Add**
5. **IMPORTANT:** Check **ALL PRIVILEGES** checkbox
6. Click **Make Changes**

### Step 4: Verify .env File Configuration

Your `.env` file should now contain:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=khbevents_aebooths
DB_USERNAME=khbevents_admaebooths
DB_PASSWORD=ASDasd12345$$$%%%
```

### Step 5: Generate Application Key

If you haven't already, generate your Laravel application key:

```bash
php artisan key:generate
```

This will automatically update the `APP_KEY` in your `.env` file.

### Step 6: Test Database Connection

**Option A: Using Laravel Artisan (Recommended)**
```bash
php artisan db:show
```

**Option B: Using Test Script**
1. Upload `test-db-connection.php` to your server
2. Update the credentials in the file (they're already set)
3. Access via browser: `https://yourdomain.com/test-db-connection.php`
4. **Delete the file after testing** for security

### Step 7: Run Database Migrations

Once the connection is verified, run the migrations:

```bash
php artisan migrate
```

This will create all necessary database tables.

### Step 8: Seed Database (Optional)

If you have seeders, run:

```bash
php artisan db:seed
```

## 🔍 Verification Checklist

After setup, verify:

- [ ] Database `khbevents_aebooths` exists in cPanel
- [ ] User `khbevents_admaebooths` exists in cPanel
- [ ] User is assigned to database with **ALL PRIVILEGES**
- [ ] `.env` file contains correct database credentials
- [ ] `APP_KEY` is generated (`php artisan key:generate`)
- [ ] Database connection test successful
- [ ] Migrations run successfully (`php artisan migrate`)

## 🛠️ Troubleshooting

### Issue: "Access denied for user 'khbevents_admaebooths'"

**Solutions:**
1. Verify the user exists in cPanel MySQL Databases
2. Check that the password is exactly: `ASDasd12345$$$%%%`
3. Ensure the user is assigned to the database
4. Verify the user has ALL PRIVILEGES

### Issue: "Unknown database 'khbevents_aebooths'"

**Solutions:**
1. Verify the database exists in cPanel
2. Check the exact database name (including prefix)
3. Ensure the database name matches exactly: `khbevents_aebooths`

### Issue: "Connection refused" or "Can't connect to MySQL server"

**Solutions:**
1. Try changing `DB_HOST` from `localhost` to `127.0.0.1` in `.env`
2. Verify MySQL is running on your server
3. Check with your hosting provider for the correct host value

### Issue: Password contains special characters

Your password `ASDasd12345$$$%%%` contains special characters. Make sure:
- The password in `.env` is exactly as shown (no quotes needed)
- If you have issues, try wrapping it in quotes: `DB_PASSWORD="ASDasd12345$$$%%%"`

## 📝 Important Notes

1. **Password Security:** Your password contains special characters (`$`, `%`). In `.env` files, these are usually handled correctly, but if you encounter issues, you can wrap the password in quotes.

2. **Host Configuration:** Most cPanel servers use `localhost`. If that doesn't work, try `127.0.0.1`.

3. **Database Prefix:** Your database and username already include the cPanel prefix (`khbevents_`), so use them exactly as provided.

4. **Security:** Never commit your `.env` file to version control (it's already in `.gitignore`).

## 🚀 Next Steps

After successful database configuration:

1. **Update APP_URL** in `.env` with your actual domain:
   ```env
   APP_URL=https://yourdomain.com
   ```

2. **Set APP_DEBUG** to `false` for production:
   ```env
   APP_DEBUG=false
   ```

3. **Test your application** by accessing it in a browser

4. **Check Laravel logs** if you encounter any issues:
   - Location: `storage/logs/laravel.log`

## 📞 Need Help?

If you encounter issues:
1. Check the troubleshooting section above
2. Verify all credentials match exactly in cPanel
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test connection using `test-db-connection.php`

---

**Your database is configured!** Follow the steps above to complete the setup. 🎉
