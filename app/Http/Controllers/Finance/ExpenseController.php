<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FinanceCategory;
use App\Models\FloorPlan;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'floorPlan', 'booking', 'createdBy', 'approvedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhere('amount', 'like', "%{$search}%");
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
            $query->whereDate('expense_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        // Floor plan filter
        if ($request->filled('floor_plan_id')) {
            $query->where('floor_plan_id', $request->floor_plan_id);
        }

        $expenses = $query->latest('expense_date')->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_expenses' => Expense::count(),
            'total_amount' => Expense::where('status', Expense::STATUS_PAID)->sum('amount'),
            'pending_amount' => Expense::where('status', Expense::STATUS_PENDING)->sum('amount'),
            'approved_amount' => Expense::where('status', Expense::STATUS_APPROVED)->sum('amount'),
            'this_month_amount' => Expense::whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->where('status', Expense::STATUS_PAID)
                ->sum('amount'),
        ];

        // Get categories for filter
        $categories = FinanceCategory::ofType(FinanceCategory::TYPE_EXPENSE)->active()->orderBy('name')->get();

        // Get floor plans for filter
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();

        // Get unique payment methods
        $paymentMethods = Expense::distinct()->pluck('payment_method')->filter();

        return view('finance.expenses.index', compact('expenses', 'stats', 'categories', 'floorPlans', 'paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = FinanceCategory::ofType(FinanceCategory::TYPE_EXPENSE)->active()->orderBy('name')->get();
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();
        $bookings = Book::with('client')->latest('date_book')->limit(100)->get(); // Limit for performance

        return view('finance.expenses.create', compact('categories', 'floorPlans', 'bookings'));
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
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'reference_number' => 'nullable|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'booking_id' => 'nullable|exists:book,id',
            'status' => 'nullable|in:pending,approved,paid,cancelled',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['status'] ?? Expense::STATUS_PENDING;

        $expense = Expense::create($validated);

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $expense->load(['category', 'floorPlan', 'booking', 'createdBy', 'approvedBy']);
        return view('finance.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $categories = FinanceCategory::ofType(FinanceCategory::TYPE_EXPENSE)->active()->orderBy('name')->get();
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();
        $bookings = Book::with('client')->latest('date_book')->limit(100)->get();

        return view('finance.expenses.edit', compact('expense', 'categories', 'floorPlans', 'bookings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:finance_categories,id',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'reference_number' => 'nullable|string|max:255',
            'vendor_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'booking_id' => 'nullable|exists:book,id',
            'status' => 'nullable|in:pending,approved,paid,cancelled',
        ]);

        // Handle approval (remove isAdmin check for now - can be added later)
        if ($request->filled('approve') && $request->approve) {
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
            $validated['status'] = Expense::STATUS_APPROVED;
        }

        $expense->update($validated);

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
