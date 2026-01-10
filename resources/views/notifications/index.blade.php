@extends('layouts.adminlte')

@section('title', 'Notifications')
@section('page-title', 'Notifications')
@section('breadcrumb', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-bell mr-2"></i>All Notifications</h3>
            <div class="card-tools">
                <button onclick="markAllAsRead()" class="btn btn-sm btn-primary">
                    <i class="fas fa-check-double mr-1"></i>Mark All as Read
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                @forelse($notifications as $notification)
                <li class="list-group-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                @if(!$notification->is_read)
                                <span class="badge badge-primary mr-2">New</span>
                                @endif
                                {{ $notification->title }}
                            </h6>
                            <p class="mb-1">{{ $notification->message }}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        <div>
                            @if(!$notification->is_read)
                            <button onclick="markAsRead({{ $notification->id }})" class="btn btn-sm btn-outline-primary">
                                Mark Read
                            </button>
                            @endif
                        </div>
                    </div>
                </li>
                @empty
                <li class="list-group-item text-center text-muted py-4">No notifications</li>
                @endforelse
            </ul>
        </div>
        <div class="card-footer">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(() => location.reload());
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => location.reload());
}
</script>
@endpush
