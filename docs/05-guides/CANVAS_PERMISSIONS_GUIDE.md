# Canvas Permissions Guide

## Overview
The floor plan canvas now has role-based permissions that control who can edit the canvas design versus who can only view and book booths.

## Permission: `booths.canvas.edit`

### What It Controls
Users with this permission (or admins) can:
- Upload floor plan images
- Remove floor plan images
- Move booths on the canvas (drag and drop)
- Resize and rotate booths
- Save booth positions
- Edit canvas settings
- Use all canvas editing tools

Users without this permission can:
- ✅ View the floor plan canvas
- ✅ View all booths
- ✅ Book booths (if they have booking permissions)
- ✅ Zoom and pan the canvas
- ✅ Print the floor plan
- ❌ Cannot edit canvas design
- ❌ Cannot move booths
- ❌ Cannot upload/remove floor plans

## Implementation Details

### Backend (Controller)
Permission checks have been added to the following methods in `BoothController`:
- `uploadFloorplan()` - Upload floor plan image
- `removeFloorplan()` - Remove floor plan image
- `savePosition()` - Save individual booth position
- `saveAllPositions()` - Save all booth positions in bulk
- `updateExternalView()` - Toggle booth visibility
- `createBoothInZone()` - Create booths in zones
- `deleteBoothsInZone()` - Delete booths from zones

All these methods now check:
```php
if (!auth()->user()->hasPermission('booths.canvas.edit') && !auth()->user()->isAdmin()) {
    return response()->json([
        'status' => 403,
        'message' => 'You do not have permission to edit canvas design.'
    ], 403);
}
```

### Frontend (View)
1. **Toolbar Buttons**: Editing buttons are conditionally shown/hidden:
   - Upload/Remove Floorplan (hidden for non-editors)
   - Delete, Undo, Redo (hidden for non-editors)
   - Rotate buttons (hidden for non-editors)
   - Clear Canvas (hidden for non-editors)
   - Save button (hidden for non-editors)
   - Canvas Settings (hidden for non-editors)
   - Lock/Unlock booths (hidden for non-editors)

2. **JavaScript Permissions**: 
   - `canEditCanvas` variable is passed from backend to JavaScript
   - Drag and drop is disabled for non-editors
   - Booth dragging/resizing is disabled for non-editors
   - Transform controls are hidden for non-editors

3. **Viewing Features Still Available**:
   - Zoom controls (always visible)
   - Print button (always visible)
   - Booth numbers sidebar (always visible)
   - Show booths button (always visible)

## Setting Up Permissions

### Step 1: Add the Permission
Run the SQL script to add the permission:
```sql
-- See database/ADD_CANVAS_EDIT_PERMISSION.sql
```

Or manually add:
```sql
INSERT INTO `permissions` (`name`, `slug`, `module`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`)
VALUES ('Edit Canvas Design', 'booths.canvas.edit', 'booths', 'Edit floor plan canvas design, upload floor plans, move booths, and modify canvas layout', 1, 6, NOW(), NOW());
```

### Step 2: Assign to Roles
1. Go to Roles management
2. Edit the role that should have canvas editing access
3. Assign the `booths.canvas.edit` permission
4. Save the role

### Step 3: Assign Role to Users
1. Go to Users management
2. Edit the user
3. Assign the role with canvas edit permission
4. Save the user

## Testing

### Test as Admin
1. Admin should see all editing tools
2. Admin should be able to drag booths
3. Admin should be able to upload floor plans
4. Admin should be able to save positions

### Test as Regular User (without permission)
1. User should see the canvas and booths
2. User should be able to zoom and pan
3. User should NOT see editing toolbar buttons
4. User should NOT be able to drag booths
5. User should get 403 error if trying to save positions via API

### Test as User with Permission
1. User should see all editing tools
2. User should be able to perform all editing actions
3. User should be able to save changes

## Notes

- **Admins Always Have Access**: Users with `type = 1` (admins) automatically have all permissions, including canvas editing
- **Viewing is Always Allowed**: All authenticated users can view the canvas, regardless of permissions
- **Booking is Separate**: Booking permissions (`bookings.create`, etc.) are separate from canvas editing permissions
- **Permission Check is Server-Side**: Even if someone modifies the frontend, the backend will reject unauthorized requests

## Related Permissions

- `booths.view` - View booths and floor plans (required to see canvas)
- `booths.create` - Create new booths
- `booths.edit` - Edit booth properties
- `bookings.create` - Create bookings (separate from canvas editing)
- `booths.floor-plans` - Manage floor plan records (separate from canvas editing)
