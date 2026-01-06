@extends('layouts.adminlte')

@section('title', 'Booth Details')
@section('page-title', 'Booth Details')
@section('breadcrumb', 'Booth Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booth Information</h3>
                <div class="card-tools">
                    <a href="{{ route('booths.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Booths
                    </a>
                    @auth
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('booths.edit', $booth) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endif
                    @endauth
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Booth Number:</dt>
                    <dd class="col-sm-8"><strong>{{ $booth->booth_number }}</strong></dd>

                    <dt class="col-sm-4">Status:</dt>
                    <dd class="col-sm-8">
                        <span class="badge badge-{{ $booth->getStatusColor() }}">
                            {{ $booth->getStatusLabel() }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Price:</dt>
                    <dd class="col-sm-8">${{ number_format($booth->price, 2) }}</dd>

                    @if($booth->category)
                    <dt class="col-sm-4">Category:</dt>
                    <dd class="col-sm-8">{{ $booth->category->name }}</dd>
                    @endif

                    @if($booth->subCategory)
                    <dt class="col-sm-4">Sub Category:</dt>
                    <dd class="col-sm-8">{{ $booth->subCategory->name }}</dd>
                    @endif

                    @if($booth->asset)
                    <dt class="col-sm-4">Asset:</dt>
                    <dd class="col-sm-8">{{ $booth->asset->name }}</dd>
                    @endif

                    @if($booth->boothType)
                    <dt class="col-sm-4">Booth Type:</dt>
                    <dd class="col-sm-8">{{ $booth->boothType->name }}</dd>
                    @endif

                    @if($booth->user)
                    <dt class="col-sm-4">Booked By:</dt>
                    <dd class="col-sm-8">{{ $booth->user->username }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        @if($booth->client)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Client Information</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Name:</dt>
                    <dd class="col-sm-8">{{ $booth->client->name }}</dd>

                    <dt class="col-sm-4">Company:</dt>
                    <dd class="col-sm-8">{{ $booth->client->company }}</dd>

                    <dt class="col-sm-4">Position:</dt>
                    <dd class="col-sm-8">{{ $booth->client->position }}</dd>

                    <dt class="col-sm-4">Phone:</dt>
                    <dd class="col-sm-8">{{ $booth->client->phone_number }}</dd>
                </dl>

                @auth
                @if(auth()->user()->isAdmin())
                <div class="mt-3">
                    <a href="{{ route('clients.show', $booth->client->id) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i> View Client Details
                    </a>
                </div>
                @endif
                @endauth
            </div>
        </div>
        @endif

        @if($booth->book)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking Information</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Booking ID:</dt>
                    <dd class="col-sm-8">#{{ $booth->book->id }}</dd>

                    <dt class="col-sm-4">Booking Date:</dt>
                    <dd class="col-sm-8">{{ $booth->book->date_book ? $booth->book->date_book->format('Y-m-d H:i:s') : 'N/A' }}</dd>

                    <dt class="col-sm-4">Booking Type:</dt>
                    <dd class="col-sm-8">{{ $booth->book->type }}</dd>
                </dl>

                @auth
                @if(auth()->user()->isAdmin())
                <div class="mt-3">
                    <a href="{{ route('books.show', $booth->book->id) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i> View Booking Details
                    </a>
                </div>
                @endif
                @endauth
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <a href="{{ route('booths.index') }}" class="btn btn-block btn-secondary mb-2">
                    <i class="fas fa-list"></i> All Booths
                </a>
                @auth
                @if(auth()->user()->isAdmin())
                <a href="{{ route('booths.edit', $booth) }}" class="btn btn-block btn-primary mb-2">
                    <i class="fas fa-edit"></i> Edit Booth
                </a>
                @endif
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
