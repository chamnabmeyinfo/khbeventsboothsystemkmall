@extends('layouts.adminlte')

@section('title', 'Costings')
@section('page-title', 'Costing Management')
@section('breadcrumb', 'Finance / Costings')

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
    .kpi-card.danger::before { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); }
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
    .kpi-card.danger .kpi-icon { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); }
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.payments.index') }}">Finance</a></li>
            <li class="breadcrumb-item active">Costings</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="kpi-label">Total Estimated</div>
                    <div class="kpi-value">${{ number_format($stats['total_estimated'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-calculator"></i></div>
                    <div class="kpi-label">Total Actual</div>
                    <div class="kpi-value">${{ number_format($stats['total_actual'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="kpi-label">Variance</div>
                    <div class="kpi-value">${{ number_format($stats['total_variance'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card danger">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-list"></i></div>
                    <div class="kpi-label">Total Costings</div>
                    <div class="kpi-value">{{ number_format($stats['total_costings'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="{{ route('finance.costings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Add Costing
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('finance.costings.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Name, description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-toggle-on mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-calendar-alt mr-1"></i>Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-calendar-check mr-1"></i>Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-map mr-1"></i>Floor Plan</label>
                    <select name="floor_plan_id" class="form-control">
                        <option value="">All Floor Plans</option>
                        @foreach($floorPlans ?? [] as $floorPlan)
                            <option value="{{ $floorPlan->id }}" {{ request('floor_plan_id') == $floorPlan->id ? 'selected' : '' }}>
                                {{ $floorPlan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 mb-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('finance.costings.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Costing Records</h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $costings->total() }} Total</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Name</th>
                            <th style="width: 150px;">Estimated</th>
                            <th style="width: 150px;">Actual</th>
                            <th style="width: 150px;">Variance</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 120px;">Date</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($costings as $costing)
                        <tr class="table-row-hover">
                            <td><strong class="text-primary">#{{ $costing->id }}</strong></td>
                            <td><strong>{{ $costing->name }}</strong></td>
                            <td><strong>${{ number_format($costing->estimated_cost ?? 0, 2) }}</strong></td>
                            <td><strong>${{ number_format($costing->actual_cost ?? 0, 2) }}</strong></td>
                            <td>
                                @php
                                    $variance = ($costing->actual_cost ?? 0) - ($costing->estimated_cost ?? 0);
                                @endphp
                                <strong class="{{ $variance >= 0 ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($variance, 2) }}
                                </strong>
                            </td>
                            <td>
                                @php
                                    $statusColors = ['draft' => 'secondary', 'approved' => 'info', 'in_progress' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                                    $color = $statusColors[$costing->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $costing->status)) }}</span>
                            </td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($costing->costing_date)->format('M d, Y') }}</strong>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('finance.costings.show', $costing) }}" class="btn btn-info" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('finance.costings.edit', $costing) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('finance.costings.destroy', $costing) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-calculator fa-3x mb-3"></i>
                                    <p class="mb-0">No costings found</p>
                                    <a href="{{ route('finance.costings.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus mr-1"></i>Add First Costing
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($costings, 'hasPages') && $costings->hasPages())
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="text-muted">
                        @if($costings->firstItem())
                        Showing {{ $costings->firstItem() }} to {{ $costings->lastItem() }} of {{ $costings->total() }} costings
                        @else
                        {{ $costings->total() }} costing(s) total
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="float-right">{{ $costings->links() }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
