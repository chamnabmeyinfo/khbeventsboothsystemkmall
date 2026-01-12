@extends('layouts.adminlte')

@section('title', 'Create Email Template')
@section('page-title', 'Create Email Template')
@section('breadcrumb', 'Email Templates / Create')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-envelope mr-2"></i>Create Email Template</h3>
        </div>
        <form action="{{ route('email-templates.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Template Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required placeholder="e.g., booking-confirmation">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category') }}" list="categories" placeholder="e.g., booking, payment, notification">
                    <datalist id="categories">
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="form-group">
                    <label>Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required placeholder="Use {{variable_name}} for dynamic content">
                </div>
                <div class="form-group">
                    <label>Body <span class="text-danger">*</span></label>
                    <textarea name="body" class="form-control" rows="10" required placeholder="Use {{variable_name}} for dynamic content">{{ old('body') }}</textarea>
                    <small class="form-text text-muted">Use {{variable_name}} syntax for dynamic content (e.g., {{client_name}}, {{booth_number}})</small>
                </div>
                <div class="form-group">
                    <label>Available Variables (one per line, format: variable_name: Description)</label>
                    <textarea name="variables" class="form-control" rows="5" placeholder="client_name: Client Name&#10;booth_number: Booth Number">{{ old('variables') }}</textarea>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Create Template
                </button>
                <a href="{{ route('email-templates.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

