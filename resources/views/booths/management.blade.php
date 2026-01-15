@extends('layouts.adminlte')

@section('title', 'Booth Management')
@section('page-title', 'Booth Management')
@section('breadcrumb', 'Booths / Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/modern-design-system.css') }}">
<style>
    /* Modern Booth Management Design - All Devices */
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif !important;
    }
    
    @media (min-width: 769px) {
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%) !important;
        }
        
        .container-fluid {
            max-width: 1400px !important;
            margin: 0 auto !important;
            padding: 24px !important;
        }
    }
    
    @media (max-width: 768px) {
        body {
            background: #f5f7fa !important;
            padding-bottom: 90px !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        
        /* Mobile App Header */
        .mobile-app-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            padding: 16px 20px !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 1000 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        }
        
        .mobile-app-header-content {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            color: white !important;
        }
        
        .mobile-app-title {
            font-size: 24px !important;
            font-weight: 800 !important;
            color: white !important;
            margin: 0 !important;
            letter-spacing: -0.5px !important;
        }
        
        .mobile-app-subtitle {
            font-size: 13px !important;
            color: rgba(255, 255, 255, 0.9) !important;
            margin-top: 4px !important;
        }
        
        .mobile-header-actions {
            display: flex !important;
            gap: 12px !important;
            align-items: center !important;
        }
        
        .mobile-header-btn {
            width: 44px !important;
            height: 44px !important;
            border-radius: 12px !important;
            background: rgba(255, 255, 255, 0.2) !important;
            border: none !important;
            color: white !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            transition: all 0.2s ease !important;
        }
        
        .mobile-header-btn:active {
            background: rgba(255, 255, 255, 0.3) !important;
            transform: scale(0.95) !important;
        }
        
        /* Hide desktop header on mobile */
        .modern-page-header {
            display: none !important;
        }
    }
    
    @media (min-width: 769px) and (max-width: 1024px) {
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%) !important;
        }
        
        .container-fluid {
            padding: 20px !important;
        }
    }
    
    /* Modern Page Header */
    .modern-page-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;
        border-radius: 24px !important;
        padding: 32px !important;
        margin-bottom: 32px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        color: white !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    .modern-page-header::before {
        content: '' !important;
        position: absolute !important;
        top: -50% !important;
        right: -20% !important;
        width: 500px !important;
        height: 500px !important;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%) !important;
        border-radius: 50% !important;
    }
    
    .modern-page-header h2 {
        font-size: 36px !important;
        font-weight: 800 !important;
        margin: 0 !important;
        color: white !important;
        position: relative !important;
        z-index: 1 !important;
    }
    
    .modern-page-header .btn {
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: white !important;
        border-radius: 12px !important;
        padding: 10px 20px !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
    }
    
    .modern-page-header .btn:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        transform: translateY(-2px) !important;
    }
    
    .booth-image-preview {
        width: 60px !important;
        height: 60px !important;
        object-fit: cover !important;
        border-radius: 12px !important;
        border: 2px solid #e5e7eb !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
    }
    
    .booth-image-preview:hover {
        transform: scale(1.1) !important;
        border-color: #6366f1 !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
    }
    
    /* Modern Stat Cards - Matching Dashboard */
    .modern-stat-card {
        background: white !important;
        border-radius: 24px !important;
        padding: 24px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
        position: relative !important;
        overflow: hidden !important;
        transition: all 0.3s ease !important;
        text-align: center !important;
    }
    
    .modern-stat-card::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 4px !important;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;
    }
    
    .modern-stat-card:hover {
        transform: translateY(-8px) scale(1.02) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        border-color: #818cf8 !important;
    }
    
    .modern-stat-card.success::before {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }
    
    .modern-stat-card.warning::before {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }
    
    .modern-stat-card.info::before {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
    }
    
    .modern-stat-card.danger::before {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    }
    
    .modern-stat-value {
        font-size: 36px !important;
        font-weight: 800 !important;
        color: #111827 !important;
        margin: 12px 0 !important;
        line-height: 1 !important;
    }
    
    .modern-stat-label {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #4b5563 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
    }
    
    /* Legacy stat-card support */
    .stat-card {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        color: white !important;
        border-radius: 24px !important;
        padding: 24px !important;
        text-align: center !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
    }
    
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
    }
    
    .stat-card.success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }
    
    .stat-card.info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
    }
    
    .stat-card.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    }
    
    .stat-card > div:first-child {
        font-size: 36px !important;
        font-weight: 800 !important;
    }
    
    .stat-card > div:last-child {
        font-size: 14px !important;
        font-weight: 600 !important;
        opacity: 0.95 !important;
    }
    
    /* Modern Quick Filters */
    .quick-filter-btn {
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
        border-radius: 12px !important;
        margin-bottom: 8px !important;
        white-space: nowrap !important;
        padding: 10px 20px !important;
        border: 2px solid #e5e7eb !important;
        background: white !important;
    }
    
    .quick-filter-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        border-color: #6366f1 !important;
    }
    
    .quick-filter-btn.active {
        font-weight: 700 !important;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        color: white !important;
        border-color: #6366f1 !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
    }
    
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        /* Quick Filters - Scrollable horizontal on mobile */
        .btn-group {
            display: flex !important;
            flex-direction: row !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            gap: 8px !important;
            padding: 0 16px !important;
            margin-bottom: 16px !important;
        }
        
        .quick-filter-btn {
            min-width: 120px !important;
            margin-bottom: 0 !important;
            font-size: 13px !important;
            padding: 10px 16px !important;
            white-space: nowrap !important;
        }
        
        /* Filter Bar - Stack inputs on mobile */
        .filter-bar {
            margin: 0 16px 16px !important;
            padding: 16px !important;
            border-radius: 20px !important;
        }
        
        .filter-bar .row {
            flex-direction: column !important;
        }
        
        .filter-bar .col-md-3,
        .filter-bar .col-md-2,
        .filter-bar .col-md-1 {
            width: 100% !important;
            margin-bottom: 12px !important;
        }
        
        /* Table - Make scrollable on mobile */
        .table-modern {
            border-radius: 20px !important;
            margin: 0 16px !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        
        .table-modern table {
            min-width: 800px !important;
            font-size: 13px !important;
        }
        
        .table-modern thead th {
            padding: 12px 8px !important;
            font-size: 11px !important;
        }
        
        .table-modern tbody td {
            padding: 12px 8px !important;
        }
        
        /* Action Buttons - Compact on mobile */
        .action-buttons {
            gap: 4px !important;
        }
        
        .action-buttons .btn {
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            font-size: 12px !important;
        }
        
        /* Actions Bar - Stack on mobile */
        .card-body .d-flex {
            flex-direction: column !important;
            gap: 12px !important;
        }
        
        .card-body .btn {
            width: 100% !important;
            margin: 0 !important;
        }
        
        /* Modal - Full screen on mobile */
        .modal-dialog {
            margin: 0 !important;
            max-width: 100% !important;
            height: 100vh !important;
        }
        
        .modal-content {
            height: 100vh !important;
            border-radius: 0 !important;
        }
        
        /* Mobile App Stats Cards */
        .mobile-stats-container {
            padding: 16px 20px !important;
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
            background: white !important;
            margin-bottom: 12px !important;
        }
        
        .mobile-stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 16px !important;
            padding: 16px !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2) !important;
            position: relative !important;
            overflow: hidden !important;
        }
        
        .mobile-stat-card:nth-child(1) {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }
        
        .mobile-stat-card:nth-child(2) {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        }
        
        .mobile-stat-card:nth-child(3) {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        }
        
        .mobile-stat-card:nth-child(4) {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%) !important;
        }
        
        .mobile-stat-label {
            font-size: 11px !important;
            opacity: 0.9 !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 8px !important;
        }
        
        .mobile-stat-value {
            font-size: 28px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
        }
        
        /* Mobile Search Bar */
        .mobile-search-container {
            padding: 0 20px 16px !important;
            background: white !important;
        }
        
        .mobile-search-bar {
            position: relative !important;
        }
        
        .mobile-search-input {
            width: 100% !important;
            padding: 14px 16px 14px 48px !important;
            border: 2px solid #e5e7eb !important;
            border-radius: 16px !important;
            font-size: 16px !important;
            background: #f9fafb !important;
            transition: all 0.2s ease !important;
        }
        
        .mobile-search-input:focus {
            outline: none !important;
            border-color: #667eea !important;
            background: white !important;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
        }
        
        .mobile-search-icon {
            position: absolute !important;
            left: 16px !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            color: #9ca3af !important;
            font-size: 18px !important;
        }
        
        /* Mobile Quick Filters */
        .mobile-filters-container {
            padding: 0 20px 16px !important;
            background: white !important;
        }
        
        .mobile-filters-scroll {
            display: flex !important;
            gap: 10px !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            padding-bottom: 4px !important;
        }
        
        .mobile-filter-chip {
            padding: 10px 18px !important;
            border-radius: 20px !important;
            background: #f3f4f6 !important;
            color: #374151 !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            border: 2px solid transparent !important;
            transition: all 0.2s ease !important;
            min-height: 40px !important;
            display: flex !important;
            align-items: center !important;
        }
        
        .mobile-filter-chip.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
        }
        
        /* Mobile App Booth Cards */
        .mobile-booth-card {
            background: white !important;
            border-radius: 20px !important;
            margin: 0 20px 16px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
            overflow: hidden !important;
            transition: all 0.3s ease !important;
            border: 1px solid #f3f4f6 !important;
        }
        
        .mobile-booth-card:active {
            transform: scale(0.98) !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
        }
        
        .mobile-booth-card-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
            padding: 20px 20px 16px !important;
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%) !important;
        }
        
        .mobile-booth-number-section {
            flex: 1 !important;
        }
        
        .mobile-booth-number {
            font-size: 32px !important;
            font-weight: 900 !important;
            color: #111827 !important;
            line-height: 1 !important;
            margin-bottom: 4px !important;
            letter-spacing: -1px !important;
        }
        
        .mobile-booth-type {
            font-size: 13px !important;
            color: #6b7280 !important;
            font-weight: 600 !important;
            margin-top: 4px !important;
        }
        
        .mobile-booth-status-badge {
            padding: 8px 14px !important;
            border-radius: 12px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }
        
        .mobile-booth-image-container {
            width: 100% !important;
            height: 200px !important;
            overflow: hidden !important;
            background: #f3f4f6 !important;
            position: relative !important;
        }
        
        .mobile-booth-image {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }
        
        .mobile-booth-info {
            padding: 16px 20px !important;
        }
        
        .mobile-info-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 12px 0 !important;
            border-bottom: 1px solid #f3f4f6 !important;
        }
        
        .mobile-info-row:last-child {
            border-bottom: none !important;
        }
        
        .mobile-info-label {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            color: #6b7280 !important;
            font-weight: 500 !important;
            font-size: 14px !important;
        }
        
        .mobile-info-label i {
            width: 20px !important;
            text-align: center !important;
            color: #9ca3af !important;
        }
        
        .mobile-info-value {
            color: #111827 !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            text-align: right !important;
        }
        
        .mobile-info-value.price {
            color: #10b981 !important;
            font-size: 18px !important;
        }
        
        .mobile-booth-actions {
            display: flex !important;
            gap: 8px !important;
            padding: 16px 20px !important;
            background: #f9fafb !important;
            border-top: 1px solid #f3f4f6 !important;
        }
        
        .mobile-booth-actions .btn {
            flex: 1 !important;
            min-height: 48px !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            border: none !important;
            transition: all 0.2s ease !important;
        }
        
        .mobile-booth-actions .btn:active {
            transform: scale(0.95) !important;
        }
        
        .mobile-booth-actions .btn-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
            color: white !important;
        }
        
        .mobile-booth-actions .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }
        
        .mobile-booth-actions .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: white !important;
        }
        
        /* Empty State */
        .mobile-empty-state {
            text-align: center !important;
            padding: 60px 20px !important;
            background: white !important;
            margin: 20px !important;
            border-radius: 20px !important;
        }
        
        .mobile-empty-state i {
            font-size: 64px !important;
            color: #d1d5db !important;
            margin-bottom: 16px !important;
        }
        
        .mobile-empty-state p {
            color: #6b7280 !important;
            font-size: 16px !important;
            font-weight: 600 !important;
        }
        
        /* Mobile Pagination */
        .mobile-pagination {
            padding: 20px !important;
            text-align: center !important;
            background: white !important;
        }
        
        .mobile-pagination .pagination {
            justify-content: center !important;
        }
        
        .mobile-pagination .page-link {
            min-width: 44px !important;
            min-height: 44px !important;
            border-radius: 12px !important;
            margin: 0 4px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    }
    
    /* Tablet Specific */
    @media (min-width: 769px) and (max-width: 1024px) {
        .modern-stat-card {
            padding: 20px !important;
        }
        
        .modern-stat-value {
            font-size: 28px !important;
        }
        
        .quick-filter-btn {
            font-size: 13px !important;
            padding: 10px 16px !important;
        }
        
        .table-modern thead th {
            font-size: 11px !important;
            padding: 16px 12px !important;
        }
        
        .table-modern tbody td {
            font-size: 13px !important;
            padding: 14px 12px !important;
        }
    }
    
    /* Touch-friendly improvements */
    @media (hover: none) and (pointer: coarse) {
        /* Increase touch targets */
        .btn, .quick-filter-btn, .action-buttons .btn {
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        /* Make checkboxes larger */
        input[type="checkbox"] {
            width: 20px !important;
            height: 20px !important;
        }
        
        /* Ensure buttons are touch-friendly */
        .btn-action {
            min-width: 44px !important;
            min-height: 44px !important;
        }
    }
    
    /* Mobile Card View (Alternative to table) */
    @media (max-width: 768px) {
        .mobile-booth-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        
        .mobile-booth-card.available {
            border-left-color: #28a745;
        }
        
        .mobile-booth-card.paid {
            border-left-color: #17a673;
        }
        
        .mobile-booth-card.reserved {
            border-left-color: #f6c23e;
        }
        
        .mobile-booth-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .mobile-booth-number {
            font-size: 1.3rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .mobile-booth-info {
            display: grid;
            grid-template-columns: 1fr;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .mobile-info-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }
        
        .mobile-info-label {
            color: #6c757d;
            font-weight: 600;
        }
        
        .mobile-info-value {
            color: #333;
            font-weight: 500;
        }
        
        .mobile-booth-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .mobile-booth-actions .btn {
            flex: 1;
            min-width: calc(50% - 4px);
            font-size: 0.85rem;
            padding: 8px 12px;
        }
        
        /* Hide desktop table on mobile */
        .table-responsive.d-none-mobile {
            display: none;
        }
        
        /* Show mobile cards on mobile */
        .mobile-view {
            display: block;
        }
    }
    
    /* Show table on desktop */
    @media (min-width: 769px) {
        .mobile-view {
            display: none;
        }
        
        .table-responsive.d-none-mobile {
            display: block;
        }
    }
    
    /* Improve form inputs on mobile */
    @media (max-width: 768px) {
        .form-control, .form-select, select.form-control {
            font-size: 16px; /* Prevents iOS zoom */
            padding: 12px;
            height: auto;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        
        /* Better spacing */
        .card-body {
            padding: 15px;
        }
        
        .card-header {
            padding: 12px 15px;
        }
        
        /* Improve pagination */
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .pagination .page-link {
            padding: 8px 12px;
            min-width: 40px;
        }
    }
    
    /* Modern Filter Bar */
    .filter-bar {
        background: white !important;
        padding: 24px !important;
        border-radius: 24px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
    }
    
    .filter-bar .form-label {
        font-weight: 600 !important;
        color: #111827 !important;
        margin-bottom: 8px !important;
        font-size: 14px !important;
    }
    
    .filter-bar .form-control,
    .filter-bar .form-select {
        border-radius: 12px !important;
        border: 2px solid #e5e7eb !important;
        padding: 12px 16px !important;
        font-size: 14px !important;
        transition: all 0.2s ease !important;
    }
    
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
    }
    
    .filter-bar .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        border: none !important;
        border-radius: 12px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
    }
    
    .filter-bar .btn-primary:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
    }
    
    /* Modern Table */
    .table-modern {
        background: white !important;
        border-radius: 24px !important;
        overflow: hidden !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        color: white !important;
    }
    
    .table-modern thead th {
        border: none !important;
        padding: 20px 16px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 12px !important;
        letter-spacing: 0.1em !important;
    }
    
    .table-modern tbody tr {
        transition: all 0.2s ease !important;
        border-bottom: 1px solid #f3f4f6 !important;
    }
    
    .table-modern tbody tr:hover {
        background: #f8f9fc !important;
        transform: translateX(4px) !important;
    }
    
    .table-modern tbody tr:last-child {
        border-bottom: none !important;
    }
    
    .table-modern tbody td {
        padding: 16px !important;
        vertical-align: middle !important;
        color: #374151 !important;
    }
    
    /* Modern Action Buttons */
    .action-buttons {
        display: flex !important;
        gap: 8px !important;
    }
    
    .btn-action {
        width: 40px !important;
        height: 40px !important;
        padding: 0 !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
        font-size: 14px !important;
    }
    
    .btn-action:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
    
    /* Modern Badges */
    .badge {
        padding: 6px 12px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        font-size: 12px !important;
    }
    
    .badge-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: white !important;
    }
    
    .badge-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: white !important;
    }
    
    .badge-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
        color: white !important;
    }
    
    .badge-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: white !important;
    }
    
    .badge-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        color: white !important;
    }
    
    /* Modal Form Styles */
    #boothModal .nav-pills .nav-link {
        border-radius: 8px 8px 0 0;
        padding: 12px 20px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s;
        border: none;
        background: transparent;
    }
    
    #boothModal .nav-pills .nav-link:hover {
        color: #667eea;
        background: rgba(102, 126, 234, 0.1);
    }
    
    #boothModal .nav-pills .nav-link.active {
        color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-bottom: 3px solid #667eea;
    }
    
    #boothModal .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    #boothModal .form-label {
        display: flex;
        align-items: center;
    }
    
    #boothModal .tab-content {
        padding-top: 20px;
    }
    
    #boothModal .image-upload-area:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    }
    
    @media (max-width: 768px) {
        #boothModal .nav-pills {
            flex-direction: column;
        }
        
        #boothModal .nav-pills .nav-link {
            border-radius: 8px;
            margin-bottom: 5px;
        }
    }
    
    /* Modern Pagination Footer */
    .card-footer-modern {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-top: 2px solid #e9ecef;
        padding: 20px 30px;
        border-radius: 0 0 12px 12px;
    }
    
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .pagination-info {
        display: flex;
        align-items: center;
    }
    
    .pagination-text {
        color: #495057;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }
    
    .pagination-text strong {
        color: #667eea;
        font-weight: 700;
        margin: 0 4px;
    }
    
    .pagination-controls {
        display: flex;
        align-items: center;
    }
    
    /* Custom Pagination Styles */
    .pagination {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .pagination .page-item {
        margin: 0;
    }
    
    .pagination .page-link {
        color: #667eea;
        background: white;
        border: 2px solid #e9ecef;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        min-width: 44px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .pagination .page-link:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .pagination .page-item.disabled .page-link:hover {
        transform: none;
        box-shadow: none;
        background: #f8f9fa;
        color: #adb5bd;
    }
    
    .pagination .page-link i {
        font-size: 0.85rem;
    }
    
    @media (max-width: 768px) {
        .pagination-wrapper {
            flex-direction: column;
            align-items: stretch;
        }
        
        .pagination-info {
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .pagination-controls {
            justify-content: center;
        }
        
        .pagination {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .pagination .page-link {
            padding: 8px 12px;
            min-width: 40px;
            font-size: 0.9rem;
        }
    }
    
    .btn-action {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    
    /* Modern Modal Styles */
    .modal-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        color: white !important;
        border-radius: 20px 20px 0 0 !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 10 !important;
        padding: 24px 30px !important;
    }
    
    #boothModal .modal-dialog {
        max-width: 90% !important;
        margin: 1.75rem auto !important;
    }
    
    #boothModal .modal-content {
        max-height: 90vh !important;
        display: flex !important;
        flex-direction: column !important;
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
    }
    
    #boothModal .modal-body {
        overflow-y: auto !important;
        overflow-x: hidden !important;
        flex: 1 1 auto !important;
        padding: 30px !important;
    }
    
    #boothModal .modal-footer {
        position: sticky !important;
        bottom: 0 !important;
        background: white !important;
        border-top: 2px solid #f3f4f6 !important;
        z-index: 10 !important;
        padding: 20px 30px !important;
        border-radius: 0 0 20px 20px !important;
    }
    
    #boothModal .modal-footer .btn {
        border-radius: 12px !important;
        padding: 10px 24px !important;
        font-weight: 600 !important;
    }
    
    #boothModal .modal-footer .btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
    }
    
    #boothModal .modal-footer .btn-primary:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4) !important;
    }
    
    #boothModal .form-group {
        margin-bottom: 1.25rem;
    }
    
    #boothModal .form-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    #boothModal .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 0.625rem 0.75rem;
        transition: all 0.2s;
    }
    
    #boothModal .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    #boothModal h6 {
        color: #667eea;
        font-weight: 700;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    .image-upload-area {
        border: 2px dashed #667eea;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        background: #f8f9fc;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .image-upload-area:hover {
        background: #e9ecef;
        border-color: #764ba2;
    }
    
    .image-upload-area.dragover {
        background: #e3f2fd;
        border-color: #2196f3;
    }
    
    .image-preview-container {
        position: relative;
        display: inline-block;
        margin-top: 16px;
    }
    
    .image-preview-container img {
        max-width: 300px;
        max-height: 300px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .remove-image-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e74a3b;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    /* Status Settings Modal Styles */
    #statusSettingsModal .modal-dialog {
        max-width: 95%;
    }
    
    #statusSettingsModal .form-control-color {
        height: 38px;
        width: 60px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        cursor: pointer;
        padding: 2px;
    }
    
    #statusSettingsModal .form-control-color:hover {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    #statusSettingsTable tbody tr {
        cursor: move;
        transition: background-color 0.2s;
    }
    
    #statusSettingsTable tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    #statusSettingsTable .status-bg-color,
    #statusSettingsTable .status-border-color,
    #statusSettingsTable .status-text-color {
        cursor: pointer;
    }
    
    .status-color-preview {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: 2px solid #dee2e6;
        margin-right: 8px;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Modern Page Header -->
    <div class="modern-page-header d-none d-md-block">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2><i class="fas fa-store me-2"></i>Booth Management</h2>
                <p style="margin: 8px 0 0 0; opacity: 0.95; font-size: 18px;">Manage and organize all your booth listings</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn" onclick="openStatusSettingsModal()">
                    <i class="fas fa-tags me-2"></i>Status Settings
                </button>
                <a href="{{ url('/booths?view=canvas') }}" class="btn">
                    <i class="fas fa-map me-2"></i>Canvas View
                </a>
            </div>
        </div>
    </div>
    
    <!-- Mobile App Header -->
    <div class="mobile-app-header d-md-none">
        <div class="mobile-app-header-content">
            <div>
                <h1 class="mobile-app-title">Booth Management</h1>
                <div class="mobile-app-subtitle">Manage all your booths</div>
            </div>
            <div class="mobile-header-actions">
                <button type="button" class="mobile-header-btn" onclick="openStatusSettingsModal()" title="Status Settings">
                    <i class="fas fa-tags"></i>
                </button>
                <a href="{{ url('/booths?view=canvas') }}" class="mobile-header-btn" title="Canvas View">
                    <i class="fas fa-map"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Modern Statistics Cards - Desktop -->
    <div class="row mb-4 d-none d-md-flex">
        <div class="col-md-2">
            <div class="modern-stat-card">
                <div class="modern-stat-value">{{ number_format($stats['total']) }}</div>
                <div class="modern-stat-label">Total Booths</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="modern-stat-card success">
                <div class="modern-stat-value">{{ number_format($stats['available']) }}</div>
                <div class="modern-stat-label">Available</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="modern-stat-card warning">
                <div class="modern-stat-value">{{ number_format($stats['reserved']) }}</div>
                <div class="modern-stat-label">Reserved</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="modern-stat-card info">
                <div class="modern-stat-value">{{ number_format($stats['confirmed']) }}</div>
                <div class="modern-stat-label">Confirmed</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="modern-stat-card danger">
                <div class="modern-stat-value">{{ number_format($stats['paid']) }}</div>
                <div class="modern-stat-label">Paid</div>
            </div>
        </div>
        <div class="col-md-2">
            <a href="{{ url('/booths?view=canvas') }}" class="modern-stat-card" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 140px;">
                <i class="fas fa-map" style="font-size: 32px; color: #6366f1; margin-bottom: 12px;"></i>
                <div class="modern-stat-label">Canvas View</div>
            </a>
        </div>
    </div>
    
    <!-- Mobile App Stats Cards -->
    <div class="mobile-stats-container d-md-none">
        <div class="mobile-stat-card">
            <div class="mobile-stat-label">Total Booths</div>
            <div class="mobile-stat-value">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="mobile-stat-card">
            <div class="mobile-stat-label">Available</div>
            <div class="mobile-stat-value">{{ number_format($stats['available']) }}</div>
        </div>
        <div class="mobile-stat-card">
            <div class="mobile-stat-label">Reserved</div>
            <div class="mobile-stat-value">{{ number_format($stats['reserved']) }}</div>
        </div>
        <div class="mobile-stat-card">
            <div class="mobile-stat-label">Paid</div>
            <div class="mobile-stat-value">{{ number_format($stats['paid']) }}</div>
        </div>
    </div>

    <!-- Mobile Search Bar -->
    <div class="mobile-search-container d-md-none">
        <form method="GET" action="{{ route('booths.index', ['view' => 'table']) }}" id="mobileSearchForm">
            <div class="mobile-search-bar">
                <i class="fas fa-search mobile-search-icon"></i>
                <input type="text" name="search" class="mobile-search-input" placeholder="Search booths..." value="{{ request('search') }}" autocomplete="off">
            </div>
        </form>
    </div>

    <!-- Mobile Quick Filters -->
    <div class="mobile-filters-container d-md-none">
        <div class="mobile-filters-scroll">
            <button type="button" class="mobile-filter-chip active" onclick="applyQuickFilter('all')">
                All
            </button>
            <button type="button" class="mobile-filter-chip" onclick="applyQuickFilter('available')">
                Available
            </button>
            <button type="button" class="mobile-filter-chip" onclick="applyQuickFilter('reserved')">
                Reserved
            </button>
            <button type="button" class="mobile-filter-chip" onclick="applyQuickFilter('paid')">
                Paid
            </button>
            <button type="button" class="mobile-filter-chip" onclick="applyQuickFilter('confirmed')">
                Confirmed
            </button>
        </div>
    </div>

    <!-- Modern Quick Filters - Desktop -->
    <div class="mb-4 d-none d-md-block" style="padding: 0 16px;">
        <div class="btn-group" role="group" aria-label="Quick Filters" style="display: flex; gap: 8px; flex-wrap: wrap;">
            <button type="button" class="btn btn-outline-primary quick-filter-btn" onclick="applyQuickFilter('all')">
                <i class="fas fa-list"></i> All Booths
            </button>
            <button type="button" class="btn btn-outline-success quick-filter-btn" onclick="applyQuickFilter('available')">
                <i class="fas fa-check-circle"></i> Available
            </button>
            <button type="button" class="btn btn-outline-info quick-filter-btn" onclick="applyQuickFilter('booked')">
                <i class="fas fa-bookmark"></i> Booked
            </button>
            <button type="button" class="btn btn-outline-warning quick-filter-btn" onclick="applyQuickFilter('paid')">
                <i class="fas fa-dollar-sign"></i> Paid
            </button>
            <button type="button" class="btn btn-outline-secondary quick-filter-btn" onclick="applyQuickFilter('today')">
                <i class="fas fa-calendar-day"></i> Today
            </button>
            <button type="button" class="btn btn-outline-danger quick-filter-btn" onclick="applyQuickFilter('overdue')">
                <i class="fas fa-exclamation-triangle"></i> Overdue
            </button>
            <button type="button" class="btn btn-link" onclick="clearAllFilters()" style="color: #6366f1; text-decoration: none; font-weight: 600;">
                <i class="fas fa-times-circle"></i> Clear
            </button>
        </div>
    </div>

    <!-- Filter Bar - Desktop -->
    <div class="filter-bar d-none d-md-block">
        <form method="GET" action="{{ route('booths.index', ['view' => 'table']) }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-search mr-1"></i>Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Booth number, company, category..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-map mr-1"></i>Floor Plan</label>
                    <select name="floor_plan_id" class="form-control">
                        <option value="">All Floor Plans</option>
                        @foreach($floorPlans as $fp)
                            <option value="{{ $fp->id }}" {{ request('floor_plan_id') == $fp->id ? 'selected' : '' }}>
                                {{ $fp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-tag mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Available</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Confirmed</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Reserved</option>
                        <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Hidden</option>
                        <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-building mr-1"></i>Booth Type</label>
                    <select name="booth_type_id" class="form-control">
                        <option value="">All Types</option>
                        @foreach($boothTypes as $type)
                            <option value="{{ $type->id }}" {{ request('booth_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-folder mr-1"></i>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Modern Actions Bar -->
    <div class="card mb-3" style="background: white; border-radius: 24px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
        <div class="card-body" style="padding: 20px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-success" onclick="openCreateModal()" style="border-radius: 12px; padding: 10px 20px; font-weight: 600;">
                        <i class="fas fa-plus mr-1"></i>Create New Booth
                    </button>
                    <button type="button" class="btn btn-warning" onclick="bulkUpdateStatus()" style="border-radius: 12px; padding: 10px 20px; font-weight: 600;">
                        <i class="fas fa-edit mr-1"></i>Bulk Update
                    </button>
                    <button type="button" class="btn btn-danger" onclick="bulkDelete()" style="border-radius: 12px; padding: 10px 20px; font-weight: 600;">
                        <i class="fas fa-trash mr-1"></i>Bulk Delete
                    </button>
                </div>
                <div>
                    <a href="{{ route('booths.index', ['view' => 'table', 'export' => 'csv']) }}" class="btn btn-info" style="border-radius: 12px; padding: 10px 20px; font-weight: 600;">
                        <i class="fas fa-download mr-1"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Booths Table - Desktop View -->
    <div class="card table-modern d-none d-md-block">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="boothsTable">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Image</th>
                        <th>Booth #</th>
                        <th>Type</th>
                        <th>Floor Plan</th>
                        <th>Company</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Area</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($booths as $booth)
                    <tr>
                        <td>
                            <input type="checkbox" class="booth-checkbox" value="{{ $booth->id }}">
                        </td>
                        <td>
                            @if($booth->booth_image)
                                <img src="{{ asset($booth->booth_image) }}" alt="Booth Image" class="booth-image-preview" onclick="viewImage('{{ asset($booth->booth_image) }}')">
                            @else
                                <div style="width: 60px; height: 60px; background: #f3f4f6; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="font-size: 16px; font-weight: 700; color: #111827;">{{ $booth->booth_number }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                {{ $booth->boothType ? $booth->boothType->name : ($booth->type == 1 ? 'Booth' : 'Space Only') }}
                            </span>
                        </td>
                        <td>
                            <span style="color: #4b5563; font-weight: 500;">{{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}</span>
                        </td>
                        <td>
                            <span style="color: #374151; font-weight: 500;">{{ $booth->client ? $booth->client->company : 'N/A' }}</span>
                        </td>
                        <td>
                            <span style="color: #4b5563; font-weight: 500;">{{ $booth->category ? $booth->category->name : 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $booth->getStatusColor() }}">
                                {{ $booth->getStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            <strong style="color: #10b981; font-size: 15px; font-weight: 700;">${{ number_format($booth->price, 2) }}</strong>
                        </td>
                        <td>
                            <span style="color: #6b7280;">{{ $booth->area_sqm ? number_format($booth->area_sqm, 2) . ' m²' : 'N/A' }}</span>
                        </td>
                        <td>
                            <span style="color: #6b7280;">{{ $booth->capacity ? $booth->capacity . ' people' : 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-info btn-action" onclick="viewBooth({{ $booth->id }})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editBooth({{ $booth->id }})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteBooth({{ $booth->id }})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No booths found. Create your first booth!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($booths->hasPages())
        <div class="card-footer-modern">
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    <span class="pagination-text">
                        <i class="fas fa-list mr-2"></i>
                        Showing <strong>{{ $booths->firstItem() }}</strong> to <strong>{{ $booths->lastItem() }}</strong> of <strong>{{ $booths->total() }}</strong> booths
                    </span>
                </div>
                <div class="pagination-controls">
                    {{ $booths->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Mobile App Booth Cards -->
    <div class="d-md-none" style="padding-bottom: 20px;">
        @forelse($booths as $booth)
        <div class="mobile-booth-card" onclick="viewBooth({{ $booth->id }})" style="cursor: pointer;">
            <div class="mobile-booth-card-header">
                <div class="mobile-booth-number-section">
                    <div class="mobile-booth-number">#{{ $booth->booth_number }}</div>
                    <div class="mobile-booth-type">{{ $booth->boothType ? $booth->boothType->name : ($booth->type == 1 ? 'Booth' : 'Space Only') }}</div>
                </div>
                <span class="badge mobile-booth-status-badge badge-{{ $booth->getStatusColor() }}">
                    {{ $booth->getStatusLabel() }}
                </span>
            </div>
            
            @if($booth->booth_image)
            <div class="mobile-booth-image-container">
                <img src="{{ asset($booth->booth_image) }}" alt="Booth Image" class="mobile-booth-image" onclick="event.stopPropagation(); viewImage('{{ asset($booth->booth_image) }}')">
            </div>
            @endif
            
            <div class="mobile-booth-info">
                <div class="mobile-info-row">
                    <span class="mobile-info-label">
                        <i class="fas fa-map"></i>
                        <span>Floor Plan</span>
                    </span>
                    <span class="mobile-info-value">{{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}</span>
                </div>
                <div class="mobile-info-row">
                    <span class="mobile-info-label">
                        <i class="fas fa-building"></i>
                        <span>Company</span>
                    </span>
                    <span class="mobile-info-value">{{ $booth->client ? $booth->client->company : 'N/A' }}</span>
                </div>
                <div class="mobile-info-row">
                    <span class="mobile-info-label">
                        <i class="fas fa-folder"></i>
                        <span>Category</span>
                    </span>
                    <span class="mobile-info-value">{{ $booth->category ? $booth->category->name : 'N/A' }}</span>
                </div>
                <div class="mobile-info-row">
                    <span class="mobile-info-label">
                        <i class="fas fa-dollar-sign"></i>
                        <span>Price</span>
                    </span>
                    <span class="mobile-info-value price">${{ number_format($booth->price, 2) }}</span>
                </div>
                @if($booth->area_sqm)
                <div class="mobile-info-row">
                    <span class="mobile-info-label">
                        <i class="fas fa-ruler-combined"></i>
                        <span>Area</span>
                    </span>
                    <span class="mobile-info-value">{{ number_format($booth->area_sqm, 2) }} m²</span>
                </div>
                @endif
                @if($booth->capacity)
                <div class="mobile-info-row">
                    <span class="mobile-info-label">
                        <i class="fas fa-users"></i>
                        <span>Capacity</span>
                    </span>
                    <span class="mobile-info-value">{{ $booth->capacity }} people</span>
                </div>
                @endif
            </div>
            
            <div class="mobile-booth-actions" onclick="event.stopPropagation();">
                <button type="button" class="btn btn-info" onclick="viewBooth({{ $booth->id }})">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" class="btn btn-primary" onclick="editBooth({{ $booth->id }})">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteBooth({{ $booth->id }})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="mobile-empty-state">
            <i class="fas fa-inbox"></i>
            <p>No booths found</p>
            <p style="font-size: 14px; margin-top: 8px; color: #9ca3af;">Create your first booth to get started</p>
        </div>
        @endforelse
        
        @if($booths->hasPages())
        <div class="mobile-pagination">
            {{ $booths->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Booth Modal -->
<div class="modal fade" id="boothModal" tabindex="-1" role="dialog" aria-labelledby="boothModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0; padding: 20px 30px; border-bottom: none;">
                <h5 class="modal-title text-white" id="modalTitle" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-store mr-2"></i><span id="modalTitleText">Create New Booth</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9; font-size: 1.5rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="boothForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="boothId" name="id">
                <div class="modal-body" style="padding: 30px;">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-pills nav-fill mb-4" id="boothFormTabs" role="tablist" style="border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">
                        <li class="nav-item">
                            <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic-info" role="tab" aria-controls="basic-info" aria-selected="true">
                                <i class="fas fa-info-circle mr-2"></i>Basic Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="details-tab" data-toggle="tab" href="#details-info" role="tab" aria-controls="details-info" aria-selected="false">
                                <i class="fas fa-clipboard-list mr-2"></i>Details & Specifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="content-tab" data-toggle="tab" href="#content-info" role="tab" aria-controls="content-info" aria-selected="false">
                                <i class="fas fa-align-left mr-2"></i>Content & Description
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="media-tab" data-toggle="tab" href="#media-info" role="tab" aria-controls="media-info" aria-selected="false">
                                <i class="fas fa-image mr-2"></i>Media
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="boothFormTabContent">
                        <!-- Basic Information Tab -->
                        <div class="tab-pane fade show active" id="basic-info" role="tabpanel" aria-labelledby="basic-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="booth_number" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-hashtag text-primary mr-2"></i>Booth Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="booth_number" id="booth_number" class="form-control" required 
                                               style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; transition: all 0.3s;"
                                               onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                               onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none'">
                                        <small class="form-text text-muted">Unique identifier for this booth</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="floor_plan_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-map text-primary mr-2"></i>Floor Plan <span class="text-danger">*</span>
                                        </label>
                                        <select name="floor_plan_id" id="floor_plan_id" class="form-control" required
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px; transition: all 0.3s;"
                                                onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                                onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none'">
                                            <option value="">Select Floor Plan</option>
                                            @foreach($floorPlans as $fp)
                                                <option value="{{ $fp->id }}">{{ $fp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="booth_type_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-tags text-primary mr-2"></i>Booth Type
                                        </label>
                                        <select name="booth_type_id" id="booth_type_id" class="form-control"
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Type</option>
                                            @foreach($boothTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="type" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-cube text-primary mr-2"></i>Type <span class="text-danger">*</span>
                                        </label>
                                        <select name="type" id="type" class="form-control" required
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="1">Booth</option>
                                            <option value="2">Space Only</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-dollar-sign text-success mr-2"></i>Price <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" style="border-radius: 8px 0 0 8px; background: #f8f9fa; border: 1px solid #dee2e6;">$</span>
                                            </div>
                                            <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" required
                                                   style="border-radius: 0 8px 8px 0; border: 1px solid #dee2e6; padding: 10px 15px;"
                                                   onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 0.2rem rgba(102, 126, 234, 0.25)'"
                                                   onblur="this.style.borderColor='#dee2e6'; this.style.boxShadow='none'">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="status" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-toggle-on text-primary mr-2"></i>Status <span class="text-danger">*</span>
                                        </label>
                                        <select name="status" id="status" class="form-control" required
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="1">Available</option>
                                            <option value="2">Confirmed</option>
                                            <option value="3">Reserved</option>
                                            <option value="4">Hidden</option>
                                            <option value="5">Paid</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="client_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-user-tie text-primary mr-2"></i>Client
                                        </label>
                                        <select name="client_id" id="client_id" class="form-control"
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->company ?? $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="category_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-folder text-primary mr-2"></i>Category
                                        </label>
                                        <select name="category_id" id="category_id" class="form-control"
                                                style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Details & Specifications Tab -->
                        <div class="tab-pane fade" id="details-info" role="tabpanel" aria-labelledby="details-tab">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="area_sqm" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-ruler-combined text-primary mr-2"></i>Area (m²)
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="area_sqm" id="area_sqm" class="form-control" step="0.01" min="0"
                                                   style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="border-radius: 0 8px 8px 0; background: #f8f9fa; border: 1px solid #dee2e6;">m²</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="capacity" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-users text-primary mr-2"></i>Capacity (people)
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="capacity" id="capacity" class="form-control" min="0"
                                                   style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="border-radius: 0 8px 8px 0; background: #f8f9fa; border: 1px solid #dee2e6;">
                                                    <i class="fas fa-user"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="electricity_power" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-bolt text-warning mr-2"></i>Electricity Power
                                        </label>
                                        <input type="text" name="electricity_power" id="electricity_power" class="form-control" 
                                               placeholder="e.g., 10A, 20A, 30A"
                                               style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                        <small class="form-text text-muted">Specify the electrical power requirements</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content & Description Tab -->
                        <div class="tab-pane fade" id="content-info" role="tabpanel" aria-labelledby="content-tab">
                            <div class="form-group">
                                <label for="description" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                    <i class="fas fa-align-left text-primary mr-2"></i>Description
                                </label>
                                <textarea name="description" id="description" class="form-control" rows="5" 
                                          placeholder="Enter a detailed description of the booth..."
                                          style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px 15px; resize: vertical;"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="features" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                    <i class="fas fa-list-check text-primary mr-2"></i>Features
                                </label>
                                <textarea name="features" id="features" class="form-control" rows="5" 
                                          placeholder="List booth features (one per line)..."
                                          style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px 15px; resize: vertical;"></textarea>
                                <small class="form-text text-muted">Enter each feature on a new line</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                    <i class="fas fa-sticky-note text-primary mr-2"></i>Additional Notes
                                </label>
                                <textarea name="notes" id="notes" class="form-control" rows="4" 
                                          placeholder="Enter any additional notes or special instructions..."
                                          style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px 15px; resize: vertical;"></textarea>
                            </div>
                        </div>

                        <!-- Media Tab -->
                        <div class="tab-pane fade" id="media-info" role="tabpanel" aria-labelledby="media-tab">
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 15px;">
                                    <i class="fas fa-image text-primary mr-2"></i>Booth Image
                                </label>
                                <div class="image-upload-wrapper" style="position: relative;">
                                    <div class="image-upload-area" id="imageUploadArea" 
                                         onclick="document.getElementById('booth_image').click()"
                                         style="border: 2px dashed #667eea; border-radius: 12px; padding: 40px; text-align: center; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); cursor: pointer; transition: all 0.3s;"
                                         onmouseover="this.style.borderColor='#764ba2'; this.style.transform='scale(1.02)'"
                                         onmouseout="this.style.borderColor='#667eea'; this.style.transform='scale(1)'">
                                        <i class="fas fa-cloud-upload-alt fa-4x mb-3" style="color: #667eea;"></i>
                                        <p class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #495057;">Click to upload or drag and drop</p>
                                        <small class="text-muted">PNG, JPG, GIF up to 5MB</small>
                                    </div>
                                    <input type="file" name="booth_image" id="booth_image" class="d-none" accept="image/*" onchange="previewImage(this)">
                                    <div id="imagePreviewContainer" class="image-preview-container" style="display: none; margin-top: 20px; position: relative; text-align: center;">
                                        <div style="position: relative; display: inline-block;">
                                            <img id="imagePreview" src="" alt="Preview" 
                                                 style="max-width: 100%; max-height: 400px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                            <button type="button" class="remove-image-btn" onclick="removeImage()"
                                                    style="position: absolute; top: 10px; right: 10px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 35px; height: 35px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.2); transition: all 0.3s;"
                                                    onmouseover="this.style.transform='scale(1.1)'; this.style.background='#c82333'"
                                                    onmouseout="this.style.transform='scale(1)'; this.style.background='#dc3545'">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 20px 30px; background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" 
                            style="border-radius: 8px; padding: 10px 25px; font-weight: 600;">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px; padding: 10px 30px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                        <i class="fas fa-save mr-2"></i>Save Booth
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal fade" id="imageViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booth Image</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="viewImageSrc" src="" alt="Booth Image" style="max-width: 100%; border-radius: 12px;">
            </div>
        </div>
    </div>
</div>

<!-- Status Settings Modal -->
<div class="modal fade" id="statusSettingsModal" tabindex="-1" role="dialog" aria-labelledby="statusSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="statusSettingsModalLabel">
                    <i class="fas fa-tags me-2"></i>Booth Status Settings
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Customize booking status names, colors, and assign them to specific floor plans.</strong>
                    <br>Statuses assigned to a floor plan will only be available for that floor plan. Global statuses (no floor plan assigned) are available for all floor plans.
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Status Configuration</h6>
                        <small class="text-muted">Drag rows to reorder, or use the Order field</small>
                    </div>
                    <button type="button" class="btn btn-primary" id="btnAddStatus">
                        <i class="fas fa-plus me-2"></i>Add New Status
                    </button>
                </div>

                <div id="statusSettingsContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading status settings...</p>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-success" id="btnSaveStatusSettings">
                        <i class="fas fa-save me-2"></i>Save All Status Settings
                    </button>
                    <button type="button" class="btn btn-secondary ms-2" id="btnResetStatusSettings">
                        <i class="fas fa-undo me-2"></i>Reset to Defaults
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('vendor/jquery-ui/css/jquery-ui.min.css') }}">
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
let currentBoothId = null;

// Initialize DataTable
$(document).ready(function() {
    $('#boothsTable').DataTable({
        pageLength: 50,
        order: [[2, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 1, 11] }
        ]
    });
    
    // Auto-scroll modal body to top and reset to first tab when modal is shown
    $('#boothModal').on('shown.bs.modal', function() {
        $(this).find('.modal-body').scrollTop(0);
        // Reset to first tab
        $('#basic-tab').tab('show');
    });
    
    // Auto-open edit modal if edit parameter is in URL
    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('edit');
    if (editId) {
        // Remove edit parameter from URL to clean it up
        urlParams.delete('edit');
        const newSearch = urlParams.toString();
        const newUrl = window.location.pathname + (newSearch ? '?' + newSearch : '');
        window.history.replaceState({}, '', newUrl);
        
        // Open edit modal after a short delay to ensure page is fully loaded
        setTimeout(function() {
            editBooth(editId);
        }, 500);
    }
});

// Open Create Modal
function openCreateModal() {
    currentBoothId = null;
    $('#modalTitleText').text('Create New Booth');
    $('#boothForm')[0].reset();
    $('#boothId').val('');
    $('#imagePreviewContainer').hide();
    // Reset to first tab
    $('#basic-tab').tab('show');
    $('#boothModal').modal('show');
}

// Edit Booth
function editBooth(id) {
    currentBoothId = id;
    $('#modalTitleText').text('Edit Booth');
    
    // Fetch booth data with proper headers to ensure JSON response
    // Use the JSON endpoint to ensure we always get JSON
    fetch(`/booths/${id}?json=1`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
        .then(response => {
            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Expected JSON but got:', text.substring(0, 200));
                    throw new Error('Server returned HTML instead of JSON. Check console for details.');
                });
            }
            
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            // Check if we got an error response
            if (data.error) {
                throw new Error(data.message || data.error);
            }
            
            $('#boothId').val(data.id);
            $('#booth_number').val(data.booth_number);
            $('#floor_plan_id').val(data.floor_plan_id);
            $('#booth_type_id').val(data.booth_type_id);
            $('#type').val(data.type);
            $('#price').val(data.price);
            $('#status').val(data.status);
            $('#client_id').val(data.client_id);
            $('#category_id').val(data.category_id);
            $('#area_sqm').val(data.area_sqm);
            $('#capacity').val(data.capacity);
            $('#electricity_power').val(data.electricity_power);
            $('#description').val(data.description || '');
            $('#features').val(data.features || '');
            $('#notes').val(data.notes || '');
            
            // Show image if exists
            if (data.booth_image) {
                $('#imagePreview').attr('src', data.booth_image);
                $('#imagePreviewContainer').show();
            } else {
                $('#imagePreviewContainer').hide();
            }
            
            // Reset to first tab
            $('#basic-tab').tab('show');
            $('#boothModal').modal('show');
        })
        .catch(error => {
            console.error('Error loading booth:', error);
            Swal.fire({
                icon: 'error',
                title: 'Failed to load booth data',
                text: error.message || 'An unexpected error occurred. Please try again.',
                confirmButtonText: 'OK'
            });
        });
}

// View Booth
function viewBooth(id) {
    window.location.href = `/booths/${id}`;
}

// Delete Booth
function deleteBooth(id) {
    Swal.fire({
        title: 'Delete Booth?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/booths/${id}`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Preview Image
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result);
            $('#imagePreviewContainer').show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove Image
function removeImage() {
    $('#booth_image').val('');
    $('#imagePreviewContainer').hide();
}

// View Image
function viewImage(src) {
    $('#viewImageSrc').attr('src', src);
    $('#imageViewModal').modal('show');
}

// Form Submit
$('#boothForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentBoothId ? `/booths/${currentBoothId}` : '/booths';
    const method = currentBoothId ? 'PUT' : 'POST';
    
    // Add _method for PUT
    if (currentBoothId) {
        formData.append('_method', 'PUT');
    }
    
    showLoading();
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        redirect: 'follow'
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            hideLoading();
            if (data.success || response.ok) {
                Swal.fire('Success', currentBoothId ? 'Booth updated successfully!' : 'Booth created successfully!', 'success')
                    .then(() => {
                        window.location.reload();
                    });
            } else {
                // Handle validation errors
                let errorMsg = data.message || 'An error occurred';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        } else {
            // If HTML response (redirect), reload the page
            hideLoading();
            Swal.fire('Success', currentBoothId ? 'Booth updated successfully!' : 'Booth created successfully!', 'success')
                .then(() => {
                    window.location.reload();
                });
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        Swal.fire('Error', 'An error occurred while saving: ' + error.message, 'error');
    });
});

// Toggle Select All
function toggleSelectAll() {
    const checked = $('#selectAll').is(':checked');
    $('.booth-checkbox').prop('checked', checked);
}

// Bulk Delete
function bulkDelete() {
    const selected = $('.booth-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selected.length === 0) {
        Swal.fire('Warning', 'Please select at least one booth', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Delete Selected Booths?',
        text: `You are about to delete ${selected.length} booth(s). This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch('/bulk/booths/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: selected })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    Swal.fire('Success', `${selected.length} booth(s) deleted successfully!`, 'success')
                        .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error', 'An error occurred', 'error');
            });
        }
    });
}

// Bulk Update Status
function bulkUpdateStatus() {
    const selected = $('.booth-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selected.length === 0) {
        Swal.fire('Warning', 'Please select at least one booth', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Update Status',
        input: 'select',
        inputOptions: {
            '1': 'Available',
            '2': 'Confirmed',
            '3': 'Reserved',
            '4': 'Hidden',
            '5': 'Paid'
        },
        inputPlaceholder: 'Select new status',
        showCancelButton: true,
        confirmButtonText: 'Update',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to select a status!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch('/bulk/booths/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    ids: selected,
                    field: 'status',
                    value: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    Swal.fire('Success', `Status updated for ${selected.length} booth(s)!`, 'success')
                        .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error', 'An error occurred', 'error');
            });
        }
    });
}

// Drag and drop for image
const imageUploadArea = document.getElementById('imageUploadArea');
if (imageUploadArea) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, () => {
            imageUploadArea.classList.add('dragover');
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, () => {
            imageUploadArea.classList.remove('dragover');
        }, false);
    });
    
    imageUploadArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            document.getElementById('booth_image').files = files;
            previewImage(document.getElementById('booth_image'));
        }
    }, false);
}

// Status Settings Modal
function openStatusSettingsModal() {
    loadStatusSettings();
    $('#statusSettingsModal').modal('show');
}

function loadStatusSettings() {
    $.get('{{ route("settings.booth-statuses") }}')
        .done(function(response) {
            if (response.status === 200) {
                renderStatusSettings(response.data);
            }
        })
        .fail(function() {
            $('#statusSettingsContainer').html('<div class="alert alert-danger">Failed to load status settings</div>');
        });
}

function renderStatusSettings(statuses) {
    if (!statuses || statuses.length === 0) {
        $('#statusSettingsContainer').html('<div class="alert alert-info">No status settings found. Click "Add New Status" to create one.</div>');
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-bordered table-hover" id="statusSettingsTable">';
    html += '<thead class="table-light"><tr>';
    html += '<th style="width: 60px;">Order</th>';
    html += '<th style="width: 80px;">Code</th>';
    html += '<th>Status Name</th>';
    html += '<th style="width: 150px;">Background</th>';
    html += '<th style="width: 150px;">Border Color</th>';
    html += '<th style="width: 100px;">Border Width</th>';
    html += '<th style="width: 120px;">Border Style</th>';
    html += '<th style="width: 100px;">Border Radius</th>';
    html += '<th style="width: 150px;">Text</th>';
    html += '<th style="width: 120px;">Badge</th>';
    html += '<th>Description</th>';
    html += '<th style="width: 200px;">Floor Plan</th>';
    html += '<th style="width: 100px;">Default</th>';
    html += '<th style="width: 100px;">Active</th>';
    html += '<th style="width: 120px;">Actions</th>';
    html += '</tr></thead><tbody id="statusSettingsBody">';

    statuses.forEach(function(status, index) {
        html += renderStatusRow(status, index);
    });

    html += '</tbody></table></div>';
    $('#statusSettingsContainer').html(html);
    
    // Make rows sortable
    makeStatusRowsSortable();
    
    // Attach event handlers
    attachStatusEventHandlers();
}

function renderStatusRow(status, index) {
    const rowId = 'status-row-' + (status.id || 'new-' + index);
    let html = '<tr id="' + rowId + '" data-status-id="' + (status.id || '') + '" data-status-code="' + status.status_code + '">';
    
    // Sort Order
    html += '<td><input type="number" class="form-control form-control-sm status-sort-order" value="' + (status.sort_order || 0) + '" min="0" style="width: 60px;"></td>';
    
    // Status Code
    html += '<td><input type="number" class="form-control form-control-sm status-code" value="' + status.status_code + '" min="1" required style="width: 70px;"></td>';
    
    // Status Name
    html += '<td><input type="text" class="form-control form-control-sm status-name" value="' + (status.status_name || '') + '" required maxlength="100"></td>';
    
    // Background Color with visual picker
    html += '<td><div class="input-group input-group-sm"><input type="color" class="form-control form-control-color status-bg-color" value="' + (status.status_color || '#28a745') + '" style="width: 60px; height: 38px;"><input type="text" class="form-control form-control-sm status-bg-color-text" value="' + (status.status_color || '#28a745') + '" maxlength="7" style="width: 80px;"></div></td>';
    
    // Border Color with visual picker
    html += '<td><div class="input-group input-group-sm"><input type="color" class="form-control form-control-color status-border-color" value="' + (status.border_color || status.status_color || '#28a745') + '" style="width: 60px; height: 38px;"><input type="text" class="form-control form-control-sm status-border-color-text" value="' + (status.border_color || status.status_color || '#28a745') + '" maxlength="7" style="width: 80px;"></div></td>';
    
    // Border Width
    html += '<td><input type="number" class="form-control form-control-sm status-border-width" value="' + (status.border_width || 2) + '" min="0" max="10" style="width: 80px;" title="Border width (0-10px)"></td>';
    
    // Border Style
    html += '<td><select class="form-control form-control-sm status-border-style" style="width: 100px;">';
    const borderStyles = ['solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset', 'none'];
    borderStyles.forEach(function(style) {
        html += '<option value="' + style + '"' + ((status.border_style || 'solid') === style ? ' selected' : '') + '>' + style.charAt(0).toUpperCase() + style.slice(1) + '</option>';
    });
    html += '</select></td>';
    
    // Border Radius
    html += '<td><input type="number" class="form-control form-control-sm status-border-radius" value="' + (status.border_radius || 4) + '" min="0" max="50" style="width: 80px;" title="Border radius (0-50px)"></td>';
    
    // Text Color with visual picker
    html += '<td><div class="input-group input-group-sm"><input type="color" class="form-control form-control-color status-text-color" value="' + (status.text_color || '#ffffff') + '" style="width: 60px; height: 38px;"><input type="text" class="form-control form-control-sm status-text-color-text" value="' + (status.text_color || '#ffffff') + '" maxlength="7" style="width: 80px;"></div></td>';
    
    // Badge Color
    html += '<td><select class="form-control form-control-sm status-badge-color">';
    const badgeColors = ['success', 'info', 'warning', 'danger', 'primary', 'secondary', 'dark', 'light'];
    badgeColors.forEach(function(color) {
        html += '<option value="' + color + '"' + (status.badge_color === color ? ' selected' : '') + '>' + color.charAt(0).toUpperCase() + color.slice(1) + '</option>';
    });
    html += '</select></td>';
    
    // Description
    html += '<td><input type="text" class="form-control form-control-sm status-description" value="' + (status.description || '') + '" placeholder="Status description"></td>';
    
    // Floor Plan Assignment
    html += '<td><select class="form-control form-control-sm status-floor-plan">';
    html += '<option value="">Global (All Floor Plans)</option>';
    @foreach($floorPlans as $fp)
    html += '<option value="{{ $fp->id }}"' + (status.floor_plan_id == {{ $fp->id }} ? ' selected' : '') + '>{{ $fp->name }}</option>';
    @endforeach
    html += '</select></td>';
    
    // Is Default
    html += '<td class="text-center"><input type="checkbox" class="form-check-input status-is-default" ' + (status.is_default ? 'checked' : '') + '></td>';
    
    // Is Active
    html += '<td class="text-center"><input type="checkbox" class="form-check-input status-is-active" ' + (status.is_active !== false ? 'checked' : '') + '></td>';
    
    // Actions
    html += '<td class="text-center">';
    html += '<button type="button" class="btn btn-sm btn-danger btn-delete-status" title="Delete"><i class="fas fa-trash"></i></button>';
    html += '</td>';
    
    html += '</tr>';
    return html;
}

function makeStatusRowsSortable() {
    $('#statusSettingsBody').sortable({
        handle: '.status-sort-order',
        axis: 'y',
        update: function(event, ui) {
            $('#statusSettingsBody tr').each(function(index) {
                $(this).find('.status-sort-order').val(index + 1);
            });
        }
    });
}

function attachStatusEventHandlers() {
    // Sync color pickers with text inputs
    $('.status-bg-color, .status-border-color, .status-text-color').on('change', function() {
        const textInput = $(this).siblings('input[type="text"]');
        textInput.val($(this).val());
    });

    $('.status-bg-color-text, .status-border-color-text, .status-text-color-text').on('input', function() {
        const colorInput = $(this).siblings('input[type="color"]');
        const value = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            colorInput.val(value);
        }
    });

    // Only one default status allowed
    $('.status-is-default').on('change', function() {
        if ($(this).is(':checked')) {
            $('.status-is-default').not(this).prop('checked', false);
        }
    });

    // Delete status
    $('.btn-delete-status').on('click', function() {
        const row = $(this).closest('tr');
        const statusId = row.data('status-id');
        const statusName = row.find('.status-name').val();

        if (!confirm('Are you sure you want to delete status "' + statusName + '"? This action cannot be undone.')) {
            return;
        }

        if (statusId) {
            $.ajax({
                url: '{{ route("settings.booth-statuses.delete", ":id") }}'.replace(':id', statusId),
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.status === 200) {
                        toastr.success(response.message);
                        row.fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to delete status');
                }
            });
        } else {
            row.fadeOut(300, function() {
                $(this).remove();
            });
        }
    });
}

$('#btnAddStatus').on('click', function() {
    const maxCode = Math.max(...$('.status-code').map(function() {
        return parseInt($(this).val()) || 0;
    }).get(), 0);
    
        const newStatus = {
            id: null,
            status_code: maxCode + 1,
            status_name: 'New Status',
            status_color: '#6c757d',
            border_color: '#6c757d',
            border_width: 2,
            border_style: 'solid',
            border_radius: 4,
            text_color: '#ffffff',
            badge_color: 'secondary',
            description: '',
            floor_plan_id: null,
            is_active: true,
            sort_order: $('#statusSettingsBody tr').length + 1,
            is_default: false
        };

    if ($('#statusSettingsBody').length === 0) {
        renderStatusSettings([newStatus]);
    } else {
        const newRow = $(renderStatusRow(newStatus, $('#statusSettingsBody tr').length));
        $('#statusSettingsBody').append(newRow);
        attachStatusEventHandlers();
    }
});

$('#btnSaveStatusSettings').on('click', function() {
    const btn = $(this);
    const originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

    const statuses = [];
    $('#statusSettingsBody tr').each(function() {
        const row = $(this);
        const statusId = row.data('status-id');
        
        const floorPlanId = row.find('.status-floor-plan').val();
        
        statuses.push({
            id: statusId || null,
            status_code: parseInt(row.find('.status-code').val()) || 1,
            status_name: row.find('.status-name').val() || '',
            status_color: row.find('.status-bg-color-text').val() || '#28a745',
            border_color: row.find('.status-border-color-text').val() || null,
            border_width: parseInt(row.find('.status-border-width').val()) || 2,
            border_style: row.find('.status-border-style').val() || 'solid',
            border_radius: parseInt(row.find('.status-border-radius').val()) || 4,
            text_color: row.find('.status-text-color-text').val() || '#ffffff',
            badge_color: row.find('.status-badge-color').val() || 'success',
            description: row.find('.status-description').val() || '',
            floor_plan_id: (floorPlanId && floorPlanId !== '') ? parseInt(floorPlanId) : null,
            is_active: row.find('.status-is-active').is(':checked'),
            sort_order: parseInt(row.find('.status-sort-order').val()) || 0,
            is_default: row.find('.status-is-default').is(':checked')
        });
    });

    $.ajax({
        url: '{{ route("settings.booth-statuses.save") }}',
        method: 'POST',
        data: {
            statuses: statuses,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.status === 200) {
                toastr.success(response.message || 'Status settings saved successfully');
                setTimeout(function() {
                    loadStatusSettings();
                }, 500);
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors || {};
            let message = xhr.responseJSON?.message || 'Failed to save status settings';
            if (Object.keys(errors).length > 0) {
                message += ': ' + Object.values(errors).flat().join(', ');
            }
            toastr.error(message);
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
});

// Load on page load if modal is opened
$(document).ready(function() {
    if ($('#statusSettingsModal').hasClass('show')) {
        loadStatusSettings();
    }
});

// Quick Filter Functions
function applyQuickFilter(filterType) {
    const form = document.getElementById('filterForm');
    const searchInput = form.querySelector('input[name="search"]');
    const statusSelect = form.querySelector('select[name="status"]');
    const categorySelect = form.querySelector('select[name="category_id"]');
    
    // Clear existing filters first
    searchInput.value = '';
    statusSelect.value = '';
    categorySelect.value = '';
    
    // Highlight active button
    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.quick-filter-btn').classList.add('active');
    
    // Apply filter based on type
    switch(filterType) {
        case 'all':
            // No filters, just submit
            break;
            
        case 'available':
            statusSelect.value = '1'; // Available status
            break;
            
        case 'booked':
            // Filter for confirmed, reserved, or paid (not available)
            statusSelect.value = '2'; // Confirmed
            break;
            
        case 'paid':
            statusSelect.value = '5'; // Paid status
            break;
            
        case 'today':
            // Add hidden input for today's bookings
            let todayInput = form.querySelector('input[name="booked_today"]');
            if (!todayInput) {
                todayInput = document.createElement('input');
                todayInput.type = 'hidden';
                todayInput.name = 'booked_today';
                form.appendChild(todayInput);
            }
            todayInput.value = '1';
            break;
            
        case 'overdue':
            // Add hidden input for overdue payments
            let overdueInput = form.querySelector('input[name="payment_overdue"]');
            if (!overdueInput) {
                overdueInput = document.createElement('input');
                overdueInput.type = 'hidden';
                overdueInput.name = 'payment_overdue';
                form.appendChild(overdueInput);
            }
            overdueInput.value = '1';
            break;
    }
    
    // Submit the form
    form.submit();
}

function clearAllFilters() {
    const form = document.getElementById('filterForm');
    
    // Clear all inputs
    form.querySelectorAll('input[type="text"]').forEach(input => input.value = '');
    form.querySelectorAll('select').forEach(select => select.value = '');
    
    // Remove hidden inputs
    form.querySelectorAll('input[type="hidden"]').forEach(input => {
        if (input.name === 'booked_today' || input.name === 'payment_overdue') {
            input.remove();
        }
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Submit to show all
    form.submit();
}

// Mark active filter on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const bookedToday = urlParams.get('booked_today');
    const paymentOverdue = urlParams.get('payment_overdue');

    // Mobile search debounce
    const mobileSearchInput = document.querySelector('.mobile-search-input');
    if (mobileSearchInput) {
        let searchTimeout;
        mobileSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('mobileSearchForm').submit();
            }, 500);
        });
    }

    // Set active mobile filter chip based on current status
    if (window.innerWidth <= 768) {
        const statusMap = {
            '1': 'available',
            '2': 'confirmed',
            '3': 'reserved',
            '5': 'paid'
        };
        
        const activeFilter = statusMap[status] || 'all';
        document.querySelectorAll('.mobile-filter-chip').forEach(chip => {
            chip.classList.remove('active');
            if (chip.textContent.trim().toLowerCase() === activeFilter || 
                (activeFilter === 'all' && chip.textContent.trim().toLowerCase() === 'all')) {
                chip.classList.add('active');
            }
        });
    }

    // Highlight appropriate button based on current filters
    if (bookedToday) {
        document.querySelector('.quick-filter-btn[onclick*="today"]')?.classList.add('active');
    } else if (paymentOverdue) {
        document.querySelector('.quick-filter-btn[onclick*="overdue"]')?.classList.add('active');
    } else if (status === '1') {
        document.querySelector('.quick-filter-btn[onclick*="available"]')?.classList.add('active');
    } else if (status === '5') {
        document.querySelector('.quick-filter-btn[onclick*="paid"]')?.classList.add('active');
    } else if (status === '2' || status === '3') {
        document.querySelector('.quick-filter-btn[onclick*="booked"]')?.classList.add('active');
    } else if (!status && !bookedToday && !paymentOverdue) {
        document.querySelector('.quick-filter-btn[onclick*="all"]')?.classList.add('active');
    }
});
</script>
@endpush
