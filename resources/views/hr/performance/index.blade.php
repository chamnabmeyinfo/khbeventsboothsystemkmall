@extends('layouts.adminlte')

@section('title', 'Performance Reviews')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-star mr-2"></i>Performance Reviews</h1>
        @if(auth()->user()->hasPermission('hr.performance.create'))
        <a href="{{ route('hr.performance.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>New Review
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
            <form method="GET" action="{{ route('hr.performance.index') }}" class="row g-3">
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
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">Year</label>
                    <input type="number" class="form-control form-control-modern" name="year" value="{{ request('year', date('Y')) }}" placeholder="Year">
                </div>
                <div class="col-md-5 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern btn-modern-primary"><i class="fas fa-search mr-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Reviews Table -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Performance Reviews List</h3>
            <span class="badge-modern badge-modern-primary">{{ $reviews->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Reviewer</th>
                            <th>Period</th>
                            <th>Review Date</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th style="width: 180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $review->id }}</td>
                            <td>
                                <a href="{{ route('hr.employees.show', $review->employee) }}" class="font-weight-bold text-primary">
                                    {{ $review->employee->full_name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('hr.employees.show', $review->reviewer) }}" class="font-weight-semibold">
                                    {{ $review->reviewer->full_name }}
                                </a>
                            </td>
                            <td><small>{{ $review->review_period_start->format('M d') }} - {{ $review->review_period_end->format('M d, Y') }}</small></td>
                            <td><span class="text-muted">{{ $review->review_date->format('M d, Y') }}</span></td>
                            <td>
                                @if($review->overall_rating)
                                    <span class="badge-modern badge-modern-primary">{{ number_format($review->overall_rating, 2) }}/5.00</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-modern {{ $review->status == 'completed' ? 'badge-modern-success' : 'badge-modern-warning' }}">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.performance.show', $review) }}" class="btn-action btn-modern btn-modern-info" title="View"><i class="fas fa-eye"></i></a>
                                    @if(auth()->user()->hasPermission('hr.performance.edit'))
                                    <a href="{{ route('hr.performance.edit', $review) }}" class="btn-action btn-modern btn-modern-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr.performance.delete'))
                                    <form action="{{ route('hr.performance.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this performance review?');">
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
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-star"></i>
                                    <p class="mb-3">No performance reviews found</p>
                                    @if(auth()->user()->hasPermission('hr.performance.create'))
                                    <a href="{{ route('hr.performance.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Create First Review
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
        @if($reviews->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>
@stop
