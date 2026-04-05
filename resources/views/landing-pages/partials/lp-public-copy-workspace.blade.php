{{-- All public-facing multilingual copy: same fields as the "Text by language" tab on the main edit form. Set $lpI18nInputRoot to "visual" or "translation_hub". --}}
@php
    use App\Models\LandingPage;
    $lpCopyRoot = $lpI18nInputRoot ?? 'visual';
@endphp
<h6 class="mt-0 mb-2">{{ $lpPublicCopyHeading ?? 'Text by language' }}</h6>
@if(!empty($canAutoTranslate))
    <div class="alert alert-info border mb-3 d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
        <div class="mb-2 mb-md-0 pr-md-3">
            <strong>Translate from English</strong>
            <p class="small mb-0 text-muted">Write and edit copy in the <strong>English</strong> tab first. Use <strong>Update all languages from English</strong> for a full pass, or <strong>From English</strong> next to each field on non-English tabs to translate that field only. Review all tabs before publishing.</p>
        </div>
        <button type="button" class="btn btn-primary text-nowrap" id="lpTranslateFromEnglishBtn">
            <i class="fas fa-language"></i> Update all languages from English
        </button>
    </div>
@endif
<ul class="nav nav-tabs lp-lang-tabs" id="lpLangTabs" role="tablist">
    @foreach($adminLocales as $i => $loc)
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $i === 0 ? 'active' : '' }}" id="tab-{{ $loc }}" data-toggle="tab" href="#pane-{{ $loc }}" role="tab" aria-controls="pane-{{ $loc }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">{{ $localeLabels[$loc] ?? strtoupper($loc) }}</a>
        </li>
    @endforeach
</ul>
<div class="tab-content border border-top-0 p-3 rounded-bottom bg-white">
    @foreach($adminLocales as $i => $loc)
        {{-- Locale vars must live in this view (not a child @include) so section-copy-fields see $lpI18nOldKeyPrefix, $pfx, etc. --}}
        @php
            $lpI18nOldKeyPrefix = $lpCopyRoot.'.i18n';
            $vloc = $visualForm['i18n'][$loc] ?? [];
            $heroStatsText = old($lpI18nOldKeyPrefix.'.'.$loc.'.hero_stats_text', collect($vloc['hero_stats'] ?? [])->map(fn ($row) => trim(($row['value'] ?? '').'|'.($row['label'] ?? '').'|'.($row['icon'] ?? '')))->implode("\n"));
            $packageItemsText = old($lpI18nOldKeyPrefix.'.'.$loc.'.package_items_text', collect($vloc['package_items'] ?? [])->map(function ($row) {
                if (is_string($row)) {
                    return $row;
                }
                if (! is_array($row)) {
                    return '';
                }
                $t = trim((string) ($row['text'] ?? ''));
                $iconKey = trim((string) ($row['icon'] ?? ''));

                return $iconKey !== '' ? $t.'|'.$iconKey : $t;
            })->implode("\n"));
            $tripFromDb = collect($vloc['trip_dates'] ?? [])->map(fn ($row) => trim(($row['phase'] ?? '').'|'.($row['date'] ?? '').'|'.($row['status'] ?? '').'|'.($row['seats_left'] ?? '')))->implode("\n");
            $tripDemoPrefill = ($landingPage === null && $tripFromDb === '') ? LandingPage::defaultTripDatesText() : '';
            $tripDatesText = old($lpI18nOldKeyPrefix.'.'.$loc.'.trip_dates_text', $tripFromDb !== '' ? $tripFromDb : $tripDemoPrefill);
            $tripPhasesOld = old($lpI18nOldKeyPrefix.'.'.$loc.'.trip_phases');
            if (is_array($tripPhasesOld)) {
                $tripPhasesForForm = $tripPhasesOld;
            } elseif (isset($vloc['trip_phases']) && is_array($vloc['trip_phases']) && $vloc['trip_phases'] !== []) {
                $tripPhasesForForm = $vloc['trip_phases'];
            } else {
                $flatRows = LandingPage::parseTripDateRowsFromText($tripDatesText, []);
                $defaultFlat = LandingPage::defaultTripDateRows();
                if ($flatRows === [] || json_encode($flatRows) === json_encode($defaultFlat)) {
                    $tripPhasesForForm = LandingPage::defaultTripPhases();
                } else {
                    $tripPhasesForForm = LandingPage::tripPhasesFromFlatTripDates($flatRows);
                }
            }
            $tripPhaseFormSlots = 8;
            $tripSubsectionSlotsPerPhase = 4;
            $promoFormOld = old($lpI18nOldKeyPrefix.'.'.$loc.'.promotion_discounts');
            if (is_array($promoFormOld)) {
                $promotionDiscountsForForm = $promoFormOld;
            } elseif (isset($vloc['promotion_discounts']) && is_array($vloc['promotion_discounts']) && $vloc['promotion_discounts'] !== []) {
                $promotionDiscountsForForm = LandingPage::sanitizePromotionDiscounts($vloc['promotion_discounts']);
            } else {
                $promotionDiscountsForForm = LandingPage::defaultPromotionDiscountsData();
            }
            $promoShowOld = old($lpI18nOldKeyPrefix.'.'.$loc.'.promotion_discounts_show');
            if ($promoShowOld === null) {
                $promotionDiscountsShowChecked = ($vloc['promotion_discounts_show'] ?? true) !== false;
            } else {
                $promotionDiscountsShowChecked = in_array($promoShowOld, [1, '1', true, 'true'], true);
            }
            $promotionTierFormSlots = 8;
            $agendaFromDb = collect($vloc['agenda_items'] ?? [])->map(fn ($row) => trim(($row['slot'] ?? '').'|'.($row['activity'] ?? '').'|'.($row['detail'] ?? '')))->implode("\n");
            $agendaDemoPrefill = ($landingPage === null && $agendaFromDb === '') ? LandingPage::defaultDemoAgendaItemsText($loc) : '';
            $agendaItemsText = old($lpI18nOldKeyPrefix.'.'.$loc.'.agenda_items_text', $agendaFromDb !== '' ? $agendaFromDb : $agendaDemoPrefill);
            $oldAgendaText = old($lpI18nOldKeyPrefix.'.'.$loc.'.agenda_items_text');
            $agendaDaysForForm = [];
            if ($oldAgendaText !== null && (string) $oldAgendaText !== '') {
                $agendaDaysForForm = LandingPage::buildAgendaDaysFromItems(
                    LandingPage::parseAgendaItemsFromText((string) $oldAgendaText),
                    $loc
                );
            } elseif (! empty($vloc['agenda_days']) && is_array($vloc['agenda_days'])) {
                $agendaDaysForForm = LandingPage::sanitizeAgendaDays($vloc['agenda_days']);
            } elseif (! empty($vloc['agenda_items']) && is_array($vloc['agenda_items'])) {
                $agendaDaysForForm = LandingPage::buildAgendaDaysFromItems($vloc['agenda_items'], $loc);
            } else {
                $agendaDaysForForm = LandingPage::buildAgendaDaysFromItems(
                    LandingPage::parseAgendaItemsFromText((string) $agendaItemsText),
                    $loc
                );
            }
            if ($agendaDaysForForm === []) {
                $agendaDaysForForm = [['day' => 1, 'label' => $loc === 'zh' ? '第1天' : 'Day 1', 'rows' => []]];
            }
            $agendaDayRowTexts = [];
            foreach ($agendaDaysForForm as $di => $dayBlock) {
                $agendaDayRowTexts[$di] = collect($dayBlock['rows'] ?? [])->map(function ($r) {
                    if (! is_array($r)) {
                        return '';
                    }

                    return trim(($r['slot'] ?? '').'|'.($r['activity'] ?? '').'|'.($r['detail'] ?? ''));
                })->filter(fn ($l) => $l !== '')->implode("\n");
            }
            $faqItemsText = old($lpI18nOldKeyPrefix.'.'.$loc.'.faq_items_text', collect($vloc['faq_items'] ?? [])->map(fn ($row) => trim(($row['question'] ?? '').'|'.($row['answer'] ?? '')))->implode("\n"));
            $contactPhonesText = old($lpI18nOldKeyPrefix.'.'.$loc.'.contact_phones_text', collect($vloc['contact_phones'] ?? [])->implode("\n"));
            $tripActivitySlidesText = old($lpI18nOldKeyPrefix.'.'.$loc.'.trip_activity_gallery_slides_text', (string) ($vloc['trip_activity_gallery_slides_text'] ?? ''));
            $pfx = $lpCopyRoot.'[i18n]['.$loc.']';
        @endphp
        <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="pane-{{ $loc }}" role="tabpanel" aria-labelledby="tab-{{ $loc }}">
            <div class="lp-landing-section-layout">
                <div class="lp-landing-section-tabs-wrap">
                    <ul class="nav nav-tabs flex-nowrap lp-landing-section-tabs mb-0" id="lp-section-nav-{{ $loc }}" role="tablist" aria-label="Page sections — {{ $localeLabels[$loc] ?? $loc }} (same order as Sections &amp; order)">
                        @foreach($tabBlueprint as $bi => $secRow)
                            @php
                                $__layout = (string) ($secRow['layout'] ?? '');
                                if (! in_array($__layout, LandingPage::SECTION_LAYOUT_KEYS, true)) {
                                    continue;
                                }
                                $__secId = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) ($secRow['id'] ?? 'sec'));
                                $__tabNum = $bi + 1;
                                $__tabTitle = $sectionLayoutLabels[$__layout] ?? $__layout;
                            @endphp
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $bi === 0 ? 'active' : '' }}" id="lp-{{ $loc }}-tab-{{ $__secId }}" data-toggle="tab" href="#lp-{{ $loc }}-sec-{{ $__secId }}" role="tab" aria-controls="lp-{{ $loc }}-sec-{{ $__secId }}" aria-selected="{{ $bi === 0 ? 'true' : 'false' }}">{{ $__tabNum }}. {{ $__tabTitle }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="tab-content border border-top-0 rounded-bottom bg-white lp-landing-section-panels" id="lp-section-panels-{{ $loc }}">
                    @foreach($tabBlueprint as $bi => $secRow)
                        @php
                            $__layout = (string) ($secRow['layout'] ?? '');
                            if (! in_array($__layout, LandingPage::SECTION_LAYOUT_KEYS, true)) {
                                continue;
                            }
                            $__secId = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) ($secRow['id'] ?? 'sec'));
                            $__previewId = LandingPage::sectionLayoutPreviewSectionId($__layout);
                        @endphp
                        <div class="tab-pane fade {{ $bi === 0 ? 'show active' : '' }}" id="lp-{{ $loc }}-sec-{{ $__secId }}" role="tabpanel" aria-labelledby="lp-{{ $loc }}-tab-{{ $__secId }}" tabindex="0">
                            @include('landing-pages.partials.section-layout-preview', ['section' => $__previewId, 'loc' => $loc])
                            @include('landing-pages.partials.section-copy-fields.'.$__layout, ['sectionOrdinal' => $bi + 1])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
