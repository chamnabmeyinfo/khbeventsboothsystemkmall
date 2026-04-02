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
