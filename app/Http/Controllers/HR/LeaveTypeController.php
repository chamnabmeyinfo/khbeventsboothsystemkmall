<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveType::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $leaveTypes = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();

        return view('hr.leave-types.index', compact('leaveTypes'));
    }

    public function create()
    {
        return view('hr.leave-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:leave_types,code',
            'description' => 'nullable|string',
            'max_days_per_year' => 'nullable|integer|min:0',
            'carry_forward' => 'boolean',
            'requires_approval' => 'boolean',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        LeaveType::create($validated);

        return redirect()->route('hr.leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    public function show(LeaveType $leaveType)
    {
        $leaveType->loadCount('leaveRequests');

        return view('hr.leave-types.show', compact('leaveType'));
    }

    public function edit(LeaveType $leaveType)
    {
        return view('hr.leave-types.edit', compact('leaveType'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:leave_types,code,'.$leaveType->id,
            'description' => 'nullable|string',
            'max_days_per_year' => 'nullable|integer|min:0',
            'carry_forward' => 'boolean',
            'requires_approval' => 'boolean',
            'is_paid' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $leaveType->update($validated);

        return redirect()->route('hr.leave-types.show', $leaveType)
            ->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        if ($leaveType->leaveRequests()->count() > 0) {
            return redirect()->route('hr.leave-types.index')
                ->with('error', 'Cannot delete leave type with existing leave requests.');
        }

        $leaveType->delete();

        return redirect()->route('hr.leave-types.index')
            ->with('success', 'Leave type deleted successfully.');
    }
}
