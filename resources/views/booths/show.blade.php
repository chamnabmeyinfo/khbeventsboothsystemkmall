@extends('layouts.admin')

@section('title', 'Booth Details')
@section('page-title', 'Booth Details')
@section('breadcrumb', 'Booth Details')

@section('content')
<div class='looker-dashboard'>
<div class="row">
    <div class="col-md-8">
        <div class="glass-card">
            <div class="p-4 border-bottom">
                <h3 class="h5 fw-bold mb-0 text-dark-gray-gray">Booth Information</h3>
                <div class="card-tools">
                    <a href="{{ route('booths.index') }}" class="btn btn-sm btn-glass-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Booths
                    </a>
                    @auth
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('booths.edit', $booth) }}" class="btn btn-sm btn-glass-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endif
                    @endauth
                </div>
            </div>
            <div class="p-4">
                <dl class="row">
                    <dt class="col-sm-4">Booth Number:</dt>
                    <dd class="col-sm-8"><strong>{{ $booth->booth_number }}</strong></dd>

                    <dt class="col-sm-4">Status:</dt>
                    <dd class="col-sm-8">
                        <span class="badge badge-{{ $booth->getStatusColor() }}">
                            {{ $booth->getStatusLabel() }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Price:</dt>
                    <dd class="col-sm-8">${{ number_format($booth->price, 2) }}</dd>

                    @if($booth->category)
                    <dt class="col-sm-4">Category:</dt>
                    <dd class="col-sm-8">{{ $booth->category->name }}</dd>
                    @endif

                    @if($booth->subCategory)
                    <dt class="col-sm-4">Sub Category:</dt>
                    <dd class="col-sm-8">{{ $booth->subCategory->name }}</dd>
                    @endif

                    @if($booth->asset)
                    <dt class="col-sm-4">Asset:</dt>
                    <dd class="col-sm-8">{{ $booth->asset->name }}</dd>
                    @endif

                    @if($booth->boothType)
                    <dt class="col-sm-4">Booth Type:</dt>
                    <dd class="col-sm-8">{{ $booth->boothType->name }}</dd>
                    @endif

                    @if($booth->user)
                    <dt class="col-sm-4">Booked By:</dt>
                    <dd class="col-sm-8">{{ $booth->user->username }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Booth Image Gallery --}}
        <div class="glass-card">
            <div class="p-4 border-bottom">
                <h3 class="h5 fw-bold mb-0 text-dark-gray-gray">
                    <i class="fas fa-images"></i> Booth Images
                </h3>
                @auth
                @if(auth()->user()->isAdmin())
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-glass-primary" onclick="showUploadModal()">
                        <i class="fas fa-upload"></i> Upload Images
                    </button>
                </div>
                @endif
                @endauth
            </div>
            <div class="p-4">
                <div id="boothGalleryContainer">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading images...</p>
                    </div>
                </div>
            </div>
        </div>

        @if($booth->client)
        <div class="glass-card">
            <div class="p-4 border-bottom">
                <h3 class="h5 fw-bold mb-0 text-dark-gray-gray">Client Information</h3>
            </div>
            <div class="p-4">
                <dl class="row">
                    <dt class="col-sm-4">Name:</dt>
                    <dd class="col-sm-8">{{ $booth->client->name }}</dd>

                    <dt class="col-sm-4">Company:</dt>
                    <dd class="col-sm-8">{{ $booth->client->company }}</dd>

                    <dt class="col-sm-4">Position:</dt>
                    <dd class="col-sm-8">{{ $booth->client->position }}</dd>

                    <dt class="col-sm-4">Phone:</dt>
                    <dd class="col-sm-8">{{ $booth->client->phone_number }}</dd>
                </dl>

                @auth
                @if(auth()->user()->isAdmin())
                <div class="mt-3">
                    <a href="{{ route('clients.show', $booth->client->id) }}" class="btn btn-sm btn-glass-secondary">
                        <i class="fas fa-eye"></i> View Client Details
                    </a>
                </div>
                @endif
                @endauth
            </div>
        </div>
        @endif

        @if($booth->book)
        <div class="glass-card">
            <div class="p-4 border-bottom">
                <h3 class="h5 fw-bold mb-0 text-dark-gray-gray">Booking Information</h3>
            </div>
            <div class="p-4">
                <dl class="row">
                    <dt class="col-sm-4">Booking ID:</dt>
                    <dd class="col-sm-8">#{{ $booth->book->id }}</dd>

                    <dt class="col-sm-4">Booking Date:</dt>
                    <dd class="col-sm-8">{{ $booth->book->date_book ? $booth->book->date_book->format('Y-m-d H:i:s') : 'N/A' }}</dd>

                    <dt class="col-sm-4">Booking Type:</dt>
                    <dd class="col-sm-8">{{ $booth->book->type }}</dd>
                </dl>

                @auth
                @if(auth()->user()->isAdmin())
                <div class="mt-3">
                    <a href="{{ route('books.show', $booth->book->id) }}" class="btn btn-sm btn-glass-secondary">
                        <i class="fas fa-eye"></i> View Booking Details
                    </a>
                </div>
                @endif
                @endauth
            </div>
        </div>
        @endif

        {{-- Booking Timeline --}}
        @if($booth->timeline->count() > 0)
        <div class="glass-card">
            <div class="p-4 border-bottom">
                <h3 class="h5 fw-bold mb-0 text-dark-gray-gray">
                    <i class="fas fa-history"></i> Booking Timeline
                </h3>
            </div>
            <div class="p-4">
                <div class="timeline">
                    @foreach($booth->timeline as $entry)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-{{ $entry->actionColor }}">
                            <i class="fas {{ $entry->actionIcon }}"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <strong>{{ $entry->actionLabel }}</strong>
                                <small class="text-muted float-right">
                                    {{ $entry->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                            @if($entry->details)
                            <div class="timeline-body">
                                {{ $entry->details }}
                            </div>
                            @endif
                            @if($entry->amount)
                            <div class="timeline-footer">
                                <span class="badge badge-success">
                                    <i class="fas fa-dollar-sign"></i> {{ number_format($entry->amount, 2) }}
                                </span>
                            </div>
                            @endif
                            @if($entry->old_status && $entry->new_status)
                            <div class="timeline-footer">
                                <span class="badge badge-secondary">{{ $entry->old_status }}</span>
                                <i class="fas fa-arrow-right mx-1"></i>
                                <span class="badge badge-primary">{{ $entry->new_status }}</span>
                            </div>
                            @endif
                            @if($entry->user)
                            <div class="timeline-user">
                                <small class="text-muted">
                                    <i class="fas fa-user"></i> {{ $entry->user->username }}
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="glass-card">
            <div class="p-4 border-bottom">
                <h3 class="h5 fw-bold mb-0 text-dark-gray-gray">Quick Actions</h3>
            </div>
            <div class="p-4">
                <a href="{{ route('booths.index') }}" class="btn btn-block btn-glass-secondary mb-2">
                    <i class="fas fa-list"></i> All Booths
                </a>
                @auth
                @if(auth()->user()->isAdmin())
                <a href="{{ route('booths.edit', $booth) }}" class="btn btn-block btn-glass-primary mb-2">
                    <i class="fas fa-edit"></i> Edit Booth
                </a>
                @endif
                @endauth
            </div>
        </div>
    </div>
</div>

{{-- Upload Images Modal --}}
<div class="modal fade" id="uploadImagesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Booth Images</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="uploadImagesForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Images (Max 10)</label>
                        <input type="file" class="glass-input" name="images[]" id="imageFiles" multiple accept="image/*" required>
                        <small class="form-text text-muted">You can select multiple images. {{ \App\Helpers\UploadSettingsHelper::getHint('booth') }} per image.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Image Type</label>
                        <select class="glass-input" name="image_type">
                            <option value="photo">Photo</option>
                            <option value="layout">Layout Plan</option>
                            <option value="setup">Setup</option>
                            <option value="teardown">Teardown</option>
                            <option value="facility">Facility</option>
                        </select>
                    </div>
                    
                    <div id="imagePreviewContainer" class="row"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-glass-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-glass-primary" id="uploadBtn">
                        <i class="fas fa-upload"></i> Upload Images
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Image View Modal --}}
<div class="modal fade" id="imageViewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageViewTitle">Image</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <img id="viewImageElement" src="" alt="Booth Image" style="max-width: 100%; max-height: 80vh;">
                <p id="viewImageCaption" class="mt-3"></p>
            </div>
        </div>
    </div>
</div>


@push('styles')
<link rel="stylesheet" href="{{ asset('css/booths-premium-glamor.css') }}?v=1.0">
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=2.6">
<style>
    
    
    
    .kpi-card-looker {
        margin-bottom: 24px;
    }
</style>
@endpush

@push('body-class', 'ios-dashboard-mode')


@push('scripts')
<script>
const boothId = {{ $booth->id }};

// Load images on page load
document.addEventListener('DOMContentLoaded', function() {
    loadBoothImages();
});

// Load booth images
function loadBoothImages() {
    fetch(`/booths/${boothId}/images`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayGallery(data.images);
            } else {
                showError('Failed to load images');
            }
        })
        .catch(error => {
            console.error('Error loading images:', error);
            document.getElementById('boothGalleryContainer').innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-images fa-2x"></i>
                    <p class="mt-2">No images uploaded yet</p>
                </div>
            `;
        });
}

// Display gallery
function displayGallery(images) {
    const container = document.getElementById('boothGalleryContainer');
    
    if (images.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="fas fa-images fa-2x"></i>
                <p class="mt-2">No images uploaded yet</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="gallery-grid">';
    
    images.forEach(image => {
        html += `
            <div class="gallery-item">
                <div class="gallery-item-overlay">
                    ${image.is_primary ? '<span class="primary-badge">PRIMARY</span>' : ''}
                    <span class="type-badge ml-2">${image.type_label}</span>
                </div>
                <img src="${image.image_url}" alt="${image.caption || 'Booth image'}" 
                     onclick="viewImage('${image.image_url}', '${image.caption || ''}', '${image.type_label}')">
                <div class="gallery-item-actions">
                    <div class="text-dark-gray-gray small" style="flex: 1;">
                        ${image.caption ? image.caption.substring(0, 30) : 'No caption'}
                    </div>
                    @auth
                    @if(auth()->user()->isAdmin())
                    <div class="btn-group btn-group-sm">
                        ${!image.is_primary ? `<button class="btn btn-glass-primary btn-sm" onclick="setPrimary(${image.id})" title="Set as Primary">
                            <i class="fas fa-star"></i>
                        </button>` : ''}
                        <button class="btn btn-danger btn-sm" onclick="deleteImage(${image.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    @endif
                    @endauth
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Show upload modal
function showUploadModal() {
    $('#uploadImagesModal').modal('show');
    document.getElementById('uploadImagesForm').reset();
    document.getElementById('imagePreviewContainer').innerHTML = '';
}

// Preview selected images
document.getElementById('imageFiles')?.addEventListener('change', function(e) {
    const previewContainer = document.getElementById('imagePreviewContainer');
    previewContainer.innerHTML = '';
    
    const files = Array.from(e.target.files);
    files.slice(0, 10).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewContainer.innerHTML += `
                <div class="col-md-3 image-preview-item">
                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                    <small class="d-block text-center">${file.name}</small>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    });
});

// Upload images
document.getElementById('uploadImagesForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const uploadBtn = document.getElementById('uploadBtn');
    uploadBtn.disabled = true;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    
    fetch(`/booths/${boothId}/upload-gallery`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#uploadImagesModal').modal('hide');
            loadBoothImages();
            showSuccess(data.message);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to upload images');
    })
    .finally(() => {
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Images';
    });
});

// View image
function viewImage(url, caption, type) {
    document.getElementById('viewImageElement').src = url;
    document.getElementById('viewImageTitle').textContent = type;
    document.getElementById('viewImageCaption').textContent = caption || '';
    $('#imageViewModal').modal('show');
}

// Set primary image
function setPrimary(imageId) {
    if (!confirm('Set this as the primary image for this booth?')) return;
    
    fetch(`/booths/${boothId}/images/${imageId}/set-primary`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadBoothImages();
            showSuccess(data.message);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to set primary image');
    });
}

// Delete image
function deleteImage(imageId) {
    if (!confirm('Are you sure you want to delete this image? This action cannot be undone.')) return;
    
    fetch(`/booths/${boothId}/images/${imageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadBoothImages();
            showSuccess(data.message);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Failed to delete image');
    });
}

// Helper functions
function showSuccess(message) {
    alert(message); // You can replace with a better notification system
}

function showError(message) {
    alert('Error: ' + message);
}
</script>
@endpush
</div>
@endsection


<style>
    .glass-card, .looker-table, .designer-toolbar, .controls-panel {
        box-shadow: 0 10px 40px rgba(0,0,0,0.08), 0 1px 1px rgba(255,255,255,0.3) !important;
    }
    .btn-glass-primary {
        box-shadow: 0 4px 14px 0 rgba(0, 118, 255, 0.39) !important;
    }
</style>
