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
                <div class="form-group">
                    <label>Booking</label>
                    <select name="booking_id" class="form-control" required>
                        <option value="">Select Booking</option>
                        @foreach(\App\Models\Book::with('client')->latest()->get() as $book)
                        <option value="{{ $book->id }}" {{ old('booking_id') == $book->id ? 'selected' : '' }}>
                            #{{ $book->id }} - {{ $book->client->company ?? 'N/A' }} ({{ $book->date_book->format('Y-m-d') }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Amount ($)</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0" required value="{{ old('amount') }}">
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                        <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
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
