<?php

namespace App\Http\Controllers;

use App\Models\ZoneSetting;
use App\Models\FloorPlan;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    /**
     * Display a listing of zones
     */
    public function index(Request $request)
    {
        $query = ZoneSetting::with('floorPlan');
        
        // Filter by floor plan
        if ($request->filled('floor_plan_id')) {
            $query->where('floor_plan_id', $request->floor_plan_id);
        }
        
        // Search by zone name
        if ($request->filled('search')) {
            $query->where('zone_name', 'like', '%' . $request->search . '%');
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'zone_name');
        $sortDir = $request->get('sort_dir', 'asc');
        
        if (in_array($sortBy, ['zone_name', 'price', 'created_at'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('zone_name', 'asc');
        }
        
        $zones = $query->paginate(20);
        $floorPlans = FloorPlan::orderBy('name')->get();
        
        return view('zones.index', compact('zones', 'floorPlans', 'sortBy', 'sortDir'));
    }

    /**
     * Show the form for creating a new zone
     */
    public function create()
    {
        $floorPlans = FloorPlan::orderBy('name')->get();
        return view('zones.create', compact('floorPlans'));
    }

    /**
     * Store a newly created zone
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'zone_name' => 'required|string|max:255',
            'width' => 'nullable|integer|min:1|max:1000',
            'height' => 'nullable|integer|min:1|max:1000',
            'rotation' => 'nullable|integer|min:-360|max:360',
            'z_index' => 'nullable|integer|min:0|max:1000',
            'border_radius' => 'nullable|numeric|min:0|max:100',
            'border_width' => 'nullable|numeric|min:0|max:50',
            'opacity' => 'nullable|numeric|min:0|max:1',
            'price' => 'nullable|numeric|min:0',
            'zone_about' => 'nullable|string|max:1000',
            'background_color' => 'nullable|string|max:50',
            'border_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50',
            'font_weight' => 'nullable|string|max:50',
            'font_family' => 'nullable|string|max:100',
            'text_align' => 'nullable|string|in:left,center,right',
            'box_shadow' => 'nullable|string|max:255',
        ]);
        
        // Check for duplicate zone name in same floor plan
        $existing = ZoneSetting::where('zone_name', $validated['zone_name'])
            ->where('floor_plan_id', $validated['floor_plan_id'] ?? null)
            ->first();
            
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['zone_name' => 'A zone with this name already exists for this floor plan.']);
        }
        
        $zone = ZoneSetting::create($validated);
        
        return redirect()->route('zones.index')
            ->with('success', 'Zone created successfully.');
    }

    /**
     * Display the specified zone
     */
    public function show(ZoneSetting $zone)
    {
        $zone->load('floorPlan');
        return view('zones.show', compact('zone'));
    }

    /**
     * Show the form for editing the specified zone
     */
    public function edit(ZoneSetting $zone)
    {
        $zone->load('floorPlan');
        $floorPlans = FloorPlan::orderBy('name')->get();
        return view('zones.edit', compact('zone', 'floorPlans'));
    }

    /**
     * Update the specified zone
     */
    public function update(Request $request, ZoneSetting $zone)
    {
        $validated = $request->validate([
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'zone_name' => 'required|string|max:255',
            'width' => 'nullable|integer|min:1|max:1000',
            'height' => 'nullable|integer|min:1|max:1000',
            'rotation' => 'nullable|integer|min:-360|max:360',
            'z_index' => 'nullable|integer|min:0|max:1000',
            'border_radius' => 'nullable|numeric|min:0|max:100',
            'border_width' => 'nullable|numeric|min:0|max:50',
            'opacity' => 'nullable|numeric|min:0|max:1',
            'price' => 'nullable|numeric|min:0',
            'zone_about' => 'nullable|string|max:1000',
            'background_color' => 'nullable|string|max:50',
            'border_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50',
            'font_weight' => 'nullable|string|max:50',
            'font_family' => 'nullable|string|max:100',
            'text_align' => 'nullable|string|in:left,center,right',
            'box_shadow' => 'nullable|string|max:255',
        ]);
        
        // Check for duplicate zone name in same floor plan (excluding current zone)
        $existing = ZoneSetting::where('zone_name', $validated['zone_name'])
            ->where('floor_plan_id', $validated['floor_plan_id'] ?? null)
            ->where('id', '!=', $zone->id)
            ->first();
            
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['zone_name' => 'A zone with this name already exists for this floor plan.']);
        }
        
        $zone->update($validated);
        
        return redirect()->route('zones.index')
            ->with('success', 'Zone updated successfully.');
    }

    /**
     * Remove the specified zone
     */
    public function destroy(ZoneSetting $zone)
    {
        $zoneName = $zone->zone_name;
        $zone->delete();
        
        return redirect()->route('zones.index')
            ->with('success', "Zone '{$zoneName}' deleted successfully.");
    }
}
