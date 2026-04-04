@php
    $user = auth()->user() ?? auth('admin')->user();
    if (!$user) return; // Safeguard if accessed without auth
@endphp

{{-- Mobile: tap outside to close (desktop: hidden via CSS) --}}
<div class="modern-sidebar-backdrop" id="modernSidebarBackdrop" aria-hidden="true"></div>

<aside class="modern-sidebar {{ $sidebarClass ?? '' }}" id="mainSidebar" aria-label="Main navigation">
    <div class="sidebar-footer flex-shrink-0">
        <div class="sidebar-footer-inner">
            <div class="user-avatar-modern-small" aria-hidden="true">
                {{ strtoupper(substr($user->username ?? $user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="user-info-mini overflow-hidden flex-grow-1">
                <div class="user-info-mini-name text-truncate">{{ $user->username ?? $user->name }}</div>
                <div class="user-info-mini-role text-muted text-truncate">{{ $user->isAdmin() ? 'Administrator' : 'Sales Rep' }}</div>
            </div>
            <button type="button"
                    class="sidebar-collapse-btn"
                    id="sidebarCollapseBtn"
                    aria-label="Collapse navigation rail"
                    aria-expanded="true"
                    title="Collapse sidebar">
                <i class="fas fa-chevron-left sidebar-collapse-chevron" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-content">
        <!-- Overview Section -->
        <h6 class="sidebar-section-label">Overview</h6>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->is('dashboard*') ? 'active' : '' }}" title="Dashboard">
                    <i class="fas fa-chart-line" aria-hidden="true"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if($user->hasPermission('booths.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('booths.index') }}" class="sidebar-nav-link {{ request()->is('booths*') && !request()->is('floor-plans*') ? 'active' : '' }}" title="Booth Inventory">
                    <i class="fas fa-cube" aria-hidden="true"></i>
                    <span>Booth Inventory</span>
                </a>
            </li>
            @endif
            @if($user->hasPermission('floor_plans.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('floor-plans.index') }}" class="sidebar-nav-link {{ request()->is('floor-plans*') ? 'active' : '' }}" title="Floor Plans">
                    <i class="fas fa-map" aria-hidden="true"></i>
                    <span>Floor Plans</span>
                </a>
            </li>
            @endif
            @if($user->hasPermission('clients.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('clients.index') }}" class="sidebar-nav-link {{ request()->is('clients*') ? 'active' : '' }}" title="Clients">
                    <i class="fas fa-building" aria-hidden="true"></i>
                    <span>Clients</span>
                </a>
            </li>
            @endif
        </ul>

        <!-- Operations Section -->
        <h6 class="sidebar-section-label">Operations</h6>
        <ul class="sidebar-nav-list">
            @if($user->hasPermission('bookings.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('books.index') }}" class="sidebar-nav-link {{ request()->is('books*') ? 'active' : '' }}" title="Bookings">
                    <i class="fas fa-calendar-check" aria-hidden="true"></i>
                    <span>Bookings</span>
                </a>
            </li>
            @endif
            @if($user->hasPermission('affiliates.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('affiliates.index') }}" class="sidebar-nav-link {{ request()->is('affiliates*') ? 'active' : '' }}" title="Affiliates">
                    <i class="fas fa-handshake" aria-hidden="true"></i>
                    <span>Affiliates</span>
                </a>
            </li>
            @endif
            @if($user->hasPermission('reports.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('reports.index') }}" class="sidebar-nav-link {{ request()->is('reports*') ? 'active' : '' }}" title="Analytics">
                    <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i>
                    <span>Analytics</span>
                </a>
            </li>
            @endif
        </ul>

        <!-- HR Management - Collapsible -->
        @if(method_exists($user, 'hasPermission') && ($user->hasPermission('hr.dashboard.view') || $user->isAdmin()))
        <h6 class="sidebar-section-label">Human Resources</h6>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item sidebar-nav-dropdown">
                <a href="javascript:void(0)" class="sidebar-nav-link {{ request()->routeIs('hr.*') || request()->routeIs('users.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#hrSubmenu" data-toggle="collapse" data-target="#hrSubmenu" aria-expanded="{{ request()->routeIs('hr.*') ? 'true' : 'false' }}" aria-controls="hrSubmenu" role="button" title="HR Management">
                    <i class="fas fa-user-tie" aria-hidden="true"></i>
                    <span>HR Management</span>
                    <i class="fas fa-chevron-right dropdown-toggle-icon" aria-hidden="true"></i>
                </a>
                <ul class="sidebar-sub-nav collapse {{ request()->routeIs('hr.*') ? 'show' : '' }}" id="hrSubmenu" role="list">
                    <li>
                        <a href="{{ route('hr.root') }}" class="sidebar-sub-link {{ request()->routeIs('hr.dashboard', 'hr.root') ? 'active' : '' }}" title="HR Dashboard">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>HR Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hr.employees.index') }}" class="sidebar-sub-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}" title="Employees">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Employees</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hr.attendance.index') }}" class="sidebar-sub-link {{ request()->routeIs('hr.attendance.*') ? 'active' : '' }}" title="Attendance">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Attendance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('hr.leaves.index') }}" class="sidebar-sub-link {{ request()->routeIs('hr.leaves.*') || request()->routeIs('hr.leave-calendar.*') || request()->routeIs('hr.leave-types.*') ? 'active' : '' }}" title="Leave Requests">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Leave Requests</span>
                        </a>
                    </li>
                    @if($user->isAdmin())
                    <li>
                        <a href="{{ route('users.index') }}" class="sidebar-sub-link {{ request()->routeIs('users.*') ? 'active' : '' }}" title="Staff accounts">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Staff</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
        </ul>
        @endif

        <!-- Finance Management - Collapsible -->
        @if(method_exists($user, 'hasPermission') && ($user->hasPermission('finance.view') || $user->isAdmin()))
        <h6 class="sidebar-section-label">Finance & Assets</h6>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item sidebar-nav-dropdown">
                <a href="javascript:void(0)" class="sidebar-nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#financeSubmenu" data-toggle="collapse" data-target="#financeSubmenu" aria-expanded="{{ request()->routeIs('finance.*') ? 'true' : 'false' }}" aria-controls="financeSubmenu" role="button" title="Finance Hub">
                    <i class="fas fa-wallet" aria-hidden="true"></i>
                    <span>Finance Hub</span>
                    <i class="fas fa-chevron-right dropdown-toggle-icon" aria-hidden="true"></i>
                </a>
                <ul class="sidebar-sub-nav collapse {{ request()->routeIs('finance.*') ? 'show' : '' }}" id="financeSubmenu" role="list">
                    <li>
                        <a href="{{ route('finance.payments.index') }}" class="sidebar-sub-link {{ request()->routeIs('finance.payments.*') ? 'active' : '' }}" title="Payments">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Payments</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('finance.expenses.index') }}" class="sidebar-sub-link {{ request()->routeIs('finance.expenses.*') ? 'active' : '' }}" title="Expenses">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Expenses</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('finance.revenues.index') }}" class="sidebar-sub-link {{ request()->routeIs('finance.revenues.*') ? 'active' : '' }}" title="Revenues">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Revenues</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        @endif

        <!-- Administration Section -->
        @if($user->isAdmin())
        @php
            $settingsUrl = route('settings.index');
        @endphp
        <h6 class="sidebar-section-label">System</h6>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item sidebar-nav-dropdown">
                <a href="javascript:void(0)" class="sidebar-nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#settingsSubmenu" data-toggle="collapse" data-target="#settingsSubmenu" aria-expanded="{{ request()->routeIs('settings.index') ? 'true' : 'false' }}" aria-controls="settingsSubmenu" role="button" title="Settings">
                    <i class="fas fa-sliders-h" aria-hidden="true"></i>
                    <span>Settings</span>
                    <i class="fas fa-chevron-right dropdown-toggle-icon" aria-hidden="true"></i>
                </a>
                <ul class="sidebar-sub-nav collapse {{ request()->routeIs('settings.index') ? 'show' : '' }}" id="settingsSubmenu" role="list">
                    <li>
                        <a href="{{ $settingsUrl }}#cache-management" class="sidebar-sub-link js-settings-subnav" data-settings-tab="cache-management" title="Cache">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Cache</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#settings-upload-control" class="sidebar-sub-link js-settings-subnav" data-settings-tab="settings-upload-control" title="Upload">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Upload</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#settings-public-view" class="sidebar-sub-link js-settings-subnav" data-settings-tab="settings-public-view" title="Public view">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Public view</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#push-notifications" class="sidebar-sub-link js-settings-subnav" data-settings-tab="push-notifications" title="Push">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Push</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#system-information" class="sidebar-sub-link js-settings-subnav" data-settings-tab="system-information" title="System">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>System</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#access-roles-settings" class="sidebar-sub-link js-settings-subnav" data-settings-tab="access-roles-settings" title="Roles &amp; features">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Roles &amp; features</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#security-settings" class="sidebar-sub-link js-settings-subnav" data-settings-tab="security-settings" title="Security">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Security</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#company" class="sidebar-sub-link js-settings-subnav" data-settings-tab="company" title="Company">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Company</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#global-color-settings" class="sidebar-sub-link js-settings-subnav" data-settings-tab="global-color-settings" title="Color">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Color</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#appearance" class="sidebar-sub-link js-settings-subnav" data-settings-tab="appearance" title="UI colors">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>UI colors</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#cdn" class="sidebar-sub-link js-settings-subnav" data-settings-tab="cdn" title="CDN">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>CDN</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ $settingsUrl }}#module-display" class="sidebar-sub-link js-settings-subnav" data-settings-tab="module-display" title="Modules">
                            <span class="sidebar-sub-bullet" aria-hidden="true"></span><span>Modules</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('landing-pages.index') }}" class="sidebar-nav-link {{ request()->is('landing-pages*') ? 'active' : '' }}" title="Landing Pages">
                    <i class="fas fa-rocket" aria-hidden="true"></i>
                    <span>Landing Pages</span>
                </a>
            </li>
        </ul>
        @endif
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('mainSidebar');
    const backdrop = document.getElementById('modernSidebarBackdrop');
    const mainContent = document.getElementById('main-content')
        || document.querySelector('.wrapper > .content-wrapper');

    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const MOBILE_MAX = 991;

    function syncSidebarCollapsedBody() {
        if (!sidebar) return;
        document.body.classList.toggle('sidebar-collapsed', sidebar.classList.contains('collapsed'));
        if (collapseBtn) {
            const collapsed = sidebar.classList.contains('collapsed');
            collapseBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            collapseBtn.setAttribute('aria-label', collapsed ? 'Expand navigation rail' : 'Collapse navigation rail');
            collapseBtn.title = collapsed ? 'Expand sidebar' : 'Collapse sidebar';
        }
    }

    function setMobileOpen(open) {
        if (!sidebar) return;
        sidebar.classList.toggle('show', open);
        if (backdrop) {
            backdrop.classList.toggle('is-open', open);
            backdrop.setAttribute('aria-hidden', open ? 'false' : 'true');
        }
        document.body.classList.toggle('modern-sidebar-open-mobile', open);
    }

    function isMobile() {
        return window.innerWidth <= MOBILE_MAX;
    }

    syncSidebarCollapsedBody();

    if (backdrop) {
        backdrop.addEventListener('click', function() {
            setMobileOpen(false);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMobile() && sidebar && sidebar.classList.contains('show')) {
            setMobileOpen(false);
        }
    });

    // Close drawer after navigating (mobile)
    if (sidebar) {
        sidebar.querySelectorAll('.sidebar-sub-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (isMobile()) setMobileOpen(false);
            });
        });
        sidebar.querySelectorAll('.sidebar-nav-item:not(.sidebar-nav-dropdown) > a.sidebar-nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (isMobile()) setMobileOpen(false);
            });
        });
    }

    const toggleBtns = document.querySelectorAll('#sidebarCollapseBtn, .sidebar-toggle-btn');
    toggleBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (isMobile()) {
                if (sidebar) setMobileOpen(!sidebar.classList.contains('show'));
                return;
            }
            if (sidebar) sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');
            syncSidebarCollapsedBody();
        });
    });

    window.addEventListener('resize', function() {
        if (!isMobile()) setMobileOpen(false);
    });

    /** Settings submenu: highlight item from URL hash (defaults to Cache). */
    function syncSettingsSubnavActive() {
        var links = document.querySelectorAll('.js-settings-subnav');
        if (!links.length) {
            return;
        }
        var path = (window.location.pathname || '').replace(/\/+$/, '');
        var parts = path.split('/');
        if (parts[parts.length - 1] !== 'settings') {
            links.forEach(function (a) { a.classList.remove('active'); });
            return;
        }
        var hash = (window.location.hash || '').replace(/^#/, '') || 'cache-management';
        links.forEach(function (a) {
            var tab = a.getAttribute('data-settings-tab') || '';
            a.classList.toggle('active', tab === hash);
        });
    }
    syncSettingsSubnavActive();
    window.addEventListener('hashchange', syncSettingsSubnavActive);
});
</script>
