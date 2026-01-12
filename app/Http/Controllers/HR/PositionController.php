<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Position;
use App\Models\HR\Department;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::with(['department', 'employees']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $positions = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();
        $departments = Department::active()->orderBy('name')->get();

        return view('hr.positions.index', compact('positions', 'departments'));
    }

    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();

        return view('hr.positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:positions,code',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        Position::create($validated);

        return redirect()->route('hr.positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function show(Position $position)
    {
        $position->load(['department', 'employees']);

        return view('hr.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        $departments = Department::active()->orderBy('name')->get();

        return view('hr.positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:positions,code,' . $position->id,
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'min_salary' => 'nullable|numeric|min:0',
            'max_salary' => 'nullable|numeric|min:0|gte:min_salary',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $position->update($validated);

        return redirect()->route('hr.positions.show', $position)
            ->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        if ($position->employees()->count() > 0) {
            return redirect()->route('hr.positions.index')
                ->with('error', 'Cannot delete position with employees. Please reassign employees first.');
        }

        $position->delete();

        return redirect()->route('hr.positions.index')
            ->with('success', 'Position deleted successfully.');
    }

    /**
     * Duplicate a position
     */
    public function duplicate(Position $position)
    {
        $newPosition = $position->replicate();
        $newPosition->name = $position->name . ' (Copy)';
        $newPosition->code = null; // Clear code to avoid unique constraint
        $newPosition->is_active = false; // Set as inactive by default
        $newPosition->save();

        return redirect()->route('hr.positions.edit', $newPosition)
            ->with('success', 'Position duplicated successfully. Please update the details.');
    }
}
