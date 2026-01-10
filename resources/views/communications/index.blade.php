@extends('layouts.adminlte')

@section('title', 'Messages')
@section('page-title', 'Messages & Communications')
@section('breadcrumb', 'Communications')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-envelope mr-2"></i>Messages</h3>
            <div class="card-tools">
                <a href="{{ route('communications.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i>New Message
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>To</th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                        <tr class="{{ !$message->is_read && $message->to_user_id == auth()->id() ? 'bg-light' : '' }}">
                            <td>{{ $message->fromUser->username ?? 'System' }}</td>
                            <td>{{ $message->toUser->username ?? 'All' }}</td>
                            <td><strong>{{ $message->subject }}</strong></td>
                            <td>
                                <span class="badge badge-{{ $message->type == 'announcement' ? 'warning' : 'info' }}">
                                    {{ ucfirst($message->type) }}
                                </span>
                            </td>
                            <td>{{ $message->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($message->to_user_id == auth()->id())
                                    @if($message->is_read)
                                        <span class="badge badge-success">Read</span>
                                    @else
                                        <span class="badge badge-warning">Unread</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('communications.show', $message->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No messages found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection
