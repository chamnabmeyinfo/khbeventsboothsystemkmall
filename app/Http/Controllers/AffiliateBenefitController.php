<?php

namespace App\Http\Controllers;

use App\Models\AffiliateBenefit;
use App\Models\FloorPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateBenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check permissions
        if (! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->hasPermission('affiliates.manage'))) {
            abort(403, 'Unauthorized access');
        }

        $query = AffiliateBenefit::with(['floorPlan', 'user', 'creator']);

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $benefits = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $types = [
            AffiliateBenefit::TYPE_COMMISSION => 'Commission',
            AffiliateBenefit::TYPE_BONUS => 'Bonus',
            AffiliateBenefit::TYPE_INCENTIVE => 'Incentive',
            AffiliateBenefit::TYPE_REWARD => 'Reward',
        ];

        return view('affiliates.benefits.index', compact('benefits', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->hasPermission('affiliates.manage'))) {
            abort(403, 'Unauthorized access');
        }

        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();
        $users = User::where('status', 1)->orderBy('username')->get();

        return view('affiliates.benefits.create', compact('floorPlans', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->hasPermission('affiliates.manage'))) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:commission,bonus,incentive,reward',
            'calculation_method' => 'required|in:percentage,fixed_amount,tiered_percentage,tiered_amount',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'target_revenue' => 'nullable|numeric|min:0',
            'target_bookings' => 'nullable|integer|min:0',
            'target_clients' => 'nullable|integer|min:0',
            'tier_structure' => 'nullable|json',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'user_id' => 'nullable|exists:user,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'description' => 'nullable|string',
            'min_revenue' => 'nullable|numeric|min:0',
            'max_benefit' => 'nullable|numeric|min:0',
        ]);

        // Validate based on calculation method
        if ($validated['calculation_method'] === 'percentage' && ! $validated['percentage']) {
            return back()->withErrors(['percentage' => 'Percentage is required for percentage-based benefits.'])->withInput();
        }

        if ($validated['calculation_method'] === 'fixed_amount' && ! $validated['fixed_amount']) {
            return back()->withErrors(['fixed_amount' => 'Fixed amount is required for fixed amount benefits.'])->withInput();
        }

        if (in_array($validated['calculation_method'], ['tiered_percentage', 'tiered_amount']) && ! $validated['tier_structure']) {
            return back()->withErrors(['tier_structure' => 'Tier structure is required for tiered benefits.'])->withInput();
        }

        // Parse tier structure if provided
        if ($validated['tier_structure']) {
            $validated['tier_structure'] = json_decode($validated['tier_structure'], true);
        }

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        AffiliateBenefit::create($validated);

        return redirect()->route('affiliates.benefits.index')
            ->with('success', 'Benefit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AffiliateBenefit $benefit)
    {
        if (! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->hasPermission('affiliates.manage'))) {
            abort(403, 'Unauthorized access');
        }

        $benefit->load(['floorPlan', 'user', 'creator']);

        return view('affiliates.benefits.show', compact('benefit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AffiliateBenefit $benefit)
    {
        if (! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->hasPermission('affiliates.manage'))) {
            abort(403, 'Unauthorized access');
        }

        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();
        $users = User::where('status', 1)->orderBy('username')->get();

        return view('affiliates.benefits.edit', compact('benefit', 'floorPlans', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AffiliateBenefit $benefit)
    {
        if (! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->hasPermission('affiliates.manage'))) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:commission,bonus,incentive,reward',
            'calculation_method' => 'required|in:percentage,fixed_amount,tiered_percentage,tiered_amount',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'fixed_amount' => 'nullable|numeric|min:0',
            'target_revenue' => 'nullable|numeric|min:0',
            'target_bookings' => 'nullable|integer|min:0',
            'target_clients' => 'nullable|integer|min:0',
            'tier_structure' => 'nullable|json',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'user_id' => 'nullable|exists:user,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'description' => 'nullable|string',
            'min_revenue' => 'nullable|numeric|min:0',
            'max_benefit' => 'nullable|numeric|min:0',
        ]);

        // Validate based on calculation method
        if ($validated['calculation_method'] === 'percentage' && ! $validated['percentage']) {
            return back()->withErrors(['percentage' => 'Percentage is required for percentage-based benefits.'])->withInput();
        }

        if ($validated['calculation_method'] === 'fixed_amount' && ! $validated['fixed_amount']) {
            return back()->withErrors(['fixed_amount' => 'Fixed amount is required for fixed amount benefits.'])->withInput();
        }

        if (in_array($validated['calculation_method'], ['tiered_percentage', 'tiered_amount']) && ! $validated['tier_structure']) {
            return back()->withErrors(['tier_structure' => 'Tier structure is required for tiered benefits.'])->withInput();
        }

        // Parse tier structure if provided
        if ($validated['tier_structure']) {
            $validated['tier_structure'] = json_decode($validated['tier_structure'], true);
        }

        $validated['is_active'] = $request->has('is_active');

        $benefit->update($validated);

        return redirect()->route('affiliates.benefits.index')
            ->with('success', 'Benefit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AffiliateBenefit $benefit)
    {
        if (! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->hasPermission('affiliates.manage'))) {
            abort(403, 'Unauthorized access');
        }

        $benefit->delete();

        return redirect()->route('affiliates.benefits.index')
            ->with('success', 'Benefit deleted successfully.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        if (! Auth::check() || (! Auth::user()->isAdmin() && ! Auth::user()->hasPermission('affiliates.manage'))) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $benefit = AffiliateBenefit::findOrFail($id);
        $benefit->is_active = ! $benefit->is_active;
        $benefit->save();

        return response()->json([
            'success' => true,
            'is_active' => $benefit->is_active,
            'message' => 'Status updated successfully.',
        ]);
    }
}
