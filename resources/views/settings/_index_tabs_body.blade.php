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

@include('settings._index_tabs_panes')

            </div>
        </div>
    </div>
</div>
