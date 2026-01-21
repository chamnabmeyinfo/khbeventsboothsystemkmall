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
.selected-booths-summary-mobile,
.modern-mobile-nav {
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

/* Global fix for icon alignment */
i[class^="fas"], i[class^="far"], i[class^="fab"] {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    vertical-align: middle;
}

/* Prevent layout shifts */
button, a, input, select, textarea {
    box-sizing: border-box;
}

/* Fix for flex containers to prevent gaps */
.mobile-form-section,
.mobile-action-buttons,
.client-search-input-wrapper,
.selected-booth-item-mobile {
    box-sizing: border-box;
}

/* Force all mobile elements visible - NO EXCEPTIONS */
.modern-mobile-header *,
.mobile-booking-container *,
.mobile-form-section *,
.mobile-action-buttons *,
.modern-mobile-nav * {
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
    position: sticky !important;
    top: 0 !important;
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

/* Progress Step Styles - Minimal */
.progress-step {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.progress-step.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.progress-step.completed {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
    color: white !important;
    transform: scale(1.02);
    box-shadow: 0 2px 6px rgba(34, 197, 94, 0.3);
}

.progress-step.completed::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    border: 2px solid #22c55e;
    opacity: 0;
    animation: ripple 1.5s ease-out;
}

.progress-step.pending {
    background: #e5e7eb !important;
    color: #9ca3af !important;
}

@keyframes pulseActive {
    0%, 100% {
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        transform: scale(1.05);
    }
    50% {
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.4);
        transform: scale(1.06);
    }
}

@keyframes ripple {
    0% {
        transform: scale(0.8);
        opacity: 1;
    }
    100% {
        transform: scale(1.4);
        opacity: 0;
    }
}

/* Animated Progress Line - Minimal */
.progress-line {
    position: relative;
    height: 2px;
    background: #e5e7eb;
    border-radius: 1px;
    overflow: hidden;
}

.progress-line.completed .progress-line-fill {
    width: 100% !important;
}

.progress-line-fill {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 0;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 1px;
}

.progress-line.completed {
    background: #22c55e;
}

.progress-line.completed::after {
    width: 100%;
    background: #22c55e;
}

/* Step Info Styling */
.step-info {
    transition: all 0.3s ease;
}

.step-title {
    transition: color 0.3s ease;
}

.step-status {
    transition: color 0.3s ease;
}

/* Progress Step Container */
.progress-step-container {
    position: relative;
}

/* Booking Progress Container */
.booking-progress-container {
    position: relative;
}

.current-step-highlight {
    animation: fadeInUp 0.3s ease;
}

/* Minimal Progress Container */
.booking-progress-container {
    transition: all 0.3s ease;
}

.progress-steps-wrapper {
    min-height: 50px;
}

/* Booth Help Button */
.booth-help-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 8px;
    color: #667eea;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    box-shadow: 0 1px 3px rgba(102, 126, 234, 0.1);
    line-height: 1.4;
}

.booth-help-btn:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
    border-color: rgba(102, 126, 234, 0.3);
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.15);
}

.booth-help-btn:active {
    transform: translateY(0);
    box-shadow: 0 1px 3px rgba(102, 126, 234, 0.1);
}

.booth-help-btn i {
    font-size: 14px;
    line-height: 1;
}

.booth-help-btn .help-btn-text {
    font-size: 12px;
    font-weight: 600;
    line-height: 1.4;
}

.booth-help-btn.active {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
    border-color: rgba(102, 126, 234, 0.4);
    color: #5568d3;
}

/* Booth Grid Info - Hidden by default, shown on click */
.booth-grid-info {
    display: none;
    flex-direction: column;
    animation: slideDown 0.3s ease;
}

.booth-grid-info.show {
    display: flex;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive help button */
@media (max-width: 480px) {
    .booth-help-btn {
        padding: 6px 12px;
        font-size: 11px;
    }
    
    .booth-help-btn .help-btn-text {
        font-size: 11px;
    }
    
    .booth-help-btn i {
        font-size: 13px;
    }
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

/* Responsive adjustments for progress steps */
@media (max-width: 480px) {
    .progress-step {
        width: 40px !important;
        height: 40px !important;
        font-size: 16px !important;
    }
    
    .step-info {
        max-width: 80px !important;
    }
    
    .step-title {
        font-size: 12px !important;
    }
    
    .step-status {
        font-size: 10px !important;
    }
}

/* Media query to ensure this only applies on mobile */
@media (min-width: 769px) {
    /* On desktop, show normal elements (but this view shouldn't load on desktop) */
    nav.navbar,
    .navbar {
        display: block !important;
    }
}

/* Modern Mobile Header - iOS/Android Style - Always Sticky at Top - FORCED */
.modern-mobile-header {
    position: sticky !important;
    position: -webkit-sticky !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
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
    min-height: 68px !important;
    box-sizing: border-box !important;
    margin: 0 !important;
    max-width: 100% !important;
    overflow: visible !important;
}

/* Force sticky on all browsers */
.modern-mobile-header,
#mobileHeader {
    position: -webkit-sticky !important;
    position: sticky !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 1000 !important;
}

/* Fallback for older browsers - use fixed */
@supports not (position: sticky) {
    .modern-mobile-header,
    #mobileHeader {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
    }
    
    body {
        padding-top: 68px !important;
    }
}

/* Additional force rules for sticky header */
.modern-mobile-header,
#mobileHeader {
    will-change: transform !important;
    transform: translateZ(0) !important;
    -webkit-transform: translateZ(0) !important;
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
    min-width: 44px;
    min-height: 44px;
    border-radius: 12px;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
    font-size: 20px;
    line-height: 1;
    transition: all 0.2s ease;
    cursor: pointer;
    -webkit-tap-highlight-color: transparent;
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

.app-header-btn i {
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    vertical-align: middle;
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
        padding-bottom: max(100px, env(safe-area-inset-bottom) + 90px);
        padding-top: 16px; /* Normal top padding since buttons are at top */
    }
    
    .mobile-action-buttons {
        padding-bottom: max(14px, env(safe-area-inset-bottom) + 14px);
        min-height: max(70px, env(safe-area-inset-bottom) + 56px);
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

/* Section completion indicator - Enhanced */
.mobile-form-section.completed {
    border-left: 4px solid #22c55e;
    background: linear-gradient(to right, rgba(34, 197, 94, 0.03) 0%, #ffffff 4%);
    animation: slideInCompletion 0.4s ease;
}

.mobile-form-section.completed .mobile-form-section-title {
    color: #22c55e;
}

.mobile-form-section.completed .mobile-form-section-title i {
    color: #22c55e;
    animation: checkmarkPop 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.mobile-form-section.completed .mobile-form-section-title::after {
    content: ' ✓';
    color: #22c55e;
    font-weight: 700;
    margin-left: 8px;
    animation: checkmarkPop 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes slideInCompletion {
    from {
        border-left-width: 0;
        background: #ffffff;
    }
    to {
        border-left-width: 4px;
        background: linear-gradient(to right, rgba(34, 197, 94, 0.03) 0%, #ffffff 4%);
    }
}

@keyframes checkmarkPop {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
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
    min-width: 24px;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    flex-shrink: 0;
}

/* Selected Floor Plan Name Display */
.floor-plan-selected-name {
    margin-top: 6px;
    padding: 6px 12px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #667eea;
    border: 1px solid rgba(102, 126, 234, 0.2);
    animation: fadeInUp 0.3s ease;
}

.floor-plan-selected-name i {
    color: #667eea;
    font-size: 12px;
    width: auto;
    min-width: auto;
    flex-shrink: 0;
}

.floor-plan-selected-name span {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Form Controls - Modern iOS Style with Expand/Collapse */
.mobile-form-group {
    margin-bottom: 24px;
    position: relative;
}

.mobile-form-group:last-child {
    margin-bottom: 0;
}

/* Collapsible Form Group */
.mobile-form-group.collapsible {
    border: 1.5px solid rgba(102, 126, 234, 0.1);
    border-radius: 12px;
    padding: 16px;
    background: #fafbfc;
    transition: all 0.3s ease;
}

.mobile-form-group.collapsible.collapsed {
    padding: 16px;
    background: #ffffff;
}

.mobile-form-group-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    user-select: none;
    -webkit-user-select: none;
    padding: 0;
    margin: 0 0 12px 0;
    min-height: 24px;
}

.mobile-form-group-header .mobile-form-label {
    margin-bottom: 0 !important;
    flex: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
}

.mobile-form-group-header .mobile-form-label i {
    color: #667eea;
    font-size: 16px;
    line-height: 1;
    flex-shrink: 0;
}

.mobile-form-group-header .mobile-form-label .required {
    color: #ef4444;
    margin-left: 2px;
}

.mobile-form-group-toggle {
    width: 32px;
    height: 32px;
    min-width: 32px;
    min-height: 32px;
    border-radius: 8px;
    background: rgba(102, 126, 234, 0.1);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
    color: #667eea;
    font-size: 14px;
    -webkit-tap-highlight-color: transparent;
}

.mobile-form-group-toggle:hover {
    background: rgba(102, 126, 234, 0.15);
}

.mobile-form-group-toggle:active {
    transform: scale(0.9);
}

.mobile-form-group-toggle i {
    transition: transform 0.3s ease;
    line-height: 1;
}

.mobile-form-group.collapsed .mobile-form-group-toggle i {
    transform: rotate(-90deg);
}

.mobile-form-group-content {
    max-height: 1000px;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, margin 0.3s ease, padding 0.3s ease;
    opacity: 1;
    margin-top: 0;
    padding-top: 0;
}

.mobile-form-group.collapsed .mobile-form-group-content {
    max-height: 0 !important;
    opacity: 0;
    margin-top: 0 !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    overflow: hidden;
    border: none;
}

/* Ensure labels in collapsed groups are still visible */
.mobile-form-group.collapsible .mobile-form-group-header {
    margin-bottom: 0;
}

.mobile-form-group.collapsible:not(.collapsed) .mobile-form-group-header {
    margin-bottom: 12px;
}

/* Auto-expand if has error or required field is focused */
.mobile-form-group.collapsible:has(.mobile-form-input:focus),
.mobile-form-group.collapsible:has(.mobile-form-select:focus),
.mobile-form-group.collapsible:has([class*="error"]),
.mobile-form-group.collapsible:has([style*="color: #ef4444"]) {
    border-color: rgba(102, 126, 234, 0.2);
}

.mobile-form-group.collapsible:has(.mobile-form-input:focus) .mobile-form-group-content,
.mobile-form-group.collapsible:has(.mobile-form-select:focus) .mobile-form-group-content {
    max-height: 1000px !important;
    opacity: 1;
}

.mobile-form-group.collapsible:has(.mobile-form-input:focus),
.mobile-form-group.collapsible:has(.mobile-form-select:focus) {
    border-color: #667eea;
    background: #ffffff;
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
    box-sizing: border-box;
    margin: 0;
    line-height: 1.5;
    vertical-align: middle;
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

/* Booking Type Selector - Multi-View Styles */
.booking-type-view-switcher {
    display: flex;
    gap: 6px;
    background: #f3f4f6;
    padding: 4px;
    border-radius: 10px;
    flex-shrink: 0;
}

.view-switch-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: transparent;
    color: #6b7280;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    -webkit-tap-highlight-color: transparent;
    flex-shrink: 0;
}

.view-switch-btn:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.view-switch-btn.active {
    background: #667eea;
    color: white;
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
}

.view-switch-btn:active {
    transform: scale(0.95);
}

.booking-type-view {
    display: none;
}

.booking-type-view.active {
    display: block;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Icon View Styles */
.booking-type-options-icon {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.booking-type-option-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 12px;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    position: relative;
    overflow: hidden;
}

.booking-type-option-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.booking-type-option-icon:hover {
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.booking-type-option-icon:hover::before {
    opacity: 1;
}

.booking-type-option-icon.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.2);
}

.booking-type-icon-wrapper {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.booking-type-option-icon.selected .booking-type-icon-wrapper {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.booking-type-option-icon[data-value="2"] .booking-type-icon-wrapper {
    background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.booking-type-option-icon[data-value="2"].selected .booking-type-icon-wrapper {
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
}

.booking-type-option-icon[data-value="3"] .booking-type-icon-wrapper {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.booking-type-option-icon[data-value="3"].selected .booking-type-icon-wrapper {
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.booking-type-label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    text-align: center;
    margin-top: 4px;
}

.booking-type-option-icon.selected .booking-type-label {
    color: #667eea;
    font-weight: 700;
}

/* List View Styles */
.booking-type-options-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.booking-type-option-list {
    display: flex;
    align-items: center;
    padding: 16px;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    gap: 14px;
    position: relative;
}

.booking-type-option-list:hover {
    border-color: #667eea;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

.booking-type-option-list.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
}

.booking-type-list-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
}

.booking-type-option-list[data-value="2"] .booking-type-list-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25);
}

.booking-type-option-list[data-value="3"] .booking-type-list-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
}

.booking-type-list-content {
    flex: 1;
    min-width: 0;
}

.booking-type-list-title {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.booking-type-list-desc {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.4;
}

.booking-type-list-check {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 14px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.booking-type-option-list.selected .booking-type-list-check {
    background: #667eea;
    color: white;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

/* Card View Styles */
.booking-type-options-card {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

.booking-type-option-card {
    padding: 20px;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    position: relative;
    overflow: hidden;
}

.booking-type-option-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.booking-type-option-card:hover {
    border-color: #667eea;
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
}

.booking-type-option-card:hover::before {
    opacity: 1;
}

.booking-type-option-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
    box-shadow: 0 8px 28px rgba(102, 126, 234, 0.2);
}

.booking-type-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.booking-type-card-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.booking-type-option-card[data-value="2"] .booking-type-card-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.booking-type-option-card[data-value="3"] .booking-type-card-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.booking-type-card-badge {
    padding: 6px 12px;
    background: #e5e7eb;
    color: #6b7280;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.booking-type-card-badge.premium {
    background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
    color: white;
}

.booking-type-card-badge.temp {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.booking-type-card-title {
    font-size: 17px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 8px;
    letter-spacing: -0.3px;
}

.booking-type-card-desc {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.5;
    margin-bottom: 12px;
}

.booking-type-card-check {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 14px;
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.3s ease;
}

.booking-type-option-card.selected .booking-type-card-check {
    opacity: 1;
    transform: scale(1);
    background: #667eea;
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Floor Plan Image Display */
/* Floor Plan Image Display - Minimal */
.floor-plan-image-display {
    margin-bottom: 12px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(102, 126, 234, 0.1);
    animation: fadeInUp 0.3s ease;
}

/* Floor Plan Image Wrapper - Minimal */
.floor-plan-image-wrapper {
    position: relative;
    width: 100%;
    min-height: 120px;
    max-height: 250px;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.floor-plan-image-wrapper.no-image {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
}

.floor-plan-image {
    width: 100%;
    height: auto;
    max-height: 250px;
    object-fit: contain;
    display: block;
    background: transparent;
}

.floor-plan-image[src=""],
.floor-plan-image:not([src]) {
    display: none;
}

/* Floor Plan Image Overlay - Minimal */
.floor-plan-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5) 0%, transparent 40%, transparent 60%, rgba(0, 0, 0, 0.3) 100%);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 12px;
    pointer-events: none;
}

.floor-plan-image-wrapper.no-image .floor-plan-image-overlay {
    background: transparent;
    position: relative;
    padding: 16px;
    pointer-events: auto;
}

.floor-plan-image-info {
    pointer-events: auto;
    color: white;
    flex: 1;
}

/* Floor Plan Image Info - Minimal */
.floor-plan-image-info h4 {
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 3px 0;
    text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

.floor-plan-image-wrapper.no-image .floor-plan-image-info h4 {
    color: #374151;
    text-shadow: none;
    font-size: 16px;
}

.floor-plan-image-wrapper.no-image .floor-plan-image-info p {
    color: #6b7280;
    text-shadow: none;
}

.floor-plan-image-info p {
    font-size: 13px;
    margin: 0;
    opacity: 0.95;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
}

.floor-plan-image-close {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    pointer-events: auto;
    flex-shrink: 0;
    -webkit-tap-highlight-color: transparent;
}

.floor-plan-image-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.floor-plan-image-close:active {
    transform: scale(0.95);
}

/* Floor Plan View Switcher (reuse booking type styles) */
.floor-plan-view-switcher {
    display: flex;
    gap: 6px;
    background: #f3f4f6;
    padding: 4px;
    border-radius: 10px;
    flex-shrink: 0;
}

/* Floor Plan Views (similar to booking type) */
.floor-plan-view {
    display: none;
}

.floor-plan-view.active {
    display: block;
    animation: fadeInUp 0.3s ease;
}

/* Floor Plan Icon View */
/* Floor Plan Minimal Container */
.floor-plan-minimal-container {
    margin-bottom: 16px;
}

/* Floor Plan Options Icon - Ultra Minimal */
.floor-plan-options-icon {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
    gap: 6px;
}

/* Floor Plan Option Icon - Ultra Minimal */
.floor-plan-option-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px 6px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    -webkit-tap-highlight-color: transparent;
    position: relative;
    overflow: hidden;
    min-height: 60px;
}

/* Remove ::before overlay for minimal design */
.floor-plan-option-icon::before {
    display: none;
}

/* Floor Plan Option Hover & Selected - Minimal */
.floor-plan-option-icon:hover {
    border-color: rgba(102, 126, 234, 0.3);
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.1);
}

.floor-plan-option-icon:hover::before {
    opacity: 0.5;
}

.floor-plan-option-icon.selected {
    border-color: #667eea;
    border-width: 2px;
    background: rgba(102, 126, 234, 0.05);
    box-shadow: 0 1px 3px rgba(102, 126, 234, 0.15);
}

/* Floor Plan Icon Wrapper - Ultra Minimal */
.floor-plan-icon-wrapper {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #667eea;
    font-size: 14px;
    margin-bottom: 4px;
    transition: all 0.2s ease;
}

.floor-plan-option-icon.selected .floor-plan-icon-wrapper {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: scale(1.05);
    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.25);
}

/* Floor Plan Label - Ultra Minimal */
.floor-plan-label {
    font-size: 10px;
    font-weight: 600;
    color: #6b7280;
    text-align: center;
    margin-top: 2px;
    line-height: 1.2;
    word-break: break-word;
    max-width: 100%;
}

.floor-plan-option-icon.selected .floor-plan-label {
    color: #667eea;
    font-weight: 700;
}

/* Floor Plan Badge - Ultra Minimal */
.floor-plan-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    padding: 2px 4px;
    background: #f59e0b;
    color: white;
    border-radius: 4px;
    font-size: 7px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    line-height: 1.2;
}

/* Floor Plan List View */
.floor-plan-options-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.floor-plan-option-list {
    display: flex;
    align-items: center;
    padding: 16px;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    gap: 14px;
}

.floor-plan-option-list:hover {
    border-color: #667eea;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

.floor-plan-option-list.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
}

.floor-plan-list-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
}

.floor-plan-list-content {
    flex: 1;
    min-width: 0;
}

.floor-plan-list-title {
    font-size: 15px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.floor-plan-list-desc {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.4;
}

.floor-plan-badge-inline {
    display: inline-block;
    padding: 2px 8px;
    background: #f59e0b;
    color: white;
    border-radius: 8px;
    font-size: 10px;
    font-weight: 700;
    margin-left: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.floor-plan-list-check {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 14px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.floor-plan-option-list.selected .floor-plan-list-check {
    background: #667eea;
    color: white;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

/* Floor Plan Card View */
.floor-plan-options-card {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

.floor-plan-option-card {
    padding: 20px;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    position: relative;
    overflow: hidden;
}

.floor-plan-option-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.floor-plan-option-card:hover {
    border-color: #667eea;
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
}

.floor-plan-option-card:hover::before {
    opacity: 1;
}

.floor-plan-option-card.selected {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
    box-shadow: 0 8px 28px rgba(102, 126, 234, 0.2);
}

.floor-plan-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.floor-plan-card-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.floor-plan-card-badge {
    padding: 6px 12px;
    background: #f59e0b;
    color: white;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.floor-plan-card-title {
    font-size: 17px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 8px;
    letter-spacing: -0.3px;
}

.floor-plan-card-desc {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.5;
    margin-bottom: 12px;
}

.floor-plan-card-check {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 14px;
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.3s ease;
}

.floor-plan-option-card.selected .floor-plan-card-check {
    opacity: 1;
    transform: scale(1);
    background: #667eea;
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Modern Mobile Bottom Navigation - Always Sticky at Bottom */
.modern-mobile-nav {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(20px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
    border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
    padding: 10px 8px !important;
    padding-bottom: calc(10px + env(safe-area-inset-bottom)) !important;
    z-index: 10000 !important; /* Above action buttons */
    display: flex !important;
    justify-content: space-around !important;
    align-items: center !important;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1) !important;
    will-change: transform !important;
    transform: translateZ(0) !important;
    margin: 0 !important;
    box-sizing: border-box !important;
    min-height: 70px;
}

.modern-mobile-nav-item {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 4px !important;
    padding: 8px 12px !important;
    border-radius: 12px !important;
    color: #6b7280 !important;
    text-decoration: none !important;
    transition: all 0.2s ease !important;
    min-width: 60px !important;
    max-width: 80px !important;
    flex: 1 !important;
    -webkit-tap-highlight-color: transparent !important;
    position: relative !important;
}

.modern-mobile-nav-item i {
    font-size: 22px !important;
    line-height: 1 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.modern-mobile-nav-item span {
    font-size: 11px !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;
    text-align: center !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 100% !important;
}

.modern-mobile-nav-item.active {
    color: #667eea !important;
    background: rgba(102, 126, 234, 0.1) !important;
}

.modern-mobile-nav-item:active {
    transform: scale(0.95) !important;
    background: rgba(102, 126, 234, 0.15) !important;
}

/* Adjust action buttons to be above navigation */
.mobile-action-buttons {
    bottom: 70px !important; /* Position above navigation */
    z-index: 9999 !important; /* Below navigation */
}

/* Add padding to content to prevent overlap with navigation */
.mobile-booking-container {
    padding-bottom: calc(180px + env(safe-area-inset-bottom)) !important; /* Action buttons + Navigation + spacing */
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .modern-mobile-nav {
        padding: 8px 4px !important;
        padding-bottom: calc(8px + env(safe-area-inset-bottom)) !important;
        min-height: 66px;
    }
    
    .modern-mobile-nav-item {
        padding: 6px 8px !important;
        min-width: 50px !important;
        max-width: 70px !important;
    }
    
    .modern-mobile-nav-item i {
        font-size: 20px !important;
    }
    
    .modern-mobile-nav-item span {
        font-size: 10px !important;
    }
    
    .mobile-action-buttons {
        bottom: 66px !important;
    }
    
    .mobile-booking-container {
        padding-bottom: calc(170px + env(safe-area-inset-bottom)) !important;
    }
}

/* Client Search - Modern App Style */
/* Modern Client Search Wrapper - Redesigned */
.client-search-wrapper-mobile {
    position: relative;
    z-index: 100;
    width: 100%;
}

.client-search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: #ffffff;
    border-radius: 14px;
    border: 2px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    min-height: 56px;
    gap: 0;
}

.client-search-input-wrapper:focus-within {
    border-color: #667eea;
    background: #ffffff;
    box-shadow: 
        0 0 0 4px rgba(102, 126, 234, 0.08),
        0 4px 12px rgba(102, 126, 234, 0.12);
    transform: translateY(-1px);
}

.client-search-icon {
    padding: 0 18px;
    color: #6b7280;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    flex-shrink: 0;
    transition: color 0.2s ease;
}

.client-search-input-wrapper:focus-within .client-search-icon {
    color: #667eea;
}

.client-search-icon i {
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.client-search-input {
    flex: 1;
    padding: 18px 0;
    border: none;
    background: transparent;
    font-size: 16px;
    color: #111827;
    font-weight: 400;
    margin: 0;
    box-sizing: border-box;
    min-width: 0;
    letter-spacing: -0.01em;
}

.client-search-input:focus {
    outline: none;
}

.client-search-input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

.client-search-btn {
    padding: 0 20px;
    height: 100%;
    min-height: 56px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    -webkit-tap-highlight-color: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    white-space: nowrap;
    flex-shrink: 0;
    box-sizing: border-box;
    margin: 0;
    position: relative;
    overflow: hidden;
    letter-spacing: 0.3px;
}

.client-search-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.client-search-btn:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a3d8f 100%);
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.client-search-btn:active {
    transform: scale(0.98);
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.2);
}

.client-search-btn:hover::before {
    left: 100%;
}

/* Client Results Dropdown - Modern Bottom Sheet Style - Enhanced */
.client-results-dropdown-mobile {
    position: absolute;
    top: calc(100% + 10px);
    left: 0;
    right: 0;
    z-index: 9999;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 
        0 12px 40px rgba(0, 0, 0, 0.15),
        0 4px 12px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.08);
    max-height: 420px;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(10px);
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    flex-shrink: 0;
    vertical-align: middle;
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
    min-width: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    flex-shrink: 0;
    vertical-align: middle;
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
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    flex-shrink: 0;
    vertical-align: middle;
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
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    line-height: 1;
    white-space: nowrap;
    box-sizing: border-box;
    margin: 0;
}

.change-client-btn-mobile i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    flex-shrink: 0;
}

.change-client-btn-mobile:active {
    transform: scale(0.96);
    background: #fef2f2;
}

/* Booth Selection - Compact & Minimal Design with Horizontal Scroll */
.booth-grid-wrapper {
    position: relative;
    margin-bottom: 20px;
}

.booth-grid-mobile {
    display: flex;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    padding: 8px 4px 8px 4px;
    margin-bottom: 12px;
    gap: 0;
    /* Hide scrollbar but keep functionality */
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE and Edge */
    /* Enable momentum scrolling on iOS */
    -webkit-overflow-scrolling: touch;
    /* Snap scrolling for better UX - snap by row groups */
    scroll-snap-type: x mandatory;
    position: relative;
    width: 100%;
}

.booth-grid-mobile::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Opera */
}

/* Group booths into 5-row slides */
.booth-row-group {
    display: grid !important;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(5, auto);
    gap: 8px;
    min-width: 100%;
    width: 100%;
    flex-shrink: 0;
    scroll-snap-align: start;
    scroll-snap-stop: always;
    padding: 0 2px;
    box-sizing: border-box;
    /* Ensure all slides are visible and accessible */
    visibility: visible !important;
    opacity: 1 !important;
}

.booth-card-mobile {
    scroll-snap-align: none;
    min-width: 0;
    width: 100%;
}

/* Scroll indicators */
.booth-grid-wrapper::before,
.booth-grid-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 30px;
    pointer-events: none;
    z-index: 1;
    transition: opacity 0.3s ease;
}

.booth-grid-wrapper::before {
    left: 0;
    background: linear-gradient(to right, rgba(255, 255, 255, 0.95), transparent);
    opacity: 0;
}

.booth-grid-wrapper::after {
    right: 0;
    background: linear-gradient(to left, rgba(255, 255, 255, 0.95), transparent);
    opacity: 1;
}

.booth-grid-wrapper.scrolled-left::before {
    opacity: 1;
}

.booth-grid-wrapper.scrolled-right::after {
    opacity: 0;
}

.booth-grid-wrapper.scrolled-both::before,
.booth-grid-wrapper.scrolled-both::after {
    opacity: 1;
}

/* Scroll buttons */
.booth-scroll-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(102, 126, 234, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 2;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    transition: all 0.2s ease;
    opacity: 0.8;
    pointer-events: auto;
    font-size: 14px;
    -webkit-tap-highlight-color: transparent;
}

/* Show scroll buttons on mobile by default, hide on desktop hover */
@media (max-width: 768px) {
    .booth-scroll-btn {
        opacity: 0.9;
    }
}

@media (min-width: 769px) {
    .booth-scroll-btn {
        opacity: 0;
    }
    
    .booth-grid-wrapper:hover .booth-scroll-btn {
        opacity: 0.9;
    }
}

.booth-scroll-btn:hover {
    background: rgba(102, 126, 234, 1);
    transform: translateY(-50%) scale(1.1);
}

.booth-scroll-btn:active {
    transform: translateY(-50%) scale(0.95);
}

.booth-scroll-btn-left {
    left: 8px;
}

.booth-scroll-btn-right {
    right: 8px;
}

.booth-scroll-btn.hidden {
    display: none;
}

/* Lazy Loading Styles */
.booth-lazy-hidden {
    display: none !important;
}

.booth-load-more-container {
    margin-top: 16px;
    margin-bottom: 16px;
}

.booth-load-more-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 14px 24px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    -webkit-tap-highlight-color: transparent;
}

.booth-load-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

.booth-load-more-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.booth-load-more-btn i {
    transition: transform 0.3s ease;
}

.booth-load-more-btn.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.booth-load-more-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

@media (max-width: 360px) {
    .booth-grid-mobile {
        grid-auto-columns: minmax(90px, 1fr);
    }
    
    .booth-card-mobile {
        min-width: 90px;
    }
}

/* Ensure booth cards maintain minimum width for horizontal scroll */
.booth-card-mobile {
    min-width: 100px;
    flex-shrink: 0;
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
    box-sizing: border-box;
    margin: 0;
    line-height: 1.5;
    vertical-align: middle;
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
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    pointer-events: none;
}

.booth-search-filter-wrapper {
    position: relative;
}

/* A-Z Letter Tabs Navigation - Modern Design */
.booth-letter-tabs {
    display: flex;
    gap: 6px;
    margin-bottom: 16px;
    overflow-x: auto;
    padding-bottom: 8px;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
    scroll-padding: 0 16px;
}

.booth-letter-tabs::-webkit-scrollbar {
    display: none;
}

.booth-letter-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    padding: 10px 16px;
    min-width: 56px;
    background: #f9fafb;
    border: 1.5px solid rgba(102, 126, 234, 0.12);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
    position: relative;
    box-sizing: border-box;
}

.booth-letter-tab .letter-tab-label {
    font-size: 18px;
    font-weight: 700;
    color: #667eea;
    text-align: center;
    line-height: 1;
    display: block;
    letter-spacing: -0.3px;
}

.booth-letter-tab .letter-tab-count {
    font-size: 10px;
    color: #9ca3af;
    font-weight: 600;
    line-height: 1;
    display: block;
    margin-top: 2px;
}

.booth-letter-tab.active {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
    border-color: #667eea;
    border-width: 2px;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
    transform: translateY(-2px);
    animation: letterTabActive 0.3s ease;
}

.booth-letter-tab.active .letter-tab-label {
    color: #667eea;
    transform: scale(1.1);
}

.booth-letter-tab.active .letter-tab-count {
    color: #667eea;
    font-weight: 700;
}

@keyframes letterTabActive {
    0% {
        transform: translateY(0) scale(1);
    }
    50% {
        transform: translateY(-2px) scale(1.05);
    }
    100% {
        transform: translateY(-2px) scale(1);
    }
}

.booth-letter-tab:active {
    transform: scale(0.95);
}

/* Letter Content Sections */
.booth-letter-content {
    display: none;
    animation: fadeInUp 0.3s ease;
}

.booth-letter-content.active {
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

/* Selected Booths Summary - Modern Card - Enhanced - Sticky Below Progress */
.selected-booths-summary-mobile {
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 
        0 4px 12px rgba(0, 0, 0, 0.06),
        0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(102, 126, 234, 0.1);
    position: sticky;
    position: -webkit-sticky;
    top: calc(68px + 70px + 20px); /* Header + Action buttons + spacing */
    z-index: 997; /* Below action buttons (999) but above content */
    overflow: hidden;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Ensure sticky works on all browsers */
@supports (position: sticky) {
    .selected-booths-summary-mobile {
        position: sticky;
        top: calc(68px + 70px + 20px);
    }
}

/* Fallback for older browsers */
@supports not (position: sticky) {
    .selected-booths-summary-mobile {
        position: relative;
    }
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .selected-booths-summary-mobile {
        top: calc(64px + 66px + 16px); /* Smaller header and buttons on mobile */
        padding: 16px;
    }
}

.selected-booths-summary-mobile::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    opacity: 0.6;
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
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 16px;
    padding-right: 4px;
}

/* Custom scrollbar for selected booths list */
.selected-booths-list-mobile::-webkit-scrollbar {
    width: 6px;
}

.selected-booths-list-mobile::-webkit-scrollbar-track {
    background: rgba(102, 126, 234, 0.05);
    border-radius: 10px;
}

.selected-booths-list-mobile::-webkit-scrollbar-thumb {
    background: rgba(102, 126, 234, 0.3);
    border-radius: 10px;
}

.selected-booths-list-mobile::-webkit-scrollbar-thumb:hover {
    background: rgba(102, 126, 234, 0.5);
}

.selected-booth-item-mobile {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 14px;
    background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
    border-radius: 10px;
    margin-bottom: 8px;
    animation: slideInRight 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transition: all 0.2s ease;
    border: 1px solid rgba(102, 126, 234, 0.1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    box-sizing: border-box;
    gap: 12px;
    min-height: 48px;
}

.selected-booth-item-mobile:hover {
    transform: translateX(2px);
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.15);
    border-color: rgba(102, 126, 234, 0.2);
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

/* Selected Booth Info Box Design */
.selected-booth-info-mobile {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1.5px solid rgba(102, 126, 234, 0.15);
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 10px;
    box-shadow: 
        0 2px 8px rgba(0, 0, 0, 0.04),
        0 1px 3px rgba(0, 0, 0, 0.06);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.selected-booth-info-mobile::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    opacity: 0.8;
}

.selected-booth-info-mobile:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 4px 12px rgba(102, 126, 234, 0.12),
        0 2px 6px rgba(0, 0, 0, 0.08);
    border-color: rgba(102, 126, 234, 0.25);
}

/* Booth Header Section */
.selected-booth-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
    gap: 12px;
}

.selected-booth-main-info {
    flex: 1;
    min-width: 0;
}

.selected-booth-number {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    font-weight: 700;
    font-size: 16px;
    color: #1a1a1a;
    line-height: 1.3;
}

.selected-booth-number i {
    color: #667eea;
    font-size: 16px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.selected-booth-price-mobile {
    font-weight: 700;
    color: #22c55e;
    font-size: 18px;
    letter-spacing: -0.3px;
    line-height: 1.2;
    flex-shrink: 0;
    white-space: nowrap;
    text-align: right;
}

/* Booth Details Section */
.selected-booth-details {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid rgba(102, 126, 234, 0.08);
}

.selected-booth-detail-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #6b7280;
    background: rgba(102, 126, 234, 0.05);
    padding: 4px 8px;
    border-radius: 6px;
    font-weight: 500;
    line-height: 1.3;
}

.selected-booth-detail-item i {
    font-size: 10px;
    color: #667eea;
    flex-shrink: 0;
}

.selected-booth-detail-item .detail-label {
    font-weight: 600;
    color: #4b5563;
    margin-right: 2px;
}

.selected-booth-detail-item .detail-value {
    color: #1f2937;
    font-weight: 600;
}

/* Status Badge */
.selected-booth-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    line-height: 1.2;
}

.selected-booth-status.available {
    background: linear-gradient(135deg, rgba(28, 200, 138, 0.15) 0%, rgba(23, 166, 115, 0.15) 100%);
    color: #17a673;
}

.selected-booth-status.reserved {
    background: linear-gradient(135deg, rgba(246, 194, 62, 0.15) 0%, rgba(221, 162, 10, 0.15) 100%);
    color: #dda20a;
}

.selected-booth-status.booked {
    background: linear-gradient(135deg, rgba(231, 74, 59, 0.15) 0%, rgba(194, 51, 33, 0.15) 100%);
    color: #c23321;
}

.selected-booth-status.other {
    background: linear-gradient(135deg, rgba(54, 185, 204, 0.15) 0%, rgba(44, 159, 175, 0.15) 100%);
    color: #2c9faf;
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
    position: relative;
}

.summary-total-value-mobile.updated {
    animation: numberPop 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes numberPop {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.15);
        color: #16a34a;
    }
    100% {
        transform: scale(1);
    }
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

/* Action Buttons - Sticky Below Header - Flexible & Responsive */
.mobile-action-buttons {
    position: sticky;
    position: -webkit-sticky;
    top: 68px; /* Height of modern-mobile-header */
    left: 0;
    right: 0;
    width: 100%;
    z-index: 998; /* Below header (1000) but above content */
    background: rgba(255, 255, 255, 0.98);
    border-top: 0.5px solid rgba(0, 0, 0, 0.1);
    border-bottom: 0.5px solid rgba(0, 0, 0, 0.08);
    padding: 14px 16px;
    padding-bottom: calc(14px + env(safe-area-inset-bottom));
    box-shadow: 
        0 2px 12px rgba(0, 0, 0, 0.06),
        0 1px 4px rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    box-sizing: border-box;
    min-height: 70px;
    max-width: 100%;
    margin: 0;
}

.mobile-action-buttons-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    flex-wrap: nowrap;
}

/* Calculate top position dynamically based on header height */
@media (max-width: 480px) {
    .modern-mobile-header {
        min-height: 64px;
        padding: 10px 16px !important;
    }
    
    .mobile-action-buttons {
        top: 64px; /* Smaller header on mobile */
    }
}

/* Ensure sticky works on all browsers */
@supports (position: sticky) {
    .mobile-action-buttons {
        position: sticky;
        top: 68px;
    }
}

/* Fallback for older browsers */
@supports not (position: sticky) {
    .mobile-action-buttons {
        position: fixed;
        top: 68px;
    }
}


/* Responsive adjustments */
@media (max-width: 360px) {
    .mobile-action-buttons {
        padding: 10px 12px;
        padding-bottom: calc(10px + env(safe-area-inset-bottom));
        min-height: 64px;
    }
    
    .mobile-action-buttons-container {
        gap: 8px;
    }
    
    .mobile-action-buttons-container .mobile-btn {
        padding: 12px 14px;
        font-size: 13px;
        min-height: 48px;
    }
    
    .mobile-action-buttons-container .mobile-btn .btn-text {
        display: none;
    }
    
    .mobile-action-buttons-container .mobile-btn i {
        margin: 0;
        font-size: 18px;
    }
    
    .mobile-action-buttons-container .mobile-btn-secondary {
        min-width: 48px;
        max-width: 48px;
        flex: 0 0 48px;
    }
    
    .mobile-action-buttons-container .mobile-btn-primary {
        flex: 1;
        min-width: 0;
    }
}

@media (max-width: 480px) {
    .mobile-action-buttons {
        padding: 12px 14px;
        padding-bottom: calc(12px + env(safe-area-inset-bottom));
        min-height: 66px;
    }
    
    .mobile-action-buttons-container {
        gap: 10px;
    }
    
    .mobile-action-buttons-container .mobile-btn {
        padding: 14px 16px;
        font-size: 14px;
        min-height: 50px;
    }
}

@media (min-width: 481px) and (max-width: 767px) {
    .mobile-action-buttons {
        padding: 14px 20px;
        padding-bottom: calc(14px + env(safe-area-inset-bottom));
    }
    
    .mobile-action-buttons-container {
        gap: 14px;
    }
    
    .mobile-action-buttons-container .mobile-btn {
        padding: 16px 20px;
    }
}

@media (min-width: 768px) {
    .mobile-action-buttons {
        max-width: 768px;
        margin: 0 auto;
        left: auto;
        right: auto;
        padding: 16px 24px;
        padding-bottom: calc(16px + env(safe-area-inset-bottom));
    }
    
    .mobile-action-buttons-container {
        gap: 16px;
        max-width: 100%;
    }
    
    .mobile-action-buttons-container .mobile-btn {
        padding: 16px 24px;
    }
}

.mobile-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px 20px;
    border-radius: 12px;
    border: none;
    font-weight: 600;
    font-size: 15px;
    line-height: 1.4;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    white-space: nowrap;
    text-align: center;
    box-sizing: border-box;
    min-height: 52px;
}

.mobile-btn i {
    font-size: 16px;
    line-height: 1;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.mobile-btn .btn-text {
    line-height: 1.4;
    white-space: nowrap;
}
    border: none;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    -webkit-tap-highlight-color: transparent;
    letter-spacing: -0.2px;
    box-sizing: border-box;
    margin: 0;
    line-height: 1.4;
    white-space: nowrap;
    min-height: 52px;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    position: relative;
}

/* Responsive button sizing */
@media (max-width: 360px) {
    .mobile-btn {
        padding: 14px 16px;
        font-size: 14px;
        gap: 6px;
        min-height: 48px;
    }
    
    .mobile-btn i {
        font-size: 14px;
    }
}

@media (min-width: 481px) {
    .mobile-btn {
        padding: 16px 24px;
        font-size: 16px;
    }
}

.mobile-btn i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    vertical-align: middle;
    flex-shrink: 0;
    font-size: inherit;
}

.mobile-btn span {
    display: inline-block;
    flex-shrink: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Ensure icons and text align properly */
.mobile-action-buttons-container .mobile-btn {
    word-break: keep-all;
    overflow-wrap: normal;
    flex-shrink: 1;
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
    flex: 0 1 auto;
    min-width: 120px;
}

.mobile-btn-secondary:hover {
    background: #e5e7eb;
    border-color: #d1d5db;
}

.mobile-btn-secondary:active {
    background: #e5e7eb;
    transform: scale(0.96);
    border-color: #d1d5db;
}

.mobile-btn-primary {
    flex: 1 1 auto;
    min-width: 140px;
}

/* Button text and icon alignment */
/* Button hover and active states */
.mobile-btn:hover {
    transform: translateY(-1px);
}

.mobile-btn:active {
    transform: scale(0.97) translateY(0);
}

.mobile-action-buttons-container .mobile-btn-secondary {
    flex: 0 0 auto;
    min-width: min(120px, 35%);
    max-width: 40%;
}

.mobile-action-buttons-container .mobile-btn-primary {
    flex: 1 1 auto;
    min-width: min(160px, 60%);
    max-width: 65%;
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
    
    /* Client Card View Styles */
    .client-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .client-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        border-color: rgba(102, 126, 234, 0.3);
    }
    
    .client-card-details {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
    }
    
    .select-client-card-btn {
        margin-top: 12px;
        width: 100%;
        padding: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .select-client-card-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }
    
    .select-client-card-btn:active {
        transform: translateY(0);
    }
    
    /* View Toggle Button Styles */
    .view-toggle-btn {
        padding: 6px 12px;
        border: none;
        background: transparent;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        color: #6b7280;
        transition: all 0.2s ease;
    }
    
    .view-toggle-btn:hover {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }
    
    .view-toggle-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
    }
    
    /* Client Cards Container */
    .client-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        max-height: calc(100vh - 350px);
        overflow-y: auto;
        padding: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .client-cards-container {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 12px;
        }
    }
    
    @media (max-width: 480px) {
        .client-cards-container {
            grid-template-columns: 1fr;
        }
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
            <button type="button" class="app-header-btn" id="clearBookingBtn" title="Clear Booking" aria-label="Clear booking and start fresh" style="color: #ef4444;">
                <i class="fas fa-undo"></i>
            </button>
            @if(isset($currentFloorPlan) && $currentFloorPlan)
            <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $currentFloorPlan->id]) }}" class="app-header-btn" title="View Floor Plan" aria-label="View floor plan canvas">
                <i class="fas fa-map-marked-alt"></i>
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Action Buttons (Sticky Below Header) - Well Organized -->
<div class="mobile-action-buttons">
    <div class="mobile-action-buttons-container">
        <button type="button" class="mobile-btn mobile-btn-secondary" onclick="window.location.href='{{ route('books.index') }}'" aria-label="Cancel booking">
            <i class="fas fa-times"></i>
            <span class="btn-text">Cancel</span>
        </button>
        <button type="submit" form="bookingForm" class="mobile-btn mobile-btn-primary" id="submitBtn" disabled aria-label="Create booking">
            <i class="fas fa-check"></i>
            <span class="btn-text">Create Booking</span>
        </button>
    </div>
</div>

<!-- Main Content -->
<div class="mobile-booking-container" style="display: block !important; visibility: visible !important; opacity: 1 !important; padding: 16px !important; padding-bottom: 120px !important;">
    <!-- Progress Indicator - Enhanced with Step Info -->
    <div class="booking-progress-container" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 16px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08); border: 1px solid rgba(102, 126, 234, 0.1);">
        <!-- Progress Steps Visual -->
        <div class="progress-steps-wrapper" style="position: relative; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 1;">
                <!-- Step 1: Client -->
                <div class="progress-step-container" style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative;">
                    <div id="progressStep1" class="progress-step" style="width: 44px; height: 44px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 18px; flex-shrink: 0; z-index: 2; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); transition: all 0.3s ease;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="step-info" id="stepInfo1" style="margin-top: 10px; text-align: center; max-width: 100px;">
                        <div class="step-title" style="font-size: 13px; font-weight: 700; color: #667eea; margin-bottom: 4px; line-height: 1.3;">Client</div>
                        <div class="step-status" style="font-size: 11px; color: #9ca3af; line-height: 1.3;">Select client</div>
                    </div>
                </div>
                
                <!-- Progress Line 1 -->
                <div id="progressLine1" class="progress-line" style="flex: 1; height: 4px; margin: 0 8px; border-radius: 2px; background: #e5e7eb; position: relative; overflow: hidden; margin-top: -30px;">
                    <div class="progress-line-fill" style="position: absolute; top: 0; left: 0; height: 100%; width: 0%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); border-radius: 2px; transition: width 0.5s ease;"></div>
                </div>
                
                <!-- Step 2: Details -->
                <div class="progress-step-container" style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative;">
                    <div id="progressStep2" class="progress-step" style="width: 44px; height: 44px; border-radius: 50%; background: #e5e7eb; color: #9ca3af; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 18px; flex-shrink: 0; z-index: 2; transition: all 0.3s ease;">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="step-info" id="stepInfo2" style="margin-top: 10px; text-align: center; max-width: 100px;">
                        <div class="step-title" style="font-size: 13px; font-weight: 700; color: #9ca3af; margin-bottom: 4px; line-height: 1.3;">Details</div>
                        <div class="step-status" style="font-size: 11px; color: #d1d5db; line-height: 1.3;">Set date</div>
                    </div>
                </div>
                
                <!-- Progress Line 2 -->
                <div id="progressLine2" class="progress-line" style="flex: 1; height: 4px; margin: 0 8px; border-radius: 2px; background: #e5e7eb; position: relative; overflow: hidden; margin-top: -30px;">
                    <div class="progress-line-fill" style="position: absolute; top: 0; left: 0; height: 100%; width: 0%; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); border-radius: 2px; transition: width 0.5s ease;"></div>
                </div>
                
                <!-- Step 3: Booths -->
                <div class="progress-step-container" style="flex: 1; display: flex; flex-direction: column; align-items: center; position: relative;">
                    <div id="progressStep3" class="progress-step" style="width: 44px; height: 44px; border-radius: 50%; background: #e5e7eb; color: #9ca3af; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 18px; flex-shrink: 0; z-index: 2; transition: all 0.3s ease;">
                        <i class="fas fa-cube"></i>
                    </div>
                    <div class="step-info" id="stepInfo3" style="margin-top: 10px; text-align: center; max-width: 100px;">
                        <div class="step-title" style="font-size: 13px; font-weight: 700; color: #9ca3af; margin-bottom: 4px; line-height: 1.3;">Booths</div>
                        <div class="step-status" style="font-size: 11px; color: #d1d5db; line-height: 1.3;">Select booths</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Current Step Highlight -->
        <div id="currentStepHighlight" class="current-step-highlight" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border-radius: 12px; padding: 12px 16px; border-left: 4px solid #667eea; margin-top: 8px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-info-circle" style="color: #667eea; font-size: 16px; flex-shrink: 0;"></i>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 2px; line-height: 1.3;">Current Step</div>
                    <div id="currentStepMessage" style="font-size: 12px; color: #6b7280; line-height: 1.4;">Please select a client to begin</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Booths Summary - Sticky Below Progress -->
    <div class="selected-booths-summary-mobile">
        <div class="selected-booths-title-mobile" style="display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-list" style="line-height: 1; flex-shrink: 0;"></i>
            <span style="line-height: 1.4;">Selected Booths</span>
        </div>
        <div id="selectedBoothsList" class="selected-booths-list-mobile">
            <div class="empty-state-mobile" style="padding: 20px;">
                <p style="font-size: 14px; color: #9ca3af;">No booths selected</p>
            </div>
        </div>
        <div class="summary-total-mobile" style="padding-top: 16px; border-top: 1px solid #f3f4f6; margin-top: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <div class="summary-total-label-mobile" style="display: flex; align-items: center; gap: 6px; line-height: 1.4;">
                    <i class="fas fa-cube" style="font-size: 14px; color: #6b7280; line-height: 1; flex-shrink: 0;"></i> 
                    <span>Total Booths:</span>
                </div>
                <span id="totalBooths" style="font-weight: 700; font-size: 18px; color: #667eea; line-height: 1; flex-shrink: 0;">0</span>
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
                <div style="flex: 1;">
                    <span>Filter by Floor Plan</span>
                    <div id="selectedFloorPlanName" class="floor-plan-selected-name" style="display: {{ isset($currentFloorPlan) && $currentFloorPlan ? 'block' : 'none' }};">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="selectedFloorPlanNameText">
                            @if(isset($currentFloorPlan) && $currentFloorPlan)
                                {{ $currentFloorPlan->name }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Floor Plan Image Display (always shown when selected) -->
            <div id="floorPlanImageDisplay" class="floor-plan-image-display" style="display: {{ isset($currentFloorPlan) && $currentFloorPlan ? 'block' : 'none' }};">
                <div class="floor-plan-image-wrapper">
                    <img src="{{ isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->floor_image ? asset($currentFloorPlan->floor_image) : '' }}" 
                         alt="{{ isset($currentFloorPlan) && $currentFloorPlan ? $currentFloorPlan->name : '' }}" 
                         class="floor-plan-image"
                         id="floorPlanImage"
                         onerror="this.style.display='none'; this.closest('.floor-plan-image-wrapper').classList.add('no-image');">
                    <div class="floor-plan-image-overlay">
                        <div class="floor-plan-image-info">
                            <h4 id="floorPlanImageName">{{ isset($currentFloorPlan) && $currentFloorPlan ? $currentFloorPlan->name : '' }}</h4>
                            <p id="floorPlanImageDesc">{{ isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->description ? $currentFloorPlan->description : (isset($currentFloorPlan) && $currentFloorPlan ? 'No description available' : '') }}</p>
                        </div>
                        <button type="button" class="floor-plan-image-close" onclick="closeFloorPlanImage()" aria-label="Close image">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Floor Plan Selection - Minimal Design -->
            <div class="floor-plan-minimal-container">
                <!-- Hidden select for form submission -->
                <select class="mobile-form-select" id="floor_plan_filter" name="floor_plan_filter" style="display: none;">
                    <option value="">All Floor Plans</option>
                    @foreach($floorPlans as $fp)
                        <option value="{{ $fp->id }}" 
                                data-image="{{ $fp->floor_image ? asset($fp->floor_image) : '' }}"
                                data-name="{{ $fp->name }}"
                                data-description="{{ $fp->description ?? '' }}"
                                {{ (isset($floorPlanId) && $floorPlanId == $fp->id) ? 'selected' : '' }}>
                            {{ $fp->name }}
                            @if($fp->is_default) (Default) @endif
                            @if($fp->event) - {{ $fp->event->title }} @endif
                        </option>
                    @endforeach
                </select>
                
                <!-- Icon View - Minimal -->
                <div class="floor-plan-view floor-plan-icon-view active" data-view="icon">
                        <div class="floor-plan-options-icon">
                            <div class="floor-plan-option-icon {{ (isset($floorPlanId) && $floorPlanId == '') ? 'selected' : '' }}" data-value="" data-image="" data-name="All Floor Plans" data-description="">
                                <div class="floor-plan-icon-wrapper">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <span class="floor-plan-label">All</span>
                            </div>
                            @foreach($floorPlans as $fp)
                            <div class="floor-plan-option-icon {{ (isset($floorPlanId) && $floorPlanId == $fp->id) ? 'selected' : '' }}" 
                                 data-value="{{ $fp->id }}" 
                                 data-image="{{ $fp->floor_image ? asset($fp->floor_image) : '' }}"
                                 data-name="{{ $fp->name }}"
                                 data-description="{{ $fp->description ?? '' }}">
                                <div class="floor-plan-icon-wrapper">
                                    <i class="fas fa-map"></i>
                                </div>
                                <span class="floor-plan-label">{{ $fp->name }}</span>
                                @if($fp->is_default)
                                    <span class="floor-plan-badge">Default</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <small style="display: flex; align-items: center; color: #9ca3af; font-size: 12px; margin-top: 12px; gap: 4px; line-height: 1.4;">
                        <i class="fas fa-info-circle" style="font-size: 11px; line-height: 1; flex-shrink: 0;"></i>
                        <span>Filter booths by specific floor plan</span>
                    </small>
                </div>
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
                        <div class="client-name" id="selectedClientName" style="display: none;"></div>
                        <div class="client-company" id="selectedClientCompany" style="display: none;"></div>
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
                                   placeholder="Search by name, company, email, or phone..." 
                                   autocomplete="off"
                                   spellcheck="false">
                            <button type="button" class="client-search-btn" id="btnSearchSelectClient" data-toggle="modal" data-target="#searchClientModal" aria-label="Open advanced search">
                                <i class="fas fa-sliders-h" style="margin-right: 6px; font-size: 13px;"></i>
                                <span>Filter</span>
                            </button>
                        </div>
                        
                        <!-- Inline Search Results Dropdown -->
                        <div id="inlineClientResults" class="client-results-dropdown-mobile" style="display: none;">
                            <div id="inlineClientResultsList"></div>
                        </div>
                    </div>
            @error('clientid')
                <div style="color: #ef4444; font-size: 13px; margin-top: 8px; display: flex; align-items: center; gap: 6px; line-height: 1.4;">
                    <i class="fas fa-exclamation-circle" style="font-size: 12px; line-height: 1; flex-shrink: 0;"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 12px; flex-wrap: wrap; gap: 8px;">
                        <small style="color: #9ca3af; font-size: 13px; flex: 1; min-width: 200px; display: flex; align-items: center; gap: 4px;">
                            <i class="fas fa-info-circle" style="font-size: 12px; line-height: 1; flex-shrink: 0;"></i> 
                            <span>Start typing to see suggestions</span>
                        </small>
                        <button type="button" class="mobile-btn mobile-btn-secondary" style="padding: 12px 18px; font-size: 14px; font-weight: 600; white-space: nowrap; flex-shrink: 0;" data-toggle="modal" data-target="#createClientModal">
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
                <small style="display: flex; align-items: center; color: #9ca3af; font-size: 12px; margin-top: 6px; gap: 4px; line-height: 1.4;">
                    <i class="fas fa-info-circle" style="font-size: 11px; line-height: 1; flex-shrink: 0;"></i>
                    <span>Select date and time for this booking</span>
                </small>
                @error('date_book')
                    <div style="color: #ef4444; font-size: 13px; margin-top: 8px; display: flex; align-items: center; gap: 6px; line-height: 1.4;">
                        <i class="fas fa-exclamation-circle" style="font-size: 12px; line-height: 1; flex-shrink: 0;"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>
            <div class="mobile-form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <label for="type" class="mobile-form-label" style="margin-bottom: 0;">Booking Type</label>
                    <div class="booking-type-view-switcher">
                        <button type="button" class="view-switch-btn active" data-view="icon" aria-label="Icon view">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="view-switch-btn" data-view="list" aria-label="List view">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button" class="view-switch-btn" data-view="card" aria-label="Card view">
                            <i class="fas fa-th"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Hidden select for form submission -->
                <select class="mobile-form-select" id="type" name="type" style="display: none;">
                    <option value="1" {{ old('type', 1) == 1 ? 'selected' : '' }}>Regular Booking</option>
                    <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>Special Booking</option>
                    <option value="3" {{ old('type') == 3 ? 'selected' : '' }}>Temporary Booking</option>
                </select>
                
                <!-- Icon View -->
                <div class="booking-type-view booking-type-icon-view active" data-view="icon">
                    <div class="booking-type-options-icon">
                        <div class="booking-type-option-icon {{ old('type', 1) == 1 ? 'selected' : '' }}" data-value="1">
                            <div class="booking-type-icon-wrapper">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <span class="booking-type-label">Regular</span>
                        </div>
                        <div class="booking-type-option-icon {{ old('type') == 2 ? 'selected' : '' }}" data-value="2">
                            <div class="booking-type-icon-wrapper">
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="booking-type-label">Special</span>
                        </div>
                        <div class="booking-type-option-icon {{ old('type') == 3 ? 'selected' : '' }}" data-value="3">
                            <div class="booking-type-icon-wrapper">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="booking-type-label">Temporary</span>
                        </div>
                    </div>
                </div>
                
                <!-- List View -->
                <div class="booking-type-view booking-type-list-view" data-view="list">
                    <div class="booking-type-options-list">
                        <div class="booking-type-option-list {{ old('type', 1) == 1 ? 'selected' : '' }}" data-value="1">
                            <div class="booking-type-list-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="booking-type-list-content">
                                <div class="booking-type-list-title">Regular Booking</div>
                                <div class="booking-type-list-desc">Standard booking with full access</div>
                            </div>
                            <div class="booking-type-list-check">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="booking-type-option-list {{ old('type') == 2 ? 'selected' : '' }}" data-value="2">
                            <div class="booking-type-list-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="booking-type-list-content">
                                <div class="booking-type-list-title">Special Booking</div>
                                <div class="booking-type-list-desc">Premium booking with special privileges</div>
                            </div>
                            <div class="booking-type-list-check">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="booking-type-option-list {{ old('type') == 3 ? 'selected' : '' }}" data-value="3">
                            <div class="booking-type-list-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="booking-type-list-content">
                                <div class="booking-type-list-title">Temporary Booking</div>
                                <div class="booking-type-list-desc">Short-term booking arrangement</div>
                            </div>
                            <div class="booking-type-list-check">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Card View -->
                <div class="booking-type-view booking-type-card-view" data-view="card">
                    <div class="booking-type-options-card">
                        <div class="booking-type-option-card {{ old('type', 1) == 1 ? 'selected' : '' }}" data-value="1">
                            <div class="booking-type-card-header">
                                <div class="booking-type-card-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="booking-type-card-badge">Standard</div>
                            </div>
                            <div class="booking-type-card-title">Regular Booking</div>
                            <div class="booking-type-card-desc">Standard booking with full access to all features</div>
                            <div class="booking-type-card-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="booking-type-option-card {{ old('type') == 2 ? 'selected' : '' }}" data-value="2">
                            <div class="booking-type-card-header">
                                <div class="booking-type-card-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="booking-type-card-badge premium">Premium</div>
                            </div>
                            <div class="booking-type-card-title">Special Booking</div>
                            <div class="booking-type-card-desc">Premium booking with special privileges and benefits</div>
                            <div class="booking-type-card-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="booking-type-option-card {{ old('type') == 3 ? 'selected' : '' }}" data-value="3">
                            <div class="booking-type-card-header">
                                <div class="booking-type-card-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="booking-type-card-badge temp">Temporary</div>
                            </div>
                            <div class="booking-type-card-title">Temporary Booking</div>
                            <div class="booking-type-card-desc">Short-term booking arrangement for limited time</div>
                            <div class="booking-type-card-check">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <small style="display: block; color: #9ca3af; font-size: 12px; margin-top: 12px;">
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
                <div style="display: flex; gap: 6px; flex-shrink: 0;">
                    <button type="button" class="mobile-btn mobile-btn-secondary" style="padding: 8px 12px; font-size: 13px; font-weight: 600; white-space: nowrap; min-width: auto; flex: 0 0 auto;" onclick="selectAllBooths()">
                        <i class="fas fa-check-double" style="margin-right: 4px; font-size: 11px;"></i> All
                    </button>
                    <button type="button" class="mobile-btn mobile-btn-secondary" style="padding: 8px 12px; font-size: 13px; font-weight: 600; white-space: nowrap; min-width: auto; flex: 0 0 auto;" onclick="clearSelection()">
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
            
            <!-- A-Z Letter Tabs Navigation -->
            <div class="booth-letter-tabs" id="boothLetterTabs">
                @foreach($boothsByCategory as $letter => $letterData)
                <button type="button" 
                        class="booth-letter-tab {{ $loop->first ? 'active' : '' }}" 
                        data-letter="{{ $letter }}"
                        onclick="switchBoothLetter('{{ $letter }}')">
                    <span class="letter-tab-label">{{ $letter === '#' ? '#' : $letter }}</span>
                    <span class="letter-tab-count">{{ $letterData['booths']->count() }}</span>
                </button>
                @endforeach
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
                <small style="color: #9ca3af; font-size: 11px; display: flex; align-items: center; gap: 4px; line-height: 1.4;">
                    <i class="fas fa-info-circle" style="font-size: 10px; line-height: 1; flex-shrink: 0;"></i>
                    <span id="boothCount">{{ $booths->count() }}</span> booths available
                </small>
                <small style="color: #9ca3af; font-size: 11px; display: none; line-height: 1.4;" id="filteredBoothCount">
                    <span id="filteredCount">0</span> matching booths
                </small>
            </div>
            
            <!-- Help Button & Info Panel -->
            <div style="margin-bottom: 12px; position: relative;">
                <button type="button" class="booth-help-btn" id="boothHelpBtn" onclick="toggleBoothHelp()" aria-label="Show booth selection help" title="Click for help">
                    <i class="fas fa-question-circle"></i>
                    <span class="help-btn-text">Help</span>
                </button>
                <div class="booth-grid-info" id="boothGridInfo" style="display: none; background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%); border: 1px solid rgba(102, 126, 234, 0.15); border-radius: 12px; padding: 12px 16px; margin-top: 8px;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <div style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 8px; background: rgba(102, 126, 234, 0.15); display: flex; align-items: center; justify-content: center; color: #667eea;">
                            <i class="fas fa-info-circle" style="font-size: 14px;"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px; line-height: 1.3;">
                                <i class="fas fa-arrows-alt-h" style="margin-right: 6px; color: #667eea;"></i>
                                Slide left/right to see more booths (5 rows per slide)
                            </div>
                            <div style="font-size: 11px; color: #6b7280; line-height: 1.4;">
                                All <span id="boothTotalCount">{{ $booths->count() }}</span> booths are loaded. Each slide shows 5 rows (15 booths). Slide right to see more booths.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Booth Grids by Letter (A-Z) -->
            @foreach($boothsByCategory as $letter => $letterData)
            <div class="booth-letter-content {{ $loop->first ? 'active' : '' }}" 
                 id="letterContent_{{ $letter }}" 
                 data-letter="{{ $letter }}">
                <div class="booth-grid-wrapper" id="boothWrapper_{{ $letter }}">
                    <button type="button" class="booth-scroll-btn booth-scroll-btn-left" onclick="scrollBoothGrid('boothSelector_{{ $letter }}', 'left')" aria-label="Scroll left">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="booth-scroll-btn booth-scroll-btn-right" onclick="scrollBoothGrid('boothSelector_{{ $letter }}', 'right')" aria-label="Scroll right">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <div class="booth-grid-mobile" id="boothSelector_{{ $letter }}" data-total="{{ $letterData['booths']->count() }}" data-visible="15">
                        @php
                            $rowsPerSlide = 5;
                            $itemsPerRow = 3;
                            $itemsPerSlide = $rowsPerSlide * $itemsPerRow; // 15 items per slide (5 rows × 3 columns)
                            $boothsChunked = $letterData['booths']->chunk($itemsPerSlide);
                        @endphp
                        @foreach($boothsChunked as $slideIndex => $slideBooths)
                        <div class="booth-row-group" data-slide="{{ $slideIndex }}">
                            @foreach($slideBooths as $index => $booth)
                            @php
                                $globalIndex = ($slideIndex * $itemsPerSlide) + $index;
                            @endphp
                    <div class="booth-card-mobile {{ in_array($booth->id, old('booth_ids', [])) ? 'selected' : '' }}" 
                         data-booth-id="{{ $booth->id }}" 
                         data-price="{{ $booth->price }}"
                         data-booth-number="{{ strtolower($booth->booth_number) }}"
                         data-category="{{ $booth->category ? strtolower($booth->category->name) : '' }}"
                         data-status="{{ strtolower($booth->getStatusLabel()) }}"
                         data-index="{{ $globalIndex }}"
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
                        @endforeach
                    </div>
                </div>
                <!-- All booths are loaded and accessible via horizontal sliding -->
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
            
            <div class="booth-grid-wrapper" id="boothWrapper">
                <button type="button" class="booth-scroll-btn booth-scroll-btn-left" onclick="scrollBoothGrid('boothSelector', 'left')" aria-label="Scroll left">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" class="booth-scroll-btn booth-scroll-btn-right" onclick="scrollBoothGrid('boothSelector', 'right')" aria-label="Scroll right">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="booth-grid-mobile" id="boothSelector" data-total="{{ $booths->count() }}" data-visible="15">
                    @php
                        $rowsPerSlide = 5;
                        $itemsPerRow = 3;
                        $itemsPerSlide = $rowsPerSlide * $itemsPerRow; // 15 items per slide (5 rows × 3 columns)
                        $boothsChunked = $booths->chunk($itemsPerSlide);
                    @endphp
                    @foreach($boothsChunked as $slideIndex => $slideBooths)
                    <div class="booth-row-group" data-slide="{{ $slideIndex }}">
                        @foreach($slideBooths as $index => $booth)
                        @php
                            $globalIndex = ($slideIndex * $itemsPerSlide) + $index;
                        @endphp
                <div class="booth-card-mobile {{ in_array($booth->id, old('booth_ids', [])) ? 'selected' : '' }}" 
                     data-booth-id="{{ $booth->id }}" 
                     data-price="{{ $booth->price }}"
                     data-booth-number="{{ strtolower($booth->booth_number) }}"
                     data-category="{{ $booth->category ? strtolower($booth->category->name) : '' }}"
                     data-status="{{ strtolower($booth->getStatusLabel()) }}"
                     data-index="{{ $globalIndex }}"
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
                    @endforeach
                </div>
            </div>
            <!-- All booths are loaded and accessible via horizontal sliding -->
            @else
            <div class="empty-state-mobile" style="padding: 32px 20px;">
                <i class="fas fa-inbox" style="font-size: 48px; color: #d1d5db; margin-bottom: 16px;"></i>
                <p style="font-size: 15px; font-weight: 500; color: #6b7280; margin-bottom: 8px;">No available booths found</p>
                <p style="font-size: 13px; color: #9ca3af;">Try selecting a different floor plan or check back later</p>
            </div>
            @endif
            @error('booth_ids')
                <div style="color: #ef4444; font-size: 13px; margin-top: 12px; display: flex; align-items: center; gap: 6px; line-height: 1.4;">
                    <i class="fas fa-exclamation-circle" style="font-size: 12px; line-height: 1; flex-shrink: 0;"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

    </form>
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
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h6 class="mb-0 font-weight-bold"><i class="fas fa-users mr-1"></i><span id="clientResultsCount">0</span> Clients</h6>
                        <div id="clientViewToggle" style="display: flex; gap: 6px; background: #f3f4f6; padding: 4px; border-radius: 8px;">
                            <button type="button" class="view-toggle-btn active" data-view="card" style="padding: 6px 12px; border: none; background: transparent; border-radius: 6px; cursor: pointer; font-size: 12px; color: #6b7280;">
                                <i class="fas fa-th-large"></i> Card
                            </button>
                            <button type="button" class="view-toggle-btn" data-view="list" style="padding: 6px 12px; border: none; background: transparent; border-radius: 6px; cursor: pointer; font-size: 12px; color: #6b7280;">
                                <i class="fas fa-list"></i> List
                            </button>
                        </div>
                    </div>
                    <div id="clientSearchResultsList" class="client-cards-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; max-height: calc(100vh - 350px); overflow-y: auto; padding: 0.5rem;"></div>
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
    
    // Force header to be sticky
    function forceHeaderSticky() {
        const header = document.getElementById('mobileHeader');
        if (header) {
            header.style.position = '-webkit-sticky';
            header.style.position = 'sticky';
            header.style.top = '0';
            header.style.left = '0';
            header.style.right = '0';
            header.style.zIndex = '1000';
            header.style.width = '100%';
            header.style.maxWidth = '100%';
            header.style.boxSizing = 'border-box';
            header.style.margin = '0';
            header.style.display = 'block';
            header.style.visibility = 'visible';
            header.style.opacity = '1';
            header.style.background = '#ffffff';
            header.style.willChange = 'transform';
            header.style.transform = 'translateZ(0)';
            header.style.webkitTransform = 'translateZ(0)';
        }
    }
    
    // Force sticky on load and continuously
    forceHeaderSticky();
    setInterval(forceHeaderSticky, 100);
    
    // Also force on scroll
    window.addEventListener('scroll', forceHeaderSticky, { passive: true });
    window.addEventListener('resize', forceHeaderSticky, { passive: true });
    
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
            mobileHeader.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important; position: -webkit-sticky !important; position: sticky !important; top: 0 !important; left: 0 !important; right: 0 !important; z-index: 1000 !important; background: #ffffff !important; width: 100% !important; max-width: 100% !important; box-sizing: border-box !important; margin: 0 !important; will-change: transform !important; transform: translateZ(0) !important; -webkit-transform: translateZ(0) !important;';
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

// LocalStorage Helper Functions
function saveToLocalStorage(key, value) {
    try {
        localStorage.setItem('booking_create_' + key, JSON.stringify(value));
    } catch(e) {
        console.warn('Failed to save to localStorage:', e);
    }
}

function getFromLocalStorage(key, defaultValue) {
    try {
        const item = localStorage.getItem('booking_create_' + key);
        return item ? JSON.parse(item) : defaultValue;
    } catch(e) {
        console.warn('Failed to read from localStorage:', e);
        return defaultValue;
    }
}

// Floor Plan Selector - View Switching and Selection
$(document).ready(function() {
    // Restore view mode preference
    const savedFloorPlanView = getFromLocalStorage('floor_plan_view', 'icon');
    $(`.floor-plan-view-switcher .view-switch-btn[data-view="${savedFloorPlanView}"]`).click();
    
    // View switcher functionality
    $('.floor-plan-view-switcher .view-switch-btn').on('click', function() {
        const view = $(this).data('view');
        
        // Save view preference
        saveToLocalStorage('floor_plan_view', view);
        
        // Update active button
        $('.floor-plan-view-switcher .view-switch-btn').removeClass('active');
        $(this).addClass('active');
        
        // Switch views
        $('.floor-plan-view').removeClass('active');
        $(`.floor-plan-view[data-view="${view}"]`).addClass('active');
    });
    
    // Handle floor plan selection
    function selectFloorPlan(value, image, name, description, shouldReload) {
        // Save to localStorage
        saveToLocalStorage('floor_plan', {
            value: value,
            image: image,
            name: name,
            description: description
        });
        
        // Update hidden select
        $('#floor_plan_filter').val(value);
        
        // Update all view options (only icon view now)
        $('.floor-plan-option-icon').removeClass('selected');
        $(`.floor-plan-option-icon[data-value="${value}"]`).addClass('selected');
        
        // Update selected floor plan name display
        const selectedNameDisplay = $('#selectedFloorPlanName');
        const selectedNameText = $('#selectedFloorPlanNameText');
        
        if (value && name && name !== 'All Floor Plans') {
            // Show selected floor plan name
            selectedNameText.text(name);
            selectedNameDisplay.slideDown(200);
        } else {
            // Hide selected floor plan name
            selectedNameDisplay.slideUp(200);
        }
        
        // Always show image display when a floor plan is selected
        const imageDisplay = $('#floorPlanImageDisplay');
        const imageElement = $('#floorPlanImage');
        const imageInfo = imageDisplay.find('.floor-plan-image-info');
        const imageWrapper = imageDisplay.find('.floor-plan-image-wrapper');
        
        if (value && value !== '') {
            // Always show the display when a floor plan is selected
            if (image) {
                // Check if image exists
                const img = new Image();
                img.onload = function() {
                    // Image exists, show it
                    imageElement.attr('src', image);
                    imageElement.attr('alt', name);
                    imageElement.show();
                    imageWrapper.removeClass('no-image');
                    imageInfo.find('h4').text(name);
                    imageInfo.find('p').text(description || '');
                    $('#floorPlanImageName').text(name);
                    $('#floorPlanImageDesc').text(description || '');
                    imageDisplay.slideDown(300);
                };
                img.onerror = function() {
                    // Image doesn't exist, but still show display with placeholder
                    imageElement.attr('src', '');
                    imageElement.attr('alt', name);
                    imageInfo.find('h4').text(name);
                    imageInfo.find('p').text(description || 'No image available');
                    // Add no-image class for styling
                    imageWrapper.addClass('no-image');
                    imageElement.hide();
                    $('#floorPlanImageName').text(name);
                    $('#floorPlanImageDesc').text(description || 'No image available');
                    imageDisplay.slideDown(300);
                };
                img.src = image;
            } else {
                // No image URL, but still show display with info
                imageElement.attr('src', '');
                imageElement.attr('alt', name);
                imageInfo.find('h4').text(name);
                imageInfo.find('p').text(description || 'No image available');
                imageWrapper.addClass('no-image');
                imageElement.hide();
                $('#floorPlanImageName').text(name);
                $('#floorPlanImageDesc').text(description || 'No image available');
                imageDisplay.slideDown(300);
            }
        } else {
            // Hide image display when "All Floor Plans" is selected
            imageDisplay.slideUp(300);
        }
        
        // Only reload if explicitly requested (user click, not initialization)
        if (shouldReload !== false) {
            // Check if the value is different from current URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const currentFloorPlanId = urlParams.get('floor_plan_id');
            
            // Only reload if the value has changed
            if (value !== currentFloorPlanId) {
                filterByFloorPlan(value);
            }
        }
    }
    
    // Icon view selection (only view available)
    $('.floor-plan-option-icon').on('click', function() {
        const value = $(this).data('value');
        const image = $(this).data('image');
        const name = $(this).data('name');
        const description = $(this).data('description');
        selectFloorPlan(value, image, name, description, true); // true = allow reload
    });
    
    // Close image display
    window.closeFloorPlanImage = function() {
        $('#floorPlanImageDisplay').slideUp(300);
    };
    
    // Initialize with saved or default selected value (without reloading)
    const urlParams = new URLSearchParams(window.location.search);
    const urlFloorPlanId = urlParams.get('floor_plan_id');
    const savedFloorPlan = getFromLocalStorage('floor_plan', null);
    
    // Priority: URL parameter > Saved preference > Default
    let defaultValue = urlFloorPlanId || (savedFloorPlan ? savedFloorPlan.value : '');
    
    if (defaultValue) {
        const selectedOption = $(`#floor_plan_filter option[value="${defaultValue}"]`);
        if (selectedOption.length) {
            const image = selectedOption.data('image') || (savedFloorPlan ? savedFloorPlan.image : '');
            const name = selectedOption.data('name') || (savedFloorPlan ? savedFloorPlan.name : '');
            const description = selectedOption.data('description') || (savedFloorPlan ? savedFloorPlan.description : '');
            // false = don't reload on initialization
            selectFloorPlan(defaultValue, image, name, description, false);
        } else if (savedFloorPlan && savedFloorPlan.value) {
            // If saved value doesn't exist in options, try to restore saved data
            selectFloorPlan(savedFloorPlan.value, savedFloorPlan.image, savedFloorPlan.name, savedFloorPlan.description, false);
        }
    } else {
        // If no value selected, make sure "All" is selected
        $('.floor-plan-option-icon[data-value=""], .floor-plan-option-list[data-value=""], .floor-plan-option-card[data-value=""]').addClass('selected');
    }
});

function toggleBooth(element, boothId) {
    const checkbox = element.querySelector('.booth-checkbox-mobile');
    checkbox.checked = !checkbox.checked;
    element.classList.toggle('selected', checkbox.checked);
    updateSelection();
}

// Booking Type Selector - View Switching and Selection
$(document).ready(function() {
    // Restore booking type view mode preference
    const savedBookingTypeView = getFromLocalStorage('booking_type_view', 'icon');
    $(`.view-switch-btn[data-view="${savedBookingTypeView}"]`).click();
    
    // View switcher functionality
    $('.view-switch-btn').on('click', function() {
        const view = $(this).data('view');
        
        // Save view preference
        saveToLocalStorage('booking_type_view', view);
        
        // Update active button
        $('.view-switch-btn').removeClass('active');
        $(this).addClass('active');
        
        // Switch views
        $('.booking-type-view').removeClass('active');
        $(`.booking-type-view[data-view="${view}"]`).addClass('active');
    });
    
    // Handle booking type selection
    function selectBookingType(value) {
        // Save to localStorage
        saveToLocalStorage('booking_type', value);
        
        // Update hidden select
        $('#type').val(value);
        
        // Update all view options
        $('.booking-type-option-icon, .booking-type-option-list, .booking-type-option-card').removeClass('selected');
        $(`.booking-type-option-icon[data-value="${value}"], .booking-type-option-list[data-value="${value}"], .booking-type-option-card[data-value="${value}"]`).addClass('selected');
    }
    
    // Icon view selection
    $('.booking-type-option-icon').on('click', function() {
        const value = $(this).data('value');
        selectBookingType(value);
    });
    
    // List view selection
    $('.booking-type-option-list').on('click', function() {
        const value = $(this).data('value');
        selectBookingType(value);
    });
    
    // Card view selection
    $('.booking-type-option-card').on('click', function() {
        const value = $(this).data('value');
        selectBookingType(value);
    });
    
    // Initialize with saved or default selected value
    const savedBookingType = getFromLocalStorage('booking_type', null);
    const defaultValue = $('#type').val() || savedBookingType || '1'; // Default to 1 if nothing saved
    
    if (defaultValue) {
        selectBookingType(defaultValue);
    }
});

// Toggle form group expand/collapse
function toggleFormGroup(header) {
    const formGroup = header.closest('.mobile-form-group');
    if (!formGroup) return;
    
    const isCollapsed = formGroup.classList.contains('collapsed');
    const content = formGroup.querySelector('.mobile-form-group-content');
    const toggleIcon = header.querySelector('.mobile-form-group-toggle i');
    
    if (isCollapsed) {
        // Expand
        formGroup.classList.remove('collapsed');
        if (content) {
            // Set max-height to actual content height
            const scrollHeight = content.scrollHeight;
            content.style.maxHeight = scrollHeight + 'px';
            // After transition, set to auto for dynamic content
            setTimeout(function() {
                if (!formGroup.classList.contains('collapsed')) {
                    content.style.maxHeight = 'none';
                }
            }, 400);
        }
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(0deg)';
        }
    } else {
        // Collapse
        if (content) {
            // Get current height before collapsing
            const currentHeight = content.scrollHeight;
            content.style.maxHeight = currentHeight + 'px';
            // Force reflow
            content.offsetHeight;
            // Then collapse
            formGroup.classList.add('collapsed');
            content.style.maxHeight = '0';
        } else {
            formGroup.classList.add('collapsed');
        }
        if (toggleIcon) {
            toggleIcon.style.transform = 'rotate(-90deg)';
        }
    }
}

// Auto-expand form groups on focus
$(document).ready(function() {
    // Auto-expand when input/select is focused
    $(document).on('focus', '.mobile-form-group.collapsible .mobile-form-input, .mobile-form-group.collapsible .mobile-form-select', function() {
        const formGroup = $(this).closest('.mobile-form-group.collapsible');
        if (formGroup.hasClass('collapsed')) {
            const header = formGroup.find('.mobile-form-group-header');
            if (header.length) {
                toggleFormGroup(header[0]);
            }
        }
    });
    
    // Auto-expand if form group has errors
    $('.mobile-form-group.collapsible').each(function() {
        const $group = $(this);
        if ($group.find('[style*="color: #ef4444"], .text-danger, [class*="error"]').length > 0) {
            if ($group.hasClass('collapsed')) {
                const header = $group.find('.mobile-form-group-header');
                if (header.length) {
                    toggleFormGroup(header[0]);
                }
            }
        }
    });
});

// Toggle Booth Help Info
function toggleBoothHelp() {
    const helpBtn = document.getElementById('boothHelpBtn');
    const infoPanel = document.getElementById('boothGridInfo');
    
    if (helpBtn && infoPanel) {
        const isVisible = infoPanel.style.display === 'flex' || infoPanel.classList.contains('show');
        
        if (isVisible) {
            infoPanel.style.display = 'none';
            infoPanel.classList.remove('show');
            helpBtn.classList.remove('active');
        } else {
            infoPanel.style.display = 'flex';
            infoPanel.classList.add('show');
            helpBtn.classList.add('active');
        }
    }
}

// Switch between booth letters (A-Z)
function switchBoothLetter(letter) {
    // Update tabs
    document.querySelectorAll('.booth-letter-tab').forEach(function(tab) {
        tab.classList.remove('active');
    });
    const activeTab = document.querySelector('.booth-letter-tab[data-letter="' + letter + '"]');
    if (activeTab) {
        activeTab.classList.add('active');
        // Scroll active tab into view
        activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
    
    // Update content sections
    document.querySelectorAll('.booth-letter-content').forEach(function(content) {
        content.classList.remove('active');
    });
    const activeContent = document.getElementById('letterContent_' + letter);
    if (activeContent) {
        activeContent.classList.add('active');
    }
    
    // Clear search when switching letters
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
    
    // All booths are already loaded and visible via horizontal scrolling
    
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

// Horizontal Scroll Functions - Slide by 5-row groups
function scrollBoothGrid(gridId, direction) {
    const grid = document.getElementById(gridId);
    if (!grid) return;
    
    // Get the width of one slide (100% of container)
    const slideWidth = grid.clientWidth;
    const currentScroll = grid.scrollLeft;
    
    // Calculate which slide we're on
    const currentSlide = Math.round(currentScroll / slideWidth);
    const totalSlides = grid.querySelectorAll('.booth-row-group').length;
    
    let targetSlide;
    if (direction === 'left') {
        targetSlide = Math.max(0, currentSlide - 1);
    } else {
        targetSlide = Math.min(totalSlides - 1, currentSlide + 1);
    }
    
    // Scroll to target slide
    const targetScroll = targetSlide * slideWidth;
    
    grid.scrollTo({
        left: targetScroll,
        behavior: 'smooth'
    });
    
    // Update scroll indicators after scroll
    setTimeout(function() {
        updateBoothScrollIndicators(gridId);
    }, 300);
}

function updateBoothScrollIndicators(gridId) {
    const grid = document.getElementById(gridId);
    if (!grid) return;
    
    const wrapper = grid.closest('.booth-grid-wrapper');
    if (!wrapper) return;
    
    const leftBtn = wrapper.querySelector('.booth-scroll-btn-left');
    const rightBtn = wrapper.querySelector('.booth-scroll-btn-right');
    
    const isAtStart = grid.scrollLeft <= 0;
    const isAtEnd = grid.scrollLeft >= (grid.scrollWidth - grid.clientWidth - 1);
    
    // Update wrapper classes for gradient indicators
    wrapper.classList.remove('scrolled-left', 'scrolled-right', 'scrolled-both');
    if (isAtStart && isAtEnd) {
        // No scroll needed
    } else if (isAtStart) {
        wrapper.classList.add('scrolled-right');
        if (leftBtn) leftBtn.classList.add('hidden');
        if (rightBtn) rightBtn.classList.remove('hidden');
    } else if (isAtEnd) {
        wrapper.classList.add('scrolled-left');
        if (leftBtn) leftBtn.classList.remove('hidden');
        if (rightBtn) rightBtn.classList.add('hidden');
    } else {
        wrapper.classList.add('scrolled-both');
        if (leftBtn) leftBtn.classList.remove('hidden');
        if (rightBtn) rightBtn.classList.remove('hidden');
    }
}

// Initialize scroll indicators on page load and scroll
$(document).ready(function() {
    // Update scroll indicators for all grids
    $('.booth-grid-mobile').each(function() {
        const gridId = $(this).attr('id');
        if (gridId) {
            updateBoothScrollIndicators(gridId);
            
            // Update on scroll
            $(this).on('scroll', function() {
                updateBoothScrollIndicators(gridId);
            });
        }
    });
    
    // Update visible count on page load
    function updateBoothVisibleCount() {
        const activeGrid = $('.booth-letter-content.active .booth-grid-mobile, #boothSelector').first();
        if (activeGrid.length) {
            // Count all booths since all are loaded
            const totalCount = parseInt(activeGrid.data('total')) || activeGrid.find('.booth-card-mobile').length;
            const visibleCount = totalCount; // All booths are visible via horizontal scrolling
            
            // Update all info displays
            $('.booth-grid-info').each(function() {
                const visibleCountEl = $(this).find('#boothVisibleCount');
                const totalCountEl = $(this).find('#boothTotalCount');
                if (visibleCountEl.length) {
                    visibleCountEl.text(visibleCount);
                }
                if (totalCountEl.length) {
                    totalCountEl.text(totalCount);
                }
            });
        }
    }
    
    // Update count initially and when switching tabs
    updateBoothVisibleCount();
    
    // Update when switching letter tabs
    $(document).on('click', '.booth-letter-tab', function() {
        setTimeout(updateBoothVisibleCount, 100);
    });
    
    // Update on window resize
    $(window).on('resize', function() {
        $('.booth-grid-mobile').each(function() {
            const gridId = $(this).attr('id');
            if (gridId) {
                updateBoothScrollIndicators(gridId);
            }
        });
        updateBoothVisibleCount();
    });
});

// Lazy Loading - Load More Booths (Vertical Expansion)
function loadMoreBooths(gridId) {
    const grid = document.getElementById(gridId);
    if (!grid) return;
    
    const wrapper = grid.closest('.booth-grid-wrapper');
    const loadMoreBtn = wrapper ? wrapper.nextElementSibling?.querySelector('.booth-load-more-btn') : null;
    if (!loadMoreBtn) return;
    
    // Show loading state
    loadMoreBtn.disabled = true;
    loadMoreBtn.classList.add('loading');
    const originalHTML = loadMoreBtn.innerHTML;
    loadMoreBtn.innerHTML = '<i class="fas fa-spinner"></i> <span>Loading...</span>';
    
    // Get all hidden booths in this grid
    const hiddenBooths = Array.from(grid.querySelectorAll('.booth-card-mobile.booth-lazy-hidden'));
    const itemsPerLoad = 15; // 5 rows × 3 columns = 15 items
    const boothsToShow = hiddenBooths.slice(0, itemsPerLoad);
    
    // Update visible count info
    const currentVisible = grid.querySelectorAll('.booth-card-mobile:not(.booth-lazy-hidden)').length;
    const newVisible = currentVisible + boothsToShow.length;
    const totalBooths = parseInt(grid.dataset.total) || grid.querySelectorAll('.booth-card-mobile').length;
    
    // Update info display
    const infoDiv = document.querySelector('.booth-grid-info');
    if (infoDiv) {
        const visibleCountEl = infoDiv.querySelector('#boothVisibleCount');
        const totalCountEl = infoDiv.querySelector('#boothTotalCount');
        if (visibleCountEl) {
            visibleCountEl.textContent = newVisible;
        }
        if (totalCountEl) {
            totalCountEl.textContent = totalBooths;
        }
    }
    
    // Show booths with animation
    setTimeout(function() {
        boothsToShow.forEach(function(booth, index) {
            setTimeout(function() {
                booth.classList.remove('booth-lazy-hidden');
                booth.style.opacity = '0';
                booth.style.transform = 'translateY(10px)';
                
                // Animate in
                setTimeout(function() {
                    booth.style.transition = 'all 0.3s ease';
                    booth.style.opacity = '1';
                    booth.style.transform = 'translateY(0)';
                }, 10);
            }, index * 20); // Stagger animation
        });
        
        // Update remaining count
        const remainingBooths = hiddenBooths.length - boothsToShow.length;
        const remainingCountEl = loadMoreBtn.querySelector('.booth-remaining-count');
        
        // Update scroll indicators after new items are added
        setTimeout(function() {
            updateBoothScrollIndicators(gridId);
        }, 400);
        
        if (remainingBooths > 0) {
            if (remainingCountEl) {
                remainingCountEl.textContent = remainingBooths;
            }
            // Restore button
            loadMoreBtn.disabled = false;
            loadMoreBtn.classList.remove('loading');
            loadMoreBtn.innerHTML = originalHTML;
        } else {
            // Hide button if all loaded
            const container = loadMoreBtn.closest('.booth-load-more-container');
            if (container) {
                container.style.transition = 'opacity 0.3s ease';
                container.style.opacity = '0';
                setTimeout(function() {
                    container.style.display = 'none';
                }, 300);
            }
        }
    }, 300);
}

function updateSelection() {
    const selected = [];
    let totalAmount = 0;
    
    document.querySelectorAll('.booth-checkbox-mobile:checked').forEach(function(checkbox) {
        const boothCard = checkbox.closest('.booth-card-mobile');
        const boothId = checkbox.value;
        const boothNumber = boothCard.querySelector('.booth-number-mobile').textContent;
        const price = parseFloat(boothCard.dataset.price) || 0;
        const category = boothCard.dataset.category || 'N/A';
        const status = boothCard.dataset.status || 'N/A';
        
        // Get status label from the status badge if available
        const statusBadge = boothCard.querySelector('.booth-status-mobile');
        const statusLabel = statusBadge ? statusBadge.textContent.trim() : status;
        
        selected.push({ 
            id: boothId, 
            number: boothNumber, 
            price: price,
            category: category,
            status: statusLabel
        });
        totalAmount += price;
    });
    
    // Update selected list
    const listContainer = document.getElementById('selectedBoothsList');
    if (selected.length > 0) {
        let html = '';
        selected.forEach(function(booth) {
            // Determine status class
            const statusLower = booth.status.toLowerCase();
            let statusClass = 'other';
            if (statusLower.includes('available') || statusLower.includes('free')) {
                statusClass = 'available';
            } else if (statusLower.includes('reserved')) {
                statusClass = 'reserved';
            } else if (statusLower.includes('booked') || statusLower.includes('occupied')) {
                statusClass = 'booked';
            }
            
            html += '<div class="selected-booth-info-mobile">';
            html += '<div class="selected-booth-header">';
            html += '<div class="selected-booth-main-info">';
            html += '<div class="selected-booth-number">';
            html += '<i class="fas fa-cube"></i>';
            html += '<span>' + booth.number + '</span>';
            html += '</div>';
            html += '</div>';
            html += '<div class="selected-booth-price-mobile">$' + booth.price.toFixed(2) + '</div>';
            html += '</div>';
            html += '<div class="selected-booth-details">';
            html += '<div class="selected-booth-detail-item">';
            html += '<i class="fas fa-hashtag"></i>';
            html += '<span class="detail-label">ID:</span>';
            html += '<span class="detail-value">' + booth.id + '</span>';
            html += '</div>';
            html += '<div class="selected-booth-detail-item">';
            html += '<i class="fas fa-tag"></i>';
            html += '<span class="detail-label">Category:</span>';
            html += '<span class="detail-value">' + (booth.category !== 'n/a' ? booth.category.charAt(0).toUpperCase() + booth.category.slice(1) : 'N/A') + '</span>';
            html += '</div>';
            html += '<div class="selected-booth-status ' + statusClass + '">';
            html += '<i class="fas fa-circle" style="font-size: 6px;"></i>';
            html += '<span>' + booth.status + '</span>';
            html += '</div>';
            html += '</div>';
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
    // Only select visible booths in the active letter tab (including lazy loaded ones)
    const activeLetter = document.querySelector('.booth-letter-content.active');
    if (activeLetter) {
        activeLetter.querySelectorAll('.booth-card-mobile').forEach(function(card) {
            const checkbox = card.querySelector('.booth-checkbox-mobile');
            if (card.style.display !== 'none' && !card.classList.contains('disabled') && checkbox) {
                checkbox.checked = true;
                card.classList.add('selected');
            }
        });
    } else {
        // Fallback: select all visible booths (including lazy loaded ones)
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

// Booking Persistence Functions
function saveBookingToStorage() {
    const bookingData = {
        clientId: $('#clientid').val() || '',
        date: $('#date_book').val() || '',
        type: $('#type').val() || '',
        floorPlanId: $('#floor_plan_id').val() || '',
        boothIds: [],
        bookingTypeView: getFromLocalStorage('booking_type_view', 'icon'),
        floorPlanView: getFromLocalStorage('floor_plan_view', 'icon')
    };
    
    // Get selected booths
    $('.booth-checkbox-mobile:checked').each(function() {
        bookingData.boothIds.push($(this).val());
    });
    
    // Get client info if available
    const clientInfo = $('#selectedClientInfo');
    if (clientInfo.is(':visible')) {
        bookingData.clientName = $('#selectedClientName').text() || '';
        bookingData.clientCompany = $('#selectedClientDetails').text() || '';
    }
    
    localStorage.setItem('booking_draft', JSON.stringify(bookingData));
}

function loadBookingFromStorage() {
    try {
        const savedData = localStorage.getItem('booking_draft');
        if (!savedData) return false;
        
        const bookingData = JSON.parse(savedData);
        let hasData = false;
        
        // Restore client
        if (bookingData.clientId && bookingData.clientId !== '') {
            $('#clientid').val(bookingData.clientId);
            hasData = true;
            
            // Try to fetch client data via AJAX to restore full client info
            $.ajax({
                url: '{{ route("clients.search") }}',
                method: 'GET',
                data: { q: '', id: bookingData.clientId },
                success: function(clients) {
                    if (clients && clients.length > 0) {
                        const client = clients[0];
                        if (typeof selectClient === 'function') {
                            selectClient(client);
                        }
                    } else if (bookingData.clientName) {
                        // Fallback: restore with saved name if AJAX fails
                        const clientInfo = $('#selectedClientInfo');
                        if (clientInfo.length) {
                            $('#selectedClientName').text(bookingData.clientName);
                            if (bookingData.clientCompany) {
                                $('#selectedClientCompany').text(bookingData.clientCompany);
                            }
                            clientInfo.show();
                            $('#clientSearchContainer').hide();
                        }
                    }
                },
                error: function() {
                    // Fallback: restore with saved name if AJAX fails
                    if (bookingData.clientName) {
                        const clientInfo = $('#selectedClientInfo');
                        if (clientInfo.length) {
                            $('#selectedClientName').text(bookingData.clientName);
                            if (bookingData.clientCompany) {
                                $('#selectedClientCompany').text(bookingData.clientCompany);
                            }
                            clientInfo.show();
                            $('#clientSearchContainer').hide();
                        }
                    }
                }
            });
        }
        
        // Restore date
        if (bookingData.date && bookingData.date !== '') {
            $('#date_book').val(bookingData.date);
            hasData = true;
        }
        
        // Restore booking type
        if (bookingData.type && bookingData.type !== '') {
            $('#type').val(bookingData.type);
            if (typeof selectBookingType === 'function') {
                selectBookingType(bookingData.type, false);
            }
            hasData = true;
        }
        
        // Restore floor plan
        if (bookingData.floorPlanId && bookingData.floorPlanId !== '') {
            $('#floor_plan_id').val(bookingData.floorPlanId);
            // Don't reload page, just update selection
            const floorPlanOption = $('.floor-plan-option[data-value="' + bookingData.floorPlanId + '"]');
            if (floorPlanOption.length && typeof selectFloorPlan === 'function') {
                const image = floorPlanOption.data('image') || '';
                const name = floorPlanOption.data('name') || '';
                const description = floorPlanOption.data('description') || '';
                selectFloorPlan(bookingData.floorPlanId, image, name, description, false);
            }
            hasData = true;
        }
        
        // Restore booths (after a short delay to ensure DOM is ready)
        if (bookingData.boothIds && bookingData.boothIds.length > 0) {
            setTimeout(function() {
                bookingData.boothIds.forEach(function(boothId) {
                    const checkbox = $('.booth-checkbox-mobile[value="' + boothId + '"]');
                    if (checkbox.length) {
                        checkbox.prop('checked', true);
                        checkbox.closest('.booth-card-mobile').addClass('selected');
                    }
                });
                if (typeof updateSelection === 'function') {
                    updateSelection();
                }
                if (typeof updateProgress === 'function') {
                    updateProgress();
                }
            }, 500);
            hasData = true;
        }
        
        // Restore view preferences
        if (bookingData.bookingTypeView) {
            saveToLocalStorage('booking_type_view', bookingData.bookingTypeView);
        }
        if (bookingData.floorPlanView) {
            saveToLocalStorage('floor_plan_view', bookingData.floorPlanView);
        }
        
        return hasData;
    } catch (e) {
        console.error('Error loading booking from storage:', e);
        return false;
    }
}

function clearBookingData() {
    // Clear localStorage
    localStorage.removeItem('booking_draft');
    
    // Clear form
    $('#clientid').val('');
    $('#date_book').val('');
    $('#type').val('');
    $('#floor_plan_id').val('');
    
    // Clear client selection UI
    $('#selectedClientInfo').hide();
    $('#clientSearchContainer').show();
    $('#clientSearchInline').val('');
    $('#inlineClientResults').hide();
    
    // Clear all booth selections
    $('.booth-checkbox-mobile').prop('checked', false);
    $('.booth-card-mobile').removeClass('selected');
    
    // Reset booking type view
    if (typeof selectBookingType === 'function') {
        selectBookingType('', false);
    }
    
    // Reset floor plan (reload page to reset floor plan filter)
    window.location.href = '{{ route("books.create") }}';
}

// Client Search & Select Functionality (adapted for mobile)
$(document).ready(function() {
    let clientSearchTimeout;
    let inlineSearchTimeout;
    let selectedClient = null;
    
    // Load saved booking data on page load
    const hasSavedData = loadBookingFromStorage();
    if (hasSavedData) {
        // Show a subtle notification that data was restored
        setTimeout(function() {
            if (typeof updateProgress === 'function') {
                updateProgress();
            }
            if (typeof updateSelection === 'function') {
                updateSelection();
            }
        }, 300);
    }
    
    // Save booking data on form changes
    $('#clientid, #date_book, #type, #floor_plan_id').on('change', function() {
        saveBookingToStorage();
    });
    
    // Save when booths are selected/deselected
    $(document).on('change', '.booth-checkbox-mobile', function() {
        setTimeout(function() {
            saveBookingToStorage();
        }, 100);
    });
    
    // Clear Booking Button
    $('#clearBookingBtn').on('click', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Clear Booking?',
                text: 'This will clear all your selections and start fresh. This action cannot be undone.',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Clear All',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    clearBookingData();
                }
            });
        } else {
            if (confirm('Clear all booking data and start fresh?')) {
                clearBookingData();
            }
        }
    });
    
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
        
        // Save to storage
        saveBookingToStorage();
        
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
        
        // Save to storage
        saveBookingToStorage();
        
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
    
    // Client Search Function (for modal) - Card View with 150+ clients
    let currentClientView = 'card';
    
    function searchClients(query) {
        // If query is empty or less than 2 chars, load all clients (150+)
        const searchQuery = query && query.length >= 2 ? query : '';
        
        $.ajax({
            url: '{{ route("clients.search") }}',
            method: 'GET',
            data: { q: searchQuery },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(clients) {
                const resultsDiv = $('#clientSearchResults');
                const resultsList = $('#clientSearchResultsList');
                const noResultsDiv = $('#noClientResults');
                const resultsCount = $('#clientResultsCount');
                
                resultsList.empty();
                const clientsArray = Array.isArray(clients) ? clients : (clients ? Object.values(clients) : []);
                
                if (!clientsArray || clientsArray.length === 0) {
                    resultsDiv.hide();
                    noResultsDiv.show();
                    return;
                }
                
                noResultsDiv.hide();
                resultsDiv.show();
                resultsCount.text(clientsArray.length);
                
                // Render clients based on current view
                renderClients(clientsArray, currentClientView);
            },
            error: function() {
                $('#clientSearchResults').hide();
                $('#noClientResults').show();
            }
        });
    }
    
    function renderClients(clientsArray, view) {
        const resultsList = $('#clientSearchResultsList');
        resultsList.empty();
        
        if (view === 'card') {
            // Card View
            resultsList.removeClass('client-list-container').addClass('client-cards-container');
            resultsList.css({
                'display': 'grid',
                'grid-template-columns': 'repeat(auto-fill, minmax(280px, 1fr))',
                'gap': '16px',
                'max-height': 'calc(100vh - 350px)',
                'overflow-y': 'auto',
                'padding': '0.5rem'
            });
            
            clientsArray.forEach(function(client) {
                const displayName = (client.company || client.name || 'N/A');
                const clientName = client.name || '';
                const companyName = client.company || '';
                
                let cardHTML = '<div class="client-card" style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; cursor: pointer; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">';
                cardHTML += '<div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">';
                cardHTML += '<div style="flex: 1;">';
                cardHTML += '<div style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">';
                cardHTML += '<i class="fas fa-building" style="color: #667eea; font-size: 14px;"></i>';
                cardHTML += '<span>' + displayName + '</span>';
                cardHTML += '</div>';
                if (clientName && companyName) {
                    cardHTML += '<div style="font-size: 13px; color: #6b7280; margin-bottom: 8px;"><i class="fas fa-user" style="margin-right: 6px; color: #9ca3af;"></i>' + clientName + '</div>';
                }
                cardHTML += '</div>';
                cardHTML += '</div>';
                
                cardHTML += '<div class="client-card-details" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6;">';
                if (client.email) {
                    cardHTML += '<div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; color: #6b7280;">';
                    cardHTML += '<i class="fas fa-envelope" style="color: #9ca3af; width: 16px;"></i>';
                    cardHTML += '<span style="word-break: break-word;">' + client.email + '</span>';
                    cardHTML += '</div>';
                }
                if (client.phone_number) {
                    cardHTML += '<div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; color: #6b7280;">';
                    cardHTML += '<i class="fas fa-phone" style="color: #9ca3af; width: 16px;"></i>';
                    cardHTML += '<span>' + client.phone_number + '</span>';
                    cardHTML += '</div>';
                }
                if (client.position) {
                    cardHTML += '<div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #6b7280;">';
                    cardHTML += '<i class="fas fa-briefcase" style="color: #9ca3af; width: 16px;"></i>';
                    cardHTML += '<span>' + client.position + '</span>';
                    cardHTML += '</div>';
                }
                cardHTML += '</div>';
                
                cardHTML += '<button type="button" class="select-client-card-btn" data-client-id="' + client.id + '" style="margin-top: 12px; width: 100%; padding: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s ease;">';
                cardHTML += '<i class="fas fa-check mr-1"></i>Select Client';
                cardHTML += '</button>';
                cardHTML += '</div>';
                
                const item = $(cardHTML).data('client', client);
                resultsList.append(item);
            });
        } else {
            // List View
            resultsList.removeClass('client-cards-container').addClass('client-list-container');
            resultsList.css({
                'display': 'block',
                'max-height': 'calc(100vh - 350px)',
                'overflow-y': 'auto',
                'padding': '0.5rem'
            });
            
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
        }
        
        // Attach click handlers
        $('.select-client-card-btn, .select-client-btn').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const client = $(this).closest('.client-card, .client-search-result').data('client');
            if (client) {
                selectClient(client);
                $('#searchClientModal').modal('hide');
            }
        });
        
        // Card click handler
        $('.client-card').off('click').on('click', function(e) {
            if (!$(e.target).closest('.select-client-card-btn').length) {
                const client = $(this).data('client');
                if (client) {
                    selectClient(client);
                    $('#searchClientModal').modal('hide');
                }
            }
        });
    }
    
    // View toggle handler
    $('.view-toggle-btn').on('click', function() {
        const view = $(this).data('view');
        currentClientView = view;
        $('.view-toggle-btn').removeClass('active');
        $(this).addClass('active');
        
        // Re-render with current search query
        const query = $('#clientSearchInput').val().trim();
        searchClients(query);
    });
    
    // Modal open handler - load 150 clients automatically
    $('#searchClientModal').on('shown.bs.modal', function() {
        // Load all clients (150+) when modal opens
        searchClients('');
        $('#clientSearchResults').show();
    });
    
    $('#clientSearchInput').on('input keyup', function(e) {
        const query = $(this).val().trim();
        clearTimeout(clientSearchTimeout);
        
        if (query.length === 0) {
            // If empty, show all clients
            $('#btnClearClientSearch').hide();
            searchClients('');
            return;
        }
        
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
        searchClients(query);
    });
    
    $('#btnClearClientSearch').on('click', function() {
        $('#clientSearchInput').val('');
        $('#clientSearchResults').show();
        $('#noClientResults').hide();
        $(this).hide();
        // Reload all clients
        searchClients('');
    });
    
    // Reset modal when closed
    $('#searchClientModal').on('hidden.bs.modal', function() {
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $('#btnClearClientSearch').hide();
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
                    // Clear saved booking data on successful submission
                    localStorage.removeItem('booking_draft');
                    
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
    
    // Progress Indicator Updates - Enhanced with step info
    function updateProgress() {
        const clientId = $('#clientid').val();
        const hasBooths = $('.booth-checkbox-mobile:checked').length > 0;
        const hasDate = $('#date_book').val() && $('#date_book').val() !== '';
        const selectedClientName = $('#selectedClientName').text() || '';
        const selectedDate = $('#date_book').val() || '';
        const boothCount = $('.booth-checkbox-mobile:checked').length;
        
        // Step 1: Client
        if (clientId && clientId !== '') {
            $('#progressStep1').addClass('completed').removeClass('active pending').html('<i class="fas fa-check"></i>');
            $('#clientSection').addClass('completed');
            $('#stepInfo1 .step-title').css('color', '#22c55e');
            $('#stepInfo1 .step-status').text(selectedClientName || 'Client selected').css('color', '#22c55e');
            // Animate progress line 1
            setTimeout(function() {
                $('#progressLine1').addClass('completed');
                $('#progressLine1 .progress-line-fill').css('width', '100%');
            }, 200);
        } else {
            $('#progressStep1').addClass('active').removeClass('completed pending').html('<i class="fas fa-user"></i>');
            $('#clientSection').removeClass('completed');
            $('#stepInfo1 .step-title').css('color', '#667eea');
            $('#stepInfo1 .step-status').text('Select client').css('color', '#9ca3af');
            $('#progressLine1').removeClass('completed');
            $('#progressLine1 .progress-line-fill').css('width', '0%');
        }
        
        // Step 2: Details (active after client, completed when date is set)
        if (clientId && clientId !== '') {
            if (hasDate) {
                $('#progressStep2').addClass('completed').removeClass('active pending').html('<i class="fas fa-check"></i>');
                $('#stepInfo2 .step-title').css('color', '#22c55e');
                $('#stepInfo2 .step-status').text(selectedDate || 'Date set').css('color', '#22c55e');
                // Animate progress line 2
                setTimeout(function() {
                    $('#progressLine2').addClass('completed');
                    $('#progressLine2 .progress-line-fill').css('width', '100%');
                }, 200);
            } else {
                $('#progressStep2').addClass('active').removeClass('completed pending').html('<i class="fas fa-calendar"></i>');
                $('#stepInfo2 .step-title').css('color', '#667eea');
                $('#stepInfo2 .step-status').text('Set booking date').css('color', '#9ca3af');
                $('#progressLine2').removeClass('completed');
                $('#progressLine2 .progress-line-fill').css('width', '0%');
            }
        } else {
            $('#progressStep2').addClass('pending').removeClass('active completed').html('<i class="fas fa-calendar"></i>');
            $('#stepInfo2 .step-title').css('color', '#9ca3af');
            $('#stepInfo2 .step-status').text('Set date').css('color', '#d1d5db');
            $('#progressLine2').removeClass('completed');
            $('#progressLine2 .progress-line-fill').css('width', '0%');
        }
        
        // Step 3: Booths
        if (hasBooths) {
            $('#progressStep3').addClass('completed').removeClass('active pending').html('<i class="fas fa-check"></i>');
            $('#boothSection').addClass('completed');
            $('#stepInfo3 .step-title').css('color', '#22c55e');
            $('#stepInfo3 .step-status').text(boothCount + ' booth' + (boothCount > 1 ? 's' : '') + ' selected').css('color', '#22c55e');
        } else {
            if (clientId && hasDate) {
                $('#progressStep3').addClass('active').removeClass('completed pending').html('<i class="fas fa-cube"></i>');
                $('#stepInfo3 .step-title').css('color', '#667eea');
                $('#stepInfo3 .step-status').text('Select booths').css('color', '#9ca3af');
            } else {
                $('#progressStep3').addClass('pending').removeClass('active completed').html('<i class="fas fa-cube"></i>');
                $('#stepInfo3 .step-title').css('color', '#9ca3af');
                $('#stepInfo3 .step-status').text('Select booths').css('color', '#d1d5db');
            }
            $('#boothSection').removeClass('completed');
        }
        
        // Update current step message
        let currentMessage = '';
        let currentStep = 0;
        
        if (!clientId || clientId === '') {
            currentMessage = 'Please select a client to begin your booking';
            currentStep = 1;
        } else if (!hasDate) {
            currentMessage = 'Great! Now please set the booking date';
            currentStep = 2;
        } else if (!hasBooths) {
            currentMessage = 'Almost done! Select at least one booth for this booking';
            currentStep = 3;
        } else {
            currentMessage = 'Perfect! All steps completed. Review and submit your booking';
            currentStep = 0;
        }
        
        $('#currentStepMessage').text(currentMessage);
        
        // Update highlight border color based on current step
        if (currentStep === 1) {
            $('#currentStepHighlight').css('border-left-color', '#667eea');
        } else if (currentStep === 2) {
            $('#currentStepHighlight').css('border-left-color', '#667eea');
        } else if (currentStep === 3) {
            $('#currentStepHighlight').css('border-left-color', '#667eea');
        } else {
            $('#currentStepHighlight').css('border-left-color', '#22c55e');
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

<!-- Modern Mobile Bottom Navigation - Sticky at Bottom -->
<nav class="modern-mobile-nav">
    @php
        try {
            $moduleSettings = \App\Models\Setting::getModuleDisplaySettings();
        } catch (\Exception $e) {
            $moduleSettings = [
                'dashboard' => ['mobile' => true, 'tablet' => true],
                'booths' => ['mobile' => true, 'tablet' => true],
                'bookings' => ['mobile' => true, 'tablet' => true],
                'clients' => ['mobile' => true, 'tablet' => true],
                'settings' => ['mobile' => true, 'tablet' => true],
            ];
        }
    @endphp
    
    @if(($moduleSettings['dashboard']['mobile'] ?? true) || ($moduleSettings['dashboard']['tablet'] ?? true))
    <a href="{{ route('dashboard') }}" class="modern-mobile-nav-item" data-route="dashboard">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    @endif
    
    @if(($moduleSettings['booths']['mobile'] ?? true) || ($moduleSettings['booths']['tablet'] ?? true))
    <a href="{{ route('booths.index', ['view' => 'table']) }}" class="modern-mobile-nav-item" data-route="booths">
        <i class="fas fa-store"></i>
        <span>Booths</span>
    </a>
    @endif
    
    @if(($moduleSettings['bookings']['mobile'] ?? true) || ($moduleSettings['bookings']['tablet'] ?? true))
    <a href="{{ route('books.index') }}" class="modern-mobile-nav-item active" data-route="books">
        <i class="fas fa-calendar-check"></i>
        <span>Bookings</span>
    </a>
    @endif
    
    @if(($moduleSettings['clients']['mobile'] ?? true) || ($moduleSettings['clients']['tablet'] ?? true))
    <a href="{{ route('clients.index') }}" class="modern-mobile-nav-item" data-route="clients">
        <i class="fas fa-users"></i>
        <span>Clients</span>
    </a>
    @endif
    
    @if(($moduleSettings['settings']['mobile'] ?? true) || ($moduleSettings['settings']['tablet'] ?? true))
    <a href="{{ route('settings.index') }}" class="modern-mobile-nav-item" data-route="settings">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
    </a>
    @endif
</nav>
