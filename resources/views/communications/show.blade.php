@extends('layouts.adminlte')

@section('title', 'View Message')
@section('page-title', 'View Message')
@section('breadcrumb', 'Communications / View')

@section('content')
<div class="container-fluid">
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
            <a href="{{ route('communications.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-1"></i>Back to Messages
            </a>
        </div>
    </div>
</div>
@endsection
