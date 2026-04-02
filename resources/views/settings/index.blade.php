@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class='looker-dashboard'>
<div class="row mb-4">
    <div class="col">
        <h2 class="mb-1"><i class="fas fa-cog me-2"></i>Global Settings</h2>
        <p class="text-muted mb-0">System configuration, appearance, uploads, and maintenance in one place.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="glass-card settings-global-tabs-card">
            <div class="settings-tabs-scroll border-bottom">
                <ul class="nav nav-tabs settings-main-tabs flex-nowrap mb-0 px-2 px-md-3 pt-3" id="globalSettingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="cache-tab" data-bs-toggle="tab" data-bs-target="#cache-management" type="button" role="tab" aria-controls="cache-management" aria-selected="true">
                            <i class="fas fa-broom me-1 me-md-2"></i><span>Cache</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="upload-control-tab" data-bs-toggle="tab" data-bs-target="#settings-upload-control" type="button" role="tab" aria-controls="settings-upload-control" aria-selected="false">
                            <i class="fas fa-upload me-1 me-md-2"></i><span class="d-none d-md-inline">Upload</span><span class="d-md-none">Up</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="public-view-tab" data-bs-toggle="tab" data-bs-target="#settings-public-view" type="button" role="tab" aria-controls="settings-public-view" aria-selected="false">
                            <i class="fas fa-eye me-1 me-md-2"></i><span class="d-none d-lg-inline">Public view</span><span class="d-lg-none">Public</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="push-tab" data-bs-toggle="tab" data-bs-target="#push-notifications" type="button" role="tab" aria-controls="push-notifications" aria-selected="false">
                            <i class="fas fa-bell me-1 me-md-2"></i><span>Push</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="system-info-tab" data-bs-toggle="tab" data-bs-target="#system-information" type="button" role="tab" aria-controls="system-information" aria-selected="false">
                            <i class="fas fa-info-circle me-1 me-md-2"></i><span class="d-none d-md-inline">System</span><span class="d-md-none">Sys</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" type="button" role="tab" aria-controls="company" aria-selected="false">
                            <i class="fas fa-building me-1 me-md-2"></i><span>Company</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="global-color-tab" data-bs-toggle="tab" data-bs-target="#global-color-settings" type="button" role="tab" aria-controls="global-color-settings" aria-selected="false">
                            <i class="fas fa-fill-drip me-1 me-md-2"></i><span class="d-none d-xl-inline">Color</span><span class="d-xl-none">Color</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab" aria-controls="appearance" aria-selected="false">
                            <i class="fas fa-palette me-1 me-md-2"></i><span class="d-none d-lg-inline">UI colors</span><span class="d-lg-none">UI</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cdn-tab" data-bs-toggle="tab" data-bs-target="#cdn" type="button" role="tab" aria-controls="cdn" aria-selected="false">
                            <i class="fas fa-cloud me-1 me-md-2"></i><span>CDN</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="module-display-tab" data-bs-toggle="tab" data-bs-target="#module-display" type="button" role="tab" aria-controls="module-display" aria-selected="false">
                            <i class="fas fa-mobile-alt me-1 me-md-2"></i><span class="d-none d-xl-inline">Modules</span><span class="d-xl-none">Mod</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="globalSettingsTabsContent">
                <!-- Cache Management -->
                <div class="tab-pane fade show active" id="cache-management" role="tabpanel" aria-labelledby="cache-tab" tabindex="0">
                    <div class="p-4">
                        <h5 class="mb-3 h6 text-dark fw-bold"><i class="fas fa-broom me-2"></i>Cache Management</h5>
                        <p class="text-muted">Clear various caches to ensure the application is using the latest data and configurations.</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="p-4">
                                        <h6 class="h5 fw-bold mb-0 text-dark">
                                            <i class="fas fa-database me-2 text-primary"></i>Application Cache
                                        </h6>
                                        <p class="card-text text-muted small">Clear the application cache (stored data, queries, etc.)</p>
                                        <form action="{{ route('settings.cache.clear') }}" method="POST" class="d-inline settings-form-ajax">
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
                                    <div class="p-4">
                                        <h6 class="h5 fw-bold mb-0 text-dark">
                                            <i class="fas fa-file-code me-2 text-info"></i>Configuration Cache
                                        </h6>
                                        <p class="card-text text-muted small">Clear the configuration cache (config files)</p>
                                        <form action="{{ route('settings.config.clear') }}" method="POST" class="d-inline settings-form-ajax">
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
                                    <div class="p-4">
                                        <h6 class="h5 fw-bold mb-0 text-dark">
                                            <i class="fas fa-route me-2 text-success"></i>Route Cache
                                        </h6>
                                        <p class="card-text text-muted small">Clear the route cache (route definitions)</p>
                                        <form action="{{ route('settings.route.clear') }}" method="POST" class="d-inline settings-form-ajax">
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
                                    <div class="p-4">
                                        <h6 class="h5 fw-bold mb-0 text-dark">
                                            <i class="fas fa-eye me-2 text-warning"></i>View Cache
                                        </h6>
                                        <p class="card-text text-muted small">Clear the compiled view cache (Blade templates)</p>
                                        <form action="{{ route('settings.view.clear') }}" method="POST" class="d-inline settings-form-ajax">
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
                                    <div class="p-4">
                                        <h6 class="card-title text-danger">
                                            <i class="fas fa-broom me-2"></i>Clear All Caches
                                        </h6>
                                        <p class="card-text text-muted small">Clear all caches at once (Application, Config, Route, View)</p>
                                        <form action="{{ route('settings.clear-all') }}" method="POST" class="d-inline settings-form-ajax" data-confirm="Are you sure you want to clear all caches?">
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
                                    <div class="p-4">
                                        <h6 class="card-title text-primary">
                                            <i class="fas fa-rocket me-2"></i>Optimize Application
                                        </h6>
                                        <p class="card-text text-muted small">Clear all caches and optimize the application for better performance</p>
                                        <form action="{{ route('settings.optimize') }}" method="POST" class="d-inline settings-form-ajax" data-confirm="This will clear all caches and optimize the application. Continue?">
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

                <!-- Upload Control -->
                <div class="tab-pane fade" id="settings-upload-control" role="tabpanel" aria-labelledby="upload-control-tab" tabindex="0">
                    <div class="p-4">
                        <h5 class="mb-3 h6 text-dark fw-bold"><i class="fas fa-upload me-2"></i>Upload Control</h5>
                        <p class="text-muted">Control file uploads across the system. Set global defaults or per-context limits (floor plan, booth, avatar, etc.).</p>
                        @include('settings.partials.upload-control-form', [
                            'formId' => 'uploadControlForm',
                            'idPrefix' => '',
                            'uploadSettings' => $uploadSettings,
                        ])
                    </div>
                </div>

                <!-- Public View Actions -->
                <div class="tab-pane fade" id="settings-public-view" role="tabpanel" aria-labelledby="public-view-tab" tabindex="0">
                    <div class="p-4">
                        <h5 class="mb-3 h6 text-dark fw-bold"><i class="fas fa-eye me-2"></i>Public View Actions</h5>
                        <p class="text-muted">Control what logged-in users can do on the public floor plan view (<code>/floor-plans/{id}/public</code>). Colors for the public map and ticks are managed under the <strong>Color (map &amp; bookings)</strong> tab.</p>
                        @include('settings.partials.public-view-behavior-form', [
                            'formId' => 'publicViewBehaviorForm',
                            'idPrefix' => '',
                        ])
                    </div>
                </div>

                <!-- Push Notifications -->
                <div class="tab-pane fade" id="push-notifications" role="tabpanel" aria-labelledby="push-tab" tabindex="0">
                    <div class="p-4">
                        <h5 class="mb-3 h6 text-dark fw-bold"><i class="fas fa-bell me-2"></i>Push Notifications</h5>
                        <p class="text-muted">Enable browser push notifications so users receive alerts (e.g. new bookings, booth updates) even when the tab is in the background. Uses Web Push (VAPID).</p>
                        <form id="pushNotificationSettingsForm" action="{{ route('settings.push-notifications.save') }}" method="POST" class="settings-form-ajax">
                            @csrf
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="push_notifications_enabled" id="push_notifications_enabled" value="1" {{ ($pushNotificationsEnabled ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="push_notifications_enabled">
                                        <strong>Enable push notifications</strong>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1">When enabled, the system will send browser push notifications for in-app events (requires VAPID keys and user permission).</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="push_vapid_public_key">VAPID public key (optional)</label>
                                <input type="text" class="form-control font-monospace" name="push_vapid_public_key" id="push_vapid_public_key" value="{{ old('push_vapid_public_key', $pushVapidPublicKey ?? '') }}" placeholder="e.g. BN1a2b3c..." maxlength="500">
                                <small class="text-muted d-block mt-1">Required for Web Push. Generate with <code>php artisan webpush:vapid</code> (or similar). You can also set <code>PUSH_VAPID_PUBLIC_KEY</code> and <code>PUSH_VAPID_PRIVATE_KEY</code> in <code>.env</code>; the private key must never be stored in the database.</small>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Push Notification Settings
                            </button>
                        </form>
                    </div>
                </div>

                <!-- System Information -->
                <div class="tab-pane fade" id="system-information" role="tabpanel" aria-labelledby="system-info-tab" tabindex="0">
                    <div class="p-4">
                        <h5 class="mb-3 h6 text-dark fw-bold"><i class="fas fa-info-circle me-2"></i>System Information</h5>
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

<!-- Company Information Tab -->
            <div class="tab-pane fade" id="company" role="tabpanel" aria-labelledby="company-tab" tabindex="0">
                <div class="p-4">
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

            <!-- Color: floor plan, booth map, booking statuses (single responsive implementation; breakpoints align with project standard) -->
            <div class="tab-pane fade" id="global-color-settings" role="tabpanel" aria-labelledby="global-color-tab" tabindex="0">
                <div class="p-4">
                        <h5 class="mb-2"><i class="fas fa-fill-drip me-2"></i>Color (map &amp; bookings)</h5>
                        <p class="text-muted mb-4">Public floor plan accent and booked-tick appearance, booth colors on the canvas/public map, and booking record status colors.</p>

                        <div class="mb-5 pb-4 border-bottom">
                            <h6 class="mb-3"><i class="fas fa-map me-2 text-primary"></i>Public floor plan &amp; booked tick</h6>
                            <p class="text-muted small">Applies to the public floor plan view and the booked-booth checkmark on the designer and public view.</p>
                            @include('settings.partials.floor-plan-color-settings-form', [
                                'formId' => 'floorPlanColorSettingsForm',
                                'idPrefix' => '',
                            ])
                        </div>

                        <div class="mb-5 pb-4 border-bottom color-tab-booth-status">
                            <h6 class="mb-3"><i class="fas fa-th-large me-2 text-primary"></i>Booth status (floor plan map)</h6>
                            <p class="text-muted small">Colors and labels for booth states on the canvas and public floor plan. You can scope a status to a specific floor plan or leave it global.</p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-primary btn-sm" id="ct_btnAddStatus"><i class="fas fa-plus me-1"></i>Add status</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="ct_btnSaveStatusSettings"><i class="fas fa-save me-1"></i>Save booth statuses</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="ct_btnResetStatusSettings"><i class="fas fa-undo me-1"></i>Reload</button>
                                <button type="button" class="btn btn-outline-warning btn-sm" id="ct_btnRestoreBoothDefaults"><i class="fas fa-history me-1"></i>Restore defaults</button>
                            </div>
                            <p class="text-muted small mb-3">Restore defaults replaces all booth statuses with the five factory global statuses (custom and floor-plan-specific rows are removed).</p>
                            <div id="ct_statusSettingsContainer">
                                <div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2" role="status"></span>Open this tab to load booth status colors.</div>
                            </div>
                        </div>

                        <div class="color-tab-booking-status">
                            <h6 class="mb-3"><i class="fas fa-calendar-check me-2 text-primary"></i>Booking status (bookings module)</h6>
                            <p class="text-muted small">Labels and colors for booking records (lists, cards, detail pages).</p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-primary btn-sm" id="bk_btnAddBookingStatus"><i class="fas fa-plus me-1"></i>Add status</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="bk_btnSaveBookingStatuses"><i class="fas fa-save me-1"></i>Save booking statuses</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="bk_btnReloadBookingStatuses"><i class="fas fa-undo me-1"></i>Reload</button>
                                <button type="button" class="btn btn-outline-warning btn-sm" id="bk_btnRestoreBookingDefaults"><i class="fas fa-history me-1"></i>Restore defaults</button>
                            </div>
                            <p class="text-muted small mb-3">Restore defaults resets all booking statuses to the six factory defaults (custom rows are removed).</p>
                            <div id="bk_bookingStatusSettingsContainer">
                                <div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2" role="status"></span>Open this tab to load booking statuses.</div>
                            </div>
                        </div>
                    </div>
            </div>

            <!-- Appearance/Colors Tab -->
            <div class="tab-pane fade" id="appearance" role="tabpanel" aria-labelledby="appearance-tab" tabindex="0">
                <div class="p-4">
                        <h5 class="mb-4"><i class="fas fa-palette me-2"></i>System Color Scheme</h5>
                        <p class="text-muted mb-4">Customize the color scheme for your entire system. Changes will be applied across all pages.</p>

                        <form id="appearanceSettingsForm">
                            @csrf
                            <div class="row g-4">
                                <!-- Primary Colors -->
                                <div class="col-md-6">
                                    <h6 class="mb-3 d-flex flex-wrap align-items-center gap-2">Primary Colors
                                        <button type="button" class="btn btn-sm btn-outline-secondary appearance-restore-section" data-appearance-section="primary">Restore defaults</button>
                                    </h6>
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
                                    <h6 class="mb-3 d-flex flex-wrap align-items-center gap-2">Status Colors
                                        <button type="button" class="btn btn-sm btn-outline-secondary appearance-restore-section" data-appearance-section="status">Restore defaults</button>
                                    </h6>
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
                                    <h6 class="mb-3 d-flex flex-wrap align-items-center gap-2">Layout Colors
                                        <button type="button" class="btn btn-sm btn-outline-secondary appearance-restore-section" data-appearance-section="layout">Restore defaults</button>
                                    </h6>
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
                                    <i class="fas fa-undo me-2"></i>Reset all to defaults
                                </button>
                            </div>
                        </form>
                    </div>
            </div>

            <!-- CDN Settings Tab -->
            <div class="tab-pane fade" id="cdn" role="tabpanel" aria-labelledby="cdn-tab" tabindex="0">
                <div class="p-4">
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
                                        <div class="p-4">
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
                                <button type="button" class="btn btn-secondary ms-2" id="cdnRefreshPageHintBtn">
                                    <i class="fas fa-sync-alt me-2"></i>Refresh Page
                                </button>
                            </div>
                        </form>
                    </div>
            </div>

            <!-- Module Display Customize Tab -->
            <div class="tab-pane fade" id="module-display" role="tabpanel" aria-labelledby="module-display-tab" tabindex="0">
                <div class="p-4">
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

                        @include('settings.partials.module-display-form', [
                            'moduleDisplayFormId' => 'moduleDisplayForm',
                            'moduleDisplayContainerId' => 'moduleDisplayContainer',
                            'useGlobalResetOnclick' => true,
                        ])
                    </div>
            </div>


            </div>
        </div>
    </div>
</div>

</div>


@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<style>
    .looker-dashboard { padding: 0 !important; }
    .glass-card {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(40px) saturate(180%);
        -webkit-backdrop-filter: blur(40px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 24px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        margin-bottom: 24px;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }
    .glass-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.55);
        box-shadow: 0 15px 45px rgba(31, 38, 135, 0.2);
    }
    .kpi-card-looker {
        margin-bottom: 24px;
    }
    /* Booth status table — fits tab container: fixed layout + % cols; vertical scroll only */
    .color-tab-booth-status .booth-status-table-shell {
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.45);
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 4px 24px rgba(31, 38, 135, 0.08);
        overflow: hidden;
        width: 100%;
        max-width: 100%;
    }
    .color-tab-booth-status .khb-booth-status-scroll {
        max-height: min(70vh, 720px);
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        width: 100%;
    }
    .color-tab-booth-status .khb-booth-status-scroll .booth-status-table {
        width: 100%;
        max-width: 100%;
        table-layout: fixed;
        border-collapse: separate;
    }
    .color-tab-booth-status .khb-booth-status-scroll .looker-table th,
    .color-tab-booth-status .khb-booth-status-scroll .looker-table td {
        padding: 0.5rem 0.3rem;
        min-width: 0;
        overflow: hidden;
        vertical-align: middle;
    }
    .color-tab-booth-status .khb-booth-status-scroll .looker-table th {
        white-space: normal;
        line-height: 1.2;
        hyphens: auto;
        word-break: break-word;
    }
    .color-tab-booth-status .booth-status-table input.glass-input,
    .color-tab-booth-status .booth-status-table select.glass-input {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    .color-tab-booth-status .khb-booth-status-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 4;
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06);
        background: rgba(248, 250, 252, 0.92) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .color-tab-booth-status .khb-booth-status-scroll thead th:not(:last-child) {
        padding-right: 0.65rem;
    }
    .color-tab-booth-status .khb-col-resize-handle {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 14px;
        margin-right: -7px;
        cursor: col-resize;
        z-index: 6;
        user-select: none;
        touch-action: none;
        pointer-events: auto;
    }
    .color-tab-booth-status .khb-col-resize-handle::after {
        content: '';
        position: absolute;
        right: 5px;
        top: 18%;
        bottom: 18%;
        width: 2px;
        border-radius: 1px;
        background: rgba(0, 0, 0, 0.14);
    }
    .color-tab-booth-status .khb-col-resize-handle:hover::after,
    .color-tab-booth-status .khb-col-resize-handle:focus-visible::after {
        background: rgba(13, 110, 253, 0.55);
    }
    .color-tab-booth-status .khb-col-resize-handle:focus {
        outline: none;
    }
    .color-tab-booth-status .khb-col-resize-handle:focus-visible {
        outline: 2px solid rgba(13, 110, 253, 0.45);
        outline-offset: 1px;
    }
    .color-tab-booth-status .glass-input-color {
        height: 36px;
        width: 100%;
        max-width: 40px;
        min-height: 36px;
        border: 2px solid rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        cursor: pointer;
        padding: 2px;
        flex-shrink: 0;
    }
    .color-tab-booth-status .khb-color-pair {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.25rem;
        min-width: 0;
    }
    .color-tab-booth-status .khb-color-pair .status-bg-color-text,
    .color-tab-booth-status .khb-color-pair .status-border-color-text,
    .color-tab-booth-status .khb-color-pair .status-text-color-text {
        width: 100%;
        min-width: 0;
        font-variant-numeric: tabular-nums;
        font-size: 0.8125rem;
    }
    .color-tab-booth-status .khb-status-drag-handle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        min-height: 40px;
        margin: -4px 0;
        color: var(--text-tertiary, #6c757d);
        cursor: grab;
        border-radius: 10px;
        flex-shrink: 0;
        touch-action: none;
    }
    .color-tab-booth-status .khb-status-drag-handle:hover {
        background: rgba(0, 0, 0, 0.04);
        color: var(--text-secondary, #495057);
    }
    .color-tab-booth-status .khb-status-drag-handle:active {
        cursor: grabbing;
    }
    .color-tab-booth-status .khb-bs-order-cell .d-flex {
        min-width: 0;
    }
    .color-tab-booth-status .khb-bs-order-cell .status-sort-order {
        flex: 1 1 auto;
        min-width: 0;
        max-width: 100%;
        min-height: 38px;
        text-align: center;
    }
    .color-tab-booth-status .khb-bs-name-cell input,
    .color-tab-booth-status .khb-bs-desc-cell input {
        text-overflow: ellipsis;
    }
    .color-tab-booth-status .khb-bs-floor-cell select,
    .color-tab-booth-status .khb-bs-select-cell select {
        min-width: 0;
    }
    .color-tab-booth-status .glass-input-sm,
    .color-tab-booth-status .glass-input.glass-input-sm {
        min-height: 40px;
        font-size: 0.9375rem;
    }
    .color-tab-booth-status .khb-bs-check-wrap .form-check-input {
        width: 1.15rem;
        height: 1.15rem;
        margin-top: 0;
        cursor: pointer;
    }
    .color-tab-booth-status .khb-booth-status-empty {
        max-width: 36rem;
    }
    .color-tab-booth-status .khb-booth-status-empty-icon {
        width: 2.5rem;
        height: 2.5rem;
    }
    .color-tab-booth-status .looker-table tbody tr.khb-booth-status-row {
        cursor: default;
    }
    .color-tab-booth-status .looker-table tbody tr.khb-booth-status-row:nth-child(even) td {
        background-color: rgba(255, 255, 255, 0.12);
    }
    .color-tab-booth-status .looker-table tbody tr.khb-booth-status-row:hover td {
        background-color: rgba(13, 110, 253, 0.06) !important;
    }
    .color-tab-booth-status .khb-bs-actions-cell .btn {
        max-width: 100%;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    @media (max-width: 575.98px) {
        .color-tab-booth-status .khb-booth-status-scroll .looker-table th,
        .color-tab-booth-status .khb-booth-status-scroll .looker-table td {
            padding: 0.35rem 0.2rem;
        }
    }
    /* Global Settings: single responsive tab bar (scroll on narrow viewports; touch-friendly targets) */
    .settings-global-tabs-card:hover {
        transform: none;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    }
    .settings-tabs-scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    .settings-main-tabs {
        flex-wrap: nowrap;
        border-bottom: 0;
    }
    .settings-main-tabs .nav-link {
        white-space: nowrap;
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem 0.75rem 0 0;
    }
    .settings-main-tabs .nav-link.active {
        font-weight: 600;
    }
</style>
@endpush

@push('body-class', 'ios-dashboard-mode')


@push('scripts')
<script src="{{ asset('vendor/bootstrap5/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('vendor/jquery-ui/css/jquery-ui.min.css') }}">
<script src="{{ asset('js/booth-status-settings-manager.js') }}"></script>
<script src="{{ asset('js/booking-status-settings-manager.js') }}"></script>
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
    // Sync booked tick color picker and hex input
    (function() {
        var colorEl = document.getElementById('booth_booked_tick_color');
        var hexEl = document.getElementById('booth_booked_tick_color_hex');
        if (colorEl && hexEl) {
            colorEl.addEventListener('input', function() { hexEl.value = this.value; });
            hexEl.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) colorEl.value = this.value;
            });
        }
        // Public floor plan button color (picker + hex)
        var pvBtnColor = document.getElementById('public_view_button_color');
        var pvBtnHex = document.getElementById('public_view_button_color_hex');
        if (pvBtnColor && pvBtnHex) {
            pvBtnColor.addEventListener('input', function() { pvBtnHex.value = this.value; });
            pvBtnHex.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) pvBtnColor.value = this.value;
            });
        }
        // Sync booked tick background color and toggle visibility
        var bgNone = document.getElementById('booth_booked_tick_bg_none');
        var bgColor = document.getElementById('booth_booked_tick_bg_color');
        var bgHex = document.getElementById('booth_booked_tick_bg_color_hex');
        var bgWrap = document.getElementById('booth_booked_tick_bg_wrap');
        if (bgColor && bgHex) {
            bgColor.addEventListener('input', function() { bgHex.value = this.value; });
            bgHex.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) bgColor.value = this.value;
            });
        }
        if (bgNone && bgWrap && bgColor && bgHex) {
            function toggleBgInputs() {
                var off = bgNone.checked;
                bgWrap.style.opacity = off ? '0.5' : '1';
                bgWrap.style.pointerEvents = off ? 'none' : '';
                bgColor.disabled = off;
                bgHex.disabled = off;
            }
            bgNone.addEventListener('change', toggleBgInputs);
            toggleBgInputs();
        }
        // Sync booked tick border color picker and hex input
        var borderColorEl = document.getElementById('booth_booked_tick_border_color');
        var borderHexEl = document.getElementById('booth_booked_tick_border_color_hex');
        if (borderColorEl && borderHexEl) {
            borderColorEl.addEventListener('input', function() { borderHexEl.value = this.value; });
            borderHexEl.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) borderColorEl.value = this.value;
            });
        }
        // Toggle overall size: fixed vs relative to booth
        var sizeModeEl = document.getElementById('booth_booked_tick_size_mode');
        var relativeWrap = document.getElementById('booth_booked_tick_relative_wrap');
        var fixedWrap = document.getElementById('booth_booked_tick_fixed_wrap');
        if (sizeModeEl && relativeWrap && fixedWrap) {
            function toggleSizeMode() {
                var isRelative = sizeModeEl.value === 'relative';
                relativeWrap.style.display = isRelative ? 'block' : 'none';
                fixedWrap.style.display = isRelative ? 'none' : 'block';
            }
            sizeModeEl.addEventListener('change', toggleSizeMode);
            toggleSizeMode();
        }
    })();
</script>
<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Cache, upload, public view, push — submit via AJAX (no full page reload)
    $(document).on('submit', 'form.settings-form-ajax', function(e) {
        e.preventDefault();
        var $form = $(this);
        var confirmMsg = $form.data('confirm');
        if (confirmMsg && !window.confirm(confirmMsg)) {
            return;
        }
        var $btn = $form.find('button[type="submit"], input[type="submit"]');
        $btn.prop('disabled', true);
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function(res) {
                if (res.status === 200) {
                    toastr.success(res.message || 'Saved.');
                } else {
                    toastr.error(res.message || 'Something went wrong.');
                }
            },
            error: function(xhr) {
                var r = xhr.responseJSON || {};
                var msg = r.message || 'Request failed.';
                if (r.errors) {
                    msg += ' ' + Object.values(r.errors).flat().join(' ');
                }
                toastr.error(msg);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    function applyTickSettingsToForm(d) {
        $('#booth_booked_show_tick').prop('checked', !!d.show_tick);
        $('#booth_booked_tick_color').val(d.color);
        $('#booth_booked_tick_color_hex').val(d.color);
        $('#booth_booked_tick_size').val(d.size);
        $('#booth_booked_tick_shape').val(d.shape);
        $('#booth_booked_tick_position').val(d.position);
        $('#booth_booked_tick_animation').val(d.animation);
        $('#booth_booked_tick_font_size').val(d.font_size);
        $('#booth_booked_tick_relative_percent').val(String(d.relative_percent));
        $('#booth_booked_tick_border_width').val(String(d.border_width));
        $('#booth_booked_tick_border_color').val(d.border_color);
        $('#booth_booked_tick_border_color_hex').val(d.border_color);
        var bgEmpty = !d.bg_color || d.bg_color === '';
        $('#booth_booked_tick_bg_none').prop('checked', bgEmpty);
        if (!bgEmpty) {
            $('#booth_booked_tick_bg_color').val(d.bg_color);
            $('#booth_booked_tick_bg_color_hex').val(d.bg_color);
        }
        $('#booth_booked_tick_size_mode').val(d.size_mode).trigger('change');
        $('#booth_booked_tick_bg_none').trigger('change');
    }

    $('#tick_floor_plan_id').on('change', function() {
        var id = $(this).val();
        $.get('{{ route("settings.tick-settings") }}', { floor_plan_id: id || '' })
            .done(function(res) {
                if (res.status !== 200 || !res.data) {
                    return;
                }
                applyTickSettingsToForm(res.data);
            })
            .fail(function() {
                toastr.error('Could not load tick settings for this floor plan.');
            });
    });

    $('#cdnRefreshPageHintBtn').on('click', function() {
        toastr.info('Refresh this page when you want assets to reload with the saved CDN option.', 'Optional refresh', { timeOut: 6000 });
    });

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
                    
                    // Show logo preview if exists (build URL so images load properly)
                    if (data.company_logo) {
                        var base = '{{ rtrim(url("/"), "/") }}';
                        var path = (data.company_logo + '').replace(/^\/+/, '').replace(/\\/g, '/');
                        $('#logoPreview').attr('src', path ? base + '/' + path : '').show();
                    }
                    
                    // Show favicon preview if exists
                    if (data.company_favicon) {
                        var base = '{{ rtrim(url("/"), "/") }}';
                        var path = (data.company_favicon + '').replace(/^\/+/, '').replace(/\\/g, '/');
                        $('#faviconPreview').attr('src', path ? base + '/' + path : '').show();
                    }
                }
            })
            .fail(function() {
                toastr.error('Failed to load company settings');
            });
    }

    function applyAppearanceDataToForm(data) {
        if (!data) {
            return;
        }
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

    // Load appearance settings
    function loadAppearanceSettings() {
        $.get('{{ route("settings.appearance") }}')
            .done(function(response) {
                if (response.status === 200) {
                    applyAppearanceDataToForm(response.data);
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
                    toastr.success((response.message || 'Appearance settings saved successfully') + ' Refresh the page when you want the layout to use the new colors.');
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let message = xhr.responseJSON?.message || 'Failed to save appearance settings';
                toastr.error(message);
            }
        });
    });

    function postAppearanceRestore(section) {
        $.ajax({
            url: '{{ route("settings.appearance.restore-section") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', section: section },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function(res) {
                if (res.status === 200 && res.data) {
                    applyAppearanceDataToForm(res.data);
                    toastr.success(res.message || 'Defaults restored. Refresh other pages to see layout colors.');
                } else {
                    toastr.error(res.message || 'Restore failed.');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Restore failed.';
                toastr.error(msg);
            }
        });
    }

    $(document).on('click', '.appearance-restore-section', function() {
        var section = $(this).data('appearance-section');
        if (!section || !window.confirm('Restore default colors for this group? This saves immediately.')) {
            return;
        }
        postAppearanceRestore(section);
    });

    // Reset all system colors to defaults (persisted)
    $('#resetColors').on('click', function() {
        if (!window.confirm('Reset all system colors to default values? This saves immediately.')) {
            return;
        }
        postAppearanceRestore('all');
    });

    $('#btnRestoreFloorPlanColorDefaults').on('click', function() {
        if (!window.confirm('Restore default public button color and booked-tick settings for the selected floor plan scope? This saves immediately.')) {
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: '{{ route("settings.floor-plan-colors.restore") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                tick_floor_plan_id: $('#tick_floor_plan_id').val() || ''
            },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function(res) {
                if (res.status === 200 && res.data) {
                    $('#public_view_button_color').val(res.data.public_view_button_color);
                    $('#public_view_button_color_hex').val(res.data.public_view_button_color);
                    if (res.data.tick) {
                        applyTickSettingsToForm(res.data.tick);
                    }
                    toastr.success(res.message || 'Restored.');
                } else {
                    toastr.error(res.message || 'Restore failed.');
                }
            },
            error: function(xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Restore failed.';
                toastr.error(msg);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    $('#ct_btnRestoreBoothDefaults').on('click', function() {
        if (!window.confirm('Replace all booth statuses with the five factory defaults? Custom and floor-plan-specific statuses will be removed. This cannot be undone.')) {
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: '{{ route("settings.booth-statuses.restore-defaults") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function(res) {
                if (res.status === 200) {
                    toastr.success(res.message || 'Restored.');
                    if (typeof window.khbReloadBoothStatusesFromServer === 'function') {
                        window.khbReloadBoothStatusesFromServer();
                    }
                } else {
                    toastr.error(res.message || 'Restore failed.');
                }
            },
            error: function(xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Restore failed.');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    $('#bk_btnRestoreBookingDefaults').on('click', function() {
        if (!window.confirm('Replace all booking statuses with the six factory defaults? Custom statuses will be removed. This cannot be undone.')) {
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true);
        $.ajax({
            url: '{{ route("settings.booking-statuses.restore-defaults") }}',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            success: function(res) {
                if (res.status === 200) {
                    toastr.success(res.message || 'Restored.');
                    if (typeof window.khbReloadBookingStatusesFromServer === 'function') {
                        window.khbReloadBookingStatusesFromServer();
                    }
                } else {
                    toastr.error(res.message || 'Restore failed.');
                }
            },
            error: function(xhr) {
                toastr.error((xhr.responseJSON && xhr.responseJSON.message) || 'Restore failed.');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
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
                    toastr.info('Open other pages in a new tab or refresh them to pick up CDN changes; refresh this page only if you need scripts reloaded here.', 'CDN note', { timeOut: 8000 });
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
                        <div class="p-4">
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
                    toastr.success((response.message || 'Module display settings saved successfully') + ' Open another page or refresh to see menu changes on mobile/tablet.');
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

    var floorPlansForColorTab = @json($floorPlans->map(function ($fp) { return ['id' => $fp->id, 'name' => $fp->name]; })->values());
    var colorTabManagersLoaded = false;

    function initColorTabManagers() {
        if (colorTabManagersLoaded) {
            return;
        }
        colorTabManagersLoaded = true;
        if (typeof window.khbInitBoothStatusSettings === 'function') {
            window.khbInitBoothStatusSettings({
                idPrefix: 'ct_',
                floorPlans: floorPlansForColorTab,
                listUrl: '{{ route("settings.booth-statuses") }}',
                saveUrl: '{{ route("settings.booth-statuses.save") }}',
                deleteUrlTemplate: '{{ url('/settings/booth-statuses') }}/:id',
                csrfToken: '{{ csrf_token() }}',
            });
        }
        if (typeof window.khbInitBookingStatusSettings === 'function') {
            window.khbInitBookingStatusSettings({
                idPrefix: 'bk_',
                listUrl: '{{ route("settings.booking-statuses") }}',
                saveUrl: '{{ route("settings.booking-statuses.save") }}',
                deleteUrlTemplate: '{{ url('/settings/booking-statuses') }}/:id',
                csrfToken: '{{ csrf_token() }}',
            });
        }
    }

    $('#global-color-tab').on('shown.bs.tab', function() {
        initColorTabManagers();
    });

    function applySettingsHashTab() {
        var hash = (window.location.hash || '').replace(/^#/, '');
        if (!hash) {
            return;
        }
        var map = {
            'global-color-settings': 'global-color-tab',
            'settings-upload-control': 'upload-control-tab',
            'settings-public-view': 'public-view-tab',
            'module-display': 'module-display-tab',
            'cache-management': 'cache-tab',
            'push-notifications': 'push-tab',
            'system-information': 'system-info-tab',
            'company': 'company-tab',
            'appearance': 'appearance-tab',
            'cdn': 'cdn-tab'
        };
        var tabId = map[hash];
        if (!tabId) {
            return;
        }
        var el = document.getElementById(tabId);
        if (el && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(el).show();
            if (hash === 'global-color-settings') {
                initColorTabManagers();
            }
            try {
                el.scrollIntoView({ block: 'nearest', inline: 'nearest', behavior: 'smooth' });
            } catch (e) { /* ignore */ }
        }
    }

    $(document).ready(function() {
        loadCompanySettings();
        loadAppearanceSettings();
        loadCDNSettings();
        applySettingsHashTab();
        window.addEventListener('hashchange', applySettingsHashTab);

        // Load module display settings if tab is active
        if ($('#module-display-tab').hasClass('active')) {
            loadModuleDisplaySettings();
        }
    });
</script>
@endpush
@endsection
