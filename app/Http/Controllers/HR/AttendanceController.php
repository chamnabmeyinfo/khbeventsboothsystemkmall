<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Attendance;
use App\Models\HR\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['employee', 'approver']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('check_in_time', 'desc')->paginate(50)->withQueryString();
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.attendance.index', compact('attendances', 'employees'));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'break_duration' => 'nullable|integer|min:0',
            'status' => 'required|in:present,absent,late,half-day,on-leave,holiday',
            'notes' => 'nullable|string',
        ]);

        // Check if attendance already exists for this date
        $existing = Attendance::where('employee_id', $validated['employee_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', 'Attendance record already exists for this date.');
        }

        // Calculate total hours if check-in and check-out are provided
        if ($validated['check_in_time'] && $validated['check_out_time']) {
            $checkIn = Carbon::parse($validated['date'].' '.$validated['check_in_time']);
            $checkOut = Carbon::parse($validated['date'].' '.$validated['check_out_time']);
            $totalMinutes = $checkOut->diffInMinutes($checkIn) - ($validated['break_duration'] ?? 0);
            $validated['total_hours'] = round($totalMinutes / 60, 2);
        }

        $attendance = Attendance::create($validated);

        return redirect()->route('hr.attendance.index')
            ->with('success', 'Attendance record created successfully.');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['employee', 'approver']);

        return view('hr.attendance.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::active()->orderBy('first_name')->orderBy('last_name')->get();

        return view('hr.attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'break_duration' => 'nullable|integer|min:0',
            'status' => 'required|in:present,absent,late,half-day,on-leave,holiday',
            'notes' => 'nullable|string',
        ]);

        // Check for duplicate if date or employee changed
        if ($validated['date'] != $attendance->date || $validated['employee_id'] != $attendance->employee_id) {
            $existing = Attendance::where('employee_id', $validated['employee_id'])
                ->where('date', $validated['date'])
                ->where('id', '!=', $attendance->id)
                ->first();

            if ($existing) {
                return back()->withInput()->with('error', 'Attendance record already exists for this date.');
            }
        }

        // Recalculate total hours
        if ($validated['check_in_time'] && $validated['check_out_time']) {
            $checkIn = Carbon::parse($validated['date'].' '.$validated['check_in_time']);
            $checkOut = Carbon::parse($validated['date'].' '.$validated['check_out_time']);
            $totalMinutes = $checkOut->diffInMinutes($checkIn) - ($validated['break_duration'] ?? 0);
            $validated['total_hours'] = round($totalMinutes / 60, 2);
        } else {
            $validated['total_hours'] = null;
        }

        $attendance->update($validated);

        return redirect()->route('hr.attendance.show', $attendance)
            ->with('success', 'Attendance record updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('hr.attendance.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    public function approve(Attendance $attendance)
    {
        $attendance->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Attendance record approved.');
    }
}
