@extends('layouts.adminlte')

@section('title', 'Attendance')
@section('page-title', 'Attendance')
@section('breadcrumb', 'Human Resources')

@push('body-class', 'ios-dashboard-mode')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/hr-looker.css') }}?v=1">
@include('hr._mobile-styles')
@endpush

@section('content')
<div class="looker-dashboard hr-index-page">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Attendance</h1>
            <p>Filter records by employee, date range, and status.</p>
        </div>
        <div class="looker-actions">
            @if(auth()->user()->hasPermission('hr.attendance.create'))
            <a href="{{ route('hr.attendance.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus"></i> Add record
            </a>
            @endif
        </div>
    </header>

    <div class="canvas-panel mb-4">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-filter"></i> Filters</h2>
            <button type="button" class="action-btn action-btn-secondary action-btn-icon" data-toggle="collapse" data-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse" title="Toggle filters">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show hr-filters-body" id="filtersCollapse">
            <form method="GET" action="{{ route('hr.attendance.index') }}" class="container-fluid px-0">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label font-weight-bold">Employee</label>
                        <select class="form-control" name="employee_id">
                            <option value="">All employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">From</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">To</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select class="form-control" name="status">
                            <option value="">All</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                            <option value="half-day" {{ request('status') == 'half-day' ? 'selected' : '' }}>Half day</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="action-btn action-btn-primary w-100 justify-content-center"><i class="fas fa-search mr-1"></i> Apply</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="canvas-panel hr-panel-flush">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-list"></i> Records</h2>
            <span class="status-badge status-badge-blue">{{ $attendances->total() }} total</span>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Hours</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $att)
                    <tr>
                        <td class="text-muted small font-weight-bold">{{ $att->id }}</td>
                        <td><a href="{{ route('hr.employees.show', $att->employee) }}" class="text-link-strong">{{ $att->employee->full_name }}</a></td>
                        <td>{{ $att->date->format('M d, Y') }}</td>
                        <td class="text-muted small">{{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('H:i') : '—' }}</td>
                        <td class="text-muted small">{{ $att->check_out_time ? \Carbon\Carbon::parse($att->check_out_time)->format('H:i') : '—' }}</td>
                        <td>{{ $att->total_hours ?? '—' }}</td>
                        <td>
                            @php
                                $sb = [
                                    'present' => 'status-badge-green',
                                    'absent' => 'status-badge-red',
                                    'late' => 'status-badge-orange',
                                    'half-day' => 'status-badge-blue',
                                ];
                                $bc = $sb[$att->status] ?? 'status-badge-neutral';
                            @endphp
                            <span class="status-badge {{ $bc }}">{{ ucfirst(str_replace('-', ' ', $att->status)) }}</span>
                        </td>
                        <td>
                            <div class="hr-table-actions">
                                <a href="{{ route('hr.attendance.show', $att) }}" class="action-btn action-btn-secondary action-btn-icon" title="View"><i class="fas fa-eye"></i></a>
                                @if(auth()->user()->hasPermission('hr.attendance.edit'))
                                <a href="{{ route('hr.attendance.edit', $att) }}" class="action-btn action-btn-secondary action-btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                @endif
                                @if(auth()->user()->hasPermission('hr.attendance.delete'))
                                <form action="{{ route('hr.attendance.destroy', $att) }}" method="POST" onsubmit="return confirm('Delete this attendance record?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-danger-soft action-btn-icon" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="hr-empty-cell">
                            <i class="fas fa-clock"></i>
                            <p class="mb-2">No attendance records</p>
                            @if(auth()->user()->hasPermission('hr.attendance.create'))
                            <a href="{{ route('hr.attendance.create') }}" class="action-btn action-btn-primary"><i class="fas fa-plus"></i> Create first record</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
        <div class="hr-panel-footer">{{ $attendances->links() }}</div>
        @endif
    </div>
</div>
@stop
