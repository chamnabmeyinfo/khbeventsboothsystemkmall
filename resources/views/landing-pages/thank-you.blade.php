<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('landing-pages.partials.seo-head', ['seo' => $seo])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/landing-pages-public.css') }}?v=1.0">
</head>
<body class="lp-thx-page">
    <main class="lp-thx" role="main">
        <div class="lp-thx__card">
            <div class="lp-thx__icon" aria-hidden="true">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="lp-thx__title">{{ __('Thank you!') }}</h1>
            <p class="lp-thx__lead">
                {{ __('We have received your details. Our team will follow up with you soon.') }}
            </p>
            <div class="lp-thx__actions">
                <a class="lp-thx__btn lp-thx__btn--primary" href="{{ route('landing-pages.public.show', $landingPage) }}">
                    <i class="fa-solid fa-arrow-left lp-thx__btn-icon" aria-hidden="true"></i>{{ __('Back to offer') }}
                </a>
            </div>
            <p class="lp-thx__fine">
                {{ __('Already have an account?') }}
                <a href="{{ url('/login') }}">{{ __('Log in') }}</a>
            </p>
        </div>
    </main>
</body>
</html>
