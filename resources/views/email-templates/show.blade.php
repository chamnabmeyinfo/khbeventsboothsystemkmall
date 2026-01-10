@extends('layouts.adminlte')

@section('title', 'View Email Template')
@section('page-title', 'View Email Template')
@section('breadcrumb', 'Email Templates / View')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-envelope mr-2"></i>{{ $emailTemplate->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('email-templates.edit', $emailTemplate) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <a href="{{ route('email-templates.preview', $emailTemplate) }}" class="btn btn-sm btn-warning" target="_blank">
                            <i class="fas fa-eye mr-1"></i>Preview
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Slug:</dt>
                        <dd class="col-sm-9"><code>{{ $emailTemplate->slug }}</code></dd>
                        
                        <dt class="col-sm-3">Category:</dt>
                        <dd class="col-sm-9">{{ $emailTemplate->category ?? 'General' }}</dd>
                        
                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            @if($emailTemplate->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Subject:</dt>
                        <dd class="col-sm-9">{{ $emailTemplate->subject }}</dd>
                    </dl>
                    
                    <hr>
                    <h5>Body:</h5>
                    <div class="border p-3 bg-light">
                        {!! nl2br(e($emailTemplate->body)) !!}
                    </div>
                    
                    @if($emailTemplate->variables)
                    <hr>
                    <h5>Available Variables:</h5>
                    <ul>
                        @foreach($emailTemplate->variables as $key => $description)
                        <li><code>{{$key}}</code>: {{ $description }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-paper-plane mr-2"></i>Send Test Email</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('email-templates.send-test', $emailTemplate) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" required placeholder="test@example.com">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane mr-1"></i>Send Test
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
