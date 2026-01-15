@extends('layouts.adminlte')

@section('title', 'Booking Trends')
@section('page-title', 'Booking Trends')
@section('breadcrumb', 'Reports / Booking Trends')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/chartjs/chart.min.css') }}">
<style>
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

    .stat-summary {
        background: rgba(255, 255, 255, 0.7);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
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
            <li class="breadcrumb-item active">Booking Trends</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 style="font-weight: 700; color: #2d3748;">
                        <i class="fas fa-chart-line mr-2 text-info"></i>Booking Trends
                    </h2>
                    <p class="text-muted mb-0">Track booking patterns and identify trends over time</p>
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
        <form method="GET" action="{{ route('reports.trends') }}" class="row align-items-end">
            <div class="col-md-4 mb-3">
                <label><i class="fas fa-calendar-alt mr-1"></i>Time Period</label>
                <select name="days" class="form-control" onchange="this.form.submit()">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="60" {{ $days == 60 ? 'selected' : '' }}>Last 60 Days</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 Days</option>
                </select>
            </div>
            <div class="col-md-8 mb-3">
                <div class="btn-group">
                    <a href="{{ route('reports.trends', ['days' => 7]) }}" class="btn btn-sm {{ $days == 7 ? 'btn-primary' : 'btn-outline-primary' }}">7 Days</a>
                    <a href="{{ route('reports.trends', ['days' => 30]) }}" class="btn btn-sm {{ $days == 30 ? 'btn-primary' : 'btn-outline-primary' }}">30 Days</a>
                    <a href="{{ route('reports.trends', ['days' => 60]) }}" class="btn btn-sm {{ $days == 60 ? 'btn-primary' : 'btn-outline-primary' }}">60 Days</a>
                    <a href="{{ route('reports.trends', ['days' => 90]) }}" class="btn btn-sm {{ $days == 90 ? 'btn-primary' : 'btn-outline-primary' }}">90 Days</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Statistics -->
    @php
        $totalBookings = array_sum(array_column($trends, 'bookings'));
        $totalConfirmed = array_sum(array_column($trends, 'confirmed'));
        $totalPaid = array_sum(array_column($trends, 'paid'));
        $avgBookings = $days > 0 ? round($totalBookings / $days, 1) : 0;
    @endphp
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-summary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Bookings</h6>
                        <h3 class="mb-0 text-primary">{{ number_format($totalBookings) }}</h3>
                    </div>
                    <i class="fas fa-calendar-check fa-2x text-primary" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-summary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Confirmed</h6>
                        <h3 class="mb-0 text-info">{{ number_format($totalConfirmed) }}</h3>
                    </div>
                    <i class="fas fa-check-double fa-2x text-info" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-summary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Paid</h6>
                        <h3 class="mb-0 text-success">{{ number_format($totalPaid) }}</h3>
                    </div>
                    <i class="fas fa-money-bill-wave fa-2x text-success" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-summary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Avg Daily</h6>
                        <h3 class="mb-0 text-warning">{{ number_format($avgBookings, 1) }}</h3>
                    </div>
                    <i class="fas fa-chart-bar fa-2x text-warning" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Trends Chart -->
    <div class="chart-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 style="font-weight: 600; color: #2d3748;">
                <i class="fas fa-chart-bar mr-2 text-info"></i>Booking Trends (Last {{ $days }} Days)
            </h3>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-info active" onclick="switchChartType('bar')">Bar</button>
                <button type="button" class="btn btn-outline-info" onclick="switchChartType('line')">Line</button>
            </div>
        </div>
        <canvas id="trendsChart" height="80"></canvas>
    </div>

    <!-- Trend Analysis -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Trend Analysis</h3>
                </div>
                <div class="card-body">
                    @php
                        $firstHalf = array_slice($trends, 0, floor(count($trends) / 2));
                        $secondHalf = array_slice($trends, floor(count($trends) / 2));
                        $firstHalfAvg = count($firstHalf) > 0 ? array_sum(array_column($firstHalf, 'bookings')) / count($firstHalf) : 0;
                        $secondHalfAvg = count($secondHalf) > 0 ? array_sum(array_column($secondHalf, 'bookings')) / count($secondHalf) : 0;
                        $trendDirection = $secondHalfAvg > $firstHalfAvg ? 'up' : ($secondHalfAvg < $firstHalfAvg ? 'down' : 'stable');
                        $trendPercentage = $firstHalfAvg > 0 ? abs((($secondHalfAvg - $firstHalfAvg) / $firstHalfAvg) * 100) : 0;
                    @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">First Half Average</span>
                            <strong>{{ number_format($firstHalfAvg, 1) }} bookings/day</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Second Half Average</span>
                            <strong>{{ number_format($secondHalfAvg, 1) }} bookings/day</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Trend</span>
                            <span class="badge badge-{{ $trendDirection == 'up' ? 'success' : ($trendDirection == 'down' ? 'danger' : 'secondary') }}">
                                <i class="fas fa-arrow-{{ $trendDirection == 'up' ? 'up' : ($trendDirection == 'down' ? 'down' : 'right') }} mr-1"></i>
                                {{ ucfirst($trendDirection) }} ({{ number_format($trendPercentage, 1) }}%)
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-check mr-2"></i>Peak Days</h3>
                </div>
                <div class="card-body">
                    @php
                        $sortedTrends = collect($trends)->sortByDesc('bookings')->take(5);
                    @endphp
                    <ul class="list-unstyled mb-0">
                        @foreach($sortedTrends as $trend)
                        <li class="mb-2 d-flex justify-content-between align-items-center p-2" style="background: rgba(0,0,0,0.02); border-radius: 8px;">
                            <div>
                                <strong>{{ $trend['date'] }}</strong>
                                <br><small class="text-muted">{{ $trend['date_full'] }}</small>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-primary">{{ $trend['bookings'] }} bookings</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
let trendsChart;
let currentChartType = 'bar';

const trends = @json($trends);
const ctx = document.getElementById('trendsChart');

if (ctx) {
    trendsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: trends.map(t => t.date),
            datasets: [{
                label: 'Bookings',
                data: trends.map(t => t.bookings),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }, {
                label: 'Confirmed',
                data: trends.map(t => t.confirmed),
                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            }, {
                label: 'Paid',
                data: trends.map(t => t.paid),
                backgroundColor: 'rgba(153, 102, 255, 0.8)',
                borderColor: 'rgb(153, 102, 255)',
                borderWidth: 1
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
                    padding: 12
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
                        stepSize: 1
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
    document.querySelectorAll('.btn-outline-info').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Update chart
    if (trendsChart) {
        trendsChart.config.type = type;
        if (type === 'line') {
            trendsChart.config.data.datasets.forEach(dataset => {
                dataset.fill = true;
                dataset.tension = 0.4;
            });
        } else {
            trendsChart.config.data.datasets.forEach(dataset => {
                dataset.fill = false;
                dataset.tension = 0;
            });
        }
        trendsChart.update();
    }
}
</script>
@endpush

