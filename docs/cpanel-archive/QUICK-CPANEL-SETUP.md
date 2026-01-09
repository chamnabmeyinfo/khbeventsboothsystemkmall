# Quick cPanel Database Setup Guide

## 🚀 Quick Setup Steps

### 1. Create Database in cPanel
- Go to **MySQL Databases** in cPanel
- Create database: `boothsystem_db` → Full name: `username_boothsystem_db`
- Create user: `boothsystem_user` → Full name: `username_boothsystem_user`
- Assign user to database with **ALL PRIVILEGES**

### 2. Configure .env File

Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

Edit `.env` and update these lines:
```env
DB_HOST=localhost
DB_DATABASE=username_boothsystem_db
DB_USERNAME=username_boothsystem_user
DB_PASSWORD=your_password_here
```

**Important:** Replace `username_` with your actual cPanel username prefix!

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Run Migrations
```bash
php artisan migrate
```

### 5. Test Connection
```bash
php artisan db:show
```

## ✅ Common Issues

| Issue | Solution |
|-------|----------|
| Access denied | Check username includes cPanel prefix |
| Unknown database | Check database name includes cPanel prefix |
| Connection refused | Try `127.0.0.1` instead of `localhost` |

## 📝 Example Configuration

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=john_boothsystem_db
DB_USERNAME=john_boothsystem_user
DB_PASSWORD=MySecurePass123!
```

---

For detailed instructions, see [CPANEL-DATABASE-CONFIG.md](./CPANEL-DATABASE-CONFIG.md)
