@extends('layouts.admin')

@section('title', 'User Details')
@section('page-title', 'User Details')
@section('breadcrumb', 'Staff Management / Users / View')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<link rel="stylesheet" href="{{ asset('css/users-page.css') }}?v=1.4">
@endpush

@push('body-class', 'ios-dashboard-mode users-show-page')

@section('content')
<div class="looker-dashboard">
<div class="container-fluid px-0">
    <nav class="users-show-breadcrumb animate-slide-up delay-1" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="users-show-breadcrumb__sep" aria-hidden="true">/</span>
        <a href="{{ route('users.index') }}">Users</a>
        <span class="users-show-breadcrumb__sep" aria-hidden="true">/</span>
        <span class="users-show-breadcrumb__current">{{ $user->username }}</span>
    </nav>

    <!-- Facebook-style profile: cover + toolbar (avatar, name, actions) + section tabs -->
    <div class="profile-header">
        <div class="profile-cover" id="profileCover">
            @php
                $coverPosition = $user->cover_position ?? null;
                if (!$coverPosition && \Illuminate\Support\Facades\Schema::hasColumn('user', 'cover_position') === false) {
                    $coverPosition = \App\Models\Setting::getValue('user_' . $user->id . '_cover_position', null);
                }
                $coverPosition = $coverPosition ?? 'center center';
            @endphp
            @if(($coverUrl = \App\Helpers\AssetHelper::imageUrl($user->cover_image)))
                <img src="{{ $coverUrl }}" alt="Cover Image" id="coverImage" 
                     style="object-position: {{ $coverPosition }};"
                     data-initial-position="{{ $coverPosition }}">
            @endif
            <div class="profile-cover-upload" onclick="openCoverUploadModal()">
                <div class="users-show-cover-upload-inner">
                    <i class="fas fa-camera fa-2x mb-2" aria-hidden="true"></i>
                    <p class="mb-0">Change cover</p>
                </div>
            </div>
            <div class="profile-cover-drag-hint users-show-cover-hint">
                <i class="fas fa-arrows-alt me-1" aria-hidden="true"></i> Drag to reposition
            </div>
        </div>

        <div class="profile-header__bar">
            <div class="profile-header__bar-inner">
                <div class="profile-header__identity">
                    <div class="profile-avatar-wrapper">
                        <div class="profile-avatar" onclick="openAvatarUploadModal()">
                            <x-avatar
                                :avatar="$user->avatar"
                                :name="$user->username"
                                :size="'xl'"
                                :type="$user->isAdmin() ? 'admin' : 'user'"
                                :shape="'circle'"
                            />
                            <div class="profile-avatar-upload" onclick="event.stopPropagation(); openAvatarUploadModal()">
                                <i class="fas fa-camera" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                    <div class="profile-header__text">
                        <h1 class="profile-header__name">{{ $user->username }}</h1>
                        <div class="profile-header__badges">
                            @if($user->isAdmin())
                                <span class="status-badge status-badge-purple">
                                    <i class="fas fa-shield-alt me-1" aria-hidden="true"></i>Administrator
                                </span>
                            @else
                                <span class="status-badge status-badge-neutral">
                                    <i class="fas fa-user-tie me-1" aria-hidden="true"></i>Sale staff
                                </span>
                            @endif
                            @if($user->role)
                                <span class="status-badge status-badge-blue">
                                    <i class="fas fa-shield me-1" aria-hidden="true"></i>{{ $user->role->name }}
                                </span>
                            @endif
                            @if($user->isActive())
                                <span class="status-badge status-badge-green">
                                    <i class="fas fa-check-circle me-1" aria-hidden="true"></i>Active
                                </span>
                            @else
                                <span class="status-badge status-badge-orange">
                                    <i class="fas fa-times-circle me-1" aria-hidden="true"></i>Inactive
                                </span>
                            @endif
                        </div>
                        <p class="profile-header__meta">
                            <span class="status-badge status-badge-blue">#{{ $user->id }}</span>
                            @if($user->last_login)
                                <span class="profile-header__meta-extra"><i class="fas fa-clock me-1" aria-hidden="true"></i>Last login: {{ $user->last_login }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="profile-header__actions">
                    <a href="{{ route('users.index') }}" class="action-btn action-btn-secondary">
                        <i class="fas fa-arrow-left text-tertiary" aria-hidden="true"></i> Back
                    </a>
                    <a href="{{ route('users.edit', $user) }}" class="action-btn action-btn-primary">
                        <i class="fas fa-edit" aria-hidden="true"></i> Edit profile
                    </a>
                    @if(auth()->user()->isAdmin() && $user->id != auth()->id())
                    <button type="button" class="action-btn action-btn-secondary text-danger border-danger-subtle" onclick="deleteUser({{ $user->id }}, {{ json_encode($user->username) }})">
                        <i class="fas fa-trash" aria-hidden="true"></i> Delete
                    </button>
                    @endif
                </div>
            </div>
            <nav class="profile-header__subnav" aria-label="Profile sections">
                <ul class="profile-header__tabs list-unstyled mb-0">
                    <li>
                        <span class="profile-header__tab profile-header__tab--active" aria-current="page">Profile</span>
                    </li>
                    <li>
                        <a class="profile-header__tab" href="#section-stats">Overview</a>
                    </li>
                    <li>
                        <a class="profile-header__tab" href="#section-account">Account</a>
                    </li>
                    @if($user->role)
                    <li>
                        <a class="profile-header__tab" href="#section-permissions">Permissions</a>
                    </li>
                    @endif
                    <li>
                        <a class="profile-header__tab" href="#section-password">Security</a>
                    </li>
                    @if(isset($affiliateStats) && $affiliateStats['total_bookings'] > 0)
                    <li>
                        <a class="profile-header__tab" href="#section-affiliate">Affiliate</a>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>

    @php
        try {
            $boothCount = $user->booths()->count();
        } catch (\Exception $e) {
            $boothCount = 0;
        }
        try {
            $bookingCount = $user->books()->count();
        } catch (\Exception $e) {
            $bookingCount = 0;
        }
        try {
            $permissionsCount = $user->getPermissions()->count();
        } catch (\Exception $e) {
            $permissionsCount = 0;
        }
    @endphp
    <div class="kpi-wrapper mb-4" id="section-stats">
        <div class="kpi-card-looker">
            <div class="kpi-top">
                <div class="kpi-title">Booths</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-cube" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($boothCount) }}</div>
            <div class="kpi-bottom trend-neutral"><i class="fas fa-fw fa-circle" style="font-size: 6px;" aria-hidden="true"></i> Linked booths</div>
        </div>
        <div class="kpi-card-looker success">
            <div class="kpi-top">
                <div class="kpi-title">Bookings</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-calendar-check" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($bookingCount) }}</div>
            <div class="kpi-bottom trend-positive"><i class="fas fa-fw fa-link" aria-hidden="true"></i> Total bookings</div>
        </div>
        <div class="kpi-card-looker purple">
            <div class="kpi-top">
                <div class="kpi-title">Permissions</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-key" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($permissionsCount) }}</div>
            <div class="kpi-bottom trend-neutral"><i class="fas fa-fw fa-shield-alt" aria-hidden="true"></i> Effective access</div>
        </div>
        @if(isset($affiliateStats) && $affiliateStats['total_bookings'] > 0)
        <div class="kpi-card-looker warning">
            <div class="kpi-top">
                <div class="kpi-title">Affiliate revenue</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-dollar-sign" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">${{ number_format($affiliateStats['total_revenue'], 0) }}</div>
            <div class="kpi-bottom trend-warning"><i class="fas fa-fw fa-chart-line" aria-hidden="true"></i> From affiliate</div>
        </div>
        @endif
    </div>

    @if(isset($affiliateStats) && $affiliateStats['total_bookings'] > 0)
    <div class="canvas-panel users-show-panel users-show-panel--affiliate mb-4" id="section-affiliate">
        <div class="panel-header flex-wrap gap-2">
            <h2 class="panel-title mb-0"><i class="fas fa-chart-line" aria-hidden="true"></i> Affiliate &amp; commission</h2>
            <a href="{{ route('affiliates.show', $user->id) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-external-link-alt text-tertiary" aria-hidden="true"></i> Full report
            </a>
        </div>
        <div class="users-show-panel__body px-2 px-md-0 pb-2">
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="users-show-metric-tile">
                        <i class="fas fa-shopping-cart users-show-metric-tile__icon" style="color: var(--accent-blue);" aria-hidden="true"></i>
                        <div class="users-show-metric-tile__value">{{ number_format($affiliateStats['total_bookings']) }}</div>
                        <div class="users-show-metric-tile__label">Affiliate bookings</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="users-show-metric-tile">
                        <i class="fas fa-dollar-sign users-show-metric-tile__icon" style="color: var(--accent-green);" aria-hidden="true"></i>
                        <div class="users-show-metric-tile__value">${{ number_format($affiliateStats['total_revenue'], 2) }}</div>
                        <div class="users-show-metric-tile__label">Revenue generated</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="users-show-metric-tile">
                        <i class="fas fa-users users-show-metric-tile__icon" style="color: var(--accent-purple);" aria-hidden="true"></i>
                        <div class="users-show-metric-tile__value">{{ number_format($affiliateStats['unique_clients']) }}</div>
                        <div class="users-show-metric-tile__label">Unique clients</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="users-show-metric-tile">
                        <i class="fas fa-mouse-pointer users-show-metric-tile__icon" style="color: var(--accent-orange);" aria-hidden="true"></i>
                        <div class="users-show-metric-tile__value">{{ number_format($affiliateStats['total_clicks']) }}</div>
                        <div class="users-show-metric-tile__label">Link clicks</div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="users-show-highlight users-show-highlight--blue">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <small class="text-muted d-block mb-1">Avg. booking value</small>
                                <span class="fs-5 fw-bold" style="color: var(--accent-blue);">${{ number_format($affiliateStats['avg_booking_value'], 2) }}</span>
                            </div>
                            <i class="fas fa-chart-bar fa-2x text-secondary opacity-50" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="users-show-highlight users-show-highlight--green">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <small class="text-muted d-block mb-1">Conversion rate</small>
                                <span class="fs-5 fw-bold" style="color: var(--accent-green);">{{ number_format($affiliateStats['conversion_rate'], 1) }}%</span>
                            </div>
                            <i class="fas fa-percentage fa-2x text-secondary opacity-50" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="users-show-highlight users-show-highlight--purple">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <small class="text-muted d-block mb-1">Floor plans worked</small>
                                <span class="fs-5 fw-bold" style="color: var(--accent-purple);">{{ number_format($affiliateStats['unique_floor_plans']) }}</span>
                            </div>
                            <i class="fas fa-map fa-2x text-secondary opacity-50" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="users-show-timeline-box">
                        <small class="text-muted d-block mb-2"><i class="fas fa-calendar-check me-1" aria-hidden="true"></i>First booking</small>
                        <strong>
                            @if($affiliateStats['first_booking_at'])
                                {{ \Carbon\Carbon::parse($affiliateStats['first_booking_at'])->format('M d, Y') }}
                            @else
                                <span class="text-muted">No bookings yet</span>
                            @endif
                        </strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="users-show-timeline-box">
                        <small class="text-muted d-block mb-2"><i class="fas fa-clock me-1" aria-hidden="true"></i>Last booking</small>
                        <strong>
                            @if($affiliateStats['last_booking_at'])
                                {{ \Carbon\Carbon::parse($affiliateStats['last_booking_at'])->format('M d, Y') }}
                            @else
                                <span class="text-muted">No bookings yet</span>
                            @endif
                        </strong>
                    </div>
                </div>
            </div>
            @if($affiliateStats['recent_bookings']->count() > 0)
            <div class="mt-2">
                <h3 class="h6 fw-bold text-secondary mb-3"><i class="fas fa-history me-2" aria-hidden="true"></i>Recent affiliate bookings</h3>
                <div class="looker-table-wrapper overflow-x-auto">
                    <table class="looker-table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Floor plan</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($affiliateStats['recent_bookings'] as $booking)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($booking->date_book)->format('M d, Y') }}</td>
                                <td>
                                    @if($booking->client)
                                        {{ $booking->client->company ?? $booking->client->name }}
                                    @else
                                        Client #{{ $booking->clientid }}
                                    @endif
                                </td>
                                <td>
                                    @if($booking->floorPlan)
                                        {{ $booking->floorPlan->name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-end fw-semibold" style="color: var(--accent-green);">
                                    ${{ number_format($booking->booths()->sum('price') ?? 0, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="row g-4" id="section-account">
        <div class="{{ $user->role ? 'col-lg-6' : 'col-12' }}">
            <div class="canvas-panel users-show-panel h-100">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="fas fa-id-card" aria-hidden="true"></i> Account details</h2>
                </div>
                <div class="users-show-panel__body">
                    <div class="users-show-info-row">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="text-muted"><i class="fas fa-hashtag me-2" aria-hidden="true"></i>User ID</span>
                            <span class="status-badge status-badge-blue">#{{ $user->id }}</span>
                        </div>
                    </div>
                    <div class="users-show-info-row">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="text-muted"><i class="fas fa-user me-2" aria-hidden="true"></i>Username</span>
                            <strong>{{ $user->username }}</strong>
                        </div>
                    </div>
                    <div class="users-show-info-row">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="text-muted"><i class="fas fa-user-tag me-2" aria-hidden="true"></i>Type</span>
                            @if($user->isAdmin())
                                <span class="status-badge status-badge-purple"><i class="fas fa-shield-alt me-1" aria-hidden="true"></i>Administrator</span>
                            @else
                                <span class="status-badge status-badge-neutral"><i class="fas fa-user-tie me-1" aria-hidden="true"></i>Sale staff</span>
                            @endif
                        </div>
                    </div>
                    <div class="users-show-info-row">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="text-muted"><i class="fas fa-user-shield me-2" aria-hidden="true"></i>Role</span>
                            @if($user->role)
                                <span class="status-badge status-badge-blue"><i class="fas fa-shield me-1" aria-hidden="true"></i>{{ $user->role->name }}</span>
                            @else
                                <span class="status-badge status-badge-neutral">No role</span>
                            @endif
                        </div>
                    </div>
                    <div class="users-show-info-row">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="text-muted"><i class="fas fa-toggle-on me-2" aria-hidden="true"></i>Status</span>
                            @if($user->isActive())
                                <span class="status-badge status-badge-green"><i class="fas fa-check-circle me-1" aria-hidden="true"></i>Active</span>
                            @else
                                <span class="status-badge status-badge-orange"><i class="fas fa-times-circle me-1" aria-hidden="true"></i>Inactive</span>
                            @endif
                        </div>
                    </div>
                    @if($user->last_login)
                    <div class="users-show-info-row">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="text-muted"><i class="fas fa-clock me-2" aria-hidden="true"></i>Last login</span>
                            <span>{{ $user->last_login }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @if($user->role)
        <div class="col-lg-6" id="section-permissions">
            <div class="canvas-panel users-show-panel users-show-panel--permissions h-100">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="fas fa-key" aria-hidden="true"></i> Role permissions</h2>
                </div>
                <div class="users-show-panel__body" style="max-height: 420px; overflow-y: auto;">
                    @php
                        $permissions = $user->getPermissions()->groupBy('module');
                    @endphp
                    @if($permissions->count() > 0)
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="users-show-permission-module">
                            <h6>
                                <i class="fas fa-folder me-1" aria-hidden="true"></i>{{ ucfirst($module ?: 'General') }}
                                <span class="status-badge status-badge-neutral ms-1">{{ $modulePermissions->count() }}</span>
                            </h6>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($modulePermissions as $permission)
                                <span class="status-badge status-badge-blue text-start text-wrap" style="text-transform: none; letter-spacing: normal; font-weight: 600; white-space: normal;">{{ $permission->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">No permissions assigned.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="canvas-panel users-show-panel mt-4" id="section-password">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-lock" aria-hidden="true"></i> Change password</h2>
        </div>
        <div class="users-show-panel__body">
            <form action="{{ route('users.password.update', $user->id) }}" method="POST" id="passwordForm">
                @csrf
                @method('POST')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold text-secondary"><i class="fas fa-lock me-1" aria-hidden="true"></i>New password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-lock text-muted" aria-hidden="true"></i></span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" placeholder="Enter new password" required autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')" aria-label="Show password">
                                <i class="fas fa-eye" id="passwordToggleIcon" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold text-secondary"><i class="fas fa-lock me-1" aria-hidden="true"></i>Confirm password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-lock text-muted" aria-hidden="true"></i></span>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation"
                                   placeholder="Confirm new password" required autocomplete="new-password">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')" aria-label="Show password">
                                <i class="fas fa-eye" id="password_confirmationToggleIcon" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" class="mt-2"></div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-key" aria-hidden="true"></i> Update password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Avatar Upload Modal -->
    <div class="modal fade" id="avatarUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content users-modal-content">
                <div class="modal-header users-modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="fas fa-camera" aria-hidden="true"></i>Upload avatar
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-image-upload 
                        type="avatar"
                        entity-type="user"
                        entity-id="{{ $user->id }}"
                        current-image="{{ $user->avatar }}"
                        name="{{ $user->username }}"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Cover Upload Modal -->
    <div class="modal fade" id="coverUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content users-modal-content">
                <div class="modal-header users-modal-header">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="fas fa-image" aria-hidden="true"></i>Upload cover image
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-image-upload 
                        type="cover"
                        entity-type="user"
                        entity-id="{{ $user->id }}"
                        current-image="{{ $user->cover_image }}"
                        name="{{ $user->username }}"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
function openAvatarUploadModal() {
    const el = document.getElementById('avatarUploadModal');
    if (window.bootstrap && bootstrap.Modal && el) {
        bootstrap.Modal.getOrCreateInstance(el).show();
    } else if (typeof $ !== 'undefined') {
        $('#avatarUploadModal').modal('show');
    }
}

function openCoverUploadModal() {
    const el = document.getElementById('coverUploadModal');
    if (window.bootstrap && bootstrap.Modal && el) {
        bootstrap.Modal.getOrCreateInstance(el).show();
    } else if (typeof $ !== 'undefined') {
        $('#coverUploadModal').modal('show');
    }
}

// Cover Image Drag to Reposition
(function() {
    const coverContainer = document.getElementById('profileCover');
    const coverImage = document.getElementById('coverImage');
    const dragHint = coverContainer.querySelector('.profile-cover-drag-hint');
    
    if (!coverImage || !coverContainer) return;
    
    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let currentX = 0;
    let currentY = 0;
    let imageElement = coverImage;
    
    // Get current object-position or default to center
    function getCurrentPosition() {
        const style = window.getComputedStyle(imageElement);
        let objectPosition = style.objectPosition || imageElement.getAttribute('data-initial-position') || 'center center';
        
        // Parse position (can be "center center", "50% 50%", "50% center", etc.)
        const parts = objectPosition.trim().split(/\s+/);
        let x = 50;
        let y = 50;
        
        if (parts.length >= 1) {
            if (parts[0] === 'center') {
                x = 50;
            } else {
                const xVal = parseFloat(parts[0]);
                if (!isNaN(xVal)) {
                    x = xVal;
                }
            }
        }
        
        if (parts.length >= 2) {
            if (parts[1] === 'center') {
                y = 50;
            } else {
                const yVal = parseFloat(parts[1]);
                if (!isNaN(yVal)) {
                    y = yVal;
                }
            }
        }
        
        return { x: x, y: y };
    }
    
    // Set object-position
    function setPosition(x, y) {
        imageElement.style.objectPosition = `${x}% ${y}%`;
    }
    
    // Show drag hint on hover
    coverContainer.addEventListener('mouseenter', function() {
        if (coverImage && coverImage.src) {
            dragHint.style.display = 'block';
        }
    });
    
    coverContainer.addEventListener('mouseleave', function() {
        if (!isDragging) {
            dragHint.style.display = 'none';
        }
    });
    
    // Mouse events
    coverContainer.addEventListener('mousedown', function(e) {
        if (!coverImage || !coverImage.src) return;
        if (e.target.closest('.profile-cover-upload')) return; // Don't drag when clicking upload button
        
        isDragging = true;
        coverContainer.classList.add('dragging');
        dragHint.style.display = 'none';
        
        const rect = coverContainer.getBoundingClientRect();
        startX = e.clientX - rect.left;
        startY = e.clientY - rect.top;
        
        const pos = getCurrentPosition();
        currentX = pos.x;
        currentY = pos.y;
        
        e.preventDefault();
    });
    
    document.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        
        const rect = coverContainer.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;
        
        // Calculate percentage offset
        const deltaX = ((mouseX - startX) / rect.width) * 100;
        const deltaY = ((mouseY - startY) / rect.height) * 100;
        
        // Update position (clamp between 0-100%)
        const newX = Math.max(0, Math.min(100, currentX + deltaX));
        const newY = Math.max(0, Math.min(100, currentY + deltaY));
        
        setPosition(newX, newY);
    });
    
    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            coverContainer.classList.remove('dragging');
            
            // Save position to database
            const pos = getCurrentPosition();
            saveCoverPosition(pos.x, pos.y);
        }
    });
    
    // Touch events for mobile
    coverContainer.addEventListener('touchstart', function(e) {
        if (!coverImage || !coverImage.src) return;
        if (e.target.closest('.profile-cover-upload')) return;
        
        isDragging = true;
        coverContainer.classList.add('dragging');
        dragHint.style.display = 'none';
        
        const rect = coverContainer.getBoundingClientRect();
        const touch = e.touches[0];
        startX = touch.clientX - rect.left;
        startY = touch.clientY - rect.top;
        
        const pos = getCurrentPosition();
        currentX = pos.x;
        currentY = pos.y;
        
        e.preventDefault();
    });
    
    document.addEventListener('touchmove', function(e) {
        if (!isDragging) return;
        
        const rect = coverContainer.getBoundingClientRect();
        const touch = e.touches[0];
        const mouseX = touch.clientX - rect.left;
        const mouseY = touch.clientY - rect.top;
        
        const deltaX = ((mouseX - startX) / rect.width) * 100;
        const deltaY = ((mouseY - startY) / rect.height) * 100;
        
        const newX = Math.max(0, Math.min(100, currentX + deltaX));
        const newY = Math.max(0, Math.min(100, currentY + deltaY));
        
        setPosition(newX, newY);
        e.preventDefault();
    });
    
    document.addEventListener('touchend', function() {
        if (isDragging) {
            isDragging = false;
            coverContainer.classList.remove('dragging');
            
            const pos = getCurrentPosition();
            saveCoverPosition(pos.x, pos.y);
        }
    });
    
    // Save cover position to database
    function saveCoverPosition(x, y) {
        const userId = {{ $user->id }};
        const position = `${x}% ${y}%`;
        
        fetch(`/users/${userId}/cover-position`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                position: position,
                x: x,
                y: y
            })
        }).catch(error => {
            console.error('Failed to save cover position:', error);
        });
    }
})();

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + 'ToggleIcon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password match indicator
$('#password_confirmation').on('input', function() {
    const password = $('#password').val();
    const confirmation = $(this).val();
    const matchDiv = $('#passwordMatch');
    
    if (confirmation.length === 0) {
        matchDiv.html('');
        return;
    }
    
    if (password === confirmation) {
        matchDiv.html('<small class="text-success"><i class="fas fa-check-circle mr-1"></i>Passwords match</small>');
    } else {
        matchDiv.html('<small class="text-danger"><i class="fas fa-times-circle mr-1"></i>Passwords do not match</small>');
    }
});

// Form validation
$('#passwordForm').on('submit', function(e) {
    const password = $('#password').val();
    const confirmation = $('#password_confirmation').val();
    
    if (password !== confirmation) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Mismatch',
            text: 'Password and confirmation password do not match.',
            confirmButtonColor: '#007AFF'
        });
        return false;
    }
    
    showLoading();
});

function deleteUser(id, username) {
    Swal.fire({
        title: 'Delete User?',
        text: `Are you sure you want to delete user "${username}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch(`/users/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE'
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json();
                }
            })
            .then(data => {
                hideLoading();
                if (data && data.success) {
                    Swal.fire('Deleted!', data.message || 'User has been deleted.', 'success')
                        .then(() => {
                            window.location.href = '{{ route("users.index") }}';
                        });
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error!', 'An error occurred while deleting the user.', 'error');
                console.error('Error:', error);
            });
        }
    });
}
</script>
@endpush

