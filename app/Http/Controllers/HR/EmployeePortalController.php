<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Attendance;
use App\Models\HR\Employee;
use App\Models\HR\EmployeeDocument;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use App\Models\HR\PerformanceReview;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeePortalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Employee Dashboard
     */
    public function dashboard()
    {
        $employee = auth()->user()->employee;

        if (! $employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found. Please contact HR.');
        }

        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->year;

        // Leave Balance
        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', $thisYear)
            ->with('leaveType')
            ->get()
            ->map(function ($balance) {
                $balance->balance = $balance->remaining_days; // Add balance alias for views

                return $balance;
            });

        // Recent Leave Requests
        $recentLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Pending Leave Requests
        $pendingLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();

        // Attendance Stats (This Month)
        $monthAttendance = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$thisMonth, Carbon::now()])
            ->get();

        $presentDays = $monthAttendance->where('status', 'present')->count();
        $absentDays = $monthAttendance->where('status', 'absent')->count();
        $lateDays = $monthAttendance->where('status', 'late')->count();
        $totalWorkingDays = $monthAttendance->count();

        // Today's Attendance
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->first();

        // Upcoming Performance Reviews
        $upcomingReviews = PerformanceReview::where('employee_id', $employee->id)
            ->where('status', '!=', 'completed')
            ->orderBy('review_date', 'asc')
            ->limit(3)
            ->get();

        // Recent Documents
        $recentDocuments = EmployeeDocument::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming Training
        $upcomingTraining = $employee->training()
            ->where('status', 'scheduled')
            ->where('start_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->limit(3)
            ->get();

        return view('employee-portal.dashboard', compact(
            'employee',
            'leaveBalances',
            'recentLeaves',
            'pendingLeaves',
            'presentDays',
            'absentDays',
            'lateDays',
            'totalWorkingDays',
            'todayAttendance',
            'upcomingReviews',
            'recentDocuments',
            'upcomingTraining'
        ));
    }

    /**
     * Employee Profile
     */
    public function profile()
    {
        $employee = auth()->user()->employee;

        if (! $employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $employee->load(['department', 'position', 'manager', 'user']);

        return view('employee-portal.profile', compact('employee'));
    }

    /**
     * Update Employee Profile
     */
    public function updateProfile(Request $request)
    {
        $employee = auth()->user()->employee;

        if (! $employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }

            $path = $request->file('avatar')->store('employee-avatars', 'public');
            $validated['avatar'] = $path;
        }

        $employee->update($validated);

        return redirect()->route('employee.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Leave Management
     */
    public function leaves(Request $request)
    {
        $employee = auth()->user()->employee;

        if (! $employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $query = LeaveRequest::where('employee_id', $employee->id)
            ->with('leaveType');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Get leave balances
        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', Carbon::now()->year)
            ->with('leaveType')
            ->get()
            ->map(function ($balance) {
                $balance->balance = $balance->remaining_days; // Add balance alias for views

                return $balance;
            });

        // Get leave types for application
        $leaveTypes = LeaveType::where('is_active', true)->get();

        return view('employee-portal.leaves', compact('leaves', 'leaveBalances', 'leaveTypes'));
    }

    /**
     * Apply for Leave
     */
    public function applyLeave(Request $request)
    {
        $employee = auth()->user()->employee;

        if (! $employee) {
            return back()->with('error', 'Employee profile not found.');
        }

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'half_day' => 'nullable|boolean',
        ]);

        // Check leave balance
        $leaveBalance = LeaveBalance::where('employee_id', $employee->id)
            ->where('leave_type_id', $validated['leave_type_id'])
            ->where('year', Carbon::parse($validated['start_date'])->year)
            ->first();

        if (! $leaveBalance || $leaveBalance->remaining_days <= 0) {
            return back()->with('error', 'Insufficient leave balance.');
        }

        // Calculate days
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDays = $validated['half_day'] ? 0.5 : $startDate->diffInDays($endDate) + 1;

        if ($totalDays > $leaveBalance->remaining_days) {
            return back()->with('error', 'Requested days exceed available balance.');
        }

        // Check for overlapping leaves
        $overlapping = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->with('error', 'You have an overlapping leave request.');
        }

        $validated['employee_id'] = $employee->id;
        $validated['total_days'] = $totalDays;
        $validated['status'] = 'pending';

        $leaveRequest = LeaveRequest::create($validated);

        // Send notification to manager
        $notificationService = new \App\Services\HRNotificationService;
        $notificationService->notifyLeaveRequestSubmitted($leaveRequest);

        return redirect()->route('employee.leaves')
            ->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Attendance History
     */
    public function attendance(Request $request)
    {
        $employee = auth()->user()->employee;

        if (! $employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $query = Attendance::where('employee_id', $employee->id);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        } else {
            // Default to current month
            $query->whereBetween('date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);
        }

        $attendance = $query->orderBy('date', 'desc')->paginate(30)->withQueryString();

        // Statistics
        $stats = [
            'total_days' => $attendance->total(),
            'present' => Attendance::where('employee_id', $employee->id)
                ->whereIn('id', $attendance->pluck('id'))
                ->where('status', 'present')
                ->count(),
            'absent' => Attendance::where('employee_id', $employee->id)
                ->whereIn('id', $attendance->pluck('id'))
                ->where('status', 'absent')
                ->count(),
            'late' => Attendance::where('employee_id', $employee->id)
                ->whereIn('id', $attendance->pluck('id'))
                ->where('status', 'late')
                ->count(),
        ];

        return view('employee-portal.attendance', compact('attendance', 'stats'));
    }

    /**
     * Documents
     */
    public function documents()
    {
        $employee = auth()->user()->employee;

        if (! $employee) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found.');
        }

        $documents = EmployeeDocument::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('employee-portal.documents', compact('documents'));
    }

    /**
     * Download Document
     */
    public function downloadDocument(EmployeeDocument $document)
    {
        $employee = auth()->user()->employee;

        if (! $employee || $document->employee_id != $employee->id) {
            return back()->with('error', 'Unauthorized access.');
        }

        if (! Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->document_name);
    }
}
