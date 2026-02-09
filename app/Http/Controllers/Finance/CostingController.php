<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Costing;
use App\Models\FloorPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Costing::with(['floorPlan', 'booking', 'createdBy', 'approvedBy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('costing_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('costing_date', '<=', $request->date_to);
        }

        // Floor plan filter
        if ($request->filled('floor_plan_id')) {
            $query->where('floor_plan_id', $request->floor_plan_id);
        }

        $costings = $query->latest('costing_date')->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = [
            'total_costings' => Costing::count(),
            'total_estimated' => Costing::sum('estimated_cost') ?? 0,
            'total_actual' => Costing::sum('actual_cost') ?? 0,
            'total_variance' => (Costing::sum('actual_cost') ?? 0) - (Costing::sum('estimated_cost') ?? 0),
            'completed_count' => Costing::where('status', Costing::STATUS_COMPLETED)->count(),
        ];

        // Get floor plans for filter
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();

        return view('finance.costings.index', compact('costings', 'stats', 'floorPlans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();
        $bookings = Book::with('client')->latest('date_book')->limit(100)->get();

        return view('finance.costings.create', compact('floorPlans', 'bookings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'costing_date' => 'required|date',
            'notes' => 'nullable|string',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'booking_id' => 'nullable|exists:book,id',
            'status' => 'nullable|in:draft,approved,in_progress,completed,cancelled',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['status'] ?? Costing::STATUS_DRAFT;

        $costing = Costing::create($validated);

        return redirect()->route('finance.costings.index')
            ->with('success', 'Costing created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Costing $costing)
    {
        $costing->load(['floorPlan', 'booking', 'createdBy', 'approvedBy']);

        return view('finance.costings.show', compact('costing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Costing $costing)
    {
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();
        $bookings = Book::with('client')->latest('date_book')->limit(100)->get();

        return view('finance.costings.edit', compact('costing', 'floorPlans', 'bookings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Costing $costing)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'costing_date' => 'required|date',
            'notes' => 'nullable|string',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'booking_id' => 'nullable|exists:book,id',
            'status' => 'nullable|in:draft,approved,in_progress,completed,cancelled',
        ]);

        // Handle approval (remove isAdmin check for now - can be added later)
        if ($request->filled('approve') && $request->approve) {
            $validated['approved_by'] = Auth::id();
            $validated['approved_at'] = now();
            $validated['status'] = Costing::STATUS_APPROVED;
        }

        $costing->update($validated);

        return redirect()->route('finance.costings.index')
            ->with('success', 'Costing updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Costing $costing)
    {
        $costing->delete();

        return redirect()->route('finance.costings.index')
            ->with('success', 'Costing deleted successfully.');
    }
}
