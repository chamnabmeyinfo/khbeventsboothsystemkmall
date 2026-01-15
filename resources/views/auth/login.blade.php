@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
    /* Modern Login Page Design - All Devices */
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #ec4899 100%) !important;
        min-height: 100vh !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 20px !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif !important;
    }
    
    /* Login Container */
    .login-container {
        width: 100% !important;
        max-width: 450px !important;
        margin: 0 auto !important;
        position: relative !important;
        z-index: 1 !important;
    }
    
    /* Modern Login Card */
    .modern-login-card {
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-radius: 32px !important;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
        padding: 0 !important;
        overflow: hidden !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        animation: fadeInUp 0.6s ease-out !important;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Login Header */
    .login-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;
        padding: 40px 32px !important;
        text-align: center !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    .login-header::before {
        content: '' !important;
        position: absolute !important;
        top: -50% !important;
        right: -20% !important;
        width: 300px !important;
        height: 300px !important;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%) !important;
        border-radius: 50% !important;
    }
    
    .login-header h1 {
        font-size: 32px !important;
        font-weight: 800 !important;
        color: white !important;
        margin: 0 0 8px 0 !important;
        position: relative !important;
        z-index: 1 !important;
    }
    
    .login-header p {
        font-size: 16px !important;
        color: rgba(255, 255, 255, 0.9) !important;
        margin: 0 !important;
        position: relative !important;
        z-index: 1 !important;
    }
    
    .login-header .login-icon {
        width: 80px !important;
        height: 80px !important;
        background: rgba(255, 255, 255, 0.2) !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 auto 20px !important;
        font-size: 36px !important;
        color: white !important;
        position: relative !important;
        z-index: 1 !important;
        border: 3px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    /* Login Body */
    .login-body {
        padding: 40px 32px !important;
    }
    
    /* Form Styles */
    .modern-form-group {
        margin-bottom: 24px !important;
    }
    
    .modern-form-label {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #374151 !important;
        margin-bottom: 8px !important;
        display: block !important;
    }
    
    .modern-form-input {
        width: 100% !important;
        padding: 14px 18px !important;
        font-size: 16px !important;
        border: 2px solid #e5e7eb !important;
        border-radius: 12px !important;
        background: white !important;
        transition: all 0.3s ease !important;
        box-sizing: border-box !important;
    }
    
    .modern-form-input:focus {
        outline: none !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
        transform: translateY(-2px) !important;
    }
    
    .modern-form-input.is-invalid {
        border-color: #ef4444 !important;
    }
    
    .modern-form-input.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
    }
    
    .invalid-feedback {
        display: block !important;
        margin-top: 6px !important;
        font-size: 13px !important;
        color: #ef4444 !important;
        font-weight: 500 !important;
    }
    
    /* Remember Me */
    .remember-me-group {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 24px !important;
    }
    
    .remember-me-checkbox {
        width: 20px !important;
        height: 20px !important;
        margin-right: 10px !important;
        cursor: pointer !important;
        accent-color: #6366f1 !important;
    }
    
    .remember-me-label {
        font-size: 14px !important;
        color: #6b7280 !important;
        cursor: pointer !important;
        user-select: none !important;
    }
    
    /* Submit Button */
    .modern-login-btn {
        width: 100% !important;
        padding: 16px !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        color: white !important;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        border: none !important;
        border-radius: 12px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
    }
    
    .modern-login-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5) !important;
    }
    
    .modern-login-btn:active {
        transform: translateY(0) !important;
    }
    
    .modern-login-btn:disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
        transform: none !important;
    }
    
    /* Error Alert */
    .login-error-alert {
        background: #fef2f2 !important;
        border: 2px solid #fecaca !important;
        border-radius: 12px !important;
        padding: 16px !important;
        margin-bottom: 24px !important;
        color: #991b1b !important;
        font-size: 14px !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }
    
    /* Mobile Optimizations */
    @media (max-width: 768px) {
        body {
            padding: 16px !important;
        }
        
        .login-container {
            max-width: 100% !important;
        }
        
        .modern-login-card {
            border-radius: 24px !important;
        }
        
        .login-header {
            padding: 32px 24px !important;
        }
        
        .login-header h1 {
            font-size: 28px !important;
        }
        
        .login-header .login-icon {
            width: 64px !important;
            height: 64px !important;
            font-size: 28px !important;
        }
        
        .login-body {
            padding: 32px 24px !important;
        }
        
        .modern-form-input {
            font-size: 16px !important; /* Prevents iOS zoom */
            padding: 14px 16px !important;
        }
        
        .modern-login-btn {
            padding: 14px !important;
            min-height: 50px !important; /* Touch-friendly */
        }
    }
    
    /* Tablet Optimizations */
    @media (min-width: 769px) and (max-width: 1024px) {
        .login-container {
            max-width: 400px !important;
        }
        
        .login-header {
            padding: 36px 28px !important;
        }
        
        .login-body {
            padding: 36px 28px !important;
        }
    }
    
    /* Desktop Optimizations */
    @media (min-width: 1025px) {
        .modern-login-card {
            transition: transform 0.3s ease !important;
        }
        
        .modern-login-card:hover {
            transform: translateY(-4px) !important;
        }
    }
    
    /* Loading State */
    .login-loading {
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Hide navbar on login page */
    nav.navbar {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="login-container">
    <div class="modern-login-card">
        <!-- Login Header -->
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-sign-in-alt"></i>
            </div>
            <h1>Welcome Back</h1>
            <p>Sign in to your account</p>
        </div>
        
        <!-- Login Body -->
        <div class="login-body">
            @if ($errors->any())
                <div class="login-error-alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <!-- Username Field -->
                <div class="modern-form-group">
                    <label for="username" class="modern-form-label">
                        <i class="fas fa-user me-2"></i>Username
                    </label>
                    <input 
                        type="text" 
                        class="modern-form-input @error('username') is-invalid @enderror" 
                        id="username" 
                        name="username" 
                        value="{{ old('username') }}" 
                        required 
                        autofocus
                        placeholder="Enter your username"
                        autocomplete="username"
                    >
                    @error('username')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
                
                <!-- Password Field -->
                <div class="modern-form-group">
                    <label for="password" class="modern-form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <input 
                        type="password" 
                        class="modern-form-input @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Enter your password"
                        autocomplete="current-password"
                    >
                    @error('password')
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>
                
                <!-- Remember Me -->
                <div class="remember-me-group">
                    <input 
                        type="checkbox" 
                        class="remember-me-checkbox" 
                        id="remember" 
                        name="remember"
                    >
                    <label class="remember-me-label" for="remember">
                        Remember me
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="modern-login-btn" id="loginBtn">
                    <span class="btn-text">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </span>
                    <span class="btn-loading" style="display: none;">
                        <span class="login-loading"></span>
                        Signing in...
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const btnText = loginBtn.querySelector('.btn-text');
    const btnLoading = loginBtn.querySelector('.btn-loading');
    
    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        // Show loading state
        loginBtn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'flex';
        loginBtn.style.opacity = '0.8';
    });
    
    // Auto-focus username field on load
    const usernameField = document.getElementById('username');
    if (usernameField && !usernameField.value) {
        setTimeout(function() {
            usernameField.focus();
        }, 100);
    }
    
    // Add enter key support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
            const form = e.target.closest('form');
            if (form && form.id === 'loginForm') {
                form.requestSubmit();
            }
        }
    });
});
</script>
@endpush
@endsection
