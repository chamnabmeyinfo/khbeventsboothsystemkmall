@extends('layouts.adminlte')

@section('title', 'Document Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-file-alt mr-2"></i>Document Details</h1>
        <div>
            <a href="{{ route('hr.documents.download', $document) }}" class="btn btn-success">
                <i class="fas fa-download mr-1"></i>Download
            </a>
            @if(auth()->user()->hasPermission('hr.documents.edit'))
            <a href="{{ route('hr.documents.edit', $document) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
            @endif
            @if(auth()->user()->hasPermission('hr.documents.delete'))
            <form action="{{ route('hr.documents.destroy', $document) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Delete">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.documents.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Document Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Document Name</th>
                            <td><strong>{{ $document->document_name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Document Type</th>
                            <td><span class="badge badge-info">{{ $document->document_type }}</span></td>
                        </tr>
                        <tr>
                            <th>Employee</th>
                            <td>
                                <a href="{{ route('hr.employees.show', $document->employee) }}">
                                    {{ $document->employee->full_name }} ({{ $document->employee->employee_code }})
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Expiry Date</th>
                            <td>
                                @if($document->expiry_date)
                                    @if($document->expiry_date->isPast())
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Expired on {{ $document->expiry_date->format('M d, Y') }}</span>
                                    @elseif($document->expiry_date->diffInDays(now()) <= 30)
                                        <span class="text-warning"><i class="fas fa-exclamation-circle"></i> Expires on {{ $document->expiry_date->format('M d, Y') }} ({{ $document->expiry_date->diffForHumans() }})</span>
                                    @else
                                        {{ $document->expiry_date->format('M d, Y') }}
                                    @endif
                                @else
                                    <span class="text-muted">No expiry date</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>File Size</th>
                            <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                        </tr>
                        <tr>
                            <th>File Type</th>
                            <td>{{ $document->mime_type }}</td>
                        </tr>
                        <tr>
                            <th>Uploaded By</th>
                            <td>{{ $document->uploader->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Upload Date</th>
                            <td>{{ $document->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @if($document->description)
                        <tr>
                            <th>Description</th>
                            <td>{{ $document->description }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('hr.documents.download', $document) }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-download mr-1"></i>Download Document
                    </a>
                    @if(auth()->user()->hasPermission('hr.documents.edit'))
                    <a href="{{ route('hr.documents.edit', $document) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit mr-1"></i>Edit Document
                    </a>
                    @endif
                    <a href="{{ route('hr.employees.show', $document->employee) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-user mr-1"></i>View Employee
                    </a>
                    <a href="{{ route('hr.documents.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
