<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FinanceCategory::with('createdBy');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_categories' => FinanceCategory::count(),
            'expense_categories' => FinanceCategory::ofType(FinanceCategory::TYPE_EXPENSE)->count(),
            'revenue_categories' => FinanceCategory::ofType(FinanceCategory::TYPE_REVENUE)->count(),
            'costing_categories' => FinanceCategory::ofType(FinanceCategory::TYPE_COSTING)->count(),
            'active_categories' => FinanceCategory::active()->count(),
        ];

        return view('finance.categories.index', compact('categories', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('finance.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:expense,revenue,costing',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category = FinanceCategory::create($validated);

        return redirect()->route('finance.categories.index')
            ->with('success', 'Finance category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FinanceCategory $category)
    {
        $category->load('createdBy');
        
        // Get related records
        $expenseCount = 0;
        $revenueCount = 0;
        
        if ($category->type === FinanceCategory::TYPE_EXPENSE) {
            $expenseCount = \App\Models\Expense::where('category_id', $category->id)->count();
        } elseif ($category->type === FinanceCategory::TYPE_REVENUE) {
            $revenueCount = \App\Models\Revenue::where('category_id', $category->id)->count();
        }
        
        return view('finance.categories.show', compact('category', 'expenseCount', 'revenueCount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinanceCategory $category)
    {
        return view('finance.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FinanceCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:expense,revenue,costing',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->update($validated);

        return redirect()->route('finance.categories.index')
            ->with('success', 'Finance category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FinanceCategory $category)
    {
        // Check if category is in use
        $inUse = false;
        $usageCount = 0;
        
        if ($category->type === FinanceCategory::TYPE_EXPENSE) {
            $usageCount = \App\Models\Expense::where('category_id', $category->id)->count();
        } elseif ($category->type === FinanceCategory::TYPE_REVENUE) {
            $usageCount = \App\Models\Revenue::where('category_id', $category->id)->count();
        }
        
        if ($usageCount > 0) {
            return redirect()->route('finance.categories.index')
                ->with('error', "Cannot delete category. It is currently used by {$usageCount} " . ($category->type === FinanceCategory::TYPE_EXPENSE ? 'expense(s)' : 'revenue(s)') . ".");
        }

        $category->delete();

        return redirect()->route('finance.categories.index')
            ->with('success', 'Finance category deleted successfully.');
    }
}
