# KHB Events - K Mall Booth Booking System

A modern Laravel-based booth booking and management system for KHB Events.

## 🚀 Features

- **Booth Management**: Complete CRUD operations for booth management
- **Booking System**: Reserve, confirm, and track booth bookings
- **Client Management**: Store and manage client/vendor information
- **User Authentication**: Secure login with role-based access control
- **Admin Dashboard**: Statistics and overview of all bookings
- **Category Management**: Hierarchical category system
- **Status Tracking**: Track booth status (Available, Reserved, Confirmed, Paid)

## 📋 Requirements

- PHP >= 8.1
- Composer
- MySQL 5.7+ or MariaDB
- XAMPP (for local development)

## 🔧 Local Development Setup

### Step 1: Install Dependencies

```bash
composer install
```

### Step 2: Configure Environment

Copy `.env.example` to `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

### Step 3: Configure Database

Edit `.env` file and set your **local** database credentials:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=khbeventskmallxmas
DB_USERNAME=root
DB_PASSWORD=
```

**Important:** 
- Update `DB_DATABASE` to match your local database name
- For XAMPP, `DB_PASSWORD` is usually empty

### Step 4: Run Migrations

```bash
php artisan migrate
```

### Step 5: Start Development Server

```bash
php artisan serve
```

Visit: **http://localhost:8000**

## 🔐 Default Login Credentials

- **Username**: `vutha_admin` (or check your database)
- **Password**: (check your database)

⚠️ **Important**: Change passwords after first login!

## 📁 Project Structure

```
boothsystemv1/
├── app/
│   ├── Http/Controllers/    # Application controllers
│   ├── Models/              # Eloquent models
│   └── Http/Middleware/     # Custom middleware
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   └── views/               # Blade templates
├── routes/
│   └── web.php              # Web routes
└── config/                  # Configuration files
```

## 🛠️ Technology Stack

- **Framework**: Laravel 10
- **Frontend**: Bootstrap 5, jQuery
- **Database**: MySQL/MariaDB
- **PHP**: 8.1+
- **Icons**: Font Awesome 6

## 📝 Features Overview

### Booth Management
- View all booths with filtering options
- Create, edit, and delete booths
- Status management (Available, Reserved, Confirmed, Paid, Hidden)
- Visual status indicators

### Booking System
- Create bookings with multiple booths
- Reserve booths for clients
- Confirm reservations
- Mark booths as paid
- Clear/cancel reservations

### User Management
- Admin and regular user roles
- User status (active/inactive)
- Last login tracking

### Client Management
- Store client/vendor information
- Link clients to booths and bookings

## 🔒 Security

- Password hashing with bcrypt
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- Role-based access control
- Session-based authentication

## 🐛 Troubleshooting

### Issue: "Not Found" error
- Make sure you're using `php artisan serve` (not XAMPP Apache on port 8000)
- Check that `vendor/` directory exists
- Run `php artisan config:clear`

### Issue: Database connection error
- Verify MySQL is running in XAMPP
- Check database name in `.env` matches your local database
- Run `php artisan config:clear` after changing `.env`

### Issue: Login doesn't work
- Verify database has user records
- Check `.env` has correct database credentials
- Clear cache: `php artisan config:clear && php artisan cache:clear`

## 📄 License

Proprietary - KHB Events

## 👥 Contributing

This is a private project for KHB Events. For issues or questions, please contact the development team.

---

**Built with ❤️ using Laravel**
