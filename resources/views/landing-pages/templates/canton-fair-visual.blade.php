@php
    $heroTitle = $visual['hero_title'] ?? 'Join Canton Fair 2025 With KHB Events - Just $499!';
    $heroSubtitle = $visual['hero_subtitle'] ?? 'Business delegation with limited seats for Cambodian entrepreneurs.';
    $heroCtaText = $visual['hero_cta_text'] ?? 'Reserve Your Seat Now';
    $heroCtaTarget = $visual['hero_cta_target'] ?? ($landingPage->redirect_url ?? '/login');
    $aboutTitle = $visual['about_title'] ?? 'About Canton Fair';
    $aboutTextEn = $visual['about_text_en'] ?? 'Canton Fair is one of the world largest wholesale trade fairs with global manufacturers and buyers.';
    $aboutTextKh = $visual['about_text_kh'] ?? 'Canton Fair ជាពិព័រណ៍បោះដុំធំបំផុតក្នុងពិភពលោក។';
    $packageTitle = $visual['package_title'] ?? 'Your Package Includes';
    $packagePrice = $visual['package_price'] ?? '$499';
    $bookingTitle = $visual['booking_title'] ?? 'Book Your Seat Now';
    $faqTitle = $visual['faq_title'] ?? 'Frequently Asked Questions';
    $tripSectionTitle = $visual['trip_section_title'] ?? 'Choose Your Trip Date';
    $perPersonLabel = $visual['per_person_label'] ?? 'per person';
    $seatsLeftSuffix = $visual['seats_left_suffix'] ?? 'seats left';
    $bookingNamePh = $visual['booking_name_placeholder'] ?? 'Full name';
    $bookingEmailPh = $visual['booking_email_placeholder'] ?? 'Email';
    $bookingPhonePh = $visual['booking_phone_placeholder'] ?? 'Phone';
    $bookingTripPh = $visual['booking_trip_placeholder'] ?? 'Preferred trip date';
    $bookingSubmitText = $visual['booking_submit_text'] ?? 'Book My Seat Now';
    $enabledLocales = $enabledLocales ?? [];
    $localeLabels = is_array($localeLabels ?? null) && ($localeLabels ?? []) !== [] ? $localeLabels : [
        'en' => 'English',
        'km' => 'ខ្មែរ (Khmer)',
        'zh' => '中文 (Chinese)',
    ];
    $langSwitcherUrls = $langSwitcherUrls ?? [];
    $currentLocale = $currentLocale ?? 'en';
    $showLangSwitch = is_array($enabledLocales) && count($enabledLocales) >= 2;
    /** Flag images (public/images/landing-flags); full names for aria/title (no locale shortcuts). */
    $localeFlagImages = [
        'en' => 'gb.png',
        'km' => 'kh.png',
        'zh' => 'cn.png',
    ];
    $localeSwitcherNames = [
        'en' => 'English',
        'km' => 'Khmer',
        'zh' => 'Chinese',
    ];
    $heroStats = is_array($visual['hero_stats'] ?? null) ? $visual['hero_stats'] : [
        ['value' => '70,000+', 'label' => 'Booths & Exhibitors'],
        ['value' => '200+', 'label' => 'Countries Participating'],
        ['value' => '25', 'label' => 'Seats Available'],
    ];
    $packageItems = is_array($visual['package_items'] ?? null) ? $visual['package_items'] : [
        'Round-trip flight tickets',
        '3 nights / 4 days hotel accommodation',
        'Local transport and support',
    ];
    $tripDates = is_array($visual['trip_dates'] ?? null) ? $visual['trip_dates'] : [
        ['date' => '17 - 20 October 2025', 'status' => 'Available', 'seats_left' => '18'],
        ['date' => '25 - 28 October 2025', 'status' => 'Almost Full', 'seats_left' => '7'],
        ['date' => '2 - 5 November 2025', 'status' => 'Available', 'seats_left' => '23'],
    ];
    $faqItems = is_array($visual['faq_items'] ?? null) ? $visual['faq_items'] : [
        ['question' => 'Do I need visa for China?', 'answer' => 'Yes, and we provide guidance.'],
        ['question' => 'Is translation support included?', 'answer' => 'Yes, Khmer + English assistance is included.'],
    ];
    $contactPhones = is_array($visual['contact_phones'] ?? null) ? $visual['contact_phones'] : ['060 815 515', '010 94 76 40'];
    $logo = !empty($visual['logo_image']) ? asset($visual['logo_image']) : '';
    $heroBg = !empty($visual['hero_background_image']) ? asset($visual['hero_background_image']) : 'https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1600&auto=format&fit=crop';
    $aboutImage = !empty($visual['about_image']) ? asset($visual['about_image']) : 'https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=1400&auto=format&fit=crop';
    $whyImage = !empty($visual['why_image']) ? asset($visual['why_image']) : 'https://images.unsplash.com/photo-1549692520-acc6669e2f0c?q=80&w=1400&auto=format&fit=crop';
@endphp

{{-- Single responsive landing CSS; breakpoints align with project: 576 / 768 / 992 / 1200 --}}
{{-- Fonts: Roboto (Latin/English), Hanuman (Khmer); Chinese uses system UI via sans-serif fallback --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700&family=Roboto:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap');
    .lv-wrap{
        --lv-primary:#c41e1e;
        --lv-primary-hover:#9e1818;
        --lv-accent:#c9a227;
        --lv-accent-soft:#fff8e7;
        --lv-ink:#0f172a;
        /* Body / secondary text: darker than slate-500 so copy does not fade into light backgrounds */
        --lv-body:#1e293b;
        --lv-muted:#475569;
        --lv-surface:#ffffff;
        --lv-surface-2:#f8fafc;
        --lv-border:rgba(15,23,42,.12);
        --lv-radius:16px;
        --lv-radius-sm:12px;
        --lv-shadow:0 4px 24px rgba(15,23,42,.08);
        --lv-shadow-lg:0 20px 50px rgba(15,23,42,.12);
        font-family:"Roboto","Hanuman",sans-serif;
        color:var(--lv-body);
        background:var(--lv-surface-2);
        font-size:17px;
        line-height:1.6;
        -webkit-font-smoothing:antialiased;
    }
    .lv-wrap h1,.lv-wrap h2,.lv-wrap h3{font-family:"Roboto","Hanuman",sans-serif;font-weight:600;letter-spacing:-0.02em;line-height:1.2;color:var(--lv-ink)}
    .lv-wrap .lv-section p{color:var(--lv-body)}
    .lv-container{width:min(1140px,92vw);margin:0 auto}
    .lv-hero{position:relative;min-height:min(92vh,900px);display:flex;align-items:center;justify-content:center;background-size:cover;background-position:center}
    /* Stronger scrim so hero copy stays readable on bright or busy photos */
    .lv-hero:before{content:"";position:absolute;inset:0;background:linear-gradient(165deg,rgba(15,23,42,.9) 0%,rgba(30,27,75,.68) 42%,rgba(196,30,30,.42) 100%)}
    .lv-hero:after{content:"";position:absolute;inset:0;background:radial-gradient(ellipse 85% 55% at 50% 18%,rgba(0,0,0,.35),transparent 58%);pointer-events:none}
    .lv-content{position:relative;z-index:2;color:#fff;text-align:center;padding:clamp(48px,12vw,100px) 0}
    /* Logo: soft white glow + light depth (mostly white) */
    .lv-logo{display:inline-block;line-height:0}
    .lv-logo img{
        height:clamp(72px,14vw,100px);
        max-width:min(92vw,300px);
        width:auto;
        object-fit:contain;
        vertical-align:middle;
        filter:
            drop-shadow(0 1px 2px rgba(0,0,0,.22))
            drop-shadow(0 3px 10px rgba(0,0,0,.14))
            drop-shadow(0 0 12px rgba(255,255,255,.85))
            drop-shadow(0 0 24px rgba(255,255,255,.7))
            drop-shadow(0 0 40px rgba(255,255,255,.45))
            drop-shadow(0 0 56px rgba(255,255,255,.22));
    }
    .lv-hero h1{font-size:clamp(1.75rem,5vw,3.25rem);margin:16px 0 12px;color:#fff;text-shadow:0 2px 4px rgba(0,0,0,.55),0 4px 28px rgba(0,0,0,.45)}
    .lv-hero h2{font-size:clamp(1.05rem,2.8vw,1.4rem);font-family:"Roboto","Hanuman",sans-serif;font-weight:500;color:rgba(255,255,255,.98);margin:0 0 28px;max-width:52ch;margin-left:auto;margin-right:auto;line-height:1.55;text-shadow:0 1px 3px rgba(0,0,0,.65),0 2px 14px rgba(0,0,0,.4)}
    /* Buttons do not inherit .lv-wrap fonts from UA styles; set stack so Khmer (Hanuman) applies to hero_cta_text etc. */
    .lv-wrap .lv-btn{
        min-height:48px;border:0;border-radius:999px;padding:14px 28px;
        font-family:"Roboto","Hanuman",sans-serif;
        background:linear-gradient(135deg,var(--lv-primary) 0%,#8b1414 100%);
        color:#fff;font-weight:700;font-size:1rem;cursor:pointer;
        box-shadow:0 8px 28px rgba(196,30,30,.45),inset 0 1px 0 rgba(255,255,255,.15);
        transition:transform .2s ease,box-shadow .2s ease,background .2s ease;
    }
    .lv-wrap .lv-btn [data-lv-key]{font-family:inherit}
    .lv-wrap .lv-btn:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(196,30,30,.5)}
    .lv-wrap .lv-btn:focus{outline:3px solid var(--lv-accent);outline-offset:3px}
    .lv-section{padding:clamp(48px,8vw,80px) 0}
    .lv-section--about{background:var(--lv-surface)}
    .lv-section--package{background:linear-gradient(180deg,var(--lv-accent-soft) 0%,#fff 100%)}
    .lv-section--trip{background:var(--lv-surface)}
    .lv-section--booking{background:var(--lv-surface-2)}
    .lv-section--faq{background:linear-gradient(180deg,#f1f5f9 0%,var(--lv-surface-2) 100%)}
    .lv-grid{display:grid;grid-template-columns:1fr 1fr;gap:clamp(20px,4vw,40px);align-items:center}
    .lv-image{min-height:min(360px,50vh);border-radius:var(--lv-radius);background-size:cover;background-position:center;box-shadow:var(--lv-shadow-lg);border:1px solid var(--lv-border)}
    .lv-card{
        background:var(--lv-surface);border:1px solid var(--lv-border);border-radius:var(--lv-radius-sm);
        padding:16px 18px;box-shadow:var(--lv-shadow);transition:box-shadow .2s ease,transform .2s ease;
        color:var(--lv-body);line-height:1.5;
    }
    /* Dark translucent panels so stats never wash out against the hero image */
    .lv-hero .lv-card{background:rgba(15,23,42,.78);border:1px solid rgba(255,255,255,.32);backdrop-filter:blur(10px);color:#fff;box-shadow:0 8px 32px rgba(0,0,0,.25)}
    .lv-hero .lv-card div:last-child{color:rgba(255,255,255,.95);font-size:.95rem;text-shadow:0 1px 2px rgba(0,0,0,.35)}
    .lv-stat-num{font-size:clamp(1.5rem,4vw,1.85rem);font-weight:800;font-family:"Roboto","Hanuman",sans-serif;letter-spacing:-0.03em;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.45)}
    .lv-three{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:8px}
    .lv-section h2{font-size:clamp(1.5rem,3.5vw,2rem);margin:0 0 24px}
    .lv-section--package h2,.lv-section--trip h2{text-align:center;max-width:40ch;margin-left:auto;margin-right:auto;margin-bottom:28px}
    .lv-price-panel{
        margin-top:20px;text-align:center;border-radius:var(--lv-radius);padding:28px 20px;
        background:linear-gradient(145deg,var(--lv-primary) 0%,#6b1010 100%);
        color:#fff;border:1px solid rgba(255,255,255,.12);
        box-shadow:var(--lv-shadow-lg);
    }
    .lv-price-panel .lv-price-num{font-size:clamp(2.25rem,6vw,3.25rem);font-weight:800;font-family:"Roboto","Hanuman",sans-serif;line-height:1}
    .lv-price-panel .lv-price-sub{color:rgba(255,255,255,.95);margin-top:10px;font-size:1.05rem;text-shadow:0 1px 2px rgba(0,0,0,.2)}
    .lv-trip-card{text-align:left}
    .lv-trip-date{font-weight:700;font-size:1.05rem;color:var(--lv-ink)}
    .lv-trip-status{display:inline-block;margin-top:8px;font-size:.9rem;font-weight:700;color:var(--lv-primary)}
    .lv-trip-meta{margin-top:6px;font-size:.92rem;color:var(--lv-body);font-weight:500}
    .lv-card.lv-about-highlight{background:var(--lv-accent-soft);border-left:4px solid var(--lv-accent);padding:16px 18px;border-radius:0 var(--lv-radius-sm) var(--lv-radius-sm) 0;color:var(--lv-ink)}
    .lv-booking{background:var(--lv-surface);border-radius:var(--lv-radius);padding:24px;border:1px solid var(--lv-border);box-shadow:var(--lv-shadow)}
    .lv-booking form{display:grid;gap:12px}
    .lv-booking input,.lv-booking select{
        min-height:48px;border:1px solid #94a3b8;border-radius:var(--lv-radius-sm);padding:12px 14px;font-size:16px;
        color:var(--lv-ink);background:#fff;
        transition:border-color .15s,box-shadow .15s;
    }
    .lv-booking input:focus,.lv-booking select:focus{border-color:var(--lv-primary);outline:0;box-shadow:0 0 0 3px rgba(196,30,30,.2)}
    .lv-faq-q{display:block;font-size:1.05rem;margin-bottom:8px;color:var(--lv-ink)}
    .lv-faq-a{color:var(--lv-body);font-size:1rem;line-height:1.55}
    .lv-contact-bar a{color:var(--lv-primary);text-decoration:none;font-weight:700}
    .lv-contact-bar a:hover{text-decoration:underline}
    @media (max-width:991.98px){.lv-grid,.lv-three{grid-template-columns:1fr}}
    /* Site header: simple language row (no nested pills / glass) */
    .lv-site-header{
        position:sticky;top:0;z-index:100;
        background:#0f172a;
        border-bottom:1px solid rgba(255,255,255,.1);
    }
    .lv-site-header__inner{
        width:min(1140px,92vw);margin:0 auto;
        padding:10px 0;
        display:flex;align-items:center;justify-content:flex-end;
    }
    .lv-lang-switch{
        display:flex;flex-wrap:wrap;align-items:center;justify-content:flex-end;gap:10px 16px;
        width:100%;
    }
    .lv-lang-switch__label{
        display:inline-flex;align-items:center;gap:6px;
        color:rgba(255,255,255,.82);
        font-size:.875rem;font-weight:600;
        white-space:nowrap;
    }
    .lv-lang-switch__links{
        display:inline-flex;flex-wrap:wrap;align-items:center;gap:0;
    }
    .lv-lang-switch__links > *{
        padding:8px 14px;
        min-height:44px;
        display:inline-flex;align-items:center;gap:6px;
        box-sizing:border-box;
        font-size:.875rem;font-weight:500;
        color:rgba(255,255,255,.65);
        text-decoration:none;
        border-radius:6px;
        transition:color .15s ease,background .15s ease;
    }
    .lv-lang-switch__flag{display:inline-flex;align-items:center;justify-content:center;line-height:0;flex-shrink:0;border-radius:4px;overflow:hidden;box-shadow:0 0 0 1px rgba(255,255,255,.25)}
    .lv-lang-switch__flag-img{display:block;width:32px;height:auto;vertical-align:middle;object-fit:cover}
    .lv-lang-switch__text{font-size:.875rem}
    .lv-lang-switch__links > * + *{
        border-left:1px solid rgba(255,255,255,.15);
        margin-left:2px;
    }
    .lv-lang-switch a:hover{color:#fff;background:rgba(255,255,255,.06)}
    .lv-lang-switch a:focus-visible{outline:2px solid rgba(255,255,255,.45);outline-offset:2px}
    .lv-lang-switch__links > .is-active{
        color:#fff;
        font-weight:600;
        box-shadow:inset 0 -2px 0 #fff;
    }
    .lv-lang-guide-badge{
        display:none;align-items:center;justify-content:center;
        min-width:18px;height:18px;padding:0 5px;
        background:var(--lv-accent);color:#1a1408;font-size:10px;font-weight:700;border-radius:999px;
    }
    .lv-lang-switch.is-guide-active .lv-lang-guide-badge{display:inline-flex}
    .lv-lang-switch.is-guide-active{
        outline:1px solid rgba(201,162,39,.45);
        outline-offset:6px;border-radius:8px;
    }
    .lv-lang-welcome{
        position:absolute;left:50%;transform:translateX(-50%);top:100%;z-index:101;
        margin-top:10px;max-width:min(92vw,440px);width:min(92vw,440px);
        padding:14px 16px 16px;margin:0;
        background:linear-gradient(180deg,#fffef8,#fff);
        border:1px solid rgba(201,162,39,.55);border-radius:var(--lv-radius-sm);
        box-shadow:0 16px 40px rgba(15,23,42,.18);
    }
    .lv-lang-welcome__pointer{
        position:absolute;left:50%;top:-9px;transform:translateX(-50%);
        width:0;height:0;border-left:11px solid transparent;border-right:11px solid transparent;
        border-bottom:11px solid rgba(201,162,39,.55);
    }
    .lv-lang-welcome__pointer:after{
        content:"";position:absolute;left:-10px;top:2px;
        border-left:10px solid transparent;border-right:10px solid transparent;border-bottom:10px solid #fffef8;
    }
    .lv-lang-welcome__title{margin:0 0 6px;font-size:1.05rem;font-weight:700;color:var(--lv-ink);font-family:"Roboto","Hanuman",sans-serif}
    .lv-lang-welcome__text{margin:0 0 12px;font-size:.95rem;color:var(--lv-body);line-height:1.5}
    .lv-lang-welcome__actions{display:flex;flex-wrap:wrap;gap:10px;align-items:center}
    .lv-lang-welcome__dismiss{
        min-height:44px;padding:10px 18px;border-radius:999px;border:0;cursor:pointer;font-weight:700;font-size:.95rem;
        background:linear-gradient(135deg,var(--lv-primary) 0%,#8b1414 100%);color:#fff;
        box-shadow:0 4px 16px rgba(196,30,30,.35);
    }
    .lv-lang-welcome__dismiss:focus{outline:3px solid var(--lv-accent);outline-offset:2px}
    .lv-lang-welcome__hint{font-size:.85rem;color:var(--lv-body);display:flex;align-items:center;gap:6px}
    .lv-lang-welcome__hint span{color:var(--lv-accent);font-weight:800}
    @media (max-width:575.98px){
        .lv-site-header__inner{justify-content:center;padding:8px 0}
        .lv-lang-switch{justify-content:center;gap:8px 12px}
        .lv-lang-switch__links{justify-content:center}
    }
    @media (prefers-reduced-motion:reduce){
        .lv-wrap .lv-btn,.lv-card{transition:none !important}
        .lv-wrap .lv-btn:hover{transform:none}
    }
</style>

<main class="lv-wrap">
    @if($showLangSwitch)
        <header class="lv-site-header" role="banner">
            <div class="lv-site-header__inner">
                <nav id="lvLangSwitchNav" class="lv-lang-switch" aria-labelledby="lvLangSwitchLabel">
                    <span class="lv-lang-switch__label">
                        <span class="lv-lang-guide-badge" aria-hidden="true" title="Language">1</span>
                        <span class="lv-lang-switch__label-text" id="lvLangSwitchLabel">Language</span>
                    </span>
                    <div class="lv-lang-switch__links" role="group" aria-label="Available languages">
                        @foreach($enabledLocales as $loc)
                            @php($label = $localeLabels[$loc] ?? ($localeSwitcherNames[$loc] ?? $loc))
                            @php($switcherTitle = $localeSwitcherNames[$loc] ?? (is_string($label) ? strip_tags($label) : $loc))
                            @php($flagFile = $localeFlagImages[$loc] ?? null)
                            @if(($langSwitcherUrls[$loc] ?? null))
                                <a href="{{ $langSwitcherUrls[$loc] }}" class="lv-lang-switch-link {{ $loc === $currentLocale ? 'is-active' : '' }}" hreflang="{{ $loc }}" data-lv-lang-link="1" aria-label="{{ $switcherTitle }}" title="{{ $switcherTitle }}" onclick="if(typeof trackLandingEvent==='function'){trackLandingEvent('lang_switch',{lang:'{{ $loc }}',source:'lang-switcher'});}">@if($flagFile)<span class="lv-lang-switch__flag" aria-hidden="true"><img class="lv-lang-switch__flag-img" src="{{ asset('images/landing-flags/'.$flagFile) }}" width="32" height="24" alt="" loading="lazy" decoding="async"></span>@else<span class="lv-lang-switch__text">{{ $label }}</span>@endif</a>
                            @else
                                <span class="{{ $loc === $currentLocale ? 'is-active' : '' }}" @if($flagFile) role="img" aria-label="{{ $switcherTitle }}" @endif title="{{ $switcherTitle }}">@if($flagFile)<span class="lv-lang-switch__flag" aria-hidden="true"><img class="lv-lang-switch__flag-img" src="{{ asset('images/landing-flags/'.$flagFile) }}" width="32" height="24" alt="" loading="lazy" decoding="async"></span>@else<span class="lv-lang-switch__text">{{ $label }}</span>@endif</span>
                            @endif
                        @endforeach
                    </div>
                </nav>
            </div>
            <div id="lvLangWelcome" class="lv-lang-welcome" hidden role="alert" aria-live="polite" aria-labelledby="lvLangWelcomeTitle">
                <div class="lv-lang-welcome__pointer" aria-hidden="true"></div>
                <p id="lvLangWelcomeTitle" class="lv-lang-welcome__title">Please select your preferred language</p>
                <p class="lv-lang-welcome__text">Select the language in which you would like to view this page. Content will be shown in your chosen language. You may change your selection at any time using the language bar in the header.</p>
                <div class="lv-lang-welcome__actions">
                    <button type="button" class="lv-lang-welcome__dismiss" id="lvLangWelcomeDismiss">Continue</button>
                    <span class="lv-lang-welcome__hint" aria-hidden="true"><span>↑</span> Language bar in the header</span>
                </div>
            </div>
        </header>
    @endif
    <section class="lv-hero" data-lv-image-key="hero_background_image" data-lv-image-current="{{ $heroBg }}" style="background-image:url('{{ $heroBg }}')">
        <div class="lv-content lv-container">
            @if($logo)<span class="lv-logo"><img src="{{ $logo }}" alt="Logo" data-lv-image-key="logo_image" data-lv-image-current="{{ $logo }}"></span>@endif
            <h1 data-lv-key="hero_title">{{ $heroTitle }}</h1>
            <h2 data-lv-key="hero_subtitle">{!! nl2br(e($heroSubtitle)) !!}</h2>
            <button class="lv-btn" onclick="trackLandingEvent('cta_click',{cta_label:'VisualHeroCTA',source:'hero'});landingContinue('{{ $heroCtaTarget }}')"><span data-lv-key="hero_cta_text">{{ $heroCtaText }}</span></button>
            <div class="lv-three" style="margin-top:14px;">
                @foreach($heroStats as $stat)
                    <div class="lv-card">
                        <div class="lv-stat-num">{{ $stat['value'] ?? '' }}</div>
                        <div>{{ $stat['label'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="lv-section lv-section--about">
        <div class="lv-container lv-grid">
            <div class="lv-image" data-lv-image-key="about_image" data-lv-image-current="{{ $aboutImage }}" style="background-image:url('{{ $aboutImage }}')"></div>
            <div>
                <h2 data-lv-key="about_title">{{ $aboutTitle }}</h2>
                <p data-lv-key="about_text_en">{{ $aboutTextEn }}</p>
                <div class="lv-card lv-about-highlight" data-lv-key="about_text_kh">{{ $aboutTextKh }}</div>
            </div>
        </div>
    </section>

    <section class="lv-section lv-section--package">
        <div class="lv-container">
            <h2 data-lv-key="package_title">{{ $packageTitle }}</h2>
            <div class="lv-three">
                @foreach($packageItems as $item)
                    <div class="lv-card">{{ $item }}</div>
                @endforeach
            </div>
            <div class="lv-price-panel" style="margin-top:14px;">
                <div class="lv-price-num" data-lv-key="package_price">{{ $packagePrice }}</div>
                <div class="lv-price-sub" data-lv-key="per_person_label">{{ $perPersonLabel }}</div>
            </div>
        </div>
    </section>

    <section class="lv-section lv-section--trip">
        <div class="lv-container">
            <h2 data-lv-key="trip_section_title">{{ $tripSectionTitle }}</h2>
            <div class="lv-three">
                @foreach($tripDates as $trip)
                    <div class="lv-card lv-trip-card">
                        <div class="lv-trip-date">{{ $trip['date'] ?? '' }}</div>
                        <div class="lv-trip-status">{{ $trip['status'] ?? '' }}</div>
                        <div class="lv-trip-meta">{{ $trip['seats_left'] ?? '' }} {{ $seatsLeftSuffix }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="lv-section lv-section--booking">
        <div class="lv-container lv-grid">
            <div>
                <h2 data-lv-key="booking_title">{{ $bookingTitle }}</h2>
                <div class="lv-booking">
                    <form id="lvBookingForm">
                        <input type="text" name="name" placeholder="{{ $bookingNamePh }}" required>
                        <input type="email" name="email" placeholder="{{ $bookingEmailPh }}" required>
                        <input type="text" name="phone" placeholder="{{ $bookingPhonePh }}">
                        <select name="tripDate" aria-label="{{ $bookingTripPh }}">
                            <option value="">{{ $bookingTripPh }}</option>
                            @foreach($tripDates as $trip)
                                <option value="{{ $trip['date'] ?? '' }}">{{ $trip['date'] ?? '' }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="lv-btn" data-lv-key="booking_submit_text">{{ $bookingSubmitText }}</button>
                    </form>
                </div>
            </div>
            <div class="lv-image" data-lv-image-key="why_image" data-lv-image-current="{{ $whyImage }}" style="background-image:url('{{ $whyImage }}')"></div>
        </div>
    </section>

    <section class="lv-section lv-section--faq">
        <div class="lv-container">
            <h2 data-lv-key="faq_title">{{ $faqTitle }}</h2>
            @foreach($faqItems as $idx => $faq)
                <div class="lv-card" @if($idx > 0) style="margin-top:12px" @endif>
                    <strong class="lv-faq-q">{{ $faq['question'] ?? '' }}</strong>
                    <div class="lv-faq-a">{{ $faq['answer'] ?? '' }}</div>
                </div>
            @endforeach
            <div class="lv-card lv-contact-bar" style="margin-top:14px;text-align:center;">
                @foreach($contactPhones as $p)
                    <a href="tel:{{ preg_replace('/\\s+/', '', $p) }}" style="margin:0 8px;display:inline-block;">{{ $p }}</a>
                @endforeach
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('lvBookingForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var payload = {
                lead_name: form.name.value || '',
                lead_email: form.email.value || '',
                lead_phone: form.phone.value || '',
                source: 'visual-builder-form',
                meta: { tripDate: form.tripDate.value || '' }
            };
            if (typeof submitLandingLead === 'function') {
                submitLandingLead(payload);
            }
            if (typeof trackLandingEvent === 'function') {
                trackLandingEvent('lead_submit', { cta_label: 'VisualFormSubmit', source: 'visual-builder-form' });
            }
            alert('Lead submitted successfully.');
            form.reset();
        });
    }

    var welcome = document.getElementById('lvLangWelcome');
    var nav = document.getElementById('lvLangSwitchNav');
    var storageKey = 'lv_lang_welcome_dismissed_' + @json((string) $landingPage->id);
    function dismissLangWelcome() {
        if (welcome) {
            welcome.setAttribute('hidden', '');
            welcome.setAttribute('aria-hidden', 'true');
        }
        if (nav) {
            nav.classList.remove('is-guide-active');
        }
        try {
            localStorage.setItem(storageKey, '1');
        } catch (e) {}
    }
    if (welcome && nav) {
        try {
            if (!localStorage.getItem(storageKey)) {
                welcome.removeAttribute('hidden');
                welcome.removeAttribute('aria-hidden');
                nav.classList.add('is-guide-active');
                if (typeof trackLandingEvent === 'function') {
                    trackLandingEvent('lang_welcome_shown', { source: 'lang-welcome-banner' });
                }
            }
        } catch (e) {
            welcome.removeAttribute('hidden');
            nav.classList.add('is-guide-active');
        }
        var btn = document.getElementById('lvLangWelcomeDismiss');
        if (btn) {
            btn.addEventListener('click', function () {
                if (typeof trackLandingEvent === 'function') {
                    trackLandingEvent('lang_welcome_dismiss', { source: 'continue' });
                }
                dismissLangWelcome();
            });
        }
        nav.querySelectorAll('a[data-lv-lang-link]').forEach(function (a) {
            a.addEventListener('click', function () {
                dismissLangWelcome();
            });
        });
    }
});
</script>
