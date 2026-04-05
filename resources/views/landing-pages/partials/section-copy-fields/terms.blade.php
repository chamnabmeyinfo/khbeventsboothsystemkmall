@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. Terms &amp; Conditions</strong>
        <small class="text-muted">Legal or policy copy; the section heading still appears if the body is empty.</small>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Terms section title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'terms_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[terms_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.terms_title', $vloc['terms_title'] ?? '') }}" placeholder="Terms &amp; Conditions">
        </div>
        <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Terms &amp; Conditions text</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'terms_text', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <textarea name="{{ $pfx }}[terms_text]" class="form-control" rows="8" placeholder="Booking rules, cancellation policy, liability, visas, etc.">{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.terms_text', $vloc['terms_text'] ?? '') }}</textarea>
        </div>
    </div>
</div>
