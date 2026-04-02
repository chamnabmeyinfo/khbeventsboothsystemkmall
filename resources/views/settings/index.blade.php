@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class='looker-dashboard'>
@include('settings._index_tabs_body')
</div>


@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
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
    /* Booth status table in Color tab (matches booth management modal styling) */
    .color-tab-booth-status .glass-input-color {
        height: 38px;
        width: 60px;
        border: 2px solid #dee2e6;
        border-radius: 6px;
        cursor: pointer;
        padding: 2px;
    }
    .color-tab-booth-status .looker-table tbody tr {
        cursor: move;
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
