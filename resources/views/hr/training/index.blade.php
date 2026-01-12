@extends('layouts.adminlte')

@section('title', 'Training Records')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-graduation-cap mr-2"></i>Training Records</h1>
        @if(auth()->user()->hasPermission('hr.training.create'))
        <a href="{{ route('hr.training.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>Add Training
        </a>
        @endif
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Filters Card -->
    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-filter mr-2"></i>Filters</h3>
            <button type="button" class="btn btn-sm btn-modern btn-modern-primary" data-toggle="collapse" data-target="#filtersCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="card-body collapse show" id="filtersCollapse">
            <form method="GET" action="{{ route('hr.training.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Employee</label>
                    <select class="form-control form-control-modern" name="employee_id">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">Status</label>
                    <select class="form-control form-control-modern" name="status">
                        <option value="">All Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">From Date</label>
                    <input type="date" class="form-control form-control-modern" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">To Date</label>
                    <input type="date" class="form-control form-control-modern" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern btn-modern-primary w-100"><i class="fas fa-search mr-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Training Records Table -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Training Records List</h3>
            <span class="badge-modern badge-modern-primary">{{ $trainings->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Training Name</th>
                            <th>Provider</th>
                            <th>Period</th>
                            <th>Status</th>
                            <th style="width: 180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainings as $training)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $training->id }}</td>
                            <td>
                                <a href="{{ route('hr.employees.show', $training->employee) }}" class="font-weight-bold text-primary">
                                    {{ $training->employee->full_name }}
                                </a>
                            </td>
                            <td><strong class="text-primary">{{ $training->training_name }}</strong></td>
                            <td><span class="text-muted">{{ $training->training_provider ?? '-' }}</span></td>
                            <td>
                                @if($training->start_date && $training->end_date)
                                    <small>{{ $training->start_date->format('M d') }} - {{ $training->end_date->format('M d, Y') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusBadges = [
                                        'completed' => 'badge-modern-success',
                                        'in-progress' => 'badge-modern-info',
                                        'scheduled' => 'badge-modern-warning',
                                        'cancelled' => 'badge-modern-danger'
                                    ];
                                    $badgeClass = $statusBadges[$training->status] ?? 'badge-modern-info';
                                @endphp
                                <span class="badge-modern {{ $badgeClass }}">{{ ucfirst(str_replace('-', ' ', $training->status)) }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.training.show', $training) }}" class="btn-action btn-modern btn-modern-info" title="View"><i class="fas fa-eye"></i></a>
                                    @if(auth()->user()->hasPermission('hr.training.edit'))
                                    <a href="{{ route('hr.training.edit', $training) }}" class="btn-action btn-modern btn-modern-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr.training.delete'))
                                    <form action="{{ route('hr.training.destroy', $training) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this training record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-modern btn-modern-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-graduation-cap"></i>
                                    <p class="mb-3">No training records found</p>
                                    @if(auth()->user()->hasPermission('hr.training.create'))
                                    <a href="{{ route('hr.training.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Create First Training
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($trainings->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $trainings->links() }}
        </div>
        @endif
    </div>
</div>
@stop
