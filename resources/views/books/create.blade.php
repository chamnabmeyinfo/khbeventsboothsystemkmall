@extends('layouts.adminlte')

@section('title', 'Create Booking')
@section('page-title', 'Create New Booking')
@section('breadcrumb', 'Bookings / Create')

@push('styles')
<style>
    /* ============================================================
       SMART BOOKING — Modern, glass-style, single responsive view
       Breakpoints: 576, 768, 992 (ui-design-engineering rule)
       ============================================================ */
    
    :root {
        --bf-primary: #6366f1;
        --bf-primary-light: #818cf8;
        --bf-primary-dark: #4f46e5;
        --bf-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
        --bf-success: #10b981;
        --bf-warning: #f59e0b;
        --bf-danger: #ef4444;
        --bf-info: #06b6d4;
        --bf-gray-50: #f8fafc;
        --bf-gray-100: #f1f5f9;
        --bf-gray-200: #e2e8f0;
        --bf-gray-300: #cbd5e1;
        --bf-gray-500: #64748b;
        --bf-gray-600: #475569;
        --bf-gray-700: #334155;
        --bf-gray-800: #0f172a;
        --bf-radius: 16px;
        --bf-radius-sm: 12px;
        --bf-shadow: 0 1px 3px rgba(0,0,0,0.05);
        --bf-shadow-lg: 0 20px 40px -12px rgba(99, 102, 241, 0.25);
    }

    .bf-booking-page {
        font-family: "Khmer OS Battambang", "Hanuman", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background: linear-gradient(165deg, #f8fafc 0%, #f1f5f9 45%, #eef2ff 100%);
        min-height: 100%;
        padding-bottom: 120px;
        width: 100%;
        max-width: 100vw;
        overflow-x: hidden;
        box-sizing: border-box;
        overflow-wrap: break-word;
        word-wrap: break-word;
    }

    /* Constrain all content to container-fluid width; no horizontal overflow */
    .bf-booking-page .container-fluid.bf-container {
        width: 100%;
        max-width: 1320px;
        margin-left: auto;
        margin-right: auto;
        padding: 0 15px 32px;
        box-sizing: border-box;
        overflow-x: hidden;
    }
    .bf-container { padding: 0 15px 32px; max-width: 1320px; margin: 0 auto; box-sizing: border-box; }

    /* ----- Two-column layout (desktop) ----- */
    .bf-layout {
        display: flex;
        gap: 28px;
        align-items: start;
        margin-top: 8px;
        max-width: 100%;
    }
    .bf-main-col { flex: 1; min-width: 0; overflow-x: hidden; }
    .bf-sidebar-col {
        width: 360px;
        flex-shrink: 0;
        position: sticky;
        top: 24px;
    }
    .bf-sidebar-card {
        background: white;
        border-radius: var(--bf-radius);
        border: 1px solid var(--bf-gray-200);
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        overflow: hidden;
        position: relative;
    }
    .bf-sidebar-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        background: var(--bf-gradient);
        border-radius: 4px 0 0 4px;
    }
    .bf-sidebar-header {
        padding: 18px 20px;
        background: linear-gradient(180deg, #fafbff 0%, #fff 100%);
        border-bottom: 1px solid var(--bf-gray-200);
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--bf-gray-500);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .bf-sidebar-body { padding: 20px; }
    .bf-sidebar-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 10px 0;
        border-bottom: 1px solid var(--bf-gray-100);
        font-size: 0.875rem;
    }
    .bf-sidebar-row:last-child { border-bottom: none; }
    .bf-sidebar-row .k { color: var(--bf-gray-500); font-weight: 600; }
    .bf-sidebar-row .v { font-weight: 700; color: var(--bf-gray-800); }
    .bf-sidebar-row.total .v { font-size: 1.25rem; color: var(--bf-success); }
    .bf-sidebar-booths {
        max-height: 140px;
        overflow-y: auto;
        padding: 8px 0;
        border-bottom: 1px solid var(--bf-gray-100);
    }
    .bf-sidebar-booths .item { font-size: 0.8125rem; padding: 4px 0; color: var(--bf-gray-700); }
    .bf-sidebar-booths-empty {
        font-size: 0.8rem;
        color: var(--bf-gray-400);
        text-align: center;
        padding: 12px 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .bf-sidebar-booths-empty i { opacity: 0.8; }
    .bf-sidebar-header { position: relative; }
    .bf-sidebar-badge-ready {
        margin-left: auto;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: rgba(16, 185, 129, 0.15);
        color: var(--bf-success);
        display: none;
    }
    .bf-sidebar-card.ready .bf-sidebar-badge-ready { display: inline-flex; align-items: center; gap: 4px; }
    .bf-sidebar-footer-ctx {
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid var(--bf-gray-100);
        font-size: 0.7rem;
        color: var(--bf-gray-400);
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .bf-sidebar-cta { margin-top: 18px; }
    .bf-sidebar-cta .bf-btn-submit { width: 100%; justify-content: center; }
    .bf-sidebar-cta .bf-cta-row { display: flex; gap: 10px; margin-top: 12px; }
    .bf-sidebar-cta .bf-cta-row .bf-btn-clear { flex: 1; }
    .bf-sidebar-cta .bf-cta-row .bf-btn-submit { flex: 2; }

    /* Section titles with step numbers */
    .bf-section-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--bf-gray-700);
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .bf-section-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--bf-gradient);
        color: white;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .bf-section-title + .bf-client-block { margin-top: 0; }
    .bf-section-title + .row { margin-top: 0; }

    /* ----- Progress stepper (advanced) ----- */
    .bf-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 20px;
        padding: 20px 24px;
        background: white;
        border-radius: var(--bf-radius);
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid var(--bf-gray-200);
        position: relative;
    }
    .bf-step {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 12px;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--bf-gray-400);
        position: relative;
        z-index: 1;
    }
    .bf-step-num {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--bf-gray-200);
        color: var(--bf-gray-500);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        transition: all 0.25s ease;
        flex-shrink: 0;
    }
    .bf-step.done { color: var(--bf-success); }
    .bf-step.done .bf-step-num { background: var(--bf-success); color: white; font-size: 0; }
    .bf-step.done .bf-step-num::after { content: '\f00c'; font-family: 'Font Awesome 5 Free'; font-weight: 900; font-size: 0.75rem; color: white; }
    .bf-step.active { color: var(--bf-primary); }
    .bf-step.active .bf-step-num { background: var(--bf-gradient); color: white; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.2); }
    .bf-step-divider {
        flex: 1;
        max-width: 60px;
        height: 3px;
        background: var(--bf-gray-200);
        border-radius: 2px;
        transition: background 0.25s ease;
    }
    .bf-step-divider.filled { background: var(--bf-success); }
    .bf-steps-help { font-size: 0.75rem; color: var(--bf-gray-500); margin-top: 10px; text-align: center; }

    /* ----- Smart hero ----- */
    .bf-hero {
        background: var(--bf-gradient);
        color: white;
        border-radius: var(--bf-radius);
        padding: 28px 28px 32px;
        margin-bottom: 28px;
        box-shadow: var(--bf-shadow-lg), 0 0 0 1px rgba(255,255,255,0.1) inset;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        position: relative;
        overflow: hidden;
    }
    .bf-hero::before {
        content: '';
        position: absolute;
        top: -50%; right: -20%;
        width: 60%; height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.12) 0%, transparent 70%);
        pointer-events: none;
    }
    .bf-hero-inner { position: relative; z-index: 1; }
    .bf-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(255,255,255,0.2);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 10px;
    }
    .bf-hero h1 { font-size: 1.65rem; font-weight: 800; margin: 0 0 4px 0; letter-spacing: -0.02em; display: flex; align-items: center; gap: 12px; }
    .bf-hero-context { font-size: 0.9rem; opacity: 0.92; font-weight: 500; }
    .bf-hero-context-sep { margin: 0 0.35rem; opacity: 0.6; }
    .bf-hero-actions { display: flex; gap: 10px; flex-wrap: wrap; position: relative; z-index: 1; }

    .bf-hero-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: var(--bf-radius-sm);
        font-weight: 600;
        font-size: 0.875rem;
        border: none;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
        text-decoration: none;
        color: inherit;
    }
    .bf-hero-btn-outline { background: rgba(255,255,255,0.2); color: white; }
    .bf-hero-btn-outline:hover { background: rgba(255,255,255,0.3); color: white; }
    .bf-hero-btn-light { background: white; color: var(--bf-primary-dark); }
    .bf-hero-btn-light:hover { background: var(--bf-gray-100); color: var(--bf-primary-dark); }
    .click-animate:active { transform: scale(0.98); }

    /* ----- Cards (modern) ----- */
    .bf-card {
        background: white;
        border-radius: var(--bf-radius);
        border: 1px solid var(--bf-gray-200);
        box-shadow: var(--bf-shadow);
        overflow: hidden;
        margin-bottom: 22px;
        transition: box-shadow 0.25s, transform 0.2s;
        position: relative;
    }
    .bf-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--bf-gradient);
        opacity: 0;
        transition: opacity 0.2s;
        border-radius: 4px 0 0 4px;
    }
    .bf-card:hover { box-shadow: 0 12px 32px rgba(0,0,0,0.08); }
    .bf-card:hover::before { opacity: 1; }
    .bf-card-header {
        padding: 16px 22px;
        background: linear-gradient(180deg, #fafbff 0%, white 100%);
        border-bottom: 1px solid var(--bf-gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .bf-card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--bf-gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .bf-card-title i { opacity: 0.85; }
    .bf-card-body { padding: 22px; }

    /* ----- Client block (smart) ----- */
    .bf-client-block {
        position: relative;
        border: 2px dashed var(--bf-gray-200);
        border-radius: var(--bf-radius);
        padding: 22px 24px;
        background: white;
        margin-bottom: 26px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: border-color 0.25s, box-shadow 0.25s;
        max-width: 100%;
        box-sizing: border-box;
    }
    .bf-client-block.has-client {
        border-style: solid;
        border-color: var(--bf-primary);
        background: white;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.12), 0 0 0 1px rgba(99, 102, 241, 0.08);
    }
    .bf-client-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--bf-gray-500); margin-bottom: 10px; }
    .bf-client-selected {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .bf-client-selected-info { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .bf-client-avatar { width: 44px; height: 44px; border-radius: 12px; background: var(--bf-gradient); color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; }
    .bf-client-name { font-weight: 700; font-size: 1.05rem; color: var(--bf-gray-800); }
    .bf-client-meta { font-size: 0.8125rem; color: var(--bf-gray-500); }
    .bf-btn-change { padding: 6px 12px; border-radius: 8px; font-size: 0.8125rem; font-weight: 600; background: var(--bf-gray-100); color: var(--bf-gray-700); border: none; cursor: pointer; }
    .bf-btn-change:hover { background: var(--bf-gray-200); }
    .bf-client-search-wrap { position: relative; }
    .bf-client-search-input {
        width: 100%;
        padding: 12px 16px 12px 42px;
        border: 1.5px solid var(--bf-gray-200);
        border-radius: var(--bf-radius-sm);
        font-size: 0.9375rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .bf-client-search-input:focus { border-color: var(--bf-primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12); outline: none; }
    .bf-client-search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--bf-gray-400); pointer-events: none; }
    .bf-client-results {
        position: absolute;
        left: 0; right: 0;
        top: 100%;
        margin-top: 6px;
        background: white;
        border-radius: var(--bf-radius-sm);
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        border: 1px solid var(--bf-gray-200);
        max-height: 260px;
        overflow-y: auto;
        z-index: 1050;
    }
    .bf-client-result-item {
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid var(--bf-gray-100);
        transition: background 0.15s;
    }
    .bf-client-result-item:last-child { border-bottom: none; }
    .bf-client-result-item:hover { background: var(--bf-gray-50); }
    .bf-client-result-item .name { font-weight: 600; color: var(--bf-gray-800); }
    .bf-client-result-item .meta { font-size: 0.8125rem; color: var(--bf-gray-500); }
    .bf-btn-new-client {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 10px 16px;
        border-radius: var(--bf-radius-sm);
        font-weight: 600;
        font-size: 0.875rem;
        background: var(--bf-success);
        color: white;
        border: none;
        cursor: pointer;
        margin-top: 10px;
    }
    .bf-btn-new-client:hover { background: #047857; color: white; }

    /* ----- Form elements ----- */
    .bf-form-group { margin-bottom: 14px; }
    .bf-form-label { font-size: 0.8rem; font-weight: 600; color: var(--bf-gray-600); margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
    .bf-form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--bf-gray-200);
        border-radius: var(--bf-radius-sm);
        font-size: 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: white;
    }
    .bf-form-control:focus { border-color: var(--bf-primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12); outline: none; }
    textarea.bf-form-control { min-height: 72px; resize: vertical; }

    /* ----- Booth grid ----- */
    .bf-booth-selector { padding: 16px; background: var(--bf-gray-50); border-radius: var(--bf-radius-sm); }
    .bf-booth-toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
    .bf-booth-view-wrap { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
    .bf-view-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--bf-gray-500); margin-right: 2px; white-space: nowrap; }
    .bf-booth-view-switcher {
        display: inline-flex;
        align-items: stretch;
        gap: 0;
        background: white;
        border: 1px solid var(--bf-gray-200);
        border-radius: var(--bf-radius-sm);
        padding: 3px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }
    .bf-booth-view-switcher .bf-icon-btn {
        width: 36px; height: 36px;
        min-width: 36px; min-height: 36px;
        border: none;
        border-radius: 6px;
        display: inline-flex; align-items: center; justify-content: center;
        background: transparent;
        color: var(--bf-gray-600);
        cursor: pointer;
        transition: all 0.2s;
        padding: 0;
        touch-action: manipulation;
        -webkit-tap-highlight-color: transparent;
    }
    .bf-booth-view-switcher .bf-icon-btn:hover { background: var(--bf-gray-100); color: var(--bf-primary); }
    .bf-booth-view-switcher .bf-icon-btn.active { background: var(--bf-gradient); color: white; box-shadow: 0 1px 3px rgba(99, 102, 241, 0.3); }
    .bf-icon-btn {
        width: 36px; height: 36px;
        border-radius: var(--bf-radius-sm);
        display: inline-flex; align-items: center; justify-content: center;
        background: white;
        border: 1px solid var(--bf-gray-200);
        color: var(--bf-gray-600);
        cursor: pointer;
        transition: all 0.2s;
        padding: 0;
    }
    .bf-icon-btn:hover { border-color: var(--bf-primary); color: var(--bf-primary); background: rgba(79, 70, 229, 0.06); }
    .bf-icon-btn.active { background: var(--bf-gradient); border-color: transparent; color: white; }
    .bf-booth-search-wrap { position: relative; margin-bottom: 16px; }
    .bf-booth-search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--bf-gray-400); pointer-events: none; }
    .bf-booth-search { width: 100%; padding: 10px 14px 10px 40px; border: 1.5px solid var(--bf-gray-200); border-radius: var(--bf-radius-sm); font-size: 0.9rem; background: white; transition: border-color 0.2s, box-shadow 0.2s; }
    .bf-booth-search:focus { border-color: var(--bf-primary); box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12); outline: none; }
    .bf-booth-search::placeholder { color: var(--bf-gray-400); }
    .bf-zone-group { margin-bottom: 20px; transition: opacity 0.2s; }
    .bf-zone-group.bf-zone-hidden { display: none; }
    .bf-zone-header {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--bf-gray-500);
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--bf-gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    .bf-zone-header-toggle { display: inline-flex; align-items: center; gap: 6px; }
    .bf-zone-chevron { display: none; color: var(--bf-gray-400); font-size: 0.75em; transition: transform 0.25s ease; }
    /* Mobile: zone tabs + 20 booths per zone + swipe — tab style, less scroll */
    .bf-zone-tabs-wrap { display: none; }
    .bf-zone-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding: 10px 0 8px;
        margin: 0 -4px;
    }
    .bf-zone-tabs::-webkit-scrollbar { display: none; }
    .bf-zone-tab {
        flex-shrink: 0;
        padding: 10px 16px;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 700;
        border: 1.5px solid var(--bf-gray-200);
        background: white;
        color: var(--bf-gray-600);
        cursor: pointer;
        transition: background 0.2s, border-color 0.2s, color 0.2s;
        white-space: nowrap;
    }
    .bf-zone-tab:hover { background: var(--bf-gray-50); border-color: var(--bf-gray-300); }
    .bf-zone-tab[aria-selected="true"] {
        background: var(--bf-gradient);
        border-color: transparent;
        color: white;
    }
    .bf-zone-swipe-hint {
        font-size: 0.7rem;
        color: var(--bf-gray-400);
        text-align: center;
        padding: 4px 0 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .bf-zone-swipe-hint i { font-size: 0.75em; }
    .bf-zone-show-more-wrap { text-align: center; padding: 12px 0; }
    .bf-zone-show-more-btn {
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid var(--bf-primary);
        background: rgba(99, 102, 241, 0.08);
        color: var(--bf-primary);
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }
    .bf-zone-show-more-btn:hover { background: rgba(99, 102, 241, 0.15); color: var(--bf-primary-dark); }
    @media (max-width: 767.98px) {
        .bf-zones-tab-mode .bf-zone-tabs-wrap { display: block !important; }
        .bf-zones-tab-mode .bf-zone-group { display: none !important; }
        .bf-zones-tab-mode .bf-zone-group.bf-zone-active { display: block !important; }
        .bf-zones-tab-mode .bf-zone-group .bf-zone-header { display: none; }
        .bf-zones-tab-mode .bf-booth-item-wrapper.bf-booth-folded { display: none !important; }
        .bf-booking-page { padding-bottom: 160px; }
    }
    @media (min-width: 768px) {
        .bf-zone-tabs-wrap { display: none !important; }
    }
    .bf-zone-name-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 0;
        margin: -4px 0;
        font-size: inherit;
        font-weight: inherit;
        text-transform: inherit;
        letter-spacing: inherit;
        color: var(--bf-gray-700);
        background: none;
        border: none;
        cursor: pointer;
        border-radius: 6px;
        transition: color 0.2s, background 0.2s;
    }
    .bf-zone-name-btn:hover { color: var(--bf-primary); background: rgba(99, 102, 241, 0.08); }
    .bf-zone-name-btn:focus-visible { outline: 2px solid var(--bf-primary); outline-offset: 2px; }
    .bf-zone-name-btn i { font-size: 0.7em; opacity: 0.8; }
    body.bf-zone-popover-open { overflow: hidden !important; }
    .bf-zone-booths-popover-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1049;
        background: rgba(0, 0, 0, 0.35);
        -webkit-tap-highlight-color: transparent;
        cursor: pointer;
        touch-action: none;
    }
    .bf-zone-booths-popover {
        position: fixed;
        z-index: 1050;
        min-width: 200px;
        max-width: 320px;
        max-height: 240px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        background: white;
        border-radius: var(--bf-radius-sm);
        box-shadow: 0 10px 40px rgba(0,0,0,0.15), 0 0 0 1px var(--bf-gray-200);
        font-size: 0.8125rem;
    }
    .bf-zone-booths-popover-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 14px;
        flex-shrink: 0;
        border-bottom: 1px solid var(--bf-gray-200);
    }
    .bf-zone-booths-popover-title { font-weight: 700; color: var(--bf-gray-800); display: flex; align-items: center; gap: 6px; margin: 0; }
    .bf-zone-booths-popover-close {
        width: 36px;
        height: 36px;
        min-width: 36px;
        min-height: 36px;
        border: none;
        background: var(--bf-gray-100);
        color: var(--bf-gray-600);
        font-size: 1.5rem;
        line-height: 1;
        cursor: pointer;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        -webkit-tap-highlight-color: transparent;
    }
    .bf-zone-booths-popover-close:hover { background: var(--bf-gray-200); color: var(--bf-gray-800); }
    .bf-zone-booths-popover-close { touch-action: manipulation; }
    .bf-zone-booths-popover-list {
        padding: 12px 14px;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        max-height: 200px;
    }
    .bf-zone-booths-popover-list span {
        display: inline-block;
        padding: 4px 10px;
        background: var(--bf-gray-100);
        border-radius: 6px;
        font-weight: 600;
        color: var(--bf-gray-700);
        font-size: 0.75rem;
    }
    .bf-zone-booths-popover-handle {
        display: none;
        flex-shrink: 0;
        padding: 10px 0 6px;
        align-items: center;
        justify-content: center;
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }
    .bf-zone-booths-popover-handle::before {
        content: '';
        width: 36px;
        height: 4px;
        background: var(--bf-gray-300);
        border-radius: 2px;
    }
    @media (max-width: 767.98px) {
        .bf-zone-booths-popover-handle { display: flex; min-height: 28px; cursor: pointer; }
        .bf-zone-booths-popover {
            min-width: 0;
            max-width: none;
            left: 0;
            right: 0;
            bottom: 0;
            top: auto;
            max-height: 60vh;
            border-radius: 16px 16px 0 0;
            box-shadow: 0 -8px 32px rgba(0,0,0,0.2);
        }
        .bf-zone-booths-popover-list { max-height: 50vh; }
        .bf-zone-booths-popover-close { width: 44px; height: 44px; min-width: 44px; min-height: 44px; font-size: 1.75rem; }
        .bf-zone-btn { min-height: 44px; min-width: 44px; padding: 10px 14px; font-size: 0.8125rem; touch-action: manipulation; -webkit-tap-highlight-color: transparent; }
        .bf-zone-name-btn { min-height: 44px; padding: 10px 12px; -webkit-tap-highlight-color: transparent; touch-action: manipulation; }
        .bf-booth-item { min-height: 56px; -webkit-tap-highlight-color: transparent; touch-action: manipulation; cursor: pointer; }
    }
    .bf-zone-header { position: relative; }
    .bf-zone-actions { display: flex; gap: 4px; }
    .bf-zone-btn {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 600;
        border: 1px solid var(--bf-gray-200);
        background: white;
        color: var(--bf-gray-600);
        cursor: pointer;
        transition: all 0.15s;
    }
    .bf-zone-btn:hover { background: var(--bf-gray-100); border-color: var(--bf-gray-300); }
    .bf-zone-btn.bf-zone-btn-primary { background: rgba(79, 70, 229, 0.1); border-color: var(--bf-primary); color: var(--bf-primary); }
    .bf-booth-item-wrapper.bf-booth-hidden { display: none !important; }
    .bf-btn-empty-cta { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: var(--bf-radius-sm); font-weight: 600; font-size: 0.875rem; background: rgba(79, 70, 229, 0.1); border: 1px solid rgba(79, 70, 229, 0.3); color: var(--bf-primary); cursor: pointer; transition: all 0.2s; }
    .bf-btn-empty-cta:hover { background: rgba(79, 70, 229, 0.15); border-color: var(--bf-primary); color: var(--bf-primary-dark); }
    .bf-booth-grid { display: flex; flex-wrap: wrap; margin: 0 -6px; }
    .bf-booth-item-wrapper { padding: 6px; transition: all 0.2s; }
    .bf-col-6 { width: 50%; }
    /* View-mode layout: scope to #boothGridArea so layout classes override .bf-col-6 */
    #boothGridArea.view-mode-default .bf-booth-item-wrapper { width: 50%; }
    #boothGridArea.view-mode-minimal .bf-booth-item-wrapper { width: 33.333%; }
    #boothGridArea.view-mode-tiny .bf-booth-item-wrapper { width: 25%; }
    #boothGridArea.view-mode-expand .bf-booth-item-wrapper { width: 100%; }
    .bf-booth-item {
        position: relative;
        border: 2px solid var(--bf-gray-200);
        border-radius: var(--bf-radius-sm);
        padding: 14px 12px;
        min-height: 64px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .bf-booth-item:hover { border-color: var(--bf-primary-light); box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15); }
    .bf-booth-item.selected {
        border-color: var(--bf-primary);
        background: linear-gradient(180deg, rgba(79, 70, 229, 0.12) 0%, rgba(79, 70, 229, 0.06) 100%);
        box-shadow: 0 0 0 2px var(--bf-primary), 0 4px 16px rgba(79, 70, 229, 0.2);
    }
    .bf-booth-item.selected::after {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 6px; right: 6px;
        width: 22px; height: 22px;
        background: var(--bf-primary);
        color: white;
        border-radius: 50%;
        font-size: 0.65rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bf-booth-item.selected { animation: bf-booth-pick 0.35s ease; }
    @keyframes bf-booth-pick {
        0% { transform: scale(1); }
        50% { transform: scale(1.04); }
        100% { transform: scale(1); }
    }
    .bf-booth-item:focus-visible { outline: 2px solid var(--bf-primary); outline-offset: 2px; }
    .bf-booth-item-wrapper.bf-booth-highlight .bf-booth-item {
        box-shadow: 0 0 0 3px var(--bf-primary), 0 8px 24px rgba(99, 102, 241, 0.35);
        animation: bf-booth-highlight-pulse 1.2s ease 2;
    }
    @keyframes bf-booth-highlight-pulse {
        0%, 100% { box-shadow: 0 0 0 3px var(--bf-primary), 0 8px 24px rgba(99, 102, 241, 0.35); }
        50% { box-shadow: 0 0 0 5px var(--bf-primary-light), 0 12px 28px rgba(99, 102, 241, 0.45); }
    }
    .bf-sidebar-booths .bf-sidebar-booth-item {
        cursor: pointer;
        padding: 6px 10px;
        margin: -4px -8px 4px -8px;
        border-radius: 8px;
        transition: background 0.15s, color 0.15s;
    }
    .bf-sidebar-booths .bf-sidebar-booth-item:hover { background: var(--bf-gray-100); color: var(--bf-primary); }
    .bf-sidebar-booths .bf-sidebar-booth-item:focus-visible { outline: 2px solid var(--bf-primary); outline-offset: 2px; }
    .bf-tag-num { cursor: pointer; }
    .bf-tag-num:hover { text-decoration: underline; }
    .bf-tag-remove { cursor: pointer; color: var(--bf-gray-500); font-size: 0.9em; vertical-align: middle; }
    .bf-tag-remove:hover { color: var(--bf-danger); }
    .bf-btn-submit:focus-visible, .bf-btn-clear:focus-visible { outline: 2px solid var(--bf-primary); outline-offset: 2px; }
    .bf-key-hint { font-size: 0.7rem; color: var(--bf-gray-400); margin-left: 8px; }
    @media (max-width: 767.98px) { .bf-key-hint { display: none; } }
    .bf-jump-to-booths {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--bf-primary);
        background: none;
        border: none;
        cursor: pointer;
        padding: 6px 0;
        margin-top: 8px;
        transition: color 0.2s;
    }
    .bf-jump-to-booths:hover { color: var(--bf-primary-dark); }
    .bf-jump-to-booths:focus-visible { outline: 2px solid var(--bf-primary); outline-offset: 2px; border-radius: 4px; }
    .bf-jump-wrap { display: none; }
    .bf-client-block.has-client + .bf-jump-wrap { display: block; }
    .bf-jump-wrap + .bf-section-title { margin-top: 0.5rem; }
    .bf-jump-wrap ~ .row .bf-section-title { margin-top: 0; }
    .bf-jump-wrap { margin-bottom: 0; }
    .bf-booking-page .bf-section-title.mt-4 { margin-top: 1rem; }
    .bf-client-block.has-client + .bf-jump-wrap ~ .bf-section-title.mt-4 { margin-top: 1rem; }
    .bf-booth-number { font-size: 1.05rem; font-weight: 800; color: var(--bf-gray-800); line-height: 1.2; }
    .bf-booth-price { font-size: 0.8rem; font-weight: 600; color: var(--bf-success); margin-top: 2px; }
    .bf-fp-label { font-size: 0.6rem; color: var(--bf-gray-400); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px; }
    .bf-section-view-switcher { display: flex; gap: 4px; }

    /* ----- Selected booths list ----- */
    .bf-selected-list {
        min-height: 80px;
        max-height: 160px;
        overflow-y: auto;
        padding: 12px;
        background: var(--bf-gray-50);
        border-radius: var(--bf-radius-sm);
        border: 1px solid var(--bf-gray-200);
    }
    .bf-selected-list .bf-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 10px;
        margin: 4px 6px 4px 0;
        background: white;
        border: 1px solid var(--bf-gray-200);
        border-radius: 8px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--bf-gray-700);
        cursor: pointer;
        transition: all 0.15s;
    }
    .bf-selected-list .bf-tag:hover { border-color: var(--bf-danger); color: var(--bf-danger); background: #fef2f2; }

    /* ----- Smart status line ----- */
    .bf-smart-status {
        padding: 12px 18px;
        border-radius: var(--bf-radius-sm);
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background 0.2s, color 0.2s;
    }
    .bf-smart-status.ready { background: rgba(16, 185, 129, 0.12); color: #047857; border: 1px solid rgba(16, 185, 129, 0.25); }
    .bf-smart-status.ready i { color: var(--bf-success); }
    .bf-smart-status.pending { background: rgba(99, 102, 241, 0.08); color: var(--bf-gray-700); border: 1px solid rgba(99, 102, 241, 0.15); }
    .bf-smart-status.pending i { color: var(--bf-primary); }
    .bf-smart-status.start { background: var(--bf-gray-100); color: var(--bf-gray-600); border: 1px solid var(--bf-gray-200); }

    /* ----- Quick date actions ----- */
    .bf-quick-date { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
    .bf-quick-date-btn {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        background: var(--bf-gray-100);
        border: 1px solid var(--bf-gray-200);
        color: var(--bf-gray-700);
        cursor: pointer;
        transition: all 0.2s;
    }
    .bf-quick-date-btn:hover { background: var(--bf-gray-200); border-color: var(--bf-gray-300); }

    /* ----- Glass summary bar (floating) ----- */
    .bf-summary-bar {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: calc(100% - 32px);
        max-width: 720px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.8);
        border-radius: var(--bf-radius);
        padding: 16px 22px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        z-index: 200;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.04);
    }
    .bf-summary-stats { display: flex; flex-wrap: wrap; gap: 20px; align-items: center; }
    .bf-summary-stat label { display: block; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--bf-gray-500); margin-bottom: 2px; }
    .bf-summary-stat span { font-size: 1.1rem; font-weight: 800; color: var(--bf-gray-800); }
    .bf-summary-stat.highlight span { color: var(--bf-success); font-size: 1.3rem; }
    .bf-summary-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .bf-summary-status-text { font-size: 0.8rem; font-weight: 600; color: var(--bf-gray-500); margin-right: 8px; }
    .bf-summary-bar.ready .bf-summary-status-text { color: var(--bf-success); }
    .bf-btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9375rem;
        background: var(--bf-gradient);
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .bf-btn-submit:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45); color: white; }
    .bf-btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }
    .bf-btn-submit { min-height: 44px; position: relative; }
    .bf-btn-submit .bf-spin { margin-right: 8px; animation: bf-spin 0.8s linear infinite; }
    @keyframes bf-spin { to { transform: rotate(360deg); } }
    .bf-form-loading { pointer-events: none; opacity: 0.85; }
    .bf-btn-clear { padding: 10px 18px; border-radius: var(--bf-radius-sm); font-weight: 600; font-size: 0.875rem; background: var(--bf-gray-100); color: var(--bf-gray-700); border: 1px solid var(--bf-gray-200); cursor: pointer; min-height: 44px; }
    .bf-btn-clear:hover { background: var(--bf-gray-200); }

    /* ----- Booking review (everything related to booking) ----- */
    .bf-booking-review {
        background: white;
        border: 1px solid var(--bf-gray-200);
        border-radius: var(--bf-radius);
        padding: 14px 20px;
        margin-bottom: 20px;
        box-shadow: var(--bf-shadow);
    }
    .bf-booking-review-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--bf-gray-500); margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
    .bf-booking-review-grid { display: flex; flex-wrap: wrap; gap: 16px 24px; }
    .bf-booking-review-item { display: flex; flex-direction: column; gap: 2px; }
    .bf-booking-review-key { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: var(--bf-gray-500); }
    .bf-booking-review-val { font-size: 0.9375rem; font-weight: 600; color: var(--bf-gray-800); }
    .bf-booking-review-total .bf-booking-review-val { font-size: 1.1rem; color: var(--bf-success); }
    .bf-form-hint { font-size: 0.75rem; }

    /* ----- Quick guide: Help button + Tutorial button ----- */
    .bf-quick-guide {
        background: white;
        border-radius: var(--bf-radius);
        border: 1px solid var(--bf-gray-200);
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .bf-guide-btns {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding: 12px 18px;
        background: linear-gradient(180deg, #fafbff 0%, #fff 100%);
        border-bottom: 1px solid var(--bf-gray-100);
    }
    .bf-quick-guide.help-open .bf-guide-btns { border-bottom-color: var(--bf-gray-200); }
    .bf-btn-help, .bf-btn-tutorial {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: var(--bf-radius-sm);
        font-size: 0.9rem;
        font-weight: 600;
        border: 1px solid var(--bf-gray-200);
        background: white;
        color: var(--bf-gray-700);
        cursor: pointer;
        min-height: 44px;
        transition: all 0.2s;
    }
    .bf-btn-help:hover, .bf-btn-tutorial:hover {
        border-color: var(--bf-primary);
        color: var(--bf-primary);
        background: rgba(99, 102, 241, 0.06);
    }
    .bf-btn-help[aria-expanded="true"], .bf-btn-help.active {
        border-color: var(--bf-primary);
        background: rgba(99, 102, 241, 0.1);
        color: var(--bf-primary-dark);
    }
    .bf-btn-tutorial {
        border-color: var(--bf-primary);
        background: rgba(99, 102, 241, 0.08);
        color: var(--bf-primary-dark);
    }
    .bf-btn-tutorial:hover { background: rgba(99, 102, 241, 0.15); }
    .bf-quick-guide-body {
        padding: 16px 18px 18px;
        border-top: none;
        display: none;
    }
    .bf-quick-guide.help-open .bf-quick-guide-body { display: block; }
    .bf-quick-guide-step {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 0.875rem;
        color: var(--bf-gray-700);
        line-height: 1.45;
        padding: 8px 0;
        border-bottom: 1px solid var(--bf-gray-100);
    }
    .bf-quick-guide-step:last-child { border-bottom: none; padding-bottom: 0; }
    .bf-quick-guide-step .num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--bf-gradient);
        color: white;
        font-size: 0.75rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .bf-quick-guide-step strong { color: var(--bf-gray-800); font-weight: 700; }
    .bf-quick-guide-step .tip { color: var(--bf-gray-500); font-weight: 500; }
    /* Tutorial modal (fallback / inline copy) */
    #bfTutorialModal .modal-header { background: var(--bf-gradient) !important; color: white; border-radius: 16px 16px 0 0; }
    #bfTutorialModal .bf-quick-guide-step { padding: 10px 0; border-bottom-color: var(--bf-gray-200); }
    #bfTutorialModal .bf-quick-guide-step:last-child { border-bottom: none; }
    #bfTutorialModal kbd { padding: 2px 6px; font-size: 0.8em; background: var(--bf-gray-100); border: 1px solid var(--bf-gray-300); border-radius: 4px; }

    /* ----- Highlight navigation tour (bright step + black fade) ----- */
    .bf-tour-overlay {
        position: fixed;
        inset: 0;
        z-index: 2050;
        background: rgba(0, 0, 0, 0.82);
        pointer-events: auto;
        transition: opacity 0.3s ease;
    }
    .tour-highlight {
        position: relative;
        z-index: 2100 !important;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.98),
                    0 0 0 6px rgba(99, 102, 241, 0.7),
                    0 0 32px 8px rgba(99, 102, 241, 0.4);
        border-radius: var(--bf-radius);
        transition: box-shadow 0.3s ease;
    }
    .bf-tour-tooltip {
        position: fixed;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2200;
        width: calc(100% - 32px);
        max-width: 420px;
        background: white;
        border-radius: var(--bf-radius);
        box-shadow: 0 12px 48px rgba(0,0,0,0.25), 0 0 0 1px rgba(0,0,0,0.06);
        padding: 20px 22px;
        pointer-events: auto;
    }
    .bf-tour-tooltip-inner { text-align: center; }
    .bf-tour-step-label {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--bf-primary);
        margin: 0 0 6px 0;
    }
    .bf-tour-step-title { font-size: 1.1rem; font-weight: 800; color: var(--bf-gray-800); margin: 0 0 8px 0; }
    .bf-tour-step-desc { font-size: 0.875rem; color: var(--bf-gray-600); margin: 0 0 16px 0; line-height: 1.4; }
    .bf-tour-actions { display: flex; flex-direction: column; gap: 12px; align-items: center; }
    .bf-tour-skip {
        background: none;
        border: none;
        color: var(--bf-gray-500);
        font-size: 0.8rem;
        cursor: pointer;
        padding: 4px 8px;
        text-decoration: underline;
    }
    .bf-tour-skip:hover { color: var(--bf-gray-700); }
    .bf-tour-nav { display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; }
    .bf-tour-prev, .bf-tour-next, .bf-tour-finish {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: var(--bf-radius-sm);
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid var(--bf-gray-200);
        background: white;
        color: var(--bf-gray-700);
        cursor: pointer;
        min-height: 44px;
    }
    .bf-tour-prev:hover, .bf-tour-next:hover { border-color: var(--bf-primary); color: var(--bf-primary); background: rgba(99, 102, 241, 0.06); }
    .bf-tour-finish {
        background: var(--bf-gradient);
        border: none;
        color: white;
    }
    .bf-tour-finish:hover { opacity: 0.95; color: white; }

    /* ----- Modals ----- */
    .client-popup-card, #fpPickerModal .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    #createClientModal .modal-header { border-radius: 16px 16px 0 0; background: var(--bf-gradient) !important; }

    /* ----- Responsive: mobile overflow containment ----- */
    @media (max-width: 991.98px) {
        body { overflow-x: hidden; }
        .content-wrapper { overflow-x: hidden; max-width: 100%; }
        .content { overflow-x: hidden; max-width: 100%; box-sizing: border-box; }
        .bf-booking-page .container-fluid.bf-container { padding: 0 12px 20px; }
        .bf-layout { flex-direction: column; }
        .bf-sidebar-col { display: none !important; width: 100%; position: static; }
        .bf-main-col { min-width: 0; overflow-x: hidden; max-width: 100%; }
        .bf-container { padding: 0 12px 20px; }
        .bf-client-block { padding: 16px; }
        .bf-steps { flex-wrap: wrap; justify-content: flex-start; padding: 14px 16px; }
        .bf-step { padding: 0 8px; }
        .bf-step > span:last-child { display: none; }
        .bf-step-divider { max-width: 24px; }
        .bf-steps-help { font-size: 0.7rem; margin-top: 8px; }
        .bf-card-header, .bf-card-body { padding: 14px 16px; }
        .bf-zone-actions { width: 100%; justify-content: flex-end; }
        /* Match Bootstrap .row margin to container padding so nothing goes off-screen */
        .bf-booking-page .row { margin-left: -12px; margin-right: -12px; max-width: calc(100% + 24px); }
        .bf-booking-page .row > [class*="col-"] { padding-left: 12px; padding-right: 12px; max-width: 100%; box-sizing: border-box; }
        #boothGridArea { max-width: 100%; overflow-x: hidden; }
        .bf-booth-grid { margin: 0 -6px; max-width: calc(100% + 12px); }
        .bf-zone-tabs-wrap { max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .bf-zone-tabs { margin: 0; padding: 0 2px; }
    }
    @media (max-width: 768px) {
        .bf-booth-toolbar { flex-direction: column; align-items: stretch; gap: 10px; }
        .bf-booth-toolbar .bf-view-label { margin-bottom: 2px; }
        .bf-booth-view-switcher { align-self: flex-start; padding: 4px; }
        .bf-booth-view-switcher .bf-icon-btn { width: 44px; height: 44px; min-width: 44px; min-height: 44px; }
        .bf-guide-btns { padding: 10px 14px; gap: 8px; }
        .bf-btn-help, .bf-btn-tutorial { padding: 8px 14px; font-size: 0.85rem; min-height: 40px; }
        .bf-quick-guide-body { padding: 12px 14px 14px; }
        .bf-quick-guide-step { font-size: 0.8125rem; padding: 6px 0; }
        .bf-quick-guide-step .num { width: 22px; height: 22px; font-size: 0.7rem; }
        .bf-hero { padding: 20px 20px 24px; }
        .bf-hero h1 { font-size: 1.35rem; }
        .bf-hero-context { font-size: 0.8125rem; }
        .bf-hero-badge { font-size: 0.65rem; padding: 3px 8px; }
        .bf-smart-status { padding: 10px 14px; font-size: 0.8125rem; }
        .bf-col-6 { width: 50%; }
        #boothGridArea.view-mode-default .bf-booth-item-wrapper,
        #boothGridArea.view-mode-minimal .bf-booth-item-wrapper,
        #boothGridArea.view-mode-tiny .bf-booth-item-wrapper { width: 50%; }
        #boothGridArea.view-mode-expand .bf-booth-item-wrapper { width: 100%; }
        .bf-booth-item { padding: 10px 8px; }
        .bf-booking-review { padding: 12px 16px; }
        .bf-booking-review-grid { gap: 12px 16px; }
        .bf-summary-bar { bottom: 12px; left: 12px; right: 12px; width: auto; max-width: none; transform: none; flex-direction: column; align-items: stretch; padding: 14px 18px; box-sizing: border-box; }
        .bf-summary-status-text { margin-right: 0; margin-bottom: 4px; text-align: center; }
        .bf-summary-actions { justify-content: stretch; }
        .bf-btn-submit { flex: 1; min-height: 44px; }
        .bf-btn-clear { min-height: 44px; }
        .bf-tour-tooltip { bottom: 16px; left: 12px; right: 12px; width: auto; max-width: none; transform: none; padding: 16px 18px; box-sizing: border-box; }
        .bf-tour-step-title { font-size: 1rem; }
    }
    .bf-back-to-top {
        display: none;
        position: fixed;
        bottom: 90px;
        right: 16px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--bf-gradient);
        color: white;
        border: none;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
        cursor: pointer;
        z-index: 199;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s, opacity 0.2s;
        -webkit-tap-highlight-color: transparent;
    }
    .bf-back-to-top:hover { transform: translateY(-2px); }
    .bf-back-to-top:focus-visible { outline: 2px solid var(--bf-primary); outline-offset: 2px; }
    @media (min-width: 768px) {
        .bf-back-to-top { display: none !important; }
    }
    @media (max-width: 767.98px) {
        .bf-back-to-top { display: none; bottom: 88px; right: 12px; width: 44px; height: 44px; font-size: 1rem; }
        .bf-back-to-top.bf-back-to-top-visible { display: flex !important; }
    }
    @media (max-width: 575.98px) {
        .bf-col-6 { width: 100%; }
        #boothGridArea.view-mode-default .bf-booth-item-wrapper,
        #boothGridArea.view-mode-minimal .bf-booth-item-wrapper,
        #boothGridArea.view-mode-tiny .bf-booth-item-wrapper,
        #boothGridArea.view-mode-expand .bf-booth-item-wrapper { width: 100%; }
        .bf-booth-view-switcher .bf-icon-btn { width: 44px; height: 44px; min-width: 44px; min-height: 44px; }
    }
    /* Extra-small viewports (e.g. 320–360px): keep everything on screen */
    @media (max-width: 399.98px) {
        .bf-booking-page .container-fluid.bf-container,
        .bf-container { padding: 0 10px 20px; }
        .bf-booking-page .row { margin-left: -10px; margin-right: -10px; max-width: calc(100% + 20px); }
        .bf-booking-page .row > [class*="col-"] { padding-left: 10px; padding-right: 10px; }
        .bf-summary-bar { left: 10px; right: 10px; padding: 12px 14px; }
        .bf-tour-tooltip { left: 10px; right: 10px; padding: 12px 14px; }
        .bf-hero { padding: 16px; }
        .bf-hero h1 { font-size: 1.2rem; }
        .bf-hero-actions { gap: 8px; }
        .bf-steps { padding: 12px; }
        .bf-card-header, .bf-card-body { padding: 12px; }
        .bf-client-block { padding: 12px; }
        .bf-guide-btns, .bf-quick-guide-body { padding: 10px 12px; }
    }
    .bf-booking-page .bf-client-name,
    .bf-booking-page .bf-client-meta,
    .bf-booking-page .bf-hero-context,
    .bf-booking-page .bf-booth-number,
    .bf-booking-page .bf-selected-list { overflow-wrap: break-word; word-wrap: break-word; max-width: 100%; }

    /* SweetAlert2 popup (aria-labelledby="swal2-title") at top of screen on this page */
    .swal2-container {
        align-items: flex-start !important;
        padding-top: 1.25em !important;
    }

    /* Booking create settings modal */
    #bfCreateSettingsModal .bf-setting-row { cursor: pointer; margin: 0; }
    #bfCreateSettingsModal .bf-setting-toggle { width: 1.25rem; height: 1.25rem; cursor: pointer; }
    #bfCreateSettingsModal .bf-settings-list .border-bottom:last-of-type { border-bottom: none !important; }

    /* Lock screen pinch: reduce accidental zoom on touch devices */
    body.bf-pinch-lock {
        touch-action: manipulation;
        -ms-touch-action: manipulation;
    }
    body.bf-pinch-lock .bf-booking-page,
    body.bf-pinch-lock .container-fluid {
        touch-action: manipulation;
    }
</style>
@endpush

@section('content')
<section class="content bf-booking-page">
    <div class="container-fluid bf-container">
        <form action="{{ route('books.store') }}" method="POST" id="bookingForm">
            @csrf

            <!-- Progress stepper -->
            <div class="bf-steps" id="bfSteps">
                <div class="bf-step active" data-step="1"><span class="bf-step-num">1</span><span>Client</span></div>
                <div class="bf-step-divider" data-divider="1"></div>
                <div class="bf-step" data-step="2"><span class="bf-step-num">2</span><span>Details</span></div>
                <div class="bf-step-divider" data-divider="2"></div>
                <div class="bf-step" data-step="3"><span class="bf-step-num">3</span><span>Booths</span></div>
            </div>
            <p class="bf-steps-help" id="bfStepsHelp">Select client → set details → pick booths → review & create</p>

            <!-- Smart hero (Step 1: Select Floor plan) -->
            <div class="bf-hero" id="tourStep1">
                <div class="bf-hero-inner">
                    <div class="bf-hero-badge"><i class="fas fa-bolt"></i> Smart booking</div>
                    <h1><i class="fas fa-calendar-check"></i> New booking</h1>
                    @if($currentFloorPlan)
                    <p class="bf-hero-context mb-0">
                        <i class="fas fa-map-marker-alt"></i> {{ $currentFloorPlan->name }}
                        @if($currentFloorPlan->event)
                        <span class="bf-hero-context-sep">·</span> {{ $currentFloorPlan->event->title }}
                        @endif
                    </p>
                    @else
                    <p class="bf-hero-context mb-0"><i class="fas fa-layer-group"></i> Choose a floor plan to start</p>
                    @endif
                </div>
                <div class="bf-hero-actions">
                    <button type="button" class="bf-hero-btn bf-hero-btn-outline click-animate" id="btnBookingSettings" title="Booking settings" aria-label="Open booking settings">
                        <i class="fas fa-cog"></i> <span class="d-none d-md-inline">Settings</span>
                    </button>
                    <button type="button" class="bf-hero-btn bf-hero-btn-outline click-animate" id="btnFilterFP" title="Change floor plan">
                        <i class="fas fa-map"></i> <span class="d-none d-md-inline">{{ $currentFloorPlan->name ?? 'Floor plan' }}</span>
                    </button>
                    <a href="{{ route('books.index') }}" class="bf-hero-btn bf-hero-btn-light click-animate" title="Back to list"><i class="fas fa-arrow-left"></i> <span class="d-none d-md-inline">List</span></a>
                </div>
            </div>

            <!-- Help & Tutorial: buttons to show help text or open tutorial -->
            <div class="bf-quick-guide" id="bfQuickGuide" role="region" aria-label="Booking help and tutorial">
                <div class="bf-guide-btns">
                    <button type="button" class="bf-btn-help click-animate" id="bfBtnHelp" aria-expanded="false" aria-controls="bfQuickGuideBody" title="Show or hide help text">
                        <i class="fas fa-question-circle"></i> Help
                    </button>
                    <button type="button" class="bf-btn-tutorial click-animate" id="bfBtnTutorial" title="Start step-by-step highlight tutorial">
                        <i class="fas fa-play-circle"></i> Tutorial
                    </button>
                </div>
                <div class="bf-quick-guide-body" id="bfQuickGuideBody">
                    <div class="bf-quick-guide-step">
                        <span class="num">1</span>
                        <span><strong>Select a client</strong> <span class="tip">— Search by name or company, or add a new client.</span></span>
                    </div>
                    <div class="bf-quick-guide-step">
                        <span class="num">2</span>
                        <span><strong>Set details</strong> <span class="tip">— Choose type, date &amp; time. Use &quot;Today&quot; or &quot;Now&quot; for speed.</span></span>
                    </div>
                    <div class="bf-quick-guide-step">
                        <span class="num">3</span>
                        <span><strong>Pick booths</strong> <span class="tip">— Click tiles in the grid. Use &quot;Select all&quot; per zone or search to find booths.</span></span>
                    </div>
                    <div class="bf-quick-guide-step">
                        <span class="num">4</span>
                        <span><strong>Review &amp; create</strong> <span class="tip">— Check the summary (or sidebar), then Create booking or press Ctrl+Enter.</span></span>
                    </div>
                </div>
            </div>

            <!-- Two-column layout: main content + sticky Booking summary -->
            <div class="bf-layout">
                <div class="bf-main-col">
            <!-- Smart status (updates as you fill); screen readers hear changes -->
            <div class="bf-smart-status start" id="bfSmartStatus" role="status" aria-live="polite" aria-atomic="true">
                <i class="fas fa-info-circle"></i>
                <span id="bfSmartStatusText">Select a client to start your booking</span>
            </div>

            <!-- 1. Client -->
            <h2 class="bf-section-title"><span class="bf-section-num">1</span> Client</h2>
            <div class="bf-client-block" id="bfClientBlock">
                <input type="hidden" id="clientid" name="clientid" required>
                <div class="bf-client-label">Who is this booking for?</div>
                <div id="selectedClientUI" style="display: none;">
                    <div class="bf-client-selected">
                        <div class="bf-client-selected-info">
                            <div class="bf-client-avatar" id="uiClientInitial">—</div>
                            <div>
                                <div class="bf-client-name" id="uiClientName"></div>
                                <div class="bf-client-meta" id="uiClientDetails"></div>
                            </div>
                        </div>
                        <button type="button" class="bf-btn-change click-animate" id="btnChangeClient">Change</button>
                    </div>
                </div>
                <div id="searchClientUI">
                    <div class="bf-client-search-wrap">
                        <i class="fas fa-search bf-client-search-icon"></i>
                        <input type="text" id="clientSearchInline" class="bf-client-search-input" placeholder="Search by name or company..." autocomplete="off">
                        <div id="inlineClientResults" class="bf-client-results" style="display: none;">
                            <div id="inlineClientResultsList"></div>
                        </div>
                    </div>
                    <button type="button" class="bf-btn-new-client click-animate" data-toggle="modal" data-target="#createClientModal">
                        <i class="fas fa-user-plus"></i> New client
                    </button>
                </div>
            </div>

            <div class="bf-jump-wrap">
                <button type="button" class="bf-jump-to-booths click-animate" id="btnJumpToBooths" aria-label="Jump to booth grid">
                    <i class="fas fa-arrow-down"></i> Jump to booths
                </button>
            </div>

            <!-- 2. Details + 3. Booths -->
            <h2 class="bf-section-title mt-4" id="sectionDetailsBooths"><span class="bf-section-num">2</span> Details &amp; booths</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="bf-card" id="tourStep2">
                        <div class="bf-card-header">
                            <h3 class="bf-card-title"><i class="fas fa-sliders-h text-info"></i> Details</h3>
                            <div class="bf-section-view-switcher">
                                <button type="button" class="bf-icon-btn active" data-view="1" title="Stacked"><i class="fas fa-list"></i></button>
                                <button type="button" class="bf-icon-btn" data-view="2" title="Side by side"><i class="fas fa-columns"></i></button>
                            </div>
                        </div>
                        <div class="bf-card-body" id="sectionDetails">
                            <div class="bf-form-group">
                                <label class="bf-form-label"><i class="fas fa-tag"></i> Type</label>
                                <select class="bf-form-control" name="type" id="bookingType" title="Regular=Reserved, Special=Confirmed, Temporary=Short-term">
                                    <option value="1">Regular (Reserved)</option>
                                    <option value="2">Special (Confirmed)</option>
                                    <option value="3">Temporary (Short-term)</option>
                                </select>
                                <small class="bf-form-hint text-muted d-block mt-1">Determines booth status after booking.</small>
                            </div>
                            <div class="bf-form-group">
                                <label class="bf-form-label"><i class="fas fa-calendar-alt"></i> Date & time</label>
                                <input type="datetime-local" class="bf-form-control" name="date_book" id="bookingDateBook" value="{{ now()->format('Y-m-d\TH:i') }}">
                                <div class="bf-quick-date">
                                    <button type="button" class="bf-quick-date-btn" data-set="today">Today</button>
                                    <button type="button" class="bf-quick-date-btn" data-set="now">Now</button>
                                </div>
                            </div>
                            <div class="bf-form-group">
                                <label class="bf-form-label"><i class="fas fa-comment-alt"></i> Notes</label>
                                <textarea class="bf-form-control" name="notes" id="bookingNotes" rows="2" placeholder="Optional notes..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bf-card">
                        <div class="bf-card-header">
                            <h3 class="bf-card-title"><i class="fas fa-check-double text-success"></i> Selected booths</h3>
                        </div>
                        <div class="bf-card-body">
                            <div class="bf-selected-list" id="selectedBoothsList">
                                <p class="text-muted small mb-0 text-center">Select booths from the grid →</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="bf-card" id="tourStep3">
                        <div class="bf-card-header">
                            <h3 class="bf-card-title"><i class="fas fa-th-large text-primary"></i> Booths</h3>
                            <div class="bf-booth-toolbar">
                                <span class="small text-muted font-weight-bold" id="currentFPName">{{ $currentFloorPlan->name ?? 'Select Floor Plan' }}</span>
                                <div class="bf-booth-view-wrap" role="group" aria-label="Booth layout view">
                                    <span class="bf-view-label">View</span>
                                    <div class="bf-booth-view-switcher bf-view-switcher">
                                        <button type="button" class="bf-icon-btn click-animate active" data-mode="default" title="Default (2 per row)" aria-pressed="true" aria-label="Default grid"><i class="fas fa-th-large" aria-hidden="true"></i></button>
                                        <button type="button" class="bf-icon-btn click-animate" data-mode="minimal" title="Compact (3 per row)" aria-pressed="false" aria-label="Compact"><i class="fas fa-th" aria-hidden="true"></i></button>
                                        <button type="button" class="bf-icon-btn click-animate" data-mode="tiny" title="Dense (4 per row)" aria-pressed="false" aria-label="Dense"><i class="fas fa-border-all" aria-hidden="true"></i></button>
                                        <button type="button" class="bf-icon-btn click-animate" data-mode="expand" title="List (1 per row)" aria-pressed="false" aria-label="List"><i class="fas fa-list" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bf-card-body bf-booth-selector" id="boothGridArea">
                            @if($booths->count() > 0)
                                @php
                                    $zones = [];
                                    foreach($booths as $booth) {
                                        $zoneName = preg_replace('/[0-9]+/', '', $booth->booth_number) ?: 'Other';
                                        if (!isset($zones[$zoneName])) $zones[$zoneName] = [];
                                        $zones[$zoneName][] = $booth;
                                    }
                                    ksort($zones);
                                @endphp

                                <div class="bf-booth-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input type="text" class="bf-booth-search" id="boothSearchInput" placeholder="Search booth number or zone..." autocomplete="off">
                                </div>

                                <div class="bf-zone-tabs-wrap" id="bfZoneTabsWrap" aria-hidden="true">
                                    <div class="bf-zone-tabs" id="bfZoneTabs" role="tablist">
                                        @foreach($zones as $zn => $zb)
                                        <button type="button" class="bf-zone-tab" data-zone="{{ $zn }}" role="tab" aria-selected="false">{{ $zn }} ({{ count($zb) }})</button>
                                        @endforeach
                                    </div>
                                    <div class="bf-zone-swipe-hint" aria-hidden="true"><i class="fas fa-arrows-alt-h"></i> Swipe to change zone</div>
                                </div>

                                @foreach($zones as $zoneName => $zoneBooths)
                                    @php
                                        $boothNumbers = array_map(function($b) { return $b->booth_number; }, $zoneBooths);
                                        $boothListStr = implode(', ', $boothNumbers);
                                    @endphp
                                    <div class="bf-zone-group" data-zone="{{ $zoneName }}">
                                        <div class="bf-zone-header">
                                            <div class="bf-zone-header-toggle" role="button" tabindex="0" aria-expanded="false" aria-label="Expand or collapse zone {{ $zoneName }}">
                                                <button type="button" class="bf-zone-name-btn click-animate" title="Show booth IDs in this zone" data-zone="{{ $zoneName }}" data-booths="{{ e($boothListStr) }}" aria-expanded="false" aria-haspopup="true">
                                                    <i class="fas fa-layer-group"></i>
                                                    Zone {{ $zoneName }} <span class="opacity-50">({{ count($zoneBooths) }})</span>
                                                </button>
                                                <span class="bf-zone-chevron" aria-hidden="true"><i class="fas fa-chevron-down"></i></span>
                                            </div>
                                            <div class="bf-zone-actions">
                                                <button type="button" class="bf-zone-btn bf-zone-btn-primary bf-zone-select-all" data-zone="{{ $zoneName }}" title="Select all in zone">Select all</button>
                                                <button type="button" class="bf-zone-btn bf-zone-clear" data-zone="{{ $zoneName }}" title="Clear zone">Clear</button>
                                            </div>
                                        </div>
                                        <div class="bf-booth-grid" data-zone="{{ $zoneName }}" data-total="{{ count($zoneBooths) }}">
                                            @foreach($zoneBooths as $idx => $booth)
                                                <div class="bf-col bf-col-6 bf-mb-2 bf-booth-item-wrapper" 
                                                     data-id="{{ $booth->id }}" 
                                                     data-number="{{ $booth->booth_number }}" 
                                                     data-price="{{ $booth->price }}"
                                                     data-zone="{{ $zoneName }}"
                                                     data-booth-index="{{ $idx }}">
                                                    <div class="bf-booth-item click-animate" onclick="toggleBooth(this)">
                                                        <div class="bf-fp-label">{{ $currentFloorPlan->name ?? '' }}</div>
                                                        <div class="bf-booth-number">{{ $booth->booth_number }}</div>
                                                        <div class="bf-booth-price">${{ number_format($booth->price, 0) }}</div>
                                                        <input type="checkbox" name="booth_ids[]" value="{{ $booth->id }}" class="d-none">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="bf-zone-show-more-wrap" data-zone="{{ $zoneName }}" style="display: none;"></div>
                                    </div>
                                @endforeach
                                <div id="bfZoneBoothsPopoverBackdrop" class="bf-zone-booths-popover-backdrop" style="display: none;" aria-hidden="true" role="presentation"></div>
                                <div id="bfZoneBoothsPopover" class="bf-zone-booths-popover" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="bfZoneBoothsPopoverTitle" aria-hidden="true">
                                    <div class="bf-zone-booths-popover-handle" id="bfZoneBoothsPopoverHandle" aria-hidden="true" role="button" tabindex="-1" title="Tap to close"></div>
                                    <div class="bf-zone-booths-popover-header">
                                        <span class="bf-zone-booths-popover-title" id="bfZoneBoothsPopoverTitle"><i class="fas fa-th-large"></i> <span id="bfZoneBoothsPopoverTitleText">Zone</span></span>
                                        <button type="button" class="bf-zone-booths-popover-close" id="bfZoneBoothsPopoverClose" aria-label="Close">×</button>
                                    </div>
                                    <div class="bf-zone-booths-popover-list" id="bfZoneBoothsPopoverList"></div>
                                </div>
                            @else
                                <div class="bf-empty-state text-center py-5 px-3">
                                    <div class="bf-empty-icon mb-3"><i class="fas fa-store-slash fa-3x text-muted"></i></div>
                                    <h4 class="h5 text-dark mb-1">No booths in this plan</h4>
                                    <p class="text-muted small mb-3">Switch to another floor plan or add booths first.</p>
                                    <button type="button" class="bf-btn-empty-cta click-animate" id="btnFilterFPEmpty">
                                        <i class="fas fa-map"></i> Change floor plan
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

                </div>
                <!-- Sticky Booking summary (desktop); hidden on <992px -->
                <aside class="bf-sidebar-col" aria-label="Booking summary">
                    <div class="bf-sidebar-card" id="bfSidebarCard">
                        <div class="bf-sidebar-header">
                            <i class="fas fa-clipboard-check"></i> Booking summary
                            <span class="bf-sidebar-badge-ready" id="sidebarBadgeReady" aria-hidden="true"><i class="fas fa-check"></i> All set</span>
                        </div>
                        <div class="bf-sidebar-body">
                            <div class="bf-sidebar-row">
                                <span class="k">Client</span>
                                <span class="v" id="reviewClient">—</span>
                            </div>
                            <div class="bf-sidebar-row">
                                <span class="k">Type</span>
                                <span class="v" id="reviewType">Regular</span>
                            </div>
                            <div class="bf-sidebar-row">
                                <span class="k">Date &amp; time</span>
                                <span class="v" id="reviewDateTime">—</span>
                            </div>
                            <div class="bf-sidebar-row">
                                <span class="k">Booths</span>
                                <span class="v" id="reviewBooths">0</span>
                            </div>
                            <div class="bf-sidebar-booths">
                                <div id="sidebarBoothsList"></div>
                            </div>
                            <div class="bf-sidebar-row total">
                                <span class="k">Total</span>
                                <span class="v" id="reviewTotal">$0.00</span>
                            </div>
                            <div class="bf-sidebar-row" id="reviewNotesWrap" style="display: none;">
                                <span class="k">Notes</span>
                                <span class="v text-break" id="reviewNotes"></span>
                            </div>
                            <div class="bf-sidebar-footer-ctx" id="sidebarFooterCtx">
                                <i class="fas fa-map-marker-alt"></i>
                                <span id="sidebarFpName">{{ $currentFloorPlan->name ?? 'No floor plan' }}</span>
                            </div>
                        </div>
                        <div class="bf-sidebar-cta">
                            <div class="bf-cta-row">
                                <button type="reset" class="bf-btn-clear click-animate">Clear</button>
                                <button type="submit" class="bf-btn-submit click-animate" id="btnSubmitSidebar" disabled>
                                    <i class="fas fa-circle-notch bf-spin" id="btnSubmitSidebarSpinner" style="display: none;"></i>
                                    <i class="fas fa-check-circle" id="btnSubmitSidebarIcon"></i>
                                    <span id="btnSubmitSidebarText">Create booking</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- Glass floating summary -->
            <div class="bf-summary-bar pending" id="bfSummaryBar">
                <div class="bf-summary-stats">
                    <div class="bf-summary-stat">
                        <label>Booths</label>
                        <span id="sumCount">0</span>
                    </div>
                    <div class="bf-summary-stat highlight">
                        <label>Total</label>
                        <span id="sumAmount">$0.00</span>
                    </div>
                </div>
                <div class="bf-summary-actions">
                    <span class="bf-summary-status-text" id="bfSummaryStatusText">Complete the form to continue</span>
                    <span class="bf-key-hint d-none d-sm-inline" id="bfKeyHint" aria-hidden="true">Ctrl+Enter to create</span>
                    <button type="reset" class="bf-btn-clear click-animate">Clear</button>
                    <button type="submit" class="bf-btn-submit click-animate" id="btnSubmit" disabled>
                        <i class="fas fa-circle-notch bf-spin" id="btnSubmitSpinner" style="display: none;"></i>
                        <i class="fas fa-check-circle" id="btnSubmitIcon"></i> <span id="btnSubmitText">Create booking</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Highlight navigation tour: overlay + tooltip (shown when Tutorial is run) -->
        <div id="bfTourOverlay" class="bf-tour-overlay" aria-hidden="true" style="display: none;"></div>
        <div id="bfTourTooltip" class="bf-tour-tooltip" role="dialog" aria-live="polite" aria-label="Tutorial step" style="display: none;">
            <div class="bf-tour-tooltip-inner">
                <p class="bf-tour-step-label">Step <span id="bfTourStepNum">1</span> of <span id="bfTourStepTotal">5</span></p>
                <h4 class="bf-tour-step-title" id="bfTourStepTitle">Select a client</h4>
                <p class="bf-tour-step-desc" id="bfTourStepDesc">Search by name or company, or add a new client.</p>
                <div class="bf-tour-actions">
                    <button type="button" class="bf-tour-skip" id="bfTourSkip">Skip tutorial</button>
                    <div class="bf-tour-nav">
                        <button type="button" class="bf-tour-prev click-animate" id="bfTourPrev"><i class="fas fa-arrow-left"></i> Previous</button>
                        <button type="button" class="bf-tour-next click-animate" id="bfTourNext">Next <i class="fas fa-arrow-right"></i></button>
                        <button type="button" class="bf-tour-finish click-animate" id="bfTourFinish" style="display: none;">Finish <i class="fas fa-check"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to top FAB (mobile only): quick jump to top without long scroll -->
        <button type="button" id="bfBackToTop" class="bf-back-to-top" aria-label="Back to top" style="display: none;">
            <i class="fas fa-chevron-up"></i>
        </button>
    </div>
</section>

<!-- FP Picker Modal -->
<div class="modal fade" id="fpPickerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content client-popup-card">
            <div class="modal-body p-4">
                <h5 class="font-weight-bold mb-4">Choose Floor Plan</h5>
                <div class="d-flex flex-column gap-2">
                    @foreach($floorPlans as $fp)
                    <div class="p-3 border rounded-lg d-flex justify-content-between align-items-center click-animate" 
                         style="cursor: pointer;" onclick="selectFP({{ $fp->id }})">
                        <div>
                            <div class="font-weight-bold">{{ $fp->name }}</div>
                            <div class="small text-muted">{{ $fp->event->title ?? 'General' }}</div>
                        </div>
                        <i class="fas fa-chevron-right text-primary"></i>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content client-popup-card">
            <div class="modal-header bg-success text-white py-3 border-0" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-2"></i>New Client</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="createClientForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div id="createClientError" class="alert alert-danger" style="display: none; font-size: 0.8rem;"></div>
                    <div class="bf-form-group">
                        <label class="bf-form-label">Full Name *</label>
                        <input type="text" class="bf-form-control" name="name" required>
                    </div>
                    <div class="bf-form-group">
                        <label class="bf-form-label">Company *</label>
                        <input type="text" class="bf-form-control" name="company" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="bf-form-group">
                                <label class="bf-form-label">Phone *</label>
                                <input type="tel" class="bf-form-control" name="phone_number" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bf-form-group">
                                <label class="bf-form-label">Gender</label>
                                <select class="bf-form-control" name="sex"><option value="1">Male</option><option value="2">Female</option></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-success btn-block rounded-pill font-weight-bold py-2 shadow-sm click-animate" id="createClientSubmitBtn">Save & Select</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tutorial Modal -->
<div class="modal fade" id="bfTutorialModal" tabindex="-1" role="dialog" aria-labelledby="bfTutorialModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content client-popup-card">
            <div class="modal-header py-3 border-0 rounded-top">
                <h5 class="modal-title font-weight-bold" id="bfTutorialModalTitle"><i class="fas fa-play-circle mr-2"></i>Booking tutorial</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted mb-4">Follow these steps to create a booking quickly.</p>
                <div class="bf-quick-guide-step">
                    <span class="num">1</span>
                    <span><strong>Select a client</strong> <span class="tip">— Search by name or company in the client field, or click &quot;New client&quot; to add one.</span></span>
                </div>
                <div class="bf-quick-guide-step">
                    <span class="num">2</span>
                    <span><strong>Set details</strong> <span class="tip">— Choose booking type (Regular / Special / Temporary), date &amp; time. Use &quot;Today&quot; or &quot;Now&quot; for quick entry.</span></span>
                </div>
                <div class="bf-quick-guide-step">
                    <span class="num">3</span>
                    <span><strong>Pick booths</strong> <span class="tip">— Click booth tiles in the grid. Use &quot;Select all&quot; per zone or the search box to find booths. Click &quot;Jump to booths&quot; after selecting a client to scroll to the grid.</span></span>
                </div>
                <div class="bf-quick-guide-step">
                    <span class="num">4</span>
                    <span><strong>Review &amp; create</strong> <span class="tip">— Check the Booking summary (sidebar on desktop or the bar at the bottom). When ready, click &quot;Create booking&quot; or press <kbd>Ctrl+Enter</kbd>.</span></span>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-primary rounded-pill font-weight-bold py-2 px-4 click-animate" data-dismiss="modal" style="background: var(--bf-gradient); border: none;">Got it</button>
            </div>
        </div>
    </div>
</div>

<!-- Booking create settings modal -->
<div class="modal fade" id="bfCreateSettingsModal" tabindex="-1" role="dialog" aria-labelledby="bfCreateSettingsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background: var(--bf-gradient); color: white;">
                <h5 class="modal-title font-weight-bold d-flex align-items-center gap-2" id="bfCreateSettingsModalTitle">
                    <i class="fas fa-cog"></i> Booking create settings
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">Control what appears on this page and lock zoom on mobile.</p>
                <div class="bf-settings-list">
                    <label class="bf-setting-row d-flex align-items-center justify-content-between py-3 border-bottom">
                        <span><i class="fas fa-list-ol text-primary mr-2"></i>Show step progress</span>
                        <input type="checkbox" id="bfSettingShowSteps" class="bf-setting-toggle" data-setting="showSteps" checked>
                    </label>
                    <label class="bf-setting-row d-flex align-items-center justify-content-between py-3 border-bottom">
                        <span><i class="fas fa-question-circle text-info mr-2"></i>Show help &amp; guide</span>
                        <input type="checkbox" id="bfSettingShowHelp" class="bf-setting-toggle" data-setting="showHelp" checked>
                    </label>
                    <label class="bf-setting-row d-flex align-items-center justify-content-between py-3 border-bottom">
                        <span><i class="fas fa-info-circle text-secondary mr-2"></i>Show status line</span>
                        <input type="checkbox" id="bfSettingShowStatus" class="bf-setting-toggle" data-setting="showStatus" checked>
                    </label>
                    <label class="bf-setting-row d-flex align-items-center justify-content-between py-3">
                        <span><i class="fas fa-compress-arrows-alt text-warning mr-2"></i>Lock screen pinch (no zoom)</span>
                        <input type="checkbox" id="bfSettingLockPinch" class="bf-setting-toggle" data-setting="lockPinch">
                    </label>
                </div>
                <p class="small text-muted mt-3 mb-0"><strong>Lock screen pinch</strong> prevents accidental zoom when using this page on touch devices.</p>
            </div>
            <div class="modal-footer border-0 py-3 px-4 bg-light">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary rounded-pill click-animate" id="bfSettingsReset" style="background: var(--bf-gradient); border: none;">Reset to defaults</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    var bfHasFloorPlan = {{ $currentFloorPlan ? 'true' : 'false' }};

    function ensureFloorPlan() {
        if (bfHasFloorPlan) return true;
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Select floor plan first', text: 'Please choose a floor plan to continue.' });
        } else {
            alert('Please select a floor plan first.');
        }
        $('#fpPickerModal').modal('show');
        return false;
    }

    function escapeHtml(s) {
        if (s == null || s === '') return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    var bfCreateSettingsKey = 'bf_create_settings';
    var bfCreateSettingsDefaults = { showSteps: true, showHelp: true, showStatus: true, lockPinch: false };

    function getBookingCreateSettings() {
        try {
            var raw = localStorage.getItem(bfCreateSettingsKey);
            if (raw) {
                var o = JSON.parse(raw);
                return {
                    showSteps: o.showSteps !== undefined ? o.showSteps : bfCreateSettingsDefaults.showSteps,
                    showHelp: o.showHelp !== undefined ? o.showHelp : bfCreateSettingsDefaults.showHelp,
                    showStatus: o.showStatus !== undefined ? o.showStatus : bfCreateSettingsDefaults.showStatus,
                    lockPinch: o.lockPinch !== undefined ? o.lockPinch : bfCreateSettingsDefaults.lockPinch
                };
            }
        } catch (e) {}
        return {
            showSteps: bfCreateSettingsDefaults.showSteps,
            showHelp: bfCreateSettingsDefaults.showHelp,
            showStatus: bfCreateSettingsDefaults.showStatus,
            lockPinch: bfCreateSettingsDefaults.lockPinch
        };
    }

    function saveBookingCreateSettings(s) {
        try { localStorage.setItem(bfCreateSettingsKey, JSON.stringify(s)); } catch (e) {}
    }

    function applyBookingCreateSettings(s) {
        if (!s) s = getBookingCreateSettings();
        $('#bfSteps, #bfStepsHelp').toggle(!!s.showSteps);
        $('#bfQuickGuide').toggle(!!s.showHelp);
        $('#bfSmartStatus').toggle(!!s.showStatus);
        if (s.lockPinch) {
            document.body.classList.add('bf-pinch-lock');
        } else {
            document.body.classList.remove('bf-pinch-lock');
        }
    }

    function bindBookingCreateSettings() {
        var s = getBookingCreateSettings();
        $('#bfSettingShowSteps').prop('checked', s.showSteps);
        $('#bfSettingShowHelp').prop('checked', s.showHelp);
        $('#bfSettingShowStatus').prop('checked', s.showStatus);
        $('#bfSettingLockPinch').prop('checked', s.lockPinch);
    }

    $(document).ready(function() {
        applyBookingCreateSettings();
        bindBookingCreateSettings();

        $('#btnBookingSettings').on('click', function() {
            bindBookingCreateSettings();
            $('#bfCreateSettingsModal').modal('show');
        });
        $('.bf-setting-toggle').on('change', function() {
            var key = $(this).data('setting');
            var val = $(this).prop('checked');
            var s = getBookingCreateSettings();
            s[key] = val;
            saveBookingCreateSettings(s);
            applyBookingCreateSettings(s);
        });
        $('#bfSettingsReset').on('click', function() {
            saveBookingCreateSettings(bfCreateSettingsDefaults);
            bindBookingCreateSettings();
            applyBookingCreateSettings();
            $('#bfCreateSettingsModal').modal('hide');
        });

        // Init view modes
        const savedMode = localStorage.getItem('bf_create_mode') || 'default';
        applyViewMode(savedMode);

        $('.bf-booth-view-switcher.bf-view-switcher .bf-icon-btn[data-mode]').on('click', function() {
            const mode = $(this).data('mode');
            applyViewMode(mode);
            localStorage.setItem('bf_create_mode', mode);
        });

        $('.bf-section-view-switcher .bf-icon-btn').on('click', function() {
            const view = $(this).data('view');
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            
            if (view == 2) {
                $('#sectionDetails').addClass('row').find('.bf-form-group').addClass('col-md-6');
            } else {
                $('#sectionDetails').removeClass('row').find('.bf-form-group').removeClass('col-md-6');
            }
        });

        initClientSearch();
        initBoothSearch();
        initZoneAccordion();
        initZoneActions();
        initZoneBoothsPopover();
        initBackToTop();
        initQuickGuide();
        initTutorial();
        updateSummary();
        updateReview();
        updateSteps();
        updateSmartStatus();
        $('#bookingType, #bookingDateBook').on('change', function() { updateReview(); updateSteps(); updateSmartStatus(); });
        $('#bookingNotes').on('input', updateReview);
        $('#btnFilterFPEmpty').on('click', function() { $('#fpPickerModal').modal('show'); });
        document.addEventListener('click', function(e) {
            var t = e.target.closest && e.target.closest('[data-target="#createClientModal"]');
            if (t && !bfHasFloorPlan) { e.preventDefault(); e.stopPropagation(); ensureFloorPlan(); }
        }, true);
        $(document).on('focus', '#clientSearchInline, #bookingType, #bookingDateBook, #bookingNotes', function() {
            if (!ensureFloorPlan()) $(this).blur();
        });
        $('#bookingForm').on('submit', onBookingSubmit);
        $('#bookingForm').on('reset', onBookingReset);
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                if ($('#bfTourOverlay').is(':visible') || $('#bfTourTooltip').is(':visible')) {
                    e.preventDefault();
                    if (typeof endTour === 'function') endTour();
                    return;
                }
                if ($('#bfZoneBoothsPopover').is(':visible')) {
                    e.preventDefault();
                    $(document).trigger('bf:hideZoneBoothsPopover');
                    return;
                }
                if ($('#createClientModal').hasClass('show') || $('#createClientModal').is(':visible')) {
                    $('#createClientModal').modal('hide');
                    return;
                }
                if ($('#fpPickerModal').hasClass('show') || $('#fpPickerModal').is(':visible')) {
                    $('#fpPickerModal').modal('hide');
                    return;
                }
                if ($('#bfCreateSettingsModal').hasClass('show') || $('#bfCreateSettingsModal').is(':visible')) {
                    $('#bfCreateSettingsModal').modal('hide');
                    return;
                }
                if ($('#bfQuickGuide').hasClass('help-open')) {
                    e.preventDefault();
                    $('#bfQuickGuide').removeClass('help-open');
                    $('#bfBtnHelp').attr('aria-expanded', 'false').removeClass('active');
                    try { sessionStorage.setItem('bf_help_open', 'false'); } catch (err) {}
                    return;
                }
            }
            if (e.ctrlKey && e.key === 'Enter') {
                var $btn = $('#btnSubmit');
                if ($btn.length && $btn.is(':visible') && !$btn.prop('disabled')) {
                    e.preventDefault();
                    $('#bookingForm').submit();
                }
            }
        });
        $('#btnJumpToBooths').on('click', function() {
            if (!ensureFloorPlan()) return;
            var el = document.getElementById('boothGridArea');
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        $('.bf-quick-date-btn').on('click', function() {
            if (!ensureFloorPlan()) return;
            var action = $(this).data('set');
            var el = document.getElementById('bookingDateBook');
            if (!el) return;
            var d = new Date();
            if (action === 'today') {
                d.setHours(9, 0, 0, 0);
            }
            var y = d.getFullYear(), m = String(d.getMonth() + 1).padStart(2, '0'), day = String(d.getDate()).padStart(2, '0');
            var h = String(d.getHours()).padStart(2, '0'), min = String(d.getMinutes()).padStart(2, '0');
            el.value = y + '-' + m + '-' + day + 'T' + h + ':' + min;
            updateReview();
        });
    });

    function initQuickGuide() {
        var $guide = $('#bfQuickGuide');
        var $body = $('#bfQuickGuideBody');
        var $btn = $('#bfBtnHelp');
        var key = 'bf_help_open';
        try {
            if (sessionStorage.getItem(key) === 'true') {
                $guide.addClass('help-open');
                $btn.attr('aria-expanded', 'true').addClass('active');
            }
        } catch (e) {}
        $btn.on('click', function() {
            var open = !$guide.hasClass('help-open');
            $guide.toggleClass('help-open', open);
            $btn.attr('aria-expanded', open ? 'true' : 'false').toggleClass('active', open);
            try { sessionStorage.setItem(key, open ? 'true' : 'false'); } catch (e) {}
        });
    }

    var bfTourSteps = [
        { selector: '#tourStep1', title: 'Select Floor plan', desc: 'Choose the floor plan for this booking. Use the button here to change it.' },
        { selector: '#bfClientBlock', title: 'Select client or create new', desc: 'Search by name or company, or add a new client.' },
        { selector: '#tourStep2', title: 'Set details', desc: 'Choose type, date & time. Use "Today" or "Now" for speed.' },
        { selector: '#tourStep3', title: 'Pick booths', desc: 'Click tiles in the grid. Use "Select all" per zone or search to find booths.' },
        { selector: '#bfSummaryBar', title: 'Create booking', desc: 'Review the summary, then click Create booking or press Ctrl+Enter.' }
    ];
    var bfTourCurrent = 0;

    function initTutorial() {
        $('#bfBtnTutorial').on('click', startTour);
        $('#bfTourSkip').on('click', endTour);
        $('#bfTourPrev').on('click', function() { goTourStep(bfTourCurrent - 1); });
        $('#bfTourNext').on('click', function() { goTourStep(bfTourCurrent + 1); });
        $('#bfTourFinish').on('click', endTour);
    }

    function startTour() {
        bfTourCurrent = 0;
        $('#bfTourOverlay').show().attr('aria-hidden', 'false');
        $('#bfTourTooltip').show().attr('aria-hidden', 'false');
        goTourStep(0);
    }

    function endTour() {
        $('.tour-highlight').removeClass('tour-highlight');
        $('#bfTourOverlay').hide().attr('aria-hidden', 'true');
        $('#bfTourTooltip').hide().attr('aria-hidden', 'true');
    }

    function goTourStep(idx) {
        if (idx < 0 || idx >= bfTourSteps.length) return;
        bfTourCurrent = idx;
        var step = bfTourSteps[idx];
        var $el = $(step.selector);
        $('.tour-highlight').removeClass('tour-highlight');
        if ($el.length) {
            $el[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(function() {
                $el.addClass('tour-highlight');
            }, 320);
        }
        $('#bfTourStepNum').text(idx + 1);
        $('#bfTourStepTotal').text(bfTourSteps.length);
        $('#bfTourStepTitle').text(step.title);
        $('#bfTourStepDesc').text(step.desc);
        $('#bfTourPrev').toggle(idx > 0);
        $('#bfTourNext').toggle(idx < bfTourSteps.length - 1);
        $('#bfTourFinish').toggle(idx === bfTourSteps.length - 1);
        $('#bfTourTooltip').attr('aria-label', 'Tutorial step ' + (idx + 1) + ' of ' + bfTourSteps.length + ': ' + step.title);
    }

    function updateSmartStatus() {
        var hasClient = $('#clientid').val() && $('#selectedClientUI').is(':visible');
        var dateVal = ($('#bookingDateBook').val() || '').trim();
        var count = 0;
        $('.bf-booth-item-wrapper').each(function() { if ($(this).find('input').prop('checked')) count++; });
        var $line = $('#bfSmartStatus'), $text = $('#bfSmartStatusText'), $icon = $line.find('i').first();
        var $bar = $('#bfSummaryBar'), $barText = $('#bfSummaryStatusText');
        $('#bfSidebarCard').toggleClass('ready', hasClient && count > 0);
        $('#bfKeyHint').toggle(hasClient && count > 0);
        if (hasClient && count > 0) {
            $line.removeClass('start pending').addClass('ready');
            $icon.attr('class', 'fas fa-check-circle');
            $text.text('Ready to create booking');
            if ($bar.length) { $bar.removeClass('pending').addClass('ready'); }
            if ($barText.length) $barText.text('Ready to book');
        } else if (hasClient && count === 0 && !dateVal) {
            $line.removeClass('start ready').addClass('pending');
            $icon.attr('class', 'fas fa-calendar-alt');
            $text.text('Set date & time, then pick booths');
            if ($bar.length) { $bar.removeClass('ready').addClass('pending'); }
            if ($barText.length) $barText.text('Set date & time');
        } else if (hasClient) {
            $line.removeClass('start ready').addClass('pending');
            $icon.attr('class', 'fas fa-hand-pointer');
            $text.text('Almost there — pick one or more booths');
            if ($bar.length) { $bar.removeClass('ready').addClass('pending'); }
            if ($barText.length) $barText.text('Select booths');
        } else {
            $line.removeClass('ready pending').addClass('start');
            $icon.attr('class', 'fas fa-info-circle');
            $text.text('Select a client to start your booking');
            if ($bar.length) { $bar.removeClass('ready').addClass('pending'); }
            if ($barText.length) $barText.text('Complete the form to continue');
        }
    }

    function updateSteps() {
        var hasClient = $('#clientid').val() && $('#selectedClientUI').is(':visible');
        var count = 0;
        $('.bf-booth-item-wrapper').each(function() { if ($(this).find('input').prop('checked')) count++; });
        $('#bfSteps .bf-step').removeClass('done active');
        $('#bfSteps .bf-step-divider').removeClass('filled');
        if (hasClient) {
            $('#bfSteps .bf-step[data-step="1"]').addClass('done');
            $('#bfSteps .bf-step-divider[data-divider="1"]').addClass('filled');
        }
        if (hasClient) $('#bfSteps .bf-step[data-step="2"]').addClass('active');
        else $('#bfSteps .bf-step[data-step="1"]').addClass('active');
        if (count > 0) {
            $('#bfSteps .bf-step[data-step="2"]').removeClass('active').addClass('done');
            $('#bfSteps .bf-step-divider[data-divider="2"]').addClass('filled');
            $('#bfSteps .bf-step[data-step="3"]').addClass('active');
        } else if (hasClient) $('#bfSteps .bf-step[data-step="3"]').removeClass('active');
    }

    function initBoothSearch() {
        var $input = $('#boothSearchInput');
        if (!$input.length) return;
        $input.on('input', function() {
            var q = ($(this).val() || '').trim().toLowerCase();
            if (!q) {
                $('.bf-zone-group').removeClass('bf-zone-hidden');
                $('.bf-booth-item-wrapper').removeClass('bf-booth-hidden');
                return;
            }
            $('.bf-zone-group').each(function() {
                var zone = ($(this).data('zone') || '').toString().toLowerCase();
                var zoneMatch = zone.indexOf(q) >= 0;
                var vis = 0;
                $(this).find('.bf-booth-item-wrapper').each(function() {
                    var num = ($(this).data('number') || '').toString().toLowerCase();
                    var match = zoneMatch || num.indexOf(q) >= 0;
                    $(this).toggleClass('bf-booth-hidden', !match);
                    if (match) vis++;
                });
                $(this).toggleClass('bf-zone-hidden', vis === 0);
            });
            if (typeof isZoneTabMode === 'function' && isZoneTabMode() && typeof applyZoneTabMode === 'function') applyZoneTabMode();
        });
    }

    var BF_BOOTHS_PER_PAGE = 20;

    function isZoneTabMode() {
        return window.innerWidth < 768;
    }

    function getZoneTabOrder() {
        var list = [];
        $('#boothGridArea .bf-zone-group:not(.bf-zone-hidden)').each(function() { list.push($(this).data('zone')); });
        return list;
    }

    function setActiveZoneTab(zone) {
        var $area = $('#boothGridArea');
        var $tabs = $('#bfZoneTabs');
        $area.find('.bf-zone-group').removeClass('bf-zone-active');
        $area.find('.bf-zone-group[data-zone="' + zone + '"]').addClass('bf-zone-active');
        $tabs.find('.bf-zone-tab').attr('aria-selected', 'false');
        $tabs.find('.bf-zone-tab[data-zone="' + zone + '"]').attr('aria-selected', 'true');
        $area.find('.bf-zone-tabs-wrap').attr('aria-hidden', 'false');
    }

    function applyZoneTabMode() {
        var $area = $('#boothGridArea');
        var tabMode = isZoneTabMode();
        if (!$area.length) return;
        if (tabMode) {
            $area.addClass('bf-zones-tab-mode');
            var order = getZoneTabOrder();
            var first = order.length ? order[0] : null;
            if (first) setActiveZoneTab(first);
            applyBoothLimit();
        } else {
            $area.removeClass('bf-zones-tab-mode');
            $area.find('.bf-booth-item-wrapper').removeClass('bf-booth-folded');
            $area.find('.bf-zone-show-more-wrap').empty().hide();
            $area.find('.bf-zone-tabs-wrap').attr('aria-hidden', 'true');
        }
    }

    function applyBoothLimit() {
        if (!isZoneTabMode()) return;
        var limit = BF_BOOTHS_PER_PAGE;
        $('#boothGridArea .bf-zone-group').each(function() {
            var $grid = $(this).find('.bf-booth-grid');
            var zone = $(this).data('zone');
            var total = parseInt($grid.attr('data-total') || '0', 10);
            var $wrappers = $grid.find('.bf-booth-item-wrapper');
            var $moreWrap = $(this).find('.bf-zone-show-more-wrap');
            $wrappers.removeClass('bf-booth-folded');
            $moreWrap.empty().hide();
            if (total <= limit) return;
            $wrappers.each(function() {
                var idx = parseInt($(this).data('booth-index') || '0', 10);
                if (idx >= limit) $(this).addClass('bf-booth-folded');
            });
            var hidden = total - limit;
            $moreWrap.html('<button type="button" class="bf-zone-show-more-btn click-animate" data-zone="' + zone + '" data-shown="' + limit + '">Show more (+' + hidden + ')</button>').show();
        });
    }

    function expandBoothLimit(zone, currentlyShown) {
        var limit = BF_BOOTHS_PER_PAGE;
        var nextShown = currentlyShown + limit;
        var $grid = $('#boothGridArea .bf-zone-group[data-zone="' + zone + '"] .bf-booth-grid');
        var total = parseInt($grid.attr('data-total') || '0', 10);
        $grid.find('.bf-booth-item-wrapper').each(function() {
            var idx = parseInt($(this).data('booth-index') || '0', 10);
            if (idx >= currentlyShown && idx < nextShown) $(this).removeClass('bf-booth-folded');
        });
        var $moreWrap = $('#boothGridArea .bf-zone-group[data-zone="' + zone + '"] .bf-zone-show-more-wrap');
        if (nextShown >= total) {
            $moreWrap.empty().hide();
        } else {
            var hidden = total - nextShown;
            $moreWrap.html('<button type="button" class="bf-zone-show-more-btn click-animate" data-zone="' + zone + '" data-shown="' + nextShown + '">Show more (+' + hidden + ')</button>').show();
        }
    }

    function initZoneAccordion() {
        applyZoneTabMode();
        $(window).on('resize', function() { applyZoneTabMode(); });
        $(document).on('click', '.bf-zone-tab', function() {
            var z = $(this).data('zone');
            setActiveZoneTab(z);
        });
        $(document).on('click', '.bf-zone-show-more-btn', function() {
            var z = $(this).data('zone');
            var shown = parseInt($(this).data('shown') || '0', 10);
            expandBoothLimit(z, shown);
        });
        var swipeStart = null;
        $(document).on('touchstart', '#boothGridArea .bf-zones-tab-mode .bf-zone-group.bf-zone-active', function(e) {
            if (e.touches.length === 1) swipeStart = { x: e.touches[0].clientX, y: e.touches[0].clientY };
        });
        $(document).on('touchend', '#boothGridArea .bf-zones-tab-mode .bf-zone-group.bf-zone-active', function(e) {
            if (!swipeStart || !e.changedTouches || !e.changedTouches.length) return;
            var end = e.changedTouches[0];
            var dx = end.clientX - swipeStart.x;
            var dy = end.clientY - swipeStart.y;
            swipeStart = null;
            if (Math.abs(dy) > 50 && Math.abs(dy) > Math.abs(dx)) return;
            if (Math.abs(dx) < 50) return;
            var order = getZoneTabOrder();
            var current = $(this).data('zone');
            var idx = order.indexOf(current);
            if (dx < 0 && idx < order.length - 1) setActiveZoneTab(order[idx + 1]);
            else if (dx > 0 && idx > 0) setActiveZoneTab(order[idx - 1]);
        });
    }

    function initBackToTop() {
        var $btn = $('#bfBackToTop');
        var showAt = 400;
        function updateVisibility() {
            if (window.innerWidth >= 768) { $btn.removeClass('bf-back-to-top-visible'); return; }
            if (window.pageYOffset > showAt) $btn.addClass('bf-back-to-top-visible');
            else $btn.removeClass('bf-back-to-top-visible');
        }
        $(window).on('scroll', updateVisibility);
        updateVisibility();
        $btn.on('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function initZoneBoothsPopover() {
        var $pop = $('#bfZoneBoothsPopover');
        var $backdrop = $('#bfZoneBoothsPopoverBackdrop');
        var $titleText = $('#bfZoneBoothsPopoverTitleText');
        var $list = $('#bfZoneBoothsPopoverList');
        var openZone = null;
        if (!$pop.length) return;

        function hidePopover() {
            $pop.hide().attr('aria-hidden', 'true');
            $backdrop.hide().attr('aria-hidden', 'true');
            $('body').removeClass('bf-zone-popover-open');
            $('.bf-zone-name-btn').attr('aria-expanded', 'false');
            openZone = null;
        }

        function showPopover(zoneName, boothListStr, $btn) {
            var ids = (boothListStr || '').split(',').map(function(s) { return s.trim(); }).filter(Boolean);
            $titleText.text('Booths in Zone ' + zoneName + ' (' + ids.length + ')');
            $list.empty();
            ids.forEach(function(id) {
                $list.append('<span>' + escapeHtml(id) + '</span>');
            });
            var isMobile = window.innerWidth <= 768;
            if (isMobile) {
                $pop.css({ display: 'block', top: 'auto', left: 0, right: 0, bottom: 0 });
                $('body').addClass('bf-zone-popover-open');
            } else {
                var r = $btn[0].getBoundingClientRect();
                var padding = 8;
                $pop.css({
                    top: (r.bottom + padding) + 'px',
                    left: Math.max(padding, Math.min(r.left, window.innerWidth - 320 - padding)) + 'px',
                    right: 'auto',
                    bottom: 'auto',
                    display: 'block'
                });
            }
            $backdrop.show().attr('aria-hidden', 'false');
            $pop.attr('aria-hidden', 'false');
            $btn.attr('aria-expanded', 'true');
            openZone = zoneName;
        }

        $(document).on('bf:hideZoneBoothsPopover', hidePopover);

        $(document).on('click', '.bf-zone-name-btn', function(e) {
            e.stopPropagation();
            var $btn = $(this);
            var zone = $btn.data('zone');
            var booths = $btn.data('booths') || '';
            if (openZone === zone && $pop.is(':visible')) {
                hidePopover();
                return;
            }
            showPopover(zone, booths, $btn);
        });

        $('#bfZoneBoothsPopoverClose').on('click', function(e) { e.preventDefault(); hidePopover(); });
        $('#bfZoneBoothsPopoverHandle').on('click', function(e) { e.preventDefault(); hidePopover(); });
        $backdrop.on('click', function(e) { e.preventDefault(); hidePopover(); });
        if ($backdrop[0]) {
            $backdrop[0].addEventListener('touchend', function(ev) {
                ev.preventDefault();
                hidePopover();
            }, { passive: false });
        }
        var handleEl = document.getElementById('bfZoneBoothsPopoverHandle');
        if (handleEl) {
            handleEl.addEventListener('touchend', function(ev) {
                ev.preventDefault();
                hidePopover();
            }, { passive: false });
        }

        $(document).on('click', function(e) {
            if ($pop.is(':visible') && !$(e.target).closest('#bfZoneBoothsPopover, .bf-zone-name-btn').length) {
                hidePopover();
            }
        });
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $pop.is(':visible')) hidePopover();
        });
    }

    function initZoneActions() {
        $(document).on('click', '.bf-zone-select-all', function() {
            if (!ensureFloorPlan()) return;
            var z = $(this).data('zone');
            $('.bf-booth-item-wrapper[data-zone="' + z + '"]').not('.bf-booth-hidden').each(function() {
                var $chk = $(this).find('input[type="checkbox"]');
                if (!$chk.prop('checked')) { $chk.prop('checked', true); $(this).find('.bf-booth-item').addClass('selected'); }
            });
            updateSummary(); updateReview(); updateSteps();
        });
        $(document).on('click', '.bf-zone-clear', function() {
            if (!ensureFloorPlan()) return;
            var z = $(this).data('zone');
            $('.bf-booth-item-wrapper[data-zone="' + z + '"]').find('input[type="checkbox"]').prop('checked', false).end().find('.bf-booth-item').removeClass('selected');
            updateSummary(); updateReview(); updateSteps();
        });
    }

    function onBookingSubmit(e) {
        if (!ensureFloorPlan()) { e.preventDefault(); return false; }
        if (!$('#clientid').val()) { e.preventDefault(); if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Select a client' }); return false; }
        if ($('.bf-booth-item-wrapper input:checked').length === 0) { e.preventDefault(); if (typeof Swal !== 'undefined') Swal.fire({ icon: 'warning', title: 'Select at least one booth' }); return false; }
        var $form = $('#bookingForm');
        $form.addClass('bf-form-loading');
        $('#btnSubmit, #btnSubmitSidebar').prop('disabled', true);
        $('#btnSubmitSpinner, #btnSubmitSidebarSpinner').show();
        $('#btnSubmitIcon, #btnSubmitSidebarIcon').hide();
        $('#btnSubmitText, #btnSubmitSidebarText').text('Creating…');
        return true;
    }

    function onBookingReset() {
        setTimeout(function() {
            $('#clientid').val('');
            $('#selectedClientUI').hide();
            $('#searchClientUI').show();
            $('#bfClientBlock').removeClass('has-client');
            $('#clientSearchInline').val('');
            $('#inlineClientResults').hide().find('#inlineClientResultsList').empty();
            $('.bf-booth-item-wrapper').find('input[name="booth_ids[]"]').prop('checked', false);
            $('.bf-booth-item').removeClass('selected');
            if (typeof updateSummary === 'function') updateSummary();
            if (typeof updateReview === 'function') updateReview();
            if (typeof updateSteps === 'function') updateSteps();
            if (typeof updateSmartStatus === 'function') updateSmartStatus();
        }, 0);
    }

    function updateReview() {
        var clientName = $('#selectedClientUI').is(':visible') ? ($('#uiClientName').text() || '—') : '—';
        var typeSel = document.getElementById('bookingType');
        var typeText = typeSel ? typeSel.options[typeSel.selectedIndex].text : 'Regular';
        var dateVal = $('#bookingDateBook').val();
        var dateStr = '—';
        if (dateVal) {
            try {
                var d = new Date(dateVal);
                dateStr = d.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }) + ' ' + d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
            } catch (e) { dateStr = dateVal; }
        }
        var count = 0, total = 0;
        $('.bf-booth-item-wrapper').each(function() {
            if ($(this).find('input').prop('checked')) {
                count++;
                total += parseFloat($(this).data('price')) || 0;
            }
        });
        $('#reviewClient').text(clientName);
        $('#reviewType').text(typeText);
        $('#reviewDateTime').text(dateStr);
        $('#reviewBooths').text(count + (count === 1 ? ' booth' : ' booths'));
        $('#reviewTotal').text('$' + total.toLocaleString(undefined, { minimumFractionDigits: 2 }));
        var $list = $('#sidebarBoothsList');
        if ($list.length) {
            if (count === 0) {
                $list.html('<div class="bf-sidebar-booths-empty"><i class="fas fa-th-large"></i> Select booths from the grid →</div>');
            } else {
                $list.empty();
                $('.bf-booth-item-wrapper').each(function() {
                    if ($(this).find('input').prop('checked')) {
                        var id = $(this).data('id');
                        var num = $(this).data('number');
                        $list.append('<div class="item bf-sidebar-booth-item click-animate" data-booth-id="' + id + '" role="button" tabindex="0" title="Show in grid">' + escapeHtml(String(num)) + '</div>');
                    }
                });
            }
        }
        var notes = ($('#bookingNotes').val() || '').trim();
        if (notes) {
            $('#reviewNotesWrap').show();
            $('#reviewNotes').text(notes.length > 80 ? notes.substring(0, 80) + '…' : notes);
        } else {
            $('#reviewNotesWrap').hide();
        }
    }

    function applyViewMode(mode) {
        var $switcher = $('.bf-booth-view-switcher.bf-view-switcher');
        $switcher.find('.bf-icon-btn').removeClass('active').attr('aria-pressed', 'false');
        $switcher.find('.bf-icon-btn[data-mode="' + mode + '"]').addClass('active').attr('aria-pressed', 'true');
        $('#boothGridArea').removeClass('view-mode-default view-mode-minimal view-mode-tiny view-mode-expand')
                          .addClass('view-mode-' + mode);
    }

    function toggleBooth(el) {
        if (!ensureFloorPlan()) return;
        const $el = $(el).parent();
        const checkbox = $el.find('input[type="checkbox"]');
        const isChecked = !checkbox.prop('checked');
        
        checkbox.prop('checked', isChecked);
        if (isChecked) $(el).addClass('selected');
        else $(el).removeClass('selected');
        
        updateSummary();
        updateReview();
        if (typeof updateSteps === 'function') updateSteps();
    }

    function updateSummary() {
        let total = 0;
        let count = 0;
        const list = $('#selectedBoothsList');
        list.empty();

        $('.bf-booth-item-wrapper').each(function() {
            if ($(this).find('input').prop('checked')) {
                const id = $(this).data('id');
                const num = $(this).data('number');
                const price = parseFloat($(this).data('price')) || 0;
                
                total += price;
                count++;
                
                list.append('<span class="bf-tag"><span class="bf-tag-num click-animate" data-booth-id="' + id + '" title="Show in grid">#' + escapeHtml(String(num)) + '</span> <button type="button" class="bf-tag-remove" onclick="removeBooth(' + id + ')" title="Remove" aria-label="Remove booth ' + escapeHtml(String(num)) + '"><i class="fas fa-times"></i></button></span>');
            }
        });

        if (count === 0) list.html('<p class="text-muted small mb-0 text-center">Select booths from the grid →</p>');

        $('#sumCount').text(count);
        $('#sumAmount').text('$' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        var hasClient = !!$('#clientid').val();
        var canSubmit = hasClient && count > 0;
        $('#btnSubmit, #btnSubmitSidebar').prop('disabled', !canSubmit).toggleClass('opacity-50', !canSubmit);
        if (typeof updateReview === 'function') updateReview();
        if (typeof updateSteps === 'function') updateSteps();
        if (typeof updateSmartStatus === 'function') updateSmartStatus();
    }

    window.removeBooth = function(id) {
        $(`.bf-booth-item-wrapper[data-id="${id}"]`).find('input').prop('checked', false).parent().find('.bf-booth-item').removeClass('selected');
        updateSummary();
    };

    window.scrollToBooth = function(id) {
        var $w = $('.bf-booth-item-wrapper[data-id="' + id + '"]');
        if (!$w.length) return;
        $w[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        $w.removeClass('bf-booth-highlight').addClass('bf-booth-highlight');
        window.clearTimeout(window._bfBoothHighlightTimer);
        window._bfBoothHighlightTimer = window.setTimeout(function() {
            $w.removeClass('bf-booth-highlight');
        }, 1800);
    };

    $(document).on('click', '.bf-sidebar-booth-item', function() {
        var id = $(this).data('booth-id');
        if (id != null) scrollToBooth(id);
    });
    $(document).on('keydown', '.bf-sidebar-booth-item', function(e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); scrollToBooth($(this).data('booth-id')); }
    });
    $(document).on('click', '.bf-tag-num', function() {
        var id = $(this).data('booth-id');
        if (id != null) scrollToBooth(id);
    });

    function initClientSearch() {
        let timer;
        $('#clientSearchInline').on('input', function() {
            const q = $(this).val().trim();
            clearTimeout(timer);
            if (q.length < 2) { $('#inlineClientResults').hide(); return; }
            
            timer = setTimeout(() => {
                $.get('{{ route("clients.search") }}', { q: q }, function(res) {
                    const list = $('#inlineClientResultsList');
                    list.empty();
                    if (res && res.length > 0) {
                        res.forEach(c => {
                            const item = $(`<div class="bf-client-result-item click-animate"><div class="name">${escapeHtml(c.company || c.name)}</div><div class="meta">${escapeHtml(c.phone_number || c.email || '')}</div></div>`);
                            item.on('click', function() { if (!ensureFloorPlan()) return; selectClient(c); });
                            list.append(item);
                        });
                        $('#inlineClientResults').show();
                    } else {
                        list.html('<div class="bf-client-result-item"><div class="meta text-center">No clients found</div></div>');
                        $('#inlineClientResults').show();
                    }
                });
            }, 300);
        });

        $(document).on('click', e => { if (!$(e.target).closest('#clientSearchInline, #inlineClientResults').length) $('#inlineClientResults').hide(); });
        $('#btnChangeClient').on('click', function() {
            $('#selectedClientUI').hide();
            $('#searchClientUI').show();
            $('#clientid').val('');
            $('#bfClientBlock').removeClass('has-client');
            if (typeof updateReview === 'function') updateReview();
            if (typeof updateSteps === 'function') updateSteps();
            if (typeof updateSmartStatus === 'function') updateSmartStatus();
            if (typeof updateSummary === 'function') updateSummary();
        });
    }

    function selectClient(c) {
        $('#clientid').val(c.id);
        $('#uiClientName').text(c.company || c.name);
        $('#uiClientDetails').text([c.email, c.phone_number].filter(Boolean).join(' · ') || '—');
        var initial = (c.company || c.name || '—').toString().charAt(0).toUpperCase();
        $('#uiClientInitial').text(initial === ' ' ? '—' : initial);
        $('#searchClientUI').hide();
        $('#selectedClientUI').fadeIn();
        $('#inlineClientResults').hide();
        $('#bfClientBlock').addClass('has-client');
        if (typeof updateReview === 'function') updateReview();
        if (typeof updateSteps === 'function') updateSteps();
        if (typeof updateSmartStatus === 'function') updateSmartStatus();
        if (typeof updateSummary === 'function') updateSummary();
    }

    $('#btnFilterFP').on('click', () => $('#fpPickerModal').modal('show'));
    window.selectFP = id => window.location.href = '{{ route("books.create") }}?floor_plan_id=' + id;

    $('#createClientForm').on('submit', function(e) {
        e.preventDefault();
        if (!ensureFloorPlan()) return false;
        const btn = $('#createClientSubmitBtn');
        const old = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    selectClient(res.client);
                    $('#createClientModal').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Client Created', timer: 1500, showConfirmButton: false });
                }
            },
            error: function(xhr) {
                $('#createClientError').text('Error saving client. Please check fields.').show();
            },
            complete: () => btn.prop('disabled', false).html(old)
        });
    });

</script>
@endpush
