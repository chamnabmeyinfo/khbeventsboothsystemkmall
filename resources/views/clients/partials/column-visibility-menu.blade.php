{{-- Expects: $clientsColumnDefs as list of ['key' => string, 'label' => string] --}}
<div class="dropdown">
    <button class="action-btn action-btn-secondary dropdown-toggle" type="button" id="clientsColumnsMenu"
            data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-haspopup="true"
            aria-controls="clientsColumnsPanel" title="Show or hide table columns">
        <i class="fas fa-columns" aria-hidden="true"></i> Columns
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 clients-column-picker py-3 px-3" id="clientsColumnsPanel" aria-labelledby="clientsColumnsMenu">
        <div class="small fw-bold text-uppercase text-muted mb-2 px-1" style="letter-spacing: 0.04em;">Visible columns</div>
        <div class="d-flex flex-column gap-1">
            @foreach($clientsColumnDefs as $col)
                <div class="form-check">
                    <input class="form-check-input clients-col-checkbox" type="checkbox"
                           id="clients-col-toggle-{{ $col['key'] }}"
                           data-clients-col-key="{{ $col['key'] }}"
                           checked>
                    <label class="form-check-label small" for="clients-col-toggle-{{ $col['key'] }}">{{ $col['label'] }}</label>
                </div>
            @endforeach
        </div>
        <hr class="my-2">
        <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="clientsColsShowAll">Show all columns</button>
    </div>
</div>
