@extends('layouts.adminlte')

@section('title', 'Booth Pricing Management')
@section('page-title', 'Booth Pricing Management')
@section('breadcrumb', 'Finance / Booth Pricing')

@push('styles')
<style>
    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
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
    .kpi-card.warning::before { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .kpi-card.info::before { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
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
    .kpi-card.warning .kpi-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .kpi-card.info .kpi-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
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
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    .bulk-actions {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        display: none;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
        transition: all 0.3s;
    }
    .bulk-actions.active {
        display: block;
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .bulk-actions input,
    .bulk-actions button {
        border-radius: 8px;
    }
    .table-row-hover {
        transition: all 0.2s;
    }
    .table-row-hover:hover {
        background-color: #f8f9fc;
        transform: scale(1.01);
    }
    .price-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-block;
    }
    .btn-modern {
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    .action-buttons .btn {
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }
    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    .quick-edit-btn {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
        color: white;
    }
    .quick-edit-btn:hover {
        background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
        color: white;
    }
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        border: none;
    }
    .modal-header .close {
        color: white;
        opacity: 0.9;
    }
    .modal-header .close:hover {
        opacity: 1;
    }
    .form-control-modern {
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s;
    }
    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    /* View Toggle Styles */
    .view-toggle {
        display: inline-flex;
        background: #f7fafc;
        border-radius: 12px;
        padding: 4px;
        border: 2px solid #e2e8f0;
        gap: 4px;
    }
    .view-toggle button {
        border: none;
        background: transparent;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        color: #718096;
        white-space: nowrap;
    }
    .view-toggle button.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    .view-toggle button:hover:not(.active) {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }
    
    /* Zone Section Styles */
    .zone-section {
        margin-bottom: 24px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s;
    }
    .zone-section:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }
    .zone-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px 24px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
        user-select: none;
    }
    .zone-header:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
    .zone-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
    }
    .zone-header .zone-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-left: 12px;
    }
    .zone-header .zone-toggle {
        font-size: 1.2rem;
        transition: transform 0.3s;
    }
    .zone-section.collapsed .zone-toggle {
        transform: rotate(-90deg);
    }
    .zone-content {
        padding: 0;
    }
    .zone-content .table {
        margin-bottom: 0;
    }
    
    /* Card View Styles */
    .booth-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        height: 100%;
        position: relative;
    }
    .booth-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: #667eea;
    }
    .booth-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f1f5f9;
    }
    .booth-card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }
    .booth-card-id {
        color: #718096;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .booth-card-body {
        margin-bottom: 16px;
    }
    .booth-card-field {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .booth-card-field:last-child {
        border-bottom: none;
    }
    .booth-card-label {
        color: #718096;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .booth-card-value {
        color: #2d3748;
        font-weight: 600;
        font-size: 0.9rem;
    }
    .booth-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 2px solid #f1f5f9;
    }
    .booth-card-checkbox {
        position: absolute;
        top: 16px;
        right: 16px;
        transform: scale(1.3);
        cursor: pointer;
    }
    
    /* Tab View Styles */
    .zone-tabs {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .zone-tabs-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 0;
        display: flex;
        overflow-x: auto;
        border-bottom: none;
    }
    .zone-tabs-header .nav-item {
        margin: 0;
    }
    .zone-tabs-header .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 16px 24px;
        border: none;
        border-radius: 0;
        font-weight: 600;
        transition: all 0.3s;
        white-space: nowrap;
        position: relative;
    }
    .zone-tabs-header .nav-link:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }
    .zone-tabs-header .nav-link.active {
        color: white;
        background: rgba(255, 255, 255, 0.2);
    }
    .zone-tabs-header .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: white;
    }
    .zone-tabs-content {
        padding: 0;
    }
    .zone-tab-pane {
        display: none;
    }
    .zone-tab-pane.active {
        display: block;
    }
    
    /* View Container Styles */
    .view-container {
        display: none;
    }
    .view-container.active {
        display: block;
        animation: fadeIn 0.3s ease-in;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('finance.payments.index') }}">Finance</a></li>
            <li class="breadcrumb-item active">Booth Pricing</li>
        </ol>
    </nav>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-th-large"></i></div>
                    <div class="kpi-label">Total Booths</div>
                    <div class="kpi-value">{{ number_format($stats['total_booths']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="kpi-label">Total Value</div>
                    <div class="kpi-value">${{ number_format($stats['total_value'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-calculator"></i></div>
                    <div class="kpi-label">Average Price</div>
                    <div class="kpi-value">${{ number_format($stats['average_price'], 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="kpi-label">Price Range</div>
                    <div class="kpi-value" style="font-size: 1.5rem;">
                        ${{ number_format($stats['min_price'], 2) }} - ${{ number_format($stats['max_price'], 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0" style="color: white;">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong><span id="selectedCount">0</span> booth(s) selected</strong>
                </h5>
            </div>
            <div class="col-md-6">
                <div class="input-group" style="max-width: 400px; margin-left: auto;">
                    <div class="input-group-prepend">
                        <span class="input-group-text" style="background: white; border: none; border-radius: 8px 0 0 8px;">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                    </div>
                    <input type="number" class="form-control" id="bulkPrice" placeholder="Enter new price" min="0" step="0.01" style="border: none; border-radius: 0;">
                    <div class="input-group-append">
                        <button class="btn btn-light" id="bulkUpdateBtn" style="border-radius: 0 8px 8px 0; border: none; font-weight: 600;">
                            <i class="fas fa-save mr-1"></i>Update Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar with View Toggle -->
    <div class="card mb-4" style="border-radius: 16px; border: none; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign mr-2 text-primary"></i>Booth Pricing Management</h5>
                </div>
                <div class="col-md-6 text-right">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <div class="view-toggle">
                            <button type="button" class="active" onclick="switchView('table')" id="viewTable" title="Table View">
                                <i class="fas fa-table mr-1"></i>Table
                            </button>
                            <button type="button" onclick="switchView('tab')" id="viewTab" title="Tab View">
                                <i class="fas fa-folder-open mr-1"></i>Tab
                            </button>
                            <button type="button" onclick="switchView('card')" id="viewCard" title="Card View">
                                <i class="fas fa-th-large mr-1"></i>Card
                            </button>
                        </div>
                        <a href="{{ route('finance.booth-pricing.export', request()->all()) }}" class="btn btn-success btn-modern">
                            <i class="fas fa-download mr-1"></i>Export CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('finance.booth-pricing.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label><i class="fas fa-map mr-1 text-primary"></i>Floor Plan</label>
                    <select name="floor_plan_id" class="form-control form-control-modern">
                        <option value="">All Floor Plans</option>
                        @foreach($floorPlans as $floorPlan)
                            <option value="{{ $floorPlan->id }}" {{ request('floor_plan_id') == $floorPlan->id ? 'selected' : '' }}>
                                {{ $floorPlan->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label><i class="fas fa-search mr-1 text-primary"></i>Search</label>
                    <input type="text" name="search" class="form-control form-control-modern" 
                           placeholder="Search by booth number or ID" 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-modern btn-block">
                            <i class="fas fa-filter mr-1"></i>Filter
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('finance.booth-pricing.index') }}" class="btn btn-secondary btn-sm btn-modern">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if(isset($zones) && count($zones) > 0)
        <!-- TABLE VIEW -->
        <div id="tableView" class="view-container active">
            @foreach($zones as $zoneName => $zoneBooths)
                <div class="zone-section" data-zone="{{ $zoneName }}">
                    <div class="zone-header" onclick="toggleZone('{{ $zoneName }}')">
                        <h5>
                            <i class="fas fa-layer-group mr-2"></i>
                            Zone {{ $zoneName }}
                            <span class="zone-count">({{ count($zoneBooths) }})</span>
                        </h5>
                        <div>
                            <i class="fas fa-chevron-down zone-toggle"></i>
                        </div>
                    </div>
                    <div class="zone-content" id="zone-table-{{ $zoneName }}">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light" style="background: #f8f9fc;">
                                    <tr>
                                        <th style="width: 50px; padding: 16px;">
                                            <input type="checkbox" class="zone-select-all" data-zone="{{ $zoneName }}" style="cursor: pointer; transform: scale(1.2);">
                                        </th>
                                        <th style="padding: 16px; font-weight: 600; color: #2d3748;">Booth ID</th>
                                        <th style="padding: 16px; font-weight: 600; color: #2d3748;">Booth Number</th>
                                        <th style="padding: 16px; font-weight: 600; color: #2d3748;">Floor Plan</th>
                                        <th style="padding: 16px; font-weight: 600; color: #2d3748;">Current Price</th>
                                        <th style="padding: 16px; font-weight: 600; color: #2d3748;">Status</th>
                                        <th style="padding: 16px; font-weight: 600; color: #2d3748; width: 180px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($zoneBooths as $booth)
                                        <tr class="table-row-hover">
                                            <td style="padding: 16px; vertical-align: middle;">
                                                <input type="checkbox" class="booth-checkbox" value="{{ $booth->id }}" data-zone="{{ $zoneName }}" style="cursor: pointer; transform: scale(1.2);">
                                            </td>
                                            <td style="padding: 16px; vertical-align: middle;">
                                                <strong class="text-primary">#{{ $booth->id }}</strong>
                                            </td>
                                            <td style="padding: 16px; vertical-align: middle;">
                                                <strong style="font-size: 1.1rem; color: #2d3748;">{{ $booth->booth_number }}</strong>
                                            </td>
                                            <td style="padding: 16px; vertical-align: middle;">
                                                <span class="badge badge-info" style="padding: 6px 12px; font-size: 0.875rem;">
                                                    {{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}
                                                </span>
                                            </td>
                                            <td style="padding: 16px; vertical-align: middle;">
                                                <span class="price-badge">${{ number_format($booth->price ?? 0, 2) }}</span>
                                            </td>
                                            <td style="padding: 16px; vertical-align: middle;">
                                                @if($booth->status == 1)
                                                    <span class="badge badge-success" style="padding: 8px 14px; font-size: 0.875rem; font-weight: 600;">
                                                        <i class="fas fa-check-circle mr-1"></i>Available
                                                    </span>
                                                @elseif($booth->status == 2)
                                                    <span class="badge badge-warning" style="padding: 8px 14px; font-size: 0.875rem; font-weight: 600;">
                                                        <i class="fas fa-calendar-check mr-1"></i>Booked
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary" style="padding: 8px 14px; font-size: 0.875rem; font-weight: 600;">
                                                        <i class="fas fa-ban mr-1"></i>Unavailable
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="padding: 16px; vertical-align: middle;">
                                                <div class="action-buttons">
                                                    <a href="{{ route('finance.booth-pricing.edit', $booth->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <button class="btn btn-sm quick-edit-btn" 
                                                            data-booth-id="{{ $booth->id }}" 
                                                            data-current-price="{{ $booth->price ?? 0 }}" 
                                                            data-booth-number="{{ $booth->booth_number }}"
                                                            title="Quick Edit">
                                                        <i class="fas fa-bolt"></i> Quick
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- TAB VIEW -->
        <div id="tabView" class="view-container">
            <div class="zone-tabs">
                <ul class="nav zone-tabs-header" role="tablist">
                    @foreach($zones as $zoneName => $zoneBooths)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" 
                               id="tab-{{ $zoneName }}-tab" 
                               data-toggle="tab" 
                               href="#tab-{{ $zoneName }}" 
                               role="tab"
                               onclick="switchZoneTab('{{ $zoneName }}')">
                                Zone {{ $zoneName }} <span class="badge badge-light ml-2">{{ count($zoneBooths) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content zone-tabs-content">
                    @foreach($zones as $zoneName => $zoneBooths)
                        <div class="zone-tab-pane {{ $loop->first ? 'active' : '' }}" 
                             id="tab-{{ $zoneName }}" 
                             role="tabpanel">
                            <div class="table-responsive" style="padding: 24px;">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light" style="background: #f8f9fc;">
                                        <tr>
                                            <th style="width: 50px; padding: 16px;">
                                                <input type="checkbox" class="zone-select-all-tab" data-zone="{{ $zoneName }}" style="cursor: pointer; transform: scale(1.2);">
                                            </th>
                                            <th style="padding: 16px; font-weight: 600; color: #2d3748;">Booth ID</th>
                                            <th style="padding: 16px; font-weight: 600; color: #2d3748;">Booth Number</th>
                                            <th style="padding: 16px; font-weight: 600; color: #2d3748;">Floor Plan</th>
                                            <th style="padding: 16px; font-weight: 600; color: #2d3748;">Current Price</th>
                                            <th style="padding: 16px; font-weight: 600; color: #2d3748;">Status</th>
                                            <th style="padding: 16px; font-weight: 600; color: #2d3748; width: 180px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($zoneBooths as $booth)
                                            <tr class="table-row-hover">
                                                <td style="padding: 16px; vertical-align: middle;">
                                                    <input type="checkbox" class="booth-checkbox" value="{{ $booth->id }}" data-zone="{{ $zoneName }}" style="cursor: pointer; transform: scale(1.2);">
                                                </td>
                                                <td style="padding: 16px; vertical-align: middle;">
                                                    <strong class="text-primary">#{{ $booth->id }}</strong>
                                                </td>
                                                <td style="padding: 16px; vertical-align: middle;">
                                                    <strong style="font-size: 1.1rem; color: #2d3748;">{{ $booth->booth_number }}</strong>
                                                </td>
                                                <td style="padding: 16px; vertical-align: middle;">
                                                    <span class="badge badge-info" style="padding: 6px 12px; font-size: 0.875rem;">
                                                        {{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}
                                                    </span>
                                                </td>
                                                <td style="padding: 16px; vertical-align: middle;">
                                                    <span class="price-badge">${{ number_format($booth->price ?? 0, 2) }}</span>
                                                </td>
                                                <td style="padding: 16px; vertical-align: middle;">
                                                    @if($booth->status == 1)
                                                        <span class="badge badge-success" style="padding: 8px 14px; font-size: 0.875rem; font-weight: 600;">
                                                            <i class="fas fa-check-circle mr-1"></i>Available
                                                        </span>
                                                    @elseif($booth->status == 2)
                                                        <span class="badge badge-warning" style="padding: 8px 14px; font-size: 0.875rem; font-weight: 600;">
                                                            <i class="fas fa-calendar-check mr-1"></i>Booked
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary" style="padding: 8px 14px; font-size: 0.875rem; font-weight: 600;">
                                                            <i class="fas fa-ban mr-1"></i>Unavailable
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="padding: 16px; vertical-align: middle;">
                                                    <div class="action-buttons">
                                                        <a href="{{ route('finance.booth-pricing.edit', $booth->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <button class="btn btn-sm quick-edit-btn" 
                                                                data-booth-id="{{ $booth->id }}" 
                                                                data-current-price="{{ $booth->price ?? 0 }}" 
                                                                data-booth-number="{{ $booth->booth_number }}"
                                                                title="Quick Edit">
                                                            <i class="fas fa-bolt"></i> Quick
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- CARD VIEW -->
        <div id="cardView" class="view-container">
            @foreach($zones as $zoneName => $zoneBooths)
                <div class="zone-section" data-zone="{{ $zoneName }}">
                    <div class="zone-header" onclick="toggleZoneCard('{{ $zoneName }}')">
                        <h5>
                            <i class="fas fa-layer-group mr-2"></i>
                            Zone {{ $zoneName }}
                            <span class="zone-count">({{ count($zoneBooths) }})</span>
                        </h5>
                        <div>
                            <i class="fas fa-chevron-down zone-toggle" id="zone-toggle-card-{{ $zoneName }}"></i>
                        </div>
                    </div>
                    <div class="zone-content" id="zone-card-{{ $zoneName }}">
                        <div class="row" style="padding: 24px;">
                            @foreach($zoneBooths as $booth)
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                    <div class="booth-card">
                                        <input type="checkbox" class="booth-checkbox booth-card-checkbox" value="{{ $booth->id }}" data-zone="{{ $zoneName }}">
                                        <div class="booth-card-header">
                                            <div>
                                                <h5 class="booth-card-title">{{ $booth->booth_number }}</h5>
                                                <div class="booth-card-id">ID: #{{ $booth->id }}</div>
                                            </div>
                                        </div>
                                        <div class="booth-card-body">
                                            <div class="booth-card-field">
                                                <span class="booth-card-label"><i class="fas fa-map mr-2 text-primary"></i>Floor Plan</span>
                                                <span class="booth-card-value">
                                                    <span class="badge badge-info">{{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}</span>
                                                </span>
                                            </div>
                                            <div class="booth-card-field">
                                                <span class="booth-card-label"><i class="fas fa-dollar-sign mr-2 text-success"></i>Price</span>
                                                <span class="booth-card-value">
                                                    <span class="price-badge">${{ number_format($booth->price ?? 0, 2) }}</span>
                                                </span>
                                            </div>
                                            <div class="booth-card-field">
                                                <span class="booth-card-label"><i class="fas fa-info-circle mr-2 text-info"></i>Status</span>
                                                <span class="booth-card-value">
                                                    @if($booth->status == 1)
                                                        <span class="badge badge-success" style="padding: 6px 10px;">
                                                            <i class="fas fa-check-circle mr-1"></i>Available
                                                        </span>
                                                    @elseif($booth->status == 2)
                                                        <span class="badge badge-warning" style="padding: 6px 10px;">
                                                            <i class="fas fa-calendar-check mr-1"></i>Booked
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary" style="padding: 6px 10px;">
                                                            <i class="fas fa-ban mr-1"></i>Unavailable
                                                        </span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="booth-card-footer">
                                            <a href="{{ route('finance.booth-pricing.edit', $booth->id) }}" class="btn btn-primary btn-sm btn-modern">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                            <button class="btn btn-sm quick-edit-btn btn-modern" 
                                                    data-booth-id="{{ $booth->id }}" 
                                                    data-current-price="{{ $booth->price ?? 0 }}" 
                                                    data-booth-number="{{ $booth->booth_number }}">
                                                <i class="fas fa-bolt mr-1"></i>Quick
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);">
            <div class="card-body text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                    <p class="mb-0" style="font-size: 1.1rem;">No booths found</p>
                    <small>Try adjusting your filters</small>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Quick Edit Modal -->
<div class="modal fade" id="quickEditModal" tabindex="-1" role="dialog" aria-labelledby="quickEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickEditModalLabel">
                    <i class="fas fa-bolt mr-2"></i>Quick Edit Price
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="quickEditForm">
                <div class="modal-body" style="padding: 24px;">
                    <input type="hidden" id="quickEditBoothId">
                    <div class="form-group">
                        <label style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-hashtag mr-2 text-primary"></i>Booth Number
                        </label>
                        <input type="text" class="form-control form-control-modern" id="quickEditBoothNumber" readonly style="background: #f8f9fc;">
                    </div>
                    <div class="form-group">
                        <label style="font-weight: 600; color: #2d3748;">
                            <i class="fas fa-dollar-sign mr-2 text-primary"></i>New Price <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                            </div>
                            <input type="number" class="form-control form-control-modern" id="quickEditPrice" min="0" step="0.01" required style="border-left: none;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 16px 24px;">
                    <button type="button" class="btn btn-secondary btn-modern" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-save mr-1"></i>Update Price
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/sweetalert2/js/sweetalert2.min.js') }}"></script>
<script>
$(document).ready(function() {
    // View Switch Function
    window.switchView = function(view) {
        // Hide all views
        $('.view-container').removeClass('active');
        
        // Show selected view
        if (view === 'table') {
            $('#tableView').addClass('active');
            $('#viewTable').addClass('active');
            $('#viewTab').removeClass('active');
            $('#viewCard').removeClass('active');
            localStorage.setItem('boothPricingView', 'table');
        } else if (view === 'tab') {
            $('#tabView').addClass('active');
            $('#viewTab').addClass('active');
            $('#viewTable').removeClass('active');
            $('#viewCard').removeClass('active');
            localStorage.setItem('boothPricingView', 'tab');
        } else if (view === 'card') {
            $('#cardView').addClass('active');
            $('#viewCard').addClass('active');
            $('#viewTable').removeClass('active');
            $('#viewTab').removeClass('active');
            localStorage.setItem('boothPricingView', 'card');
        }
    };

    // Load saved view preference
    const savedView = localStorage.getItem('boothPricingView') || 'table';
    switchView(savedView);

    // Zone Tab Switch Function
    window.switchZoneTab = function(zoneName) {
        $('.zone-tab-pane').removeClass('active');
        $('#tab-' + zoneName).addClass('active');
        
        $('.zone-tabs-header .nav-link').removeClass('active');
        $('#tab-' + zoneName + '-tab').addClass('active');
    };

    // Zone toggle function for table view
    window.toggleZone = function(zoneName) {
        const zoneSection = document.querySelector(`#tableView .zone-section[data-zone="${zoneName}"]`);
        if (zoneSection) {
            zoneSection.classList.toggle('collapsed');
            const zoneContent = document.getElementById(`zone-table-${zoneName}`);
            if (zoneSection.classList.contains('collapsed')) {
                zoneContent.style.display = 'none';
            } else {
                zoneContent.style.display = 'block';
            }
        }
    };

    // Zone toggle function for card view
    window.toggleZoneCard = function(zoneName) {
        const zoneSection = document.querySelector(`#cardView .zone-section[data-zone="${zoneName}"]`);
        if (zoneSection) {
            zoneSection.classList.toggle('collapsed');
            const zoneContent = document.getElementById(`zone-card-${zoneName}`);
            if (zoneSection.classList.contains('collapsed')) {
                zoneContent.style.display = 'none';
            } else {
                zoneContent.style.display = 'block';
            }
        }
    };

    // Zone select all checkbox (table view)
    $(document).on('change', '.zone-select-all', function() {
        const zoneName = $(this).data('zone');
        const isChecked = $(this).prop('checked');
        $(`.booth-checkbox[data-zone="${zoneName}"]`).prop('checked', isChecked);
        updateBulkActions();
    });

    // Zone select all checkbox (tab view)
    $(document).on('change', '.zone-select-all-tab', function() {
        const zoneName = $(this).data('zone');
        const isChecked = $(this).prop('checked');
        $(`.booth-checkbox[data-zone="${zoneName}"]`).prop('checked', isChecked);
        updateBulkActions();
    });

    // Individual checkbox change
    $(document).on('change', '.booth-checkbox', function() {
        updateBulkActions();
        const zoneName = $(this).data('zone');
        if (zoneName) {
            updateZoneSelectAll(zoneName);
        }
    });

    function updateZoneSelectAll(zoneName) {
        const zoneCheckboxes = $(`.booth-checkbox[data-zone="${zoneName}"]`);
        const checkedCount = zoneCheckboxes.filter(':checked').length;
        const totalCount = zoneCheckboxes.length;
        $(`.zone-select-all[data-zone="${zoneName}"]`).prop('checked', totalCount === checkedCount && totalCount > 0);
        $(`.zone-select-all-tab[data-zone="${zoneName}"]`).prop('checked', totalCount === checkedCount && totalCount > 0);
    }

    function updateBulkActions() {
        var count = $('.booth-checkbox:checked').length;
        $('#selectedCount').text(count);
        if (count > 0) {
            $('#bulkActions').addClass('active');
        } else {
            $('#bulkActions').removeClass('active');
        }
    }

    // Bulk update
    $('#bulkUpdateBtn').on('click', function() {
        var selectedIds = [];
        $('.booth-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select at least one booth',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        var price = parseFloat($('#bulkPrice').val());
        if (isNaN(price) || price < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid price (>= 0)',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        Swal.fire({
            title: 'Confirm Bulk Update',
            html: `<p>Update price to <strong>$${price.toFixed(2)}</strong> for <strong>${selectedIds.length} booth(s)</strong>?</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check mr-1"></i>Yes, Update',
            cancelButtonText: '<i class="fas fa-times mr-1"></i>Cancel',
            confirmButtonColor: '#667eea',
            cancelButtonColor: '#aaa',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("finance.booth-pricing.bulk-update") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        booth_ids: selectedIds,
                        price: price
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                confirmButtonColor: '#667eea',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                                confirmButtonColor: '#667eea'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonColor: '#667eea'
                        });
                    }
                });
            }
        });
    });

    // Quick edit modal
    $(document).on('click', '.quick-edit-btn', function() {
        var boothId = $(this).data('booth-id');
        var currentPrice = $(this).data('current-price');
        var boothNumber = $(this).data('booth-number');

        $('#quickEditBoothId').val(boothId);
        $('#quickEditBoothNumber').val(boothNumber);
        $('#quickEditPrice').val(currentPrice);
        $('#quickEditModal').modal('show');
    });

    // Quick edit form submission
    $('#quickEditForm').on('submit', function(e) {
        e.preventDefault();
        
        var boothId = $('#quickEditBoothId').val();
        var price = parseFloat($('#quickEditPrice').val());

        if (isNaN(price) || price < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid price (>= 0)',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        $.ajax({
            url: '/finance/booth-pricing/' + boothId,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                _method: 'PUT',
                price: price
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonColor: '#667eea',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        $('#quickEditModal').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message,
                        confirmButtonColor: '#667eea'
                    });
                }
            },
            error: function(xhr) {
                var message = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonColor: '#667eea'
                });
            }
        });
    });
});
</script>
@endpush
