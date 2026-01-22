@extends('layouts.adminlte')

@section('title', 'Bookings Management')
@section('page-title', 'Bookings Management')
@section('breadcrumb', 'Bookings')

@push('styles')
<style>
    /* Khmer Font Support for Bookings Page */
    .compact-booking-card,
    .booking-card-modern,
    .table-modern,
    #compactBookingsContainer,
    #cardBookingsContainer,
    .compact-card-content,
    .compact-card-row,
    .compact-card-id {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Khmer OS Battambang", "KhmerOSBattambang", "Hanuman", "Hanuman-Regular", "Noto Sans Khmer", "Khmer OS", "Khmer", sans-serif;
    }
    
    /* Modern Design System - Enhanced Glassmorphism Cards */
    .stats-card {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, currentColor, transparent);
        opacity: 0;
        transition: opacity 0.4s;
    }

    .stats-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.4s;
    }

    .stats-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .stats-card:hover::before {
        opacity: 1;
    }

    .stats-card:hover::after {
        opacity: 1;
    }

    .stats-card.primary { color: #667eea; }
    .stats-card.success { color: #48bb78; }
    .stats-card.info { color: #4299e1; }
    .stats-card.warning { color: #ed8936; }

    .stats-card .card-body {
        position: relative;
        z-index: 1;
        padding: 36px !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 180px;
    }

    .stats-icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .stats-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: white;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
    }

    .stats-icon::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
        transform: rotate(45deg);
        animation: shine 3s infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }

    .stats-card.primary .stats-icon { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    .stats-card.success .stats-icon { 
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        box-shadow: 0 8px 20px rgba(72, 187, 120, 0.4);
    }
    .stats-card.info .stats-icon { 
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        box-shadow: 0 8px 20px rgba(66, 153, 225, 0.4);
    }
    .stats-card.warning .stats-icon { 
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        box-shadow: 0 8px 20px rgba(237, 137, 54, 0.4);
    }

    .stats-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }

    .stats-value {
        font-size: 3rem;
        font-weight: 800;
        color: #1a202c;
        margin: 8px 0;
        line-height: 1.1;
        letter-spacing: -0.03em;
        background: linear-gradient(135deg, #1a202c 0%, #4a5568 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stats-label {
        font-size: 0.8125rem;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-top: 4px;
    }

    .stats-trend {
        font-size: 0.75rem;
        color: #a0aec0;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Filter Bar - Modern Glassmorphism */
    .filter-container {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        padding: 28px;
        margin-bottom: 32px;
    }

    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e2e8f0;
    }

    .filter-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filter-title i {
        color: #667eea;
        font-size: 1.75rem;
    }

    /* Modern Input Styles */
    .form-control-modern {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 0.9375rem;
        transition: all 0.3s;
        background: white;
    }

    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .input-group-modern .input-group-prepend {
        border-right: none;
        border-radius: 12px 0 0 12px;
        background: #f7fafc;
        border: 2px solid #e2e8f0;
        border-right: none;
    }

    .input-group-modern .input-group-text {
        background: transparent;
        border: none;
        color: #667eea;
        padding: 12px 16px;
    }

    .input-group-modern .form-control {
        border-left: none;
        border-radius: 0 12px 12px 0;
    }

    .input-group-modern .form-control:focus {
        border-left: 2px solid #667eea;
    }

    /* Action Bar */
    .action-bar {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        padding: 20px 28px;
        margin-bottom: 32px;
    }

    .btn-modern {
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .btn-modern-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-modern-success {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
    }

    .btn-modern-info {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }

    /* Booking Cards - Modern Design */
    .booking-card-modern {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 255, 255, 0.95) 100%);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        border-left: 5px solid;
    }

    .booking-card-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
        opacity: 0;
        transition: opacity 0.4s;
    }

    .booking-card-modern:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
    }

    .booking-card-modern:hover::before {
        opacity: 1;
    }

    .booking-card-modern.regular { border-left-color: #667eea; }
    .booking-card-modern.special { border-left-color: #ed8936; }
    .booking-card-modern.temporary { border-left-color: #f56565; }

    .booking-card-header {
        padding: 24px 24px 16px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .booking-card-body {
        padding: 24px;
    }

    .booking-card-footer {
        padding: 16px 24px;
        background: #f7fafc;
        border-top: 1px solid #e2e8f0;
        border-radius: 0 0 20px 20px;
    }

    /* Modern Table */
    .table-modern {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table-modern thead th {
        border: none;
        padding: 20px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.75rem;
    }

    .table-modern tbody tr {
        border-bottom: 1px solid #e2e8f0;
        transition: all 0.3s;
    }

    .table-modern tbody tr:hover {
        background: #f7fafc;
        transform: scale(1.01);
    }

    .table-modern tbody td {
        padding: 20px;
        vertical-align: middle;
        border: none;
    }

    /* Badge Modern */
    .badge-modern {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-modern-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .badge-modern-warning {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        color: white;
    }

    .badge-modern-danger {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        color: white;
    }

    .badge-modern-info {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }

    /* View Toggle */
    .view-toggle {
        display: inline-flex;
        background: #f7fafc;
        border-radius: 12px;
        padding: 4px;
        border: 2px solid #e2e8f0;
        gap: 4px;
    }

    .view-toggle button {
        border: none;
        background: transparent;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        color: #718096;
        white-space: nowrap;
    }

    .view-toggle button.active {
        background: white;
        color: #667eea;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* View Mode Styles */
    .view-mode-selector {
        display: inline-flex;
        background: #f7fafc;
        border-radius: 12px;
        padding: 4px;
        border: 2px solid #e2e8f0;
        gap: 4px;
        margin-left: 12px;
    }

    .view-mode-selector button {
        border: none;
        background: transparent;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s;
        color: #718096;
        white-space: nowrap;
    }

    .view-mode-selector button.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    /* Default Mode - Current design */
    .view-mode-default .stats-card .card-body {
        padding: 36px !important;
        min-height: 180px;
    }

    .view-mode-default .booking-card-body {
        padding: 24px;
    }

    .view-mode-default .table-modern tbody td {
        padding: 20px;
    }

    /* Minimal Mode - Ultra Compact List Design */
    .view-mode-minimal .stats-card {
        margin-bottom: 12px;
    }

    .view-mode-minimal .stats-card .card-body {
        padding: 16px !important;
        min-height: auto;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }

    .view-mode-minimal .stats-icon-wrapper {
        margin-bottom: 0;
        margin-right: 16px;
    }

    .view-mode-minimal .stats-icon {
        width: 48px;
        height: 48px;
        font-size: 20px;
        margin-bottom: 0;
    }

    .view-mode-minimal .stats-content {
        flex: 1;
        text-align: right;
    }

    .view-mode-minimal .stats-value {
        font-size: 1.75rem;
        margin: 0;
        line-height: 1.2;
    }

    .view-mode-minimal .stats-label {
        font-size: 0.6875rem;
        margin-top: 4px;
    }

    .view-mode-minimal .stats-trend {
        display: none;
    }

    /* Minimal Table - Ultra Compact */
    .view-mode-minimal .table-modern {
        font-size: 0.8125rem;
    }

    .view-mode-minimal .table-modern thead th {
        padding: 8px 10px;
        font-size: 0.6875rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .view-mode-minimal .table-modern tbody td {
        padding: 8px 10px;
        font-size: 0.8125rem;
        vertical-align: middle;
    }

    .view-mode-minimal .table-modern tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }

    .view-mode-minimal .table-modern tbody tr:hover {
        background: #f8fafc;
        transform: none;
    }

    /* Minimal Cards - Horizontal List Style */
    .view-mode-minimal .booking-card-modern {
        border-left: 3px solid;
        border-radius: 8px;
        margin-bottom: 8px;
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 0;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .view-mode-minimal .booking-card-modern:hover {
        transform: translateX(4px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    }

    .view-mode-minimal .booking-card-header {
        padding: 12px 16px;
        border-bottom: none;
        border-right: 1px solid #e2e8f0;
        min-width: 120px;
        flex-shrink: 0;
    }

    .view-mode-minimal .booking-card-header h5 {
        font-size: 0.875rem;
        margin: 0;
        font-weight: 700;
    }

    .view-mode-minimal .booking-card-header .badge-modern {
        font-size: 0.625rem;
        padding: 4px 8px;
        margin-top: 4px;
        display: inline-block;
    }

    .view-mode-minimal .booking-card-body {
        padding: 12px 16px;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .view-mode-minimal .booking-card-body > div {
        margin: 0;
    }

    .view-mode-minimal .booking-card-body .mb-4,
    .view-mode-minimal .booking-card-body .mb-3 {
        margin-bottom: 0 !important;
    }

    .view-mode-minimal .booking-card-body .d-flex.align-items-center {
        gap: 8px;
    }

    .view-mode-minimal .booking-card-body .mr-3 {
        margin-right: 8px !important;
    }

    .view-mode-minimal .booking-card-body .font-weight-700 {
        font-size: 0.875rem;
        line-height: 1.3;
    }

    .view-mode-minimal .booking-card-body .text-muted {
        font-size: 0.75rem;
    }

    .view-mode-minimal .booking-card-body .badge-modern {
        font-size: 0.75rem;
        padding: 4px 10px;
    }

    .view-mode-minimal .booking-card-footer {
        padding: 0;
        background: transparent;
        border-top: none;
        border-left: 1px solid #e2e8f0;
        border-radius: 0;
        flex-shrink: 0;
        min-width: 80px;
    }

    .view-mode-minimal .booking-card-footer .btn-group {
        flex-direction: column;
        width: 100%;
        gap: 4px;
        padding: 8px;
    }

    .view-mode-minimal .booking-card-footer .btn {
        border-radius: 6px !important;
        padding: 6px 8px;
        font-size: 0.75rem;
        width: 100%;
    }

    .view-mode-minimal .booking-card-body .x-avatar {
        width: 32px !important;
        height: 32px !important;
    }

    /* Compact row layout for minimal cards */
    .view-mode-minimal #cardBookingsContainer .col-md-6,
    .view-mode-minimal #cardBookingsContainer .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
        padding-left: 12px;
        padding-right: 12px;
    }

    /* Hide less important elements in minimal mode */
    .view-mode-minimal .booking-card-body .ml-5 {
        display: none;
    }

    /* Compact filter bar */
    .view-mode-minimal .filter-container {
        padding: 16px;
        margin-bottom: 16px;
    }

    .view-mode-minimal .filter-header {
        margin-bottom: 16px;
        padding-bottom: 12px;
    }

    .view-mode-minimal .action-bar {
        padding: 12px 20px;
        margin-bottom: 16px;
    }

    .view-mode-minimal .btn-modern {
        padding: 8px 16px;
        font-size: 0.875rem;
    }

    /* Compact table cell content */
    .view-mode-minimal .table-modern tbody td .d-flex {
        gap: 8px;
    }

    .view-mode-minimal .table-modern tbody td .x-avatar {
        width: 28px !important;
        height: 28px !important;
    }

    .view-mode-minimal .table-modern tbody td .font-weight-700 {
        font-size: 0.8125rem;
    }

    .view-mode-minimal .table-modern tbody td .text-muted {
        font-size: 0.75rem;
    }

    .view-mode-minimal .table-modern tbody td .badge-modern {
        font-size: 0.6875rem;
        padding: 4px 8px;
    }

    .view-mode-minimal .table-modern tbody td .btn-group-sm .btn {
        padding: 4px 8px;
        font-size: 0.75rem;
    }

    /* Hide some elements in minimal table */
    .view-mode-minimal .table-modern tbody td .text-muted:has(i.fa-envelope) {
        display: none;
    }

    /* Row actions compact */
    .view-mode-minimal .table-modern tbody td:last-child {
        width: 80px;
    }

    /* Expand Mode - More spacious design */
    .view-mode-expand .stats-card .card-body {
        padding: 48px !important;
        min-height: 220px;
    }

    .view-mode-expand .stats-icon {
        width: 96px;
        height: 96px;
        font-size: 44px;
    }

    .view-mode-expand .stats-value {
        font-size: 3.5rem;
    }

    .view-mode-expand .stats-label {
        font-size: 0.9375rem;
    }

    .view-mode-expand .booking-card-body {
        padding: 32px;
    }

    .view-mode-expand .booking-card-header {
        padding: 32px 32px 20px;
    }

    .view-mode-expand .booking-card-footer {
        padding: 24px 32px;
    }

    /* Master Layout Styles */
    .master-layout-switcher {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .master-layout-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        border-radius: 6px;
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .master-layout-btn:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        transform: translateY(-1px);
    }

    .master-layout-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
    }

    .master-layout-btn i {
        font-size: 12px;
    }

    /* Master Layout: Default */
    .master-layout-default .compact-card-view {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    .master-layout-default .compact-booking-card {
        padding: 20px;
        border-radius: 16px;
    }

    .master-layout-default .compact-card-id {
        font-size: 1.125rem;
    }

    /* Master Layout: Min (Minimal) */
    .master-layout-min .compact-card-view {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 12px;
        padding: 12px;
    }

    .master-layout-min .compact-booking-card {
        padding: 12px;
        border-radius: 10px;
    }

    .master-layout-min .compact-card-id {
        font-size: 0.9375rem;
    }

    .master-layout-min .compact-card-row {
        font-size: 0.75rem;
    }

    /* Master Layout: Max (Maximum) */
    .master-layout-max .compact-card-view {
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 24px;
        padding: 24px;
    }

    .master-layout-max .compact-booking-card {
        padding: 24px;
        border-radius: 20px;
    }

    .master-layout-max .compact-card-id {
        font-size: 1.25rem;
    }

    .master-layout-max .compact-card-row {
        font-size: 0.9375rem;
    }

    /* Master Layout: Tiny (Smallest) */
    .master-layout-tiny .compact-card-view {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 8px;
        padding: 8px;
    }

    .master-layout-tiny .compact-booking-card {
        padding: 10px;
        border-radius: 8px;
    }

    .master-layout-tiny .compact-card-id {
        font-size: 0.8125rem;
    }

    .master-layout-tiny .compact-card-row {
        font-size: 0.6875rem;
    }

    .master-layout-tiny .compact-card-actions .btn {
        width: 24px;
        height: 24px;
        font-size: 0.6875rem;
    }

    /* Compact Card/Icon View Styles */
    .compact-card-view {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px;
        padding: 16px;
    }

    .compact-booking-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .compact-booking-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .compact-booking-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .compact-booking-card.special::before {
        background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
    }

    .compact-booking-card.temporary::before {
        background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    }

    .compact-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f3f4f6;
    }

    .compact-card-id {
        font-weight: 700;
        font-size: 1rem;
        color: #1a202c;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .compact-card-id i {
        color: #667eea;
        font-size: 0.875rem;
    }

    .compact-card-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .compact-card-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .compact-card-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.8125rem;
    }

    .compact-card-row i {
        width: 16px;
        color: #6b7280;
        font-size: 0.75rem;
    }

    .compact-card-row strong {
        color: #1a202c;
        font-weight: 600;
    }

    .compact-card-row span {
        color: #6b7280;
    }

    .compact-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
    }

    .compact-card-actions {
        display: flex;
        gap: 4px;
    }

    .compact-card-actions .btn {
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 0.75rem;
    }

    /* Instant Search Styles */
    #instantSearchInput:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    #instantSearchIndicator {
        display: inline-flex;
        align-items: center;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Search Loading State */
    .input-group-modern.searching .input-group-text i {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .view-mode-expand .table-modern tbody td {
        padding: 28px;
        font-size: 1rem;
    }

    .view-mode-expand .table-modern thead th {
        padding: 24px;
        font-size: 0.8125rem;
    }

    .view-mode-expand .booking-card-modern {
        margin-bottom: 32px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    }

    .empty-state-icon {
        font-size: 5rem;
        color: #cbd5e0;
        margin-bottom: 24px;
    }

    /* Lazy Loading */
    .lazy-load-container {
        position: relative;
        min-height: 200px;
    }

    .lazy-load-spinner {
        display: none;
        text-align: center;
        padding: 40px 20px;
    }

    .lazy-load-spinner.active {
        display: block;
    }

    .lazy-load-spinner i {
        font-size: 2.5rem;
        color: #667eea;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .lazy-load-end {
        text-align: center;
        padding: 32px 20px;
        color: #718096;
        font-weight: 600;
        font-size: 0.9375rem;
    }

    .lazy-load-trigger {
        height: 1px;
        width: 100%;
        visibility: hidden;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-value {
            font-size: 2rem;
        }
        .filter-container {
            padding: 20px;
        }
        .action-bar {
            padding: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid view-mode-default" id="viewModeContainer">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card primary">
                <div class="card-body">
                    <div class="stats-icon-wrapper">
                        <div class="stats-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Total Bookings</div>
                        <div class="stats-value">{{ number_format(\App\Models\Book::count()) }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-chart-line"></i>
                            <span>All time records</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card success">
                <div class="card-body">
                    <div class="stats-icon-wrapper">
                        <div class="stats-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Today's Bookings</div>
                        <div class="stats-value">{{ number_format(\App\Models\Book::whereDate('date_book', today())->count()) }}</div>
                        <div class="stats-trend">
                            <i class="fas fa-clock"></i>
                            <span>Created today</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card info">
                <div class="card-body">
                    <div class="stats-icon-wrapper">
                        <div class="stats-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">This Month</div>
                        <div class="stats-value">
                            {{ number_format(\App\Models\Book::whereMonth('date_book', now()->month)->whereYear('date_book', now()->year)->count()) }}
                        </div>
                        <div class="stats-trend">
                            <i class="fas fa-calendar-week"></i>
                            <span>{{ now()->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card warning">
                <div class="card-body">
                    <div class="stats-icon-wrapper">
                        <div class="stats-icon">
                            <i class="fas fa-cube"></i>
                        </div>
                    </div>
                    <div class="stats-content">
                        <div class="stats-label">Total Booths</div>
                        <div class="stats-value">
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
                        <div class="stats-trend">
                            <i class="fas fa-building"></i>
                            <span>All booked booths</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('books.create') }}" class="btn btn-modern btn-modern-primary">
                        <i class="fas fa-plus mr-2"></i>New Booking
                    </a>
                    <a href="{{ route('export.bookings') }}" class="btn btn-modern btn-modern-success">
                        <i class="fas fa-file-csv mr-2"></i>Export CSV
                    </a>
                    <button type="button" class="btn btn-modern btn-modern-info" onclick="refreshPage()">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                    @if(auth()->user()->isAdmin())
                    <button type="button" class="btn btn-modern" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); color: white;" onclick="showDeleteAllModal()">
                        <i class="fas fa-trash-alt mr-2"></i>Delete All Records
                    </button>
                    @endif
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap">
                    <!-- Master Layout Switcher -->
                    <div class="master-layout-switcher" style="display: flex; align-items: center; padding: 6px 10px; background: rgba(102, 126, 234, 0.05); border-radius: 8px; border: 1px solid rgba(102, 126, 234, 0.1); margin-right: 12px;">
                        <button type="button" class="master-layout-btn active" onclick="switchMasterLayout('default')" id="masterLayoutDefault" title="Default Layout" style="width: 32px; height: 32px; padding: 0; border-radius: 6px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; cursor: pointer; margin: 0 2px;">
                            <i class="fas fa-adjust" style="font-size: 12px;"></i>
                        </button>
                        <button type="button" class="master-layout-btn" onclick="switchMasterLayout('min')" id="masterLayoutMin" title="Minimal Layout" style="width: 32px; height: 32px; padding: 0; border-radius: 6px; background: #ffffff; border: 1.5px solid #e5e7eb; color: #6b7280; cursor: pointer; margin: 0 2px;">
                            <i class="fas fa-compress" style="font-size: 12px;"></i>
                        </button>
                        <button type="button" class="master-layout-btn" onclick="switchMasterLayout('max')" id="masterLayoutMax" title="Maximum Layout" style="width: 32px; height: 32px; padding: 0; border-radius: 6px; background: #ffffff; border: 1.5px solid #e5e7eb; color: #6b7280; cursor: pointer; margin: 0 2px;">
                            <i class="fas fa-expand" style="font-size: 12px;"></i>
                        </button>
                        <button type="button" class="master-layout-btn" onclick="switchMasterLayout('tiny')" id="masterLayoutTiny" title="Tiny Layout" style="width: 32px; height: 32px; padding: 0; border-radius: 6px; background: #ffffff; border: 1.5px solid #e5e7eb; color: #6b7280; cursor: pointer; margin: 0 2px;">
                            <i class="fas fa-compress-arrows-alt" style="font-size: 12px;"></i>
                        </button>
                    </div>
                    <div class="view-toggle">
                        <button type="button" class="active" onclick="switchView('table')" id="viewTable">
                            <i class="fas fa-table mr-1"></i>Table
                        </button>
                        <button type="button" onclick="switchView('cards')" id="viewCards">
                            <i class="fas fa-th-large mr-1"></i>Cards
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Search and Filter -->
    <div class="filter-container">
        <div class="filter-header">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                <span>Search & Filters</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleFilters()">
                <i class="fas fa-chevron-down" id="filterToggleIcon"></i>
            </button>
        </div>
        <div id="filterSection">
            <form method="GET" action="{{ route('books.index') }}" id="filterForm" onsubmit="event.preventDefault(); performInstantSearch(); return false;">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-600 mb-2"><i class="fas fa-search mr-1 text-primary"></i>Search</label>
                        <div class="input-group input-group-modern">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" name="search" id="instantSearchInput" class="form-control form-control-modern" 
                                   placeholder="Client name, company, or user..." 
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-600 mb-2"><i class="fas fa-calendar mr-1 text-primary"></i>From Date</label>
                        <input type="date" name="date_from" class="form-control form-control-modern" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="font-weight-600 mb-2"><i class="fas fa-calendar mr-1 text-primary"></i>To Date</label>
                        <input type="date" name="date_to" class="form-control form-control-modern" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="font-weight-600 mb-2"><i class="fas fa-tag mr-1 text-primary"></i>Type</label>
                        <select name="type" class="form-control form-control-modern">
                            <option value="">All Types</option>
                            <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Regular</option>
                            <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Special</option>
                            <option value="3" {{ request('type') == '3' ? 'selected' : '' }}>Temporary</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="button" onclick="performInstantSearch()" class="btn btn-modern btn-modern-primary" id="applyFiltersBtn">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('books.index') }}" class="btn btn-modern" style="background: #e2e8f0; color: #4a5568;">
                            <i class="fas fa-times mr-2"></i>Clear
                        </a>
                        <span class="ml-3 text-muted" style="font-size: 0.875rem; display: none;" id="instantSearchIndicator">
                            <i class="fas fa-search fa-spin mr-1"></i>Searching...
                        </span>
                        @if(request()->hasAny(['search', 'date_from', 'date_to', 'type']))
                        <span class="badge-modern badge-modern-info ml-3" style="padding: 12px 20px; font-size: 0.875rem;">
                            <i class="fas fa-check-circle mr-1"></i>{{ isset($total) ? $total : count($books) }} result(s) found
                        </span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table View (Compact Card/Icon View) -->
    <div id="tableView" class="view-content">
        <div class="compact-card-view" id="compactBookingsContainer">
            @forelse($books as $book)
            @php
                $boothIds = json_decode($book->boothid, true) ?? [];
                $boothCount = count($boothIds);
                $typeClass = 'regular';
                $typeBadge = 'badge-modern-primary';
                if ($book->type == 2) {
                    $typeClass = 'special';
                    $typeBadge = 'badge-modern-warning';
                } elseif ($book->type == 3) {
                    $typeClass = 'temporary';
                    $typeBadge = 'badge-modern-danger';
                }
                try {
                    $statusSetting = $book->statusSetting ?? \App\Models\BookingStatusSetting::getByCode($book->status ?? 1);
                    $statusColor = $statusSetting ? $statusSetting->status_color : '#6c757d';
                    $statusTextColor = $statusSetting && $statusSetting->text_color ? $statusSetting->text_color : '#ffffff';
                    $statusName = $statusSetting ? $statusSetting->status_name : 'Pending';
                } catch (\Exception $e) {
                    $statusColor = '#6c757d';
                    $statusTextColor = '#ffffff';
                    $statusName = 'Pending';
                }
                $totalAmount = $book->total_amount ?? \App\Models\Booth::whereIn('id', $boothIds)->sum('price');
                $paidAmount = $book->paid_amount ?? 0;
                $balanceAmount = $book->balance_amount ?? ($totalAmount - $paidAmount);
            @endphp
            <div class="compact-booking-card {{ $typeClass }}" onclick="window.location='{{ route('books.show', $book) }}'">
                <div class="compact-card-header">
                    <div class="compact-card-id">
                        <i class="fas fa-hashtag"></i>
                        <span>#{{ $book->id }}</span>
                    </div>
                    <span class="compact-card-badge {{ $typeBadge }}" style="background: {{ $statusColor }}; color: {{ $statusTextColor }};">
                        {{ $statusName }}
                    </span>
                </div>
                <div class="compact-card-content">
                    <div class="compact-card-row">
                        <i class="fas fa-building"></i>
                        <strong>{{ $book->client ? ($book->client->company ?? $book->client->name) : 'N/A' }}</strong>
                    </div>
                    @if($book->client && $book->client->name && $book->client->company)
                    <div class="compact-card-row">
                        <i class="fas fa-user"></i>
                        <span>{{ $book->client->name }}</span>
                    </div>
                    @endif
                    <div class="compact-card-row">
                        <i class="fas fa-cube"></i>
                        <span>{{ $boothCount }} {{ $boothCount == 1 ? 'Booth' : 'Booths' }}</span>
                    </div>
                    <div class="compact-card-row">
                        <i class="fas fa-calendar"></i>
                        <span>{{ $book->date_book->format('M d, Y') }}</span>
                        <i class="fas fa-clock ml-2"></i>
                        <span>{{ $book->date_book->format('h:i A') }}</span>
                    </div>
                    <div class="compact-card-row">
                        <i class="fas fa-dollar-sign"></i>
                        <strong style="color: #10b981;">${{ number_format($totalAmount, 2) }}</strong>
                        @if($balanceAmount > 0)
                        <span style="color: #f59e0b; margin-left: 8px;">Balance: ${{ number_format($balanceAmount, 2) }}</span>
                        @endif
                    </div>
                </div>
                <div class="compact-card-footer">
                    <div class="compact-card-row">
                        @if($book->user)
                        <x-avatar 
                            :avatar="$book->user->avatar" 
                            :name="$book->user->username" 
                            :size="'xs'" 
                            :type="$book->user->isAdmin() ? 'admin' : 'user'"
                            :shape="'circle'"
                        />
                        <span style="font-size: 0.75rem; color: #6b7280;">{{ $book->user->username }}</span>
                        @else
                        <i class="fas fa-server" style="color: #6b7280;"></i>
                        <span style="font-size: 0.75rem; color: #6b7280;">System</span>
                        @endif
                    </div>
                    <div class="compact-card-actions" onclick="event.stopPropagation()">
                        <a href="{{ route('books.show', $book) }}" class="btn btn-info btn-sm" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteBooking({{ $book->id }})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-center; padding: 40px;">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No bookings found</p>
            </div>
            @endforelse
        </div>
        <!-- Lazy Loading Trigger -->
        <div id="tableLazyLoadTrigger" style="height: 20px; margin: 10px 0;"></div>
        <!-- Lazy Loading Spinner -->
        <div id="tableLazyLoadSpinner" class="text-center py-3" style="display: none;">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <span class="ml-2 text-muted">Loading more bookings...</span>
        </div>
        <!-- Lazy Loading End -->
        <div id="tableLazyLoadEnd" class="text-center py-3" style="display: none;">
            <span class="text-muted">No more bookings to load</span>
        </div>
    </div>

    <!-- Card View -->
    <div id="cardView" class="view-content" style="display: none;">
        <div class="row" id="cardBookingsContainer">
            @forelse($books as $book)
            @php
                $boothIds = json_decode($book->boothid, true) ?? [];
                $boothCount = count($boothIds);
                $typeClass = 'regular';
                $typeBadge = 'badge-modern-primary';
                if ($book->type == 2) {
                    $typeClass = 'special';
                    $typeBadge = 'badge-modern-warning';
                } elseif ($book->type == 3) {
                    $typeClass = 'temporary';
                    $typeBadge = 'badge-modern-danger';
                }
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="booking-card-modern {{ $typeClass }}" onclick="window.location='{{ route('books.show', $book) }}'">
                    <div class="booking-card-header">
                        <div>
                            <h5 class="mb-0" style="font-weight: 700; color: #1a202c;">
                                <i class="fas fa-calendar-check mr-2"></i>Booking #{{ $book->id }}
                            </h5>
                        </div>
                        <span class="badge-modern {{ $typeBadge }}">
                            @if($book->type == 1) Regular
                            @elseif($book->type == 2) Special
                            @elseif($book->type == 3) Temporary
                            @else {{ $book->type }}
                            @endif
                        </span>
                    </div>
                    <div class="booking-card-body">
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="mr-3">
                                    <x-avatar 
                                        :avatar="$book->client->avatar ?? null" 
                                        :name="$book->client->name ?? 'N/A'" 
                                        :size="'md'" 
                                        :type="'client'"
                                        :shape="'circle'"
                                    />
                                </div>
                                <div>
                                    <div class="font-weight-700" style="font-size: 1.125rem; color: #1a202c;">
                                        {{ $book->client ? ($book->client->company ?? $book->client->name) : 'N/A' }}
                                    </div>
                                    @if($book->client && $book->client->name && $book->client->company)
                                    <div class="text-muted" style="font-size: 0.875rem; margin-top: 4px;">
                                        <i class="fas fa-user mr-1"></i>{{ $book->client->name }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @if($book->client && $book->client->email)
                            <div class="text-muted ml-5" style="font-size: 0.8125rem;">
                                <i class="fas fa-envelope mr-1"></i>{{ $book->client->email }}
                            </div>
                            @endif
                        </div>
                        
                        <div class="mb-3 d-flex align-items-center">
                            <div class="mr-3">
                                <span class="badge-modern badge-modern-info" style="font-size: 1rem; padding: 10px 18px;">
                                    <i class="fas fa-cube mr-2"></i>{{ $boothCount }} {{ $boothCount == 1 ? 'Booth' : 'Booths' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-calendar text-muted mr-2" style="width: 20px;"></i>
                                <strong style="color: #1a202c;">{{ $book->date_book->format('M d, Y') }}</strong>
                            </div>
                            <div class="ml-5 text-muted" style="font-size: 0.875rem;">
                                <i class="fas fa-clock mr-1"></i>{{ $book->date_book->format('h:i A') }}
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <span class="text-muted mr-2" style="font-size: 0.875rem;">Booked by:</span>
                            @if($book->user)
                                <x-avatar 
                                    :avatar="$book->user->avatar" 
                                    :name="$book->user->username" 
                                    :size="'xs'" 
                                    :type="$book->user->isAdmin() ? 'admin' : 'user'"
                                    :shape="'circle'"
                                />
                                <span class="text-muted ml-2" style="font-size: 0.875rem;">{{ $book->user->username }}</span>
                            @else
                                <i class="fas fa-server text-muted mr-1"></i>
                                <span class="text-muted" style="font-size: 0.875rem;">System</span>
                            @endif
                        </div>
                    </div>
                    <div class="booking-card-footer">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('books.show', $book) }}" class="btn btn-info" onclick="event.stopPropagation()" style="border-radius: 12px 0 0 12px;">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            @if(auth()->user()->isAdmin())
                            <button type="button" class="btn btn-danger" onclick="event.stopPropagation(); deleteBooking({{ $book->id }});" style="border-radius: 0 12px 12px 0;">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h4 style="color: #4a5568; margin-bottom: 12px;">No Bookings Found</h4>
                    <p class="text-muted mb-4">Get started by creating your first booking</p>
                    <a href="{{ route('books.create') }}" class="btn btn-modern btn-modern-primary">
                        <i class="fas fa-plus mr-2"></i>Create First Booking
                    </a>
                </div>
            </div>
            @endforelse
        </div>
        <!-- Lazy Load Trigger -->
        <div class="lazy-load-trigger" id="cardLazyLoadTrigger"></div>
        <!-- Lazy Load Spinner -->
        <div class="lazy-load-spinner" id="cardLazyLoadSpinner">
            <i class="fas fa-spinner"></i>
            <p style="margin-top: 16px; color: #718096; font-weight: 600;">Loading more bookings...</p>
        </div>
        <!-- End Message -->
        <div class="lazy-load-end" id="cardLazyLoadEnd" style="display: none;">
            <i class="fas fa-check-circle mr-2" style="color: #48bb78;"></i>
            All bookings loaded
        </div>
    </div>
</div>

{{-- Modal removed - direct navigation to create page instead --}}
{{-- @include('books.modal-create') --}}

<!-- Delete All Records Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1" role="dialog" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title" id="deleteAllModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Delete All Booking Records
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 32px;">
                <div class="alert alert-danger" role="alert" style="border-radius: 12px; border-left: 4px solid #e53e3e;">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle mr-2"></i>Warning!</h5>
                    <p class="mb-2"><strong>This action will permanently delete ALL booking records from the database.</strong></p>
                    <p class="mb-0">This action cannot be undone. Please verify your password to confirm this operation.</p>
                </div>
                <div class="mb-3">
                    <label for="deleteAllPassword" class="font-weight-600 mb-2">
                        <i class="fas fa-lock mr-1 text-danger"></i>Enter Your Password to Confirm
                    </label>
                    <input type="password" class="form-control form-control-modern" id="deleteAllPassword" 
                           placeholder="Enter your password" autocomplete="current-password">
                    <small class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle mr-1"></i>You must enter your current password to proceed.
                    </small>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirmDeleteAll" required>
                    <label class="form-check-label" for="confirmDeleteAll">
                        I understand this will permanently delete <strong>ALL</strong> booking records and this action cannot be undone.
                    </label>
                </div>
                <div id="deleteAllError" class="alert alert-danger" style="display: none; border-radius: 12px;"></div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 20px 32px; border-radius: 0 0 20px 20px;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 12px; padding: 10px 24px;">
                    <i class="fas fa-times mr-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteAll()" style="border-radius: 12px; padding: 10px 24px; background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); border: none;">
                    <i class="fas fa-trash-alt mr-1"></i>Delete All Records
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Make functions available globally immediately
window.switchView = function(view) {
    currentView = view;
    if (view === 'table') {
        $('#tableView').show();
        $('#cardView').hide();
        $('#viewTable').addClass('active');
        $('#viewCards').removeClass('active');
        localStorage.setItem('bookingView', 'table');
    } else {
        $('#tableView').hide();
        $('#cardView').show();
        $('#viewTable').removeClass('active');
        $('#viewCards').addClass('active');
        localStorage.setItem('bookingView', 'cards');
    }
    
    // Reset lazy loading when switching views
    resetLazyLoading();
}

// Reset Lazy Loading
function resetLazyLoading() {
    currentPage = 1;
    isLoading = false;
    hasMoreData = true;
    $('#tableLazyLoadSpinner').removeClass('active');
    $('#cardLazyLoadSpinner').removeClass('active');
    $('#tableLazyLoadEnd').hide();
    $('#cardLazyLoadEnd').hide();
}

// Lazy Loading Variables
let currentPage = 1;
let isLoading = false;
let hasMoreData = true;
let currentView = 'table';
let currentViewMode = 'default';
let filterParams = {};

// Master Layout Toggle
window.switchMasterLayout = function(layout) {
    // Remove all master layout classes from body
    $('body').removeClass('master-layout-default master-layout-min master-layout-max master-layout-tiny');
    
    // Add selected layout class
    $('body').addClass('master-layout-' + layout);
    
    // Update button states
    $('.master-layout-btn').removeClass('active');
    $('#masterLayout' + layout.charAt(0).toUpperCase() + layout.slice(1)).addClass('active');
    
    // Save preference
    localStorage.setItem('bookingMasterLayout', layout);
}

// View Mode Toggle
window.switchViewMode = function(mode) {
    currentViewMode = mode;
    
    // Remove all mode classes
    $('#viewModeContainer').removeClass('view-mode-default view-mode-minimal view-mode-expand');
    
    // Add selected mode class
    $('#viewModeContainer').addClass('view-mode-' + mode);
    
    // Update button states
    $('.view-mode-selector button').removeClass('active');
    $('#viewMode' + mode.charAt(0).toUpperCase() + mode.slice(1)).addClass('active');
    
    // Save preference
    localStorage.setItem('bookingViewMode', mode);
}

// Instant Search Variables
let instantSearchTimeout = null;
let isSearching = false;

// Instant Search Function
window.performInstantSearch = function() {
    // Only enable instant search on desktop (screen width > 768px)
    if ($(window).width() <= 768) {
        // On mobile, submit form normally
        $('#filterForm').off('submit').submit();
        return;
    }
    
    if (isSearching) return;
    
    const searchQuery = $('#instantSearchInput').val().trim();
    const dateFrom = $('input[name="date_from"]').val();
    const dateTo = $('input[name="date_to"]').val();
    const type = $('select[name="type"]').val();
    
    // Update filter params
    filterParams = {
        search: searchQuery,
        date_from: dateFrom,
        date_to: dateTo,
        type: type
    };
    
    // Reset lazy loading
    currentPage = 1;
    hasMoreData = true;
    isLoading = false;
    
    // Show loading indicator
    if (currentView === 'table') {
        $('#compactBookingsContainer').html('<div style="grid-column: 1 / -1; text-center; padding: 40px;"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-3 text-muted">Searching...</p></div>');
    } else {
        $('#cardBookingsContainer').html('<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-3 text-muted">Searching...</p></div>');
    }
    
    isSearching = true;
    
    // Show search indicator
    $('#instantSearchIndicator').show();
    
    // Build params
    const params = new URLSearchParams({
        page: 1,
        view: currentView
    });
    
    if (searchQuery) params.append('search', searchQuery);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    if (type) params.append('type', type);
    
    fetch('{{ route("books.index") }}?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.html) {
            if (currentView === 'table') {
                $('#compactBookingsContainer').html(data.html);
            } else {
                $('#cardBookingsContainer').html(data.html);
            }
            
            hasMoreData = data.hasMore !== false;
            
            if (!data.hasMore) {
                const endId = currentView === 'table' ? 'tableLazyLoadEnd' : 'cardLazyLoadEnd';
                $('#' + endId).show();
            } else {
                const endId = currentView === 'table' ? 'tableLazyLoadEnd' : 'cardLazyLoadEnd';
                $('#' + endId).hide();
                // Re-initialize lazy loading
                setTimeout(function() {
                    initLazyLoading();
                }, 100);
            }
        } else {
            // No results
            if (currentView === 'table') {
                $('#compactBookingsContainer').html('<div style="grid-column: 1 / -1; text-center; padding: 40px;"><i class="fas fa-search fa-3x text-muted mb-3"></i><p class="text-muted">No bookings found matching your search</p></div>');
            } else {
                $('#cardBookingsContainer').html('<div class="col-12 text-center py-5"><i class="fas fa-search fa-3x text-muted mb-3"></i><p class="text-muted">No bookings found matching your search</p></div>');
            }
            hasMoreData = false;
        }
    })
    .catch(error => {
        console.error('Error performing instant search:', error);
        if (currentView === 'table') {
            $('#compactBookingsContainer').html('<div style="grid-column: 1 / -1; text-center; padding: 40px;"><i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i><p class="text-danger">Error searching bookings. Please try again.</p></div>');
        } else {
            $('#cardBookingsContainer').html('<div class="col-12 text-center py-5"><i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i><p class="text-danger">Error searching bookings. Please try again.</p></div>');
        }
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to search bookings. Please try again.');
        }
    })
    .finally(() => {
        isSearching = false;
        $('#instantSearchIndicator').hide();
    });
};

// Load saved view preference
$(document).ready(function() {
    // Load master layout preference
    const savedMasterLayout = localStorage.getItem('bookingMasterLayout') || 'default';
    switchMasterLayout(savedMasterLayout);
    
    // Load view mode preference
    const savedViewMode = localStorage.getItem('bookingViewMode') || 'default';
    switchViewMode(savedViewMode);
    
    // Load view type preference
    const savedView = localStorage.getItem('bookingView') || 'table';
    switchView(savedView);
    currentView = savedView;
    
    // Store filter parameters
    filterParams = {
        search: '{{ request('search') }}',
        date_from: '{{ request('date_from') }}',
        date_to: '{{ request('date_to') }}',
        type: '{{ request('type') }}'
    };
    
    // Initialize lazy loading
    initLazyLoading();
    
    // Instant Search Input Handler (Desktop Only)
    $(document).on('input', '#instantSearchInput', function() {
        // Only enable instant search on desktop (screen width > 768px)
        if ($(window).width() <= 768) {
            return;
        }
        
        clearTimeout(instantSearchTimeout);
        
        const query = $(this).val().trim();
        
        // Show search indicator while typing
        if (query.length > 0) {
            $('#instantSearchIndicator').show();
        } else {
            $('#instantSearchIndicator').hide();
        }
        
        // If search is completely cleared and no other filters, reload page
        if (query.length === 0 && !$('input[name="date_from"]').val() && !$('input[name="date_to"]').val() && !$('select[name="type"]').val()) {
            // Only reload if we're not already on the base page
            if (window.location.search) {
                clearTimeout(instantSearchTimeout);
                window.location.href = '{{ route("books.index") }}';
                return;
            }
        }
        
        // Debounce: wait 500ms after user stops typing
        instantSearchTimeout = setTimeout(function() {
            performInstantSearch();
        }, 500);
    });

    // Also trigger search on date and type changes (Desktop Only)
    $(document).on('change', 'input[name="date_from"], input[name="date_to"], select[name="type"]', function() {
        // Only enable instant search on desktop (screen width > 768px)
        if ($(window).width() <= 768) {
            return;
        }
        
        clearTimeout(instantSearchTimeout);
        instantSearchTimeout = setTimeout(function() {
            performInstantSearch();
        }, 300);
    });

    // Prevent form submission on Enter key in search (use instant search instead - Desktop Only)
    $(document).on('keydown', '#instantSearchInput', function(e) {
        if (e.key === 'Enter') {
            // Only prevent default on desktop
            if ($(window).width() > 768) {
                e.preventDefault();
                clearTimeout(instantSearchTimeout);
                performInstantSearch();
            }
        }
    });
    
    // Add search icon animation on focus
    $('#instantSearchInput').on('focus', function() {
        $(this).closest('.input-group').addClass('searching');
    }).on('blur', function() {
        $(this).closest('.input-group').removeClass('searching');
    });
});

// Lazy Loading Observer (global to allow re-initialization)
let lazyLoadObserver = null;

// Initialize Lazy Loading
function initLazyLoading() {
    // Disconnect existing observer if any
    if (lazyLoadObserver) {
        lazyLoadObserver.disconnect();
    }
    
    // Use Intersection Observer API for better performance
    const tableTrigger = document.getElementById('tableLazyLoadTrigger');
    const cardTrigger = document.getElementById('cardLazyLoadTrigger');
    
    const observerOptions = {
        root: null,
        rootMargin: '200px',
        threshold: 0.1
    };
    
    lazyLoadObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && hasMoreData && !isLoading) {
                loadMoreBookings();
            }
        });
    }, observerOptions);
    
    // Observe trigger based on current view
    if (currentView === 'table' && tableTrigger) {
        lazyLoadObserver.observe(tableTrigger);
    } else if (currentView === 'cards' && cardTrigger) {
        lazyLoadObserver.observe(cardTrigger);
    }
}

// Load More Bookings
function loadMoreBookings() {
    if (isLoading || !hasMoreData) return;
    
    isLoading = true;
    currentPage++;
    
    const spinnerId = currentView === 'table' ? 'tableLazyLoadSpinner' : 'cardLazyLoadSpinner';
    $('#' + spinnerId).addClass('active');
    
    // Build params exactly like initial load
    const params = new URLSearchParams({
        page: currentPage
    });
    
    // Add filter params if they exist
    if (filterParams.search) params.append('search', filterParams.search);
    if (filterParams.date_from) params.append('date_from', filterParams.date_from);
    if (filterParams.date_to) params.append('date_to', filterParams.date_to);
    if (filterParams.type) params.append('type', filterParams.type);
    params.append('view', currentView);
    
    fetch('{{ route("books.index") }}?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
        .then(data => {
            if (data.success && data.html) {
                if (currentView === 'table') {
                    // Append new compact cards to container
                    $('#compactBookingsContainer').append(data.html);
                } else {
                    // Append new cards to card container
                    $('#cardBookingsContainer').append(data.html);
                }
            
            hasMoreData = data.hasMore !== false;
            
            if (!data.hasMore) {
                const endId = currentView === 'table' ? 'tableLazyLoadEnd' : 'cardLazyLoadEnd';
                $('#' + endId).show();
                $('#' + spinnerId).removeClass('active');
            } else {
                // Re-initialize lazy loading observer for new content after a brief delay
                setTimeout(function() {
                    initLazyLoading();
                }, 100);
            }
        } else {
            hasMoreData = false;
            $('#' + spinnerId).removeClass('active');
        }
    })
    .catch(error => {
        console.error('Error loading more bookings:', error);
        hasMoreData = false;
        $('#' + spinnerId).removeClass('active');
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to load more bookings. Please try again.');
        }
    })
    .finally(() => {
        isLoading = false;
        $('#' + spinnerId).removeClass('active');
    });
}

// Toggle Filters
window.toggleFilters = function() {
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
window.deleteBooking = function(id) {
    if (confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
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
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message || 'Booking deleted successfully');
                } else {
                    alert(data.message || 'Booking deleted successfully');
                }
                setTimeout(() => location.reload(), 1000);
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(data.message || 'Failed to delete booking');
                } else {
                    alert(data.message || 'Failed to delete booking');
                }
            }
        })
        .catch(error => {
            hideLoading();
            if (typeof toastr !== 'undefined') {
                toastr.error('Error: ' + error.message);
            } else {
                alert('Error: ' + error.message);
            }
        });
    }
}

// Refresh Page
window.refreshPage = function() {
    if (typeof showLoading === 'function') {
        showLoading();
    }
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Delete All Records Modal
window.showDeleteAllModal = function() {
    $('#deleteAllModal').modal('show');
    $('#deleteAllPassword').val('');
    $('#confirmDeleteAll').prop('checked', false);
    $('#deleteAllError').hide().text('');
}

function confirmDeleteAll() {
    const password = $('#deleteAllPassword').val();
    const confirmed = $('#confirmDeleteAll').prop('checked');
    
    if (!password) {
        $('#deleteAllError').text('Please enter your password.').show();
        $('#deleteAllPassword').focus();
        return;
    }
    
    if (!confirmed) {
        $('#deleteAllError').text('Please confirm that you understand the consequences of this action.').show();
        return;
    }
    
    // Final confirmation
    if (!confirm('Are you absolutely sure you want to delete ALL booking records? This action cannot be undone!')) {
        return;
    }
    
    showLoading();
    
    fetch('{{ route("books.delete-all") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            $('#deleteAllModal').modal('hide');
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message || 'All booking records deleted successfully');
            } else {
                alert(data.message || 'All booking records deleted successfully');
            }
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            $('#deleteAllError').text(data.message || 'Failed to delete records. Please check your password.').show();
            $('#deleteAllPassword').val('').focus();
        }
    })
    .catch(error => {
        hideLoading();
        $('#deleteAllError').text('An error occurred: ' + error.message).show();
        console.error('Error:', error);
    });
}

// Clear error when modal is closed
$('#deleteAllModal').on('hidden.bs.modal', function () {
    $('#deleteAllPassword').val('');
    $('#confirmDeleteAll').prop('checked', false);
    $('#deleteAllError').hide().text('');
});

// Allow Enter key to submit
$('#deleteAllPassword').on('keypress', function(e) {
    if (e.which === 13) {
        confirmDeleteAll();
    }
});

// Show Create Booking Modal - REMOVED: Now using direct navigation to books.create route
/*
function showCreateBookingModal() {
    $('#createBookingModal').modal('show');
    $('#createBookingForm')[0].reset();
    $('#createBookingError').hide();
    $('#modal_date_book').val(new Date().toISOString().slice(0, 16));
    
    // Ensure all checkboxes have correct class and no inline handlers
    $('#modalBoothSelector .booth-checkbox').each(function() {
        $(this).removeClass('booth-checkbox').addClass('modal-booth-checkbox');
        $(this).removeAttr('onchange');
    });
    $('#modalBoothSelector .booth-option').removeClass('booth-option').addClass('booth-option-modal');
    
    modalUpdateSelection();
    
    // Filter booths by floor plan
    $('#modal_floor_plan_filter').off('change').on('change', function() {
        const floorPlanId = $(this).val();
        filterBoothsInModal(floorPlanId);
    });
}
*/

// Filter booths in modal by floor plan
function filterBoothsInModal(floorPlanId) {
    const boothList = $('#modalBoothList');
    const url = '{{ route("books.create") }}' + (floorPlanId ? '?floor_plan_id=' + floorPlanId : '');
    
    // Show loading
    boothList.html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3">Loading booths...</p></div>');
    
    // Load booths via AJAX
    $.ajax({
        url: url,
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(html) {
            // Extract booth list from response
            const tempDiv = $('<div>').html(html);
            const newBoothList = tempDiv.find('#boothSelector .row').html() || tempDiv.find('.booth-selector .row').html();
            
            if (newBoothList) {
                // Clean up the HTML: remove inline onchange handlers and fix classes
                const cleanedHtml = $(newBoothList);
                
                // Remove any inline onchange handlers and update classes
                cleanedHtml.find('input[type="checkbox"]').each(function() {
                    // Remove inline onchange attribute
                    $(this).removeAttr('onchange');
                    // Ensure it has the modal-booth-checkbox class
                    $(this).removeClass('booth-checkbox').addClass('modal-booth-checkbox');
                });
                
                // Update booth-option to booth-option-modal
                cleanedHtml.find('.booth-option').removeClass('booth-option').addClass('booth-option-modal');
                
                boothList.html(cleanedHtml);
                
                // Re-attach event handlers using event delegation
                boothList.off('change', '.modal-booth-checkbox').on('change', '.modal-booth-checkbox', function() {
                    modalUpdateSelection();
                });
                
                $(document).off('click', '#modalBoothSelector .booth-option-modal').on('click', '#modalBoothSelector .booth-option-modal', function(e) {
                    if (e.target.type !== 'checkbox' && !$(e.target).closest('input').length) {
                        const checkbox = $(this).find('input[type="checkbox"]');
                        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                    }
                });
            } else {
                boothList.html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i>No available booths found.</div>');
            }
            modalUpdateSelection();
        },
        error: function() {
            boothList.html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading booths. Please try again.</div>');
        }
    });
}

// Modal Booth Selection Functions - Must be global for inline handlers
window.modalUpdateSelection = function() {
    const selected = [];
    let totalAmount = 0;
    
    $('.modal-booth-checkbox:checked').each(function() {
        const boothId = $(this).val();
        const boothOption = $(this).closest('.booth-option-modal');
        const boothNumber = boothOption.find('strong').text();
        const price = parseFloat(boothOption.data('price')) || 0;
        
        selected.push({ id: boothId, number: boothNumber, price: price });
        totalAmount += price;
    });
    
    // Update selected list
    const listContainer = $('#modalSelectedBoothsList');
    if (selected.length > 0) {
        let html = '';
        selected.forEach(function(booth) {
            html += '<div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded border">';
            html += '<div><i class="fas fa-cube text-primary mr-1"></i><strong style="font-size: 0.875rem;">' + booth.number + '</strong></div>';
            html += '<strong class="text-success" style="font-size: 0.875rem;">$' + booth.price.toFixed(2) + '</strong>';
            html += '</div>';
        });
        listContainer.html(html);
    } else {
        listContainer.html('<p class="text-muted text-center mb-0 py-4" style="font-size: 0.875rem;">No booths selected</p>');
    }
    
    // Update summary
    $('#modalTotalBooths').text(selected.length);
    $('#modalTotalAmount').text('$' + totalAmount.toFixed(2));
    
    // Update visual state
    $('.booth-option-modal').removeClass('selected');
    $('.modal-booth-checkbox:checked').closest('.booth-option-modal').addClass('selected');
    
    // Show/hide warning
    if (selected.length === 0) {
        $('#modalSelectionWarning').show();
        $('#createBookingSubmitBtn').prop('disabled', true);
    } else {
        $('#modalSelectionWarning').hide();
        $('#createBookingSubmitBtn').prop('disabled', false);
    }
}

window.modalSelectAllBooths = function() {
    $('.modal-booth-checkbox').prop('checked', true);
    modalUpdateSelection();
};

window.modalClearSelection = function() {
    $('.modal-booth-checkbox').prop('checked', false);
    modalUpdateSelection();
};

// Handle Create Booking Form Submission
$(document).ready(function() {
    // Create Booking Form
    $('#createBookingForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createBookingSubmitBtn');
        const errorDiv = $('#createBookingError');
        const originalText = submitBtn.html();
        
        // Validate
        const selectedCount = $('.modal-booth-checkbox:checked').length;
        if (selectedCount === 0) {
            errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i>Please select at least one booth.').show();
            return;
        }
        
        // Hide error
        errorDiv.hide();
        
        // Validate form
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }
        
        // Disable submit button
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');
        
        // Submit via AJAX
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    // Modal removed - redirect to bookings page instead
                    // $('#createBookingModal').modal('hide');
                    window.location.href = '{{ route("books.index") }}';
                    form[0].reset();
                    errorDiv.hide();
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Booking created successfully');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Booking created successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(response.message || 'Booking created successfully');
                    }
                    
                    // Refresh page after short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the booking.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const firstError = Object.values(errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                }
                
                errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i>' + errorMessage).show();
                errorDiv[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Handle Create Client Form in Booking Modal
    $('#createClientFormInBooking').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createClientSubmitBtnInBooking');
        const errorDiv = $('#createClientErrorInBooking');
        const originalText = submitBtn.html();
        
        errorDiv.hide();
        
        // Remove HTML5 validation since all fields are optional
        // if (!form[0].checkValidity()) {
        //     form[0].reportValidity();
        //     return;
        // }
        
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
                    const client = response.client;
                    
                    // Select the newly created client using the modalSelectClient function
                    if (typeof modalSelectClient === 'function') {
                        modalSelectClient(client);
                    } else {
                        // Fallback if modalSelectClient is not available
                        $('#modal_clientid').val(client.id);
                        const displayName = client.company || client.name || 'N/A';
                        let details = [];
                        if (client.email) details.push('<i class="fas fa-envelope mr-1"></i>' + client.email);
                        if (client.phone_number) details.push('<i class="fas fa-phone mr-1"></i>' + client.phone_number);
                        
                        $('#modalSelectedClientName').text(displayName);
                        $('#modalSelectedClientDetails').html(details.length > 0 ? details.join(' <span class="mx-2 text-muted">|</span> ') : '');
                        $('#modalSelectedClientInfo').show();
                        $('#modalClientSearchContainer').hide();
                    }
                    
                    $('#createClientModalInBooking').modal('hide');
                    form[0].reset();
                    errorDiv.hide();
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Client Created!',
                            text: 'Client "' + (client.company || client.name) + '" has been created and selected.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success('Client created and selected successfully');
                    } else {
                        alert('Client created successfully!');
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the client.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    // Collect all error messages
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
                // Safely scroll to error div if it exists
                if (errorDiv.length > 0 && errorDiv[0]) {
                    try {
                        errorDiv[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } catch (e) {
                        // Fallback if scrollIntoView fails
                        const modalBody = errorDiv.closest('.modal-body');
                        if (modalBody.length > 0 && modalBody[0]) {
                            modalBody[0].scrollTop = 0;
                        }
                    }
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Form validation for modal booking form
    $('#createBookingForm').on('submit', function(e) {
        // Validate client selection
        const clientId = $('#modal_clientid').val();
        if (!clientId || clientId === '' || clientId === null) {
            e.preventDefault();
            e.stopPropagation();
            
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
            
            if ($('#modalClientSearchContainer').is(':hidden')) {
                $('#modalSelectedClientInfo').hide();
                $('#modalClientSearchContainer').show();
            }
            
            setTimeout(function() {
                $('#modalClientSearchInline').focus();
            }, 100);
            
            return false;
        }
        
        // Validate booth selection
        const selectedCount = $('.modal-booth-checkbox:checked').length;
        if (selectedCount === 0) {
            e.preventDefault();
            e.stopPropagation();
            
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
    });
    
    // Reset forms when modals are closed - MODAL REMOVED
    /*
    $('#createBookingModal').on('hidden.bs.modal', function() {
        $('#createBookingForm')[0].reset();
        $('#createBookingError').hide();
        $('.modal-booth-checkbox').prop('checked', false);
        modalUpdateSelection();
        // Client selection is reset in the modal client search section above
    });
    */
    
    $('#createClientModalInBooking').on('hidden.bs.modal', function() {
        $('#createClientFormInBooking')[0].reset();
        $('#createClientErrorInBooking').hide();
    });
    
    // Booth option click handler for modal
    $(document).on('click', '.booth-option-modal', function(e) {
        if (e.target.type !== 'checkbox' && !$(e.target).closest('input').length) {
            const checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        }
    });
    
    // Bind change event to modal booth checkboxes
    // Use event delegation for modal booth checkboxes (handles both initial and dynamically loaded checkboxes)
    $(document).off('change', '#modalBoothSelector .modal-booth-checkbox').on('change', '#modalBoothSelector .modal-booth-checkbox', function() {
        modalUpdateSelection();
    });
    
    // Initialize selection when modal is shown - MODAL REMOVED
    /*
    $('#createBookingModal').on('shown.bs.modal', function() {
        modalUpdateSelection();
    });
    */
    
    // ============================================
    // MODAL CLIENT SEARCH FUNCTIONALITY
    // Same as main create page
    // ============================================
    
    let modalSelectedClient = null;
    let modalInlineSearchTimeout = null;
    let modalClientSearchTimeout = null;
    
    // Modal Inline Client Search - Auto-suggest function
    function modalSearchClientsInline(query) {
        if (!query || query.length < 2) {
            $('#modalInlineClientResults').hide();
            return;
        }
        
        const resultsDiv = $('#modalInlineClientResults');
        const resultsList = $('#modalInlineClientResultsList');
        const searchIcon = $('#modalSearchIcon');
        
        if (searchIcon.length) {
            searchIcon.removeClass('fa-search').addClass('fa-spinner fa-spin');
        }
        
        resultsDiv.show();
        resultsList.html(
            '<div class="client-results-loading">' +
                '<i class="fas fa-spinner fa-spin"></i>' +
                '<p class="mb-0 mt-2">Searching clients...</p>' +
            '</div>'
        );
        
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
                    resultsList.html(
                        '<div class="client-results-empty">' +
                            '<i class="fas fa-search"></i>' +
                            '<p class="mb-0"><strong>No clients found</strong></p>' +
                            '<p class="mb-0 mt-1" style="font-size: 0.85rem;">Try different keywords or create a new client</p>' +
                        '</div>'
                    );
                    return;
                }
                
                clientsArray.slice(0, 8).forEach(function(client) {
                    const displayName = (client.company || client.name || 'N/A');
                    const highlightQuery = query.toLowerCase();
                    
                    let highlightedName = displayName;
                    if (highlightQuery) {
                        const escapedQuery = highlightQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        const regex = new RegExp(`(${escapedQuery})`, 'gi');
                        highlightedName = displayName.replace(regex, '<mark>$1</mark>');
                    }
                    
                    let detailsHTML = '<div class="client-result-details">';
                    if (client.name && client.company) {
                        detailsHTML += '<div class="client-result-detail user"><i class="fas fa-user"></i><span>' + client.name + '</span></div>';
                    }
                    if (client.email) {
                        detailsHTML += '<div class="client-result-detail email"><i class="fas fa-envelope"></i><span>' + client.email + '</span></div>';
                    }
                    if (client.phone_number) {
                        detailsHTML += '<div class="client-result-detail phone"><i class="fas fa-phone"></i><span>' + client.phone_number + '</span></div>';
                    }
                    detailsHTML += '</div>';
                    
                    const item = $('<div class="client-search-result"></div>')
                        .html(
                            '<div class="client-result-content">' +
                                '<div class="client-result-name">' +
                                    '<i class="fas fa-building"></i>' +
                                    '<span>' + highlightedName + '</span>' +
                                '</div>' +
                                detailsHTML +
                            '</div>' +
                            '<button type="button" class="btn btn-modern btn-modern-primary select-client-inline-btn" data-client-id="' + client.id + '">' +
                                '<i class="fas fa-check"></i>' +
                            '</button>'
                        )
                        .data('client', client);
                    
                    resultsList.append(item);
                });
                
                $(document).off('click', '#modalInlineClientResultsList .select-client-inline-btn').on('click', '#modalInlineClientResultsList .select-client-inline-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const client = $(this).closest('.client-search-result').data('client');
                    if (client) {
                        modalSelectClient(client);
                        $('#modalInlineClientResults').hide();
                    }
                });
                
                $(document).off('click', '#modalInlineClientResultsList .client-search-result').on('click', '#modalInlineClientResultsList .client-search-result', function(e) {
                    if (!$(e.target).closest('.select-client-inline-btn').length) {
                        e.preventDefault();
                        const client = $(this).data('client');
                        if (client) {
                            modalSelectClient(client);
                            $('#modalInlineClientResults').hide();
                        }
                    }
                });
            },
            error: function(xhr) {
                if (searchIcon.length) {
                    searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
                resultsList.html(
                    '<div class="client-results-empty" style="color: #e74a3b;">' +
                        '<i class="fas fa-exclamation-triangle"></i>' +
                        '<p class="mb-0"><strong>Error</strong></p>' +
                        '<p class="mb-0 mt-1" style="font-size: 0.85rem;">Error searching clients. Please try again.</p>' +
                    '</div>'
                );
            }
        });
    }
    
    // Modal Inline search input handler
    $(document).on('input keyup paste', '#modalClientSearchInline', function(e) {
        if ([38, 40, 13, 27].includes(e.keyCode)) {
            return;
        }
        
        const query = $(this).val().trim();
        clearTimeout(modalInlineSearchTimeout);
        
        if (query.length < 2) {
            $('#modalInlineClientResults').hide();
            const searchIcon = $('#modalSearchIcon');
            if (searchIcon.length) {
                searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
            }
            if (query.length === 0 && modalSelectedClient) {
                modalSelectedClient = null;
                $('#modal_clientid').val('');
                $('#modalSelectedClientInfo').hide();
                $('#modalClientSearchContainer').show();
            }
            return;
        }
        
        modalInlineSearchTimeout = setTimeout(function() {
            modalSearchClientsInline(query);
        }, 300);
    });
    
    // Modal Select Client Function
    function modalSelectClient(client) {
        modalSelectedClient = client;
        $('#modal_clientid').val(client.id);
        
        const displayName = client.company || client.name || 'N/A';
        let details = [];
        if (client.email) details.push('<i class="fas fa-envelope mr-1"></i>' + client.email);
        if (client.phone_number) details.push('<i class="fas fa-phone mr-1"></i>' + client.phone_number);
        
        $('#modalSelectedClientName').text(displayName);
        $('#modalSelectedClientDetails').html(details.length > 0 ? details.join(' <span class="mx-2 text-muted">|</span> ') : '');
        $('#modalSelectedClientInfo').show();
        $('#modalClientSearchContainer').hide();
        $('#modalClientSearchInline').val('');
        $('#modalInlineClientResults').hide();
        $('#modalSearchClientModal').modal('hide');
    }
    
    // Modal Clear Client Selection
    $(document).on('click', '#modalBtnClearClient', function() {
        modalSelectedClient = null;
        $('#modal_clientid').val('');
        $('#modalSelectedClientInfo').hide();
        $('#modalClientSearchContainer').show();
        $('#modalClientSearchInline').val('');
        $('#modalInlineClientResults').hide();
        setTimeout(function() {
            $('#modalClientSearchInline').focus();
        }, 100);
    });
    
    // Modal Search Client Function (for modal)
    function modalSearchClients(query) {
        if (!query || query.length < 2) {
            $('#modalClientSearchResults').hide();
            $('#modalNoClientResults').hide();
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
                const resultsDiv = $('#modalClientSearchResults');
                const resultsList = $('#modalClientSearchResultsList');
                const noResultsDiv = $('#modalNoClientResults');
                
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
                    if (client.name && client.company) {
                        detailsHTML += '<div class="client-result-detail user"><i class="fas fa-user"></i><span>' + client.name + '</span></div>';
                    }
                    if (client.email) {
                        detailsHTML += '<div class="client-result-detail email"><i class="fas fa-envelope"></i><span>' + client.email + '</span></div>';
                    }
                    if (client.phone_number) {
                        detailsHTML += '<div class="client-result-detail phone"><i class="fas fa-phone"></i><span>' + client.phone_number + '</span></div>';
                    }
                    if (client.address) {
                        detailsHTML += '<div class="client-result-detail"><i class="fas fa-map-marker-alt"></i><span>' + client.address + '</span></div>';
                    }
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
                    modalSelectClient(client);
                });
                
                resultsList.find('.client-search-result').on('click', function(e) {
                    if (!$(e.target).closest('.select-client-btn').length) {
                        e.preventDefault();
                        const client = $(this).data('client');
                        modalSelectClient(client);
                    }
                });
            },
            error: function() {
                $('#modalClientSearchResults').hide();
                $('#modalNoClientResults').show();
            }
        });
    }
    
    // Modal search input handlers
    $('#modalClientSearchInput').on('input keyup', function(e) {
        const query = $(this).val().trim();
        clearTimeout(modalClientSearchTimeout);
        
        if (query.length < 2) {
            $('#modalClientSearchResults').hide();
            $('#modalNoClientResults').hide();
            $('#modalBtnClearClientSearch').hide();
            return;
        }
        
        $('#modalBtnClearClientSearch').show();
        modalClientSearchTimeout = setTimeout(function() {
            modalSearchClients(query);
        }, 300);
    });
    
    $('#modalBtnSearchClient').on('click', function() {
        const query = $('#modalClientSearchInput').val().trim();
        if (query.length >= 2) {
            modalSearchClients(query);
        }
    });
    
    $('#modalBtnClearClientSearch').on('click', function() {
        $('#modalClientSearchInput').val('');
        $('#modalClientSearchResults').hide();
        $('#modalNoClientResults').hide();
        $(this).hide();
    });
    
    // Hide inline results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#modalClientSearchInline, #modalInlineClientResults').length) {
            $('#modalInlineClientResults').hide();
        }
    });
    
    // Reset modal client selection when modal closes - MODAL REMOVED
    /*
    $('#createBookingModal').on('hidden.bs.modal', function() {
        modalSelectedClient = null;
        $('#modal_clientid').val('');
        $('#modalSelectedClientInfo').hide();
        $('#modalClientSearchContainer').show();
        $('#modalClientSearchInline').val('');
        $('#modalInlineClientResults').hide();
    });
    */
});
</script>
@endpush

