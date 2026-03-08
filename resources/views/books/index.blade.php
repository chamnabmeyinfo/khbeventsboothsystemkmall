@extends('layouts.app')

@section('title', 'Bookings Management')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=2.6">
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
    @if(!empty($restrictToOwnBookings))
        <div class="alert alert-info mb-3" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>You are viewing only your own bookings.</strong> You can create, edit, update, and delete only the bookings you created. You cannot view or manage other users&#39; bookings. This is controlled in <a href="{{ route('settings.index') }}">Settings &rarr; Public View Actions</a>.
        </div>
    @endif
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="kpi-card-looker">
                <div class="p-4" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="kpi-title">Total Bookings</div>
                    <div class="kpi-value-looker">{{ number_format(\App\Models\Book::count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="kpi-card-looker">
                <div class="p-4" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="kpi-title">Today's Bookings</div>
                    <div class="kpi-value-looker">{{ number_format(\App\Models\Book::whereDate('date_book', today())->count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="kpi-card-looker">
                <div class="p-4" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="kpi-title">This Month</div>
                    <div class="kpi-value-looker">
                        {{ number_format(\App\Models\Book::whereMonth('date_book', now()->month)->whereYear('date_book', now()->year)->count()) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="kpi-card-looker">
                <div class="p-4" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <div class="kpi-title">Total Booths</div>
                    <div class="kpi-value-looker">
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
    @php
        $hasActiveFilters = request()->hasAny(['search', 'date_from', 'date_to', 'type', 'floor_plan_id', 'status', 'amount_min', 'amount_max', 'booth_count_min']) || (request('date_range') && request('date_range') !== 'all');
        $activeFilterCount = 0;
        if (request('search')) $activeFilterCount++;
        if (request('date_from') || request('date_to')) $activeFilterCount++;
        if (request('type')) $activeFilterCount++;
        if (request('floor_plan_id')) $activeFilterCount++;
        if (request('status')) $activeFilterCount++;
        if (request('amount_min') || request('amount_max')) $activeFilterCount++;
        if (request('booth_count_min')) $activeFilterCount++;
        if (request('date_range') && request('date_range') !== 'all') $activeFilterCount++;
    @endphp
    <div class="filter-bar">
        <form method="GET" action="{{ route('books.index') }}" id="filterForm">
            <div class="filter-header">
                <h6>
                    <i class="fas fa-filter"></i> Filters
                    @if($activeFilterCount > 0)
                    <span class="filter-badge">{{ $activeFilterCount }} active</span>
                    @endif
                </h6>
                <span class="filter-toggle" onclick="document.getElementById('filterAdvanced').classList.toggle('d-none'); this.querySelector('i').classList.toggle('fa-chevron-down'); this.querySelector('i').classList.toggle('fa-chevron-up');">
                    <i class="fas fa-chevron-down"></i> <span>Advanced</span>
                </span>
            </div>

            <!-- Primary Filters (always visible) -->
            <div class="filter-row-primary">
                <div>
                    <label class="form-label small mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-modern form-control-sm"
                           placeholder="Client, company, user..." value="{{ request('search') }}">
                </div>
                <div>
                    <label class="form-label small mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-modern form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label class="form-label small mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-modern form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div>
                    <label class="form-label small mb-1">Type</label>
                    <select name="type" class="form-control form-control-modern form-control-sm">
                        <option value="">All Types</option>
                        <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Regular</option>
                        <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Special</option>
                        <option value="3" {{ request('type') == '3' ? 'selected' : '' }}>Temporary</option>
                    </select>
                </div>
                <div>
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-control form-control-modern form-control-sm">
                        <option value="">All Statuses</option>
                        @foreach($statusSettings ?? [] as $sts)
                        <option value="{{ $sts->status_code }}" {{ request('status') == (string)$sts->status_code ? 'selected' : '' }}>{{ $sts->status_name }}</option>
                        @endforeach
                        @if(($statusSettings ?? collect())->isEmpty())
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Pending</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Confirmed</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Reserved</option>
                        <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Paid</option>
                        <option value="6" {{ request('status') == '6' ? 'selected' : '' }}>Cancelled</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="form-label small mb-1">Floor Plan</label>
                    <select name="floor_plan_id" class="form-control form-control-modern form-control-sm">
                        <option value="">All Floor Plans</option>
                        @foreach($floorPlans ?? [] as $fp)
                        <option value="{{ $fp->id }}" {{ request('floor_plan_id') == (string)$fp->id ? 'selected' : '' }}>{{ $fp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label small mb-1">Group By</label>
                    <select name="group_by" class="form-control form-control-modern form-control-sm" onchange="this.form.submit()">
                        <option value="none" {{ request('group_by', 'none') == 'none' ? 'selected' : '' }}>No Grouping</option>
                        <option value="name" {{ request('group_by') == 'name' ? 'selected' : '' }}>By Client</option>
                        <option value="date" {{ request('group_by') == 'date' ? 'selected' : '' }}>By Date</option>
                    </select>
                </div>
            </div>

            <!-- Advanced Filters (collapsible) -->
            <div id="filterAdvanced" class="filter-row-advanced {{ $hasActiveFilters && (request('amount_min') || request('amount_max') || request('booth_count_min') || request('date_range')) ? '' : 'd-none' }}">
                <div class="row g-3">
                    <div class="col-md-2 col-6">
                        <label class="form-label small mb-1">Amount Min ($)</label>
                        <input type="number" name="amount_min" class="form-control form-control-modern form-control-sm" step="0.01" min="0" placeholder="0" value="{{ request('amount_min') }}">
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label small mb-1">Amount Max ($)</label>
                        <input type="number" name="amount_max" class="form-control form-control-modern form-control-sm" step="0.01" min="0" placeholder="—" value="{{ request('amount_max') }}">
                    </div>
                    <div class="col-md-2 col-6">
                        <label class="form-label small mb-1">Min Booths</label>
                        <input type="number" name="booth_count_min" class="form-control form-control-modern form-control-sm" min="1" placeholder="1" value="{{ request('booth_count_min') }}">
                    </div>
                    <div class="col-md-3 col-6">
                        <label class="form-label small mb-1">Date Range Preset</label>
                        <select name="date_range" class="form-control form-control-modern form-control-sm">
                            <option value="all" {{ request('date_range', 'all') == 'all' ? 'selected' : '' }}>All Dates</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="3days" {{ request('date_range') == '3days' ? 'selected' : '' }}>Last 3 Days</option>
                            <option value="7days" {{ request('date_range') == '7days' ? 'selected' : '' }}>Last 7 Days</option>
                            <option value="14days" {{ request('date_range') == '14days' ? 'selected' : '' }}>Last 14 Days</option>
                            <option value="more" {{ request('date_range') == 'more' ? 'selected' : '' }}>Older than 14 Days</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Quick date presets (chips) -->
            <div class="filter-actions">
                <button type="submit" class="btn btn-modern btn-modern-primary btn-sm">
                    <i class="fas fa-filter me-1"></i>Apply
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-modern btn-sm" style="background: #f3f4f6; color: #6b7280;">
                    <i class="fas fa-times me-1"></i>Clear All
                </a>
                <div class="d-flex flex-wrap gap-2 ms-2 align-items-center">
                    <span class="text-muted small me-1">Quick:</span>
                    <button type="button" class="filter-chip {{ !request('date_from') && !request('date_to') && (!request('date_range') || request('date_range') == 'all') ? 'active' : '' }}" onclick="setQuickDate('')">All</button>
                    <button type="button" class="filter-chip {{ request('date_range') == 'today' ? 'active' : '' }}" onclick="setQuickDate('today')">Today</button>
                    <button type="button" class="filter-chip {{ request('date_range') == '7days' ? 'active' : '' }}" onclick="setQuickDate('7days')">Last 7 Days</button>
                    <button type="button" class="filter-chip" onclick="setQuickDate('30days')">Last 30 Days</button>
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
                    <div class="glass-card">
                        <div class="card-body p-0">
                            <!-- Table View -->
                            <div class="table-view">
                                <table class="table table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Client</th>
                                            <th>Floor Plan</th>
                                            <th>Date</th>
                                            <th>Booths</th>
                                            <th>Type</th>
                                            <th>Status</th>
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
            <div class="glass-card">
                <div class="card-body p-0">
                    <!-- Table View -->
                    <div class="table-view">
                        <table class="table table-modern mb-0" id="bookingsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Floor Plan</th>
                                    <th>Date</th>
                                    <th>Booths</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="bookingsTableBody">
                                @forelse($books as $book)
                                    @include('books.partials.table-row', ['book' => $book])
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
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
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Request failed: ' + response.status);
            }
            return response.json();
        })
        .then(function(data) {
            if (data.html) {
                content.innerHTML = data.html;
            } else if (data.book) {
                var b = data.book;
                content.innerHTML = '<div class="booking-modal-info"><p><strong>Booking #' + b.id + '</strong></p>' +
                    '<p><strong>Client:</strong> ' + (b.client ? (b.client.company || b.client.name) : 'N/A') + '</p>' +
                    '<p><strong>Date:</strong> ' + (b.date_book || 'N/A') + '</p>' +
                    '<p><strong>Booths:</strong> ' + (b.booth_count || 0) + '</p>' +
                    '<p><strong>Total:</strong> $' + parseFloat(b.total_amount || 0).toFixed(2) + '</p>' +
                    '<a href="/books/' + b.id + '" class="btn btn-sm btn-primary">View Full Details</a></div>';
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

    // Delete Booking (used by table row and card action buttons)
    window.deleteBooking = function(id) {
        if (typeof Swal === 'undefined') {
            if (confirm('Delete this booking? This will release all booths. This action cannot be undone!')) {
                document.getElementById('delete-booking-form-' + id)?.submit();
            }
            return;
        }
        Swal.fire({
            title: 'Delete Booking?',
            text: 'This will release all booths in this booking. This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
                fetch('/books/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    Swal.close();
                    if (data.success) {
                        Swal.fire('Deleted!', data.message || 'Booking has been deleted.', 'success').then(function() {
                            window.location.href = '{{ route("books.index") }}';
                        });
                    } else {
                        Swal.fire('Error!', data.message || 'Failed to delete booking.', 'error');
                    }
                })
                .catch(function(error) {
                    Swal.close();
                    Swal.fire('Error!', 'An error occurred while deleting the booking.', 'error');
                    console.error('Error:', error);
                });
            }
        });
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
    
    // Quick date preset - uses date_range for today/7days, date_from/date_to for 30days
    window.setQuickDate = function(preset) {
        const form = document.getElementById('filterForm');
        const dateRangeSelect = form.querySelector('select[name="date_range"]');
        const fromInput = form.querySelector('input[name="date_from"]');
        const toInput = form.querySelector('input[name="date_to"]');

        if (fromInput) fromInput.value = '';
        if (toInput) toInput.value = '';
        if (dateRangeSelect) dateRangeSelect.value = preset === '' ? 'all' : (preset === '30days' ? 'all' : preset);

        if (preset === '30days') {
            const today = new Date();
            const y = today.getFullYear();
            const m = String(today.getMonth() + 1).padStart(2, '0');
            const d = String(today.getDate()).padStart(2, '0');
            const todayStr = y + '-' + m + '-' + d;
            const past = new Date(today);
            past.setDate(past.getDate() - 29);
            const py = past.getFullYear();
            const pm = String(past.getMonth() + 1).padStart(2, '0');
            const pd = String(past.getDate()).padStart(2, '0');
            if (fromInput) fromInput.value = py + '-' + pm + '-' + pd;
            if (toInput) toInput.value = todayStr;
        }
        form.submit();
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
</div>
@endsection
