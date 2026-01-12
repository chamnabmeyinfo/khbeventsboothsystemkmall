@extends('layouts.adminlte')

@section('title', 'Export & Import')
@section('page-title', 'Export & Import Data')
@section('breadcrumb', 'Data / Export & Import')

@push('styles')
<style>
    .export-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        border-left: 4px solid;
        margin-bottom: 16px;
        cursor: pointer;
    }

    .export-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .export-card.primary { border-left-color: #667eea; }
    .export-card.success { border-left-color: #48bb78; }
    .export-card.info { border-left-color: #4299e1; }
    .export-card.warning { border-left-color: #ed8936; }

    .import-section {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        padding: 24px;
    }

    .upload-area {
        border: 2px dashed #cbd5e0;
        border-radius: 12px;
        padding: 48px;
        text-align: center;
        transition: all 0.3s;
        background: rgba(248, 249, 250, 0.5);
    }

    .upload-area:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
    }

    .upload-area.dragover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 style="font-weight: 700; color: #2d3748;">
                        <i class="fas fa-file-export mr-2 text-primary"></i>Export & Import Data
                    </h2>
                    <p class="text-muted mb-0">Export data to CSV/PDF or import data from CSV files</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Export Section -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-download mr-2"></i>Export Data
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Export Booths -->
                    <div class="export-card primary" onclick="exportData('booths', 'csv')">
                        <div style="padding: 20px;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div style="width: 56px; height: 56px; border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                </div>
                                <div style="flex: 1;">
                                    <h5 class="mb-1" style="font-weight: 600;">Export Booths</h5>
                                    <p class="mb-0 text-muted small">Download all booth data as CSV file</p>
                                </div>
                                <div>
                                    <span class="badge badge-primary">CSV</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Clients -->
                    <div class="export-card success" onclick="exportData('clients', 'csv')">
                        <div style="padding: 20px;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div style="width: 56px; height: 56px; border-radius: 12px; background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div style="flex: 1;">
                                    <h5 class="mb-1" style="font-weight: 600;">Export Clients</h5>
                                    <p class="mb-0 text-muted small">Download all client data as CSV file</p>
                                </div>
                                <div>
                                    <span class="badge badge-success">CSV</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Bookings -->
                    <div class="export-card info" onclick="exportData('bookings', 'csv')">
                        <div style="padding: 20px;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div style="width: 56px; height: 56px; border-radius: 12px; background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                </div>
                                <div style="flex: 1;">
                                    <h5 class="mb-1" style="font-weight: 600;">Export Bookings</h5>
                                    <p class="mb-0 text-muted small">Download all booking records as CSV file</p>
                                </div>
                                <div>
                                    <span class="badge badge-info">CSV</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export PDF -->
                    <div class="export-card warning" onclick="exportData('booths', 'pdf')">
                        <div style="padding: 20px;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div style="width: 56px; height: 56px; border-radius: 12px; background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                                        <i class="fas fa-file-pdf"></i>
                                    </div>
                                </div>
                                <div style="flex: 1;">
                                    <h5 class="mb-1" style="font-weight: 600;">Export Booths (PDF)</h5>
                                    <p class="mb-0 text-muted small">Generate PDF report of all booths</p>
                                </div>
                                <div>
                                    <span class="badge badge-warning">PDF</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Section -->
        <div class="col-lg-6 mb-4">
            <div class="import-section">
                <h3 class="mb-4" style="font-weight: 600; color: #2d3748;">
                    <i class="fas fa-upload mr-2 text-info"></i>Import Data
                </h3>
                
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h6><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</h6>
                        @if(session('import_errors') && count(session('import_errors')) > 0)
                            <hr>
                            <strong>Import Errors:</strong>
                            <ul class="mb-0">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}</h6>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form id="importForm" action="{{ route('export.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-4">
                        <label><i class="fas fa-list mr-1"></i>Select Data Type</label>
                        <select name="type" class="form-control" required>
                            <option value="">Select type...</option>
                            <option value="booths">Booths</option>
                            <option value="clients">Clients</option>
                            <option value="bookings">Bookings</option>
                        </select>
                        <small class="form-text text-muted">Choose the type of data you want to import</small>
                    </div>
                    <div class="form-group mb-4">
                        <label><i class="fas fa-file-csv mr-1"></i>CSV File</label>
                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="mb-2">
                                <strong>Click to upload</strong> or drag and drop
                            </p>
                            <p class="text-muted small mb-0">CSV files only</p>
                            <input type="file" name="file" id="fileInput" class="d-none" accept=".csv" required>
                        </div>
                        <div id="fileName" class="mt-2" style="display: none;">
                            <span class="badge badge-success">
                                <i class="fas fa-file-csv mr-1"></i><span id="fileNameText"></span>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info btn-block" id="importBtn">
                        <i class="fas fa-upload mr-1"></i>Import Data
                    </button>
                </form>

                <!-- Import History -->
                <div class="mt-4 pt-4" style="border-top: 1px solid rgba(0,0,0,0.1);">
                    <h6 class="mb-3 text-muted">
                        <i class="fas fa-history mr-1"></i>Import Guidelines
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            CSV file must have headers matching database columns
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Ensure data format matches expected structure
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Large files may take time to process
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            Review imported data after completion
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Export Data
function exportData(type, format) {
    showLoading();
    let url = '';
    
    if (format === 'csv') {
        switch(type) {
            case 'booths':
                url = '{{ route("export.booths") }}';
                break;
            case 'clients':
                url = '{{ route("export.clients") }}';
                break;
            case 'bookings':
                url = '{{ route("export.bookings") }}';
                break;
        }
    } else if (format === 'pdf') {
        url = '{{ route("export.pdf", ["type" => "booths"]) }}';
    }
    
    if (url) {
        window.location.href = url;
        setTimeout(() => {
            hideLoading();
        }, 2000);
    }
}

// File Upload Area
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
const fileName = document.getElementById('fileName');
const fileNameText = document.getElementById('fileNameText');

uploadArea.addEventListener('click', () => {
    fileInput.click();
});

uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        fileInput.files = files;
        updateFileName(files[0].name);
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        updateFileName(e.target.files[0].name);
    }
});

function updateFileName(name) {
    fileNameText.textContent = name;
    fileName.style.display = 'block';
}

// Import Form
document.getElementById('importForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!fileInput.files.length) {
        Swal.fire({
            icon: 'error',
            title: 'No File Selected',
            text: 'Please select a CSV file to import.',
            confirmButtonColor: '#667eea'
        });
        return;
    }
    
    Swal.fire({
        title: 'Import Data?',
        text: 'This will import data from the CSV file. Continue?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, import it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            const formData = new FormData(this);
            
            fetch('{{ route("export.import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                // Check if response is a redirect (status 200 but redirected)
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                if (!data) return; // Response was redirected
                
                if (data.success) {
                    let message = data.message || 'Data imported successfully.';
                    let html = `<p>${message}</p>`;
                    
                    // Show errors if any
                    if (data.errors && data.errors.length > 0) {
                        html += '<hr><strong>Errors:</strong><ul class="text-left mb-0">';
                        data.errors.slice(0, 10).forEach(error => {
                            html += `<li>${error}</li>`;
                        });
                        if (data.errors.length > 10) {
                            html += `<li><em>... and ${data.errors.length - 10} more errors</em></li>`;
                        }
                        html += '</ul>';
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Import Successful!',
                        html: html,
                        confirmButtonColor: '#667eea',
                        width: '600px'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    let message = data.message || 'An error occurred during import.';
                    let html = `<p>${message}</p>`;
                    
                    if (data.errors && data.errors.length > 0) {
                        html += '<hr><strong>Errors:</strong><ul class="text-left mb-0">';
                        data.errors.forEach(error => {
                            html += `<li>${error}</li>`;
                        });
                        html += '</ul>';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Failed',
                        html: html,
                        confirmButtonColor: '#667eea',
                        width: '600px'
                    });
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Import error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred: ' + (error.message || 'Unknown error'),
                    confirmButtonColor: '#667eea'
                });
            });
        }
    });
});
</script>
@endpush

