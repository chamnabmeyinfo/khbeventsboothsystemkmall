@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. Agenda</strong>
        <small class="text-muted">Shown as a <strong>table</strong> on the public page. Each line is one row: <code>slot|activity|detail</code> (detail can be empty). Optional column titles below override the default English headers.</small>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Agenda section title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'agenda_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[agenda_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.agenda_title', $vloc['agenda_title'] ?? '') }}" placeholder="Business Tour Itinerary">
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label class="small text-muted mb-0">Table column: time / slot</label>
                <input type="text" name="{{ $pfx }}[agenda_hdr_slot]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.agenda_hdr_slot', $vloc['agenda_hdr_slot'] ?? '') }}" placeholder="Time / slot" maxlength="120">
            </div>
            <div class="form-group col-md-4">
                <label class="small text-muted mb-0">Table column: activity</label>
                <input type="text" name="{{ $pfx }}[agenda_hdr_activity]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.agenda_hdr_activity', $vloc['agenda_hdr_activity'] ?? '') }}" placeholder="Activity" maxlength="120">
            </div>
            <div class="form-group col-md-4">
                <label class="small text-muted mb-0">Table column: details</label>
                <input type="text" name="{{ $pfx }}[agenda_hdr_detail]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.agenda_hdr_detail', $vloc['agenda_hdr_detail'] ?? '') }}" placeholder="Details" maxlength="120">
            </div>
        </div>
        <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0" for="lp-agenda-items-text-{{ $loc }}">Agenda by day</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'agenda_items_text', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <p class="small text-muted mb-2">Use a <strong>tab per day</strong> (Day 1, Day 2, …). Each tab uses the same columns as the public page: <code>time/slot|activity|details</code> — you can omit the &ldquo;Day 1 ·&rdquo; prefix in the first column; it is added when saving. <strong>Add day</strong> adds another tab.</p>
            <div class="lp-agenda-by-day-editor mb-2" id="lp-agenda-days-editor-{{ $loc }}" data-locale="{{ $loc }}">
                <ul class="nav nav-tabs flex-wrap lp-agenda-admin-nav mb-0" role="tablist">
                    @foreach($agendaDaysForForm as $di => $dayBlock)
                        <li class="nav-item lp-agenda-day-li" role="presentation">
                            <a class="nav-link py-2 px-3 {{ $di === 0 ? 'active' : '' }}" id="lp-agenda-{{ $loc }}-nav-{{ $di }}" data-toggle="tab" href="#lp-agenda-{{ $loc }}-pane-{{ $di }}" role="tab" aria-controls="lp-agenda-{{ $loc }}-pane-{{ $di }}" aria-selected="{{ $di === 0 ? 'true' : 'false' }}">{{ $dayBlock['label'] ?? ('Day '.($di + 1)) }}</a>
                        </li>
                    @endforeach
                    <li class="nav-item lp-agenda-add-day-li align-self-center ml-1" role="presentation">
                        <button type="button" class="btn btn-sm btn-outline-secondary lp-agenda-add-day" data-locale="{{ $loc }}">+ Add day</button>
                    </li>
                </ul>
                <div class="tab-content lp-agenda-admin-content">
                    @foreach($agendaDaysForForm as $di => $dayBlock)
                        <div class="tab-pane fade lp-agenda-day-pane {{ $di === 0 ? 'show active' : '' }}" id="lp-agenda-{{ $loc }}-pane-{{ $di }}" role="tabpanel" aria-labelledby="lp-agenda-{{ $loc }}-nav-{{ $di }}">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                <span class="small text-muted mb-0">One row per line: <code>time or slot|activity|details</code></span>
                                @if(count($agendaDaysForForm) > 1)
                                    <button type="button" class="btn btn-sm btn-outline-danger lp-agenda-remove-day" data-locale="{{ $loc }}">Remove this day</button>
                                @endif
                            </div>
                            <textarea class="form-control font-monospace lp-agenda-day-rows" rows="8" placeholder="06:00|Airport meet|&#10;12:00|Canton Fair|Halls 1–3">{{ $agendaDayRowTexts[$di] ?? '' }}</textarea>
                        </div>
                    @endforeach
                </div>
            </div>
            <textarea name="{{ $pfx }}[agenda_items_text]" class="d-none lp-agenda-items-hidden" id="lp-agenda-items-text-{{ $loc }}" autocomplete="off">{{ $agendaItemsText }}</textarea>
            <small class="text-muted d-block mt-1">Saved data is merged into one list for the API; the public page shows <strong>Day 1 / Day 2</strong> tabs when there are multiple days.</small>
        </div>
    </div>
</div>
