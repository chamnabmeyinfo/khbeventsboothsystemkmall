/**
 * Universal Image Upload Handler
 * Handles avatar and cover image uploads with preview, progress, and error handling
 */

(function() {
    'use strict';

    // Initialize image upload components
    document.addEventListener('DOMContentLoaded', function() {
        initializeImageUploads();
    });

    function initializeImageUploads() {
        // Handle all image file inputs
        document.querySelectorAll('.image-file-input').forEach(function(input) {
            const uploadArea = input.closest('.upload-area');
            const previewContainer = uploadArea.querySelector('.preview-container');
            const previewImage = uploadArea.querySelector('.preview-image, img[id^="previewImage"]');
            const uploadProgress = uploadArea.querySelector('.upload-progress');
            const progressBar = uploadProgress ? uploadProgress.querySelector('.progress-bar') : null;
            const uploadActions = uploadArea.parentElement.querySelector('.upload-actions');
            const currentPreview = uploadArea.parentElement.querySelector('.current-image-preview');
            
            // Click to upload
            uploadArea.addEventListener('click', function(e) {
                if (!e.target.closest('.remove-image-btn') && !e.target.closest('.cancel-upload-btn')) {
                    input.click();
                }
            });

            // Drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    handleFileSelect(input, previewContainer, previewImage, uploadProgress, progressBar, uploadActions);
                }
            });

            // File selection
            input.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFileSelect(input, previewContainer, previewImage, uploadProgress, progressBar, uploadActions);
                }
            });
        });

        // Handle remove image buttons
        document.querySelectorAll('.remove-image-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                removeImage(this);
            });
        });

        // Handle cancel upload buttons
        document.querySelectorAll('.cancel-upload-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                cancelUpload(this);
            });
        });

        // Handle save image buttons
        document.querySelectorAll('.save-image-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                saveImage(this);
            });
        });
    }

    function handleFileSelect(input, previewContainer, previewImage, uploadProgress, progressBar, uploadActions) {
        const file = input.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.match('image.*')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please select an image file (JPEG, PNG, GIF)',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        // Validate file size (5MB for avatar, 10MB for cover)
        const maxSize = input.dataset.type === 'avatar' ? 5 * 1024 * 1024 : 10 * 1024 * 1024;
        if (file.size > maxSize) {
            const maxSizeMB = maxSize / (1024 * 1024);
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: `Image size must be less than ${maxSizeMB}MB`,
                confirmButtonColor: '#667eea'
            });
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewImage) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            if (uploadActions) {
                uploadActions.style.display = 'flex';
            }
        };
        reader.readAsDataURL(file);
    }

    function removeImage(btn) {
        const component = btn.closest('.image-upload-component');
        const entityType = component.dataset.entityType;
        const entityId = component.dataset.entityId;
        const type = component.dataset.type;

        Swal.fire({
            title: 'Remove Image?',
            text: `Are you sure you want to remove this ${type}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                const url = type === 'avatar' ? '/images/avatar/remove' : '/images/cover/remove';
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        entity_type: entityType,
                        entity_id: entityId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        toastr.success(data.message || 'Image removed successfully');
                        location.reload();
                    } else {
                        toastr.error(data.message || 'Error removing image');
                    }
                })
                .catch(error => {
                    hideLoading();
                    toastr.error('Error removing image: ' + error.message);
                    console.error('Error:', error);
                });
            }
        });
    }

    function cancelUpload(btn) {
        const component = btn.closest('.image-upload-component');
        const previewContainer = component.querySelector('.preview-container');
        const uploadActions = component.querySelector('.upload-actions');
        const fileInput = component.querySelector('.image-file-input');
        const uploadProgress = component.querySelector('.upload-progress');
        const progressBar = uploadProgress ? uploadProgress.querySelector('.progress-bar') : null;

        // Reset everything
        if (fileInput) fileInput.value = '';
        if (previewContainer) previewContainer.style.display = 'none';
        if (uploadActions) uploadActions.style.display = 'none';
        if (uploadProgress) uploadProgress.style.display = 'none';
        if (progressBar) {
            progressBar.style.width = '0%';
            progressBar.setAttribute('aria-valuenow', '0');
        }
    }

    function saveImage(btn) {
        const component = btn.closest('.image-upload-component');
        const fileInput = component.querySelector('.image-file-input');
        const entityType = component.dataset.entityType;
        const entityId = component.dataset.entityId;
        const type = component.dataset.type;

        if (!fileInput || !fileInput.files || !fileInput.files[0]) {
            Swal.fire({
                icon: 'warning',
                title: 'No File Selected',
                text: 'Please select an image file first',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        const file = fileInput.files[0];
        const formData = new FormData();
        formData.append(type === 'avatar' ? 'avatar' : 'cover', file);
        formData.append('entity_type', entityType);
        formData.append('entity_id', entityId);

        const uploadProgress = component.querySelector('.upload-progress');
        const progressBar = uploadProgress ? uploadProgress.querySelector('.progress-bar') : null;
        const saveBtn = btn;
        const originalText = saveBtn.innerHTML;

        // Show progress
        if (uploadProgress) uploadProgress.style.display = 'block';
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Uploading...';

        const url = type === 'avatar' ? '/images/avatar/upload' : '/images/cover/upload';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
            if (uploadProgress) uploadProgress.style.display = 'none';
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', '0');
            }

            if (data.success) {
                toastr.success(data.message || 'Image uploaded successfully');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                toastr.error(data.message || 'Error uploading image');
            }
        })
        .catch(error => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
            if (uploadProgress) uploadProgress.style.display = 'none';
            toastr.error('Error uploading image: ' + error.message);
            console.error('Error:', error);
        });
    }

    // Global helper functions
    window.showLoading = function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.add('active');
        }
    };

    window.hideLoading = function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.remove('active');
        }
    };
})();
