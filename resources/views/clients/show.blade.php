@extends('layouts.adminlte')

@section('title', 'Client Details')
@section('page-title', 'Client Details')
@section('breadcrumb', 'Clients / View')

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
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        position: relative;
        overflow: hidden;
        cursor: move;
        user-select: none;
    }

    .profile-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
        cursor: move;
        transition: transform 0.1s ease-out;
        user-select: none;
        -webkit-user-drag: none;
    }
    
    .profile-cover.dragging img {
        cursor: grabbing;
    }
    
    .profile-cover.dragging {
        cursor: grabbing;
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
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
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
            <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item active">Client #{{ $client->id }}</li>
        </ol>
    </nav>

    <!-- Profile Header with Cover and Avatar -->
    <div class="profile-header">
        <!-- Cover Image -->
        <div class="profile-cover" id="profileCover">
            @php
                $coverPosition = $client->cover_position ?? null;
                if (!$coverPosition) {
                    $coverPosition = \App\Models\Setting::getValue('client_' . $client->id . '_cover_position', null);
                }
                $coverPosition = $coverPosition ?? 'center center';
            @endphp
            @if($client->cover_image)
                <img src="{{ asset($client->cover_image) }}" alt="Cover Image" id="coverImage" 
                     style="object-position: {{ $coverPosition }};"
                     data-initial-position="{{ $coverPosition }}">
            @endif
            <div class="profile-cover-upload" onclick="openCoverUploadModal()">
                <div style="text-align: center; color: white;">
                    <i class="fas fa-camera fa-2x mb-2"></i>
                    <p class="mb-0">Change Cover</p>
                </div>
            </div>
            <div class="profile-cover-drag-hint" style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.6); color: white; padding: 8px 12px; border-radius: 6px; font-size: 0.85rem; display: none; pointer-events: none;">
                <i class="fas fa-arrows-alt mr-1"></i> Drag to reposition
            </div>
        </div>

        <!-- Avatar -->
        <div class="profile-avatar-wrapper">
            <div class="profile-avatar" onclick="openAvatarUploadModal()">
                <x-avatar 
                    :avatar="$client->avatar" 
                    :name="$client->name" 
                    :size="'xl'" 
                    :type="'client'"
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
                        {{ $client->name }}
                    </h2>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        @if($client->company)
                            <span class="badge badge-info" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="fas fa-building mr-1"></i>{{ $client->company }}
                            </span>
                        @endif
                        @if($client->position)
                            <span class="badge badge-secondary" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="fas fa-briefcase mr-1"></i>{{ $client->position }}
                            </span>
                        @endif
                        @if($client->sex == 1)
                            <span class="badge badge-info" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="fas fa-mars mr-1"></i>Male
                            </span>
                        @elseif($client->sex == 2)
                            <span class="badge badge-pink" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                <i class="fas fa-venus mr-1"></i>Female
                            </span>
                        @endif
                    </div>
                    <p class="text-muted mb-0">
                        <i class="fas fa-hashtag mr-1"></i>Client ID: #{{ $client->id }}
                        @if($client->phone_number)
                            <span class="ml-3">
                                <i class="fas fa-phone mr-1"></i>{{ $client->phone_number }}
                            </span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    <div class="profile-actions">
                        <div class="btn-group" role="group">
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i>Back
                            </a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteClient({{ $client->id }}, '{{ $client->name }}')">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="fas fa-store fa-2x mb-2"></i>
                <h3 class="mb-1" style="font-weight: 700;">{{ number_format($stats['total_booths'] ?? 0) }}</h3>
                <small style="opacity: 0.9;">Total Booths</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="fas fa-calendar-check fa-2x mb-2"></i>
                <h3 class="mb-1" style="font-weight: 700;">{{ number_format($stats['total_bookings'] ?? 0) }}</h3>
                <small style="opacity: 0.9;">Total Bookings</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h3 class="mb-1" style="font-weight: 700;">{{ number_format($stats['paid_booths'] ?? 0) }}</h3>
                <small style="opacity: 0.9;">Paid Booths</small>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                <h3 class="mb-1" style="font-weight: 700;">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
                <small style="opacity: 0.9;">Total Revenue</small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Client Information -->
        <div class="col-md-6 mb-4">
            <div class="card detail-card primary">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Client Information</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-hashtag mr-2"></i>Client ID:</span>
                            <strong class="text-primary">#{{ $client->id }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-user mr-2"></i>Name:</span>
                            <strong>{{ $client->name }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-building mr-2"></i>Company:</span>
                            <span>{{ $client->company ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-briefcase mr-2"></i>Position:</span>
                            <span>{{ $client->position ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-phone mr-2"></i>Phone:</span>
                            @if($client->phone_number)
                                <a href="tel:{{ $client->phone_number }}" class="text-primary">
                                    <i class="fas fa-phone-alt mr-1"></i>{{ $client->phone_number }}
                                </a>
                            @else
                                <span>N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-venus-mars mr-2"></i>Gender:</span>
                            @if($client->sex == 1)
                                <span class="badge badge-info">
                                    <i class="fas fa-mars mr-1"></i>Male
                                </span>
                            @elseif($client->sex == 2)
                                <span class="badge badge-pink">
                                    <i class="fas fa-venus mr-1"></i>Female
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Statistics -->
        <div class="col-md-6 mb-4">
            <div class="card detail-card success">
                <div class="card-header" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Activity Statistics</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                                <i class="fas fa-check-double fa-2x mb-2"></i>
                                <h4 class="mb-0" style="font-weight: 700;">{{ number_format($stats['confirmed_booths'] ?? 0) }}</h4>
                                <small style="opacity: 0.9;">Confirmed</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h4 class="mb-0" style="font-weight: 700;">{{ number_format($stats['reserved_booths'] ?? 0) }}</h4>
                                <small style="opacity: 0.9;">Reserved</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    @if(isset($recentBookings) && $recentBookings->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card detail-card warning">
                <div class="card-header" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Recent Bookings</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Booths</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td><strong>#{{ $booking->id }}</strong></td>
                                    <td>{{ $booking->date_book ? \Carbon\Carbon::parse($booking->date_book)->format('M d, Y H:i') : 'N/A' }}</td>
                                    <td>
                                        @if($booking->user)
                                            <x-avatar 
                                                :avatar="$booking->user->avatar" 
                                                :name="$booking->user->username" 
                                                :size="'sm'" 
                                                :type="$booking->user->isAdmin() ? 'admin' : 'user'"
                                            />
                                            <span class="ml-2">{{ $booking->user->username }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $boothIds = json_decode($booking->boothid, true) ?? [];
                                            $boothCount = count($boothIds);
                                        @endphp
                                        <span class="badge badge-primary">{{ $boothCount }} booth(s)</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('books.show', $booking) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Booths List -->
    @if($client->booths->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card detail-card primary">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-store mr-2"></i>Assigned Booths ({{ $client->booths->count() }})</h5>
                </div>
                <div class="card-body" style="padding: 24px;">
                    <div class="row">
                        @foreach($client->booths as $booth)
                        <div class="col-md-4 mb-3">
                            <div class="card" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.2s;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">
                                            <a href="{{ route('booths.show', $booth) }}" class="text-primary">
                                                <i class="fas fa-store mr-1"></i>{{ $booth->booth_number }}
                                            </a>
                                        </h6>
                                        <span class="badge badge-{{ $booth->getStatusColor() ?? 'secondary' }}">
                                            {{ $booth->getStatusLabel() ?? 'Unknown' }}
                                        </span>
                                    </div>
                                    @if($booth->price)
                                    <div class="text-muted small">
                                        <i class="fas fa-dollar-sign mr-1"></i>Price: ${{ number_format($booth->price, 2) }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Avatar Upload Modal -->
    <div class="modal fade" id="avatarUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); color: white;">
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
                        entity-type="client"
                        entity-id="{{ $client->id }}"
                        current-image="{{ $client->avatar }}"
                        name="{{ $client->name }}"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Cover Upload Modal -->
    <div class="modal fade" id="coverUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); color: white;">
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
                        entity-type="client"
                        entity-id="{{ $client->id }}"
                        current-image="{{ $client->cover_image }}"
                        name="{{ $client->name }}"
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

// Cover Image Drag to Reposition (same as users)
(function() {
    const coverContainer = document.getElementById('profileCover');
    const coverImage = document.getElementById('coverImage');
    const dragHint = coverContainer ? coverContainer.querySelector('.profile-cover-drag-hint') : null;
    
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
    
    function setPosition(x, y) {
        imageElement.style.objectPosition = `${x}% ${y}%`;
    }
    
    if (dragHint) {
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
    }
    
    coverContainer.addEventListener('mousedown', function(e) {
        if (!coverImage || !coverImage.src) return;
        if (e.target.closest('.profile-cover-upload')) return;
        
        isDragging = true;
        coverContainer.classList.add('dragging');
        if (dragHint) dragHint.style.display = 'none';
        
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
        
        const deltaX = ((mouseX - startX) / rect.width) * 100;
        const deltaY = ((mouseY - startY) / rect.height) * 100;
        
        const newX = Math.max(0, Math.min(100, currentX + deltaX));
        const newY = Math.max(0, Math.min(100, currentY + deltaY));
        
        setPosition(newX, newY);
    });
    
    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            coverContainer.classList.remove('dragging');
            
            const pos = getCurrentPosition();
            saveCoverPosition(pos.x, pos.y);
        }
    });
    
    // Touch events
    coverContainer.addEventListener('touchstart', function(e) {
        if (!coverImage || !coverImage.src) return;
        if (e.target.closest('.profile-cover-upload')) return;
        
        isDragging = true;
        coverContainer.classList.add('dragging');
        if (dragHint) dragHint.style.display = 'none';
        
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
    
    function saveCoverPosition(x, y) {
        const clientId = {{ $client->id }};
        const position = `${x}% ${y}%`;
        
        fetch(`/clients/${clientId}/cover-position`, {
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

function deleteClient(id, name) {
    Swal.fire({
        title: 'Delete Client?',
        text: `Are you sure you want to delete client "${name}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch(`/clients/${id}`, {
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
                    Swal.fire('Deleted!', data.message || 'Client has been deleted.', 'success')
                        .then(() => {
                            window.location.href = '{{ route("clients.index") }}';
                        });
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error!', 'An error occurred while deleting the client.', 'error');
                console.error('Error:', error);
            });
        }
    });
}
</script>
@endpush

