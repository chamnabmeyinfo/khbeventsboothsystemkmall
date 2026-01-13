@extends('layouts.adminlte')

@section('title', 'User Details')
@section('page-title', 'User Details')
@section('breadcrumb', 'Staff Management / Users / View')

@push('styles')
<style>
    /* Profile Header with Cover and Avatar */
    .profile-header {
        position: relative;
        margin-bottom: 32px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
    }

    .profile-cover {
        width: 100%;
        height: 300px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .profile-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-cover-upload {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .profile-cover:hover .profile-cover-upload {
        display: flex;
    }

    .profile-avatar-wrapper {
        position: absolute;
        bottom: -64px;
        left: 32px;
        z-index: 10;
    }

    .profile-avatar {
        width: 128px;
        height: 128px;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        position: relative;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
    }

    .profile-avatar-upload {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: 3px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        transition: transform 0.2s;
    }

    .profile-avatar-upload:hover {
        transform: scale(1.1);
    }

    .profile-avatar-upload i {
        color: white;
        font-size: 16px;
    }

    .profile-info {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 80px 32px 32px 32px;
        border-top: 1px solid rgba(255, 255, 255, 0.18);
    }

    .profile-actions {
        position: absolute;
        top: 24px;
        right: 24px;
        z-index: 20;
    }

    .detail-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        border-left: 4px solid;
        transition: transform 0.2s;
        margin-bottom: 24px;
    }

    .detail-card:hover {
        transform: translateX(4px);
    }

    .detail-card.primary { border-left-color: #667eea; }
    .detail-card.success { border-left-color: #84fab0; }
    .detail-card.warning { border-left-color: #fa709a; }

    .info-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .stat-card {
        text-align: center;
        padding: 1.5rem;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: transform 0.3s;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">{{ $user->username }}</li>
        </ol>
    </nav>

    <!-- Profile Header with Cover and Avatar -->
    <div class="profile-header">
        <!-- Cover Image -->
        <div class="profile-cover" id="profileCover">
            @if($user->cover_image)
                <img src="{{ asset($user->cover_image) }}" alt="Cover Image" id="coverImage">
            @endif
            <div class="profile-cover-upload" onclick="openCoverUploadModal()">
                <div style="text-align: center; color: white;">
                    <i class="fas fa-camera fa-2x mb-2"></i>
                    <p class="mb-0">Change Cover</p>
                </div>
            </div>
        </div>

        <!-- Avatar -->
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
                    <i class="fas fa-camera"></i>
                </div>
            </div>
        </div>

        <!-- Profile Info Bar -->
        <div class="profile-info">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 style="font-weight: 700; color: #2d3748; margin-bottom: 8px;">
                        {{ $user->username }}
                    </h2>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if($user->isAdmin())
                            <span class="badge badge-danger" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="fas fa-shield-alt mr-1"></i>Administrator
                            </span>
                        @else
                            <span class="badge badge-secondary" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="fas fa-user-tie mr-1"></i>Sale Staff
                            </span>
                        @endif
                        @if($user->role)
                            <span class="badge badge-info" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="fas fa-shield mr-1"></i>{{ $user->role->name }}
                            </span>
                        @endif
                        @if($user->isActive())
                            <span class="badge badge-success" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @else
                            <span class="badge badge-warning" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="fas fa-times-circle mr-1"></i>Inactive
                            </span>
                        @endif
                    </div>
                    <p class="text-muted mb-0">
                        <i class="fas fa-hashtag mr-1"></i>User ID: #{{ $user->id }}
                        @if($user->last_login)
                            <span class="ml-3">
                                <i class="fas fa-clock mr-1"></i>Last Login: {{ $user->last_login }}
                            </span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    <div class="profile-actions">
                        <div class="btn-group" role="group">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i>Back
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            @if(auth()->user()->isAdmin() && $user->id != auth()->id())
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteUser({{ $user->id }}, '{{ $user->username }}')">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
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
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-cube fa-2x mb-2"></i>
                <h3 class="mb-1" style="font-weight: 700;">{{ number_format($boothCount) }}</h3>
                <small style="opacity: 0.9;">Total Booths</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fas fa-calendar-check fa-2x mb-2"></i>
                <h3 class="mb-1" style="font-weight: 700;">{{ number_format($bookingCount) }}</h3>
                <small style="opacity: 0.9;">Total Bookings</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fas fa-key fa-2x mb-2"></i>
                <h3 class="mb-1" style="font-weight: 700;">{{ number_format($permissionsCount) }}</h3>
                <small style="opacity: 0.9;">Total Permissions</small>
            </div>
        </div>
        @if(isset($affiliateStats) && $affiliateStats['total_bookings'] > 0)
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                <h3 class="mb-1" style="font-weight: 700;">${{ number_format($affiliateStats['total_revenue'], 0) }}</h3>
                <small style="opacity: 0.9;">Affiliate Revenue</small>
            </div>
        </div>
        @endif
    </div>

    <!-- Affiliate Benefits & Commission Section -->
    @if(isset($affiliateStats) && $affiliateStats['total_bookings'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card detail-card success" style="border-left-color: #43e97b;">
                <div class="card-header" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; border-radius: 12px 12px 0 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-line mr-2"></i>Affiliate Benefits & Commission Summary</h5>
                        <a href="{{ route('affiliates.show', $user->id) }}" class="btn btn-light btn-sm">
                            <i class="fas fa-external-link-alt mr-1"></i>View Full Report
                        </a>
                    </div>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <!-- Key Metrics -->
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3" style="background: #f8f9fa; border-radius: 12px;">
                                <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                <h4 class="mb-1" style="font-weight: 700; color: #495057;">{{ number_format($affiliateStats['total_bookings']) }}</h4>
                                <small class="text-muted">Total Affiliate Bookings</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3" style="background: #f8f9fa; border-radius: 12px;">
                                <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                                <h4 class="mb-1" style="font-weight: 700; color: #495057;">${{ number_format($affiliateStats['total_revenue'], 2) }}</h4>
                                <small class="text-muted">Total Revenue Generated</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3" style="background: #f8f9fa; border-radius: 12px;">
                                <i class="fas fa-users fa-2x text-info mb-2"></i>
                                <h4 class="mb-1" style="font-weight: 700; color: #495057;">{{ number_format($affiliateStats['unique_clients']) }}</h4>
                                <small class="text-muted">Unique Clients</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3" style="background: #f8f9fa; border-radius: 12px;">
                                <i class="fas fa-mouse-pointer fa-2x text-warning mb-2"></i>
                                <h4 class="mb-1" style="font-weight: 700; color: #495057;">{{ number_format($affiliateStats['total_clicks']) }}</h4>
                                <small class="text-muted">Total Link Clicks</small>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Stats -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="p-3" style="background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%); border-radius: 12px; border-left: 4px solid #667eea;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block mb-1">Average Booking Value</small>
                                        <h5 class="mb-0" style="font-weight: 700; color: #667eea;">${{ number_format($affiliateStats['avg_booking_value'], 2) }}</h5>
                                    </div>
                                    <i class="fas fa-chart-bar fa-2x text-muted"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3" style="background: linear-gradient(135deg, #43e97b15 0%, #38f9d715 100%); border-radius: 12px; border-left: 4px solid #43e97b;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block mb-1">Conversion Rate</small>
                                        <h5 class="mb-0" style="font-weight: 700; color: #43e97b;">{{ number_format($affiliateStats['conversion_rate'], 1) }}%</h5>
                                    </div>
                                    <i class="fas fa-percentage fa-2x text-muted"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3" style="background: linear-gradient(135deg, #f093fb15 0%, #f5576c15 100%); border-radius: 12px; border-left: 4px solid #f093fb;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted d-block mb-1">Floor Plans Worked</small>
                                        <h5 class="mb-0" style="font-weight: 700; color: #f093fb;">{{ number_format($affiliateStats['unique_floor_plans']) }}</h5>
                                    </div>
                                    <i class="fas fa-map fa-2x text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="p-3" style="background: #f8f9fa; border-radius: 12px;">
                                <small class="text-muted d-block mb-2"><i class="fas fa-calendar-check mr-1"></i>First Booking</small>
                                <strong>
                                    @if($affiliateStats['first_booking_at'])
                                        {{ \Carbon\Carbon::parse($affiliateStats['first_booking_at'])->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">No bookings yet</span>
                                    @endif
                                </strong>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3" style="background: #f8f9fa; border-radius: 12px;">
                                <small class="text-muted d-block mb-2"><i class="fas fa-clock mr-1"></i>Last Booking</small>
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

                    <!-- Recent Bookings -->
                    @if($affiliateStats['recent_bookings']->count() > 0)
                    <div class="mt-4">
                        <h6 class="mb-3" style="font-weight: 600; color: #495057;">
                            <i class="fas fa-history mr-2"></i>Recent Affiliate Bookings
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead style="background: #f8f9fa;">
                                    <tr>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>Floor Plan</th>
                                        <th class="text-right">Revenue</th>
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
                                        <td class="text-right">
                                            <strong class="text-success">
                                                ${{ number_format($booking->booths()->sum('price') ?? 0, 2) }}
                                            </strong>
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
        </div>
    </div>
    @endif

    <div class="row">
        <!-- User Information -->
        <div class="col-md-6 mb-4">
            <div class="card detail-card primary">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-user mr-2"></i>User Information</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-hashtag mr-2"></i>User ID:</span>
                            <strong class="text-primary">#{{ $user->id }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-user mr-2"></i>Username:</span>
                            <strong>{{ $user->username }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-user-tag mr-2"></i>Type:</span>
                            @if($user->isAdmin())
                                <span class="badge badge-danger">
                                    <i class="fas fa-shield-alt mr-1"></i>Administrator
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-user-tie mr-1"></i>Sale Staff
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-user-shield mr-2"></i>Role:</span>
                            @if($user->role)
                                <span class="badge badge-info">
                                    <i class="fas fa-shield mr-1"></i>{{ $user->role->name }}
                                </span>
                            @else
                                <span class="badge badge-light">No Role Assigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-toggle-on mr-2"></i>Status:</span>
                            @if($user->isActive())
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($user->last_login)
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-clock mr-2"></i>Last Login:</span>
                            <span>{{ $user->last_login }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Permissions -->
        @if($user->role)
        <div class="col-md-6 mb-4">
            <div class="card detail-card warning">
                <div class="card-header" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-key mr-2"></i>Role Permissions</h5>
                </div>
                <div class="card-body" style="padding: 24px; max-height: 400px; overflow-y: auto;">
                    @php
                        $permissions = $user->getPermissions()->groupBy('module');
                    @endphp
                    @if($permissions->count() > 0)
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2" style="font-weight: 600;">
                                <i class="fas fa-folder mr-1"></i>{{ ucfirst($module ?: 'General') }}
                                <span class="badge badge-secondary ml-2">{{ $modulePermissions->count() }}</span>
                            </h6>
                            <div class="d-flex flex-wrap">
                                @foreach($modulePermissions as $permission)
                                <span class="badge badge-primary mr-2 mb-2" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">
                                    {{ $permission->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">No permissions assigned</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Change Password -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card detail-card primary">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-key mr-2"></i>Change Password</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <form action="{{ route('users.password.update', $user->id) }}" method="POST" id="passwordForm">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label"><i class="fas fa-lock mr-1"></i>New Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password" placeholder="Enter new password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label"><i class="fas fa-lock mr-1"></i>Confirm Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" 
                                               id="password_confirmation" name="password_confirmation" 
                                               placeholder="Confirm new password" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                                <i class="fas fa-eye" id="passwordConfirmationToggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="passwordMatch" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key mr-1"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Avatar Upload Modal -->
    <div class="modal fade" id="avatarUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-camera mr-2"></i>Upload Avatar
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-image mr-2"></i>Upload Cover Image
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
@endsection

@push('scripts')
<script>
function openAvatarUploadModal() {
    $('#avatarUploadModal').modal('show');
}

function openCoverUploadModal() {
    $('#coverUploadModal').modal('show');
}

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
            confirmButtonColor: '#667eea'
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

