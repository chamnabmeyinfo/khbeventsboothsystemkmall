@extends('layouts.adminlte')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')
@section('breadcrumb', 'Insights / Reports')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    /* Modern Glassmorphism Report Cards */
    .report-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        cursor: pointer;
    }

    .report-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .report-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .report-card:hover::before {
        opacity: 1;
    }

    .report-card.primary::before { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .report-card.success::before { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .report-card.info::before { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
    .report-card.warning::before { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

    .report-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: white;
        margin-bottom: 20px;
    }

    .report-card.primary .report-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .report-card.success .report-icon { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .report-card.info .report-icon { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
    .report-card.warning .report-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

    .stat-box {
        background: rgba(255, 255, 255, 0.7);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }

    .stat-box:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateX(4px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px;">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="text-white mb-2" style="font-weight: 700;">
                            <i class="fas fa-chart-line mr-2"></i>Reports & Analytics
                        </h2>
                        <p class="text-white mb-0" style="opacity: 0.9;">
                            Comprehensive insights and data-driven analytics for your business
                        </p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <button type="button" class="btn btn-light btn-sm" onclick="refreshPage()">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Bookings</h6>
                        <h3 class="mb-0 text-primary">{{ number_format(\App\Models\Book::count()) }}</h3>
                    </div>
                    <div class="text-primary" style="font-size: 2rem; opacity: 0.3;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Revenue</h6>
                        <h3 class="mb-0 text-success">${{ number_format(\App\Models\Booth::where('status', \App\Models\Booth::STATUS_PAID)->sum('price'), 2) }}</h3>
                    </div>
                    <div class="text-success" style="font-size: 2rem; opacity: 0.3;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Clients</h6>
                        <h3 class="mb-0 text-info">{{ number_format(\App\Models\Client::count()) }}</h3>
                    </div>
                    <div class="text-info" style="font-size: 2rem; opacity: 0.3;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-box">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Booths</h6>
                        <h3 class="mb-0 text-warning">{{ number_format(\App\Models\Booth::count()) }}</h3>
                    </div>
                    <div class="text-warning" style="font-size: 2rem; opacity: 0.3;">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Sales Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="report-card primary" onclick="window.location='{{ route('reports.sales') }}'">
                <div style="padding: 32px;">
                    <div class="report-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h4 class="mb-2" style="font-weight: 700; color: #2d3748;">Sales Report</h4>
                    <p class="text-muted mb-3">Revenue & bookings analysis with detailed breakdowns</p>
                    <ul class="list-unstyled mb-3">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Revenue trends over time
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Paid vs pending analysis
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Group by day/week/month
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Export to CSV/PDF
                        </li>
                    </ul>
                    <a href="{{ route('reports.sales') }}" class="btn btn-primary btn-block" onclick="event.stopPropagation()">
                        <i class="fas fa-arrow-right mr-1"></i>View Sales Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Booking Trends -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="report-card info" onclick="window.location='{{ route('reports.trends') }}'">
                <div style="padding: 32px;">
                    <div class="report-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4 class="mb-2" style="font-weight: 700; color: #2d3748;">Booking Trends</h4>
                    <p class="text-muted mb-3">Track booking patterns and identify trends</p>
                    <ul class="list-unstyled mb-3">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-info mr-2"></i>
                            Daily booking counts
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-info mr-2"></i>
                            Confirmed vs paid trends
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-info mr-2"></i>
                            Customizable time periods
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-info mr-2"></i>
                            Visual trend analysis
                        </li>
                    </ul>
                    <a href="{{ route('reports.trends') }}" class="btn btn-info btn-block" onclick="event.stopPropagation()">
                        <i class="fas fa-arrow-right mr-1"></i>View Trends
                    </a>
                </div>
            </div>
        </div>

        <!-- User Performance -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="report-card success" onclick="window.location='{{ route('reports.user-performance') }}'">
                <div style="padding: 32px;">
                    <div class="report-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="mb-2" style="font-weight: 700; color: #2d3748;">User Performance</h4>
                    <p class="text-muted mb-3">Sales team analytics and performance metrics</p>
                    <ul class="list-unstyled mb-3">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Revenue by user
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Booking counts per user
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Conversion rates
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Performance rankings
                        </li>
                    </ul>
                    <a href="{{ route('reports.user-performance') }}" class="btn btn-success btn-block" onclick="event.stopPropagation()">
                        <i class="fas fa-arrow-right mr-1"></i>View Performance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bolt mr-2"></i>Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reports.sales', ['date_from' => now()->subDays(7)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-calendar-week mr-1"></i>Last 7 Days
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reports.sales', ['date_from' => now()->subDays(30)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-calendar-alt mr-1"></i>Last 30 Days
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reports.sales', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-calendar mr-1"></i>This Month
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reports.trends', ['days' => 90]) }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-chart-area mr-1"></i>90-Day Trends
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}
</script>
@endpush

