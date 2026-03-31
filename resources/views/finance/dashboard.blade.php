@extends('layouts.admin')

@section('title', 'Financial Dashboard')
@section('page-title', 'Financial Dashboard')
@section('breadcrumb', 'Finance / Dashboard')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<style>
    .looker-dashboard { padding: 0 !important; }
    .glass-card {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(40px) saturate(180%);
        -webkit-backdrop-filter: blur(40px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 24px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        margin-bottom: 24px;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.55);
        box-shadow: 0 15px 45px rgba(31, 38, 135, 0.2);
    }
    .kpi-card-looker {
        margin-bottom: 24px;
    }
</style>
@endpush

@push('body-class', 'ios-dashboard-mode')


@section('content')
<div class='looker-dashboard'>
<div class="container-fluid">
    
    {{-- Filters --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('finance.dashboard') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Floor Plan</label>
                <select name="floor_plan_id" class="form-control">
                    <option value="">All Floor Plans</option>
                    @foreach($floorPlans as $plan)
                    <option value="{{ $plan->id }}" {{ $floorPlanId == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <a href="{{ route('finance.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
        </form>
    </div>
    
    {{-- KPI Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-card-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-card-value">${{ number_format($stats['total_revenue'], 0) }}</div>
                <div class="stat-card-label">Total Revenue</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-card-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-card-value">${{ number_format($stats['collected_revenue'], 0) }}</div>
                <div class="stat-card-label">Collected</div>
                <small style="opacity: 0.8;">{{ $stats['collection_rate'] }}% Collection Rate</small>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card warning">
                <div class="stat-card-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-card-value">${{ number_format($stats['total_pending'], 0) }}</div>
                <div class="stat-card-label">Pending Payments</div>
                <small style="opacity: 0.8;">Deposits: ${{ number_format($stats['pending_deposits'], 0) }} | Balance: ${{ number_format($stats['pending_balance'], 0) }}</small>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card danger">
                <div class="stat-card-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-card-value">{{ $stats['overdue_payments'] }}</div>
                <div class="stat-card-label">Overdue Payments</div>
            </div>
        </div>
    </div>
    
    {{-- Secondary KPIs --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card info">
                <div class="stat-card-icon">
                    <i class="fas fa-store"></i>
                </div>
                <div class="stat-card-value">{{ $stats['booked_booths'] }}/{{ $stats['total_booths'] }}</div>
                <div class="stat-card-label">Booked Booths</div>
                <small style="opacity: 0.8;">{{ $stats['occupancy_rate'] }}% Occupancy</small>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-card-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="stat-card-value">${{ number_format($stats['total_deposits'], 0) }}</div>
                <div class="stat-card-label">Total Deposits Paid</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card success">
                <div class="stat-card-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-card-value">${{ number_format($stats['total_balance'], 0) }}</div>
                <div class="stat-card-label">Total Balance Paid</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card info">
                <div class="stat-card-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-card-value">{{ $stats['available_booths'] }}</div>
                <div class="stat-card-label">Available Booths</div>
            </div>
        </div>
    </div>
    
    {{-- Charts --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-bar"></i> Revenue by Zone</h5>
                <canvas id="revenueByZoneChart"></canvas>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-pie"></i> Revenue by Category</h5>
                <canvas id="revenueByCategoryChart"></canvas>
            </div>
        </div>
    </div>
    
    {{-- Top Clients Table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card table-modern">
                <div class="p-4 border-bottom">
                    <h5 class="mb-0"><i class="fas fa-crown"></i> Top Clients by Revenue</h5>
                </div>
                <div class="card-body p-0">
                    <table class="looker-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Booths</th>
                                <th>Total Spent</th>
                                <th>Total Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topClients as $index => $client)
                            <tr>
                                <td><strong>{{ $index + 1 }}</strong></td>
                                <td><strong>{{ $client->company }}</strong></td>
                                <td>{{ $client->name }}</td>
                                <td><span class="badge badge-primary">{{ $client->booth_count }} booth(s)</span></td>
                                <td><strong class="text-primary">${{ number_format($client->total_spent, 2) }}</strong></td>
                                <td><strong class="text-success">${{ number_format($client->total_paid, 2) }}</strong></td>
                                <td>
                                    @if($client->total_paid >= $client->total_spent)
                                        <span class="badge badge-success">Fully Paid</span>
                                    @elseif($client->total_paid > 0)
                                        <span class="badge badge-warning">Partial</span>
                                    @else
                                        <span class="badge badge-danger">Unpaid</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No client data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>
</div>
@endsection

@push('scripts')
@if($useCDN)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@else
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
@endif
<script>
// Revenue by Zone Chart
const zoneData = {
    labels: [{{ $revenueByZone->pluck('zone')->map(function($z) { return "'".$z."'"; })->implode(',') }}],
    datasets: [{
        label: 'Total Revenue',
        data: [{{ $revenueByZone->pluck('total_revenue')->implode(',') }}],
        backgroundColor: 'rgba(102, 126, 234, 0.8)',
    }, {
        label: 'Collected',
        data: [{{ $revenueByZone->pluck('collected')->implode(',') }}],
        backgroundColor: 'rgba(28, 200, 138, 0.8)',
    }]
};

new Chart(document.getElementById('revenueByZoneChart'), {
    type: 'bar',
    data: zoneData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Revenue by Category Chart
const categoryData = {
    labels: [{!! $revenueByCategory->pluck('name')->map(function($n) { return "'".$n."'"; })->implode(',') !!}],
    datasets: [{
        data: [{{ $revenueByCategory->pluck('total_revenue')->implode(',') }}],
        backgroundColor: [
            'rgba(102, 126, 234, 0.8)',
            'rgba(118, 75, 162, 0.8)',
            'rgba(28, 200, 138, 0.8)',
            'rgba(54, 185, 204, 0.8)',
            'rgba(246, 194, 62, 0.8)',
            'rgba(231, 74, 59, 0.8)',
        ]
    }]
};

new Chart(document.getElementById('revenueByCategoryChart'), {
    type: 'pie',
    data: categoryData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'right' }
        }
    }
});
</script>
@endpush
