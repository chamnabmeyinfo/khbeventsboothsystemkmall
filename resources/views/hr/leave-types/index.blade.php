@extends('layouts.adminlte')

@section('title', 'Leave Types')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-calendar-check mr-2"></i>Leave Types</h1>
        @if(auth()->user()->hasPermission('hr.leaves.manage'))
        <a href="{{ route('hr.leave-types.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>Add Leave Type
        </a>
        @endif
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Leave Types Table -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Leave Types List</h3>
            <span class="badge-modern badge-modern-primary">{{ $leaveTypes->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Max Days/Year</th>
                            <th>Carry Forward</th>
                            <th>Paid</th>
                            <th>Status</th>
                            <th style="width: 180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveTypes as $type)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $type->id }}</td>
                            <td><strong class="text-primary">{{ $type->name }}</strong></td>
                            <td><span class="text-muted">{{ $type->code ?? '-' }}</span></td>
                            <td><span class="font-weight-semibold">{{ $type->max_days_per_year ?? 'Unlimited' }}</span></td>
                            <td>
                                <span class="badge-modern {{ $type->carry_forward ? 'badge-modern-success' : 'badge-modern-info' }}">
                                    {{ $type->carry_forward ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-modern {{ $type->is_paid ? 'badge-modern-success' : 'badge-modern-warning' }}">
                                    {{ $type->is_paid ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-modern {{ $type->is_active ? 'badge-modern-success' : 'badge-modern-info' }}">
                                    {{ $type->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.leave-types.show', $type) }}" class="btn-action btn-modern btn-modern-info" title="View"><i class="fas fa-eye"></i></a>
                                    @if(auth()->user()->hasPermission('hr.leaves.manage'))
                                    <a href="{{ route('hr.leave-types.edit', $type) }}" class="btn-action btn-modern btn-modern-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr.leaves.manage'))
                                    <form action="{{ route('hr.leave-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this leave type?');">
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
                                    <i class="fas fa-calendar-check"></i>
                                    <p class="mb-3">No leave types found</p>
                                    @if(auth()->user()->hasPermission('hr.leaves.manage'))
                                    <a href="{{ route('hr.leave-types.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Create First Leave Type
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
        @if($leaveTypes->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $leaveTypes->links() }}
        </div>
        @endif
    </div>
</div>
@stop
