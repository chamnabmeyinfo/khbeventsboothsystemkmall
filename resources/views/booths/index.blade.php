@extends('layouts.app')

@section('title', 'Booths — KHB Events')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<link rel="stylesheet" href="{{ asset('css/books-page-index.css') }}?v=1.0">
<link rel="stylesheet" href="{{ asset('css/booths-on-books.css') }}?v=1.7">
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

    <div class="books-toolbar booths-toolbar d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="books-view-toggle booths-table-cards-toggle" role="group" aria-label="Booth list layout">
            <button type="button" class="active plastic-btn-press" onclick="switchBoothsView('table')" id="boothsViewTable">
                <i class="fas fa-table me-1" aria-hidden="true"></i>Table
            </button>
            <button type="button" class="plastic-btn-press" onclick="switchBoothsView('cards')" id="boothsViewCards" title="Card view — adjust size in Settings">
                <i class="fas fa-th-large me-1" aria-hidden="true"></i>Cards
            </button>
        </div>
        <button type="button" class="action-btn action-btn-secondary booths-list-settings-btn" data-bs-toggle="offcanvas" data-bs-target="#boothsListSettingsOffcanvas" aria-controls="boothsListSettingsOffcanvas" id="boothsListSettingsOpen">
            <i class="fas fa-cog me-1" aria-hidden="true"></i>Booth settings
        </button>
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

    <div class="canvas-panel books-data-panel" id="boothsContainer">
        <div class="table-view">
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

        <div class="card-view" style="display: none;">
            <div class="books-card-view-inner booths-card-density--medium" id="boothsCardViewInner">
                @forelse($booths as $booth)
                @php
                    $boothCardImageUrl = $booth->getDisplayImageUrl();
                @endphp
                <div class="booking-card books-booking-card books-booking-card--type-1"
                     onclick="window.location='{{ route('booths.show', $booth) }}'">
                    <div class="booths-card-media">
                        @if($boothCardImageUrl)
                            <img
                                src="{{ $boothCardImageUrl }}"
                                alt="Booth {{ $booth->booth_number }} photo"
                                class="booths-card-media__img"
                                loading="lazy"
                                decoding="async"
                            >
                        @else
                            <div class="booths-card-media__placeholder">
                                <i class="fas fa-image" aria-hidden="true"></i>
                                <span class="visually-hidden">No booth photo</span>
                            </div>
                        @endif
                    </div>
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
                    <div class="books-booking-card__mid booths-card-mid">
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

    {{-- Booth list settings: card size + (admins) default card image --}}
    <div class="offcanvas offcanvas-end booths-list-settings-offcanvas text-body" tabindex="-1" id="boothsListSettingsOffcanvas" aria-labelledby="boothsListSettingsLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title d-flex align-items-center gap-2" id="boothsListSettingsLabel">
                <i class="fas fa-sliders-h text-primary" aria-hidden="true"></i>Booth list settings
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <section class="mb-4">
                <h6 class="fw-bold mb-2">Card layout</h6>
                <p class="text-muted small mb-3">Used when <strong>Cards</strong> view is on. This preference is saved in your browser.</p>
                <div class="books-view-toggle booths-density-toggle booths-density-toggle--settings w-100 flex-wrap" role="group" aria-label="Card size">
                    <button type="button" class="plastic-btn-press" onclick="setBoothsCardDensity('tiny')" id="boothsDensityTiny" title="Tiny list — compact rows">
                        <i class="fas fa-list me-1" aria-hidden="true"></i>Tiny
                    </button>
                    <button type="button" class="plastic-btn-press" onclick="setBoothsCardDensity('small')" id="boothsDensitySmall">Small</button>
                    <button type="button" class="active plastic-btn-press" onclick="setBoothsCardDensity('medium')" id="boothsDensityMedium">Medium</button>
                    <button type="button" class="plastic-btn-press" onclick="setBoothsCardDensity('large')" id="boothsDensityLarge">Large</button>
                </div>
            </section>

            @if(auth()->user()->isAdmin())
            <hr class="my-4">
            <section class="booths-settings-master-image" role="region" aria-label="Default booth card image">
                <h6 class="fw-bold mb-2">Default card image</h6>
                <p class="text-muted small mb-3">Shown when a booth has no photo. Per-booth images always override this.</p>
                <div class="d-flex flex-wrap align-items-start gap-3 mb-3">
                    <div class="booths-settings-master-image__preview flex-shrink-0">
                        <img id="boothsPageMasterImagePreview" src="{{ $masterBoothImageUrl ?? '' }}" alt="" class="rounded border bg-light" width="120" height="120" style="object-fit: cover; {{ $masterBoothImageUrl ? '' : 'display: none;' }}">
                        <p id="boothsPageMasterImagePlaceholder" class="text-muted small mb-0 mt-2 {{ $masterBoothImageUrl ? 'd-none' : '' }}" style="width: 120px;">No image set</p>
                    </div>
                    <div class="flex-grow-1" style="min-width: 200px;">
                        <label class="form-label small" for="boothsPageMasterImageFile">Upload</label>
                        <input type="file" class="form-control form-control-sm" id="boothsPageMasterImageFile" accept="image/jpeg,image/png,image/gif" aria-label="Choose default card image file">
                        <small class="text-muted d-block mt-2">{{ $masterBoothUploadHint ?? '' }}</small>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary btn-sm" id="boothsPageMasterImageSave" disabled>
                        <i class="fas fa-save me-1"></i>Save
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="boothsPageMasterImageRemove" {{ ($masterBoothImageUrl ?? null) ? '' : 'disabled' }}>
                        <i class="fas fa-times me-1"></i>Remove
                    </button>
                </div>
            </section>

            <hr class="my-4">
            <section class="booths-settings-upload" aria-labelledby="boothsSettingsUploadHeading">
                <h6 class="fw-bold mb-2" id="boothsSettingsUploadHeading">Booth photo uploads</h6>
                <p class="text-muted small mb-3">Max size and allowed extensions for <strong>booth images</strong> (gallery and legacy field). Other upload types (floor plan, avatar, …) stay in System Settings.</p>
                <div class="row g-2 mb-2">
                    <div class="col-auto" style="min-width: 7rem;">
                        <label class="form-label small mb-0" for="boothsUploadContextMaxMb">Max size (MB)</label>
                        <input type="number" class="form-control form-control-sm" id="boothsUploadContextMaxMb" name="uploads_booth_max_size_mb" value="{{ old('uploads_booth_max_size_mb', $boothsUploadContext['max_mb'] ?? '') }}" min="0" max="100" step="0.5" placeholder="—">
                    </div>
                    <div class="col">
                        <label class="form-label small mb-0" for="boothsUploadContextExts">Allowed extensions</label>
                        <input type="text" class="form-control form-control-sm" id="boothsUploadContextExts" name="uploads_booth_allowed_extensions" value="{{ old('uploads_booth_allowed_extensions', $boothsUploadContext['extensions'] ?? '') }}" placeholder="e.g. jpg, png, gif" autocomplete="off">
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-sm" id="boothsPageUploadLimitsSave">
                    <i class="fas fa-save me-1"></i>Save upload limits
                </button>
            </section>

            <hr class="my-4">
            <section class="booths-settings-module-nav" aria-labelledby="boothsSettingsModuleHeading">
                <h6 class="fw-bold mb-2" id="boothsSettingsModuleHeading">Booths in menu</h6>
                <p class="text-muted small mb-3">Show the Booths item in the sidebar on smaller screens (desktop is unchanged).</p>
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="boothsModuleNavMobile" value="1" {{ ($boothsModuleNav['mobile'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="boothsModuleNavMobile">Mobile (≤768px)</label>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="boothsModuleNavTablet" value="1" {{ ($boothsModuleNav['tablet'] ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="boothsModuleNavTablet">Tablet (769px–1024px)</label>
                </div>
                <button type="button" class="btn btn-primary btn-sm" id="boothsPageModuleNavSave">
                    <i class="fas fa-save me-1"></i>Save menu visibility
                </button>
            </section>

            <hr class="my-4">
            <section class="booths-settings-more" aria-labelledby="boothsSettingsMoreHeading">
                <h6 class="fw-bold mb-2" id="boothsSettingsMoreHeading">More in System Settings</h6>
                <p class="text-muted small mb-2">These pages still live under <strong>Settings</strong> (full forms, all modules).</p>
                <ul class="mb-0 small ps-3 booths-settings-more__list">
                    <li><a href="{{ route('settings.index') }}#settings-upload-control">Upload control</a> — all file types and global limits</li>
                    <li><a href="{{ route('settings.index') }}#settings-public-view">Public floor plan &amp; booked tick</a> — canvas and public view</li>
                    <li><a href="{{ route('settings.index') }}#module-display">Module display</a> — all modules on mobile/tablet</li>
                </ul>
            </section>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    function staggerTableRows() {
        var rows = document.querySelectorAll('#boothsContainer .books-looker-table tbody tr.books-table-row--booths');
        rows.forEach(function (row, i) {
            row.style.opacity = '0';
            row.style.transform = 'translateY(8px)';
            row.style.transition = 'opacity 0.3s ease ' + (i * 0.03) + 's, transform 0.3s ease ' + (i * 0.03) + 's';
            requestAnimationFrame(function () {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            });
        });
    }

    function staggerCards() {
        var cards = document.querySelectorAll('#boothsContainer .books-booking-card');
        cards.forEach(function (card, i) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(10px)';
            card.style.transition = 'opacity 0.35s ease ' + (i * 0.05) + 's, transform 0.35s ease ' + (i * 0.05) + 's';
            requestAnimationFrame(function () {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });
        });
    }

    window.setBoothsCardDensity = function (size) {
        var allowed = { tiny: 1, small: 1, medium: 1, large: 1 };
        if (!allowed[size]) {
            size = 'medium';
        }
        var inner = document.getElementById('boothsCardViewInner');
        if (!inner) return;

        inner.classList.remove(
            'booths-card-density--tiny',
            'booths-card-density--small',
            'booths-card-density--medium',
            'booths-card-density--large'
        );
        inner.classList.add('booths-card-density--' + size);

        var idSuffix = { tiny: 'Tiny', small: 'Small', medium: 'Medium', large: 'Large' };
        document.querySelectorAll('.booths-density-toggle button').forEach(function (btn) {
            btn.classList.remove('active');
        });
        var activeBtn = document.getElementById('boothsDensity' + idSuffix[size]);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        try {
            localStorage.setItem('boothsCardDensity', size);
        } catch (e) {}

        var cardsBtn = document.getElementById('boothsViewCards');
        if (cardsBtn && cardsBtn.classList.contains('active')) {
            staggerCards();
        }
    };

    window.switchBoothsView = function (view) {
        var tableBtn = document.getElementById('boothsViewTable');
        var cardsBtn = document.getElementById('boothsViewCards');
        if (!tableBtn || !cardsBtn) return;

        document.querySelectorAll('.booths-table-cards-toggle button').forEach(function (btn) {
            btn.classList.remove('active');
        });
        if (view === 'cards') {
            cardsBtn.classList.add('active');
        } else {
            tableBtn.classList.add('active');
            view = 'table';
        }

        document.querySelectorAll('#boothsContainer .table-view').forEach(function (el) {
            el.style.display = view === 'table' ? 'block' : 'none';
        });
        document.querySelectorAll('#boothsContainer .card-view').forEach(function (el) {
            el.style.display = view === 'cards' ? 'block' : 'none';
        });

        try {
            localStorage.setItem('boothsListView', view);
        } catch (e) {}

        if (view === 'table') {
            staggerTableRows();
        } else {
            staggerCards();
        }
    };

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

        var savedDensity = 'medium';
        try {
            savedDensity = localStorage.getItem('boothsCardDensity') || 'medium';
        } catch (e) {}
        setBoothsCardDensity(savedDensity);

        var saved = null;
        try {
            saved = localStorage.getItem('boothsListView');
        } catch (e) {}
        if (saved === 'cards' || saved === 'table') {
            switchBoothsView(saved);
        } else {
            staggerTableRows();
        }

        @if(auth()->user()->isAdmin())
        (function initBoothsAdminSettings() {
            function notify(msg, type) {
                if (typeof window.showNotification === 'function') {
                    window.showNotification(msg, type || 'info');
                } else {
                    alert(msg);
                }
            }
            function updatePreview(url) {
                var img = document.getElementById('boothsPageMasterImagePreview');
                var ph = document.getElementById('boothsPageMasterImagePlaceholder');
                var rm = document.getElementById('boothsPageMasterImageRemove');
                if (!img || !ph) {
                    return;
                }
                if (url) {
                    img.src = url;
                    img.style.display = '';
                    ph.classList.add('d-none');
                    if (rm) {
                        rm.disabled = false;
                    }
                } else {
                    img.style.display = 'none';
                    img.removeAttribute('src');
                    ph.classList.remove('d-none');
                    if (rm) {
                        rm.disabled = true;
                    }
                }
            }
            if (typeof jQuery === 'undefined') {
                return;
            }
            var $file = document.getElementById('boothsPageMasterImageFile');
            var $save = document.getElementById('boothsPageMasterImageSave');
            if ($file && $save) {
                jQuery($file).on('change', function () {
                    jQuery($save).prop('disabled', !this.files[0]);
                });
                jQuery($save).on('click', function () {
                    var input = document.getElementById('boothsPageMasterImageFile');
                    var file = input && input.files[0];
                    if (!file) {
                        notify('Choose an image file first.', 'warning');
                        return;
                    }
                    var formData = new FormData();
                    formData.append('master_image', file);
                    formData.append('_token', '{{ csrf_token() }}');
                    var $btn = jQuery(this);
                    $btn.prop('disabled', true);
                    jQuery.ajax({
                        url: "{{ route('settings.booth-master-image.upload') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.status === 200) {
                                updatePreview(response.url);
                                notify(response.message || 'Default card image saved', 'success');
                                if (input) {
                                    input.value = '';
                                }
                                $btn.prop('disabled', true);
                            }
                        },
                        error: function (xhr) {
                            var msg = (xhr.responseJSON && (xhr.responseJSON.message || (xhr.responseJSON.errors && xhr.responseJSON.errors.master_image && xhr.responseJSON.errors.master_image[0]))) || 'Failed to save';
                            notify(msg, 'error');
                            $btn.prop('disabled', false);
                        }
                    });
                });
                jQuery('#boothsPageMasterImageRemove').on('click', function () {
                    jQuery.ajax({
                        url: "{{ route('settings.booth-master-image.remove') }}",
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function (response) {
                            if (response.status === 200) {
                                updatePreview(null);
                                notify(response.message || 'Default card image removed', 'success');
                            }
                        },
                        error: function (xhr) {
                            notify((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to remove', 'error');
                        }
                    });
                });
            }
            jQuery('#boothsPageUploadLimitsSave').on('click', function () {
                var $btn = jQuery(this);
                $btn.prop('disabled', true);
                jQuery.ajax({
                    url: "{{ route('settings.booth-upload-context.save') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        uploads_booth_max_size_mb: jQuery('#boothsUploadContextMaxMb').val(),
                        uploads_booth_allowed_extensions: jQuery('#boothsUploadContextExts').val()
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            notify(response.message || 'Upload limits saved', 'success');
                        }
                    },
                    error: function (xhr) {
                        notify((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save', 'error');
                    },
                    complete: function () {
                        $btn.prop('disabled', false);
                    }
                });
            });
            jQuery('#boothsPageModuleNavSave').on('click', function () {
                var $btn = jQuery(this);
                $btn.prop('disabled', true);
                jQuery.ajax({
                    url: "{{ route('settings.booths-module-display.save') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        mobile: jQuery('#boothsModuleNavMobile').is(':checked') ? 1 : 0,
                        tablet: jQuery('#boothsModuleNavTablet').is(':checked') ? 1 : 0
                    },
                    success: function (response) {
                        if (response.status === 200) {
                            notify(response.message || 'Menu visibility saved', 'success');
                        }
                    },
                    error: function (xhr) {
                        notify((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to save', 'error');
                    },
                    complete: function () {
                        $btn.prop('disabled', false);
                    }
                });
            });
        })();
        @endif
    });
})();
</script>
@endpush
