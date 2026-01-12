<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['manager', 'parent', 'employees']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $departments = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();

        return view('hr.departments.index', compact('departments'));
    }

    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();
        $managers = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.departments.create', compact('departments', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'parent_id' => 'nullable|exists:departments,id',
            'budget' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        Department::create($validated);

        return redirect()->route('hr.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load(['manager', 'parent', 'children', 'employees', 'positions']);

        return view('hr.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        $departments = Department::active()->where('id', '!=', $department->id)->orderBy('name')->get();
        $managers = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.departments.edit', compact('department', 'departments', 'managers'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'parent_id' => 'nullable|exists:departments,id|not_in:' . $department->id,
            'budget' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $department->update($validated);

        return redirect()->route('hr.departments.show', $department)
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if ($department->employees()->count() > 0) {
            return redirect()->route('hr.departments.index')
                ->with('error', 'Cannot delete department with employees. Please reassign employees first.');
        }

        $department->delete();

        return redirect()->route('hr.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    /**
     * Duplicate a department
     */
    public function duplicate(Department $department)
    {
        $newDepartment = $department->replicate();
        $newDepartment->name = $department->name . ' (Copy)';
        $newDepartment->code = null; // Clear code to avoid unique constraint
        $newDepartment->manager_id = null; // Clear manager
        $newDepartment->is_active = false; // Set as inactive by default
        $newDepartment->save();

        return redirect()->route('hr.departments.edit', $newDepartment)
            ->with('success', 'Department duplicated successfully. Please update the details.');
    }
}
