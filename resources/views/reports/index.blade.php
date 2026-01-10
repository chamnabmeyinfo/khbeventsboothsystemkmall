@extends('layouts.adminlte')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')
@section('breadcrumb', 'Reports')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.css">
<style>
    .report-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .report-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Reports & Analytics Dashboard</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Select a report to view detailed analytics and insights.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Cards -->
    <div class="row">
        <!-- Sales Report -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card bg-gradient-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-2">Sales Report</h5>
                            <p class="text-white-50 mb-0">Revenue & bookings analysis</p>
                        </div>
                        <div class="report-icon text-white">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                    <a href="{{ route('reports.sales') }}" class="btn btn-light btn-sm mt-3">
                        <i class="fas fa-arrow-right mr-1"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Booking Trends -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card bg-gradient-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-2">Booking Trends</h5>
                            <p class="text-white-50 mb-0">Track booking patterns</p>
                        </div>
                        <div class="report-icon text-white">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <a href="{{ route('reports.trends') }}" class="btn btn-light btn-sm mt-3">
                        <i class="fas fa-arrow-right mr-1"></i>View Report
                    </a>
                </div>
            </div>
        </div>

        <!-- User Performance -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card report-card bg-gradient-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title text-white mb-2">User Performance</h5>
                            <p class="text-white-50 mb-0">Sales team analytics</p>
                        </div>
                        <div class="report-icon text-white">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <a href="{{ route('reports.user-performance') }}" class="btn btn-light btn-sm mt-3">
                        <i class="fas fa-arrow-right mr-1"></i>View Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tachometer-alt mr-2"></i>Quick Statistics</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Bookings</span>
                                    <span class="info-box-number">{{ \App\Models\Book::count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Revenue</span>
                                    <span class="info-box-number">${{ number_format(\App\Models\Booth::where('status', \App\Models\Booth::STATUS_PAID)->sum('price'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-building"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Clients</span>
                                    <span class="info-box-number">{{ \App\Models\Client::count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-cube"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Booths</span>
                                    <span class="info-box-number">{{ \App\Models\Booth::count() }}</span>
                                </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush
