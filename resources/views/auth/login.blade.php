<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KHB Events Booking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brand-primary: #2563eb;
            --brand-secondary: #3b82f6;
            --brand-accent: #10b981;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-surface: #ffffff;
            --bg-body: #f1f5f9;
            --border-color: #e2e8f0;
            --focus-ring: rgba(37, 99, 235, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: radial-gradient(circle at 0% 0%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
                              radial-gradient(circle at 100% 100%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
            -webkit-font-smoothing: antialiased;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1100px;
            min-height: 600px;
            background: var(--bg-surface);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0,0,0,0.02);
            overflow: hidden;
            display: flex;
            margin: 2rem;
            position: relative;
        }

        /* Left Side: Branding / Visual */
        .login-visual {
            flex: 1;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            color: white;
            overflow: hidden;
        }

        .login-visual::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 70%;
            height: 70%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            border-radius: 50%;
        }

        .login-visual::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
            border-radius: 50%;
        }

        .brand-logo {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
        }

        .brand-logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .visual-content {
            position: relative;
            z-index: 10;
        }

        .visual-content h2 {
            font-size: 2.75rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -0.03em;
        }

        .visual-content p {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            max-width: 400px;
        }

        .visual-footer {
            position: relative;
            z-index: 10;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .system-badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            backdrop-filter: blur(4px);
        }

        /* Right Side: Form */
        .login-form-container {
            flex: 0 0 480px;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--bg-surface);
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            font-size: 1rem;
            color: var(--text-main);
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:hover {
            border-color: #cbd5e1;
        }

        .form-input:focus {
            background: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px var(--focus-ring);
        }

        .form-input:focus + .input-icon {
            color: var(--brand-primary);
        }

        .form-input.is-invalid {
            border-color: #ef4444;
            background: #fef2f2;
        }
        
        .form-input.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .input-error {
            color: #ef4444;
            font-size: 0.8rem;
            font-weight: 500;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .form-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .remember-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            user-select: none;
        }

        .remember-checkbox input {
            width: 1.2rem;
            height: 1.2rem;
            border-radius: 4px;
            accent-color: var(--brand-primary);
            cursor: pointer;
        }

        .remember-checkbox span {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: var(--brand-primary);
            color: white;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 4px 6px -1px var(--focus-ring);
        }

        .btn-submit:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px var(--focus-ring);
        }

        .btn-submit:active {
            transform: translateY(1px);
        }

        .global-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 1rem;
            border-radius: 12px;
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
            }
            .login-visual {
                padding: 3rem 2rem;
            }
            .visual-content h2 {
                font-size: 2rem;
            }
            .login-form-container {
                flex: auto;
                padding: 3rem 2rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        
        <!-- Left Visual Side -->
        <div class="login-visual">
            <div class="brand-logo">
                <div class="brand-logo-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <span>KHB Events</span>
            </div>

            <div class="visual-content">
                <h2>Intelligent Floorplan Management.</h2>
                <p>Access the core engine to manage events, interactive booking canvases, and live data telemetry all in one unified workspace.</p>
            </div>

            <div class="visual-footer">
                <span class="system-badge">Secure Analytics Hub</span>
                <span>© {{ date('Y') }} Data Studio v2.0</span>
            </div>
        </div>

        <!-- Right Form Side -->
        <div class="login-form-container">
            <div class="form-header">
                <h1>Welcome Back</h1>
                <p>Please enter your credentials to continue.</p>
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
                            placeholder="e.g. admin"
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
                            placeholder="Enter your password"
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
                        <span>Remember securely</span>
                    </label>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn">
                    <span>Access Dashboard</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            var btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Authenticating...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
        });
    </script>
</body>
</html>
