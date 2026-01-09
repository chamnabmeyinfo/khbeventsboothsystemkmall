@extends('layouts.app')

@section('title', 'Create Booking')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-book-plus me-2"></i>Create New Booking</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('books.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Bookings
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('books.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="clientid" class="form-label">Client <span class="text-danger">*</span></label>
                    <select class="form-control @error('clientid') is-invalid @enderror" id="clientid" name="clientid" required>
                        <option value="">Select a client...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('clientid') == $client->id ? 'selected' : '' }}>
                                {{ $client->company ?? $client->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('clientid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="date_book" class="form-label">Booking Date <span class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control @error('date_book') is-invalid @enderror" 
                           id="date_book" name="date_book" value="{{ old('date_book', now()->format('Y-m-d\TH:i')) }}" required>
                    @error('date_book')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="booth_ids" class="form-label">Booths <span class="text-danger">*</span></label>
                    <select class="form-control @error('booth_ids') is-invalid @enderror" 
                            id="booth_ids" name="booth_ids[]" multiple required size="10">
                        @foreach($booths as $booth)
                            <option value="{{ $booth->id }}" {{ in_array($booth->id, old('booth_ids', [])) ? 'selected' : '' }}>
                                {{ $booth->booth_number }} - {{ $booth->getStatusLabel() }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple booths.</small>
                    @error('booth_ids')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Booking Type</label>
                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                        <option value="1" {{ old('type', 1) == 1 ? 'selected' : '' }}>Regular</option>
                        <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>Special</option>
                        <option value="3" {{ old('type') == 3 ? 'selected' : '' }}>Temporary</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Booking
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
