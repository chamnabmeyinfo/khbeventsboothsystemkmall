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
        var html = '<tr id="' + rowId + '" class="table-row-no-press khb-booth-status-row" data-status-id="' + (status.id || '') + '" data-status-code="' + status.status_code + '">';

        html += '<td class="khb-bs-order-cell">';
        html += '<div class="d-flex align-items-center gap-2 flex-nowrap">';
        html += '<span class="khb-status-drag-handle" role="button" tabindex="0" aria-label="Drag to reorder row"><i class="fas fa-grip-vertical" aria-hidden="true"></i></span>';
        html += '<input type="number" class="glass-input glass-input-sm status-sort-order" value="' + (status.sort_order || 0) + '" min="0" inputmode="numeric" aria-label="Sort order">';
        html += '</div></td>';
        html += '<td><input type="number" class="glass-input glass-input-sm status-code" value="' + status.status_code + '" min="1" required inputmode="numeric" aria-label="Status code"></td>';
        html += '<td class="khb-bs-name-cell"><input type="text" class="glass-input glass-input-sm status-name" value="' + escapeHtml(status.status_name || '') + '" required maxlength="100" autocomplete="off"></td>';

        html += '<td><div class="khb-color-pair"><input type="color" class="glass-input glass-input-color status-bg-color" value="' + (status.status_color || '#28a745') + '" title="Background color" aria-label="Background color">';
        html += '<input type="text" class="glass-input glass-input-sm status-bg-color-text" value="' + (status.status_color || '#28a745') + '" maxlength="7" inputmode="text" aria-label="Background hex"></div></td>';
        html += '<td><div class="khb-color-pair"><input type="color" class="glass-input glass-input-color status-border-color" value="' + (status.border_color || status.status_color || '#28a745') + '" title="Border color" aria-label="Border color">';
        html += '<input type="text" class="glass-input glass-input-sm status-border-color-text" value="' + (status.border_color || status.status_color || '#28a745') + '" maxlength="7" aria-label="Border hex"></div></td>';
        html += '<td><input type="number" class="glass-input glass-input-sm status-border-width" value="' + (status.border_width || 2) + '" min="0" max="10" inputmode="numeric" title="Border width (0-10px)" aria-label="Border width"></td>';

        html += '<td class="khb-bs-select-cell"><select class="glass-input glass-input-sm status-border-style" aria-label="Border style">';
        var borderStyles = ['solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset', 'none'];
        borderStyles.forEach(function (style) {
            html += '<option value="' + style + '"' + ((status.border_style || 'solid') === style ? ' selected' : '') + '>' + style.charAt(0).toUpperCase() + style.slice(1) + '</option>';
        });
        html += '</select></td>';

        html += '<td><input type="number" class="glass-input glass-input-sm status-border-radius" value="' + (status.border_radius || 4) + '" min="0" max="50" inputmode="numeric" title="Border radius (0-50px)" aria-label="Border radius"></td>';

        html += '<td><div class="khb-color-pair"><input type="color" class="glass-input glass-input-color status-text-color" value="' + (status.text_color || '#ffffff') + '" title="Text color" aria-label="Text color">';
        html += '<input type="text" class="glass-input glass-input-sm status-text-color-text" value="' + (status.text_color || '#ffffff') + '" maxlength="7" aria-label="Text hex"></div></td>';

        html += '<td class="khb-bs-select-cell"><select class="glass-input glass-input-sm status-badge-color" aria-label="Badge color">';
        var badgeColors = ['success', 'info', 'warning', 'danger', 'primary', 'secondary', 'dark', 'light'];
        badgeColors.forEach(function (color) {
            html += '<option value="' + color + '"' + (status.badge_color === color ? ' selected' : '') + '>' + color.charAt(0).toUpperCase() + color.slice(1) + '</option>';
        });
        html += '</select></td>';

        html += '<td class="khb-bs-desc-cell"><input type="text" class="glass-input glass-input-sm status-description" value="' + escapeHtml(status.description || '') + '" placeholder="Optional description" autocomplete="off"></td>';

        html += '<td class="khb-bs-floor-cell"><select class="glass-input glass-input-sm status-floor-plan" aria-label="Floor plan scope">';
        html += '<option value="">Global (all floor plans)</option>';
        (floorPlans || []).forEach(function (fp) {
            var sel = parseInt(status.floor_plan_id, 10) === parseInt(fp.id, 10) ? ' selected' : '';
            html += '<option value="' + fp.id + '"' + sel + '>' + escapeHtml(fp.name) + '</option>';
        });
        html += '</select></td>';

        html += '<td class="text-center khb-bs-check-cell"><div class="form-check d-flex justify-content-center mb-0 mx-auto khb-bs-check-wrap"><input type="checkbox" class="form-check-input status-is-default" ' + (status.is_default ? 'checked' : '') + ' aria-label="Default status"></div></td>';
        html += '<td class="text-center khb-bs-check-cell"><div class="form-check d-flex justify-content-center mb-0 mx-auto khb-bs-check-wrap"><input type="checkbox" class="form-check-input status-is-active" ' + (status.is_active !== false ? 'checked' : '') + ' aria-label="Active"></div></td>';
        html += '<td class="text-center khb-bs-actions-cell"><button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-delete-status px-3" title="Remove this status"><i class="fas fa-trash-alt" aria-hidden="true"></i><span class="visually-hidden">Delete</span></button></td>';
        html += '</tr>';
        return html;
    }

    var BOOTH_COL_MIN_PX = 36;
    var BOOTH_STATUS_COL_WIDTHS_STORAGE_KEY = 'khb_booth_status_col_widths_v1';

    function disconnectBoothStatusResizeObserver(table) {
        if (table && table._khbResizeObserver) {
            table._khbResizeObserver.disconnect();
            table._khbResizeObserver = null;
        }
    }

    function getColWidthPercent(col) {
        var w = col && col.style ? col.style.width : '';
        if (!w) {
            var attr = col.getAttribute('style') || '';
            var am = attr.match(/width:\s*([\d.]+)\s*%/i);
            return am ? parseFloat(am[1]) : null;
        }
        var m = String(w).trim().match(/^([\d.]+)\s*%$/);
        return m ? parseFloat(m[1]) : null;
    }

    function syncBoothStatusColWidthsToPixels(table) {
        var scroll = table.closest('.khb-booth-status-scroll');
        if (!scroll) {
            return;
        }
        var totalW = scroll.clientWidth;
        if (totalW < 60) {
            return;
        }
        var cols = table.querySelectorAll('colgroup col');
        if (!cols.length) {
            return;
        }
        var pcts = [];
        var sumPct = 0;
        var i;
        for (i = 0; i < cols.length; i++) {
            var pctVal = getColWidthPercent(cols[i]);
            if (pctVal == null || isNaN(pctVal)) {
                return;
            }
            pcts.push(pctVal);
            sumPct += pctVal;
        }
        if (sumPct <= 0) {
            return;
        }
        var allocated = 0;
        for (i = 0; i < cols.length - 1; i++) {
            var w = Math.round((totalW * pcts[i]) / sumPct);
            if (w < BOOTH_COL_MIN_PX) {
                w = BOOTH_COL_MIN_PX;
            }
            cols[i].style.width = w + 'px';
            allocated += w;
        }
        cols[cols.length - 1].style.width = Math.max(BOOTH_COL_MIN_PX, totalW - allocated) + 'px';
    }

    function redistributeBoothStatusColsToContainer(table) {
        var scroll = table.closest('.khb-booth-status-scroll');
        if (!scroll || scroll.clientWidth < 60) {
            return;
        }
        var target = scroll.clientWidth;
        var cols = table.querySelectorAll('colgroup col');
        var sum = 0;
        var i;
        var widths = [];
        for (i = 0; i < cols.length; i++) {
            var v = parseFloat(cols[i].style.width);
            if (isNaN(v)) {
                return;
            }
            widths.push(v);
            sum += v;
        }
        if (sum <= 0 || Math.abs(sum - target) < 2) {
            return;
        }
        var scale = target / sum;
        var newSum = 0;
        for (i = 0; i < cols.length - 1; i++) {
            var nw = Math.max(BOOTH_COL_MIN_PX, Math.round(widths[i] * scale));
            cols[i].style.width = nw + 'px';
            newSum += nw;
        }
        cols[cols.length - 1].style.width = Math.max(BOOTH_COL_MIN_PX, target - newSum) + 'px';
    }

    function loadBoothStatusColumnWidths(expectedCount) {
        if (typeof localStorage === 'undefined' || !expectedCount) {
            return null;
        }
        try {
            var raw = localStorage.getItem(BOOTH_STATUS_COL_WIDTHS_STORAGE_KEY);
            if (!raw) {
                return null;
            }
            var data = JSON.parse(raw);
            var widths = data && data.widths;
            if (!Array.isArray(widths) || widths.length !== expectedCount) {
                return null;
            }
            var i;
            for (i = 0; i < widths.length; i++) {
                var n = Number(widths[i]);
                if (!isFinite(n) || n < BOOTH_COL_MIN_PX) {
                    return null;
                }
            }
            return widths;
        } catch (e) {
            return null;
        }
    }

    function applySavedBoothStatusColumnWidths(table, savedWidths) {
        var scroll = table.closest('.khb-booth-status-scroll');
        if (!scroll) {
            return false;
        }
        var target = scroll.clientWidth;
        if (target < 60) {
            return false;
        }
        var cols = table.querySelectorAll('colgroup col');
        if (!cols.length || cols.length !== savedWidths.length) {
            return false;
        }
        var sum = 0;
        var i;
        for (i = 0; i < savedWidths.length; i++) {
            sum += Number(savedWidths[i]);
        }
        if (sum <= 0) {
            return false;
        }
        var scale = target / sum;
        var allocated = 0;
        for (i = 0; i < cols.length - 1; i++) {
            var w = Math.max(BOOTH_COL_MIN_PX, Math.round(Number(savedWidths[i]) * scale));
            cols[i].style.width = w + 'px';
            allocated += w;
        }
        cols[cols.length - 1].style.width = Math.max(BOOTH_COL_MIN_PX, target - allocated) + 'px';
        return true;
    }

    function saveBoothStatusColumnWidths(table) {
        if (typeof localStorage === 'undefined' || !table) {
            return;
        }
        var cols = table.querySelectorAll('colgroup col');
        if (!cols.length) {
            return;
        }
        var widths = [];
        var i;
        for (i = 0; i < cols.length; i++) {
            var v = parseFloat(cols[i].style.width);
            if (isNaN(v)) {
                return;
            }
            widths.push(Math.round(v));
        }
        try {
            localStorage.setItem(
                BOOTH_STATUS_COL_WIDTHS_STORAGE_KEY,
                JSON.stringify({ v: 1, widths: widths })
            );
        } catch (e) {
            /* quota or private mode */
        }
    }

    function schedulePersistBoothStatusColumnWidths(table) {
        if (!table) {
            return;
        }
        if (table._khbColPersistTimer) {
            clearTimeout(table._khbColPersistTimer);
        }
        table._khbColPersistTimer = setTimeout(function () {
            table._khbColPersistTimer = null;
            saveBoothStatusColumnWidths(table);
        }, 280);
    }

    function attachBoothStatusColumnResize(table) {
        var cols = table.querySelectorAll('colgroup col');
        var ths = table.querySelectorAll('thead th');
        if (!cols.length || cols.length !== ths.length) {
            return;
        }

        $(table).find('.khb-col-resize-handle').remove();

        var c;
        for (c = 0; c < ths.length - 1; c++) {
            (function (colIndex) {
                var handle = document.createElement('span');
                handle.className = 'khb-col-resize-handle';
                handle.setAttribute('data-col-index', String(colIndex));
                handle.setAttribute('role', 'separator');
                handle.setAttribute('aria-orientation', 'vertical');
                handle.setAttribute('aria-label', 'Resize column');
                handle.tabIndex = 0;

                function startDrag(clientX) {
                    var colList = table.querySelectorAll('colgroup col');
                    var w0 = parseFloat(colList[colIndex].style.width);
                    var w1 = parseFloat(colList[colIndex + 1].style.width);
                    if (isNaN(w0) || isNaN(w1)) {
                        return;
                    }
                    var startX = clientX;

                    function onMove(e) {
                        if (e.type === 'touchmove' && e.cancelable) {
                            e.preventDefault();
                        }
                        var cx = e.clientX != null ? e.clientX : (e.touches && e.touches[0] ? e.touches[0].clientX : 0);
                        var dx = cx - startX;
                        var new0 = w0 + dx;
                        var new1 = w1 - dx;
                        if (new0 < BOOTH_COL_MIN_PX) {
                            new1 -= BOOTH_COL_MIN_PX - new0;
                            new0 = BOOTH_COL_MIN_PX;
                        }
                        if (new1 < BOOTH_COL_MIN_PX) {
                            new0 -= BOOTH_COL_MIN_PX - new1;
                            new1 = BOOTH_COL_MIN_PX;
                        }
                        colList[colIndex].style.width = Math.round(new0) + 'px';
                        colList[colIndex + 1].style.width = Math.round(new1) + 'px';
                    }

                    function onUp() {
                        document.removeEventListener('mousemove', onMove);
                        document.removeEventListener('mouseup', onUp);
                        document.removeEventListener('touchmove', onMove);
                        document.removeEventListener('touchend', onUp);
                        document.removeEventListener('touchcancel', onUp);
                        document.body.style.cursor = '';
                        document.body.style.userSelect = '';
                        saveBoothStatusColumnWidths(table);
                    }

                    document.addEventListener('mousemove', onMove);
                    document.addEventListener('mouseup', onUp);
                    document.addEventListener('touchmove', onMove, { passive: false });
                    document.addEventListener('touchend', onUp);
                    document.addEventListener('touchcancel', onUp);
                    document.body.style.cursor = 'col-resize';
                    document.body.style.userSelect = 'none';
                }

                handle.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    startDrag(e.clientX);
                });
                handle.addEventListener('touchstart', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (e.touches && e.touches[0]) {
                        startDrag(e.touches[0].clientX);
                    }
                }, { passive: false });

                handle.addEventListener('keydown', function (e) {
                    var colList = table.querySelectorAll('colgroup col');
                    var w0 = parseFloat(colList[colIndex].style.width);
                    var w1 = parseFloat(colList[colIndex + 1].style.width);
                    if (isNaN(w0) || isNaN(w1)) {
                        return;
                    }
                    var step = e.shiftKey ? 12 : 4;
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        var n0 = Math.max(BOOTH_COL_MIN_PX, w0 - step);
                        var n1 = w1 + (w0 - n0);
                        if (n1 < BOOTH_COL_MIN_PX) {
                            n1 = BOOTH_COL_MIN_PX;
                            n0 = w0 + w1 - n1;
                        }
                        colList[colIndex].style.width = Math.round(n0) + 'px';
                        colList[colIndex + 1].style.width = Math.round(n1) + 'px';
                        schedulePersistBoothStatusColumnWidths(table);
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        var n0r = w0 + step;
                        var n1r = w1 - step;
                        if (n1r < BOOTH_COL_MIN_PX) {
                            n1r = BOOTH_COL_MIN_PX;
                            n0r = w0 + w1 - n1r;
                        }
                        colList[colIndex].style.width = Math.round(n0r) + 'px';
                        colList[colIndex + 1].style.width = Math.round(n1r) + 'px';
                        schedulePersistBoothStatusColumnWidths(table);
                    }
                });

                ths[colIndex].appendChild(handle);
            })(c);
        }
    }

    function setupBoothStatusResizeObserver(table) {
        disconnectBoothStatusResizeObserver(table);
        var scroll = table.closest('.khb-booth-status-scroll');
        if (!scroll || typeof ResizeObserver === 'undefined') {
            return;
        }
        var t = null;
        var ro = new ResizeObserver(function () {
            if (t) {
                clearTimeout(t);
            }
            t = setTimeout(function () {
                redistributeBoothStatusColsToContainer(table);
            }, 120);
        });
        ro.observe(scroll);
        table._khbResizeObserver = ro;
    }

    function initBoothStatusTableColumns(table) {
        if (!table) {
            return;
        }
        disconnectBoothStatusResizeObserver(table);
        table.removeAttribute('data-khb-col-resize-ready');

        function tryInit() {
            var scroll = table.closest('.khb-booth-status-scroll');
            if (!scroll || scroll.clientWidth < 60) {
                return false;
            }
            var cols = table.querySelectorAll('colgroup col');
            if (!cols.length) {
                return false;
            }
            var saved = loadBoothStatusColumnWidths(cols.length);
            if (saved && applySavedBoothStatusColumnWidths(table, saved)) {
                /* use remembered widths, scaled to current container */
            } else {
                var st0 = cols[0].style && cols[0].style.width ? cols[0].style.width : cols[0].getAttribute('style') || '';
                if (st0.indexOf('%') !== -1) {
                    syncBoothStatusColWidthsToPixels(table);
                } else {
                    redistributeBoothStatusColsToContainer(table);
                }
            }
            attachBoothStatusColumnResize(table);
            setupBoothStatusResizeObserver(table);
            table.setAttribute('data-khb-col-resize-ready', '1');
            return true;
        }

        var tries = 0;
        function attempt() {
            if (tryInit()) {
                return;
            }
            tries += 1;
            if (tries < 50) {
                setTimeout(attempt, 50);
            }
        }
        attempt();
    }

    function renderStatusSettings(statuses, floorPlans, idPrefix) {
        var p = idPrefix || '';
        var $container = $('#' + p + 'statusSettingsContainer');
        if (!statuses || statuses.length === 0) {
            $container.html(
                '<div class="alert alert-light border rounded-3 shadow-sm mb-0 d-flex align-items-start gap-3 khb-booth-status-empty">' +
                    '<span class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center flex-shrink-0 khb-booth-status-empty-icon" aria-hidden="true"><i class="fas fa-th-large"></i></span>' +
                    '<div><strong class="d-block mb-1">No booth statuses yet</strong><span class="text-muted small">Add a row with <strong>Add status</strong>, then save. You can drag rows by the grip to reorder.</span></div>' +
                '</div>'
            );
            return;
        }

        var html = '<p class="text-muted small mb-2 khb-booth-status-col-hint"><i class="fas fa-arrows-alt-h me-1" aria-hidden="true"></i>Drag between column headers (or use <kbd class="small">←</kbd> <kbd class="small">→</kbd> when a handle is focused) to adjust column widths. Widths are saved in this browser.</p>';
        html += '<div class="booth-status-table-shell">';
        html += '<div class="looker-table-wrapper khb-booth-status-scroll" role="region" aria-label="Booth status color settings">';
        html += '<table class="looker-table table-hover mb-0 booth-status-table" id="' + p + 'statusSettingsTable">';
        html += '<colgroup>';
        html += '<col span="1" style="width:5%"><col span="1" style="width:3.5%"><col span="1" style="width:13%">';
        html += '<col span="1" style="width:7%"><col span="1" style="width:7%"><col span="1" style="width:2.5%">';
        html += '<col span="1" style="width:5.5%"><col span="1" style="width:2.5%"><col span="1" style="width:6%">';
        html += '<col span="1" style="width:5%"><col span="1" style="width:16%"><col span="1" style="width:14%">';
        html += '<col span="1" style="width:3.5%"><col span="1" style="width:3.5%"><col span="1" style="width:6%">';
        html += '</colgroup>';
        html += '<thead><tr>';
        html += '<th scope="col" class="khb-th-order">Order</th><th scope="col" class="khb-th-code">Code</th><th scope="col" class="khb-th-name">Name</th>';
        html += '<th scope="col">Fill</th><th scope="col">Border</th><th scope="col" class="khb-th-num" title="Border width (px)">W</th>';
        html += '<th scope="col" class="khb-th-style">Style</th><th scope="col" class="khb-th-num" title="Corner radius (px)">R</th><th scope="col">Text</th>';
        html += '<th scope="col" class="khb-th-badge">Badge</th><th scope="col" class="khb-th-desc">Description</th><th scope="col" class="khb-th-floor">Floor plan</th>';
        html += '<th scope="col" class="khb-th-flag text-center">Default</th><th scope="col" class="khb-th-flag text-center">Active</th><th scope="col" class="khb-th-actions text-center">Remove</th>';
        html += '</tr></thead><tbody id="' + p + 'statusSettingsBody">';

        statuses.forEach(function (status, index) {
            html += renderStatusRow(status, index, floorPlans, idPrefix);
        });
        html += '</tbody></table></div></div>';
        var prevTable = document.getElementById(p + 'statusSettingsTable');
        if (prevTable) {
            disconnectBoothStatusResizeObserver(prevTable);
        }
        $container.html(html);

        var tableEl = document.getElementById(p + 'statusSettingsTable');
        initBoothStatusTableColumns(tableEl);

        $('#' + p + 'statusSettingsBody').sortable({
            handle: '.khb-status-drag-handle',
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

        if (!window._khbBoothStatusColTabBound) {
            window._khbBoothStatusColTabBound = true;
            $(document).on('shown.bs.tab', 'button[data-bs-target="#global-color-settings"], a[data-bs-target="#global-color-settings"]', function () {
                var t = document.getElementById('ct_statusSettingsTable');
                if (!t) {
                    return;
                }
                if (!t.getAttribute('data-khb-col-resize-ready')) {
                    initBoothStatusTableColumns(t);
                } else {
                    redistributeBoothStatusColsToContainer(t);
                }
            });
        }

        loadStatusSettings(cfg);
    };

    /** Clears saved booth status column widths for this browser (next load uses defaults). */
    window.khbClearSavedBoothStatusColumnWidths = function () {
        try {
            localStorage.removeItem(BOOTH_STATUS_COL_WIDTHS_STORAGE_KEY);
        } catch (e) {
            /* ignore */
        }
    };
})(window, jQuery);
