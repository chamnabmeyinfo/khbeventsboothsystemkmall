@php
    use App\Models\LandingPage;
    $lpSectionKeys = LandingPage::SECTION_LAYOUT_KEYS;
    $lpSectionLabels = LandingPage::sectionLayoutLabels();
    $templatesByLayout = $sectionTemplatesForApply->groupBy('layout_key');
    $templatesForJs = $templatesByLayout->map(function ($g) {
        return $g->map(function ($t) {
            return ['id' => $t->id, 'name' => $t->name];
        })->values();
    });
@endphp
<div class="card card-outline-secondary mb-0 lp-section-blueprint">
    <div class="card-header py-2">
        <strong class="d-block">Public page sections</strong>
        <small class="text-muted">Choose which blocks appear on the Canton Fair visual page and in what order. Each layout can appear once. <strong>Hero</strong> is always kept if missing. Optional blocks (e.g. promotion, trip slider) still hide themselves when empty.</small>
    </div>
    <div class="card-body">
        <input type="hidden" name="visual[section_blueprint_json]" id="lpSectionBlueprintJson" value="{{ e($sectionBlueprintJson) }}">

        <div class="lp-section-toolbar">
            <div class="form-inline flex-grow-1">
                <label class="sr-only" for="lpSectionAddLayout">Add section layout</label>
                <select class="form-control" id="lpSectionAddLayout" aria-label="Layout to add">
                    <option value="">— Add layout —</option>
                    @foreach($lpSectionKeys as $lk)
                        <option value="{{ $lk }}">{{ $lpSectionLabels[$lk] ?? $lk }}</option>
                    @endforeach
                </select>
                <button type="button" class="action-btn action-btn-primary" id="lpSectionAddBtn">Add section</button>
            </div>
            <a href="{{ route('landing-pages.section-templates.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-file-alt mr-1" aria-hidden="true"></i>Section templates
            </a>
        </div>

        <div class="mb-3">
            <div class="text-muted small font-weight-bold mb-1">Quick add — one section at a time</div>
            <p class="small text-muted mb-2 mb-md-1">Tap a layout to append it to the list (only layouts not already added). Reorder with ↑ ↓ in the table.</p>
            <div class="d-flex flex-wrap align-items-center lp-section-quick-add" id="lpSectionQuickAdd" aria-label="Quick add section layout"></div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" id="lpSectionBlueprintTable">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" class="lp-col-order">#</th>
                        <th scope="col">Layout</th>
                        <th scope="col" class="lp-col-order-actions">Order</th>
                        @if(!empty($landingPage))
                            <th scope="col">Apply template</th>
                        @endif
                        <th scope="col" class="lp-col-remove">Remove</th>
                    </tr>
                </thead>
                <tbody id="lpSectionBlueprintTbody"></tbody>
            </table>
        </div>
        <p class="text-muted small mb-0 mt-2"><strong>Section copy</strong> tabs under each language follow this order after you <strong>Update / Create</strong> the page (or reload). Use <strong>Section templates</strong> for reusable copy; <strong>Apply</strong> saves to the database immediately.</p>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    var hidden = document.getElementById('lpSectionBlueprintJson');
    var tbody = document.getElementById('lpSectionBlueprintTbody');
    var addLayout = document.getElementById('lpSectionAddLayout');
    var addBtn = document.getElementById('lpSectionAddBtn');
    var quickRoot = document.getElementById('lpSectionQuickAdd');
    if (!hidden || !tbody || !addLayout || !addBtn) return;

    var LAYOUTS = @json($lpSectionKeys);
    var LABELS = @json($lpSectionLabels);
    var TEMPLATES_BY_LAYOUT = @json($templatesForJs);
    var applyUrl = @json(!empty($landingPage) ? route('landing-pages.apply-section-template', $landingPage) : '');
    var csrf = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrf ? csrf.getAttribute('content') : '';

    function parseState() {
        try {
            var raw = hidden.value.trim();
            var a = JSON.parse(raw);
            if (Array.isArray(a) && a.length > 0) {
                return a;
            }
        } catch (e) {}
        return LAYOUTS.map(function (l) {
            return { id: uuid(), layout: l };
        });
    }

    function syncHidden(rows) {
        hidden.value = JSON.stringify(rows);
    }

    function uuid() {
        if (window.crypto && crypto.randomUUID) return crypto.randomUUID();
        return 'sec-' + Math.random().toString(36).slice(2, 12);
    }

    function render() {
        var rows = parseState();
        tbody.innerHTML = '';
        rows.forEach(function (row, idx) {
            var layout = row.layout || '';
            var tr = document.createElement('tr');
            var tdN = document.createElement('td');
            tdN.textContent = String(idx + 1);
            var tdL = document.createElement('td');
            tdL.textContent = LABELS[layout] || layout;
            var tdO = document.createElement('td');
            var up = document.createElement('button');
            up.type = 'button';
            up.className = 'btn btn-sm btn-outline-secondary lp-section-order-btn mr-1';
            up.setAttribute('aria-label', 'Move up');
            up.textContent = '↑';
            up.disabled = idx === 0;
            up.addEventListener('click', function () {
                if (idx < 1) return;
                var r = parseState();
                var t = r[idx - 1];
                r[idx - 1] = r[idx];
                r[idx] = t;
                syncHidden(r);
                render();
            });
            var down = document.createElement('button');
            down.type = 'button';
            down.className = 'btn btn-sm btn-outline-secondary lp-section-order-btn';
            down.setAttribute('aria-label', 'Move down');
            down.textContent = '↓';
            down.disabled = idx >= rows.length - 1;
            down.addEventListener('click', function () {
                if (idx >= rows.length - 1) return;
                var r = parseState();
                var t = r[idx + 1];
                r[idx + 1] = r[idx];
                r[idx] = t;
                syncHidden(r);
                render();
            });
            tdO.appendChild(up);
            tdO.appendChild(down);

            tr.appendChild(tdN);
            tr.appendChild(tdL);
            tr.appendChild(tdO);

            if (applyUrl) {
                var tdA = document.createElement('td');
                var list = TEMPLATES_BY_LAYOUT[layout] || [];
                if (list.length === 0) {
                    tdA.innerHTML = '<span class="text-muted small">No templates</span>';
                } else {
                    var form = document.createElement('form');
                    form.method = 'post';
                    form.action = applyUrl;
                    form.className = 'lp-section-apply-form';
                    var tok = document.createElement('input');
                    tok.type = 'hidden';
                    tok.name = '_token';
                    tok.value = csrfToken;
                    form.appendChild(tok);
                    var lk = document.createElement('input');
                    lk.type = 'hidden';
                    lk.name = 'layout_key';
                    lk.value = layout;
                    form.appendChild(lk);
                    var sel = document.createElement('select');
                    sel.name = 'landing_page_section_template_id';
                    sel.className = 'form-control form-control-sm';
                    sel.setAttribute('aria-label', 'Template for ' + (LABELS[layout] || layout));
                    var opt0 = document.createElement('option');
                    opt0.value = '';
                    opt0.textContent = 'Choose…';
                    sel.appendChild(opt0);
                    list.forEach(function (t) {
                        var o = document.createElement('option');
                        o.value = String(t.id);
                        o.textContent = t.name;
                        sel.appendChild(o);
                    });
                    var sub = document.createElement('button');
                    sub.type = 'submit';
                    sub.className = 'action-btn action-btn-secondary lp-btn-compact';
                    sub.textContent = 'Apply';
                    form.appendChild(sel);
                    form.appendChild(sub);
                    tdA.appendChild(form);
                }
                tr.appendChild(tdA);
            }

            var tdR = document.createElement('td');
            var rm = document.createElement('button');
            rm.type = 'button';
            rm.className = 'btn btn-sm btn-outline-danger lp-section-remove-btn';
            rm.textContent = 'Remove';
            rm.addEventListener('click', function () {
                var r = parseState();
                r.splice(idx, 1);
                if (r.length === 0) {
                    r = LAYOUTS.map(function (l) { return { id: uuid(), layout: l }; });
                } else {
                    var hasHero = r.some(function (x) { return x.layout === 'hero'; });
                    if (!hasHero) r.unshift({ id: uuid(), layout: 'hero' });
                }
                syncHidden(r);
                render();
            });
            tdR.appendChild(rm);
            tr.appendChild(tdR);

            tbody.appendChild(tr);
        });
        updateQuickAdd();
    }

    function updateQuickAdd() {
        if (!quickRoot) return;
        quickRoot.innerHTML = '';
        var r = parseState();
        var used = {};
        r.forEach(function (x) { used[x.layout] = true; });
        LAYOUTS.forEach(function (lk) {
            if (used[lk]) return;
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'btn btn-sm btn-outline-primary mr-1 mb-1';
            b.style.minHeight = '44px';
            b.textContent = '+ ' + (LABELS[lk] || lk);
            b.setAttribute('aria-label', 'Add section: ' + (LABELS[lk] || lk));
            b.addEventListener('click', function () {
                addLayoutKey(lk);
            });
            quickRoot.appendChild(b);
        });
        if (quickRoot.children.length === 0) {
            var done = document.createElement('span');
            done.className = 'text-muted small';
            done.textContent = 'All layouts are in the list.';
            quickRoot.appendChild(done);
        }
    }

    function addLayoutKey(layout) {
        if (!layout) return;
        var r = parseState();
        var used = {};
        r.forEach(function (x) { used[x.layout] = true; });
        if (used[layout]) {
            alert('That layout is already in the list (each layout can appear once).');
            return;
        }
        r.push({ id: uuid(), layout: layout });
        syncHidden(r);
        render();
        addLayout.value = '';
    }

    addBtn.addEventListener('click', function () {
        addLayoutKey(addLayout.value);
    });

    var form = hidden.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            var r = parseState();
            if (!r.length) {
                r = LAYOUTS.map(function (l) { return { id: uuid(), layout: l }; });
            }
            syncHidden(r);
        });
    }

    render();
})();
</script>
@endpush
@endonce
