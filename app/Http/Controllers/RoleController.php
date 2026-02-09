<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(Request $request)
    {
        $query = Role::with(['users', 'permissions']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active' ? 1 : 0);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->orderBy('sort_order')->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_roles' => Role::count(),
            'active_roles' => Role::where('is_active', true)->count(),
            'total_users_with_roles' => $this->getUsersWithRolesCount(),
        ];

        return view('roles.index', compact('roles', 'stats'));
    }

    /**
     * Safely get count of users with roles
     */
    private function getUsersWithRolesCount()
    {
        try {
            // Check if role_id column exists
            $hasRoleId = Schema::hasColumn('user', 'role_id');
            if ($hasRoleId) {
                return \App\Models\User::whereNotNull('role_id')->count();
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        // Load all permissions (active and inactive) for free assignment
        $permissions = Permission::orderBy('module')->orderBy('sort_order')->get()->groupBy('module');

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'permissions' => 'nullable|array',
            // Removed exists validation to allow free assignment - permissions.* can be any valid integer
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => true, // Auto-default to true (active) for all new roles
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        // Assign permissions freely - no restrictions
        if (isset($validated['permissions']) && is_array($validated['permissions'])) {
            // Filter out any invalid IDs and ensure they're integers
            $permissionIds = array_filter(array_map('intval', $validated['permissions']), function ($id) {
                return $id > 0; // Only positive integers
            });
            if (! empty($permissionIds)) {
                $role->assignPermissions($permissionIds);
            } else {
                // If no valid permissions, detach all
                $role->permissions()->detach();
            }
        } else {
            // If permissions not provided, detach all
            $role->permissions()->detach();
        }

        // Return JSON if request expects JSON (for AJAX/modal requests)
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'role' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'slug' => $role->slug,
                    'description' => $role->description,
                    'is_active' => $role->is_active,
                ],
            ], 200);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['users', 'permissions']);

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        // Load all permissions (active and inactive) for free assignment
        $permissions = Permission::orderBy('module')->orderBy('sort_order')->get()->groupBy('module');
        $role->load('permissions');
        $selectedPermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'slug' => 'required|string|max:255|unique:roles,slug,'.$role->id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'permissions' => 'nullable|array',
            // Removed exists validation to allow free assignment - permissions.* can be any valid integer
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? true : $role->is_active, // If checkbox is checked, set to true; otherwise keep existing value
            'sort_order' => $validated['sort_order'] ?? $role->sort_order,
        ]);

        // Assign permissions freely - no restrictions
        if (isset($validated['permissions']) && is_array($validated['permissions'])) {
            // Filter out any invalid IDs and ensure they're integers
            $permissionIds = array_filter(array_map('intval', $validated['permissions']), function ($id) {
                return $id > 0; // Only positive integers
            });
            if (! empty($permissionIds)) {
                $role->assignPermissions($permissionIds);
            } else {
                // If no valid permissions, detach all
                $role->permissions()->detach();
            }
        } else {
            // If permissions not provided, detach all
            $role->permissions()->detach();
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role. There are users assigned to this role.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
