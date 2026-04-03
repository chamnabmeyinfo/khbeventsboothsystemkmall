{{-- Single responsive SEO block: meta, canonical, hreflang, Open Graph, Twitter, JSON-LD (Bootstrap breakpoints N/A). --}}
@php
    $desc = trim((string) ($seo['description'] ?? ''));
    if ($desc === '') {
        $desc = (string) ($seo['title'] ?? '');
    }
@endphp
<title>{{ $seo['title'] ?? config('app.name', 'Landing') }}</title>
<meta name="description" content="{{ $desc }}">
<meta name="robots" content="{{ $seo['robots'] ?? 'index, follow' }}">
<link rel="canonical" href="{{ $seo['canonical_url'] ?? url()->current() }}">
@foreach(($seo['alternates'] ?? []) as $hreflang => $altUrl)
    @if($altUrl !== '')
        <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $altUrl }}">
    @endif
@endforeach
@if(!empty($seo['x_default_url']))
    <link rel="alternate" hreflang="x-default" href="{{ $seo['x_default_url'] }}">
@endif
<meta property="og:title" content="{{ $seo['title'] ?? '' }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ $seo['canonical_url'] ?? url()->current() }}">
<meta property="og:type" content="{{ $seo['og_type'] ?? 'website' }}">
@if(!empty($seo['og_site_name']))
    <meta property="og:site_name" content="{{ $seo['og_site_name'] }}">
@endif
@if(!empty($seo['og_locale']))
    <meta property="og:locale" content="{{ $seo['og_locale'] }}">
@endif
@foreach($seo['alternate_og_locales'] ?? [] as $ogAlt)
    @if(!empty($ogAlt['locale']))
        <meta property="og:locale:alternate" content="{{ $ogAlt['locale'] }}">
    @endif
@endforeach
@if(!empty($seo['og_image']))
    <meta property="og:image" content="{{ $seo['og_image'] }}">
@endif
<meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary_large_image' }}">
<meta name="twitter:title" content="{{ $seo['title'] ?? '' }}">
<meta name="twitter:description" content="{{ $desc }}">
@if(!empty($seo['og_image']))
    <meta name="twitter:image" content="{{ $seo['og_image'] }}">
@endif
@if(!empty($seo['json_ld']))
    <script type="application/ld+json">@json($seo['json_ld'])</script>
@endif
