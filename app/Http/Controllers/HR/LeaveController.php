<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee', 'leaveType', 'approver', 'rejector']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();
        $leaveTypes = LeaveType::active()->orderBy('name')->get();

        return view('hr.leaves.index', compact('leaveRequests', 'employees', 'leaveTypes'));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();
        $leaveTypes = LeaveType::active()->orderBy('name')->get();

        return view('hr.leaves.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        // Calculate total days
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $validated['total_days'] = $start->diffInDays($end) + 1;

        // Check leave balance
        $leaveBalance = LeaveBalance::where('employee_id', $validated['employee_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', date('Y'))
            ->first();

        if (! $leaveBalance) {
            return back()->withInput()->with('error', 'Leave balance not found for this employee.');
        }

        if ($leaveBalance->remaining_days < $validated['total_days']) {
            return back()->withInput()->with('error', 'Insufficient leave balance. Remaining: '.$leaveBalance->remaining_days.' days');
        }

        // Check for overlapping leave requests
        $overlapping = LeaveRequest::where('employee_id', $validated['employee_id'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withInput()->with('error', 'Leave request overlaps with existing leave.');
        }

        $leaveRequest = LeaveRequest::create($validated);

        // Send notification to manager
        $notificationService = new \App\Services\HRNotificationService;
        $notificationService->notifyLeaveRequestSubmitted($leaveRequest);

        return redirect()->route('hr.leaves.show', $leaveRequest)
            ->with('success', 'Leave request created successfully.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['employee', 'leaveType', 'approver', 'rejector']);

        return view('hr.leaves.show', compact('leaveRequest'));
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        // Only allow editing pending leave requests
        if ($leaveRequest->status != 'pending') {
            return redirect()->route('hr.leaves.show', $leaveRequest)
                ->with('error', 'Only pending leave requests can be edited.');
        }

        $leaveRequest->load(['employee', 'leaveType']);
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();
        $leaveTypes = LeaveType::active()->orderBy('name')->get();

        return view('hr.leaves.edit', compact('leaveRequest', 'employees', 'leaveTypes'));
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // Only allow updating pending leave requests
        if ($leaveRequest->status != 'pending') {
            return redirect()->route('hr.leaves.show', $leaveRequest)
                ->with('error', 'Only pending leave requests can be updated.');
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        // Calculate total days
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $validated['total_days'] = $start->diffInDays($end) + 1;

        // Check leave balance (including current request if changing employee or leave type)
        $leaveBalance = LeaveBalance::where('employee_id', $validated['employee_id'])
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', Carbon::parse($validated['start_date'])->year)
            ->first();

        if (! $leaveBalance) {
            return back()->withInput()->with('error', 'Leave balance not found for this employee.');
        }

        // Calculate available balance (add back current request days if same employee/type)
        $availableBalance = $leaveBalance->remaining_days;
        if ($leaveRequest->employee_id == $validated['employee_id']
            && $leaveRequest->leave_type_id == $validated['leave_type_id']
            && $leaveRequest->start_date->year == Carbon::parse($validated['start_date'])->year) {
            $availableBalance += $leaveRequest->total_days;
        }

        if ($availableBalance < $validated['total_days']) {
            return back()->withInput()->with('error', 'Insufficient leave balance. Available: '.$availableBalance.' days');
        }

        // Check for overlapping leave requests (excluding current request)
        $overlapping = LeaveRequest::where('employee_id', $validated['employee_id'])
            ->where('id', '!=', $leaveRequest->id)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withInput()->with('error', 'Leave request overlaps with existing leave.');
        }

        $leaveRequest->update($validated);

        return redirect()->route('hr.leaves.show', $leaveRequest)
            ->with('success', 'Leave request updated successfully.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        // Only allow deleting pending or cancelled leave requests
        if (! in_array($leaveRequest->status, ['pending', 'cancelled'])) {
            return back()->with('error', 'Only pending or cancelled leave requests can be deleted.');
        }

        $leaveRequest->delete();

        return redirect()->route('hr.leaves.index')
            ->with('success', 'Leave request deleted successfully.');
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status != 'pending') {
            return back()->with('error', 'Only pending leave requests can be approved.');
        }

        // Update leave balance
        $leaveBalance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', date('Y'))
            ->first();

        if ($leaveBalance) {
            $leaveBalance->used_days += $leaveRequest->total_days;
            $leaveBalance->remaining_days = $leaveBalance->allocated_days + $leaveBalance->carried_forward_days - $leaveBalance->used_days;
            $leaveBalance->save();
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Send notification to employee
        $notificationService = new \App\Services\HRNotificationService;
        $notificationService->notifyLeaveApproved($leaveRequest);

        return back()->with('success', 'Leave request approved successfully.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status != 'pending') {
            return back()->with('error', 'Only pending leave requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Send notification to employee
        $notificationService = new \App\Services\HRNotificationService;
        $notificationService->notifyLeaveRejected($leaveRequest);

        return back()->with('success', 'Leave request rejected.');
    }

    public function cancel(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status == 'approved') {
            // Refund leave balance
            $leaveBalance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', date('Y'))
                ->first();

            if ($leaveBalance) {
                $leaveBalance->used_days -= $leaveRequest->total_days;
                $leaveBalance->remaining_days = $leaveBalance->allocated_days + $leaveBalance->carried_forward_days - $leaveBalance->used_days;
                $leaveBalance->save();
            }
        }

        $leaveRequest->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Leave request cancelled.');
    }
}
