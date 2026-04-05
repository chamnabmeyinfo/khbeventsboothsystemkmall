@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. Hero</strong>
        <small class="text-muted">Full-width banner: dark overlay on the shared background image, optional logo, headline, subtitle, primary CTA, then a row of stat cards. Keep copy scannable; stats use <code>value|label|icon</code> per line.</small>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Hero title</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_title', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[hero_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.hero_title', $vloc['hero_title'] ?? '') }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <label class="mb-0">Hero CTA Text</label>
                        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_cta_text', 'canAutoTranslate' => $canAutoTranslate])
                    </div>
                    <input type="text" name="{{ $pfx }}[hero_cta_text]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.hero_cta_text', $vloc['hero_cta_text'] ?? '') }}" placeholder="Reserve Your Seat Now">
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Hero subtitle</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_subtitle', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <textarea name="{{ $pfx }}[hero_subtitle]" class="form-control" rows="3">{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.hero_subtitle', $vloc['hero_subtitle'] ?? '') }}</textarea>
        </div>
        <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Hero stats</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_stats_text', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <textarea name="{{ $pfx }}[hero_stats_text]" class="form-control" rows="4" placeholder="200+|Countries|globe&#10;70,000+|Exhibition Booths|store&#10;500,000+|Annual Visitors|users">{{ $heroStatsText }}</textarea>
            <small class="text-muted d-block mt-1">One stat per line: <code>value|label|icon</code> (icon keys: globe, store, users, plane, hotel, etc.).</small>
        </div>
    </div>
</div>
