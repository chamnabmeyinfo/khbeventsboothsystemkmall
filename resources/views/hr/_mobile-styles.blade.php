{{-- Mobile-Responsive Styles for HR Module --}}
<style>
    /* Mobile Responsive Improvements */
    @media (max-width: 768px) {
        /* Tables - Make scrollable on mobile */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            font-size: 0.875rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem;
            white-space: nowrap;
        }
        
        /* Cards - Better spacing on mobile */
        .card {
            margin-bottom: 1rem;
        }
        
        .card-header {
            padding: 0.75rem 1rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        /* Buttons - Larger touch targets */
        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        /* Forms - Full width on mobile */
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-control,
        .select2-container {
            width: 100% !important;
        }
        
        /* Small boxes - Stack on mobile */
        .small-box {
            margin-bottom: 1rem;
        }
        
        /* Info boxes - Better layout */
        .info-box {
            margin-bottom: 1rem;
        }
        
        .info-box-icon {
            width: 70px;
            height: 70px;
            font-size: 1.5rem;
        }
        
        /* Filters - Stack vertically */
        .card-primary.card-outline .card-body .row > div {
            margin-bottom: 1rem;
        }
        
        /* Calendar - Better mobile view */
        .calendar-day {
            height: 80px !important;
            font-size: 0.75rem;
            padding: 3px !important;
        }
        
        .calendar-day .badge {
            font-size: 0.65rem;
            padding: 1px 3px;
            margin-bottom: 2px;
        }
        
        /* Leave calendar week view - Scrollable */
        .table-responsive table {
            min-width: 600px;
        }
        
        /* Modal - Better on mobile */
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        /* Navigation - Better mobile menu */
        .sidebar {
            transform: translateX(-100%);
        }
        
        .sidebar.show {
            transform: translateX(0);
        }
        
        /* Dashboard stats - Stack on mobile */
        .row .col-lg-3,
        .row .col-md-3,
        .row .col-md-4,
        .row .col-md-6 {
            margin-bottom: 1rem;
        }
        
        /* Employee portal dashboard */
        .user-panel {
            padding: 0.75rem;
        }
        
        /* Manager dashboard - Better mobile layout */
        .manager-dashboard .card {
            margin-bottom: 1rem;
        }
        
        /* Leave calendar filters - Stack */
        .leave-calendar-filters .col-md-2,
        .leave-calendar-filters .col-md-3,
        .leave-calendar-filters .col-md-4 {
            margin-bottom: 0.75rem;
        }
        
        /* Profile form - Better spacing */
        .profile-form .row > div {
            margin-bottom: 1rem;
        }
        
        /* Document list - Better on mobile */
        .document-list .table td {
            font-size: 0.8rem;
        }
        
        /* Attendance table - Scrollable */
        .attendance-table {
            overflow-x: auto;
        }
        
        /* Hide less important columns on mobile */
        .table-responsive .d-none-mobile {
            display: none !important;
        }
    }
    
    @media (max-width: 576px) {
        /* Extra small devices */
        .card-header h3 {
            font-size: 1rem;
        }
        
        .small-box .inner h3 {
            font-size: 1.5rem;
        }
        
        .small-box .inner p {
            font-size: 0.875rem;
        }
        
        .btn-group {
            display: flex;
            flex-direction: column;
        }
        
        .btn-group .btn {
            margin-bottom: 0.25rem;
            width: 100%;
        }
        
        /* Calendar - Even more compact */
        .calendar-day {
            height: 60px !important;
            font-size: 0.7rem;
        }
        
        .calendar-day .badge {
            font-size: 0.6rem;
            padding: 1px 2px;
        }
        
        /* Tables - Hide more columns */
        .table th:nth-child(n+4),
        .table td:nth-child(n+4) {
            display: none;
        }
        
        /* Show important columns only */
        .table th:first-child,
        .table td:first-child,
        .table th:nth-child(2),
        .table td:nth-child(2),
        .table th:last-child,
        .table td:last-child {
            display: table-cell;
        }
    }
    
    /* Touch-friendly improvements */
    @media (hover: none) and (pointer: coarse) {
        .btn {
            min-height: 44px;
            min-width: 44px;
        }
        
        .nav-link {
            padding: 0.75rem 1rem;
        }
        
        .table tbody tr {
            cursor: pointer;
        }
        
        /* Larger tap targets */
        .badge {
            padding: 0.35em 0.65em;
        }
    }
    
    /* Print styles */
    @media print {
        .sidebar,
        .main-header,
        .content-header,
        .card-header .card-tools,
        .btn {
            display: none !important;
        }
        
        .content-wrapper {
            margin-left: 0 !important;
        }
    }
</style>
