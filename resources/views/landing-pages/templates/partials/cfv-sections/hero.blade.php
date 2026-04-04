{{-- Canton Fair visual — Hero (shared media from $visual) --}}
<section class="lv-hero @if($heroEnableRotation) lv-hero--rotation @endif" data-lp-section="hero" data-lv-image-key="hero_background_image" data-lv-image-current="{{ $heroBg }}" style="background-image:url('{{ $heroBg }}')" @if(!$heroEnableRotation && ($heroVideoYoutubeId !== null || $heroVideoSrc !== '')) data-hero-video-deferred="1" data-hero-video-delay="3000" @endif @if($heroEnableRotation) data-lp-hero-rotation="1" @endif>
    @if($heroEnableRotation)
        <div class="lv-hero__bg-slideshow" aria-hidden="true">
            <div class="lv-hero__bg-slide" id="lvHeroBgSlide" style="background-image:url('{{ $heroBg }}')"></div>
        </div>
    @endif
    @if($heroVideoYoutubeId !== null)
        <div class="lv-hero__bg-video lv-hero__bg-video--youtube @if($heroEnableRotation) lv-hero__bg-video--rotation-deferred @else lv-hero__bg-video--deferred @endif" aria-hidden="true">
            <iframe
                id="lvHeroYoutubeIframe"
                data-src="{{ $heroYoutubeEmbedUrl }}"
                title="Hero background video"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
            ></iframe>
        </div>
    @elseif($heroVideoSrc !== '')
        <video id="lvHeroHtml5Video" class="lv-hero__bg-video @if($heroEnableRotation) lv-hero__bg-video--rotation-deferred @else lv-hero__bg-video--deferred @endif" muted loop playsinline poster="{{ $heroBg }}" preload="none" aria-hidden="true">
            <source src="{{ $heroVideoSrc }}" type="{{ $heroVideoMime }}">
        </video>
    @endif
    <div class="lv-content lv-container">
        @if($logo)<span class="lv-logo"><img src="{{ $logo }}" alt="Logo" data-lv-image-key="logo_image" data-lv-image-current="{{ $logo }}"></span>@endif
        <h1 data-lv-key="hero_title">{{ $heroTitle }}</h1>
        <h2 data-lv-key="hero_subtitle">{!! nl2br(e($heroSubtitle)) !!}</h2>
        <button type="button" class="lv-btn lv-btn--hero-cta" onclick="trackLandingEvent('cta_click',{cta_label:'VisualHeroCTA',source:'hero'});landingContinue('{{ $heroCtaTarget }}')"><span data-lv-key="hero_cta_text">{{ $heroCtaText }}</span></button>
        <div class="lv-three" style="margin-top:14px;">
            @foreach($heroStats as $stat)
                @php
                    $ik = trim((string) ($stat['icon'] ?? ''));
                    $faClass = $lvFaIcons[$ik] ?? 'fa-solid fa-circle-check';
                @endphp
                <div class="lv-card lv-stat-card">
                    <span class="lv-stat__icon" aria-hidden="true"><i class="{{ $faClass }}"></i></span>
                    <div class="lv-stat-num">{{ $stat['value'] ?? '' }}</div>
                    <div class="lv-stat__label">{{ $stat['label'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
