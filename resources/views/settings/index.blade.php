@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="looker-dashboard settings-page">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1><i class="fas fa-sliders-h me-2" aria-hidden="true"></i>Global settings</h1>
            <p>System configuration, appearance, uploads, and maintenance in one place.</p>
        </div>
        <div class="looker-actions d-none d-md-flex">
            <a href="{{ route('dashboard') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-chart-line" aria-hidden="true"></i> Dashboard
            </a>
        </div>
    </header>

    <div class="canvas-panel settings-tabs-panel p-0 overflow-hidden">
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
                        <button class="nav-link" id="access-roles-tab" data-bs-toggle="tab" data-bs-target="#access-roles-settings" type="button" role="tab" aria-controls="access-roles-settings" aria-selected="false">
                            <i class="fas fa-user-shield me-1 me-md-2"></i><span class="d-none d-xl-inline">Roles &amp; features</span><span class="d-xl-none">Roles</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-settings" type="button" role="tab" aria-controls="security-settings" aria-selected="false">
                            <i class="fas fa-shield-alt me-1 me-md-2"></i><span>Security</span>
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
                        <header class="settings-tab-header">
                            <h2 class="panel-title settings-tab-header__title"><i class="fas fa-broom" aria-hidden="true"></i> Cache management</h2>
                            <p class="settings-tab-lead">Clear Laravel caches after config, route, or view changes. Use “Clear all” or “Optimize” when you know the impact.</p>
                        </header>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="settings-mini-panel h-100">
                                    <h3 class="settings-mini-panel__title">
                                        <span class="settings-mini-panel__icon settings-mini-panel__icon--blue" aria-hidden="true"><i class="fas fa-database"></i></span>
                                        Application cache
                                    </h3>
                                    <p class="settings-mini-panel__desc">Application cache (stored data, queries, etc.).</p>
                                    <form action="{{ route('settings.cache.clear') }}" method="POST" class="settings-form-ajax mt-auto">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-primary action-btn-compact">
                                            <i class="fas fa-trash" aria-hidden="true"></i>Clear cache
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="settings-mini-panel h-100">
                                    <h3 class="settings-mini-panel__title">
                                        <span class="settings-mini-panel__icon settings-mini-panel__icon--purple" aria-hidden="true"><i class="fas fa-file-code"></i></span>
                                        Configuration cache
                                    </h3>
                                    <p class="settings-mini-panel__desc">Compiled configuration from config files.</p>
                                    <form action="{{ route('settings.config.clear') }}" method="POST" class="settings-form-ajax mt-auto">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-secondary action-btn-compact">
                                            <i class="fas fa-trash" aria-hidden="true"></i>Clear config
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="settings-mini-panel h-100">
                                    <h3 class="settings-mini-panel__title">
                                        <span class="settings-mini-panel__icon settings-mini-panel__icon--green" aria-hidden="true"><i class="fas fa-route"></i></span>
                                        Route cache
                                    </h3>
                                    <p class="settings-mini-panel__desc">Cached route definitions.</p>
                                    <form action="{{ route('settings.route.clear') }}" method="POST" class="settings-form-ajax mt-auto">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-secondary action-btn-compact">
                                            <i class="fas fa-trash" aria-hidden="true"></i>Clear routes
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="settings-mini-panel h-100">
                                    <h3 class="settings-mini-panel__title">
                                        <span class="settings-mini-panel__icon settings-mini-panel__icon--orange" aria-hidden="true"><i class="fas fa-eye"></i></span>
                                        View cache
                                    </h3>
                                    <p class="settings-mini-panel__desc">Compiled Blade templates.</p>
                                    <form action="{{ route('settings.view.clear') }}" method="POST" class="settings-form-ajax mt-auto">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-secondary action-btn-compact">
                                            <i class="fas fa-trash" aria-hidden="true"></i>Clear views
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 settings-divider">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="settings-mini-panel settings-mini-panel--danger h-100">
                                    <h3 class="settings-mini-panel__title text-danger">
                                        <span class="settings-mini-panel__icon settings-mini-panel__icon--danger" aria-hidden="true"><i class="fas fa-broom"></i></span>
                                        Clear all caches
                                    </h3>
                                    <p class="settings-mini-panel__desc">Clears application, config, route, and view caches in one step.</p>
                                    <form action="{{ route('settings.clear-all') }}" method="POST" class="settings-form-ajax mt-auto" data-confirm="Are you sure you want to clear all caches?">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-destructive action-btn-compact">
                                            <i class="fas fa-trash-alt" aria-hidden="true"></i>Clear all
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="settings-mini-panel h-100">
                                    <h3 class="settings-mini-panel__title">
                                        <span class="settings-mini-panel__icon settings-mini-panel__icon--blue" aria-hidden="true"><i class="fas fa-rocket"></i></span>
                                        Optimize application
                                    </h3>
                                    <p class="settings-mini-panel__desc">Removes compiled Laravel caches (optimize:clear): bootstrap config, routes, views, and related.</p>
                                    <form action="{{ route('settings.optimize') }}" method="POST" class="settings-form-ajax mt-auto" data-confirm="This removes compiled caches (optimize:clear). Continue?">
                                        @csrf
                                        <button type="submit" class="action-btn action-btn-primary action-btn-compact">
                                            <i class="fas fa-magic" aria-hidden="true"></i>Optimize
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Control -->
                <div class="tab-pane fade" id="settings-upload-control" role="tabpanel" aria-labelledby="upload-control-tab" tabindex="0">
                    <div class="p-4">
                        <header class="settings-tab-header">
                            <h2 class="panel-title settings-tab-header__title"><i class="fas fa-upload" aria-hidden="true"></i> Upload control</h2>
                            <p class="settings-tab-lead">Set global upload limits and overrides per context (floor plan, booth, avatar, HR documents, and more).</p>
                        </header>
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
                        <header class="settings-tab-header">
                            <h2 class="panel-title settings-tab-header__title"><i class="fas fa-eye" aria-hidden="true"></i> Public view actions</h2>
                            <p class="settings-tab-lead">Control what signed-in users can do on <code class="settings-code">/floor-plans/{id}/public</code>. Map colors and booked ticks are under <strong>Color (map &amp; bookings)</strong>.</p>
                        </header>
                        @include('settings.partials.public-view-behavior-form', [
                            'formId' => 'publicViewBehaviorForm',
                            'idPrefix' => '',
                        ])
                    </div>
                </div>

                <!-- Push Notifications -->
                <div class="tab-pane fade" id="push-notifications" role="tabpanel" aria-labelledby="push-tab" tabindex="0">
                    <div class="p-4">
                        <header class="settings-tab-header">
                            <h2 class="panel-title settings-tab-header__title"><i class="fas fa-bell" aria-hidden="true"></i> Push notifications</h2>
                            <p class="settings-tab-lead">Web Push (VAPID) for alerts when the tab is in the background. Users must still grant permission in the browser.</p>
                        </header>
                        <form id="pushNotificationSettingsForm" action="{{ route('settings.push-notifications.save') }}" method="POST" class="settings-form-ajax">
                            @csrf
                            <div class="settings-mini-panel mb-3">
                                <div class="form-check form-switch d-flex align-items-start gap-3">
                                    <input class="form-check-input settings-form-switch flex-shrink-0" type="checkbox" name="push_notifications_enabled" id="push_notifications_enabled" value="1" {{ ($pushNotificationsEnabled ?? true) ? 'checked' : '' }}>
                                    <div>
                                        <label class="form-check-label fw-semibold" for="push_notifications_enabled">Enable push notifications</label>
                                        <p class="text-muted small mb-0 mt-1">Requires VAPID keys and per-user browser permission.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="settings-mini-panel mb-4">
                                <label class="form-label" for="push_vapid_public_key">VAPID public key (optional)</label>
                                <input type="text" class="form-control font-monospace" name="push_vapid_public_key" id="push_vapid_public_key" value="{{ old('push_vapid_public_key', $pushVapidPublicKey ?? '') }}" placeholder="e.g. BN1a2b3c..." maxlength="500" autocomplete="off">
                                <p class="text-muted small mb-0 mt-2">Use <code class="settings-code">php artisan webpush:vapid</code> or set <code class="settings-code">PUSH_VAPID_PUBLIC_KEY</code> / <code class="settings-code">PUSH_VAPID_PRIVATE_KEY</code> in <code class="settings-code">.env</code>. Never store the private key in the database.</p>
                            </div>
                            <button type="submit" class="action-btn action-btn-primary">
                                <i class="fas fa-save" aria-hidden="true"></i>Save push notification settings
                            </button>
                        </form>
                    </div>
                </div>

                <!-- System Information -->
                <div class="tab-pane fade" id="system-information" role="tabpanel" aria-labelledby="system-info-tab" tabindex="0">
                    <div class="p-4">
                        <header class="settings-tab-header">
                            <h2 class="panel-title settings-tab-header__title"><i class="fas fa-info-circle" aria-hidden="true"></i> System information</h2>
                            <p class="settings-tab-lead">Runtime and application identity. Useful when reporting issues or verifying deployment.</p>
                        </header>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="looker-table-wrapper settings-sys-table-wrap">
                                    <table class="looker-table looker-table--kv">
                                        <tbody>
                                            <tr>
                                                <th scope="row">Laravel version</th>
                                                <td>{{ app()->version() }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">PHP version</th>
                                                <td>{{ PHP_VERSION }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Environment</th>
                                                <td>
                                                    <span class="status-badge {{ app()->environment() === 'production' ? 'status-badge-red' : 'status-badge-blue' }}">{{ app()->environment() }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Debug mode</th>
                                                <td>
                                                    <span class="status-badge {{ config('app.debug') ? 'status-badge-orange' : 'status-badge-green' }}">{{ config('app.debug') ? 'ON' : 'OFF' }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="looker-table-wrapper settings-sys-table-wrap">
                                    <table class="looker-table looker-table--kv">
                                        <tbody>
                                            <tr>
                                                <th scope="row">App name</th>
                                                <td>{{ config('app.name') }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">App URL</th>
                                                <td class="text-break">{{ config('app.url') }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Timezone</th>
                                                <td>{{ config('app.timezone') }}</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Locale</th>
                                                <td>{{ config('app.locale') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles and permissions (features) -->
                <div class="tab-pane fade" id="access-roles-settings" role="tabpanel" aria-labelledby="access-roles-tab" tabindex="0">
                    <div class="p-4">
                        <header class="settings-tab-header">
                            <h2 class="panel-title settings-tab-header__title"><i class="fas fa-user-shield" aria-hidden="true"></i> Roles &amp; features</h2>
                            <p class="settings-tab-lead">Define roles and feature permissions. Assign roles to staff under <strong>Security</strong> here or via <strong>HR → Staff</strong>.</p>
                        </header>
                        <div class="row g-3">
                            <div class="col-12 col-lg-8">
                                <div class="settings-mini-panel h-100">
                                    <h3 class="settings-mini-panel__title">
                                        <span class="settings-mini-panel__icon settings-mini-panel__icon--blue" aria-hidden="true"><i class="fas fa-users-cog"></i></span>
                                        Roles &amp; permissions
                                    </h3>
                                    <p class="settings-mini-panel__desc">One screen with tabs: define roles, attach permissions, and maintain the feature catalog.</p>
                                    <div class="d-flex flex-wrap gap-2 mt-auto pt-2">
                                        <a href="{{ route('staff.access', ['tab' => 'roles']) }}" class="action-btn action-btn-primary action-btn-compact">
                                            <i class="fas fa-user-shield" aria-hidden="true"></i> Roles tab
                                        </a>
                                        <a href="{{ route('staff.access', ['tab' => 'permissions']) }}" class="action-btn action-btn-secondary action-btn-compact">
                                            <i class="fas fa-key" aria-hidden="true"></i> Permissions tab
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Security: user accounts & access administration -->
                <div class="tab-pane fade" id="security-settings" role="tabpanel" aria-labelledby="security-tab" tabindex="0">
                    <div class="p-4">
                        <header class="settings-tab-header">
                            <h2 class="panel-title settings-tab-header__title"><i class="fas fa-shield-alt" aria-hidden="true"></i> Security</h2>
                            <p class="settings-tab-lead">Accounts, passwords, activation, and role assignment. Staff directory: <strong>HR Management → Staff</strong>.</p>
                        </header>
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-5">
                                <div class="settings-mini-panel h-100">
                                    <h3 class="settings-mini-panel__title">
                                        <span class="settings-mini-panel__icon settings-mini-panel__icon--blue" aria-hidden="true"><i class="fas fa-user-lock"></i></span>
                                        User &amp; account security
                                    </h3>
                                    <p class="settings-mini-panel__desc">Staff accounts, passwords, activation status, and role assignment for system access.</p>
                                    <a href="{{ route('users.index') }}" class="action-btn action-btn-primary action-btn-compact mt-auto align-self-start">
                                        <i class="fas fa-arrow-right" aria-hidden="true"></i>Open user administration
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<!-- Company Information Tab -->
            <div class="tab-pane fade" id="company" role="tabpanel" aria-labelledby="company-tab" tabindex="0">
                <div class="p-4">
                    <header class="settings-tab-header">
                        <h2 class="panel-title settings-tab-header__title"><i class="fas fa-building" aria-hidden="true"></i> Company</h2>
                        <p class="settings-tab-lead">Branding and contact details used across the app, emails, and exports.</p>
                    </header>
                    <form id="companySettingsForm">
                        @csrf
                        <div class="settings-mini-panel mb-4">
                            <h3 class="settings-section__title mb-3">Contact &amp; profile</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="company_name">Company name</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter company name" autocomplete="organization">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="company_email">Company email</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email" placeholder="contact@company.com" autocomplete="email">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="company_phone">Company phone</label>
                                    <input type="text" class="form-control" id="company_phone" name="company_phone" placeholder="+1234567890" autocomplete="tel">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="company_website">Company website</label>
                                    <input type="url" class="form-control" id="company_website" name="company_website" placeholder="https://www.company.com" autocomplete="url">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label" for="company_address">Company address</label>
                                    <textarea class="form-control" id="company_address" name="company_address" rows="2" placeholder="Enter company address" autocomplete="street-address"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <div class="settings-mini-panel h-100">
                                    <label class="form-label" for="logoFile">Company logo</label>
                                    <div class="d-flex align-items-start flex-wrap gap-3 mt-2">
                                        <div class="flex-shrink-0">
                                            <img id="logoPreview" src="" alt="" class="settings-media-preview settings-media-preview--logo d-none">
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <input type="file" class="form-control" id="logoFile" accept="image/*" aria-describedby="logoFile_help">
                                            <p class="text-muted small mb-0 mt-2" id="logoFile_help">About 200×80px, PNG or JPG, max 2MB.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" id="company_logo" name="company_logo">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="settings-mini-panel h-100">
                                    <label class="form-label" for="faviconFile">Favicon</label>
                                    <div class="d-flex align-items-start flex-wrap gap-3 mt-2">
                                        <div class="flex-shrink-0">
                                            <img id="faviconPreview" src="" alt="" class="settings-media-preview settings-media-preview--favicon d-none">
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <input type="file" class="form-control" id="faviconFile" accept="image/*" aria-describedby="faviconFile_help">
                                            <p class="text-muted small mb-0 mt-2" id="faviconFile_help">About 32×32px, ICO or PNG, max 512KB.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" id="company_favicon" name="company_favicon">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex flex-wrap gap-2">
                            <button type="submit" class="action-btn action-btn-primary">
                                <i class="fas fa-save" aria-hidden="true"></i>Save company settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Color: floor plan, booth map, booking statuses (single responsive implementation; breakpoints align with project standard) -->
            <div class="tab-pane fade" id="global-color-settings" role="tabpanel" aria-labelledby="global-color-tab" tabindex="0">
                <div class="p-4">
                    <header class="settings-tab-header">
                        <h2 class="panel-title settings-tab-header__title"><i class="fas fa-fill-drip" aria-hidden="true"></i> Color (map &amp; bookings)</h2>
                        <p class="settings-tab-lead">Public map accent, booked-tick look, booth status colors on the floor plan, and booking status colors in the bookings module.</p>
                    </header>

                        <section class="settings-section mb-5 pb-4 settings-section-divider" aria-labelledby="settings-color-floor-plan-heading">
                            <h3 class="settings-section__title mb-2" id="settings-color-floor-plan-heading"><i class="fas fa-map" aria-hidden="true"></i> Public floor plan &amp; booked tick</h3>
                            <p class="settings-tab-lead mb-3">Applies to the public floor plan and the booked-booth checkmark in the designer and public view.</p>
                            @include('settings.partials.floor-plan-color-settings-form', [
                                'formId' => 'floorPlanColorSettingsForm',
                                'idPrefix' => '',
                            ])
                        </section>

                        <section class="settings-section mb-5 pb-4 settings-section-divider color-tab-booth-status" aria-labelledby="settings-color-booth-status-heading">
                            <h3 class="settings-section__title mb-2" id="settings-color-booth-status-heading"><i class="fas fa-th-large" aria-hidden="true"></i> Booth status (floor plan map)</h3>
                            <p class="settings-tab-lead mb-3">Labels and colors for booth states on the canvas and public map. Scope a row to one floor plan or leave it global.</p>
                            <div class="settings-toolbar mb-3">
                                <button type="button" class="action-btn action-btn-primary action-btn-compact" id="ct_btnAddStatus"><i class="fas fa-plus" aria-hidden="true"></i>Add status</button>
                                <button type="button" class="action-btn action-btn-secondary action-btn-compact" id="ct_btnSaveStatusSettings"><i class="fas fa-save" aria-hidden="true"></i>Save booth statuses</button>
                                <button type="button" class="action-btn action-btn-secondary action-btn-compact" id="ct_btnResetStatusSettings"><i class="fas fa-undo" aria-hidden="true"></i>Reload</button>
                                <button type="button" class="action-btn action-btn-warning-outline action-btn-compact" id="ct_btnRestoreBoothDefaults"><i class="fas fa-history" aria-hidden="true"></i>Restore defaults</button>
                            </div>
                            <p class="text-muted small mb-3">Restore defaults replaces all booth statuses with the five factory global statuses (custom and floor-plan-specific rows are removed).</p>
                            <div id="ct_statusSettingsContainer">
                                <div class="settings-inline-hint text-center py-4"><span class="spinner-border spinner-border-sm me-2 text-primary" role="status" aria-hidden="true"></span><span class="text-muted">Open this tab to load booth status colors.</span></div>
                            </div>
                        </section>

                        <section class="settings-section color-tab-booking-status" aria-labelledby="settings-color-booking-status-heading">
                            <h3 class="settings-section__title mb-2" id="settings-color-booking-status-heading"><i class="fas fa-calendar-check" aria-hidden="true"></i> Booking status (bookings module)</h3>
                            <p class="settings-tab-lead mb-3">Labels and colors for booking records in lists, cards, and detail views.</p>
                            <div class="settings-toolbar mb-3">
                                <button type="button" class="action-btn action-btn-primary action-btn-compact" id="bk_btnAddBookingStatus"><i class="fas fa-plus" aria-hidden="true"></i>Add status</button>
                                <button type="button" class="action-btn action-btn-secondary action-btn-compact" id="bk_btnSaveBookingStatuses"><i class="fas fa-save" aria-hidden="true"></i>Save booking statuses</button>
                                <button type="button" class="action-btn action-btn-secondary action-btn-compact" id="bk_btnReloadBookingStatuses"><i class="fas fa-undo" aria-hidden="true"></i>Reload</button>
                                <button type="button" class="action-btn action-btn-warning-outline action-btn-compact" id="bk_btnRestoreBookingDefaults"><i class="fas fa-history" aria-hidden="true"></i>Restore defaults</button>
                            </div>
                            <p class="text-muted small mb-3">Restore defaults resets all booking statuses to the six factory defaults (custom rows are removed).</p>
                            <div id="bk_bookingStatusSettingsContainer">
                                <div class="settings-inline-hint text-center py-4"><span class="spinner-border spinner-border-sm me-2 text-primary" role="status" aria-hidden="true"></span><span class="text-muted">Open this tab to load booking statuses.</span></div>
                            </div>
                        </section>
                    </div>
            </div>

            <!-- Appearance/Colors Tab -->
            <div class="tab-pane fade" id="appearance" role="tabpanel" aria-labelledby="appearance-tab" tabindex="0">
                <div class="p-4">
                    <header class="settings-tab-header">
                        <h2 class="panel-title settings-tab-header__title"><i class="fas fa-palette" aria-hidden="true"></i> System color scheme</h2>
                        <p class="settings-tab-lead">Theme colors for the classic layout (sidebar, navbar, status colors). Refresh other tabs after saving to see full effect.</p>
                    </header>

                        <form id="appearanceSettingsForm">
                            @csrf
                            <div class="row g-4">
                                <!-- Primary Colors -->
                                <div class="col-md-6">
                                    <div class="settings-mini-panel h-100">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                        <h3 class="settings-section__title mb-0">Primary colors</h3>
                                        <button type="button" class="action-btn action-btn-secondary action-btn-compact appearance-restore-section" data-appearance-section="primary">Restore defaults</button>
                                    </div>
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
                                </div>

                                <!-- Status Colors -->
                                <div class="col-md-6">
                                    <div class="settings-mini-panel h-100">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                        <h3 class="settings-section__title mb-0">Status colors</h3>
                                        <button type="button" class="action-btn action-btn-secondary action-btn-compact appearance-restore-section" data-appearance-section="status">Restore defaults</button>
                                    </div>
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
                                </div>

                                <!-- Layout Colors -->
                                <div class="col-md-12">
                                    <div class="settings-mini-panel">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                        <h3 class="settings-section__title mb-0">Layout colors</h3>
                                        <button type="button" class="action-btn action-btn-secondary action-btn-compact appearance-restore-section" data-appearance-section="layout">Restore defaults</button>
                                    </div>
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
                            </div>

                            <div class="mt-4 d-flex flex-wrap gap-2">
                                <button type="submit" class="action-btn action-btn-primary">
                                    <i class="fas fa-save" aria-hidden="true"></i>Save appearance settings
                                </button>
                                <button type="button" class="action-btn action-btn-secondary" id="resetColors">
                                    <i class="fas fa-undo" aria-hidden="true"></i>Reset all to defaults
                                </button>
                            </div>
                        </form>
                    </div>
            </div>

            <!-- CDN Settings Tab -->
            <div class="tab-pane fade" id="cdn" role="tabpanel" aria-labelledby="cdn-tab" tabindex="0">
                <div class="p-4">
                    <header class="settings-tab-header">
                        <h2 class="panel-title settings-tab-header__title"><i class="fas fa-cloud" aria-hidden="true"></i> CDN settings</h2>
                        <p class="settings-tab-lead">Load shared CSS/JS from a CDN or from files on this server (affects Chart.js, Bootstrap vendor bundles, etc., where the layout supports it).</p>
                    </header>

                        <div class="settings-callout settings-callout--info mb-4" role="note">
                            <p class="settings-callout__title mb-2"><i class="fas fa-info-circle me-2" aria-hidden="true"></i>CDN vs local</p>
                            <ul class="mb-0 ps-3 small">
                                <li><strong>CDN on:</strong> Often faster globally; needs internet from the browser.</li>
                                <li><strong>Local:</strong> Served from your host; better for air-gapped or strict networks.</li>
                            </ul>
                        </div>

                        <form id="cdnSettingsForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="settings-mini-panel">
                                        <div class="form-check form-switch d-flex align-items-start gap-3">
                                            <input class="form-check-input settings-cdn-switch flex-shrink-0" type="checkbox" id="use_cdn" name="use_cdn" aria-describedby="use_cdn_help">
                                            <div>
                                                <label class="form-check-label fw-semibold" for="use_cdn">Use CDN for assets</label>
                                                <p class="text-muted small mb-0 mt-1" id="use_cdn_help">When enabled, CSS and JavaScript libraries load from the CDN instead of local files.</p>
                                            </div>
                                        </div>

                                        <div id="cdnStatus" class="settings-cdn-status mt-3 p-3 rounded d-none" role="status" aria-live="polite">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-circle me-2 settings-cdn-status__dot" aria-hidden="true"></i>
                                                <span id="cdnStatusText"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 d-flex flex-wrap gap-2">
                                <button type="submit" class="action-btn action-btn-primary">
                                    <i class="fas fa-save" aria-hidden="true"></i>Save CDN settings
                                </button>
                                <button type="button" class="action-btn action-btn-secondary" id="cdnRefreshPageHintBtn">
                                    <i class="fas fa-sync-alt" aria-hidden="true"></i>Refresh page
                                </button>
                            </div>
                        </form>
                    </div>
            </div>

            <!-- Module Display Customize Tab -->
            <div class="tab-pane fade" id="module-display" role="tabpanel" aria-labelledby="module-display-tab" tabindex="0">
                <div class="p-4">
                    <header class="settings-tab-header">
                        <h2 class="panel-title settings-tab-header__title"><i class="fas fa-mobile-alt" aria-hidden="true"></i> Module display</h2>
                        <p class="settings-tab-lead">Show or hide sidebar modules on smaller viewports. Desktop navigation is unchanged.</p>
                    </header>

                        <div class="settings-callout settings-callout--info mb-4" role="note">
                            <p class="settings-callout__title mb-2"><i class="fas fa-tablet-alt me-2" aria-hidden="true"></i>Device scope</p>
                            <ul class="mb-0 ps-3 small">
                                <li><strong>Mobile:</strong> up to 768px wide.</li>
                                <li><strong>Tablet:</strong> 769px–1024px.</li>
                                <li><strong>Desktop:</strong> not controlled here.</li>
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


@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<style>
    /* Settings (/settings): single responsive layout; breakpoints match project standard (576 / 768 / 992 / 1200). */
    .settings-page.looker-dashboard {
        padding: clamp(1rem, 2.5vw, 2rem) !important;
        box-sizing: border-box;
    }
    .settings-page .settings-tabs-panel {
        box-shadow: var(--shadow-md);
    }
    .settings-page .settings-tabs-panel:hover {
        transform: none;
    }
    .settings-page .settings-divider {
        border-color: var(--border-light);
        opacity: 1;
    }
    .settings-page .settings-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }
    .settings-page .action-btn-compact {
        padding: 0.45rem 0.9rem;
        font-size: 0.8125rem;
    }
    .settings-page .action-btn-destructive {
        background: #dc3545;
        color: #fff;
        box-shadow:
            0 4px 0 0 rgba(120, 20, 30, 0.35),
            0 5px 14px rgba(220, 53, 69, 0.28),
            inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    .settings-page .action-btn-destructive:hover {
        background: #c82333;
        color: #fff;
        transform: translateY(-2px);
    }
    .settings-page .action-btn-destructive:active {
        transform: translateY(3px) scale(0.985);
    }
    .settings-page .action-btn-warning-outline {
        background: linear-gradient(180deg, #fffdf8 0%, #fff6e8 100%);
        color: var(--accent-orange);
        border: 1px solid rgba(255, 149, 0, 0.45);
        box-shadow: 0 2px 0 0 rgba(255, 149, 0, 0.12), 0 3px 10px rgba(255, 149, 0, 0.08);
    }
    .settings-page .action-btn-warning-outline:hover {
        border-color: var(--accent-orange);
        color: #c65a00;
        transform: translateY(-1px);
    }
    .settings-page .settings-mini-panel {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
        min-height: 100%;
        padding: 1.25rem 1.35rem;
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        transition: var(--transition-all);
    }
    .settings-page .settings-mini-panel:hover {
        background: var(--bg-card-hover);
        box-shadow: var(--shadow-md);
    }
    .settings-page .settings-mini-panel--danger {
        border-color: rgba(255, 59, 48, 0.35);
        background: rgba(255, 59, 48, 0.04);
    }
    .settings-page .settings-mini-panel__title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .settings-page .settings-mini-panel__desc {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin: 0;
        flex: 1 1 auto;
    }
    .settings-page .settings-mini-panel__icon {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-pill);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .settings-page .settings-mini-panel__icon--blue {
        background: var(--accent-blue-light);
        color: var(--accent-blue);
    }
    .settings-page .settings-mini-panel__icon--green {
        background: var(--accent-green-light);
        color: var(--accent-green);
    }
    .settings-page .settings-mini-panel__icon--orange {
        background: var(--accent-orange-light);
        color: var(--accent-orange);
    }
    .settings-page .settings-mini-panel__icon--purple {
        background: var(--accent-purple-light);
        color: var(--accent-purple);
    }
    .settings-page .settings-mini-panel__icon--danger {
        background: rgba(255, 59, 48, 0.15);
        color: #ff3b30;
    }
    .settings-page .status-badge-red {
        background: rgba(255, 59, 48, 0.15);
        color: #ff3b30;
    }
    .settings-page .settings-tab-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px dashed var(--border-light);
    }
    .settings-page .settings-tab-header__title {
        margin-bottom: 0.35rem;
    }
    .settings-page .settings-tab-lead {
        color: var(--text-secondary);
        font-size: 0.9375rem;
        line-height: 1.5;
        max-width: 52rem;
        margin: 0;
    }
    .settings-page .settings-code {
        font-size: 0.875em;
        padding: 0.1em 0.35em;
        border-radius: 6px;
        background: rgba(0, 0, 0, 0.04);
        color: var(--text-secondary);
    }
    .settings-page .settings-section__title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .settings-page .settings-section__title i {
        color: var(--accent-blue);
        font-size: 1rem;
    }
    .settings-page .settings-section-divider {
        border-bottom: 1px solid var(--border-light);
    }
    .settings-page .settings-callout {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-light);
        padding: 1rem 1.15rem;
        background: var(--bg-card);
    }
    .settings-page .settings-callout--info {
        border-color: rgba(0, 122, 255, 0.22);
        background: var(--accent-blue-light);
    }
    .settings-page .settings-callout--danger {
        border-color: rgba(255, 59, 48, 0.35);
        background: rgba(255, 59, 48, 0.08);
        color: var(--text-primary);
        font-weight: 600;
    }
    .settings-page .settings-callout__title {
        font-weight: 700;
        margin: 0;
        color: var(--text-primary);
    }
    .settings-page .settings-nested-panel {
        padding: 1rem 1rem 1rem 1.15rem;
        border-left: 4px solid var(--accent-blue);
        border-radius: 0 var(--radius-md) var(--radius-md) 0;
        background: rgba(255, 255, 255, 0.35);
        border-top: 1px solid var(--border-light);
        border-right: 1px solid var(--border-light);
        border-bottom: 1px solid var(--border-light);
    }
    .settings-page .settings-input-narrow {
        max-width: 20rem;
    }
    .settings-page .settings-form-switch {
        width: 3rem;
        min-width: 3rem;
        height: 1.5rem;
        cursor: pointer;
    }
    .settings-page .settings-module-switch {
        width: 2.75rem;
        min-width: 2.75rem;
        height: 1.35rem;
        cursor: pointer;
    }
    .settings-page .settings-upload-table-wrap .looker-table th,
    .settings-page .settings-upload-table-wrap .looker-table td {
        vertical-align: middle;
    }
    .settings-page .settings-upload-col-size {
        width: 9rem;
        max-width: 30%;
    }
    .settings-page .looker-table.looker-table--compact th,
    .settings-page .looker-table.looker-table--compact td {
        padding: 0.65rem 0.5rem;
        font-size: 0.875rem;
    }
    .settings-page .settings-module-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        padding: 1.25rem 1.35rem;
        background: var(--bg-card);
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        transition: var(--transition-all);
    }
    .settings-page .settings-module-card:hover {
        background: var(--bg-card-hover);
        box-shadow: var(--shadow-md);
    }
    .settings-page .settings-module-card__icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-pill);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .settings-page .settings-module-card__icon--blue {
        background: var(--accent-blue-light);
        color: var(--accent-blue);
    }
    .settings-page .settings-module-card__icon--purple {
        background: var(--accent-purple-light);
        color: var(--accent-purple);
    }
    .settings-page .settings-module-card__icon--green {
        background: var(--accent-green-light);
        color: var(--accent-green);
    }
    .settings-page .settings-module-card__icon--orange {
        background: var(--accent-orange-light);
        color: var(--accent-orange);
    }
    .settings-page .settings-module-card__name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    .settings-page .settings-module-card__desc {
        font-size: 0.8125rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }
    .settings-page .settings-module-loading__spinner {
        width: 2.5rem;
        height: 2.5rem;
        color: var(--accent-blue);
    }
    .settings-page .settings-media-preview {
        object-fit: contain;
        border: 1px solid var(--border-light);
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, 0.6);
    }
    .settings-page .settings-media-preview--logo {
        max-width: 150px;
        max-height: 80px;
        padding: 6px;
    }
    .settings-page .settings-media-preview--favicon {
        width: 40px;
        height: 40px;
        padding: 4px;
    }
    .settings-page .settings-inline-hint {
        border-radius: var(--radius-md);
        border: 1px dashed var(--border-light);
        background: rgba(255, 255, 255, 0.25);
    }
    .settings-page .settings-sys-table-wrap .looker-table.looker-table--kv tbody th {
        width: 42%;
        max-width: 12rem;
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
        background: rgba(255, 255, 255, 0.35);
        border-right: 1px solid var(--border-light);
        vertical-align: middle;
    }
    .settings-page .settings-sys-table-wrap .looker-table.looker-table--kv tbody td {
        font-size: 0.9375rem;
        color: var(--text-primary);
        vertical-align: middle;
    }
    .settings-page .settings-cdn-switch {
        width: 3rem;
        height: 1.5rem;
        min-width: 3rem;
        cursor: pointer;
    }
    .settings-page .settings-cdn-status {
        border: 1px solid var(--border-light);
    }
    .settings-page .settings-cdn-status--on {
        background: var(--accent-blue-light);
        color: var(--text-primary);
        border-color: rgba(0, 122, 255, 0.25);
    }
    .settings-page .settings-cdn-status--off {
        background: var(--accent-green-light);
        color: var(--text-primary);
        border-color: rgba(52, 199, 89, 0.25);
    }
    .settings-page .settings-cdn-status__dot {
        font-size: 0.65rem;
    }
    .settings-page .settings-cdn-status--on .settings-cdn-status__dot--muted {
        color: var(--accent-blue);
    }
    .settings-page .settings-cdn-status--off .settings-cdn-status__dot--local {
        color: var(--accent-green);
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
        padding: 0.65rem 0.45rem;
        min-width: 0;
        overflow: hidden;
        vertical-align: middle;
    }
    .color-tab-booth-status .khb-booth-status-scroll .looker-table th {
        white-space: normal;
        line-height: 1.25;
        hyphens: auto;
        word-break: break-word;
        font-size: 0.75rem;
        letter-spacing: 0.04em;
    }
    /* Booth status row: calmer bands + separator (replaces flat zebra) */
    .color-tab-booth-status .looker-table tbody tr.khb-booth-status-row td {
        background: rgba(255, 255, 255, 0.72);
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.65);
    }
    .color-tab-booth-status .looker-table tbody tr.khb-booth-status-row:nth-child(even) td {
        background: rgba(248, 250, 252, 0.88);
    }
    .color-tab-booth-status .looker-table tbody tr.khb-booth-status-row:hover td {
        background: rgba(255, 255, 255, 0.95) !important;
        border-bottom-color: rgba(0, 122, 255, 0.12);
    }
    .color-tab-booth-status .looker-table tbody tr.khb-booth-status-row:last-child td {
        border-bottom-color: transparent;
    }
    /* Fields: dedicated tokens (avoid glass-input / !important from other sheets) */
    .color-tab-booth-status .booth-status-table .khb-booth-field,
    .color-tab-booth-status .booth-status-table select.khb-booth-field {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        display: block;
        margin: 0;
        font-family: inherit;
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 0.5rem 0.65rem;
        font-size: 0.875rem;
        line-height: 1.35;
        min-height: 44px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .color-tab-booth-status .booth-status-table select.khb-booth-field {
        cursor: pointer;
        padding-right: 2rem;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='%23666' d='M1.41 0L6 4.58 10.59 0 12 1.41l-6 6-6-6z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.65rem center;
        background-size: 10px;
    }
    .color-tab-booth-status .booth-status-table .khb-booth-field:hover {
        border-color: rgba(0, 122, 255, 0.25);
        background: #fff;
    }
    .color-tab-booth-status .booth-status-table .khb-booth-field:focus {
        outline: none;
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 3px var(--accent-blue-light);
        background: #fff;
    }
    .color-tab-booth-status .booth-status-table .khb-booth-field::placeholder {
        color: var(--text-tertiary);
    }
    .color-tab-booth-status .booth-status-table .khb-booth-field--num {
        text-align: center;
        font-variant-numeric: tabular-nums;
        padding-left: 0.35rem;
        padding-right: 0.35rem;
    }
    .color-tab-booth-status .booth-status-table .khb-booth-field--hex {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.8125rem;
        letter-spacing: 0.02em;
    }
    .color-tab-booth-status .booth-status-table .khb-booth-swatch {
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        min-width: 44px;
        min-height: 44px;
        padding: 3px;
        border: 2px solid rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        cursor: pointer;
        background-clip: padding-box;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.4);
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }
    .color-tab-booth-status .booth-status-table .khb-booth-swatch:hover {
        transform: scale(1.04);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.5), 0 2px 8px rgba(0, 122, 255, 0.15);
    }
    .color-tab-booth-status .booth-status-table .khb-booth-swatch:focus {
        outline: none;
        box-shadow: 0 0 0 3px var(--accent-blue-light), inset 0 0 0 1px rgba(255, 255, 255, 0.5);
    }
    .color-tab-booth-status .khb-color-pair {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.4rem;
        min-width: 0;
    }
    .color-tab-booth-status .khb-color-pair--row {
        flex-direction: row;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0.5rem;
    }
    .color-tab-booth-status .khb-color-pair--row .khb-booth-field--hex {
        flex: 1 1 auto;
        min-width: 0;
        min-height: 44px;
    }
    @media (max-width: 575.98px) {
        .color-tab-booth-status .khb-color-pair--row {
            flex-wrap: wrap;
        }
        .color-tab-booth-status .khb-color-pair--row .khb-booth-swatch {
            width: 40px;
            height: 40px;
            min-width: 40px;
            min-height: 40px;
        }
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
    .color-tab-booth-status .khb-status-drag-handle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        min-height: 44px;
        color: var(--text-tertiary);
        cursor: grab;
        border-radius: 12px;
        flex-shrink: 0;
        touch-action: none;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: rgba(255, 255, 255, 0.85);
        transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }
    .color-tab-booth-status .khb-status-drag-handle:hover {
        background: #fff;
        border-color: rgba(0, 122, 255, 0.22);
        color: var(--accent-blue);
    }
    .color-tab-booth-status .khb-status-drag-handle:active {
        cursor: grabbing;
    }
    .color-tab-booth-status .khb-bs-order-stack {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        min-width: 0;
        flex-wrap: nowrap;
    }
    .color-tab-booth-status .khb-bs-order-stack .status-sort-order {
        flex: 1 1 auto;
        min-width: 0;
        max-width: 100%;
    }
    .color-tab-booth-status .khb-bs-name-cell .khb-booth-field,
    .color-tab-booth-status .khb-bs-desc-cell .khb-booth-field {
        text-overflow: ellipsis;
    }
    .color-tab-booth-status .khb-bs-floor-cell .khb-booth-field,
    .color-tab-booth-status .khb-bs-select-cell .khb-booth-field {
        min-width: 0;
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
        background: var(--accent-blue-light);
        color: var(--accent-blue);
    }
    .color-tab-booth-status .looker-table tbody tr.khb-booth-status-row {
        cursor: default;
    }
    .color-tab-booth-status .khb-bs-actions-cell .action-btn {
        max-width: 100%;
        min-width: 2.5rem;
        justify-content: center;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    @media (max-width: 575.98px) {
        .color-tab-booth-status .khb-booth-status-scroll .looker-table th,
        .color-tab-booth-status .khb-booth-status-scroll .looker-table td {
            padding: 0.35rem 0.2rem;
        }
    }
    /* Booking status table (Color tab) — same shell + looker-table treatment as booth statuses */
    .color-tab-booking-status .khb-booking-status-table-shell {
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.45);
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 4px 24px rgba(31, 38, 135, 0.08);
        overflow: hidden;
        width: 100%;
        max-width: 100%;
    }
    .color-tab-booking-status .khb-booking-status-scroll {
        max-height: min(70vh, 720px);
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        width: 100%;
    }
    .color-tab-booking-status .khb-booking-status-scroll .khb-booking-status-table {
        width: 100%;
        max-width: 100%;
        table-layout: fixed;
        border-collapse: separate;
    }
    .color-tab-booking-status .khb-booking-status-scroll .looker-table th,
    .color-tab-booking-status .khb-booking-status-scroll .looker-table td {
        padding: 0.5rem 0.35rem;
        min-width: 0;
        overflow: hidden;
        vertical-align: middle;
    }
    .color-tab-booking-status .khb-booking-status-scroll .looker-table th {
        white-space: normal;
        line-height: 1.2;
        hyphens: auto;
        word-break: break-word;
    }
    .color-tab-booking-status .khb-booking-status-scroll thead th {
        position: sticky;
        top: 0;
        z-index: 4;
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06);
        background: rgba(248, 250, 252, 0.92) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .color-tab-booking-status .khb-bk-color-pair {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0.25rem;
        min-width: 0;
    }
    .color-tab-booking-status .khb-bk-color-swatch {
        height: 34px;
        width: 100%;
        max-width: 42px;
        min-height: 34px;
        padding: 2px;
        border: 2px solid rgba(0, 0, 0, 0.08);
        border-radius: 8px;
        cursor: pointer;
    }
    .color-tab-booking-status .khb-bk-color-pair .form-control-sm {
        font-variant-numeric: tabular-nums;
        font-size: 0.8125rem;
        min-height: 38px;
    }
    .color-tab-booking-status .khb-bk-order .bk-sort-order,
    .color-tab-booking-status .khb-bk-code .bk-status-code {
        min-height: 38px;
        text-align: center;
    }
    .color-tab-booking-status .khb-bk-name .bk-name,
    .color-tab-booking-status .khb-bk-desc .bk-desc {
        min-height: 38px;
        text-overflow: ellipsis;
    }
    .color-tab-booking-status .khb-bk-badge .bk-badge {
        min-height: 38px;
    }
    .color-tab-booking-status .khb-bk-check .form-check-input {
        width: 1.15rem;
        height: 1.15rem;
        margin-top: 0;
        cursor: pointer;
    }
    .color-tab-booking-status .looker-table tbody tr.khb-booking-status-row {
        cursor: default;
    }
    .color-tab-booking-status .looker-table tbody tr.khb-booking-status-row:nth-child(even) td {
        background-color: rgba(255, 255, 255, 0.12);
    }
    .color-tab-booking-status .looker-table tbody tr.khb-booking-status-row:hover td {
        background-color: rgba(13, 110, 253, 0.06) !important;
    }
    .color-tab-booking-status .khb-bk-actions .action-btn {
        min-width: 2.5rem;
        justify-content: center;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    @media (max-width: 575.98px) {
        .color-tab-booking-status .khb-booking-status-scroll .looker-table th,
        .color-tab-booking-status .khb-booking-status-scroll .looker-table td {
            padding: 0.35rem 0.2rem;
        }
    }
    /* Upload + system key-value tables */
    .settings-page .settings-upload-table-wrap .looker-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: rgba(248, 250, 252, 0.92) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06);
    }
    /* Tab bar: horizontal scroll on narrow viewports; touch-friendly targets (≥44px) */
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
                    
                    if (data.company_logo) {
                        var base = '{{ rtrim(url("/"), "/") }}';
                        var path = (data.company_logo + '').replace(/^\/+/, '').replace(/\\/g, '/');
                        $('#logoPreview').attr('src', path ? base + '/' + path : '').removeClass('d-none');
                    } else {
                        $('#logoPreview').attr('src', '').addClass('d-none');
                    }

                    if (data.company_favicon) {
                        var base2 = '{{ rtrim(url("/"), "/") }}';
                        var path2 = (data.company_favicon + '').replace(/^\/+/, '').replace(/\\/g, '/');
                        $('#faviconPreview').attr('src', path2 ? base2 + '/' + path2 : '').removeClass('d-none');
                    } else {
                        $('#faviconPreview').attr('src', '').addClass('d-none');
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
        const dot = statusDiv.find('.settings-cdn-status__dot');

        statusDiv.removeClass('d-none settings-cdn-status--on settings-cdn-status--off');
        dot.removeClass('settings-cdn-status__dot--muted settings-cdn-status__dot--local');

        if (useCDN) {
            statusDiv.addClass('settings-cdn-status--on');
            statusText.html('<strong>CDN enabled:</strong> assets load from CDN servers.');
            dot.addClass('settings-cdn-status__dot--muted');
        } else {
            statusDiv.addClass('settings-cdn-status--off');
            statusText.html('<strong>Local assets:</strong> libraries load from this server.');
            dot.addClass('settings-cdn-status__dot--local');
        }
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
                    $('#logoPreview').attr('src', response.url).removeClass('d-none');
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
                    $('#faviconPreview').attr('src', response.url).removeClass('d-none');
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
                if (response.status === 200 && response.data) {
                    renderModuleDisplaySettings(response.data);
                } else {
                    toastr.error(response.message || 'Failed to load module display settings');
                    $('#moduleDisplayContainer').html('<div class="col-12"><div class="settings-callout settings-callout--danger" role="alert">Failed to load settings. Please refresh the page or open this tab again.</div></div>');
                }
            })
            .fail(function() {
                toastr.error('Failed to load module display settings');
                $('#moduleDisplayContainer').html('<div class="col-12"><div class="settings-callout settings-callout--danger" role="alert">Failed to load settings. Please refresh the page.</div></div>');
            });
    }

    var moduleIconToneClasses = ['settings-module-card__icon--blue', 'settings-module-card__icon--purple', 'settings-module-card__icon--green', 'settings-module-card__icon--orange'];
    function renderModuleDisplaySettings(settings) {
        let html = '';
        var toneIndex = 0;

        Object.keys(moduleConfig).forEach(function(moduleKey) {
            const module = moduleConfig[moduleKey];
            const moduleSettings = settings[moduleKey] || { mobile: true, tablet: true };
            var toneClass = moduleIconToneClasses[toneIndex % moduleIconToneClasses.length];
            toneIndex += 1;

            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="settings-module-card h-100">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <span class="settings-module-card__icon ${toneClass}" aria-hidden="true"><i class="fas ${module.icon}"></i></span>
                            <div class="min-w-0 flex-grow-1">
                                <h3 class="settings-module-card__name mb-1">${module.name}</h3>
                                <p class="settings-module-card__desc mb-0">${module.description}</p>
                            </div>
                        </div>
                        <div class="row g-2 mt-auto">
                            <div class="col-6">
                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                    <input class="form-check-input settings-module-switch module-toggle" type="checkbox"
                                           id="module_${moduleKey}_mobile"
                                           data-module="${moduleKey}"
                                           data-device="mobile"
                                           ${moduleSettings.mobile ? 'checked' : ''}>
                                    <label class="form-check-label small mb-0" for="module_${moduleKey}_mobile">
                                        <i class="fas fa-mobile-alt me-1" aria-hidden="true"></i>Mobile
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check form-switch d-flex align-items-center gap-2">
                                    <input class="form-check-input settings-module-switch module-toggle" type="checkbox"
                                           id="module_${moduleKey}_tablet"
                                           data-module="${moduleKey}"
                                           data-device="tablet"
                                           ${moduleSettings.tablet ? 'checked' : ''}>
                                    <label class="form-check-label small mb-0" for="module_${moduleKey}_tablet">
                                        <i class="fas fa-tablet-alt me-1" aria-hidden="true"></i>Tablet
                                    </label>
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

    // Load module display settings when tab is shown (initial spinner state, or after a load error)
    $('#module-display-tab').on('shown.bs.tab', function() {
        var $c = $('#moduleDisplayContainer');
        var loading = $c.children().length === 1 && $c.find('.spinner-border').length > 0;
        var loadError = $c.find('.settings-callout--danger').length > 0;
        if (loading || loadError) {
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
            'access-roles-settings': 'access-roles-tab',
            'security-settings': 'security-tab',
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

    var settingsTabButtonToHash = {
        'cache-tab': 'cache-management',
        'upload-control-tab': 'settings-upload-control',
        'public-view-tab': 'settings-public-view',
        'push-tab': 'push-notifications',
        'system-info-tab': 'system-information',
        'access-roles-tab': 'access-roles-settings',
        'security-tab': 'security-settings',
        'company-tab': 'company',
        'global-color-tab': 'global-color-settings',
        'appearance-tab': 'appearance',
        'cdn-tab': 'cdn',
        'module-display-tab': 'module-display'
    };

    $('#globalSettingsTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        var btnId = e.target && e.target.id;
        var h = settingsTabButtonToHash[btnId];
        if (!h) {
            return;
        }
        var next = '#' + h;
        if (window.location.hash === next) {
            return;
        }
        if (history.replaceState) {
            history.replaceState(null, '', window.location.pathname + window.location.search + next);
        } else {
            window.location.hash = h;
        }
        try {
            window.dispatchEvent(new HashChangeEvent('hashchange'));
        } catch (err) {
            window.dispatchEvent(new Event('hashchange'));
        }
    });

    $(document).ready(function() {
        loadCompanySettings();
        loadAppearanceSettings();
        loadCDNSettings();
        applySettingsHashTab();
        window.addEventListener('hashchange', applySettingsHashTab);
    });
</script>
@endpush
@endsection
