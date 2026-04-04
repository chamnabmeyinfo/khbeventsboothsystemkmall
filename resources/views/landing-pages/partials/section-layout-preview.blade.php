{{-- Wireframe sketch for Canton Fair visual section (admin only; single responsive block). --}}
@php
    $sid = (int) ($section ?? 1);
    $lpLoc = preg_replace('/[^a-z0-9_-]/i', '', (string) ($loc ?? 'en'));
@endphp
<div class="lp-sec-preview" role="region" aria-labelledby="lp-sec-prev-h-{{ $lpLoc }}-{{ $sid }}">
    <div class="lp-sec-preview__head d-flex flex-wrap align-items-center justify-content-between mb-2">
        <span class="lp-sec-preview__title text-muted small mb-0" id="lp-sec-prev-h-{{ $lpLoc }}-{{ $sid }}">Layout preview</span>
        <span class="status-badge status-badge-neutral">Public page</span>
    </div>

    @if($sid === 1)
        <div class="lp-sec-preview__frame lp-sec-preview__frame--hero" aria-hidden="true">
            <div class="lp-sec-preview__hero-bg"></div>
            <div class="lp-sec-preview__hero-stack">
                <div class="lp-sec-preview__logo"></div>
                <div class="lp-sec-preview__line lp-sec-preview__line--lg"></div>
                <div class="lp-sec-preview__line"></div>
                <div class="lp-sec-preview__cta"></div>
                <div class="lp-sec-preview__row3">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
        <p class="lp-sec-preview__hint small text-muted mb-0">Full-width hero: background, logo, title, subtitle, CTA, three stat cards.</p>
    @elseif($sid === 2)
        <div class="lp-sec-preview__frame lp-sec-preview__frame--about" aria-hidden="true">
            <div class="lp-sec-preview__about-img"></div>
            <div class="lp-sec-preview__about-copy">
                <div class="lp-sec-preview__line lp-sec-preview__line--md"></div>
                <div class="lp-sec-preview__line"></div>
                <div class="lp-sec-preview__line"></div>
                <div class="lp-sec-preview__callout"></div>
            </div>
        </div>
        <p class="lp-sec-preview__hint small text-muted mb-0">Two columns (tablet+): image left, title + text + highlight box right.</p>
    @elseif($sid === 3)
        <div class="lp-sec-preview__frame lp-sec-preview__frame--package" aria-hidden="true">
            <div class="lp-sec-preview__line lp-sec-preview__line--sm mx-auto"></div>
            <div class="lp-sec-preview__pkg-grid">
                @for($i = 0; $i < 6; $i++)<span></span>@endfor
            </div>
            <div class="lp-sec-preview__price"></div>
        </div>
        <p class="lp-sec-preview__hint small text-muted mb-0">Title, benefit grid with icons, large price panel.</p>
    @elseif($sid === 4)
        <div class="lp-sec-preview__frame lp-sec-preview__frame--promo" aria-hidden="true">
            <div class="lp-sec-preview__line lp-sec-preview__line--sm mx-auto"></div>
            <div class="lp-sec-preview__promo-base mx-auto"></div>
            <div class="lp-sec-preview__promo-tiers">
                <span></span><span></span><span></span><span></span>
            </div>
        </div>
        <p class="lp-sec-preview__hint small text-muted mb-0">Optional section: intro, base price card, tier cards in a row/wrap.</p>
    @elseif($sid === 5)
        <div class="lp-sec-preview__frame lp-sec-preview__frame--trip" aria-hidden="true">
            <div class="lp-sec-preview__line lp-sec-preview__line--sm mx-auto"></div>
            <div class="lp-sec-preview__trip-cards">
                <span></span><span></span><span></span>
            </div>
            <div class="lp-sec-preview__trip-slider-note">
                <span class="lp-sec-preview__slider-icon">◀ ▶</span>
                <span>Slider after Agenda (if you add photos below)</span>
            </div>
        </div>
        <p class="lp-sec-preview__hint small text-muted mb-0">Trip phase cards; trip activity photos are configured here but <strong>display after Agenda</strong> on the live page.</p>
    @elseif($sid === 6)
        <div class="lp-sec-preview__frame lp-sec-preview__frame--agenda" aria-hidden="true">
            <div class="lp-sec-preview__line lp-sec-preview__line--sm mx-auto"></div>
            <div class="lp-sec-preview__tabs-fake">
                <span class="is-on"></span><span></span><span></span>
            </div>
            <div class="lp-sec-preview__table">
                <div class="lp-sec-preview__th"><span></span><span></span><span></span></div>
                @for($r = 0; $r < 4; $r++)
                    <div class="lp-sec-preview__tr"><span></span><span></span><span></span></div>
                @endfor
            </div>
        </div>
        <p class="lp-sec-preview__hint small text-muted mb-0">Itinerary table; multiple <strong>Day</strong> tabs on the public page when you split by day.</p>
    @elseif($sid === 7)
        <div class="lp-sec-preview__frame lp-sec-preview__frame--book" aria-hidden="true">
            <div class="lp-sec-preview__book-form">
                <div class="lp-sec-preview__line lp-sec-preview__line--md"></div>
                <div class="lp-sec-preview__inp"></div>
                <div class="lp-sec-preview__inp"></div>
                <div class="lp-sec-preview__inp"></div>
                <div class="lp-sec-preview__inp lp-sec-preview__inp--sel"></div>
                <div class="lp-sec-preview__cta lp-sec-preview__cta--sm"></div>
            </div>
            <div class="lp-sec-preview__book-img"></div>
        </div>
        <p class="lp-sec-preview__hint small text-muted mb-0">Form column + decorative image column (shared asset).</p>
    @elseif($sid === 8)
        <div class="lp-sec-preview__frame lp-sec-preview__frame--faq" aria-hidden="true">
            <div class="lp-sec-preview__line lp-sec-preview__line--sm mx-auto"></div>
            @for($q = 0; $q < 3; $q++)
                <div class="lp-sec-preview__faq-card">
                    <div class="lp-sec-preview__line lp-sec-preview__line--md"></div>
                    <div class="lp-sec-preview__line"></div>
                </div>
            @endfor
            <div class="lp-sec-preview__contact"></div>
        </div>
        <p class="lp-sec-preview__hint small text-muted mb-0">Stacked Q&amp;A cards, then phone link bar.</p>
    @else
        <div class="lp-sec-preview__frame lp-sec-preview__frame--terms" aria-hidden="true">
            <div class="lp-sec-preview__line lp-sec-preview__line--sm mx-auto"></div>
            <div class="lp-sec-preview__terms-panel">
                <div class="lp-sec-preview__line"></div>
                <div class="lp-sec-preview__line"></div>
                <div class="lp-sec-preview__line"></div>
                <div class="lp-sec-preview__line lp-sec-preview__line--short"></div>
            </div>
        </div>
        <p class="lp-sec-preview__hint small text-muted mb-0">Heading + prose panel at the bottom of the page.</p>
    @endif
</div>
