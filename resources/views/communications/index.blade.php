@extends('layouts.adminlte')

@section('title', 'Messages & Communications')
@section('page-title', 'Messages & Communications')
@section('breadcrumb', 'Communication / Messages')

@push('styles')
<style>
    .message-card {
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

    .message-card:hover {
        transform: translateX(4px);
        box-shadow: 0 6px 20px rgba(31, 38, 135, 0.3);
    }

    .message-card.unread {
        background: rgba(66, 153, 225, 0.1);
        border-left-color: #4299e1;
    }

    .message-card.announcement {
        border-left-color: #ed8936;
    }

    .message-card.message {
        border-left-color: #667eea;
    }

    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        height: 100%;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .kpi-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 16px;
    }

    .kpi-card.primary .kpi-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success .kpi-icon { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.warning .kpi-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .kpi-card.info .kpi-icon { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 8px 0;
        line-height: 1;
    }

    .kpi-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="kpi-label">Total Messages</div>
                    <div class="kpi-value">{{ number_format($stats['total_messages'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-envelope-open"></i>
                    </div>
                    <div class="kpi-label">Unread</div>
                    <div class="kpi-value">{{ number_format($stats['unread_messages'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="kpi-label">Sent</div>
                    <div class="kpi-value">{{ number_format($stats['sent_messages'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="kpi-label">Announcements</div>
                    <div class="kpi-value">{{ number_format($stats['announcements'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="{{ route('communications.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>New Message
                    </a>
                    <button type="button" class="btn btn-info" onclick="refreshPage()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('communications.index') }}" class="row">
                <div class="col-md-4 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by subject or message..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-filter mr-1"></i>Type</label>
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="message" {{ request('type') == 'message' ? 'selected' : '' }}>Message</option>
                        <option value="announcement" {{ request('type') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-toggle-on mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages List -->
    <div id="messagesList">
        @forelse($messages as $message)
        <div class="message-card {{ !$message->is_read && $message->to_user_id == auth()->id() ? 'unread' : '' }} {{ $message->type }}" 
             onclick="window.location='{{ route('communications.show', $message->id) }}'">
            <div style="padding: 16px;">
                <div class="d-flex align-items-start">
                    <div class="mr-3">
                        @if($message->type == 'announcement')
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                        @else
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                        @endif
                    </div>
                    <div style="flex: 1;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1" style="font-weight: 600;">
                                    @if(!$message->is_read && $message->to_user_id == auth()->id())
                                    <span class="badge badge-primary mr-2">New</span>
                                    @endif
                                    {{ $message->subject }}
                                </h6>
                                <p class="mb-2 text-muted" style="line-height: 1.5;">
                                    {{ \Illuminate\Support\Str::limit($message->message, 100) }}
                                </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-flex align-items-center">
                                    <span class="mr-2">From:</span>
                                    @if($message->fromUser)
                                        <x-avatar 
                                            :avatar="$message->fromUser->avatar" 
                                            :name="$message->fromUser->username" 
                                            :size="'xs'" 
                                            :type="$message->fromUser->isAdmin() ? 'admin' : 'user'"
                                            :shape="'circle'"
                                        />
                                        <strong class="ml-1">{{ $message->fromUser->username }}</strong>
                                    @else
                                        <strong>System</strong>
                                    @endif
                                    @if($message->toUser)
                                        <span class="mx-2">→</span>
                                        <span class="mr-2">To:</span>
                                        <x-avatar 
                                            :avatar="$message->toUser->avatar" 
                                            :name="$message->toUser->username" 
                                            :size="'xs'" 
                                            :type="$message->toUser->isAdmin() ? 'admin' : 'user'"
                                            :shape="'circle'"
                                        />
                                        <strong class="ml-1">{{ $message->toUser->username }}</strong>
                                    @else
                                        <span class="mx-2">→</span>
                                        <strong>All Users</strong>
                                    @endif
                                </small>
                            </div>
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1"></i>{{ $message->created_at->diffForHumans() }}
                                </small>
                                @if($message->type)
                                <span class="badge badge-{{ $message->type == 'announcement' ? 'warning' : 'info' }} ml-2">
                                    {{ ucfirst($message->type) }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No messages found</p>
                <a href="{{ route('communications.create') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="fas fa-plus mr-1"></i>Send First Message
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($messages, 'hasPages') && $messages->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-footer">
                    <div class="float-right">
                        {{ $messages->links() }}
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
function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}
</script>
@endpush
