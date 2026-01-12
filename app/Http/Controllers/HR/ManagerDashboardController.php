<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\LeaveRequest;
use App\Models\HR\Attendance;
use App\Models\HR\PerformanceReview;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ManagerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Manager Dashboard
     */
    public function index()
    {
        $manager = auth()->user()->employee;
        
        if (!$manager) {
            return redirect()->route('dashboard')
                ->with('error', 'Employee profile not found. Please contact HR.');
        }

        // Get team members (direct reports)
        $teamMembers = Employee::where('manager_id', $manager->id)
            ->with(['department', 'position'])
            ->get();

        if ($teamMembers->isEmpty()) {
            return redirect()->route('dashboard')
                ->with('info', 'You are not assigned as a manager for any team members.');
        }

        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->year;

        // Team Statistics
        $teamSize = $teamMembers->count();
        $activeTeamMembers = $teamMembers->where('status', 'active')->count();
        $onLeaveToday = $teamMembers->where('status', 'on-leave')->count();

        // Pending Approvals
        $teamMemberIds = $teamMembers->pluck('id');

        $pendingLeaveRequests = LeaveRequest::whereIn('employee_id', $teamMemberIds)
            ->where('status', 'pending')
            ->with(['employee', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingAttendanceApprovals = Attendance::whereIn('employee_id', $teamMemberIds)
            ->where('status', 'pending')
            ->with('employee')
            ->orderBy('date', 'desc')
            ->get();

        // Today's Team Attendance
        $todayTeamAttendance = Attendance::whereIn('employee_id', $teamMemberIds)
            ->where('date', $today)
            ->with('employee')
            ->get();

        $presentToday = $todayTeamAttendance->where('status', 'present')->count();
        $absentToday = $todayTeamAttendance->where('status', 'absent')->count();
        $lateToday = $todayTeamAttendance->where('status', 'late')->count();

        // This Month Team Attendance Stats
        $monthAttendance = Attendance::whereIn('employee_id', $teamMemberIds)
            ->whereBetween('date', [$thisMonth, Carbon::now()])
            ->get();

        $monthPresent = $monthAttendance->where('status', 'present')->count();
        $monthAbsent = $monthAttendance->where('status', 'absent')->count();
        $attendanceRate = $monthAttendance->count() > 0 
            ? round(($monthPresent / $monthAttendance->count()) * 100, 1) 
            : 0;

        // Upcoming Leaves (Next 7 Days)
        $upcomingLeaves = LeaveRequest::whereIn('employee_id', $teamMemberIds)
            ->where('status', 'approved')
            ->where('start_date', '>=', $today)
            ->where('start_date', '<=', $today->copy()->addDays(7))
            ->with(['employee', 'leaveType'])
            ->orderBy('start_date', 'asc')
            ->get();

        // Team Performance
        $recentPerformanceReviews = PerformanceReview::whereIn('employee_id', $teamMemberIds)
            ->where('status', 'completed')
            ->with('employee')
            ->orderBy('review_date', 'desc')
            ->limit(5)
            ->get();

        $averageTeamRating = PerformanceReview::whereIn('employee_id', $teamMemberIds)
            ->where('status', 'completed')
            ->whereYear('review_date', $thisYear)
            ->avg('overall_rating');

        // Team Leave Calendar (Next 30 Days)
        $teamLeaves = LeaveRequest::whereIn('employee_id', $teamMemberIds)
            ->where('status', 'approved')
            ->where('start_date', '>=', $today)
            ->where('start_date', '<=', $today->copy()->addDays(30))
            ->with(['employee', 'leaveType'])
            ->orderBy('start_date', 'asc')
            ->get();

        return view('manager.dashboard', compact(
            'manager',
            'teamMembers',
            'teamSize',
            'activeTeamMembers',
            'onLeaveToday',
            'pendingLeaveRequests',
            'pendingAttendanceApprovals',
            'presentToday',
            'absentToday',
            'lateToday',
            'monthPresent',
            'monthAbsent',
            'attendanceRate',
            'upcomingLeaves',
            'recentPerformanceReviews',
            'averageTeamRating',
            'teamLeaves'
        ));
    }

    /**
     * Quick Approve Leave Request
     */
    public function approveLeave(Request $request, LeaveRequest $leaveRequest)
    {
        $manager = auth()->user()->employee;
        
        if (!$manager) {
            return back()->with('error', 'Unauthorized.');
        }

        // Verify this leave request belongs to a team member
        $teamMemberIds = Employee::where('manager_id', $manager->id)->pluck('id');
        
        if (!$teamMemberIds->contains($leaveRequest->employee_id)) {
            return back()->with('error', 'You can only approve leave requests for your team members.');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Deduct leave balance
        $leaveBalance = \App\Models\HR\LeaveBalance::where('employee_id', $leaveRequest->employee_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', $leaveRequest->start_date->year)
            ->first();

        if ($leaveBalance) {
            $leaveBalance->increment('used_days', $leaveRequest->total_days);
            $leaveBalance->updateRemaining();
        }

        // Send notification to employee
        $notificationService = new \App\Services\HRNotificationService();
        $notificationService->notifyLeaveApproved($leaveRequest);

        return back()->with('success', 'Leave request approved successfully.');
    }

    /**
     * Quick Reject Leave Request
     */
    public function rejectLeave(Request $request, LeaveRequest $leaveRequest)
    {
        $manager = auth()->user()->employee;
        
        if (!$manager) {
            return back()->with('error', 'Unauthorized.');
        }

        // Verify this leave request belongs to a team member
        $teamMemberIds = Employee::where('manager_id', $manager->id)->pluck('id');
        
        if (!$teamMemberIds->contains($leaveRequest->employee_id)) {
            return back()->with('error', 'You can only reject leave requests for your team members.');
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason ?? 'Rejected by manager',
        ]);

        // Send notification to employee
        $notificationService = new \App\Services\HRNotificationService();
        $notificationService->notifyLeaveRejected($leaveRequest);

        return back()->with('success', 'Leave request rejected.');
    }

    /**
     * Quick Approve Attendance
     */
    public function approveAttendance(Request $request, Attendance $attendance)
    {
        $manager = auth()->user()->employee;
        
        if (!$manager) {
            return back()->with('error', 'Unauthorized.');
        }

        // Verify this attendance belongs to a team member
        $teamMemberIds = Employee::where('manager_id', $manager->id)->pluck('id');
        
        if (!$teamMemberIds->contains($attendance->employee_id)) {
            return back()->with('error', 'You can only approve attendance for your team members.');
        }

        $attendance->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Attendance approved successfully.');
    }

    /**
     * Bulk Approve Leaves
     */
    public function bulkApproveLeaves(Request $request)
    {
        $manager = auth()->user()->employee;
        
        if (!$manager) {
            return back()->with('error', 'Unauthorized.');
        }

        $teamMemberIds = Employee::where('manager_id', $manager->id)->pluck('id');
        $leaveRequestIds = $request->input('leave_ids', []);

        $leaveRequests = LeaveRequest::whereIn('id', $leaveRequestIds)
            ->whereIn('employee_id', $teamMemberIds)
            ->where('status', 'pending')
            ->get();

        foreach ($leaveRequests as $leaveRequest) {
            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Deduct leave balance
            $leaveBalance = \App\Models\HR\LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', $leaveRequest->start_date->year)
                ->first();

            if ($leaveBalance) {
                $leaveBalance->increment('used_days', $leaveRequest->total_days);
                $leaveBalance->updateRemaining();
            }

            // Send notification to employee
            $notificationService = new \App\Services\HRNotificationService();
            $notificationService->notifyLeaveApproved($leaveRequest);
        }

        return back()->with('success', count($leaveRequests) . ' leave request(s) approved successfully.');
    }
}
