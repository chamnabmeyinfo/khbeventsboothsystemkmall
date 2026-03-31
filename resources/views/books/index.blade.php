@extends('layouts.app')

@section('title', 'Bookings Management')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<style>
    /* Bookings index — single responsive layout; breakpoints align with Bootstrap sm/md/lg */
    .books-page .books-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .books-page .action-btn-secondary > i:first-child {
        color: var(--text-tertiary);
    }
    .books-page .books-view-toggle {
        display: inline-flex;
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        padding: 4px;
        gap: 4px;
        box-shadow: var(--shadow-sm);
    }
    .books-page .books-view-toggle button {
        border: none;
        background: transparent;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        color: var(--text-secondary);
        min-height: 44px;
        transition: var(--transition-all, 0.2s ease);
    }
    .books-page .books-view-toggle button:hover {
        color: var(--text-primary);
    }
    .books-page .books-view-toggle button.active {
        background: #fff;
        color: var(--accent-blue);
        box-shadow: var(--shadow-sm);
    }
    .books-page .books-data-panel {
        padding: 0;
        overflow: hidden;
    }
    .books-page .books-data-panel .looker-table-wrapper {
        margin: 0;
        -webkit-overflow-scrolling: touch;
    }
    /* Bookings list table — header bar, zebra rows, action chips */
    .books-page .books-looker-table {
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
        width: 100%;
    }
    .books-page .books-col-resize-handle {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 10px;
        margin-right: -5px;
        cursor: col-resize;
        z-index: 5;
        background: transparent;
        touch-action: none;
        transition: transform 0.1s cubic-bezier(0.4, 0, 0.2, 1), filter 0.08s ease, background 0.1s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .books-page .books-col-resize-handle:hover,
    .books-page .books-col-resize-handle:focus {
        background: linear-gradient(90deg, transparent, rgba(0, 122, 255, 0.22));
    }
    .books-page .books-col-resize-handle:active {
        transform: scaleX(1.12) scaleY(0.9);
        filter: brightness(0.88);
    }
    body.books-table-resizing {
        cursor: col-resize !important;
        user-select: none;
    }
    .books-page .books-looker-table thead th {
        overflow: hidden;
        text-overflow: ellipsis;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.92) 0%, rgba(248, 250, 252, 0.88) 100%) !important;
        backdrop-filter: blur(16px) saturate(160%);
        -webkit-backdrop-filter: blur(16px) saturate(160%);
        border-top: none !important;
        border-bottom: 2px solid rgba(0, 122, 255, 0.14) !important;
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.8) inset;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: var(--text-secondary) !important;
        padding: 0.9rem 1rem !important;
        white-space: nowrap;
    }
    .books-page .books-looker-table thead th:first-child,
    .books-page .books-looker-table thead th:last-child {
        border-radius: 0 !important;
        border-left: none !important;
        border-right: none !important;
    }
    .books-page .books-looker-table tbody tr.books-table-row {
        cursor: pointer;
        transition: background-color 0.15s ease, box-shadow 0.15s ease;
    }
    .books-page .books-looker-table tbody tr.books-table-row:nth-child(even) td {
        background-color: rgba(255, 255, 255, 0.35);
    }
    .books-page .books-looker-table tbody tr.books-table-row:nth-child(odd) td {
        background-color: rgba(255, 255, 255, 0.2);
    }
    .books-page .books-looker-table tbody tr.books-table-row:hover td {
        background-color: rgba(0, 122, 255, 0.07) !important;
        box-shadow: inset 3px 0 0 var(--accent-blue);
    }
    .books-page .books-looker-table tbody td {
        border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
        vertical-align: middle;
        padding: 0.85rem 1rem !important;
        font-size: 0.9rem;
    }
    .books-page .books-looker-table tbody tr:last-child td {
        border-bottom: none !important;
    }
    .books-page .books-looker-table .books-col-rownum {
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        color: var(--text-tertiary);
        width: 3rem;
        text-align: center;
    }
    .books-page .books-looker-table .books-col-id {
        font-variant-numeric: tabular-nums;
        font-weight: 700;
        color: var(--text-secondary);
        width: 4.5rem;
    }
    .books-page .books-looker-table .books-col-actions {
        width: 1%;
        white-space: nowrap;
    }
    .books-page .books-amount-cell {
        color: var(--accent-green);
        font-variant-numeric: tabular-nums;
        font-weight: 700;
    }
    .books-page .books-type-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.28rem 0.65rem;
        border-radius: var(--radius-pill);
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .books-page .books-type-regular {
        background: var(--accent-blue-light);
        color: var(--accent-blue);
    }
    .books-page .books-type-special {
        background: var(--accent-orange-light);
        color: var(--accent-orange);
    }
    .books-page .books-type-temporary {
        background: rgba(175, 82, 222, 0.18);
        color: var(--accent-purple);
    }
    .books-page .books-status-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.7rem;
        border-radius: var(--radius-pill);
        font-size: 0.75rem;
        font-weight: 700;
        line-height: 1.2;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .books-page .books-table-actions {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }
    .books-page .books-table-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        min-width: 38px;
        min-height: 38px;
        padding: 0;
        border: 1px solid var(--border-light);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.85);
        color: var(--accent-blue);
        box-shadow: var(--shadow-sm);
        transition: transform 0.12s ease, box-shadow 0.12s ease, background 0.12s ease;
    }
    .books-page .books-table-btn:hover {
        background: #fff;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
        color: var(--accent-blue);
    }
    .books-page .books-table-btn-danger {
        color: #dc2626;
        border-color: rgba(220, 38, 38, 0.35);
    }
    .books-page .books-table-btn-danger:hover {
        background: rgba(220, 38, 38, 0.08);
        color: #b91c1c;
    }
    .books-page .books-card-view-inner {
        padding: 1rem 1.25rem 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 320px), 1fr));
        gap: 1rem 1.25rem;
        align-items: stretch;
    }
    .books-page .books-card-view-empty {
        grid-column: 1 / -1;
        max-width: 32rem;
        margin-left: auto;
        margin-right: auto;
    }
    /* Bookings — card view: higher contrast, type accent, depth */
    .books-page .books-booking-card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-height: 100%;
        padding: 1.1rem 1.2rem 1.15rem 1.35rem;
        border-radius: var(--radius-md);
        overflow: hidden;
        background:
            linear-gradient(165deg, rgba(255, 255, 255, 0.97) 0%, rgba(255, 255, 255, 0.88) 45%, rgba(248, 250, 252, 0.92) 100%);
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow:
            0 2px 4px rgba(0, 0, 0, 0.04),
            0 12px 28px rgba(31, 38, 135, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px) saturate(165%);
        -webkit-backdrop-filter: blur(20px) saturate(165%);
        transition:
            transform 0.18s cubic-bezier(0.4, 0, 0.2, 1),
            box-shadow 0.18s ease,
            border-color 0.18s ease,
            filter 0.1s ease;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        user-select: none;
        touch-action: manipulation;
    }
    .books-page .books-booking-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        border-radius: var(--radius-md) 0 0 var(--radius-md);
        background: var(--accent-blue);
        box-shadow: 2px 0 12px rgba(0, 122, 255, 0.35);
    }
    .books-page .books-booking-card--type-1::before {
        background: linear-gradient(180deg, #0a84ff 0%, var(--accent-blue) 50%, #0066cc 100%);
    }
    .books-page .books-booking-card--type-2::before {
        background: linear-gradient(180deg, #ffb340 0%, var(--accent-orange) 55%, #e08600 100%);
        box-shadow: 2px 0 12px rgba(255, 149, 0, 0.4);
    }
    .books-page .books-booking-card--type-3::before {
        background: linear-gradient(180deg, #c77dff 0%, var(--accent-purple) 55%, #8e44ad 100%);
        box-shadow: 2px 0 12px rgba(175, 82, 222, 0.4);
    }
    .books-page .books-booking-card::after {
        content: '';
        position: absolute;
        right: -20%;
        top: -30%;
        width: 70%;
        height: 55%;
        border-radius: 50%;
        background: radial-gradient(ellipse at center, rgba(0, 122, 255, 0.09) 0%, transparent 68%);
        pointer-events: none;
    }
    .books-page .books-booking-card--type-2::after {
        background: radial-gradient(ellipse at center, rgba(255, 149, 0, 0.11) 0%, transparent 68%);
    }
    .books-page .books-booking-card--type-3::after {
        background: radial-gradient(ellipse at center, rgba(175, 82, 222, 0.11) 0%, transparent 68%);
    }
    .books-page .books-booking-card:hover {
        transform: translateY(-3px);
        border-color: rgba(0, 122, 255, 0.28);
        box-shadow:
            0 4px 8px rgba(0, 0, 0, 0.06),
            0 20px 40px rgba(31, 38, 135, 0.16),
            0 0 0 1px rgba(255, 255, 255, 0.4) inset,
            0 0 0 3px rgba(0, 122, 255, 0.12);
    }
    .books-page .books-booking-card--type-2:hover {
        box-shadow:
            0 4px 8px rgba(0, 0, 0, 0.06),
            0 20px 40px rgba(255, 149, 0, 0.15),
            0 0 0 1px rgba(255, 255, 255, 0.4) inset,
            0 0 0 3px rgba(255, 149, 0, 0.15);
    }
    .books-page .books-booking-card--type-3:hover {
        box-shadow:
            0 4px 8px rgba(0, 0, 0, 0.06),
            0 20px 40px rgba(175, 82, 222, 0.15),
            0 0 0 1px rgba(255, 255, 255, 0.4) inset,
            0 0 0 3px rgba(175, 82, 222, 0.14);
    }
    /* Cards view — plastic press on pointer down (match table row + buttons) */
    .books-page .books-booking-card:active,
    .books-page .books-booking-card:hover:active {
        transform: translateY(2px) scale(0.988);
        filter: brightness(0.94);
        transition-duration: 0.06s;
        box-shadow:
            0 1px 3px rgba(0, 0, 0, 0.07),
            0 6px 16px rgba(31, 38, 135, 0.12),
            inset 0 3px 14px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.45);
        border-color: rgba(0, 122, 255, 0.2);
    }
    .books-page .books-booking-card--type-2:active,
    .books-page .books-booking-card--type-2:hover:active {
        box-shadow:
            0 1px 3px rgba(0, 0, 0, 0.07),
            0 6px 16px rgba(255, 149, 0, 0.12),
            inset 0 3px 14px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.45);
        border-color: rgba(255, 149, 0, 0.28);
    }
    .books-page .books-booking-card--type-3:active,
    .books-page .books-booking-card--type-3:hover:active {
        box-shadow:
            0 1px 3px rgba(0, 0, 0, 0.07),
            0 6px 16px rgba(175, 82, 222, 0.14),
            inset 0 3px 14px rgba(0, 0, 0, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.45);
        border-color: rgba(175, 82, 222, 0.26);
    }
    @media (prefers-reduced-motion: reduce) {
        .books-page .books-booking-card:active {
            transform: translateY(1px) scale(0.995);
        }
    }
    .books-page .books-booking-card__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
        margin: -0.25rem -0.25rem 1rem -0.1rem;
        padding: 0.5rem 0.5rem 0.85rem 0.25rem;
        border-radius: 12px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.65) 0%, rgba(248, 250, 252, 0.45) 100%);
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        position: relative;
        z-index: 1;
    }
    .books-page .books-booking-card__head-text {
        min-width: 0;
        flex: 1;
    }
    .books-page .books-booking-card__client,
    .books-page .books-booking-card__field,
    .books-page .books-booking-card__grid,
    .books-page .books-booking-card__footer,
    .books-page .books-booking-card__by {
        position: relative;
        z-index: 1;
    }
    .books-page .books-booking-card__row {
        display: inline-block;
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text-secondary);
        margin-bottom: 0.35rem;
    }
    .books-page .books-booking-card__title {
        font-size: 1.1rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: #0d0d0f;
        margin: 0 0 0.5rem;
        line-height: 1.25;
    }
    .books-page .books-booking-card__badges {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.4rem;
    }
    .books-page .books-booking-card .books-type-badge {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    .books-page .books-booking-card .books-status-pill {
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        font-weight: 800;
    }
    .books-page .books-booking-card__actions .books-table-actions {
        flex-shrink: 0;
    }
    .books-page .books-booking-card__client {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .books-page .books-booking-card__avatar {
        flex-shrink: 0;
    }
    .books-page .books-booking-card__client-name {
        font-weight: 700;
        font-size: 1rem;
        color: var(--text-primary);
        line-height: 1.3;
    }
    .books-page .books-booking-card__client-meta {
        font-size: 0.8125rem;
        color: var(--text-tertiary);
        margin-top: 0.2rem;
    }
    .books-page .books-booking-card__field {
        margin-bottom: 0.75rem;
    }
    .books-page .books-booking-card__grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem 1rem;
        margin-bottom: 1rem;
        padding: 0.75rem 0.85rem;
        border-radius: 12px;
        background: linear-gradient(180deg, rgba(0, 82, 180, 0.06) 0%, rgba(0, 0, 0, 0.03) 100%);
        border: 1px solid rgba(0, 0, 0, 0.07);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }
    .books-page .books-booking-card__label {
        display: block;
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }
    .books-page .books-booking-card__value {
        font-weight: 700;
        font-size: 0.925rem;
        color: #0d0d0f;
    }
    .books-page .books-booking-card__sub {
        font-size: 0.78rem;
        color: var(--text-secondary);
        margin-top: 0.15rem;
    }
    .books-page .books-booking-card__footer {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 0.75rem;
        margin-top: auto;
        padding-top: 0.95rem;
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }
    .books-page .books-booking-card__amount {
        font-size: 1.12rem;
        filter: contrast(1.05);
    }
    .books-page .books-booking-card__balance {
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        color: var(--accent-orange);
    }
    .books-page .books-booking-card__by {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.35rem;
        margin-top: 0.85rem;
        padding-top: 0.85rem;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }
    .books-page .books-booking-card__by-name {
        font-size: 0.8125rem;
        color: var(--text-secondary);
    }
    @media (max-width: 575.98px) {
        .books-page .books-card-view-inner {
            grid-template-columns: 1fr;
        }
    }
    .books-page .group-section .group-header {
        margin-bottom: 0.75rem;
    }
    .books-page .group-section .group-header h5 {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }
    .books-page .empty-state {
        text-align: center;
        padding: 2.5rem 1rem;
    }
    .books-page .empty-state-icon {
        font-size: 3rem;
        color: var(--text-tertiary);
        margin-bottom: 1rem;
    }
    /* Modal is rendered outside .books-page wrapper; keep class name page-specific */
    .booking-info-modal-header {
        background: linear-gradient(135deg, var(--accent-blue) 0%, var(--accent-purple) 100%);
        color: #fff;
    }
    .books-page .filter-actions .books-filter-apply,
    .books-page .filter-actions .books-filter-clear {
        padding: 0.45rem 0.95rem;
        font-size: 0.8125rem;
        min-height: 40px;
    }
    @media (max-width: 767.98px) {
        .books-page .looker-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .books-page .looker-actions {
            width: 100%;
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@push('body-class', 'ios-dashboard-mode')


@section('content')
<div class="looker-dashboard books-page">
    @if(!empty($restrictToOwnBookings))
        <div class="alert alert-info mb-3" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>You are viewing only your own bookings.</strong> You can create, edit, update, and delete only the bookings you created. You cannot view or manage other users&#39; bookings. This is controlled in <a href="{{ route('settings.index') }}">Settings &rarr; Public View Actions</a>.
        </div>
    @endif

    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Bookings</h1>
            <p>Create, filter, and manage booth bookings from one place.</p>
        </div>
        <div class="looker-actions">
            <a href="{{ route('books.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus"></i> New booking
            </a>
            <a href="{{ route('export.bookings') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <button type="button" class="action-btn action-btn-secondary" onclick="refreshPage()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            @if(auth()->user()->isAdmin())
            <button type="button" class="action-btn action-btn-secondary" style="border-color: rgba(229, 62, 62, 0.35); color: #c53030;" onclick="showDeleteAllModal()">
                <i class="fas fa-trash-alt"></i> Delete all
            </button>
            @endif
        </div>
    </header>

    @php
        try {
            $totalBoothsKpi = \App\Models\Book::get()->sum(function ($book) {
                $boothIds = json_decode($book->boothid, true);
                return is_array($boothIds) ? count($boothIds) : 0;
            });
        } catch (\Exception $e) {
            $totalBoothsKpi = 0;
        }
    @endphp
    <div class="kpi-wrapper">
        <div class="kpi-card-looker">
            <div class="kpi-top">
                <div class="kpi-title">Total bookings</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-calendar-check"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format(\App\Models\Book::count()) }}</div>
            <div class="kpi-bottom trend-neutral"><i class="fas fa-layer-group fa-fw"></i> All time</div>
        </div>
        <div class="kpi-card-looker success">
            <div class="kpi-top">
                <div class="kpi-title">Today</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-calendar-day"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format(\App\Models\Book::whereDate('date_book', today())->count()) }}</div>
            <div class="kpi-bottom trend-positive"><i class="fas fa-sun fa-fw"></i> Scheduled for today</div>
        </div>
        <div class="kpi-card-looker warning">
            <div class="kpi-top">
                <div class="kpi-title">This month</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-calendar-alt"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format(\App\Models\Book::whereMonth('date_book', now()->month)->whereYear('date_book', now()->year)->count()) }}</div>
            <div class="kpi-bottom trend-warning"><i class="fas fa-calendar-week fa-fw"></i> {{ now()->format('F Y') }}</div>
        </div>
        <div class="kpi-card-looker purple">
            <div class="kpi-top">
                <div class="kpi-title">Booth slots (booked)</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-cube"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($totalBoothsKpi) }}</div>
            <div class="kpi-bottom trend-neutral"><i class="fas fa-th fa-fw"></i> Sum across bookings</div>
        </div>
    </div>

    <div class="books-toolbar">
        <div class="books-view-toggle" role="group" aria-label="List layout">
            <button type="button" class="active plastic-btn-press" onclick="switchView('table')" id="viewTable">
                <i class="fas fa-table me-1"></i>Table
            </button>
            <button type="button" class="plastic-btn-press" onclick="switchView('cards')" id="viewCards">
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
                    <span class="filter-badge {{ $activeFilterCount > 0 ? '' : 'd-none' }}" id="booksFilterBadge">{{ $activeFilterCount }} active</span>
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
                    <select name="group_by" class="form-control form-control-modern form-control-sm books-filter-ajax-trigger">
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
                <button type="submit" class="action-btn action-btn-primary books-filter-apply">
                    <i class="fas fa-filter me-1"></i>Apply
                </button>
                <a href="{{ route('books.index') }}" class="action-btn action-btn-secondary books-filter-clear" id="booksFilterClearLink">
                    <i class="fas fa-times me-1"></i>Clear all
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
        @include('books.partials.index-bookings-container', ['books' => $books, 'groupBy' => $groupBy, 'groupedBooks' => $groupedBooks, 'boothsByBookId' => $boothsByBookId])
    </div>
</div>

<!-- Booking Info Modal -->
<div class="modal fade" id="bookingInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header booking-info-modal-header">
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
@php
    $lazyLoadMoreAvailable = ($groupBy === 'none' && isset($total, $books) && $total > $books->count());
@endphp
<script src="{{ asset('js/books-table-column-resize.js') }}?v=1"></script>
<script>
(function() {
    'use strict';
    
    let currentPage = 1;
    let isLoading = false;
    let hasMore = @json($lazyLoadMoreAvailable);
    let currentView = 'table';
    
    // Switch View
    window.switchView = function(view) {
        currentView = view;
        document.querySelectorAll('.books-view-toggle button').forEach(btn => btn.classList.remove('active'));
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
    
    // Lazy loading — GET must use query string (request body is ignored for GET in fetch)
    const lazyLoadIndexUrl = @json(route('books.index'));
    let lazyLoadGroupBy = @json($groupBy);
    let lazyObserver = null;

    function updateFilterBadge(count) {
        const badge = document.getElementById('booksFilterBadge');
        if (!badge) return;
        if (count > 0) {
            badge.classList.remove('d-none');
            badge.textContent = count + ' active';
        } else {
            badge.classList.add('d-none');
        }
    }

    function setupLazyLoadObserver() {
        if (lazyObserver) {
            lazyObserver.disconnect();
            lazyObserver = null;
        }
        const trigger = document.getElementById('lazyLoadTrigger');
        const bookingsTableBody = document.getElementById('bookingsTableBody');
        if (!trigger || !bookingsTableBody || !hasMore) {
            return;
        }
        lazyObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && hasMore && !isLoading) {
                    loadMoreBookings();
                }
            });
        }, { threshold: 0.1 });
        lazyObserver.observe(trigger);
    }

    function reinitBooksTableResize() {
        if (typeof window.initBooksTableColumnResize !== 'function') return;
        document.querySelectorAll('table.books-looker-table').forEach(function(table) {
            delete table.dataset.booksColumnResizeInit;
            window.initBooksTableColumnResize(table);
        });
    }

    function applyFiltersAjax() {
        const form = document.getElementById('filterForm');
        if (!form) return;
        const params = new URLSearchParams(new FormData(form));
        params.set('books_list_partial', '1');
        const url = lazyLoadIndexUrl + '?' + params.toString();
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(function(response) {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(function(data) {
            const container = document.getElementById('bookingsContainer');
            if (container && data.html) {
                container.innerHTML = data.html;
            }
            if (typeof data.groupBy === 'string') {
                lazyLoadGroupBy = data.groupBy;
            }
            hasMore = !!data.hasMore;
            currentPage = 1;
            if (typeof data.activeFilterCount === 'number') {
                updateFilterBadge(data.activeFilterCount);
            }
            const cleanParams = new URLSearchParams(new FormData(form));
            const cleanQs = cleanParams.toString();
            history.replaceState(null, '', lazyLoadIndexUrl + (cleanQs ? '?' + cleanQs : ''));
            switchView(currentView);
            setupLazyLoadObserver();
            reinitBooksTableResize();
        })
        .catch(function() {
            const fallback = new URLSearchParams(new FormData(form)).toString();
            window.location.href = lazyLoadIndexUrl + (fallback ? '?' + fallback : '');
        });
    }

    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            applyFiltersAjax();
        });
    }

    const groupBySelect = document.querySelector('select[name="group_by"]');
    if (groupBySelect) {
        groupBySelect.addEventListener('change', function() {
            applyFiltersAjax();
        });
    }

    const clearLink = document.getElementById('booksFilterClearLink');
    if (clearLink) {
        clearLink.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.getElementById('filterForm');
            if (!form) return;
            form.querySelectorAll('input, select').forEach(function(el) {
                if (el.name === 'group_by') {
                    el.value = 'none';
                } else if (el.name === 'date_range') {
                    el.value = 'all';
                } else if (el.type === 'text' || el.type === 'date' || el.type === 'number' || el.type === 'search') {
                    el.value = '';
                } else if (el.tagName === 'SELECT' && el.name !== 'group_by') {
                    el.selectedIndex = 0;
                }
            });
            applyFiltersAjax();
        });
    }

    setupLazyLoadObserver();
    
    function loadMoreBookings() {
        if (isLoading || !hasMore) return;
        if (!document.getElementById('bookingsTableBody')) return;
        
        isLoading = true;
        currentPage++;
        
        const spinner = document.getElementById('lazyLoadSpinner');
        if (spinner) spinner.classList.add('active');
        
        const form = document.getElementById('filterForm');
        const params = new URLSearchParams(form ? new FormData(form) : undefined);
        params.set('page', String(currentPage));
        params.set('view', currentView);
        params.set('group_by', lazyLoadGroupBy);
        
        const url = lazyLoadIndexUrl + (params.toString() ? '?' + params.toString() : '');
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(function(data) {
            if (data.html) {
                if (currentView === 'table') {
                    const tbody = document.getElementById('bookingsTableBody');
                    if (tbody) {
                        tbody.insertAdjacentHTML('beforeend', data.html);
                    }
                } else {
                    const cardView = document.querySelector('#bookingsContainer > .canvas-panel .card-view .books-card-view-inner');
                    if (cardView) {
                        cardView.insertAdjacentHTML('beforeend', data.html);
                    }
                }
            }
            
            hasMore = !!data.hasMore;
            if (!hasMore) {
                const end = document.getElementById('lazyLoadEnd');
                if (end) end.style.display = 'block';
            }
        })
        .catch(function(error) {
            console.error('Error loading bookings:', error);
            currentPage--;
        })
        .finally(function() {
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
        applyFiltersAjax();
    };

    // Instant Search (debounced)
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                applyFiltersAjax();
            }, 500);
        });
    }
})();
</script>
@endpush
@endsection
