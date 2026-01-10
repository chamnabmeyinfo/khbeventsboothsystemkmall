# 🔧 Database Migration Setup Guide

## ⚠️ Issue Identified

You're getting SQLSTATE errors because the database tables for the new features haven't been created yet. The migrations exist but need to be run.

## 📋 Required Tables

The following tables need to be created:

1. ✅ **notifications** - For notification system
2. ✅ **payments** - For payment tracking
3. ✅ **messages** - For communication system
4. ✅ **activity_logs** - For audit trail
5. ✅ **email_templates** - For email template system
6. ✅ **roles** - For RBAC system
7. ✅ **permissions** - For RBAC system
8. ✅ **role_permissions** - For RBAC pivot table
9. ✅ **user.role_id** - Column added to user table

## 🚀 Solution: Run Migrations

### Step 1: Check Database Connection

Make sure your `.env` file has correct database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 2: Run All Migrations

Open your terminal/command prompt and run:

```bash
cd C:\xampp\htdocs\KHB\khbevents\boothsystemv1
php artisan migrate
```

This will create all the missing tables.

### Step 3: (Optional) Seed Default Data

After migrations, you can seed default roles and permissions:

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

## 📝 Migration Files That Will Run

The following migration files will create the tables:

1. `2026_01_09_230442_create_notifications_table.php` → `notifications` table
2. `2026_01_09_230443_create_payments_table.php` → `payments` table
3. `2026_01_09_230444_create_messages_table.php` → `messages` table
4. `2026_01_09_233158_create_roles_table.php` → `roles` table
5. `2026_01_09_233159_create_permissions_table.php` → `permissions` table
6. `2026_01_09_233200_create_role_permissions_table.php` → `role_permissions` table
7. `2026_01_09_233201_add_role_id_to_users_table.php` → Adds `role_id` to `user` table
8. `2026_01_09_234459_create_activity_logs_table.php` → `activity_logs` table
9. `2026_01_09_234504_create_email_templates_table.php` → `email_templates` table

## 🔍 Verify Tables Were Created

After running migrations, you can verify by checking your database or running:

```sql
SHOW TABLES;
```

You should see:
- notifications
- payments
- messages
- roles
- permissions
- role_permissions
- activity_logs
- email_templates

And the `user` table should have a `role_id` column.

## ⚠️ Troubleshooting

### If you get "Table already exists" errors:

Some tables might already exist. You can:

1. **Check which migrations have run:**
   ```bash
   php artisan migrate:status
   ```

2. **Run specific migration if needed:**
   ```bash
   php artisan migrate --path=database/migrations/2026_01_09_230442_create_notifications_table.php
   ```

### If you get foreign key errors:

Make sure the base tables exist:
- `user` table
- `client` table
- `book` table

### If migrations fail:

1. Check database connection in `.env`
2. Make sure database user has CREATE TABLE permissions
3. Check if tables already exist manually

## ✅ After Migrations Complete

Once migrations are successful, all these pages should work:

- ✅ `/notifications` - Notification system
- ✅ `/payments` - Payment tracking
- ✅ `/communications` - Messaging system
- ✅ `/activity-logs` - Audit trail
- ✅ `/email-templates` - Email templates
- ✅ `/users` - User management (with roles)
- ✅ `/roles` - Role management
- ✅ `/permissions` - Permission management

## 🎯 Quick Fix Command

Run this single command to fix all issues:

```bash
cd C:\xampp\htdocs\KHB\khbevents\boothsystemv1 && php artisan migrate --force
```

The `--force` flag will run migrations in production without confirmation.

---

**Note:** Make sure to backup your database before running migrations if you have important data!
