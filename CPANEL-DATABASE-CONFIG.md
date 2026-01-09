# cPanel Database Configuration Guide

This guide will help you configure your Laravel application's database connection for cPanel hosting.

## 📋 Prerequisites

Before configuring the database, ensure you have:
1. Access to your cPanel account
2. Created a MySQL database in cPanel
3. Created a MySQL user for the database
4. Assigned the user to the database with ALL PRIVILEGES

## 🔧 Step-by-Step Configuration

### Step 1: Create Database in cPanel

1. Log in to your cPanel account
2. Navigate to **MySQL Databases** (under "Databases" section)
3. Create a new database:
   - Enter a database name (e.g., `boothsystem_db`)
   - Click **Create Database**
   - Note the full database name (usually prefixed with your cPanel username, e.g., `username_boothsystem_db`)

### Step 2: Create Database User

1. In the same MySQL Databases section, scroll down to **MySQL Users**
2. Create a new user:
   - Enter a username (e.g., `boothsystem_user`)
   - Enter a strong password (use the password generator)
   - Click **Create User**
   - Note the full username (usually prefixed, e.g., `username_boothsystem_user`)

### Step 3: Assign User to Database

1. Scroll down to **Add User To Database**
2. Select the user you created
3. Select the database you created
4. Click **Add**
5. Check **ALL PRIVILEGES** and click **Make Changes**

### Step 4: Configure .env File

1. Upload your Laravel project to cPanel (usually in `public_html` or a subdirectory)
2. Create or edit the `.env` file in your project root
3. Add the following database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_username_boothsystem_db
DB_USERNAME=your_cpanel_username_boothsystem_user
DB_PASSWORD=your_database_password
```

**Important Notes:**
- `DB_HOST` should be `localhost` (not `127.0.0.1`) for most cPanel servers
- Use the **full database name** and **full username** (with cPanel prefix)
- The password is the one you set when creating the MySQL user
- Never commit the `.env` file to version control (it's already in `.gitignore`)

### Step 5: Test Database Connection

After configuring the `.env` file, test the connection by running:

```bash
php artisan migrate:status
```

Or if you have SSH access:

```bash
php artisan db:show
```

## 🔍 Common cPanel Database Settings

### Typical cPanel Database Configuration:

| Setting | Value | Notes |
|---------|-------|-------|
| **Host** | `localhost` | Most cPanel servers use localhost |
| **Port** | `3306` | Standard MySQL port |
| **Database Name** | `cpaneluser_dbname` | Prefixed with cPanel username |
| **Username** | `cpaneluser_dbuser` | Prefixed with cPanel username |
| **Password** | Your chosen password | Set when creating user |

### Alternative Host Values (if localhost doesn't work):

Some cPanel hosts use different host values:
- `127.0.0.1` (same as localhost)
- `mysql.yourdomain.com` (some hosts)
- `localhost:3306` (with port in host)

Try `localhost` first, as it's the most common.

## 🛠️ Troubleshooting

### Issue: "Access denied for user"

**Solutions:**
1. Verify the username includes the cPanel prefix
2. Check that the password is correct
3. Ensure the user is assigned to the database
4. Verify the user has ALL PRIVILEGES

### Issue: "Unknown database"

**Solutions:**
1. Verify the database name includes the cPanel prefix
2. Check that the database exists in cPanel
3. Ensure the user has access to the database

### Issue: "Connection refused" or "Can't connect to MySQL server"

**Solutions:**
1. Try changing `DB_HOST` from `localhost` to `127.0.0.1`
2. Verify MySQL is running on the server
3. Check if your hosting provider uses a different host
4. Contact your hosting provider for the correct host value

### Issue: "SQLSTATE[HY000] [2002] Connection timed out"

**Solutions:**
1. Check if your hosting provider allows remote MySQL connections
2. Verify the port number (usually 3306)
3. Some hosts require using `localhost` with a socket path

## 📝 Example .env Configuration

Here's a complete example of database configuration in `.env`:

```env
APP_NAME="KHB Booths Booking System"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_boothsystem_db
DB_USERNAME=username_boothsystem_user
DB_PASSWORD=your_secure_password_here

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_DRIVER=file

LOG_CHANNEL=stack
LOG_LEVEL=error
```

## 🔐 Security Best Practices

1. **Never commit `.env` file** - It's already in `.gitignore`
2. **Use strong passwords** - Generate secure passwords for database users
3. **Limit privileges** - Only grant necessary privileges (though ALL PRIVILEGES is needed for Laravel migrations)
4. **Regular backups** - Set up automated database backups in cPanel
5. **Keep Laravel updated** - Regularly update Laravel and dependencies

## 📞 Additional Resources

- [Laravel Database Configuration](https://laravel.com/docs/database)
- [cPanel MySQL Databases Documentation](https://docs.cpanel.net/cpanel/databases/mysql-databases/)
- [Laravel Environment Configuration](https://laravel.com/docs/configuration#environment-configuration)

## ✅ Verification Checklist

After configuration, verify:

- [ ] Database created in cPanel
- [ ] Database user created in cPanel
- [ ] User assigned to database with ALL PRIVILEGES
- [ ] `.env` file created with correct credentials
- [ ] `APP_KEY` generated (`php artisan key:generate`)
- [ ] Database connection tested successfully
- [ ] Migrations run successfully (`php artisan migrate`)

---

**Need Help?** If you encounter issues, check the Laravel logs in `storage/logs/laravel.log` for detailed error messages.
