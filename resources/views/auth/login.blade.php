<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KHB Events</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-blue-hover: #005bb5;
            --text-main: #1d1d1f;
            --text-muted: #86868b;
            --border-glass: rgba(255, 255, 255, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
        }

        /* iOS/macOS Animated Mesh Gradient Background */
        body {
            background-color: #ffb8d2;
            background-image: 
                radial-gradient(at 0% 0%, #4facfe 0px, transparent 50%),
                radial-gradient(at 100% 0%, #00f2fe 0px, transparent 50%),
                radial-gradient(at 100% 100%, #f093fb 0px, transparent 50%),
                radial-gradient(at 0% 100%, #f5576c 0px, transparent 50%),
                radial-gradient(at 50% 50%, #5ee7df 0px, transparent 50%);
            background-size: 200% 200%;
            animation: gradientMove 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overscroll-behavior: none;
            -webkit-font-smoothing: antialiased;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glassmorphism Wrapper */
        .login-wrapper {
            width: 100%;
            max-width: 1050px;
            min-height: 600px;
            margin: 2rem;
            display: flex;
            position: relative;
            z-index: 10;
            /* Premium iOS Glass Effect */
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(40px) saturate(180%);
            -webkit-backdrop-filter: blur(40px) saturate(180%);
            border: 1px solid var(--border-glass);
            border-radius: 36px;
            box-shadow: 0 40px 80px -15px rgba(0, 0, 0, 0.15), inset 0 0 0 1px rgba(255, 255, 255, 0.6);
            overflow: hidden;
        }

        /* Left Side: Branding / Visual */
        .login-visual {
            flex: 1.2;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            color: var(--text-main);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .brand-logo-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--ios-blue);
        }

        .visual-content h2 {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.05;
            margin-bottom: 1.5rem;
            letter-spacing: -0.04em;
            color: var(--text-main);
            background: linear-gradient(135deg, #1d1d1f 0%, #434353 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .visual-content p {
            font-size: 1.125rem;
            color: rgba(29, 29, 31, 0.8);
            line-height: 1.6;
            max-width: 420px;
            font-weight: 400;
        }

        .visual-footer {
            font-size: 0.875rem;
            color: rgba(29, 29, 31, 0.6);
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 500;
        }

        .system-badge {
            background: rgba(255, 255, 255, 0.5);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border: 1px solid rgba(255,255,255,0.7);
            color: var(--text-main);
        }

        /* Right Side: Form */
        .login-form-container {
            flex: 1;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.35); /* Slightly more opaque glass */
            border-left: 1px solid rgba(255, 255, 255, 0.4);
        }

        .form-header {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .form-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            padding-left: 0.25rem;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1.2rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        /* iOS Input Fields */
        .form-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3.2rem;
            font-size: 1rem;
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            transition: all 0.25s ease;
            outline: none;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02), 0 2px 5px rgba(0,0,0,0.01);
        }

        .form-input::placeholder {
            color: #a1a1aa;
        }

        .form-input:hover {
            background: rgba(255, 255, 255, 0.8);
        }

        .form-input:focus {
            background: #ffffff;
            border-color: var(--ios-blue);
            box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.15);
        }

        .form-input:focus + .input-icon {
            color: var(--ios-blue);
        }

        .form-input.is-invalid {
            border-color: #ff3b30;
            background: rgba(255, 59, 48, 0.05);
        }
        
        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(255, 59, 48, 0.15);
        }

        .input-error {
            color: #ff3b30;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding-left: 0.25rem;
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 0 0.25rem;
        }

        /* iOS Toggle / Checkbox style */
        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox input {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 6px;
            accent-color: var(--ios-blue);
            cursor: pointer;
        }

        .remember-checkbox span {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        /* iOS Pill Button */
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--ios-blue);
            color: white;
            font-size: 1.05rem;
            font-weight: 600;
            border: none;
            border-radius: 100px; /* Fully rounded pill */
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.25, 0.8, 0.25, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 8px 16px rgba(0, 122, 255, 0.25);
        }

        .btn-submit:hover {
            background: var(--ios-blue-hover);
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(0, 122, 255, 0.3);
        }

        .btn-submit:active {
            transform: translateY(1px) scale(0.98);
        }

        .global-error {
            background: rgba(255, 59, 48, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 59, 48, 0.3);
            color: #d90429;
            padding: 1rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        /* Mobile Adjustments */
        @media (max-width: 900px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 480px;
                min-height: auto;
                border-radius: 30px;
            }
            .login-visual {
                padding: 3rem 2rem;
                flex: none;
            }
            .visual-content h2 {
                font-size: 2.25rem;
            }
            .login-form-container {
                flex: auto;
                padding: 3rem 2rem;
                border-left: none;
                border-top: 1px solid rgba(255, 255, 255, 0.4);
            }
        }
    </style>
</head>
<body>
    @php
        $companySettings = \App\Models\Setting::getCompanySettings();
        $logoPath = trim(str_replace('\\', '/', $companySettings['company_logo'] ?? ''));
        $logoUrl = $logoPath ? \App\Helpers\AssetHelper::imageUrl(ltrim($logoPath, '/')) : null;
        $companyName = $companySettings['company_name'] ?? 'KHB Events';
    @endphp

    <div class="login-wrapper">
        
        <!-- Left Visual Side -->
        <div class="login-visual">
            <div class="brand-logo">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $companyName }} Logo" style="height: 48px; object-fit: contain; border-radius: 12px; background: rgba(255,255,255,0.7); box-shadow: 0 4px 10px rgba(0,0,0,0.05); padding: 6px;">
                @else
                    <div class="brand-logo-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                @endif
                <span>{{ $companyName }}</span>
            </div>

            <div class="visual-content">
                <h2>Intelligent Workspace.</h2>
                <p>Access the core engine to manage events, interactive booking canvases, and live telemetry.</p>
            </div>

            <div class="visual-footer">
                <span class="system-badge">Secure Hub</span>
                <span>© {{ date('Y') }} Data Studio v2.0</span>
            </div>
        </div>

        <!-- Right Form Side -->
        <div class="login-form-container">
            <div class="form-header">
                <h1>Welcome Back</h1>
                <p>Sign in with your Apple ID or local credentials.</p>
            </div>

            @if ($errors->any())
                <div class="global-error">
                    <i class="fas fa-exclamation-circle" style="margin-top: 2px;"></i>
                    <div style="flex: 1;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-input @error('username') is-invalid @enderror" 
                            value="{{ old('username') }}" 
                            placeholder="username"
                            required 
                            autofocus
                        >
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    @error('username')
                        <div class="input-error"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input @error('password') is-invalid @enderror" 
                            placeholder="password"
                            required
                        >
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                    @error('password')
                        <div class="input-error"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <label class="remember-checkbox" for="remember">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span>Sign In</span>
                    <i class="fas fa-arrow-right" style="font-size: 0.9em; margin-left: 4px;"></i>
                </button>
            </form>
        </div>

    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            var btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Authenticating...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
        });
    </script>
</body>
</html>
