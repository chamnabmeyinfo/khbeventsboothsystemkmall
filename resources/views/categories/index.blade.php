@extends('layouts.adminlte')

@section('title', 'Categories & Subcategories')
@section('page-title', 'Categories & Subcategories')
@section('breadcrumb', 'Catalog / Categories')

@push('styles')
<style>
    .category-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        border-left: 4px solid #667eea;
        margin-bottom: 16px;
    }

    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .subcategory-item {
        background: rgba(248, 249, 250, 0.8);
        border-radius: 8px;
        padding: 12px;
        margin-left: 32px;
        margin-bottom: 8px;
        border-left: 3px solid #17a2b8;
    }

    .subcategory-item:hover {
        background: rgba(248, 249, 250, 1);
        transform: translateX(4px);
    }

    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        height: 100%;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .kpi-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 16px;
    }

    .kpi-card.primary .kpi-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success .kpi-icon { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.info .kpi-icon { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 8px 0;
        line-height: 1;
    }

    .kpi-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="kpi-label">Main Categories</div>
                    <div class="kpi-value">{{ number_format($stats['total_categories'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="kpi-label">Subcategories</div>
                    <div class="kpi-value">{{ number_format($stats['total_subcategories'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kpi-label">Active Categories</div>
                    <div class="kpi-value">{{ number_format($stats['active_categories'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <button type="button" class="btn btn-primary" onclick="showCreateCategoryModal()">
                        <i class="fas fa-plus mr-1"></i>Add Category
                    </button>
                    <button type="button" class="btn btn-info" onclick="refreshPage()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories List -->
    <div id="categoriesList">
        @forelse($categories as $category)
            @if($category->parent_id == 0)
            <div class="category-card">
                <div class="card-body" style="padding: 20px;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="flex: 1;">
                            <h5 class="mb-2" style="font-weight: 700;">
                                <i class="fas fa-folder text-primary mr-2"></i>{{ $category->name }}
                            </h5>
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <span class="badge badge-info">
                                        <i class="fas fa-store mr-1"></i>
                                        {{ $category->booths->count() ?? 0 }} booths
                                    </span>
                                </div>
                                <div>
                                    <span class="badge badge-secondary">
                                        Limit: {{ $category->limit ?? 'Unlimited' }}
                                    </span>
                                </div>
                                <div>
                                    @if($category->status == 1)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-warning">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger" onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Subcategories -->
                    @if($category->children->count() > 0)
                    <div class="mt-3 pt-3" style="border-top: 1px solid rgba(0,0,0,0.1);">
                        <h6 class="mb-3 text-muted">
                            <i class="fas fa-folder-open mr-1"></i>Subcategories ({{ $category->children->count() }})
                        </h6>
                        @foreach($category->children as $subcategory)
                        <div class="subcategory-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>
                                        <i class="fas fa-arrow-right text-info mr-2"></i>{{ $subcategory->name }}
                                    </strong>
                                    <div class="mt-1">
                                        <span class="badge badge-info">
                                            <i class="fas fa-store mr-1"></i>
                                            {{ $subcategory->booths->count() ?? 0 }} booths
                                        </span>
                                        <span class="badge badge-secondary ml-2">
                                            Limit: {{ $subcategory->limit ?? 'Unlimited' }}
                                        </span>
                                        @if($subcategory->status == 1)
                                            <span class="badge badge-success ml-2">Active</span>
                                        @else
                                            <span class="badge badge-warning ml-2">Inactive</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('categories.edit', $subcategory) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="deleteCategory({{ $subcategory->id }}, '{{ $subcategory->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">No categories found</p>
                <button type="button" class="btn btn-primary" onclick="showCreateCategoryModal()">
                    <i class="fas fa-plus mr-1"></i>Create First Category
                </button>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 700px;">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 20px 20px 0 0; padding: 24px 32px;">
                <h5 class="modal-title" id="createCategoryModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-tag mr-2"></i>Create New Category
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 1.5rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createCategoryForm" method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="modal-body" style="padding: 32px;">
                    <div id="createCategoryError" class="alert alert-danger" style="display: none; border-radius: 12px;"></div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_category_name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_category_name" name="name" required placeholder="Enter category name" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modal_category_parent_id" class="form-label">Parent Category</label>
                            <select class="form-control" id="modal_category_parent_id" name="parent_id" style="border-radius: 8px;">
                                <option value="">None (Main Category)</option>
                                @foreach(\App\Models\Category::where('parent_id', 0)->orderBy('name')->get() as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_category_limit" class="form-label">Limit</label>
                            <input type="number" class="form-control" id="modal_category_limit" name="limit" min="0" placeholder="Leave empty for unlimited" style="border-radius: 8px;">
                            <small class="form-text text-muted">Leave empty for unlimited.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modal_category_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="modal_category_status" name="status" required style="border-radius: 8px;">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 20px 32px; border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="createCategorySubmitBtn" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-save mr-1"></i>Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function deleteCategory(id, name) {
    Swal.fire({
        title: 'Delete Category?',
        text: `Are you sure you want to delete category "${name}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch(`/categories/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE'
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json();
                }
            })
            .then(data => {
                hideLoading();
                if (data && data.success) {
                    Swal.fire('Deleted!', data.message || 'Category has been deleted.', 'success')
                        .then(() => {
                            location.reload();
                        });
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error!', 'An error occurred while deleting the category.', 'error');
                console.error('Error:', error);
            });
        }
    });
}

function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Show Create Category Modal
function showCreateCategoryModal() {
    $('#createCategoryModal').modal('show');
    $('#createCategoryForm')[0].reset();
    $('#createCategoryError').hide();
    $('#modal_category_status').val('1');
}

// Handle Create Category Form Submission
$(document).ready(function() {
    $('#createCategoryForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createCategorySubmitBtn');
        const errorDiv = $('#createCategoryError');
        const originalText = submitBtn.html();
        
        errorDiv.hide();
        
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    $('#createCategoryModal').modal('hide');
                    form[0].reset();
                    errorDiv.hide();
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Category created successfully');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Category created successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(response.message || 'Category created successfully');
                    }
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the category.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const firstError = Object.values(errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                }
                errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i>' + errorMessage).show();
                errorDiv[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Reset form when modal is closed
    $('#createCategoryModal').on('hidden.bs.modal', function() {
        $('#createCategoryForm')[0].reset();
        $('#createCategoryError').hide();
        $('#modal_category_status').val('1');
    });
});
</script>
@endpush

