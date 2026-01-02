# KHB Events - K Mall Xmas Booth Booking System

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
   git clone https://github.com/yourusername/kmallxmas-laravel.git
   cd kmallxmas-laravel
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
   
   Edit `.env` file and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=khbevents_kmallxmas
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

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
kmallxmas-laravel/
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

## 📞 Support

For support, email support@khbevents.com or create an issue in the repository.

---

**Built with ❤️ using Laravel**
