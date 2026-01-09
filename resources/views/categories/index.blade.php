@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-tags me-2"></i>Categories</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Category
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Limit</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            <strong>{{ $category->name }}</strong>
                            @if($category->children->count() > 0)
                                <span class="badge bg-info ms-2">{{ $category->children->count() }} sub-categories</span>
                            @endif
                        </td>
                        <td>
                            @if($category->parent)
                                {{ $category->parent->name }}
                            @else
                                <span class="text-muted">Main Category</span>
                            @endif
                        </td>
                        <td>{{ $category->limit ?? 'Unlimited' }}</td>
                        <td>
                            @if($category->status == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-warning">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @if($category->children->count() > 0)
                        @foreach($category->children as $child)
                        <tr class="table-light">
                            <td></td>
                            <td class="ps-5">
                                <i class="fas fa-arrow-right me-2"></i>{{ $child->name }}
                            </td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $child->limit ?? 'Unlimited' }}</td>
                            <td>
                                @if($child->status == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('categories.edit', $child) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $child) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No categories found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
