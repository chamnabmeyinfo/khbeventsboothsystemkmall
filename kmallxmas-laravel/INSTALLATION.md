# Installation Guide

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL 5.7+ or MariaDB
- Node.js & NPM (optional, for frontend assets)

## Step 1: Install Dependencies

```bash
composer install
npm install  # Optional
```

## Step 2: Environment Configuration

1. Copy `.env.example` to `.env`:
```bash
cp .env.example .env
```

2. Edit `.env` file and configure:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=khbevents_kmallxmas
DB_USERNAME=root
DB_PASSWORD=your_password
```

## Step 3: Generate Application Key

```bash
php artisan key:generate
```

## Step 4: Run Migrations

```bash
php artisan migrate
```

## Step 5: Seed Database

```bash
php artisan db:seed
```

This will create:
- Admin user (username: `admin`, password: `password`)
- Sample assets (10A, 20A, 30A)
- Booth types
- 138 sample booths

## Step 6: Start Development Server

```bash
php artisan serve
```

Visit: http://localhost:8000

## Default Login Credentials

- **Username**: admin
- **Password**: password

**⚠️ IMPORTANT**: Change the admin password immediately after first login!

## Troubleshooting

### Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Next Steps

1. Change admin password
2. Configure your domain/virtual host
3. Set up production environment variables
4. Configure email settings (if needed)
