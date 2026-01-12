@extends('layouts.adminlte')

@section('title', 'My Documents')

@section('content_header')
    <h1 class="m-0"><i class="fas fa-file-alt mr-2"></i>My Documents</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">My Documents</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>Type</th>
                        <th>Expiry Date</th>
                        <th>Upload Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                    <tr>
                        <td><strong>{{ $document->document_name }}</strong></td>
                        <td><span class="badge badge-info">{{ $document->document_type }}</span></td>
                        <td>
                            @if($document->expiry_date)
                                @if($document->expiry_date->isPast())
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Expired</span>
                                @elseif($document->expiry_date->diffInDays(now()) <= 30)
                                    <span class="text-warning">{{ $document->expiry_date->format('M d, Y') }}</span>
                                @else
                                    {{ $document->expiry_date->format('M d, Y') }}
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $document->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('employee.documents.download', $document) }}" class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">No documents found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($documents->hasPages())
        <div class="card-footer">{{ $documents->links() }}</div>
        @endif
    </div>
</div>
@stop
