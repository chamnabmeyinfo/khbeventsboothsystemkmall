@extends('layouts.admin')

@section('title', 'Roles & permissions')
@section('page-title', 'Roles & permissions')
@section('breadcrumb', 'Staff Management / Access')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/roles-page.css') }}?v=1.0">
<link rel="stylesheet" href="{{ asset('css/permissions-page.css') }}?v=1.0">
<link rel="stylesheet" href="{{ asset('css/staff-access.css') }}?v=1.2">
@endpush

@push('body-class', 'ios-dashboard-mode staff-access-page roles-page permissions-page')

@section('content')
@php
    $staffAccessTabQuery = request()->query();
@endphp
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Roles &amp; permissions</h1>
            <p>Switch between <strong>Roles</strong> and <strong>Permissions</strong>. Each side has its own sub-tabs (overview, lists, and layouts). Filters and sub-views are kept in the URL.</p>
        </div>
    </header>

    <ul class="nav nav-tabs staff-access-nav" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeTab === 'roles' ? 'active' : '' }}"
               id="staff-access-tab-roles"
               href="{{ route('staff.access', array_merge($staffAccessTabQuery, ['tab' => 'roles'])) }}"
               role="tab"
               aria-selected="{{ $activeTab === 'roles' ? 'true' : 'false' }}"
               aria-controls="access-pane-roles">
                <i class="fas fa-user-shield" aria-hidden="true"></i> Roles
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeTab === 'permissions' ? 'active' : '' }}"
               id="staff-access-tab-permissions"
               href="{{ route('staff.access', array_merge($staffAccessTabQuery, ['tab' => 'permissions'])) }}"
               role="tab"
               aria-selected="{{ $activeTab === 'permissions' ? 'true' : 'false' }}"
               aria-controls="access-pane-permissions">
                <i class="fas fa-key" aria-hidden="true"></i> Permissions
            </a>
        </li>
    </ul>

    <div class="tab-content staff-access-tab-content">
        <div class="tab-pane fade {{ $activeTab === 'roles' ? 'show active' : '' }}" id="access-pane-roles" role="tabpanel" aria-labelledby="staff-access-tab-roles" tabindex="0">
            @include('staff.partials.access-tab-roles')
        </div>
        <div class="tab-pane fade {{ $activeTab === 'permissions' ? 'show active' : '' }}" id="access-pane-permissions" role="tabpanel" aria-labelledby="staff-access-tab-permissions" tabindex="0">
            @include('staff.partials.access-tab-permissions')
        </div>
    </div>
</div>

<div class="modal fade" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content roles-modal-content">
            <div class="modal-header roles-modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="createRoleModalLabel">
                    <i class="fas fa-user-shield" aria-hidden="true"></i> Create new role
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createRoleForm" method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div id="createRoleError" class="alert alert-danger d-none rounded-3" role="alert"></div>

                    <div class="row g-3 mb-0">
                        <div class="col-md-6">
                            <label for="modal_role_name" class="form-label">Role name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3" id="modal_role_name" name="name" required placeholder="e.g. Sales manager" autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_role_slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control rounded-3" id="modal_role_slug" name="slug" required placeholder="e.g. sales-manager" autocomplete="off">
                            <small class="text-muted">Unique identifier (lowercase, hyphens)</small>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_role_is_active" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" id="modal_role_is_active" name="is_active" required>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_role_sort_order" class="form-label">Sort order</label>
                            <input type="number" class="form-control rounded-3" id="modal_role_sort_order" name="sort_order" value="0" min="0">
                        </div>
                        <div class="col-12">
                            <label for="modal_role_description" class="form-label">Description</label>
                            <textarea class="form-control rounded-3" id="modal_role_description" name="description" rows="3" placeholder="Optional summary of this role"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light bg-opacity-50">
                    <button type="button" class="action-btn action-btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times text-tertiary" aria-hidden="true"></i> Cancel
                    </button>
                    <button type="submit" class="action-btn action-btn-primary" id="createRoleSubmitBtn">
                        <i class="fas fa-save" aria-hidden="true"></i> Create role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showCreateRoleModal() {
    const el = document.getElementById('createRoleModal');
    if (window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(el).show();
    } else if (typeof $ !== 'undefined' && $(el).modal) {
        $(el).modal('show');
    }
    const form = document.getElementById('createRoleForm');
    if (form) form.reset();
    const err = document.getElementById('createRoleError');
    if (err) {
        err.classList.add('d-none');
        err.textContent = '';
    }
    $('#modal_role_is_active').val('1');
    $('#modal_role_sort_order').val('0');

    $('#modal_role_name').off('input.rolesSlug').on('input.rolesSlug', function() {
        const name = $(this).val();
        const slug = name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
        $('#modal_role_slug').val(slug);
    });
}

$(document).ready(function() {
    $('#createRoleForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = $('#createRoleSubmitBtn');
        const errorDiv = $('#createRoleError');
        const originalText = submitBtn.html();

        errorDiv.addClass('d-none').text('');

        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Creating…');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    const modalEl = document.getElementById('createRoleModal');
                    if (window.bootstrap && bootstrap.Modal && modalEl) {
                        bootstrap.Modal.getInstance(modalEl)?.hide();
                    } else if (typeof $ !== 'undefined') {
                        $('#createRoleModal').modal('hide');
                    }
                    form[0].reset();
                    errorDiv.addClass('d-none');

                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Role created successfully');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Role created successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(response.message || 'Role created successfully');
                    }

                    setTimeout(function() {
                        window.location.href = @json(route('staff.access', ['tab' => 'roles', 'role_sub' => $activeRoleSub]));
                    }, 1500);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the role.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const firstError = Object.values(errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                }
                errorDiv.html('<i class="fas fa-exclamation-triangle me-1"></i>' + errorMessage).removeClass('d-none');
                errorDiv[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });

    $('#createRoleModal').on('hidden.bs.modal', function() {
        $('#createRoleForm')[0].reset();
        $('#createRoleError').addClass('d-none').text('');
        $('#modal_role_is_active').val('1');
        $('#modal_role_sort_order').val('0');
        $('#modal_role_name').off('input.rolesSlug');
    });
});

function deleteRole(id, name) {
    Swal.fire({
        title: 'Delete role?',
        text: 'Are you sure you want to delete role "' + name + '"? This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch('/roles/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return Promise.resolve(null);
                }
                return response.json().catch(() => ({})).then(data => ({ ok: response.ok, data }));
            })
            .then(result => {
                hideLoading();
                if (!result) {
                    return;
                }
                const data = result.data;
                if (result.ok && data && data.success) {
                    Swal.fire('Deleted', data.message || 'Role has been deleted.', 'success')
                        .then(() => {
                            window.location.href = @json(route('staff.access', ['tab' => 'roles', 'role_sub' => $activeRoleSub]));
                        });
                } else if (data && data.error) {
                    Swal.fire('Error', data.error, 'error');
                } else {
                    Swal.fire('Error', (data && data.message) || 'Could not delete this role.', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error', 'An error occurred while deleting the role.', 'error');
                console.error('Error:', error);
            });
        }
    });
}

function deletePermission(id, name) {
    Swal.fire({
        title: 'Delete permission?',
        text: 'Are you sure you want to delete permission "' + name + '"? This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch('/permissions/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return Promise.resolve(null);
                }
                return response.json().catch(() => ({})).then(data => ({ ok: response.ok, data }));
            })
            .then(result => {
                hideLoading();
                if (!result) {
                    return;
                }
                const data = result.data;
                if (result.ok && data && data.success) {
                    Swal.fire('Deleted', data.message || 'Permission has been deleted.', 'success')
                        .then(() => {
                            window.location.href = @json(route('staff.access', ['tab' => 'permissions', 'permission_sub' => $activePermissionSub]));
                        });
                } else if (data && data.error) {
                    Swal.fire('Error', data.error, 'error');
                } else {
                    Swal.fire('Error', (data && data.message) || 'Could not delete this permission.', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error', 'An error occurred while deleting the permission.', 'error');
                console.error('Error:', error);
            });
        }
    });
}

function refreshStaffAccessPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}
</script>
@endpush
