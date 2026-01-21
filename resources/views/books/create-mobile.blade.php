@extends('layouts.app')

@section('title', 'Create Booking')

@push('styles')
<!-- IMMEDIATE OLD UI HIDING - Prevents flash of old content -->
<style>
/* Hide old UI immediately - before any other styles load */
nav.navbar,
.navbar,
.navbar-expand-lg,
.sidebar,
.content-wrapper { 
    display: none !important; 
    visibility: hidden !important;
}

/* CRITICAL: Ensure mobile content is ALWAYS visible */
.modern-mobile-header,
.mobile-booking-container,
.mobile-form-section,
.mobile-action-buttons,
.selected-booths-summary-mobile {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* CRITICAL: Ensure main container is visible */
main.container-fluid,
#main-content,
main {
    padding: 0 !important;
    margin: 0 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    min-height: 100vh !important;
}

/* Override any layout padding that might hide content - ULTRA AGGRESSIVE */
main.container-fluid.py-4,
main.container-fluid,
main#main-content,
main {
    padding: 0 !important;
    margin: 0 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    min-height: 100vh !important;
    width: 100% !important;
    max-width: 100% !important;
    position: relative !important;
}

/* Ensure body and html are visible */
html, body {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 100% !important;
    height: auto !important;
    min-height: 100vh !important;
}

/* Force all mobile elements visible - NO EXCEPTIONS */
.modern-mobile-header *,
.mobile-booking-container *,
.mobile-form-section *,
.mobile-action-buttons * {
    visibility: visible !important;
}

/* Override any Bootstrap or layout styles that might hide content */
.py-4 {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}
</style>

<style>
/* ============================================
   MODERN MOBILE APP BOOKING CREATE PAGE
   Smooth, Fluid, Flexible, and Unique Design
   ============================================ */

* {
    -webkit-tap-highlight-color: transparent;
    -webkit-touch-callout: none;
}

html, body {
    background: #f5f7fa !important;
    min-height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    overflow-x: hidden !important;
    width: 100% !important;
    max-width: 100% !important;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Override any inherited styles */
body * {
    box-sizing: border-box;
}

/* Modern App Background with subtle gradient */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 300px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    z-index: 0;
    opacity: 0.05;
}

/* ============================================
   COMPLETE OLD UI REMOVAL - MOBILE VIEW ONLY
   Aggressive CSS to remove ALL old desktop UI
   ============================================ */

/* Force Mobile View Isolation - Apply to ALL mobile views */
html,
body {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: hidden !important;
}

/* COMPLETELY HIDE ALL DESKTOP/OLD UI ELEMENTS - AGGRESSIVE */
nav,
nav.navbar,
.navbar,
.navbar-expand-lg,
.navbar-brand,
.navbar-nav,
.navbar-toggler,
.navbar-collapse,
.sidebar,
.content-wrapper,
.main-sidebar,
.content-header,
.breadcrumb,
.page-header {
    display: none !important;
    visibility: hidden !important;
    height: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    opacity: 0 !important;
    position: absolute !important;
    left: -9999px !important;
    width: 0 !important;
    overflow: hidden !important;
}

/* Remove ALL padding/margins from main container - BUT KEEP IT VISIBLE */
main,
main.container-fluid,
#main-content {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
    min-height: 100vh !important;
    display: block !important;
    visibility: visible !important;
    position: relative !important;
    opacity: 1 !important;
}

/* Ensure Mobile Container is Visible and Full Width */
.mobile-booking-container {
    display: block !important;
    visibility: visible !important;
    width: 100% !important;
    max-width: 100% !important;
    padding: 16px !important;
    padding-bottom: 120px !important;
    margin: 0 !important;
    position: relative !important;
    z-index: 1 !important;
    opacity: 1 !important;
}

/* Ensure mobile header is visible */
.modern-mobile-header {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    z-index: 1000 !important;
}

/* Hide Bootstrap alerts that might be from old UI */
.alert:not(.mobile-alert):not(.mobile-alert-info) {
    display: none !important;
}

/* Hide old card elements - but keep mobile cards visible */
.card:not(.mobile-form-section):not(.selected-booths-summary-mobile):not(.client-results-dropdown-mobile):not(.modal-content) {
    /* Only hide desktop cards, not mobile ones */
}

/* Hide old form elements - but be careful not to hide mobile elements */
.form-group:not(.mobile-form-group) {
    /* Only hide if it doesn't contain mobile elements */
}

/* Ensure only mobile buttons are visible */
.btn:not(.mobile-btn):not(.app-header-btn):not(.select-client-btn-mobile):not(.change-client-btn-mobile):not(.client-search-btn):not(.select-client-inline-btn):not(.booth-checkbox-mobile) {
    /* Allow modal buttons and specific mobile buttons */
}

/* Progress Step Styles */
.progress-step {
    transition: all 0.3s ease;
}

.progress-step.active {
    background: #667eea !important;
    color: white !important;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.progress-step.completed {
    background: #22c55e !important;
    color: white !important;
}

/* Media query to ensure this only applies on mobile */
@media (min-width: 769px) {
    /* On desktop, show normal elements (but this view shouldn't load on desktop) */
    nav.navbar,
    .navbar {
        display: block !important;
    }
}

/* Modern Mobile Header - iOS/Android Style */
.modern-mobile-header {
    position: sticky !important;
    top: 0 !important;
    z-index: 1000 !important;
    background: #ffffff !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
    padding: 12px 20px !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    width: 100% !important;
}

.modern-mobile-header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modern-mobile-header-title {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.modern-mobile-header-title h1 {
    font-size: 22px;
    font-weight: 600;
    margin: 0;
    color: #1a1a1a;
    letter-spacing: -0.3px;
}

.modern-mobile-header-actions {
    display: flex;
    gap: 8px;
}

.app-header-btn {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
    font-size: 20px;
    transition: all 0.2s ease;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
}

.app-header-btn:active {
    transform: scale(0.92);
    background: rgba(102, 126, 234, 0.1);
}

/* Main Content Container - Modern App Style */
.mobile-booking-container {
    padding: 16px !important;
    padding-bottom: 120px !important;
    max-width: 100% !important;
    position: relative !important;
    z-index: 1 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Safe area support for notched devices */
@supports (padding: max(0px)) {
    .mobile-booking-container {
        padding-bottom: max(120px, env(safe-area-inset-bottom) + 100px);
    }
    
    .mobile-action-buttons {
        padding-bottom: max(12px, env(safe-area-inset-bottom) + 12px);
    }
}

/* Form Section Cards - Modern iOS Card Style */
.mobile-form-section {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 
        0 2px 8px rgba(0, 0, 0, 0.04),
        0 1px 3px rgba(0, 0, 0, 0.08);
    border: 0.5px solid rgba(0, 0, 0, 0.06);
    position: relative;
    overflow: visible;
    transition: all 0.2s ease;
}

.mobile-form-section:active {
    transform: scale(0.99);
    box-shadow: 
        0 1px 4px rgba(0, 0, 0, 0.06),
        0 0.5px 2px rgba(0, 0, 0, 0.1);
}

/* Section completion indicator */
.mobile-form-section.completed {
    border-left: 4px solid #22c55e;
}

.mobile-form-section.completed .mobile-form-section-title::after {
    content: ' ✓';
    color: #22c55e;
    font-weight: 700;
    margin-left: 8px;
}

.mobile-form-section-title {
    font-size: 17px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -0.2px;
}

.mobile-form-section-title i {
    color: #667eea;
    font-size: 20px;
    width: 24px;
    text-align: center;
}

/* Form Controls - Modern iOS Style */
.mobile-form-group {
    margin-bottom: 24px;
}

.mobile-form-group:last-child {
    margin-bottom: 0;
}

.mobile-form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    margin-bottom: 8px;
    letter-spacing: -0.1px;
}

.mobile-form-label .required {
    color: #ef4444;
    margin-left: 2px;
}

.mobile-form-input,
.mobile-form-select {
    width: 100%;
    padding: 16px;
    border-radius: 12px;
    border: 1.5px solid #e5e7eb;
    background: #f9fafb;
    font-size: 16px;
    color: #1a1a1a;
    transition: all 0.2s ease;
    -webkit-appearance: none;
    appearance: none;
    font-weight: 400;
}

.mobile-form-input:focus,
.mobile-form-select:focus {
    outline: none;
    border-color: #667eea;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.mobile-form-input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

.mobile-form-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%236b7280' d='M8 11L3 6h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 16px;
    padding-right: 44px;
}

/* Client Search - Modern App Style */
.client-search-wrapper-mobile {
    position: relative;
    z-index: 100;
}

.client-search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: #f9fafb;
    border-radius: 12px;
    border: 1.5px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.2s ease;
}

.client-search-input-wrapper:focus-within {
    border-color: #667eea;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.client-search-icon {
    padding: 0 16px;
    color: #9ca3af;
    font-size: 18px;
}

.client-search-input {
    flex: 1;
    padding: 16px 0;
    border: none;
    background: transparent;
    font-size: 16px;
    color: #1a1a1a;
    font-weight: 400;
}

.client-search-input:focus {
    outline: none;
}

.client-search-input::placeholder {
    color: #9ca3af;
}

.client-search-btn {
    padding: 16px 20px;
    background: #667eea;
    color: white;
    border: none;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    -webkit-tap-highlight-color: transparent;
}

.client-search-btn:active {
    opacity: 0.85;
    transform: scale(0.98);
}

/* Client Results Dropdown - Modern Bottom Sheet Style */
.client-results-dropdown-mobile {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    z-index: 9999;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.12),
        0 2px 8px rgba(0, 0, 0, 0.08);
    border: 0.5px solid rgba(0, 0, 0, 0.08);
    max-height: 400px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    animation: slideDown 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.client-result-item-mobile {
    padding: 16px;
    border-bottom: 0.5px solid #f3f4f6;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
    -webkit-tap-highlight-color: transparent;
}

.client-result-item-mobile:last-child {
    border-bottom: none;
}

.client-result-item-mobile:active {
    background: #f9fafb;
    transform: scale(0.99);
}

.client-result-content-mobile {
    flex: 1;
}

.client-result-name-mobile {
    font-weight: 600;
    font-size: 15px;
    color: #1a1a1a;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: -0.2px;
}

.client-result-name-mobile i {
    color: #667eea;
    font-size: 16px;
}

.client-result-details-mobile {
    font-size: 13px;
    color: #6b7280;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.client-result-details-mobile i {
    margin-right: 6px;
    width: 14px;
}

.select-client-btn-mobile {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #667eea;
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
    -webkit-tap-highlight-color: transparent;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.select-client-btn-mobile:active {
    transform: scale(0.92);
    box-shadow: 0 1px 4px rgba(102, 126, 234, 0.25);
}

/* Selected Client Card - Modern Success State */
.selected-client-card-mobile {
    background: #f0fdf4;
    border: 1.5px solid #86efac;
    border-radius: 14px;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(34, 197, 94, 0.1);
}

.selected-client-info-mobile {
    flex: 1;
}

.selected-client-name-mobile {
    font-weight: 600;
    font-size: 16px;
    color: #1a1a1a;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.selected-client-name-mobile i {
    color: #22c55e;
    font-size: 18px;
}

.selected-client-details-mobile {
    font-size: 14px;
    color: #6b7280;
}

.change-client-btn-mobile {
    padding: 10px 16px;
    background: #ffffff;
    color: #ef4444;
    border: 1.5px solid #fecaca;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    -webkit-tap-highlight-color: transparent;
}

.change-client-btn-mobile:active {
    transform: scale(0.96);
    background: #fef2f2;
}

/* Booth Selection - Compact & Minimal Design */
.booth-grid-mobile {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 20px;
}

@media (max-width: 360px) {
    .booth-grid-mobile {
        grid-template-columns: repeat(2, 1fr);
    }
}

.booth-card-mobile {
    background: white;
    border: 1.5px solid rgba(102, 126, 234, 0.12);
    border-radius: 12px;
    padding: 10px 8px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    min-height: 90px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.booth-card-mobile::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.booth-card-mobile:active {
    transform: scale(0.96);
    transition: transform 0.1s ease;
}

.booth-card-mobile.selected {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.12) 0%, rgba(118, 75, 162, 0.12) 100%);
    border-color: #667eea;
    border-width: 2px;
    box-shadow: 
        0 0 0 2px rgba(102, 126, 234, 0.15),
        0 4px 12px rgba(102, 126, 234, 0.2);
    transform: translateY(-1px);
}

.booth-card-mobile.selected::before {
    transform: scaleY(1);
    width: 5px;
}

/* Touch feedback for booth cards */
.booth-card-mobile {
    -webkit-tap-highlight-color: rgba(102, 126, 234, 0.2);
    user-select: none;
    -webkit-user-select: none;
}

/* Disabled state for unavailable booths */
.booth-card-mobile.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.booth-checkbox-mobile {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 20px;
    height: 20px;
    accent-color: #667eea;
    cursor: pointer;
    z-index: 2;
    opacity: 0;
    pointer-events: none;
}

/* Custom checkbox indicator - Compact */
.booth-card-mobile.selected::after {
    content: '✓';
    position: absolute;
    top: 6px;
    right: 6px;
    width: 20px;
    height: 20px;
    background: #667eea;
    border-radius: 5px;
    color: white;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
    line-height: 1;
}

.booth-number-mobile {
    font-size: 16px;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 4px;
    letter-spacing: -0.2px;
    line-height: 1.2;
}

.booth-status-mobile {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 6px;
    font-size: 9px;
    font-weight: 600;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.2px;
    line-height: 1.3;
}

.booth-price-mobile {
    font-size: 13px;
    font-weight: 700;
    color: #22c55e;
    margin-top: auto;
    letter-spacing: -0.1px;
    line-height: 1.2;
}

.booth-category-mobile {
    font-size: 10px;
    color: #6b7280;
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 3px;
    line-height: 1.2;
}

.booth-category-mobile i {
    font-size: 9px;
}

/* Booth Search Filter */
.booth-search-filter {
    margin-bottom: 12px;
}

.booth-search-filter input {
    width: 100%;
    padding: 10px 14px;
    padding-left: 40px;
    border: 1.5px solid rgba(102, 126, 234, 0.15);
    border-radius: 10px;
    font-size: 14px;
    background: #f9fafb;
    transition: all 0.2s ease;
}

.booth-search-filter input:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.booth-search-filter .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 14px;
}

.booth-search-filter-wrapper {
    position: relative;
}

/* Category Tabs/Icon Navigation */
.booth-category-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    overflow-x: auto;
    padding-bottom: 8px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.booth-category-tabs::-webkit-scrollbar {
    display: none;
}

.booth-category-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 10px 14px;
    min-width: 80px;
    background: #f9fafb;
    border: 1.5px solid rgba(102, 126, 234, 0.12);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
    position: relative;
}

.booth-category-tab i {
    font-size: 20px;
    color: #667eea;
    transition: all 0.2s ease;
}

.booth-category-tab .category-icon-img {
    width: 24px;
    height: 24px;
    object-fit: cover;
    border-radius: 6px;
}

.booth-category-tab .category-tab-name {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-align: center;
    line-height: 1.2;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.booth-category-tab .category-tab-count {
    font-size: 10px;
    color: #9ca3af;
    font-weight: 500;
}

.booth-category-tab.active {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-color: #667eea;
    border-width: 2px;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
    transform: translateY(-1px);
}

.booth-category-tab.active i {
    color: #667eea;
    transform: scale(1.1);
}

.booth-category-tab.active .category-tab-name {
    color: #667eea;
    font-weight: 700;
}

.booth-category-tab.active .category-tab-count {
    color: #667eea;
}

.booth-category-tab:active {
    transform: scale(0.95);
}

/* Category Content Sections */
.booth-category-content {
    display: none;
    animation: fadeInUp 0.3s ease;
}

.booth-category-content.active {
    display: block;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Selected Booths Summary - Modern Card */
.selected-booths-summary-mobile {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 
        0 2px 8px rgba(0, 0, 0, 0.04),
        0 1px 3px rgba(0, 0, 0, 0.08);
    border: 0.5px solid rgba(0, 0, 0, 0.06);
}

.selected-booths-title-mobile {
    font-size: 17px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: -0.2px;
}

.selected-booths-list-mobile {
    max-height: 200px;
    overflow-y: auto;
    margin-bottom: 16px;
}

.selected-booth-item-mobile {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 14px;
    background: #f9fafb;
    border-radius: 10px;
    margin-bottom: 8px;
    animation: slideInRight 0.25s ease-out;
    transition: all 0.2s ease;
    border: 1px solid #f3f4f6;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.selected-booth-item-mobile:last-child {
    margin-bottom: 0;
}

.selected-booth-info-mobile {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
    color: #1a1a1a;
    font-size: 15px;
}

.selected-booth-info-mobile i {
    color: #667eea;
    font-size: 16px;
}

.selected-booth-price-mobile {
    font-weight: 700;
    color: #22c55e;
    font-size: 16px;
    letter-spacing: -0.2px;
}

.summary-total-mobile {
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
    margin-top: 16px;
}

.summary-total-label-mobile {
    font-weight: 700;
    color: #0f172a;
    font-size: 16px;
}

.summary-total-value-mobile {
    font-weight: 700;
    color: #22c55e;
    font-size: 26px;
    transition: all 0.3s ease;
    letter-spacing: -0.5px;
}

.summary-total-value-mobile.updated {
    animation: pulse 0.5s ease;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Action Buttons - Modern iOS Style Bottom Bar */
.mobile-action-buttons {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 999;
    background: #ffffff;
    border-top: 0.5px solid rgba(0, 0, 0, 0.1);
    padding: 12px 16px;
    padding-bottom: calc(12px + env(safe-area-inset-bottom));
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    display: flex;
    gap: 12px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

.mobile-btn {
    flex: 1;
    padding: 16px;
    border-radius: 12px;
    border: none;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    -webkit-tap-highlight-color: transparent;
    letter-spacing: -0.2px;
}

.mobile-btn:active {
    transform: scale(0.97);
}

.mobile-btn-primary {
    background: #667eea;
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.mobile-btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
    background: #9ca3af !important;
}

.mobile-btn-primary:not(:disabled) {
    position: relative;
    overflow: hidden;
}

.mobile-btn-primary:not(:disabled)::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.mobile-btn-primary:not(:disabled):active::before {
    width: 300px;
    height: 300px;
}

.mobile-btn-primary:not(:disabled):active {
    transform: scale(0.96);
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.25);
}

.mobile-btn-secondary {
    background: #f3f4f6;
    color: #6b7280;
    border: 1.5px solid #e5e7eb;
}

.mobile-btn-secondary:active {
    background: #e5e7eb;
    transform: scale(0.96);
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.loading-overlay.active {
    display: flex;
}

.loading-spinner {
    width: 56px;
    height: 56px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

.loading-text {
    color: white;
    margin-top: 20px;
    font-size: 16px;
    font-weight: 500;
    letter-spacing: -0.2px;
}

/* Alert/Info Cards - Modern Style */
.mobile-alert {
    padding: 16px;
    border-radius: 14px;
    margin-bottom: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.mobile-alert-info {
    background: #eff6ff;
    border: 1.5px solid #bfdbfe;
    color: #1e40af;
}

.mobile-alert i {
    font-size: 20px;
    color: #3b82f6;
    margin-top: 2px;
}

/* Empty State - Modern Style */
.empty-state-mobile {
    text-align: center;
    padding: 48px 20px;
    color: #6b7280;
}

.empty-state-mobile i {
    font-size: 48px;
    color: #d1d5db;
    margin-bottom: 16px;
    opacity: 0.6;
}

.empty-state-mobile p {
    font-size: 15px;
    font-weight: 500;
    margin: 0;
    color: #9ca3af;
}

/* Loading State - Modern Style */
.loading-state-mobile {
    text-align: center;
    padding: 24px;
    color: #667eea;
}

.loading-state-mobile i {
    font-size: 24px;
    animation: spin 1s linear infinite;
    opacity: 0.8;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Scrollbar Styling - Modern Thin Style */
::-webkit-scrollbar {
    width: 4px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.mobile-form-section {
    animation: fadeInUp 0.3s cubic-bezier(0.4, 0, 0.2, 1) backwards;
}

.mobile-form-section:nth-child(1) { animation-delay: 0.03s; }
.mobile-form-section:nth-child(2) { animation-delay: 0.06s; }
.mobile-form-section:nth-child(3) { animation-delay: 0.09s; }
.mobile-form-section:nth-child(4) { animation-delay: 0.12s; }
.mobile-form-section:nth-child(5) { animation-delay: 0.15s; }

/* Mobile Modal Enhancements */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0;
        max-width: 100%;
        height: 100%;
    }
    
    .modal-content {
        border-radius: 0;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: none;
    }
    
    .modal-header {
        border-radius: 0;
        flex-shrink: 0;
    }
    
    .modal-body {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .modal-footer {
        flex-shrink: 0;
        border-radius: 0;
    }
    
    /* Search Client Modal - Mobile Optimized */
    #searchClientModal .modal-dialog {
        max-width: 100%;
    }
    
    #searchClientModal .modal-content {
        height: 100vh;
        max-height: 100vh;
    }
    
    /* Create Client Modal - Mobile Optimized */
    #createClientModal .modal-dialog {
        max-width: 100%;
    }
    
    #createClientModal .modal-content {
        height: 100vh;
        max-height: 100vh;
    }
    
    /* Better form controls in modals */
    .modal-body .form-control,
    .modal-body .form-select {
        font-size: 16px; /* Prevents zoom on iOS */
        padding: 12px 16px;
    }
    
    /* Better button spacing in modals */
    .modal-footer .btn {
        padding: 12px 20px;
        font-size: 16px;
    }
}

/* Modal Backdrop Enhancement */
.modal-backdrop {
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* Client Search Results in Modal - Mobile */
@media (max-width: 768px) {
    #clientSearchResultsList {
        max-height: calc(100vh - 300px);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .client-search-result {
        padding: 14px;
        margin-bottom: 10px;
    }
    
    .select-client-btn {
        padding: 10px 16px;
        font-size: 14px;
    }
}

/* Create Client Form - Mobile Optimizations */
@media (max-width: 768px) {
    #createClientForm .row {
        margin-left: 0;
        margin-right: 0;
    }
    
    #createClientForm .col-md-6,
    #createClientForm .col-md-12 {
        padding-left: 0;
        padding-right: 0;
        margin-bottom: 16px;
    }
    
    #createClientForm .form-group {
        margin-bottom: 20px;
    }
    
    #createClientForm h6 {
        font-size: 16px;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid rgba(102, 126, 234, 0.1);
    }
}
</style>
@endpush

@section('content')
<!-- Modern Mobile Header -->
<div class="modern-mobile-header" id="mobileHeader" style="display: block !important; visibility: visible !important; opacity: 1 !important; background: #ffffff !important;">
    <div class="modern-mobile-header-content">
        <div class="modern-mobile-header-title">
            <a href="{{ route('books.index') }}" class="app-header-btn" aria-label="Go back to bookings">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>New Booking</h1>
        </div>
        <div class="modern-mobile-header-actions">
            @if(isset($currentFloorPlan) && $currentFloorPlan)
            <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $currentFloorPlan->id]) }}" class="app-header-btn" title="View Floor Plan" aria-label="View floor plan canvas">
                <i class="fas fa-map-marked-alt"></i>
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="mobile-booking-container" style="display: block !important; visibility: visible !important; opacity: 1 !important; padding: 16px !important; padding-bottom: 120px !important;">
    <!-- Progress Indicator -->
    <div style="background: #ffffff; border-radius: 16px; padding: 16px; margin-bottom: 16px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); border: 0.5px solid rgba(0, 0, 0, 0.06);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
            <div style="flex: 1; display: flex; align-items: center; gap: 8px;">
                <div id="progressStep1" class="progress-step" style="width: 32px; height: 32px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                    <i class="fas fa-user"></i>
                </div>
                <div style="flex: 1; height: 2px; background: #e5e7eb; border-radius: 1px; margin: 0 8px;"></div>
                <div id="progressStep2" class="progress-step" style="width: 32px; height: 32px; border-radius: 50%; background: #e5e7eb; color: #9ca3af; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                    <i class="fas fa-calendar"></i>
                </div>
                <div style="flex: 1; height: 2px; background: #e5e7eb; border-radius: 1px; margin: 0 8px;"></div>
                <div id="progressStep3" class="progress-step" style="width: 32px; height: 32px; border-radius: 50%; background: #e5e7eb; color: #9ca3af; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                    <i class="fas fa-cube"></i>
                </div>
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; font-weight: 500;">
            <span>Client</span>
            <span>Details</span>
            <span>Booths</span>
        </div>
    </div>

    <form action="{{ route('books.store') }}" method="POST" id="bookingForm">
        @csrf
        <input type="hidden" id="clientid" name="clientid" value="{{ old('clientid') }}" required>

        @if(isset($currentFloorPlan) && $currentFloorPlan)
        <div class="mobile-alert mobile-alert-info" style="margin-bottom: 16px;">
            <i class="fas fa-map"></i>
            <div style="flex: 1;">
                <div style="font-weight: 600; color: #1e40af; margin-bottom: 4px; font-size: 14px;">Floor Plan: {{ $currentFloorPlan->name }}</div>
                @if($currentFloorPlan->event)
                <div style="font-size: 13px; color: #3b82f6; margin-bottom: 8px;">{{ $currentFloorPlan->event->title }}</div>
                @endif
                <a href="{{ route('books.create') }}" style="color: #3b82f6; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                    <i class="fas fa-times" style="font-size: 11px;"></i> Clear Filter
                </a>
            </div>
        </div>
        @endif

        <!-- Floor Plan Selection -->
        @if(isset($floorPlans) && $floorPlans->count() > 0)
        <div class="mobile-form-section">
            <div class="mobile-form-section-title">
                <i class="fas fa-filter"></i>
                <span>Filter by Floor Plan</span>
            </div>
            <div class="mobile-form-group">
                <label for="floor_plan_filter" class="mobile-form-label">Select Floor Plan</label>
                <select class="mobile-form-select" id="floor_plan_filter" name="floor_plan_filter" onchange="filterByFloorPlan(this.value)">
                    <option value="">All Floor Plans</option>
                    @foreach($floorPlans as $fp)
                        <option value="{{ $fp->id }}" {{ (isset($floorPlanId) && $floorPlanId == $fp->id) ? 'selected' : '' }}>
                            {{ $fp->name }}
                            @if($fp->is_default) (Default) @endif
                            @if($fp->event) - {{ $fp->event->title }} @endif
                        </option>
                    @endforeach
                </select>
                <small style="display: block; color: #9ca3af; font-size: 12px; margin-top: 6px;">
                    <i class="fas fa-info-circle" style="font-size: 11px; margin-right: 4px;"></i>Filter booths by specific floor plan
                </small>
            </div>
        </div>
        @endif

        <!-- Client Selection -->
        <div class="mobile-form-section" id="clientSection">
            <div class="mobile-form-section-title">
                <i class="fas fa-user-circle"></i>
                <span>Select Client</span>
            </div>

            <!-- Selected Client Display -->
            <div id="selectedClientInfo" style="display: none;">
                <div class="selected-client-card-mobile">
                    <div class="selected-client-info-mobile">
                        <div class="selected-client-name-mobile">
                            <i class="fas fa-check-circle"></i>
                            <span id="selectedClientName"></span>
                        </div>
                        <div class="selected-client-details-mobile" id="selectedClientDetails"></div>
                    </div>
                    <button type="button" class="change-client-btn-mobile" id="btnClearClient">
                        <i class="fas fa-times"></i> Change
                    </button>
                </div>
            </div>

            <!-- Client Search -->
            <div id="clientSearchContainer">
                <div class="mobile-form-group">
                    <label for="clientSearchInline" class="mobile-form-label">
                        Search Client <span class="required">*</span>
                    </label>
                    <div class="client-search-wrapper-mobile">
                        <div class="client-search-input-wrapper">
                            <div class="client-search-icon">
                                <i class="fas fa-search" id="searchIcon"></i>
                            </div>
                            <input type="text" 
                                   class="client-search-input" 
                                   id="clientSearchInline" 
                                   placeholder="Type client name, company, email, or phone..." 
                                   autocomplete="off">
                            <button type="button" class="client-search-btn" id="btnSearchSelectClient" data-toggle="modal" data-target="#searchClientModal">
                                Search
                            </button>
                        </div>
                        
                        <!-- Inline Search Results Dropdown -->
                        <div id="inlineClientResults" class="client-results-dropdown-mobile" style="display: none;">
                            <div id="inlineClientResultsList"></div>
                        </div>
                    </div>
            @error('clientid')
                <div style="color: #ef4444; font-size: 13px; margin-top: 8px; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 12px;"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px; flex-wrap: wrap; gap: 8px;">
                        <small style="color: #9ca3af; font-size: 13px; flex: 1; min-width: 200px;">
                            <i class="fas fa-info-circle" style="margin-right: 4px;"></i> Start typing to see suggestions
                        </small>
                        <button type="button" class="mobile-btn mobile-btn-secondary" style="padding: 12px 18px; font-size: 14px; font-weight: 600; white-space: nowrap;" data-toggle="modal" data-target="#createClientModal">
                            <i class="fas fa-plus" style="margin-right: 6px;"></i> New Client
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="mobile-form-section">
            <div class="mobile-form-section-title">
                <i class="fas fa-calendar-alt"></i>
                <span>Booking Details</span>
            </div>
            <div class="mobile-form-group">
                <label for="date_book" class="mobile-form-label">
                    Booking Date & Time <span class="required">*</span>
                </label>
                <input type="datetime-local" 
                       class="mobile-form-input" 
                       id="date_book" 
                       name="date_book" 
                       value="{{ old('date_book', now()->format('Y-m-d\TH:i')) }}" 
                       required
                       min="{{ now()->format('Y-m-d\TH:i') }}">
                <small style="display: block; color: #9ca3af; font-size: 12px; margin-top: 6px;">
                    <i class="fas fa-info-circle" style="font-size: 11px; margin-right: 4px;"></i>Select date and time for this booking
                </small>
                @error('date_book')
                    <div style="color: #ef4444; font-size: 13px; margin-top: 8px; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-exclamation-circle" style="font-size: 12px;"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>
            <div class="mobile-form-group">
                <label for="type" class="mobile-form-label">Booking Type</label>
                <select class="mobile-form-select" id="type" name="type">
                    <option value="1" {{ old('type', 1) == 1 ? 'selected' : '' }}>Regular Booking</option>
                    <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>Special Booking</option>
                    <option value="3" {{ old('type') == 3 ? 'selected' : '' }}>Temporary Booking</option>
                </select>
                <small style="display: block; color: #9ca3af; font-size: 12px; margin-top: 6px;">
                    <i class="fas fa-info-circle" style="font-size: 11px; margin-right: 4px;"></i>Select the type of booking
                </small>
                @error('type')
                    <div style="color: #ef4444; font-size: 13px; margin-top: 8px; display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-exclamation-circle" style="font-size: 12px;"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>
        </div>

        <!-- Booth Selection -->
        <div class="mobile-form-section" id="boothSection">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                <div class="mobile-form-section-title" style="margin: 0; flex: 1; min-width: 150px;">
                    <i class="fas fa-cube"></i>
                    <span>Select Booths <span class="required">*</span></span>
                </div>
                <div style="display: flex; gap: 6px;">
                    <button type="button" class="mobile-btn mobile-btn-secondary" style="padding: 8px 12px; font-size: 13px; font-weight: 600;" onclick="selectAllBooths()">
                        <i class="fas fa-check-double" style="margin-right: 4px; font-size: 11px;"></i> All
                    </button>
                    <button type="button" class="mobile-btn mobile-btn-secondary" style="padding: 8px 12px; font-size: 13px; font-weight: 600;" onclick="clearSelection()">
                        <i class="fas fa-times" style="margin-right: 4px; font-size: 11px;"></i> Clear
                    </button>
                </div>
            </div>

            @if(isset($boothsByCategory) && count($boothsByCategory) > 0)
            <!-- Booth Search Filter -->
            <div class="booth-search-filter">
                <div class="booth-search-filter-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           id="boothSearchInput" 
                           placeholder="Search by booth number, category, or status..." 
                           autocomplete="off"
                           onkeyup="filterBooths(this.value)">
                </div>
            </div>
            
            <!-- Category Tabs/Icon Navigation -->
            <div class="booth-category-tabs" id="boothCategoryTabs">
                @foreach($boothsByCategory as $categoryId => $categoryData)
                <button type="button" 
                        class="booth-category-tab {{ $loop->first ? 'active' : '' }}" 
                        data-category-id="{{ $categoryId }}"
                        onclick="switchBoothCategory('{{ $categoryId }}')">
                    @if($categoryId === 'uncategorized')
                        <i class="fas fa-th"></i>
                    @elseif($categoryData['category']->avatar)
                        <img src="{{ asset('storage/' . $categoryData['category']->avatar) }}" alt="{{ $categoryData['category']->name }}" class="category-icon-img">
                    @else
                        <i class="fas fa-folder"></i>
                    @endif
                    <span class="category-tab-name">{{ $categoryData['category']->name }}</span>
                    <span class="category-tab-count">({{ $categoryData['booths']->count() }})</span>
                </button>
                @endforeach
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
                <small style="color: #9ca3af; font-size: 11px; display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-info-circle" style="font-size: 10px;"></i>
                    <span id="boothCount">{{ $booths->count() }}</span> booths available
                </small>
                <small style="color: #9ca3af; font-size: 11px; display: none;" id="filteredBoothCount">
                    <span id="filteredCount">0</span> matching booths
                </small>
            </div>
            
            <!-- Booth Grids by Category -->
            @foreach($boothsByCategory as $categoryId => $categoryData)
            <div class="booth-category-content {{ $loop->first ? 'active' : '' }}" 
                 id="categoryContent_{{ $categoryId }}" 
                 data-category-id="{{ $categoryId }}">
                <div class="booth-grid-mobile" id="boothSelector_{{ $categoryId }}">
                    @foreach($categoryData['booths'] as $booth)
                    <div class="booth-card-mobile {{ in_array($booth->id, old('booth_ids', [])) ? 'selected' : '' }}" 
                         data-booth-id="{{ $booth->id }}" 
                         data-price="{{ $booth->price }}"
                         data-booth-number="{{ strtolower($booth->booth_number) }}"
                         data-category="{{ $booth->category ? strtolower($booth->category->name) : '' }}"
                         data-status="{{ strtolower($booth->getStatusLabel()) }}"
                         onclick="toggleBooth(this, {{ $booth->id }})">
                        <input type="checkbox" 
                               name="booth_ids[]" 
                               value="{{ $booth->id }}" 
                               class="booth-checkbox-mobile"
                               {{ in_array($booth->id, old('booth_ids', [])) ? 'checked' : '' }}
                               onchange="updateSelection()"
                               style="pointer-events: none;">
                        <div>
                            <div class="booth-number-mobile">{{ $booth->booth_number }}</div>
                            <span class="booth-status-mobile" style="background: linear-gradient(135deg, {{ $booth->getStatusColor() == 'success' ? '#1cc88a' : ($booth->getStatusColor() == 'warning' ? '#f6c23e' : ($booth->getStatusColor() == 'danger' ? '#e74a3b' : '#36b9cc')) }} 0%, {{ $booth->getStatusColor() == 'success' ? '#17a673' : ($booth->getStatusColor() == 'warning' ? '#dda20a' : ($booth->getStatusColor() == 'danger' ? '#c23321' : '#2c9faf')) }} 100%); color: white;">
                                {{ $booth->getStatusLabel() }}
                            </span>
                        </div>
                        <div class="booth-price-mobile">${{ number_format($booth->price, 2) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            @elseif($booths->count() > 0)
            <!-- Fallback: If no categories, show all booths -->
            <div class="booth-search-filter">
                <div class="booth-search-filter-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           id="boothSearchInput" 
                           placeholder="Search by booth number, category, or status..." 
                           autocomplete="off"
                           onkeyup="filterBooths(this.value)">
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
                <small style="color: #9ca3af; font-size: 11px; display: flex; align-items: center; gap: 4px;">
                    <i class="fas fa-info-circle" style="font-size: 10px;"></i>
                    <span id="boothCount">{{ $booths->count() }}</span> booths available
                </small>
            </div>
            
            <div class="booth-grid-mobile" id="boothSelector">
                @foreach($booths as $booth)
                <div class="booth-card-mobile {{ in_array($booth->id, old('booth_ids', [])) ? 'selected' : '' }}" 
                     data-booth-id="{{ $booth->id }}" 
                     data-price="{{ $booth->price }}"
                     data-booth-number="{{ strtolower($booth->booth_number) }}"
                     data-category="{{ $booth->category ? strtolower($booth->category->name) : '' }}"
                     data-status="{{ strtolower($booth->getStatusLabel()) }}"
                     onclick="toggleBooth(this, {{ $booth->id }})">
                    <input type="checkbox" 
                           name="booth_ids[]" 
                           value="{{ $booth->id }}" 
                           class="booth-checkbox-mobile"
                           {{ in_array($booth->id, old('booth_ids', [])) ? 'checked' : '' }}
                           onchange="updateSelection()"
                           style="pointer-events: none;">
                    <div>
                        <div class="booth-number-mobile">{{ $booth->booth_number }}</div>
                        <span class="booth-status-mobile" style="background: linear-gradient(135deg, {{ $booth->getStatusColor() == 'success' ? '#1cc88a' : ($booth->getStatusColor() == 'warning' ? '#f6c23e' : ($booth->getStatusColor() == 'danger' ? '#e74a3b' : '#36b9cc')) }} 0%, {{ $booth->getStatusColor() == 'success' ? '#17a673' : ($booth->getStatusColor() == 'warning' ? '#dda20a' : ($booth->getStatusColor() == 'danger' ? '#c23321' : '#2c9faf')) }} 100%); color: white;">
                            {{ $booth->getStatusLabel() }}
                        </span>
                    </div>
                    <div class="booth-price-mobile">${{ number_format($booth->price, 2) }}</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state-mobile" style="padding: 32px 20px;">
                <i class="fas fa-inbox" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                <p style="font-size: 15px; font-weight: 500; color: #6b7280; margin-bottom: 8px;">No available booths found</p>
                <p style="font-size: 13px; color: #9ca3af;">Try selecting a different floor plan or check back later</p>
            </div>
            @endif
            @error('booth_ids')
                <div style="color: #ef4444; font-size: 13px; margin-top: 12px; display: flex; align-items: center; gap: 6px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 12px;"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Selected Booths Summary -->
        <div class="selected-booths-summary-mobile">
            <div class="selected-booths-title-mobile">
                <i class="fas fa-list"></i>
                <span>Selected Booths</span>
            </div>
            <div id="selectedBoothsList" class="selected-booths-list-mobile">
                <div class="empty-state-mobile" style="padding: 20px;">
                    <p style="font-size: 14px; color: #9ca3af;">No booths selected</p>
                </div>
            </div>
            <div class="summary-total-mobile" style="padding-top: 16px; border-top: 1px solid #f3f4f6; margin-top: 16px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <div class="summary-total-label-mobile" style="display: flex; align-items: center; gap: 6px;">
                        <i class="fas fa-cube" style="font-size: 14px; color: #6b7280;"></i> 
                        <span>Total Booths:</span>
                    </div>
                    <span id="totalBooths" style="font-weight: 700; font-size: 18px; color: #667eea;">0</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="summary-total-label-mobile" style="font-size: 17px;">
                        Total Amount:
                    </div>
                    <div class="summary-total-value-mobile">
                        $<span id="totalAmount">0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Action Buttons (Fixed Bottom) - Modern iOS Style -->
<div class="mobile-action-buttons">
    <button type="button" class="mobile-btn mobile-btn-secondary" onclick="window.location.href='{{ route('books.index') }}'">
        <i class="fas fa-times" style="margin-right: 6px;"></i> Cancel
    </button>
    <button type="submit" form="bookingForm" class="mobile-btn mobile-btn-primary" id="submitBtn" disabled>
        <i class="fas fa-check" style="margin-right: 6px;"></i> Create Booking
    </button>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
    <div class="loading-text">Creating Booking...</div>
</div>

<!-- Search & Select Client Modal -->
<div class="modal fade" id="searchClientModal" tabindex="-1" role="dialog" aria-labelledby="searchClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern">
                <h5 class="modal-title" id="searchClientModalLabel">
                    <i class="fas fa-search mr-2"></i>Search & Select Client
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="form-group">
                    <label for="clientSearchInput" class="font-weight-bold mb-2">
                        <i class="fas fa-search mr-1"></i> Search Client
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control form-control-modern" 
                               id="clientSearchInput" 
                               placeholder="Type to search by name, company, email, or phone number..." 
                               autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-modern btn-modern-primary" id="btnSearchClient">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-modern" id="btnClearClientSearch" style="background: #6c757d; color: white; display: none;">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted mt-2"><i class="fas fa-info-circle mr-1"></i>Type at least 2 characters to search for existing clients</small>
                </div>
                
                <div id="clientSearchResults" class="mt-4" style="display: none;">
                    <h6 class="mb-3 font-weight-bold"><i class="fas fa-list mr-1"></i>Search Results</h6>
                    <div id="clientSearchResultsList" style="max-height: 450px; overflow-y: auto; padding: 0.75rem;"></div>
                </div>
                
                <div id="noClientResults" class="alert alert-modern alert-modern-info mt-4 text-center" style="display: none;">
                    <i class="fas fa-info-circle mr-2" style="font-size: 1.5rem;"></i>
                    <p class="mb-0 mt-2"><strong>No clients found.</strong> You can create a new client using the "New Client" button.</p>
                </div>
            </div>
            <div class="modal-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
                <button type="button" class="btn btn-modern" style="background: #6c757d; color: white;" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" role="dialog" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createClientModalLabel">
                    <i class="fas fa-user-plus mr-2"></i>Create New Client
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createClientForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="createClientError" class="alert alert-danger" style="display: none;"></div>
                    
                    <!-- Basic Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-user mr-2"></i>Basic Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="modal_name" name="name" placeholder="Enter client full name">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_sex" class="form-label">Gender</label>
                                <select class="form-control" id="modal_sex" name="sex">
                                    <option value="">Select Gender...</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="3">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-building mr-2"></i>Company Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_company" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="modal_company" name="company" placeholder="Enter company name">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_company_name_khmer" class="form-label">Company Name (Khmer)</label>
                                <input type="text" class="form-control" id="modal_company_name_khmer" name="company_name_khmer" placeholder="Enter company name in Khmer">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_position" class="form-label">Position/Title</label>
                                <input type="text" class="form-control" id="modal_position" name="position" placeholder="Enter position or title">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-phone mr-2"></i>Contact Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="modal_phone_number" name="phone_number" placeholder="Enter phone number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_phone_1" class="form-label">Phone 1</label>
                                <input type="text" class="form-control" id="modal_phone_1" name="phone_1" placeholder="Enter primary phone number">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_phone_2" class="form-label">Phone 2</label>
                                <input type="text" class="form-control" id="modal_phone_2" name="phone_2" placeholder="Enter secondary phone number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="modal_email" name="email" placeholder="Enter email address">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_email_1" class="form-label">Email 1</label>
                                <input type="email" class="form-control" id="modal_email_1" name="email_1" placeholder="Enter primary email address">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_email_2" class="form-label">Email 2</label>
                                <input type="email" class="form-control" id="modal_email_2" name="email_2" placeholder="Enter secondary email address">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_address" class="form-label">Address</label>
                                <textarea class="form-control" id="modal_address" name="address" rows="2" placeholder="Enter complete address (street, city, country)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-info-circle mr-2"></i>Additional Information (Optional)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_tax_id" class="form-label">Tax ID / Business Registration Number</label>
                                <input type="text" class="form-control" id="modal_tax_id" name="tax_id" placeholder="Enter tax ID or business registration number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="modal_website" name="website" placeholder="https://example.com">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="modal_notes" name="notes" rows="2" placeholder="Enter any additional information or notes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="createClientSubmitBtn">
                        <i class="fas fa-save mr-1"></i>Create Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// IMMEDIATE OLD UI REMOVAL - Run before page renders
(function() {
    'use strict';
    
    // Remove old UI elements immediately
    function removeOldUI() {
        const selectors = [
            'nav.navbar',
            '.navbar',
            '.navbar-expand-lg',
            '.navbar-brand',
            '.navbar-nav',
            '.navbar-toggler',
            '.sidebar',
            '.content-wrapper',
            '.main-sidebar',
            '.content-header',
            '.breadcrumb',
            '.page-header'
        ];
        
        selectors.forEach(function(selector) {
            try {
                const elements = document.querySelectorAll(selector);
                elements.forEach(function(el) {
                    if (el && !el.closest('.mobile-booking-container') && !el.closest('.modern-mobile-header')) {
                        el.remove(); // Completely remove from DOM
                    }
                });
            } catch(e) {}
        });
        
        // Clean up main container - BUT KEEP IT VISIBLE
        const main = document.querySelector('main.container-fluid, #main-content, main');
        if (main) {
            main.style.cssText = 'padding: 0 !important; margin: 0 !important; max-width: 100% !important; width: 100% !important; display: block !important; visibility: visible !important; opacity: 1 !important; position: relative !important;';
        }
        
        // Ensure mobile container is visible
        const mobileContainer = document.querySelector('.mobile-booking-container');
        if (mobileContainer) {
            mobileContainer.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; padding: 16px !important; padding-bottom: 120px !important;';
        }
        
        // Ensure mobile header is visible
        const mobileHeader = document.querySelector('.modern-mobile-header');
        if (mobileHeader) {
            mobileHeader.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
        }
    }
    
    // Run immediately
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', removeOldUI);
    } else {
        removeOldUI();
    }
    
    // Also run after a short delay to catch any dynamically loaded elements
    setTimeout(removeOldUI, 100);
    setTimeout(removeOldUI, 500);
    
    // Force visibility check
    setTimeout(function() {
        const mobileContainer = document.querySelector('.mobile-booking-container');
        const mobileHeader = document.querySelector('.modern-mobile-header');
        if (mobileContainer) {
            mobileContainer.style.display = 'block';
            mobileContainer.style.visibility = 'visible';
            mobileContainer.style.opacity = '1';
        }
        if (mobileHeader) {
            mobileHeader.style.display = 'block';
            mobileHeader.style.visibility = 'visible';
            mobileHeader.style.opacity = '1';
        }
    }, 50);
})();

// Mobile View Verification & Enhancement
(function() {
    const screenWidth = window.innerWidth || screen.width;
    const isMobile = screenWidth <= 768;
    
    // CRITICAL: Force visibility of mobile elements immediately
    function forceMobileVisibility() {
        const mobileHeader = document.querySelector('.modern-mobile-header');
        const mobileContainer = document.querySelector('.mobile-booking-container');
        const main = document.querySelector('main.container-fluid, #main-content, main');
        
        if (mobileHeader) {
            mobileHeader.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; position: sticky !important; top: 0 !important; z-index: 1000 !important; background: #ffffff !important;';
        }
        
        if (mobileContainer) {
            mobileContainer.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; padding: 16px !important; padding-bottom: 120px !important; width: 100% !important;';
        }
        
        if (main) {
            main.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; padding: 0 !important; margin: 0 !important;';
        }
        
        console.log('Mobile elements visibility forced:', {
            header: !!mobileHeader,
            container: !!mobileContainer,
            main: !!main
        });
    }
    
    // Run immediately
    forceMobileVisibility();
    
    // Verify we're on mobile view
    if (isMobile) {
        // Force mobile styles
        document.documentElement.classList.add('mobile-view');
        document.body.classList.add('mobile-view');
        
    // AGGRESSIVELY HIDE ALL OLD UI ELEMENTS
    const oldUIElements = [
        'nav.navbar',
        '.navbar',
        '.navbar-expand-lg',
        '.navbar-brand',
        '.navbar-nav',
        '.navbar-toggler',
        '.sidebar',
        '.content-wrapper',
        '.main-sidebar',
        '.content-header',
        '.breadcrumb',
        '.page-header',
        'main.container-fluid:not(:has(.mobile-booking-container))',
        '.card:not(.mobile-form-section):not(.selected-booths-summary-mobile)',
        '.alert:not(.mobile-alert)'
    ];
    
    oldUIElements.forEach(function(selector) {
        try {
            const elements = document.querySelectorAll(selector);
            elements.forEach(function(el) {
                if (el && !el.closest('.mobile-booking-container') && !el.closest('.modern-mobile-header')) {
                    el.style.display = 'none';
                    el.style.visibility = 'hidden';
                    el.style.height = '0';
                    el.style.padding = '0';
                    el.style.margin = '0';
                    el.style.opacity = '0';
                    el.style.position = 'absolute';
                    el.style.left = '-9999px';
                    el.style.width = '0';
                }
            });
        } catch(e) {
            console.warn('Error hiding element:', selector, e);
        }
    });
    
    // Force main container to be clean
    const mainContainer = document.querySelector('main.container-fluid, #main-content, main');
    if (mainContainer) {
        mainContainer.style.padding = '0';
        mainContainer.style.margin = '0';
        mainContainer.style.maxWidth = '100%';
        mainContainer.style.width = '100%';
    }
        
        // Ensure mobile container is visible
        const mobileContainer = document.querySelector('.mobile-booking-container');
        if (mobileContainer) {
            mobileContainer.style.display = 'block';
        }
        
        // Set cookie for future requests
        const expires = new Date();
        expires.setTime(expires.getTime() + (60 * 60 * 1000));
        document.cookie = 'screen_width=' + screenWidth + ';expires=' + expires.toUTCString() + ';path=/';
        document.cookie = 'preferred_view=mobile;expires=' + expires.toUTCString() + ';path=/';
        
        console.log('📱 Mobile View Active - Screen Width:', screenWidth);
    }
    
    // Prevent zoom on input focus (iOS)
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        if (input.style.fontSize !== '16px') {
            input.style.fontSize = '16px';
        }
    });
    
    // Verify mobile view is loaded and FORCE visibility
    const mobileHeader = document.getElementById('mobileHeader');
    const mobileContainer = document.querySelector('.mobile-booking-container');
    
    if (mobileHeader) {
        mobileHeader.style.display = 'block';
        mobileHeader.style.visibility = 'visible';
        mobileHeader.style.opacity = '1';
    }
    
    if (mobileContainer) {
        mobileContainer.style.display = 'block';
        mobileContainer.style.visibility = 'visible';
        mobileContainer.style.opacity = '1';
    }
    
    if (isMobile && (!mobileHeader || !mobileContainer)) {
        console.warn('⚠️ Mobile view elements not found. Reloading with mobile parameters...');
        const url = new URL(window.location.href);
        url.searchParams.set('screen_width', screenWidth);
        url.searchParams.set('mobile_view', '1');
        url.searchParams.set('force_mobile', '1');
        // Only reload if we're definitely on mobile
        if (screenWidth <= 768) {
            setTimeout(function() {
                window.location.href = url.toString();
            }, 500);
        }
    } else if (isMobile && mobileHeader && mobileContainer) {
        console.log('✅ Mobile view verified and active');
    }
    
    // Force main container visibility
    const main = document.querySelector('main.container-fluid, #main-content, main');
    if (main) {
        main.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; padding: 0 !important; margin: 0 !important;';
    }
    
    // Final visibility check and force - MULTIPLE TIMES
    setTimeout(forceMobileVisibility, 50);
    setTimeout(forceMobileVisibility, 100);
    setTimeout(forceMobileVisibility, 300);
    setTimeout(forceMobileVisibility, 500);
    setTimeout(forceMobileVisibility, 1000);
    
    // Also force on window load
    window.addEventListener('load', function() {
        forceMobileVisibility();
        console.log('Page loaded - forcing mobile visibility');
    });
})();

function filterByFloorPlan(floorPlanId) {
    if (floorPlanId) {
        window.location.href = '{{ route("books.create") }}?floor_plan_id=' + floorPlanId;
    } else {
        window.location.href = '{{ route("books.create") }}';
    }
}

function toggleBooth(element, boothId) {
    const checkbox = element.querySelector('.booth-checkbox-mobile');
    checkbox.checked = !checkbox.checked;
    element.classList.toggle('selected', checkbox.checked);
    updateSelection();
}

// Switch between booth categories
function switchBoothCategory(categoryId) {
    // Update tabs
    document.querySelectorAll('.booth-category-tab').forEach(function(tab) {
        tab.classList.remove('active');
    });
    const activeTab = document.querySelector('.booth-category-tab[data-category-id="' + categoryId + '"]');
    if (activeTab) {
        activeTab.classList.add('active');
    }
    
    // Update content sections
    document.querySelectorAll('.booth-category-content').forEach(function(content) {
        content.classList.remove('active');
    });
    const activeContent = document.getElementById('categoryContent_' + categoryId);
    if (activeContent) {
        activeContent.classList.add('active');
    }
    
    // Clear search when switching categories
    const searchInput = document.getElementById('boothSearchInput');
    if (searchInput) {
        searchInput.value = '';
        filterBooths('');
    }
    
    // Scroll to top of booth section
    const boothSection = document.getElementById('boothSection');
    if (boothSection) {
        boothSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Filter booths by search term (works across all categories)
function filterBooths(searchTerm) {
    const searchLower = searchTerm.toLowerCase().trim();
    const boothCards = document.querySelectorAll('.booth-card-mobile');
    let visibleCount = 0;
    
    boothCards.forEach(function(card) {
        const boothNumber = card.dataset.boothNumber || '';
        const category = card.dataset.category || '';
        const status = card.dataset.status || '';
        
        const matches = searchLower === '' || 
                       boothNumber.includes(searchLower) || 
                       category.includes(searchLower) || 
                       status.includes(searchLower);
        
        if (matches) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update count display
    const boothCount = document.getElementById('boothCount');
    const filteredCount = document.getElementById('filteredCount');
    const filteredBoothCount = document.getElementById('filteredBoothCount');
    
    if (searchLower === '') {
        if (boothCount) boothCount.textContent = boothCards.length;
        if (filteredBoothCount) filteredBoothCount.style.display = 'none';
    } else {
        if (filteredCount) filteredCount.textContent = visibleCount;
        if (filteredBoothCount) filteredBoothCount.style.display = 'block';
        if (boothCount) boothCount.textContent = boothCards.length;
    }
}

function updateSelection() {
    const selected = [];
    let totalAmount = 0;
    
    document.querySelectorAll('.booth-checkbox-mobile:checked').forEach(function(checkbox) {
        const boothCard = checkbox.closest('.booth-card-mobile');
        const boothId = checkbox.value;
        const boothNumber = boothCard.querySelector('.booth-number-mobile').textContent;
        const price = parseFloat(boothCard.dataset.price) || 0;
        
        selected.push({ id: boothId, number: boothNumber, price: price });
        totalAmount += price;
    });
    
    // Update selected list
    const listContainer = document.getElementById('selectedBoothsList');
    if (selected.length > 0) {
        let html = '';
        selected.forEach(function(booth) {
            html += '<div class="selected-booth-item-mobile">';
            html += '<div class="selected-booth-info-mobile">';
            html += '<i class="fas fa-cube"></i>';
            html += '<span>' + booth.number + '</span>';
            html += '</div>';
            html += '<div class="selected-booth-price-mobile">$' + booth.price.toFixed(2) + '</div>';
            html += '</div>';
        });
        listContainer.innerHTML = html;
    } else {
        listContainer.innerHTML = '<div class="empty-state-mobile" style="padding: 24px 20px;"><i class="fas fa-hand-pointer" style="font-size: 32px; color: #d1d5db; margin-bottom: 12px; opacity: 0.6;"></i><p style="font-size: 14px; color: #9ca3af; font-weight: 500;">No booths selected</p><p style="font-size: 12px; color: #d1d5db; margin-top: 4px;">Tap on booths below to select them</p></div>';
    }
    
    // Update summary with animation
    const totalBoothsEl = document.getElementById('totalBooths');
    const totalAmountEl = document.getElementById('totalAmount');
    
    if (totalBoothsEl) {
        totalBoothsEl.textContent = selected.length;
    }
    
    if (totalAmountEl) {
        const oldValue = parseFloat(totalAmountEl.textContent) || 0;
        const newValue = totalAmount;
        
        totalAmountEl.textContent = totalAmount.toFixed(2);
        
        // Add pulse animation if value changed
        if (oldValue !== newValue) {
            const parentEl = totalAmountEl.closest('.summary-total-value-mobile');
            if (parentEl) {
                parentEl.classList.add('updated');
                setTimeout(function() {
                    parentEl.classList.remove('updated');
                }, 500);
            }
        }
    }
    
    // Update submit button state
    const submitBtn = document.getElementById('submitBtn');
    const clientId = document.getElementById('clientid').value;
    
    if (submitBtn) {
        if (selected.length === 0 || !clientId || clientId === '') {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        } else {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        }
    }
    
    // Update progress indicator
    if (typeof updateProgress === 'function') {
        updateProgress();
    }
}

function selectAllBooths() {
    // Only select visible booths in the active category
    const activeCategory = document.querySelector('.booth-category-content.active');
    if (activeCategory) {
        activeCategory.querySelectorAll('.booth-card-mobile').forEach(function(card) {
            const checkbox = card.querySelector('.booth-checkbox-mobile');
            if (card.style.display !== 'none' && !card.classList.contains('disabled') && checkbox) {
                checkbox.checked = true;
                card.classList.add('selected');
            }
        });
    } else {
        // Fallback: select all visible booths
        document.querySelectorAll('.booth-card-mobile').forEach(function(card) {
            const checkbox = card.querySelector('.booth-checkbox-mobile');
            if (card.style.display !== 'none' && !card.classList.contains('disabled') && checkbox) {
                checkbox.checked = true;
                card.classList.add('selected');
            }
        });
    }
    updateSelection();
}

function clearSelection() {
    document.querySelectorAll('.booth-checkbox-mobile').forEach(function(checkbox) {
        checkbox.checked = false;
        checkbox.closest('.booth-card-mobile').classList.remove('selected');
    });
    updateSelection();
}

// Client Search & Select Functionality (adapted for mobile)
$(document).ready(function() {
    let clientSearchTimeout;
    let inlineSearchTimeout;
    let selectedClient = null;
    
    // Initialize - check if client is already selected
    @if(old('clientid'))
        const oldClientId = {{ old('clientid') }};
        $.ajax({
            url: '{{ route("clients.search") }}',
            method: 'GET',
            data: { q: '', id: oldClientId },
            success: function(clients) {
                if (clients && clients.length > 0) {
                    const client = clients.find(c => c.id == oldClientId);
                    if (client) {
                        selectClient(client);
                    }
                }
            }
        });
    @endif
    
    // Inline Client Search
    function searchClientsInline(query) {
        if (!query || query.length < 2) {
            $('#inlineClientResults').hide();
            return;
        }
        
        const resultsDiv = $('#inlineClientResults');
        const resultsList = $('#inlineClientResultsList');
        const searchIcon = $('#searchIcon');
        
        if (searchIcon.length) {
            searchIcon.removeClass('fa-search').addClass('fa-spinner fa-spin');
        }
        
        resultsDiv.show();
        resultsList.html('<div class="loading-state-mobile"><i class="fas fa-spinner fa-spin"></i><p class="mb-0 mt-2">Searching...</p></div>');
        
        $.ajax({
            url: '{{ route("clients.search") }}',
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(clients) {
                if (searchIcon.length) {
                    searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
                
                resultsList.empty();
                const clientsArray = Array.isArray(clients) ? clients : (clients ? Object.values(clients) : []);
                
                if (!clientsArray || clientsArray.length === 0) {
                    resultsList.html('<div class="empty-state-mobile"><i class="fas fa-search"></i><p>No clients found</p></div>');
                    return;
                }
                
                clientsArray.slice(0, 8).forEach(function(client) {
                    const displayName = (client.company || client.name || 'N/A');
                    let detailsHTML = '';
                    if (client.email) detailsHTML += '<div class="client-result-details-mobile"><i class="fas fa-envelope"></i><span>' + client.email + '</span></div>';
                    if (client.phone_number) detailsHTML += '<div class="client-result-details-mobile"><i class="fas fa-phone"></i><span>' + client.phone_number + '</span></div>';
                    
                    const item = $('<div class="client-result-item-mobile"></div>')
                        .html(
                            '<div class="client-result-content-mobile">' +
                                '<div class="client-result-name-mobile">' +
                                    '<i class="fas fa-building"></i>' +
                                    '<span>' + displayName + '</span>' +
                                '</div>' +
                                detailsHTML +
                            '</div>' +
                            '<button type="button" class="select-client-btn-mobile" data-client-id="' + client.id + '">' +
                                '<i class="fas fa-check"></i>' +
                            '</button>'
                        )
                        .data('client', client);
                    
                    resultsList.append(item);
                });
                
                $(document).off('click', '.select-client-btn-mobile').on('click', '.select-client-btn-mobile', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const client = $(this).closest('.client-result-item-mobile').data('client');
                    if (client) {
                        selectClient(client);
                        $('#inlineClientResults').hide();
                    }
                });
                
                $(document).off('click', '#inlineClientResultsList .client-result-item-mobile').on('click', '#inlineClientResultsList .client-result-item-mobile', function(e) {
                    if (!$(e.target).closest('.select-client-btn-mobile').length) {
                        e.preventDefault();
                        const client = $(this).data('client');
                        if (client) {
                            selectClient(client);
                            $('#inlineClientResults').hide();
                        }
                    }
                });
            },
            error: function() {
                if (searchIcon.length) {
                    searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
                resultsList.html('<div class="empty-state-mobile"><i class="fas fa-exclamation-triangle"></i><p>Error searching clients</p></div>');
            }
        });
    }
    
    // Inline search input handler
    $('#clientSearchInline').on('input keyup paste', function(e) {
        if ([38, 40, 13, 27].includes(e.keyCode)) {
            return;
        }
        
        const query = $(this).val().trim();
        clearTimeout(inlineSearchTimeout);
        
        if (query.length < 2) {
            $('#inlineClientResults').hide();
            const searchIcon = $('#searchIcon');
            if (searchIcon.length) {
                searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
            }
            if (query.length === 0 && selectedClient) {
                selectedClient = null;
                $('#clientid').val('');
                $('#selectedClientInfo').hide();
                $('#clientSearchContainer').show();
            }
            return;
        }
        
        inlineSearchTimeout = setTimeout(function() {
            searchClientsInline(query);
        }, 300);
    });
    
    // Hide inline results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#clientSearchInline, #inlineClientResults').length) {
            $('#inlineClientResults').hide();
        }
    });
    
    // Select Client Function
    function selectClient(client) {
        selectedClient = client;
        $('#clientid').val(client.id);
        
        const displayName = client.company || client.name || 'N/A';
        let details = [];
        if (client.email) details.push('<i class="fas fa-envelope"></i> ' + client.email);
        if (client.phone_number) details.push('<i class="fas fa-phone"></i> ' + client.phone_number);
        
        $('#selectedClientName').text(displayName);
        $('#selectedClientDetails').html(details.length > 0 ? details.join(' | ') : '');
        $('#selectedClientInfo').show();
        $('#clientSearchContainer').hide();
        $('#clientSearchInline').val('');
        $('#inlineClientResults').hide();
        $('#searchClientModal').modal('hide');
        
        // Update progress indicator
        if (typeof updateProgress === 'function') {
            updateProgress();
        }
    }
    
    // Clear Client Selection
    $('#btnClearClient').on('click', function() {
        selectedClient = null;
        $('#clientid').val('');
        $('#selectedClientInfo').hide();
        $('#clientSearchContainer').show();
        $('#clientSearchInline').val('');
        $('#inlineClientResults').hide();
        
        // Update progress indicator
        if (typeof updateProgress === 'function') {
            setTimeout(updateProgress, 100);
        }
        
        // Scroll to client section
        setTimeout(function() {
            const clientSection = document.getElementById('clientSection');
            if (clientSection) {
                clientSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            $('#clientSearchInline').focus();
        }, 100);
    });
    
    // Client Search Function (for modal) - same as desktop
    function searchClients(query) {
        if (!query || query.length < 2) {
            $('#clientSearchResults').hide();
            $('#noClientResults').hide();
            return;
        }
        
        $.ajax({
            url: '{{ route("clients.search") }}',
            method: 'GET',
            data: { q: query },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(clients) {
                const resultsDiv = $('#clientSearchResults');
                const resultsList = $('#clientSearchResultsList');
                const noResultsDiv = $('#noClientResults');
                
                resultsList.empty();
                const clientsArray = Array.isArray(clients) ? clients : (clients ? Object.values(clients) : []);
                
                if (!clientsArray || clientsArray.length === 0) {
                    resultsDiv.hide();
                    noResultsDiv.show();
                    return;
                }
                
                noResultsDiv.hide();
                resultsDiv.show();
                
                clientsArray.forEach(function(client) {
                    const displayName = (client.company || client.name || 'N/A');
                    let detailsHTML = '<div class="client-result-details">';
                    if (client.name && client.company) detailsHTML += '<div class="client-result-detail user"><i class="fas fa-user"></i><span>' + client.name + '</span></div>';
                    if (client.email) detailsHTML += '<div class="client-result-detail email"><i class="fas fa-envelope"></i><span>' + client.email + '</span></div>';
                    if (client.phone_number) detailsHTML += '<div class="client-result-detail phone"><i class="fas fa-phone"></i><span>' + client.phone_number + '</span></div>';
                    detailsHTML += '</div>';
                    
                    const item = $('<div class="client-search-result"></div>')
                        .html(
                            '<div class="client-result-content">' +
                                '<div class="client-result-name">' +
                                    '<i class="fas fa-building"></i>' +
                                    '<span>' + displayName + '</span>' +
                                '</div>' +
                                detailsHTML +
                            '</div>' +
                            '<button type="button" class="btn btn-modern btn-modern-primary select-client-btn" data-client-id="' + client.id + '">' +
                                '<i class="fas fa-check mr-1"></i>Select' +
                            '</button>'
                        )
                        .data('client', client);
                    
                    resultsList.append(item);
                });
                
                $('.select-client-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const client = $(this).closest('.client-search-result').data('client');
                    selectClient(client);
                });
            },
            error: function() {
                $('#clientSearchResults').hide();
                $('#noClientResults').show();
            }
        });
    }
    
    $('#clientSearchInput').on('input keyup', function(e) {
        const query = $(this).val().trim();
        clearTimeout(clientSearchTimeout);
        
        if (query.length < 2) {
            $('#clientSearchResults').hide();
            $('#noClientResults').hide();
            $('#btnClearClientSearch').hide();
            return;
        }
        
        $('#btnClearClientSearch').show();
        clientSearchTimeout = setTimeout(function() {
            searchClients(query);
        }, 300);
    });
    
    $('#btnSearchClient').on('click', function() {
        const query = $('#clientSearchInput').val().trim();
        if (query.length >= 2) {
            searchClients(query);
        }
    });
    
    $('#btnClearClientSearch').on('click', function() {
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $(this).hide();
    });
    
    // Create Client Form Submission
    $('#createClientForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createClientSubmitBtn');
        const errorDiv = $('#createClientError');
        const originalText = submitBtn.html();
        
        errorDiv.hide();
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success' && response.client) {
                    selectClient(response.client);
                    $('#createClientModal').modal('hide');
                    form[0].reset();
                    errorDiv.hide();
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Client Created!',
                            text: 'Client "' + response.client.company + '" has been created and selected.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Client created successfully!');
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the client.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];
                    Object.keys(errors).forEach(function(key) {
                        const fieldErrors = errors[key];
                        if (Array.isArray(fieldErrors)) {
                            fieldErrors.forEach(function(err) {
                                errorMessages.push('<div>' + err + '</div>');
                            });
                        } else {
                            errorMessages.push('<div>' + fieldErrors + '</div>');
                        }
                    });
                    if (errorMessages.length > 0) {
                        errorMessage = errorMessages.join('');
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i><strong>Validation Error:</strong><br>' + errorMessage).show();
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Enhanced Form Submission with AJAX
    $('#bookingForm').on('submit', function(e) {
        e.preventDefault();
        
        const clientId = $('#clientid').val();
        if (!clientId || clientId === '' || clientId === null) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Client Required',
                    text: 'Please select a client before submitting the booking.',
                    confirmButtonColor: '#667eea'
                });
            } else {
                alert('Please select a client before submitting the booking.');
            }
            if ($('#clientSearchContainer').is(':hidden')) {
                $('#selectedClientInfo').hide();
                $('#clientSearchContainer').show();
            }
            setTimeout(function() {
                $('#clientSearchInline').focus();
            }, 100);
            return false;
        }
        
        const selectedCount = $('.booth-checkbox-mobile:checked').length;
        if (selectedCount === 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Booths Selected',
                    text: 'Please select at least one booth for this booking.',
                    confirmButtonColor: '#667eea'
                });
            } else {
                alert('Please select at least one booth for this booking.');
            }
            return false;
        }
        
        // Show loading state
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        const loadingOverlay = $('#loadingOverlay');
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creating...');
        loadingOverlay.addClass('active');
        
        // Scroll to top to show any errors
        $('html, body').animate({ scrollTop: 0 }, 200);
        
        // Update progress to show all steps completed
        if (typeof updateProgress === 'function') {
            updateProgress();
        }
        
        // Submit via AJAX for better UX
        $.ajax({
            url: $('#bookingForm').attr('action'),
            method: 'POST',
            data: $('#bookingForm').serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Booking Created!',
                            text: response.message || 'Booking has been created successfully.',
                            confirmButtonColor: '#667eea',
                            timer: 2000,
                            showConfirmButton: true
                        }).then(function() {
                            window.location.href = '{{ route("books.index") }}';
                        });
                    } else {
                        alert('Booking created successfully!');
                        window.location.href = '{{ route("books.index") }}';
                    }
                } else {
                    // Handle error response
                    loadingOverlay.removeClass('active');
                    
                    let errorMessage = response.message || 'An error occurred while creating the booking.';
                    if (response.errors) {
                        const errorMessages = [];
                        Object.keys(response.errors).forEach(function(key) {
                            const fieldErrors = response.errors[key];
                            if (Array.isArray(fieldErrors)) {
                                fieldErrors.forEach(function(err) {
                                    errorMessages.push(err);
                                });
                            } else {
                                errorMessages.push(fieldErrors);
                            }
                        });
                        if (errorMessages.length > 0) {
                            errorMessage = errorMessages.join('\n');
                        }
                    }
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Creating Booking',
                            text: errorMessage,
                            confirmButtonColor: '#667eea'
                        });
                    } else {
                        alert('Error: ' + errorMessage);
                    }
                    
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }
            },
            error: function(xhr) {
                loadingOverlay.removeClass('active');
                
                let errorMessage = 'An error occurred while creating the booking.';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const errorMessages = [];
                        Object.keys(errors).forEach(function(key) {
                            const fieldErrors = errors[key];
                            if (Array.isArray(fieldErrors)) {
                                fieldErrors.forEach(function(err) {
                                    errorMessages.push(err);
                                });
                            } else {
                                errorMessages.push(fieldErrors);
                            }
                        });
                        if (errorMessages.length > 0) {
                            errorMessage = errorMessages.join('\n');
                        }
                    }
                } else if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        errorMessage = 'Server error. Please try again.';
                    }
                }
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Creating Booking',
                        html: errorMessage.replace(/\n/g, '<br>'),
                        confirmButtonColor: '#667eea'
                    });
                } else {
                    alert('Error: ' + errorMessage);
                }
                
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
        
        return false;
    });
    
    // Progress Indicator Updates
    function updateProgress() {
        const clientId = $('#clientid').val();
        const hasBooths = $('.booth-checkbox-mobile:checked').length > 0;
        const hasDate = $('#date_book').val() && $('#date_book').val() !== '';
        
        // Step 1: Client
        if (clientId && clientId !== '') {
            $('#progressStep1').addClass('completed').html('<i class="fas fa-check"></i>');
            $('#clientSection').addClass('completed');
        } else {
            $('#progressStep1').addClass('active').removeClass('completed').html('<i class="fas fa-user"></i>');
            $('#clientSection').removeClass('completed');
        }
        
        // Step 2: Details (active after client, completed when date is set)
        if (clientId && clientId !== '') {
            if (hasDate) {
                $('#progressStep2').addClass('completed').html('<i class="fas fa-check"></i>');
            } else {
                $('#progressStep2').addClass('active').removeClass('completed').html('<i class="fas fa-calendar"></i>');
            }
        } else {
            $('#progressStep2').removeClass('active').removeClass('completed').html('<i class="fas fa-calendar"></i>');
        }
        
        // Step 3: Booths
        if (hasBooths) {
            $('#progressStep3').addClass('completed').html('<i class="fas fa-check"></i>');
            $('#boothSection').addClass('completed');
        } else {
            $('#progressStep3').removeClass('completed').addClass('active').html('<i class="fas fa-cube"></i>');
            $('#boothSection').removeClass('completed');
        }
    }
    
    // Update progress when date changes
    $('#date_book').on('change', function() {
        updateProgress();
    });
    
    // Initialize
    updateSelection();
    updateProgress();
    
    // Update when checkboxes change
    $(document).on('change', '.booth-checkbox-mobile', function() {
        updateSelection();
        updateProgress();
    });
    
    // Update progress when client is selected
    $(document).on('change', '#clientid', function() {
        updateProgress();
        updateSelection(); // Also update selection to check button state
    });
    
    // Update progress on page load
    setTimeout(function() {
        updateProgress();
        updateSelection();
    }, 300);
    
    // Track form changes for unsaved changes warning
    let formChanged = false;
    let formSubmitted = false;
    
    $('#bookingForm input, #bookingForm select').on('change input', function() {
        if (!formSubmitted) {
            formChanged = true;
        }
    });
    
    // Warn before leaving page if form has changes
    $(window).on('beforeunload', function(e) {
        if (formChanged && !formSubmitted) {
            const message = 'You have unsaved changes. Are you sure you want to leave?';
            e.returnValue = message;
            return message;
        }
    });
    
    // Mark form as submitted when successfully submitted
    $('#bookingForm').on('submit', function() {
        formSubmitted = true;
        formChanged = false;
    });
    
    // Cancel button - check for unsaved changes
    $('.mobile-btn-secondary').on('click', function(e) {
        if (formChanged && !formSubmitted) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'question',
                    title: 'Unsaved Changes',
                    text: 'You have unsaved changes. Are you sure you want to leave?',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Leave',
                    cancelButtonText: 'Stay'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        formSubmitted = true;
                        window.location.href = '{{ route("books.index") }}';
                    }
                });
            } else {
                if (confirm('You have unsaved changes. Are you sure you want to leave?')) {
                    formSubmitted = true;
                    window.location.href = '{{ route("books.index") }}';
                }
            }
        }
    });
    
    // Improve accessibility - add ARIA labels
    $('#clientSearchInline').attr('aria-label', 'Search for client by name, company, email, or phone');
    $('#date_book').attr('aria-label', 'Booking date and time');
    $('#type').attr('aria-label', 'Booking type');
    $('.booth-checkbox-mobile').each(function() {
        const boothNumber = $(this).closest('.booth-card-mobile').find('.booth-number-mobile').text();
        $(this).attr('aria-label', 'Select booth ' + boothNumber);
    });
});
</script>
@endpush
