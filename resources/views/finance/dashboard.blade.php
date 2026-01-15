@extends('layouts.adminlte')

@section('title', 'Financial Dashboard')
@section('page-title', 'Financial Dashboard')
@section('breadcrumb', 'Finance / Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
/* Mobile Responsive Styles */
@media (max-width: 768px) {
    /* Container padding */
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    /* Stat cards - Full width on mobile */
    .stat-card {
        margin-bottom: 15px !important;
        padding: 18px !important;
    }
    
    .stat-card-icon {
        font-size: 2rem !important;
    }
    
    .stat-card-value {
        font-size: 1.8rem !important;
    }
    
    .stat-card-label {
        font-size: 0.8rem !important;
    }
    
    .stat-card small {
        font-size: 0.75rem !important;
    }
    
    /* Filter card - Stack inputs */
    .filter-card .row {
        flex-direction: column;
    }
    
    .filter-card .col-md-3 {
        width: 100%;
        margin-bottom: 10px;
    }
    
    .filter-card .btn {
        width: 100%;
        margin-bottom: 8px;
    }
    
    .filter-card .d-flex {
        flex-direction: column;
    }
    
    .filter-card .me-2 {
        margin-right: 0 !important;
        margin-bottom: 8px;
    }
    
    /* Charts - Adjust height for mobile */
    .chart-container {
        height: 300px !important;
        padding: 15px !important;
        margin-bottom: 20px !important;
    }
    
    .chart-container h5 {
        font-size: 1rem;
        margin-bottom: 15px !important;
    }
    
    /* Table - Horizontal scroll */
    .table-modern {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table-modern table {
        min-width: 700px;
    }
    
    .table-modern th,
    .table-modern td {
        padding: 10px 8px !important;
        font-size: 0.85rem !important;
    }
    
    .table-modern .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    
    /* Card headers */
    .card-header h5 {
        font-size: 1.1rem;
    }
    
    /* Responsive rows */
    .row.mb-4 {
        margin-bottom: 1rem !important;
    }
    
    /* Form controls */
    .form-control, .form-select {
        font-size: 16px; /* Prevent iOS zoom */
        padding: 10px;
    }
    
    .form-label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 6px;
    }
}

/* Tablet specific */
@media (min-width: 769px) and (max-width: 1024px) {
    .stat-card {
        padding: 20px !important;
    }
    
    .stat-card-value {
        font-size: 2rem !important;
    }
    
    .chart-container {
        height: 320px !important;
    }
    
    .table-modern th,
    .table-modern td {
        font-size: 0.9rem !important;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    /* Remove hover effects on touch */
    .stat-card:hover {
        transform: none;
    }
    
    /* Larger touch targets */
    .btn {
        min-height: 44px;
        padding: 12px 20px;
    }
}

/* Landscape phone optimization */
@media (max-width: 768px) and (orientation: landscape) {
    .chart-container {
        height: 250px !important;
    }
    
    .stat-card {
        padding: 12px !important;
    }
    
    .stat-card-value {
        font-size: 1.5rem !important;
    }
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    transition: transform 0.2s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
}

.stat-card.success {
    background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
}

.stat-card.warning {
    background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
}

.stat-card.info {
    background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);
}

.stat-card.danger {
    background: linear-gradient(135deg, #e74a3b 0%, #c23321 100%);
}

.stat-card-icon {
    font-size: 3rem;
    opacity: 0.8;
    margin-bottom: 10px;
}

.stat-card-value {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 10px 0;
}

.stat-card-label {
    font-size: 0.95rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.chart-container {
    position: relative;
    height: 350px;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.filter-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.table-modern {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

@section('content')
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
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-crown"></i> Top Clients by Revenue</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
