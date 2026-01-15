@extends('layouts.adminlte')

@section('title', 'HR Dashboard')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-tachometer-alt mr-2"></i>HR Dashboard
        </h1>
        <div class="btn-group">
            <a href="{{ route('hr.employees.index') }}" class="btn btn-modern btn-modern-primary">
                <i class="fas fa-users mr-1"></i>Employees
            </a>
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-modern btn-modern-info">
                <i class="fas fa-calendar-alt mr-1"></i>Leave Requests
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards - Modern Design -->
    <div class="row mb-4">
        <!-- Total Employees -->
        <div class="col-lg-3 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-card-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-value">{{ $totalEmployees }}</div>
                <div class="stat-card-label">Total Employees</div>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-modern btn-modern-primary mt-3 w-100">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Active Employees -->
        <div class="col-lg-3 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-card-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-card-value">{{ $activeEmployees }}</div>
                <div class="stat-card-label">Active Employees</div>
                <a href="{{ route('hr.employees.index', ['status' => 'active']) }}" class="btn btn-sm btn-modern btn-modern-success mt-3 w-100">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- New Hires This Month -->
        <div class="col-lg-3 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-card-icon warning">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-card-value">{{ $newHiresThisMonth }}</div>
                <div class="stat-card-label">New Hires This Month</div>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-modern btn-modern-warning mt-3 w-100">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- On Leave -->
        <div class="col-lg-3 col-6 mb-3">
            <div class="stat-card">
                <div class="stat-card-icon danger">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <div class="stat-card-value">{{ $onLeave }}</div>
                <div class="stat-card-label">On Leave</div>
                <a href="{{ route('hr.employees.index', ['status' => 'on-leave']) }}" class="btn btn-sm btn-modern btn-modern-danger mt-3 w-100">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Info Boxes Row - Modern Design -->
    <div class="row mb-4">
        <!-- Attendance Today -->
        <div class="col-lg-3 col-6 mb-3">
            <div class="info-box-modern">
                <div class="d-flex align-items-center">
                    <div class="info-box-icon-modern" style="background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold text-muted mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Present Today</div>
                        <div class="h4 mb-2 font-weight-bold">{{ $presentToday }}/<small class="text-muted">{{ $totalEmployees }}</small></div>
                        <div class="progress-modern">
                            <div class="progress-bar-modern" style="width: {{ $attendanceRate }}%"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">{{ $attendanceRate }}% Attendance Rate</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Leaves -->
        <div class="col-lg-3 col-6 mb-3">
            <div class="info-box-modern">
                <div class="d-flex align-items-center">
                    <div class="info-box-icon-modern" style="background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold text-muted mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Pending Leaves</div>
                        <div class="h4 mb-2 font-weight-bold">{{ $pendingLeaves }}</div>
                        <a href="{{ route('hr.leaves.index', ['status' => 'pending']) }}" class="btn btn-sm btn-modern btn-modern-warning mt-2">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="col-lg-3 col-6 mb-3">
            <div class="info-box-modern">
                <div class="d-flex align-items-center">
                    <div class="info-box-icon-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold text-muted mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Pending Reviews</div>
                        <div class="h4 mb-2 font-weight-bold">{{ $pendingReviews }}</div>
                        <a href="{{ route('hr.performance.index', ['status' => 'draft']) }}" class="btn btn-sm btn-modern btn-modern-primary mt-2">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Training -->
        <div class="col-lg-3 col-6 mb-3">
            <div class="info-box-modern">
                <div class="d-flex align-items-center">
                    <div class="info-box-icon-modern" style="background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold text-muted mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Upcoming Training</div>
                        <div class="h4 mb-2 font-weight-bold">{{ $upcomingTraining }}</div>
                        <a href="{{ route('hr.training.index', ['status' => 'scheduled']) }}" class="btn btn-sm btn-modern btn-modern-success mt-2">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Recent Hires -->
        <div class="col-md-6 mb-3">
            <div class="card-modern">
                <div class="card-header-modern d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-user-plus mr-2"></i>Recent Hires</h3>
                    <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-modern btn-modern-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Hire Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentHires as $employee)
                                <tr>
                                    <td>
                                        <a href="{{ route('hr.employees.show', $employee) }}" class="font-weight-bold text-primary">
                                            {{ $employee->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $employee->department->name ?? '-' }}</td>
                                    <td>{{ $employee->position->name ?? '-' }}</td>
                                    <td><span class="badge-modern badge-modern-info">{{ $employee->hire_date->format('M d, Y') }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-user-slash"></i>
                                            <p class="mb-0">No recent hires</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Leave Requests -->
        <div class="col-md-6 mb-3">
            <div class="card-modern">
                <div class="card-header-modern d-flex justify-content-between align-items-center">
                    <h3><i class="fas fa-calendar-alt mr-2"></i>Recent Leave Requests</h3>
                    <a href="{{ route('hr.leaves.index') }}" class="btn btn-sm btn-modern btn-modern-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Leave Type</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeaveRequests as $leave)
                                <tr>
                                    <td>
                                        <a href="{{ route('hr.leaves.show', $leave) }}" class="font-weight-bold text-primary">
                                            {{ $leave->employee->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $leave->leaveType->name }}</td>
                                    <td><small>{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}</small></td>
                                    <td>
                                        @php
                                            $statusBadges = [
                                                'pending' => 'badge-modern-warning',
                                                'approved' => 'badge-modern-success',
                                                'rejected' => 'badge-modern-danger',
                                                'cancelled' => 'badge-modern-info'
                                            ];
                                            $badgeClass = $statusBadges[$leave->status] ?? 'badge-modern-info';
                                        @endphp
                                        <span class="badge-modern {{ $badgeClass }}">{{ ucfirst($leave->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-calendar-times"></i>
                                            <p class="mb-0">No leave requests</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Department Statistics -->
        <div class="col-md-6 mb-3">
            <div class="card-modern">
                <div class="card-header-modern">
                    <h3><i class="fas fa-building mr-2"></i>Department Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-modern table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th class="text-right">Employees</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departmentStats as $dept)
                                <tr>
                                    <td class="font-weight-bold">{{ $dept->name }}</td>
                                    <td class="text-right">
                                        <span class="badge-modern badge-modern-primary">{{ $dept->employees_count }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-building"></i>
                                            <p class="mb-0">No departments</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Birthdays -->
        <div class="col-md-6 mb-3">
            <div class="card-modern">
                <div class="card-header-modern">
                    <h3><i class="fas fa-birthday-cake mr-2"></i>Upcoming Birthdays</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-modern table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingBirthdays as $employee)
                                <tr>
                                    <td>
                                        <a href="{{ route('hr.employees.show', $employee) }}" class="font-weight-bold text-primary">
                                            {{ $employee->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $employee->department->name ?? '-' }}</td>
                                    <td><span class="badge-modern badge-modern-warning">{{ $employee->date_of_birth->format('M d') }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-birthday-cake"></i>
                                            <p class="mb-0">No upcoming birthdays</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Trend Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card-modern">
                <div class="card-header-modern">
                    <h3><i class="fas fa-chart-line mr-2"></i>Attendance Trend (Last 6 Months)</h3>
                </div>
                <div class="card-body p-4">
                    <canvas id="attendanceChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
    // Attendance Trend Chart
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceData = @json($attendanceTrend);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: attendanceData.map(d => d.month),
            datasets: [{
                label: 'Present',
                data: attendanceData.map(d => d.present),
                borderColor: 'rgb(28, 200, 138)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(28, 200, 138)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }, {
                label: 'Absent',
                data: attendanceData.map(d => d.absent),
                borderColor: 'rgb(231, 74, 59)',
                backgroundColor: 'rgba(231, 74, 59, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(231, 74, 59)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 12
                    },
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
