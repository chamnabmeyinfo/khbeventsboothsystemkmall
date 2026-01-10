@extends('layouts.adminlte')

@section('title', 'Email Templates')
@section('page-title', 'Email Templates')
@section('breadcrumb', 'Email Templates')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-envelope mr-2"></i>Email Templates</h3>
            <div class="card-tools">
                <a href="{{ route('email-templates.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i>Create Template
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Category</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                        <tr>
                            <td><strong>{{ $template->name }}</strong></td>
                            <td><code>{{ $template->slug }}</code></td>
                            <td>{{ $template->category ?? 'General' }}</td>
                            <td>{{ Str::limit($template->subject, 50) }}</td>
                            <td>
                                @if($template->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('email-templates.show', $template) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('email-templates.preview', $template) }}" class="btn btn-sm btn-warning" target="_blank">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                                <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('email-templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No email templates found. <a href="{{ route('email-templates.create') }}">Create one</a></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
