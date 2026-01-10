@extends('layouts.adminlte')

@section('title', 'Sales Report')
@section('page-title', 'Sales Report')
@section('breadcrumb', 'Reports / Sales Report')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    /* Modern Glassmorphism Styles */
    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

    .kpi-card:hover::before {
        opacity: 1;
    }

    .kpi-card.primary::before { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success::before { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }

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

    .chart-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        padding: 24px;
        margin-bottom: 24px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Sales Report</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 style="font-weight: 700; color: #2d3748;">
                        <i class="fas fa-dollar-sign mr-2 text-primary"></i>Sales Report
                    </h2>
                    <p class="text-muted mb-0">Revenue & bookings analysis with detailed breakdowns</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('reports.sales') }}" id="filterForm">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-calendar-alt mr-1"></i>Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-calendar-check mr-1"></i>Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-layer-group mr-1"></i>Group By</label>
                    <select name="group_by" class="form-control">
                        <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Day</option>
                        <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Week</option>
                        <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Month</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter mr-1"></i>Generate Report
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('reports.sales', ['date_from' => now()->subDays(7)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d'), 'group_by' => $groupBy]) }}" class="btn btn-outline-primary">Last 7 Days</a>
                        <a href="{{ route('reports.sales', ['date_from' => now()->subDays(30)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d'), 'group_by' => $groupBy]) }}" class="btn btn-outline-primary">Last 30 Days</a>
                        <a href="{{ route('reports.sales', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d'), 'group_by' => $groupBy]) }}" class="btn btn-outline-primary">This Month</a>
                        <a href="{{ route('reports.sales', ['date_from' => now()->startOfYear()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d'), 'group_by' => 'month']) }}" class="btn btn-outline-primary">This Year</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Revenue Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-value">${{ number_format($totalRevenue, 2) }}</div>
                    <small class="text-muted">All bookings in selected period</small>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kpi-label">Paid Revenue</div>
                    <div class="kpi-value">${{ number_format($paidRevenue, 2) }}</div>
                    <small class="text-muted">Confirmed payments only</small>
                    @if($totalRevenue > 0)
                    <div class="mt-2">
                        <span class="badge badge-success">
                            {{ number_format(($paidRevenue / $totalRevenue) * 100, 1) }}% paid
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 style="font-weight: 600; color: #2d3748;">
                <i class="fas fa-chart-line mr-2 text-primary"></i>Revenue Trend
            </h3>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary active" onclick="switchChartType('line')">Line</button>
                <button type="button" class="btn btn-outline-primary" onclick="switchChartType('bar')">Bar</button>
                <button type="button" class="btn btn-outline-primary" onclick="switchChartType('area')">Area</button>
            </div>
        </div>
        <canvas id="revenueChart" height="80"></canvas>
    </div>

    <!-- Booking Details Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Booking Details</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-success" onclick="exportReport()">
                    <i class="fas fa-file-csv mr-1"></i>Export CSV
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Booths</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>User</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookingData as $booking)
                        <tr class="table-row-hover">
                            <td>{{ $booking['date'] }}</td>
                            <td>
                                <strong>{{ $booking['client'] }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <i class="fas fa-store mr-1"></i>{{ $booking['booths_count'] }} booth(s)
                                </span>
                            </td>
                            <td>
                                <strong class="text-success">${{ number_format($booking['total'], 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-{{ $booking['status'] == 'Paid' ? 'success' : 'warning' }}">
                                    <i class="fas fa-{{ $booking['status'] == 'Paid' ? 'check-circle' : 'clock' }} mr-1"></i>
                                    {{ $booking['status'] }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $booking['user'] }}</span>
                            </td>
                            <td>
                                <a href="{{ route('books.show', $booking['id']) }}" class="btn btn-sm btn-info" title="View Booking">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">No bookings found in selected period</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let revenueChart;
let currentChartType = 'line';

const chartData = @json($chartData);
const ctx = document.getElementById('revenueChart');

if (ctx) {
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.label),
            datasets: [{
                label: 'Total Revenue',
                data: chartData.map(item => item.total),
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Paid Revenue',
                data: chartData.map(item => item.paid),
                borderColor: 'rgb(72, 187, 120)',
                backgroundColor: 'rgba(72, 187, 120, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            }
        }
    });
}

function switchChartType(type) {
    currentChartType = type;
    
    // Update button states
    document.querySelectorAll('.btn-outline-primary').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Update chart
    if (revenueChart) {
        revenueChart.config.type = type;
        revenueChart.update();
    }
}

function exportReport() {
    Swal.fire({
        icon: 'info',
        title: 'Export Report',
        text: 'Export functionality will generate a CSV file with all booking data.',
        showCancelButton: true,
        confirmButtonText: 'Export CSV',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#667eea'
    }).then((result) => {
        if (result.isConfirmed) {
            const params = new URLSearchParams({
                date_from: '{{ $dateFrom }}',
                date_to: '{{ $dateTo }}',
                group_by: '{{ $groupBy }}',
                export: 'csv'
            });
            window.location.href = '{{ route("export.bookings") }}?' + params.toString();
        }
    });
}
</script>
@endpush
