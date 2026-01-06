@extends('layouts.app')

@section('title', 'My Booths')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-calendar-check me-2"></i>My Booths</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('booths.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Floor Plan
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Booth Number</th>
                        <th>Client</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($booths as $booth)
                    <tr>
                        <td>{{ $booth->booth_number }}</td>
                        <td>
                            @if($booth->client)
                                {{ $booth->client->company ?? $booth->client->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($booth->category)
                                {{ $booth->category->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $booth->getStatusColor() }}">
                                {{ $booth->getStatusLabel() }}
                            </span>
                        </td>
                        <td>{{ number_format($booth->price, 2) }}</td>
                        <td>
                            <a href="{{ route('booths.show', $booth) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">You don't have any booths assigned yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
