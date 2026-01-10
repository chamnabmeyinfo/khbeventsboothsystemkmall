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
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Add Category
                    </a>
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
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>Create First Category
                </a>
            </div>
        </div>
        @endforelse
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
</script>
@endpush
