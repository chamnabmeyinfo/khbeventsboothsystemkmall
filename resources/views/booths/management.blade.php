@extends('layouts.adminlte')

@section('title', 'Booth Management')
@section('page-title', 'Booth Management')
@section('breadcrumb', 'Booths / Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .booth-image-preview {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .booth-image-preview:hover {
        transform: scale(1.1);
        border-color: #667eea;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
    }
    
    .stat-card.success {
        background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
    }
    
    .stat-card.info {
        background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);
    }
    
    .stat-card.danger {
        background: linear-gradient(135deg, #e74a3b 0%, #c23321 100%);
    }
    
    /* Quick Filters */
    .quick-filter-btn {
        font-weight: 600;
        transition: all 0.3s ease;
        border-radius: 8px;
        margin-bottom: 8px;
        white-space: nowrap;
    }
    
    .quick-filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .quick-filter-btn.active {
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        /* Quick Filters - Stack on mobile */
        .btn-group {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        
        .quick-filter-btn {
            width: 100%;
            margin-bottom: 8px;
            font-size: 14px;
            padding: 10px 15px;
        }
        
        /* Stat Cards - Full width on mobile */
        .stat-card {
            margin-bottom: 15px;
            padding: 15px;
        }
        
        .stat-card h3 {
            font-size: 1.5rem;
        }
        
        .stat-card p {
            font-size: 0.85rem;
        }
        
        /* Filter Bar - Stack inputs */
        .filter-bar .row {
            flex-direction: column;
        }
        
        .filter-bar .col-md-3,
        .filter-bar .col-md-2 {
            width: 100%;
            margin-bottom: 10px;
        }
        
        /* Table - Make responsive */
        .table-modern {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-modern table {
            min-width: 800px;
        }
        
        /* Action Buttons - Stack on mobile */
        .action-buttons {
            flex-direction: column;
            gap: 4px;
        }
        
        .action-buttons .btn {
            width: 100%;
            margin: 2px 0;
        }
        
        /* Modal - Full screen on mobile */
        .modal-dialog {
            margin: 0;
            max-width: 100%;
            height: 100vh;
        }
        
        .modal-content {
            height: 100vh;
            border-radius: 0;
        }
        
        /* Reduce padding on mobile */
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        /* Stats section */
        .row.mb-4 {
            margin-bottom: 1rem !important;
        }
        
        /* Page header */
        .d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .d-flex.justify-content-between h2 {
            margin-bottom: 10px;
        }
        
        .d-flex.justify-content-between .btn {
            width: 100%;
        }
    }
    
    /* Tablet Specific */
    @media (min-width: 769px) and (max-width: 1024px) {
        .quick-filter-btn {
            font-size: 12px;
            padding: 8px 12px;
        }
        
        .stat-card {
            padding: 15px;
        }
        
        .table-modern thead th {
            font-size: 0.75rem;
            padding: 12px;
        }
        
        .table-modern tbody td {
            font-size: 0.85rem;
            padding: 10px;
        }
    }
    
    /* Touch-friendly improvements */
    @media (hover: none) and (pointer: coarse) {
        /* Increase touch targets */
        .btn, .quick-filter-btn, .action-buttons .btn {
            min-height: 44px;
            min-width: 44px;
        }
        
        /* Remove hover effects on touch devices */
        .quick-filter-btn:hover,
        .stat-card:hover {
            transform: none;
        }
        
        /* Make checkboxes larger */
        input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }
    }
    
    /* Mobile Card View (Alternative to table) */
    @media (max-width: 768px) {
        .mobile-booth-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        
        .mobile-booth-card.available {
            border-left-color: #28a745;
        }
        
        .mobile-booth-card.paid {
            border-left-color: #17a673;
        }
        
        .mobile-booth-card.reserved {
            border-left-color: #f6c23e;
        }
        
        .mobile-booth-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .mobile-booth-number {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .mobile-booth-info {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .mobile-info-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }
        
        .mobile-info-label {
            color: #6c757d;
            font-weight: 600;
        }
        
        .mobile-info-value {
            color: #333;
            font-weight: 500;
        }
        
        .mobile-booth-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .mobile-booth-actions .btn {
            flex: 1;
            min-width: calc(50% - 4px);
            font-size: 0.85rem;
            padding: 8px 12px;
        }
        
        /* Hide desktop table on mobile */
        .table-responsive.d-none-mobile {
            display: none;
        }
        
        /* Show mobile cards on mobile */
        .mobile-view {
            display: block;
        }
    }
    
    /* Show table on desktop */
    @media (min-width: 769px) {
        .mobile-view {
            display: none;
        }
        
        .table-responsive.d-none-mobile {
            display: block;
        }
    }
    
    /* Improve form inputs on mobile */
    @media (max-width: 768px) {
        .form-control, .form-select, select.form-control {
            font-size: 16px; /* Prevents iOS zoom */
            padding: 12px;
            height: auto;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        
        /* Better spacing */
        .card-body {
            padding: 15px;
        }
        
        .card-header {
            padding: 12px 15px;
        }
        
        /* Improve pagination */
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .pagination .page-link {
            padding: 8px 12px;
            min-width: 40px;
        }
    }
    
    .filter-bar {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .table-modern {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .table-modern thead th {
        border: none;
        padding: 16px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .table-modern tbody tr {
        transition: all 0.2s;
    }
    
    .table-modern tbody tr:hover {
        background: #f8f9fc;
        transform: scale(1.01);
    }
    
    .table-modern tbody td {
        padding: 16px;
        vertical-align: middle;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    
    .btn-action {
        width: 36px;
        height: 36px;
        padding: 0;
    }
    
    /* Modal Form Styles */
    #boothModal .nav-pills .nav-link {
        border-radius: 8px 8px 0 0;
        padding: 12px 20px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s;
        border: none;
        background: transparent;
    }
    
    #boothModal .nav-pills .nav-link:hover {
        color: #667eea;
        background: rgba(102, 126, 234, 0.1);
    }
    
    #boothModal .nav-pills .nav-link.active {
        color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-bottom: 3px solid #667eea;
    }
    
    #boothModal .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    #boothModal .form-label {
        display: flex;
        align-items: center;
    }
    
    #boothModal .tab-content {
        padding-top: 20px;
    }
    
    #boothModal .image-upload-area:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    }
    
    @media (max-width: 768px) {
        #boothModal .nav-pills {
            flex-direction: column;
        }
        
        #boothModal .nav-pills .nav-link {
            border-radius: 8px;
            margin-bottom: 5px;
        }
    }
    
    /* Modern Pagination Footer */
    .card-footer-modern {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-top: 2px solid #e9ecef;
        padding: 20px 30px;
        border-radius: 0 0 12px 12px;
    }
    
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .pagination-info {
        display: flex;
        align-items: center;
    }
    
    .pagination-text {
        color: #495057;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }
    
    .pagination-text strong {
        color: #667eea;
        font-weight: 700;
        margin: 0 4px;
    }
    
    .pagination-controls {
        display: flex;
        align-items: center;
    }
    
    /* Custom Pagination Styles */
    .pagination {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .pagination .page-item {
        margin: 0;
    }
    
    .pagination .page-link {
        color: #667eea;
        background: white;
        border: 2px solid #e9ecef;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        min-width: 44px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .pagination .page-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .pagination .page-item.disabled .page-link:hover {
        transform: none;
        box-shadow: none;
        background: #f8f9fa;
        color: #adb5bd;
    }
    
    .pagination .page-link i {
        font-size: 0.85rem;
    }
    
    @media (max-width: 768px) {
        .pagination-wrapper {
            flex-direction: column;
            align-items: stretch;
        }
        
        .pagination-info {
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .pagination-controls {
            justify-content: center;
        }
        
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .pagination .page-link {
            padding: 8px 12px;
            min-width: 40px;
            font-size: 0.9rem;
        }
    }
    
    .btn-action {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    #boothModal .modal-dialog {
        max-width: 90%;
        margin: 1.75rem auto;
    }
    
    #boothModal .modal-content {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    
    #boothModal .modal-body {
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1 1 auto;
        padding: 20px;
    }
    
    #boothModal .modal-footer {
        position: sticky;
        bottom: 0;
        background: white;
        border-top: 1px solid #dee2e6;
        z-index: 10;
        padding: 15px 20px;
    }
    
    #boothModal .form-group {
        margin-bottom: 1.25rem;
    }
    
    #boothModal .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    #boothModal .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 0.625rem 0.75rem;
        transition: all 0.2s;
    }
    
    #boothModal .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    #boothModal h6 {
        color: #667eea;
        font-weight: 700;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    .image-upload-area {
        border: 2px dashed #667eea;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        background: #f8f9fc;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .image-upload-area:hover {
        background: #e9ecef;
        border-color: #764ba2;
    }
    
    .image-upload-area.dragover {
        background: #e3f2fd;
        border-color: #2196f3;
    }
    
    .image-preview-container {
        position: relative;
        display: inline-block;
        margin-top: 16px;
    }
    
    .image-preview-container img {
        max-width: 300px;
        max-height: 300px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .remove-image-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e74a3b;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    /* Status Settings Modal Styles */
    #statusSettingsModal .modal-dialog {
        max-width: 95%;
    }
    
    #statusSettingsModal .form-control-color {
        height: 38px;
        width: 60px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        cursor: pointer;
        padding: 2px;
    }
    
    #statusSettingsModal .form-control-color:hover {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    #statusSettingsTable tbody tr {
        cursor: move;
        transition: background-color 0.2s;
    }
    
    #statusSettingsTable tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    #statusSettingsTable .status-bg-color,
    #statusSettingsTable .status-border-color,
    #statusSettingsTable .status-text-color {
        cursor: pointer;
    }
    
    .status-color-preview {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: 2px solid #dee2e6;
        margin-right: 8px;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header with Status Settings Button -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="fas fa-store me-2"></i>Booth Management</h2>
                <button type="button" class="btn btn-primary" onclick="openStatusSettingsModal()">
                    <i class="fas fa-tags me-2"></i>Booth Status Settings
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="stat-card">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['total'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Total Booths</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card success">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['available'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Available</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card warning">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['reserved'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Reserved</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card info">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['confirmed'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Confirmed</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card danger">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['paid'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Paid</div>
            </div>
        </div>
        <div class="col-md-2">
            <a href="{{ url('/booths?view=canvas') }}" class="btn btn-lg btn-primary w-100 h-100 d-flex align-items-center justify-content-center" style="border-radius: 12px; text-decoration: none;">
                <i class="fas fa-map mr-2"></i>Canvas View
            </a>
        </div>
    </div>

    <!-- Quick Filters -->
    <div class="mb-3">
        <div class="btn-group" role="group" aria-label="Quick Filters">
            <button type="button" class="btn btn-outline-primary quick-filter-btn" onclick="applyQuickFilter('all')">
                <i class="fas fa-list"></i> All Booths
            </button>
            <button type="button" class="btn btn-outline-success quick-filter-btn" onclick="applyQuickFilter('available')">
                <i class="fas fa-check-circle"></i> Available Only
            </button>
            <button type="button" class="btn btn-outline-info quick-filter-btn" onclick="applyQuickFilter('booked')">
                <i class="fas fa-bookmark"></i> Booked Only
            </button>
            <button type="button" class="btn btn-outline-warning quick-filter-btn" onclick="applyQuickFilter('paid')">
                <i class="fas fa-dollar-sign"></i> Paid Only
            </button>
            <button type="button" class="btn btn-outline-secondary quick-filter-btn" onclick="applyQuickFilter('today')">
                <i class="fas fa-calendar-day"></i> Booked Today
            </button>
            <button type="button" class="btn btn-outline-danger quick-filter-btn" onclick="applyQuickFilter('overdue')">
                <i class="fas fa-exclamation-triangle"></i> Overdue Payments
            </button>
        </div>
        <button type="button" class="btn btn-link float-right" onclick="clearAllFilters()">
            <i class="fas fa-times-circle"></i> Clear Filters
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('booths.index', ['view' => 'table']) }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-search mr-1"></i>Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Booth number, company, category..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-map mr-1"></i>Floor Plan</label>
                    <select name="floor_plan_id" class="form-control">
                        <option value="">All Floor Plans</option>
                        @foreach($floorPlans as $fp)
                            <option value="{{ $fp->id }}" {{ request('floor_plan_id') == $fp->id ? 'selected' : '' }}>
                                {{ $fp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-tag mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Available</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Confirmed</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Reserved</option>
                        <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Hidden</option>
                        <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-building mr-1"></i>Booth Type</label>
                    <select name="booth_type_id" class="form-control">
                        <option value="">All Types</option>
                        @foreach($boothTypes as $type)
                            <option value="{{ $type->id }}" {{ request('booth_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-folder mr-1"></i>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Actions Bar -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" class="btn btn-success" onclick="openCreateModal()">
                        <i class="fas fa-plus mr-1"></i>Create New Booth
                    </button>
                    <button type="button" class="btn btn-warning" onclick="bulkUpdateStatus()">
                        <i class="fas fa-edit mr-1"></i>Bulk Update
                    </button>
                    <button type="button" class="btn btn-danger" onclick="bulkDelete()">
                        <i class="fas fa-trash mr-1"></i>Bulk Delete
                    </button>
                </div>
                <div>
                    <a href="{{ route('booths.index', ['view' => 'table', 'export' => 'csv']) }}" class="btn btn-info">
                        <i class="fas fa-download mr-1"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Booths Table -->
    <div class="card table-modern">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="boothsTable">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Image</th>
                        <th>Booth #</th>
                        <th>Type</th>
                        <th>Floor Plan</th>
                        <th>Company</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Area</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($booths as $booth)
                    <tr>
                        <td>
                            <input type="checkbox" class="booth-checkbox" value="{{ $booth->id }}">
                        </td>
                        <td>
                            @if($booth->booth_image)
                                <img src="{{ asset($booth->booth_image) }}" alt="Booth Image" class="booth-image-preview" onclick="viewImage('{{ asset($booth->booth_image) }}')">
                            @else
                                <div style="width: 60px; height: 60px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="font-size: 1.1rem; color: #2d3748;">{{ $booth->booth_number }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                {{ $booth->boothType ? $booth->boothType->name : ($booth->type == 1 ? 'Booth' : 'Space Only') }}
                            </span>
                        </td>
                        <td>
                            {{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}
                        </td>
                        <td>
                            {{ $booth->client ? $booth->client->company : 'N/A' }}
                        </td>
                        <td>
                            {{ $booth->category ? $booth->category->name : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $booth->getStatusColor() }}">
                                {{ $booth->getStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            <strong style="color: #28a745;">${{ number_format($booth->price, 2) }}</strong>
                        </td>
                        <td>
                            {{ $booth->area_sqm ? number_format($booth->area_sqm, 2) . ' m²' : 'N/A' }}
                        </td>
                        <td>
                            {{ $booth->capacity ? $booth->capacity . ' people' : 'N/A' }}
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-info btn-action" onclick="viewBooth({{ $booth->id }})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editBooth({{ $booth->id }})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteBooth({{ $booth->id }})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No booths found. Create your first booth!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($booths->hasPages())
        <div class="card-footer-modern">
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    <span class="pagination-text">
                        <i class="fas fa-list mr-2"></i>
                        Showing <strong>{{ $booths->firstItem() }}</strong> to <strong>{{ $booths->lastItem() }}</strong> of <strong>{{ $booths->total() }}</strong> booths
                    </span>
                </div>
                <div class="pagination-controls">
                    {{ $booths->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Booth Modal -->
<div class="modal fade" id="boothModal" tabindex="-1" role="dialog" aria-labelledby="boothModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0; padding: 20px 30px; border-bottom: none;">
                <h5 class="modal-title text-white" id="modalTitle" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-store mr-2"></i><span id="modalTitleText">Create New Booth</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="boothForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="boothId" name="id">
                <div class="modal-body" style="padding: 30px;">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-pills nav-fill mb-4" id="boothFormTabs" role="tablist" style="border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">
                        <li class="nav-item">
                            <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic-info" role="tab" aria-controls="basic-info" aria-selected="true">
                                <i class="fas fa-info-circle mr-2"></i>Basic Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="details-tab" data-toggle="tab" href="#details-info" role="tab" aria-controls="details-info" aria-selected="false">
                                <i class="fas fa-clipboard-list mr-2"></i>Details & Specifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="content-tab" data-toggle="tab" href="#content-info" role="tab" aria-controls="content-info" aria-selected="false">
                                <i class="fas fa-align-left mr-2"></i>Content & Description
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="media-tab" data-toggle="tab" href="#media-info" role="tab" aria-controls="media-info" aria-selected="false">
                                <i class="fas fa-image mr-2"></i>Media
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="boothFormTabContent">
                        <!-- Basic Information Tab -->
                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="booth_number" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-hashtag text-primary mr-2"></i>Booth Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="booth_number" id="booth_number" class="form-control" required 
                                               style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; transition: all 0.3s;"
                                               onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                               onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none'">
                                        <small class="form-text text-muted">Unique identifier for this booth</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="floor_plan_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-map text-primary mr-2"></i>Floor Plan <span class="text-danger">*</span>
                                        </label>
                                        <select name="floor_plan_id" id="floor_plan_id" class="form-control" required
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; transition: all 0.3s;"
                                                onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                                onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none'">
                                            <option value="">Select Floor Plan</option>
                                            @foreach($floorPlans as $fp)
                                                <option value="{{ $fp->id }}">{{ $fp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="booth_type_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-tags text-primary mr-2"></i>Booth Type
                                        </label>
                                        <select name="booth_type_id" id="booth_type_id" class="form-control"
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Type</option>
                                            @foreach($boothTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="type" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-cube text-primary mr-2"></i>Type <span class="text-danger">*</span>
                                        </label>
                                        <select name="type" id="type" class="form-control" required
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="1">Booth</option>
                                            <option value="2">Space Only</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-dollar-sign text-success mr-2"></i>Price <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" style="border-radius: 8px 0 0 8px; background: #f8f9fa; border: 1px solid #dee2e6;">$</span>
                                            </div>
                                            <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" required
                                                   style="border-radius: 0 8px 8px 0; border: 1px solid #dee2e6; padding: 10px 15px;"
                                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                                   onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none'">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="status" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-toggle-on text-primary mr-2"></i>Status <span class="text-danger">*</span>
                                        </label>
                                        <select name="status" id="status" class="form-control" required
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="1">Available</option>
                                            <option value="2">Confirmed</option>
                                            <option value="3">Reserved</option>
                                            <option value="4">Hidden</option>
                                            <option value="5">Paid</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="client_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-user-tie text-primary mr-2"></i>Client
                                        </label>
                                        <select name="client_id" id="client_id" class="form-control"
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->company ?? $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="category_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-folder text-primary mr-2"></i>Category
                                        </label>
                                        <select name="category_id" id="category_id" class="form-control"
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Details & Specifications Tab -->
                        <div class="tab-pane fade" id="details-info" role="tabpanel" aria-labelledby="details-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="area_sqm" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-ruler-combined text-primary mr-2"></i>Area (m²)
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="area_sqm" id="area_sqm" class="form-control" step="0.01" min="0"
                                                   style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="border-radius: 0 8px 8px 0; background: #f8f9fa; border: 1px solid #dee2e6;">m²</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="capacity" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-users text-primary mr-2"></i>Capacity (people)
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="capacity" id="capacity" class="form-control" min="0"
                                                   style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="border-radius: 0 8px 8px 0; background: #f8f9fa; border: 1px solid #dee2e6;">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="electricity_power" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-bolt text-warning mr-2"></i>Electricity Power
                                        </label>
                                        <input type="text" name="electricity_power" id="electricity_power" class="form-control" 
                                               placeholder="e.g., 10A, 20A, 30A"
                                               style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                        <small class="form-text text-muted">Specify the electrical power requirements</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content & Description Tab -->
                        <div class="tab-pane fade" id="content-info" role="tabpanel" aria-labelledby="content-tab">
                            <div class="form-group">
                                <label for="description" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                    <i class="fas fa-align-left text-primary mr-2"></i>Description
                                </label>
                                <textarea name="description" id="description" class="form-control" rows="5" 
                                          placeholder="Enter a detailed description of the booth..."
                                          style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px 15px; resize: vertical;"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="features" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                    <i class="fas fa-list-check text-primary mr-2"></i>Features
                                </label>
                                <textarea name="features" id="features" class="form-control" rows="5" 
                                          placeholder="List booth features (one per line)..."
                                          style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px 15px; resize: vertical;"></textarea>
                                <small class="form-text text-muted">Enter each feature on a new line</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                    <i class="fas fa-sticky-note text-primary mr-2"></i>Additional Notes
                                </label>
                                <textarea name="notes" id="notes" class="form-control" rows="4" 
                                          placeholder="Enter any additional notes or special instructions..."
                                          style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px 15px; resize: vertical;"></textarea>
                            </div>
                        </div>

                        <!-- Media Tab -->
                        <div class="tab-pane fade" id="media-info" role="tabpanel" aria-labelledby="media-tab">
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 15px;">
                                    <i class="fas fa-image text-primary mr-2"></i>Booth Image
                                </label>
                                <div class="image-upload-wrapper" style="position: relative;">
                                    <div class="image-upload-area" id="imageUploadArea" 
                                         onclick="document.getElementById('booth_image').click()"
                                         style="border: 2px dashed #667eea; border-radius: 12px; padding: 40px; text-align: center; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); cursor: pointer; transition: all 0.3s;"
                                         onmouseover="this.style.borderColor='#764ba2'; this.style.transform='scale(1.02)'"
                                         onmouseout="this.style.borderColor='#667eea'; this.style.transform='scale(1)'">
                                        <i class="fas fa-cloud-upload-alt fa-4x mb-3" style="color: #667eea;"></i>
                                        <p class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #495057;">Click to upload or drag and drop</p>
                                        <small class="text-muted">PNG, JPG, GIF up to 5MB</small>
                                    </div>
                                    <input type="file" name="booth_image" id="booth_image" class="d-none" accept="image/*" onchange="previewImage(this)">
                                    <div id="imagePreviewContainer" class="image-preview-container" style="display: none; margin-top: 20px; position: relative; text-align: center;">
                                        <div style="position: relative; display: inline-block;">
                                            <img id="imagePreview" src="" alt="Preview" 
                                                 style="max-width: 100%; max-height: 400px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                            <button type="button" class="remove-image-btn" onclick="removeImage()"
                                                    style="position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.2); transition: all 0.3s;"
                                                    onmouseover="this.style.transform='scale(1.1)'; this.style.background='#c82333'"
                                                    onmouseout="this.style.transform='scale(1)'; this.style.background='#dc3545'">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 20px 30px; background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" 
                            style="border-radius: 8px; padding: 10px 25px; font-weight: 600;">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px; padding: 10px 30px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                        <i class="fas fa-save mr-2"></i>Save Booth
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal fade" id="imageViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booth Image</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="viewImageSrc" src="" alt="Booth Image" style="max-width: 100%; border-radius: 12px;">
            </div>
        </div>
    </div>
</div>

<!-- Status Settings Modal -->
<div class="modal fade" id="statusSettingsModal" tabindex="-1" role="dialog" aria-labelledby="statusSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="statusSettingsModalLabel">
                    <i class="fas fa-tags me-2"></i>Booth Status Settings
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Customize booking status names, colors, and assign them to specific floor plans.</strong>
                    <br>Statuses assigned to a floor plan will only be available for that floor plan. Global statuses (no floor plan assigned) are available for all floor plans.
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Status Configuration</h6>
                        <small class="text-muted">Drag rows to reorder, or use the Order field</small>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnAddStatus">
                        <i class="fas fa-plus me-2"></i>Add New Status
                    </button>
                </div>

                <div id="statusSettingsContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading status settings...</p>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-success" id="btnSaveStatusSettings">
                        <i class="fas fa-save me-2"></i>Save All Status Settings
                    </button>
                    <button type="button" class="btn btn-secondary ms-2" id="btnResetStatusSettings">
                        <i class="fas fa-undo me-2"></i>Reset to Defaults
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
let currentBoothId = null;

// Initialize DataTable
$(document).ready(function() {
    $('#boothsTable').DataTable({
        pageLength: 50,
        order: [[2, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 1, 11] }
        ]
    });
    
    // Auto-scroll modal body to top and reset to first tab when modal is shown
    $('#boothModal').on('shown.bs.modal', function() {
        $(this).find('.modal-body').scrollTop(0);
        // Reset to first tab
        $('#basic-tab').tab('show');
    });
    
    // Auto-open edit modal if edit parameter is in URL
    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('edit');
    if (editId) {
        // Remove edit parameter from URL to clean it up
        urlParams.delete('edit');
        const newSearch = urlParams.toString();
        const newUrl = window.location.pathname + (newSearch ? '?' + newSearch : '');
        window.history.replaceState({}, '', newUrl);
        
        // Open edit modal after a short delay to ensure page is fully loaded
        setTimeout(function() {
            editBooth(editId);
        }, 500);
    }
});

// Open Create Modal
function openCreateModal() {
    currentBoothId = null;
    $('#modalTitleText').text('Create New Booth');
    $('#boothForm')[0].reset();
    $('#boothId').val('');
    $('#imagePreviewContainer').hide();
    // Reset to first tab
    $('#basic-tab').tab('show');
    $('#boothModal').modal('show');
}

// Edit Booth
function editBooth(id) {
    currentBoothId = id;
    $('#modalTitleText').text('Edit Booth');
    
    // Fetch booth data with proper headers to ensure JSON response
    // Use the JSON endpoint to ensure we always get JSON
    fetch(`/booths/${id}?json=1`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
        .then(response => {
            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Expected JSON but got:', text.substring(0, 200));
                    throw new Error('Server returned HTML instead of JSON. Check console for details.');
                });
            }
            
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            // Check if we got an error response
            if (data.error) {
                throw new Error(data.message || data.error);
            }
            
            $('#boothId').val(data.id);
            $('#booth_number').val(data.booth_number);
            $('#floor_plan_id').val(data.floor_plan_id);
            $('#booth_type_id').val(data.booth_type_id);
            $('#type').val(data.type);
            $('#price').val(data.price);
            $('#status').val(data.status);
            $('#client_id').val(data.client_id);
            $('#category_id').val(data.category_id);
            $('#area_sqm').val(data.area_sqm);
            $('#capacity').val(data.capacity);
            $('#electricity_power').val(data.electricity_power);
            $('#description').val(data.description || '');
            $('#features').val(data.features || '');
            $('#notes').val(data.notes || '');
            
            // Show image if exists
            if (data.booth_image) {
                $('#imagePreview').attr('src', data.booth_image);
                $('#imagePreviewContainer').show();
            } else {
                $('#imagePreviewContainer').hide();
            }
            
            // Reset to first tab
            $('#basic-tab').tab('show');
            $('#boothModal').modal('show');
        })
        .catch(error => {
            console.error('Error loading booth:', error);
            Swal.fire({
                icon: 'error',
                title: 'Failed to load booth data',
                text: error.message || 'An unexpected error occurred. Please try again.',
                confirmButtonText: 'OK'
            });
        });
}

// View Booth
function viewBooth(id) {
    window.location.href = `/booths/${id}`;
}

// Delete Booth
function deleteBooth(id) {
    Swal.fire({
        title: 'Delete Booth?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/booths/${id}`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Preview Image
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result);
            $('#imagePreviewContainer').show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove Image
function removeImage() {
    $('#booth_image').val('');
    $('#imagePreviewContainer').hide();
}

// View Image
function viewImage(src) {
    $('#viewImageSrc').attr('src', src);
    $('#imageViewModal').modal('show');
}

// Form Submit
$('#boothForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentBoothId ? `/booths/${currentBoothId}` : '/booths';
    const method = currentBoothId ? 'PUT' : 'POST';
    
    // Add _method for PUT
    if (currentBoothId) {
        formData.append('_method', 'PUT');
    }
    
    showLoading();
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        redirect: 'follow'
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            hideLoading();
            if (data.success || response.ok) {
                Swal.fire('Success', currentBoothId ? 'Booth updated successfully!' : 'Booth created successfully!', 'success')
                    .then(() => {
                        window.location.reload();
                    });
            } else {
                // Handle validation errors
                let errorMsg = data.message || 'An error occurred';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        } else {
            // If HTML response (redirect), reload the page
            hideLoading();
            Swal.fire('Success', currentBoothId ? 'Booth updated successfully!' : 'Booth created successfully!', 'success')
                .then(() => {
                    window.location.reload();
                });
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        Swal.fire('Error', 'An error occurred while saving: ' + error.message, 'error');
    });
});

// Toggle Select All
function toggleSelectAll() {
    const checked = $('#selectAll').is(':checked');
    $('.booth-checkbox').prop('checked', checked);
}

// Bulk Delete
function bulkDelete() {
    const selected = $('.booth-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selected.length === 0) {
        Swal.fire('Warning', 'Please select at least one booth', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Delete Selected Booths?',
        text: `You are about to delete ${selected.length} booth(s). This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch('/bulk/booths/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: selected })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    Swal.fire('Success', `${selected.length} booth(s) deleted successfully!`, 'success')
                        .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error', 'An error occurred', 'error');
            });
        }
    });
}

// Bulk Update Status
function bulkUpdateStatus() {
    const selected = $('.booth-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selected.length === 0) {
        Swal.fire('Warning', 'Please select at least one booth', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Update Status',
        input: 'select',
        inputOptions: {
            '1': 'Available',
            '2': 'Confirmed',
            '3': 'Reserved',
            '4': 'Hidden',
            '5': 'Paid'
        },
        inputPlaceholder: 'Select new status',
        showCancelButton: true,
        confirmButtonText: 'Update',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to select a status!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch('/bulk/booths/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    ids: selected,
                    field: 'status',
                    value: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    Swal.fire('Success', `Status updated for ${selected.length} booth(s)!`, 'success')
                        .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error', 'An error occurred', 'error');
            });
        }
    });
}

// Drag and drop for image
const imageUploadArea = document.getElementById('imageUploadArea');
if (imageUploadArea) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, () => {
            imageUploadArea.classList.add('dragover');
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, () => {
            imageUploadArea.classList.remove('dragover');
        }, false);
    });
    
    imageUploadArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            document.getElementById('booth_image').files = files;
            previewImage(document.getElementById('booth_image'));
        }
    }, false);
}

// Status Settings Modal
function openStatusSettingsModal() {
    loadStatusSettings();
    $('#statusSettingsModal').modal('show');
}

function loadStatusSettings() {
    $.get('{{ route("settings.booth-statuses") }}')
        .done(function(response) {
            if (response.status === 200) {
                renderStatusSettings(response.data);
            }
        })
        .fail(function() {
            $('#statusSettingsContainer').html('<div class="alert alert-danger">Failed to load status settings</div>');
        });
}

function renderStatusSettings(statuses) {
    if (!statuses || statuses.length === 0) {
        $('#statusSettingsContainer').html('<div class="alert alert-info">No status settings found. Click "Add New Status" to create one.</div>');
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-bordered table-hover" id="statusSettingsTable">';
    html += '<thead class="table-light"><tr>';
    html += '<th style="width: 60px;">Order</th>';
    html += '<th style="width: 80px;">Code</th>';
    html += '<th>Status Name</th>';
    html += '<th style="width: 150px;">Background</th>';
    html += '<th style="width: 150px;">Border Color</th>';
    html += '<th style="width: 100px;">Border Width</th>';
    html += '<th style="width: 120px;">Border Style</th>';
    html += '<th style="width: 100px;">Border Radius</th>';
    html += '<th style="width: 150px;">Text</th>';
    html += '<th style="width: 120px;">Badge</th>';
    html += '<th>Description</th>';
    html += '<th style="width: 200px;">Floor Plan</th>';
    html += '<th style="width: 100px;">Default</th>';
    html += '<th style="width: 100px;">Active</th>';
    html += '<th style="width: 120px;">Actions</th>';
    html += '</tr></thead><tbody id="statusSettingsBody">';

    statuses.forEach(function(status, index) {
        html += renderStatusRow(status, index);
    });

    html += '</tbody></table></div>';
    $('#statusSettingsContainer').html(html);
    
    // Make rows sortable
    makeStatusRowsSortable();
    
    // Attach event handlers
    attachStatusEventHandlers();
}

function renderStatusRow(status, index) {
    const rowId = 'status-row-' + (status.id || 'new-' + index);
    let html = '<tr id="' + rowId + '" data-status-id="' + (status.id || '') + '" data-status-code="' + status.status_code + '">';
    
    // Sort Order
    html += '<td><input type="number" class="form-control form-control-sm status-sort-order" value="' + (status.sort_order || 0) + '" min="0" style="width: 60px;"></td>';
    
    // Status Code
    html += '<td><input type="number" class="form-control form-control-sm status-code" value="' + status.status_code + '" min="1" required style="width: 70px;"></td>';
    
    // Status Name
    html += '<td><input type="text" class="form-control form-control-sm status-name" value="' + (status.status_name || '') + '" required maxlength="100"></td>';
    
    // Background Color with visual picker
    html += '<td><div class="input-group input-group-sm"><input type="color" class="form-control form-control-color status-bg-color" value="' + (status.status_color || '#28a745') + '" style="width: 60px; height: 38px;"><input type="text" class="form-control form-control-sm status-bg-color-text" value="' + (status.status_color || '#28a745') + '" maxlength="7" style="width: 80px;"></div></td>';
    
    // Border Color with visual picker
    html += '<td><div class="input-group input-group-sm"><input type="color" class="form-control form-control-color status-border-color" value="' + (status.border_color || status.status_color || '#28a745') + '" style="width: 60px; height: 38px;"><input type="text" class="form-control form-control-sm status-border-color-text" value="' + (status.border_color || status.status_color || '#28a745') + '" maxlength="7" style="width: 80px;"></div></td>';
    
    // Border Width
    html += '<td><input type="number" class="form-control form-control-sm status-border-width" value="' + (status.border_width || 2) + '" min="0" max="10" style="width: 80px;" title="Border width (0-10px)"></td>';
    
    // Border Style
    html += '<td><select class="form-control form-control-sm status-border-style" style="width: 100px;">';
    const borderStyles = ['solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset', 'none'];
    borderStyles.forEach(function(style) {
        html += '<option value="' + style + '"' + ((status.border_style || 'solid') === style ? ' selected' : '') + '>' + style.charAt(0).toUpperCase() + style.slice(1) + '</option>';
    });
    html += '</select></td>';
    
    // Border Radius
    html += '<td><input type="number" class="form-control form-control-sm status-border-radius" value="' + (status.border_radius || 4) + '" min="0" max="50" style="width: 80px;" title="Border radius (0-50px)"></td>';
    
    // Text Color with visual picker
    html += '<td><div class="input-group input-group-sm"><input type="color" class="form-control form-control-color status-text-color" value="' + (status.text_color || '#ffffff') + '" style="width: 60px; height: 38px;"><input type="text" class="form-control form-control-sm status-text-color-text" value="' + (status.text_color || '#ffffff') + '" maxlength="7" style="width: 80px;"></div></td>';
    
    // Badge Color
    html += '<td><select class="form-control form-control-sm status-badge-color">';
    const badgeColors = ['success', 'info', 'warning', 'danger', 'primary', 'secondary', 'dark', 'light'];
    badgeColors.forEach(function(color) {
        html += '<option value="' + color + '"' + (status.badge_color === color ? ' selected' : '') + '>' + color.charAt(0).toUpperCase() + color.slice(1) + '</option>';
    });
    html += '</select></td>';
    
    // Description
    html += '<td><input type="text" class="form-control form-control-sm status-description" value="' + (status.description || '') + '" placeholder="Status description"></td>';
    
    // Floor Plan Assignment
    html += '<td><select class="form-control form-control-sm status-floor-plan">';
    html += '<option value="">Global (All Floor Plans)</option>';
    @foreach($floorPlans as $fp)
    html += '<option value="{{ $fp->id }}"' + (status.floor_plan_id == {{ $fp->id }} ? ' selected' : '') + '>{{ $fp->name }}</option>';
    @endforeach
    html += '</select></td>';
    
    // Is Default
    html += '<td class="text-center"><input type="checkbox" class="form-check-input status-is-default" ' + (status.is_default ? 'checked' : '') + '></td>';
    
    // Is Active
    html += '<td class="text-center"><input type="checkbox" class="form-check-input status-is-active" ' + (status.is_active !== false ? 'checked' : '') + '></td>';
    
    // Actions
    html += '<td class="text-center">';
    html += '<button type="button" class="btn btn-sm btn-danger btn-delete-status" title="Delete"><i class="fas fa-trash"></i></button>';
    html += '</td>';
    
    html += '</tr>';
    return html;
}

function makeStatusRowsSortable() {
    $('#statusSettingsBody').sortable({
        handle: '.status-sort-order',
        axis: 'y',
        update: function(event, ui) {
            $('#statusSettingsBody tr').each(function(index) {
                $(this).find('.status-sort-order').val(index + 1);
            });
        }
    });
}

function attachStatusEventHandlers() {
    // Sync color pickers with text inputs
    $('.status-bg-color, .status-border-color, .status-text-color').on('change', function() {
        const textInput = $(this).siblings('input[type="text"]');
        textInput.val($(this).val());
    });

    $('.status-bg-color-text, .status-border-color-text, .status-text-color-text').on('input', function() {
        const colorInput = $(this).siblings('input[type="color"]');
        const value = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            colorInput.val(value);
        }
    });

    // Only one default status allowed
    $('.status-is-default').on('change', function() {
        if ($(this).is(':checked')) {
            $('.status-is-default').not(this).prop('checked', false);
        }
    });

    // Delete status
    $('.btn-delete-status').on('click', function() {
        const row = $(this).closest('tr');
        const statusId = row.data('status-id');
        const statusName = row.find('.status-name').val();

        if (!confirm('Are you sure you want to delete status "' + statusName + '"? This action cannot be undone.')) {
            return;
        }

        if (statusId) {
            $.ajax({
                url: '{{ route("settings.booth-statuses.delete", ":id") }}'.replace(':id', statusId),
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        row.fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to delete status');
                }
            });
        } else {
            row.fadeOut(300, function() {
                $(this).remove();
            });
        }
    });
}

$('#btnAddStatus').on('click', function() {
    const maxCode = Math.max(...$('.status-code').map(function() {
        return parseInt($(this).val()) || 0;
    }).get(), 0);
    
        const newStatus = {
            id: null,
            status_code: maxCode + 1,
            status_name: 'New Status',
            status_color: '#6c757d',
            border_color: '#6c757d',
            border_width: 2,
            border_style: 'solid',
            border_radius: 4,
            text_color: '#ffffff',
            badge_color: 'secondary',
            description: '',
            floor_plan_id: null,
            is_active: true,
            sort_order: $('#statusSettingsBody tr').length + 1,
            is_default: false
        };

    if ($('#statusSettingsBody').length === 0) {
        renderStatusSettings([newStatus]);
    } else {
        const newRow = $(renderStatusRow(newStatus, $('#statusSettingsBody tr').length));
        $('#statusSettingsBody').append(newRow);
        attachStatusEventHandlers();
    }
});

$('#btnSaveStatusSettings').on('click', function() {
    const btn = $(this);
    const originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

    const statuses = [];
    $('#statusSettingsBody tr').each(function() {
        const row = $(this);
        const statusId = row.data('status-id');
        
        const floorPlanId = row.find('.status-floor-plan').val();
        
        statuses.push({
            id: statusId || null,
            status_code: parseInt(row.find('.status-code').val()) || 1,
            status_name: row.find('.status-name').val() || '',
            status_color: row.find('.status-bg-color-text').val() || '#28a745',
            border_color: row.find('.status-border-color-text').val() || null,
            border_width: parseInt(row.find('.status-border-width').val()) || 2,
            border_style: row.find('.status-border-style').val() || 'solid',
            border_radius: parseInt(row.find('.status-border-radius').val()) || 4,
            text_color: row.find('.status-text-color-text').val() || '#ffffff',
            badge_color: row.find('.status-badge-color').val() || 'success',
            description: row.find('.status-description').val() || '',
            floor_plan_id: (floorPlanId && floorPlanId !== '') ? parseInt(floorPlanId) : null,
            is_active: row.find('.status-is-active').is(':checked'),
            sort_order: parseInt(row.find('.status-sort-order').val()) || 0,
            is_default: row.find('.status-is-default').is(':checked')
        });
    });

    $.ajax({
        url: '{{ route("settings.booth-statuses.save") }}',
        method: 'POST',
        data: {
            statuses: statuses,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.status === 200) {
                toastr.success(response.message || 'Status settings saved successfully');
                setTimeout(function() {
                    loadStatusSettings();
                }, 500);
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors || {};
            let message = xhr.responseJSON?.message || 'Failed to save status settings';
            if (Object.keys(errors).length > 0) {
                message += ': ' + Object.values(errors).flat().join(', ');
            }
            toastr.error(message);
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
});

// Load on page load if modal is opened
$(document).ready(function() {
    if ($('#statusSettingsModal').hasClass('show')) {
        loadStatusSettings();
    }
});

// Quick Filter Functions
function applyQuickFilter(filterType) {
    const form = document.getElementById('filterForm');
    const searchInput = form.querySelector('input[name="search"]');
    const statusSelect = form.querySelector('select[name="status"]');
    const categorySelect = form.querySelector('select[name="category_id"]');
    
    // Clear existing filters first
    searchInput.value = '';
    statusSelect.value = '';
    categorySelect.value = '';
    
    // Highlight active button
    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.quick-filter-btn').classList.add('active');
    
    // Apply filter based on type
    switch(filterType) {
        case 'all':
            // No filters, just submit
            break;
            
        case 'available':
            statusSelect.value = '1'; // Available status
            break;
            
        case 'booked':
            // Filter for confirmed, reserved, or paid (not available)
            statusSelect.value = '2'; // Confirmed
            break;
            
        case 'paid':
            statusSelect.value = '5'; // Paid status
            break;
            
        case 'today':
            // Add hidden input for today's bookings
            let todayInput = form.querySelector('input[name="booked_today"]');
            if (!todayInput) {
                todayInput = document.createElement('input');
                todayInput.type = 'hidden';
                todayInput.name = 'booked_today';
                form.appendChild(todayInput);
            }
            todayInput.value = '1';
            break;
            
        case 'overdue':
            // Add hidden input for overdue payments
            let overdueInput = form.querySelector('input[name="payment_overdue"]');
            if (!overdueInput) {
                overdueInput = document.createElement('input');
                overdueInput.type = 'hidden';
                overdueInput.name = 'payment_overdue';
                form.appendChild(overdueInput);
            }
            overdueInput.value = '1';
            break;
    }
    
    // Submit the form
    form.submit();
}

function clearAllFilters() {
    const form = document.getElementById('filterForm');
    
    // Clear all inputs
    form.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
    form.querySelectorAll('select').forEach(select => select.value = '');
    
    // Remove hidden inputs
    form.querySelectorAll('input[type="hidden"]').forEach(input => {
        if (input.name === 'booked_today' || input.name === 'payment_overdue') {
            input.remove();
        }
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Submit to show all
    form.submit();
}

// Mark active filter on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const bookedToday = urlParams.get('booked_today');
    const paymentOverdue = urlParams.get('payment_overdue');
    
    // Highlight appropriate button based on current filters
    if (bookedToday) {
        document.querySelector('.quick-filter-btn[onclick*="today"]')?.classList.add('active');
    } else if (paymentOverdue) {
        document.querySelector('.quick-filter-btn[onclick*="overdue"]')?.classList.add('active');
    } else if (status === '1') {
        document.querySelector('.quick-filter-btn[onclick*="available"]')?.classList.add('active');
    } else if (status === '5') {
        document.querySelector('.quick-filter-btn[onclick*="paid"]')?.classList.add('active');
    } else if (status === '2' || status === '3') {
        document.querySelector('.quick-filter-btn[onclick*="booked"]')?.classList.add('active');
    } else if (!status && !bookedToday && !paymentOverdue) {
        document.querySelector('.quick-filter-btn[onclick*="all"]')?.classList.add('active');
    }
});
</script>
@endpush
