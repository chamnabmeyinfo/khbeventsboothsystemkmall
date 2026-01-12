@extends('layouts.adminlte')

@section('title', 'My Attendance')

@push('styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <h1 class="m-0"><i class="fas fa-clock mr-2"></i>My Attendance</h1>
@stop

@section('content')
<div class="container-fluid">
    <!-- Statistics -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Present Days</span>
                    <span class="info-box-number">{{ $stats['present'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Absent Days</span>
                    <span class="info-box-number">{{ $stats['absent'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Late Days</span>
                    <span class="info-box-number">{{ $stats['late'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Days</span>
                    <span class="info-box-number">{{ $stats['total_days'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-1"></i>Filters</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('employee.attendance') }}" class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Attendance Records</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Total Hours</th>
                        <th>Overtime</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendance as $record)
                    <tr>
                        <td>{{ $record->date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $record->status == 'present' ? 'success' : ($record->status == 'late' ? 'warning' : ($record->status == 'absent' ? 'danger' : 'info')) }}">
                                {{ ucfirst(str_replace('-', ' ', $record->status)) }}
                            </span>
                        </td>
                        <td>{{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : '-' }}</td>
                        <td>{{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') : '-' }}</td>
                        <td>{{ $record->total_hours ? number_format($record->total_hours, 2) . ' hrs' : '-' }}</td>
                        <td>{{ $record->overtime_hours ? number_format($record->overtime_hours, 2) . ' hrs' : '-' }}</td>
                        <td>{{ Str::limit($record->notes, 30) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No attendance records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendance->hasPages())
        <div class="card-footer">{{ $attendance->links() }}</div>
        @endif
    </div>
</div>
@stop
