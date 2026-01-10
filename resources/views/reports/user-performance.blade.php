@extends('layouts.adminlte')

@section('title', 'User Performance Report')
@section('page-title', 'User Performance Report')
@section('breadcrumb', 'Reports / User Performance')

@push('styles')
<style>
    .performance-card {
        border-left: 4px solid #007bff;
        transition: transform 0.2s;
    }
    .performance-card:hover {
        transform: translateX(5px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filter -->
    <div class="card card-primary card-outline mb-4">
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-4">
                    <label>Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}" required>
                </div>
                <div class="col-md-4">
                    <label>Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}" required>
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Cards -->
    <div class="row">
        @foreach($performance as $perf)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card performance-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-1">{{ $perf['username'] }}</h5>
                            <span class="badge badge-{{ $perf['type'] == 'Admin' ? 'danger' : 'info' }}">
                                {{ $perf['type'] }}
                            </span>
                        </div>
                        <i class="fas fa-user-tie fa-2x text-muted"></i>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-right">
                                <div class="text-muted small">Bookings</div>
                                <div class="h5 mb-0">{{ $perf['total_bookings'] }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border-right">
                                <div class="text-muted small">Revenue</div>
                                <div class="h5 mb-0 text-success">${{ number_format($perf['total_revenue'], 0) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted small">Paid</div>
                            <div class="h5 mb-0 text-primary">${{ number_format($perf['paid_revenue'], 0) }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Conversion Rate</span>
                            <span class="font-weight-bold">{{ $perf['conversion_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $perf['conversion_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
