@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-book me-2"></i>Booking Details</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('books.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Bookings
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Booking Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">ID:</th>
                        <td>{{ $book->id }}</td>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <td>{{ $book->date_book->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>Type:</th>
                        <td>
                            @if($book->type == 1) Regular
                            @elseif($book->type == 2) Special
                            @elseif($book->type == 3) Temporary
                            @else {{ $book->type }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>User:</th>
                        <td>{{ $book->user->username ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Client Information</h5>
            </div>
            <div class="card-body">
                @if($book->client)
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Name:</th>
                            <td>{{ $book->client->name }}</td>
                        </tr>
                        <tr>
                            <th>Company:</th>
                            <td>{{ $book->client->company ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Position:</th>
                            <td>{{ $book->client->position ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $book->client->phone_number ?? 'N/A' }}</td>
                        </tr>
                    </table>
                @else
                    <p class="text-muted">No client information available.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Booths ({{ count($book->booths()) }})</h5>
            </div>
            <div class="card-body">
                @if(count($book->booths()) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Booth Number</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($book->booths() as $booth)
                                <tr>
                                    <td>
                                        <a href="{{ route('booths.show', $booth) }}">{{ $booth->booth_number }}</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $booth->getStatusColor() }}">
                                            {{ $booth->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($booth->price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No booths in this booking.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
