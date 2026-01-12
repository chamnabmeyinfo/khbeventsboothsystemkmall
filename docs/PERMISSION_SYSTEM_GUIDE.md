# Permission System Guide

## Overview
The KHB Events system uses a role-based permission system where:
- **Users** are assigned **Roles**
- **Roles** are assigned **Permissions**
- **Permissions** control access to features in the UI

## How It Works

### 1. User → Role → Permissions Flow
```
User (has role_id) 
  → Role (has many permissions via role_permissions pivot table)
    → Permissions (slug-based, e.g., 'booths.view', 'bookings.create')
```

### 2. Permission Checking

#### In Controllers:
```php
// Check permission in controller
if (!auth()->user()->hasPermission('booths.create')) {
    abort(403, 'Unauthorized');
}
```

#### In Blade Templates:
```blade
@if(auth()->user()->hasPermission('booths.view') || auth()->user()->isAdmin())
    <a href="{{ route('booths.index') }}">View Booths</a>
@endif
```

#### In Routes (Middleware):
```php
Route::get('/booths', [BoothController::class, 'index'])
    ->middleware('permission:booths.view');
```

### 3. Permission Methods

#### User Model Methods:
- `hasPermission($slug)` - Check if user has a specific permission
- `hasAnyPermission([$slug1, $slug2])` - Check if user has any of the permissions
- `hasAllPermissions([$slug1, $slug2])` - Check if user has all permissions
- `getPermissions()` - Get all permissions for the user

#### Role Model Methods:
- `hasPermission($slug)` - Check if role has a specific permission
- `assignPermissions([$id1, $id2])` - Assign permissions to role

### 4. Admin Override
- Users with `type = 1` are considered admins
- Admins automatically have ALL permissions (bypasses all checks)
- Admin check: `auth()->user()->isAdmin()`

## Permission Structure

### Permission Slugs Format
Permissions follow the pattern: `{module}.{action}`

Examples:
- `booths.view` - View booths
- `booths.create` - Create booths
- `booths.edit` - Edit booths
- `booths.delete` - Delete booths
- `booths.floor-plans` - Manage floor plans
- `bookings.view` - View bookings
- `bookings.create` - Create bookings
- `clients.view` - View clients
- `clients.create` - Create clients
- `hr.dashboard.view` - View HR dashboard
- `hr.employees.view` - View employees
- `finance.payments.view` - View payments

## UI Integration

### Sidebar Menu
The sidebar menu (`resources/views/layouts/adminlte.blade.php`) checks permissions:
- Menu items only show if user has the required permission
- Falls back to admin check: `hasPermission('xxx') || isAdmin()`

### Action Buttons
Action buttons in views check permissions:
- Create buttons: `hasPermission('module.create')`
- Edit buttons: `hasPermission('module.edit')`
- Delete buttons: `hasPermission('module.delete')`

### Route Protection
Routes can be protected with middleware:
```php
Route::get('/booths', [BoothController::class, 'index'])
    ->middleware('permission:booths.view');
```

## Assigning Permissions

### Step 1: Create/Edit Role
1. Go to `http://localhost:8000/roles/create` or edit existing role
2. Assign permissions to the role (all permissions available, no restrictions)

### Step 2: Assign Role to User
1. Go to `http://localhost:8000/users/{id}/edit`
2. Select the role for the user
3. Save

### Step 3: User Gets Access
- User will see menu items based on their role's permissions
- User can access features based on assigned permissions
- Action buttons show/hide based on permissions

## Important Notes

1. **Free Assignment**: Permissions can be assigned freely to roles (no restrictions)
2. **Active/Inactive**: Both active and inactive permissions are available for assignment
3. **Admin Override**: Admins always have access to everything
4. **Performance**: Role and permissions are eager loaded during login to prevent N+1 queries
5. **Caching**: Permission checks are optimized to load relationships efficiently

## Testing Permissions

1. Create a test role with limited permissions
2. Assign the role to a test user
3. Log in as that user
4. Verify:
   - Only assigned menu items appear
   - Only permitted actions are available
   - Restricted features show 403 errors

## Common Permission Slugs

### Booths Module
- `booths.view`
- `booths.create`
- `booths.edit`
- `booths.delete`
- `booths.floor-plans`

### Bookings Module
- `bookings.view`
- `bookings.create`
- `bookings.edit`
- `bookings.delete`
- `bookings.manage`

### Clients Module
- `clients.view`
- `clients.create`
- `clients.edit`
- `clients.delete`

### Finance Module
- `finance.view`
- `payments.view`
- `payments.create`
- `finance.costings.view`
- `finance.expenses.view`
- `finance.revenues.view`
- `finance.pricing.view`
- `finance.categories.view`

### HR Module
- `hr.dashboard.view`
- `hr.departments.view`
- `hr.positions.view`
- `hr.employees.view`
- `hr.attendance.view`
- `hr.leaves.view`
- `hr.leaves.manage`
- `hr.performance.view`
- `hr.training.view`
- `hr.documents.view`
- `hr.salary.view`

### System Administration
- `users.view`
- `roles.view`
- `permissions.view`
- `activity-logs.view`
- `email-templates.view`
- `system.admin`

### Other Modules
- `reports.view`
- `communications.view`
- `export.data`
- `categories.view`
