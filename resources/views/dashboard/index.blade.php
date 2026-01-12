@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
.stat-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-left: 4px solid;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.stat-card.border-primary { border-left-color: #0d6efd; }
.stat-card.border-success { border-left-color: #198754; }
.stat-card.border-warning { border-left-color: #ffc107; }
.stat-card.border-info { border-left-color: #0dcaf0; }
.stat-card.border-dark { border-left-color: #212529; }
.stat-card.border-secondary { border-left-color: #6c757d; }
.stat-card.border-danger { border-left-color: #dc3545; }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
        <p class="text-muted">Welcome back, {{ auth()->user()->username }}! Here's your overview.</p>
    </div>
    <div class="col-auto">
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <a href="{{ route('settings.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-cog me-1"></i>Settings
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Booths</h6>
                        <h2 class="mb-0 text-primary">{{ $stats['total_booths'] }}</h2>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
                @if($stats['total_booths'] > 0)
                <small class="text-muted">
                    {{ number_format(($stats['available_booths'] / $stats['total_booths']) * 100, 1) }}% Available
                </small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Available</h6>
                        <h2 class="mb-0 text-success">{{ $stats['available_booths'] }}</h2>
                    </div>
                    <div class="text-success" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Reserved</h6>
                        <h2 class="mb-0 text-warning">{{ $stats['reserved_booths'] }}</h2>
                    </div>
                    <div class="text-warning" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Confirmed</h6>
                        <h2 class="mb-0 text-info">{{ $stats['confirmed_booths'] }}</h2>
                    </div>
                    <div class="text-info" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Paid</h6>
                        <h2 class="mb-0 text-dark">{{ $stats['paid_booths'] }}</h2>
                    </div>
                    <div class="text-dark" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Clients</h6>
                        <h2 class="mb-0 text-secondary">{{ $stats['total_clients'] }}</h2>
                    </div>
                    <div class="text-secondary" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Users</h6>
                        <h2 class="mb-0 text-danger">{{ $stats['total_users'] }}</h2>
                    </div>
                    <div class="text-danger" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Bookings</h6>
                        <h2 class="mb-0 text-primary">{{ $stats['total_bookings'] }}</h2>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Booth Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="boothStatusChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Booking Trends (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="bookingTrendChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Booths</th>
                                <th>Date</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->id }}</td>
                                <td>{{ $booking->client->company ?? 'N/A' }}</td>
                                <td>{{ count(json_decode($booking->boothid, true) ?? []) }} booths</td>
                                <td>{{ $booking->date_book->format('Y-m-d H:i') }}</td>
                                <td>{{ $booking->user->username }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No bookings yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isAdmin && !empty($userStats))
<div class="row mt-4">
    <div class="col">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>User Performance</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Type</th>
                                <th>Reserved</th>
                                <th>Confirmed</th>
                                <th>Paid</th>
                                <th>Total</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userStats as $stat)
                            <tr>
                                <td><strong>{{ $stat['username'] }}</strong></td>
                                <td><span class="badge bg-{{ $stat['type'] == 'Admin' ? 'danger' : 'secondary' }}">{{ $stat['type'] }}</span></td>
                                <td>{{ $stat['reserve'] }}</td>
                                <td>{{ $stat['booking'] }}</td>
                                <td>{{ $stat['paid'] }}</td>
                                <td><strong>{{ $stat['reserve'] + $stat['booking'] + $stat['paid'] }}</strong></td>
                                <td>
                                    @php
                                        $total = $stat['reserve'] + $stat['booking'] + $stat['paid'];
                                        $percentage = $stats['total_booths'] > 0 ? ($total / $stats['total_booths']) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Booth Status Chart
const boothStatusCtx = document.getElementById('boothStatusChart').getContext('2d');
new Chart(boothStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Available', 'Reserved', 'Confirmed', 'Paid', 'Hidden'],
        datasets: [{
            data: [
                {{ $stats['available_booths'] }},
                {{ $stats['reserved_booths'] }},
                {{ $stats['confirmed_booths'] }},
                {{ $stats['paid_booths'] }},
                {{ $stats['total_booths'] - $stats['available_booths'] - $stats['reserved_booths'] - $stats['confirmed_booths'] - $stats['paid_booths'] }}
            ],
            backgroundColor: [
                '#198754',
                '#ffc107',
                '#0dcaf0',
                '#212529',
                '#6c757d'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Booking Trends Chart (Last 7 days)
const bookingTrendCtx = document.getElementById('bookingTrendChart').getContext('2d');
const last7Days = @json($bookingTrendDates ?? []);
const bookingCounts = @json($bookingTrendCounts ?? []);

new Chart(bookingTrendCtx, {
    type: 'line',
    data: {
        labels: last7Days,
        datasets: [{
            label: 'Bookings',
            data: bookingCounts,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

function refreshDashboard() {
    window.location.reload();
}
</script>
@endpush

