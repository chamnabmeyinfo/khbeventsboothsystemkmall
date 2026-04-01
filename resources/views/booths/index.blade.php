@extends('layouts.app')

@section('title', 'Booths — KHB Events')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<link rel="stylesheet" href="{{ asset('css/books-page-index.css') }}?v=1.0">
<link rel="stylesheet" href="{{ asset('css/booths-on-books.css') }}?v=2.3">
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
            @if(auth()->user()->isAdmin())
            <a href="{{ route('export.booths') }}" class="action-btn action-btn-secondary d-none d-md-inline-flex">
                <i class="fas fa-file-csv" aria-hidden="true"></i> Export CSV
            </a>
            @endif
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
        <button type="button" class="action-btn action-btn-secondary booths-list-settings-btn" data-bs-toggle="modal" data-bs-target="#boothsListSettingsModal" aria-controls="boothsListSettingsModal" id="boothsListSettingsOpen">
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

            {{-- Preserves status when using Apply after a status chip was used (GET has no native field for "current status"). --}}
            <input type="hidden" name="status" id="boothFilterStatusHidden" value="{{ request('status') }}">

            <div class="filter-actions">
                <button type="submit" class="action-btn action-btn-primary books-filter-apply">
                    <i class="fas fa-filter me-1"></i> Apply
                </button>
                <a href="{{ route('booths.index') }}" class="action-btn action-btn-secondary books-filter-clear" id="boothsFilterClearLink">
                    <i class="fas fa-times me-1"></i> Clear all
                </a>
                <div class="d-flex flex-wrap gap-2 ms-md-2 align-items-center">
                    <span class="text-muted small me-1">Status:</span>
                    <button type="button" data-booth-filter="all"
                       class="filter-chip border-0 text-decoration-none {{ $activeFilter === 'all' ? 'active' : '' }}">All</button>
                    <button type="button" data-booth-filter="available" data-booth-status="1"
                            class="filter-chip border-0 {{ $activeFilter === 'available' ? 'active' : '' }}">
                        Available
                    </button>
                    <button type="button" data-booth-filter="reserved" data-booth-status="3"
                            class="filter-chip border-0 {{ $activeFilter === 'reserved' ? 'active' : '' }}">
                        Reserved
                    </button>
                    <button type="button" data-booth-filter="booked" data-booth-status="2"
                            class="filter-chip border-0 {{ $activeFilter === 'booked' ? 'active' : '' }}">
                        Booked
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="boothsListAjaxRoot">
        @include('booths.partials.list-results')
    </div>

    {{-- Booth list settings: centered modal (Looker-style) --}}
    <div class="modal fade booths-list-settings-modal text-body" id="boothsListSettingsModal" tabindex="-1" aria-labelledby="boothsListSettingsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl booths-settings-modal-dialog">
            <div class="modal-content booths-settings-modal__shell">
                <div class="modal-header booths-settings-modal__header border-0">
                    <div class="d-flex align-items-start gap-3 flex-grow-1 min-w-0">
                        <div class="booths-settings-modal__icon-wrap" aria-hidden="true">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div class="min-w-0">
                            <h5 class="modal-title mb-1" id="boothsListSettingsLabel">Booth list settings</h5>
                            <p class="booths-settings-modal__subtitle mb-0">Cards layout, defaults, and booth-related system options.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close booths-settings-modal__close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body booths-settings-modal__body">
                    <div class="booths-settings-block">
                        <div class="booths-settings-block__head">
                            <span class="booths-settings-block__icon booths-settings-block__icon--blue"><i class="fas fa-th-large" aria-hidden="true"></i></span>
                            <div>
                                <h6 class="booths-settings-block__title">Card layout</h6>
                                <p class="booths-settings-block__desc">Used when <strong>Cards</strong> is selected. Stored in this browser only.</p>
                            </div>
                        </div>
                        <div class="books-view-toggle booths-density-toggle booths-density-toggle--settings w-100 flex-wrap" role="group" aria-label="Card size">
                            <button type="button" class="plastic-btn-press" onclick="setBoothsCardDensity('tiny')" id="boothsDensityTiny" title="Tiny list — compact rows">
                                <i class="fas fa-list me-1" aria-hidden="true"></i>Tiny
                            </button>
                            <button type="button" class="plastic-btn-press" onclick="setBoothsCardDensity('small')" id="boothsDensitySmall">Small</button>
                            <button type="button" class="active plastic-btn-press" onclick="setBoothsCardDensity('medium')" id="boothsDensityMedium">Medium</button>
                            <button type="button" class="plastic-btn-press" onclick="setBoothsCardDensity('large')" id="boothsDensityLarge">Large</button>
                        </div>
                    </div>

                    @if(auth()->user()->isAdmin())
                    <div class="booths-settings-block" role="region" aria-label="Default booth card image">
                        <div class="booths-settings-block__head">
                            <span class="booths-settings-block__icon booths-settings-block__icon--purple"><i class="fas fa-image" aria-hidden="true"></i></span>
                            <div>
                                <h6 class="booths-settings-block__title" id="boothsSettingsMasterHeading">Default card image</h6>
                                <p class="booths-settings-block__desc">Fallback when a booth has no photo. Per-booth images override this.</p>
                            </div>
                        </div>
                        <div class="row g-3 align-items-start">
                            <div class="col-auto">
                                <div class="booths-settings-master-frame">
                                    <img id="boothsPageMasterImagePreview" src="{{ $masterBoothImageUrl ?? '' }}" alt="" class="booths-settings-master-frame__img {{ $masterBoothImageUrl ? '' : 'd-none' }}" width="120" height="120">
                                    <div id="boothsPageMasterImagePlaceholder" class="booths-settings-master-frame__empty {{ $masterBoothImageUrl ? 'd-none' : '' }}">
                                        <i class="fas fa-image"></i>
                                        <span>No image</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col min-w-0">
                                <label class="form-label small fw-semibold" for="boothsPageMasterImageFile">Upload file</label>
                                <input type="file" class="form-control form-control-sm" id="boothsPageMasterImageFile" accept="image/jpeg,image/png,image/gif" aria-label="Choose default card image file">
                                <small class="text-muted d-block mt-2">{{ $masterBoothUploadHint ?? '' }}</small>
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    <button type="button" class="btn btn-primary btn-sm px-3" id="boothsPageMasterImageSave" disabled>
                                        <i class="fas fa-save me-1"></i>Save
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm px-3" id="boothsPageMasterImageRemove" {{ ($masterBoothImageUrl ?? null) ? '' : 'disabled' }}>
                                        <i class="fas fa-times me-1"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="booths-settings-block" role="region" aria-labelledby="boothsSettingsMasterGalleryHeading">
                        <div class="booths-settings-block__head">
                            <span class="booths-settings-block__icon booths-settings-block__icon--purple"><i class="fas fa-images" aria-hidden="true"></i></span>
                            <div>
                                <h6 class="booths-settings-block__title" id="boothsSettingsMasterGalleryHeading">Master gallery</h6>
                                <p class="booths-settings-block__desc">Default photos when a booth has no gallery yet (detail page gallery and card image fallback after the single default image above).</p>
                            </div>
                        </div>
                        <div id="boothsPageMasterGalleryList" class="booths-settings-master-gallery-grid row g-2 mb-3" aria-live="polite"></div>
                        <div class="d-flex flex-wrap align-items-end gap-2">
                            <div class="flex-grow-1 min-w-0" style="min-width: 200px;">
                                <label class="form-label small fw-semibold" for="boothsPageMasterGalleryFile">Add image</label>
                                <input type="file" class="form-control form-control-sm" id="boothsPageMasterGalleryFile" accept="image/jpeg,image/png,image/gif" aria-label="Add master gallery image">
                            </div>
                            <button type="button" class="btn btn-primary btn-sm px-3" id="boothsPageMasterGallerySave" disabled>
                                <i class="fas fa-plus me-1"></i>Add
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2">Up to 20 images. {{ $masterBoothUploadHint ?? '' }}</small>
                    </div>

                    <div class="booths-settings-block" aria-labelledby="boothsSettingsUploadHeading">
                        <div class="booths-settings-block__head">
                            <span class="booths-settings-block__icon booths-settings-block__icon--orange"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i></span>
                            <div>
                                <h6 class="booths-settings-block__title" id="boothsSettingsUploadHeading">Booth photo uploads</h6>
                                <p class="booths-settings-block__desc">Limits for booth gallery and legacy booth image fields.</p>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="form-label small fw-semibold mb-1" for="boothsUploadContextMaxMb">Max size (MB)</label>
                                <input type="number" class="form-control" id="boothsUploadContextMaxMb" name="uploads_booth_max_size_mb" value="{{ old('uploads_booth_max_size_mb', $boothsUploadContext['max_mb'] ?? '') }}" min="0" max="100" step="0.5" placeholder="—">
                            </div>
                            <div class="col-sm-8">
                                <label class="form-label small fw-semibold mb-1" for="boothsUploadContextExts">Allowed extensions</label>
                                <input type="text" class="form-control" id="boothsUploadContextExts" name="uploads_booth_allowed_extensions" value="{{ old('uploads_booth_allowed_extensions', $boothsUploadContext['extensions'] ?? '') }}" placeholder="e.g. jpg, png, gif" autocomplete="off">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm mt-3 px-3" id="boothsPageUploadLimitsSave">
                            <i class="fas fa-save me-1"></i>Save upload limits
                        </button>
                    </div>

                    <div class="booths-settings-block" aria-labelledby="boothsSettingsModuleHeading">
                        <div class="booths-settings-block__head">
                            <span class="booths-settings-block__icon booths-settings-block__icon--green"><i class="fas fa-bars" aria-hidden="true"></i></span>
                            <div>
                                <h6 class="booths-settings-block__title" id="boothsSettingsModuleHeading">Booths in sidebar menu</h6>
                                <p class="booths-settings-block__desc">Visibility on smaller screens; desktop navigation is unchanged.</p>
                            </div>
                        </div>
                        <div class="booths-settings-switches">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="boothsModuleNavMobile" value="1" {{ ($boothsModuleNav['mobile'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="boothsModuleNavMobile">Show on mobile (≤768px)</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="boothsModuleNavTablet" value="1" {{ ($boothsModuleNav['tablet'] ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="boothsModuleNavTablet">Show on tablet (769px–1024px)</label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm mt-3 px-3" id="boothsPageModuleNavSave">
                            <i class="fas fa-save me-1"></i>Save menu visibility
                        </button>
                    </div>

                    <div class="booths-settings-block booths-settings-block--muted" aria-labelledby="boothsSettingsSystemLinkHeading">
                        <div class="booths-settings-block__head">
                            <span class="booths-settings-block__icon booths-settings-block__icon--muted" aria-hidden="true"><i class="fas fa-cog"></i></span>
                            <div>
                                <h6 class="booths-settings-block__title" id="boothsSettingsSystemLinkHeading">System-wide options</h6>
                                <p class="booths-settings-block__desc mb-2">Upload limits (all contexts), public floor plan actions, booked tick appearance, and which modules appear on mobile/tablet are configured in <strong>System Settings</strong>.</p>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <a href="{{ route('settings.index') }}#settings-upload-control" class="btn btn-sm btn-outline-secondary">Upload control</a>
                                    <a href="{{ route('settings.index') }}#settings-public-view" class="btn btn-sm btn-outline-secondary">Public floor plan &amp; tick</a>
                                    <a href="{{ route('settings.index') }}#module-display" class="btn btn-sm btn-outline-secondary">Module display</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer booths-settings-modal__footer border-0">
                    <button type="button" class="btn btn-light border booths-settings-modal__btn-done" data-bs-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/books-table-column-resize.js') }}?v=2"></script>
<script>
(function () {
    'use strict';

    var boothsIndexUrl = @json(route('booths.index'));

    function updateBoothFilterChips(activeKey) {
        document.querySelectorAll('#boothFilterForm [data-booth-filter]').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-booth-filter') === activeKey);
        });
    }

    function updateBoothFilterBadge(count) {
        var badge = document.getElementById('boothsFilterBadge');
        if (!badge) {
            return;
        }
        badge.textContent = count + ' active';
        badge.classList.toggle('d-none', count === 0);
    }

    function boothsListFinishPartial(data, historyUrl) {
        var root = document.getElementById('boothsListAjaxRoot');
        if (data.html && root) {
            root.innerHTML = data.html;
        }
        if (typeof data.activeFilter === 'string') {
            updateBoothFilterChips(data.activeFilter);
        }
        if (typeof data.activeFilterCount === 'number') {
            updateBoothFilterBadge(data.activeFilterCount);
        }
        if (historyUrl) {
            history.replaceState(null, '', historyUrl);
        }
        var savedView = 'table';
        var savedDensity = 'medium';
        try {
            savedView = localStorage.getItem('boothsListView') || 'table';
        } catch (e) {}
        try {
            savedDensity = localStorage.getItem('boothsCardDensity') || 'medium';
        } catch (e) {}
        if (typeof window.setBoothsCardDensity === 'function') {
            window.setBoothsCardDensity(savedDensity);
        }
        if (typeof window.switchBoothsView === 'function') {
            window.switchBoothsView(savedView);
        }
        reinitBoothsTableColumnResize();
    }

    function reinitBoothsTableColumnResize() {
        var table = document.querySelector('#boothsContainer table.books-looker-table');
        if (!table || typeof window.initBooksTableColumnResize !== 'function') {
            return;
        }
        delete table.dataset.booksColumnResizeInit;
        window.initBooksTableColumnResize(table);
    }

    function applyBoothFiltersAjax() {
        var form = document.getElementById('boothFilterForm');
        if (!form) {
            return;
        }
        var params = new URLSearchParams(new FormData(form));
        if (!params.get('status')) {
            params.delete('status');
        }
        params.set('booths_list_partial', '1');
        var url = boothsIndexUrl + '?' + params.toString();
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
            .then(function (data) {
                var cleanParams = new URLSearchParams(new FormData(form));
                if (!cleanParams.get('status')) {
                    cleanParams.delete('status');
                }
                var cleanQs = cleanParams.toString();
                boothsListFinishPartial(data, boothsIndexUrl + (cleanQs ? '?' + cleanQs : ''));
            })
            .catch(function () {
                var fallback = new URLSearchParams(new FormData(form));
                if (!fallback.get('status')) {
                    fallback.delete('status');
                }
                var q = fallback.toString();
                window.location.href = boothsIndexUrl + (q ? '?' + q : '');
            });
    }

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
        var boothFilterForm = document.getElementById('boothFilterForm');
        if (boothFilterForm) {
            boothFilterForm.addEventListener('submit', function (e) {
                e.preventDefault();
                applyBoothFiltersAjax();
            });
        }
        document.querySelectorAll('#boothFilterForm [data-booth-filter]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var h = document.getElementById('boothFilterStatusHidden');
                if (!h) {
                    return;
                }
                var key = btn.getAttribute('data-booth-filter');
                if (key === 'all') {
                    h.value = '';
                } else {
                    h.value = btn.getAttribute('data-booth-status') || '';
                }
                applyBoothFiltersAjax();
            });
        });
        var clearLink = document.getElementById('boothsFilterClearLink');
        if (clearLink) {
            clearLink.addEventListener('click', function (e) {
                e.preventDefault();
                var search = document.getElementById('boothSearch');
                var h = document.getElementById('boothFilterStatusHidden');
                if (search) {
                    search.value = '';
                }
                if (h) {
                    h.value = '';
                }
                applyBoothFiltersAjax();
            });
        }
        var ajaxRoot = document.getElementById('boothsListAjaxRoot');
        if (ajaxRoot) {
            ajaxRoot.addEventListener('click', function (e) {
                var a = e.target.closest('a.page-link');
                if (!a || !a.getAttribute('href') || a.getAttribute('href') === '#') {
                    return;
                }
                if (a.closest('.page-item.disabled')) {
                    return;
                }
                e.preventDefault();
                var targetUrl = a.href;
                if (!targetUrl) {
                    return;
                }
                var u = new URL(targetUrl);
                u.searchParams.set('booths_list_partial', '1');
                fetch(u.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        boothsListFinishPartial(data, targetUrl);
                    })
                    .catch(function () {
                        window.location.href = targetUrl;
                    });
            });
        }

        var searchInput = document.getElementById('boothSearch');
        if (searchInput) {
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applyBoothFiltersAjax();
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
                    img.classList.remove('d-none');
                    ph.classList.add('d-none');
                    if (rm) {
                        rm.disabled = false;
                    }
                } else {
                    img.classList.add('d-none');
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

            function renderMasterGalleryList(items) {
                var wrap = document.getElementById('boothsPageMasterGalleryList');
                if (!wrap) {
                    return;
                }
                if (!items || items.length === 0) {
                    wrap.innerHTML = '<div class="col-12"><p class="text-muted small mb-0">No master gallery images yet.</p></div>';
                    return;
                }
                var html = '';
                items.forEach(function (it) {
                    var pathEsc = String(it.path || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;');
                    html += '<div class="col-6 col-sm-4 col-md-3">';
                    html += '<div class="booths-settings-master-gallery-tile position-relative">';
                    html += '<img src="' + (it.url || '').replace(/"/g, '&quot;') + '" alt="" class="booths-settings-master-gallery-tile__img" loading="lazy" width="120" height="120">';
                    html += '<button type="button" class="btn btn-sm btn-danger booths-settings-master-gallery-tile__rm" data-path="' + pathEsc + '" title="Remove"><i class="fas fa-times" aria-hidden="true"></i></button>';
                    html += '</div></div>';
                });
                wrap.innerHTML = html;
            }

            function loadMasterGalleryList() {
                jQuery.get("{{ route('settings.booth-master-gallery') }}")
                    .done(function (res) {
                        if (res.status === 200 && res.data && res.data.items) {
                            renderMasterGalleryList(res.data.items);
                        }
                    })
                    .fail(function () {
                        var wrap = document.getElementById('boothsPageMasterGalleryList');
                        if (wrap) {
                            wrap.innerHTML = '<div class="col-12"><p class="text-danger small mb-0">Could not load master gallery.</p></div>';
                        }
                    });
            }

            var $mgFile = document.getElementById('boothsPageMasterGalleryFile');
            var $mgSave = document.getElementById('boothsPageMasterGallerySave');
            if ($mgFile && $mgSave) {
                jQuery($mgFile).on('change', function () {
                    jQuery($mgSave).prop('disabled', !this.files || !this.files[0]);
                });
                jQuery($mgSave).on('click', function () {
                    var input = document.getElementById('boothsPageMasterGalleryFile');
                    var file = input && input.files[0];
                    if (!file) {
                        notify('Choose an image file first.', 'warning');
                        return;
                    }
                    var formData = new FormData();
                    formData.append('gallery_image', file);
                    formData.append('_token', '{{ csrf_token() }}');
                    var $btn = jQuery(this);
                    $btn.prop('disabled', true);
                    jQuery.ajax({
                        url: "{{ route('settings.booth-master-gallery.upload') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.status === 200) {
                                notify(response.message || 'Image added', 'success');
                                if (input) {
                                    input.value = '';
                                }
                                $btn.prop('disabled', true);
                                loadMasterGalleryList();
                            }
                        },
                        error: function (xhr) {
                            var msg = (xhr.responseJSON && (xhr.responseJSON.message || (xhr.responseJSON.errors && xhr.responseJSON.errors.gallery_image && xhr.responseJSON.errors.gallery_image[0]))) || 'Failed to add image';
                            notify(msg, 'error');
                            $btn.prop('disabled', false);
                        }
                    });
                });
                jQuery('#boothsPageMasterGalleryList').on('click', '.booths-settings-master-gallery-tile__rm', function () {
                    var path = jQuery(this).attr('data-path');
                    if (!path) {
                        return;
                    }
                    if (!window.confirm('Remove this image from the master gallery?')) {
                        return;
                    }
                    jQuery.ajax({
                        url: "{{ route('settings.booth-master-gallery.remove') }}",
                        method: 'POST',
                        data: { _token: '{{ csrf_token() }}', path: path },
                        success: function (response) {
                            if (response.status === 200) {
                                notify(response.message || 'Removed', 'success');
                                loadMasterGalleryList();
                            }
                        },
                        error: function (xhr) {
                            notify((xhr.responseJSON && xhr.responseJSON.message) || 'Failed to remove', 'error');
                        }
                    });
                });
                loadMasterGalleryList();
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
