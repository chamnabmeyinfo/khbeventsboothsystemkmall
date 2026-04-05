@php
    $ord = (int) ($sectionOrdinal ?? 1);
    $tierSlots = (int) ($promotionTierFormSlots ?? 8);
    $pd = isset($promotionDiscountsForForm) && is_array($promotionDiscountsForForm) ? $promotionDiscountsForForm : \App\Models\LandingPage::defaultPromotionDiscountsData();
    $tiers = isset($pd['tiers']) && is_array($pd['tiers']) ? $pd['tiers'] : [];
@endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2 d-flex flex-wrap justify-content-between align-items-start gap-2">
        <div class="flex-grow-1" style="min-width: 12rem;">
            <strong class="d-block">{{ $ord }}. Promotion discounts</strong>
            <small class="text-muted">Shown below the package price block: base rate plus one card per tier. Uncheck <strong>Show promotion section</strong> to hide this block on the public page.</small>
        </div>
        @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'promotion_discounts', 'canAutoTranslate' => $canAutoTranslate])
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Promotion section title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'promotion_section_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[promotion_section_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.promotion_section_title', $vloc['promotion_section_title'] ?? '') }}" placeholder="Group promotion discounts">
        </div>
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Tier number subtitle</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'promotion_tier_subtitle', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[promotion_tier_subtitle]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.promotion_tier_subtitle', $vloc['promotion_tier_subtitle'] ?? '') }}" placeholder="participants" maxlength="120">
            <small class="form-text text-muted">Short word under each group size on the public page (e.g. English “participants”, Khmer “នាក់”).</small>
        </div>
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Default tier offer sentence</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'promotion_tier_offer_template', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[promotion_tier_offer_template]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.promotion_tier_offer_template', $vloc['promotion_tier_offer_template'] ?? '') }}" placeholder="For #N# participants, get $#OFF# off each" maxlength="1000">
            <small class="form-text text-muted">Used when a tier has no custom label. Placeholders: <code>#N#</code> = group size, <code>#OFF#</code> = discount per person (number only; put “$” or other currency in the sentence yourself).</small>
        </div>
        <div class="form-group border rounded p-3 bg-light">
            <input type="hidden" name="{{ $pfx }}[promotion_discounts_show]" value="0">
            <div class="form-check mb-0">
                <input type="checkbox" class="form-check-input" name="{{ $pfx }}[promotion_discounts_show]" id="lp-promo-show-{{ $loc }}" value="1" {{ !empty($promotionDiscountsShowChecked) ? 'checked' : '' }}>
                <label class="form-check-label font-weight-bold" for="lp-promo-show-{{ $loc }}">Show promotion section on public page</label>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-12 col-md-6">
                <label class="small font-weight-bold">Base price text</label>
                <input type="text" name="{{ $pfx }}[promotion_discounts][base_price_text]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.promotion_discounts.base_price_text', $pd['base_price_text'] ?? '') }}" placeholder="$499/person" maxlength="500">
            </div>
            <div class="form-group col-12 col-md-6">
                <label class="small font-weight-bold">Intro (optional)</label>
                <input type="text" name="{{ $pfx }}[promotion_discounts][intro_text]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.promotion_discounts.intro_text', $pd['intro_text'] ?? '') }}" placeholder="Short line above the tiers" maxlength="2000">
            </div>
        </div>
        <h6 class="text-secondary border-bottom pb-2 mb-3">Group discount tiers</h6>
        <p class="small text-muted">Each row: minimum group size (participants), discount per person, optional label. Empty rows are ignored.</p>
        @for($ti = 0; $ti < $tierSlots; $ti++)
            @php $row = $tiers[$ti] ?? []; @endphp
            <div class="form-row align-items-end border-bottom pb-2 mb-2">
                <div class="form-group col-6 col-md-3 mb-md-0">
                    <label class="small mb-0">Participants ≥</label>
                    <input type="number" name="{{ $pfx }}[promotion_discounts][tiers][{{ $ti }}][participants]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.promotion_discounts.tiers.'.$ti.'.participants', $row['participants'] ?? '') }}" min="1" max="9999" step="1" placeholder="3">
                </div>
                <div class="form-group col-6 col-md-3 mb-md-0">
                    <label class="small mb-0">Off each ($)</label>
                    <input type="number" name="{{ $pfx }}[promotion_discounts][tiers][{{ $ti }}][off_each]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.promotion_discounts.tiers.'.$ti.'.off_each', $row['off_each'] ?? '') }}" min="1" max="9999" step="1" placeholder="10">
                </div>
                <div class="form-group col-12 col-md-6 mb-0">
                    <label class="small mb-0">Label (optional)</label>
                    <input type="text" name="{{ $pfx }}[promotion_discounts][tiers][{{ $ti }}][label]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.promotion_discounts.tiers.'.$ti.'.label', $row['label'] ?? '') }}" maxlength="500" placeholder="e.g. Early bird">
                </div>
            </div>
        @endfor
    </div>
</div>
