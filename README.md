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
- Node.js & NPM (optional)

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/kmall-laravel.git
   cd kmall-laravel
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install  # Optional
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   **For Local Development:**
   Edit `.env` file and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=khbevents_kmall
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```
   
   **For cPanel/Production:**
   See [CPANEL-DATABASE-CONFIG.md](./CPANEL-DATABASE-CONFIG.md) for detailed instructions.
   
   Quick setup:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_cpanel_username_boothsystem_db
   DB_USERNAME=your_cpanel_username_boothsystem_user
   DB_PASSWORD=your_database_password
   ```
   
   **Note:** In cPanel, database names and usernames are prefixed with your cPanel username.

4. **Run migrations and seed database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start development server**
   ```bash
   php artisan serve
   ```

   Visit: http://localhost:8000

## 🔐 Default Login Credentials

- **Username**: `admin`
- **Password**: `password`

⚠️ **Important**: Change the admin password immediately after first login!

## 📁 Project Structure

```
kmall-laravel/
├── app/
│   ├── Http/Controllers/    # Application controllers
│   ├── Models/                # Eloquent models
│   └── Http/Middleware/       # Custom middleware
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   └── views/                 # Blade templates
├── routes/
│   └── web.php                # Web routes
└── config/                    # Configuration files
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

## 📄 License

Proprietary - KHB Events

## 👥 Contributing

This is a private project for KHB Events. For issues or questions, please contact the development team.

## 🌐 cPanel Deployment

For deploying to cPanel hosting, see the following guides:

- **[CPANEL-DATABASE-CONFIG.md](./CPANEL-DATABASE-CONFIG.md)** - Complete database configuration guide
- **[QUICK-CPANEL-SETUP.md](./QUICK-CPANEL-SETUP.md)** - Quick setup steps

### Quick cPanel Database Setup

1. Create database and user in cPanel MySQL Databases section
2. Copy `.env.example` to `.env` and update database credentials
3. Use `localhost` as `DB_HOST` (not `127.0.0.1`)
4. Include cPanel username prefix in database name and username
5. Test connection using `test-db-connection.php` (delete after testing)
6. Run `php artisan migrate` to set up database tables

## 📞 Support

For support, email support@khbevents.com or create an issue in the repository.

---

**Built with ❤️ using Laravel**
