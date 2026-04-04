<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HR\Attendance;
use App\Models\HR\Department;
use App\Models\HR\Employee;
use App\Models\HR\EmployeeTraining;
use App\Models\HR\LeaveRequest;
use App\Models\HR\PerformanceReview;
use App\Models\HR\Position;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class HRDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $thisYear = Carbon::now()->year;

        $hasEmployeeAccountKind = Schema::hasColumn('employees', 'account_kind');

        // Employee statistics: production (real) vs demo/test profiles
        $demoEmployeesCount = $hasEmployeeAccountKind ? Employee::demoStaff()->count() : 0;
        $totalEmployees = Employee::when($hasEmployeeAccountKind, fn ($q) => $q->realStaff())->count();
        $activeEmployees = Employee::when($hasEmployeeAccountKind, fn ($q) => $q->realStaff())->where('status', 'active')->count();
        $newHiresThisMonth = Employee::when($hasEmployeeAccountKind, fn ($q) => $q->realStaff())
            ->where('hire_date', '>=', $thisMonth)
            ->count();
        $onLeave = Employee::when($hasEmployeeAccountKind, fn ($q) => $q->realStaff())->where('status', 'on-leave')->count();

        // Department Statistics
        $totalDepartments = Department::where('is_active', true)->count();
        $totalPositions = Position::where('is_active', true)->count();

        // Attendance Statistics (real staff only when account_kind exists)
        $todayAttendance = Attendance::where('date', $today)
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $presentToday = Attendance::where('date', $today)->where('status', 'present')
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $absentToday = Attendance::where('date', $today)->where('status', 'absent')
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $lateToday = Attendance::where('date', $today)->where('status', 'late')
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $attendanceRate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0;

        // Leave Statistics
        $pendingLeaves = LeaveRequest::where('status', 'pending')
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $approvedLeavesThisMonth = LeaveRequest::where('status', 'approved')
            ->whereMonth('start_date', Carbon::now()->month)
            ->whereYear('start_date', $thisYear)
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $onLeaveToday = LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();

        // Performance Statistics
        $pendingReviews = PerformanceReview::where('status', 'draft')
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $completedReviewsThisYear = PerformanceReview::where('status', 'completed')
            ->whereYear('review_date', $thisYear)
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $averageRating = PerformanceReview::where('status', 'completed')
            ->whereYear('review_date', $thisYear)
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->avg('overall_rating');

        // Training Statistics
        $upcomingTraining = EmployeeTraining::where('status', 'scheduled')
            ->where('start_date', '>=', $today)
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $ongoingTraining = EmployeeTraining::where('status', 'in-progress')
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();
        $completedTrainingThisYear = EmployeeTraining::where('status', 'completed')
            ->whereYear('end_date', $thisYear)
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->count();

        // Recent Activities
        $recentHires = Employee::with(['department', 'position'])
            ->when($hasEmployeeAccountKind, fn ($q) => $q->realStaff())
            ->orderBy('hire_date', 'desc')
            ->limit(5)
            ->get();

        $recentLeaveRequests = LeaveRequest::with(['employee', 'leaveType'])
            ->when($hasEmployeeAccountKind, fn ($q) => $q->whereHas('employee', fn ($eq) => $eq->realStaff()))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $upcomingBirthdays = Employee::when($hasEmployeeAccountKind, fn ($q) => $q->realStaff())
            ->whereMonth('date_of_birth', Carbon::now()->month)
            ->whereDay('date_of_birth', '>=', Carbon::now()->day)
            ->orderByRaw('DAY(date_of_birth)')
            ->limit(5)
            ->get();

        // Department-wise employee count
        $departmentStats = Department::withCount(['employees' => function ($q) use ($hasEmployeeAccountKind) {
            if ($hasEmployeeAccountKind) {
                $q->realStaff();
            }
        }])
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

            $presentQuery = Attendance::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'present');
            $absentQuery = Attendance::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'absent');
            if ($hasEmployeeAccountKind) {
                $presentQuery->whereHas('employee', fn ($eq) => $eq->realStaff());
                $absentQuery->whereHas('employee', fn ($eq) => $eq->realStaff());
            }

            $attendanceTrend[] = [
                'month' => $month->format('M Y'),
                'present' => $presentQuery->count(),
                'absent' => $absentQuery->count(),
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
            'attendanceTrend',
            'demoEmployeesCount',
            'hasEmployeeAccountKind'
        ));
    }
}
