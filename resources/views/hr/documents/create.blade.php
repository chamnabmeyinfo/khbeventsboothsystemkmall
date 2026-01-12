@extends('layouts.adminlte')

@section('title', 'Upload Document')

@section('content_header')
    <h1 class="m-0"><i class="fas fa-file-upload mr-2"></i>Upload Employee Document</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('hr.documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employee_id">Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-control select2" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $employeeId) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="document_type">Document Type <span class="text-danger">*</span></label>
                            <input type="text" name="document_type" id="document_type" class="form-control" 
                                   value="{{ old('document_type') }}" 
                                   placeholder="e.g., Passport, ID Card, Contract" required>
                            @error('document_type')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="document_name">Document Name <span class="text-danger">*</span></label>
                            <input type="text" name="document_name" id="document_name" class="form-control" 
                                   value="{{ old('document_name') }}" required>
                            @error('document_name')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="expiry_date">Expiry Date</label>
                            <input type="date" name="expiry_date" id="expiry_date" class="form-control" 
                                   value="{{ old('expiry_date') }}">
                            @error('expiry_date')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="file">File <span class="text-danger">*</span></label>
                    <div class="custom-file">
                        <input type="file" name="file" id="file" class="custom-file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" required>
                        <label class="custom-file-label" for="file">Choose file (PDF, Images, Word, Excel - Max 10MB)</label>
                    </div>
                    <small class="form-text text-muted">Accepted formats: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX (Max 10MB)</small>
                    @error('file')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    @error('description')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload mr-1"></i>Upload Document
                    </button>
                    <a href="{{ route('hr.documents.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.select2').select2();
        
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@stop
