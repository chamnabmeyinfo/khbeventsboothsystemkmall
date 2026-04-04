@extends('layouts.admin')

@section('title', $permission->name)
@section('page-title', 'View permission')
@section('breadcrumb', 'Staff Management / Permissions / ' . $permission->name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/permissions-page.css') }}?v=1.0">
@endpush

@push('body-class', 'ios-dashboard-mode permissions-page')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>{{ $permission->name }}</h1>
            <p>Permission detail and which roles include it.</p>
        </div>
        <div class="looker-actions flex-wrap gap-2">
            <a href="{{ route('permissions.edit', $permission) }}" class="action-btn action-btn-primary">
                <i class="fas fa-edit" aria-hidden="true"></i> Edit
            </a>
            <a href="{{ route('staff.access', ['tab' => 'permissions']) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left text-tertiary" aria-hidden="true"></i> Back to list
            </a>
        </div>
    </header>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="canvas-panel animate-slide-up delay-2">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="fas fa-info-circle" aria-hidden="true"></i> Properties</h2>
                    @if($permission->is_active)
                        <span class="status-badge status-badge-green">Active</span>
                    @else
                        <span class="status-badge status-badge-neutral">Inactive</span>
                    @endif
                </div>
                <div class="px-3 px-md-4 pb-4">
                    <dl class="row mb-0">
                        <dt class="col-sm-3 text-secondary small text-uppercase fw-semibold">Slug</dt>
                        <dd class="col-sm-9"><code class="small">{{ $permission->slug }}</code></dd>

                        <dt class="col-sm-3 text-secondary small text-uppercase fw-semibold mt-3">Module</dt>
                        <dd class="col-sm-9 mt-sm-3">{{ $permission->module ?? 'General' }}</dd>

                        <dt class="col-sm-3 text-secondary small text-uppercase fw-semibold mt-3">Description</dt>
                        <dd class="col-sm-9 mt-sm-3">{{ $permission->description ?? '—' }}</dd>

                        <dt class="col-sm-3 text-secondary small text-uppercase fw-semibold mt-3">Roles</dt>
                        <dd class="col-sm-9 mt-sm-3">
                            <span class="status-badge status-badge-blue">{{ $permission->roles->count() }} linked</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="canvas-panel animate-slide-up delay-3">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="fas fa-user-shield" aria-hidden="true"></i> Roles</h2>
                </div>
                <div class="px-3 px-md-4 pb-4">
                    @if($permission->roles->count() > 0)
                        <ul class="list-unstyled mb-0">
                            @foreach($permission->roles as $role)
                            <li class="mb-2 pb-2 border-bottom border-light">
                                <a href="{{ route('roles.show', $role) }}" class="text-decoration-none d-flex align-items-center gap-2">
                                    <i class="fas fa-shield-alt text-primary" aria-hidden="true"></i>
                                    <span>{{ $role->name }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No roles have this permission yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
