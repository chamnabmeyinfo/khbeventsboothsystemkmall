@extends('layouts.admin')

@section('title', 'Edit permission')
@section('page-title', 'Edit permission')
@section('breadcrumb', 'Staff Management / Permissions / Edit')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/permissions-page.css') }}?v=1.0">
@endpush

@push('body-class', 'ios-dashboard-mode permissions-page permissions-form-page')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Edit permission</h1>
            <p>Update <strong>{{ $permission->name }}</strong>. Changing the slug may affect role assignments that reference it.</p>
        </div>
        <div class="looker-actions flex-wrap gap-2">
            <a href="{{ route('permissions.show', $permission) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-eye text-tertiary" aria-hidden="true"></i> View
            </a>
            <a href="{{ route('staff.access', ['tab' => 'permissions']) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left text-tertiary" aria-hidden="true"></i> Back to list
            </a>
        </div>
    </header>

    <div class="canvas-panel permissions-form-panel animate-slide-up delay-2">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-key" aria-hidden="true"></i> Details</h2>
        </div>
        <form action="{{ route('permissions.update', $permission) }}" method="POST" class="px-3 px-md-4 pb-4">
            @csrf
            @method('PUT')
            @if($errors->any())
                <div class="alert alert-danger rounded-3 mt-3" role="alert">
                    <strong class="d-block mb-2"><i class="fas fa-exclamation-triangle me-1" aria-hidden="true"></i>Please fix the following:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <label for="perm_name" class="form-label">Permission name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="perm_name" class="form-control rounded-3 @error('name') is-invalid @enderror"
                           value="{{ old('name', $permission->name) }}" required autocomplete="off">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="perm_slug" class="form-label">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="perm_slug" class="form-control rounded-3 @error('slug') is-invalid @enderror"
                           value="{{ old('slug', $permission->slug) }}" required autocomplete="off">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="perm_module" class="form-label">Module</label>
                    <input type="text" name="module" id="perm_module" class="form-control rounded-3"
                           value="{{ old('module', $permission->module) }}" list="modules_list" autocomplete="off">
                    <datalist id="modules_list">
                        @foreach($modules as $module)
                            <option value="{{ $module }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label for="perm_sort" class="form-label">Sort order</label>
                    <input type="number" name="sort_order" id="perm_sort" class="form-control rounded-3" value="{{ old('sort_order', $permission->sort_order) }}" min="0">
                </div>
                <div class="col-12">
                    <label for="perm_description" class="form-label">Description</label>
                    <textarea name="description" id="perm_description" class="form-control rounded-3" rows="3">{{ old('description', $permission->description) }}</textarea>
                </div>
                <div class="col-12">
                    @php
                        $permActiveChecked = $errors->any()
                            ? request()->old('is_active') !== null
                            : (bool) $permission->is_active;
                    @endphp
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="perm_is_active" value="1"
                               @checked($permActiveChecked)>
                        <label class="form-check-label" for="perm_is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-save" aria-hidden="true"></i> Save changes
                </button>
                <a href="{{ route('staff.access', ['tab' => 'permissions']) }}" class="action-btn action-btn-secondary">
                    <i class="fas fa-times text-tertiary" aria-hidden="true"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
