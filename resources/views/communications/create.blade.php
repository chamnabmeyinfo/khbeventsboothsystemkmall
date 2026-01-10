@extends('layouts.adminlte')

@section('title', 'Send Message')
@section('page-title', 'Send Message')
@section('breadcrumb', 'Communications / Create')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-envelope mr-2"></i>Compose Message</h3>
                </div>
                <form action="{{ route('communications.send') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>To User (Optional)</label>
                            <select name="to_user_id" class="form-control">
                                <option value="">Select User (leave empty for announcement)</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }} ({{ $user->isAdmin() ? 'Admin' : 'Sale' }})</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Leave empty to send as announcement to all users</small>
                        </div>
                        <div class="form-group">
                            <label>Client (Optional)</label>
                            <select name="client_id" class="form-control">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->company ?? $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Subject</label>
                            <input type="text" name="subject" class="form-control" required value="{{ old('subject') }}">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea name="message" class="form-control" rows="10" required>{{ old('message') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-1"></i>Send Message
                        </button>
                        <a href="{{ route('communications.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
