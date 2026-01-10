@extends('layouts.adminlte')

@section('title', 'Edit Email Template')
@section('page-title', 'Edit Email Template')
@section('breadcrumb', 'Email Templates / Edit')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-envelope mr-2"></i>Edit Email Template: {{ $emailTemplate->name }}</h3>
        </div>
        <form action="{{ route('email-templates.update', $emailTemplate) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $emailTemplate->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $emailTemplate->slug) }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', $emailTemplate->category) }}" list="categories">
                    <datalist id="categories">
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group">
                    <label>Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject', $emailTemplate->subject) }}" required>
                </div>
                <div class="form-group">
                    <label>Body <span class="text-danger">*</span></label>
                    <textarea name="body" class="form-control" rows="10" required>{{ old('body', $emailTemplate->body) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Available Variables</label>
                    <textarea name="variables" class="form-control" rows="5">{{ old('variables', is_array($emailTemplate->variables) ? implode("\n", array_map(function($k, $v) { return $k . ': ' . $v; }, array_keys($emailTemplate->variables ?? []), $emailTemplate->variables ?? [])) : '') }}</textarea>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Update Template
                </button>
                <a href="{{ route('email-templates.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
