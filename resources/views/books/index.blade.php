@extends('layouts.adminlte')

@section('title', 'Bookings Management')
@section('page-title', 'Bookings Management')
@section('breadcrumb', 'Bookings')

@push('styles')
<style>
    /* Modern Glassmorphism KPI Cards */
    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
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
    .kpi-card.info::before { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
    .kpi-card.warning::before { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

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
    .kpi-card.info .kpi-icon { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
    .kpi-card.warning .kpi-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

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

    .booking-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        border-left: 4px solid;
        cursor: pointer;
    }
    .booking-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }
    .booking-card.regular { border-left-color: #667eea; }
    .booking-card.special { border-left-color: #ffc107; }
    .booking-card.temporary { border-left-color: #dc3545; }
    
    .table-row-hover {
        transition: all 0.2s;
    }
    .table-row-hover:hover {
        background-color: #f8f9fc;
        transform: translateX(4px);
    }
    
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
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
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
        <div class="col-lg-3 col-md-6 mb-3">
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
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="kpi-label">This Month</div>
                    <div class="kpi-value">{{ number_format(\App\Models\Book::whereMonth('date_book', now()->month)->whereYear('date_book', now()->year)->count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <div class="kpi-label">Total Booths Booked</div>
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
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <a href="{{ route('books.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>New Booking
                        </a>
                        <a href="{{ route('export.bookings') }}" class="btn btn-success">
                            <i class="fas fa-file-csv mr-1"></i>Export CSV
                        </a>
                        <button type="button" class="btn btn-info" onclick="refreshPage()">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary" onclick="switchView('table')" id="viewTable">
                            <i class="fas fa-table mr-1"></i>Table
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="switchView('cards')" id="viewCards">
                            <i class="fas fa-th-large mr-1"></i>Cards
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Search and Filter -->
    <div class="filter-bar">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 style="font-weight: 600; color: #2d3748;">
                <i class="fas fa-filter mr-2 text-primary"></i>Search & Filters
            </h3>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleFilters()">
                <i class="fas fa-chevron-down" id="filterToggleIcon"></i>
            </button>
        </div>
        <div id="filterSection">
            <form method="GET" action="{{ route('books.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label><i class="fas fa-search mr-1"></i>Search</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by client, company, or user..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label><i class="fas fa-calendar mr-1"></i>From Date</label>
                        <input type="date" name="date_from" class="form-control" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label><i class="fas fa-calendar mr-1"></i>To Date</label>
                        <input type="date" name="date_to" class="form-control" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label><i class="fas fa-tag mr-1"></i>Type</label>
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Regular</option>
                            <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Special</option>
                            <option value="3" {{ request('type') == '3' ? 'selected' : '' }}>Temporary</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-1"></i>Apply Filters
                        </button>
                        <a href="{{ route('books.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i>Clear Filters
                        </a>
                        @if(request()->hasAny(['search', 'date_from', 'date_to', 'type']))
                        <span class="badge badge-info ml-2">
                            {{ $books->total() }} result(s) found
                        </span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table View -->
    <div id="tableView" class="view-content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i>All Bookings</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">{{ $books->total() }} Total</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="selectAllBookings" class="form-check-input">
                                </th>
                                <th style="width: 80px;">ID</th>
                                <th>Client Information</th>
                                <th style="width: 120px;">Booths</th>
                                <th style="width: 150px;">Booking Date</th>
                                <th style="width: 120px;">Type</th>
                                <th style="width: 120px;">Booked By</th>
                                <th style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($books as $book)
                            @php
                                $boothIds = json_decode($book->boothid, true) ?? [];
                                $boothCount = count($boothIds);
                                $typeClass = 'regular';
                                if ($book->type == 2) {
                                    $typeClass = 'special';
                                } elseif ($book->type == 3) {
                                    $typeClass = 'temporary';
                                }
                            @endphp
                            <tr class="table-row-hover">
                                <td>
                                    <input type="checkbox" class="form-check-input booking-checkbox" value="{{ $book->id }}">
                                </td>
                                <td>
                                    <strong class="text-primary">#{{ $book->id }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <x-avatar 
                                                :avatar="$book->client->avatar ?? null" 
                                                :name="$book->client->name ?? 'N/A'" 
                                                :size="'xs'" 
                                                :type="'client'"
                                                :shape="'circle'"
                                            />
                                        </div>
                                        <div>
                                            <strong>{{ $book->client ? ($book->client->company ?? $book->client->name) : 'N/A' }}</strong>
                                            @if($book->client && $book->client->company && $book->client->name)
                                            <br><small class="text-muted">{{ $book->client->name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info stat-badge">
                                        <i class="fas fa-cube mr-1"></i>{{ $boothCount }}
                                    </span>
                                    <small class="d-block text-muted">{{ $boothCount == 1 ? 'booth' : 'booths' }}</small>
                                </td>
                                <td>
                                    <div>
                                        <i class="fas fa-calendar text-muted mr-1"></i>
                                        <strong>{{ $book->date_book->format('M d, Y') }}</strong>
                                    </div>
                                    <small class="text-muted">{{ $book->date_book->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="badge booking-type-badge 
                                        {{ $book->type == 1 ? 'badge-primary' : ($book->type == 2 ? 'badge-warning' : 'badge-danger') }}">
                                        @if($book->type == 1) Regular
                                        @elseif($book->type == 2) Special
                                        @elseif($book->type == 3) Temporary
                                        @else {{ $book->type }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($book->user)
                                            <x-avatar 
                                                :avatar="$book->user->avatar" 
                                                :name="$book->user->username" 
                                                :size="'xs'" 
                                                :type="$book->user->isAdmin() ? 'admin' : 'user'"
                                                :shape="'circle'"
                                            />
                                            <small class="text-muted ml-2">{{ $book->user->username }}</small>
                                        @else
                                            <i class="fas fa-server text-muted mr-1"></i>
                                            <small class="text-muted">System</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('books.show', $book) }}" class="btn btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin())
                                        <button type="button" class="btn btn-danger" onclick="deleteBooking({{ $book->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <p class="mb-0">No bookings found</p>
                                        <a href="{{ route('books.create') }}" class="btn btn-primary btn-sm mt-3">
                                            <i class="fas fa-plus mr-1"></i>Create First Booking
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($books, 'hasPages') && $books->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="text-muted">
                            @if($books->firstItem())
                            Showing {{ $books->firstItem() }} to {{ $books->lastItem() }} of {{ $books->total() }} bookings
                            @else
                            {{ $books->total() }} booking(s) total
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $books->links() }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Card View -->
    <div id="cardView" class="view-content" style="display: none;">
        <div class="row">
            @forelse($books as $book)
            @php
                $boothIds = json_decode($book->boothid, true) ?? [];
                $boothCount = count($boothIds);
                $typeClass = 'regular';
                if ($book->type == 2) {
                    $typeClass = 'special';
                } elseif ($book->type == 3) {
                    $typeClass = 'temporary';
                }
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card booking-card {{ $typeClass }}" onclick="window.location='{{ route('books.show', $book) }}'">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-check mr-1"></i>Booking #{{ $book->id }}
                            </h6>
                            <span class="badge booking-type-badge 
                                {{ $book->type == 1 ? 'badge-primary' : ($book->type == 2 ? 'badge-warning' : 'badge-danger') }}">
                                @if($book->type == 1) Regular
                                @elseif($book->type == 2) Special
                                @elseif($book->type == 3) Temporary
                                @else {{ $book->type }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-building text-muted mr-2"></i>
                                <strong>{{ $book->client ? ($book->client->company ?? $book->client->name) : 'N/A' }}</strong>
                            </div>
                            @if($book->client && $book->client->name)
                            <small class="text-muted ml-4">{{ $book->client->name }}</small>
                            @endif
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-cube text-muted mr-2"></i>
                                <span class="badge badge-info">{{ $boothCount }} {{ $boothCount == 1 ? 'Booth' : 'Booths' }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-calendar text-muted mr-2"></i>
                                <strong>{{ $book->date_book->format('M d, Y') }}</strong>
                            </div>
                            <small class="text-muted ml-4">{{ $book->date_book->format('h:i A') }}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="text-muted mr-2">Booked by:</span>
                            @if($book->user)
                                <x-avatar 
                                    :avatar="$book->user->avatar" 
                                    :name="$book->user->username" 
                                    :size="'xs'" 
                                    :type="$book->user->isAdmin() ? 'admin' : 'user'"
                                    :shape="'circle'"
                                />
                                <small class="text-muted ml-1">{{ $book->user->username }}</small>
                            @else
                                <i class="fas fa-server text-muted mr-1"></i>
                                <small class="text-muted">System</small>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('books.show', $book) }}" class="btn btn-info" onclick="event.stopPropagation()">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            @if(auth()->user()->isAdmin())
                            <button type="button" class="btn btn-danger" onclick="event.stopPropagation(); deleteBooking({{ $book->id }});">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No bookings found</p>
                        <a href="{{ route('books.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Create First Booking
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        @if(method_exists($books, 'hasPages') && $books->hasPages())
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="text-muted">
                                    @if($books->firstItem())
                                    Showing {{ $books->firstItem() }} to {{ $books->lastItem() }} of {{ $books->total() }} bookings
                                    @else
                                    {{ $books->total() }} booking(s) total
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="float-right">
                                    {{ $books->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// View Toggle
function switchView(view) {
    if (view === 'table') {
        $('#tableView').show();
        $('#cardView').hide();
        $('#viewTable').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
        $('#viewCards').removeClass('active').removeClass('btn-primary').addClass('btn-outline-secondary');
        localStorage.setItem('bookingView', 'table');
    } else {
        $('#tableView').hide();
        $('#cardView').show();
        $('#viewTable').removeClass('active').removeClass('btn-primary').addClass('btn-outline-secondary');
        $('#viewCards').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
        localStorage.setItem('bookingView', 'cards');
    }
}

// Load saved view preference
$(document).ready(function() {
    const savedView = localStorage.getItem('bookingView') || 'table';
    switchView(savedView);
    
    // Initialize view toggle buttons
    if (savedView === 'table') {
        $('#viewTable').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
    } else {
        $('#viewCards').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
    }
});

// Toggle Filters
function toggleFilters() {
    $('#filterSection').slideToggle();
    const icon = $('#filterToggleIcon');
    icon.toggleClass('fa-chevron-down fa-chevron-up');
}

// Select All Checkboxes
$('#selectAllBookings').on('change', function() {
    $('.booking-checkbox').prop('checked', $(this).prop('checked'));
    updateBulkActions();
});

$('.booking-checkbox').on('change', function() {
    updateBulkActions();
    $('#selectAllBookings').prop('checked', $('.booking-checkbox:checked').length === $('.booking-checkbox').length);
});

function updateBulkActions() {
    const count = $('.booking-checkbox:checked').length;
    // You can add bulk actions toolbar here if needed
}

// Delete Booking
function deleteBooking(id) {
    if (confirm('Are you sure you want to delete this booking?')) {
        showLoading();
        fetch(`/books/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                toastr.success(data.message || 'Booking deleted successfully');
                location.reload();
            } else {
                toastr.error(data.message || 'Failed to delete booking');
            }
        })
        .catch(error => {
            hideLoading();
            toastr.error('Error: ' + error.message);
        });
    }
}

// Refresh Page
function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Auto-submit filters on change (optional)
$('#filterForm input, #filterForm select').on('change', function() {
    // Uncomment to auto-submit on change
    // $('#filterForm').submit();
});
</script>
@endpush
