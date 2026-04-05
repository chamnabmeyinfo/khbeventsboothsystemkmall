@if(count($tripActivitySlides) > 0)
<section class="lv-section lv-section--trip-activity lv-trip-activity" data-lp-section="trip-activity" aria-labelledby="lvTripActivityHeading">
    <div class="lv-container">
        <header class="lv-trip-activity__head">
            <h2 id="lvTripActivityHeading" data-lv-key="trip_activity_gallery_title">{{ $tripActivityGalleryTitle }}</h2>
        </header>
        <div class="lv-trip-activity__slider" data-lv-trip-activity-slider role="region" aria-roledescription="carousel" aria-label="{{ $tripActivityGalleryTitle }}">
            <button type="button" class="lv-trip-activity__nav lv-trip-activity__nav--prev" aria-label="Previous image" data-lv-trip-act-prev>
                <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
            </button>
            <div class="lv-trip-activity__viewport">
                <div class="lv-trip-activity__track" data-lv-trip-act-track tabindex="0">
                    @foreach($tripActivitySlides as $ti => $sl)
                        @php
                            $lvTaAlt = trim((string) ($sl['title'] ?? ''));
                            if ($lvTaAlt === '') {
                                $lvTaAlt = trim((string) ($sl['headline'] ?? ''));
                            }
                            if ($lvTaAlt === '') {
                                $lvTaAlt = trim((string) ($sl['caption'] ?? ''));
                            }
                            if ($lvTaAlt === '') {
                                $lvTaAlt = 'Trip activity photo '.($ti + 1);
                            }
                            $lvTaHasOverlay = trim((string) ($sl['headline'] ?? '')) !== '' || trim((string) ($sl['title'] ?? '')) !== '' || trim((string) ($sl['caption'] ?? '')) !== '';
                        @endphp
                        <figure class="lv-trip-activity__slide" data-lv-trip-act-slide="{{ $ti }}" aria-label="{{ $lvTaAlt }}">
                            <img class="lv-trip-activity__img" src="{{ $sl['src'] }}" alt="{{ $lvTaAlt }}" loading="{{ $ti === 0 ? 'eager' : 'lazy' }}" decoding="async" width="960" height="540">
                            @if($lvTaHasOverlay)
                                <figcaption class="lv-trip-activity__cap">
                                    @if(trim((string) ($sl['headline'] ?? '')) !== '')
                                        <span class="lv-trip-activity__slide-kicker">{{ $sl['headline'] }}</span>
                                    @endif
                                    @if(trim((string) ($sl['title'] ?? '')) !== '')
                                        <span class="lv-trip-activity__slide-title">{{ $sl['title'] }}</span>
                                    @endif
                                    @if(trim((string) ($sl['caption'] ?? '')) !== '')
                                        <span class="lv-trip-activity__slide-desc">{{ $sl['caption'] }}</span>
                                    @endif
                                </figcaption>
                            @endif
                        </figure>
                    @endforeach
                </div>
            </div>
            <button type="button" class="lv-trip-activity__nav lv-trip-activity__nav--next" aria-label="Next image" data-lv-trip-act-next>
                <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
            </button>
            @if(count($tripActivitySlides) > 1)
                <div class="lv-trip-activity__dots" role="tablist" aria-label="Slide indicators">
                    @foreach($tripActivitySlides as $ti => $_)
                        <button type="button" class="lv-trip-activity__dot" data-lv-trip-act-dot="{{ $ti }}" role="tab" aria-selected="{{ $ti === 0 ? 'true' : 'false' }}" aria-label="Go to slide {{ $ti + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endif
