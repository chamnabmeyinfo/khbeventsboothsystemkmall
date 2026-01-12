@extends('layouts.adminlte')

@section('title', 'Finance Categories')
@section('page-title', 'Finance Category Management')
@section('breadcrumb', 'Finance / Categories')

@push('styles')
<style>
    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }
    .kpi-card:hover::before { opacity: 1; }
    .kpi-card.primary::before { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success::before { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.warning::before { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .kpi-card.info::before { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .kpi-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 16px;
    }
    .kpi-card.primary .kpi-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success .kpi-icon { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.warning .kpi-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .kpi-card.info .kpi-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 8px 0;
        line-height: 1;
    }
    .kpi-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .filter-bar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 24px;
        margin-bottom: 24px;
    }
    .table-row-hover { transition: all 0.2s; }
    .table-row-hover:hover { background-color: #f8f9fc; }
    .color-badge {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: 1px solid #ddd;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.payments.index') }}">Finance</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-tags"></i></div>
                    <div class="kpi-label">Total Categories</div>
                    <div class="kpi-value">{{ number_format($stats['total_categories'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-arrow-down"></i></div>
                    <div class="kpi-label">Expense Categories</div>
                    <div class="kpi-value">{{ number_format($stats['expense_categories'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-arrow-up"></i></div>
                    <div class="kpi-label">Revenue Categories</div>
                    <div class="kpi-value">{{ number_format($stats['revenue_categories'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="kpi-label">Active Categories</div>
                    <div class="kpi-value">{{ number_format($stats['active_categories'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="{{ route('finance.categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Add Category
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('finance.categories.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Name, description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-filter mr-1"></i>Type</label>
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                        <option value="revenue" {{ request('type') == 'revenue' ? 'selected' : '' }}>Revenue</option>
                        <option value="costing" {{ request('type') == 'costing' ? 'selected' : '' }}>Costing</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-toggle-on mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('finance.categories.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Category Records</h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $categories->total() }} Total</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Name</th>
                            <th style="width: 120px;">Type</th>
                            <th style="width: 100px;">Color</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 100px;">Sort Order</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr class="table-row-hover">
                            <td><strong class="text-primary">#{{ $category->id }}</strong></td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                                @if($category->description)
                                <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeColors = [
                                        'expense' => 'danger',
                                        'revenue' => 'success',
                                        'costing' => 'info'
                                    ];
                                    $typeColor = $typeColors[$category->type] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $typeColor }}">{{ ucfirst($category->type) }}</span>
                            </td>
                            <td>
                                @if($category->color)
                                    <span class="color-badge" style="background-color: {{ $category->color }};" title="{{ $category->color }}"></span>
                                    <small class="text-muted ml-1">{{ $category->color }}</small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $category->sort_order ?? 0 }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('finance.categories.show', $category) }}" class="btn btn-info" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('finance.categories.edit', $category) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('finance.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-tags fa-3x mb-3"></i>
                                    <p class="mb-0">No categories found</p>
                                    <a href="{{ route('finance.categories.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus mr-1"></i>Add First Category
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($categories, 'hasPages') && $categories->hasPages())
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="text-muted">
                        @if($categories->firstItem())
                        Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} categories
                        @else
                        {{ $categories->total() }} category(ies) total
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="float-right">{{ $categories->links() }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
