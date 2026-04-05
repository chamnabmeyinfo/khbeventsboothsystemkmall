{{-- Agenda-by-day editor, horizontal tab wheel passthrough, translate slot counts. Requires $adminLocales. --}}
@once
@push('scripts')
<script>
(function () {
    var parseAgendaDaysUrl = @json(route('landing-pages.parse-agenda-days'));
    var adminLocales = @json($adminLocales);
    var tokenEl = document.querySelector('meta[name="csrf-token"]');
    var csrf = tokenEl ? tokenEl.getAttribute('content') : '';

    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function lpAgendaTabLabel(locale, index0) {
        var n = index0 + 1;
        return locale === 'zh' ? ('第' + n + '天') : ('Day ' + n);
    }

    function lpAgendaSlotPrefix(locale, index0) {
        var n = index0 + 1;
        if (locale === 'zh') {
            return '第' + n + '天 · ';
        }
        return 'Day ' + n + ' · ';
    }

    function lpAgendaSlotHasDayPrefix(s) {
        s = (s || '').trim();
        if (/^day\s*\d+/i.test(s)) {
            return true;
        }
        if (/^第\d+天/.test(s)) {
            return true;
        }
        if (/^第[一二三四五六七八九十]+天/.test(s)) {
            return true;
        }
        return false;
    }

    function lpAgendaSerializeLocale(locale) {
        var root = document.getElementById('lp-agenda-days-editor-' + locale);
        var hidden = document.getElementById('lp-agenda-items-text-' + locale);
        if (!root || !hidden) {
            return;
        }
        var panes = root.querySelectorAll('.lp-agenda-day-pane');
        var lines = [];
        panes.forEach(function (pane, idx) {
            var prefix = lpAgendaSlotPrefix(locale, idx);
            var ta = pane.querySelector('.lp-agenda-day-rows');
            if (!ta) {
                return;
            }
            (ta.value || '').split(/\r?\n/).forEach(function (line) {
                line = line.trim();
                if (!line) {
                    return;
                }
                var parts = line.split('|');
                var slot = (parts[0] || '').trim();
                var activity = (parts[1] || '').trim();
                var detail = (parts[2] || '').trim();
                if (!slot && !activity && !detail) {
                    return;
                }
                if (!lpAgendaSlotHasDayPrefix(slot)) {
                    slot = slot ? (prefix + slot) : prefix.replace(/\s+$/,'');
                }
                lines.push(slot + '|' + activity + '|' + detail);
            });
        });
        hidden.value = lines.join('\n');
    }

    function lpAgendaEnsureRemoveButtons(locale) {
        var root = document.getElementById('lp-agenda-days-editor-' + locale);
        if (!root) {
            return;
        }
        var panes = root.querySelectorAll('.lp-agenda-day-pane');
        var show = panes.length > 1;
        panes.forEach(function (pane) {
            var flex = pane.querySelector('.d-flex.justify-content-between');
            var btn = pane.querySelector('.lp-agenda-remove-day');
            if (show && !btn && flex) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'btn btn-sm btn-outline-danger lp-agenda-remove-day';
                b.setAttribute('data-locale', locale);
                b.textContent = 'Remove this day';
                flex.appendChild(b);
            }
            if (!show && btn) {
                btn.remove();
            }
        });
    }

    function lpAgendaRebuildFromGroups(locale, groups) {
        var root = document.getElementById('lp-agenda-days-editor-' + locale);
        if (!root || !groups || !groups.length) {
            return;
        }
        var nav = root.querySelector('.lp-agenda-admin-nav');
        var content = root.querySelector('.lp-agenda-admin-content');
        var addLi = nav.querySelector('.lp-agenda-add-day-li');
        nav.querySelectorAll('.lp-agenda-day-li').forEach(function (li) { li.remove(); });
        content.innerHTML = '';
        groups.forEach(function (g, di) {
            var label = g.label || lpAgendaTabLabel(locale, di);
            var li = document.createElement('li');
            li.className = 'nav-item lp-agenda-day-li';
            li.setAttribute('role', 'presentation');
            var a = document.createElement('a');
            a.className = 'nav-link py-2 px-3' + (di === 0 ? ' active' : '');
            a.id = 'lp-agenda-' + locale + '-nav-' + di;
            a.setAttribute('data-toggle', 'tab');
            a.href = '#lp-agenda-' + locale + '-pane-' + di;
            a.setAttribute('role', 'tab');
            a.setAttribute('aria-controls', 'lp-agenda-' + locale + '-pane-' + di);
            a.setAttribute('aria-selected', di === 0 ? 'true' : 'false');
            a.textContent = label;
            li.appendChild(a);
            nav.insertBefore(li, addLi);

            var rowLines = (g.rows || []).map(function (r) {
                return [r.slot || '', r.activity || '', r.detail || ''].join('|');
            }).join('\n');

            var pane = document.createElement('div');
            pane.className = 'tab-pane fade lp-agenda-day-pane' + (di === 0 ? ' show active' : '');
            pane.id = 'lp-agenda-' + locale + '-pane-' + di;
            pane.setAttribute('role', 'tabpanel');
            pane.setAttribute('aria-labelledby', 'lp-agenda-' + locale + '-nav-' + di);

            var flex = document.createElement('div');
            flex.className = 'd-flex justify-content-between align-items-center flex-wrap gap-2 mb-2';
            var hint = document.createElement('span');
            hint.className = 'small text-muted mb-0';
            hint.innerHTML = 'One row per line: <code>time or slot|activity|details</code>';
            flex.appendChild(hint);
            if (groups.length > 1) {
                var rb = document.createElement('button');
                rb.type = 'button';
                rb.className = 'btn btn-sm btn-outline-danger lp-agenda-remove-day';
                rb.setAttribute('data-locale', locale);
                rb.textContent = 'Remove this day';
                flex.appendChild(rb);
            }
            var ta = document.createElement('textarea');
            ta.className = 'form-control font-monospace lp-agenda-day-rows';
            ta.rows = 8;
            ta.placeholder = '06:00|Airport meet|\n12:00|Canton Fair|Halls 1–3';
            ta.value = rowLines;
            pane.appendChild(flex);
            pane.appendChild(ta);
            content.appendChild(pane);
        });
        if (window.jQuery) {
            jQuery(nav.querySelector('.lp-agenda-day-li a')).tab('show');
        }
        lpAgendaSerializeLocale(locale);
    }

    function lpAgendaFetchAndRebuild(locale) {
        var hidden = document.getElementById('lp-agenda-items-text-' + locale);
        if (!hidden) {
            return;
        }
        fetch(parseAgendaDaysUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ text: hidden.value || '', locale: locale })
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && data.groups && data.groups.length) {
                    lpAgendaRebuildFromGroups(locale, data.groups);
                }
            })
            .catch(function () {});
    }

    function initAgendaByDay() {
        adminLocales.forEach(function (loc) {
            lpAgendaSerializeLocale(loc);
        });
        document.querySelectorAll('form[data-lp-i18n-root]').forEach(function (form) {
            form.addEventListener('submit', function () {
                adminLocales.forEach(function (loc) {
                    lpAgendaSerializeLocale(loc);
                });
            });
        });
        document.addEventListener('input', function (e) {
            if (e.target.classList && e.target.classList.contains('lp-agenda-day-rows')) {
                var root = e.target.closest('.lp-agenda-by-day-editor');
                if (root) {
                    lpAgendaSerializeLocale(root.getAttribute('data-locale'));
                }
            }
            if (e.target.classList && e.target.classList.contains('lp-agenda-items-hidden')) {
                var loc = e.target.id.replace('lp-agenda-items-text-', '');
                lpAgendaFetchAndRebuild(loc);
            }
        });

        document.addEventListener('click', function (e) {
            var add = e.target.closest('.lp-agenda-add-day');
            if (add) {
                e.preventDefault();
                var root = add.closest('.lp-agenda-by-day-editor');
                var locale = root.getAttribute('data-locale');
                var nav = root.querySelector('.lp-agenda-admin-nav');
                var content = root.querySelector('.lp-agenda-admin-content');
                var addLi = nav.querySelector('.lp-agenda-add-day-li');
                var n = root.querySelectorAll('.lp-agenda-day-pane').length;
                var li = document.createElement('li');
                li.className = 'nav-item lp-agenda-day-li';
                li.setAttribute('role', 'presentation');
                var a = document.createElement('a');
                a.className = 'nav-link py-2 px-3';
                a.id = 'lp-agenda-' + locale + '-nav-' + n;
                a.setAttribute('data-toggle', 'tab');
                a.href = '#lp-agenda-' + locale + '-pane-' + n;
                a.setAttribute('role', 'tab');
                a.textContent = lpAgendaTabLabel(locale, n);
                li.appendChild(a);
                nav.insertBefore(li, addLi);
                var pane = document.createElement('div');
                pane.className = 'tab-pane fade lp-agenda-day-pane';
                pane.id = 'lp-agenda-' + locale + '-pane-' + n;
                pane.setAttribute('role', 'tabpanel');
                var flex = document.createElement('div');
                flex.className = 'd-flex justify-content-between align-items-center flex-wrap gap-2 mb-2';
                var hintAdd = document.createElement('span');
                hintAdd.className = 'small text-muted mb-0';
                hintAdd.innerHTML = 'One row per line: <code>time or slot|activity|details</code>';
                var rb = document.createElement('button');
                rb.type = 'button';
                rb.className = 'btn btn-sm btn-outline-danger lp-agenda-remove-day';
                rb.setAttribute('data-locale', locale);
                rb.textContent = 'Remove this day';
                flex.appendChild(hintAdd);
                flex.appendChild(rb);
                var ta = document.createElement('textarea');
                ta.className = 'form-control font-monospace lp-agenda-day-rows';
                ta.rows = 8;
                ta.placeholder = '06:00|Airport meet|\n12:00|Canton Fair|Halls 1–3';
                pane.appendChild(flex);
                pane.appendChild(ta);
                content.appendChild(pane);
                lpAgendaEnsureRemoveButtons(locale);
                if (window.jQuery) {
                    jQuery(a).tab('show');
                }
                lpAgendaSerializeLocale(locale);
                return;
            }
            var rem = e.target.closest('.lp-agenda-remove-day');
            if (rem) {
                e.preventDefault();
                var pane = rem.closest('.lp-agenda-day-pane');
                var root = rem.closest('.lp-agenda-by-day-editor');
                var locale = root.getAttribute('data-locale');
                var panes = root.querySelectorAll('.lp-agenda-day-pane');
                if (panes.length <= 1) {
                    return;
                }
                var href = '#' + pane.id;
                var link = root.querySelector('a[href="' + href + '"]');
                if (link && link.closest('li')) {
                    link.closest('li').remove();
                }
                pane.remove();
                lpAgendaEnsureRemoveButtons(locale);
                var first = root.querySelector('.lp-agenda-day-li a');
                if (first && window.jQuery) {
                    jQuery(first).tab('show');
                }
                lpAgendaSerializeLocale(locale);
            }
        });
    }

    function lpBindHorizontalStripWheelScrollThrough() {
        document.querySelectorAll('.lp-landing-section-tabs-wrap').forEach(function (el) {
            el.addEventListener('wheel', function (e) {
                if (e.ctrlKey) {
                    return;
                }
                if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) {
                    return;
                }
                e.preventDefault();
                window.scrollBy(0, e.deltaY);
            }, { passive: false });
        });
    }

    function lpInitLandingVisualFormScripts() {
        initAgendaByDay();
        lpBindHorizontalStripWheelScrollThrough();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', lpInitLandingVisualFormScripts);
    } else {
        lpInitLandingVisualFormScripts();
    }
})();
</script>
@endpush
@endonce

@once
@push('scripts')
<script>
window.lpLandingTranslateSlots = { tripPhaseSlots: 8, tripSubSlots: 4, promotionTierSlots: 8 };
</script>
@endpush
@endonce
