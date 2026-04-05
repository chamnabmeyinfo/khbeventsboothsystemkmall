@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. Package &amp; pricing</strong>
        <small class="text-muted">Warm gradient section: section title, responsive grid of benefit rows with optional icons, then a large red price panel with price + &ldquo;per person&rdquo; line.</small>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Package Title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'package_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[package_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.package_title', $vloc['package_title'] ?? '') }}">
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Package price</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'package_price', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[package_price]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.package_price', $vloc['package_price'] ?? '$499') }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">&ldquo;Per person&rdquo; label</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'per_person_label', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[per_person_label]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.per_person_label', $vloc['per_person_label'] ?? '') }}" placeholder="per person">
                </div>
            </div>
        </div>
        <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Package items (benefits)</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'package_items_text', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <textarea name="{{ $pfx }}[package_items_text]" class="form-control" rows="10" placeholder="Round-trip flight tickets|plane&#10;3 nights / 4 days hotel accommodation|hotel">{{ $packageItemsText }}</textarea>
            <small class="text-muted d-block mt-1">One benefit per line. Optional: <code>text|icon</code> (e.g. <code>text|plane</code>).</small>
        </div>
    </div>
</div>
