@extends('layouts.adminlte')

@section('title', 'Booth Management')
@section('page-title', 'Booth Management')
@section('breadcrumb', 'Booths / Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .booth-image-preview {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .booth-image-preview:hover {
        transform: scale(1.1);
        border-color: #667eea;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
    }
    
    .stat-card.success {
        background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
    }
    
    .stat-card.info {
        background: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);
    }
    
    .stat-card.danger {
        background: linear-gradient(135deg, #e74a3b 0%, #c23321 100%);
    }
    
    .filter-bar {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .table-modern {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .table-modern thead th {
        border: none;
        padding: 16px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .table-modern tbody tr {
        transition: all 0.2s;
    }
    
    .table-modern tbody tr:hover {
        background: #f8f9fc;
        transform: scale(1.01);
    }
    
    .table-modern tbody td {
        padding: 16px;
        vertical-align: middle;
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    
    .btn-action {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }
    
    .image-upload-area {
        border: 2px dashed #667eea;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        background: #f8f9fc;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .image-upload-area:hover {
        background: #e9ecef;
        border-color: #764ba2;
    }
    
    .image-upload-area.dragover {
        background: #e3f2fd;
        border-color: #2196f3;
    }
    
    .image-preview-container {
        position: relative;
        display: inline-block;
        margin-top: 16px;
    }
    
    .image-preview-container img {
        max-width: 300px;
        max-height: 300px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .remove-image-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e74a3b;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="stat-card">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['total'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Total Booths</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card success">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['available'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Available</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card warning">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['reserved'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Reserved</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card info">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['confirmed'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Confirmed</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card danger">
                <div style="font-size: 2rem; font-weight: 700;">{{ $stats['paid'] }}</div>
                <div style="font-size: 0.9rem; opacity: 0.9;">Paid</div>
            </div>
        </div>
        <div class="col-md-2">
            <a href="{{ route('booths.index', ['view' => 'canvas']) }}" class="btn btn-lg btn-primary w-100 h-100 d-flex align-items-center justify-content-center" style="border-radius: 12px; text-decoration: none;">
                <i class="fas fa-map mr-2"></i>Canvas View
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('booths.index', ['view' => 'table']) }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-search mr-1"></i>Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Booth number, company, category..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-map mr-1"></i>Floor Plan</label>
                    <select name="floor_plan_id" class="form-control">
                        <option value="">All Floor Plans</option>
                        @foreach($floorPlans as $fp)
                            <option value="{{ $fp->id }}" {{ request('floor_plan_id') == $fp->id ? 'selected' : '' }}>
                                {{ $fp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-tag mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Available</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Confirmed</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Reserved</option>
                        <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>Hidden</option>
                        <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-building mr-1"></i>Booth Type</label>
                    <select name="booth_type_id" class="form-control">
                        <option value="">All Types</option>
                        @foreach($boothTypes as $type)
                            <option value="{{ $type->id }}" {{ request('booth_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><i class="fas fa-folder mr-1"></i>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Actions Bar -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" class="btn btn-success" onclick="openCreateModal()">
                        <i class="fas fa-plus mr-1"></i>Create New Booth
                    </button>
                    <button type="button" class="btn btn-warning" onclick="bulkUpdateStatus()">
                        <i class="fas fa-edit mr-1"></i>Bulk Update
                    </button>
                    <button type="button" class="btn btn-danger" onclick="bulkDelete()">
                        <i class="fas fa-trash mr-1"></i>Bulk Delete
                    </button>
                </div>
                <div>
                    <a href="{{ route('booths.index', ['view' => 'table', 'export' => 'csv']) }}" class="btn btn-info">
                        <i class="fas fa-download mr-1"></i>Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Booths Table -->
    <div class="card table-modern">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="boothsTable">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Image</th>
                        <th>Booth #</th>
                        <th>Type</th>
                        <th>Floor Plan</th>
                        <th>Company</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Area</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($booths as $booth)
                    <tr>
                        <td>
                            <input type="checkbox" class="booth-checkbox" value="{{ $booth->id }}">
                        </td>
                        <td>
                            @if($booth->booth_image)
                                <img src="{{ asset($booth->booth_image) }}" alt="Booth Image" class="booth-image-preview" onclick="viewImage('{{ asset($booth->booth_image) }}')">
                            @else
                                <div style="width: 60px; height: 60px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong style="font-size: 1.1rem; color: #2d3748;">{{ $booth->booth_number }}</strong>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                {{ $booth->boothType ? $booth->boothType->name : ($booth->type == 1 ? 'Booth' : 'Space Only') }}
                            </span>
                        </td>
                        <td>
                            {{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}
                        </td>
                        <td>
                            {{ $booth->client ? $booth->client->company : 'N/A' }}
                        </td>
                        <td>
                            {{ $booth->category ? $booth->category->name : 'N/A' }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $booth->getStatusColor() }}">
                                {{ $booth->getStatusLabel() }}
                            </span>
                        </td>
                        <td>
                            <strong style="color: #28a745;">${{ number_format($booth->price, 2) }}</strong>
                        </td>
                        <td>
                            {{ $booth->area_sqm ? number_format($booth->area_sqm, 2) . ' m²' : 'N/A' }}
                        </td>
                        <td>
                            {{ $booth->capacity ? $booth->capacity . ' people' : 'N/A' }}
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-sm btn-info btn-action" onclick="viewBooth({{ $booth->id }})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary btn-action" onclick="editBooth({{ $booth->id }})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteBooth({{ $booth->id }})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No booths found. Create your first booth!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($booths->hasPages())
        <div class="card-footer">
            {{ $booths->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Booth Modal -->
<div class="modal fade" id="boothModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-store mr-2"></i>Create New Booth
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="boothForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="boothId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Basic Information</h6>
                            
                            <div class="form-group">
                                <label>Booth Number *</label>
                                <input type="text" name="booth_number" id="booth_number" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Floor Plan *</label>
                                <select name="floor_plan_id" id="floor_plan_id" class="form-control" required>
                                    <option value="">Select Floor Plan</option>
                                    @foreach($floorPlans as $fp)
                                        <option value="{{ $fp->id }}">{{ $fp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Booth Type</label>
                                <select name="booth_type_id" id="booth_type_id" class="form-control">
                                    <option value="">Select Type</option>
                                    @foreach($boothTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="1">Booth</option>
                                    <option value="2">Space Only</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Price *</label>
                                <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1">Available</option>
                                    <option value="2">Confirmed</option>
                                    <option value="3">Reserved</option>
                                    <option value="4">Hidden</option>
                                    <option value="5">Paid</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Additional Information -->
                        <div class="col-md-6">
                            <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Additional Information</h6>
                            
                            <div class="form-group">
                                <label>Client</label>
                                <select name="client_id" id="client_id" class="form-control">
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->company ?? $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" id="category_id" class="form-control">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Area (m²)</label>
                                <input type="number" name="area_sqm" id="area_sqm" class="form-control" step="0.01" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label>Capacity (people)</label>
                                <input type="number" name="capacity" id="capacity" class="form-control" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label>Electricity Power</label>
                                <input type="text" name="electricity_power" id="electricity_power" class="form-control" placeholder="e.g., 10A, 20A, 30A">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description & Features -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control" rows="4" placeholder="Booth description..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Features</label>
                                <textarea name="features" id="features" class="form-control" rows="4" placeholder="Booth features (one per line)..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                    </div>
                    
                    <!-- Image Upload -->
                    <div class="form-group">
                        <label>Booth Image</label>
                        <div class="image-upload-area" id="imageUploadArea" onclick="document.getElementById('booth_image').click()">
                            <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color: #667eea;"></i>
                            <p class="mb-0">Click to upload or drag and drop</p>
                            <small class="text-muted">PNG, JPG, GIF up to 5MB</small>
                        </div>
                        <input type="file" name="booth_image" id="booth_image" class="d-none" accept="image/*" onchange="previewImage(this)">
                        <div id="imagePreviewContainer" class="image-preview-container" style="display: none;">
                            <img id="imagePreview" src="" alt="Preview">
                            <button type="button" class="remove-image-btn" onclick="removeImage()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Save Booth
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal fade" id="imageViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booth Image</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="viewImageSrc" src="" alt="Booth Image" style="max-width: 100%; border-radius: 12px;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
let currentBoothId = null;

// Initialize DataTable
$(document).ready(function() {
    $('#boothsTable').DataTable({
        pageLength: 50,
        order: [[2, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0, 1, 11] }
        ]
    });
});

// Open Create Modal
function openCreateModal() {
    currentBoothId = null;
    $('#modalTitle').html('<i class="fas fa-store mr-2"></i>Create New Booth');
    $('#boothForm')[0].reset();
    $('#boothId').val('');
    $('#imagePreviewContainer').hide();
    $('#boothModal').modal('show');
}

// Edit Booth
function editBooth(id) {
    currentBoothId = id;
    $('#modalTitle').html('<i class="fas fa-edit mr-2"></i>Edit Booth');
    
    // Fetch booth data
    fetch(`/booths/${id}`)
        .then(response => response.json())
        .then(data => {
            $('#boothId').val(data.id);
            $('#booth_number').val(data.booth_number);
            $('#floor_plan_id').val(data.floor_plan_id);
            $('#booth_type_id').val(data.booth_type_id);
            $('#type').val(data.type);
            $('#price').val(data.price);
            $('#status').val(data.status);
            $('#client_id').val(data.client_id);
            $('#category_id').val(data.category_id);
            $('#area_sqm').val(data.area_sqm);
            $('#capacity').val(data.capacity);
            $('#electricity_power').val(data.electricity_power);
            $('#description').val(data.description);
            $('#features').val(data.features);
            $('#notes').val(data.notes);
            
            // Show image if exists
            if (data.booth_image) {
                $('#imagePreview').attr('src', data.booth_image);
                $('#imagePreviewContainer').show();
            } else {
                $('#imagePreviewContainer').hide();
            }
            
            $('#boothModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to load booth data', 'error');
        });
}

// View Booth
function viewBooth(id) {
    window.location.href = `/booths/${id}`;
}

// Delete Booth
function deleteBooth(id) {
    Swal.fire({
        title: 'Delete Booth?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/booths/${id}`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Preview Image
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').attr('src', e.target.result);
            $('#imagePreviewContainer').show();
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove Image
function removeImage() {
    $('#booth_image').val('');
    $('#imagePreviewContainer').hide();
}

// View Image
function viewImage(src) {
    $('#viewImageSrc').attr('src', src);
    $('#imageViewModal').modal('show');
}

// Form Submit
$('#boothForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = currentBoothId ? `/booths/${currentBoothId}` : '/booths';
    const method = currentBoothId ? 'PUT' : 'POST';
    
    // Add _method for PUT
    if (currentBoothId) {
        formData.append('_method', 'PUT');
    }
    
    showLoading();
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        redirect: 'follow'
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            hideLoading();
            if (data.success || response.ok) {
                Swal.fire('Success', currentBoothId ? 'Booth updated successfully!' : 'Booth created successfully!', 'success')
                    .then(() => {
                        window.location.reload();
                    });
            } else {
                // Handle validation errors
                let errorMsg = data.message || 'An error occurred';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        } else {
            // If HTML response (redirect), reload the page
            hideLoading();
            Swal.fire('Success', currentBoothId ? 'Booth updated successfully!' : 'Booth created successfully!', 'success')
                .then(() => {
                    window.location.reload();
                });
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        Swal.fire('Error', 'An error occurred while saving: ' + error.message, 'error');
    });
});

// Toggle Select All
function toggleSelectAll() {
    const checked = $('#selectAll').is(':checked');
    $('.booth-checkbox').prop('checked', checked);
}

// Bulk Delete
function bulkDelete() {
    const selected = $('.booth-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selected.length === 0) {
        Swal.fire('Warning', 'Please select at least one booth', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Delete Selected Booths?',
        text: `You are about to delete ${selected.length} booth(s). This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete them!'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch('/bulk/booths/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ids: selected })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    Swal.fire('Success', `${selected.length} booth(s) deleted successfully!`, 'success')
                        .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error', 'An error occurred', 'error');
            });
        }
    });
}

// Bulk Update Status
function bulkUpdateStatus() {
    const selected = $('.booth-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
    
    if (selected.length === 0) {
        Swal.fire('Warning', 'Please select at least one booth', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Update Status',
        input: 'select',
        inputOptions: {
            '1': 'Available',
            '2': 'Confirmed',
            '3': 'Reserved',
            '4': 'Hidden',
            '5': 'Paid'
        },
        inputPlaceholder: 'Select new status',
        showCancelButton: true,
        confirmButtonText: 'Update',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to select a status!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch('/bulk/booths/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    ids: selected,
                    field: 'status',
                    value: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    Swal.fire('Success', `Status updated for ${selected.length} booth(s)!`, 'success')
                        .then(() => window.location.reload());
                } else {
                    Swal.fire('Error', data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error', 'An error occurred', 'error');
            });
        }
    });
}

// Drag and drop for image
const imageUploadArea = document.getElementById('imageUploadArea');
if (imageUploadArea) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, () => {
            imageUploadArea.classList.add('dragover');
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        imageUploadArea.addEventListener(eventName, () => {
            imageUploadArea.classList.remove('dragover');
        }, false);
    });
    
    imageUploadArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            document.getElementById('booth_image').files = files;
            previewImage(document.getElementById('booth_image'));
        }
    }, false);
}
</script>
@endpush
