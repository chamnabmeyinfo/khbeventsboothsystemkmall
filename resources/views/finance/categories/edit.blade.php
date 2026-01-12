@extends('layouts.adminlte')

@section('title', 'Edit Finance Category')
@section('page-title', 'Edit Finance Category #' . $category->id)
@section('breadcrumb', 'Finance / Categories / Edit')

@push('styles')
<style>
    .color-preview {
        display: inline-block;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid #ddd;
        vertical-align: middle;
        margin-left: 10px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-tags mr-2"></i>Edit Finance Category #{{ $category->id }}</h3>
        </div>
        <form action="{{ route('finance.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   required value="{{ old('name', $category->name) }}" placeholder="Enter category name">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select Type...</option>
                                <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                                <option value="revenue" {{ old('type', $category->type) == 'revenue' ? 'selected' : '' }}>Revenue</option>
                                <option value="costing" {{ old('type', $category->type) == 'costing' ? 'selected' : '' }}>Costing</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Select the type of finance category</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3" placeholder="Enter category description...">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <div class="input-group">
                                <input type="color" name="color" id="color" class="form-control @error('color') is-invalid @enderror" 
                                       value="{{ old('color', $category->color ?? '#667eea') }}" style="height: 38px;">
                                <input type="text" id="color_hex" class="form-control" 
                                       value="{{ old('color', $category->color ?? '#667eea') }}" placeholder="#667eea" 
                                       pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$">
                            </div>
                            <span class="color-preview" id="color_preview" style="background-color: {{ old('color', $category->color ?? '#667eea') }};"></span>
                            @error('color')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Choose a color for this category</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sort_order">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                                   min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}" placeholder="0">
                            @error('sort_order')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="is_active">Status</label>
                            <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror">
                                <option value="1" {{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $category->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Update Category
                </button>
                <a href="{{ route('finance.categories.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const colorInput = document.getElementById('color');
        const colorHex = document.getElementById('color_hex');
        const colorPreview = document.getElementById('color_preview');

        // Sync color picker with hex input
        colorInput.addEventListener('input', function() {
            colorHex.value = this.value;
            colorPreview.style.backgroundColor = this.value;
        });

        // Sync hex input with color picker
        colorHex.addEventListener('input', function() {
            if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(this.value)) {
                colorInput.value = this.value;
                colorPreview.style.backgroundColor = this.value;
            }
        });
    });
</script>
@endpush
@endsection
