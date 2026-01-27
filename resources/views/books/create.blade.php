@extends('layouts.adminlte')

@section('title', 'Create Booking')
@section('page-title', 'Create New Booking')
@section('breadcrumb', 'Bookings / Create')

@push('styles')
<style>
    /* ============================================================
       MODERN MINIMAL BOOKING FORM DESIGN (Desktop & Responsive)
       Concept: Mobile-first but optimized for high-density desktop
       ============================================================ */
    
    :root {
        --bf-primary: #6366f1;
        --bf-primary-dark: #4f46e5;
        --bf-success: #10b981;
        --bf-warning: #f59e0b;
        --bf-danger: #ef4444;
        --bf-info: #06b6d4;
        --bf-gray-50: #f9fafb;
        --bf-gray-100: #f3f4f6;
        --bf-gray-200: #e5e7eb;
        --bf-gray-300: #d1d5db;
        --bf-gray-600: #4b5563;
        --bf-gray-700: #374151;
        --bf-radius: 12px;
        --bf-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* Khmer Font Support - Global Priority */
    html, body, .bf-container, .bf-container *, .modal-content, .modal-content * {
        font-family: "Khmer OS Battambang", "Hanuman", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
    }

    .bf-container {
        padding: 20px 0;
    }

    /* Minimal Card Design */
    .bf-card {
        background: white;
        border-radius: var(--bf-radius);
        border: 1px solid var(--bf-gray-200);
        box-shadow: var(--bf-shadow);
        overflow: hidden;
        margin-bottom: 20px;
    }

    .bf-card-header {
        padding: 12px 20px;
        background: var(--bf-gray-50);
        border-bottom: 1px solid var(--bf-gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .bf-card-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--bf-gray-700);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bf-card-body {
        padding: 16px;
    }

    /* Form Elements styling as requested */
    .bf-form-group {
        margin-bottom: 12px;
    }

    .bf-form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--bf-gray-600);
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .bf-form-control {
        border-radius: 8px;
        border: 1.5px solid var(--bf-gray-200);
        padding: 8px 12px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        width: 100%;
        background-color: #ffffff;
    }

    .bf-form-control:focus {
        border-color: var(--bf-primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    /* Booth Selector Area */
    .bf-booth-selector {
        padding: 10px;
        border-radius: var(--bf-radius);
        background: var(--bf-gray-50);
    }

    /* Booth Grid Layouts */
    .bf-booth-grid {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -6px;
    }

    .bf-booth-item-wrapper {
        padding: 6px;
        transition: all 0.3s ease;
    }

    /* Default View: 2 columns per group */
    .bf-col-6 { width: 50%; }
    
    /* View Mode: Minimal (3 columns) */
    .view-mode-minimal .bf-booth-item-wrapper { width: 33.333%; }
    /* View Mode: Tiny (4 columns) */
    .view-mode-tiny .bf-booth-item-wrapper { width: 25%; }
    /* View Mode: Expand (1 column) */
    .view-mode-expand .bf-booth-item-wrapper { width: 100%; }

    .bf-booth-item {
        position: relative;
        border: 1.5px solid var(--bf-gray-200);
        border-radius: 10px;
        padding: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .bf-booth-item:hover {
        border-color: var(--bf-primary);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .bf-booth-item.selected {
        border-color: var(--bf-primary);
        background: rgba(99, 102, 241, 0.05);
        box-shadow: 0 0 0 2px var(--bf-primary);
    }

    .bf-booth-number {
        font-size: 1rem;
        font-weight: 800;
        color: var(--bf-gray-700);
    }

    .bf-booth-price {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--bf-success);
    }

    .bf-fp-label {
        font-size: 0.6rem;
        color: var(--bf-gray-300);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    /* Interactive Icons */
    .bf-icon-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 1px solid var(--bf-gray-200);
        color: var(--bf-gray-600);
        cursor: pointer;
        transition: all 0.2s;
        padding: 0;
    }

    .bf-icon-btn:hover { background: var(--bf-gray-100); color: var(--bf-primary); }
    .bf-icon-btn.active { background: var(--bf-primary); color: white; border-color: var(--bf-primary); }

    /* View Switcher Controls */
    .bf-view-switcher {
        display: flex;
        gap: 4px;
        background: var(--bf-gray-100);
        padding: 3px;
        border-radius: 10px;
    }

    /* Zone Styling */
    .bf-zone-group {
        margin-bottom: 20px;
    }

    .bf-zone-header {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--bf-gray-600);
        margin-bottom: 10px;
        padding-bottom: 4px;
        border-bottom: 1.5px solid var(--bf-gray-200);
        display: flex;
        justify-content: space-between;
    }

    /* Summary Bar */
    .bf-summary-bar {
        position: sticky;
        bottom: 0;
        background: white;
        border-top: 1px solid var(--bf-gray-200);
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 100;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        margin: 0 -16px -16px -16px;
    }

    /* Click Animation */
    @keyframes clickPulse {
        0% { transform: scale(1); }
        50% { transform: scale(0.96); }
        100% { transform: scale(1); }
    }
    .click-animate:active { animation: clickPulse 0.15s ease-out; }

    /* Custom Mobile Pop-up for Clients */
    .client-popup-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    /* Section switchers */
    .bf-section-view-switcher {
        display: flex;
        gap: 2px;
    }

    @media (max-width: 768px) {
        .bf-col-6, .view-mode-minimal .bf-booth-item-wrapper, .view-mode-tiny .bf-booth-item-wrapper { width: 50%; }
    }
</style>
@endpush

@section('content')
<section class="content">
    <div class="container-fluid bf-container">
        <form action="{{ route('books.store') }}" method="POST" id="bookingForm">
            @csrf
            
            <!-- HEADER AREA -->
            <div class="bf-d-flex bf-justify-content-between bf-align-items-center bf-mb-4" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div class="d-flex align-items-center gap-3">
                    <h4 class="m-0 font-weight-bold"><i class="fas fa-plus-circle text-primary mr-2"></i>New Booking</h4>
                    <div class="bf-view-switcher">
                        <button type="button" class="bf-icon-btn btn-sm click-animate active" data-mode="default" title="Default"><i class="fas fa-th-large"></i></button>
                        <button type="button" class="bf-icon-btn btn-sm click-animate" data-mode="minimal" title="Minimal"><i class="fas fa-th"></i></button>
                        <button type="button" class="bf-icon-btn btn-sm click-animate" data-mode="tiny" title="Tiny"><i class="fas fa- Braille"></i></button>
                        <button type="button" class="bf-icon-btn btn-sm click-animate" data-mode="expand" title="Expand"><i class="fas fa-expand"></i></button>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="bf-icon-btn click-animate" id="btnFilterFP" title="Floor Plan Filter"><i class="fas fa-filter"></i></button>
                    <a href="{{ route('books.index') }}" class="bf-icon-btn click-animate" title="View List"><i class="fas fa-list"></i></a>
                </div>
            </div>

            <!-- CLIENT SECTION (Minimal Card) -->
            <div class="bf-card">
                <div class="bf-card-body p-3">
                    <input type="hidden" id="clientid" name="clientid" required>
                    <div id="selectedClientUI" style="display: none;">
                        <div class="d-flex align-items-center justify-content-between bg-primary rounded-pill px-3 py-2 text-white">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-user-check"></i>
                                <span id="uiClientName" class="font-weight-bold"></span>
                                <small id="uiClientDetails" class="opacity-75 d-none d-md-inline ml-2"></small>
                            </div>
                            <button type="button" class="btn btn-xs btn-light rounded-pill px-2" id="btnChangeClient">Change</button>
                        </div>
                    </div>
                    <div id="searchClientUI">
                        <div class="row g-2 align-items-center">
                            <div class="col">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0"><i class="fas fa-search text-muted"></i></span>
                                    </div>
                                    <input type="text" id="clientSearchInline" class="bf-form-control border-left-0" style="border-radius: 0 8px 8px 0;" placeholder="Find Client Name or Company...">
                                </div>
                                <div id="inlineClientResults" style="display: none; position: absolute; top: 100%; left: 10px; right: 10px; z-index: 1050; margin-top: 4px;">
                                    <div class="card shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
                                        <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;" id="inlineClientResultsList"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3 font-weight-bold click-animate" data-toggle="modal" data-target="#createClientModal">
                                    <i class="fas fa-plus"></i><span class="d-none d-md-inline ml-1">New Client</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="row">
                <!-- FORM SIDE (4 cols) -->
                <div class="col-md-4">
                    <div class="bf-card">
                        <div class="bf-card-header">
                            <h3 class="bf-card-title"><i class="fas fa-file-invoice text-info"></i>Details</h3>
                            <div class="bf-section-view-switcher">
                                <button type="button" class="bf-icon-btn btn-xs active" data-view="1"><i class="fas fa-square"></i></button>
                                <button type="button" class="bf-icon-btn btn-xs" data-view="2"><i class="fas fa-columns"></i></button>
                            </div>
                        </div>
                        <div class="bf-card-body" id="sectionDetails">
                            <div class="bf-form-group">
                                <label class="bf-form-label"><i class="fas fa-tag"></i>Type</label>
                                <select class="bf-form-control" name="type">
                                    <option value="1">Regular</option>
                                    <option value="2">Special</option>
                                    <option value="3">Temporary</option>
                                </select>
                            </div>
                            <div class="bf-form-group">
                                <label class="bf-form-label"><i class="fas fa-calendar-alt"></i>Date Time</label>
                                <input type="datetime-local" class="bf-form-control" name="date_book" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="bf-form-group">
                                <label class="bf-form-label"><i class="fas fa-comment-dots"></i>Notes</label>
                                <textarea class="bf-form-control" name="notes" rows="2" placeholder="Notes..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Booths Quick List -->
                    <div class="p-2 mb-3" id="selectedBoothsList" style="max-height: 150px; overflow-y: auto;">
                        <p class="text-muted small text-center italic">Select booths from the grid</p>
                    </div>
                </div>

                <!-- BOOTH SELECTOR SIDE (8 cols) -->
                <div class="col-md-8">
                    <div class="bf-card">
                        <div class="bf-card-header bg-white">
                            <h3 class="bf-card-title"><i class="fas fa-store text-primary"></i>Booths Selector</h3>
                            <div class="small text-muted font-weight-bold" id="currentFPName">
                                {{ $currentFloorPlan->name ?? 'Select Floor Plan' }}
                            </div>
                        </div>
                        <div class="bf-card-body bf-booth-selector" id="boothGridArea">
                            @if($booths->count() > 0)
                                @php
                                    $zones = [];
                                    foreach($booths as $booth) {
                                        $zoneName = preg_replace('/[0-9]+/', '', $booth->booth_number) ?: 'Other';
                                        if (!isset($zones[$zoneName])) $zones[$zoneName] = [];
                                        $zones[$zoneName][] = $booth;
                                    }
                                    ksort($zones);
                                @endphp

                                @foreach($zones as $zoneName => $zoneBooths)
                                    <div class="bf-zone-group">
                                        <div class="bf-zone-header">
                                            <span>Zone {{ $zoneName }}</span>
                                            <span class="opacity-50">{{ count($zoneBooths) }}</span>
                                        </div>
                                        <div class="bf-booth-grid">
                                            @foreach($zoneBooths as $booth)
                                                <div class="bf-col bf-col-6 bf-mb-2 bf-booth-item-wrapper" 
                                                     data-id="{{ $booth->id }}" 
                                                     data-number="{{ $booth->booth_number }}" 
                                                     data-price="{{ $booth->price }}">
                                                    <div class="bf-booth-item click-animate" onclick="toggleBooth(this)">
                                                        <div class="bf-fp-label">{{ $currentFloorPlan->name ?? '' }}</div>
                                                        <div class="bf-booth-number">{{ $booth->booth_number }}</div>
                                                        <div class="bf-booth-price">${{ number_format($booth->price, 0) }}</div>
                                                        <input type="checkbox" name="booth_ids[]" value="{{ $booth->id }}" class="d-none">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-5 opacity-50">
                                    <i class="fas fa-store-slash fa-3x mb-2"></i>
                                    <p>No available booths</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- STICKY SUMMARY -->
            <div class="bf-summary-bar">
                <div class="d-flex gap-4 align-items-center">
                    <div>
                        <span class="d-block small text-muted font-weight-bold">Selected</span>
                        <span class="font-weight-bold" id="sumCount">0 Booths</span>
                    </div>
                    <div>
                        <span class="d-block small text-muted font-weight-bold">Total</span>
                        <span class="font-weight-bold text-success" id="sumAmount">$0.00</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="reset" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Clear</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 font-weight-bold click-animate" id="btnSubmit">
                        <i class="fas fa-check-circle mr-1"></i>Create Booking
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- FP Picker Modal -->
<div class="modal fade" id="fpPickerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content client-popup-card">
            <div class="modal-body p-4">
                <h5 class="font-weight-bold mb-4">Choose Floor Plan</h5>
                <div class="d-flex flex-column gap-2">
                    @foreach($floorPlans as $fp)
                    <div class="p-3 border rounded-lg d-flex justify-content-between align-items-center click-animate" 
                         style="cursor: pointer;" onclick="selectFP({{ $fp->id }})">
                        <div>
                            <div class="font-weight-bold">{{ $fp->name }}</div>
                            <div class="small text-muted">{{ $fp->event->title ?? 'General' }}</div>
                        </div>
                        <i class="fas fa-chevron-right text-primary"></i>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content client-popup-card">
            <div class="modal-header bg-success text-white py-3 border-0" style="border-radius: 16px 16px 0 0;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-2"></i>New Client</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="createClientForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div id="createClientError" class="alert alert-danger" style="display: none; font-size: 0.8rem;"></div>
                    <div class="bf-form-group">
                        <label class="bf-form-label">Full Name *</label>
                        <input type="text" class="bf-form-control" name="name" required>
                    </div>
                    <div class="bf-form-group">
                        <label class="bf-form-label">Company *</label>
                        <input type="text" class="bf-form-control" name="company" required>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="bf-form-group">
                                <label class="bf-form-label">Phone *</label>
                                <input type="tel" class="bf-form-control" name="phone_number" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bf-form-group">
                                <label class="bf-form-label">Gender</label>
                                <select class="bf-form-control" name="sex"><option value="1">Male</option><option value="2">Female</option></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-success btn-block rounded-pill font-weight-bold py-2 shadow-sm click-animate" id="createClientSubmitBtn">Save & Select</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let selectedBooths = [];

    $(document).ready(function() {
        // Init view modes
        const savedMode = localStorage.getItem('bf_create_mode') || 'default';
        applyViewMode(savedMode);

        $('.bf-view-switcher .bf-icon-btn').on('click', function() {
            const mode = $(this).data('mode');
            applyViewMode(mode);
            localStorage.setItem('bf_create_mode', mode);
        });

        // Section view switchers
        $('.bf-section-view-switcher .bf-icon-btn').on('click', function() {
            const view = $(this).data('view');
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
            
            if (view == 2) {
                $('#sectionDetails').addClass('row').find('.bf-form-group').addClass('col-md-6');
            } else {
                $('#sectionDetails').removeClass('row').find('.bf-form-group').removeClass('col-md-6');
            }
        });

        initClientSearch();
        updateSummary();
    });

    function applyViewMode(mode) {
        $('.bf-view-switcher .bf-icon-btn').removeClass('active');
        $(`.bf-view-switcher .bf-icon-btn[data-mode="${mode}"]`).addClass('active');
        
        $('#boothGridArea').removeClass('view-mode-default view-mode-minimal view-mode-tiny view-mode-expand')
                          .addClass('view-mode-' + mode);
    }

    function toggleBooth(el) {
        const $el = $(el).parent();
        const checkbox = $el.find('input[type="checkbox"]');
        const isChecked = !checkbox.prop('checked');
        
        checkbox.prop('checked', isChecked);
        if (isChecked) $(el).addClass('selected');
        else $(el).removeClass('selected');
        
        updateSummary();
    }

    function updateSummary() {
        let total = 0;
        let count = 0;
        const list = $('#selectedBoothsList');
        list.empty();

        $('.bf-booth-item-wrapper').each(function() {
            if ($(this).find('input').prop('checked')) {
                const id = $(this).data('id');
                const num = $(this).data('number');
                const price = parseFloat($(this).data('price')) || 0;
                
                total += price;
                count++;
                
                list.append(`<span class="badge badge-primary px-2 py-1 mr-1 mb-1 click-animate" onclick="removeBooth(${id})" style="cursor:pointer">#${num} <i class="fas fa-times small ml-1"></i></span>`);
            }
        });

        if (count === 0) list.html('<p class="text-muted small text-center italic">Select booths from the grid</p>');

        $('#sumCount').text(count + (count === 1 ? ' Booth' : ' Booths'));
        $('#sumAmount').text('$' + total.toLocaleString(undefined, {minimumFractionDigits: 2}));
        
        $('#btnSubmit').prop('disabled', count === 0).toggleClass('opacity-50', count === 0);
    }

    window.removeBooth = function(id) {
        $(`.bf-booth-item-wrapper[data-id="${id}"]`).find('input').prop('checked', false).parent().find('.bf-booth-item').removeClass('selected');
        updateSummary();
    };

    function initClientSearch() {
        let timer;
        $('#clientSearchInline').on('input', function() {
            const q = $(this).val().trim();
            clearTimeout(timer);
            if (q.length < 2) { $('#inlineClientResults').hide(); return; }
            
            timer = setTimeout(() => {
                $.get('{{ route("clients.search") }}', { q: q }, function(res) {
                    const list = $('#inlineClientResultsList');
                    list.empty();
                    if (res && res.length > 0) {
                        res.forEach(c => {
                            const item = $(`<div class="p-2 border-bottom click-animate" style="cursor:pointer"><div class="font-weight-bold">${c.company || c.name}</div><div class="small text-muted">${c.phone_number || ''}</div></div>`);
                            item.on('click', () => selectClient(c));
                            list.append(item);
                        });
                        $('#inlineClientResults').show();
                    } else {
                        list.html('<div class="p-3 text-center small text-muted">No clients found</div>');
                        $('#inlineClientResults').show();
                    }
                });
            }, 300);
        });

        $(document).on('click', e => { if (!$(e.target).closest('#clientSearchInline, #inlineClientResults').length) $('#inlineClientResults').hide(); });
        $('#btnChangeClient').on('click', () => { $('#selectedClientUI').hide(); $('#searchClientUI').show(); $('#clientid').val(''); });
    }

    function selectClient(c) {
        $('#clientid').val(c.id);
        $('#uiClientName').text(c.company || c.name);
        $('#uiClientDetails').text(`${c.email || ''} ${c.phone_number ? ' | ' + c.phone_number : ''}`);
        $('#searchClientUI').hide();
        $('#selectedClientUI').fadeIn();
        $('#inlineClientResults').hide();
    }

    $('#btnFilterFP').on('click', () => $('#fpPickerModal').modal('show'));
    window.selectFP = id => window.location.href = '{{ route("books.create") }}?floor_plan_id=' + id;

    $('#createClientForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#createClientSubmitBtn');
        const old = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if (res.status === 'success') {
                    selectClient(res.client);
                    $('#createClientModal').modal('hide');
                    Swal.fire({ icon: 'success', title: 'Client Created', timer: 1500, showConfirmButton: false });
                }
            },
            error: function(xhr) {
                $('#createClientError').text('Error saving client. Please check fields.').show();
            },
            complete: () => btn.prop('disabled', false).html(old)
        });
    });

    $('#bookingForm').on('submit', function(e) {
        if (!$('#clientid').val()) { e.preventDefault(); Swal.fire({ icon: 'warning', title: 'Client required' }); return false; }
        if ($('.bf-booth-item-wrapper input:checked').length === 0) { e.preventDefault(); Swal.fire({ icon: 'warning', title: 'Select at least one booth' }); return false; }
    });
</script>
@endpush
