<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StaffAccessController extends Controller
{
    /**
     * Roles + permissions hub (tabbed UI).
     */
    public function index(Request $request)
    {
        $activeTab = $request->query('tab') === 'permissions' ? 'permissions' : 'roles';

        $activeRoleSub = in_array($request->query('role_sub'), ['overview', 'cards', 'table'], true)
            ? $request->query('role_sub')
            : 'cards';

        $activePermissionSub = in_array($request->query('permission_sub'), ['overview', 'modules', 'flat'], true)
            ? $request->query('permission_sub')
            : 'modules';

        $rolesQuery = Role::with(['users', 'permissions']);
        if ($request->filled('status')) {
            $rolesQuery->where('is_active', $request->status == 'active' ? 1 : 0);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $rolesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $roles = $rolesQuery->orderBy('sort_order')->paginate(20)->withQueryString();

        $roleStats = [
            'total_roles' => Role::count(),
            'active_roles' => Role::where('is_active', true)->count(),
            'total_users_with_roles' => $this->getUsersWithRolesCount(),
        ];

        $permQuery = Permission::query();
        if ($request->filled('module')) {
            $permQuery->where('module', $request->module);
        }
        if ($request->filled('status')) {
            $permQuery->where('is_active', $request->status == 'active' ? 1 : 0);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $permQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $permissions = $permQuery->with('roles')->orderBy('module')->orderBy('sort_order')->get()->groupBy('module');

        $permissionStats = [
            'total_permissions' => Permission::count(),
            'active_permissions' => Permission::where('is_active', true)->count(),
            'modules_count' => Permission::distinct()->count('module'),
        ];

        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();

        return view('staff.access', compact(
            'activeTab',
            'activeRoleSub',
            'activePermissionSub',
            'roles',
            'roleStats',
            'permissions',
            'permissionStats',
            'modules'
        ));
    }

    private function getUsersWithRolesCount(): int
    {
        try {
            if (Schema::hasColumn('users', 'role_id')) {
                return \App\Models\User::whereNotNull('role_id')->count();
            }

            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
