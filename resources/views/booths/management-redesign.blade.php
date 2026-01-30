@extends('layouts.adminlte')

@section('title', 'Booth Management')
@section('page-title', 'Booth Management')
@section('breadcrumb', 'Booths / Management')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/modern-design-system.css') }}">
<style>
    /* Comprehensive Redesign Styles */
    :root {
        --space-mode-default: 1;
        --space-mode-minimal: 0.85;
        --space-mode-expand: 1.2;
    }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif !important;
    }
    
    /* Space Mode Styles */
    .space-mode-default { font-size: 1rem; padding: 24px; }
    .space-mode-minimal { font-size: 0.85rem; padding: 12px; }
    .space-mode-expand { font-size: 1.2rem; padding: 32px; }
    
    /* Full Width Layout */
    @media (min-width: 769px) and (max-width: 1024px) {
        .content-wrapper {
            margin-left: 200px !important;
            width: calc(100% - 200px) !important;
            max-width: calc(100% - 200px) !important;
        }
    }
    
    @media (min-width: 1025px) {
        .content-wrapper {
            margin-left: var(--sidebar-width, 250px) !important;
            width: calc(100% - var(--sidebar-width, 250px)) !important;
            max-width: calc(100% - var(--sidebar-width, 250px)) !important;
        }
    }
    
    .content .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        padding-left: 24px !important;
        padding-right: 24px !important;
    }
    
    @media (max-width: 768px) {
        .content-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .content .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid" id="boothManagementContainer">
    <!-- This is a placeholder - full implementation will follow -->
    <div class="alert alert-info">
        <h4>Redesign in Progress</h4>
        <p>Comprehensive UI redesign is being implemented. This is a placeholder file.</p>
    </div>
</div>
@endsection
