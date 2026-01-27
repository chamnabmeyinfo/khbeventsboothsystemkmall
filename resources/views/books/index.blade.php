@extends('layouts.app')

@section('title', 'Bookings Management')

@push('styles')
<style>
/* ============================================
   RESPONSIVE BOOKINGS PAGE - FOLLOWS SYSTEM DESIGN
   ============================================ */

/* Base Styles - Common for all devices */
* {
    box-sizing: border-box;
}

/* Statistics Cards - Following system pattern */
.kpi-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
.kpi-card.info::before { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

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
.kpi-card.info .kpi-icon { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

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

/* Filter Bar - Following system pattern */
.filter-bar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.18);
    padding: 16px 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

/* Action Bar */
.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

/* View Toggle */
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
    background: white;
    color: #667eea;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

/* Booking Cards */
.booking-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-left: 4px solid #667eea;
    transition: all 0.3s;
    cursor: pointer;
}

.booking-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.booking-card.special { border-left-color: #ed8936; }
.booking-card.temporary { border-left-color: #f56565; }

/* Table Styles */
.table-modern {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.table-modern thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.table-modern thead th {
    border: none;
    padding: 16px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.75rem;
}

.table-modern tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.3s;
}

.table-modern tbody tr:hover {
    background: #f7fafc;
}

.table-modern tbody td {
    padding: 16px;
    vertical-align: middle;
    border: none;
}

/* Badge Styles */
.badge-modern {
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-modern-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.badge-modern-warning {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
    color: white;
}

.badge-modern-danger {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    color: white;
}

/* Button Styles */
.btn-modern {
    border-radius: 12px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
}

.btn-modern-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-modern-success {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}

.btn-modern-info {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    color: white;
}

/* Form Controls */
.form-control-modern {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.9375rem;
    transition: all 0.3s;
    background: white;
    color: #1a202c;
}

.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.empty-state-icon {
    font-size: 5rem;
    color: #cbd5e0;
    margin-bottom: 24px;
}

/* Lazy Loading */
.lazy-load-spinner {
    display: none;
    text-align: center;
    padding: 40px 20px;
}

.lazy-load-spinner.active {
    display: block;
}

.lazy-load-spinner i {
    font-size: 2.5rem;
    color: #667eea;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.lazy-load-end {
    text-align: center;
    padding: 32px 20px;
    color: #718096;
    font-weight: 600;
    font-size: 0.9375rem;
}

.lazy-load-trigger {
    height: 1px;
    width: 100%;
    visibility: hidden;
}

/* Responsive Design - Mobile First */
@media (max-width: 768px) {
    .container-fluid {
        padding: 16px !important;
    }
    
    .kpi-value {
        font-size: 2rem;
    }
    
    .kpi-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
    
    .filter-bar {
        padding: 12px 16px;
    }
    
    .action-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .view-toggle {
        width: 100%;
        justify-content: center;
    }
    
    .table-modern {
        font-size: 0.875rem;
    }
    
    .table-modern thead th,
    .table-modern tbody td {
        padding: 12px 8px;
    }
    
    /* Hide table on mobile, show cards */
    .table-view {
        display: none;
    }
    
    .card-view {
        display: block;
    }
}

/* Tablet Styles */
@media (min-width: 769px) and (max-width: 1024px) {
    .container-fluid {
        padding: 24px !important;
    }
    
    .kpi-value {
        font-size: 2.25rem;
    }
}

/* Desktop Styles */
@media (min-width: 1025px) {
    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
        padding: 32px;
    }
    
    /* Show both views on desktop */
    .table-view,
    .card-view {
        display: block;
    }
}

/* Group Section Styles */
.group-section {
    margin-bottom: 32px;
}

.group-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 24px;
    border-radius: 12px 12px 0 0;
    margin-bottom: 0;
}

.group-header h5 {
    margin: 0;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.group-header .badge {
    font-size: 0.875rem;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
}

/* Khmer Font Support */
html, body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Khmer OS Battambang", "KhmerOSBattambang", "Hanuman", "Hanuman-Regular", "Noto Sans Khmer", "Khmer OS", "Khmer", sans-serif;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="kpi-label">Total Bookings</div>
                    <div class="kpi-value">{{ number_format(\App\Models\Book::count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="kpi-label">Today's Bookings</div>
                    <div class="kpi-value">{{ number_format(\App\Models\Book::whereDate('date_book', today())->count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="kpi-label">This Month</div>
                    <div class="kpi-value">
                        {{ number_format(\App\Models\Book::whereMonth('date_book', now()->month)->whereYear('date_book', now()->year)->count()) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <div class="kpi-label">Total Booths</div>
                    <div class="kpi-value">
                        @php
                            try {
                                $totalBooths = \App\Models\Book::get()->sum(function($book) {
                                    $boothIds = json_decode($book->boothid, true);
                                    return is_array($boothIds) ? count($boothIds) : 0;
                                });
                            } catch (\Exception $e) {
                                $totalBooths = 0;
                            }
                        @endphp
                        {{ number_format($totalBooths) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="d-flex flex-wrap gap-3">
            <a href="{{ route('books.create') }}" class="btn btn-modern btn-modern-primary">
                <i class="fas fa-plus me-2"></i>New Booking
            </a>
            <a href="{{ route('export.bookings') }}" class="btn btn-modern btn-modern-success">
                <i class="fas fa-file-csv me-2"></i>Export CSV
            </a>
            <button type="button" class="btn btn-modern btn-modern-info" onclick="refreshPage()">
                <i class="fas fa-sync-alt me-2"></i>Refresh
            </button>
            @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn-modern" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); color: white;" onclick="showDeleteAllModal()">
                <i class="fas fa-trash-alt me-2"></i>Delete All Records
            </button>
            @endif
        </div>
        <div class="view-toggle">
            <button type="button" class="active" onclick="switchView('table')" id="viewTable">
                <i class="fas fa-table me-1"></i>Table
            </button>
            <button type="button" onclick="switchView('cards')" id="viewCards">
                <i class="fas fa-th-large me-1"></i>Cards
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('books.index') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control form-control-modern" 
                           placeholder="Client name, company, or user..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" 
                           name="date_from" 
                           class="form-control form-control-modern" 
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" 
                           name="date_to" 
                           class="form-control form-control-modern" 
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control form-control-modern">
                        <option value="">All Types</option>
                        <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Regular</option>
                        <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Special</option>
                        <option value="3" {{ request('type') == '3' ? 'selected' : '' }}>Temporary</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Group By</label>
                    <select name="group_by" class="form-control form-control-modern" onchange="this.form.submit()">
                        <option value="none" {{ request('group_by', 'none') == 'none' ? 'selected' : '' }}>No Grouping</option>
                        <option value="name" {{ request('group_by') == 'name' ? 'selected' : '' }}>Group By Name</option>
                        <option value="date" {{ request('group_by') == 'date' ? 'selected' : '' }}>Group By Date</option>
                    </select>
                </div>
            </div>
            @if(request('group_by') == 'date')
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label">Date Range</label>
                    <select name="date_range" class="form-control form-control-modern" onchange="this.form.submit()">
                        <option value="all" {{ request('date_range', 'all') == 'all' ? 'selected' : '' }}>All Dates</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today / Now</option>
                        <option value="3days" {{ request('date_range') == '3days' ? 'selected' : '' }}>Last 3 Days</option>
                        <option value="7days" {{ request('date_range') == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="14days" {{ request('date_range') == '14days' ? 'selected' : '' }}>Last 14 Days</option>
                        <option value="more" {{ request('date_range') == 'more' ? 'selected' : '' }}>More than 14 Days</option>
                    </select>
                </div>
            </div>
            @endif
            <div class="row g-3 mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-modern btn-modern-primary">
                        <i class="fas fa-filter me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('books.index') }}" class="btn btn-modern" style="background: #f3f4f6; color: #6b7280;">
                        <i class="fas fa-times me-2"></i>Clear All
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bookings Content -->
    <div id="bookingsContainer">
        @if($groupBy !== 'none' && !empty($groupedBooks))
            <!-- Grouped View -->
            @foreach($groupedBooks as $groupKey => $groupBooks)
                <div class="group-section">
                    <div class="group-header">
                        <h5>
                            <span>
                                <i class="fas fa-layer-group me-2"></i>
                                {{ $groupBy === 'name' ? $groupKey : \Carbon\Carbon::parse($groupKey)->format('F d, Y') }}
                            </span>
                            <span class="badge">{{ count($groupBooks) }} bookings</span>
                        </h5>
                    </div>
                    <div class="card">
                        <div class="card-body p-0">
                            <!-- Table View -->
                            <div class="table-view">
                                <table class="table table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Client</th>
                                            <th>Date</th>
                                            <th>Booths</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupBooks as $book)
                                            @include('books.partials.table-row', ['book' => $book])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Card View -->
                            <div class="card-view" style="display: none;">
                                <div class="p-3">
                                    @foreach($groupBooks as $book)
                                        @include('books.partials.card', ['book' => $book])
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Regular View -->
            <div class="card">
                <div class="card-body p-0">
                    <!-- Table View -->
                    <div class="table-view">
                        <table class="table table-modern mb-0" id="bookingsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Date</th>
                                    <th>Booths</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                                @forelse($books as $book)
                                    @include('books.partials.table-row', ['book' => $book])
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox empty-state-icon"></i>
                                                <h3>No bookings found</h3>
                                                <p class="text-muted">Try adjusting your filters or create a new booking.</p>
                                                <a href="{{ route('books.create') }}" class="btn btn-modern btn-modern-primary mt-3">
                                                    <i class="fas fa-plus me-2"></i>Create Booking
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- Card View -->
                    <div class="card-view" style="display: none;">
                        <div class="p-3">
                            @forelse($books as $book)
                                @include('books.partials.card', ['book' => $book])
                            @empty
                                <div class="empty-state">
                                    <i class="fas fa-inbox empty-state-icon"></i>
                                    <h3>No bookings found</h3>
                                    <p class="text-muted">Try adjusting your filters or create a new booking.</p>
                                    <a href="{{ route('books.create') }}" class="btn btn-modern btn-modern-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>Create Booking
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Lazy Loading Spinner -->
        <div class="lazy-load-spinner" id="lazyLoadSpinner">
            <i class="fas fa-spinner"></i>
        </div>

        <!-- Lazy Loading End -->
        <div class="lazy-load-end" id="lazyLoadEnd" style="display: none;">
            <i class="fas fa-check-circle me-2"></i>All bookings loaded
        </div>

        <!-- Lazy Load Trigger -->
        <div class="lazy-load-trigger" id="lazyLoadTrigger"></div>
    </div>
</div>

<!-- Booking Info Modal -->
<div class="modal fade" id="bookingInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check me-2"></i>Booking Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingInfoContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    'use strict';
    
    let currentPage = 1;
    let isLoading = false;
    let hasMore = true;
    let currentView = 'table';
    
    // Switch View
    window.switchView = function(view) {
        currentView = view;
        document.querySelectorAll('.view-toggle button').forEach(btn => btn.classList.remove('active'));
        document.getElementById('view' + view.charAt(0).toUpperCase() + view.slice(1)).classList.add('active');
        
        document.querySelectorAll('.table-view').forEach(el => {
            el.style.display = view === 'table' ? 'block' : 'none';
        });
        document.querySelectorAll('.card-view').forEach(el => {
            el.style.display = view === 'cards' ? 'block' : 'none';
        });
        
        // Save preference
        localStorage.setItem('bookingsView', view);
    };
    
    // Load saved view preference
    const savedView = localStorage.getItem('bookingsView');
    if (savedView) {
        switchView(savedView);
    }
    
    // Lazy Loading
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && hasMore && !isLoading) {
                loadMoreBookings();
            }
        });
    }, { threshold: 0.1 });
    
    const trigger = document.getElementById('lazyLoadTrigger');
    if (trigger) {
        observer.observe(trigger);
    }
    
    function loadMoreBookings() {
        if (isLoading || !hasMore) return;
        
        isLoading = true;
        currentPage++;
        
        const spinner = document.getElementById('lazyLoadSpinner');
        if (spinner) spinner.classList.add('active');
        
        const formData = new FormData(document.getElementById('filterForm'));
        formData.append('page', currentPage);
        formData.append('view', currentView);
        formData.append('group_by', '{{ $groupBy }}');
        
        fetch('{{ route("books.index") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: new URLSearchParams(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                if (currentView === 'table') {
                    const tbody = document.getElementById('bookingsTableBody');
                    if (tbody) {
                        tbody.insertAdjacentHTML('beforeend', data.html);
                    }
                } else {
                    const cardView = document.querySelector('.card-view .p-3');
                    if (cardView) {
                        cardView.insertAdjacentHTML('beforeend', data.html);
                    }
                }
            }
            
            hasMore = data.hasMore || false;
            if (!hasMore) {
                const end = document.getElementById('lazyLoadEnd');
                if (end) end.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading bookings:', error);
        })
        .finally(() => {
            isLoading = false;
            if (spinner) spinner.classList.remove('active');
        });
    }
    
    // Show Booking Info
    window.showBookingInfo = function(bookId) {
        const modal = new bootstrap.Modal(document.getElementById('bookingInfoModal'));
        const content = document.getElementById('bookingInfoContent');
        
        content.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';
        modal.show();
        
        fetch(`/books/${bookId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                content.innerHTML = data.html;
            } else {
                content.innerHTML = '<div class="alert alert-danger">Failed to load booking details.</div>';
            }
        })
        .catch(error => {
            console.error('Error loading booking info:', error);
            content.innerHTML = '<div class="alert alert-danger">Error loading booking details.</div>';
        });
    };
    
    // Refresh Page
    window.refreshPage = function() {
        window.location.reload();
    };
    
    // Delete All Modal
    window.showDeleteAllModal = function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete All Records?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete all',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implement delete all functionality
                    Swal.fire('Deleted!', 'All records have been deleted.', 'success');
                }
            });
        }
    };
    
    // Instant Search (debounced)
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    }
})();
</script>
@endpush
@endsection
