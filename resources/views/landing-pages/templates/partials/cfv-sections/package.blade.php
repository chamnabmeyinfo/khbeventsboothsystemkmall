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
