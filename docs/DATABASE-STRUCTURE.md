# Database Structure - Essential Tables

This document outlines the essential database tables needed for the KHB Events Booth Booking System.

## ✅ Core Tables (Required)

### 1. **users**
- **Purpose**: User authentication and management
- **Model**: `App\Models\User`
- **Used in**: Login, Dashboard, User management
- **Status**: ✅ Essential

### 2. **booths**
- **Purpose**: Booth management and booking
- **Model**: `App\Models\Booth`
- **Used in**: BoothController, Dashboard, Booking system
- **Status**: ✅ Essential (Main feature)

### 3. **clients**
- **Purpose**: Client/vendor information
- **Model**: `App\Models\Client`
- **Used in**: ClientController, Booking system
- **Status**: ✅ Essential

### 4. **books**
- **Purpose**: Booking records
- **Model**: `App\Models\Book`
- **Used in**: BookController, Booking system
- **Status**: ✅ Essential

### 5. **categories**
- **Purpose**: Booth categories
- **Model**: `App\Models\Category`
- **Used in**: CategoryController, Booth management
- **Status**: ✅ Essential

### 6. **asset**
- **Purpose**: Electrical assets (10A, 20A, 30A)
- **Model**: `App\Models\Asset`
- **Used in**: BoothController (dropdowns)
- **Status**: ✅ Essential

### 7. **booth_type**
- **Purpose**: Booth types (Space with booth, Space only)
- **Model**: `App\Models\BoothType`
- **Used in**: BoothController (dropdowns)
- **Status**: ✅ Essential

## ⚙️ Settings Tables (Required)

### 8. **settings**
- **Purpose**: Application settings
- **Model**: `App\Models\Setting`
- **Used in**: SettingsController
- **Status**: ✅ Essential

### 9. **canvas_settings**
- **Purpose**: Canvas/floorplan settings
- **Model**: `App\Models\CanvasSetting`
- **Used in**: SettingsController
- **Status**: ✅ Essential

### 10. **zone_settings**
- **Purpose**: Zone-specific booth settings
- **Model**: `App\Models\ZoneSetting`
- **Used in**: BoothController (zone management)
- **Status**: ✅ Essential

## 🎫 Event Management Tables (Optional - Admin System)

### 11. **events**
- **Purpose**: Event management (admin system)
- **Model**: `App\Models\Event`
- **Used in**: EventController (admin)
- **Status**: ⚠️ Optional (Admin feature)

### 12. **category_events**
- **Purpose**: Event categories (uses 'categories' table)
- **Model**: `App\Models\CategoryEvent`
- **Used in**: EventController (admin)
- **Status**: ⚠️ Optional (Admin feature)
- **Note**: Uses same 'categories' table as main Category model

### 13. **user_events**
- **Purpose**: Event users (uses 'users' table)
- **Model**: `App\Models\UserEvent`
- **Used in**: AdminDashboardController
- **Status**: ⚠️ Optional (Admin feature)
- **Note**: Uses same 'users' table as main User model

### 14. **admins**
- **Purpose**: Admin authentication (separate from users)
- **Model**: `App\Models\Admin`
- **Used in**: AdminLoginController, AdminDashboardController
- **Status**: ⚠️ Optional (Admin feature)

## ❌ Removed Tables

### **web / webs**
- **Status**: ❌ Removed (unused)
- **Reason**: No references found in codebase
- **Migration**: `2026_01_15_200000_drop_unused_tables.php`

## 📊 Table Relationships

```
users
  ├── booths (userid)
  └── books (userid)

clients
  └── books (client_id)

categories
  ├── booths (category_id)
  └── events (category_id) [if using event system]

booths
  ├── asset (asset_id)
  ├── booth_type (booth_type_id)
  ├── category (category_id)
  ├── client (client_id)
  └── books (booth_id)

books
  ├── booth (booth_id)
  ├── client (client_id)
  └── user (userid)
```

## 🎯 Focus Areas for Development

### Core Features (Essential)
1. **Booth Management** - Create, edit, delete booths
2. **Booking System** - Reserve, confirm, mark as paid
3. **Client Management** - Store client information
4. **Category Management** - Organize booths by category
5. **User Management** - Authentication and authorization

### Settings & Configuration
- Application settings
- Canvas/floorplan configuration
- Zone-specific settings

### Optional Features
- Event management system (admin)
- Advanced reporting
- Export functionality

---

**Last Updated:** 2026-01-15
