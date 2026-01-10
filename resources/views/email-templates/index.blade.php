@extends('layouts.adminlte')

@section('title', 'Email Templates')
@section('page-title', 'Email Templates')
@section('breadcrumb', 'Communication / Email Templates')

@push('styles')
<style>
    .template-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        border-left: 4px solid;
        height: 100%;
    }

    .template-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .template-card.booking { border-left-color: #ed8936; }
    .template-card.payment { border-left-color: #48bb78; }
    .template-card.notification { border-left-color: #4299e1; }
    .template-card.default { border-left-color: #667eea; }

    .template-icon {
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

    .template-card.booking .template-icon { background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); }
    .template-card.payment .template-icon { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); }
    .template-card.notification .template-icon { background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); }
    .template-card.default .template-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }

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
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div class="kpi-label">Total Templates</div>
                    <div class="kpi-value">{{ number_format($stats['total_templates'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kpi-label">Active Templates</div>
                    <div class="kpi-value">{{ number_format($stats['active_templates'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="kpi-label">Categories</div>
                    <div class="kpi-value">{{ number_format($stats['categories_count'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="{{ route('email-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create Template
                    </a>
                    <button type="button" class="btn btn-info" onclick="refreshPage()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('email-templates.index') }}" class="row">
                <div class="col-md-4 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search templates..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-folder mr-1"></i>Category</label>
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-toggle-on mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="row">
        @forelse($templates as $template)
        @php
            $categoryClass = strtolower($template->category ?? 'default');
            $iconClass = in_array($categoryClass, ['booking', 'payment', 'notification']) ? $categoryClass : 'default';
        @endphp
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card template-card {{ $iconClass }}">
                <div class="card-body" style="padding: 24px;">
                    <div class="template-icon {{ $iconClass }}">
                        @if($iconClass == 'booking')
                            <i class="fas fa-calendar-check"></i>
                        @elseif($iconClass == 'payment')
                            <i class="fas fa-money-bill-wave"></i>
                        @elseif($iconClass == 'notification')
                            <i class="fas fa-bell"></i>
                        @else
                            <i class="fas fa-envelope"></i>
                        @endif
                    </div>
                    <h5 class="mb-2" style="font-weight: 700;">{{ $template->name }}</h5>
                    <p class="text-muted mb-3 small">{{ \Illuminate\Support\Str::limit($template->subject, 60) }}</p>
                    <div class="mb-3">
                        @if($template->category)
                        <span class="badge badge-info mb-2">
                            <i class="fas fa-folder mr-1"></i>{{ $template->category }}
                        </span>
                        @endif
                        @if($template->is_active)
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-times-circle mr-1"></i>Inactive
                            </span>
                        @endif
                    </div>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <a href="{{ route('email-templates.show', $template) }}" class="btn btn-info">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('email-templates.preview', $template) }}" class="btn btn-warning" target="_blank">
                            <i class="fas fa-eye mr-1"></i>Preview
                        </a>
                        <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">No email templates found</p>
                    <a href="{{ route('email-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create First Template
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($templates, 'hasPages') && $templates->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-footer">
                    <div class="float-right">
                        {{ $templates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}
</script>
@endpush
