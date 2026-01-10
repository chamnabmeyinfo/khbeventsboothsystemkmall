# KHB Events - K Mall Booth Booking System

A modern Laravel-based booth booking and management system for KHB Events.

## 🚀 Features

- **Multi-Project Support**: Create multiple event projects, each with independent floor plans
- **Floor Plan Management**: Visual floor plan editor with drag-and-drop booth placement
- **Multi-Floor-Plan Support**: Each project can have multiple floor plans with unique settings
- **Zone Management**: Create zones (A, B, C, etc.) with zone-specific booth settings
- **Booth Management**: Complete CRUD operations with floor-plan-specific uniqueness
- **Booking System**: Reserve, confirm, and track booth bookings per project/floor plan
- **Client Management**: Store and manage client/vendor information
- **User Authentication**: Secure login with role-based access control
- **Admin Dashboard**: Statistics and overview of all bookings and projects
- **Category Management**: Hierarchical category system
- **Status Tracking**: Track booth status (Available, Reserved, Confirmed, Paid)
- **Complete Data Storage**: All settings, positions, images stored in database per floor plan

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
│   │   ├── FloorPlanController.php  # Floor plan management
│   │   ├── BoothController.php      # Booth & zone management
│   │   ├── BookController.php       # Booking management
│   │   └── ...
│   ├── Models/              # Eloquent models
│   │   ├── FloorPlan.php    # Floor plan model
│   │   ├── Booth.php        # Booth model (floor-plan-specific)
│   │   ├── Book.php         # Booking model (project-tracking)
│   │   ├── ZoneSetting.php  # Zone settings (floor-plan-specific)
│   │   └── ...
│   └── Console/Commands/
│       └── BackfillBookingProjectData.php  # Data backfill command
├── database/
│   ├── migrations/          # Database migrations (essential)
│   └── export_real_database/
│       └── khbeventskmallxmas.sql  # Database backup reference
├── resources/
│   └── views/
│       ├── floor-plans/     # Floor plan management views
│       └── booths/          # Booth editor (visual canvas)
├── docs/                    # Documentation
│   ├── DATABASE-STRUCTURE.md
│   └── SYSTEM-WIDE-AUDIT-REPORT.md
├── routes/
│   └── web.php              # Web routes
├── COMPLETE-FLOOR-PLAN-SYSTEM-FIX-SAFE.sql  # Manual SQL fix (reference)
├── COMPANY-MANAGEMENT-ARCHITECTURE.md       # System architecture
└── PROJECT-STRUCTURE.md     # This file structure guide
```

## 🛠️ Technology Stack

- **Framework**: Laravel 10
- **Frontend**: Bootstrap 5, jQuery
- **Database**: MySQL/MariaDB
- **PHP**: 8.1+
- **Icons**: Font Awesome 6

## 📝 Features Overview

### Project & Floor Plan Management
- **Multi-Project Support**: Create unlimited event projects
- **Multiple Floor Plans**: Each project can have multiple floor plans
- **Independent Settings**: Each floor plan has its own image, canvas size, and zone settings
- **Visual Editor**: Drag-and-drop booth placement on floor plan canvas
- **Zone System**: Create zones (A, B, C, etc.) with zone-specific booth settings
- **Floor Plan Images**: Each floor plan has unique background image

### Booth Management
- View all booths with filtering by floor plan
- Create, edit, and delete booths (unique per floor plan)
- Booth numbers can repeat across different floor plans (independence)
- Status management (Available, Reserved, Confirmed, Paid, Hidden)
- Visual status indicators
- Appearance customization (colors, fonts, sizes, positions)

### Booking System
- Create bookings with multiple booths
- Bookings automatically track project and floor plan
- Reserve booths for clients
- Confirm reservations
- Mark booths as paid
- Clear/cancel reservations
- Project-specific booking reports

### User Management
- Admin and regular user roles
- User status (active/inactive)
- Last login tracking

### Client Management
- Store client/vendor information
- Link clients to booths and bookings
- Clients can book booths across multiple projects

## 🔒 Security

- Password hashing with bcrypt
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- Role-based access control
- Session-based authentication

## 🎯 Multi-Project Architecture

The system supports **multi-project, multi-floor-plan** architecture:

```
Company (KHB Events)
  └── Projects/Events (Many)
        └── Floor Plans (Multiple per project)
              └── Booths (Multiple per floor plan)
                    └── Bookings (Customers can book across projects)
```

**Key Features:**
- ✅ Each floor plan is completely independent
- ✅ Same booth numbers can exist in different floor plans
- ✅ Each floor plan has its own image, settings, and zones
- ✅ Bookings track which project and floor plan they belong to
- ✅ All data stored in database (images, positions, settings)

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

### Issue: Zone creation fails - "booth number already exists"
- Run migration: `php artisan migrate`
- Or run SQL script: `COMPLETE-FLOOR-PLAN-SYSTEM-FIX-SAFE.sql`
- This adds composite unique constraint: `(booth_number, floor_plan_id)`

### Issue: Booking project data missing
- Run backfill command: `php artisan bookings:backfill-project-data`
- This populates `event_id` and `floor_plan_id` for existing bookings

## 📄 License

Proprietary - KHB Events

## 📚 Additional Documentation

- **SQL-First Database Workflow**: See `docs/SQL-FIRST-DATABASE-WORKFLOW.md` ⭐ **NEW!**
- **System Architecture**: See `COMPANY-MANAGEMENT-ARCHITECTURE.md`
- **Database Structure**: See `docs/DATABASE-STRUCTURE.md`
- **System Audit**: See `docs/SYSTEM-WIDE-AUDIT-REPORT.md`
- **Project Structure**: See `PROJECT-STRUCTURE.md`

## 🔧 Useful Commands

### Database Management (SQL-First Workflow)

```bash
# Export current database to SQL file (sync code → database)
php artisan db:export

# Import SQL file to database (sync database → code)
php artisan db:import --backup  # Creates backup before import

# Check if SQL file and database are in sync
php artisan db:sync

# Inspect database structure and data
php artisan db:inspect

# Inspect specific table
php artisan db:inspect --table=floor_plans
```

### Booking Data Management

```bash
# Backfill booking project data
php artisan bookings:backfill-project-data

# Preview backfill changes (dry-run)
php artisan bookings:backfill-project-data --dry-run
```

### Laravel Commands

```bash
# Run migrations (traditional approach)
php artisan migrate

# Clear cache
php artisan config:clear && php artisan cache:clear
```

**💡 New SQL-First Workflow:** See `docs/SQL-FIRST-DATABASE-WORKFLOW.md` for complete guide on managing database using SQL file as source of truth.

## 👥 Contributing

This is a private project for KHB Events. For issues or questions, please contact the development team.

---

**Built with ❤️ using Laravel - Multi-Project Floor Plan Management System**
