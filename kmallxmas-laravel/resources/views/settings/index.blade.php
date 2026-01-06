@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-cog me-2"></i>Settings</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-broom me-2"></i>Cache Management</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Clear various caches to ensure the application is using the latest data and configurations.</p>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-database me-2 text-primary"></i>Application Cache
                                </h6>
                                <p class="card-text text-muted small">Clear the application cache (stored data, queries, etc.)</p>
                                <form action="{{ route('settings.cache.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-trash me-1"></i>Clear Cache
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-file-code me-2 text-info"></i>Configuration Cache
                                </h6>
                                <p class="card-text text-muted small">Clear the configuration cache (config files)</p>
                                <form action="{{ route('settings.config.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info">
                                        <i class="fas fa-trash me-1"></i>Clear Config
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-route me-2 text-success"></i>Route Cache
                                </h6>
                                <p class="card-text text-muted small">Clear the route cache (route definitions)</p>
                                <form action="{{ route('settings.route.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-trash me-1"></i>Clear Routes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-eye me-2 text-warning"></i>View Cache
                                </h6>
                                <p class="card-text text-muted small">Clear the compiled view cache (Blade templates)</p>
                                <form action="{{ route('settings.view.clear') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        <i class="fas fa-trash me-1"></i>Clear Views
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-body">
                                <h6 class="card-title text-danger">
                                    <i class="fas fa-broom me-2"></i>Clear All Caches
                                </h6>
                                <p class="card-text text-muted small">Clear all caches at once (Application, Config, Route, View)</p>
                                <form action="{{ route('settings.clear-all') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear all caches?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt me-1"></i>Clear All
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary">
                                    <i class="fas fa-rocket me-2"></i>Optimize Application
                                </h6>
                                <p class="card-text text-muted small">Clear all caches and optimize the application for better performance</p>
                                <form action="{{ route('settings.optimize') }}" method="POST" class="d-inline" onsubmit="return confirm('This will clear all caches and optimize the application. Continue?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-magic me-1"></i>Optimize
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>System Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Laravel Version:</th>
                                <td>{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <th>PHP Version:</th>
                                <td>{{ PHP_VERSION }}</td>
                            </tr>
                            <tr>
                                <th>Environment:</th>
                                <td>
                                    <span class="badge bg-{{ app()->environment() === 'production' ? 'danger' : 'info' }}">
                                        {{ app()->environment() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Debug Mode:</th>
                                <td>
                                    <span class="badge bg-{{ config('app.debug') ? 'warning' : 'success' }}">
                                        {{ config('app.debug') ? 'ON' : 'OFF' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">App Name:</th>
                                <td>{{ config('app.name') }}</td>
                            </tr>
                            <tr>
                                <th>App URL:</th>
                                <td>{{ config('app.url') }}</td>
                            </tr>
                            <tr>
                                <th>Timezone:</th>
                                <td>{{ config('app.timezone') }}</td>
                            </tr>
                            <tr>
                                <th>Locale:</th>
                                <td>{{ config('app.locale') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endpush
@endsection
