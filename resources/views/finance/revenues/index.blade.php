@extends('layouts.adminlte')

@section('title', 'Revenues')
@section('page-title', 'Revenue Management')
@section('breadcrumb', 'Finance / Revenues')

@push('body-class', 'ios-dashboard-mode')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/hr-looker.css') }}?v=1">
<style>
    /* Finance / revenues index — single responsive layer (dashboard-looker breakpoints). */
    .revenues-index-page.looker-dashboard {
        padding-left: 0;
        padding-right: 0;
        max-width: 100%;
    }
    @media (min-width: 576px) {
        .revenues-index-page.looker-dashboard {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
    }
    .finance-revenue-amount {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--accent-green);
    }
    .finance-filters-body {
        padding: 1rem 1.25rem 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="looker-dashboard revenues-index-page">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Revenues</h1>
            <p>Non-payment income lines, clients, and receipt status in one place.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('finance.revenues.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> Add revenue
            </a>
        </div>
    </header>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker">
            <div class="kpi-top">
                <div class="kpi-title">Total received</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-dollar-sign" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">${{ number_format($stats['total_amount'] ?? 0, 2) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-circle" style="font-size: 6px;" aria-hidden="true"></i> Received amounts
            </div>
        </div>
        <div class="kpi-card-looker success">
            <div class="kpi-top">
                <div class="kpi-title">Total revenues</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-file-invoice-dollar" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($stats['total_revenues'] ?? 0) }}</div>
            <div class="kpi-bottom trend-positive">
                <i class="fas fa-list" aria-hidden="true"></i> All records
            </div>
        </div>
        <div class="kpi-card-looker warning">
            <div class="kpi-top">
                <div class="kpi-title">Pending amount</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-clock" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">${{ number_format($stats['pending_amount'] ?? 0, 2) }}</div>
            <div class="kpi-bottom trend-warning">
                <i class="fas fa-hourglass-half" aria-hidden="true"></i> Not yet received
            </div>
        </div>
        <div class="kpi-card-looker purple">
            <div class="kpi-top">
                <div class="kpi-title">This month</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-calendar" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">${{ number_format($stats['this_month_amount'] ?? 0, 2) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-calendar-day" aria-hidden="true"></i> Received in current month
            </div>
        </div>
    </div>

    <div class="canvas-panel mb-4">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-filter" aria-hidden="true"></i> Filters</h2>
            <button type="button" class="action-btn action-btn-secondary action-btn-icon" data-toggle="collapse" data-target="#revenuesFiltersCollapse" aria-expanded="true" aria-controls="revenuesFiltersCollapse" title="Toggle filters">
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </button>
        </div>
        <div class="collapse show finance-filters-body" id="revenuesFiltersCollapse">
            <form method="GET" action="{{ route('finance.revenues.index') }}" id="filterForm" class="container-fluid px-0">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label for="revenues_search" class="form-label font-weight-bold">Search</label>
                        <input type="text" name="search" id="revenues_search" class="form-control"
                               placeholder="Title, client…"
                               value="{{ request('search') }}" autocomplete="off">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="revenues_status" class="form-label font-weight-bold">Status</label>
                        <select name="status" id="revenues_status" class="form-control">
                            <option value="">All status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="revenues_category" class="form-label font-weight-bold">Category</label>
                        <select name="category_id" id="revenues_category" class="form-control">
                            <option value="">All categories</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="revenues_date_from" class="form-label font-weight-bold">Date from</label>
                        <input type="date" name="date_from" id="revenues_date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="revenues_date_to" class="form-label font-weight-bold">Date to</label>
                        <input type="date" name="date_to" id="revenues_date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="action-btn action-btn-primary w-100 justify-content-center" title="Apply filters">
                            <i class="fas fa-search" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 d-flex flex-wrap align-items-center">
                        <a href="{{ route('finance.revenues.index') }}" class="action-btn action-btn-secondary mr-2 mb-2">
                            <i class="fas fa-times" aria-hidden="true"></i> Clear filters
                        </a>
                        @if(request()->hasAny(['search', 'status', 'category_id', 'date_from', 'date_to']))
                            <span class="status-badge status-badge-blue mb-2">{{ $revenues->total() }} result(s)</span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="canvas-panel hr-panel-flush">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-list" aria-hidden="true"></i> Revenue records</h2>
            <span class="status-badge status-badge-blue">{{ $revenues->total() }} total</span>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenues as $revenue)
                    <tr>
                        <td class="text-muted small font-weight-bold">#{{ $revenue->id }}</td>
                        <td><strong>{{ $revenue->title }}</strong></td>
                        <td>
                            @if($revenue->client)
                                <a href="{{ route('clients.show', $revenue->client) }}" class="text-link-strong">{{ $revenue->client->company ?? 'N/A' }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($revenue->category)
                                <span class="status-badge status-badge-blue">{{ $revenue->category->name }}</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td><span class="finance-revenue-amount">${{ number_format($revenue->amount, 2) }}</span></td>
                        <td>
                            @php
                                $revenueStatusClass = [
                                    'pending' => 'status-badge-orange',
                                    'confirmed' => 'status-badge-blue',
                                    'received' => 'status-badge-green',
                                    'cancelled' => 'status-badge-neutral',
                                ];
                                $sc = $revenueStatusClass[$revenue->status] ?? 'status-badge-neutral';
                            @endphp
                            <span class="status-badge {{ $sc }}">{{ ucfirst($revenue->status) }}</span>
                        </td>
                        <td><span class="text-muted small">{{ \Carbon\Carbon::parse($revenue->revenue_date)->format('M d, Y') }}</span></td>
                        <td>
                            <div class="hr-table-actions">
                                <a href="{{ route('finance.revenues.show', $revenue) }}" class="action-btn action-btn-secondary action-btn-icon" title="View"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                <a href="{{ route('finance.revenues.edit', $revenue) }}" class="action-btn action-btn-secondary action-btn-icon" title="Edit"><i class="fas fa-edit" aria-hidden="true"></i></a>
                                <form action="{{ route('finance.revenues.destroy', $revenue) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-danger-soft action-btn-icon" title="Delete"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="hr-empty-cell">
                            <i class="fas fa-arrow-trend-up" aria-hidden="true"></i>
                            <p class="mb-2">No revenues found</p>
                            <a href="{{ route('finance.revenues.create') }}" class="action-btn action-btn-primary">
                                <i class="fas fa-plus" aria-hidden="true"></i> Add first revenue
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($revenues, 'hasPages') && $revenues->hasPages())
        <div class="hr-panel-footer">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <span class="text-muted small">
                        @if($revenues->firstItem())
                            Showing {{ $revenues->firstItem() }}–{{ $revenues->lastItem() }} of {{ $revenues->total() }}
                        @else
                            {{ $revenues->total() }} revenue(s) total
                        @endif
                    </span>
                </div>
                <div class="col-md-6 text-md-right">
                    {{ $revenues->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
