@extends('layouts.adminlte')

@section('title', 'Leave Calendar')

@php
use Carbon\Carbon;
@endphp

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
<style>
    .leave-calendar-filters {
        /* Calendar-specific mobile styles */
    }
    
    @media (max-width: 768px) {
        .calendar-day {
            min-height: 80px;
        }
        
        .table-responsive {
            border: none;
        }
    }
</style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-calendar-alt mr-2"></i>Leave Calendar</h1>
        <div class="btn-group">
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-modern btn-modern-info">
                <i class="fas fa-list mr-1"></i>Leave Requests
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-filter mr-2"></i>Filters</h3>
            <button type="button" class="btn btn-sm btn-modern btn-modern-primary" data-toggle="collapse" data-target="#filtersCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="card-body collapse show" id="filtersCollapse">
            <form method="GET" action="{{ route('hr.leave-calendar.index') }}" class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold">View</label>
                        <select name="view" class="form-control form-control-modern">
                            <option value="month" {{ $view == 'month' ? 'selected' : '' }}>Month</option>
                            <option value="week" {{ $view == 'week' ? 'selected' : '' }}>Week</option>
                            <option value="day" {{ $view == 'day' ? 'selected' : '' }}>Day</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold">Date</label>
                        <input type="date" name="date" class="form-control form-control-modern" value="{{ $selectedDate->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold">Department</label>
                        <select name="department_id" class="form-control form-control-modern">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold">Employee</label>
                        <select name="employee_id" class="form-control form-control-modern">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="font-weight-bold">Status</label>
                        <select name="status" class="form-control form-control-modern">
                            <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-modern btn-modern-primary btn-block">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="info-box-modern">
                <div class="d-flex align-items-center">
                    <div class="info-box-icon-modern" style="background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold text-muted mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Leaves</div>
                        <div class="h4 mb-0 font-weight-bold">{{ $stats['total_leaves'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="info-box-modern">
                <div class="d-flex align-items-center">
                    <div class="info-box-icon-modern" style="background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold text-muted mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Days</div>
                        <div class="h4 mb-0 font-weight-bold">{{ number_format($stats['total_days'], 1) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="info-box-modern">
                <div class="d-flex align-items-center">
                    <div class="info-box-icon-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="font-weight-bold text-muted mb-1" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Employees on Leave</div>
                        <div class="h4 mb-0 font-weight-bold">{{ $stats['unique_employees'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3>
                <i class="fas fa-calendar mr-2"></i>
                {{ $view == 'month' ? $selectedDate->format('F Y') : ($view == 'week' ? 'Week of ' . $startDate->format('M d') : $selectedDate->format('M d, Y')) }}
            </h3>
            <div class="btn-group">
                <a href="{{ route('hr.leave-calendar.index', array_merge(request()->all(), ['date' => $startDate->copy()->subMonth()->format('Y-m-d')])) }}" 
                   class="btn btn-sm btn-modern btn-modern-info">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
                <a href="{{ route('hr.leave-calendar.index', array_merge(request()->all(), ['date' => \Carbon\Carbon::today()->format('Y-m-d')])) }}" 
                   class="btn btn-sm btn-modern btn-modern-primary">
                    Today
                </a>
                <a href="{{ route('hr.leave-calendar.index', array_merge(request()->all(), ['date' => $startDate->copy()->addMonth()->format('Y-m-d')])) }}" 
                   class="btn btn-sm btn-modern btn-modern-info">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            @if($view == 'month')
                <!-- Month View -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 14.28%;">Sun</th>
                                <th class="text-center" style="width: 14.28%;">Mon</th>
                                <th class="text-center" style="width: 14.28%;">Tue</th>
                                <th class="text-center" style="width: 14.28%;">Wed</th>
                                <th class="text-center" style="width: 14.28%;">Thu</th>
                                <th class="text-center" style="width: 14.28%;">Fri</th>
                                <th class="text-center" style="width: 14.28%;">Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $currentDate = $startDate->copy();
                                $firstDayOfWeek = $currentDate->copy()->startOfWeek();
                                $lastDayOfMonth = $endDate->copy();
                                $weeks = ceil(($firstDayOfWeek->diffInDays($lastDayOfMonth) + $firstDayOfWeek->dayOfWeek + 1) / 7);
                            @endphp
                            @for($week = 0; $week < $weeks; $week++)
                            <tr>
                                @for($day = 0; $day < 7; $day++)
                                    @php
                                        $date = $firstDayOfWeek->copy()->addDays($week * 7 + $day);
                                        $dateKey = $date->format('Y-m-d');
                                        $isCurrentMonth = $date->month == $selectedDate->month;
                                        $isToday = $date->isToday();
                                        $dayLeaves = $calendarLeaves[$dateKey] ?? [];
                                    @endphp
                                    <td class="calendar-day {{ !$isCurrentMonth ? 'text-muted bg-light' : '' }} {{ $isToday ? 'bg-info' : '' }}" 
                                        style="height: 100px; vertical-align: top; padding: 5px;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <strong class="{{ $isToday ? 'text-white' : '' }}">{{ $date->day }}</strong>
                                            @if($isToday)
                                                <span class="badge badge-light">Today</span>
                                            @endif
                                        </div>
                                        <div class="mt-1" style="max-height: 60px; overflow-y: auto;">
                                            @foreach($dayLeaves as $leave)
                                                <div class="badge badge-sm mb-1 d-block text-left" 
                                                     style="background-color: {{ $leave->leaveType->name == 'Annual Leave' ? '#28a745' : ($leave->leaveType->name == 'Sick Leave' ? '#dc3545' : '#17a2b8') }};
                                                            font-size: 0.7rem; padding: 2px 4px; cursor: pointer;"
                                                     title="{{ $leave->employee->full_name }} - {{ $leave->leaveType->name }} ({{ $leave->total_days }} days)"
                                                     onclick="window.location='{{ route('hr.leaves.show', $leave) }}'">
                                                    {{ Str::limit($leave->employee->first_name, 8) }} - {{ Str::limit($leave->leaveType->name, 10) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            @elseif($view == 'week')
                <!-- Week View -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Employee</th>
                                @for($i = 0; $i < 7; $i++)
                                    @php $date = $startDate->copy()->addDays($i); @endphp
                                    <th class="text-center {{ $date->isToday() ? 'bg-info text-white' : '' }}">
                                        {{ $date->format('D') }}<br>
                                        <small>{{ $date->format('M d') }}</small>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaves->groupBy('employee_id') as $employeeId => $employeeLeaves)
                                @php $employee = $employeeLeaves->first()->employee; @endphp
                                <tr>
                                    <td><strong>{{ $employee->full_name }}</strong></td>
                                    @for($i = 0; $i < 7; $i++)
                                        @php 
                                            $date = $startDate->copy()->addDays($i);
                                            $dateKey = $date->format('Y-m-d');
                                            $dayLeave = $employeeLeaves->first(function($l) use ($date) {
                                                return $date->between($l->start_date, $l->end_date);
                                            });
                                        @endphp
                                        <td class="text-center">
                                            @if($dayLeave)
                                                <span class="badge badge-primary" 
                                                      title="{{ $dayLeave->leaveType->name }}"
                                                      onclick="window.location='{{ route('hr.leaves.show', $dayLeave) }}'"
                                                      style="cursor: pointer;">
                                                    {{ $dayLeave->leaveType->name }}
                                                </span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <!-- Day View -->
                <div class="row">
                    <div class="col-md-12">
                        <h4>{{ $selectedDate->format('l, F d, Y') }}</h4>
                        @if(isset($calendarLeaves[$selectedDate->format('Y-m-d')]))
                            <div class="list-group">
                                @foreach($calendarLeaves[$selectedDate->format('Y-m-d')] as $leave)
                                <a href="{{ route('hr.leaves.show', $leave) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $leave->employee->full_name }}</h5>
                                        <span class="badge badge-primary">{{ $leave->leaveType->name }}</span>
                                    </div>
                                    <p class="mb-1">
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                        ({{ $leave->total_days }} days)
                                    </p>
                                    <small>{{ $leave->employee->department->name ?? 'N/A' }}</small>
                                </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No leaves scheduled for this day.</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .calendar-day {
        position: relative;
    }
    .calendar-day:hover {
        background-color: #f8f9fa !important;
    }
    .badge-sm {
        font-size: 0.7rem;
    }
</style>
@stop
