@extends('layouts.adminlte')

@section('title', 'Create Booking')
@section('page-title', 'Create New Booking')
@section('breadcrumb', 'Bookings / Create')

@push('styles')
<style>
    /* ============================================
       BOOKING FORM - UNIQUE INDEPENDENT STYLESHEET
       Each element has its own independent style
       No dependency on global styles
       ============================================ */
    
    /* Unique CSS Variables - Independent */
    :root {
        --bf-primary: #667eea;
        --bf-secondary: #764ba2;
        --bf-success: #22c55e;
        --bf-warning: #f59e0b;
        --bf-danger: #ef4444;
        --bf-info: #06b6d4;
        --bf-gray-50: #f9fafb;
        --bf-gray-100: #f3f4f6;
        --bf-gray-200: #e5e7eb;
        --bf-gray-300: #d1d5db;
        --bf-gray-600: #6b7280;
        --bf-gray-700: #374151;
        --bf-gray-900: #1a1a1a;
    }
    
    /* Unique Container - Independent Style */
    .bf-container-fluid {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 0;
        box-sizing: border-box;
    }
    
    .bf-container-fluid.bf-py-4 {
        padding-top: 24px;
        padding-bottom: 24px;
    }
    
    /* Unique Page Background - Independent */
    .bf-page-wrapper {
        background: #f5f7fa;
        min-height: calc(100vh - 57px);
        padding: 24px;
        font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        position: relative;
        box-sizing: border-box;
    }
    
    .bf-page-wrapper::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 300px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        z-index: 0;
        opacity: 0.05;
        pointer-events: none;
    }
    
    /* Unique Form Section View Switcher - Independent Style */
    .bf-section-view-switcher {
        display: flex;
        align-items: center;
        padding: 6px 10px;
        background: rgba(102, 126, 234, 0.05);
        border-radius: 8px;
        border: 1px solid rgba(102, 126, 234, 0.1);
        margin-right: 12px;
    }
    
    .bf-section-view-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        min-width: 32px;
        padding: 0;
        border-radius: 6px;
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #6b7280;
        font-size: 13px;
        -webkit-tap-highlight-color: transparent;
    }
    
    .bf-section-view-btn:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        transform: translateY(-1px);
    }
    
    .bf-section-view-btn.bf-section-view-btn-active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
    }
    
    .bf-section-view-btn.bf-section-view-btn-active:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-1px) scale(1.02);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.35);
    }
    
    .bf-section-view-btn i {
        font-size: 13px;
    }
    
    /* Unique Form Section - Independent Style */
    .bf-form-section {
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
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-sizing: border-box;
    }
    
    /* Default View */
    .bf-form-section.bf-section-view-default {
        padding: 20px;
        border-radius: 16px;
    }
    
    .bf-form-section.bf-section-view-default .bf-form-section-title {
        font-size: 17px;
        margin-bottom: 20px;
    }
    
    /* Tiny View (Smallest) */
    .bf-form-section.bf-section-view-tiny {
        padding: 8px 12px;
        border-radius: 10px;
        margin-bottom: 10px;
    }
    
    .bf-form-section.bf-section-view-tiny .bf-form-section-title {
        font-size: 12px;
        margin-bottom: 8px;
    }
    
    .bf-form-section.bf-section-view-tiny .bf-form-group {
        margin-bottom: 8px;
    }
    
    .bf-form-section.bf-section-view-tiny .bf-form-label {
        font-size: 11px;
        margin-bottom: 4px;
    }
    
    .bf-form-section.bf-section-view-tiny .bf-form-control {
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 8px;
    }
    
    .bf-form-section.bf-section-view-tiny .bf-btn {
        padding: 6px 12px;
        font-size: 11px;
        border-radius: 6px;
    }
    
    /* Compact View */
    .bf-form-section.bf-section-view-compact {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 12px;
    }
    
    .bf-form-section.bf-section-view-compact .bf-form-section-title {
        font-size: 14px;
        margin-bottom: 12px;
    }
    
    .bf-form-section.bf-section-view-compact .bf-form-group {
        margin-bottom: 12px;
    }
    
    .bf-form-section.bf-section-view-compact .bf-form-label {
        font-size: 13px;
        margin-bottom: 6px;
    }
    
    .bf-form-section.bf-section-view-compact .bf-form-control {
        padding: 10px 12px;
        font-size: 14px;
    }
    
    .bf-form-section.bf-section-view-compact .bf-btn {
        padding: 8px 14px;
        font-size: 13px;
    }
    
    /* Expanded View */
    .bf-form-section.bf-section-view-expanded {
        padding: 28px 24px;
        border-radius: 20px;
        margin-bottom: 24px;
    }
    
    .bf-form-section.bf-section-view-expanded .bf-form-section-title {
        font-size: 19px;
        margin-bottom: 24px;
    }
    
    .bf-form-section.bf-section-view-expanded .bf-form-group {
        margin-bottom: 22px;
    }
    
    .bf-form-section.bf-section-view-expanded .bf-form-label {
        font-size: 16px;
        margin-bottom: 12px;
    }
    
    .bf-form-section.bf-section-view-expanded .bf-form-control {
        padding: 18px 20px;
        font-size: 16px;
    }
    
    .bf-form-section.bf-section-view-expanded .bf-btn {
        padding: 16px 28px;
        font-size: 15px;
    }
    
    .bf-form-section:hover {
        box-shadow: 
            0 4px 12px rgba(0, 0, 0, 0.06),
            0 2px 6px rgba(0, 0, 0, 0.1);
    }
    
    .bf-form-section-title {
        font-size: 17px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.2px;
        margin-top: 0;
        padding: 0;
    }
    
    .bf-form-section-title-icon {
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
    
    /* Unique Main Card - Independent Style */
    .bf-card-main {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 
            0 2px 8px rgba(0, 0, 0, 0.04),
            0 1px 3px rgba(0, 0, 0, 0.08);
        border: 0.5px solid rgba(0, 0, 0, 0.06);
        overflow: visible;
        position: relative;
        box-sizing: border-box;
    }
    
    .bf-card-header {
        background: #ffffff;
        border-bottom: 0.5px solid rgba(0, 0, 0, 0.06);
        padding: 20px 24px;
        font-weight: 600;
        border-radius: 16px 16px 0 0;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .bf-card-header-title {
        margin: 0;
        color: #1a1a1a;
        font-weight: 600;
        font-size: 22px;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
    }
    
    .bf-card-header-title-icon {
        color: #667eea;
        margin-right: 10px;
        font-size: 22px;
    }
    
    /* Unique Form Controls - Independent Style */
    .bf-form-control {
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
        font-family: inherit;
        margin: 0;
    }
    
    .bf-form-control:focus {
        outline: none;
        border-color: #667eea;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .bf-form-control::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }
    
    .bf-form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
        margin-bottom: 8px;
        letter-spacing: -0.1px;
        margin-top: 0;
    }
    
    .bf-form-group {
        margin-bottom: 24px;
        box-sizing: border-box;
    }
    
    .bf-form-group:last-child {
        margin-bottom: 0;
    }
    
    /* Unique Buttons - Independent Style */
    .bf-btn {
        border-radius: 12px;
        padding: 14px 24px;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        font-size: 16px;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-family: inherit;
    }
    
    .bf-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .bf-btn:active {
        transform: scale(0.98);
    }
    
    .bf-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .bf-btn-primary:hover {
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .bf-btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .bf-btn-success:hover {
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        color: white;
    }
    
    .bf-btn-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
    }
    
    .bf-btn-info:hover {
        box-shadow: 0 6px 16px rgba(6, 182, 212, 0.4);
        color: white;
    }
    
    .bf-btn-secondary {
        background: #6b7280;
        color: white;
        box-shadow: 0 2px 8px rgba(107, 114, 128, 0.2);
    }
    
    .bf-btn-secondary:hover {
        background: #4b5563;
        color: white;
        box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
    }
    
    .bf-btn-sm {
        padding: 10px 16px;
        font-size: 14px;
    }
    
    /* Unique View Switcher - Independent Style */
    .bf-view-switcher {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background: rgba(102, 126, 234, 0.05);
        border-radius: 10px;
        border: 1px solid rgba(102, 126, 234, 0.1);
    }
    
    .bf-view-btn {
        width: 36px;
        height: 36px;
        min-width: 36px;
        min-height: 36px;
        border-radius: 8px;
        background: #ffffff;
        border: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #6b7280;
        font-size: 14px;
        -webkit-tap-highlight-color: transparent;
    }
    
    .bf-view-btn:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
    
    .bf-view-btn.bf-view-btn-active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
    }
    
    .bf-view-btn.bf-view-btn-active:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    /* Unique Booth Selector - Independent Style */
    .bf-booth-selector {
        max-height: 500px;
        overflow-y: auto;
        background: #f9fafb;
        border-radius: 12px;
        padding: 16px;
        border: 1.5px solid #e5e7eb;
        box-sizing: border-box;
    }
    
    .bf-booth-selector::-webkit-scrollbar {
        width: 6px;
    }
    
    .bf-booth-selector::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 3px;
    }
    
    .bf-booth-selector::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }
    
    .bf-booth-selector::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    .bf-booth-item-wrapper {
        transition: all 0.3s ease;
        display: block;
        opacity: 1;
        box-sizing: border-box;
    }
    
    /* Ensure view switcher controls booth item wrappers */
    .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-grid .bf-booth-item-wrapper,
    .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-list .bf-booth-item-wrapper,
    .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-card .bf-booth-item-wrapper {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    
    .bf-booth-item-wrapper.bf-hidden {
        display: none !important;
    }
    
    /* Grid View (Default - 2 columns) - Controlled by view switcher */
    .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-grid .bf-booth-item-wrapper,
    .bf-booths-container.bf-view-grid .bf-booth-item-wrapper {
        flex: 0 0 50% !important;
        max-width: 50% !important;
    }
    
    .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-grid .bf-booth-item-wrapper.bf-col-6,
    .bf-booths-container.bf-view-grid .bf-booth-item-wrapper.bf-col-6 {
        flex: 0 0 50% !important;
        max-width: 50% !important;
    }
    
    /* List View (1 column - full width) - Controlled by view switcher */
    .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-list .bf-booth-item-wrapper,
    .bf-booths-container.bf-view-list .bf-booth-item-wrapper {
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
    
    .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-list .bf-booth-item-wrapper.bf-col-12,
    .bf-booths-container.bf-view-list .bf-booth-item-wrapper.bf-col-12 {
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
    
    .bf-booths-container.bf-view-list .bf-booth-option {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        min-height: auto;
    }
    
    .bf-booths-container.bf-view-list .bf-booth-label {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 16px;
        flex-direction: row;
    }
    
    .bf-booths-container.bf-view-list .bf-booth-checkbox {
        margin-right: 0;
        flex-shrink: 0;
        order: 1;
    }
    
    .bf-booths-container.bf-view-list .bf-booth-content {
        order: 2;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    
    .bf-booths-container.bf-view-list .bf-booth-header {
        margin-bottom: 0;
        flex: 1;
    }
    
    .bf-booths-container.bf-view-list .bf-booth-category {
        margin-top: 0;
        margin-left: 16px;
    }
    
    .bf-booths-container.bf-view-list .bf-booth-floor-plan {
        margin-top: 4px;
        margin-left: 16px;
    }
    
    /* Card View (3 columns) - Controlled by view switcher */
    .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-card .bf-booth-item-wrapper,
    .bf-booths-container.bf-view-card .bf-booth-item-wrapper {
        flex: 0 0 33.333333% !important;
        max-width: 33.333333% !important;
    }
    
    .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-card .bf-booth-item-wrapper.bf-col-4,
    .bf-booths-container.bf-view-card .bf-booth-item-wrapper.bf-col-4 {
        flex: 0 0 33.333333% !important;
        max-width: 33.333333% !important;
    }
    
    @media (max-width: 1200px) {
        .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-card .bf-booth-item-wrapper,
        .bf-view-switcher.bf-mb-3 ~ .bf-booth-selector .bf-booths-container.bf-view-card .bf-booth-item-wrapper.bf-col-4,
        .bf-booths-container.bf-view-card .bf-booth-item-wrapper,
        .bf-booths-container.bf-view-card .bf-booth-item-wrapper.bf-col-4 {
            flex: 0 0 50% !important;
            max-width: 50% !important;
        }
    }
    
    .bf-booths-container.bf-view-card .bf-booth-option {
        padding: 20px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-label {
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-checkbox {
        margin-bottom: 12px;
        margin-right: 0;
        order: 1;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-content {
        order: 2;
        width: 100%;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-header {
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
        align-items: center;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-number {
        font-size: 1.3rem;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-price {
        font-size: 1.4rem;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-category {
        margin-top: 8px;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-floor-plan {
        margin-top: 6px;
    }
    
    .bf-booth-option {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        padding: 16px;
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
        margin-bottom: 12px;
    }
    
    .bf-booth-option:last-child {
        margin-bottom: 0;
    }
    
    .bf-booth-option::before {
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
    
    .bf-booth-option:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
    
    .bf-booth-option:hover::before {
        opacity: 1;
    }
    
    .bf-booth-option.bf-selected {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.2);
    }
    
    .bf-booth-option.bf-selected::before {
        opacity: 1;
    }
    
    .bf-booth-checkbox {
        margin-right: 12px;
        cursor: pointer;
        width: 20px;
        height: 20px;
        accent-color: #667eea;
    }
    
    .bf-booth-label {
        cursor: pointer;
        width: 100%;
        margin: 0;
        display: flex;
        align-items: flex-start;
    }
    
    .bf-booth-content {
        flex: 1;
        width: 100%;
    }
    
    .bf-booth-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .bf-booth-number {
        font-size: 1.1rem;
    }
    
    .bf-booth-price {
        font-size: 1.2rem;
    }
    
    .bf-booth-category {
        margin-top: 8px;
    }
    
    .bf-booth-floor-plan {
        margin-top: 6px;
    }
    
    .bf-floor-plan-label {
        font-size: 12px;
        color: #9ca3af;
        display: flex;
        align-items: center;
    }
    
    .bf-floor-plan-label i {
        font-size: 11px;
        opacity: 0.7;
    }
    
    /* List View Specific Styles */
    .bf-booths-container.bf-view-list .bf-booth-header {
        margin-bottom: 0;
        flex: 1;
    }
    
    .bf-booths-container.bf-view-list .bf-booth-category {
        margin-top: 4px;
        margin-left: 0;
    }
    
    /* Card View Specific Styles */
    .bf-booths-container.bf-view-card .bf-booth-header {
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-number {
        font-size: 1.3rem;
    }
    
    .bf-booths-container.bf-view-card .bf-booth-price {
        font-size: 1.4rem;
    }
    
    /* Unique Selected Booths Summary - Independent Style */
    .bf-booths-summary {
        position: sticky;
        top: 20px;
        background: #ffffff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 
            0 2px 8px rgba(0, 0, 0, 0.04),
            0 1px 3px rgba(0, 0, 0, 0.08);
        border: 0.5px solid rgba(0, 0, 0, 0.06);
        box-sizing: border-box;
    }
    
    /* Unique Booking Type Views - Independent Style */
    .bf-booking-type-view {
        display: none;
    }
    
    .bf-booking-type-view.bf-active {
        display: block;
    }
    
    /* Icon View */
    .bf-booking-type-options-icon {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .bf-booking-type-option-icon {
        flex: 1;
        min-width: 100px;
        cursor: pointer;
        background: #ffffff;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px 16px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .bf-booking-type-option-icon::before {
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
    
    .bf-booking-type-option-icon:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.2);
    }
    
    .bf-booking-type-option-icon:hover::before {
        opacity: 1;
    }
    
    .bf-booking-type-option-icon.bf-selected {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    }
    
    .bf-booking-type-option-icon.bf-selected::before {
        opacity: 0;
    }
    
    .bf-booking-type-icon-wrapper {
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(102, 126, 234, 0.1);
        border-radius: 12px;
        font-size: 24px;
        color: #667eea;
        transition: all 0.3s ease;
    }
    
    .bf-booking-type-option-icon.bf-selected .bf-booking-type-icon-wrapper {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }
    
    .bf-booking-type-label {
        display: none;
    }
    
    /* List View */
    .bf-booking-type-options-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .bf-booking-type-option-list {
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: pointer;
        background: #ffffff;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .bf-booking-type-option-list::before {
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
    
    .bf-booking-type-option-list:hover {
        border-color: #667eea;
        transform: translateX(4px);
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
    }
    
    .bf-booking-type-option-list:hover::before {
        opacity: 1;
    }
    
    .bf-booking-type-option-list.bf-selected {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-color: #667eea;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
    }
    
    .bf-booking-type-option-list.bf-selected::before {
        opacity: 1;
    }
    
    .bf-booking-type-list-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(102, 126, 234, 0.1);
        border-radius: 10px;
        font-size: 20px;
        color: #667eea;
        transition: all 0.3s ease;
    }
    
    .bf-booking-type-option-list.bf-selected .bf-booking-type-list-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .bf-booking-type-list-content {
        flex: 1;
    }
    
    .bf-booking-type-list-title {
        font-weight: 600;
        font-size: 15px;
        color: #111827;
        margin-bottom: 4px;
    }
    
    .bf-booking-type-list-desc {
        display: none;
    }
    
    .bf-booking-type-list-check {
        width: 24px;
        height: 24px;
        min-width: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .bf-booking-type-option-list.bf-selected .bf-booking-type-list-check {
        opacity: 1;
        color: #667eea;
    }
    
    /* Card View */
    .bf-booking-type-options-card {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .bf-booking-type-option-card {
        flex: 1;
        min-width: 180px;
        cursor: pointer;
        background: #ffffff;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    
    .bf-booking-type-option-card::before {
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
    
    .bf-booking-type-option-card:hover {
        border-color: #667eea;
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
    }
    
    .bf-booking-type-option-card:hover::before {
        opacity: 1;
    }
    
    .bf-booking-type-option-card.bf-selected {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-color: #667eea;
        box-shadow: 0 8px 28px rgba(102, 126, 234, 0.3);
    }
    
    .bf-booking-type-option-card.bf-selected::before {
        opacity: 1;
    }
    
    .bf-booking-type-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .bf-booking-type-card-icon {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(102, 126, 234, 0.1);
        border-radius: 14px;
        font-size: 28px;
        color: #667eea;
        transition: all 0.3s ease;
    }
    
    .bf-booking-type-option-card.bf-selected .bf-booking-type-card-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .bf-booking-type-card-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #e5e7eb;
        color: #6b7280;
    }
    
    .bf-booking-type-card-badge.bf-premium {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }
    
    .bf-booking-type-card-badge.bf-temp {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }
    
    .bf-booking-type-card-title {
        font-weight: 600;
        font-size: 16px;
        color: #111827;
        margin-bottom: 8px;
    }
    
    .bf-booking-type-card-desc {
        display: none;
    }
    
    .bf-booking-type-card-check {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #667eea;
        border-radius: 50%;
        color: white;
        font-size: 14px;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .bf-booking-type-option-card.bf-selected .bf-booking-type-card-check {
        opacity: 1;
        transform: scale(1);
    }
    
    .bf-booths-summary-title {
        font-size: 17px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.2px;
        margin-top: 0;
        padding: 0;
    }
    
    .bf-booths-summary-title-icon {
        color: #667eea;
        font-size: 20px;
    }
    
    .bf-booths-list {
        max-height: 300px;
        overflow-y: auto;
        min-height: 100px;
        margin-bottom: 16px;
    }
    
    .bf-booth-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        padding: 12px;
        background: #f9fafb;
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        box-sizing: border-box;
    }
    
    .bf-booth-item:last-child {
        margin-bottom: 0;
    }
    
    .bf-summary-divider {
        border: none;
        border-top: 0.5px solid rgba(0, 0, 0, 0.06);
        margin: 16px 0;
    }
    
    .bf-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px;
        margin-bottom: 12px;
        border-radius: 8px;
        box-sizing: border-box;
    }
    
    .bf-summary-row-total {
        background: rgba(102, 126, 234, 0.05);
    }
    
    .bf-summary-row-amount {
        background: rgba(34, 197, 94, 0.1);
    }
    
    .bf-summary-label {
        font-weight: 600;
        color: #1a1a1a;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .bf-summary-value {
        font-weight: 600;
        font-size: 18px;
    }
    
    .bf-summary-value-amount {
        font-size: 20px;
        color: #22c55e;
    }
    
    /* Unique Floor Plan Cards - Independent Style */
    .bf-floor-plan-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
    }
    
    .bf-floor-plan-card {
        background: #ffffff;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    
    .bf-floor-plan-card::before {
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
    
    .bf-floor-plan-card:hover {
        border-color: #667eea;
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
    }
    
    .bf-floor-plan-card:hover::before {
        opacity: 1;
    }
    
    .bf-floor-plan-card.bf-selected {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        box-shadow: 0 8px 28px rgba(102, 126, 234, 0.3);
    }
    
    .bf-floor-plan-card.bf-selected::before {
        opacity: 1;
    }
    
    .bf-floor-plan-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .bf-floor-plan-card-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(102, 126, 234, 0.1);
        border-radius: 12px;
        font-size: 24px;
        color: #667eea;
        transition: all 0.3s ease;
    }
    
    .bf-floor-plan-card.bf-selected .bf-floor-plan-card-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .bf-floor-plan-card-badge {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .bf-floor-plan-card-badge.bf-badge-default {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }
    
    .bf-floor-plan-card-badge.bf-badge-all {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        color: white;
    }
    
    .bf-floor-plan-card-title {
        font-weight: 600;
        font-size: 16px;
        color: #111827;
        margin-bottom: 12px;
    }
    
    .bf-floor-plan-card-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .bf-floor-plan-info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #6b7280;
    }
    
    .bf-floor-plan-info-item i {
        font-size: 12px;
        width: 16px;
        text-align: center;
    }
    
    .bf-floor-plan-card-check {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #667eea;
        border-radius: 50%;
        color: white;
        font-size: 14px;
        opacity: 0;
        transform: scale(0);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .bf-floor-plan-card.bf-selected .bf-floor-plan-card-check {
        opacity: 1;
        transform: scale(1);
    }
    
    @media (max-width: 768px) {
        .bf-floor-plan-cards {
            grid-template-columns: 1fr;
        }
    }
    
    /* Unique Alerts - Independent Style */
    .bf-alert {
        border-radius: 12px;
        border: 1.5px solid;
        padding: 16px;
        margin-bottom: 16px;
        box-sizing: border-box;
    }
    
    .bf-alert-info {
        background: rgba(6, 182, 212, 0.1);
        border-color: #06b6d4;
        color: #0e7490;
    }
    
    .bf-alert-warning {
        background: rgba(245, 158, 11, 0.1);
        border-color: #f59e0b;
        color: #92400e;
    }
    
    .bf-alert-success {
        background: rgba(34, 197, 94, 0.1);
        border-color: #22c55e;
        color: #15803d;
    }
    
    /* Unique Modal - Independent Style */
    .bf-modal-content {
        border-radius: 16px;
        border: 0.5px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        background: #ffffff;
    }
    
    .bf-modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 24px;
        border: none;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .bf-modal-title {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        color: white;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .bf-modal-close {
        color: white;
        opacity: 0.9;
        text-shadow: none;
        font-size: 24px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }
    
    .bf-modal-close:hover {
        opacity: 1;
    }
    
    .bf-modal-body {
        padding: 24px;
        box-sizing: border-box;
    }
    
    .bf-modal-footer {
        padding: 16px 24px;
        border-top: 0.5px solid rgba(0, 0, 0, 0.06);
        background: rgba(102, 126, 234, 0.05);
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
    }
    
    /* Client Search Wrapper - Redesigned */
    .client-search-wrapper {
        margin-bottom: 0;
        position: relative;
        z-index: 100;
        /* Create stacking context to prevent overlap */
        isolation: isolate;
    }
    
    /* Unique Input Group - Independent Style */
    .bf-input-group {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        overflow: visible;
        position: relative;
        background: white;
        border: 1.5px solid #e5e7eb;
        display: flex;
        align-items: stretch;
        box-sizing: border-box;
    }
    
    .bf-input-group-prepend {
        display: flex;
        align-items: center;
        border-right: none;
    }
    
    .bf-input-group-text {
        border-radius: 12px 0 0 12px;
        border: none;
        border-right: 1.5px solid #e5e7eb;
        background: #f9fafb;
        padding: 16px;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }
    
    .bf-input-group .bf-form-control {
        border: none;
        padding: 16px;
        background: #f9fafb;
        z-index: 1;
        font-size: 16px;
        flex: 1;
    }
    
    .bf-input-group .bf-form-control:focus {
        box-shadow: none;
        background: #ffffff;
        z-index: 2;
    }
    
    .bf-input-group:focus-within {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: #ffffff;
    }
    
    .bf-input-group-append {
        display: flex;
        align-items: center;
        border-left: none;
    }
    
    .bf-input-group-append .bf-btn {
        border-radius: 0 12px 12px 0;
        border-left: 1.5px solid #e5e7eb;
        z-index: 1;
        margin: 0;
    }
    
    /* Unique Client Results Dropdown - Independent Style */
    .bf-client-results-dropdown {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        right: 0;
        z-index: 9999;
        animation: bf-slideDown 0.15s ease-out;
        pointer-events: auto;
    }
    
    @keyframes bf-slideDown {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .bf-client-results-dropdown .bf-card-main {
        max-height: 400px;
        overflow-y: auto;
        overflow-x: hidden;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        border: 1px solid #e5e7eb;
        margin: 0;
        background: white;
        position: relative;
        z-index: 9999;
    }
    
    .bf-client-results-dropdown .bf-card-body {
        padding: 8px;
    }
    
    /* Unique Row - Independent Style */
    .bf-row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -12px;
        margin-right: -12px;
        box-sizing: border-box;
    }
    
    .bf-col {
        flex: 1;
        padding-left: 12px;
        padding-right: 12px;
        box-sizing: border-box;
    }
    
    .bf-col-6 {
        flex: 0 0 50%;
        max-width: 50%;
    }
    
    .bf-col-8 {
        flex: 0 0 66.666667%;
        max-width: 66.666667%;
    }
    
    .bf-col-4 {
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
    
    @media (max-width: 1200px) {
        .bf-col-4 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    .bf-col-12 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    /* Unique Flex Utilities - Independent Style */
    .bf-d-flex {
        display: flex;
    }
    
    .bf-justify-content-between {
        justify-content: space-between;
    }
    
    .bf-align-items-center {
        align-items: center;
    }
    
    .bf-gap-2 {
        gap: 8px;
    }
    
    .bf-gap-3 {
        gap: 12px;
    }
    
    .bf-mb-0 {
        margin-bottom: 0;
    }
    
    .bf-mb-2 {
        margin-bottom: 8px;
    }
    
    .bf-mb-3 {
        margin-bottom: 12px;
    }
    
    .bf-mb-4 {
        margin-bottom: 16px;
    }
    
    .bf-mt-2 {
        margin-top: 8px;
    }
    
    .bf-ml-2 {
        margin-left: 8px;
    }
    
    .bf-mr-2 {
        margin-right: 8px;
    }
    
    .bf-text-center {
        text-align: center;
    }
    
    .bf-text-muted {
        color: #6b7280;
    }
    
    .bf-text-danger {
        color: #ef4444;
    }
    
    .bf-text-success {
        color: #22c55e;
    }
    
    .bf-text-primary {
        color: #667eea;
    }
    
    .bf-font-weight-bold {
        font-weight: 600;
    }
    
    .bf-font-weight-normal {
        font-weight: 400;
    }
    
    /* Unique Zone Selector - Independent Style */
    .bf-zone-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }
    
    .bf-zone-btn {
        padding: 12px 20px;
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        background: #ffffff;
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        position: relative;
        overflow: hidden;
    }
    
    .bf-zone-btn::before {
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
    
    .bf-zone-btn:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
    
    .bf-zone-btn:hover::before {
        opacity: 1;
    }
    
    .bf-zone-btn.bf-zone-btn-active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        font-weight: 600;
    }
    
    .bf-zone-btn.bf-zone-btn-active::before {
        opacity: 0;
    }
    
    .bf-zone-btn.bf-zone-btn-active:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .bf-zone-btn i {
        font-size: 14px;
        transition: transform 0.3s ease;
    }
    
    .bf-zone-btn:hover i {
        transform: scale(1.1);
    }
    
    .bf-zone-label {
        font-size: 13px;
        font-weight: 600;
        margin: 0 4px;
    }
    
    .bf-zone-count {
        margin-left: 4px;
        font-size: 11px;
        font-weight: 600;
        opacity: 0.8;
        background: rgba(0, 0, 0, 0.1);
        padding: 2px 6px;
        border-radius: 10px;
    }
    
    .bf-zone-btn.bf-zone-btn-active .bf-zone-count {
        opacity: 0.9;
        background: rgba(255, 255, 255, 0.2);
    }
    
    /* Unique Booth Info - Independent Style */
    .bf-booth-info {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background: rgba(102, 126, 234, 0.08);
        border-radius: 8px;
        border: 1px solid rgba(102, 126, 234, 0.15);
        margin-top: 8px;
        gap: 12px;
    }
    
    .bf-booth-info small {
        font-size: 13px;
        display: flex;
        align-items: center;
        color: #6b7280;
        flex: 1;
    }
    
    .bf-booth-info strong {
        color: #667eea;
        font-weight: 600;
        margin: 0 4px;
    }
    
    .bf-booth-info i {
        color: #667eea;
        font-size: 14px;
    }
    
    /* Interactive Icons - Independent Style */
    .bf-interactive-icon {
        width: 32px;
        height: 32px;
        min-width: 32px;
        min-height: 32px;
        border-radius: 8px;
        background: rgba(102, 126, 234, 0.1);
        border: 1px solid rgba(102, 126, 234, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #667eea;
        font-size: 14px;
        position: relative;
        -webkit-tap-highlight-color: transparent;
    }
    
    .bf-interactive-icon:hover {
        background: rgba(102, 126, 234, 0.2);
        border-color: #667eea;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
        color: #667eea;
    }
    
    .bf-interactive-icon:active {
        transform: translateY(0) scale(0.95);
        box-shadow: 0 2px 6px rgba(102, 126, 234, 0.2);
    }
    
    .bf-interactive-icon i {
        transition: transform 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .bf-interactive-icon:hover i {
        transform: scale(1.1);
    }
    
    .bf-interactive-icon.bf-icon-refresh:hover i {
        animation: bf-spin 1s linear infinite;
    }
    
    .bf-interactive-icon.bf-icon-filter:hover i {
        animation: bf-pulse 1.5s ease-in-out infinite;
    }
    
    .bf-interactive-icon.bf-icon-view:hover i {
        animation: bf-bounce 0.6s ease-in-out;
    }
    
    @keyframes bf-spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    @keyframes bf-pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.8;
        }
    }
    
    @keyframes bf-bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-4px);
        }
    }
    
    /* Tooltip for interactive icons */
    .bf-interactive-icon[data-tooltip] {
        position: relative;
    }
    
    .bf-interactive-icon[data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-bottom: 8px;
        padding: 6px 10px;
        background: #1a1a1a;
        color: white;
        font-size: 12px;
        white-space: nowrap;
        border-radius: 6px;
        pointer-events: none;
        z-index: 1000;
        animation: bf-fadeIn 0.2s ease;
    }
    
    .bf-interactive-icon[data-tooltip]:hover::before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        margin-bottom: 2px;
        border: 4px solid transparent;
        border-top-color: #1a1a1a;
        pointer-events: none;
        z-index: 1000;
    }
    
    @keyframes bf-fadeIn {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(-4px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
    
    /* Unique Client Search Result - Independent Style */
    .bf-client-result {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        background: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        box-sizing: border-box;
    }
    
    .bf-client-result:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }
    
    .bf-client-result.bf-highlighted {
        border-color: #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.2);
    }
    
    .bf-client-result:last-child {
        margin-bottom: 0;
    }
    
    .bf-client-result-content {
        flex: 1;
        min-width: 0;
    }
    
    .bf-client-result-name {
        color: #1a1a1a;
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .bf-client-result-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 8px;
    }
    
    .bf-client-result-detail {
        display: flex;
        align-items: center;
        font-size: 13px;
        color: #6b7280;
        gap: 8px;
    }
    
    /* Client Result Content */
    .client-result-content {
        flex: 1;
        min-width: 0;
    }
    
    .client-result-name {
        color: #1a1a2e;
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
    }
    
    .client-result-name i {
        color: var(--booking-primary);
        margin-right: 0.5rem;
        font-size: 1rem;
    }
    
    .client-result-name mark {
        background: rgba(102, 126, 234, 0.25);
        padding: 0.1rem 0.25rem;
        border-radius: 4px;
        font-weight: 800;
        color: var(--booking-primary);
    }
    
    .client-result-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        margin-top: 0.375rem;
    }
    
    .client-result-detail {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .client-result-detail i {
        width: 16px;
        margin-right: 0.5rem;
        font-size: 0.8rem;
    }
    
    .client-result-detail.email i {
        color: var(--booking-primary);
    }
    
    .client-result-detail.phone i {
        color: var(--booking-success);
    }
    
    .client-result-detail.user i {
        color: var(--booking-info);
    }
    
    /* Select Button */
    .select-client-inline-btn {
        margin-left: 0.75rem;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.25s ease;
    }
    
    .select-client-inline-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    /* Empty State */
    .client-results-empty {
        text-align: center;
        padding: 2rem 1rem;
        color: #6c757d;
    }
    
    .client-results-empty i {
        font-size: 2.5rem;
        color: #dee2e6;
        margin-bottom: 0.75rem;
        display: block;
    }
    
    /* Loading State */
    .client-results-loading {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--booking-primary);
    }
    
    .client-results-loading i {
        font-size: 1.5rem;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Unique Selected Client Card - Independent Style */
    .bf-selected-client-card {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
        border: 2px solid #22c55e;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.15);
        box-sizing: border-box;
        margin-bottom: 16px;
    }
    
    .bf-selected-client-name {
        color: #1a1a1a;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .bf-selected-client-details {
        color: #6b7280;
        font-size: 14px;
    }
    
    /* Unique Badge - Independent Style */
    .bf-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        display: inline-block;
        line-height: 1;
    }
    
    .bf-badge-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .bf-badge-success {
        background: #22c55e;
        color: white;
    }
    
    .bf-badge-warning {
        background: #f59e0b;
        color: white;
    }
    
    .bf-badge-danger {
        background: #ef4444;
        color: white;
    }
    
    .bf-badge-info {
        background: #06b6d4;
        color: white;
    }
    
    /* Unique Layout Switcher - Independent Style */
    .bf-layout-switcher {
        display: flex;
        align-items: center;
        padding: 6px 10px;
        background: rgba(102, 126, 234, 0.05);
        border-radius: 8px;
        border: 1px solid rgba(102, 126, 234, 0.1);
        margin-right: 12px;
    }
    
    .bf-layout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        min-width: 32px;
        padding: 0;
        border-radius: 6px;
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #6b7280;
        font-size: 13px;
        -webkit-tap-highlight-color: transparent;
    }
    
    .bf-layout-btn:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        transform: translateY(-1px);
    }
    
    .bf-layout-btn.bf-layout-btn-active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
    }
    
    .bf-layout-btn.bf-layout-btn-active:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-1px) scale(1.02);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.35);
    }
    
    .bf-layout-btn i {
        font-size: 13px;
    }
    
    /* Unique Card Body - Independent Style */
    .bf-card-body {
        padding: 24px;
        box-sizing: border-box;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Default Layout */
    .bf-card-body.bf-layout-default {
        padding: 24px;
    }
    
    /* Minimal Layout */
    .bf-card-body.bf-layout-minimal {
        padding: 12px 16px;
    }
    
    .bf-card-body.bf-layout-minimal .bf-form-section {
        padding: 16px;
        margin-bottom: 16px;
    }
    
    .bf-card-body.bf-layout-minimal .bf-form-section-title {
        font-size: 14px;
        margin-bottom: 12px;
    }
    
    .bf-card-body.bf-layout-minimal .bf-form-group {
        margin-bottom: 14px;
    }
    
    .bf-card-body.bf-layout-minimal .bf-form-label {
        font-size: 13px;
        margin-bottom: 6px;
    }
    
    .bf-card-body.bf-layout-minimal .bf-form-control {
        padding: 12px;
        font-size: 14px;
    }
    
    .bf-card-body.bf-layout-minimal .bf-btn {
        padding: 10px 16px;
        font-size: 13px;
    }
    
    /* Expand Layout */
    .bf-card-body.bf-layout-expand {
        padding: 32px 28px;
    }
    
    .bf-card-body.bf-layout-expand .bf-form-section {
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .bf-card-body.bf-layout-expand .bf-form-section-title {
        font-size: 17px;
        margin-bottom: 18px;
    }
    
    .bf-card-body.bf-layout-expand .bf-form-group {
        margin-bottom: 20px;
    }
    
    .bf-card-body.bf-layout-expand .bf-form-label {
        font-size: 15px;
        margin-bottom: 10px;
    }
    
    .bf-card-body.bf-layout-expand .bf-form-control {
        padding: 18px;
        font-size: 16px;
    }
    
    .bf-card-body.bf-layout-expand .bf-btn {
        padding: 14px 24px;
        font-size: 15px;
    }
    
    /* Unique Card Footer - Independent Style */
    .bf-card-footer {
        background: #ffffff;
        border-top: 0.5px solid rgba(0, 0, 0, 0.06);
        padding: 20px 24px;
        border-radius: 0 0 16px 16px;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    /* Prevent body overflow issues */
    body {
        overflow-x: hidden;
    }
    
    /* Unique Client Search Wrapper - Independent Style */
    .bf-client-search-wrapper {
        margin-bottom: 0;
        position: relative;
        z-index: 100;
        isolation: isolate;
    }
    
    .bf-form-section.bf-client-search-section {
        z-index: 10;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .bf-client-results-dropdown {
            left: -16px;
            right: -16px;
            margin-left: 16px;
            margin-right: 16px;
        }
        
        .bf-form-section {
            padding: 16px;
            margin-bottom: 16px;
        }
        
        .bf-booths-summary {
            position: relative;
            top: 0;
            margin-top: 16px;
        }
        
        .bf-client-results-dropdown .bf-card-main {
            max-height: 300px;
        }
        
        .bf-col-6,
        .bf-col-8,
        .bf-col-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    
    /* Additional spacing for dropdown */
    .bf-client-results-dropdown {
        margin-top: 8px;
    }
    
    /* Prevent body overflow issues */
    body {
        overflow-x: hidden;
    }
</style>
@endpush

@section('content')
<div class="bf-page-wrapper">
    <div class="bf-container-fluid bf-py-4">
        <div class="bf-card-main">
            <div class="bf-card-header">
                <h3 class="bf-card-header-title">
                    <i class="fas fa-calendar-plus bf-card-header-title-icon"></i>New Booking
                </h3>
                <div class="bf-d-flex bf-gap-2 bf-align-items-center">
                    <!-- Layout Type Switcher -->
                    <div class="bf-layout-switcher">
                        <div class="bf-d-flex bf-align-items-center bf-gap-2">
                            <button type="button" class="bf-layout-btn bf-layout-btn-active" data-layout="default" onclick="switchCardLayout('default')" title="Default Layout">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="bf-layout-btn" data-layout="minimal" onclick="switchCardLayout('minimal')" title="Minimal Layout">
                                <i class="fas fa-compress"></i>
                            </button>
                            <button type="button" class="bf-layout-btn" data-layout="expand" onclick="switchCardLayout('expand')" title="Expand Layout">
                                <i class="fas fa-expand"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Form Section View Switcher -->
                    <div class="bf-section-view-switcher">
                        <div class="bf-d-flex bf-align-items-center bf-gap-2">
                            <button type="button" class="bf-section-view-btn bf-section-view-btn-active" data-view="default" onclick="switchFormSectionView('default')" title="Default View">
                                <i class="fas fa-square"></i>
                            </button>
                            <button type="button" class="bf-section-view-btn" data-view="tiny" onclick="switchFormSectionView('tiny')" title="Tiny View">
                                <i class="fas fa-compress"></i>
                            </button>
                            <button type="button" class="bf-section-view-btn" data-view="compact" onclick="switchFormSectionView('compact')" title="Compact View">
                                <i class="fas fa-compress-arrows-alt"></i>
                            </button>
                            <button type="button" class="bf-section-view-btn" data-view="expanded" onclick="switchFormSectionView('expanded')" title="Expanded View">
                                <i class="fas fa-expand-arrows-alt"></i>
                            </button>
                        </div>
                    </div>
                    @if(isset($currentFloorPlan) && $currentFloorPlan)
                    <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $currentFloorPlan->id]) }}" class="bf-btn bf-btn-info bf-btn-sm" title="View Floor Plan Canvas">
                        <i class="fas fa-map-marked-alt"></i>
                    </a>
                    @endif
                    <a href="{{ route('books.index') }}" class="bf-btn bf-btn-secondary bf-btn-sm" title="Back to Bookings">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        <form action="{{ route('books.store') }}" method="POST" id="bookingForm">
            @csrf
            <div class="bf-card-body bf-layout-default" id="bfCardBody">
                @if(isset($currentFloorPlan) && $currentFloorPlan)
                <div class="bf-alert bf-alert-info bf-mb-4">
                    <div class="bf-d-flex bf-justify-content-between bf-align-items-center">
                        <div>
                            <i class="fas fa-map bf-mr-2"></i>
                            <strong>Booking for Floor Plan:</strong> {{ $currentFloorPlan->name }}
                            @if($currentFloorPlan->event) - {{ $currentFloorPlan->event->title }} @endif
                        </div>
                    <a href="{{ route('books.create') }}" class="bf-btn bf-btn-secondary bf-btn-sm" title="Clear Filter">
                        <i class="fas fa-times"></i>
                    </a>
                    </div>
                </div>
                @endif

                <!-- Floor Plan Selection -->
                @if(isset($floorPlans) && $floorPlans->count() > 0)
                <div class="bf-form-section bf-section-view-default">
                    <div class="bf-d-flex bf-justify-content-between bf-align-items-center bf-mb-3">
                        <h6 class="bf-form-section-title bf-mb-0">
                            <i class="fas fa-map bf-form-section-title-icon"></i>Floor Plan
                        </h6>
                        <!-- Section View Switcher for Floor Plan -->
                        <div class="bf-section-view-switcher">
                            <div class="bf-d-flex bf-align-items-center bf-gap-2">
                                <button type="button" class="bf-section-view-btn bf-section-view-btn-active" data-view="default" onclick="switchFormSectionView('default')" title="Default View">
                                    <i class="fas fa-square"></i>
                                </button>
                                <button type="button" class="bf-section-view-btn" data-view="tiny" onclick="switchFormSectionView('tiny')" title="Tiny View">
                                    <i class="fas fa-compress"></i>
                                </button>
                                <button type="button" class="bf-section-view-btn" data-view="compact" onclick="switchFormSectionView('compact')" title="Compact View">
                                    <i class="fas fa-compress-arrows-alt"></i>
                                </button>
                                <button type="button" class="bf-section-view-btn" data-view="expanded" onclick="switchFormSectionView('expanded')" title="Expanded View">
                                    <i class="fas fa-expand-arrows-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="bf-row">
                        <div class="bf-col bf-col-12">
                            <div class="bf-form-group">
                                <label for="floor_plan_filter" class="bf-form-label bf-font-weight-bold">
                                    <i class="fas fa-filter bf-mr-2"></i>Floor Plan
                                </label>
                                <select class="bf-form-control" id="floor_plan_filter" name="floor_plan_filter" onchange="filterByFloorPlan(this.value)" title="Filter booths by floor plan">
                                    <option value="">All</option>
                                    @foreach($floorPlans as $fp)
                                        <option value="{{ $fp->id }}" {{ (isset($floorPlanId) && $floorPlanId == $fp->id) ? 'selected' : '' }}>
                                            {{ $fp->name }}
                                            @if($fp->is_default) (Default) @endif
                                            @if($fp->event) - {{ $fp->event->title }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Client Selection -->
                <div class="bf-form-section bf-section-view-default bf-client-search-section">
                    <h6 class="bf-form-section-title">
                        <i class="fas fa-building bf-form-section-title-icon"></i>Client
                    </h6>
                    <input type="hidden" id="clientid" name="clientid" value="{{ old('clientid') }}" required>
                    
                    <!-- Selected Client Display -->
                    <div id="selectedClientInfo" class="bf-mb-3" style="display: none;">
                        <div class="bf-selected-client-card">
                            <div class="bf-d-flex bf-justify-content-between bf-align-items-center">
                                <div style="flex: 1;">
                                    <div class="bf-d-flex bf-align-items-center bf-mb-2">
                                        <i class="fas fa-check-circle bf-text-success bf-mr-2" style="font-size: 1.2rem;"></i>
                                        <strong id="selectedClientName" class="bf-selected-client-name bf-mb-0"></strong>
                                    </div>
                                    <small id="selectedClientDetails" class="bf-selected-client-details"></small>
                                </div>
                                <button type="button" class="bf-btn bf-btn-secondary bf-btn-sm bf-ml-2" id="btnClearClient" title="Change Client">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Client Search (shown when no client selected) -->
                    <div id="clientSearchContainer">
                        <div class="bf-row">
                            <div class="bf-col bf-col-12">
                                <label for="clientSearchInline" class="bf-form-label bf-font-weight-bold">
                                    <i class="fas fa-user bf-mr-2"></i>Client <span class="bf-text-danger">*</span>
                                </label>
                                <div class="bf-client-search-wrapper">
                                    <div class="bf-input-group">
                                        <div class="bf-input-group-prepend">
                                            <span class="bf-input-group-text">
                                                <i class="fas fa-search bf-text-muted" id="searchIcon"></i>
                                            </span>
                                        </div>
                                        <input type="text" 
                                               class="bf-form-control @error('clientid') is-invalid @enderror" 
                                               id="clientSearchInline" 
                                               placeholder="Search client..." 
                                               autocomplete="off">
                                        <div class="bf-input-group-append">
                                            <button type="button" class="bf-btn bf-btn-primary" id="btnSearchSelectClient" data-toggle="modal" data-target="#searchClientModal" title="Advanced Search">
                                                <i class="fas fa-search-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Inline Search Results Dropdown - Fixed Positioning -->
                                    <div id="inlineClientResults" class="bf-client-results-dropdown" style="display: none;">
                                        <div class="bf-card-main">
                                            <div class="bf-card-body" style="padding: 8px;">
                                                <div id="inlineClientResultsList"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('clientid')
                                    <div class="bf-text-danger bf-mt-2">{{ $message }}</div>
                                @enderror
                                
                                <div class="bf-d-flex bf-justify-content-end bf-mt-2">
                                    <button type="button" class="bf-btn bf-btn-success bf-btn-sm" data-toggle="modal" data-target="#createClientModal" title="Create New Client">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="bf-form-section bf-section-view-default">
                    <h6 class="bf-form-section-title">
                        <i class="fas fa-calendar-alt bf-form-section-title-icon"></i>Details
                    </h6>
                    <div class="bf-row">
                        <div class="bf-col bf-col-6">
                            <div class="bf-form-group">
                                <label for="date_book" class="bf-form-label bf-font-weight-bold">
                                    <i class="fas fa-calendar-alt bf-mr-2"></i>Date & Time <span class="bf-text-danger">*</span>
                                </label>
                                <input type="datetime-local" 
                                       class="bf-form-control @error('date_book') is-invalid @enderror" 
                                       id="date_book" 
                                       name="date_book" 
                                       value="{{ old('date_book', now()->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('date_book')
                                    <div class="bf-text-danger bf-mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="bf-col bf-col-6">
                            <div class="bf-form-group">
                                <div class="bf-d-flex bf-justify-content-between bf-align-items-center bf-mb-3">
                                    <label for="type" class="bf-form-label bf-font-weight-bold bf-mb-0">
                                        <i class="fas fa-tag bf-mr-2"></i>Type
                                    </label>
                                    <!-- View Switcher -->
                                    <div class="bf-view-switcher" style="padding: 6px 10px;">
                                        <div class="bf-d-flex bf-align-items-center bf-gap-2">
                                            <button type="button" class="bf-view-btn bf-view-btn-active" data-view="icon" onclick="switchBookingTypeView('icon')" title="Icon View">
                                                <i class="fas fa-th-large"></i>
                                            </button>
                                            <button type="button" class="bf-view-btn" data-view="list" onclick="switchBookingTypeView('list')" title="List View">
                                                <i class="fas fa-list"></i>
                                            </button>
                                            <button type="button" class="bf-view-btn" data-view="card" onclick="switchBookingTypeView('card')" title="Card View">
                                                <i class="fas fa-th"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hidden select for form submission -->
                                <select class="bf-form-control @error('type') is-invalid @enderror" id="type" name="type" style="display: none;">
                                    <option value="1" {{ old('type', 1) == 1 ? 'selected' : '' }}>Regular</option>
                                    <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>Special</option>
                                    <option value="3" {{ old('type') == 3 ? 'selected' : '' }}>Temporary</option>
                                </select>
                                
                                <!-- Icon View -->
                                <div class="bf-booking-type-view bf-booking-type-icon-view bf-active" data-view="icon">
                                    <div class="bf-booking-type-options-icon">
                                        <div class="bf-booking-type-option-icon {{ old('type', 1) == 1 ? 'bf-selected' : '' }}" data-value="1" onclick="selectBookingType(1)" title="Regular Booking">
                                            <div class="bf-booking-type-icon-wrapper">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                        </div>
                                        <div class="bf-booking-type-option-icon {{ old('type') == 2 ? 'bf-selected' : '' }}" data-value="2" onclick="selectBookingType(2)" title="Special Booking">
                                            <div class="bf-booking-type-icon-wrapper">
                                                <i class="fas fa-star"></i>
                                            </div>
                                        </div>
                                        <div class="bf-booking-type-option-icon {{ old('type') == 3 ? 'bf-selected' : '' }}" data-value="3" onclick="selectBookingType(3)" title="Temporary Booking">
                                            <div class="bf-booking-type-icon-wrapper">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- List View -->
                                <div class="bf-booking-type-view bf-booking-type-list-view" data-view="list">
                                    <div class="bf-booking-type-options-list">
                                        <div class="bf-booking-type-option-list {{ old('type', 1) == 1 ? 'bf-selected' : '' }}" data-value="1" onclick="selectBookingType(1)">
                                            <div class="bf-booking-type-list-icon">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                            <div class="bf-booking-type-list-content">
                                                <div class="bf-booking-type-list-title">Regular</div>
                                            </div>
                                            <div class="bf-booking-type-list-check">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                        <div class="bf-booking-type-option-list {{ old('type') == 2 ? 'bf-selected' : '' }}" data-value="2" onclick="selectBookingType(2)">
                                            <div class="bf-booking-type-list-icon">
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="bf-booking-type-list-content">
                                                <div class="bf-booking-type-list-title">Special</div>
                                            </div>
                                            <div class="bf-booking-type-list-check">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                        <div class="bf-booking-type-option-list {{ old('type') == 3 ? 'bf-selected' : '' }}" data-value="3" onclick="selectBookingType(3)">
                                            <div class="bf-booking-type-list-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div class="bf-booking-type-list-content">
                                                <div class="bf-booking-type-list-title">Temporary</div>
                                            </div>
                                            <div class="bf-booking-type-list-check">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Card View -->
                                <div class="bf-booking-type-view bf-booking-type-card-view" data-view="card">
                                    <div class="bf-booking-type-options-card">
                                        <div class="bf-booking-type-option-card {{ old('type', 1) == 1 ? 'bf-selected' : '' }}" data-value="1" onclick="selectBookingType(1)">
                                            <div class="bf-booking-type-card-header">
                                                <div class="bf-booking-type-card-icon">
                                                    <i class="fas fa-calendar-check"></i>
                                                </div>
                                                <div class="bf-booking-type-card-badge">Standard</div>
                                            </div>
                                            <div class="bf-booking-type-card-title">Regular</div>
                                            <div class="bf-booking-type-card-check">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="bf-booking-type-option-card {{ old('type') == 2 ? 'bf-selected' : '' }}" data-value="2" onclick="selectBookingType(2)">
                                            <div class="bf-booking-type-card-header">
                                                <div class="bf-booking-type-card-icon">
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <div class="bf-booking-type-card-badge bf-premium">Premium</div>
                                            </div>
                                            <div class="bf-booking-type-card-title">Special</div>
                                            <div class="bf-booking-type-card-check">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="bf-booking-type-option-card {{ old('type') == 3 ? 'bf-selected' : '' }}" data-value="3" onclick="selectBookingType(3)">
                                            <div class="bf-booking-type-card-header">
                                                <div class="bf-booking-type-card-icon">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div class="bf-booking-type-card-badge bf-temp">Temporary</div>
                                            </div>
                                            <div class="bf-booking-type-card-title">Temporary</div>
                                            <div class="bf-booking-type-card-check">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @error('type')
                                    <div class="bf-text-danger bf-mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Zone Selection -->
                @if(isset($boothsByCategory) && count($boothsByCategory) > 0)
                <div class="bf-form-section">
                    <h6 class="bf-form-section-title">
                        <i class="fas fa-th-large bf-form-section-title-icon"></i>Zone
                    </h6>
                    <div class="bf-zone-selector">
                        <button type="button" class="bf-zone-btn bf-zone-btn-active" data-zone="all" onclick="filterByZone('all')" title="All Zones">
                            <i class="fas fa-th"></i>
                            <span class="bf-zone-count">{{ $booths->count() }}</span>
                        </button>
                        @foreach($boothsByCategory as $zoneKey => $zoneData)
                            @php
                                $zoneName = $zoneData['category']->name;
                                $zoneBooths = $zoneData['booths'];
                                $zoneCount = $zoneBooths->count();
                            @endphp
                            <button type="button" class="bf-zone-btn" data-zone="{{ $zoneName }}" onclick="filterByZone('{{ $zoneName }}')" title="Zone {{ $zoneName }}">
                                <i class="fas fa-cube"></i>
                                <span class="bf-zone-label">{{ $zoneName }}</span>
                                <span class="bf-zone-count">{{ $zoneCount }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Booth Selection -->
                <div class="bf-form-section bf-section-view-default">
                    <div class="bf-d-flex bf-justify-content-between bf-align-items-center bf-mb-4">
                        <div>
                            <h6 class="bf-form-section-title bf-mb-0">
                                <i class="fas fa-cube bf-form-section-title-icon"></i>Booths <span class="bf-text-danger">*</span>
                            </h6>
                            <div class="bf-booth-info bf-mt-2">
                                <small class="bf-text-muted">
                                    <i class="fas fa-info-circle bf-mr-2"></i>
                                    <strong id="bf-total-booths-count">{{ $booths->count() }}</strong> <span id="bf-booths-text">{{ $booths->count() == 1 ? 'booth' : 'booths' }}</span>
                                    @if(isset($currentFloorPlan) && $currentFloorPlan)
                                        · <strong>{{ $currentFloorPlan->name }}</strong>
                                    @endif
                                    <span id="bf-zone-filter-text" class="bf-ml-2" style="display: none;">
                                        · Zone <strong id="bf-selected-zone-name"></strong>
                                    </span>
                                </small>
                                <div class="bf-d-flex bf-gap-2">
                                    <button type="button" class="bf-interactive-icon bf-icon-refresh" data-tooltip="Refresh Booths" onclick="refreshBooths()" title="Refresh booth list">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    @if(isset($floorPlans) && $floorPlans->count() > 0)
                                    <button type="button" class="bf-interactive-icon bf-icon-filter" data-tooltip="Filter by Floor Plan" data-toggle="modal" data-target="#floorPlanModal" title="Filter by floor plan">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                    @endif
                                    @if(isset($currentFloorPlan) && $currentFloorPlan)
                                    <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $currentFloorPlan->id]) }}" class="bf-interactive-icon bf-icon-view" data-tooltip="View Floor Plan" title="View floor plan canvas">
                                        <i class="fas fa-map"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bf-d-flex bf-gap-2">
                            <button type="button" class="bf-btn bf-btn-primary bf-btn-sm" onclick="selectAllBooths()" title="Select All Booths">
                                <i class="fas fa-check-double"></i>
                            </button>
                            <button type="button" class="bf-btn bf-btn-secondary bf-btn-sm" onclick="clearSelection()" title="Clear Selection">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="bf-row">
                        <div class="bf-col bf-col-8">
                            <!-- View Switcher -->
                            <div class="bf-view-switcher bf-mb-3">
                                <div class="bf-d-flex bf-align-items-center bf-gap-2">
                                    <button type="button" class="bf-view-btn bf-view-btn-active" data-view="grid" onclick="switchBoothView('grid')" title="Grid View">
                                        <i class="fas fa-th"></i>
                                    </button>
                                    <button type="button" class="bf-view-btn" data-view="list" onclick="switchBoothView('list')" title="List View">
                                        <i class="fas fa-list"></i>
                                    </button>
                                    <button type="button" class="bf-view-btn" data-view="card" onclick="switchBoothView('card')" title="Card View">
                                        <i class="fas fa-th-large"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="bf-booth-selector" id="boothSelector">
                                @if($booths->count() > 0)
                                    <div class="bf-row bf-booths-container bf-view-grid" id="bf-booths-container">
                                        @foreach($booths as $booth)
                                        @php
                                            // Extract zone from booth number (first letter)
                                            $boothNumber = trim($booth->booth_number);
                                            $firstChar = strtoupper(substr($boothNumber, 0, 1));
                                            $boothZone = ctype_alpha($firstChar) ? $firstChar : '#';
                                        @endphp
                                        <div class="bf-col bf-col-6 bf-booth-item-wrapper" data-zone="{{ $boothZone }}">
                                            <div class="bf-booth-option" data-booth-id="{{ $booth->id }}" data-price="{{ $booth->price }}" data-zone="{{ $boothZone }}">
                                                <label class="bf-booth-label">
                                                    <input type="checkbox" 
                                                           name="booth_ids[]" 
                                                           value="{{ $booth->id }}" 
                                                           class="bf-booth-checkbox"
                                                           {{ in_array($booth->id, old('booth_ids', [])) ? 'checked' : '' }}
                                                           onchange="updateSelection()">
                                                    <div class="bf-booth-content">
                                                        <div class="bf-booth-header">
                                                            <div>
                                                                <strong class="bf-text-primary bf-booth-number">{{ $booth->booth_number }}</strong>
                                                                <span class="bf-badge bf-badge-{{ $booth->getStatusColor() == 'success' ? 'success' : ($booth->getStatusColor() == 'warning' ? 'warning' : ($booth->getStatusColor() == 'danger' ? 'danger' : 'info')) }} bf-ml-2">
                                                                    {{ $booth->getStatusLabel() }}
                                                                </span>
                                                            </div>
                                                            <strong class="bf-text-success bf-booth-price">${{ number_format($booth->price, 2) }}</strong>
                                                        </div>
                                                        @if($booth->category)
                                                        <div class="bf-booth-category">
                                                            <small class="bf-text-muted">
                                                                <i class="fas fa-folder bf-mr-2"></i>{{ $booth->category->name }}
                                                            </small>
                                                        </div>
                                                        @endif
                                                        @if($booth->floorPlan)
                                                        <div class="bf-booth-floor-plan">
                                                            <small class="bf-text-muted bf-floor-plan-label">
                                                                <i class="fas fa-map-marked-alt bf-mr-2"></i>{{ $booth->floorPlan->name }}
                                                            </small>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bf-alert bf-alert-warning bf-text-center" style="padding: 32px;">
                                        <i class="fas fa-exclamation-triangle bf-mr-2" style="font-size: 2rem;"></i>
                                        <p class="bf-mb-0 bf-mt-2 bf-font-weight-bold">No available booths found.</p>
                                    </div>
                                @endif
                            </div>
                            @error('booth_ids')
                                <div class="bf-text-danger bf-mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="bf-col bf-col-4">
                            <div class="bf-booths-summary">
                                <h6 class="bf-booths-summary-title">
                                    <i class="fas fa-list bf-booths-summary-title-icon"></i>Selected Booths
                                </h6>
                                <div id="selectedBoothsList" class="bf-booths-list">
                                    <p class="bf-text-muted bf-text-center bf-mb-0" style="padding: 32px 0;">No booths selected</p>
                                </div>
                                <hr class="bf-summary-divider">
                                <div class="bf-summary-row bf-summary-row-total">
                                    <strong class="bf-summary-label">
                                        <i class="fas fa-cube bf-mr-2"></i>Total Booths:
                                    </strong>
                                    <span id="totalBooths" class="bf-badge bf-badge-primary">0</span>
                                </div>
                                <div class="bf-summary-row bf-summary-row-amount">
                                    <strong class="bf-summary-label">
                                        <i class="fas fa-dollar-sign bf-mr-2"></i>Total Amount:
                                    </strong>
                                    <span id="totalAmount" class="bf-summary-value bf-summary-value-amount">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="bf-text-muted bf-mt-2">
                        <i class="fas fa-info-circle bf-mr-2"></i>Click on booths to select them. You can select multiple booths.
                    </small>
                </div>
            </div>
            <div class="bf-card-footer">
                <button type="submit" class="bf-btn bf-btn-primary" id="submitBtn" title="Create Booking">
                    <i class="fas fa-save"></i>
                </button>
                <a href="{{ route('books.index') }}" class="bf-btn bf-btn-secondary">Cancel</a>
                <span id="selectionWarning" class="bf-text-danger bf-ml-2" style="display: none; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle bf-mr-2"></i>Please select at least one booth
                </span>
            </div>
        </form>
        </div>
    </div>
</div>
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
                            <button type="button" class="btn btn-secondary" id="btnClearClientSearch" style="display: none;">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Floor Plan Selection Modal -->
<div class="modal fade" id="floorPlanModal" tabindex="-1" role="dialog" aria-labelledby="floorPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 20px 24px;">
                <h5 class="modal-title" id="floorPlanModalLabel" style="font-weight: 600; font-size: 18px;">
                    <i class="fas fa-map-marked-alt mr-2"></i>Select Floor Plan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
                @if(isset($floorPlans) && $floorPlans->count() > 0)
                <div class="bf-floor-plan-cards">
                    <!-- All Floor Plans Option -->
                    <div class="bf-floor-plan-card {{ !isset($floorPlanId) || $floorPlanId == '' ? 'bf-selected' : '' }}" onclick="selectFloorPlan('')" style="cursor: pointer;">
                        <div class="bf-floor-plan-card-header">
                            <div class="bf-floor-plan-card-icon">
                                <i class="fas fa-th"></i>
                            </div>
                            <div class="bf-floor-plan-card-badge bf-badge-all">All</div>
                        </div>
                        <div class="bf-floor-plan-card-title">All Floor Plans</div>
                        <div class="bf-floor-plan-card-info">
                            <div class="bf-floor-plan-info-item">
                                <i class="fas fa-cube"></i>
                                <span>{{ $booths->count() }} Booths</span>
                            </div>
                        </div>
                        <div class="bf-floor-plan-card-check">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    
                    @foreach($floorPlans as $fp)
                    @php
                        $fpBooths = \App\Models\Booth::where('floor_plan_id', $fp->id)->whereIn('status', [\App\Models\Booth::STATUS_AVAILABLE, \App\Models\Booth::STATUS_HIDDEN])->get();
                        $fpStats = $fp->getStats();
                    @endphp
                    <div class="bf-floor-plan-card {{ (isset($floorPlanId) && $floorPlanId == $fp->id) ? 'bf-selected' : '' }}" onclick="selectFloorPlan('{{ $fp->id }}')" style="cursor: pointer;">
                        <div class="bf-floor-plan-card-header">
                            <div class="bf-floor-plan-card-icon">
                                <i class="fas fa-map"></i>
                            </div>
                            @if($fp->is_default)
                            <div class="bf-floor-plan-card-badge bf-badge-default">Default</div>
                            @endif
                        </div>
                        <div class="bf-floor-plan-card-title">{{ $fp->name }}</div>
                        <div class="bf-floor-plan-card-info">
                            @if($fp->event)
                            <div class="bf-floor-plan-info-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $fp->event->title }}</span>
                            </div>
                            @endif
                            <div class="bf-floor-plan-info-item">
                                <i class="fas fa-cube"></i>
                                <span>{{ $fpStats['total'] }} Total</span>
                            </div>
                            <div class="bf-floor-plan-info-item">
                                <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                <span>{{ $fpStats['available'] }} Available</span>
                            </div>
                            @if($fpStats['occupied'] > 0)
                            <div class="bf-floor-plan-info-item">
                                <i class="fas fa-user-check" style="color: #667eea;"></i>
                                <span>{{ $fpStats['occupied'] }} Occupied</span>
                            </div>
                            @endif
                        </div>
                        <div class="bf-floor-plan-card-check">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bf-alert bf-alert-warning bf-text-center" style="padding: 32px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem;"></i>
                    <p class="bf-mb-0 bf-mt-2 bf-font-weight-bold">No floor plans available.</p>
                </div>
                @endif
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
<script>
function filterByFloorPlan(floorPlanId) {
    // Clear zone selection when floor plan changes
    localStorage.removeItem('bf-selected-zone');
    localStorage.setItem('bf-selected-floor-plan-id', floorPlanId || '');
    
    if (floorPlanId) {
        window.location.href = '{{ route("books.create") }}?floor_plan_id=' + floorPlanId;
    } else {
        window.location.href = '{{ route("books.create") }}';
    }
}

// Select floor plan from modal
function selectFloorPlan(floorPlanId) {
    // Update selected state in modal
    $('.bf-floor-plan-card').removeClass('bf-selected');
    if (floorPlanId === '') {
        $('.bf-floor-plan-card').first().addClass('bf-selected');
    } else {
        $('.bf-floor-plan-card').each(function() {
            const onclickAttr = $(this).attr('onclick');
            if (onclickAttr && onclickAttr.includes("'" + floorPlanId + "'")) {
                $(this).addClass('bf-selected');
            }
        });
    }
    
    // Close modal
    $('#floorPlanModal').modal('hide');
    
    // Apply filter
    filterByFloorPlan(floorPlanId);
}

// Select floor plan from modal
function selectFloorPlan(floorPlanId) {
    // Update selected state in modal
    $('.bf-floor-plan-card').removeClass('bf-selected');
    if (floorPlanId === '') {
        $('.bf-floor-plan-card').first().addClass('bf-selected');
    } else {
        $('.bf-floor-plan-card').each(function() {
            if ($(this).attr('onclick').includes("'" + floorPlanId + "'")) {
                $(this).addClass('bf-selected');
            }
        });
    }
    
    // Close modal
    $('#floorPlanModal').modal('hide');
    
    // Apply filter
    filterByFloorPlan(floorPlanId);
}

// Refresh booths list
function refreshBooths() {
    const floorPlanId = $('#floor_plan_filter').val() || '';
    const url = floorPlanId 
        ? '{{ route("books.create") }}?floor_plan_id=' + floorPlanId
        : '{{ route("books.create") }}';
    
    // Show loading state
    const refreshIcon = $('.bf-icon-refresh i');
    refreshIcon.addClass('fa-spin');
    
    // Save current zone selection before refresh
    const currentZone = $('.bf-zone-btn.bf-zone-btn-active').data('zone') || 'all';
    localStorage.setItem('bf-selected-zone', currentZone);
    
    // Reload page after a brief delay to show animation
    setTimeout(function() {
        window.location.href = url;
    }, 300);
}

// Filter booths by zone
function filterByZone(zoneName) {
    // Save selected zone to localStorage
    localStorage.setItem('bf-selected-zone', zoneName);
    
    // Apply zone filter with animation
    applyZoneFilter(zoneName, true);
}

// Switch booth view layout
function switchBoothView(viewType) {
    // Save view preference
    localStorage.setItem('bf-booth-view', viewType);
    
    // Update active view button (only in booth selector section)
    $('.bf-view-switcher.bf-mb-3 ~ .bf-booth-selector').siblings('.bf-view-switcher').find('.bf-view-btn').removeClass('bf-view-btn-active');
    $('.bf-view-switcher.bf-mb-3').find('.bf-view-btn').removeClass('bf-view-btn-active');
    $('.bf-view-switcher.bf-mb-3').find('.bf-view-btn[data-view="' + viewType + '"]').addClass('bf-view-btn-active');
    
    // Update container class
    const $container = $('#bf-booths-container');
    $container.removeClass('bf-view-grid bf-view-list bf-view-card');
    $container.addClass('bf-view-' + viewType);
    
    // Update column classes based on view with smooth transition
    const $wrappers = $('.bf-booth-item-wrapper:not(.bf-hidden)');
    
    $wrappers.css({
        'opacity': '0.5',
        'transform': 'scale(0.98)'
    });
    
    setTimeout(function() {
        if (viewType === 'grid') {
            // Grid: 2 columns
            $wrappers.removeClass('bf-col-6 bf-col-12 bf-col-4').addClass('bf-col bf-col-6');
        } else if (viewType === 'list') {
            // List: 1 column (full width)
            $wrappers.removeClass('bf-col-6 bf-col-12 bf-col-4').addClass('bf-col bf-col-12');
        } else if (viewType === 'card') {
            // Card: 3 columns
            $wrappers.removeClass('bf-col-6 bf-col-12 bf-col-4').addClass('bf-col bf-col-4');
        }
        
        $wrappers.css({
            'opacity': '1',
            'transform': 'scale(1)'
        });
    }, 150);
}

// Restore view preference on page load
function restoreBoothView() {
    const savedView = localStorage.getItem('bf-booth-view') || 'grid';
    
    // Wait for DOM to be ready
    setTimeout(function() {
        const $viewBtn = $('.bf-view-switcher.bf-mb-3').find('.bf-view-btn[data-view="' + savedView + '"]');
        if ($viewBtn.length > 0) {
            switchBoothView(savedView);
        } else {
            // Default to grid if saved view doesn't exist
            switchBoothView('grid');
        }
    }, 100);
}

// Switch booking type view layout
function switchBookingTypeView(viewType) {
    // Save view preference
    localStorage.setItem('bf-booking-type-view', viewType);
    
    // Update active view button (only in booking type section)
    $('.bf-form-group .bf-view-switcher .bf-view-btn').removeClass('bf-view-btn-active');
    $('.bf-form-group .bf-view-switcher .bf-view-btn[data-view="' + viewType + '"]').addClass('bf-view-btn-active');
    
    // Hide all views
    $('.bf-booking-type-view').removeClass('bf-active');
    
    // Show selected view
    $('.bf-booking-type-view[data-view="' + viewType + '"]').addClass('bf-active');
}

// Select booking type
function selectBookingType(value) {
    // Update hidden select
    $('#type').val(value);
    
    // Remove selected class from all options
    $('.bf-booking-type-option-icon, .bf-booking-type-option-list, .bf-booking-type-option-card').removeClass('bf-selected');
    
    // Add selected class to clicked option in all views
    $('.bf-booking-type-option-icon[data-value="' + value + '"], .bf-booking-type-option-list[data-value="' + value + '"], .bf-booking-type-option-card[data-value="' + value + '"]').addClass('bf-selected');
}

// Restore booking type view preference on page load
function restoreBookingTypeView() {
    const savedView = localStorage.getItem('bf-booking-type-view') || 'icon';
    
    // Wait for DOM to be ready
    setTimeout(function() {
        if ($('.bf-form-group .bf-view-switcher .bf-view-btn[data-view="' + savedView + '"]').length > 0) {
            switchBookingTypeView(savedView);
        } else {
            // Default to icon if saved view doesn't exist
            switchBookingTypeView('icon');
        }
    }, 100);
}

// Switch card body layout
function switchCardLayout(layoutType) {
    // Save layout preference
    localStorage.setItem('bf-card-layout', layoutType);
    
    // Update active layout button
    $('.bf-layout-btn').removeClass('bf-layout-btn-active');
    $('.bf-layout-btn[data-layout="' + layoutType + '"]').addClass('bf-layout-btn-active');
    
    // Update card body class
    const $cardBody = $('#bfCardBody');
    $cardBody.removeClass('bf-layout-default bf-layout-minimal bf-layout-expand');
    $cardBody.addClass('bf-layout-' + layoutType);
}

// Restore card layout preference on page load
function restoreCardLayout() {
    const savedLayout = localStorage.getItem('bf-card-layout') || 'default';
    
    // Wait for DOM to be ready
    setTimeout(function() {
        const $layoutBtn = $('.bf-layout-btn[data-layout="' + savedLayout + '"]');
        if ($layoutBtn.length > 0) {
            switchCardLayout(savedLayout);
        } else {
            // Default to default if saved layout doesn't exist
            switchCardLayout('default');
        }
    }, 100);
}

// Switch form section view
function switchFormSectionView(viewType) {
    // Save view preference
    localStorage.setItem('bf-section-view', viewType);
    
    // Update active view button
    $('.bf-section-view-btn').removeClass('bf-section-view-btn-active');
    $('.bf-section-view-btn[data-view="' + viewType + '"]').addClass('bf-section-view-btn-active');
    
    // Update all form sections
    const $sections = $('.bf-form-section');
    $sections.removeClass('bf-section-view-default bf-section-view-tiny bf-section-view-compact bf-section-view-expanded');
    $sections.addClass('bf-section-view-' + viewType);
}

// Restore form section view preference on page load
function restoreFormSectionView() {
    const savedView = localStorage.getItem('bf-section-view') || 'default';
    
    // Wait for DOM to be ready
    setTimeout(function() {
        const $viewBtn = $('.bf-section-view-btn[data-view="' + savedView + '"]');
        if ($viewBtn.length > 0) {
            switchFormSectionView(savedView);
        } else {
            // Default to default if saved view doesn't exist
            switchFormSectionView('default');
        }
    }, 100);
}

// Restore zone selection on page load
function restoreZoneSelection() {
    const savedZone = localStorage.getItem('bf-selected-zone');
    const floorPlanId = $('#floor_plan_filter').val() || '';
    const currentFloorPlanId = '{{ isset($floorPlanId) ? $floorPlanId : "" }}';
    const actualFloorPlanId = floorPlanId || currentFloorPlanId;
    
    // Check if floor plan has changed (if so, reset zone filter)
    const savedFloorPlanId = localStorage.getItem('bf-selected-floor-plan-id');
    if (savedFloorPlanId && savedFloorPlanId !== actualFloorPlanId && actualFloorPlanId !== '') {
        // Floor plan changed, reset zone filter
        localStorage.removeItem('bf-selected-zone');
        localStorage.setItem('bf-selected-floor-plan-id', actualFloorPlanId);
        return;
    }
    
    // Save current floor plan ID
    if (actualFloorPlanId) {
        localStorage.setItem('bf-selected-floor-plan-id', actualFloorPlanId);
    }
    
    // Wait for zone buttons to be available
    setTimeout(function() {
        // Restore zone selection if exists and zone button is available
        if (savedZone && $('.bf-zone-btn[data-zone="' + savedZone + '"]').length > 0) {
            // Apply zone filter without animation on initial load
            applyZoneFilter(savedZone, false);
        } else {
            // No saved zone or zone doesn't exist, default to 'all'
            localStorage.setItem('bf-selected-zone', 'all');
            applyZoneFilter('all', false);
        }
    }, 200);
}

// Apply zone filter (with optional animation)
function applyZoneFilter(zoneName, animate = true) {
    // Update active zone button
    $('.bf-zone-btn').removeClass('bf-zone-btn-active');
    $('.bf-zone-btn[data-zone="' + zoneName + '"]').addClass('bf-zone-btn-active');
    
    // Filter booth items
    const boothWrappers = $('.bf-booth-item-wrapper');
    let visibleCount = 0;
    
    if (zoneName === 'all') {
        // Show all booths
        boothWrappers.each(function() {
            const $wrapper = $(this);
            $wrapper.removeClass('bf-hidden');
            if (animate) {
                $wrapper.fadeIn(300);
            } else {
                $wrapper.show();
            }
            visibleCount++;
        });
        $('#bf-zone-filter-text').hide();
    } else {
        // Show only booths in selected zone
        boothWrappers.each(function() {
            const $wrapper = $(this);
            if ($wrapper.data('zone') === zoneName) {
                $wrapper.removeClass('bf-hidden');
                if (animate) {
                    $wrapper.fadeIn(300);
                } else {
                    $wrapper.show();
                }
                visibleCount++;
            } else {
                $wrapper.addClass('bf-hidden');
                if (animate) {
                    $wrapper.fadeOut(200);
                } else {
                    $wrapper.hide();
                }
            }
        });
        
        // Update zone filter text
        $('#bf-selected-zone-name').text(zoneName);
        if (animate) {
            $('#bf-zone-filter-text').fadeIn(200);
        } else {
            $('#bf-zone-filter-text').show();
        }
    }
    
    // Update booth count display
    if (animate) {
        const $countElement = $('#bf-total-booths-count');
        const $textElement = $('#bf-booths-text');
        
        $countElement.fadeOut(100, function() {
            $(this).text(visibleCount).fadeIn(100);
        });
        
        $textElement.fadeOut(100, function() {
            $(this).text(visibleCount === 1 ? 'booth' : 'booths').fadeIn(100);
        });
    } else {
        $('#bf-total-booths-count').text(visibleCount);
        $('#bf-booths-text').text(visibleCount === 1 ? 'booth' : 'booths');
    }
    
    // Update selection if needed
    if (animate) {
        setTimeout(function() {
            updateSelection();
        }, 350);
    } else {
        updateSelection();
    }
}

// Handle Create Client Modal Form Submission
$(document).ready(function() {
    $('#createClientForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createClientSubmitBtn');
        const errorDiv = $('#createClientError');
        const originalText = submitBtn.html();
        
        // Hide error message
        errorDiv.hide();
        
        // Remove HTML5 validation since all fields are optional
        // Validate form
        // if (!form[0].checkValidity()) {
        //     form[0].reportValidity();
        //     return;
        // }
        
        // Disable submit button and show loading
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
                if (response.status === 'success' && response.client) {
                    const client = response.client;
                    
                    // Select the newly created client using the selectClient function
                    if (typeof selectClient === 'function') {
                        selectClient(client);
                    } else {
                        // Fallback if selectClient is not available
                        $('#clientid').val(client.id);
                        const displayText = client.company + (client.name ? ' - ' + client.name : '') + 
                                         (client.email ? ' (' + client.email + ')' : '') + 
                                         (client.phone_number ? ' | ' + client.phone_number : '');
                        $('#clientSearchInline').val(displayText);
                        $('#selectedClientName').text(client.company || client.name);
                        let details = [];
                        if (client.name && client.company) details.push(client.name);
                        if (client.email) details.push(client.email);
                        if (client.phone_number) details.push(client.phone_number);
                        $('#selectedClientDetails').text(details.join(' • '));
                        $('#selectedClientInfo').show();
                    }
                    
                    // Close modal and reset form
                    $('#createClientModal').modal('hide');
                    form[0].reset();
                    errorDiv.hide();
                    
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Client Created!',
                            text: 'Client "' + client.company + '" has been created and selected.',
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
                // Re-enable submit button
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Reset form when modal is closed
    $('#createClientModal').on('hidden.bs.modal', function() {
        $('#createClientForm')[0].reset();
        $('#createClientError').hide();
    });
});

    // Client Search & Select Functionality
$(document).ready(function() {
    // Restore card layout preference on page load
    restoreCardLayout();
    
    // Restore form section view preference on page load
    restoreFormSectionView();
    
    // Restore booth view preference on page load
    restoreBoothView();
    
    // Restore booking type view preference on page load
    restoreBookingTypeView();
    
    // Restore zone selection on page load
    restoreZoneSelection();
    
    let clientSearchTimeout;
    let inlineSearchTimeout;
    let selectedClient = null;
    
    // Initialize - check if client is already selected
    @if(old('clientid'))
        const oldClientId = {{ old('clientid') }};
        // Try to find and display the selected client
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
    
    // Inline Client Search - Auto-suggest function
    function searchClientsInline(query) {
        if (!query || query.length < 2) {
            $('#inlineClientResults').hide();
            return;
        }
        
        console.log('searchClientsInline called with query:', query);
        
        // Show loading indicator
        const resultsDiv = $('#inlineClientResults');
        const resultsList = $('#inlineClientResultsList');
        const searchIcon = $('#searchIcon');
        
        // Update search icon to show loading
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
        
        const searchUrl = '{{ route("clients.search") }}';
        console.log('Making AJAX request to:', searchUrl, 'with query:', query);
        
        $.ajax({
            url: searchUrl,
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(clients) {
                console.log('Inline search successful, clients found:', clients);
                
                // Reset search icon
                const searchIcon = $('#searchIcon');
                if (searchIcon.length) {
                    searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
                
                resultsList.empty();
                
                // Handle both array and object responses
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
                
                // Show up to 8 results for better visibility
                clientsArray.slice(0, 8).forEach(function(client, index) {
                    const displayName = (client.company || client.name || 'N/A');
                    const highlightQuery = query.toLowerCase();
                    
                    // Highlight matching text
                    let highlightedName = displayName;
                    if (highlightQuery) {
                        // Escape special regex characters
                        const escapedQuery = highlightQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                        const regex = new RegExp(`(${escapedQuery})`, 'gi');
                        highlightedName = displayName.replace(regex, '<mark>$1</mark>');
                    }
                    
                    // Build details HTML
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
                    
                    // Build result item
                    const item = $('<div class="bf-client-result"></div>')
                        .html(
                            '<div class="client-result-content">' +
                                '<div class="client-result-name">' +
                                    '<i class="fas fa-building"></i>' +
                                    '<span>' + highlightedName + '</span>' +
                                '</div>' +
                                detailsHTML +
                            '</div>' +
                            '<button type="button" class="bf-btn bf-btn-primary bf-btn-sm" data-client-id="' + client.id + '" title="Select this client">' +
                                '<i class="fas fa-check bf-mr-2"></i>Select' +
                            '</button>'
                        )
                        .data('client', client);
                    
                    resultsList.append(item);
                });
                
                // Bind inline select button click
                $(document).off('click', '#inlineClientResultsList .bf-btn').on('click', '#inlineClientResultsList .bf-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const client = $(this).closest('.bf-client-result').data('client');
                    if (client) {
                        selectClient(client);
                        $('#inlineClientResults').hide();
                    }
                });
                
                // Bind inline result item click
                $(document).off('click', '#inlineClientResultsList .bf-client-result').on('click', '#inlineClientResultsList .bf-client-result', function(e) {
                    if (!$(e.target).closest('.select-client-inline-btn').length) {
                        e.preventDefault();
                        const client = $(this).data('client');
                        if (client) {
                            selectClient(client);
                            $('#inlineClientResults').hide();
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Search error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    statusCode: xhr.status
                });
                
                // Reset search icon
                const searchIcon = $('#searchIcon');
                if (searchIcon.length) {
                    searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
                
                let errorMessage = 'Error searching clients. Please try again.';
                if (xhr.status === 401) {
                    errorMessage = 'Please refresh the page and try again.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please contact administrator.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                resultsList.html(
                    '<div class="client-results-empty" style="color: var(--booking-danger);">' +
                        '<i class="fas fa-exclamation-triangle"></i>' +
                        '<p class="mb-0"><strong>Error</strong></p>' +
                        '<p class="mb-0 mt-1" style="font-size: 0.85rem;">' + errorMessage + '</p>' +
                    '</div>'
                );
            }
        });
    }
    
    // Inline search input handler - Auto-suggest as user types
    $('#clientSearchInline').on('input keyup paste', function(e) {
        // Don't trigger on arrow keys, enter, escape, etc.
        if ([38, 40, 13, 27].includes(e.keyCode)) {
            return;
        }
        
        const query = $(this).val().trim();
        
        clearTimeout(inlineSearchTimeout);
        
        // If query is empty or too short, hide results
        if (query.length < 2) {
            $('#inlineClientResults').hide();
            // Reset search icon
            const searchIcon = $('#searchIcon');
            if (searchIcon.length) {
                searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
            }
            // If cleared, also clear selection
            if (query.length === 0 && selectedClient) {
                selectedClient = null;
                $('#clientid').val('');
                $('#selectedClientInfo').hide();
                $('#clientSearchContainer').show();
            }
            return;
        }
        
        // Auto-search after 300ms delay (debounce)
        console.log('Triggering search for query:', query);
        inlineSearchTimeout = setTimeout(function() {
            console.log('Executing searchClientsInline with query:', query);
            searchClientsInline(query);
        }, 300);
    });
    
    // Handle keyboard navigation in inline results
    $('#clientSearchInline').on('keydown', function(e) {
        const results = $('#inlineClientResults:visible');
        if (results.length === 0) return;
        
        const items = results.find('.bf-client-result');
        if (items.length === 0) return;
        
        let currentIndex = items.index(items.filter('.highlighted'));
        
        if (e.keyCode === 40) { // Down arrow
            e.preventDefault();
            items.removeClass('bf-highlighted');
            currentIndex = (currentIndex + 1) % items.length;
            items.eq(currentIndex).addClass('bf-highlighted').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else if (e.keyCode === 38) { // Up arrow
            e.preventDefault();
            items.removeClass('bf-highlighted');
            currentIndex = currentIndex <= 0 ? items.length - 1 : currentIndex - 1;
            items.eq(currentIndex).addClass('bf-highlighted').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else if (e.keyCode === 13) { // Enter
            e.preventDefault();
            const highlighted = items.filter('.bf-highlighted');
            if (highlighted.length > 0) {
                const client = highlighted.data('client');
                if (client) {
                    selectClient(client);
                    $('#inlineClientResults').hide();
                }
            } else if (items.length > 0) {
                // Select first item if none highlighted
                const client = items.first().data('client');
                if (client) {
                    selectClient(client);
                    $('#inlineClientResults').hide();
                }
            }
        } else if (e.keyCode === 27) { // Escape
            $('#inlineClientResults').hide();
        }
    });
    
    // Hide inline results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#clientSearchInline, #inlineClientResults').length) {
            $('#inlineClientResults').hide();
        }
    });
    
    // Prevent form submission when selecting from dropdown
    $('#clientSearchInline').on('keydown', function(e) {
        if (e.keyCode === 13 && $('#inlineClientResults:visible').length > 0) {
            e.preventDefault();
        }
    });
    
    // Client Search Function (for modal)
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
                console.log('Modal search successful, clients found:', clients);
                
                const resultsDiv = $('#clientSearchResults');
                const resultsList = $('#clientSearchResultsList');
                const noResultsDiv = $('#noClientResults');
                
                resultsList.empty();
                
                // Handle both array and object responses
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
                    
                    // Build details HTML
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
                    
                    // Build result item
                    const item = $('<div class="bf-client-result"></div>')
                        .html(
                            '<div class="client-result-content">' +
                                '<div class="client-result-name">' +
                                    '<i class="fas fa-building"></i>' +
                                    '<span>' + displayName + '</span>' +
                                '</div>' +
                                detailsHTML +
                            '</div>' +
                            '<button type="button" class="bf-btn bf-btn-primary bf-btn-sm" data-client-id="' + client.id + '" title="Select this client">' +
                                '<i class="fas fa-check bf-mr-2"></i>Select' +
                            '</button>'
                        )
                        .data('client', client);
                    
                    resultsList.append(item);
                });
                
                // Bind select button click
                $('#clientSearchResultsList .bf-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const client = $(this).closest('.bf-client-result').data('client');
                    selectClient(client);
                });
                
                // Bind result item click
                resultsList.find('.bf-client-result').on('click', function(e) {
                    if (!$(e.target).closest('.bf-btn').length) {
                        e.preventDefault();
                        const client = $(this).data('client');
                        selectClient(client);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('Modal client search error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    statusCode: xhr.status
                });
                $('#clientSearchResults').hide();
                $('#noClientResults').show();
            }
        });
    }
    
    // Select Client Function
    function selectClient(client) {
        selectedClient = client;
        
        // Set hidden input value
        $('#clientid').val(client.id);
        
        // Show selected client info card
        const displayName = client.company || client.name || 'N/A';
        let details = [];
        if (client.email) details.push('<i class="fas fa-envelope mr-1"></i>' + client.email);
        if (client.phone_number) details.push('<i class="fas fa-phone mr-1"></i>' + client.phone_number);
        
        $('#selectedClientName').text(displayName);
        $('#selectedClientDetails').html(details.length > 0 ? details.join(' <span class="mx-2 text-muted">|</span> ') : '');
        $('#selectedClientInfo').show();
        
        // Hide search container and clear search inputs
        $('#clientSearchContainer').hide();
        $('#clientSearchInline').val('');
        $('#inlineClientResults').hide();
        
        // Close modal if open
        $('#searchClientModal').modal('hide');
        
        // Clear modal search
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $('#btnClearClientSearch').hide();
    }
    
    // Clear Client Selection
    $('#btnClearClient').on('click', function() {
        selectedClient = null;
        $('#clientid').val('');
        $('#selectedClientInfo').hide();
        $('#clientSearchContainer').show();
        $('#clientSearchInline').val('');
        $('#inlineClientResults').hide();
        // Focus on search input
        setTimeout(function() {
            $('#clientSearchInline').focus();
        }, 100);
    });
    
    // Search input handlers
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
    
    // Reset modal when closed
    $('#searchClientModal').on('hidden.bs.modal', function() {
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $('#btnClearClientSearch').hide();
    });
    
    // Update selection on page load if there are checked boxes
    updateSelection();
    
    // Also update when checkboxes change
    $('.bf-booth-checkbox').on('change', function() {
        updateSelection();
    });
});

// Select/Deselect booth options (delegated event handler for dynamic content)
$(document).on('click', '.bf-booth-option', function(e) {
    if (e.target.type !== 'checkbox' && !$(e.target).closest('input').length) {
        const checkbox = $(this).find('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
    }
});

function updateSelection() {
    const selected = [];
    let totalAmount = 0;
    
    $('.bf-booth-checkbox:checked').each(function() {
        const boothId = $(this).val();
        const boothOption = $(this).closest('.bf-booth-option');
        const boothNumber = boothOption.find('strong').text();
        const price = parseFloat(boothOption.data('price')) || 0;
        
        selected.push({ id: boothId, number: boothNumber, price: price });
        totalAmount += price;
    });
    
    // Update selected list
    const listContainer = $('#selectedBoothsList');
    if (selected.length > 0) {
        let html = '';
        selected.forEach(function(booth) {
            html += '<div class="bf-booth-item">';
            html += '<div><i class="fas fa-cube bf-mr-2 bf-text-primary"></i><strong>' + booth.number + '</strong></div>';
            html += '<strong class="bf-text-success" style="font-size: 18px; font-weight: 600;">$' + booth.price.toFixed(2) + '</strong>';
            html += '</div>';
        });
        listContainer.html(html);
    } else {
        listContainer.html('<p class="bf-text-muted bf-text-center bf-mb-0" style="padding: 32px 0;">No booths selected</p>');
    }
    
    // Update summary
    $('#totalBooths').text(selected.length);
    $('#totalAmount').text('$' + totalAmount.toFixed(2));
    
    // Update visual state
    $('.bf-booth-option').removeClass('bf-selected');
    $('.bf-booth-checkbox:checked').closest('.bf-booth-option').addClass('bf-selected');
    
    // Show/hide warning
    if (selected.length === 0) {
        $('#selectionWarning').show();
        $('#submitBtn').prop('disabled', true);
    } else {
        $('#selectionWarning').hide();
        $('#submitBtn').prop('disabled', false);
    }
}

function selectAllBooths() {
    // Only select visible booths (respects zone filter)
    $('.bf-booth-item-wrapper:not(.bf-hidden) .bf-booth-checkbox').prop('checked', true);
    updateSelection();
}

function clearSelection() {
    $('.bf-booth-checkbox').prop('checked', false);
    updateSelection();
}

// Form validation
// Form validation before submission
$('#bookingForm').on('submit', function(e) {
    // Validate client selection first
    const clientId = $('#clientid').val();
    if (!clientId || clientId === '' || clientId === null) {
        e.preventDefault();
        e.stopPropagation();
        
        // Show error message
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
        
        // Show search container if hidden
        if ($('#clientSearchContainer').is(':hidden')) {
            $('#selectedClientInfo').hide();
            $('#clientSearchContainer').show();
        }
        
        // Focus on search input
        setTimeout(function() {
            $('#clientSearchInline').focus();
        }, 100);
        
        return false;
    }
    
    // Validate booth selection
    const selectedCount = $('.bf-booth-checkbox:checked').length;
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
    
    // If all validations pass, show loading and allow form submission
    showLoading();
});

// Initialize on page load
updateSelection();
</script>
@endpush

