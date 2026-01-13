@extends('layouts.adminlte')

@section('title', 'Developer Tools')
@section('page-title', 'Developer Tools')
@section('breadcrumb', 'System / Developer Tools')

@push('styles')
<style>
    .dev-card {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="card dev-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-database mr-2"></i>Pull / Update Database</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Runs <code>php artisan migrate</code> on the server to apply pending migrations. For admins only.</p>
                    <button id="runMigrateBtn" class="btn btn-primary">
                        <i class="fas fa-play mr-2"></i>Run Migrations
                    </button>
                    <pre id="migrateOutput" class="mt-3" style="display:none; background:#f8f9fa; padding:12px; border-radius:8px; max-height:240px; overflow:auto;"></pre>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card dev-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-headset mr-2"></i>Call Developer</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Need help? Click below to email the developer.</p>
                    <a href="mailto:dev@khbevents.com?subject=Support%20Request&body=Please%20describe%20the%20issue%20and%20include%20screenshots/steps." class="btn btn-info">
                        <i class="fas fa-envelope mr-2"></i>Email Developer
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('runMigrateBtn').addEventListener('click', function() {
    const btn = this;
    const outputEl = document.getElementById('migrateOutput');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Running...';
    outputEl.style.display = 'none';
    outputEl.textContent = '';

    fetch('{{ route('developer.tools.migrate') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json().then(data => ({ status: res.status, data })))
    .then(({ status, data }) => {
        let msg = '';
        if (data.output) msg += data.output;
        if (data.message) msg += (msg ? '\n' : '') + data.message;
        outputEl.textContent = msg || 'Done.';
        outputEl.style.display = 'block';

        if (status === 200 && data.success) {
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Completed';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
        } else {
            btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Error';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-danger');
        }
    })
    .catch(err => {
        outputEl.textContent = err.message || 'Error running migrate.';
        outputEl.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Error';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-danger');
    })
    .finally(() => {
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play mr-2"></i>Run Migrations';
            btn.classList.remove('btn-success', 'btn-danger');
            btn.classList.add('btn-primary');
        }, 3000);
    });
});
</script>
@endpush
