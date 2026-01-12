<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Employee;
use App\Models\HR\Department;
use App\Models\HR\Position;
use App\Models\HR\Attendance;
use App\Models\HR\LeaveRequest;
use App\Models\HR\PerformanceReview;
use App\Models\HR\EmployeeTraining;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HRDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->year;

        // Employee Statistics
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $newHiresThisMonth = Employee::where('hire_date', '>=', $thisMonth)->count();
        $onLeave = Employee::where('status', 'on-leave')->count();

        // Department Statistics
        $totalDepartments = Department::where('is_active', true)->count();
        $totalPositions = Position::where('is_active', true)->count();

        // Attendance Statistics
        $todayAttendance = Attendance::where('date', $today)->count();
        $presentToday = Attendance::where('date', $today)->where('status', 'present')->count();
        $absentToday = Attendance::where('date', $today)->where('status', 'absent')->count();
        $lateToday = Attendance::where('date', $today)->where('status', 'late')->count();
        $attendanceRate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0;

        // Leave Statistics
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
        $approvedLeavesThisMonth = LeaveRequest::where('status', 'approved')
            ->whereMonth('start_date', Carbon::now()->month)
            ->whereYear('start_date', $thisYear)
            ->count();
        $onLeaveToday = LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();

        // Performance Statistics
        $pendingReviews = PerformanceReview::where('status', 'draft')->count();
        $completedReviewsThisYear = PerformanceReview::where('status', 'completed')
            ->whereYear('review_date', $thisYear)
            ->count();
        $averageRating = PerformanceReview::where('status', 'completed')
            ->whereYear('review_date', $thisYear)
            ->avg('overall_rating');

        // Training Statistics
        $upcomingTraining = EmployeeTraining::where('status', 'scheduled')
            ->where('start_date', '>=', $today)
            ->count();
        $ongoingTraining = EmployeeTraining::where('status', 'in-progress')->count();
        $completedTrainingThisYear = EmployeeTraining::where('status', 'completed')
            ->whereYear('end_date', $thisYear)
            ->count();

        // Recent Activities
        $recentHires = Employee::with(['department', 'position'])
            ->orderBy('hire_date', 'desc')
            ->limit(5)
            ->get();

        $recentLeaveRequests = LeaveRequest::with(['employee', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $upcomingBirthdays = Employee::whereMonth('date_of_birth', Carbon::now()->month)
            ->whereDay('date_of_birth', '>=', Carbon::now()->day)
            ->orderByRaw('DAY(date_of_birth)')
            ->limit(5)
            ->get();

        // Department-wise employee count
        $departmentStats = Department::withCount('employees')
            ->where('is_active', true)
            ->orderBy('employees_count', 'desc')
            ->limit(5)
            ->get();

        // Monthly attendance trend (last 6 months)
        $attendanceTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $attendanceTrend[] = [
                'month' => $month->format('M Y'),
                'present' => Attendance::whereBetween('date', [$monthStart, $monthEnd])
                    ->where('status', 'present')
                    ->count(),
                'absent' => Attendance::whereBetween('date', [$monthStart, $monthEnd])
                    ->where('status', 'absent')
                    ->count(),
            ];
        }

        return view('hr.dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'newHiresThisMonth',
            'onLeave',
            'totalDepartments',
            'totalPositions',
            'todayAttendance',
            'presentToday',
            'absentToday',
            'lateToday',
            'attendanceRate',
            'pendingLeaves',
            'approvedLeavesThisMonth',
            'onLeaveToday',
            'pendingReviews',
            'completedReviewsThisYear',
            'averageRating',
            'upcomingTraining',
            'ongoingTraining',
            'completedTrainingThisYear',
            'recentHires',
            'recentLeaveRequests',
            'upcomingBirthdays',
            'departmentStats',
            'attendanceTrend'
        ));
    }
}
