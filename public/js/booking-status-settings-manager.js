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
        var html = '<tr id="' + rowId + '" class="table-row-no-press khb-booking-status-row" data-status-id="' + (status.id || '') + '" data-status-code="' + status.status_code + '">';
        html += '<td class="khb-bk-order"><input type="number" class="form-control form-control-sm bk-sort-order" value="' + (status.sort_order || 0) + '" min="0" aria-label="Sort order"></td>';
        html += '<td class="khb-bk-code"><input type="number" class="form-control form-control-sm bk-status-code" value="' + status.status_code + '" min="1" required aria-label="Status code"></td>';
        html += '<td class="khb-bk-name"><input type="text" class="form-control form-control-sm bk-name" value="' + escapeHtml(status.status_name || '') + '" required maxlength="100" autocomplete="off"></td>';
        html += '<td class="khb-bk-color"><div class="khb-bk-color-pair"><input type="color" class="form-control form-control-color khb-bk-color-swatch bk-bg" value="' + (status.status_color || '#6c757d') + '" title="Background" aria-label="Background color"><input type="text" class="form-control form-control-sm bk-bg-text" value="' + (status.status_color || '#6c757d') + '" maxlength="7" inputmode="text" aria-label="Background hex"></div></td>';
        html += '<td class="khb-bk-color"><div class="khb-bk-color-pair"><input type="color" class="form-control form-control-color khb-bk-color-swatch bk-border" value="' + (status.border_color || status.status_color || '#6c757d') + '" title="Border" aria-label="Border color"><input type="text" class="form-control form-control-sm bk-border-text" value="' + (status.border_color || status.status_color || '#6c757d') + '" maxlength="7" inputmode="text" aria-label="Border hex"></div></td>';
        html += '<td class="khb-bk-color"><div class="khb-bk-color-pair"><input type="color" class="form-control form-control-color khb-bk-color-swatch bk-text" value="' + (status.text_color || '#ffffff') + '" title="Text" aria-label="Text color"><input type="text" class="form-control form-control-sm bk-text-hex" value="' + (status.text_color || '#ffffff') + '" maxlength="7" inputmode="text" aria-label="Text hex"></div></td>';
        html += '<td class="khb-bk-badge"><select class="form-select form-select-sm bk-badge" aria-label="Badge style">';
        ['secondary', 'success', 'info', 'warning', 'danger', 'primary', 'dark', 'light'].forEach(function (c) {
            html += '<option value="' + c + '"' + (status.badge_color === c ? ' selected' : '') + '>' + c + '</option>';
        });
        html += '</select></td>';
        html += '<td class="khb-bk-desc"><input type="text" class="form-control form-control-sm bk-desc" value="' + escapeHtml(status.description || '') + '" placeholder="Description" autocomplete="off"></td>';
        html += '<td class="text-center khb-bk-check"><input type="checkbox" class="form-check-input bk-default" ' + (status.is_default ? 'checked' : '') + ' aria-label="Default status"></td>';
        html += '<td class="text-center khb-bk-check"><input type="checkbox" class="form-check-input bk-active" ' + (status.is_active !== false ? 'checked' : '') + ' aria-label="Active"></td>';
        html += '<td class="text-center khb-bk-actions"><button type="button" class="action-btn action-btn-destructive action-btn-compact bk-delete" title="Remove this status"><i class="fas fa-trash-alt" aria-hidden="true"></i><span class="visually-hidden">Delete</span></button></td>';
        html += '</tr>';
        return html;
    }

    function bindRowHandlers($wrap, cfg) {
        var p = cfg.idPrefix || '';
        var deleteUrl = cfg.deleteUrlTemplate;

        $wrap.find('.bk-bg, .bk-border, .bk-text').off('change').on('change', function () {
            $(this).closest('.khb-bk-color-pair').find('input[type="text"]').val($(this).val());
        });
        $wrap.find('.bk-bg-text, .bk-border-text, .bk-text-hex').off('input').on('input', function () {
            var v = $(this).val();
            if (/^#[0-9A-Fa-f]{6}$/.test(v)) {
                $(this).closest('.khb-bk-color-pair').find('input[type="color"]').val(v);
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
            $c.html(
                '<div class="settings-callout settings-callout--info mb-0" role="status">' +
                    '<p class="settings-callout__title mb-1">No booking statuses yet</p>' +
                    '<p class="small text-muted mb-0">Use <strong>Add status</strong>, edit the row, then <strong>Save booking statuses</strong>.</p>' +
                '</div>'
            );
            return;
        }
        var html =
            '<div class="khb-booking-status-table-shell">' +
            '<div class="looker-table-wrapper khb-booking-status-scroll" role="region" aria-label="Booking status settings">' +
            '<table class="looker-table mb-0 khb-booking-status-table" id="' +
            p +
            'bookingStatusTable">' +
            '<thead><tr>' +
            '<th scope="col">Order</th><th scope="col">Code</th><th scope="col">Name</th>' +
            '<th scope="col">Background</th><th scope="col">Border</th><th scope="col">Text</th><th scope="col">Badge</th><th scope="col">Description</th>' +
            '<th scope="col" class="text-center">Default</th><th scope="col" class="text-center">Active</th><th scope="col" class="text-center"><span class="visually-hidden">Remove</span></th>' +
            '</tr></thead><tbody id="' +
            p +
            'bookingStatusBody">';
        statuses.forEach(function (st, i) {
            html += renderRow(st, i, p);
        });
        html += '</tbody></table></div></div>';
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
                $('#' + p + 'bookingStatusSettingsContainer').html(
                    '<div class="settings-callout settings-callout--danger" role="alert">Failed to load booking statuses. Please refresh the page.</div>'
                );
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
