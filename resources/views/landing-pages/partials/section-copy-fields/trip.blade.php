@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. Trip dates</strong>
        <small class="text-muted">Centered title, then one card per phase with date, status, seats, optional intro, and sub-categories (<code>subsections</code> with title + detail). Use <strong>Trip phases (JSON)</strong> for full content; <strong>Trip date rows</strong> stays as a quick pipe list and is synced from JSON when JSON is saved.</small>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-8">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Trip dates section title</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_section_title', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[trip_section_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.trip_section_title', $vloc['trip_section_title'] ?? '') }}" placeholder="Choose Your Trip Date">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Seats suffix</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'seats_left_suffix', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[seats_left_suffix]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.seats_left_suffix', $vloc['seats_left_suffix'] ?? '') }}" placeholder="seats left">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Per-phase register button label</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_phase_register_cta', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[trip_phase_register_cta]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.trip_phase_register_cta', $vloc['trip_phase_register_cta'] ?? '') }}" placeholder="Register for this trip" maxlength="120">
                    <small class="text-muted d-block mt-1">Shown on each phase card; opens the registration popup.</small>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Register modal title</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_phase_modal_title', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[trip_phase_modal_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.trip_phase_modal_title', $vloc['trip_phase_modal_title'] ?? '') }}" placeholder="Complete your registration" maxlength="255">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="mb-0">Trip phases (JSON)</label>
            <textarea name="{{ $pfx }}[trip_phases_json]" class="form-control font-monospace" rows="14" spellcheck="false" placeholder='[{"label":"Phase I","date":"…","feature_image":"images/landing-pages/your-slug/phase1.jpg","status":"…","seats_left":"…","intro":"…","subsections":[{"title":"…","detail":"…"}]}]'>{{ $tripPhasesJson }}</textarea>
            <small class="text-muted d-block mt-1">Array of objects: <code>label</code>, <code>date</code>, <code>status</code>, <code>seats_left</code>, optional <code>intro</code>, optional <code>feature_image</code> (site path like <code>images/landing-pages/your-slug/photo.jpg</code> after you upload to that folder), and <code>subsections</code> as <code>[{&quot;title&quot;,&quot;detail&quot;}, …]</code>. Invalid JSON cannot be saved. Clear the field to drop rich content and use only the pipe list below.</small>
        </div>
        <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Trip date rows (pipe list)</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_dates_text', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <textarea name="{{ $pfx }}[trip_dates_text]" class="form-control" rows="5" placeholder="Phase I|15 - 19 April 2026|Available|18&#10;Phase II|23 - 27 April 2026|Available|22&#10;Phase III|1 - 5 May 2026|Available|25">{{ $tripDatesText }}</textarea>
            <small class="text-muted d-block mt-1">When you save with valid JSON above, these rows are overwritten from the JSON. Edit the pipe list alone if you are not using JSON.</small>
        </div>
    </div>
</div>
