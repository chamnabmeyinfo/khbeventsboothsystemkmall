@extends('layouts.adminlte')

@section('title', 'Revenues')
@section('page-title', 'Revenue Management')
@section('breadcrumb', 'Finance / Revenues')

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
            <li class="breadcrumb-item active">Revenues</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="kpi-label">Total Received</div>
                    <div class="kpi-value">${{ number_format($stats['total_amount'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="kpi-label">Total Revenues</div>
                    <div class="kpi-value">{{ number_format($stats['total_revenues'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-clock"></i></div>
                    <div class="kpi-label">Pending</div>
                    <div class="kpi-value">${{ number_format($stats['pending_amount'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card danger">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-calendar"></i></div>
                    <div class="kpi-label">This Month</div>
                    <div class="kpi-value">${{ number_format($stats['this_month_amount'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="{{ route('finance.revenues.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Add Revenue
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('finance.revenues.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Title, client..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-toggle-on mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-tags mr-1"></i>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
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
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i></button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('finance.revenues.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Revenue Records</h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $revenues->total() }} Total</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Title</th>
                            <th style="width: 150px;">Client</th>
                            <th style="width: 120px;">Category</th>
                            <th style="width: 150px;">Amount</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 120px;">Date</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenues as $revenue)
                        <tr class="table-row-hover">
                            <td><strong class="text-primary">#{{ $revenue->id }}</strong></td>
                            <td><strong>{{ $revenue->title }}</strong></td>
                            <td>
                                @if($revenue->client)
                                    <a href="{{ route('clients.show', $revenue->client) }}">{{ $revenue->client->company ?? 'N/A' }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($revenue->category)
                                    <span class="badge badge-info">{{ $revenue->category->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-success" style="font-size: 1.1rem;">
                                    ${{ number_format($revenue->amount, 2) }}
                                </strong>
                            </td>
                            <td>
                                @php
                                    $statusColors = ['pending' => 'warning', 'confirmed' => 'info', 'received' => 'success', 'cancelled' => 'secondary'];
                                    $color = $statusColors[$revenue->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ ucfirst($revenue->status) }}</span>
                            </td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($revenue->revenue_date)->format('M d, Y') }}</strong>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('finance.revenues.show', $revenue) }}" class="btn btn-info" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('finance.revenues.edit', $revenue) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('finance.revenues.destroy', $revenue) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
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
                                    <i class="fas fa-arrow-up fa-3x mb-3"></i>
                                    <p class="mb-0">No revenues found</p>
                                    <a href="{{ route('finance.revenues.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus mr-1"></i>Add First Revenue
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($revenues, 'hasPages') && $revenues->hasPages())
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="text-muted">
                        @if($revenues->firstItem())
                        Showing {{ $revenues->firstItem() }} to {{ $revenues->lastItem() }} of {{ $revenues->total() }} revenues
                        @else
                        {{ $revenues->total() }} revenue(s) total
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="float-right">{{ $revenues->links() }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
