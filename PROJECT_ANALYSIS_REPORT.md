# KHB Events - K Mall Xmas Booth Booking System
## Comprehensive Project Analysis Report

---

## 📋 Executive Summary

This is a **Booth Booking Management System** for KHB Events, specifically designed for the K Mall Xmas event. The application is built using the **Yii Framework 1.1.25** (PHP) and provides a complete solution for managing event booth reservations, client information, bookings, and administrative tasks.

---

## 🛠️ Technology Stack

### Backend Technologies
- **Framework**: Yii Framework 1.1.25 (PHP MVC Framework)
- **Programming Language**: PHP 7.4+
- **Database**: MySQL 5.7.36
- **Database Engine**: MyISAM
- **Server Environment**: XAMPP (Apache + MySQL + PHP)
- **Time Zone**: Asia/Phnom_Penh (UTC+7)

### Frontend Technologies
- **JavaScript Libraries**:
  - jQuery 1.12.4
  - jQuery UI
  - Bootstrap 4.3.1
  - DataTables 1.10.20 (for data grid management)
  - Popper.js
  - SweetAlert2 (for alerts)
  - Moment.js (for date/time handling)
  - Chart.js (for data visualization)
  - FullCalendar (for calendar views)
  - Select2 (for enhanced dropdowns)
  - Summernote (WYSIWYG editor)
  - InputMask (for form input masking)
  - TempusDominus Bootstrap 4 (date/time picker)

- **CSS Frameworks**:
  - Bootstrap 4.3.1
  - Font Awesome (icons)
  - Custom CSS files

### Additional Tools & Libraries
- **AdminLTE/CDW Theme**: Custom admin dashboard theme
- **Gii Code Generator**: Yii's code generation tool (enabled for development)
- **RBAC (Role-Based Access Control)**: Database-driven authentication manager
- **Yii Zii Widgets**: Framework widgets for UI components

---

## 📊 Code Statistics

### Lines of Code
- **Application PHP Code** (protected folder): **12,340 lines**
- **Total PHP Files**: **1,765 files**
- **JavaScript Code**: **970 lines** (excluding libraries)
- **CSS Code**: **364 lines** (excluding frameworks)
- **Total Project Files**: **4,488 files**

### File Distribution
- **PHP Files**: 1,765 files
- **JavaScript Files**: Multiple (including libraries)
- **CSS Files**: Multiple (including frameworks)
- **SQL Files**: 20 database files
- **Image Files**: 18+ image files (JPG, PNG, PSD)
- **Framework Files**: Yii 1.1.25 core files

---

## 🗄️ Database Structure

### Database Name
- **Production**: `khbevents_kmallxmas`
- **Development**: `khb_booth_kmall_xmas`

### Main Database Tables (8 tables)

1. **`booth`** - Main booth management table
   - Fields: id, booth_number, type, price, status, client_id, userid, bookid, category_id, sub_category_id, asset_id, booth_type_id
   - Total booths: 138 booths (A01-A08, D01-D35, SP-01 to SP-20)
   - Status values: 1=Available, 2=Confirmed, 3=Reserved, 4=Hidden, 5=Paid

2. **`book`** - Booking records
   - Fields: id, clientid, boothid (JSON array), date_book, userid, type
   - Stores multiple booth bookings per client

3. **`client`** - Client/vendor information
   - Fields: id, name, sex, position, company, phone_number

4. **`user`** - System users (admin/staff)
   - Fields: id, username, password (hashed), type, status, last_login
   - Type: 1=Admin, 2=Regular user

5. **`category`** - Booth categories
   - Fields: id, name, parent_id, limit, status, create_time, update_time
   - Supports hierarchical categories (parent-child relationship)

6. **`asset`** - Electrical assets (power requirements)
   - Fields: id, name, type, status
   - Values: 10A, 20A, 30A

7. **`booth_type`** - Booth type classification
   - Fields: id, name, status
   - Types: "Space with booth", "Space only"

8. **`web`** - Web/system configuration
   - Fields: id, name, value

---

## 🏗️ Application Architecture

### MVC Structure (Yii Framework)

#### **Models** (8 models)
Located in: `protected/models/`
- `Booth.php` - Booth management model
- `Book.php` - Booking model
- `Client.php` - Client information model
- `User.php` - User authentication model
- `Reserve.php` - Reservation model
- `Category.php` - Category model
- `ContactForm.php` - Contact form model
- `LoginForm.php` - Login form model
- `Web.php` - Web configuration model

#### **Controllers** (8 controllers)
Located in: `protected/controllers/`
- `BoothController.php` - Main booth management (460 lines)
  - Actions: index, view, create, update, delete, admin, my_booth
  - Custom actions: confirmR, clearR, rmbooth, bookPaid, upd_ext_view, bot_rss
- `BookController.php` - Booking management
- `ClientController.php` - Client management
- `UserController.php` - User management
- `ReserveController.php` - Reservation management
- `CategoryController.php` - Category management
- `DashboardController.php` - Admin dashboard
- `SiteController.php` - Public site pages (login, contact, error)

#### **Views** (50+ view files)
Located in: `protected/views/`
- Multiple layouts: main.php, main_admin.php, main_2.php, main_4_view.php, column1.php, column2.php, column2_2.php
- View folders: booth/, book/, client/, user/, reserve/, category/, dashboard/, site/, layouts/

#### **Components** (3 components)
Located in: `protected/components/`
- `Controller.php` - Base controller class
- `UserIdentity.php` - Custom authentication component
- `WebUser.php` - Extended user component

---

## 🔐 Security & Authentication

### Authentication System
- **Password Hashing**: Uses Yii's `CPasswordHelper` (bcrypt-based)
- **Session Management**: Cookie-based authentication with auto-login support
- **User Types**: 
  - Type 1: Administrator (full access)
  - Type 2: Regular user (limited access)
- **Status-based Access**: Users with status=0 are blocked

### Access Control
- **Role-Based Access Control (RBAC)**: Implemented using `CDbAuthManager`
- **Action-level Permissions**: Each controller has access rules
- **Public Actions**: Some actions accessible to all users (e.g., booth index)
- **Authenticated Actions**: Requires login (e.g., my_booth, create bookings)
- **Admin-only Actions**: Restricted to administrators

---

## 🎯 Key Features

### 1. **Booth Management**
- View all booths in a grid/list layout
- Filter booths by status, category, company, asset type
- Reserve, confirm, and manage booth bookings
- Visual booth map with color-coded status
- Multiple booth views: index, index_1, index_booth_3, index_booth_num

### 2. **Booking System**
- Create bookings with multiple booths per client
- Store booth IDs as JSON array in book table
- Booking confirmation workflow
- Payment status tracking (status 5 = Paid)
- Booking cancellation and removal

### 3. **Client Management**
- Store client/vendor information
- Company details, contact information
- Link clients to booths and bookings

### 4. **User Management**
- Admin and regular user accounts
- Password management with validation
- Last login tracking
- User status activation/deactivation

### 5. **Category Management**
- Hierarchical category system (parent-child)
- Category limits per booth
- Sub-category support

### 6. **Dashboard**
- Admin dashboard with user management
- Statistics and overview
- User status toggle (bootstrap-switch)

### 7. **Reservation System**
- Reserve booths with pending status (status 3)
- Confirm reservations (status 2)
- Clear/cancel reservations
- Company-based reservation mapping

---

## 📁 Project Structure

```
kmallxmas.khbevents.com/
├── assets/              # Yii-generated asset files
├── boothManagement/      # Empty directory
├── cdw/                  # AdminLTE/CDW theme files
│   ├── dist/            # Compiled theme files
│   └── plugins/         # 50+ JavaScript/CSS plugins
├── cgi-bin/             # CGI scripts directory
├── css/                 # Custom CSS files
│   ├── bootstrap.min.css
│   ├── main.css
│   ├── style.css
│   └── ...
├── data_table/          # DataTables library
│   └── DataTables-1.10.20/
├── DB/                  # Database SQL files (20 files)
│   ├── khb_booth_kmall_xmas.sql
│   └── ...
├── framework/           # Yii Framework 1.1.25 core
│   ├── base/
│   ├── db/
│   ├── gii/            # Code generator
│   ├── web/
│   └── zii/            # Zii widgets
├── images/             # Image assets
├── js/                 # Custom JavaScript
│   ├── jquery.js
│   ├── bootstrap.min.js
│   └── main.js
├── protected/          # Application code (protected from web access)
│   ├── commands/
│   ├── components/
│   ├── config/
│   ├── controllers/
│   ├── data/
│   ├── extensions/
│   ├── messages/
│   ├── migrations/
│   ├── models/
│   ├── runtime/        # Logs and cache
│   ├── tests/
│   └── views/
├── themes/             # Theme files
└── index.php           # Application entry point
```

---

## 🔧 Configuration

### Main Configuration (`protected/config/main.php`)
- **Application Name**: "KHB Booths Booking System"
- **Time Zone**: Asia/Phnom_Penh
- **Debug Mode**: Enabled (YII_DEBUG = true)
- **URL Format**: Path format (clean URLs)
- **Gii Tool**: Enabled with password "1" (development only)

### Database Configuration (`protected/config/database.php`)
- **Host**: localhost
- **Database**: khbevents_kmallxmas
- **Username**: khbevents_kmallxmas
- **Charset**: utf8
- **Time Zone**: +07:00 (Cambodia time)

---

## 📝 Development Tools

### Code Generation
- **Gii Module**: Enabled for rapid development
  - Model generator
  - CRUD generator
  - Accessible at: `/index.php?r=gii`

### Testing
- **PHPUnit**: Test framework configured
- **Test Structure**: Unit tests and functional tests
- **Test Database**: SQLite test database available

---

## 🌐 Frontend Features

### User Interface
- **Responsive Design**: Bootstrap 4-based responsive layout
- **Data Tables**: Interactive tables with sorting, filtering, pagination
- **Modal Dialogs**: Bootstrap modals for forms and confirmations
- **Form Validation**: Client-side and server-side validation
- **AJAX Support**: Asynchronous operations for better UX
- **Color-coded Status**: Visual indicators for booth status

### JavaScript Functionality
- Dynamic booth selection and reservation
- Real-time status updates
- Form validation
- AJAX form submissions
- Interactive maps and filters
- Company/Reserve mapping dropdowns

---

## 📈 Booth Status Workflow

1. **Status 1**: Available (default)
2. **Status 2**: Confirmed (reservation confirmed)
3. **Status 3**: Reserved (pending confirmation)
4. **Status 4**: Hidden (not visible in public view)
5. **Status 5**: Paid (payment received)

---

## 🔄 Booking Workflow

1. User selects booth(s) → Creates reservation (status 3)
2. Admin/User confirms reservation → Status changes to 2
3. Payment received → Status changes to 5
4. Cancellation → Status reverts to 1, booking removed

---

## 📦 Dependencies & Libraries

### Major JavaScript Libraries
- jQuery 1.12.4
- Bootstrap 4.3.1
- DataTables 1.10.20
- SweetAlert2
- Moment.js
- Chart.js
- FullCalendar
- Select2
- Summernote
- InputMask
- TempusDominus Bootstrap 4

### PHP Dependencies
- Yii Framework 1.1.25
- PHP 7.4+ (recommended)
- MySQL 5.7+
- PDO MySQL extension

---

## 🚀 Deployment Information

### Server Requirements
- **Web Server**: Apache (XAMPP)
- **PHP Version**: 7.4.26+
- **MySQL Version**: 5.7.36+
- **PHP Extensions**: PDO, PDO_MySQL, mbstring, GD

### Environment
- **Development**: Local XAMPP environment
- **Production**: Configured for remote hosting
- **Debug Mode**: Currently enabled (should be disabled in production)

---

## 📋 Database Files

The project includes **20 SQL files** in the `DB/` directory:
- `khb_booth_kmall_xmas.sql` - Main database schema
- `khb_booth_k_mall.sql` - Alternative schema
- `khb_booth_k_mall_update_1.sql` - Update script
- `booth_db_v2.sql`, `booth_db_v2_1.sql`, `booth_db_v3.sql` - Version history
- Multiple event-specific SQL files

---

## 🎨 Theme & Styling

### Admin Theme
- **CDW/AdminLTE**: Professional admin dashboard theme
- **Bootstrap 4**: Modern UI components
- **Font Awesome**: Icon library
- **Custom CSS**: Additional styling in `/css/` directory

### Layouts
- **Main Layout**: Public-facing layout
- **Admin Layout**: Admin dashboard layout (`main_admin.php`)
- **Column Layouts**: 1-column and 2-column layouts
- **View-specific Layouts**: Custom layouts for different views

---

## 🔍 Code Quality Notes

### Strengths
- Well-structured MVC architecture
- Proper use of Yii framework conventions
- Role-based access control implemented
- Password hashing for security
- Comprehensive CRUD operations
- Multiple view options for flexibility

### Areas for Improvement
- Debug mode enabled (should be disabled in production)
- Gii password is weak ("1")
- Some hardcoded values in controllers
- Mixed SQL queries (some direct DB queries, some Active Record)
- Large view files (some over 1000 lines)

---

## 📊 Summary Statistics

| Metric | Count |
|--------|-------|
| **Total Files** | 4,488 |
| **PHP Files** | 1,765 |
| **Application PHP Lines** | 12,340 |
| **JavaScript Lines** | 970 |
| **CSS Lines** | 364 |
| **SQL Files** | 20 |
| **Database Tables** | 8 |
| **Models** | 8 |
| **Controllers** | 8 |
| **View Files** | 50+ |
| **Booths** | 138 |
| **JavaScript Libraries** | 50+ |

---

## 🎯 Project Purpose

This system is designed for **KHB Events** to manage booth bookings for the **K Mall Xmas** event. It allows:
- Event organizers to manage booth inventory
- Vendors/clients to reserve and book booths
- Administrators to track bookings, payments, and client information
- Real-time status updates and visual booth mapping

---

## 📅 Project Information

- **Project Name**: KHB Booths Booking System
- **Event**: K Mall Xmas
- **Domain**: kmallxmas.khbevents.com
- **Framework Version**: Yii 1.1.25
- **Last Database Update**: October 22, 2023

---

## 🔐 Security Recommendations

1. **Disable Debug Mode** in production (`index.php`)
2. **Change Gii Password** or disable Gii module
3. **Use HTTPS** for production deployment
4. **Sanitize Input** (already using Yii validation, but review)
5. **Update Framework** (Yii 1.1.25 is outdated, consider migration)
6. **Database Credentials** should be in environment variables
7. **Enable Error Logging** instead of displaying errors

---

## 📝 Conclusion

This is a **well-structured PHP web application** built with Yii Framework 1.1.25 for managing event booth bookings. The system includes comprehensive features for booth management, client tracking, booking workflows, and administrative functions. The codebase follows MVC architecture and includes modern frontend libraries for an enhanced user experience.

**Total Application Code**: ~13,674 lines (PHP + JS + CSS)
**Framework Code**: Additional (Yii 1.1.25 core files)
**Third-party Libraries**: Extensive (50+ JavaScript/CSS libraries)

---

*Report Generated: $(Get-Date)*
*Analyzed by: Code Analysis Tool*
