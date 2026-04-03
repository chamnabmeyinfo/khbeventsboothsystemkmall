<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle ?? 'Thank you' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanuman:wght@400;700&family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        :root {
            --lp-thx-primary: #c41e1e;
            --lp-thx-accent: #c9a227;
            --lp-thx-ink: #0f172a;
            --lp-thx-muted: #475569;
            --lp-thx-bg: #e8edf6;
            --lp-thx-card: rgba(255, 255, 255, 0.92);
            --lp-thx-radius: 20px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Roboto", "Hanuman", sans-serif;
            font-size: 17px;
            line-height: 1.6;
            color: var(--lp-thx-ink);
            background:
                radial-gradient(120% 80% at 10% 0%, rgba(201, 162, 39, 0.12) 0%, transparent 45%),
                radial-gradient(90% 60% at 100% 20%, rgba(196, 30, 30, 0.08) 0%, transparent 42%),
                linear-gradient(165deg, #e4eaf3 0%, #eef2f9 38%, var(--lp-thx-bg) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(24px, 6vw, 48px);
        }
        .lp-thx {
            width: min(520px, 100%);
            text-align: center;
        }
        .lp-thx__card {
            background: var(--lp-thx-card);
            border-radius: var(--lp-thx-radius);
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 24px 64px rgba(15, 23, 42, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.6) inset;
            padding: clamp(32px, 6vw, 48px) clamp(24px, 5vw, 40px);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .lp-thx__icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: linear-gradient(145deg, rgba(196, 30, 30, 0.12), rgba(201, 162, 39, 0.15));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--lp-thx-primary);
            font-size: 2rem;
        }
        .lp-thx__title {
            margin: 0 0 12px;
            font-size: clamp(1.65rem, 4vw, 2rem);
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--lp-thx-ink);
        }
        .lp-thx__lead {
            margin: 0 0 28px;
            color: var(--lp-thx-muted);
            font-size: 1.05rem;
        }
        .lp-thx__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }
        .lp-thx__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 12px 24px;
            border-radius: 999px;
            font-family: inherit;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .lp-thx__btn:focus {
            outline: 3px solid var(--lp-thx-accent);
            outline-offset: 3px;
        }
        .lp-thx__btn--primary {
            background: linear-gradient(135deg, var(--lp-thx-primary) 0%, #7a1212 100%);
            color: #fff;
            box-shadow: 0 8px 24px rgba(196, 30, 30, 0.35);
        }
        .lp-thx__btn--primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(196, 30, 30, 0.42);
        }
        .lp-thx__btn--ghost {
            background: rgba(255, 255, 255, 0.85);
            color: var(--lp-thx-ink);
            border-color: rgba(15, 23, 42, 0.12);
        }
        .lp-thx__btn--ghost:hover {
            background: #fff;
            transform: translateY(-1px);
        }
        .lp-thx__fine {
            margin-top: 24px;
            font-size: 0.9rem;
            color: var(--lp-thx-muted);
        }
        .lp-thx__fine a {
            color: var(--lp-thx-primary);
            font-weight: 600;
            text-decoration: none;
        }
        .lp-thx__fine a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
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
                    <i class="fa-solid fa-arrow-left" style="margin-right:8px" aria-hidden="true"></i>{{ __('Back to offer') }}
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
