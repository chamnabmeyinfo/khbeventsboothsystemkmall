<?php

namespace App\Http\Controllers;

use App\Models\FloorPlan;
use App\Models\Event;
use App\Models\Booth;
use App\Models\CanvasSetting;
use App\Models\ZoneSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class FloorPlanController extends Controller
{
    /**
     * Display a listing of floor plans
     */
    public function index(Request $request)
    {
        try {
            // Eager load relationships safely (don't eager load event if table doesn't exist)
            $with = ['createdBy'];
            // Note: We don't eager load 'event' to avoid errors if table doesn't exist
            
            $query = FloorPlan::with($with)->withCount('booths');

            // Filter by event
            if ($request->filled('event_id')) {
                $query->where('event_id', $request->event_id);
            }

            // Filter by active status
            if ($request->filled('is_active')) {
                $query->where('is_active', $request->is_active);
            } else {
                $query->where('is_active', true); // Default to active only
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('project_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $floorPlans = $query->latest('created_at')->paginate(20)->withQueryString();
            
            // Load additional data for each floor plan (canvas settings, zone count)
            $floorPlans->getCollection()->transform(function($floorPlan) {
                // Get zone settings count for this floor plan
                try {
                    $floorPlan->zone_count = ZoneSetting::where('floor_plan_id', $floorPlan->id)->count();
                } catch (\Exception $e) {
                    $floorPlan->zone_count = 0;
                }
                
                // Get canvas settings for this floor plan
                try {
                    $floorPlan->canvas_settings = CanvasSetting::getForFloorPlan($floorPlan->id);
                } catch (\Exception $e) {
                    $floorPlan->canvas_settings = null;
                }
                
                return $floorPlan;
            });
            
            // Get events if table exists - check table existence first, then query safely
            $events = collect([]);
            try {
                // First check if table exists using SHOW TABLES (doesn't query the events table itself)
                $tableCheck = DB::select("SHOW TABLES LIKE 'events'");
                if (!empty($tableCheck)) {
                    // Table exists - query it safely
                    try {
                        $eventsData = DB::select('SELECT * FROM events WHERE status = 1 ORDER BY title ASC');
                        if (is_array($eventsData)) {
                            $events = collect($eventsData);
                        }
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Query failed even though table exists - return empty
                        $events = collect([]);
                    } catch (\Exception $e) {
                        // Any other error - return empty
                        $events = collect([]);
                    }
                }
            } catch (\Illuminate\Database\QueryException $e) {
                // SHOW TABLES failed or events table doesn't exist - return empty (expected)
                $events = collect([]);
            } catch (\PDOException $e) {
                // PDO exceptions - return empty
                $events = collect([]);
            } catch (\Exception $e) {
                // Any other exception - return empty
                $events = collect([]);
            }

            return view('floor-plans.index', compact('floorPlans', 'events'));
        } catch (\Illuminate\Database\QueryException $e) {
            // Database query exception (table doesn't exist, etc.) - return with empty events
            try {
                $floorPlans = FloorPlan::with('createdBy')->withCount('booths')->latest('created_at')->paginate(20);
            } catch (\Exception $e2) {
                $floorPlans = collect([]);
            }
            $events = collect([]);
            return view('floor-plans.index', compact('floorPlans', 'events'));
        } catch (\Exception $e) {
            // If anything fails, return with empty events
            try {
                $floorPlans = FloorPlan::with('createdBy')->withCount('booths')->latest('created_at')->paginate(20);
            } catch (\Exception $e2) {
                $floorPlans = collect([]);
            }
            $events = collect([]);
            return view('floor-plans.index', compact('floorPlans', 'events'));
        }
    }

    /**
     * Show the form for creating a new floor plan
     */
    public function create()
    {
        // Get events if table exists - check table existence first, then query safely
        $events = collect([]);
        try {
            // First check if table exists using SHOW TABLES (doesn't query the events table itself)
            $tableCheck = DB::select("SHOW TABLES LIKE 'events'");
            if (!empty($tableCheck)) {
                // Table exists - query it safely
                try {
                    $eventsData = DB::select('SELECT * FROM events WHERE status = 1 ORDER BY title ASC');
                    if (is_array($eventsData)) {
                        $events = collect($eventsData);
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // Query failed - return empty
                    $events = collect([]);
                } catch (\Exception $e) {
                    // Any other error - return empty
                    $events = collect([]);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // SHOW TABLES failed or events table doesn't exist - return empty (expected)
            $events = collect([]);
        } catch (\PDOException $e) {
            // PDO exceptions - return empty
            $events = collect([]);
        } catch (\Exception $e) {
            // Any other exception - return empty
            $events = collect([]);
        }
        
        return view('floor-plans.create', compact('events'));
    }

    /**
     * Store a newly created floor plan
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_name' => 'nullable|string|max:255',
            'floor_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'canvas_width' => 'nullable|integer|min:100|max:5000',
            'canvas_height' => 'nullable|integer|min:100|max:5000',
            'is_active' => 'nullable|boolean',
        ];
        
        // Only validate event_id if events table exists (safe check - avoid exists rule if table doesn't exist)
        try {
            // Try to check if table exists - if query fails, table doesn't exist
            $tables = DB::select("SHOW TABLES LIKE 'events'");
            if (!empty($tables)) {
                // Table exists - validate with exists rule
                $rules['event_id'] = 'nullable|exists:events,id';
            } else {
                // Table doesn't exist - just validate as nullable (no exists check)
                $rules['event_id'] = 'nullable';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Table check failed - don't validate event_id with exists rule
            $rules['event_id'] = 'nullable';
        } catch (\Exception $e) {
            // Any other error - don't validate event_id with exists rule
            $rules['event_id'] = 'nullable';
        }
        
        $validated = $request->validate($rules);

        // Handle image upload (store with unique name including floor_plan_id will be added after creation)
        if ($request->hasFile('floor_image')) {
            $image = $request->file('floor_image');
            $imageExtension = $image->getClientOriginalExtension();
            $imageName = time() . '_floor_plan_temp.' . $imageExtension; // Temporary name, will update after creation
            $imagePath = public_path('images/floor-plans');
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            $image->move($imagePath, $imageName);
            $validated['floor_image'] = 'images/floor-plans/' . $imageName;
            
            // Auto-update canvas dimensions from image if uploaded
            $fullImagePath = $imagePath . '/' . $imageName;
            if (file_exists($fullImagePath)) {
                $imageInfo = getimagesize($fullImagePath);
                if ($imageInfo) {
                    $validated['canvas_width'] = $imageInfo[0];
                    $validated['canvas_height'] = $imageInfo[1];
                }
            }
        }

        // Get user ID safely - ensure it's always an integer, never username
        $userId = null;
        if (Auth::check()) {
            $user = Auth::user();
            // Get the actual ID from the user object (ensure it's an integer)
            if ($user && isset($user->id) && is_numeric($user->id)) {
                $userId = (int) $user->id;
            }
        }
        
        // Explicitly remove created_by from validated data if present (prevent request override)
        // This ensures the request can never set created_by with a username or invalid value
        unset($validated['created_by']);
        
        // Set created_by manually to ensure it's always an integer (never username)
        $validated['created_by'] = $userId;
        
        // Set other defaults
        $validated['is_active'] = $request->has('is_active') ? true : true; // Default active
        $validated['canvas_width'] = $validated['canvas_width'] ?? 1200;
        $validated['canvas_height'] = $validated['canvas_height'] ?? 800;

        // If this is the first floor plan, set as default
        if (FloorPlan::count() === 0) {
            $validated['is_default'] = true;
        }

        // Create floor plan - created_by will be properly cast as integer by the model
        $floorPlan = FloorPlan::create($validated);
        
        // Update image name to include floor_plan_id for uniqueness
        if ($floorPlan->floor_image && strpos($floorPlan->floor_image, '_floor_plan_temp') !== false) {
            $oldPath = public_path($floorPlan->floor_image);
            $imageExtension = pathinfo($floorPlan->floor_image, PATHINFO_EXTENSION);
            $newImageName = time() . '_floor_plan_' . $floorPlan->id . '.' . $imageExtension;
            $newPath = public_path('images/floor-plans/' . $newImageName);
            
            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
                $floorPlan->floor_image = 'images/floor-plans/' . $newImageName;
                $floorPlan->save();
            }
        }
        
        // Create default canvas settings for this floor plan
        try {
            CanvasSetting::updateOrCreate(
                ['floor_plan_id' => $floorPlan->id],
                [
                    'canvas_width' => $floorPlan->canvas_width ?? 1200,
                    'canvas_height' => $floorPlan->canvas_height ?? 800,
                    'canvas_resolution' => 300,
                    'grid_size' => 10,
                    'zoom_level' => 1.00,
                    'pan_x' => 0,
                    'pan_y' => 0,
                    'floorplan_image' => $floorPlan->floor_image,
                    'grid_enabled' => true,
                    'snap_to_grid' => false,
                ]
            );
        } catch (\Exception $e) {
            \Log::warning('Could not create canvas settings for floor plan: ' . $e->getMessage());
        }

        return redirect()->route('floor-plans.index')
            ->with('success', 'Floor plan created successfully.');
    }

    /**
     * Display the specified floor plan
     */
    public function show(FloorPlan $floorPlan)
    {
        // Load relationships safely (don't eager load event if table doesn't exist)
        $load = ['createdBy', 'booths.client', 'booths.category'];
        // Note: We don't eager load 'event' to avoid errors if table doesn't exist
        
        $floorPlan->load($load);
        $stats = $floorPlan->getStats();

        return view('floor-plans.show', compact('floorPlan', 'stats'));
    }

    /**
     * Show the form for editing the specified floor plan
     */
    public function edit(FloorPlan $floorPlan)
    {
        // Get events if table exists - check table existence first, then query safely
        $events = collect([]);
        try {
            // First check if table exists using SHOW TABLES (doesn't query the events table itself)
            $tableCheck = DB::select("SHOW TABLES LIKE 'events'");
            if (!empty($tableCheck)) {
                // Table exists - query it safely
                try {
                    $eventsData = DB::select('SELECT * FROM events WHERE status = 1 ORDER BY title ASC');
                    if (is_array($eventsData)) {
                        $events = collect($eventsData);
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    // Query failed - return empty
                    $events = collect([]);
                } catch (\Exception $e) {
                    // Any other error - return empty
                    $events = collect([]);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // SHOW TABLES failed or events table doesn't exist - return empty (expected)
            $events = collect([]);
        } catch (\PDOException $e) {
            // PDO exceptions - return empty
            $events = collect([]);
        } catch (\Exception $e) {
            // Any other exception - return empty
            $events = collect([]);
        }
        
        return view('floor-plans.edit', compact('floorPlan', 'events'));
    }

    /**
     * Update the specified floor plan
     */
    public function update(Request $request, FloorPlan $floorPlan)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_name' => 'nullable|string|max:255',
            'floor_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'canvas_width' => 'nullable|integer|min:100|max:5000',
            'canvas_height' => 'nullable|integer|min:100|max:5000',
            'is_active' => 'nullable|boolean',
        ];
        
        // Only validate event_id if events table exists (safe check - avoid exists rule if table doesn't exist)
        try {
            // Try to check if table exists - if query fails, table doesn't exist
            $tables = DB::select("SHOW TABLES LIKE 'events'");
            if (!empty($tables)) {
                // Table exists - validate with exists rule
                $rules['event_id'] = 'nullable|exists:events,id';
            } else {
                // Table doesn't exist - just validate as nullable (no exists check)
                $rules['event_id'] = 'nullable';
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Table check failed - don't validate event_id with exists rule
            $rules['event_id'] = 'nullable';
        } catch (\Exception $e) {
            // Any other error - don't validate event_id with exists rule
            $rules['event_id'] = 'nullable';
        }
        
        $validated = $request->validate($rules);

        // Handle image upload (from edit form)
        // CRITICAL: Save new image FIRST before deleting old one (prevents data loss)
        if ($request->hasFile('floor_image')) {
            try {
                $image = $request->file('floor_image');
                $imageExtension = $image->getClientOriginalExtension();
                $imageName = time() . '_floor_plan_' . $floorPlan->id . '.' . $imageExtension; // Include floor_plan_id for uniqueness
                
                $imagePath = public_path('images/floor-plans');
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0755, true);
                }
                
                // Save new image FIRST (before deleting old one)
                $newImagePath = $imagePath . '/' . $imageName;
                $image->move($imagePath, $imageName);
                
                // Verify the new file was created successfully
                if (!file_exists($newImagePath)) {
                    throw new \Exception('Failed to upload image file - file not found after move');
                }
                
                // Get image dimensions from the new file
                $imageInfo = getimagesize($newImagePath);
                $imageWidth = $imageInfo[0] ?? $floorPlan->canvas_width ?? 1200;
                $imageHeight = $imageInfo[1] ?? $floorPlan->canvas_height ?? 800;
                
                // Update validated array with new image path and dimensions
                $validated['floor_image'] = 'images/floor-plans/' . $imageName;
                $validated['canvas_width'] = $imageWidth;
                $validated['canvas_height'] = $imageHeight;
                
                \Log::info('Floor plan image uploaded from edit form', [
                    'floor_plan_id' => $floorPlan->id,
                    'floor_plan_name' => $floorPlan->name,
                    'new_image_path' => $validated['floor_image'],
                    'new_image_width' => $imageWidth,
                    'new_image_height' => $imageHeight,
                    'old_image_path' => $floorPlan->floor_image
                ]);
            } catch (\Exception $e) {
                \Log::error('Error uploading floor plan image from edit form: ' . $e->getMessage(), [
                    'floor_plan_id' => $floorPlan->id,
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Remove floor_image from validated if upload failed
                unset($validated['floor_image']);
                unset($validated['canvas_width']);
                unset($validated['canvas_height']);
            }
        }

        // Preserve existing canvas dimensions if not uploading new image
        if (!$request->hasFile('floor_image')) {
            // Not uploading image - preserve existing dimensions
            if (!isset($validated['canvas_width'])) {
                unset($validated['canvas_width']);
            }
            if (!isset($validated['canvas_height'])) {
                unset($validated['canvas_height']);
            }
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        // IMPORTANT: Preserve existing floor_image unless explicitly uploading new one
        if (!isset($validated['floor_image'])) {
            // Not uploading image - preserve existing floor_image
            unset($validated['floor_image']);
        }

        // Store old image path before update (for cleanup after successful save)
        $oldImagePath = $floorPlan->floor_image ? public_path($floorPlan->floor_image) : null;
        $oldImageExists = $oldImagePath && file_exists($oldImagePath);
        $isUploadingNewImage = isset($validated['floor_image']);

        // Update floor plan (only fields that were provided)
        $floorPlan->update($validated);
        
        // Refresh floor plan from database to get latest values
        $floorPlan->refresh();
        
        // Delete old image AFTER successful database update (only if uploading new image)
        if ($isUploadingNewImage && $oldImageExists) {
            try {
                $newImagePath = $floorPlan->floor_image ? public_path($floorPlan->floor_image) : null;
                // Only delete if it's different from the new image
                if ($newImagePath && $oldImagePath !== $newImagePath) {
                    unlink($oldImagePath);
                    \Log::info('Deleted old floor plan image after successful upload from edit form', [
                        'floor_plan_id' => $floorPlan->id,
                        'old_image' => str_replace(public_path() . '/', '', $oldImagePath),
                        'new_image' => $floorPlan->floor_image
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Could not delete old floor plan image (non-critical): ' . $e->getMessage(), [
                    'floor_plan_id' => $floorPlan->id,
                    'old_image_path' => $oldImagePath
                ]);
            }
        }
        
        // CRITICAL: Always sync canvas_settings with floor_plans.floor_image after update
        // This ensures the canvas automatically loads the correct image when viewing booths
        try {
            $canvasSetting = CanvasSetting::updateOrCreate(
                ['floor_plan_id' => $floorPlan->id],
                [
                    'canvas_width' => $floorPlan->canvas_width ?? 1200,
                    'canvas_height' => $floorPlan->canvas_height ?? 800,
                    'floorplan_image' => $floorPlan->floor_image, // Always sync with floor_plans.floor_image
                ]
            );
            
            
            \Log::info('Canvas settings synced after floor plan update', [
                'floor_plan_id' => $floorPlan->id,
                'floor_image' => $floorPlan->floor_image,
                'canvas_width' => $floorPlan->canvas_width,
                'canvas_height' => $floorPlan->canvas_height
            ]);
        } catch (\Exception $e) {
            
            \Log::warning('Could not update canvas settings for floor plan: ' . $e->getMessage(), [
                'floor_plan_id' => $floorPlan->id
            ]);
        }

        // Prepare success message
        $message = 'Floor plan updated successfully.';
        if ($isUploadingNewImage && $floorPlan->floor_image) {
            $message .= ' Floor plan image uploaded successfully. The canvas will automatically load this image when you click "View Booths" for this floor plan.';
        }

        return redirect()->route('floor-plans.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified floor plan
     */
    public function destroy(Request $request, FloorPlan $floorPlan)
    {
        try {
            DB::beginTransaction();

            // Check if this is the default floor plan
            if ($floorPlan->is_default) {
                return back()->with('error', 'Cannot delete the default floor plan. Please set another floor plan as default first.');
            }

            // Get booth count
            $boothCount = $floorPlan->booths()->count();
            
            // Handle booths if floor plan has any
            if ($boothCount > 0) {
                $action = $request->input('booth_action', 'delete'); // 'delete' or 'move'
                
                if ($action === 'move') {
                    // Move booths to another floor plan
                    $targetFloorPlanId = $request->input('target_floor_plan_id');
                    
                    if (!$targetFloorPlanId) {
                        return back()->with('error', 'Please select a target floor plan to move booths to.');
                    }
                    
                    $targetFloorPlan = FloorPlan::find($targetFloorPlanId);
                    if (!$targetFloorPlan) {
                        return back()->with('error', 'Target floor plan not found.');
                    }
                    
                    if ($targetFloorPlan->id === $floorPlan->id) {
                        return back()->with('error', 'Cannot move booths to the same floor plan.');
                    }
                    
                    // Move all booths to target floor plan
                    $floorPlan->booths()->update(['floor_plan_id' => $targetFloorPlanId]);
                    
                    \Log::info('Moved booths from floor plan to another', [
                        'from_floor_plan_id' => $floorPlan->id,
                        'to_floor_plan_id' => $targetFloorPlanId,
                        'booth_count' => $boothCount
                    ]);
                } else {
                    // Delete all associated booths (and their bookings)
                    foreach ($floorPlan->booths as $booth) {
                        // Delete associated bookings
                        if ($booth->book) {
                            $booth->book->delete();
                        }
                        $booth->delete();
                    }
                    
                    \Log::info('Deleted booths with floor plan', [
                        'floor_plan_id' => $floorPlan->id,
                        'booth_count' => $boothCount
                    ]);
                }
            }

            // Delete canvas settings for this floor plan
            try {
                CanvasSetting::where('floor_plan_id', $floorPlan->id)->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete canvas settings for floor plan: ' . $e->getMessage());
            }

            // Delete zone settings for this floor plan
            try {
                ZoneSetting::where('floor_plan_id', $floorPlan->id)->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete zone settings for floor plan: ' . $e->getMessage());
            }

            // Delete floor image if exists
            if ($floorPlan->floor_image && file_exists(public_path($floorPlan->floor_image))) {
                try {
                    unlink(public_path($floorPlan->floor_image));
                } catch (\Exception $e) {
                    \Log::warning('Could not delete floor plan image: ' . $e->getMessage());
                }
            }

            // Delete the floor plan
            $floorPlan->delete();

            DB::commit();

            $message = 'Floor plan deleted successfully.';
            if ($boothCount > 0) {
                if ($action === 'move') {
                    $message .= " {$boothCount} booth(s) moved to target floor plan.";
                } else {
                    $message .= " {$boothCount} booth(s) deleted.";
                }
            }

            return redirect()->route('floor-plans.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting floor plan: ' . $e->getMessage(), [
                'floor_plan_id' => $floorPlan->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error deleting floor plan: ' . $e->getMessage());
        }
    }

    /**
     * Set floor plan as default
     */
    public function setDefault(FloorPlan $floorPlan)
    {
        $floorPlan->setAsDefault();

        return back()->with('success', 'Floor plan set as default successfully.');
    }

    /**
     * Duplicate floor plan with booths
     */
    public function duplicate(FloorPlan $floorPlan)
    {
        try {
            DB::beginTransaction();

            // Create new floor plan
            $userId = null;
            if (Auth::check()) {
                $user = Auth::user();
                $userId = isset($user->id) ? (int) $user->id : null;
            }
            $newFloorPlan = $floorPlan->replicate();
            $newFloorPlan->name = $floorPlan->name . ' (Copy)';
            $newFloorPlan->is_default = false;
            $newFloorPlan->created_by = $userId;
            $newFloorPlan->save();

            // Duplicate booths
            foreach ($floorPlan->booths as $booth) {
                $newBooth = $booth->replicate();
                $newBooth->floor_plan_id = $newFloorPlan->id;
                $newBooth->client_id = null; // Clear client assignment
                $newBooth->userid = null;
                $newBooth->bookid = null;
                $newBooth->status = Booth::STATUS_AVAILABLE; // Reset to available
                $newBooth->save();
            }

            DB::commit();

            return redirect()->route('floor-plans.index')
                ->with('success', 'Floor plan duplicated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error duplicating floor plan: ' . $e->getMessage());
        }
    }
}
