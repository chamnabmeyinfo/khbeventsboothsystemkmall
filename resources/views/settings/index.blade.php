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

        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
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
    $(document).ready(function() {
        loadCompanySettings();
        loadAppearanceSettings();
    });
</script>
@push('styles')
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
@endsection

