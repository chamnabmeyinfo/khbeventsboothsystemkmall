<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\LeaveRequest;
use App\Models\HR\Employee;
use App\Models\HR\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveCalendarController extends Controller
{
    /**
     * Display leave calendar
     */
    public function index(Request $request)
    {
        $view = $request->get('view', 'month'); // month, week, day
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);

        // Get filters
        $departmentId = $request->get('department_id');
        $employeeId = $request->get('employee_id');
        $status = $request->get('status', 'approved'); // Only show approved by default

        // Build query
        $query = LeaveRequest::with(['employee.department', 'leaveType'])
            ->where('status', $status);

        // Apply filters
        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        // Get date range based on view
        switch ($view) {
            case 'month':
                $startDate = $selectedDate->copy()->startOfMonth();
                $endDate = $selectedDate->copy()->endOfMonth();
                break;
            case 'week':
                $startDate = $selectedDate->copy()->startOfWeek();
                $endDate = $selectedDate->copy()->endOfWeek();
                break;
            case 'day':
                $startDate = $selectedDate->copy();
                $endDate = $selectedDate->copy();
                break;
            default:
                $startDate = $selectedDate->copy()->startOfMonth();
                $endDate = $selectedDate->copy()->endOfMonth();
        }

        // Get leaves in date range
        $leaves = $query->where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($query) use ($startDate, $endDate) {
                  $query->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
              });
        })->get();

        // Format leaves for calendar
        $calendarLeaves = [];
        foreach ($leaves as $leave) {
            $currentDate = $leave->start_date->copy();
            while ($currentDate <= $leave->end_date) {
                if ($currentDate >= $startDate && $currentDate <= $endDate) {
                    $dateKey = $currentDate->format('Y-m-d');
                    if (!isset($calendarLeaves[$dateKey])) {
                        $calendarLeaves[$dateKey] = [];
                    }
                    $calendarLeaves[$dateKey][] = $leave;
                }
                $currentDate->addDay();
            }
        }

        // Get departments and employees for filters
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $employees = Employee::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Calculate statistics
        $stats = [
            'total_leaves' => $leaves->count(),
            'total_days' => $leaves->sum('total_days'),
            'unique_employees' => $leaves->pluck('employee_id')->unique()->count(),
        ];

        return view('hr.leave-calendar.index', compact(
            'view',
            'selectedDate',
            'startDate',
            'endDate',
            'calendarLeaves',
            'leaves',
            'departments',
            'employees',
            'departmentId',
            'employeeId',
            'status',
            'stats'
        ));
    }

    /**
     * Get calendar data as JSON (for AJAX)
     */
    public function getCalendarData(Request $request)
    {
        $start = Carbon::parse($request->get('start', Carbon::today()->startOfMonth()));
        $end = Carbon::parse($request->get('end', Carbon::today()->endOfMonth()));
        $departmentId = $request->get('department_id');
        $employeeId = $request->get('employee_id');
        $status = $request->get('status', 'approved');

        $query = LeaveRequest::with(['employee.department', 'leaveType'])
            ->where('status', $status)
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function($query) use ($start, $end) {
                      $query->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                  });
            });

        if ($departmentId) {
            $query->whereHas('employee', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $leaves = $query->get();

        $events = [];
        foreach ($leaves as $leave) {
            $events[] = [
                'id' => $leave->id,
                'title' => $leave->employee->full_name . ' - ' . $leave->leaveType->name,
                'start' => $leave->start_date->format('Y-m-d'),
                'end' => $leave->end_date->copy()->addDay()->format('Y-m-d'), // FullCalendar uses exclusive end
                'backgroundColor' => $this->getLeaveTypeColor($leave->leaveType->name),
                'borderColor' => $this->getLeaveTypeColor($leave->leaveType->name),
                'extendedProps' => [
                    'employee' => $leave->employee->full_name,
                    'leaveType' => $leave->leaveType->name,
                    'days' => $leave->total_days,
                    'department' => $leave->employee->department->name ?? 'N/A',
                ],
            ];
        }

        return response()->json($events);
    }

    /**
     * Get color for leave type
     */
    private function getLeaveTypeColor($leaveTypeName)
    {
        $colors = [
            'Annual Leave' => '#28a745',
            'Sick Leave' => '#dc3545',
            'Personal Leave' => '#17a2b8',
            'Maternity Leave' => '#e83e8c',
            'Paternity Leave' => '#6f42c1',
            'Emergency Leave' => '#fd7e14',
            'Unpaid Leave' => '#6c757d',
        ];

        return $colors[$leaveTypeName] ?? '#007bff';
    }
}
