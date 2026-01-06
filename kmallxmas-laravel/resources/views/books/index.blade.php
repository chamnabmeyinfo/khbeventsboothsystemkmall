@extends('layouts.app')

@section('title', 'Bookings')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-book me-2"></i>Bookings</h2>
    </div>
    <div class="col-auto">
        <div class="btn-group">
            <a href="{{ route('books.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>New Booking
            </a>
            <a href="{{ route('export.bookings') }}" class="btn btn-success">
                <i class="fas fa-file-csv me-2"></i>Export CSV
            </a>
        </div>
    </div>
</div>

<!-- Advanced Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('books.index') }}" class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by client or user..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" 
                       placeholder="From Date" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" 
                       placeholder="To Date" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('books.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Booths</th>
                        <th>Date</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                    <tr>
                        <td>{{ $book->id }}</td>
                        <td>
                            @if($book->client)
                                {{ $book->client->company ?? $book->client->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @php
                                $boothIds = json_decode($book->boothid, true) ?? [];
                            @endphp
                            {{ count($boothIds) }} booth(s)
                        </td>
                        <td>{{ $book->date_book->format('Y-m-d H:i') }}</td>
                        <td>{{ $book->user->username ?? 'N/A' }}</td>
                        <td>
                            @if($book->type == 1) Regular
                            @elseif($book->type == 2) Special
                            @elseif($book->type == 3) Temporary
                            @else {{ $book->type }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No bookings found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $books->links() }}
        </div>
    </div>
</div>
@endsection
