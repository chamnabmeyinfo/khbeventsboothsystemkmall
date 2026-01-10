<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Book;
use App\Models\Booth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display payments list
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking', 'client', 'user']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $payments = $query->latest()->paginate(20);

        return view('payments.index', compact('payments'));
    }

    /**
     * Create payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:book,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,online,check',
            'notes' => 'nullable|string',
        ]);

        $booking = Book::findOrFail($request->booking_id);
        $totalAmount = $booking->booths()->sum('price');

        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'client_id' => $booking->clientid,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'status' => Payment::STATUS_COMPLETED,
            'notes' => $request->notes,
            'paid_at' => now(),
            'user_id' => Auth::id(),
        ]);

        // Update booth status to paid
        foreach ($booking->booths() as $booth) {
            $booth->update(['status' => Booth::STATUS_PAID]);
        }

        return redirect()->route('payments.index')
            ->with('success', 'Payment recorded successfully');
    }

    /**
     * Generate invoice
     */
    public function invoice($id)
    {
        $payment = Payment::with(['booking', 'client', 'user'])->findOrFail($id);
        
        return view('payments.invoice', compact('payment'));
    }

    /**
     * Show payment form
     */
    public function create(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $booking = $bookingId ? Book::with('booths')->findOrFail($bookingId) : null;
        
        return view('payments.create', compact('booking'));
    }
}
