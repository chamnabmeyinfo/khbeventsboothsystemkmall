@extends('layouts.app')

@section('title', 'Client Details')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-user me-2"></i>Client Details</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Clients
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Client Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">ID:</th>
                        <td>{{ $client->id }}</td>
                    </tr>
                    <tr>
                        <th>Name:</th>
                        <td>{{ $client->name }}</td>
                    </tr>
                    <tr>
                        <th>Company:</th>
                        <td>{{ $client->company ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Position:</th>
                        <td>{{ $client->position ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $client->phone_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Gender:</th>
                        <td>
                            @if($client->sex == 1) Male
                            @elseif($client->sex == 2) Female
                            @else N/A
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Booths ({{ $client->booths->count() }})</h5>
            </div>
            <div class="card-body">
                @if($client->booths->count() > 0)
                    <ul class="list-group">
                        @foreach($client->booths as $booth)
                        <li class="list-group-item">
                            <a href="{{ route('booths.show', $booth) }}">{{ $booth->booth_number }}</a>
                            <span class="badge bg-{{ $booth->getStatusColor() }} float-end">
                                {{ $booth->getStatusLabel() }}
                            </span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No booths assigned.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
