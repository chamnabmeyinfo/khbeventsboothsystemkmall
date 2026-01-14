@extends('layouts.adminlte')

@section('title', 'Create Booking')
@section('page-title', 'Create New Booking')
@section('breadcrumb', 'Bookings / Create')

@push('styles')
<style>
    /* ============================================
       MODERN BOOKING CREATE PAGE DESIGN 2026
       Glassmorphism + Gradients + Smooth Animations
       ============================================ */
    
    :root {
        --booking-primary: #667eea;
        --booking-secondary: #764ba2;
        --booking-success: #1cc88a;
        --booking-warning: #f6c23e;
        --booking-danger: #e74a3b;
        --booking-info: #36b9cc;
    }
    
    /* Modern Form Sections */
    .form-section {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(102, 126, 234, 0.1);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.08),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
        position: relative;
        overflow: visible; /* Changed from hidden to allow dropdowns */
        z-index: 1;
    }
    
    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--booking-primary) 0%, var(--booking-secondary) 100%);
    }
    
    .form-section:hover {
        box-shadow: 
            0 12px 48px rgba(102, 126, 234, 0.12),
            inset 0 1px 0 rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }
    
    .form-section h6 {
        color: #1a1a2e;
        font-weight: 700;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
    }
    
    .form-section h6 i {
        background: linear-gradient(135deg, var(--booking-primary) 0%, var(--booking-secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-right: 0.75rem;
        font-size: 1.2rem;
    }
    
    /* Modern Card */
    .card-modern {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(102, 126, 234, 0.1);
        border-radius: 16px;
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        overflow: visible; /* Changed from hidden to allow dropdowns */
        position: relative;
        z-index: 1;
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, 
            rgba(102, 126, 234, 0.1) 0%,
            rgba(118, 75, 162, 0.1) 100%);
        border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        padding: 1.5rem;
        font-weight: 700;
    }
    
    .card-header-modern h3 {
        margin: 0;
        color: #1a1a2e;
        font-weight: 800;
        background: linear-gradient(135deg, var(--booking-primary) 0%, var(--booking-secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Modern Form Controls */
    .form-control-modern {
        border-radius: 12px;
        border: 2px solid rgba(102, 126, 234, 0.1);
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }
    
    .form-control-modern:focus {
        border-color: var(--booking-primary);
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        transform: translateY(-1px);
    }
    
    /* Modern Buttons */
    .btn-modern {
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, var(--booking-primary) 0%, var(--booking-secondary) 100%);
        color: white;
    }
    
    .btn-modern-primary:hover {
        background: linear-gradient(135deg, var(--booking-secondary) 0%, var(--booking-primary) 100%);
        color: white;
    }
    
    .btn-modern-success {
        background: linear-gradient(135deg, var(--booking-success) 0%, #17a673 100%);
        color: white;
    }
    
    .btn-modern-info {
        background: linear-gradient(135deg, var(--booking-info) 0%, #2c9faf 100%);
        color: white;
    }
    
    /* Booth Selector */
    .booth-selector {
        max-height: 500px;
        overflow-y: auto;
        background: rgba(248, 249, 252, 0.5);
        border-radius: 12px;
        padding: 1.5rem;
        border: 2px solid rgba(102, 126, 234, 0.1);
    }
    
    .booth-selector::-webkit-scrollbar {
        width: 8px;
    }
    
    .booth-selector::-webkit-scrollbar-track {
        background: rgba(102, 126, 234, 0.05);
        border-radius: 10px;
    }
    
    .booth-selector::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--booking-primary) 0%, var(--booking-secondary) 100%);
        border-radius: 10px;
    }
    
    .booth-option {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        border: 2px solid rgba(102, 126, 234, 0.1) !important;
        border-radius: 12px;
        padding: 1rem;
        position: relative;
        overflow: hidden;
    }
    
    .booth-option::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--booking-primary) 0%, var(--booking-secondary) 100%);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }
    
    .booth-option:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-color: var(--booking-primary) !important;
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
    }
    
    .booth-option:hover::before {
        transform: scaleY(1);
    }
    
    .booth-option.selected {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-color: var(--booking-primary) !important;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .booth-option.selected::before {
        transform: scaleY(1);
    }
    
    .booth-option input[type="checkbox"] {
        margin-right: 0.75rem;
        cursor: pointer;
        width: 20px;
        height: 20px;
        accent-color: var(--booking-primary);
    }
    
    .booth-option label {
        cursor: pointer;
        width: 100%;
        margin: 0;
    }
    
    /* Selected Booths Summary */
    .selected-booths-summary {
        position: sticky;
        top: 20px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(102, 126, 234, 0.1);
    }
    
    .selected-booths-summary h6 {
        color: #1a1a2e;
        font-weight: 700;
        margin-bottom: 1rem;
        font-size: 1rem;
    }
    
    /* Modern Alert */
    .alert-modern {
        border-radius: 12px;
        border: none;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .alert-modern-info {
        background: linear-gradient(135deg, rgba(54, 185, 204, 0.1) 0%, rgba(44, 159, 175, 0.1) 100%);
        border-left: 4px solid var(--booking-info);
        color: #1a1a2e;
    }
    
    /* Client Search Modal */
    .modal-content-modern {
        border-radius: 16px;
        border: none;
        box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .modal-header-modern {
        background: linear-gradient(135deg, var(--booking-primary) 0%, var(--booking-secondary) 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }
    
    .modal-header-modern .close {
        color: white;
        opacity: 0.9;
        text-shadow: none;
    }
    
    .modal-header-modern .close:hover {
        opacity: 1;
    }
    
    /* Client Search Wrapper - Redesigned */
    .client-search-wrapper {
        margin-bottom: 0;
        position: relative;
        z-index: 100;
        /* Create stacking context to prevent overlap */
        isolation: isolate;
    }
    
    .input-group-modern {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-radius: 12px;
        overflow: visible; /* Changed from hidden to allow dropdown */
        position: relative;
        background: white;
    }
    
    .input-group-modern .input-group-prepend {
        border-right: none;
    }
    
    .input-group-modern .input-group-text {
        border-radius: 12px 0 0 12px;
        border: 2px solid rgba(102, 126, 234, 0.1);
        border-right: none;
        background: white;
        padding: 0.75rem 1rem;
        z-index: 1;
    }
    
    .input-group-modern .form-control {
        border-left: none;
        border-right: none;
        border-top: 2px solid rgba(102, 126, 234, 0.1);
        border-bottom: 2px solid rgba(102, 126, 234, 0.1);
        padding: 0.75rem 1rem;
        background: white;
        z-index: 1;
    }
    
    .input-group-modern .form-control:focus {
        border-color: var(--booking-primary);
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        z-index: 2;
    }
    
    .input-group-modern .input-group-append {
        border-left: none;
    }
    
    .input-group-modern .input-group-append .btn {
        border-radius: 0 12px 12px 0;
        border-left: 2px solid rgba(102, 126, 234, 0.1);
        z-index: 1;
    }
    
    /* Client Results Dropdown - Fixed Positioning */
    .client-results-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        right: 0;
        z-index: 9999; /* Very high z-index to ensure it's on top */
        animation: slideDown 0.2s ease-out;
        /* Ensure it doesn't get clipped */
        pointer-events: auto;
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
    
    .client-results-dropdown .card-modern {
        max-height: 400px;
        overflow-y: auto;
        overflow-x: hidden;
        box-shadow: 
            0 12px 48px rgba(0, 0, 0, 0.2),
            0 4px 16px rgba(102, 126, 234, 0.15);
        border: 2px solid rgba(102, 126, 234, 0.25);
        margin: 0;
        background: white;
        /* Ensure it's above everything */
        position: relative;
        z-index: 9999;
    }
    
    .client-results-dropdown .card-body {
        padding: 0.75rem;
    }
    
    /* Ensure form sections don't overlap */
    .form-section {
        position: relative;
        z-index: 1;
        overflow: visible; /* Allow dropdowns to show */
    }
    
    /* Container adjustments */
    .container-fluid {
        position: relative;
        z-index: 1;
    }
    
    .card-modern {
        position: relative;
        z-index: 1;
        overflow: visible; /* Allow dropdowns */
    }
    
    .card-body {
        position: relative;
        z-index: 1;
        overflow: visible; /* Allow dropdowns */
    }
    
    /* Client Search Result Item */
    .client-search-result {
        border: 2px solid rgba(102, 126, 234, 0.1);
        border-radius: 10px;
        padding: 0.875rem 1rem;
        margin-bottom: 0.5rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        background: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    
    .client-search-result::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, var(--booking-primary) 0%, var(--booking-secondary) 100%);
        transform: scaleY(0);
        transition: transform 0.25s ease;
    }
    
    .client-search-result:hover {
        border-color: var(--booking-primary);
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        transform: translateX(4px);
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
    }
    
    .client-search-result:hover::before {
        transform: scaleY(1);
    }
    
    .client-search-result.highlighted {
        border-color: var(--booking-primary);
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.12) 0%, rgba(118, 75, 162, 0.12) 100%);
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.25);
    }
    
    .client-search-result.highlighted::before {
        transform: scaleY(1);
    }
    
    .client-search-result:last-child {
        margin-bottom: 0;
    }
    
    /* Client Result Content */
    .client-result-content {
        flex: 1;
        min-width: 0;
    }
    
    .client-result-name {
        color: #1a1a2e;
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
    }
    
    .client-result-name i {
        color: var(--booking-primary);
        margin-right: 0.5rem;
        font-size: 1rem;
    }
    
    .client-result-name mark {
        background: rgba(102, 126, 234, 0.25);
        padding: 0.1rem 0.25rem;
        border-radius: 4px;
        font-weight: 800;
        color: var(--booking-primary);
    }
    
    .client-result-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        margin-top: 0.375rem;
    }
    
    .client-result-detail {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .client-result-detail i {
        width: 16px;
        margin-right: 0.5rem;
        font-size: 0.8rem;
    }
    
    .client-result-detail.email i {
        color: var(--booking-primary);
    }
    
    .client-result-detail.phone i {
        color: var(--booking-success);
    }
    
    .client-result-detail.user i {
        color: var(--booking-info);
    }
    
    /* Select Button */
    .select-client-inline-btn {
        margin-left: 0.75rem;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.25s ease;
    }
    
    .select-client-inline-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    /* Empty State */
    .client-results-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #6c757d;
    }
    
    .client-results-empty i {
        font-size: 2.5rem;
        color: #dee2e6;
        margin-bottom: 0.75rem;
        display: block;
    }
    
    /* Loading State */
    .client-results-loading {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--booking-primary);
    }
    
    .client-results-loading i {
        font-size: 1.5rem;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Selected Client Info Card */
    .selected-client-card {
        background: linear-gradient(135deg, rgba(28, 200, 138, 0.08) 0%, rgba(23, 166, 115, 0.08) 100%);
        border: 2px solid rgba(28, 200, 138, 0.3);
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 12px rgba(28, 200, 138, 0.1);
    }
    
    .selected-client-card strong {
        color: #1a1a2e;
        font-size: 1.05rem;
    }
    
    .selected-client-card small {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    /* Badge Modern */
    .badge-modern {
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    
    /* Prevent body overflow issues */
    body {
        overflow-x: hidden;
    }
    
    /* Ensure dropdown doesn't get cut off */
    @media (max-width: 768px) {
        .client-results-dropdown {
            left: -1rem;
            right: -1rem;
            margin-left: 1rem;
            margin-right: 1rem;
        }
        
        .form-section {
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        
        .selected-booths-summary {
            position: relative;
            top: 0;
            margin-top: 1.5rem;
        }
        
        .client-results-dropdown .card-modern {
            max-height: 300px;
        }
    }
    
    /* Additional spacing for dropdown */
    .client-results-dropdown {
        margin-top: 0.5rem;
    }
    
    /* Ensure proper stacking */
    .form-section:has(.client-search-wrapper) {
        z-index: 10;
    }
    
    /* Fallback for browsers that don't support :has() */
    .form-section.client-search-section {
        z-index: 10;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3 class="m-0"><i class="fas fa-calendar-plus mr-2"></i>Create New Booking</h3>
            <div>
                @if(isset($currentFloorPlan) && $currentFloorPlan)
                <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $currentFloorPlan->id]) }}" class="btn btn-modern btn-modern-info btn-sm mr-2">
                    <i class="fas fa-map-marked-alt mr-1"></i>View Floor Plan Canvas
                </a>
                @endif
                <a href="{{ route('books.index') }}" class="btn btn-modern btn-sm" style="background: #6c757d; color: white;">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Bookings
                </a>
            </div>
        </div>
        <form action="{{ route('books.store') }}" method="POST" id="bookingForm">
            @csrf
            <div class="card-body" style="padding: 2rem; position: relative; z-index: 1; overflow: visible;">
                @if(isset($currentFloorPlan) && $currentFloorPlan)
                <div class="alert alert-modern alert-modern-info mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-map mr-2"></i>
                            <strong>Booking for Floor Plan:</strong> {{ $currentFloorPlan->name }}
                            @if($currentFloorPlan->event) - {{ $currentFloorPlan->event->title }} @endif
                        </div>
                        <a href="{{ route('books.create') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times mr-1"></i>Clear Filter
                        </a>
                    </div>
                </div>
                @endif

                <!-- Floor Plan Selection -->
                @if(isset($floorPlans) && $floorPlans->count() > 0)
                <div class="form-section">
                    <h6><i class="fas fa-map mr-2"></i>Floor Plan (Optional Filter)</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="floor_plan_filter" class="form-label font-weight-bold">Filter Booths by Floor Plan</label>
                                <select class="form-control form-control-modern" id="floor_plan_filter" name="floor_plan_filter" onchange="filterByFloorPlan(this.value)">
                                    <option value="">All Floor Plans</option>
                                    @foreach($floorPlans as $fp)
                                        <option value="{{ $fp->id }}" {{ (isset($floorPlanId) && $floorPlanId == $fp->id) ? 'selected' : '' }}>
                                            {{ $fp->name }}
                                            @if($fp->is_default) (Default) @endif
                                            @if($fp->event) - {{ $fp->event->title }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted mt-1"><i class="fas fa-info-circle mr-1"></i>Select a floor plan to filter available booths, or leave blank to see all booths</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Client Selection -->
                <div class="form-section client-search-section">
                    <h6><i class="fas fa-building mr-2"></i>Client Information</h6>
                    <input type="hidden" id="clientid" name="clientid" value="{{ old('clientid') }}" required>
                    
                    <!-- Selected Client Display -->
                    <div id="selectedClientInfo" class="mb-3" style="display: none;">
                        <div class="selected-client-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-check-circle text-success mr-2" style="font-size: 1.2rem;"></i>
                                        <strong id="selectedClientName" class="d-block mb-0" style="font-size: 1.05rem; color: #1a1a2e;"></strong>
                                    </div>
                                    <small id="selectedClientDetails" class="text-muted d-block ml-4"></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger ml-3" id="btnClearClient">
                                    <i class="fas fa-times mr-1"></i>Change Client
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Client Search (shown when no client selected) -->
                    <div id="clientSearchContainer">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="clientSearchInline" class="form-label font-weight-bold">Search Client <span class="text-danger">*</span></label>
                                <div class="client-search-wrapper">
                                    <div class="input-group input-group-modern">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0">
                                                <i class="fas fa-search text-muted" id="searchIcon"></i>
                                            </span>
                                        </div>
                                        <input type="text" 
                                               class="form-control form-control-modern border-left-0 @error('clientid') is-invalid @enderror" 
                                               id="clientSearchInline" 
                                               placeholder="Type client name, company, email, or phone number..." 
                                               autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-modern btn-modern-primary" id="btnSearchSelectClient" data-toggle="modal" data-target="#searchClientModal">
                                                <i class="fas fa-search-plus mr-1"></i>Advanced Search
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Inline Search Results Dropdown - Fixed Positioning -->
                                    <div id="inlineClientResults" class="client-results-dropdown" style="display: none;">
                                        <div class="card-modern">
                                            <div class="card-body p-0">
                                                <div id="inlineClientResultsList" class="p-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('clientid')
                                    <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                @enderror
                                
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="form-text text-muted mb-0">
                                        <i class="fas fa-info-circle mr-1"></i>Start typing to see instant suggestions
                                    </small>
                                    <button type="button" class="btn btn-modern btn-modern-success btn-sm" data-toggle="modal" data-target="#createClientModal">
                                        <i class="fas fa-plus mr-1"></i>Create New Client
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="form-section">
                    <h6><i class="fas fa-calendar-alt mr-2"></i>Booking Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_book" class="form-label font-weight-bold">Booking Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" 
                                       class="form-control form-control-modern @error('date_book') is-invalid @enderror" 
                                       id="date_book" 
                                       name="date_book" 
                                       value="{{ old('date_book', now()->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('date_book')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type" class="form-label font-weight-bold">Booking Type</label>
                                <select class="form-control form-control-modern @error('type') is-invalid @enderror" id="type" name="type">
                                    <option value="1" {{ old('type', 1) == 1 ? 'selected' : '' }}>Regular</option>
                                    <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>Special</option>
                                    <option value="3" {{ old('type') == 3 ? 'selected' : '' }}>Temporary</option>
                                </select>
                                <small class="form-text text-muted mt-1"><i class="fas fa-info-circle mr-1"></i>Select the type of booking</small>
                                @error('type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booth Selection -->
                <div class="form-section">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="mb-0"><i class="fas fa-cube mr-2"></i>Select Booths <span class="text-danger">*</span></h6>
                        <div class="btn-group">
                            <button type="button" class="btn btn-modern btn-sm" style="background: linear-gradient(135deg, var(--booking-primary) 0%, var(--booking-secondary) 100%); color: white;" onclick="selectAllBooths()">
                                <i class="fas fa-check-double mr-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-modern btn-sm" style="background: #6c757d; color: white;" onclick="clearSelection()">
                                <i class="fas fa-times mr-1"></i>Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="booth-selector" id="boothSelector">
                                @if($booths->count() > 0)
                                    <div class="row">
                                        @foreach($booths as $booth)
                                        <div class="col-md-6 mb-2">
                                            <div class="booth-option border rounded p-2" data-booth-id="{{ $booth->id }}" data-price="{{ $booth->price }}">
                                                <label class="mb-0 w-100" style="cursor: pointer;">
                                                    <input type="checkbox" 
                                                           name="booth_ids[]" 
                                                           value="{{ $booth->id }}" 
                                                           class="booth-checkbox"
                                                           {{ in_array($booth->id, old('booth_ids', [])) ? 'checked' : '' }}
                                                           onchange="updateSelection()">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div>
                                                            <strong style="color: var(--booking-primary); font-size: 1.1rem;">{{ $booth->booth_number }}</strong>
                                                            <span class="badge-modern ml-2" style="background: linear-gradient(135deg, {{ $booth->getStatusColor() == 'success' ? '#1cc88a' : ($booth->getStatusColor() == 'warning' ? '#f6c23e' : ($booth->getStatusColor() == 'danger' ? '#e74a3b' : '#36b9cc')) }} 0%, {{ $booth->getStatusColor() == 'success' ? '#17a673' : ($booth->getStatusColor() == 'warning' ? '#dda20a' : ($booth->getStatusColor() == 'danger' ? '#c23321' : '#2c9faf')) }} 100%); color: white;">
                                                                {{ $booth->getStatusLabel() }}
                                                            </span>
                                                        </div>
                                                        <strong style="color: var(--booking-success); font-size: 1.2rem;">${{ number_format($booth->price, 2) }}</strong>
                                                    </div>
                                                    @if($booth->category)
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-folder mr-1" style="color: var(--booking-info);"></i>{{ $booth->category->name }}
                                                        </small>
                                                    </div>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-modern alert-warning text-center py-4">
                                        <i class="fas fa-exclamation-triangle mr-2" style="font-size: 2rem;"></i>
                                        <p class="mb-0 mt-2"><strong>No available booths found.</strong></p>
                                    </div>
                                @endif
                            </div>
                            @error('booth_ids')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <div class="selected-booths-summary">
                                <h6 class="mb-3"><i class="fas fa-list mr-2"></i>Selected Booths</h6>
                                <div id="selectedBoothsList" class="mb-3" style="max-height: 300px; overflow-y: auto; min-height: 100px;">
                                    <p class="text-muted text-center mb-0 py-4">No booths selected</p>
                                </div>
                                <hr style="border-color: rgba(102, 126, 234, 0.2);">
                                <div class="d-flex justify-content-between align-items-center mb-3 p-2" style="background: rgba(102, 126, 234, 0.05); border-radius: 8px;">
                                    <strong><i class="fas fa-cube mr-1"></i>Total Booths:</strong>
                                    <span id="totalBooths" class="badge-modern badge-modern-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center p-2" style="background: linear-gradient(135deg, rgba(28, 200, 138, 0.1) 0%, rgba(23, 166, 115, 0.1) 100%); border-radius: 8px;">
                                    <strong><i class="fas fa-dollar-sign mr-1"></i>Total Amount:</strong>
                                    <span id="totalAmount" class="font-weight-bold" style="font-size: 1.3rem; color: var(--booking-success);">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle mr-1"></i>Click on booths to select them. You can select multiple booths.
                    </small>
                </div>
            </div>
            <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1); padding: 1.5rem;">
                <button type="submit" class="btn btn-modern btn-modern-primary" id="submitBtn">
                    <i class="fas fa-save mr-1"></i>Create Booking
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-modern" style="background: #6c757d; color: white; margin-left: 0.5rem;">Cancel</a>
                <span id="selectionWarning" class="text-danger ml-3" style="display: none; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Please select at least one booth
                </span>
            </div>
        </form>
    </div>
</div>

<!-- Search & Select Client Modal -->
<div class="modal fade" id="searchClientModal" tabindex="-1" role="dialog" aria-labelledby="searchClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <h5 class="modal-title" id="searchClientModalLabel">
                    <i class="fas fa-search mr-2"></i>Search & Select Client
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="form-group">
                    <label for="clientSearchInput" class="font-weight-bold mb-2">
                        <i class="fas fa-search mr-1"></i> Search Client
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control form-control-modern" 
                               id="clientSearchInput" 
                               placeholder="Type to search by name, company, email, or phone number..." 
                               autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-modern btn-modern-primary" id="btnSearchClient">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-modern" id="btnClearClientSearch" style="background: #6c757d; color: white; display: none;">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted mt-2"><i class="fas fa-info-circle mr-1"></i>Type at least 2 characters to search for existing clients</small>
                </div>
                
                <div id="clientSearchResults" class="mt-4" style="display: none;">
                    <h6 class="mb-3 font-weight-bold"><i class="fas fa-list mr-1"></i>Search Results</h6>
                    <div id="clientSearchResultsList" style="max-height: 450px; overflow-y: auto; padding: 0.75rem;"></div>
                </div>
                
                <div id="noClientResults" class="alert alert-modern alert-modern-info mt-4 text-center" style="display: none;">
                    <i class="fas fa-info-circle mr-2" style="font-size: 1.5rem;"></i>
                    <p class="mb-0 mt-2"><strong>No clients found.</strong> You can create a new client using the "New Client" button.</p>
                </div>
            </div>
            <div class="modal-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
                <button type="button" class="btn btn-modern" style="background: #6c757d; color: white;" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" role="dialog" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createClientModalLabel">
                    <i class="fas fa-user-plus mr-2"></i>Create New Client
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createClientForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="createClientError" class="alert alert-danger" style="display: none;"></div>
                    
                    <!-- Basic Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-user mr-2"></i>Basic Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="modal_name" name="name" placeholder="Enter client full name">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_sex" class="form-label">Gender</label>
                                <select class="form-control" id="modal_sex" name="sex">
                                    <option value="">Select Gender...</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="3">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-building mr-2"></i>Company Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_company" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="modal_company" name="company" placeholder="Enter company name">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_company_name_khmer" class="form-label">Company Name (Khmer)</label>
                                <input type="text" class="form-control" id="modal_company_name_khmer" name="company_name_khmer" placeholder="Enter company name in Khmer">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_position" class="form-label">Position/Title</label>
                                <input type="text" class="form-control" id="modal_position" name="position" placeholder="Enter position or title">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-phone mr-2"></i>Contact Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="modal_phone_number" name="phone_number" placeholder="Enter phone number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_phone_1" class="form-label">Phone 1</label>
                                <input type="text" class="form-control" id="modal_phone_1" name="phone_1" placeholder="Enter primary phone number">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_phone_2" class="form-label">Phone 2</label>
                                <input type="text" class="form-control" id="modal_phone_2" name="phone_2" placeholder="Enter secondary phone number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="modal_email" name="email" placeholder="Enter email address">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_email_1" class="form-label">Email 1</label>
                                <input type="email" class="form-control" id="modal_email_1" name="email_1" placeholder="Enter primary email address">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_email_2" class="form-label">Email 2</label>
                                <input type="email" class="form-control" id="modal_email_2" name="email_2" placeholder="Enter secondary email address">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_address" class="form-label">Address</label>
                                <textarea class="form-control" id="modal_address" name="address" rows="2" placeholder="Enter complete address (street, city, country)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-info-circle mr-2"></i>Additional Information (Optional)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_tax_id" class="form-label">Tax ID / Business Registration Number</label>
                                <input type="text" class="form-control" id="modal_tax_id" name="tax_id" placeholder="Enter tax ID or business registration number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="modal_website" name="website" placeholder="https://example.com">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="modal_notes" name="notes" rows="2" placeholder="Enter any additional information or notes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="createClientSubmitBtn">
                        <i class="fas fa-save mr-1"></i>Create Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterByFloorPlan(floorPlanId) {
    if (floorPlanId) {
        window.location.href = '{{ route("books.create") }}?floor_plan_id=' + floorPlanId;
    } else {
        window.location.href = '{{ route("books.create") }}';
    }
}

// Handle Create Client Modal Form Submission
$(document).ready(function() {
    $('#createClientForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createClientSubmitBtn');
        const errorDiv = $('#createClientError');
        const originalText = submitBtn.html();
        
        // Hide error message
        errorDiv.hide();
        
        // Validate form
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }
        
        // Disable submit button and show loading
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');
        
        // Submit via AJAX
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success' && response.client) {
                    const client = response.client;
                    
                    // Select the newly created client using the selectClient function
                    if (typeof selectClient === 'function') {
                        selectClient(client);
                    } else {
                        // Fallback if selectClient is not available
                        $('#clientid').val(client.id);
                        const displayText = client.company + (client.name ? ' - ' + client.name : '') + 
                                         (client.email ? ' (' + client.email + ')' : '') + 
                                         (client.phone_number ? ' | ' + client.phone_number : '');
                        $('#clientSearchInline').val(displayText);
                        $('#selectedClientName').text(client.company || client.name);
                        let details = [];
                        if (client.name && client.company) details.push(client.name);
                        if (client.email) details.push(client.email);
                        if (client.phone_number) details.push(client.phone_number);
                        $('#selectedClientDetails').text(details.join(' • '));
                        $('#selectedClientInfo').show();
                    }
                    
                    // Close modal and reset form
                    $('#createClientModal').modal('hide');
                    form[0].reset();
                    errorDiv.hide();
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Client Created!',
                            text: 'Client "' + client.company + '" has been created and selected.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Client created successfully!');
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the client.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const firstError = Object.values(errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                }
                
                errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i>' + errorMessage);
                errorDiv.show();
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Reset form when modal is closed
    $('#createClientModal').on('hidden.bs.modal', function() {
        $('#createClientForm')[0].reset();
        $('#createClientError').hide();
    });
});

    // Client Search & Select Functionality
$(document).ready(function() {
    let clientSearchTimeout;
    let inlineSearchTimeout;
    let selectedClient = null;
    
    // Initialize - check if client is already selected
    @if(old('clientid'))
        const oldClientId = {{ old('clientid') }};
        // Try to find and display the selected client
        $.ajax({
            url: '{{ route("clients.search") }}',
            method: 'GET',
            data: { q: '', id: oldClientId },
            success: function(clients) {
                if (clients && clients.length > 0) {
                    const client = clients.find(c => c.id == oldClientId);
                    if (client) {
                        selectClient(client);
                    }
                }
            }
        });
    @endif
    
    // Inline Client Search - Auto-suggest function
    function searchClientsInline(query) {
        if (!query || query.length < 2) {
            $('#inlineClientResults').hide();
            return;
        }
        
        console.log('searchClientsInline called with query:', query);
        
        // Show loading indicator
        const resultsDiv = $('#inlineClientResults');
        const resultsList = $('#inlineClientResultsList');
        const searchIcon = $('#searchIcon');
        
        // Update search icon to show loading
        if (searchIcon.length) {
            searchIcon.removeClass('fa-search').addClass('fa-spinner fa-spin');
        }
        
        resultsDiv.show();
        resultsList.html(
            '<div class="client-results-loading">' +
                '<i class="fas fa-spinner fa-spin"></i>' +
                '<p class="mb-0 mt-2">Searching clients...</p>' +
            '</div>'
        );
        
        const searchUrl = '{{ route("clients.search") }}';
        console.log('Making AJAX request to:', searchUrl, 'with query:', query);
        
        $.ajax({
            url: searchUrl,
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(clients) {
                console.log('Inline search successful, clients found:', clients);
                
                // Reset search icon
                const searchIcon = $('#searchIcon');
                if (searchIcon.length) {
                    searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
                
                resultsList.empty();
                
                // Handle both array and object responses
                const clientsArray = Array.isArray(clients) ? clients : (clients ? Object.values(clients) : []);
                
                if (!clientsArray || clientsArray.length === 0) {
                    resultsList.html(
                        '<div class="client-results-empty">' +
                            '<i class="fas fa-search"></i>' +
                            '<p class="mb-0"><strong>No clients found</strong></p>' +
                            '<p class="mb-0 mt-1" style="font-size: 0.85rem;">Try different keywords or create a new client</p>' +
                        '</div>'
                    );
                    return;
                }
                
                // Show up to 8 results for better visibility
                clientsArray.slice(0, 8).forEach(function(client, index) {
                    const displayName = (client.company || client.name || 'N/A');
                    const highlightQuery = query.toLowerCase();
                    
                    // Highlight matching text
                    let highlightedName = displayName;
                    if (highlightQuery) {
                        // Escape special regex characters
                        const escapedQuery = highlightQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        const regex = new RegExp(`(${escapedQuery})`, 'gi');
                        highlightedName = displayName.replace(regex, '<mark>$1</mark>');
                    }
                    
                    // Build details HTML
                    let detailsHTML = '<div class="client-result-details">';
                    if (client.name && client.company) {
                        detailsHTML += '<div class="client-result-detail user"><i class="fas fa-user"></i><span>' + client.name + '</span></div>';
                    }
                    if (client.email) {
                        detailsHTML += '<div class="client-result-detail email"><i class="fas fa-envelope"></i><span>' + client.email + '</span></div>';
                    }
                    if (client.phone_number) {
                        detailsHTML += '<div class="client-result-detail phone"><i class="fas fa-phone"></i><span>' + client.phone_number + '</span></div>';
                    }
                    detailsHTML += '</div>';
                    
                    // Build result item
                    const item = $('<div class="client-search-result"></div>')
                        .html(
                            '<div class="client-result-content">' +
                                '<div class="client-result-name">' +
                                    '<i class="fas fa-building"></i>' +
                                    '<span>' + highlightedName + '</span>' +
                                '</div>' +
                                detailsHTML +
                            '</div>' +
                            '<button type="button" class="btn btn-modern btn-modern-primary select-client-inline-btn" data-client-id="' + client.id + '" title="Select this client">' +
                                '<i class="fas fa-check"></i>' +
                            '</button>'
                        )
                        .data('client', client);
                    
                    resultsList.append(item);
                });
                
                // Bind inline select button click
                $(document).off('click', '.select-client-inline-btn').on('click', '.select-client-inline-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const client = $(this).closest('.client-search-result').data('client');
                    if (client) {
                        selectClient(client);
                        $('#inlineClientResults').hide();
                    }
                });
                
                // Bind inline result item click
                $(document).off('click', '#inlineClientResultsList .client-search-result').on('click', '#inlineClientResultsList .client-search-result', function(e) {
                    if (!$(e.target).closest('.select-client-inline-btn').length) {
                        e.preventDefault();
                        const client = $(this).data('client');
                        if (client) {
                            selectClient(client);
                            $('#inlineClientResults').hide();
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Search error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    statusCode: xhr.status
                });
                
                // Reset search icon
                const searchIcon = $('#searchIcon');
                if (searchIcon.length) {
                    searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
                
                let errorMessage = 'Error searching clients. Please try again.';
                if (xhr.status === 401) {
                    errorMessage = 'Please refresh the page and try again.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please contact administrator.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                resultsList.html(
                    '<div class="client-results-empty" style="color: var(--booking-danger);">' +
                        '<i class="fas fa-exclamation-triangle"></i>' +
                        '<p class="mb-0"><strong>Error</strong></p>' +
                        '<p class="mb-0 mt-1" style="font-size: 0.85rem;">' + errorMessage + '</p>' +
                    '</div>'
                );
            }
        });
    }
    
    // Inline search input handler - Auto-suggest as user types
    $('#clientSearchInline').on('input keyup paste', function(e) {
        // Don't trigger on arrow keys, enter, escape, etc.
        if ([38, 40, 13, 27].includes(e.keyCode)) {
            return;
        }
        
        const query = $(this).val().trim();
        
        clearTimeout(inlineSearchTimeout);
        
        // If query is empty or too short, hide results
        if (query.length < 2) {
            $('#inlineClientResults').hide();
            // Reset search icon
            const searchIcon = $('#searchIcon');
            if (searchIcon.length) {
                searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
            }
            // If cleared, also clear selection
            if (query.length === 0 && selectedClient) {
                selectedClient = null;
                $('#clientid').val('');
                $('#selectedClientInfo').hide();
                $('#clientSearchContainer').show();
            }
            return;
        }
        
        // Auto-search after 300ms delay (debounce)
        console.log('Triggering search for query:', query);
        inlineSearchTimeout = setTimeout(function() {
            console.log('Executing searchClientsInline with query:', query);
            searchClientsInline(query);
        }, 300);
    });
    
    // Handle keyboard navigation in inline results
    $('#clientSearchInline').on('keydown', function(e) {
        const results = $('#inlineClientResults:visible');
        if (results.length === 0) return;
        
        const items = results.find('.client-search-result');
        if (items.length === 0) return;
        
        let currentIndex = items.index(items.filter('.highlighted'));
        
        if (e.keyCode === 40) { // Down arrow
            e.preventDefault();
            items.removeClass('highlighted');
            currentIndex = (currentIndex + 1) % items.length;
            items.eq(currentIndex).addClass('highlighted').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else if (e.keyCode === 38) { // Up arrow
            e.preventDefault();
            items.removeClass('highlighted');
            currentIndex = currentIndex <= 0 ? items.length - 1 : currentIndex - 1;
            items.eq(currentIndex).addClass('highlighted').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else if (e.keyCode === 13) { // Enter
            e.preventDefault();
            const highlighted = items.filter('.highlighted');
            if (highlighted.length > 0) {
                const client = highlighted.data('client');
                if (client) {
                    selectClient(client);
                    $('#inlineClientResults').hide();
                }
            } else if (items.length > 0) {
                // Select first item if none highlighted
                const client = items.first().data('client');
                if (client) {
                    selectClient(client);
                    $('#inlineClientResults').hide();
                }
            }
        } else if (e.keyCode === 27) { // Escape
            $('#inlineClientResults').hide();
        }
    });
    
    // Hide inline results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#clientSearchInline, #inlineClientResults').length) {
            $('#inlineClientResults').hide();
        }
    });
    
    // Prevent form submission when selecting from dropdown
    $('#clientSearchInline').on('keydown', function(e) {
        if (e.keyCode === 13 && $('#inlineClientResults:visible').length > 0) {
            e.preventDefault();
        }
    });
    
    // Client Search Function (for modal)
    function searchClients(query) {
        if (!query || query.length < 2) {
            $('#clientSearchResults').hide();
            $('#noClientResults').hide();
            return;
        }
        
        $.ajax({
            url: '{{ route("clients.search") }}',
            method: 'GET',
            data: { q: query },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(clients) {
                console.log('Modal search successful, clients found:', clients);
                
                const resultsDiv = $('#clientSearchResults');
                const resultsList = $('#clientSearchResultsList');
                const noResultsDiv = $('#noClientResults');
                
                resultsList.empty();
                
                // Handle both array and object responses
                const clientsArray = Array.isArray(clients) ? clients : (clients ? Object.values(clients) : []);
                
                if (!clientsArray || clientsArray.length === 0) {
                    resultsDiv.hide();
                    noResultsDiv.show();
                    return;
                }
                
                noResultsDiv.hide();
                resultsDiv.show();
                
                clientsArray.forEach(function(client) {
                    const displayName = (client.company || client.name || 'N/A');
                    
                    // Build details HTML
                    let detailsHTML = '<div class="client-result-details">';
                    if (client.name && client.company) {
                        detailsHTML += '<div class="client-result-detail user"><i class="fas fa-user"></i><span>' + client.name + '</span></div>';
                    }
                    if (client.email) {
                        detailsHTML += '<div class="client-result-detail email"><i class="fas fa-envelope"></i><span>' + client.email + '</span></div>';
                    }
                    if (client.phone_number) {
                        detailsHTML += '<div class="client-result-detail phone"><i class="fas fa-phone"></i><span>' + client.phone_number + '</span></div>';
                    }
                    if (client.address) {
                        detailsHTML += '<div class="client-result-detail"><i class="fas fa-map-marker-alt"></i><span>' + client.address + '</span></div>';
                    }
                    detailsHTML += '</div>';
                    
                    // Build result item
                    const item = $('<div class="client-search-result"></div>')
                        .html(
                            '<div class="client-result-content">' +
                                '<div class="client-result-name">' +
                                    '<i class="fas fa-building"></i>' +
                                    '<span>' + displayName + '</span>' +
                                '</div>' +
                                detailsHTML +
                            '</div>' +
                            '<button type="button" class="btn btn-modern btn-modern-primary select-client-btn" data-client-id="' + client.id + '" title="Select this client">' +
                                '<i class="fas fa-check mr-1"></i>Select' +
                            '</button>'
                        )
                        .data('client', client);
                    
                    resultsList.append(item);
                });
                
                // Bind select button click
                $('.select-client-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const client = $(this).closest('.client-search-result').data('client');
                    selectClient(client);
                });
                
                // Bind result item click
                resultsList.find('.client-search-result').on('click', function(e) {
                    if (!$(e.target).closest('.select-client-btn').length) {
                        e.preventDefault();
                        const client = $(this).data('client');
                        selectClient(client);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Modal client search error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    statusCode: xhr.status
                });
                $('#clientSearchResults').hide();
                $('#noClientResults').show();
            }
        });
    }
    
    // Select Client Function
    function selectClient(client) {
        selectedClient = client;
        
        // Set hidden input value
        $('#clientid').val(client.id);
        
        // Show selected client info card
        const displayName = client.company || client.name || 'N/A';
        let details = [];
        if (client.email) details.push('<i class="fas fa-envelope mr-1"></i>' + client.email);
        if (client.phone_number) details.push('<i class="fas fa-phone mr-1"></i>' + client.phone_number);
        
        $('#selectedClientName').text(displayName);
        $('#selectedClientDetails').html(details.length > 0 ? details.join(' <span class="mx-2 text-muted">|</span> ') : '');
        $('#selectedClientInfo').show();
        
        // Hide search container and clear search inputs
        $('#clientSearchContainer').hide();
        $('#clientSearchInline').val('');
        $('#inlineClientResults').hide();
        
        // Close modal if open
        $('#searchClientModal').modal('hide');
        
        // Clear modal search
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $('#btnClearClientSearch').hide();
    }
    
    // Clear Client Selection
    $('#btnClearClient').on('click', function() {
        selectedClient = null;
        $('#clientid').val('');
        $('#selectedClientInfo').hide();
        $('#clientSearchContainer').show();
        $('#clientSearchInline').val('');
        $('#inlineClientResults').hide();
        // Focus on search input
        setTimeout(function() {
            $('#clientSearchInline').focus();
        }, 100);
    });
    
    // Search input handlers
    $('#clientSearchInput').on('input keyup', function(e) {
        const query = $(this).val().trim();
        
        clearTimeout(clientSearchTimeout);
        
        if (query.length < 2) {
            $('#clientSearchResults').hide();
            $('#noClientResults').hide();
            $('#btnClearClientSearch').hide();
            return;
        }
        
        $('#btnClearClientSearch').show();
        
        clientSearchTimeout = setTimeout(function() {
            searchClients(query);
        }, 300);
    });
    
    $('#btnSearchClient').on('click', function() {
        const query = $('#clientSearchInput').val().trim();
        if (query.length >= 2) {
            searchClients(query);
        }
    });
    
    $('#btnClearClientSearch').on('click', function() {
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $(this).hide();
    });
    
    // Reset modal when closed
    $('#searchClientModal').on('hidden.bs.modal', function() {
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $('#btnClearClientSearch').hide();
    });
    
    // Update selection on page load if there are checked boxes
    updateSelection();
    
    // Also update when checkboxes change
    $('.booth-checkbox').on('change', function() {
        updateSelection();
    });
});

// Select/Deselect booth options (delegated event handler for dynamic content)
$(document).on('click', '.booth-option', function(e) {
    if (e.target.type !== 'checkbox' && !$(e.target).closest('input').length) {
        const checkbox = $(this).find('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
    }
});

function updateSelection() {
    const selected = [];
    let totalAmount = 0;
    
    $('.booth-checkbox:checked').each(function() {
        const boothId = $(this).val();
        const boothOption = $(this).closest('.booth-option');
        const boothNumber = boothOption.find('strong').text();
        const price = parseFloat(boothOption.data('price')) || 0;
        
        selected.push({ id: boothId, number: boothNumber, price: price });
        totalAmount += price;
    });
    
    // Update selected list
    const listContainer = $('#selectedBoothsList');
    if (selected.length > 0) {
        let html = '';
        selected.forEach(function(booth) {
            html += '<div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background: rgba(102, 126, 234, 0.05); border-radius: 8px; border: 1px solid rgba(102, 126, 234, 0.1);">';
            html += '<div><i class="fas fa-cube mr-2" style="color: var(--booking-primary);"></i><strong>' + booth.number + '</strong></div>';
            html += '<strong style="color: var(--booking-success); font-size: 1.1rem;">$' + booth.price.toFixed(2) + '</strong>';
            html += '</div>';
        });
        listContainer.html(html);
    } else {
        listContainer.html('<p class="text-muted text-center mb-0">No booths selected</p>');
    }
    
    // Update summary
    $('#totalBooths').text(selected.length);
    $('#totalAmount').text('$' + totalAmount.toFixed(2));
    
    // Update visual state
    $('.booth-option').removeClass('selected');
    $('.booth-checkbox:checked').closest('.booth-option').addClass('selected');
    
    // Show/hide warning
    if (selected.length === 0) {
        $('#selectionWarning').show();
        $('#submitBtn').prop('disabled', true);
    } else {
        $('#selectionWarning').hide();
        $('#submitBtn').prop('disabled', false);
    }
}

function selectAllBooths() {
    $('.booth-checkbox').prop('checked', true);
    updateSelection();
}

function clearSelection() {
    $('.booth-checkbox').prop('checked', false);
    updateSelection();
}

// Form validation
// Form validation before submission
$('#bookingForm').on('submit', function(e) {
    // Validate client selection first
    const clientId = $('#clientid').val();
    if (!clientId || clientId === '' || clientId === null) {
        e.preventDefault();
        e.stopPropagation();
        
        // Show error message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Client Required',
                text: 'Please select a client before submitting the booking.',
                confirmButtonColor: '#667eea'
            });
        } else {
            alert('Please select a client before submitting the booking.');
        }
        
        // Show search container if hidden
        if ($('#clientSearchContainer').is(':hidden')) {
            $('#selectedClientInfo').hide();
            $('#clientSearchContainer').show();
        }
        
        // Focus on search input
        setTimeout(function() {
            $('#clientSearchInline').focus();
        }, 100);
        
        return false;
    }
    
    // Validate booth selection
    const selectedCount = $('.booth-checkbox:checked').length;
    if (selectedCount === 0) {
        e.preventDefault();
        e.stopPropagation();
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'No Booths Selected',
                text: 'Please select at least one booth for this booking.',
                confirmButtonColor: '#667eea'
            });
        } else {
            alert('Please select at least one booth for this booking.');
        }
        
        return false;
    }
    
    // If all validations pass, show loading and allow form submission
    showLoading();
});

// Initialize on page load
updateSelection();
</script>
@endpush

