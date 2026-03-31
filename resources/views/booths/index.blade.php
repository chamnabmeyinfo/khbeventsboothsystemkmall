@extends('layouts.app')

@section('title', 'Booths — KHB Events')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<link rel="stylesheet" href="{{ asset('css/books-page-index.css') }}?v=1.0">
<link rel="stylesheet" href="{{ asset('css/booths-on-books.css') }}?v=1.0">
@endpush

@push('body-class', 'ios-dashboard-mode')

@section('content')
@php
    $allBooths = $booths instanceof \Illuminate\Pagination\LengthAwarePaginator
        ? $booths->getCollection()
        : collect($booths ?? []);

    $boothsTotal    = isset($booths) && method_exists($booths, 'total') ? $booths->total() : $allBooths->count();
    $availableCount = $stats['available'] ?? $allBooths->filter(fn($b) => $b->status == 1)->count();
    $reservedCount  = $stats['reserved']  ?? $allBooths->filter(fn($b) => $b->status == 3)->count();
    $bookedCount    = $stats['booked']    ?? $allBooths->filter(fn($b) => in_array($b->status, [2,4,5]))->count();

    $activeFilter = request('status', 'all');
    if ($activeFilter === '1') $activeFilter = 'available';
    elseif ($activeFilter === '3') $activeFilter = 'reserved';
    elseif (in_array($activeFilter, ['2','4','5'])) $activeFilter = 'booked';

    $boothFilterActiveCount = (request()->filled('search') ? 1 : 0) + (request()->filled('status') ? 1 : 0);
@endphp

<div class="looker-dashboard books-page">

    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Booths</h1>
            <p>Manage your booth inventory, availability, and assignments.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('booths.index', ['view' => 'management']) }}" class="action-btn action-btn-secondary d-none d-md-inline-flex">
                <i class="fas fa-table" aria-hidden="true"></i> Full management
            </a>
            <a href="{{ route('booths.index', ['view' => 'canvas']) }}" class="action-btn action-btn-secondary d-none d-md-inline-flex">
                <i class="fas fa-th-large" aria-hidden="true"></i> Floor Plan
            </a>
            <a href="{{ route('booths.index', ['view' => 'canvas', 'create' => 1]) }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus"></i> New Booth
            </a>
        </div>
    </header>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker">
            <div class="kpi-top">
                <div class="kpi-title">Total Booths</div>
                <div class="kpi-icon-wrapper primary-icon">
                    <i class="fas fa-cube"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $boothsTotal }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-circle" style="font-size: 8px;"></i> In directory
            </div>
        </div>
        <div class="kpi-card-looker success">
            <div class="kpi-top">
                <div class="kpi-title">Available</div>
                <div class="kpi-icon-wrapper success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $availableCount }}</div>
            <div class="kpi-bottom trend-positive">
                <i class="fas fa-fw fa-check"></i> Open for booking
            </div>
        </div>
        <div class="kpi-card-looker warning">
            <div class="kpi-top">
                <div class="kpi-title">Reserved</div>
                <div class="kpi-icon-wrapper warning-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $reservedCount }}</div>
            <div class="kpi-bottom trend-warning">
                <i class="fas fa-fw fa-hourglass-half"></i> Pending
            </div>
        </div>
        <div class="kpi-card-looker purple">
            <div class="kpi-top">
                <div class="kpi-title">Booked</div>
                <div class="kpi-icon-wrapper purple-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $bookedCount }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-link"></i> Confirmed / paid
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('booths.index') }}" id="boothFilterForm">
            <div class="filter-header">
                <h6>
                    <i class="fas fa-filter"></i> Filters
                    <span class="filter-badge {{ $boothFilterActiveCount > 0 ? '' : 'd-none' }}" id="boothsFilterBadge">{{ $boothFilterActiveCount }} active</span>
                </h6>
            </div>

            <div class="filter-row-primary">
                <div class="flex-grow-1" style="min-width: 200px;">
                    <label class="form-label small mb-1" for="boothSearch">Search</label>
                    <input
                        type="search"
                        name="search"
                        id="boothSearch"
                        class="form-control form-control-modern form-control-sm"
                        placeholder="Booth number, client…"
                        value="{{ request('search') }}"
                        autocomplete="off"
                    >
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="action-btn action-btn-primary books-filter-apply">
                    <i class="fas fa-filter me-1"></i> Apply
                </button>
                <a href="{{ route('booths.index') }}" class="action-btn action-btn-secondary books-filter-clear">
                    <i class="fas fa-times me-1"></i> Clear all
                </a>
                <div class="d-flex flex-wrap gap-2 ms-md-2 align-items-center">
                    <span class="text-muted small me-1">Status:</span>
                    <a href="{{ route('booths.index', ['search' => request('search')]) }}"
                       class="filter-chip text-decoration-none {{ $activeFilter === 'all' ? 'active' : '' }}">All</a>
                    <button type="submit" name="status" value="1"
                            class="filter-chip border-0 {{ $activeFilter === 'available' ? 'active' : '' }}">
                        Available
                    </button>
                    <button type="submit" name="status" value="3"
                            class="filter-chip border-0 {{ $activeFilter === 'reserved' ? 'active' : '' }}">
                        Reserved
                    </button>
                    <button type="submit" name="status" value="2"
                            class="filter-chip border-0 {{ $activeFilter === 'booked' ? 'active' : '' }}">
                        Booked
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(isset($booths) && method_exists($booths, 'total'))
    <div class="booths-section-meta">
        {{ $booths->total() }} booth{{ $booths->total() !== 1 ? 's' : '' }}
        @if(request('search')) · matching "{{ request('search') }}"@endif
    </div>
    @endif

    <div class="canvas-panel books-data-panel">
        <div class="table-view d-none d-md-block">
            <div class="looker-table-wrapper">
                <table class="looker-table books-looker-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Booth</th>
                            <th scope="col">Status</th>
                            <th scope="col">Client</th>
                            <th scope="col">Event / Floor Plan</th>
                            <th scope="col">Price</th>
                            <th scope="col">Next Booking</th>
                            <th scope="col" class="books-col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booths as $booth)
                        <tr class="books-table-row books-table-row--booths">
                            <td>
                                <div class="avatar-chip">
                                    <div class="avatar-circle" aria-hidden="true">
                                        {{ strtoupper(substr($booth->booth_number ?? 'B', 0, 2)) }}
                                    </div>
                                    <div class="avatar-chip-text">
                                        <span class="avatar-chip-title">{{ $booth->booth_number }}</span>
                                        <span class="avatar-chip-sub">{{ $booth->boothType?->name ?? 'Standard' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="ap-badge badge-{{ $booth->getStatusColor() }}">
                                    {{ $booth->getStatusLabel() }}
                                </span>
                            </td>
                            <td class="cell-muted">
                                @if($booth->client)
                                    <a href="{{ route('clients.show', $booth->client) }}" class="booths-client-link">
                                        {{ $booth->client->company }}
                                    </a>
                                @else
                                    <span class="booths-dash">—</span>
                                @endif
                            </td>
                            <td class="cell-muted">
                                {{ $booth->floorPlan?->name ?? '—' }}
                            </td>
                            <td>
                                <strong class="books-amount-cell">${{ number_format($booth->price, 2) }}</strong>
                            </td>
                            <td class="cell-muted">
                                @if($booth->book && $booth->book->date_book)
                                    <span class="booths-booking-date">
                                        {{ $booth->book->date_book->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="booths-dash">—</span>
                                @endif
                            </td>
                            <td class="books-col-actions" onclick="event.stopPropagation()">
                                <div class="books-table-actions" role="group" aria-label="Booth actions">
                                    <a href="{{ route('booths.show', $booth) }}"
                                       class="books-table-btn plastic-btn-press"
                                       title="View booth"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ route('booths.edit', $booth) }}"
                                       class="books-table-btn plastic-btn-press"
                                       title="Edit booth"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                                    <div class="dropdown d-inline-block">
                                        <button type="button"
                                                class="books-table-btn plastic-btn-press"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-label="More options for booth {{ $booth->booth_number }}"
                                                title="More options">
                                            <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('booths.show', $booth) }}">
                                                    <i class="fas fa-eye me-2 text-muted" style="width:14px;" aria-hidden="true"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('booths.edit', $booth) }}">
                                                    <i class="fas fa-pencil-alt me-2 text-muted" style="width:14px;" aria-hidden="true"></i>Edit Booth
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form action="{{ route('booths.destroy', $booth) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Delete booth {{ addslashes($booth->booth_number) }}? This cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash-alt me-2" style="width:14px;" aria-hidden="true"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-store-slash empty-state-icon" aria-hidden="true"></i>
                                    <h3>No booths found</h3>
                                    <p class="text-muted">
                                        @if(request('search'))Try clearing filters or add a booth for "{{ request('search') }}".@else Add booths from the floor plan or full management view.@endif
                                    </p>
                                    <a href="{{ route('booths.index', ['view' => 'canvas', 'create' => 1]) }}" class="action-btn action-btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Add your first booth
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-view d-md-none">
            <div class="books-card-view-inner">
                @forelse($booths as $booth)
                <div class="booking-card books-booking-card books-booking-card--type-1"
                     onclick="window.location='{{ route('booths.show', $booth) }}'">
                    <div class="books-booking-card__head">
                        <div class="books-booking-card__head-text">
                            <span class="books-booking-card__row">Booth</span>
                            <h3 class="books-booking-card__title">
                                <i class="fas fa-cube me-2" aria-hidden="true"></i>{{ $booth->booth_number }}
                            </h3>
                            <div class="books-booking-card__badges">
                                <span class="ap-badge badge-{{ $booth->getStatusColor() }}">{{ $booth->getStatusLabel() }}</span>
                            </div>
                        </div>
                        <div class="books-booking-card__actions" onclick="event.stopPropagation()">
                            <div class="books-table-actions" role="group" aria-label="Booth actions">
                                <a href="{{ route('booths.show', $booth) }}" class="books-table-btn plastic-btn-press" title="View"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                <a href="{{ route('booths.edit', $booth) }}" class="books-table-btn plastic-btn-press" title="Edit"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="books-booking-card__field">
                        <span class="books-booking-card__label">Floor plan</span>
                        <div class="books-booking-card__value">{{ $booth->floorPlan?->name ?? '—' }}</div>
                    </div>
                    <div class="books-booking-card__client">
                        <div class="books-booking-card__client-text">
                            <div class="books-booking-card__client-name">{{ $booth->client?->company ?? 'No client' }}</div>
                            <div class="books-booking-card__client-meta">{{ $booth->boothType?->name ?? 'Standard type' }}</div>
                        </div>
                    </div>
                    <div class="books-booking-card__footer">
                        <span class="books-booking-card__amount books-amount-cell">${{ number_format($booth->price, 2) }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state books-card-view-empty">
                    <i class="fas fa-store-slash empty-state-icon" aria-hidden="true"></i>
                    <h3>No booths found</h3>
                    <p class="text-muted">Try adjusting filters or create a booth on the floor plan.</p>
                    <a href="{{ route('booths.index', ['view' => 'canvas', 'create' => 1]) }}" class="action-btn action-btn-primary mt-3">
                        <i class="fas fa-plus"></i> New Booth
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        @if(isset($booths) && $booths->hasPages())
        <div class="booths-table-foot">
            <small>
                Showing {{ $booths->firstItem() ?? 0 }}–{{ $booths->lastItem() ?? 0 }}
                of {{ $booths->total() }} booths
            </small>
            <nav aria-label="Booths pagination">
                <ul class="pagination">
                    <li class="page-item {{ $booths->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $booths->onFirstPage() ? '#' : $booths->appends(request()->query())->url(1) }}" aria-label="First"><i class="fas fa-angle-double-left"></i></a>
                    </li>
                    <li class="page-item {{ $booths->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $booths->onFirstPage() ? '#' : $booths->appends(request()->query())->previousPageUrl() }}" aria-label="Previous"><i class="fas fa-angle-left"></i></a>
                    </li>
                    @php
                        $currentPage = $booths->currentPage();
                        $lastPage    = $booths->lastPage();
                        $winStart    = max(1, $currentPage - 2);
                        $winEnd      = min($lastPage, $currentPage + 2);
                    @endphp
                    @if($winStart > 1)
                        <li class="page-item"><a class="page-link" href="{{ $booths->appends(request()->query())->url(1) }}">1</a></li>
                        @if($winStart > 2)
                            <li class="page-item disabled"><span class="page-link booths-page-ellipsis">…</span></li>
                        @endif
                    @endif
                    @for($p = $winStart; $p <= $winEnd; $p++)
                        <li class="page-item {{ $p === $currentPage ? 'active' : '' }}">
                            <a class="page-link" href="{{ $booths->appends(request()->query())->url($p) }}">{{ $p }}</a>
                        </li>
                    @endfor
                    @if($winEnd < $lastPage)
                        @if($winEnd < $lastPage - 1)
                            <li class="page-item disabled"><span class="page-link booths-page-ellipsis">…</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $booths->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a></li>
                    @endif
                    <li class="page-item {{ $booths->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $booths->hasMorePages() ? $booths->appends(request()->query())->nextPageUrl() : '#' }}" aria-label="Next"><i class="fas fa-angle-right"></i></a>
                    </li>
                    <li class="page-item {{ $booths->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $booths->hasMorePages() ? $booths->appends(request()->query())->url($lastPage) : '#' }}" aria-label="Last"><i class="fas fa-angle-double-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('boothSearch');
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.closest('form').submit();
            }
        });
    }

    var rows = document.querySelectorAll('.books-page .books-looker-table tbody tr.books-table-row--booths');
    rows.forEach(function (row, i) {
        row.style.opacity = '0';
        row.style.transform = 'translateY(8px)';
        row.style.transition = 'opacity 0.3s ease ' + (i * 0.03) + 's, transform 0.3s ease ' + (i * 0.03) + 's';
        requestAnimationFrame(function () {
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        });
    });

    var cards = document.querySelectorAll('.books-page .books-booking-card');
    cards.forEach(function (card, i) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        card.style.transition = 'opacity 0.35s ease ' + (i * 0.05) + 's, transform 0.35s ease ' + (i * 0.05) + 's';
        requestAnimationFrame(function () {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush
