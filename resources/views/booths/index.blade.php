@extends('layouts.admin')

@section('title', 'Booths — KHB Events')
@section('page-title', 'Booths')
@section('breadcrumb', 'Booths')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<style>
/* ================================================
   BOOTHS PAGE — Apple iOS/macOS Premium Theme
   Consistent with dashboard-looker.css tokens.
   ================================================ */

/* ---------- Page-scoped variables ---------- */
.booths-page {
    --ap-bg:            #f5f5f7;
    --ap-surface:       #ffffff;
    --ap-surface-alt:   rgba(255,255,255,0.72);
    --ap-glass:         rgba(255,255,255,0.60);
    --ap-border:        rgba(0,0,0,0.06);
    --ap-border-light:  rgba(0,0,0,0.04);
    --ap-text:          #1d1d1f;
    --ap-text-2:        rgba(29,29,31,0.65);
    --ap-text-3:        rgba(29,29,31,0.40);
    --ap-accent:        #007AFF;
    --ap-accent-muted:  rgba(0,122,255,0.10);
    --ap-accent-ring:   rgba(0,122,255,0.20);
    --ap-green:         #34C759;
    --ap-green-bg:      rgba(52,199,89,0.10);
    --ap-orange:        #FF9500;
    --ap-orange-bg:     rgba(255,149,0,0.10);
    --ap-red:           #FF3B30;
    --ap-red-bg:        rgba(255,59,48,0.10);
    --ap-indigo:        #5856D6;
    --ap-indigo-bg:     rgba(88,86,214,0.10);
    --ap-radius:        14px;
    --ap-radius-sm:     8px;
    --ap-radius-pill:   9999px;
    --ap-shadow-xs:     0 1px 2px rgba(0,0,0,0.04);
    --ap-shadow-sm:     0 2px 8px rgba(0,0,0,0.06);
    --ap-shadow-md:     0 4px 20px rgba(0,0,0,0.07), 0 1px 4px rgba(0,0,0,0.04);
    --ap-shadow-focus:  0 0 0 3px var(--ap-accent-ring);
    --ap-transition:    0.22s cubic-bezier(0.4,0,0.2,1);
    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "SF Pro Display", "Inter", "Segoe UI", sans-serif;
    -webkit-font-smoothing: antialiased;
}

/* ---------- Page wrapper ---------- */
.booths-page .booths-container {
    padding: 0;
    max-width: 100%;
    margin-bottom: 2.5rem;
    animation: bpFadeUp 0.45s cubic-bezier(0.16,1,0.3,1) both;
}

@keyframes bpFadeUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ---------- Page Header ---------- */
.booths-page .bp-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.booths-page .bp-title-wrap { display: flex; flex-direction: column; gap: 0.2rem; }
.booths-page .bp-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--ap-text);
    letter-spacing: -0.025em;
    margin: 0;
    line-height: 1.15;
}
.booths-page .bp-subtitle {
    font-size: 0.875rem;
    color: var(--ap-text-2);
    font-weight: 400;
    margin: 0;
}
.booths-page .bp-header-actions { display: flex; gap: 0.625rem; align-items: center; }

/* Primary CTA */
.booths-page .btn-new-booth {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: var(--ap-accent);
    color: #fff;
    border: none;
    border-radius: var(--ap-radius-sm);
    font-weight: 600;
    font-size: 0.9375rem;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(0,122,255,0.25);
    transition: box-shadow var(--ap-transition), transform var(--ap-transition);
    white-space: nowrap;
}
.booths-page .btn-new-booth:hover {
    color: #fff;
    box-shadow: 0 6px 20px rgba(0,122,255,0.35);
    transform: translateY(-1px) scale(1.01);
}
.booths-page .btn-new-booth:active { transform: scale(0.98); }

/* Secondary outline btn */
.booths-page .btn-outline-soft {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.6rem 1.1rem;
    border: 1px solid var(--ap-border);
    border-radius: var(--ap-radius-sm);
    background: var(--ap-surface);
    color: var(--ap-text-2);
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    box-shadow: var(--ap-shadow-xs);
    transition: all var(--ap-transition);
    white-space: nowrap;
}
.booths-page .btn-outline-soft:hover {
    background: var(--ap-surface-alt);
    color: var(--ap-text);
    box-shadow: var(--ap-shadow-sm);
}

/* ---------- KPI Summary Row ---------- */
.booths-page .kpi-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1.75rem;
}
.booths-page .kpi-tile {
    background: var(--ap-glass);
    backdrop-filter: blur(30px) saturate(180%);
    -webkit-backdrop-filter: blur(30px) saturate(180%);
    border: 1px solid rgba(255,255,255,0.55);
    border-radius: var(--ap-radius);
    padding: 1.25rem 1.5rem;
    box-shadow: var(--ap-shadow-md);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform var(--ap-transition), box-shadow var(--ap-transition);
    position: relative;
    overflow: hidden;
}
.booths-page .kpi-tile::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0) 100%);
    pointer-events: none;
}
.booths-page .kpi-tile:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.10), inset 0 0 0 1px rgba(255,255,255,0.7);
}
.booths-page .kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.booths-page .kpi-icon.blue   { background: var(--ap-accent-muted); color: var(--ap-accent); }
.booths-page .kpi-icon.green  { background: var(--ap-green-bg);   color: var(--ap-green); }
.booths-page .kpi-icon.orange { background: var(--ap-orange-bg);  color: var(--ap-orange); }
.booths-page .kpi-icon.indigo { background: var(--ap-indigo-bg);  color: var(--ap-indigo); }
.booths-page .kpi-body { display: flex; flex-direction: column; min-width: 0; }
.booths-page .kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--ap-text);
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.booths-page .kpi-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ap-text-2);
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-top: 0.2rem;
}

/* ---------- Filter Bar ---------- */
.booths-page .filter-bar-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 0.875rem;
    align-items: center;
    margin-bottom: 1.5rem;
}
.booths-page .bp-search-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
    max-width: 380px;
}
.booths-page .bp-search-icon {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--ap-text-3);
    font-size: 0.8125rem;
    pointer-events: none;
}
.booths-page .bp-search-input {
    width: 100%;
    padding: 0.625rem 1rem 0.625rem 2.5rem;
    border: 1px solid var(--ap-border);
    border-radius: var(--ap-radius-sm);
    font-size: 0.9375rem;
    background: var(--ap-surface);
    color: var(--ap-text);
    box-shadow: var(--ap-shadow-xs);
    transition: border-color var(--ap-transition), box-shadow var(--ap-transition);
}
.booths-page .bp-search-input::placeholder { color: var(--ap-text-3); }
.booths-page .bp-search-input:focus {
    outline: none;
    border-color: var(--ap-accent);
    box-shadow: var(--ap-shadow-focus);
}

/* Filter chips */
.booths-page .chip-group {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    align-items: center;
}
.booths-page .filter-chip {
    padding: 0.45rem 0.9rem;
    border-radius: var(--ap-radius-pill);
    font-size: 0.8125rem;
    font-weight: 500;
    border: 1px solid var(--ap-border);
    background: var(--ap-surface);
    color: var(--ap-text-2);
    text-decoration: none;
    cursor: pointer;
    transition: all var(--ap-transition);
    white-space: nowrap;
    line-height: 1;
}
.booths-page .filter-chip:hover {
    background: rgba(0,0,0,0.03);
    color: var(--ap-text);
}
.booths-page .filter-chip.active {
    background: var(--ap-accent-muted);
    color: var(--ap-accent);
    border-color: rgba(0,122,255,0.25);
    font-weight: 600;
}

/* Right side filter controls */
.booths-page .filter-bar-right {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    margin-left: auto;
}

/* ---------- Section label above table ---------- */
.booths-page .section-eyebrow {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--ap-text-3);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 0.75rem;
}

/* ---------- Table Card (Glassmorphism) ---------- */
.booths-page .table-glass {
    background: var(--ap-glass);
    backdrop-filter: blur(30px) saturate(180%);
    -webkit-backdrop-filter: blur(30px) saturate(180%);
    border: 1px solid rgba(255,255,255,0.6);
    border-radius: var(--ap-radius);
    overflow: hidden;
    box-shadow: var(--ap-shadow-md);
}

/* Table head */
.booths-page .table-glass table { width: 100%; border-collapse: collapse; }
.booths-page .table-glass thead tr {
    background: rgba(255,255,255,0.50);
    backdrop-filter: blur(10px);
}
.booths-page .table-glass thead th {
    padding: 0.875rem 1.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--ap-text-2);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border: none;
    border-bottom: 1px solid rgba(255,255,255,0.6);
    white-space: nowrap;
    text-align: left;
}

/* Table rows */
.booths-page .table-glass tbody tr {
    border-bottom: 1px solid rgba(0,0,0,0.04);
    transition: background var(--ap-transition);
}
.booths-page .table-glass tbody tr:last-child { border-bottom: none; }
.booths-page .table-glass tbody tr:hover { background: rgba(255,255,255,0.65); }
.booths-page .table-glass tbody td {
    padding: 1.1rem 1.25rem;
    vertical-align: middle;
    font-size: 0.9375rem;
    color: var(--ap-text);
}

/* Booth number avatar chip (like dashboard) */
.booths-page .booth-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
}
.booths-page .booth-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    color: var(--ap-accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    flex-shrink: 0;
    box-shadow: inset 0 1px 3px rgba(255,255,255,0.6);
}
.booths-page .booth-chip-text { display: flex; flex-direction: column; }
.booths-page .booth-name {
    font-weight: 700;
    font-size: 0.9375rem;
    color: var(--ap-text);
    white-space: nowrap;
}
.booths-page .booth-type {
    font-size: 0.75rem;
    color: var(--ap-text-2);
    margin-top: 1px;
    font-weight: 400;
}

/* Muted text cells */
.booths-page .cell-muted {
    font-size: 0.9rem;
    color: var(--ap-text-2);
}

/* Price cell */
.booths-page .cell-price {
    font-weight: 600;
    font-size: 0.9375rem;
    color: #248a3D;
}

/* ---------- Badges ---------- */
.booths-page .ap-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.7rem;
    border-radius: var(--ap-radius-pill);
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    white-space: nowrap;
}
.booths-page .ap-badge::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    opacity: 0.75;
    flex-shrink: 0;
}
.booths-page .ap-badge.badge-success  { background: var(--ap-green-bg);  color: #1a7f37; }
.booths-page .ap-badge.badge-warning  { background: var(--ap-orange-bg); color: #b45309; }
.booths-page .ap-badge.badge-info     { background: var(--ap-accent-muted); color: #0056b3; }
.booths-page .ap-badge.badge-secondary{ background: rgba(110,110,115,0.12); color: #6e6e73; }
.booths-page .ap-badge.badge-danger   { background: var(--ap-red-bg);    color: #c0392b; }
.booths-page .ap-badge.badge-primary  { background: var(--ap-accent-muted); color: var(--ap-accent); }

/* ---------- Action Buttons ---------- */
.booths-page .act-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.8rem;
    border-radius: var(--ap-radius-sm);
    font-size: 0.8125rem;
    font-weight: 500;
    cursor: pointer;
    border: 1px solid transparent;
    background: transparent;
    transition: all var(--ap-transition);
    text-decoration: none;
    white-space: nowrap;
}
.booths-page .act-btn-view {
    border-color: rgba(0,122,255,0.3);
    color: var(--ap-accent);
}
.booths-page .act-btn-view:hover {
    background: var(--ap-accent-muted);
    border-color: rgba(0,122,255,0.4);
    color: var(--ap-accent);
}
.booths-page .act-btn-edit {
    border-color: var(--ap-border);
    color: var(--ap-text-2);
}
.booths-page .act-btn-edit:hover {
    background: rgba(0,0,0,0.04);
    color: var(--ap-text);
}
.booths-page .act-btn-more {
    border-color: var(--ap-border);
    color: var(--ap-text-2);
    padding: 0.35rem 0.55rem;
}
.booths-page .act-btn-more:hover {
    background: rgba(0,0,0,0.04);
    color: var(--ap-text);
}

/* ---------- Empty state ---------- */
.booths-page .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--ap-text-2);
}
.booths-page .empty-state i {
    font-size: 2.5rem;
    opacity: 0.3;
    margin-bottom: 1rem;
    display: block;
    color: var(--ap-text);
}
.booths-page .empty-state p {
    margin-bottom: 1.25rem;
    font-size: 0.9375rem;
    color: var(--ap-text-2);
}

/* ---------- Pagination footer ---------- */
.booths-page .table-foot {
    background: rgba(255,255,255,0.55);
    backdrop-filter: blur(10px);
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.booths-page .table-foot small {
    font-size: 0.8125rem;
    color: var(--ap-text-2);
}

/* ---- Compact Apple-style pagination ---------------------------------
   Bootstrap's default pagination embeds hard-coded SVG arrows that
   ignore CSS font-size. We override EVERYTHING here to keep it tight.
   --------------------------------------------------------------------- */
.booths-page .table-foot nav { line-height: 1; }
.booths-page .table-foot .pagination {
    display: flex !important;
    align-items: center !important;
    gap: 3px !important;
    margin: 0 !important;
    flex-wrap: wrap !important;
    list-style: none !important;
    padding: 0 !important;
}

/* Every page link: fixed compact size */
.booths-page .table-foot .page-item .page-link {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 30px !important;
    min-width: 30px !important;
    padding: 0 8px !important;
    font-size: 0.8rem !important;
    line-height: 1 !important;
    border-radius: 7px !important;
    border: 1px solid var(--ap-border) !important;
    background: var(--ap-surface) !important;
    color: var(--ap-text-2) !important;
    text-decoration: none !important;
    transition: background var(--ap-transition), color var(--ap-transition), border-color var(--ap-transition) !important;
    white-space: nowrap !important;
    cursor: pointer !important;
}
.booths-page .table-foot .page-item .page-link:hover {
    background: rgba(0,0,0,0.04) !important;
    color: var(--ap-text) !important;
}
.booths-page .table-foot .page-item.active .page-link {
    background: var(--ap-accent-muted) !important;
    color: var(--ap-accent) !important;
    border-color: rgba(0,122,255,0.25) !important;
    font-weight: 700 !important;
}
.booths-page .table-foot .page-item.disabled .page-link {
    opacity: 0.35 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
}

/* Force all SVGs inside page-link to be 10×10 — kills Bootstrap's big arrows */
.booths-page .table-foot .page-item .page-link svg {
    width: 10px !important;
    height: 10px !important;
    max-width: 10px !important;
    max-height: 10px !important;
    display: block !important;
    flex-shrink: 0 !important;
}
/* Font Awesome icons used in our custom pagination */
.booths-page .table-foot .page-item .page-link i,
.booths-page .table-foot .page-item .page-link .fa {
    font-size: 0.625rem !important;
    line-height: 1 !important;
}

/* ---------- Mobile card list ---------- */
.booths-page .mobile-card-list { padding: 1rem 1.25rem; }
.booths-page .mobile-booth-card {
    background: rgba(255,255,255,0.65);
    border-radius: 12px;
    padding: 1rem 1.25rem;
    margin-bottom: 0.75rem;
    border: 1px solid rgba(255,255,255,0.7);
    box-shadow: var(--ap-shadow-xs);
    transition: box-shadow var(--ap-transition);
}
.booths-page .mobile-booth-card:last-child { margin-bottom: 0; }
.booths-page .mobile-booth-card:hover { box-shadow: var(--ap-shadow-sm); }
.booths-page .mobile-card-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.875rem;
    padding-top: 0.875rem;
    border-top: 1px solid rgba(0,0,0,0.05);
}
.booths-page .mobile-card-actions .act-btn { flex: 1; justify-content: center; }

/* ---------- Dropdown override ---------- */
.booths-page .dropdown-menu {
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.07);
    box-shadow: 0 8px 32px rgba(0,0,0,0.12), inset 0 0 0 1px rgba(255,255,255,0.5);
    padding: 0.4rem;
    min-width: 180px;
}
.booths-page .dropdown-item {
    border-radius: 8px;
    font-size: 0.875rem;
    padding: 0.5rem 0.875rem;
    transition: background var(--ap-transition);
}
.booths-page .dropdown-item:hover { background: rgba(0,0,0,0.04); }

/* ---------- Stagger animation for KPI tiles ---------- */
.booths-page .kpi-tile:nth-child(1) { animation: bpFadeUp 0.45s 0.05s cubic-bezier(0.16,1,0.3,1) both; }
.booths-page .kpi-tile:nth-child(2) { animation: bpFadeUp 0.45s 0.10s cubic-bezier(0.16,1,0.3,1) both; }
.booths-page .kpi-tile:nth-child(3) { animation: bpFadeUp 0.45s 0.15s cubic-bezier(0.16,1,0.3,1) both; }
.booths-page .kpi-tile:nth-child(4) { animation: bpFadeUp 0.45s 0.20s cubic-bezier(0.16,1,0.3,1) both; }

/* ---------- Responsive ---------- */
@media (max-width: 768px) {
    .booths-page .bp-title { font-size: 1.5rem; }
    .booths-page .kpi-row { grid-template-columns: 1fr 1fr; }
    .booths-page .filter-bar-wrap { flex-direction: column; align-items: stretch; }
    .booths-page .bp-search-wrap { max-width: 100%; }
    .booths-page .filter-bar-right { margin-left: 0; }
    .booths-page .bp-header-actions .btn-outline-soft { display: none; }
}
@media (max-width: 480px) {
    .booths-page .kpi-row { grid-template-columns: 1fr; }
}
</style>
@endpush

@push('body-class', 'ios-dashboard-mode booths-page')

@section('content')
@php
    /* ---- Aggregate KPI counts from the paginator ---- */
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
@endphp

<div class="booths-container">

    {{-- ========= PAGE HEADER ========= --}}
    <header class="bp-header">
        <div class="bp-title-wrap">
            <h1 class="bp-title">Booths</h1>
            <p class="bp-subtitle">Manage your booth inventory, availability, and assignments.</p>
        </div>
        <div class="bp-header-actions">
            <a href="{{ route('booths.index', ['view' => 'canvas']) }}" class="btn-outline-soft">
                <i class="fas fa-th-large"></i> Floor Plan
            </a>
            <a href="{{ route('booths.index', ['view' => 'canvas', 'create' => 1]) }}" class="btn-new-booth">
                <i class="fas fa-plus"></i> New Booth
            </a>
        </div>
    </header>

    {{-- ========= KPI SUMMARY ROW ========= --}}
    <div class="kpi-row">
        <div class="kpi-tile">
            <div class="kpi-icon blue"><i class="fas fa-cube"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ $boothsTotal }}</div>
                <div class="kpi-label">Total Booths</div>
            </div>
        </div>
        <div class="kpi-tile">
            <div class="kpi-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ $availableCount }}</div>
                <div class="kpi-label">Available</div>
            </div>
        </div>
        <div class="kpi-tile">
            <div class="kpi-icon orange"><i class="fas fa-clock"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ $reservedCount }}</div>
                <div class="kpi-label">Reserved</div>
            </div>
        </div>
        <div class="kpi-tile">
            <div class="kpi-icon indigo"><i class="fas fa-calendar-check"></i></div>
            <div class="kpi-body">
                <div class="kpi-value">{{ $bookedCount }}</div>
                <div class="kpi-label">Booked</div>
            </div>
        </div>
    </div>

    {{-- ========= FILTER BAR ========= --}}
    <form method="GET" action="{{ route('booths.index') }}" class="filter-bar-wrap" id="boothFilterForm">

        {{-- Search --}}
        <div class="bp-search-wrap">
            <i class="fas fa-search bp-search-icon"></i>
            <input
                type="text"
                name="search"
                id="boothSearch"
                class="bp-search-input"
                placeholder="Search booths, clients…"
                value="{{ request('search') }}"
                autocomplete="off"
            >
        </div>

        {{-- Status chips --}}
        <div class="chip-group">
            <a href="{{ route('booths.index', ['search' => request('search')]) }}"
               class="filter-chip {{ $activeFilter === 'all' ? 'active' : '' }}">All</a>
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

        {{-- Clear filters --}}
        <div class="filter-bar-right">
            @if(request('search') || request('status'))
            <a href="{{ route('booths.index') }}" class="btn-outline-soft" style="font-size: 0.8125rem;">
                <i class="fas fa-times-circle"></i> Clear
            </a>
            @endif
        </div>
    </form>

    {{-- ========= SECTION EYEBROW ========= --}}
    <div class="section-eyebrow">
        @if(isset($booths) && method_exists($booths, 'total'))
            {{ $booths->total() }} booth{{ $booths->total() !== 1 ? 's' : '' }}
            @if(request('search')) · matching "{{ request('search') }}"@endif
        @endif
    </div>

    {{-- ========= TABLE CARD (glass) ========= --}}
    <div class="table-glass">

        {{-- Desktop table --}}
        <div class="d-none d-md-block">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="padding-left: 1.5rem;">Booth</th>
                            <th>Status</th>
                            <th>Client</th>
                            <th>Event / Floor Plan</th>
                            <th>Price</th>
                            <th>Next Booking</th>
                            <th style="width: 148px; padding-right: 1.5rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booths as $booth)
                        <tr>
                            {{-- Booth Name --}}
                            <td style="padding-left: 1.5rem;">
                                <div class="booth-chip">
                                    <div class="booth-avatar" aria-hidden="true">
                                        {{ strtoupper(substr($booth->booth_number ?? 'B', 0, 2)) }}
                                    </div>
                                    <div class="booth-chip-text">
                                        <span class="booth-name">{{ $booth->booth_number }}</span>
                                        <span class="booth-type">{{ $booth->boothType?->name ?? 'Standard' }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="ap-badge badge-{{ $booth->getStatusColor() }}">
                                    {{ $booth->getStatusLabel() }}
                                </span>
                            </td>

                            {{-- Client --}}
                            <td class="cell-muted">
                                @if($booth->client)
                                    <a href="{{ route('clients.show', $booth->client) }}"
                                       class="text-decoration-none"
                                       style="color: var(--ap-accent); font-weight: 500;">
                                        {{ $booth->client->company }}
                                    </a>
                                @else
                                    <span style="color: var(--ap-text-3);">—</span>
                                @endif
                            </td>

                            {{-- Event / Floor Plan --}}
                            <td class="cell-muted">
                                {{ $booth->floorPlan?->name ?? '—' }}
                            </td>

                            {{-- Price --}}
                            <td class="cell-price">
                                ${{ number_format($booth->price, 2) }}
                            </td>

                            {{-- Next Booking --}}
                            <td class="cell-muted">
                                @if($booth->book && $booth->book->date_book)
                                    <span style="font-weight: 500; color: var(--ap-text);">
                                        {{ $booth->book->date_book->format('M d, Y') }}
                                    </span>
                                @else
                                    <span style="color: var(--ap-text-3);">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td style="padding-right: 1.5rem;">
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('booths.show', $booth) }}"
                                       class="act-btn act-btn-view"
                                       title="View booth">View</a>
                                    <a href="{{ route('booths.edit', $booth) }}"
                                       class="act-btn act-btn-edit"
                                       title="Edit booth">Edit</a>
                                    <div class="dropdown">
                                        <button class="act-btn act-btn-more"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                title="More options">
                                            <i class="fas fa-ellipsis-h" style="font-size: 0.75rem;"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('booths.show', $booth) }}">
                                                    <i class="fas fa-eye me-2 text-muted" style="width:14px;"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('booths.edit', $booth) }}">
                                                    <i class="fas fa-pencil-alt me-2 text-muted" style="width:14px;"></i>Edit Booth
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
                                                        <i class="fas fa-trash-alt me-2" style="width:14px;"></i>Delete
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
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-store-slash"></i>
                                    <p>No booths found
                                        @if(request('search'))for "<strong>{{ request('search') }}</strong>"@endif
                                    </p>
                                    <a href="{{ route('booths.index', ['view' => 'canvas', 'create' => 1]) }}"
                                       class="btn-new-booth">
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

        {{-- Mobile: card list --}}
        <div class="d-md-none mobile-card-list">
            @forelse($booths as $booth)
            <div class="mobile-booth-card">
                <div class="d-flex justify-content-between align-items-start">
                    <a href="{{ route('booths.show', $booth) }}" class="text-decoration-none flex-grow-1">
                        <div class="booth-chip">
                            <div class="booth-avatar" style="width:36px;height:36px;font-size:0.8rem;">
                                {{ strtoupper(substr($booth->booth_number ?? 'B', 0, 2)) }}
                            </div>
                            <div class="booth-chip-text">
                                <span class="booth-name" style="font-size:0.9375rem;">{{ $booth->booth_number }}</span>
                                <span class="booth-type">{{ $booth->floorPlan?->name ?? 'No event' }}</span>
                            </div>
                        </div>
                    </a>
                    <span class="ap-badge badge-{{ $booth->getStatusColor() }} ms-2">{{ $booth->getStatusLabel() }}</span>
                </div>

                <div class="d-flex gap-3 mt-2" style="font-size: 0.8125rem; color: var(--ap-text-2);">
                    <span><i class="fas fa-building me-1" style="opacity:.5;"></i>{{ $booth->client?->company ?? '—' }}</span>
                    <span style="font-weight:600;color:#1a7f37;">${{ number_format($booth->price, 2) }}</span>
                </div>

                <div class="mobile-card-actions">
                    <a href="{{ route('booths.show', $booth) }}" class="act-btn act-btn-view">View</a>
                    <a href="{{ route('booths.edit', $booth) }}" class="act-btn act-btn-edit">Edit</a>
                </div>
            </div>
            @empty
            <div class="empty-state" style="padding: 2.5rem 1rem;">
                <i class="fas fa-store-slash"></i>
                <p>No booths found</p>
                <a href="{{ route('booths.index', ['view' => 'canvas', 'create' => 1]) }}" class="btn-new-booth">
                    <i class="fas fa-plus"></i> New Booth
                </a>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if(isset($booths) && $booths->hasPages())
        <div class="table-foot">
            <small>
                Showing {{ $booths->firstItem() ?? 0 }}–{{ $booths->lastItem() ?? 0 }}
                of {{ $booths->total() }} booths
            </small>

            {{-- Custom compact Apple-style pagination (Font Awesome, no Bootstrap SVG arrows) --}}
            <nav aria-label="Booths pagination">
                <ul class="pagination">

                    {{-- « First --}}
                    <li class="page-item {{ $booths->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link"
                           href="{{ $booths->onFirstPage() ? '#' : $booths->appends(request()->query())->url(1) }}"
                           title="First page" aria-label="First">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>

                    {{-- ‹ Prev --}}
                    <li class="page-item {{ $booths->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link"
                           href="{{ $booths->onFirstPage() ? '#' : $booths->appends(request()->query())->previousPageUrl() }}"
                           title="Previous page" aria-label="Previous">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>

                    {{-- Page numbers (show a window of ±2 around current page) --}}
                    @php
                        $currentPage = $booths->currentPage();
                        $lastPage    = $booths->lastPage();
                        $winStart    = max(1, $currentPage - 2);
                        $winEnd      = min($lastPage, $currentPage + 2);
                    @endphp

                    @if($winStart > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $booths->appends(request()->query())->url(1) }}">1</a>
                        </li>
                        @if($winStart > 2)
                            <li class="page-item disabled">
                                <span class="page-link" style="border:none;background:transparent;padding:0 4px;">…</span>
                            </li>
                        @endif
                    @endif

                    @for($p = $winStart; $p <= $winEnd; $p++)
                        <li class="page-item {{ $p === $currentPage ? 'active' : '' }}">
                            <a class="page-link" href="{{ $booths->appends(request()->query())->url($p) }}">{{ $p }}</a>
                        </li>
                    @endfor

                    @if($winEnd < $lastPage)
                        @if($winEnd < $lastPage - 1)
                            <li class="page-item disabled">
                                <span class="page-link" style="border:none;background:transparent;padding:0 4px;">…</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="{{ $booths->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a>
                        </li>
                    @endif

                    {{-- › Next --}}
                    <li class="page-item {{ $booths->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link"
                           href="{{ $booths->hasMorePages() ? $booths->appends(request()->query())->nextPageUrl() : '#' }}"
                           title="Next page" aria-label="Next">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>

                    {{-- » Last --}}
                    <li class="page-item {{ $booths->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link"
                           href="{{ $booths->hasMorePages() ? $booths->appends(request()->query())->url($lastPage) : '#' }}"
                           title="Last page" aria-label="Last">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
        @endif

    </div>{{-- /.table-glass --}}

</div>{{-- /.booths-container --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Live search: submit on Enter, or let the form button handle it
    const searchInput = document.getElementById('boothSearch');
    if (searchInput) {
        let debounce;
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.closest('form').submit();
            }
        });
    }

    // Subtle row entrance animation
    const rows = document.querySelectorAll('.table-glass tbody tr');
    rows.forEach((row, i) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(8px)';
        row.style.transition = `opacity 0.3s ease ${i * 0.03}s, transform 0.3s ease ${i * 0.03}s`;
        requestAnimationFrame(() => {
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        });
    });

    // Mobile cards
    const cards = document.querySelectorAll('.mobile-booth-card');
    cards.forEach((card, i) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        card.style.transition = `opacity 0.35s ease ${i * 0.05}s, transform 0.35s ease ${i * 0.05}s`;
        requestAnimationFrame(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush
