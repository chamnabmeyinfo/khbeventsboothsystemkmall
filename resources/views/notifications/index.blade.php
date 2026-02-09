@extends('layouts.adminlte')

@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('breadcrumb', 'Activity / Notifications')

@push('styles')
<style>
    .notification-group-title {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #718096;
        margin: 1.5rem 0 0.75rem;
        padding-bottom: 0.25rem;
    }
    .notification-group-title:first-child { margin-top: 0; }
    .notif-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 0.75rem;
        transition: box-shadow 0.2s, transform 0.2s;
        overflow: hidden;
    }
    .notif-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .notif-card.unread { background: linear-gradient(90deg, rgba(66, 153, 225, 0.08) 0%, #fff 100%); border-left: 4px solid #4299e1; }
    .notif-card .notif-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: #fff;
        flex-shrink: 0;
    }
    .notif-card .notif-icon.booking { background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); }
    .notif-card .notif-icon.payment { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); }
    .notif-card .notif-icon.system { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .notif-card .notif-icon.security { background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); }
    .notif-card .notif-icon.hr { background: linear-gradient(135deg, #805ad5 0%, #6b46c1 100%); }
    .notif-card .notif-icon.default { background: linear-gradient(135deg, #718096 0%, #4a5568 100%); }
    .notif-actor { font-size: 0.8rem; color: #718096; }
    .notif-time { font-size: 0.75rem; color: #a0aec0; }
    .notif-action-btn { font-size: 0.8rem; padding: 0.25rem 0.75rem; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h2 class="h5 mb-1 font-weight-bold text-dark">
                <i class="fas fa-bell mr-2 text-primary"></i>Notifications
            </h2>
            <p class="text-muted small mb-0">Activity across the system — bookings, clients, payments, and more</p>
        </div>
        <div>
            <button type="button" onclick="markAllAsRead()" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-check-double mr-1"></i>Mark all as read
            </button>
            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary btn-sm ml-1"><i class="fas fa-sync-alt mr-1"></i>Refresh</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card card-outline card-secondary mb-3">
        <div class="card-body py-2">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary active" data-filter="all">All</button>
                <button type="button" class="btn btn-outline-secondary" data-filter="unread">Unread</button>
                <button type="button" class="btn btn-outline-secondary" data-filter="booking">Booking</button>
                <button type="button" class="btn btn-outline-secondary" data-filter="payment">Payment</button>
                <button type="button" class="btn btn-outline-secondary" data-filter="system">System</button>
            </div>
        </div>
    </div>

    <!-- Grouped list -->
    @php
        $grouped = $notifications->groupBy(function ($n) {
            if ($n->created_at->isToday()) return 'today';
            if ($n->created_at->isYesterday()) return 'yesterday';
            if ($n->created_at->isCurrentWeek()) return 'week';
            return 'older';
        });
        $labels = ['today' => 'Today', 'yesterday' => 'Yesterday', 'week' => 'This week', 'older' => 'Older'];
    @endphp

    @foreach($labels as $key => $label)
        @if(isset($grouped[$key]) && $grouped[$key]->isNotEmpty())
            <div class="notification-group-title" data-group="{{ $key }}">{{ $label }}</div>
            @foreach($grouped[$key] as $n)
                @php
                    $typeClass = preg_match('/^hr\./', $n->type ?? '') ? 'hr' : (in_array($n->type, ['booking', 'payment', 'system', 'security']) ? $n->type : 'default');
                @endphp
                <div class="card notif-card {{ $n->is_read ? '' : 'unread' }}" data-id="{{ $n->id }}" data-type="{{ $n->type }}" data-read="{{ $n->is_read ? '1' : '0' }}" data-group="{{ $key }}">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-start">
                            <div class="notif-icon {{ $typeClass }} mr-3">
                                <i class="fas {{ $n->icon }}"></i>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        @if(!$n->is_read)
                                            <span class="badge badge-primary badge-pill mr-2">New</span>
                                        @endif
                                        <strong class="d-block">{{ $n->title }}</strong>
                                        <p class="mb-1 text-muted small">{{ $n->message }}</p>
                                        @if($n->actor)
                                            <span class="notif-actor"><i class="fas fa-user mr-1"></i>{{ $n->actor->username }}</span>
                                        @endif
                                        <span class="notif-time ml-2"><i class="far fa-clock mr-1"></i>{{ $n->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @if($n->link)
                                            <a href="{{ $n->link }}" class="btn btn-sm btn-primary notif-action-btn mr-1" onclick="markAsReadAndGo({{ $n->id }}, this.href); return false;">
                                                View
                                            </a>
                                        @endif
                                        @if(!$n->is_read)
                                            <button type="button" class="btn btn-sm btn-outline-secondary notif-action-btn" onclick="markAsRead({{ $n->id }})">Mark read</button>
                                        @endif
                                    </div>
                                </div>
                                @if($n->booking_id || $n->client_id)
                                    <div class="mt-2">
                                        @if($n->booking_id)
                                            <a href="{{ route('books.show', $n->booking_id) }}" class="btn btn-xs btn-outline-primary mr-1" onclick="event.stopPropagation()">Booking</a>
                                        @endif
                                        @if($n->client_id)
                                            <a href="{{ route('clients.show', $n->client_id) }}" class="btn btn-xs btn-outline-info" onclick="event.stopPropagation()">Client</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    @endforeach

    @if($notifications->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No notifications yet. Activity in the system will appear here.</p>
            </div>
        </div>
    @endif

    @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function markAsRead(id) {
    fetch('{{ url("notifications") }}/' + id + '/read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.success) {
            var card = document.querySelector('.notif-card[data-id="' + id + '"]');
            if (card) {
                card.classList.remove('unread');
                card.setAttribute('data-read', '1');
            }
            if (typeof updateNotificationBadge === 'function') updateNotificationBadge();
        }
    });
}

function markAsReadAndGo(id, url) {
    fetch('{{ url("notifications") }}/' + id + '/read?redirect=1', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(function() {
        window.location.href = url;
    });
}

function markAllAsRead() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Mark all as read?',
            text: 'All notifications will be marked as read.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then(function(result) {
            if (result.isConfirmed) doMarkAllRead();
        });
    } else {
        doMarkAllRead();
    }
}

function doMarkAllRead() {
    fetch('{{ route("notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.success) {
            document.querySelectorAll('.notif-card.unread').forEach(function(c) {
                c.classList.remove('unread');
                c.setAttribute('data-read', '1');
            });
            if (typeof updateNotificationBadge === 'function') updateNotificationBadge();
            if (typeof toastr !== 'undefined') toastr.success('All marked as read');
        }
    });
}

document.querySelectorAll('[data-filter]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-filter]').forEach(function(b) { b.classList.remove('active'); });
        this.classList.add('active');
        var filter = this.getAttribute('data-filter');
        document.querySelectorAll('.notif-card').forEach(function(card) {
            var show = filter === 'all'
                || (filter === 'unread' && card.getAttribute('data-read') === '0')
                || (filter !== 'unread' && card.getAttribute('data-type') === filter);
            card.style.display = show ? '' : 'none';
        });
        document.querySelectorAll('.notification-group-title').forEach(function(title) {
            var group = title.getAttribute('data-group');
            var anyVisible = false;
            document.querySelectorAll('.notif-card[data-group="' + group + '"]').forEach(function(c) {
                if (c.style.display !== 'none') anyVisible = true;
            });
            title.style.display = anyVisible ? '' : 'none';
        });
    });
});
</script>
@endpush
