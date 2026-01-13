<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\Client;
use App\Models\Category;
use App\Models\Asset;
use App\Models\BoothType;
use App\Models\Book;
use App\Models\ZoneSetting;
use App\Models\FloorPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\DebugLogger;

class BoothController extends Controller
{
    /**
     * Display a listing of booths (Floor Plan)
     */
    public function index(Request $request)
    {
        // #region agent log
        DebugLogger::log(['request_method'=>$request->method(),'user_authenticated'=>auth()->check()], 'BoothController.php:22', 'BoothController::index() called');
        // #endregion
        
        // Get floor plan filter (from query param or default)
        $floorPlanId = $request->input('floor_plan_id');
        
        // If no floor plan specified, get default floor plan
        if (!$floorPlanId) {
            $defaultFloorPlan = FloorPlan::where('is_default', true)->first();
            $floorPlanId = $defaultFloorPlan ? $defaultFloorPlan->id : null;
        }
        
        // Get all floor plans for selector
        $floorPlans = FloorPlan::where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();
        
        // Get current floor plan with all its settings
        // CRITICAL: Always reload from database to ensure we have latest floor_image
        // This ensures when user clicks "View Booths", the correct floor plan image loads automatically
        // Use fresh() to ensure we get the absolute latest data (not from cache)
        $currentFloorPlan = $floorPlanId ? FloorPlan::withoutGlobalScopes()->find($floorPlanId) : null;
        
        // CRITICAL: Force refresh the model to ensure we have the absolute latest floor_image
        if ($currentFloorPlan) {
            $currentFloorPlan->refresh();
            
        }
        
        // Verify floor plan exists and has image (for automatic canvas loading)
        if ($currentFloorPlan) {
            // Verify image file exists
            if ($currentFloorPlan->floor_image && !file_exists(public_path($currentFloorPlan->floor_image))) {
                \Log::warning('Floor plan image file not found, but path exists in database', [
                    'floor_plan_id' => $floorPlanId,
                    'floor_plan_name' => $currentFloorPlan->name,
                    'floor_image_path' => $currentFloorPlan->floor_image,
                    'full_path' => public_path($currentFloorPlan->floor_image)
                ]);
            }
            
            \Log::info('Loading floor plan for canvas (automatic image load)', [
                'floor_plan_id' => $floorPlanId,
                'floor_plan_name' => $currentFloorPlan->name,
                'floor_image' => $currentFloorPlan->floor_image,
                'image_exists' => $currentFloorPlan->floor_image && file_exists(public_path($currentFloorPlan->floor_image)),
                'canvas_width' => $currentFloorPlan->canvas_width,
                'canvas_height' => $currentFloorPlan->canvas_height
            ]);
        }
        
        // Get all booths ordered by booth number, including positions
        $boothsQuery = Booth::with(['client', 'category', 'subCategory', 'asset', 'boothType', 'user', 'floorPlan']);
        
        // Filter by floor plan if specified
        if ($floorPlanId) {
            $boothsQuery->where('floor_plan_id', $floorPlanId);
        } else {
            // If no floor plan exists, show all booths (backward compatibility)
            $boothsQuery->whereNull('floor_plan_id');
        }
        
        $booths = $boothsQuery->orderBy('booth_number', 'asc')->get();
        
        // Get floor plan canvas settings (for JavaScript and canvas initialization)
        // CRITICAL: Always use floor_plans.floor_image as source of truth
        $canvasWidth = $currentFloorPlan ? $currentFloorPlan->canvas_width : 1200;
        $canvasHeight = $currentFloorPlan ? $currentFloorPlan->canvas_height : 800;
        $floorImage = $currentFloorPlan ? $currentFloorPlan->floor_image : null;
        
        // Verify floor image file exists and is accessible (if path is set)
        $floorImageExists = false;
        $floorImageUrl = null;
        if ($floorImage) {
            $fullPath = public_path($floorImage);
            $floorImageExists = file_exists($fullPath) && is_readable($fullPath);
            
            if (!$floorImageExists) {
                \Log::warning('Floor plan image file not found or not readable', [
                    'floor_plan_id' => $floorPlanId,
                    'floor_image_path' => $floorImage,
                    'full_path' => $fullPath,
                    'file_exists' => file_exists($fullPath),
                    'is_readable' => file_exists($fullPath) ? is_readable($fullPath) : false
                ]);
            } else {
                // Generate absolute URL for the image
                $floorImageUrl = asset($floorImage);
                // Ensure it's absolute (handle cases where APP_URL might not be set correctly)
                if (strpos($floorImageUrl, 'http') !== 0) {
                    $floorImageUrl = url($floorImage);
                }
            }
        }

        // #region agent log
        DebugLogger::log(['booth_count'=>$booths->count(),'first_booth_id'=>$booths->first()?->id ?? null], 'BoothController.php:31', 'Booths fetched from database');
        // #endregion

        // Calculate booth statistics
        $totalBooths = $booths->count();
        $availableBooths = $booths->where('status', Booth::STATUS_AVAILABLE)->count();
        $bookedBooths = $booths->where('status', Booth::STATUS_CONFIRMED)->count();
        $reservedBoothsCount = $booths->where('status', Booth::STATUS_RESERVED)->count();
        $paidBooths = $booths->where('status', Booth::STATUS_PAID)->count();
        $occupiedBooths = $totalBooths - $availableBooths;
        $occupancyPercentage = $totalBooths > 0 ? round(($occupiedBooths / $totalBooths) * 100, 1) : 0;
        
        // Calculate total revenue (if price data is available)
        $totalRevenue = $booths->sum('price');
        $paidRevenue = $booths->where('status', Booth::STATUS_PAID)->sum('price');
        
        // #region agent log
        DebugLogger::log(['totalBooths'=>$totalBooths,'availableBooths'=>$availableBooths,'bookedBooths'=>$bookedBooths,'reservedBoothsCount'=>$reservedBoothsCount,'paidBooths'=>$paidBooths,'totalRevenue'=>$totalRevenue,'paidRevenue'=>$paidRevenue], 'BoothController.php:48', 'Booth statistics calculated');
        // #endregion
        
        // Prepare booth data for JavaScript (to avoid parsing issues in Blade)
        $boothsForJS = $booths->map(function($booth) {
            return [
                'id' => $booth->id,
                'booth_number' => $booth->booth_number,
                'company' => $booth->client ? $booth->client->company : '',
                'category' => $booth->category ? $booth->category->name : '',
                'sub_category' => $booth->subCategory ? $booth->subCategory->name : '',
                'status' => $booth->status,
                'price' => $booth->price,
                'position_x' => $booth->position_x,
                'position_y' => $booth->position_y,
                'width' => $booth->width,
                'height' => $booth->height,
                'rotation' => $booth->rotation,
                'z_index' => $booth->z_index,
                'font_size' => $booth->font_size,
                'border_width' => $booth->border_width,
                'border_radius' => $booth->border_radius,
                'opacity' => $booth->opacity,
                // Appearance properties
                'background_color' => $booth->background_color,
                'border_color' => $booth->border_color,
                'text_color' => $booth->text_color,
                'font_weight' => $booth->font_weight,
                'font_family' => $booth->font_family,
                'text_align' => $booth->text_align,
                'box_shadow' => $booth->box_shadow,
            ];
        })->values();
        
        // #region agent log
        DebugLogger::log(['boothsForJS_count'=>count($boothsForJS),'first_booth_js'=>($boothsForJS->first() ?? null)], 'BoothController.php:65', 'Booth data prepared for JavaScript');
        // #endregion

        // Get all categories, assets, and booth types for dropdowns
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $assets = Asset::where('status', 1)->orderBy('name')->get();
        $boothTypes = BoothType::where('status', 1)->orderBy('name')->get();

        // Get mapping data for filters (only if user is authenticated)
        $reserveMap = [];
        $companyMap = [];
        $categoryMap = [];
        $subCategoryMap = [];
        $assetMap = [];
        $boothTypeMap = [];
        $clients = [];

        if (auth()->check()) {
            // Reserve Map - only reserved booths (status 3)
            $reservedBooths = Booth::where('status', Booth::STATUS_RESERVED)
                ->with('client')
                ->get();
            
            foreach ($reservedBooths as $booth) {
                $company = $booth->client ? $booth->client->company : '';
                if (empty($company)) {
                    $company = '===No Complete Form===';
                }
                if (!isset($reserveMap[$company])) {
                    $reserveMap[$company] = [];
                }
                $reserveMap[$company][] = $booth->id;
            }

            // Company Map - all booths with clients
            $companyBooths = Booth::whereNotNull('client_id')
                ->where('client_id', '!=', 0)
                ->with('client')
                ->get();
            
            foreach ($companyBooths as $booth) {
                if ($booth->client) {
                    $company = $booth->client->company;
                    if (!isset($companyMap[$company])) {
                        $companyMap[$company] = [];
                    }
                    $companyMap[$company][] = $booth->id;
                }
            }

            // Category Map
            $categoryBooths = Booth::whereNotNull('category_id')
                ->with('category')
                ->get();
            
            foreach ($categoryBooths as $booth) {
                if ($booth->category) {
                    $categoryName = $booth->category->name;
                    if (!isset($categoryMap[$categoryName])) {
                        $categoryMap[$categoryName] = [];
                    }
                    $categoryMap[$categoryName][] = $booth->id;
                }
            }

            // Sub-Category Map
            $subCategoryBooths = Booth::whereNotNull('sub_category_id')
                ->with('subCategory')
                ->get();
            
            foreach ($subCategoryBooths as $booth) {
                if ($booth->subCategory) {
                    $subCategoryName = $booth->subCategory->name;
                    if (!isset($subCategoryMap[$subCategoryName])) {
                        $subCategoryMap[$subCategoryName] = [];
                    }
                    $subCategoryMap[$subCategoryName][] = $booth->id;
                }
            }

            // Asset Map
            $assetBooths = Booth::whereNotNull('asset_id')
                ->with('asset')
                ->get();
            
            foreach ($assetBooths as $booth) {
                if ($booth->asset) {
                    $assetName = $booth->asset->name;
                    if (!isset($assetMap[$assetName])) {
                        $assetMap[$assetName] = [];
                    }
                    $assetMap[$assetName][] = $booth->id;
                }
            }

            // Booth Type Map
            $boothTypeBooths = Booth::whereNotNull('booth_type_id')
                ->with('boothType')
                ->get();
            
            foreach ($boothTypeBooths as $booth) {
                if ($booth->boothType) {
                    $boothTypeName = $booth->boothType->name;
                    if (!isset($boothTypeMap[$boothTypeName])) {
                        $boothTypeMap[$boothTypeName] = [];
                    }
                    $boothTypeMap[$boothTypeName][] = $booth->id;
                }
            }

            // Get all clients
            $clients = Client::orderBy('company')->get();
        }

        // Check if user has permission to edit canvas
        $canEditCanvas = auth()->user()->hasPermission('booths.canvas.edit') || auth()->user()->isAdmin();
        
        return view('booths.index', compact(
            'booths',
            'boothsForJS',
            'categories',
            'assets',
            'boothTypes',
            'reserveMap',
            'companyMap',
            'categoryMap',
            'subCategoryMap',
            'assetMap',
            'boothTypeMap',
            'floorImageUrl',
            'floorImageExists',
            'clients',
            'totalBooths',
            'availableBooths',
            'bookedBooths',
            'reservedBoothsCount',
            'paidBooths',
            'occupiedBooths',
            'occupancyPercentage',
            'totalRevenue',
            'paidRevenue',
            'floorPlans',
            'currentFloorPlan',
            'floorPlanId',
            'canvasWidth',
            'canvasHeight',
            'floorImage',
            'canEditCanvas'
        ));
    }

    /**
     * Show the form for creating a new booth
     */
    public function create(Request $request)
    {
        $categories = Category::where('status', 1)->get();
        $assets = Asset::where('status', 1)->get();
        $boothTypes = BoothType::where('status', 1)->get();
        
        // Get floor plan from query param or default
        $floorPlanId = $request->input('floor_plan_id');
        if (!$floorPlanId) {
            $defaultFloorPlan = FloorPlan::where('is_default', true)->first();
            $floorPlanId = $defaultFloorPlan ? $defaultFloorPlan->id : null;
        }
        
        $floorPlans = FloorPlan::where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();
        
        return view('booths.create', compact('categories', 'assets', 'boothTypes', 'floorPlans', 'floorPlanId'));
    }

    /**
     * Store a newly created booth
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booth_number' => 'required|string|max:45',
            'type' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:category,id',
            'asset_id' => 'nullable|exists:asset,id',
            'booth_type_id' => 'nullable|exists:booth_type,id',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
        ]);
        
        // If no floor plan specified, use default
        if (empty($validated['floor_plan_id'])) {
            $defaultFloorPlan = FloorPlan::where('is_default', true)->first();
            if ($defaultFloorPlan) {
                $validated['floor_plan_id'] = $defaultFloorPlan->id;
            }
        }

                        // Double-check for duplicates before creating (floor-plan-specific)
        $existingBoothQuery = Booth::where('booth_number', $validated['booth_number']);
        if (!empty($validated['floor_plan_id'])) {
            $existingBoothQuery->where('floor_plan_id', $validated['floor_plan_id']);
        }
        $existingBooth = $existingBoothQuery->first();
        if ($existingBooth) {
            return back()
                ->withInput()
                ->withErrors(['booth_number' => !empty($validated['floor_plan_id']) 
                    ? 'This booth number already exists in this floor plan. Please choose a different number.'
                    : 'This booth number already exists. Please choose a different number.']);
        }

        $booth = Booth::create($validated);

        // Preserve floor_plan_id in redirect if specified
        $redirectUrl = route('booths.index');
        if (!empty($validated['floor_plan_id'])) {
            $redirectUrl .= '?floor_plan_id=' . $validated['floor_plan_id'];
        }

        return redirect($redirectUrl)
            ->with('success', 'Booth created successfully.');
    }

    /**
     * Display the specified booth
     */
    public function show(Booth $booth)
    {
        $booth->load(['client', 'user', 'category', 'subCategory', 'asset', 'boothType', 'book']);
        return view('booths.show', compact('booth'));
    }

    /**
     * Show the form for editing the specified booth
     */
    public function edit(Booth $booth, Request $request)
    {
        $categories = Category::where('status', 1)->get();
        $assets = Asset::where('status', 1)->get();
        $boothTypes = BoothType::where('status', 1)->get();
        $clients = Client::orderBy('company')->get();
        
        // Get all floor plans for selector
        $floorPlans = FloorPlan::where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();
        
        // Get current floor plan (from booth or query param)
        $currentFloorPlanId = $request->input('floor_plan_id', $booth->floor_plan_id);

        return view('booths.edit', compact('booth', 'categories', 'assets', 'boothTypes', 'clients', 'floorPlans', 'currentFloorPlanId'));
    }

    /**
     * Update the specified booth
     */
    public function update(Request $request, Booth $booth)
    {
        $validated = $request->validate([
            'booth_number' => 'required|string|max:45',
            'type' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'status' => 'required|integer',
            'client_id' => 'nullable|exists:client,id',
            'category_id' => 'nullable|exists:category,id',
            'asset_id' => 'nullable|exists:asset,id',
            'booth_type_id' => 'nullable|exists:booth_type,id',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
        ]);

        // Double-check for duplicates before updating (floor-plan-specific)
        $floorPlanId = $validated['floor_plan_id'] ?? $booth->floor_plan_id;
        $existingBoothQuery = Booth::where('booth_number', $validated['booth_number'])
            ->where('id', '!=', $booth->id);
        
        if ($floorPlanId) {
            $existingBoothQuery->where('floor_plan_id', $floorPlanId);
        }
        
        $existingBooth = $existingBoothQuery->first();
        if ($existingBooth) {
            return back()
                ->withInput()
                ->withErrors(['booth_number' => $floorPlanId
                    ? 'This booth number already exists in this floor plan. Please choose a different number.'
                    : 'This booth number already exists. Please choose a different number.']);
        }

        // If no floor plan specified, keep current one
        if (empty($validated['floor_plan_id']) && $booth->floor_plan_id) {
            $validated['floor_plan_id'] = $booth->floor_plan_id;
        } elseif (empty($validated['floor_plan_id'])) {
            // If booth has no floor plan, assign to default
            $defaultFloorPlan = FloorPlan::where('is_default', true)->first();
            if ($defaultFloorPlan) {
                $validated['floor_plan_id'] = $defaultFloorPlan->id;
            }
        }

        $booth->update($validated);

        // Preserve floor_plan_id in redirect
        $redirectUrl = route('booths.index');
        if (!empty($validated['floor_plan_id'])) {
            $redirectUrl .= '?floor_plan_id=' . $validated['floor_plan_id'];
        }

        return redirect($redirectUrl)
            ->with('success', 'Booth updated successfully.');
    }

    /**
     * Remove the specified booth
     */
    public function destroy(Booth $booth)
    {
        if ($booth->status !== Booth::STATUS_AVAILABLE) {
            return back()->with('error', 'Cannot delete a booth that is not available.');
        }

        $booth->delete();

        return redirect()->route('booths.index')
            ->with('success', 'Booth deleted successfully.');
    }

    /**
     * Confirm reservation (confirmR)
     * Changes status from 3 (reserved) to 2 (confirmed)
     */
    public function confirmReservation($id)
    {
        $booth = Booth::findOrFail($id);
        $user = auth()->user();
        
        // Check if user owns the booth or is admin
        if ($booth->userid === auth()->id() || $user->isAdmin()) {
            // Only confirm if status is 3 (reserved) or user is admin
            if ($booth->status === Booth::STATUS_RESERVED || $user->isAdmin()) {
                $booth->update(['status' => Booth::STATUS_CONFIRMED]);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Successful.'
                ]);
            }
        }
        
        return response()->json([
            'status' => 403,
            'message' => 'These actions are available for registered booths only.'
        ], 403);
    }

    /**
     * Clear reservation (clearR)
     * Only works if booth status is 3 (reserved) and user owns it
     */
    public function clearReservation($id)
    {
        $booth = Booth::findOrFail($id);
        
        // Only allow if user owns the booth
        if ($booth->userid === auth()->id()) {
            // Only clear if status is 3 (reserved)
            if ($booth->status === Booth::STATUS_RESERVED) {
                // Remove from book table
                if ($booth->bookid) {
                    $book = Book::find($booth->bookid);
                    if ($book) {
                        $bookBooths = json_decode($book->boothid, true) ?? [];
                        
                        // Remove booth id from array
                        $bookBooths = array_values(array_diff($bookBooths, [$id]));
                        
                        if (count($bookBooths) > 0) {
                            $book->boothid = json_encode($bookBooths);
                            $book->save();
                        } else {
                            $book->delete();
                        }
                    }
                }
                
                // Reset booth to available
                $booth->update([
                    'status' => Booth::STATUS_AVAILABLE,
                    'client_id' => 0,
                    'userid' => 0,
                    'bookid' => 0,
                    'category_id' => null,
                    'sub_category_id' => null,
                    'asset_id' => null,
                    'booth_type_id' => null,
                ]);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Successful.'
                ]);
            }
        }
        
        return response()->json([
            'status' => 403,
            'message' => 'Please Check Data Before Submit'
        ], 403);
    }

    /**
     * Mark booth as paid (bookPaid)
     * Changes status from 2 (confirmed) to 5 (paid)
     */
    public function markPaid($id)
    {
        $booth = Booth::findOrFail($id);
        $user = auth()->user();
        
        // Check if user owns the booth or is admin
        if ($booth->userid === auth()->id() || $user->isAdmin()) {
            // Only mark as paid if status is 2 (confirmed) or user is admin
            if ($booth->status === Booth::STATUS_CONFIRMED || $user->isAdmin()) {
                $booth->update(['status' => Booth::STATUS_PAID]);
                
                return response()->json([
                    'status' => 200,
                    'message' => 'Successful.'
                ]);
            }
        }
        
        return response()->json([
            'status' => 403,
            'message' => 'These actions are available for registered booths only.'
        ], 403);
    }

    /**
     * Get user's booths
     */
    public function myBooths()
    {
        $booths = Booth::where('userid', auth()->id())
            ->with(['client', 'category', 'asset'])
            ->orderBy('booth_number')
            ->get();

        return view('booths.my-booths', compact('booths'));
    }

    /**
     * Remove booth from booking (rmbooth)
     * Only works if booth is not available (status != 1) and user owns it or is admin
     */
    public function removeBooth($id)
    {
        $booth = Booth::findOrFail($id);
        $user = auth()->user();
        
        // Check authorization: booth must not be available, and user must own it or be admin
        if (($booth->status !== Booth::STATUS_AVAILABLE && $booth->userid === auth()->id()) 
            || ($booth->status !== Booth::STATUS_AVAILABLE && $user->isAdmin())) {
            
            // Remove from book table
            if ($booth->bookid) {
                $book = Book::find($booth->bookid);
                if ($book) {
                    $bookBooths = json_decode($book->boothid, true) ?? [];
                    
                    // Remove booth id from array
                    $bookBooths = array_values(array_diff($bookBooths, [$id]));
                    
                    if (count($bookBooths) > 0) {
                        $book->boothid = json_encode($bookBooths);
                        $book->save();
                    } else {
                        $book->delete();
                    }
                }
            }
            
            // Reset booth to available
            $booth->update([
                'status' => Booth::STATUS_AVAILABLE,
                'client_id' => 0,
                'userid' => 0,
                'bookid' => 0,
                'category_id' => null,
                'sub_category_id' => null,
                'asset_id' => null,
                'booth_type_id' => null,
            ]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Successful.'
            ]);
        }
        
        return response()->json([
            'status' => 403,
            'message' => 'Please Check Data Before Submit'
        ], 403);
    }

    /**
     * Update external view status (toggle between available and hidden)
     * Toggles status between 1 (available) and 4 (hidden)
     */
    public function updateExternalView(Request $request)
    {
        // Check permission for canvas editing
        if (!auth()->user()->hasPermission('booths.canvas.edit') && !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.'
            ], 403);
        }
        
        $data = $request->input('data');
        
        if (!isset($data)) {
            return response()->json([
                'status' => 403,
                'message' => 'Please Check Data Before Submit'
            ], 403);
        }
        
        $data = json_decode($data, true);
        
        if (!is_array($data)) {
            return response()->json([
                'status' => 403,
                'message' => 'Invalid data format'
            ], 403);
        }
        
        foreach ($data as $boothId) {
            $booth = Booth::find($boothId);
            if ($booth) {
                if ($booth->status == Booth::STATUS_AVAILABLE) {
                    $booth->status = Booth::STATUS_HIDDEN;
                } elseif ($booth->status == Booth::STATUS_HIDDEN) {
                    $booth->status = Booth::STATUS_AVAILABLE;
                }
                $booth->save();
            }
        }
        
        return response()->json([
            'status' => 200,
            'message' => 'Successful.'
        ]);
    }

    /**
     * Save booth position on floorplan
     */
    public function savePosition(Request $request, $id)
    {
        try {
            // Validate with proper nullable handling
            $validated = $request->validate([
                'position_x' => 'nullable|numeric',
                'position_y' => 'nullable|numeric',
                'width' => 'nullable|numeric',
                'height' => 'nullable|numeric',
                'rotation' => 'nullable|numeric',
                'z_index' => 'nullable|integer|min:1|max:1000',
                'font_size' => 'nullable|integer|min:8|max:48',
                'border_width' => 'nullable|integer|min:0|max:10',
                'border_radius' => 'nullable|integer|min:0|max:50',
                'opacity' => 'nullable|numeric|min:0|max:1',
                'price' => 'nullable|numeric|min:0',
                // Appearance properties
                'background_color' => 'nullable|string|max:50',
                'border_color' => 'nullable|string|max:50',
                'text_color' => 'nullable|string|max:50',
                'font_weight' => 'nullable|string|max:20',
                'font_family' => 'nullable|string|max:255',
                'text_align' => 'nullable|string|max:20',
                'box_shadow' => 'nullable|string|max:255',
            ]);

            $booth = Booth::findOrFail($id);
            
            // Save position, size, rotation, and style properties
            $booth->position_x = $validated['position_x'] ?? null;
            $booth->position_y = $validated['position_y'] ?? null;
            $booth->width = $validated['width'] ?? null;
            $booth->height = $validated['height'] ?? null;
            $booth->rotation = $validated['rotation'] ?? 0;
            $booth->z_index = $validated['z_index'] ?? 10;
            $booth->font_size = $validated['font_size'] ?? 14;
            $booth->border_width = $validated['border_width'] ?? 2;
            $booth->border_radius = $validated['border_radius'] ?? 6;
            $booth->opacity = $validated['opacity'] ?? 1.00;
            
            // Save appearance properties
            if (isset($validated['background_color'])) {
                $booth->background_color = $validated['background_color'];
            }
            if (isset($validated['border_color'])) {
                $booth->border_color = $validated['border_color'];
            }
            if (isset($validated['text_color'])) {
                $booth->text_color = $validated['text_color'];
            }
            if (isset($validated['font_weight'])) {
                $booth->font_weight = $validated['font_weight'];
            }
            if (isset($validated['font_family'])) {
                $booth->font_family = $validated['font_family'];
            }
            if (isset($validated['text_align'])) {
                $booth->text_align = $validated['text_align'];
            }
            if (isset($validated['box_shadow'])) {
                $booth->box_shadow = $validated['box_shadow'];
            }
            
            $booth->save();

            return response()->json([
                'status' => 200,
                'message' => 'Position saved successfully.',
                'booth' => [
                    'id' => $booth->id,
                    'booth_number' => $booth->booth_number,
                    'position_x' => $booth->position_x,
                    'position_y' => $booth->position_y,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving booth position: ' . $e->getMessage(), [
                'booth_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 500,
                'message' => 'Error saving position: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save all booth positions in bulk
     */
    public function saveAllPositions(Request $request)
    {
        // Check permission for canvas editing
        if (!auth()->user()->hasPermission('booths.canvas.edit') && !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.'
            ], 403);
        }
        
        try {
            \Log::info('saveAllPositions called', ['request_data' => $request->all()]);
            
            $validated = $request->validate([
                'booths' => 'required|array',
                'booths.*.id' => 'required|exists:booth,id',
                'booths.*.position_x' => 'nullable|numeric',
                'booths.*.position_y' => 'nullable|numeric',
                'booths.*.width' => 'nullable|numeric',
                'booths.*.height' => 'nullable|numeric',
                'booths.*.rotation' => 'nullable|numeric',
                'booths.*.z_index' => 'nullable|integer|min:1|max:1000',
                'booths.*.font_size' => 'nullable|integer|min:8|max:48',
                'booths.*.border_width' => 'nullable|integer|min:0|max:10',
                'booths.*.border_radius' => 'nullable|integer|min:0|max:50',
                'booths.*.opacity' => 'nullable|numeric|min:0|max:1',
                'booths.*.price' => 'nullable|numeric|min:0',
                // Appearance properties
                'booths.*.background_color' => 'nullable|string|max:50',
                'booths.*.border_color' => 'nullable|string|max:50',
                'booths.*.text_color' => 'nullable|string|max:50',
                'booths.*.font_weight' => 'nullable|string|max:20',
                'booths.*.font_family' => 'nullable|string|max:255',
                'booths.*.text_align' => 'nullable|string|max:20',
                'booths.*.box_shadow' => 'nullable|string|max:255',
                'booths.*.is_locked' => 'nullable|integer|in:0,1', // Lock state: 0=unlocked, 1=locked
            ]);

            \Log::info('Validation passed', ['booth_count' => count($validated['booths'])]);

            $saved = 0;
            $errors = [];

            foreach ($validated['booths'] as $boothData) {
                try {
                    $booth = Booth::findOrFail($boothData['id']);
                    
                    \Log::info('Saving booth', [
                        'booth_id' => $booth->id,
                        'booth_number' => $booth->booth_number,
                        'position_x' => $boothData['position_x'] ?? null,
                        'position_y' => $boothData['position_y'] ?? null,
                        'width' => $boothData['width'] ?? null,
                        'height' => $boothData['height'] ?? null,
                    ]);
                    
                    $booth->position_x = $boothData['position_x'] ?? null;
                    $booth->position_y = $boothData['position_y'] ?? null;
                    $booth->width = $boothData['width'] ?? null;
                    $booth->height = $boothData['height'] ?? null;
                    $booth->rotation = $boothData['rotation'] ?? 0;
                    $booth->z_index = $boothData['z_index'] ?? 10;
                    $booth->font_size = $boothData['font_size'] ?? 14;
                    $booth->border_width = $boothData['border_width'] ?? 2;
                    $booth->border_radius = $boothData['border_radius'] ?? 6;
                    $booth->opacity = $boothData['opacity'] ?? 1.00;
                    
                    // Save price if provided
                    if (isset($boothData['price'])) {
                        $booth->price = $boothData['price'];
                    }
                    
                    // Save appearance properties
                    if (isset($boothData['background_color'])) {
                        $booth->background_color = $boothData['background_color'];
                    }
                    if (isset($boothData['border_color'])) {
                        $booth->border_color = $boothData['border_color'];
                    }
                    if (isset($boothData['text_color'])) {
                        $booth->text_color = $boothData['text_color'];
                    }
                    if (isset($boothData['font_weight'])) {
                        $booth->font_weight = $boothData['font_weight'];
                    }
                    if (isset($boothData['font_family'])) {
                        $booth->font_family = $boothData['font_family'];
                    }
                    if (isset($boothData['text_align'])) {
                        $booth->text_align = $boothData['text_align'];
                    }
                    if (isset($boothData['box_shadow'])) {
                        $booth->box_shadow = $boothData['box_shadow'];
                    }
                    
                    // Save lock state (will be stored and can be retrieved on load)
                    // Note: If you want to persist this in database, add an 'is_locked' column to the booth table
                    // For now, we'll store it in localStorage on the frontend
                    
                    $savedSuccess = $booth->save();
                    
                    if ($savedSuccess) {
                        \Log::info('Booth saved successfully', [
                            'booth_id' => $booth->id,
                            'position_x' => $booth->position_x,
                            'position_y' => $booth->position_y,
                        ]);
                        $saved++;
                    } else {
                        \Log::error('Booth save returned false', ['booth_id' => $booth->id]);
                        $errors[] = [
                            'booth_id' => $boothData['id'],
                            'error' => 'Save operation returned false'
                        ];
                    }
                } catch (\Exception $e) {
                    \Log::error('Error saving booth', [
                        'booth_id' => $boothData['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $errors[] = [
                        'booth_id' => $boothData['id'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Positions saved successfully.',
                'saved' => $saved,
                'total' => count($validated['booths']),
                'errors' => $errors
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving all booth positions: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 500,
                'message' => 'Error saving positions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload floorplan image (floor-plan-specific)
     */
    public function uploadFloorplan(Request $request)
    {
        // Check permission for canvas editing
        if (!auth()->user()->hasPermission('booths.canvas.edit') && !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.'
            ], 403);
        }
        
        try {
            // Get upload size limit from environment or use default (100MB = 102400 KB)
            $maxSizeKB = env('UPLOAD_MAX_SIZE_KB', 102400); // Default 100MB in KB
            
            $request->validate([
                'floorplan_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:' . $maxSizeKB,
                'floor_plan_id' => 'required|exists:floor_plans,id',
            ]);

            $floorPlanId = $request->input('floor_plan_id');
            $floorPlan = FloorPlan::findOrFail($floorPlanId);

            $image = $request->file('floorplan_image');
            $imageExtension = $image->getClientOriginalExtension();
            $imageName = time() . '_floor_plan_' . $floorPlanId . '.' . $imageExtension;
            
            // Ensure floor plans images directory exists
            $floorPlansPath = public_path('images/floor-plans');
            if (!file_exists($floorPlansPath)) {
                mkdir($floorPlansPath, 0755, true);
            }
            
            // CRITICAL: Save new image FIRST before deleting old one (prevents data loss if save fails)
            // Move uploaded file to temporary location first, then verify it exists
            $image->move($floorPlansPath, $imageName);
            
            // Verify the new file was created successfully
            $newImagePath = $floorPlansPath . '/' . $imageName;
            if (!file_exists($newImagePath)) {
                throw new \Exception('Failed to upload image file - file not found after move');
            }
            
            // Get image dimensions from the new file
            $imageInfo = getimagesize($newImagePath);
            $imageWidth = $imageInfo[0] ?? $floorPlan->canvas_width;
            $imageHeight = $imageInfo[1] ?? $floorPlan->canvas_height;
            
            // Store old image path for cleanup (only delete after successful database update)
            $oldImagePath = $floorPlan->floor_image ? public_path($floorPlan->floor_image) : null;
            $oldImageExists = $oldImagePath && file_exists($oldImagePath);
            
            // Update floor plan with NEW image path and dimensions
            // CRITICAL: Save the relative path (not full URL) with unique name including floor_plan_id
            $floorPlan->floor_image = 'images/floor-plans/' . $imageName;
            $floorPlan->canvas_width = $imageWidth;
            $floorPlan->canvas_height = $imageHeight;
            
            // Save to database (CRITICAL: Save BEFORE deleting old file)
            $saved = $floorPlan->save();
            
            if (!$saved) {
                // Database save failed - delete the new file we just created to prevent orphaned files
                if (file_exists($newImagePath)) {
                    unlink($newImagePath);
                }
                \Log::error('Failed to save floor plan image path to database', [
                    'floor_plan_id' => $floorPlanId,
                    'image_name' => $imageName,
                    'image_path' => 'images/floor-plans/' . $imageName
                ]);
                throw new \Exception('Failed to save floor plan image path to database');
            }
            
            // Refresh floor plan from database to ensure we have latest values
            $floorPlan->refresh();
            
            // Verify the image path was saved correctly
            if ($floorPlan->floor_image !== 'images/floor-plans/' . $imageName) {
                \Log::error('Floor plan image path mismatch after save - attempting fix', [
                    'floor_plan_id' => $floorPlanId,
                    'expected' => 'images/floor-plans/' . $imageName,
                    'actual' => $floorPlan->floor_image
                ]);
                // Try to fix it
                $floorPlan->floor_image = 'images/floor-plans/' . $imageName;
                $floorPlan->save();
                $floorPlan->refresh();
            }
            
            // NOW delete old image (only after successful database update)
            // This ensures we don't lose data if database update fails
            if ($oldImageExists && $oldImagePath !== $newImagePath) {
                try {
                    unlink($oldImagePath);
                    \Log::info('Deleted old floor plan image', [
                        'floor_plan_id' => $floorPlanId,
                        'old_image' => $oldImagePath,
                        'new_image' => $newImagePath
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Could not delete old floor plan image (non-critical): ' . $e->getMessage(), [
                        'floor_plan_id' => $floorPlanId,
                        'old_image' => $oldImagePath
                    ]);
                }
            }
            
            // Update canvas settings for this floor plan (floor-plan-specific)
            // CRITICAL: Always sync canvas_settings.floorplan_image with floor_plans.floor_image
            // This ensures consistency when switching between floor plans
            try {
                \App\Models\CanvasSetting::updateOrCreate(
                    ['floor_plan_id' => $floorPlanId],
                    [
                        'canvas_width' => $imageWidth,
                        'canvas_height' => $imageHeight,
                        'floorplan_image' => $floorPlan->floor_image, // Relative path: 'images/floor-plans/...'
                    ]
                );
                
                \Log::info('Canvas settings updated for floor plan ' . $floorPlanId, [
                    'floor_plan_id' => $floorPlanId,
                    'floorplan_image' => $floorPlan->floor_image,
                    'canvas_width' => $imageWidth,
                    'canvas_height' => $imageHeight
                ]);
            } catch (\Exception $e) {
                \Log::error('Could not update canvas settings for floor plan: ' . $e->getMessage(), [
                    'floor_plan_id' => $floorPlanId,
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            // Get the URL for the image
            $imageUrl = asset($floorPlan->floor_image);
            
            return response()->json([
                'status' => 200,
                'message' => 'Floorplan uploaded successfully.',
                'image_url' => $imageUrl,
                'image_path' => $floorPlan->floor_image, // Relative path for database storage
                'image_width' => $imageWidth,
                'image_height' => $imageHeight,
                'canvas_width' => $imageWidth,
                'canvas_height' => $imageHeight,
                'floor_plan_id' => $floorPlanId
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error uploading floorplan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 500,
                'message' => 'Error uploading floorplan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the current floorplan image (floor-plan-specific).
     */
    public function removeFloorplan(Request $request)
    {
        // Check permission for canvas editing
        if (!auth()->user()->hasPermission('booths.canvas.edit') && !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.'
            ], 403);
        }
        
        try {
            $request->validate([
                'floor_plan_id' => 'required|exists:floor_plans,id',
            ]);

            $floorPlanId = $request->input('floor_plan_id');
            $floorPlan = FloorPlan::findOrFail($floorPlanId);

            // Delete floor plan's image if exists
            if ($floorPlan->floor_image && file_exists(public_path($floorPlan->floor_image))) {
                unlink(public_path($floorPlan->floor_image));
            }

            // Clear floor image from floor plan record
            $floorPlan->floor_image = null;
            $floorPlan->save();

            return response()->json([
                'status' => 200,
                'message' => 'Floorplan removed successfully.',
                'image_removed' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error removing floorplan: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Error removing floorplan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if booth number is duplicate (floor-plan-specific)
     */
    public function checkDuplicate(Request $request, $boothNumber = null)
    {
        $boothNumber = $boothNumber ?? $request->input('booth_number');
        $excludeId = $request->input('exclude_id'); // For update operations
        $floorPlanId = $request->input('floor_plan_id'); // For floor-plan-specific check
        
        if (!$boothNumber) {
            return response()->json([
                'status' => 400,
                'message' => 'Booth number is required',
                'is_duplicate' => false
            ], 400);
        }

        $query = Booth::where('booth_number', $boothNumber);
        
        // Filter by floor_plan_id if specified (floor-plan-specific uniqueness)
        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        }
        
        // Exclude current booth when checking for updates
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $exists = $query->exists();
        
        $message = $exists 
            ? ($floorPlanId 
                ? 'This booth number already exists in this floor plan' 
                : 'This booth number already exists')
            : 'Booth number is available';

        return response()->json([
            'status' => 200,
            'is_duplicate' => $exists,
            'booth_number' => $boothNumber,
            'floor_plan_id' => $floorPlanId,
            'message' => $message
        ]);
    }

    /**
     * Get zone settings (floor-plan-specific)
     */
    public function getZoneSettings(Request $request, $zoneName)
    {
        try {
            // Get floor plan ID from request (required)
            $floorPlanId = $request->input('floor_plan_id');
            if (!$floorPlanId) {
                // If no floor plan specified, try to get default
                $defaultFloorPlan = FloorPlan::where('is_default', true)->first();
                $floorPlanId = $defaultFloorPlan ? $defaultFloorPlan->id : null;
            }
            
            $settings = ZoneSetting::getZoneDefaults($zoneName, $floorPlanId);
            
            return response()->json([
                'status' => 200,
                'zone_name' => $zoneName,
                'floor_plan_id' => $floorPlanId,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting zone settings: ' . $e->getMessage());
            
            return response()->json([
                'status' => 500,
                'message' => 'Error getting zone settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new booth in a specific zone
     */
    public function createBoothInZone(Request $request, $zoneName)
    {
        // Check permission for canvas editing
        if (!auth()->user()->hasPermission('booths.canvas.edit') && !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.'
            ], 403);
        }
        
        try {
            $validated = $request->validate([
                'booth_number' => 'nullable|string|max:45',
                'count' => 'nullable|integer|min:1|max:100',
                'from' => 'nullable|integer|min:1|max:9999',
                'to' => 'nullable|integer|min:1|max:9999',
                'format' => 'nullable|integer|min:1|max:4',
                'floor_plan_id' => 'required|exists:floor_plans,id',
            ]);

            $floorPlanId = $validated['floor_plan_id'];
            $floorPlan = FloorPlan::findOrFail($floorPlanId);

            // Get zone price from zone settings (floor-plan-specific)
            $zoneSettings = ZoneSetting::getZoneDefaults($zoneName, $floorPlanId);
            $zonePrice = $zoneSettings['price'] ?? 500; // Default to 500 if not set

            $createdBooths = [];
            $skippedBooths = [];
            $errors = [];

            // Check if using range mode (from/to) or count mode
            if (isset($validated['from']) && isset($validated['to'])) {
                // Range mode: Create booths from X to Y
                $from = $validated['from'];
                $to = $validated['to'];
                $format = $validated['format'] ?? 2;
                
                if ($from > $to) {
                    return response()->json([
                        'status' => 422,
                        'message' => '"From" number must be less than or equal to "To" number'
                    ], 422);
                }
                
                $count = $to - $from + 1;
                if ($count > 100) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Maximum 100 booths can be created at once. Your range would create ' . $count . ' booths.'
                    ], 422);
                }
                
                // Create booths in range
                for ($i = $from; $i <= $to; $i++) {
                    try {
                        $boothNumber = $zoneName . str_pad($i, $format, '0', STR_PAD_LEFT);
                        
                        // Check if booth number already exists in this floor plan
                        if (Booth::where('booth_number', $boothNumber)
                            ->where('floor_plan_id', $floorPlanId)
                            ->exists()) {
                            $skippedBooths[] = $boothNumber;
                            continue;
                        }

                        $booth = Booth::create([
                            'booth_number' => $boothNumber,
                            'type' => 2, // Default type
                            'price' => $zonePrice, // Use zone price
                            'status' => Booth::STATUS_AVAILABLE,
                            'floor_plan_id' => $floorPlanId, // Assign to current floor plan
                        ]);

                        $createdBooths[] = [
                            'id' => $booth->id,
                            'booth_number' => $booth->booth_number,
                            'status' => $booth->status,
                        ];
                    } catch (\Exception $e) {
                        $errors[] = [
                            'booth_number' => $boothNumber ?? 'unknown',
                            'error' => $e->getMessage()
                        ];
                    }
                }
            } else {
                // Count mode: Create N booths (backward compatibility)
                $count = $validated['count'] ?? 1;
                $customBoothNumber = $validated['booth_number'] ?? null;
                
                for ($i = 0; $i < $count; $i++) {
                    try {
                        // Generate booth number if not provided
                        $boothNumber = null;
                        
                        if ($customBoothNumber && $i === 0) {
                            // Use custom booth number for first booth
                            $boothNumber = $customBoothNumber;
                        } else {
                            // Auto-generate booth number for the zone
                            $boothNumber = $this->generateNextBoothNumber($zoneName, $floorPlanId);
                        }

                        // Check if booth number already exists in this floor plan
                        if (Booth::where('booth_number', $boothNumber)
                            ->where('floor_plan_id', $floorPlanId)
                            ->exists()) {
                            $skippedBooths[] = $boothNumber;
                            // Try to generate next available
                            $boothNumber = $this->generateNextBoothNumber($zoneName, $floorPlanId);
                            if (Booth::where('booth_number', $boothNumber)
                                ->where('floor_plan_id', $floorPlanId)
                                ->exists()) {
                                continue; // Skip if still exists
                            }
                        }

                        $booth = Booth::create([
                            'booth_number' => $boothNumber,
                            'type' => 2, // Default type
                            'price' => $zonePrice, // Use zone price
                            'status' => Booth::STATUS_AVAILABLE,
                            'floor_plan_id' => $floorPlanId, // Assign to current floor plan
                        ]);

                        $createdBooths[] = [
                            'id' => $booth->id,
                            'booth_number' => $booth->booth_number,
                            'status' => $booth->status,
                        ];
                    } catch (\Exception $e) {
                        $errors[] = [
                            'index' => $i,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            }

            // Check if any booths were created
            if (count($createdBooths) === 0) {
                if (count($skippedBooths) > 0) {
                    // All booths were skipped (already exist)
                    return response()->json([
                        'status' => 409, // Conflict
                        'message' => 'Zone ' . $zoneName . ' already exists in this floor plan. ' . count($skippedBooths) . ' booth(s) already exist: ' . implode(', ', array_slice($skippedBooths, 0, 5)) . (count($skippedBooths) > 5 ? '...' : ''),
                        'created' => [],
                        'skipped' => $skippedBooths,
                        'errors' => $errors
                    ], 409);
                } elseif (count($errors) > 0) {
                    // Errors occurred during creation
                    return response()->json([
                        'status' => 500,
                        'message' => 'Failed to create booth(s) in Zone ' . $zoneName . '. Errors: ' . implode('; ', array_column($errors, 'error')),
                        'created' => [],
                        'skipped' => $skippedBooths,
                        'errors' => $errors
                    ], 500);
                } else {
                    // No booths created for unknown reason
                    return response()->json([
                        'status' => 500,
                        'message' => 'Failed to create booth(s) in Zone ' . $zoneName . '. No booths were created.',
                        'created' => [],
                        'skipped' => $skippedBooths,
                        'errors' => $errors
                    ], 500);
                }
            }

            // Success - at least one booth was created
            $message = count($createdBooths) . ' booth(s) created successfully in Zone ' . $zoneName . ' (Floor Plan: ' . $floorPlan->name . ')';
            if (count($skippedBooths) > 0) {
                $message .= '. ' . count($skippedBooths) . ' booth(s) skipped (already exist).';
            }
            if (count($errors) > 0) {
                $message .= '. ' . count($errors) . ' error(s) occurred: ' . implode('; ', array_column($errors, 'error'));
            }

            return response()->json([
                'status' => 200,
                'message' => $message,
                'created' => $createdBooths,
                'skipped' => $skippedBooths,
                'errors' => $errors,
                'floor_plan_id' => $floorPlanId,
                'floor_plan_name' => $floorPlan->name
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating booth in zone: ' . $e->getMessage(), [
                'zone' => $zoneName,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 500,
                'message' => 'Error creating booth: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate the next available booth number for a zone (floor-plan-specific)
     */
    private function generateNextBoothNumber($zoneName, $floorPlanId = null)
    {
        // Get all booths in this zone and floor plan (booth numbers starting with zone letter)
        $zoneBoothsQuery = Booth::where('booth_number', 'LIKE', $zoneName . '%');
        
        // Filter by floor plan if specified
        if ($floorPlanId) {
            $zoneBoothsQuery->where('floor_plan_id', $floorPlanId);
        }
        
        $zoneBooths = $zoneBoothsQuery->get();

        if ($zoneBooths->isEmpty()) {
            // No booths in this zone yet, start with 01
            return $zoneName . '01';
        }

        // Find the highest number in this zone
        $maxNumber = 0;
        foreach ($zoneBooths as $booth) {
            $boothNumber = $booth->booth_number;
            // Extract numeric part after zone prefix
            // Handle formats like: A01, A1, A001, A-01, etc.
            if (preg_match('/^' . preg_quote($zoneName, '/') . '[-_]?(\d+)/i', $boothNumber, $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        // Generate next number (use 2-digit format: A01, A02, etc.)
        $nextNumber = $maxNumber + 1;
        return $zoneName . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Delete booths from a specific zone
     */
    public function deleteBoothsInZone(Request $request, $zoneName)
    {
        // Check permission for canvas editing
        if (!auth()->user()->hasPermission('booths.canvas.edit') && !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.'
            ], 403);
        }
        
        try {
            $validated = $request->validate([
                'mode' => 'required|in:all,specific,range',
                'booth_ids' => 'required_if:mode,specific|array',
                'booth_ids.*' => 'exists:booth,id',
                'from' => 'required_if:mode,range|nullable|integer|min:1|max:9999',
                'to' => 'required_if:mode,range|nullable|integer|min:1|max:9999',
            ]);

            $deletedBooths = [];
            $errors = [];
            $mode = $validated['mode'];

            if ($mode === 'all') {
                // Delete all booths in zone
                $zoneBooths = Booth::where('booth_number', 'LIKE', $zoneName . '%')->get();
                
                foreach ($zoneBooths as $booth) {
                    try {
                        $boothNumber = $booth->booth_number;
                        $booth->delete();
                        $deletedBooths[] = $boothNumber;
                    } catch (\Exception $e) {
                        $errors[] = [
                            'booth_number' => $booth->booth_number,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            } elseif ($mode === 'specific') {
                // Delete specific booths by ID
                $boothIds = $validated['booth_ids'] ?? [];
                
                foreach ($boothIds as $boothId) {
                    try {
                        $booth = Booth::findOrFail($boothId);
                        $boothNumber = $booth->booth_number;
                        $booth->delete();
                        $deletedBooths[] = $boothNumber;
                    } catch (\Exception $e) {
                        $errors[] = [
                            'booth_id' => $boothId,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            } elseif ($mode === 'range') {
                // Delete booths in range
                $from = $validated['from'];
                $to = $validated['to'];
                
                if ($from > $to) {
                    return response()->json([
                        'status' => 422,
                        'message' => '"From" number must be less than or equal to "To" number'
                    ], 422);
                }
                
                // Try different formats (2, 3, 4 digits)
                for ($i = $from; $i <= $to; $i++) {
                    for ($format = 2; $format <= 4; $format++) {
                        $boothNumber = $zoneName . str_pad($i, $format, '0', STR_PAD_LEFT);
                        $booth = Booth::where('booth_number', $boothNumber)->first();
                        
                        if ($booth) {
                            try {
                                $booth->delete();
                                $deletedBooths[] = $boothNumber;
                                break; // Found and deleted, move to next number
                            } catch (\Exception $e) {
                                $errors[] = [
                                    'booth_number' => $boothNumber,
                                    'error' => $e->getMessage()
                                ];
                            }
                        }
                    }
                }
            }

            $message = count($deletedBooths) . ' booth(s) deleted successfully from Zone ' . $zoneName;
            if (count($errors) > 0) {
                $message .= '. ' . count($errors) . ' booth(s) failed to delete.';
            }

            return response()->json([
                'status' => 200,
                'message' => $message,
                'deleted' => $deletedBooths,
                'errors' => $errors
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error deleting booths in zone: ' . $e->getMessage(), [
                'zone' => $zoneName,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 500,
                'message' => 'Error deleting booths: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save zone settings (floor-plan-specific)
     */
    public function saveZoneSettings(Request $request, $zoneName)
    {
        try {
            $validated = $request->validate([
                'width' => 'required|integer|min:5',
                'height' => 'required|integer|min:5',
                'rotation' => 'required|numeric',
                'zIndex' => 'required|integer|min:1|max:1000',
                'borderRadius' => 'required|numeric|min:0|max:50',
                'borderWidth' => 'required|numeric|min:0|max:10',
                'opacity' => 'required|numeric|min:0|max:1',
                'price' => 'nullable|numeric|min:0',
                'floor_plan_id' => 'required|exists:floor_plans,id',
            ]);

            $floorPlanId = $validated['floor_plan_id'];
            ZoneSetting::saveZoneSettings($zoneName, $validated, $floorPlanId);
            
            return response()->json([
                'status' => 200,
                'message' => 'Zone settings saved successfully.',
                'zone_name' => $zoneName,
                'floor_plan_id' => $floorPlanId,
                'settings' => $validated
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving zone settings: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 500,
                'message' => 'Error saving zone settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Book a booth with client information
     */
    public function bookBooth(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validate([
                'booth_id' => 'required|integer|exists:booth,id',
                'client_id' => 'nullable|integer|exists:client,id',
                'name' => 'required|string|max:255',
                'sex' => 'nullable|integer|in:1,2,3',
                'company' => 'required|string|max:255',
                'position' => 'nullable|string|max:255',
                'phone_number' => 'required|string|max:50',
                'email' => 'required|email|max:191',
                'address' => 'required|string',
                'tax_id' => 'nullable|string|max:50',
                'website' => 'nullable|url|max:255',
                'notes' => 'nullable|string',
                'status' => 'required|integer|in:2,3,5', // 2=Confirmed, 3=Reserved, 5=Paid
                'type' => 'nullable|integer|in:1,2,3', // 1=Regular, 2=Special, 3=Temporary
            ]);

            // Normalize sex to integer (1=Male, 2=Female, 3=Other)
            $sex = null;
            if (isset($validated['sex'])) {
                $sexVal = $validated['sex'];
                if (is_numeric($sexVal)) {
                    $sexInt = (int) $sexVal;
                    if (in_array($sexInt, [1,2,3])) {
                        $sex = $sexInt;
                    }
                } else {
                    $sexStr = strtolower(trim((string) $sexVal));
                    if (in_array($sexStr, ['male','m'])) $sex = 1;
                    elseif (in_array($sexStr, ['female','f'])) $sex = 2;
                    elseif (in_array($sexStr, ['other','o'])) $sex = 3;
                }
            }

            // Find the booth with lock to prevent race conditions
            $booth = Booth::where('id', $validated['booth_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // Check if booth is available before booking
            if (!in_array($booth->status, [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])) {
                DB::rollBack();
                return response()->json([
                    'status' => 403,
                    'message' => 'Booth is not available for booking. Current status: ' . $booth->getStatusLabel()
                ], 403);
            }

            // Check if client_id is provided (from search/selection)
            $client = null;
            if (isset($validated['client_id']) && !empty($validated['client_id'])) {
                $client = Client::find($validated['client_id']);
            }
            
            // If client not found by ID, check by email or phone number
            if (!$client) {
                $client = Client::where('email', $validated['email'])
                    ->orWhere('phone_number', $validated['phone_number'])
                    ->first();
            }

            // Create or update client with ALL required information
            $clientData = [
                'name' => $validated['name'],
                'sex' => $sex ?? null,
                'company' => $validated['company'],
                'position' => $validated['position'] ?? null,
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'tax_id' => $validated['tax_id'] ?? null,
                'website' => $validated['website'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ];

            if ($client) {
                // Update existing client with latest information
                $client->update($clientData);
            } else {
                // Create new client
                $client = Client::create($clientData);
            }

            // Get user ID (ensure it's an integer)
            $userId = auth()->user()->id ?? null;
            if ($userId) {
                $userId = (int) $userId;
            }

            // Map status to booking type OR use provided type
            // Booking types: 1=Regular=RESERVED, 2=Special=CONFIRMED, 3=Temporary=RESERVED
            // If type is provided, use it; otherwise map from status
            $bookingType = $validated['type'] ?? null;
            if (!$bookingType) {
                // Map status to booking type
                if ($validated['status'] == Booth::STATUS_CONFIRMED || $validated['status'] == Booth::STATUS_PAID) {
                    $bookingType = 2; // Special/Confirmed or Paid
                } else {
                    $bookingType = 1; // Regular/Reserved
                }
            }
            
            // Ensure status matches booking type for consistency
            // Type 1 (Regular) or 3 (Temporary) = RESERVED
            // Type 2 (Special) = CONFIRMED (or PAID if status is 5)
            $finalStatus = $validated['status'];
            if ($bookingType == 1 || $bookingType == 3) {
                // Regular or Temporary should be RESERVED
                if ($finalStatus != Booth::STATUS_RESERVED) {
                    $finalStatus = Booth::STATUS_RESERVED;
                }
            } elseif ($bookingType == 2) {
                // Special should be CONFIRMED (or PAID if explicitly set)
                if ($finalStatus == Booth::STATUS_PAID) {
                    $finalStatus = Booth::STATUS_PAID;
                } else {
                    $finalStatus = Booth::STATUS_CONFIRMED;
                }
            }

            // Get floor plan and event from booth
            $floorPlanId = $booth->floor_plan_id;
            $eventId = null;
            
            if ($floorPlanId) {
                $floorPlan = FloorPlan::find($floorPlanId);
                $eventId = $floorPlan ? $floorPlan->event_id : null;
            }

            // Create Book record to link booking with floor plan and event
            $book = Book::create([
                'event_id' => $eventId,
                'floor_plan_id' => $floorPlanId,
                'clientid' => $client->id,
                'boothid' => json_encode([$booth->id]),
                'date_book' => now(),
                'userid' => $userId,
                'type' => $bookingType,
            ]);

            // Update booth with client, status, and book ID
            $booth->update([
                'client_id' => $client->id,
                'status' => $finalStatus,
                'userid' => $userId,
                'bookid' => $book->id,
            ]);
            
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Booth booked successfully!',
                'booth_id' => $booth->id,
                'booth_number' => $booth->booth_number,
                'book_id' => $book->id,
                'client_id' => $client->id,
                'client_name' => $client->name,
                'client_company' => $client->company,
                'status' => $booth->status,
                'booking_type' => $bookingType,
                'floor_plan_id' => $floorPlanId
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error booking booth: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 500,
                'message' => 'Error booking booth: ' . $e->getMessage()
            ], 500);
        }
    }
}

