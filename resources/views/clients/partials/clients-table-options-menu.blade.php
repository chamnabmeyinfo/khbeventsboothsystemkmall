{{-- Single compact menu: visible columns, auto-fit, default column width. Expects $clientsColumnDefs --}}
<div class="dropdown clients-table-options">
    <button type="button" class="btn clients-table-options__trigger dropdown-toggle"
            id="clientsTableOptionsTrigger"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false"
            aria-haspopup="true"
            aria-controls="clientsTableOptionsPanel"
            title="Columns, auto-fit, and default column width">
        <i class="fas fa-sliders-h" aria-hidden="true"></i>
        <span class="clients-table-options__trigger-label">Table</span>
    </button>
    <div class="dropdown-menu dropdown-menu-end clients-table-options__panel shadow-sm border"
         id="clientsTableOptionsPanel"
         aria-labelledby="clientsTableOptionsTrigger">
        <div class="clients-table-options__scroll">
            <div class="clients-table-options__caption">Visible</div>
            <div class="clients-table-options__checks">
                @foreach($clientsColumnDefs as $col)
                    <div class="form-check clients-table-options__check">
                        <input class="form-check-input clients-col-checkbox" type="checkbox"
                               id="clients-col-toggle-{{ $col['key'] }}"
                               data-clients-col-key="{{ $col['key'] }}"
                               checked>
                        <label class="form-check-label" for="clients-col-toggle-{{ $col['key'] }}">{{ $col['label'] }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <button type="button" class="dropdown-item clients-table-options__link py-1" id="clientsColsShowAll">Show all columns</button>
        <div class="dropdown-divider my-1"></div>
        <button type="button" class="dropdown-item clients-table-options__link py-2" id="clientsColsAutoFitBtn">
            <i class="fas fa-arrows-alt-h fa-fw me-2 opacity-50" aria-hidden="true"></i>Auto-fit widths
        </button>
        <div class="clients-table-options__default-width">
            <div class="clients-table-options__caption">Default width (px)</div>
            <div class="clients-table-options__default-width-row">
                <input type="number" class="form-control form-control-sm" id="clientsColDefaultWidthInput"
                       min="0" max="300" step="4" value="100" inputmode="numeric" autocomplete="off"
                       title="Target width floor for columns (default 100). Use 0 for built-in only. Capped per column (e.g. checkbox ≤80px).">
                <button type="button" class="btn btn-sm btn-primary" id="clientsColDefaultWidthApply">Apply</button>
                <button type="button" class="btn btn-sm btn-outline-secondary clients-table-options__icon-btn" id="clientsColDefaultWidthReset" title="Restore default (100px)">
                    <i class="fas fa-undo-alt" aria-hidden="true"></i><span class="visually-hidden">Reset to default width</span>
                </button>
            </div>
        </div>
    </div>
</div>
