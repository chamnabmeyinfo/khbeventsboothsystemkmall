@php
    // Detect active guard
    $user = null;
    $roleName = 'User';
    
    if (auth('admin')->check()) {
        $user = auth('admin')->user();
        $roleName = 'Administrator';
    } elseif (auth()->check()) {
        $user = auth()->user();
        $roleName = $user->isAdmin() ? 'Administrator' : 'Sales Rep';
    }
    
    $companySettings = \App\Models\Setting::getCompanySettings();
    $logoPath = trim(str_replace('\\', '/', $companySettings['company_logo'] ?? ''));
    $logoUrl = $logoPath ? \App\Helpers\AssetHelper::imageUrl(ltrim($logoPath, '/')) : null;
@endphp

<header class="modern-unified-header {{ $headerClass ?? '' }}">
    <div class="header-left">
        @if($useSidebarToggle ?? false)
            <button class="sidebar-toggle-btn" data-widget="pushmenu" role="button" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
        @endif

        <a href="{{ route('dashboard') }}" class="header-brand">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo" class="brand-icon-wrapper" style="object-fit: contain; padding: 4px; background: white;">
            @else
                <div class="brand-icon-wrapper">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            @endif
            <div class="brand-text-modern">
                <span class="brand-name">{{ $companySettings['company_name'] ?? 'KHB Booths' }}</span>
                <span class="brand-sub">Management System</span>
            </div>
        </a>
    </div>

    <div class="header-center">
        <div class="modern-search-wrapper">
            <i class="fas fa-search search-icon-fixed"></i>
            <input type="text" class="modern-search-input" id="globalModernSearch" placeholder="Search booths, clients, bookings..." autocomplete="off">
            <span class="search-shortcut">⌘ K</span>
        </div>
    </div>

    <div class="header-right">
        <!-- Dashboard Link -->
        <a href="{{ route('dashboard') }}" class="header-action-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
            <i class="fas fa-th-large"></i>
        </a>

        <!-- Booths Link -->
        <a href="{{ route('booths.index') }}" class="header-action-btn {{ request()->routeIs('booths.*') ? 'active' : '' }}" title="Booths">
            <i class="fas fa-store"></i>
        </a>

        <!-- Notifications -->
        <div class="header-action-btn notifications">
            <i class="fas fa-bell"></i>
            <span class="notification-badge">3</span>
        </div>

        @auth
            <div class="dropdown">
                <div class="user-profile-pill" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar-modern">
                        {{ strtoupper(substr($user->username ?? $user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="user-info-text">
                        <span class="user-name-header">{{ $user->username ?? $user->name }}</span>
                        <span class="user-role-header">{{ $roleName }}</span>
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" aria-labelledby="userDropdown" style="border-radius: 12px; min-width: 200px; z-index: 1100;">
                    <li class="p-2 border-bottom mb-2">
                        <div class="fw-bold">{{ $user->username ?? $user->name }}</div>
                        <div class="text-muted small">{{ $user->email ?? 'No email set' }}</div>
                    </li>
                    @if(auth('admin')->check())
                        <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Admin Dashboard</a></li>
                        <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.events.index') }}"><i class="fas fa-calendar-alt me-2 text-info"></i>Manage Events</a></li>
                    @endif
                    <li><a class="dropdown-item rounded-2 py-2" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Booth Dashboard</a></li>
                    <li><a class="dropdown-item rounded-2 py-2" href="{{ route('booths.my-booths') }}"><i class="fas fa-id-badge me-2 text-info"></i>My Booths</a></li>
                    @if(auth()->check() && $user->isAdmin())
                        <li><a class="dropdown-item rounded-2 py-2" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2 text-secondary"></i>Settings</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ auth('admin')->check() ? route('admin.logout') : route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <a href="{{ route('login') }}" class="modern-btn modern-btn-primary py-2 px-4 shadow-sm" style="font-size: 0.85rem; border-radius: 30px;">
                Login
            </a>
        @endauth
    </div>
</header>
