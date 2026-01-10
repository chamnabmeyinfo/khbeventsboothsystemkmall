@extends('layouts.adminlte')

@section('title', 'Export & Import')
@section('page-title', 'Export & Import Data')
@section('breadcrumb', 'Export / Import')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Export Section -->
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-download mr-2"></i>Export Data</h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('export.booths') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><i class="fas fa-cube mr-2"></i>Export Booths</h5>
                                <small class="text-muted">CSV</small>
                            </div>
                            <p class="mb-1">Download all booth data as CSV file</p>
                        </a>
                        <a href="{{ route('export.clients') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><i class="fas fa-building mr-2"></i>Export Clients</h5>
                                <small class="text-muted">CSV</small>
                            </div>
                            <p class="mb-1">Download all client data as CSV file</p>
                        </a>
                        <a href="{{ route('export.bookings') }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><i class="fas fa-calendar-check mr-2"></i>Export Bookings</h5>
                                <small class="text-muted">CSV</small>
                            </div>
                            <p class="mb-1">Download all booking records as CSV file</p>
                        </a>
                        <a href="{{ route('export.pdf', ['type' => 'booths']) }}" target="_blank" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1"><i class="fas fa-file-pdf mr-2"></i>Export Booths (PDF)</h5>
                                <small class="text-muted">PDF</small>
                            </div>
                            <p class="mb-1">Generate PDF report of all booths</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Section -->
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-upload mr-2"></i>Import Data</h3>
                </div>
                <div class="card-body">
                    <form id="importForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Select File Type</label>
                            <select name="type" class="form-control" required>
                                <option value="booths">Booths</option>
                                <option value="clients">Clients</option>
                                <option value="bookings">Bookings</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>CSV File</label>
                            <input type="file" name="file" class="form-control-file" accept=".csv" required>
                            <small class="form-text text-muted">Upload a CSV file to import data</small>
                        </div>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-upload mr-1"></i>Import Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('{{ route("export.import") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Import successful! ' + data.message);
        } else {
            alert('Import failed: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});
</script>
@endpush
