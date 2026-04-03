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
    $termsTitle = $visual['terms_title'] ?? 'Terms & Conditions';
    $termsText = $visual['terms_text'] ?? '';
    $currentLocale = $currentLocale ?? 'en';
    $agendaTitle = $visual['agenda_title'] ?? 'Trip agenda';
    $rawAgendaItems = is_array($visual['agenda_items'] ?? null) ? $visual['agenda_items'] : [];
    $agendaItems = \App\Models\LandingPage::resolveAgendaItemsForDisplay($rawAgendaItems, $currentLocale);
    $agendaHdrSlot = trim((string) ($visual['agenda_hdr_slot'] ?? '')) ?: 'Time / slot';
    $agendaHdrActivity = trim((string) ($visual['agenda_hdr_activity'] ?? '')) ?: 'Activity';
    $agendaHdrDetail = trim((string) ($visual['agenda_hdr_detail'] ?? '')) ?: 'Details';
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
    /** Font Awesome 6 class by short key (admin: value|label|icon or text|icon). */
    $lvFaIcons = [
        'globe' => 'fa-solid fa-globe',
        'store' => 'fa-solid fa-store',
        'users' => 'fa-solid fa-users',
        'plane' => 'fa-solid fa-plane',
        'hotel' => 'fa-solid fa-hotel',
        'mug-hot' => 'fa-solid fa-mug-hot',
        'bus' => 'fa-solid fa-bus',
        'gift' => 'fa-solid fa-gift',
        'id-card' => 'fa-solid fa-id-card',
        'user-tie' => 'fa-solid fa-user-tie',
        'language' => 'fa-solid fa-language',
        'circle-check' => 'fa-solid fa-circle-check',
    ];
    $heroStatsDefault = [
        ['value' => '200+', 'label' => 'Countries', 'icon' => 'globe'],
        ['value' => '70,000+', 'label' => 'Exhibition Booths', 'icon' => 'store'],
        ['value' => '500,000+', 'label' => 'Annual Visitors', 'icon' => 'users'],
    ];
    $heroStats = is_array($visual['hero_stats'] ?? null) ? $visual['hero_stats'] : $heroStatsDefault;
    $heroIconFallback = ['globe', 'store', 'users'];
    foreach ($heroStats as $i => $row) {
        if (! is_array($heroStats[$i])) {
            unset($heroStats[$i]);
            continue;
        }
        $ic = trim((string) ($heroStats[$i]['icon'] ?? ''));
        $heroStats[$i]['icon'] = $ic !== '' ? $ic : ($heroIconFallback[$i] ?? 'circle-check');
    }
    $heroStats = array_values($heroStats);
    if ($heroStats === []) {
        $heroStats = $heroStatsDefault;
    }
    $packageItemsDefault = [
        ['text' => 'Round-trip flight tickets', 'icon' => 'plane'],
        ['text' => '3 nights / 4 days hotel accommodation', 'icon' => 'hotel'],
        ['text' => 'Free breakfast at the hotel', 'icon' => 'mug-hot'],
        ['text' => 'Local transportation in Guangzhou', 'icon' => 'bus'],
        ['text' => 'Exclusive souvenirs from KHB Events', 'icon' => 'gift'],
        ['text' => 'Fair registration assistance', 'icon' => 'id-card'],
        ['text' => 'Professional tour guide (Khmer + English support)', 'icon' => 'language'],
    ];
    $packageItems = is_array($visual['package_items'] ?? null) ? $visual['package_items'] : $packageItemsDefault;
    $pkgIconFallback = ['plane', 'hotel', 'mug-hot', 'bus', 'gift', 'id-card', 'language'];
    foreach ($packageItems as $i => $row) {
        if (is_string($row)) {
            $packageItems[$i] = [
                'text' => $row,
                'icon' => $pkgIconFallback[$i] ?? 'circle-check',
            ];
        } elseif (is_array($row)) {
            $t = trim((string) ($row['icon'] ?? ''));
            $packageItems[$i] = [
                'text' => $row['text'] ?? '',
                'icon' => $t !== '' ? $t : ($pkgIconFallback[$i] ?? 'circle-check'),
            ];
        } else {
            unset($packageItems[$i]);
        }
    }
    $packageItems = array_values($packageItems);
    if ($packageItems === []) {
        $packageItems = $packageItemsDefault;
    }
    $tripPhases = \App\Models\LandingPage::resolveTripPhasesForDisplay($visual);
    $tripDates = \App\Models\LandingPage::tripPhasesToFlatRows($tripPhases);
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
    .lv-wrap .sr-only{
        position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0
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
    /* Hero primary CTA: eye-catching pulse (scale + glow ring); pauses on hover/focus; booking submit unchanged */
    @keyframes lvHeroCtaPulse{
        0%,100%{
            transform:translateY(0) scale(1);
            box-shadow:
                0 8px 28px rgba(196,30,30,.48),
                inset 0 1px 0 rgba(255,255,255,.14),
                0 0 0 0 rgba(201,162,39,0),
                0 0 20px rgba(255,80,80,.15);
        }
        40%{
            transform:translateY(-3px) scale(1.055);
            box-shadow:
                0 18px 48px rgba(196,30,30,.62),
                inset 0 1px 0 rgba(255,255,255,.28),
                0 0 0 5px rgba(201,162,39,.55),
                0 0 40px 14px rgba(201,162,39,.45);
        }
        70%{
            transform:translateY(-1px) scale(1.03);
            box-shadow:
                0 12px 38px rgba(196,30,30,.55),
                inset 0 1px 0 rgba(255,255,255,.2),
                0 0 0 3px rgba(255,255,255,.35),
                0 0 32px 18px rgba(255,220,120,.3);
        }
    }
    @keyframes lvHeroCtaText{
        0%,100%{transform:scale(1);text-shadow:0 1px 3px rgba(0,0,0,.4);filter:brightness(1)}
        45%{transform:scale(1.06);text-shadow:0 0 22px rgba(255,255,255,.65),0 0 8px rgba(201,162,39,.5);filter:brightness(1.12)}
        70%{transform:scale(1.02);text-shadow:0 0 12px rgba(255,255,255,.4);filter:brightness(1.05)}
    }
    .lv-hero .lv-btn--hero-cta{
        transform:translateZ(0);
        animation:lvHeroCtaPulse 1.85s ease-in-out infinite;
    }
    .lv-hero .lv-btn--hero-cta [data-lv-key]{
        display:inline-block;
        transform-origin:center;
        animation:lvHeroCtaText 1.85s ease-in-out infinite;
    }
    .lv-hero .lv-btn--hero-cta:hover,.lv-hero .lv-btn--hero-cta:focus-visible{animation-play-state:paused}
    .lv-hero .lv-btn--hero-cta:hover [data-lv-key],.lv-hero .lv-btn--hero-cta:focus-visible [data-lv-key]{animation-play-state:paused}
    .lv-section{padding:clamp(48px,8vw,80px) 0}
    .lv-section--about{background:var(--lv-surface)}
    .lv-section--package{background:linear-gradient(180deg,var(--lv-accent-soft) 0%,#fff 100%)}
    .lv-section--trip{background:var(--lv-surface)}
    .lv-section--agenda{background:linear-gradient(180deg,#fff 0%,var(--lv-surface-2) 100%)}
    /* Agenda: single responsive table (public + preview) */
    .lv-agenda-table-wrap{width:100%;margin-top:8px;overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:var(--lv-radius-sm);border:1px solid var(--lv-border);background:var(--lv-surface);box-shadow:var(--lv-shadow)}
    .lv-agenda-table{width:100%;min-width:min(100%,560px);border-collapse:collapse;font-size:.95rem;line-height:1.45}
    .lv-agenda-table thead th{text-align:left;padding:12px 14px;background:var(--lv-surface-2);color:var(--lv-ink);font-weight:700;font-size:.82rem;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid var(--lv-border)}
    .lv-agenda-table tbody td{padding:14px 14px;vertical-align:top;border-bottom:1px solid var(--lv-border)}
    .lv-agenda-table tbody tr:last-child td{border-bottom:none}
    .lv-agenda-table tbody tr:nth-child(even){background:rgba(15,23,42,.025)}
    .lv-agenda-table .lv-agenda-slot{font-weight:700;color:var(--lv-primary);font-size:.95rem}
    .lv-agenda-table .lv-agenda-activity{font-weight:600;color:var(--lv-ink)}
    .lv-agenda-table .lv-agenda-detail{color:var(--lv-body);line-height:1.55}
    .lv-section--booking{background:var(--lv-surface-2)}
    .lv-section--faq{background:linear-gradient(180deg,#f1f5f9 0%,var(--lv-surface-2) 100%)}
    .lv-section--terms{background:var(--lv-surface);border-top:1px solid var(--lv-border)}
    .lv-terms-prose{max-width:min(72ch,100%);margin:0 auto;text-align:left;font-size:1rem;line-height:1.65;color:var(--lv-body);min-height:3rem}
    .lv-terms-prose p{margin:0 0 1em}
    .lv-terms-prose p:last-child{margin-bottom:0}
    .lv-grid{display:grid;grid-template-columns:1fr 1fr;gap:clamp(20px,4vw,40px);align-items:center}
    .lv-image{min-height:min(360px,50vh);border-radius:var(--lv-radius);background-size:cover;background-position:center;box-shadow:var(--lv-shadow-lg);border:1px solid var(--lv-border)}
    .lv-card{
        background:var(--lv-surface);border:1px solid var(--lv-border);border-radius:var(--lv-radius-sm);
        padding:16px 18px;box-shadow:var(--lv-shadow);transition:box-shadow .2s ease,transform .2s ease;
        color:var(--lv-body);line-height:1.5;
    }
    /* Dark translucent panels so stats never wash out against the hero image */
    .lv-hero .lv-card{background:rgba(15,23,42,.78);border:1px solid rgba(255,255,255,.32);backdrop-filter:blur(10px);color:#fff;box-shadow:0 8px 32px rgba(0,0,0,.25)}
    .lv-stat-card{display:flex;flex-direction:column;align-items:center;gap:8px;padding:18px 14px;text-align:center}
    .lv-stat__icon{font-size:clamp(1.35rem,3.5vw,1.75rem);color:rgba(255,255,255,.98);line-height:1}
    .lv-stat__icon i{filter:drop-shadow(0 2px 8px rgba(0,0,0,.25))}
    .lv-stat__label{color:rgba(255,255,255,.95);font-size:.95rem;line-height:1.35;text-shadow:0 1px 2px rgba(0,0,0,.35)}
    .lv-stat-num{font-size:clamp(1.5rem,4vw,1.85rem);font-weight:800;font-family:"Roboto","Hanuman",sans-serif;letter-spacing:-0.03em;color:#fff;text-shadow:0 1px 3px rgba(0,0,0,.45)}
    .lv-three{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-top:8px}
    .lv-package-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,280px),1fr));gap:14px;margin-top:8px}
    .lv-package-item{display:flex;align-items:flex-start;gap:14px;text-align:left;padding:16px 18px}
    .lv-package-item__icon{flex-shrink:0;width:44px;height:44px;border-radius:50%;background:rgba(196,30,30,.1);color:var(--lv-primary);display:flex;align-items:center;justify-content:center;font-size:1.1rem}
    .lv-package-item__text{line-height:1.45;color:var(--lv-body);font-size:1rem;flex:1;min-width:0}
    .lv-section h2{font-size:clamp(1.5rem,3.5vw,2rem);margin:0 0 24px}
    .lv-section--package h2,.lv-section--trip h2{text-align:center;max-width:40ch;margin-left:auto;margin-right:auto;margin-bottom:28px}
    .lv-price-panel{
        margin-top:20px;text-align:center;border-radius:var(--lv-radius);padding:28px 20px;
        background:linear-gradient(145deg,var(--lv-primary) 0%,#6b1010 100%);
        color:#fff;border:1px solid rgba(255,255,255,.12);
        box-shadow:var(--lv-shadow-lg);
    }
    .lv-price-panel--clickable{
        cursor:pointer;
        transition:transform .2s ease,box-shadow .2s ease;
        -webkit-tap-highlight-color:transparent;
    }
    .lv-price-panel--clickable:hover{transform:translateY(-2px);box-shadow:0 16px 44px rgba(196,30,30,.38)}
    .lv-price-panel--clickable:focus-visible{outline:3px solid var(--lv-accent);outline-offset:4px}
    .lv-price-panel--clickable:active{transform:translateY(0)}
    .lv-price-panel .lv-price-num{font-size:clamp(2.25rem,6vw,3.25rem);font-weight:800;font-family:"Roboto","Hanuman",sans-serif;line-height:1}
    .lv-price-panel .lv-price-sub{color:rgba(255,255,255,.95);margin-top:10px;font-size:1.05rem;text-shadow:0 1px 2px rgba(0,0,0,.2)}
    /* Section 4 — Trip dates: Phase I / II / III cards */
    .lv-trip-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-top:8px}
    .lv-trip-card{text-align:left;position:relative;padding:20px 18px 18px;border-radius:var(--lv-radius-sm);border:1px solid var(--lv-border);background:var(--lv-surface);box-shadow:var(--lv-shadow);border-top:4px solid var(--lv-trip-accent,var(--lv-primary))}
    .lv-trip-grid .lv-trip-card:nth-child(3n+1){--lv-trip-accent:#b45309}
    .lv-trip-grid .lv-trip-card:nth-child(3n+2){--lv-trip-accent:#c41e1e}
    .lv-trip-grid .lv-trip-card:nth-child(3n+3){--lv-trip-accent:#1d4ed8}
    .lv-trip-phase{margin:0 0 10px;line-height:1.2}
    .lv-trip-phase-badge{display:inline-block;padding:5px 11px;border-radius:999px;font-size:.72rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
    .lv-trip-grid .lv-trip-card:nth-child(3n+1) .lv-trip-phase-badge{color:#b45309;background:rgba(180,83,9,.12)}
    .lv-trip-grid .lv-trip-card:nth-child(3n+2) .lv-trip-phase-badge{color:#c41e1e;background:rgba(196,30,30,.1)}
    .lv-trip-grid .lv-trip-card:nth-child(3n+3) .lv-trip-phase-badge{color:#1d4ed8;background:rgba(29,78,216,.1)}
    .lv-trip-date{font-weight:700;font-size:clamp(1.05rem,2.2vw,1.2rem);color:var(--lv-ink);line-height:1.35;margin:0 0 10px}
    .lv-trip-status{display:inline-block;margin:0;font-size:.9rem;font-weight:700;color:var(--lv-primary)}
    .lv-trip-meta{margin:8px 0 0;font-size:.92rem;color:var(--lv-body);font-weight:500}
    .lv-trip-intro{margin:10px 0 0;font-size:.94rem;line-height:1.55;color:var(--lv-body)}
    .lv-trip-subs{margin:12px 0 0;padding:0;list-style:none;display:grid;gap:0.75rem}
    .lv-trip-sub{border-left:3px solid var(--lv-trip-accent,var(--lv-primary));padding:0 0 0 12px;margin:0}
    .lv-trip-sub-title{margin:0 0 4px;font-size:.82rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;color:var(--lv-muted)}
    .lv-trip-sub-detail{margin:0;font-size:.9rem;line-height:1.5;color:var(--lv-body)}
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
    @media (max-width:991.98px){.lv-grid,.lv-three,.lv-trip-grid{grid-template-columns:1fr}}
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
        .lv-hero .lv-btn--hero-cta,.lv-hero .lv-btn--hero-cta [data-lv-key]{animation:none !important}
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
                            @php
                                $label = $localeLabels[$loc] ?? ($localeSwitcherNames[$loc] ?? $loc);
                                $switcherTitle = $localeSwitcherNames[$loc] ?? (is_string($label) ? strip_tags($label) : $loc);
                                $flagFile = $localeFlagImages[$loc] ?? null;
                            @endphp
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
    {{-- Section 1 — Hero (admin form card 1); shared images set above --}}
    <section class="lv-hero" data-lp-section="hero" data-lv-image-key="hero_background_image" data-lv-image-current="{{ $heroBg }}" style="background-image:url('{{ $heroBg }}')">
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

    {{-- Section 2 — About --}}
    <section class="lv-section lv-section--about" data-lp-section="about">
        <div class="lv-container lv-grid">
            <div class="lv-image" data-lv-image-key="about_image" data-lv-image-current="{{ $aboutImage }}" style="background-image:url('{{ $aboutImage }}')"></div>
            <div>
                <h2 data-lv-key="about_title">{{ $aboutTitle }}</h2>
                <p data-lv-key="about_text_en">{{ $aboutTextEn }}</p>
                <div class="lv-card lv-about-highlight" data-lv-key="about_text_kh">{{ $aboutTextKh }}</div>
            </div>
        </div>
    </section>

    {{-- Section 3 — Package & pricing --}}
    <section class="lv-section lv-section--package" data-lp-section="package">
        <div class="lv-container">
            <h2 data-lv-key="package_title">{{ $packageTitle }}</h2>
            <div class="lv-package-grid">
                @foreach($packageItems as $item)
                    @php
                        $pkgText = $item['text'] ?? '';
                        $ik = trim((string) ($item['icon'] ?? ''));
                        $faClass = $lvFaIcons[$ik] ?? 'fa-solid fa-circle-check';
                    @endphp
                    <div class="lv-card lv-package-item">
                        <span class="lv-package-item__icon" aria-hidden="true"><i class="{{ $faClass }}"></i></span>
                        <div class="lv-package-item__text">{{ $pkgText }}</div>
                    </div>
                @endforeach
            </div>
            <div
                class="lv-price-panel lv-price-panel--clickable"
                style="margin-top:14px;"
                role="button"
                tabindex="0"
                aria-label="Register or continue"
                onclick="if(typeof trackLandingEvent==='function'){trackLandingEvent('cta_click',{cta_label:'PricePanel',source:'package-price'});} if(typeof landingContinue==='function'){landingContinue('{{ $heroCtaTarget }}');}"
                onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}"
            >
                <div class="lv-price-num" data-lv-key="package_price">{{ $packagePrice }}</div>
                <div class="lv-price-sub" data-lv-key="per_person_label">{{ $perPersonLabel }}</div>
            </div>
        </div>
    </section>

    {{-- Section 4 — Trip phases (Phase I–III: intro + subsections + seats; booking options sync from flat rows) --}}
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
                    @endphp
                    <article class="lv-trip-card">
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
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Section 5 — Agenda (table; uses saved rows or LandingPage::defaultDemoAgendaItemsForLocale) --}}
    <section class="lv-section lv-section--agenda" data-lp-section="agenda" aria-labelledby="lvAgendaHeading">
        <div class="lv-container">
            <h2 id="lvAgendaHeading" data-lv-key="agenda_title">{{ $agendaTitle }}</h2>
            <div class="lv-agenda-table-wrap">
                <table class="lv-agenda-table">
                    <caption class="sr-only">{{ $agendaTitle }}</caption>
                    <thead>
                        <tr>
                            <th scope="col">{{ $agendaHdrSlot }}</th>
                            <th scope="col">{{ $agendaHdrActivity }}</th>
                            <th scope="col">{{ $agendaHdrDetail }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendaItems as $row)
                            @php
                                $slot = trim((string) ($row['slot'] ?? ''));
                                $act = trim((string) ($row['activity'] ?? ''));
                                $det = trim((string) ($row['detail'] ?? ''));
                            @endphp
                            <tr>
                                <td class="lv-agenda-slot">{{ $slot !== '' ? $slot : '—' }}</td>
                                <td class="lv-agenda-activity">{{ $act !== '' ? $act : '—' }}</td>
                                <td class="lv-agenda-detail">{{ $det !== '' ? $det : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Section 6 — Booking --}}
    <section class="lv-section lv-section--booking" data-lp-section="booking">
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
                                @php
                                    $optPhase = trim((string) ($trip['phase'] ?? ''));
                                    $optDate = trim((string) ($trip['date'] ?? ''));
                                    $optLabel = $optPhase !== '' ? $optPhase.' — '.$optDate : $optDate;
                                @endphp
                                <option value="{{ $optDate }}">{{ $optLabel }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="lv-btn" data-lv-key="booking_submit_text">{{ $bookingSubmitText }}</button>
                    </form>
                </div>
            </div>
            <div class="lv-image" data-lv-image-key="why_image" data-lv-image-current="{{ $whyImage }}" style="background-image:url('{{ $whyImage }}')"></div>
        </div>
    </section>

    {{-- Section 7 — FAQ & contact --}}
    <section class="lv-section lv-section--faq" data-lp-section="faq">
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

    {{-- Section 8 — Terms & Conditions (always visible on public page) --}}
    <section class="lv-section lv-section--terms" data-lp-section="terms" aria-labelledby="lvTermsHeading">
        <div class="lv-container">
            <h2 id="lvTermsHeading" data-lv-key="terms_title">{{ $termsTitle }}</h2>
            <div class="lv-card lv-terms-prose">
                <div class="mb-0" data-lv-key="terms_text" style="white-space:pre-wrap;">{{ $termsText }}</div>
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
                meta: {
                    tripDate: form.tripDate.value || '',
                    locale: (window.LandingPageConfig && window.LandingPageConfig.currentLocale) ? window.LandingPageConfig.currentLocale : 'en'
                }
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
