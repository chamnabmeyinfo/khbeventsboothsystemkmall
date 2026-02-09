<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Client;
use App\Models\FinanceCategory;
use App\Models\FloorPlan;
use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Revenue::with(['category', 'client', 'floorPlan', 'booking', 'createdBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('company', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('revenue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('revenue_date', '<=', $request->date_to);
        }

        // Client filter
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Floor plan filter
        if ($request->filled('floor_plan_id')) {
            $query->where('floor_plan_id', $request->floor_plan_id);
        }

        $revenues = $query->latest('revenue_date')->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_revenues' => Revenue::count(),
            'total_amount' => Revenue::where('status', Revenue::STATUS_RECEIVED)->sum('amount'),
            'pending_amount' => Revenue::where('status', Revenue::STATUS_PENDING)->sum('amount'),
            'confirmed_amount' => Revenue::where('status', Revenue::STATUS_CONFIRMED)->sum('amount'),
            'this_month_amount' => Revenue::whereMonth('revenue_date', now()->month)
                ->whereYear('revenue_date', now()->year)
                ->where('status', Revenue::STATUS_RECEIVED)
                ->sum('amount'),
        ];

        // Get categories for filter
        $categories = FinanceCategory::ofType(FinanceCategory::TYPE_REVENUE)->active()->orderBy('name')->get();

        // Get clients for filter
        $clients = Client::orderBy('company')->get();

        // Get floor plans for filter
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();

        // Get unique payment methods
        $paymentMethods = Revenue::distinct()->pluck('payment_method')->filter();

        return view('finance.revenues.index', compact('revenues', 'stats', 'categories', 'clients', 'floorPlans', 'paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = FinanceCategory::ofType(FinanceCategory::TYPE_REVENUE)->active()->orderBy('name')->get();
        $clients = Client::orderBy('company')->get();
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();
        $bookings = Book::with('client')->latest('date_book')->limit(100)->get();

        return view('finance.revenues.create', compact('categories', 'clients', 'floorPlans', 'bookings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:finance_categories,id',
            'revenue_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'reference_number' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:client,id',
            'notes' => 'nullable|string',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'booking_id' => 'nullable|exists:book,id',
            'status' => 'nullable|in:pending,confirmed,received,cancelled',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['status'] ?? Revenue::STATUS_PENDING;

        $revenue = Revenue::create($validated);

        return redirect()->route('finance.revenues.index')
            ->with('success', 'Revenue created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Revenue $revenue)
    {
        $revenue->load(['category', 'client', 'floorPlan', 'booking', 'createdBy']);

        return view('finance.revenues.show', compact('revenue'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Revenue $revenue)
    {
        $categories = FinanceCategory::ofType(FinanceCategory::TYPE_REVENUE)->active()->orderBy('name')->get();
        $clients = Client::orderBy('company')->get();
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();
        $bookings = Book::with('client')->latest('date_book')->limit(100)->get();

        return view('finance.revenues.edit', compact('revenue', 'categories', 'clients', 'floorPlans', 'bookings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Revenue $revenue)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:finance_categories,id',
            'revenue_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'reference_number' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:client,id',
            'notes' => 'nullable|string',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'booking_id' => 'nullable|exists:book,id',
            'status' => 'nullable|in:pending,confirmed,received,cancelled',
        ]);

        $revenue->update($validated);

        return redirect()->route('finance.revenues.index')
            ->with('success', 'Revenue updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Revenue $revenue)
    {
        $revenue->delete();

        return redirect()->route('finance.revenues.index')
            ->with('success', 'Revenue deleted successfully.');
    }
}
