@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. Booking</strong>
        <small class="text-muted">Split layout: form + fields on one side, shared <strong>Why / booking image</strong> on the other. Set the section heading and all form control labels for this language.</small>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Booking Section Title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[booking_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.booking_title', $vloc['booking_title'] ?? '') }}">
        </div>
        <p class="text-muted small mb-2">Form labels (this language)</p>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Name placeholder</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_name_placeholder', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[booking_name_placeholder]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.booking_name_placeholder', $vloc['booking_name_placeholder'] ?? '') }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Email placeholder</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_email_placeholder', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[booking_email_placeholder]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.booking_email_placeholder', $vloc['booking_email_placeholder'] ?? '') }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Phone placeholder</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_phone_placeholder', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[booking_phone_placeholder]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.booking_phone_placeholder', $vloc['booking_phone_placeholder'] ?? '') }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Trip dropdown label</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_trip_placeholder', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[booking_trip_placeholder]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.booking_trip_placeholder', $vloc['booking_trip_placeholder'] ?? '') }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Submit button</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_submit_text', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[booking_submit_text]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.booking_submit_text', $vloc['booking_submit_text'] ?? '') }}">
                </div>
            </div>
        </div>
    </div>
</div>
