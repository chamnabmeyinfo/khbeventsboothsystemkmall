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

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('company', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        // Client filter
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $payments = $query->latest('paid_at')->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_payments' => Payment::count(),
            'total_amount' => Payment::where('status', Payment::STATUS_COMPLETED)->sum('amount'),
            'pending_payments' => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'failed_payments' => Payment::where('status', Payment::STATUS_FAILED)->count(),
            'today_payments' => Payment::whereDate('paid_at', today())->where('status', Payment::STATUS_COMPLETED)->sum('amount'),
            'this_month_payments' => Payment::whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->where('status', Payment::STATUS_COMPLETED)
                ->sum('amount'),
        ];

        // Get unique payment methods
        $paymentMethods = Payment::distinct()->pluck('payment_method')->filter();

        // Get clients for filter
        $clients = \App\Models\Client::orderBy('company')->get();

        return view('payments.index', compact('payments', 'stats', 'paymentMethods', 'clients'));
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

        // Update booth status to paid and send notifications
        // Use lockForUpdate to prevent race conditions
        $boothIds = json_decode($booking->boothid, true) ?? [];
        if (!empty($boothIds)) {
            $booths = Booth::whereIn('id', $boothIds)->lockForUpdate()->get();
            
            foreach ($booths as $booth) {
                $oldStatus = $booth->status;
                
                // Only update if not already paid (idempotent)
                if ($oldStatus != Booth::STATUS_PAID) {
                    $booth->update(['status' => Booth::STATUS_PAID]);
                    
                    // Send notification about payment and status change
                    try {
                        \App\Services\NotificationService::notifyPaymentReceived($booth, $booth->price ?? 0);
                        \App\Services\NotificationService::notifyBoothStatusChange($booth, $oldStatus, Booth::STATUS_PAID);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send payment notification: ' . $e->getMessage());
                    }
                }
            }
        }

        return redirect()->route('finance.payments.index')
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

    /**
     * Refund a payment
     */
    public function refund(Request $request, $id)
    {
        $payment = Payment::with(['booking', 'booking.booths'])->findOrFail($id);

        // Validate payment can be refunded
        if ($payment->status === Payment::STATUS_REFUNDED) {
            return back()->with('error', 'This payment has already been refunded.');
        }

        if ($payment->status !== Payment::STATUS_COMPLETED) {
            return back()->with('error', 'Only completed payments can be refunded.');
        }

        try {
            \DB::beginTransaction();

            // Create refund payment entry (negative amount)
            $refundPayment = Payment::create([
                'booking_id' => $payment->booking_id,
                'client_id' => $payment->client_id,
                'amount' => -$payment->amount, // Negative amount for refund
                'payment_method' => $payment->payment_method,
                'status' => Payment::STATUS_REFUNDED,
                'transaction_id' => 'REFUND-' . $payment->transaction_id ?? 'REFUND-' . $payment->id,
                'notes' => 'Refund for Payment #' . $payment->id . ($request->notes ? ': ' . $request->notes : ''),
                'paid_at' => now(),
                'user_id' => Auth::id(),
            ]);

            // Update original payment status
            $payment->update([
                'status' => Payment::STATUS_REFUNDED,
                'notes' => ($payment->notes ? $payment->notes . ' | ' : '') . 'Refunded on ' . now()->format('Y-m-d H:i:s'),
            ]);

            // Revert booth status from paid to confirmed (or reserved if no confirmation)
            // Use lock to prevent race conditions
            if ($payment->booking) {
                $boothIds = json_decode($payment->booking->boothid, true) ?? [];
                if (!empty($boothIds)) {
                    Booth::whereIn('id', $boothIds)
                        ->where('status', Booth::STATUS_PAID)
                        ->lockForUpdate()
                        ->update([
                            'status' => Booth::STATUS_CONFIRMED, // Revert to confirmed, not available
                        ]);
                }
            }

            \DB::commit();

            return redirect()->route('finance.payments.index')
                ->with('success', 'Payment refunded successfully. Refund payment #' . $refundPayment->id . ' created.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error processing refund: ' . $e->getMessage());
        }
    }

    /**
     * Void a payment (cancel before completion)
     */
    public function void(Request $request, $id)
    {
        $payment = Payment::with(['booking', 'booking.booths'])->findOrFail($id);

        // Validate payment can be voided
        if ($payment->status === Payment::STATUS_REFUNDED) {
            return back()->with('error', 'This payment has already been refunded and cannot be voided.');
        }

        if ($payment->status === Payment::STATUS_FAILED) {
            return back()->with('error', 'This payment has already failed.');
        }

        try {
            \DB::beginTransaction();

            // Store original status before update
            $originalStatus = $payment->status;
            
            // Update payment status to failed (voided)
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'notes' => ($payment->notes ? $payment->notes . ' | ' : '') . 'VOIDED on ' . now()->format('Y-m-d H:i:s') . ($request->notes ? ': ' . $request->notes : ''),
            ]);

            // Revert booth status if payment was completed (use lock to prevent race conditions)
            if ($originalStatus === Payment::STATUS_COMPLETED && $payment->booking) {
                $boothIds = json_decode($payment->booking->boothid, true) ?? [];
                if (!empty($boothIds)) {
                    Booth::whereIn('id', $boothIds)
                        ->where('status', Booth::STATUS_PAID)
                        ->lockForUpdate()
                        ->update([
                            'status' => Booth::STATUS_CONFIRMED, // Revert to confirmed
                        ]);
                }
            }

            \DB::commit();

            return redirect()->route('finance.payments.index')
                ->with('success', 'Payment voided successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error voiding payment: ' . $e->getMessage());
        }
    }
}

