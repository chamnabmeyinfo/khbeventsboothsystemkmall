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
        return redirect()->route('staff.access', array_merge(
            $request->query(),
            ['tab' => 'permissions']
        ));
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

        return redirect()->route('staff.access', ['tab' => 'permissions'])
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
            'slug' => 'required|string|max:255|unique:permissions,slug,'.$permission->id,
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

        return redirect()->route('staff.access', ['tab' => 'permissions'])
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Request $request, Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            $message = 'Cannot delete permission. It is assigned to one or more roles.';
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => $message], 422);
            }

            return redirect()->route('staff.access', ['tab' => 'permissions'])
                ->with('error', $message);
        }

        $permission->delete();

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully.',
            ]);
        }

        return redirect()->route('staff.access', ['tab' => 'permissions'])
            ->with('success', 'Permission deleted successfully.');
    }
}
