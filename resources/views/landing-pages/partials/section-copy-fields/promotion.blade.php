@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. Promotion discounts</strong>
        <small class="text-muted">Shown below the package price block: base rate card plus one card per tier. Edit JSON for copy and numbers.</small>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Promotion section title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'promotion_section_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[promotion_section_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.promotion_section_title', $vloc['promotion_section_title'] ?? '') }}" placeholder="Group promotion discounts">
        </div>
        <div class="form-group mb-0">
            <label class="mb-1">Promotion discounts (JSON)</label>
            <textarea name="{{ $pfx }}[promotion_discounts_json]" class="form-control font-monospace" rows="14" spellcheck="false">{{ $promotionDiscountsJson }}</textarea>
            <small class="text-muted d-block mt-1">Object with <code>base_price_text</code>, optional <code>intro_text</code>, and <code>tiers</code> (each: <code>participants</code>, <code>off_each</code>, optional <code>label</code>). Or submit a JSON array of tiers only. Clear this field and save to hide the promotion section on the public page.</small>
        </div>
    </div>
</div>
