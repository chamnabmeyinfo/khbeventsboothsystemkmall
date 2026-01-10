<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active' ? 1 : 0);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $permissions = $query->orderBy('module')->orderBy('sort_order')->get()->groupBy('module');

        // Statistics
        $stats = [
            'total_permissions' => Permission::count(),
            'active_permissions' => Permission::where('is_active', true)->count(),
            'modules_count' => Permission::distinct()->count('module'),
        ];

        // Get unique modules
        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();

        return view('permissions.index', compact('permissions', 'stats', 'modules'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create()
    {
        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();
        return view('permissions.create', compact('modules'));
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'module' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        Permission::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'module' => $validated['module'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified permission
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission
     */
    public function edit(Permission $permission)
    {
        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();
        return view('permissions.edit', compact('permission', 'modules'));
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
            'module' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $permission->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'module' => $validated['module'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
            'sort_order' => $validated['sort_order'] ?? $permission->sort_order,
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete permission. It is assigned to one or more roles.');
        }

        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
