@extends('layouts.adminlte')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')
@section('breadcrumb', 'Payments / Create')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i>Record New Payment</h3>
        </div>
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <!-- General Error Display -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="form-group">
                    <label for="booking_id">Booking <span class="text-danger">*</span></label>
                    <select name="booking_id" id="booking_id" class="form-control @error('booking_id') is-invalid @enderror" required>
                        <option value="">Select Booking</option>
                        @foreach(\App\Models\Book::with('client')->latest()->get() as $book)
                        <option value="{{ $book->id }}" {{ old('booking_id', request('booking_id')) == $book->id ? 'selected' : '' }}>
                            #{{ $book->id }} - {{ $book->client->company ?? 'N/A' }} ({{ $book->date_book->format('Y-m-d') }})
                        </option>
                        @endforeach
                    </select>
                    @error('booking_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Select the booking this payment is for</small>
                </div>
                <div class="form-group">
                    <label for="amount">Amount ($) <span class="text-danger">*</span></label>
                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                           step="0.01" min="0" required value="{{ old('amount') }}" placeholder="0.00">
                    @error('amount')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Enter the payment amount</small>
                </div>
                <div class="form-group">
                    <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                        <option value="">Select Payment Method...</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Select how the payment was received</small>
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" 
                              placeholder="Optional: Add any additional notes about this payment...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Record Payment
                </button>
                <a href="{{ route('payments.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
