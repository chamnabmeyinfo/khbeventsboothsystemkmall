@extends('layouts.adminlte')

@section('title', 'View Message')
@section('page-title', 'View Message')
@section('breadcrumb', 'Communications / View')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('communications.index') }}">Messages</a></li>
            <li class="breadcrumb-item active">Message #{{ $message->id }}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-envelope-open mr-2"></i>{{ $message->subject }}</h3>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>From:</strong> {{ $message->fromUser->username ?? 'System' }}
                </div>
                <div class="col-md-6">
                    <strong>To:</strong> {{ $message->toUser->username ?? 'All Users' }}
                </div>
            </div>
            @if($message->client)
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Client:</strong> {{ $message->client->company ?? $message->client->name }}
                </div>
            </div>
            @endif
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Type:</strong> 
                    <span class="badge badge-{{ $message->type == 'announcement' ? 'warning' : 'info' }}">
                        {{ ucfirst($message->type) }}
                    </span>
                </div>
                <div class="col-md-6">
                    <strong>Date:</strong> {{ $message->created_at->format('Y-m-d H:i:s') }}
                </div>
            </div>
            <hr>
            <div class="message-content">
                {!! nl2br(e($message->message)) !!}
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('communications.index') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Messages
                </a>
                @if($message->fromUser && $message->fromUser->id != auth()->id())
                <button type="button" class="btn btn-primary" onclick="showReplyForm()">
                    <i class="fas fa-reply mr-1"></i>Reply
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Reply Form (Hidden by default) -->
    @if($message->fromUser && $message->fromUser->id != auth()->id())
    <div class="card mt-3" id="replyForm" style="display: none;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-reply mr-2"></i>Reply to {{ $message->fromUser->username }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('communications.send') }}" method="POST">
                @csrf
                <input type="hidden" name="to_user_id" value="{{ $message->from_user_id }}">
                <input type="hidden" name="client_id" value="{{ $message->client_id }}">
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control" value="Re: {{ $message->subject }}" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" class="form-control" rows="5" required placeholder="Type your reply..."></textarea>
                </div>
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i>Send Reply
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="hideReplyForm()">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function showReplyForm() {
    document.getElementById('replyForm').style.display = 'block';
    document.getElementById('replyForm').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function hideReplyForm() {
    document.getElementById('replyForm').style.display = 'none';
}
</script>
@endpush
