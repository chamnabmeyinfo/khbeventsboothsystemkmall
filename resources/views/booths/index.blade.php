@extends('layouts.app')

@section('title', 'Booths — KHB Events')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<link rel="stylesheet" href="{{ asset('css/books-page-index.css') }}?v=1.0">
<link rel="stylesheet" href="{{ asset('css/booths-on-books.css') }}?v=2.1">
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
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
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

                    <div class="booths-settings-block booths-settings-block--global-embed" aria-labelledby="boothsSettingsGlobalHeading">
                        <div class="booths-settings-block__head">
                            <span class="booths-settings-block__icon booths-settings-block__icon--muted" aria-hidden="true"><i class="fas fa-globe"></i></span>
                            <div>
                                <h6 class="booths-settings-block__title" id="boothsSettingsGlobalHeading">Global settings (same as System Settings)</h6>
                                <p class="booths-settings-block__desc">Upload limits, public floor plan actions, booked tick, and which modules appear on mobile/tablet.</p>
                            </div>
                        </div>
                        <div class="accordion booths-settings-global-accordion" id="boothsSettingsGlobalAccordion">
                            <div class="accordion-item border rounded overflow-hidden mb-2">
                                <h2 class="accordion-header" id="boothsSettingsAccUploadHdr">
                                    <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#boothsSettingsAccUpload" aria-expanded="false" aria-controls="boothsSettingsAccUpload">
                                        <i class="fas fa-cloud-upload-alt me-2 text-primary"></i>Upload control
                                    </button>
                                </h2>
                                <div id="boothsSettingsAccUpload" class="accordion-collapse collapse" aria-labelledby="boothsSettingsAccUploadHdr" data-bs-parent="#boothsSettingsGlobalAccordion">
                                    <div class="accordion-body pt-0">
                                        <p class="text-muted small">Control file uploads across the system (global defaults and per-context limits).</p>
                                        @include('settings.partials.upload-control-form', [
                                            'formId' => 'boothsEmbedUploadControlForm',
                                            'idPrefix' => 'boothsEmbed_',
                                            'uploadSettings' => $uploadSettings,
                                            'extraFormClass' => 'booths-settings-embedded-ajax',
                                        ])
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border rounded overflow-hidden mb-2">
                                <h2 class="accordion-header" id="boothsSettingsAccPublicHdr">
                                    <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#boothsSettingsAccPublic" aria-expanded="false" aria-controls="boothsSettingsAccPublic">
                                        <i class="fas fa-eye me-2 text-info"></i>Public floor plan &amp; booked tick
                                    </button>
                                </h2>
                                <div id="boothsSettingsAccPublic" class="accordion-collapse collapse" aria-labelledby="boothsSettingsAccPublicHdr" data-bs-parent="#boothsSettingsGlobalAccordion">
                                    <div class="accordion-body pt-0">
                                        <p class="text-muted small">Logged-in actions on the public floor plan and appearance of the booked tick.</p>
                                        @include('settings.partials.public-view-form', [
                                            'formId' => 'boothsEmbedPublicViewForm',
                                            'idPrefix' => 'boothsEmbed_',
                                            'extraFormClass' => 'booths-settings-embedded-ajax',
                                        ])
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border rounded overflow-hidden mb-0">
                                <h2 class="accordion-header" id="boothsSettingsAccModuleHdr">
                                    <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#boothsSettingsAccModule" aria-expanded="false" aria-controls="boothsSettingsAccModule">
                                        <i class="fas fa-mobile-alt me-2 text-success"></i>Module display (all modules)
                                    </button>
                                </h2>
                                <div id="boothsSettingsAccModule" class="accordion-collapse collapse" aria-labelledby="boothsSettingsAccModuleHdr" data-bs-parent="#boothsSettingsGlobalAccordion">
                                    <div class="accordion-body pt-0">
                                        <p class="text-muted small">Which modules show in the sidebar on mobile and tablet.</p>
                                        @include('settings.partials.module-display-form', [
                                            'moduleDisplayFormId' => 'boothsModuleDisplayForm',
                                            'moduleDisplayContainerId' => 'boothsModuleDisplayContainer',
                                            'useGlobalResetOnclick' => false,
                                        ])
                                    </div>
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

            jQuery(document).on('submit', '#boothsListSettingsModal form.booths-settings-embedded-ajax', function (e) {
                e.preventDefault();
                var $form = jQuery(this);
                var $btn = $form.find('button[type="submit"]');
                $btn.prop('disabled', true);
                jQuery.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    success: function (res) {
                        if (res.status === 200) {
                            notify(res.message || 'Saved.', 'success');
                        } else {
                            notify(res.message || 'Something went wrong.', 'error');
                        }
                    },
                    error: function (xhr) {
                        var r = xhr.responseJSON || {};
                        var msg = r.message || 'Request failed.';
                        if (r.errors) {
                            var parts = [];
                            jQuery.each(r.errors, function (_k, v) {
                                if (jQuery.isArray(v)) {
                                    jQuery.merge(parts, v);
                                } else {
                                    parts.push(v);
                                }
                            });
                            msg += ' ' + parts.join(' ');
                        }
                        notify(msg, 'error');
                    },
                    complete: function () {
                        $btn.prop('disabled', false);
                    }
                });
            });

            (function initBoothsEmbedTickSync() {
                var colorEl = document.getElementById('boothsEmbed_booth_booked_tick_color');
                var hexEl = document.getElementById('boothsEmbed_booth_booked_tick_color_hex');
                if (colorEl && hexEl) {
                    colorEl.addEventListener('input', function () { hexEl.value = this.value; });
                    hexEl.addEventListener('input', function () {
                        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) colorEl.value = this.value;
                    });
                }
                var bgNone = document.getElementById('boothsEmbed_booth_booked_tick_bg_none');
                var bgColor = document.getElementById('boothsEmbed_booth_booked_tick_bg_color');
                var bgHex = document.getElementById('boothsEmbed_booth_booked_tick_bg_color_hex');
                var bgWrap = document.getElementById('boothsEmbed_booth_booked_tick_bg_wrap');
                if (bgColor && bgHex) {
                    bgColor.addEventListener('input', function () { bgHex.value = this.value; });
                    bgHex.addEventListener('input', function () {
                        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) bgColor.value = this.value;
                    });
                }
                if (bgNone && bgWrap && bgColor && bgHex) {
                    function toggleBgInputs() {
                        var off = bgNone.checked;
                        bgWrap.style.opacity = off ? '0.5' : '1';
                        bgWrap.style.pointerEvents = off ? 'none' : '';
                        bgColor.disabled = off;
                        bgHex.disabled = off;
                    }
                    bgNone.addEventListener('change', toggleBgInputs);
                    toggleBgInputs();
                }
                var borderColorEl = document.getElementById('boothsEmbed_booth_booked_tick_border_color');
                var borderHexEl = document.getElementById('boothsEmbed_booth_booked_tick_border_color_hex');
                if (borderColorEl && borderHexEl) {
                    borderColorEl.addEventListener('input', function () { borderHexEl.value = this.value; });
                    borderHexEl.addEventListener('input', function () {
                        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) borderColorEl.value = this.value;
                    });
                }
                var sizeModeEl = document.getElementById('boothsEmbed_booth_booked_tick_size_mode');
                var relativeWrap = document.getElementById('boothsEmbed_booth_booked_tick_relative_wrap');
                var fixedWrap = document.getElementById('boothsEmbed_booth_booked_tick_fixed_wrap');
                if (sizeModeEl && relativeWrap && fixedWrap) {
                    function toggleSizeMode() {
                        var isRelative = sizeModeEl.value === 'relative';
                        relativeWrap.style.display = isRelative ? 'block' : 'none';
                        fixedWrap.style.display = isRelative ? 'none' : 'block';
                    }
                    sizeModeEl.addEventListener('change', toggleSizeMode);
                    toggleSizeMode();
                }
            })();

            function applyTickSettingsToFormEmbed(d) {
                jQuery('#boothsEmbed_booth_booked_show_tick').prop('checked', !!d.show_tick);
                jQuery('#boothsEmbed_booth_booked_tick_color').val(d.color);
                jQuery('#boothsEmbed_booth_booked_tick_color_hex').val(d.color);
                jQuery('#boothsEmbed_booth_booked_tick_size').val(d.size);
                jQuery('#boothsEmbed_booth_booked_tick_shape').val(d.shape);
                jQuery('#boothsEmbed_booth_booked_tick_position').val(d.position);
                jQuery('#boothsEmbed_booth_booked_tick_animation').val(d.animation);
                jQuery('#boothsEmbed_booth_booked_tick_font_size').val(d.font_size);
                jQuery('#boothsEmbed_booth_booked_tick_relative_percent').val(String(d.relative_percent));
                jQuery('#boothsEmbed_booth_booked_tick_border_width').val(String(d.border_width));
                jQuery('#boothsEmbed_booth_booked_tick_border_color').val(d.border_color);
                jQuery('#boothsEmbed_booth_booked_tick_border_color_hex').val(d.border_color);
                var bgEmpty = !d.bg_color || d.bg_color === '';
                jQuery('#boothsEmbed_booth_booked_tick_bg_none').prop('checked', bgEmpty);
                if (!bgEmpty) {
                    jQuery('#boothsEmbed_booth_booked_tick_bg_color').val(d.bg_color);
                    jQuery('#boothsEmbed_booth_booked_tick_bg_color_hex').val(d.bg_color);
                }
                jQuery('#boothsEmbed_booth_booked_tick_size_mode').val(d.size_mode).trigger('change');
                jQuery('#boothsEmbed_booth_booked_tick_bg_none').trigger('change');
            }

            jQuery('#boothsEmbed_tick_floor_plan_id').on('change', function () {
                var id = jQuery(this).val();
                jQuery.get(@json(route('settings.tick-settings')), { floor_plan_id: id || '' })
                    .done(function (res) {
                        if (res.status !== 200 || !res.data) {
                            return;
                        }
                        applyTickSettingsToFormEmbed(res.data);
                    })
                    .fail(function () {
                        notify('Could not load tick settings for this floor plan.', 'error');
                    });
            });

            var boothsModuleDisplayModuleConfig = {
                dashboard: { name: 'Dashboard', icon: 'fa-home', description: 'Main dashboard with statistics and overview' },
                booths: { name: 'Booths', icon: 'fa-store', description: 'Booth management and floor plan designer' },
                bookings: { name: 'Bookings', icon: 'fa-calendar-check', description: 'Booking management and calendar' },
                clients: { name: 'Clients', icon: 'fa-users', description: 'Client management and directory' },
                settings: { name: 'Settings', icon: 'fa-cog', description: 'System settings and configuration' },
                reports: { name: 'Reports', icon: 'fa-chart-bar', description: 'Analytics and reporting tools' },
                finance: { name: 'Finance', icon: 'fa-dollar-sign', description: 'Financial management and transactions' },
                hr: { name: 'HR', icon: 'fa-user-tie', description: 'Human resources management' },
                users: { name: 'Users', icon: 'fa-user-shield', description: 'User management and permissions' },
                categories: { name: 'Categories', icon: 'fa-folder', description: 'Category and classification management' }
            };

            function renderBoothsModuleDisplaySettings(settings) {
                var html = '';
                var cfg = boothsModuleDisplayModuleConfig;
                Object.keys(cfg).forEach(function (moduleKey) {
                    var module = cfg[moduleKey];
                    var moduleSettings = settings[moduleKey] || { mobile: true, tablet: true };
                    html += '<div class="col-md-6 col-lg-4">';
                    html += '<div class="card border h-100"><div class="p-4">';
                    html += '<div class="d-flex align-items-center mb-3"><div class="flex-shrink-0"><div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="fas ' + module.icon + ' fa-lg"></i></div></div>';
                    html += '<div class="flex-grow-1 ms-3"><h6 class="mb-0">' + module.name + '</h6><small class="text-muted">' + module.description + '</small></div></div>';
                    html += '<div class="row g-2"><div class="col-6"><div class="form-check form-switch">';
                    html += '<input class="form-check-input module-toggle" type="checkbox" id="booths_md_' + moduleKey + '_mobile" data-module="' + moduleKey + '" data-device="mobile"';
                    if (moduleSettings.mobile) html += ' checked';
                    html += ' style="width:2.5rem;height:1.25rem;">';
                    html += '<label class="form-check-label" for="booths_md_' + moduleKey + '_mobile"><i class="fas fa-mobile-alt me-1"></i> Mobile</label></div></div>';
                    html += '<div class="col-6"><div class="form-check form-switch">';
                    html += '<input class="form-check-input module-toggle" type="checkbox" id="booths_md_' + moduleKey + '_tablet" data-module="' + moduleKey + '" data-device="tablet"';
                    if (moduleSettings.tablet) html += ' checked';
                    html += ' style="width:2.5rem;height:1.25rem;">';
                    html += '<label class="form-check-label" for="booths_md_' + moduleKey + '_tablet"><i class="fas fa-tablet-alt me-1"></i> Tablet</label></div></div></div>';
                    html += '</div></div></div>';
                });
                jQuery('#boothsModuleDisplayContainer').html(html);
            }

            var boothsModuleDisplayLoaded = false;

            function loadBoothsModuleDisplaySettings() {
                jQuery.get(@json(route('settings.module-display')))
                    .done(function (response) {
                        if (response.status === 200) {
                            renderBoothsModuleDisplaySettings(response.data || {});
                            boothsModuleDisplayLoaded = true;
                        } else {
                            notify('Failed to load module display settings', 'error');
                            jQuery('#boothsModuleDisplayContainer').html('<div class="col-12"><div class="alert alert-danger">Failed to load settings. Try again.</div></div>');
                        }
                    })
                    .fail(function () {
                        notify('Failed to load module display settings', 'error');
                        jQuery('#boothsModuleDisplayContainer').html('<div class="col-12"><div class="alert alert-danger">Failed to load settings. Try again.</div></div>');
                    });
            }

            jQuery('#boothsModuleDisplayForm').on('submit', function (e) {
                e.preventDefault();
                var modules = {};
                jQuery(this).find('.module-toggle').each(function () {
                    var mod = jQuery(this).data('module');
                    var device = jQuery(this).data('device');
                    if (!modules[mod]) {
                        modules[mod] = {};
                    }
                    modules[mod][device] = jQuery(this).is(':checked');
                });
                jQuery.ajax({
                    url: @json(route('settings.module-display.save')),
                    method: 'POST',
                    data: { modules: modules, _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        if (response.status === 200) {
                            notify((response.message || 'Module display settings saved') + ' Refresh or open another page to see menu changes.', 'success');
                        } else {
                            notify(response.message || 'Failed to save', 'error');
                        }
                    },
                    error: function (xhr) {
                        var r = xhr.responseJSON || {};
                        notify(r.message || 'Failed to save module display settings', 'error');
                    }
                });
            });

            jQuery('#boothsModuleDisplayForm_resetBtn').on('click', function () {
                loadBoothsModuleDisplaySettings();
            });

            jQuery('#boothsListSettingsModal').on('shown.bs.modal', function () {
                if (boothsModuleDisplayLoaded) {
                    return;
                }
                var $c = jQuery('#boothsModuleDisplayContainer');
                if ($c.length && $c.find('.spinner-border').length) {
                    loadBoothsModuleDisplaySettings();
                }
            });
        })();
        @endif
    });
})();
</script>
@endpush
