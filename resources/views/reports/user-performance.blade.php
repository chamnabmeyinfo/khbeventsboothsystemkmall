@extends('layouts.adminlte')

@section('title', 'User Performance Report')
@section('page-title', 'User Performance Report')
@section('breadcrumb', 'Reports / User Performance')

@push('styles')
<style>
    .performance-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 4px solid #007bff;
        height: 100%;
    }

    .performance-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .performance-card.rank-1 { border-left-color: #ffc107; }
    .performance-card.rank-2 { border-left-color: #6c757d; }
    .performance-card.rank-3 { border-left-color: #cd7f32; }

    .rank-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        color: white;
    }

    .rank-badge.rank-1 { background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); }
    .rank-badge.rank-2 { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
    .rank-badge.rank-3 { background: linear-gradient(135deg, #cd7f32 0%, #b8860b 100%); }

    .filter-bar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
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
            <li class="breadcrumb-item active">User Performance</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 style="font-weight: 700; color: #2d3748;">
                        <i class="fas fa-users mr-2 text-success"></i>User Performance Report
                    </h2>
                    <p class="text-muted mb-0">Sales team analytics and performance metrics</p>
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
        <form method="GET" action="{{ route('reports.user-performance') }}" class="row">
            <div class="col-md-4 mb-3">
                <label><i class="fas fa-calendar-alt mr-1"></i>Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label><i class="fas fa-calendar-check mr-1"></i>Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-filter mr-1"></i>Generate Report
                </button>
            </div>
        </form>
        <div class="row">
            <div class="col-12">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('reports.user-performance', ['date_from' => now()->subDays(7)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}" class="btn btn-outline-primary">Last 7 Days</a>
                    <a href="{{ route('reports.user-performance', ['date_from' => now()->subDays(30)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}" class="btn btn-outline-primary">Last 30 Days</a>
                    <a href="{{ route('reports.user-performance', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}" class="btn btn-outline-primary">This Month</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Summary -->
    @php
        $totalRevenue = array_sum(array_column($performance, 'total_revenue'));
        $totalPaidRevenue = array_sum(array_column($performance, 'paid_revenue'));
        $totalBookings = array_sum(array_column($performance, 'total_bookings'));
    @endphp
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 24px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 style="opacity: 0.9;">Total Revenue</h6>
                        <h2 class="mb-0">${{ number_format($totalRevenue, 2) }}</h2>
                    </div>
                    <i class="fas fa-dollar-sign fa-3x" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); border: none; color: white; padding: 24px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 style="opacity: 0.9;">Paid Revenue</h6>
                        <h2 class="mb-0">${{ number_format($totalPaidRevenue, 2) }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); border: none; color: white; padding: 24px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 style="opacity: 0.9;">Total Bookings</h6>
                        <h2 class="mb-0">{{ number_format($totalBookings) }}</h2>
                    </div>
                    <i class="fas fa-calendar-check fa-3x" style="opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Cards -->
    <div class="row">
        @foreach($performance as $index => $perf)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card performance-card {{ $index < 3 ? 'rank-' . ($index + 1) : '' }}" style="position: relative;">
                @if($index < 3)
                <div class="rank-badge rank-{{ $index + 1 }}">
                    #{{ $index + 1 }}
                </div>
                @endif
                <div class="card-body" style="padding: 24px;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1" style="font-weight: 700;">{{ $perf['username'] }}</h5>
                            <span class="badge badge-{{ $perf['type'] == 'Admin' ? 'danger' : 'info' }}">
                                <i class="fas fa-{{ $perf['type'] == 'Admin' ? 'shield-alt' : 'user-tie' }} mr-1"></i>
                                {{ $perf['type'] }}
                            </span>
                        </div>
                        <i class="fas fa-user-tie fa-2x text-muted" style="opacity: 0.3;"></i>
                    </div>
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="border-right pr-2">
                                <div class="text-muted small mb-1">Bookings</div>
                                <div class="h5 mb-0 text-primary">{{ $perf['total_bookings'] }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-right pr-2">
                                <div class="text-muted small mb-1">Revenue</div>
                                <div class="h5 mb-0 text-success">${{ number_format($perf['total_revenue'], 0) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small mb-1">Paid</div>
                            <div class="h5 mb-0 text-info">${{ number_format($perf['paid_revenue'], 0) }}</div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Conversion Rate</span>
                            <span class="font-weight-bold">{{ $perf['conversion_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 5px;">
                            <div class="progress-bar bg-gradient-success" 
                                 style="width: {{ min($perf['conversion_rate'], 100) }}%; border-radius: 5px;"
                                 role="progressbar">
                            </div>
                        </div>
                    </div>
                    @if($perf['total_revenue'] > 0)
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-percentage mr-1"></i>
                            {{ number_format(($perf['total_revenue'] / $totalRevenue) * 100, 1) }}% of total revenue
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if(empty($performance))
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">No performance data available for the selected period</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Add any interactive features here
</script>
@endpush
