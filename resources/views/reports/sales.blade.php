@extends('layouts.adminlte')

@section('title', 'Sales Report')
@section('page-title', 'Sales Report')
@section('breadcrumb', 'Reports / Sales Report')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.css">
<style>
    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filter Options</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Group By</label>
                        <select name="group_by" class="form-control">
                            <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Day</option>
                            <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Week</option>
                            <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Month</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i>Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Revenue Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card stat-card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-muted mb-1">Total Revenue</h5>
                            <h2 class="mb-0 text-primary">${{ number_format($totalRevenue, 2) }}</h2>
                            <small class="text-muted">All bookings in period</small>
                        </div>
                        <div class="text-primary" style="font-size: 3rem; opacity: 0.3;">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card stat-card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-muted mb-1">Paid Revenue</h5>
                            <h2 class="mb-0 text-success">${{ number_format($paidRevenue, 2) }}</h2>
                            <small class="text-muted">Confirmed payments</small>
                        </div>
                        <div class="text-success" style="font-size: 3rem; opacity: 0.3;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Revenue Trend</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Details Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list mr-2"></i>Booking Details</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Booths</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookingData as $booking)
                            <tr>
                                <td>{{ $booking['date'] }}</td>
                                <td>{{ $booking['client'] }}</td>
                                <td><span class="badge badge-info">{{ $booking['booths_count'] }}</span></td>
                                <td><strong>${{ number_format($booking['total'], 2) }}</strong></td>
                                <td>
                                    <span class="badge badge-{{ $booking['status'] == 'Paid' ? 'success' : 'warning' }}">
                                        {{ $booking['status'] }}
                                    </span>
                                </td>
                                <td>{{ $booking['user'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No bookings found in selected period</td>
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
    const chartData = @json($chartData);
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.label),
            datasets: [{
                label: 'Total Revenue',
                data: chartData.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Paid Revenue',
                data: chartData.map(item => item.paid),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
