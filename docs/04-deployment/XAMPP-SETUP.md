# XAMPP setup: run project at localhost:80/project_directories

This project is a Laravel app. To run it at **http://localhost/khbeventsboothsystemkmall** (port 80, in a subdirectory), use XAMPP and the steps below.

---

## 0. Fix XAMPP config if Apache/ProFTPD fail to start

If you see **"ServerRoot must be a valid directory"**, **"DocumentRoot ... does not exist"**, **"SSLCertificateFile ... does not exist"** (httpd-ssl.conf), or **"relative path not allowed"** (proftpd.conf), the installer left placeholders `@@BITNAMI_XAMPP_ROOT@@` in several config files. Replace them everywhere under XAMPP’s `etc`:

```bash
# Fix all config files under /opt/lampp/etc/ (httpd.conf, httpd-ssl.conf, httpd-xampp.conf, proftpd.conf, php.ini, my.cnf, etc.)
sudo find /opt/lampp/etc -type f \( -name '*.conf' -o -name '*.ini' -o -name '*.cnf' \) -exec sed -i 's|@@BITNAMI_XAMPP_ROOT@@|/opt/lampp|g' {} \;
```

Then start XAMPP again:

```bash
sudo /opt/lampp/lampp start
```

(If another web server was using port 80, stop it first, e.g. `sudo systemctl stop apache2` and `sudo systemctl disable apache2`.)

---

## 1. Install XAMPP on Linux

### Option A: Official installer (recommended)

1. Download XAMPP for Linux from: https://www.apachefriends.org/download.html  
   Choose the PHP version that matches your project (e.g. PHP 8.1 or 8.2).

2. Make the installer executable and run it:
   ```bash
   chmod +x xampp-linux-x64-*-installer.run
   sudo ./xampp-linux-x64-*-installer.run
   ```
   Default install path: `/opt/lampp`.

3. Start Apache and MySQL:
   ```bash
   sudo /opt/lampp/lampp start
   ```
   Or use the manager:
   ```bash
   sudo /opt/lampp/manager-linux-x64.run
   ```

### Option B: Package (if available for your distro)

- **Ubuntu/Debian**: XAMPP is not in the default repos; use Option A or install Apache + PHP + MySQL separately.
- To only use Apache + PHP + MariaDB from the system instead of XAMPP, you can configure the system Apache the same way (see Section 2), using your distro's Apache config directory (e.g. `/etc/apache2/`).

---

## 2. Configure Apache so the site runs at localhost/khbeventsboothsystemkmall

Laravel's web root is the `public` folder. Apache must serve that folder for the URL path `/khbeventsboothsystemkmall`.

### 2.1 Include the project config in XAMPP

**Option A – Append to existing config**

Append the contents of the project's Apache config to XAMPP's vhosts file:

```bash
sudo cat /home/chamnabmey/khbeventsboothsystemkmall/docs/xampp/khbeventsboothsystemkmall.conf | sudo tee -a /opt/lampp/etc/extra/httpd-vhosts.conf
```

**Option B – Include as a separate file**

If your XAMPP `httpd.conf` has an line like `Include etc/extra/httpd-vhosts.conf`, you can create a directory and include our file:

```bash
sudo mkdir -p /opt/lampp/etc/extra/httpd-vhosts.conf.d
sudo cp /home/chamnabmey/khbeventsboothsystemkmall/docs/xampp/khbeventsboothsystemkmall.conf /opt/lampp/etc/extra/httpd-vhosts.conf.d/
```

Then in `/opt/lampp/etc/httpd.conf`, add (e.g. after the existing `Include etc/extra/httpd-vhosts.conf`):

```apache
Include etc/extra/httpd-vhosts.conf.d/*.conf
```

### 2.2 Restart Apache

```bash
sudo /opt/lampp/lampp restartapache
```

---

## 3. Laravel: .env and APP_URL

1. Create `.env` from the example (if you don't have one):
   ```bash
   cd /home/chamnabmey/khbeventsboothsystemkmall
   cp .env.example .env
   php artisan key:generate
   ```

2. Set the app URL to the subdirectory (no trailing slash):
   ```env
   APP_URL=http://localhost/khbeventsboothsystemkmall
   ```

3. Configure database (XAMPP's MySQL is usually `root` with no password). This project uses the database **khbeventskmallxmas** (create it in phpMyAdmin if needed; see below):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=khbeventskmallxmas
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Create the database in phpMyAdmin** (if it does not exist):
   - Open **http://localhost/phpmyadmin** (with XAMPP running).
   - Click **New** (or “Databases”) and create a database named **khbeventskmallxmas** (collation: `utf8mb4_unicode_ci`).
   - The project `.env` is already set to use this database name.

5. Install dependencies and run migrations if needed:
   ```bash
   composer install
   php artisan migrate
   ```

---

## 3.5 Pull database from cPanel phpMyAdmin

To use the same data as your live site (cPanel), export the database from cPanel phpMyAdmin, then import it into XAMPP’s MySQL.

### Step 1: Export from cPanel phpMyAdmin

1. Log in to **cPanel** (your hosting).
2. Open **phpMyAdmin** (under “Databases” or “MySQL Databases”).
3. In the left sidebar, click the **database name** you use for this project.
4. Go to the **Export** tab.
5. **Export method:** “Quick” is fine for a full copy; use “Custom” if you need only some tables or options.
6. **Format:** leave **SQL**.
7. Click **Go** / **Export**. Your browser will download a `.sql` file (e.g. `your_database.sql`).

### Step 2: Create the database locally (if it doesn’t exist)

1. Open local phpMyAdmin: **http://localhost/phpmyadmin** (with XAMPP running).
2. Click **New** (or “Databases”) and create a database with the **same name** as in your `.env` (this project uses **khbeventskmallxmas**).  
   Or create any name and set `DB_DATABASE` in `.env` to match.

### Step 3: Import into XAMPP

**Option A – Using phpMyAdmin (easiest)**

1. In **http://localhost/phpmyadmin**, select the database you created (left sidebar).
2. Open the **Import** tab.
3. Click **Choose File** and select the `.sql` file you downloaded from cPanel.
4. Leave defaults (e.g. format: SQL) and click **Go**.  
   Wait until you see a success message.

**Option B – Using command line**

If the `.sql` file is large or phpMyAdmin times out, use MySQL from the terminal:

```bash
# Replace DB_NAME with your database name (e.g. khbevents) and path/to/export.sql with your file path
/opt/lampp/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS DB_NAME;"
/opt/lampp/bin/mysql -u root DB_NAME < /path/to/export.sql
```

Example:

```bash
/opt/lampp/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS khbeventskmallxmas;"
/opt/lampp/bin/mysql -u root khbeventskmallxmas < ~/Downloads/khbevents.sql
```

### Step 4: Match .env to the local database

Ensure `.env` uses the database you imported into (this project uses **khbeventskmallxmas**):

```env
DB_DATABASE=khbeventskmallxmas
DB_USERNAME=root
DB_PASSWORD=
```

Then clear config cache:

```bash
cd /home/chamnabmey/khbeventsboothsystemkmall
php artisan config:clear
```

After this, the app at **http://localhost/khbeventsboothsystemkmall** will use the data you pulled from cPanel.

---

## 3.6 Direct push/pull (remote ↔ local)

You can **pull** (remote → local) and **push** (local → remote) the database from the command line so you don’t have to use phpMyAdmin each time.

### Option A: Direct MySQL (cPanel “Remote MySQL”)

1. In **cPanel**, open **Remote MySQL** (under “Databases”).
2. Add your **current IP** (or use `%` for any IP; less secure) so the server allows external MySQL connections.
3. In your project `.env`, set the remote DB (use the same DB name/user/pass as in cPanel phpMyAdmin):

```env
REMOTE_DB_HOST=your-domain.com
REMOTE_DB_PORT=3306
REMOTE_DB_DATABASE=your_cpanel_db_name
REMOTE_DB_USERNAME=your_cpanel_db_user
REMOTE_DB_PASSWORD=your_cpanel_db_password
```

4. Optional: if `mysql`/`mysqldump` are not in your PATH (e.g. XAMPP only):

```env
DB_MYSQL_PATH=/opt/lampp/bin
```

5. From the project root:

```bash
php artisan db:pull          # remote → local (with confirmation)
php artisan db:pull --force  # same, no confirmation
php artisan db:push          # local → remote (with confirmation)
php artisan db:push --force  # same, no confirmation
```

### Option B: SSH (no need to open MySQL to the internet)

If you have **SSH** access to the server (e.g. cPanel SSH or SFTP account), you can push/pull without enabling Remote MySQL:

1. In `.env`, set SSH and the same remote DB credentials (used on the server for `mysqldump`/`mysql`):

```env
REMOTE_SSH_HOST=your-domain.com
REMOTE_SSH_USER=your_cpanel_ssh_username
REMOTE_DB_DATABASE=your_cpanel_db_name
REMOTE_DB_USERNAME=your_cpanel_db_user
REMOTE_DB_PASSWORD=your_cpanel_db_password
REMOTE_DB_PORT=3306
```

2. Run the same commands; the tools use SSH when `REMOTE_SSH_HOST` is set:

```bash
php artisan db:pull --force
php artisan db:push --force
```

- **Pull:** runs `mysqldump` on the server over SSH and pipes the result into your local `mysql`.
- **Push:** runs `mysqldump` locally and pipes the result into `mysql` on the server over SSH.

Keep `.env` (and any secrets) out of version control.

---

## 4. Optional: .htaccess RewriteBase (if links or redirects break)

If you see wrong redirects or asset paths when using **http://localhost/khbeventsboothsystemkmall**, add `RewriteBase` in `public/.htaccess` right after `RewriteEngine On`:

```apache
RewriteEngine On
RewriteBase /khbeventsboothsystemkmall/
```

Remove or comment out `RewriteBase` when you run the app from the document root or another base path.

---

## 5. Run other projects the same way (localhost:80/project_directories)

For each project:

1. Add an Apache config that uses **Alias /other_project** pointing to that project's **public** (or document root) directory.
2. Set that project's **APP_URL** (or equivalent) to `http://localhost/other_project`.
3. Restart Apache after config changes.

---

## Summary

| Item | Value |
|------|--------|
| Project URL | http://localhost/khbeventsboothsystemkmall |
| Project path | /home/chamnabmey/khbeventsboothsystemkmall |
| Document root (Apache) | /home/chamnabmey/khbeventsboothsystemkmall/public |
| APP_URL in .env | http://localhost/khbeventsboothsystemkmall |
| Database name | khbeventskmallxmas |
| phpMyAdmin | http://localhost/phpmyadmin |

After setup, open **http://localhost/khbeventsboothsystemkmall** in your browser.
