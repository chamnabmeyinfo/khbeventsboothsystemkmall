@extends('layouts.admin')
@push('body-class', 'ios-dashboard-mode')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/booths-premium-glamor.css') }}?v=1.0">
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.4">
<style>
    .looker-dashboard {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 2rem !important;
    }
</style>
@endpush

@section('title', 'My Booths')

@section('content')
<div class='looker-dashboard'>
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-calendar-check me-2"></i>My Booths</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('booths.index') }}" class="btn btn-glass-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Floor Plan
        </a>
    </div>
</div>

<div class="glass-card">
    <div class="p-4">
        <div class="looker-table-container">
            <table class="looker-table table-striped table-hover">
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
                            <a href="{{ route('booths.show', $booth) }}" class="btn btn-sm btn-glass-secondary">
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
