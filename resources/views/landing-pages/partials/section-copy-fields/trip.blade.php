@php
    $ord = (int) ($sectionOrdinal ?? 1);
    $phaseSlots = (int) ($tripPhaseFormSlots ?? 8);
    $subSlots = (int) ($tripSubsectionSlotsPerPhase ?? 4);
    $phasesData = isset($tripPhasesForForm) && is_array($tripPhasesForForm) ? $tripPhasesForForm : [];
@endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2 d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div class="flex-grow-1" style="min-width: 12rem;">
            <strong class="d-block">{{ $ord }}. Trip dates</strong>
            <small class="text-muted">One block per phase: label, date, status, seats, intro, optional feature image path, and up to {{ $subSlots }} sub-categories (title + detail). Empty phase rows are ignored. <strong>Trip date rows</strong> (pipe list) is synced from these phases when you save.</small>
        </div>
        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_phases', 'canAutoTranslate' => $canAutoTranslate])
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-8">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Trip dates section title</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_section_title', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[trip_section_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_section_title', $vloc['trip_section_title'] ?? '') }}" placeholder="Choose Your Trip Date">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Seats suffix</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'seats_left_suffix', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[seats_left_suffix]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.seats_left_suffix', $vloc['seats_left_suffix'] ?? '') }}" placeholder="seats left">
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
                    <input type="text" name="{{ $pfx }}[trip_phase_register_cta]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phase_register_cta', $vloc['trip_phase_register_cta'] ?? '') }}" placeholder="Register for this trip" maxlength="120">
                    <small class="text-muted d-block mt-1">Shown on each phase card; opens the registration popup.</small>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Register modal title</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_phase_modal_title', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[trip_phase_modal_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phase_modal_title', $vloc['trip_phase_modal_title'] ?? '') }}" placeholder="Complete your registration" maxlength="255">
                </div>
            </div>
        </div>

        <h6 class="text-secondary border-bottom pb-2 mb-3 mt-2">Trip phases</h6>
        @for($pi = 0; $pi < $phaseSlots; $pi++)
            @php
                $phase = $phasesData[$pi] ?? [];
                $subs = isset($phase['subsections']) && is_array($phase['subsections']) ? $phase['subsections'] : [];
            @endphp
            <div class="card border mb-3 lp-trip-phase-form-card">
                <div class="card-header py-2 bg-light">
                    <strong class="small text-uppercase text-muted">Phase {{ $pi + 1 }}</strong>
                </div>
                <div class="card-body pt-3">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6">
                            <label class="small font-weight-bold">Label</label>
                            <input type="text" name="{{ $pfx }}[trip_phases][{{ $pi }}][label]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phases.'.$pi.'.label', $phase['label'] ?? '') }}" placeholder="Phase I" maxlength="255">
                        </div>
                        <div class="form-group col-12 col-md-6">
                            <label class="small font-weight-bold">Date</label>
                            <input type="text" name="{{ $pfx }}[trip_phases][{{ $pi }}][date]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phases.'.$pi.'.date', $phase['date'] ?? '') }}" placeholder="15 – 19 April 2026" maxlength="500">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12 col-md-4">
                            <label class="small font-weight-bold">Status</label>
                            <input type="text" name="{{ $pfx }}[trip_phases][{{ $pi }}][status]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phases.'.$pi.'.status', $phase['status'] ?? '') }}" placeholder="Available" maxlength="120">
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label class="small font-weight-bold">Seats left</label>
                            <input type="text" name="{{ $pfx }}[trip_phases][{{ $pi }}][seats_left]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phases.'.$pi.'.seats_left', $phase['seats_left'] ?? '') }}" placeholder="18" maxlength="120">
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label class="small font-weight-bold">Feature image (path)</label>
                            <input type="text" name="{{ $pfx }}[trip_phases][{{ $pi }}][feature_image]" class="form-control font-monospace small" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phases.'.$pi.'.feature_image', $phase['feature_image'] ?? '') }}" placeholder="images/landing-pages/your-slug/phase1.jpg" maxlength="512">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Intro</label>
                        <textarea name="{{ $pfx }}[trip_phases][{{ $pi }}][intro]" class="form-control" rows="2" maxlength="8000" placeholder="Short paragraph under the phase header">{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phases.'.$pi.'.intro', $phase['intro'] ?? '') }}</textarea>
                    </div>
                    <p class="small text-muted mt-3 mb-2">Sub-categories (title + detail)</p>
                    @for($si = 0; $si < $subSlots; $si++)
                        @php $sub = $subs[$si] ?? []; @endphp
                        <div class="border rounded p-2 mb-2 bg-white">
                            <div class="form-group mb-2">
                                <label class="small mb-0">Sub {{ $si + 1 }} title</label>
                                <input type="text" name="{{ $pfx }}[trip_phases][{{ $pi }}][subsections][{{ $si }}][title]" class="form-control form-control-sm" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phases.'.$pi.'.subsections.'.$si.'.title', $sub['title'] ?? '') }}" maxlength="500">
                            </div>
                            <div class="form-group mb-0">
                                <label class="small mb-0">Sub {{ $si + 1 }} detail</label>
                                <textarea name="{{ $pfx }}[trip_phases][{{ $pi }}][subsections][{{ $si }}][detail]" class="form-control form-control-sm" rows="2" maxlength="8000">{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.trip_phases.'.$pi.'.subsections.'.$si.'.detail', $sub['detail'] ?? '') }}</textarea>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endfor

        <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Trip date rows (pipe list)</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_dates_text', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <textarea name="{{ $pfx }}[trip_dates_text]" class="form-control" rows="5" placeholder="Phase I|15 - 19 April 2026|Available|18&#10;Phase II|23 - 27 April 2026|Available|22&#10;Phase III|1 - 5 May 2026|Available|25">{{ $tripDatesText }}</textarea>
            <small class="text-muted d-block mt-1">When you save, these rows are overwritten from the trip phases above (when at least one phase has a label or date).</small>
        </div>
    </div>
</div>
