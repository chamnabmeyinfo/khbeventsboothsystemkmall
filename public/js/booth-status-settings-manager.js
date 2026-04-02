/**
 * Booth status settings table (floor plan map colors). Used on Global Settings → Color tab.
 * Call khbInitBoothStatusSettings(config) after DOM ready.
 */
(function (window, $) {
    'use strict';

    function escapeHtml(s) {
        if (s == null || s === '') {
            return '';
        }
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderStatusRow(status, index, floorPlans, idPrefix) {
        var rowId = 'status-row-' + (status.id || 'new-' + index);
        var html = '<tr id="' + rowId + '" data-status-id="' + (status.id || '') + '" data-status-code="' + status.status_code + '">';

        html += '<td><input type="number" class="glass-input glass-input-sm status-sort-order" value="' + (status.sort_order || 0) + '" min="0" style="width: 60px;"></td>';
        html += '<td><input type="number" class="glass-input glass-input-sm status-code" value="' + status.status_code + '" min="1" required style="width: 70px;"></td>';
        html += '<td><input type="text" class="glass-input glass-input-sm status-name" value="' + escapeHtml(status.status_name || '') + '" required maxlength="100"></td>';

        html += '<td><div class="input-group input-group-sm"><input type="color" class="glass-input glass-input-color status-bg-color" value="' + (status.status_color || '#28a745') + '" style="width: 60px; height: 38px;"><input type="text" class="glass-input glass-input-sm status-bg-color-text" value="' + (status.status_color || '#28a745') + '" maxlength="7" style="width: 80px;"></div></td>';
        html += '<td><div class="input-group input-group-sm"><input type="color" class="glass-input glass-input-color status-border-color" value="' + (status.border_color || status.status_color || '#28a745') + '" style="width: 60px; height: 38px;"><input type="text" class="glass-input glass-input-sm status-border-color-text" value="' + (status.border_color || status.status_color || '#28a745') + '" maxlength="7" style="width: 80px;"></div></td>';
        html += '<td><input type="number" class="glass-input glass-input-sm status-border-width" value="' + (status.border_width || 2) + '" min="0" max="10" style="width: 80px;" title="Border width (0-10px)"></td>';

        html += '<td><select class="glass-input glass-input-sm status-border-style" style="width: 100px;">';
        var borderStyles = ['solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset', 'none'];
        borderStyles.forEach(function (style) {
            html += '<option value="' + style + '"' + ((status.border_style || 'solid') === style ? ' selected' : '') + '>' + style.charAt(0).toUpperCase() + style.slice(1) + '</option>';
        });
        html += '</select></td>';

        html += '<td><input type="number" class="glass-input glass-input-sm status-border-radius" value="' + (status.border_radius || 4) + '" min="0" max="50" style="width: 80px;" title="Border radius (0-50px)"></td>';

        html += '<td><div class="input-group input-group-sm"><input type="color" class="glass-input glass-input-color status-text-color" value="' + (status.text_color || '#ffffff') + '" style="width: 60px; height: 38px;"><input type="text" class="glass-input glass-input-sm status-text-color-text" value="' + (status.text_color || '#ffffff') + '" maxlength="7" style="width: 80px;"></div></td>';

        html += '<td><select class="glass-input glass-input-sm status-badge-color">';
        var badgeColors = ['success', 'info', 'warning', 'danger', 'primary', 'secondary', 'dark', 'light'];
        badgeColors.forEach(function (color) {
            html += '<option value="' + color + '"' + (status.badge_color === color ? ' selected' : '') + '>' + color.charAt(0).toUpperCase() + color.slice(1) + '</option>';
        });
        html += '</select></td>';

        html += '<td><input type="text" class="glass-input glass-input-sm status-description" value="' + escapeHtml(status.description || '') + '" placeholder="Status description"></td>';

        html += '<td><select class="glass-input glass-input-sm status-floor-plan">';
        html += '<option value="">Global (All Floor Plans)</option>';
        (floorPlans || []).forEach(function (fp) {
            var sel = parseInt(status.floor_plan_id, 10) === parseInt(fp.id, 10) ? ' selected' : '';
            html += '<option value="' + fp.id + '"' + sel + '>' + escapeHtml(fp.name) + '</option>';
        });
        html += '</select></td>';

        html += '<td class="text-center"><input type="checkbox" class="form-check-input status-is-default" ' + (status.is_default ? 'checked' : '') + '></td>';
        html += '<td class="text-center"><input type="checkbox" class="form-check-input status-is-active" ' + (status.is_active !== false ? 'checked' : '') + '></td>';
        html += '<td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-delete-status" title="Delete"><i class="fas fa-trash"></i></button></td>';
        html += '</tr>';
        return html;
    }

    function renderStatusSettings(statuses, floorPlans, idPrefix) {
        var p = idPrefix || '';
        var $container = $('#' + p + 'statusSettingsContainer');
        if (!statuses || statuses.length === 0) {
            $container.html('<div class="alert alert-info">No status settings found. Click "Add New Status" to create one.</div>');
            return;
        }

        var html = '<div class="table-responsive looker-table-container"><table class="looker-table table-bordered table-hover" id="' + p + 'statusSettingsTable">';
        html += '<thead class="table-light"><tr>';
        html += '<th style="width: 60px;">Order</th><th style="width: 80px;">Code</th><th>Status Name</th>';
        html += '<th style="width: 150px;">Background</th><th style="width: 150px;">Border Color</th><th style="width: 100px;">Border Width</th>';
        html += '<th style="width: 120px;">Border Style</th><th style="width: 100px;">Border Radius</th><th style="width: 150px;">Text</th>';
        html += '<th style="width: 120px;">Badge</th><th>Description</th><th style="width: 200px;">Floor Plan</th>';
        html += '<th style="width: 100px;">Default</th><th style="width: 100px;">Active</th><th style="width: 120px;">Actions</th>';
        html += '</tr></thead><tbody id="' + p + 'statusSettingsBody">';

        statuses.forEach(function (status, index) {
            html += renderStatusRow(status, index, floorPlans, idPrefix);
        });
        html += '</tbody></table></div>';
        $container.html(html);

        $('#' + p + 'statusSettingsBody').sortable({
            handle: '.status-sort-order',
            axis: 'y',
            update: function () {
                $('#' + p + 'statusSettingsBody tr').each(function (index) {
                    $(this).find('.status-sort-order').val(index + 1);
                });
            },
        });

        attachStatusEventHandlers($container, idPrefix, floorPlans);
    }

    function attachStatusEventHandlers($container, idPrefix, floorPlans) {
        var p = idPrefix || '';
        var deleteUrl = window.khbBoothStatusUi.deleteUrl;

        $container.find('.status-bg-color, .status-border-color, .status-text-color').off('change').on('change', function () {
            $(this).siblings('input[type="text"]').val($(this).val());
        });
        $container.find('.status-bg-color-text, .status-border-color-text, .status-text-color-text').off('input').on('input', function () {
            var v = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/.test(v)) {
                $(this).siblings('input[type="color"]').val(v);
            }
        });

        $container.find('.status-is-default').off('change').on('change', function () {
            if ($(this).is(':checked')) {
                $container.find('.status-is-default').not(this).prop('checked', false);
            }
        });

        $container.find('.btn-delete-status').off('click').on('click', function () {
            var row = $(this).closest('tr');
            var statusId = row.data('status-id');
            var statusName = row.find('.status-name').val();
            if (!window.confirm('Are you sure you want to delete status "' + statusName + '"? This action cannot be undone.')) {
                return;
            }
            if (statusId) {
                $.ajax({
                    url: deleteUrl.replace(':id', statusId),
                    method: 'DELETE',
                    data: { _token: window.khbBoothStatusUi.csrf },
                    success: function (response) {
                        if (response.status === 200) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message);
                            }
                            row.fadeOut(300, function () {
                                $(this).remove();
                            });
                        }
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete status';
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        }
                    },
                });
            } else {
                row.fadeOut(300, function () {
                    $(this).remove();
                });
            }
        });
    }

    function loadStatusSettings(cfg) {
        var p = cfg.idPrefix || '';
        $.get(cfg.listUrl)
            .done(function (response) {
                if (response.status === 200) {
                    renderStatusSettings(response.data, cfg.floorPlans, cfg.idPrefix);
                }
            })
            .fail(function () {
                $('#' + p + 'statusSettingsContainer').html('<div class="alert alert-danger">Failed to load status settings</div>');
            });
    }

    // Single global config for delete URL (set by page)
    window.khbBoothStatusUi = window.khbBoothStatusUi || { csrf: '', deleteUrl: '' };

    window.khbInitBoothStatusSettings = function (cfg) {
        if (!cfg || !cfg.listUrl || !cfg.saveUrl) {
            return;
        }
        cfg.idPrefix = cfg.idPrefix || '';
        cfg.floorPlans = cfg.floorPlans || [];
        window.khbBoothStatusUi.csrf = cfg.csrfToken || '';
        window.khbBoothStatusUi.deleteUrl = cfg.deleteUrlTemplate || '';

        var p = cfg.idPrefix || '';
        $('#' + p + 'btnAddStatus').on('click', function () {
            var maxCode = Math.max.apply(
                null,
                $('#' + p + 'statusSettingsBody .status-code')
                    .map(function () {
                        return parseInt($(this).val(), 10) || 0;
                    })
                    .get()
                    .concat([0])
            );
            var newStatus = {
                id: null,
                status_code: maxCode + 1,
                status_name: 'New Status',
                status_color: '#6c757d',
                border_color: '#6c757d',
                border_width: 2,
                border_style: 'solid',
                border_radius: 4,
                text_color: '#ffffff',
                badge_color: 'secondary',
                description: '',
                floor_plan_id: null,
                is_active: true,
                sort_order: ($('#' + p + 'statusSettingsBody tr').length || 0) + 1,
                is_default: false,
            };
            if ($('#' + p + 'statusSettingsBody').length === 0) {
                renderStatusSettings([newStatus], cfg.floorPlans, cfg.idPrefix);
            } else {
                var rowHtml = renderStatusRow(newStatus, $('#' + p + 'statusSettingsBody tr').length, cfg.floorPlans, cfg.idPrefix);
                $('#' + p + 'statusSettingsBody').append(rowHtml);
                attachStatusEventHandlers($('#' + p + 'statusSettingsContainer'), cfg.idPrefix, cfg.floorPlans);
            }
        });

        $('#' + p + 'btnSaveStatusSettings').on('click', function () {
            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

            var statuses = [];
            $('#' + p + 'statusSettingsBody tr').each(function () {
                var row = $(this);
                var statusId = row.data('status-id');
                var floorPlanId = row.find('.status-floor-plan').val();
                statuses.push({
                    id: statusId || null,
                    status_code: parseInt(row.find('.status-code').val(), 10) || 1,
                    status_name: row.find('.status-name').val() || '',
                    status_color: row.find('.status-bg-color-text').val() || '#28a745',
                    border_color: row.find('.status-border-color-text').val() || null,
                    border_width: parseInt(row.find('.status-border-width').val(), 10) || 2,
                    border_style: row.find('.status-border-style').val() || 'solid',
                    border_radius: parseInt(row.find('.status-border-radius').val(), 10) || 4,
                    text_color: row.find('.status-text-color-text').val() || '#ffffff',
                    badge_color: row.find('.status-badge-color').val() || 'success',
                    description: row.find('.status-description').val() || '',
                    floor_plan_id: floorPlanId && floorPlanId !== '' ? parseInt(floorPlanId, 10) : null,
                    is_active: row.find('.status-is-active').is(':checked'),
                    sort_order: parseInt(row.find('.status-sort-order').val(), 10) || 0,
                    is_default: row.find('.status-is-default').is(':checked'),
                });
            });

            $.ajax({
                url: cfg.saveUrl,
                method: 'POST',
                data: {
                    statuses: statuses,
                    _token: cfg.csrfToken,
                },
                success: function (response) {
                    if (response.status === 200) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Status settings saved successfully');
                        }
                        setTimeout(function () {
                            loadStatusSettings(cfg);
                        }, 400);
                    }
                },
                error: function (xhr) {
                    var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
                    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save status settings';
                    if (Object.keys(errors).length > 0) {
                        message += ': ' + Object.values(errors).flat().join(', ');
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html(originalText);
                },
            });
        });

        $('#' + p + 'btnResetStatusSettings').on('click', function () {
            if (!window.confirm('Reload booth status settings from the server? Unsaved changes will be lost.')) {
                return;
            }
            loadStatusSettings(cfg);
        });

        window.khbBoothStatusLastCfg = cfg;
        window.khbReloadBoothStatusesFromServer = function () {
            if (window.khbBoothStatusLastCfg) {
                loadStatusSettings(window.khbBoothStatusLastCfg);
            }
        };

        loadStatusSettings(cfg);
    };
})(window, jQuery);
