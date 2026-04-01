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
    $logo = !empty($visual['logo_image']) ? asset($visual['logo_image']) : '';
    $heroBg = !empty($visual['hero_background_image']) ? asset($visual['hero_background_image']) : 'https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1600&auto=format&fit=crop';
    $aboutImage = !empty($visual['about_image']) ? asset($visual['about_image']) : 'https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=1400&auto=format&fit=crop';
    $whyImage = !empty($visual['why_image']) ? asset($visual['why_image']) : 'https://images.unsplash.com/photo-1549692520-acc6669e2f0c?q=80&w=1400&auto=format&fit=crop';
@endphp

<style>
    .lv-wrap{font-family:Inter,Hanuman,"Noto Sans Khmer","Khmer OS Battambang",Arial,sans-serif;color:#111;background:#fff}
    .lv-container{width:min(1120px,92vw);margin:0 auto}
    .lv-hero{position:relative;min-height:88vh;display:flex;align-items:center;justify-content:center;background-size:cover;background-position:center}
    .lv-hero:before{content:"";position:absolute;inset:0;background:rgba(0,0,0,.52)}
    .lv-content{position:relative;z-index:2;color:#fff;text-align:center;padding:80px 0}
    .lv-logo{height:90px;max-width:92vw;object-fit:contain}
    .lv-hero h1{font-size:clamp(30px,5vw,60px);margin:14px 0}
    .lv-hero h2{font-size:clamp(16px,2.6vw,28px);margin:0 0 20px}
    .lv-btn{min-height:44px;border:0;border-radius:12px;padding:12px 20px;background:#e53935;color:#fff;font-weight:700;cursor:pointer}
    .lv-section{padding:56px 0}
    .lv-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:center}
    .lv-image{min-height:300px;border-radius:14px;background-size:cover;background-position:center}
    .lv-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px}
    .lv-three{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
    .lv-booking{background:#f9fafb;border-radius:14px;padding:20px}
    .lv-booking form{display:grid;gap:10px}
    .lv-booking input,.lv-booking select{min-height:44px;border:1px solid #d1d5db;border-radius:10px;padding:10px}
    @media (max-width:991.98px){.lv-grid,.lv-three{grid-template-columns:1fr}}
</style>

<main class="lv-wrap">
    <section class="lv-hero" data-lv-image-key="hero_background_image" data-lv-image-current="{{ $heroBg }}" style="background-image:url('{{ $heroBg }}')">
        <div class="lv-content lv-container">
            @if($logo)<img src="{{ $logo }}" alt="Logo" class="lv-logo" data-lv-image-key="logo_image" data-lv-image-current="{{ $logo }}">@endif
            <h1 data-lv-key="hero_title">{{ $heroTitle }}</h1>
            <h2 data-lv-key="hero_subtitle">{!! nl2br(e($heroSubtitle)) !!}</h2>
            <button class="lv-btn" onclick="trackLandingEvent('cta_click',{cta_label:'VisualHeroCTA',source:'hero'});landingContinue('{{ $heroCtaTarget }}')"><span data-lv-key="hero_cta_text">{{ $heroCtaText }}</span></button>
        </div>
    </section>

    <section class="lv-section">
        <div class="lv-container lv-grid">
            <div class="lv-image" data-lv-image-key="about_image" data-lv-image-current="{{ $aboutImage }}" style="background-image:url('{{ $aboutImage }}')"></div>
            <div>
                <h2 data-lv-key="about_title">{{ $aboutTitle }}</h2>
                <p data-lv-key="about_text_en">{{ $aboutTextEn }}</p>
                <div class="lv-card" data-lv-key="about_text_kh">{{ $aboutTextKh }}</div>
            </div>
        </div>
    </section>

    <section class="lv-section" style="background:#fff7ed;">
        <div class="lv-container">
            <h2 style="text-align:center" data-lv-key="package_title">{{ $packageTitle }}</h2>
            <div class="lv-three">
                <div class="lv-card">Round-trip flight tickets</div>
                <div class="lv-card">3 nights / 4 days hotel accommodation</div>
                <div class="lv-card">Local transport and support</div>
            </div>
            <div class="lv-card" style="margin-top:14px;text-align:center;background:#e53935;color:#fff">
                <div style="font-size:48px;font-weight:900" data-lv-key="package_price">{{ $packagePrice }}</div>
                <div>per person</div>
            </div>
        </div>
    </section>

    <section class="lv-section">
        <div class="lv-container lv-grid">
            <div>
                <h2 data-lv-key="booking_title">{{ $bookingTitle }}</h2>
                <div class="lv-booking">
                    <form id="lvBookingForm">
                        <input type="text" name="name" placeholder="Full name" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="text" name="phone" placeholder="Phone">
                        <select name="tripDate">
                            <option value="">Preferred trip date</option>
                            <option>17 - 20 October 2025</option>
                            <option>25 - 28 October 2025</option>
                            <option>2 - 5 November 2025</option>
                        </select>
                        <button type="submit" class="lv-btn">Book My Seat Now</button>
                    </form>
                </div>
            </div>
            <div class="lv-image" data-lv-image-key="why_image" data-lv-image-current="{{ $whyImage }}" style="background-image:url('{{ $whyImage }}')"></div>
        </div>
    </section>

    <section class="lv-section" style="background:#f9fafb;">
        <div class="lv-container">
            <h2 style="text-align:center" data-lv-key="faq_title">{{ $faqTitle }}</h2>
            <div class="lv-card">Do I need visa for China? Yes, and we provide guidance.</div>
            <div class="lv-card" style="margin-top:10px">Is translation support included? Yes, Khmer + English assistance is included.</div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('lvBookingForm');
    if (!form) return;
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
});
</script>
