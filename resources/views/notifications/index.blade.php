@extends('layouts.adminlte')

@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('breadcrumb', 'Communication / Notifications')

@push('styles')
<style>
    .notification-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.2);
        transition: all 0.3s;
        margin-bottom: 12px;
        border-left: 4px solid;
        cursor: pointer;
    }

    .notification-card:hover {
        transform: translateX(4px);
        box-shadow: 0 6px 20px rgba(31, 38, 135, 0.3);
    }

    .notification-card.unread {
        background: rgba(66, 153, 225, 0.1);
        border-left-color: #4299e1;
    }

    .notification-card.system {
        border-left-color: #667eea;
    }

    .notification-card.payment {
        border-left-color: #48bb78;
    }

    .notification-card.booking {
        border-left-color: #ed8936;
    }

    .notification-card.security {
        border-left-color: #f56565;
    }

    .notification-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        flex-shrink: 0;
    }

    .notification-icon.system { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .notification-icon.payment { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); }
    .notification-icon.booking { background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); }
    .notification-icon.security { background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); }
    .notification-icon.default { background: linear-gradient(135deg, #718096 0%, #4a5568 100%); }

    .filter-tabs {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 16px;
        margin-bottom: 24px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 style="font-weight: 700; color: #2d3748;">
                        <i class="fas fa-bell mr-2 text-warning"></i>Notifications
                    </h2>
                    <p class="text-muted mb-0">Stay updated with system alerts and important messages</p>
                </div>
                <div class="mt-3 mt-md-0">
                    <button onclick="markAllAsRead()" class="btn btn-primary">
                        <i class="fas fa-check-double mr-1"></i>Mark All as Read
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
            <label class="btn btn-outline-primary active" onclick="filterNotifications('all')">
                <input type="radio" name="filter" value="all" checked> All
            </label>
            <label class="btn btn-outline-primary" onclick="filterNotifications('unread')">
                <input type="radio" name="filter" value="unread"> Unread
            </label>
            <label class="btn btn-outline-primary" onclick="filterNotifications('system')">
                <input type="radio" name="filter" value="system"> System
            </label>
            <label class="btn btn-outline-primary" onclick="filterNotifications('payment')">
                <input type="radio" name="filter" value="payment"> Payment
            </label>
            <label class="btn btn-outline-primary" onclick="filterNotifications('booking')">
                <input type="radio" name="filter" value="booking"> Booking
            </label>
        </div>
    </div>

    <!-- Notifications List -->
    <div id="notificationsList">
        @forelse($notifications as $notification)
        @php
            $typeClass = strtolower($notification->type ?? 'default');
            $iconClass = in_array($typeClass, ['system', 'payment', 'booking', 'security']) ? $typeClass : 'default';
        @endphp
        <div class="notification-card {{ !$notification->is_read ? 'unread' : '' }} {{ $typeClass }}" 
             data-type="{{ $typeClass }}"
             onclick="viewNotification({{ $notification->id }})">
            <div style="padding: 16px;">
                <div class="d-flex align-items-start">
                    <div class="notification-icon {{ $iconClass }} mr-3">
                        @if($typeClass == 'system')
                            <i class="fas fa-cog"></i>
                        @elseif($typeClass == 'payment')
                            <i class="fas fa-money-bill-wave"></i>
                        @elseif($typeClass == 'booking')
                            <i class="fas fa-calendar-check"></i>
                        @elseif($typeClass == 'security')
                            <i class="fas fa-shield-alt"></i>
                        @else
                            <i class="fas fa-bell"></i>
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1" style="font-weight: 600;">
                                    @if(!$notification->is_read)
                                    <span class="badge badge-primary mr-2">New</span>
                                    @endif
                                    {{ $notification->title }}
                                </h6>
                                <p class="mb-2 text-muted" style="line-height: 1.5;">
                                    {{ $notification->message }}
                                </p>
                            </div>
                            @if(!$notification->is_read)
                            <button onclick="event.stopPropagation(); markAsRead({{ $notification->id }})" 
                                    class="btn btn-sm btn-outline-primary ml-2">
                                <i class="fas fa-check"></i>
                            </button>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                                </small>
                                @if($notification->booking_id)
                                <a href="{{ route('books.show', $notification->booking_id) }}" 
                                   class="btn btn-xs btn-primary ml-2" 
                                   title="View Booking"
                                   onclick="event.stopPropagation()">
                                    <i class="fas fa-calendar-check mr-1"></i>View Booking
                                </a>
                                @endif
                                @if($notification->client_id)
                                <a href="{{ route('clients.show', $notification->client_id) }}" 
                                   class="btn btn-xs btn-info ml-2" 
                                   title="View Client"
                                   onclick="event.stopPropagation()">
                                    <i class="fas fa-user mr-1"></i>View Client
                                </a>
                                @endif
                            </div>
                            @if($notification->type)
                            <span class="badge badge-secondary">
                                {{ ucfirst($notification->type) }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No notifications found</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-footer">
                    <div class="float-right">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function markAsRead(id) {
    showLoading();
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            toastr.success('Notification marked as read');
            setTimeout(() => location.reload(), 500);
        }
    })
    .catch(error => {
        hideLoading();
        toastr.error('Error marking notification as read');
        console.error('Error:', error);
    });
}

function markAllAsRead() {
    Swal.fire({
        title: 'Mark All as Read?',
        text: 'This will mark all notifications as read.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, mark all as read',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    toastr.success('All notifications marked as read');
                    setTimeout(() => location.reload(), 500);
                }
            })
            .catch(error => {
                hideLoading();
                toastr.error('Error marking all as read');
                console.error('Error:', error);
            });
        }
    });
}

function filterNotifications(type) {
    const cards = document.querySelectorAll('.notification-card');
    cards.forEach(card => {
        if (type === 'all') {
            card.style.display = '';
        } else if (type === 'unread') {
            card.style.display = card.classList.contains('unread') ? '' : 'none';
        } else {
            card.style.display = card.dataset.type === type ? '' : 'none';
        }
    });

    // Update active button
    document.querySelectorAll('.btn-outline-primary').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('label').classList.add('active');
}

function viewNotification(id) {
    // Navigate to booking or payment if linked
    window.location.href = '/notifications';
}
</script>
@endpush

