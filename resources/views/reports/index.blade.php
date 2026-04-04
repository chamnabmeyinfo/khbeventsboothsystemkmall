@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')
@section('breadcrumb', 'Insights / Reports')


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
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px;">
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
                        <button type="button" class="btn btn-light btn-sm" onclick="refreshPage()"
                            title="Reload this page so the summary numbers at the top match the latest data in the database."
                            aria-label="Refresh reports dashboard">
                            <i class="fas fa-sync-alt mr-1" aria-hidden="true"></i>Refresh
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
            <div class="report-card primary" onclick="window.location='{{ route('reports.sales') }}'" role="link" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location='{{ route('reports.sales') }}';}"
                title="Open Sales Report: revenue and bookings for a date range you choose (default range on the report page).">
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
                    <a href="{{ route('reports.sales') }}" class="btn btn-primary btn-block" onclick="event.stopPropagation()"
                        title="Go to Sales Report (same as clicking the card).">
                        <i class="fas fa-arrow-right mr-1" aria-hidden="true"></i>View Sales Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Booking Trends -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="report-card info" onclick="window.location='{{ route('reports.trends') }}'" role="link" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location='{{ route('reports.trends') }}';}"
                title="Open Booking Trends: daily booking activity over a number of days you can adjust.">
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
                    <a href="{{ route('reports.trends') }}" class="btn btn-info btn-block" onclick="event.stopPropagation()"
                        title="Go to Booking Trends (same as clicking the card).">
                        <i class="fas fa-arrow-right mr-1" aria-hidden="true"></i>View Trends
                    </a>
                </div>
            </div>
        </div>

        <!-- User Performance -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="report-card success" onclick="window.location='{{ route('reports.user-performance') }}'" role="link" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();window.location='{{ route('reports.user-performance') }}';}"
                title="Open User Performance: revenue and bookings attributed to each staff user.">
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
                    <a href="{{ route('reports.user-performance') }}" class="btn btn-success btn-block" onclick="event.stopPropagation()"
                        title="Go to User Performance (same as clicking the card).">
                        <i class="fas fa-arrow-right mr-1" aria-hidden="true"></i>View Performance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick report shortcuts: each opens a report with dates/filters pre-filled -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="glass-card">
                <div class="p-4 border-bottom">
                    <h3 class="h5 fw-bold mb-1 text-dark"><i class="fas fa-bolt mr-2" aria-hidden="true"></i>Quick report shortcuts</h3>
                    <p class="text-muted small mb-0">Each button opens the named report in a new context: Sales shortcuts set <strong>date from</strong> and <strong>date to</strong> on the Sales report; Trends opens Booking Trends with a fixed day range. Change dates on the report page anytime.</p>
                </div>
                <div class="p-4">
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-4">
                            <a href="{{ route('reports.sales', ['date_from' => now()->subDays(7)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}"
                                class="btn btn-outline-primary btn-block"
                                id="qa-sales-7"
                                title="Opens Sales Report: bookings from 7 days ago through today, revenue table and chart for that window."
                                aria-describedby="qa-sales-7-help">
                                <i class="fas fa-calendar-week mr-1" aria-hidden="true"></i>Last 7 Days
                            </a>
                            <p class="small text-muted mb-0 mt-2" id="qa-sales-7-help"><strong>Goes to:</strong> Sales Report. <strong>Does:</strong> sets booking date range to the last 7 days (including today). You can adjust filters on the next page.</p>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <a href="{{ route('reports.sales', ['date_from' => now()->subDays(30)->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}"
                                class="btn btn-outline-primary btn-block"
                                id="qa-sales-30"
                                title="Opens Sales Report: bookings from 30 days ago through today."
                                aria-describedby="qa-sales-30-help">
                                <i class="fas fa-calendar-alt mr-1" aria-hidden="true"></i>Last 30 Days
                            </a>
                            <p class="small text-muted mb-0 mt-2" id="qa-sales-30-help"><strong>Goes to:</strong> Sales Report. <strong>Does:</strong> sets the range to the last 30 days through today.</p>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <a href="{{ route('reports.sales', ['date_from' => now()->startOfMonth()->format('Y-m-d'), 'date_to' => now()->format('Y-m-d')]) }}"
                                class="btn btn-outline-primary btn-block"
                                id="qa-sales-month"
                                title="Opens Sales Report: from the first day of this calendar month through today."
                                aria-describedby="qa-sales-month-help">
                                <i class="fas fa-calendar mr-1" aria-hidden="true"></i>This Month
                            </a>
                            <p class="small text-muted mb-0 mt-2" id="qa-sales-month-help"><strong>Goes to:</strong> Sales Report. <strong>Does:</strong> sets range from the start of the current month to today (calendar month, not rolling 30 days).</p>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-4">
                            <a href="{{ route('reports.trends', ['days' => 90]) }}"
                                class="btn btn-outline-info btn-block"
                                id="qa-trends-90"
                                title="Opens Booking Trends: daily booking counts for the last 90 days."
                                aria-describedby="qa-trends-90-help">
                                <i class="fas fa-chart-area mr-1" aria-hidden="true"></i>90-Day Trends
                            </a>
                            <p class="small text-muted mb-0 mt-2" id="qa-trends-90-help"><strong>Goes to:</strong> Booking Trends (not Sales). <strong>Does:</strong> shows trend charts for the last 90 days. Use the report page to change the number of days.</p>
                        </div>
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

