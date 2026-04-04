@extends('layouts.admin')

@section('title', 'HR Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/hr-looker.css') }}?v=2">
@include('hr._mobile-styles')
@endpush

@push('body-class', 'ios-dashboard-mode')

@section('content')
<div class="looker-dashboard hr-dashboard-page">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>HR Dashboard</h1>
            <p>Workforce overview, attendance, and leave activity.</p>
        </div>
        <div class="looker-actions">
            <a href="{{ route('hr.employees.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-users"></i> Employees
            </a>
            <a href="{{ route('hr.attendance.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-clock"></i> Attendance
            </a>
            <a href="{{ route('hr.leaves.index') }}" class="action-btn action-btn-primary">
                <i class="fas fa-calendar-alt"></i> Leave Requests
            </a>
        </div>
    </header>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker">
            <div class="kpi-top">
                <div class="kpi-title">Total Employees</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="kpi-value-looker">{{ $totalEmployees }}</div>
            <div class="kpi-bottom trend-neutral">
                <a href="{{ route('hr.employees.index') }}" class="action-btn action-btn-secondary">Open directory</a>
            </div>
        </div>
        <div class="kpi-card-looker success">
            <div class="kpi-top">
                <div class="kpi-title">Active</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-user-check"></i></div>
            </div>
            <div class="kpi-value-looker">{{ $activeEmployees }}</div>
            <div class="kpi-bottom trend-positive">
                <a href="{{ route('hr.employees.index', ['status' => 'active']) }}" class="action-btn action-btn-secondary">Active only</a>
            </div>
        </div>
        <div class="kpi-card-looker warning">
            <div class="kpi-top">
                <div class="kpi-title">New Hires (Month)</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-user-plus"></i></div>
            </div>
            <div class="kpi-value-looker">{{ $newHiresThisMonth }}</div>
            <div class="kpi-bottom trend-warning">This calendar month</div>
        </div>
        <div class="kpi-card-looker purple">
            <div class="kpi-top">
                <div class="kpi-title">On Leave</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-calendar-times"></i></div>
            </div>
            <div class="kpi-value-looker">{{ $onLeave }}</div>
            <div class="kpi-bottom trend-neutral">
                <a href="{{ route('hr.employees.index', ['status' => 'on-leave']) }}" class="action-btn action-btn-secondary">On leave list</a>
            </div>
        </div>
    </div>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker">
            <div class="kpi-top">
                <div class="kpi-title">Present Today</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="kpi-value-looker">{{ $presentToday }}<span class="hr-kpi-sub">/ {{ $totalEmployees }}</span></div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: {{ min(100, max(0, (float) $attendanceRate)) }}%; background: var(--accent-green);"></div>
            </div>
            <div class="kpi-bottom trend-neutral">{{ $attendanceRate }}% attendance rate</div>
        </div>
        <div class="kpi-card-looker warning">
            <div class="kpi-top">
                <div class="kpi-title">Pending Leaves</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-calendar-check"></i></div>
            </div>
            <div class="kpi-value-looker">{{ $pendingLeaves }}</div>
            <div class="kpi-bottom">
                <a href="{{ route('hr.leaves.index', ['status' => 'pending']) }}" class="action-btn action-btn-primary">Review</a>
            </div>
        </div>
        <div class="kpi-card-looker purple">
            <div class="kpi-top">
                <div class="kpi-title">Pending Reviews</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-star"></i></div>
            </div>
            <div class="kpi-value-looker">{{ $pendingReviews }}</div>
            <div class="kpi-bottom">
                <a href="{{ route('hr.performance.index', ['status' => 'draft']) }}" class="action-btn action-btn-secondary">Open</a>
            </div>
        </div>
        <div class="kpi-card-looker success">
            <div class="kpi-top">
                <div class="kpi-title">Upcoming Training</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-graduation-cap"></i></div>
            </div>
            <div class="kpi-value-looker">{{ $upcomingTraining }}</div>
            <div class="kpi-bottom">
                <a href="{{ route('hr.training.index', ['status' => 'scheduled']) }}" class="action-btn action-btn-secondary">Schedule</a>
            </div>
        </div>
    </div>

    <div class="data-canvas-grid">
        <div class="col-span-6 canvas-panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-user-plus"></i> Recent Hires</h2>
                <a href="{{ route('hr.employees.index') }}" class="action-btn action-btn-secondary action-btn-icon" title="All employees"><i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
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
                            <td><a href="{{ route('hr.employees.show', $employee) }}" class="text-link-strong">{{ $employee->full_name }}</a></td>
                            <td>{{ $employee->department->name ?? '—' }}</td>
                            <td>{{ $employee->position->name ?? '—' }}</td>
                            <td><span class="status-badge status-badge-blue">{{ $employee->hire_date->format('M d, Y') }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="hr-empty-cell"><i class="fas fa-user-slash"></i><p class="mb-0">No recent hires</p></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-span-6 canvas-panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-calendar-alt"></i> Recent Leave Requests</h2>
                <a href="{{ route('hr.leaves.index') }}" class="action-btn action-btn-secondary action-btn-icon" title="All requests"><i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Period</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLeaveRequests as $leave)
                        <tr>
                            <td><a href="{{ route('hr.leaves.show', $leave) }}" class="text-link-strong">{{ $leave->employee->full_name }}</a></td>
                            <td>{{ $leave->leaveType->name }}</td>
                            <td><span class="text-muted small">{{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}</span></td>
                            <td>
                                @php
                                    $leaveBadges = [
                                        'pending' => 'status-badge-orange',
                                        'approved' => 'status-badge-green',
                                        'rejected' => 'status-badge-red',
                                        'cancelled' => 'status-badge-neutral',
                                    ];
                                    $lb = $leaveBadges[$leave->status] ?? 'status-badge-neutral';
                                @endphp
                                <span class="status-badge {{ $lb }}">{{ ucfirst($leave->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="hr-empty-cell"><i class="fas fa-calendar-times"></i><p class="mb-0">No leave requests</p></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="data-canvas-grid">
        <div class="col-span-6 canvas-panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-building"></i> Department Statistics</h2>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th class="text-end">Employees</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departmentStats as $dept)
                        <tr>
                            <td class="fw-bold">{{ $dept->name }}</td>
                            <td class="text-end"><span class="status-badge status-badge-blue">{{ $dept->employees_count }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="hr-empty-cell"><i class="fas fa-building"></i><p class="mb-0">No departments</p></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-span-6 canvas-panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-birthday-cake"></i> Upcoming Birthdays</h2>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
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
                            <td><a href="{{ route('hr.employees.show', $employee) }}" class="text-link-strong">{{ $employee->full_name }}</a></td>
                            <td>{{ $employee->department->name ?? '—' }}</td>
                            <td><span class="status-badge status-badge-orange">{{ $employee->date_of_birth->format('M d') }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="hr-empty-cell"><i class="fas fa-birthday-cake"></i><p class="mb-0">No upcoming birthdays</p></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="data-canvas-grid">
        <div class="col-span-12 canvas-panel">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-chart-line"></i> Attendance Trend (Last 6 Months)</h2>
            </div>
            <div class="p-2">
                <canvas id="attendanceChart" height="80" aria-label="Attendance trend chart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    try {
        $cdnSettings = \App\Models\Setting::getCDNSettings();
        $useCDN = $cdnSettings['use_cdn'] ?? true;
    } catch (\Exception $e) {
        $useCDN = true;
    }
@endphp
@if($useCDN)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@else
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
@endif
<script>
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        const attendanceData = @json($attendanceTrend);
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: attendanceData.map(d => d.month),
                datasets: [{
                    label: 'Present',
                    data: attendanceData.map(d => d.present),
                    borderColor: 'rgb(52, 199, 89)',
                    backgroundColor: 'rgba(52, 199, 89, 0.12)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(52, 199, 89)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }, {
                    label: 'Absent',
                    data: attendanceData.map(d => d.absent),
                    borderColor: 'rgb(255, 59, 48)',
                    backgroundColor: 'rgba(255, 59, 48, 0.08)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(255, 59, 48)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { usePointStyle: true, padding: 16, font: { size: 12, weight: '600' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.82)',
                        padding: 12,
                        cornerRadius: 10
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.06)' },
                        ticks: { font: { size: 11, weight: '600' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '600' } }
                    }
                }
            }
        });
    }
</script>
@endpush
