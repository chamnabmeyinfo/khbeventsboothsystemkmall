@extends('layouts.adminlte')

@section('title', 'Employee Documents')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-file-alt mr-2"></i>Employee Documents</h1>
        @if(auth()->user()->hasPermission('hr.documents.create'))
        <a href="{{ route('hr.documents.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>Upload Document
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
            <form method="GET" action="{{ route('hr.documents.index') }}" class="row g-3">
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
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Document Type</label>
                    <select class="form-control form-control-modern" name="document_type">
                        <option value="">All Types</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}" {{ request('document_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <label class="form-check-label">
                        <input type="checkbox" name="expiring_soon" value="1" {{ request('expiring_soon') ? 'checked' : '' }}>
                        Expiring Soon (30 days)
                    </label>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern btn-modern-primary mr-2"><i class="fas fa-search mr-1"></i>Filter</button>
                    <a href="{{ route('hr.documents.index') }}" class="btn btn-modern btn-modern-info">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Documents List</h3>
            <span class="badge-modern badge-modern-primary">{{ $documents->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Document Name</th>
                            <th>Type</th>
                            <th>Expiry Date</th>
                            <th>Uploaded By</th>
                            <th>Upload Date</th>
                            <th style="width: 220px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $document)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $document->id }}</td>
                            <td>
                                <a href="{{ route('hr.employees.show', $document->employee) }}" class="font-weight-bold text-primary">
                                    {{ $document->employee->full_name }}
                                </a>
                            </td>
                            <td><strong class="text-primary">{{ $document->document_name }}</strong></td>
                            <td><span class="badge-modern badge-modern-info">{{ $document->document_type }}</span></td>
                            <td>
                                @if($document->expiry_date)
                                    @if($document->expiry_date->isPast())
                                        <span class="badge-modern badge-modern-danger"><i class="fas fa-exclamation-triangle"></i> Expired</span>
                                    @elseif($document->expiry_date->diffInDays(now()) <= 30)
                                        <span class="badge-modern badge-modern-warning">{{ $document->expiry_date->format('M d, Y') }}</span>
                                    @else
                                        <span class="text-muted">{{ $document->expiry_date->format('M d, Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="text-muted">{{ $document->uploader->name ?? '-' }}</span></td>
                            <td><span class="text-muted">{{ $document->created_at->format('M d, Y') }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.documents.download', $document) }}" class="btn-action btn-modern btn-modern-success" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('hr.documents.show', $document) }}" class="btn-action btn-modern btn-modern-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->hasPermission('hr.documents.edit'))
                                    <a href="{{ route('hr.documents.edit', $document) }}" class="btn-action btn-modern btn-modern-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr.documents.delete'))
                                    <form action="{{ route('hr.documents.destroy', $document) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-modern btn-modern-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-file-alt"></i>
                                    <p class="mb-3">No documents found</p>
                                    @if(auth()->user()->hasPermission('hr.documents.create'))
                                    <a href="{{ route('hr.documents.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Upload First Document
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
        @if($documents->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $documents->links() }}
        </div>
        @endif
    </div>
</div>
@stop
