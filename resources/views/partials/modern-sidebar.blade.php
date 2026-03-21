@php
    $user = auth()->user() ?? auth('admin')->user();
    if (!$user) return; // Safeguard if accessed without auth
@endphp

<aside class="modern-sidebar {{ $sidebarClass ?? '' }}" id="mainSidebar">
    <div class="sidebar-content">
        <!-- Overview Section -->
        <h6 class="sidebar-section-label">Overview</h6>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-link {{ request()->is('dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if($user->hasPermission('booths.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('booths.index') }}" class="sidebar-nav-link {{ request()->is('booths*') && !request()->is('floor-plans*') ? 'active' : '' }}">
                    <i class="fas fa-cube"></i>
                    <span>Booth Inventory</span>
                </a>
            </li>
            @endif
            @if($user->hasPermission('floor_plans.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('floor-plans.index') }}" class="sidebar-nav-link {{ request()->is('floor-plans*') ? 'active' : '' }}">
                    <i class="fas fa-map"></i>
                    <span>Floor Plans</span>
                </a>
            </li>
            @endif
            @if($user->hasPermission('clients.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('clients.index') }}" class="sidebar-nav-link {{ request()->is('clients*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
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
                <a href="{{ route('books.index') }}" class="sidebar-nav-link {{ request()->is('books*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>
            @endif
            @if($user->hasPermission('affiliates.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('affiliates.index') }}" class="sidebar-nav-link {{ request()->is('affiliates*') ? 'active' : '' }}">
                    <i class="fas fa-handshake"></i>
                    <span>Affiliates</span>
                </a>
            </li>
            @endif
            @if($user->hasPermission('reports.view') || $user->isAdmin())
            <li class="sidebar-nav-item">
                <a href="{{ route('reports.index') }}" class="sidebar-nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
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
                <a href="javascript:void(0)" class="sidebar-nav-link {{ request()->is('hr*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#hrSubmenu" aria-expanded="{{ request()->is('hr*') ? 'true' : 'false' }}">
                    <i class="fas fa-user-tie"></i>
                    <span>HR Management</span>
                    <i class="fas fa-chevron-right dropdown-toggle-icon"></i>
                </a>
                <ul class="sidebar-sub-nav collapse {{ request()->is('hr*') ? 'show' : '' }}" id="hrSubmenu">
                    <li><a href="{{ route('hr.dashboard') }}" class="sidebar-sub-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">HR Dashboard</a></li>
                    <li><a href="{{ route('hr.employees.index') }}" class="sidebar-sub-link {{ request()->is('hr/employees*') ? 'active' : '' }}">Employees</a></li>
                    <li><a href="{{ route('hr.attendance.index') }}" class="sidebar-sub-link {{ request()->is('hr/attendance*') ? 'active' : '' }}">Attendance</a></li>
                    <li><a href="{{ route('hr.leaves.index') }}" class="sidebar-sub-link {{ request()->is('hr/leaves*') ? 'active' : '' }}">Leave Requests</a></li>
                </ul>
            </li>
        </ul>
        @endif

        <!-- Finance Management - Collapsible -->
        @if(method_exists($user, 'hasPermission') && ($user->hasPermission('finance.view') || $user->isAdmin()))
        <h6 class="sidebar-section-label">Finance & Assets</h6>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item sidebar-nav-dropdown">
                <a href="javascript:void(0)" class="sidebar-nav-link {{ request()->is('finance*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#financeSubmenu" aria-expanded="{{ request()->is('finance*') ? 'true' : 'false' }}">
                    <i class="fas fa-wallet"></i>
                    <span>Finance Hub</span>
                    <i class="fas fa-chevron-right dropdown-toggle-icon"></i>
                </a>
                <ul class="sidebar-sub-nav collapse {{ request()->is('finance*') ? 'show' : '' }}" id="financeSubmenu">
                    <li><a href="{{ route('finance.payments.index') }}" class="sidebar-sub-link {{ request()->is('finance/payments*') ? 'active' : '' }}">Payments</a></li>
                    <li><a href="{{ route('finance.expenses.index') }}" class="sidebar-sub-link {{ request()->is('finance/expenses*') ? 'active' : '' }}">Expenses</a></li>
                    <li><a href="{{ route('finance.revenues.index') }}" class="sidebar-sub-link {{ request()->is('finance/revenues*') ? 'active' : '' }}">Revenues</a></li>
                </ul>
            </li>
        </ul>
        @endif

        <!-- Administration Section -->
        @if($user->isAdmin())
        <h6 class="sidebar-section-label">System</h6>
        <ul class="sidebar-nav-list">
            <li class="sidebar-nav-item">
                <a href="{{ route('users.index') }}" class="sidebar-nav-link {{ request()->is('users*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i>
                    <span>Security & Staff</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="{{ route('settings.index') }}" class="sidebar-nav-link {{ request()->is('settings*') ? 'active' : '' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Global Settings</span>
                </a>
            </li>
        </ul>
        @endif
    </div>
    
    <!-- Sidebar Footer -->
    <div class="sidebar-footer p-3 border-top mt-auto" style="border-color: rgba(0, 0, 0, 0.05) !important;">
        <div class="d-flex align-items-center gap-2">
            <div class="user-avatar-modern-small" style="width: 32px; height: 32px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem;">
                {{ strtoupper(substr($user->username ?? $user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="user-info-mini overflow-hidden flex-grow-1">
                <div class="text-dark small fw-bold text-truncate" style="font-size: 0.85rem;">{{ $user->username ?? $user->name }}</div>
                <div class="text-muted x-small text-truncate" style="font-size: 0.7rem;">{{ $user->isAdmin() ? 'Administrator' : 'Sales Rep' }}</div>
            </div>
            <a href="javascript:void(0)" class="text-muted hover-dark" id="sidebarCollapseBtn">
                <i class="fas fa-indent"></i>
            </a>
        </div>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('mainSidebar');
    const mainContent = document.getElementById('main-content');

    function syncSidebarCollapsedBody() {
        if (!sidebar) return;
        document.body.classList.toggle('sidebar-collapsed', sidebar.classList.contains('collapsed'));
    }

    syncSidebarCollapsedBody();

    // Dropdown arrow rotation
    const dropdowns = document.querySelectorAll('.sidebar-nav-dropdown [data-bs-toggle="collapse"]');
    dropdowns.forEach(btn => {
        btn.addEventListener('click', function() {
            const icon = this.querySelector('.dropdown-toggle-icon');
            if (icon) {
                // Rotation is handled by CSS based on aria-expanded,
                // but bootstrap 5 handles aria-expanded automatically.
            }
        });
    });

    // Sidebar toggling (desktop: collapse width; mobile: off-canvas drawer)
    const toggleBtns = document.querySelectorAll('#sidebarCollapseBtn, .sidebar-toggle-btn');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (window.innerWidth <= 991) {
                if (sidebar) sidebar.classList.toggle('show');
                return;
            }
            if (sidebar) sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');
            syncSidebarCollapsedBody();
        });
    });
});
</script>
