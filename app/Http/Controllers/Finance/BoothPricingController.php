<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Booth;
use App\Models\FloorPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoothPricingController extends Controller
{
    /**
     * Display a listing of booth pricing.
     */
    public function index(Request $request)
    {
        $query = Booth::with(['floorPlan']);
        
        // Filter by floor plan if provided
        if ($request->filled('floor_plan_id')) {
            $query->where('floor_plan_id', $request->floor_plan_id);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booth_number', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        
        // Get all floor plans for filter dropdown
        $floorPlans = FloorPlan::orderBy('name')->get();
        
        // Get all booths (no pagination)
        $booths = $query->orderBy('booth_number')->get();
        
        // Organize booths by zone
        $zones = [];
        foreach ($booths as $booth) {
            $boothNumber = $booth->booth_number;
            // Extract zone from booth number (first letter(s) - can be 1-3 characters)
            $zone = '';
            if (preg_match('/^([A-Za-z]{1,3})/', $boothNumber, $matches)) {
                $zone = strtoupper($matches[1]);
            } else {
                // If no letter found, use first character or default to "OTHER"
                $zone = !empty($boothNumber) ? strtoupper(substr($boothNumber, 0, 1)) : 'OTHER';
            }
            
            if (!isset($zones[$zone])) {
                $zones[$zone] = [];
            }
            $zones[$zone][] = $booth;
        }
        // Sort zones alphabetically
        ksort($zones);
        
        // Get statistics (based on filtered query)
        $baseQuery = Booth::query();
        if ($request->filled('floor_plan_id')) {
            $baseQuery->where('floor_plan_id', $request->floor_plan_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
                $q->where('booth_number', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%');
            });
        }
        
        $stats = [
            'total_booths' => $baseQuery->count(),
            'total_value' => $baseQuery->sum('price') ?? 0,
            'average_price' => $baseQuery->avg('price') ?? 0,
            'min_price' => $baseQuery->min('price') ?? 0,
            'max_price' => $baseQuery->max('price') ?? 0,
        ];
        
        return view('finance.booth-pricing.index', compact('booths', 'floorPlans', 'stats', 'zones'));
    }

    /**
     * Show the form for editing the specified booth pricing.
     */
    public function edit($id)
    {
        $booth = Booth::with(['floorPlan'])->findOrFail($id);
        
        return view('finance.booth-pricing.edit', compact('booth'));
    }

    /**
     * Update the specified booth pricing.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booth = Booth::findOrFail($id);
        
        DB::beginTransaction();
        try {
            $booth->price = $request->price;
            if ($request->filled('notes')) {
                // Store notes in a JSON field or extend booth table
                // For now, we'll just update the price
            }
            $booth->save();
            
            DB::commit();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booth pricing updated successfully',
                    'booth' => $booth
                ]);
            }
            
            return redirect()->route('finance.booth-pricing.index')
                ->with('success', 'Booth pricing updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating booth pricing: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->withInput()
                ->with('error', 'Error updating booth pricing: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update booth pricing.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'booth_ids' => 'required|array',
            'booth_ids.*' => 'exists:booth,id',
            'price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $updated = Booth::whereIn('id', $request->booth_ids)
                ->update(['price' => $request->price]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully updated pricing for {$updated} booth(s)",
                'updated_count' => $updated
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating booth pricing: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export booth pricing data.
     */
    public function export(Request $request)
    {
        $query = Booth::with(['floorPlan']);
        
        if ($request->filled('floor_plan_id')) {
            $query->where('floor_plan_id', $request->floor_plan_id);
        }
        
        $booths = $query->orderBy('booth_number')->get();
        
        $filename = 'booth_pricing_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($booths) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, ['Booth ID', 'Booth Number', 'Floor Plan', 'Price', 'Status', 'Updated At']);
            
            // Add data rows
            foreach ($booths as $booth) {
                fputcsv($file, [
                    $booth->id,
                    $booth->booth_number,
                    $booth->floorPlan ? $booth->floorPlan->name : 'N/A',
                    number_format($booth->price ?? 0, 2),
                    $booth->status == 1 ? 'Available' : ($booth->status == 2 ? 'Booked' : 'Unavailable'),
                    $booth->updated_at ? $booth->updated_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
