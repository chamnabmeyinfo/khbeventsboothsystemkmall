<section class="lv-section lv-section--trip" data-lp-section="trip">
    <div class="lv-container">
        <h2 data-lv-key="trip_section_title">{{ $tripSectionTitle }}</h2>
        <div class="lv-trip-grid">
            @foreach($tripPhases as $ph)
                @php
                    $tripPhase = trim((string) ($ph['label'] ?? ''));
                    $tripDate = trim((string) ($ph['date'] ?? ''));
                    $intro = trim((string) ($ph['intro'] ?? ''));
                    $subs = is_array($ph['subsections'] ?? null) ? $ph['subsections'] : [];
                    $tripFeaturePath = \App\Models\LandingPage::sanitizeTripPhaseFeatureImagePath((string) ($ph['feature_image'] ?? ''));
                    $tripFeatureUrl = $tripFeaturePath !== '' ? asset($tripFeaturePath) : '';
                    $tripFeatureAlt = trim($tripPhase.($tripDate !== '' ? ' · '.$tripDate : ''));
                    $tripFeatureAlt = $tripFeatureAlt !== '' ? $tripFeatureAlt : $tripSectionTitle;
                @endphp
                <article class="lv-trip-card">
                    @if($tripFeatureUrl !== '')
                        <div class="lv-trip-card__media">
                            <img class="lv-trip-card__media-img" src="{{ $tripFeatureUrl }}" alt="{{ $tripFeatureAlt }}" loading="lazy" decoding="async" width="800" height="500">
                        </div>
                    @endif
                    <div class="lv-trip-card__body">
                    @if($tripPhase !== '')
                        <p class="lv-trip-phase"><span class="lv-trip-phase-badge">{{ $tripPhase }}</span></p>
                    @endif
                    <p class="lv-trip-date">{{ $tripDate }}</p>
                    <p class="lv-trip-status">{{ $ph['status'] ?? '' }}</p>
                    <p class="lv-trip-meta">{{ $ph['seats_left'] ?? '' }} {{ $seatsLeftSuffix }}</p>
                    @if($intro !== '')
                        <p class="lv-trip-intro">{{ $intro }}</p>
                    @endif
                    @if($subs !== [])
                        <ul class="lv-trip-subs">
                            @foreach($subs as $sub)
                                @php
                                    $st = trim((string) (is_array($sub) ? ($sub['title'] ?? '') : ''));
                                    $sd = trim((string) (is_array($sub) ? ($sub['detail'] ?? '') : ''));
                                @endphp
                                @if($st !== '' || $sd !== '')
                                    <li class="lv-trip-sub">
                                        @if($st !== '')
                                            <p class="lv-trip-sub-title">{{ $st }}</p>
                                        @endif
                                        @if($sd !== '')
                                            <p class="lv-trip-sub-detail">{{ $sd }}</p>
                                        @endif
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                    @php
                        $phaseBtnLabel = $tripPhase !== '' ? $tripPhase : ($tripDate !== '' ? $tripDate : 'Trip');
                        $phaseAria = $tripPhaseRegisterCta.': '.$phaseBtnLabel.($tripDate !== '' ? ' · '.$tripDate : '');
                    @endphp
                    <button
                        type="button"
                        class="lv-btn lv-btn--trip-register"
                        data-trip-date="{{ e($tripDate) }}"
                        data-phase-label="{{ e($phaseBtnLabel) }}"
                        aria-label="{{ e($phaseAria) }}"
                    >{{ $tripPhaseRegisterCta }}</button>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
