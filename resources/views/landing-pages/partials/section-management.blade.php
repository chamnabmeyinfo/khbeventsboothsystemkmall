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
<div class="card card-outline-secondary mb-0">
    <div class="card-header py-2">
        <strong class="d-block">Public page sections</strong>
        <small class="text-muted">Choose which blocks appear on the Canton Fair visual page and in what order. Each layout can appear once. <strong>Hero</strong> is always kept if missing. Optional blocks (e.g. promotion, trip slider) still hide themselves when empty.</small>
    </div>
    <div class="card-body">
        <input type="hidden" name="visual[section_blueprint_json]" id="lpSectionBlueprintJson" value="{{ e($sectionBlueprintJson) }}">

        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <div class="form-inline flex-grow-1 flex-wrap gap-2">
                <label class="sr-only" for="lpSectionAddLayout">Add section layout</label>
                <select class="form-control" id="lpSectionAddLayout" aria-label="Layout to add">
                    <option value="">— Add layout —</option>
                    @foreach($lpSectionKeys as $lk)
                        <option value="{{ $lk }}">{{ $lpSectionLabels[$lk] ?? $lk }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-outline-primary" id="lpSectionAddBtn" style="min-height:44px;">Add section</button>
            </div>
            <a href="{{ route('landing-pages.section-templates.index') }}" class="btn btn-default" style="min-height:44px;">
                <i class="fas fa-file-alt mr-1" aria-hidden="true"></i>Section templates
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0" id="lpSectionBlueprintTable">
                <thead class="thead-light">
                    <tr>
                        <th scope="col" style="width:3rem;">#</th>
                        <th scope="col">Layout</th>
                        <th scope="col" style="width:12rem;">Order</th>
                        @if(!empty($landingPage))
                            <th scope="col">Apply template</th>
                        @endif
                        <th scope="col" style="width:6rem;">Remove</th>
                    </tr>
                </thead>
                <tbody id="lpSectionBlueprintTbody"></tbody>
            </table>
        </div>
        <p class="text-muted small mb-0 mt-2">Save the landing page to persist section order. Use <strong>Section templates</strong> to create reusable copy per layout, then apply from this table (saved immediately).</p>
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
            up.className = 'btn btn-sm btn-outline-secondary mr-1';
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
            down.className = 'btn btn-sm btn-outline-secondary';
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
                    form.className = 'd-flex flex-wrap align-items-center gap-1';
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
                    sub.className = 'btn btn-sm btn-outline-primary';
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
            rm.className = 'btn btn-sm btn-outline-danger';
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
    }

    addBtn.addEventListener('click', function () {
        var layout = addLayout.value;
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
