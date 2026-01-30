@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-cog me-2"></i>Settings</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-broom me-2"></i>Cache Management</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Clear various caches to ensure the application is using the latest data and configurations.</p>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-database me-2 text-primary"></i>Application Cache
                                </h6>
                                <p class="card-text text-muted small">Clear the application cache (stored data, queries, etc.)</p>
                                <form action="{{ route('settings.cache.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-trash me-1"></i>Clear Cache
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-file-code me-2 text-info"></i>Configuration Cache
                                </h6>
                                <p class="card-text text-muted small">Clear the configuration cache (config files)</p>
                                <form action="{{ route('settings.config.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info">
                                        <i class="fas fa-trash me-1"></i>Clear Config
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-route me-2 text-success"></i>Route Cache
                                </h6>
                                <p class="card-text text-muted small">Clear the route cache (route definitions)</p>
                                <form action="{{ route('settings.route.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-trash me-1"></i>Clear Routes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-eye me-2 text-warning"></i>View Cache
                                </h6>
                                <p class="card-text text-muted small">Clear the compiled view cache (Blade templates)</p>
                                <form action="{{ route('settings.view.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="fas fa-trash me-1"></i>Clear Views
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h6 class="card-title text-danger">
                                    <i class="fas fa-broom me-2"></i>Clear All Caches
                                </h6>
                                <p class="card-text text-muted small">Clear all caches at once (Application, Config, Route, View)</p>
                                <form action="{{ route('settings.clear-all') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all caches?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt me-1"></i>Clear All
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fas fa-rocket me-2"></i>Optimize Application
                                </h6>
                                <p class="card-text text-muted small">Clear all caches and optimize the application for better performance</p>
                                <form action="{{ route('settings.optimize') }}" method="POST" class="d-inline" onsubmit="return confirm('This will clear all caches and optimize the application. Continue?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-magic me-1"></i>Optimize
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Public view actions (logged-in users on public floor plan) -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Public View Actions</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Control what logged-in users can do on the public floor plan view (<code>/floor-plans/{id}/public</code>).</p>
                <form id="publicViewSettingsForm" action="{{ url('settings/public-view') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="public_view_allow_create_booking" id="public_view_allow_create_booking" value="1" {{ ($publicViewAllowCreate ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="public_view_allow_create_booking">
                                <strong>Allow create booking on public view</strong>
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1">When enabled, logged-in users with &quot;Create Bookings&quot; permission can create a booking from the public floor plan page (e.g. Sales can book a booth from the public link).</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="public_view_restrict_crud_to_own_booking" id="public_view_restrict_crud_to_own_booking" value="1" {{ ($publicViewRestrictOwn ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="public_view_restrict_crud_to_own_booking">
                                <strong>Restrict booking CRUD to own bookings (non-admin)</strong>
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1">When enabled, users who are not Administrators can only view, edit, update, and delete <strong>their own</strong> bookings (bookings they created). Sales can only manage their own; they cannot edit or delete other sales&#39; bookings. Administrators can always manage all bookings.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Public View Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>System Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Laravel Version:</th>
                                <td>{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <th>PHP Version:</th>
                                <td>{{ PHP_VERSION }}</td>
                            </tr>
                            <tr>
                                <th>Environment:</th>
                                <td>
                                    <span class="badge bg-{{ app()->environment() === 'production' ? 'danger' : 'info' }}">
                                        {{ app()->environment() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Debug Mode:</th>
                                <td>
                                    <span class="badge bg-{{ config('app.debug') ? 'warning' : 'success' }}">
                                        {{ config('app.debug') ? 'ON' : 'OFF' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">App Name:</th>
                                <td>{{ config('app.name') }}</td>
                            </tr>
                            <tr>
                                <th>App URL:</th>
                                <td>{{ config('app.url') }}</td>
                            </tr>
                            <tr>
                                <th>Timezone:</th>
                                <td>{{ config('app.timezone') }}</td>
                            </tr>
                            <tr>
                                <th>Locale:</th>
                                <td>{{ config('app.locale') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Company & Appearance Settings -->
<div class="row mt-4">
    <div class="col-md-12">
        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab">
                    <i class="fas fa-building me-2"></i>Company Information
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab">
                    <i class="fas fa-palette me-2"></i>System Colors
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cdn-tab" data-bs-toggle="tab" data-bs-target="#cdn" type="button" role="tab">
                    <i class="fas fa-cloud me-2"></i>CDN Settings
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="module-display-tab" data-bs-toggle="tab" data-bs-target="#module-display" type="button" role="tab">
                    <i class="fas fa-mobile-alt me-2"></i>Module Display Customize
                </button>
            </li>
        </ul>

        <!-- Tabs Content -->
        <div class="tab-content" id="settingsTabsContent">
            <!-- Company Information Tab -->
            <div class="tab-pane fade show active" id="company" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <h5 class="mb-4"><i class="fas fa-building me-2"></i>Company Information</h5>
                        <form id="companySettingsForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter company name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Company Email</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email" placeholder="contact@company.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Company Phone</label>
                                    <input type="text" class="form-control" id="company_phone" name="company_phone" placeholder="+1234567890">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Company Website</label>
                                    <input type="url" class="form-control" id="company_website" name="company_website" placeholder="https://www.company.com">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Company Address</label>
                                    <textarea class="form-control" id="company_address" name="company_address" rows="2" placeholder="Enter company address"></textarea>
                                </div>
                                
                                <!-- Logo Upload -->
                                <div class="col-md-6">
                                    <label class="form-label">Company Logo</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <img id="logoPreview" src="" alt="Logo" style="max-width: 150px; max-height: 80px; display: none; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" class="form-control" id="logoFile" accept="image/*">
                                            <small class="text-muted">Recommended: 200x80px, PNG/JPG (max 2MB)</small>
                                        </div>
                                    </div>
                                    <input type="hidden" id="company_logo" name="company_logo">
                                </div>
                                
                                <!-- Favicon Upload -->
                                <div class="col-md-6">
                                    <label class="form-label">Favicon</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <img id="faviconPreview" src="" alt="Favicon" style="max-width: 32px; max-height: 32px; display: none; border: 1px solid #ddd; padding: 2px; border-radius: 4px;">
                                        </div>
                                        <div class="flex-grow-1">
                                            <input type="file" class="form-control" id="faviconFile" accept="image/*">
                                            <small class="text-muted">Recommended: 32x32px, ICO/PNG (max 512KB)</small>
                                        </div>
                                    </div>
                                    <input type="hidden" id="company_favicon" name="company_favicon">
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Company Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Appearance/Colors Tab -->
            <div class="tab-pane fade" id="appearance" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <h5 class="mb-4"><i class="fas fa-palette me-2"></i>System Color Scheme</h5>
                        <p class="text-muted mb-4">Customize the color scheme for your entire system. Changes will be applied across all pages.</p>
                        
                        <form id="appearanceSettingsForm">
                            @csrf
                            <div class="row g-4">
                                <!-- Primary Colors -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">Primary Colors</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Primary Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="#4e73df">
                                            <input type="text" class="form-control" id="primary_color_text" value="#4e73df" readonly>
                                        </div>
                                        <small class="text-muted">Main brand color for buttons and links</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Secondary Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="#667eea">
                                            <input type="text" class="form-control" id="secondary_color_text" value="#667eea" readonly>
                                        </div>
                                        <small class="text-muted">Secondary brand color</small>
                                    </div>
                                </div>
                                
                                <!-- Status Colors -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">Status Colors</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Success Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="success_color" name="success_color" value="#1cc88a">
                                            <input type="text" class="form-control" id="success_color_text" value="#1cc88a" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Info Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="info_color" name="info_color" value="#36b9cc">
                                            <input type="text" class="form-control" id="info_color_text" value="#36b9cc" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Warning Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="warning_color" name="warning_color" value="#f6c23e">
                                            <input type="text" class="form-control" id="warning_color_text" value="#f6c23e" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Danger Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color" id="danger_color" name="danger_color" value="#e74a3b">
                                            <input type="text" class="form-control" id="danger_color_text" value="#e74a3b" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Layout Colors -->
                                <div class="col-md-12">
                                    <h6 class="mb-3">Layout Colors</h6>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Sidebar Background</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="sidebar_bg" name="sidebar_bg" value="#224abe">
                                                <input type="text" class="form-control" id="sidebar_bg_text" value="#224abe" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Navbar Background</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="navbar_bg" name="navbar_bg" value="#ffffff">
                                                <input type="text" class="form-control" id="navbar_bg_text" value="#ffffff" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Footer Background</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" id="footer_bg" name="footer_bg" value="#f8f9fc">
                                                <input type="text" class="form-control" id="footer_bg_text" value="#f8f9fc" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Appearance Settings
                                </button>
                                <button type="button" class="btn btn-secondary ms-2" id="resetColors">
                                    <i class="fas fa-undo me-2"></i>Reset to Defaults
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- CDN Settings Tab -->
            <div class="tab-pane fade" id="cdn" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <h5 class="mb-4"><i class="fas fa-cloud me-2"></i>CDN Settings</h5>
                        <p class="text-muted mb-4">Choose whether to load CSS and JavaScript libraries from CDN (Content Delivery Network) or from your local server.</p>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>CDN vs Local Assets:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>CDN (ON):</strong> Faster loading from global CDN servers, but requires internet connection</li>
                                <li><strong>Local (OFF):</strong> Loads from your server, works offline, but may be slower</li>
                            </ul>
                        </div>

                        <form id="cdnSettingsForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-body">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="use_cdn" name="use_cdn" style="width: 3rem; height: 1.5rem;">
                                                <label class="form-check-label ms-3" for="use_cdn">
                                                    <strong>Use CDN for Assets</strong>
                                                    <p class="text-muted small mb-0 mt-1">When enabled, CSS and JavaScript libraries will be loaded from CDN instead of local files.</p>
                                                </label>
                                            </div>

                                            <div id="cdnStatus" class="mt-3 p-3 rounded" style="display: none;">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-circle me-2" style="font-size: 0.75rem;"></i>
                                                    <span id="cdnStatusText"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save CDN Settings
                                </button>
                                <button type="button" class="btn btn-secondary ms-2" onclick="location.reload()">
                                    <i class="fas fa-sync-alt me-2"></i>Refresh Page
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Module Display Customize Tab -->
            <div class="tab-pane fade" id="module-display" role="tabpanel">
                <div class="card border-top-0 rounded-top-0">
                    <div class="card-body">
                        <h5 class="mb-4"><i class="fas fa-mobile-alt me-2"></i>Module Display Customize</h5>
                        <p class="text-muted mb-4">Control which modules and features are visible on Mobile and Tablet devices. This allows you to customize the user experience for different screen sizes.</p>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Device-Specific Display:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Mobile (≤768px):</strong> Control visibility on smartphones</li>
                                <li><strong>Tablet (769px-1024px):</strong> Control visibility on tablets</li>
                                <li>Desktop views are not affected by these settings</li>
                            </ul>
                        </div>

                        <form id="moduleDisplayForm">
                            @csrf
                            <div class="row g-3" id="moduleDisplayContainer">
                                <!-- Modules will be loaded here -->
                                <div class="col-12 text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Loading module settings...</p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Module Display Settings
                                </button>
                                <button type="button" class="btn btn-secondary ms-2" onclick="loadModuleDisplaySettings()">
                                    <i class="fas fa-sync-alt me-2"></i>Reset to Defaults
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/toastr/css/toastr.min.css') }}">
<style>
    #statusSettingsTable tbody tr {
        cursor: move;
    }
    #statusSettingsTable tbody tr:hover {
        background-color: #f8f9fa;
    }
    .status-bg-color, .status-border-color, .status-text-color {
        cursor: pointer;
    }
    .form-control-color {
        height: 31px;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('vendor/bootstrap5/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('vendor/jquery-ui/css/jquery-ui.min.css') }}">
<script>
    // Configure toastr
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
</script>
<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Load company settings
    function loadCompanySettings() {
        $.get('{{ route("settings.company") }}')
            .done(function(response) {
                if (response.status === 200) {
                    const data = response.data;
                    $('#company_name').val(data.company_name || '');
                    $('#company_email').val(data.company_email || '');
                    $('#company_phone').val(data.company_phone || '');
                    $('#company_website').val(data.company_website || '');
                    $('#company_address').val(data.company_address || '');
                    $('#company_logo').val(data.company_logo || '');
                    $('#company_favicon').val(data.company_favicon || '');
                    
                    // Show logo preview if exists
                    if (data.company_logo) {
                        $('#logoPreview').attr('src', '{{ asset("") }}' + data.company_logo).show();
                    }
                    
                    // Show favicon preview if exists
                    if (data.company_favicon) {
                        $('#faviconPreview').attr('src', '{{ asset("") }}' + data.company_favicon).show();
                    }
                }
            })
            .fail(function() {
                toastr.error('Failed to load company settings');
            });
    }

    // Load appearance settings
    function loadAppearanceSettings() {
        $.get('{{ route("settings.appearance") }}')
            .done(function(response) {
                if (response.status === 200) {
                    const data = response.data;
                    $('#primary_color, #primary_color_text').val(data.primary_color);
                    $('#secondary_color, #secondary_color_text').val(data.secondary_color);
                    $('#success_color, #success_color_text').val(data.success_color);
                    $('#info_color, #info_color_text').val(data.info_color);
                    $('#warning_color, #warning_color_text').val(data.warning_color);
                    $('#danger_color, #danger_color_text').val(data.danger_color);
                    $('#sidebar_bg, #sidebar_bg_text').val(data.sidebar_bg);
                    $('#navbar_bg, #navbar_bg_text').val(data.navbar_bg);
                    $('#footer_bg, #footer_bg_text').val(data.footer_bg);
                }
            })
            .fail(function() {
                toastr.error('Failed to load appearance settings');
            });
    }

    // Load CDN settings
    function loadCDNSettings() {
        $.get('{{ route("settings.cdn") }}')
            .done(function(response) {
                if (response.status === 200) {
                    const data = response.data;
                    // The model defaults to true (CDN enabled) if setting doesn't exist
                    // So data.use_cdn will always be a boolean (true or false)
                    $('#use_cdn').prop('checked', data.use_cdn === true);
                    updateCDNStatus(data.use_cdn === true);
                }
            })
            .fail(function() {
                toastr.error('Failed to load CDN settings');
            });
    }

    // Update CDN status display
    function updateCDNStatus(useCDN) {
        const statusDiv = $('#cdnStatus');
        const statusText = $('#cdnStatusText');
        
        if (useCDN) {
            statusDiv.removeClass('bg-light').addClass('bg-info text-white');
            statusText.html('<strong>CDN Enabled:</strong> Assets will be loaded from CDN servers');
            statusDiv.find('i').removeClass('text-success text-danger').addClass('text-white');
        } else {
            statusDiv.removeClass('bg-info text-white').addClass('bg-light');
            statusText.html('<strong>Local Assets:</strong> Assets will be loaded from your local server');
            statusDiv.find('i').removeClass('text-white').addClass('text-success');
        }
        statusDiv.show();
    }

    // CDN toggle change handler
    $('#use_cdn').on('change', function() {
        updateCDNStatus($(this).is(':checked'));
    });

    // Sync color picker with text input
    $('input[type="color"]').on('change', function() {
        const textId = $(this).attr('id') + '_text';
        $('#' + textId).val($(this).val());
    });

    // Sync text input with color picker
    $('input[id$="_text"]').on('input', function() {
        const colorId = $(this).attr('id').replace('_text', '');
        const value = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            $('#' + colorId).val(value);
        }
    });

    // Save company settings
    $('#companySettingsForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("settings.company.save") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.message || 'Company settings saved successfully');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let message = xhr.responseJSON?.message || 'Failed to save company settings';
                toastr.error(message);
            }
        });
    });

    // Upload logo
    $('#logoFile').on('change', function() {
        const file = this.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('logo', file);
        formData.append('_token', '{{ csrf_token() }}');
        
        $.ajax({
            url: '{{ route("settings.company.upload-logo") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 200) {
                    $('#company_logo').val(response.path);
                    $('#logoPreview').attr('src', response.url).show();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to upload logo');
            }
        });
    });

    // Upload favicon
    $('#faviconFile').on('change', function() {
        const file = this.files[0];
        if (!file) return;
        
        const formData = new FormData();
        formData.append('favicon', file);
        formData.append('_token', '{{ csrf_token() }}');
        
        $.ajax({
            url: '{{ route("settings.company.upload-favicon") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 200) {
                    $('#company_favicon').val(response.path);
                    $('#faviconPreview').attr('src', response.url).show();
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to upload favicon');
            }
        });
    });

    // Save appearance settings
    $('#appearanceSettingsForm').on('submit', function(e) {
        e.preventDefault();
        const data = {
            primary_color: $('#primary_color').val(),
            secondary_color: $('#secondary_color').val(),
            success_color: $('#success_color').val(),
            info_color: $('#info_color').val(),
            warning_color: $('#warning_color').val(),
            danger_color: $('#danger_color').val(),
            sidebar_bg: $('#sidebar_bg').val(),
            navbar_bg: $('#navbar_bg').val(),
            footer_bg: $('#footer_bg').val(),
            _token: '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: '{{ route("settings.appearance.save") }}',
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.message || 'Appearance settings saved successfully');
                    // Reload page to apply changes
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let message = xhr.responseJSON?.message || 'Failed to save appearance settings';
                toastr.error(message);
            }
        });
    });

    // Reset colors to defaults
    $('#resetColors').on('click', function() {
        if (confirm('Reset all colors to default values?')) {
            $('#primary_color, #primary_color_text').val('#4e73df');
            $('#secondary_color, #secondary_color_text').val('#667eea');
            $('#success_color, #success_color_text').val('#1cc88a');
            $('#info_color, #info_color_text').val('#36b9cc');
            $('#warning_color, #warning_color_text').val('#f6c23e');
            $('#danger_color, #danger_color_text').val('#e74a3b');
            $('#sidebar_bg, #sidebar_bg_text').val('#224abe');
            $('#navbar_bg, #navbar_bg_text').val('#ffffff');
            $('#footer_bg, #footer_bg_text').val('#f8f9fc');
        }
    });

    // Load settings on page load
    // Save CDN settings
    $('#cdnSettingsForm').on('submit', function(e) {
        e.preventDefault();
        const data = {
            use_cdn: $('#use_cdn').is(':checked') ? 1 : 0,
            _token: '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: '{{ route("settings.cdn.save") }}',
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.message || 'CDN settings saved successfully');
                    updateCDNStatus(data.use_cdn === 1);
                    // Show message to refresh page
                    setTimeout(function() {
                        toastr.info('Please refresh the page to apply CDN changes', 'Refresh Required', {
                            timeOut: 0,
                            extendedTimeOut: 0,
                            closeButton: true
                        });
                    }, 1000);
                } else {
                    toastr.error(response.message || 'Failed to save CDN settings');
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Failed to save CDN settings');
            }
        });
    });

    // Module Display Settings
    const moduleConfig = {
        'dashboard': { name: 'Dashboard', icon: 'fa-home', description: 'Main dashboard with statistics and overview' },
        'booths': { name: 'Booths', icon: 'fa-store', description: 'Booth management and floor plan designer' },
        'bookings': { name: 'Bookings', icon: 'fa-calendar-check', description: 'Booking management and calendar' },
        'clients': { name: 'Clients', icon: 'fa-users', description: 'Client management and directory' },
        'settings': { name: 'Settings', icon: 'fa-cog', description: 'System settings and configuration' },
        'reports': { name: 'Reports', icon: 'fa-chart-bar', description: 'Analytics and reporting tools' },
        'finance': { name: 'Finance', icon: 'fa-dollar-sign', description: 'Financial management and transactions' },
        'hr': { name: 'HR', icon: 'fa-user-tie', description: 'Human resources management' },
        'users': { name: 'Users', icon: 'fa-user-shield', description: 'User management and permissions' },
        'categories': { name: 'Categories', icon: 'fa-folder', description: 'Category and classification management' }
    };

    function loadModuleDisplaySettings() {
        $.get('{{ route("settings.module-display") }}')
            .done(function(response) {
                if (response.status === 200) {
                    renderModuleDisplaySettings(response.data);
                }
            })
            .fail(function() {
                toastr.error('Failed to load module display settings');
                $('#moduleDisplayContainer').html('<div class="alert alert-danger">Failed to load settings. Please refresh the page.</div>');
            });
    }

    function renderModuleDisplaySettings(settings) {
        let html = '';
        
        Object.keys(moduleConfig).forEach(function(moduleKey) {
            const module = moduleConfig[moduleKey];
            const moduleSettings = settings[moduleKey] || { mobile: true, tablet: true };
            
            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="card border h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="fas ${module.icon} fa-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">${module.name}</h6>
                                    <small class="text-muted">${module.description}</small>
                                </div>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input module-toggle" type="checkbox" 
                                               id="module_${moduleKey}_mobile" 
                                               data-module="${moduleKey}" 
                                               data-device="mobile"
                                               ${moduleSettings.mobile ? 'checked' : ''}
                                               style="width: 2.5rem; height: 1.25rem;">
                                        <label class="form-check-label" for="module_${moduleKey}_mobile">
                                            <i class="fas fa-mobile-alt me-1"></i> Mobile
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input module-toggle" type="checkbox" 
                                               id="module_${moduleKey}_tablet" 
                                               data-module="${moduleKey}" 
                                               data-device="tablet"
                                               ${moduleSettings.tablet ? 'checked' : ''}
                                               style="width: 2.5rem; height: 1.25rem;">
                                        <label class="form-check-label" for="module_${moduleKey}_tablet">
                                            <i class="fas fa-tablet-alt me-1"></i> Tablet
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#moduleDisplayContainer').html(html);
    }

    // Save module display settings
    $('#moduleDisplayForm').on('submit', function(e) {
        e.preventDefault();
        
        const modules = {};
        $('.module-toggle').each(function() {
            const module = $(this).data('module');
            const device = $(this).data('device');
            
            if (!modules[module]) {
                modules[module] = {};
            }
            
            modules[module][device] = $(this).is(':checked');
        });
        
        const data = {
            modules: modules,
            _token: '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: '{{ route("settings.module-display.save") }}',
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.message || 'Module display settings saved successfully');
                    // Reload to apply changes
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Failed to save module display settings');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let message = xhr.responseJSON?.message || 'Failed to save module display settings';
                if (Object.keys(errors).length > 0) {
                    message += ': ' + Object.values(errors).flat().join(', ');
                }
                toastr.error(message);
            }
        });
    });

    // Load module display settings when tab is shown
    $('#module-display-tab').on('shown.bs.tab', function() {
        if ($('#moduleDisplayContainer').children().length === 1 && $('#moduleDisplayContainer').find('.spinner-border').length > 0) {
            loadModuleDisplaySettings();
        }
    });

    $(document).ready(function() {
        loadCompanySettings();
        loadAppearanceSettings();
        loadCDNSettings();
        
        // Load module display settings if tab is active
        if ($('#module-display-tab').hasClass('active')) {
            loadModuleDisplaySettings();
        }
    });
</script>
@endpush
@endsection

