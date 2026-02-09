@extends('layouts.adminlte')

@section('title', 'My Dashboard')

@push('styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <h1 class="m-0"><i class="fas fa-user-circle mr-2"></i>My Dashboard</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(!$employee)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Employee profile not found. Please contact HR.
        </div>
    @else
        <!-- Welcome Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            @if(($employeeAvatarUrl = \App\Helpers\AssetHelper::imageUrl($employee->avatar ? 'storage/' . ltrim($employee->avatar, '/') : null)))
                                <img src="{{ $employeeAvatarUrl }}" alt="Avatar" class="img-circle mr-3" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.onerror=null; this.style.display='none';">
                            @else
                                <div class="img-circle bg-primary d-flex align-items-center justify-content-center mr-3" style="width: 80px; height: 80px;">
                                    <span class="text-white" style="font-size: 32px; font-weight: bold;">{{ strtoupper(substr($employee->first_name, 0, 1)) }}</span>
                                </div>
                            @endif
                            <div>
                                <h3 class="mb-0">Welcome, {{ $employee->full_name }}!</h3>
                                <p class="mb-0 text-muted">
                                    {{ $employee->position->name ?? 'N/A' }} | {{ $employee->department->name ?? 'N/A' }}
                                </p>
                                <small class="text-muted">Employee Code: {{ $employee->employee_code }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row">
            <!-- Leave Balance -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $leaveBalances->sum('balance') }}</h3>
                        <p>Total Leave Balance</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <a href="{{ route('employee.leaves') }}" class="small-box-footer">
                        View Details <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Pending Leaves -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $pendingLeaves }}</h3>
                        <p>Pending Leave Requests</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('employee.leaves') }}" class="small-box-footer">
                        View Details <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Attendance This Month -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $presentDays }}/{{ $totalWorkingDays }}</h3>
                        <p>Present Days (This Month)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ route('employee.attendance') }}" class="small-box-footer">
                        View Details <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Upcoming Reviews -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $upcomingReviews->count() }}</h3>
                        <p>Upcoming Reviews</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <a href="{{ route('hr.performance.index') }}" class="small-box-footer">
                        View Details <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Today's Attendance -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Today's Attendance</h3>
                    </div>
                    <div class="card-body">
                        @if($todayAttendance)
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Date:</th>
                                    <td>{{ $todayAttendance->date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge badge-{{ $todayAttendance->status == 'present' ? 'success' : ($todayAttendance->status == 'late' ? 'warning' : 'danger') }}">
                                            {{ ucfirst(str_replace('-', ' ', $todayAttendance->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                @if($todayAttendance->check_in_time)
                                <tr>
                                    <th>Check In:</th>
                                    <td>{{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('h:i A') }}</td>
                                </tr>
                                @endif
                                @if($todayAttendance->check_out_time)
                                <tr>
                                    <th>Check Out:</th>
                                    <td>{{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('h:i A') }}</td>
                                </tr>
                                @endif
                                @if($todayAttendance->total_hours)
                                <tr>
                                    <th>Total Hours:</th>
                                    <td><strong>{{ number_format($todayAttendance->total_hours, 2) }} hrs</strong></td>
                                </tr>
                                @endif
                            </table>
                        @else
                            <p class="text-muted mb-0">No attendance record for today.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Leave Balances -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Leave Balances ({{ Carbon::now()->year }})</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaveBalances as $balance)
                                <tr>
                                    <td>{{ $balance->leaveType->name }}</td>
                                    <td class="text-right">
                                        <strong>{{ number_format($balance->balance, 1) }} days</strong>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">No leave balances found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('employee.leaves') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus mr-1"></i>Apply for Leave
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Leave Requests -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list mr-2"></i>Recent Leave Requests</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Period</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeaves as $leave)
                                <tr>
                                    <td>{{ $leave->leaveType->name }}</td>
                                    <td>
                                        {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                    </td>
                                    <td>{{ $leave->days }}</td>
                                    <td>
                                        <span class="badge badge-{{ $leave->status == 'approved' ? 'success' : ($leave->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No leave requests found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('employee.leaves') }}" class="btn btn-sm btn-secondary">View All</a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Training -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-graduation-cap mr-2"></i>Upcoming Training</h3>
                    </div>
                    <div class="card-body p-0">
                        @forelse($upcomingTraining as $training)
                        <div class="p-3 border-bottom">
                            <h5 class="mb-1">{{ $training->training_name }}</h5>
                            <p class="mb-1 text-muted small">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $training->start_date->format('M d, Y') }}
                                @if($training->end_date)
                                    - {{ $training->end_date->format('M d, Y') }}
                                @endif
                            </p>
                            @if($training->training_provider)
                            <p class="mb-0 text-muted small">
                                <i class="fas fa-building mr-1"></i>{{ $training->training_provider }}
                            </p>
                            @endif
                        </div>
                        @empty
                        <div class="p-3 text-center text-muted">
                            No upcoming training scheduled
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@stop
