@if($promotionShow)
<section class="lv-section lv-section--promotion" data-lp-section="promotion" aria-labelledby="lvPromoHeading">
    <div class="lv-container">
        <h2 id="lvPromoHeading" data-lv-key="promotion_section_title">{{ $promotionSectionTitle }}</h2>
        @if(trim((string) ($promotion['intro_text'] ?? '')) !== '')
            <div class="lv-promo-intro-banner">
                <p class="lv-promo-intro">{{ $promotion['intro_text'] }}</p>
            </div>
        @endif
        <div class="lv-promo-base-wrap">
            <div class="lv-card lv-promo-base">
                <div class="lv-promo-base__price">{{ $promotion['base_price_text'] }}</div>
            </div>
        </div>
        <div class="lv-promo-tier-grid">
            @foreach($promotion['tiers'] as $tier)
                @php
                    $pN = (int) ($tier['participants'] ?? 0);
                    $pOff = (int) ($tier['off_each'] ?? 0);
                    $pLbl = trim((string) ($tier['label'] ?? ''));
                    $pTmpl = $promotionTierOfferTemplate ?? 'For #N# participants, get $#OFF# off each';
                    $pLine = $pLbl !== '' ? $pLbl : str_replace(['#N#', '#OFF#'], [(string) $pN, (string) $pOff], $pTmpl);
                @endphp
                <article class="lv-card lv-promo-tier">
                    <div class="lv-promo-tier__icon" aria-hidden="true"><i class="fa-solid fa-users"></i></div>
                    <div class="lv-promo-tier__num">{{ $pN }}</div>
                    <div class="lv-promo-tier__num-sub">{{ $promotionTierSubtitle ?? 'participants' }}</div>
                    <p class="lv-promo-tier__off">{{ $pLine }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
