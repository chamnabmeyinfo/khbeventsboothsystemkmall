/**
 * Booking status labels & colors (bookings module). Global Settings → Color tab.
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

    function renderRow(status, index, idPrefix) {
        var rowId = 'bk-status-row-' + (status.id || 'new-' + index);
        var html = '<tr id="' + rowId + '" data-status-id="' + (status.id || '') + '" data-status-code="' + status.status_code + '">';
        html += '<td><input type="number" class="form-control form-control-sm bk-sort-order" value="' + (status.sort_order || 0) + '" min="0" style="width: 64px;"></td>';
        html += '<td><input type="number" class="form-control form-control-sm bk-status-code" value="' + status.status_code + '" min="1" required style="width: 72px;"></td>';
        html += '<td><input type="text" class="form-control form-control-sm bk-name" value="' + escapeHtml(status.status_name || '') + '" required maxlength="100"></td>';
        html += '<td><div class="input-group input-group-sm"><input type="color" class="form-control form-control-color bk-bg" value="' + (status.status_color || '#6c757d') + '"><input type="text" class="form-control bk-bg-text" value="' + (status.status_color || '#6c757d') + '" maxlength="7" style="max-width: 88px;"></div></td>';
        html += '<td><div class="input-group input-group-sm"><input type="color" class="form-control form-control-color bk-border" value="' + (status.border_color || status.status_color || '#6c757d') + '"><input type="text" class="form-control bk-border-text" value="' + (status.border_color || status.status_color || '#6c757d') + '" maxlength="7" style="max-width: 88px;"></div></td>';
        html += '<td><div class="input-group input-group-sm"><input type="color" class="form-control form-control-color bk-text" value="' + (status.text_color || '#ffffff') + '"><input type="text" class="form-control bk-text-hex" value="' + (status.text_color || '#ffffff') + '" maxlength="7" style="max-width: 88px;"></div></td>';
        html += '<td><select class="form-select form-select-sm bk-badge">';
        ['secondary', 'success', 'info', 'warning', 'danger', 'primary', 'dark', 'light'].forEach(function (c) {
            html += '<option value="' + c + '"' + (status.badge_color === c ? ' selected' : '') + '>' + c + '</option>';
        });
        html += '</select></td>';
        html += '<td><input type="text" class="form-control form-control-sm bk-desc" value="' + escapeHtml(status.description || '') + '" placeholder="Description"></td>';
        html += '<td class="text-center"><input type="checkbox" class="form-check-input bk-default" ' + (status.is_default ? 'checked' : '') + '></td>';
        html += '<td class="text-center"><input type="checkbox" class="form-check-input bk-active" ' + (status.is_active !== false ? 'checked' : '') + '></td>';
        html += '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger bk-delete"><i class="fas fa-trash"></i></button></td>';
        html += '</tr>';
        return html;
    }

    function bindRowHandlers($wrap, cfg) {
        var p = cfg.idPrefix || '';
        var deleteUrl = cfg.deleteUrlTemplate;

        $wrap.find('.bk-bg, .bk-border, .bk-text').off('change').on('change', function () {
            $(this).siblings('input[type="text"]').val($(this).val());
        });
        $wrap.find('.bk-bg-text, .bk-border-text, .bk-text-hex').off('input').on('input', function () {
            var v = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/.test(v)) {
                $(this).siblings('input[type="color"]').val(v);
            }
        });
        $wrap.find('.bk-default').off('change').on('change', function () {
            if ($(this).is(':checked')) {
                $wrap.find('.bk-default').not(this).prop('checked', false);
            }
        });
        $wrap.find('.bk-delete').off('click').on('click', function () {
            var row = $(this).closest('tr');
            var statusId = row.data('status-id');
            var name = row.find('.bk-name').val();
            if (!window.confirm('Delete booking status "' + name + '"?')) {
                return;
            }
            if (statusId) {
                $.ajax({
                    url: deleteUrl.replace(':id', statusId),
                    method: 'DELETE',
                    data: { _token: cfg.csrfToken },
                    success: function (res) {
                        if (res.status === 200) {
                            if (typeof toastr !== 'undefined') {
                                toastr.success(res.message);
                            }
                            row.remove();
                        }
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Delete failed';
                        if (typeof toastr !== 'undefined') {
                            toastr.error(msg);
                        }
                    },
                });
            } else {
                row.remove();
            }
        });
    }

    function renderTable(statuses, cfg) {
        var p = cfg.idPrefix || '';
        var $c = $('#' + p + 'bookingStatusSettingsContainer');
        if (!statuses || statuses.length === 0) {
            $c.html('<div class="alert alert-info mb-0">No booking statuses. Click Add to create one.</div>');
            return;
        }
        var html =
            '<div class="table-responsive"><table class="table table-bordered table-hover table-sm align-middle"><thead class="table-light"><tr>' +
            '<th>Order</th><th>Code</th><th>Name</th><th>Background</th><th>Border</th><th>Text</th><th>Badge</th><th>Description</th><th>Default</th><th>Active</th><th></th>' +
            '</tr></thead><tbody id="' +
            p +
            'bookingStatusBody">';
        statuses.forEach(function (st, i) {
            html += renderRow(st, i, p);
        });
        html += '</tbody></table></div>';
        $c.html(html);
        $('#' + p + 'bookingStatusBody').sortable({
            handle: '.bk-sort-order',
            axis: 'y',
            update: function () {
                $('#' + p + 'bookingStatusBody tr').each(function (idx) {
                    $(this).find('.bk-sort-order').val(idx + 1);
                });
            },
        });
        bindRowHandlers($c, cfg);
    }

    function load(cfg) {
        var p = cfg.idPrefix || '';
        $.get(cfg.listUrl)
            .done(function (res) {
                if (res.status === 200) {
                    renderTable(res.data, cfg);
                }
            })
            .fail(function () {
                $('#' + p + 'bookingStatusSettingsContainer').html('<div class="alert alert-danger">Failed to load booking statuses.</div>');
            });
    }

    window.khbInitBookingStatusSettings = function (cfg) {
        cfg.idPrefix = cfg.idPrefix || 'bk_';
        $('#' + cfg.idPrefix + 'btnAddBookingStatus').on('click', function () {
            var maxCode = Math.max.apply(
                null,
                $('#' + cfg.idPrefix + 'bookingStatusBody .bk-status-code')
                    .map(function () {
                        return parseInt($(this).val(), 10) || 0;
                    })
                    .get()
                    .concat([0])
            );
            var row = {
                id: null,
                status_code: maxCode + 1,
                status_name: 'New status',
                status_color: '#6c757d',
                border_color: '#6c757d',
                text_color: '#ffffff',
                badge_color: 'secondary',
                description: '',
                is_active: true,
                sort_order: ($('#' + cfg.idPrefix + 'bookingStatusBody tr').length || 0) + 1,
                is_default: false,
            };
            if ($('#' + cfg.idPrefix + 'bookingStatusBody').length === 0) {
                renderTable([row], cfg);
            } else {
                $('#' + cfg.idPrefix + 'bookingStatusBody').append(renderRow(row, $('#' + cfg.idPrefix + 'bookingStatusBody tr').length, cfg.idPrefix));
                bindRowHandlers($('#' + cfg.idPrefix + 'bookingStatusSettingsContainer'), cfg);
            }
        });

        $('#' + cfg.idPrefix + 'btnSaveBookingStatuses').on('click', function () {
            var btn = $(this);
            var orig = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving…');
            var statuses = [];
            $('#' + cfg.idPrefix + 'bookingStatusBody tr').each(function () {
                var row = $(this);
                statuses.push({
                    id: row.data('status-id') || null,
                    status_code: parseInt(row.find('.bk-status-code').val(), 10) || 1,
                    status_name: row.find('.bk-name').val() || '',
                    status_color: row.find('.bk-bg-text').val() || '#6c757d',
                    border_color: row.find('.bk-border-text').val() || null,
                    text_color: row.find('.bk-text-hex').val() || '#ffffff',
                    badge_color: row.find('.bk-badge').val() || 'secondary',
                    description: row.find('.bk-desc').val() || '',
                    is_active: row.find('.bk-active').is(':checked'),
                    sort_order: parseInt(row.find('.bk-sort-order').val(), 10) || 0,
                    is_default: row.find('.bk-default').is(':checked'),
                });
            });
            $.ajax({
                url: cfg.saveUrl,
                method: 'POST',
                data: { statuses: statuses, _token: cfg.csrfToken },
                success: function (res) {
                    if (res.status === 200) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(res.message || 'Saved.');
                        }
                        setTimeout(function () {
                            load(cfg);
                        }, 300);
                    }
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Save failed';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html(orig);
                },
            });
        });

        $('#' + cfg.idPrefix + 'btnReloadBookingStatuses').on('click', function () {
            if (window.confirm('Reload from server? Unsaved changes will be lost.')) {
                load(cfg);
            }
        });

        window.khbBookingStatusLastCfg = cfg;
        window.khbReloadBookingStatusesFromServer = function () {
            if (window.khbBookingStatusLastCfg) {
                load(window.khbBookingStatusLastCfg);
            }
        };

        load(cfg);
    };
})(window, jQuery);
