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
use App\Models\AffiliateClick;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\DebugLogger;

class BoothController extends Controller
{
    /**
     * Display a listing of booths (Floor Plan or Management Table)
     */
    public function index(Request $request)
    {
        // #region agent log
        DebugLogger::log(['request_method'=>$request->method(),'user_authenticated'=>auth()->check()], 'BoothController.php:22', 'BoothController::index() called');
        // #endregion
        
        // Check if user wants canvas view instead of table (default is now table)
        $view = $request->input('view', 'table'); // 'table' (default) or 'canvas'
        
        // If view is 'table', show management table interface
        if ($view === 'table') {
            return $this->managementTable($request);
        }
        
        // If view is 'canvas' (or anything else), render the canvas/floor plan designer view
        // This is the original floor plan designer where users can design and edit booths visually
        
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
        
        // Get booth status settings for custom colors (filtered by current floor plan)
        try {
            $statusSettings = \App\Models\BoothStatusSetting::getActiveStatuses($currentFloorPlan ? $currentFloorPlan->id : null);
            $statusColors = \App\Models\BoothStatusSetting::getStatusColors($currentFloorPlan ? $currentFloorPlan->id : null);
        } catch (\Exception $e) {
            // If table doesn't exist yet or error occurs, use empty collections
            \Log::warning('Error loading booth status settings: ' . $e->getMessage());
            $statusSettings = collect([]);
            $statusColors = [];
        }
        
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
            'statusSettings',
            'statusColors',
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
            'status' => 'required|integer',
            'category_id' => 'nullable|exists:category,id',
            'asset_id' => 'nullable|exists:asset,id',
            'booth_type_id' => 'nullable|exists:booth_type,id',
            'floor_plan_id' => 'required|exists:floor_plans,id',
            'client_id' => 'nullable|exists:client,id',
            'description' => 'nullable|string|max:2000',
            'features' => 'nullable|string|max:2000',
            'capacity' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|numeric|min:0',
            'electricity_power' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
            'booth_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120',
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
            $errorMessage = !empty($validated['floor_plan_id']) 
                ? 'This booth number already exists in this floor plan. Please choose a different number.'
                : 'This booth number already exists. Please choose a different number.';
            
            // Return JSON if requested (for AJAX)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['booth_number' => [$errorMessage]]
                ], 422);
            }
            
            return back()
                ->withInput()
                ->withErrors(['booth_number' => $errorMessage]);
        }

        // Handle image upload
        if ($request->hasFile('booth_image')) {
            $image = $request->file('booth_image');
            $imageName = 'booth_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'images/booths';
            
            // Create directory if it doesn't exist
            $fullPath = public_path($imagePath);
            if (!file_exists($fullPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($fullPath, 0755, true);
            }
            
            // Move uploaded file
            $image->move($fullPath, $imageName);
            $validated['booth_image'] = $imagePath . '/' . $imageName;
        }
        
        $booth = Booth::create($validated);

        // Send notification about booth creation
        try {
            \App\Services\NotificationService::notifyBoothAction('created', $booth, $booth->userid);
        } catch (\Exception $e) {
            \Log::error('Failed to send booth creation notification: ' . $e->getMessage());
        }

        // Return JSON if requested (for AJAX)
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Booth created successfully.',
                'booth' => $booth
            ]);
        }

        // Preserve floor_plan_id in redirect if specified
        $redirectUrl = route('booths.index', ['view' => 'table']);
        if (!empty($validated['floor_plan_id'])) {
            $redirectUrl .= '&floor_plan_id=' . $validated['floor_plan_id'];
        }

        return redirect($redirectUrl)
            ->with('success', 'Booth created successfully.');
    }

    /**
     * Display the specified booth
     */
    public function show(Booth $booth, Request $request)
    {
        try {
            $booth->load(['client', 'user', 'category', 'subCategory', 'asset', 'boothType', 'book', 'floorPlan']);
            
            // Check if request wants JSON (multiple ways to detect)
            $wantsJson = $request->expectsJson() || 
                        $request->wantsJson() || 
                        $request->ajax() || 
                        $request->header('Accept') === 'application/json' ||
                        str_contains($request->header('Accept', ''), 'application/json') ||
                        $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                        $request->has('json') || // Check for ?json=1 parameter
                        $request->input('json') == '1';
            
            // Return JSON if requested (for AJAX)
            if ($wantsJson) {
                return response()->json([
                    'id' => $booth->id,
                    'booth_number' => $booth->booth_number,
                    'floor_plan_id' => $booth->floor_plan_id,
                    'booth_type_id' => $booth->booth_type_id,
                    'type' => $booth->type,
                    'price' => $booth->price,
                    'status' => $booth->status,
                    'client_id' => $booth->client_id,
                    'category_id' => $booth->category_id,
                    'sub_category_id' => $booth->sub_category_id,
                    'asset_id' => $booth->asset_id,
                    'area_sqm' => $booth->area_sqm,
                    'capacity' => $booth->capacity,
                    'electricity_power' => $booth->electricity_power,
                    'description' => $booth->description,
                    'features' => $booth->features,
                    'notes' => $booth->notes,
                    'booth_image' => $booth->booth_image ? asset($booth->booth_image) : null,
                ]);
            }
            
            return view('booths.show', compact('booth'));
        } catch (\Exception $e) {
            \Log::error('Error in BoothController@show: ' . $e->getMessage());
            
            // If JSON was requested, return JSON error
            if ($request->expectsJson() || 
                $request->wantsJson() || 
                $request->ajax() || 
                str_contains($request->header('Accept', ''), 'application/json')) {
                return response()->json([
                    'error' => 'Failed to load booth data',
                    'message' => $e->getMessage()
                ], 500);
            }
            
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified booth
     * Redirects to management table with edit parameter to auto-open modal
     */
    public function edit(Booth $booth, Request $request)
    {
        // Redirect to management table with edit parameter
        // The JavaScript in management.blade.php will auto-open the edit modal
        $queryParams = ['view' => 'table', 'edit' => $booth->id];
        
        if ($booth->floor_plan_id) {
            $queryParams['floor_plan_id'] = $booth->floor_plan_id;
        }
        
        return redirect()->route('booths.index', $queryParams);
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
            'description' => 'nullable|string|max:2000',
            'features' => 'nullable|string|max:2000',
            'capacity' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|numeric|min:0',
            'electricity_power' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
            'booth_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
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
            $errorMessage = $floorPlanId
                ? 'This booth number already exists in this floor plan. Please choose a different number.'
                : 'This booth number already exists. Please choose a different number.';
            
            // Return JSON if requested (for AJAX)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['booth_number' => [$errorMessage]]
                ], 422);
            }
            
            return back()
                ->withInput()
                ->withErrors(['booth_number' => $errorMessage]);
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

        // Handle image upload
        if ($request->hasFile('booth_image')) {
            $image = $request->file('booth_image');
            $imageName = 'booth_' . $booth->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = 'images/booths';
            
            // Create directory if it doesn't exist
            $fullPath = public_path($imagePath);
            if (!file_exists($fullPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($fullPath, 0755, true);
            }
            
            // Delete old image if exists
            if ($booth->booth_image && file_exists(public_path($booth->booth_image))) {
                \Illuminate\Support\Facades\File::delete(public_path($booth->booth_image));
            }
            
            // Move uploaded file
            $image->move($fullPath, $imageName);
            $validated['booth_image'] = $imagePath . '/' . $imageName;
        }

        // Check if status changed
        $oldStatus = $booth->status;
        $newStatus = $validated['status'] ?? $booth->status;
        
        $booth->update($validated);
        
        // Refresh to get updated data
        $booth->refresh();

        // Send notification about booth update
        try {
            if ($oldStatus != $newStatus) {
                \App\Services\NotificationService::notifyBoothStatusChange($booth, $oldStatus, $newStatus);
            } else {
                \App\Services\NotificationService::notifyBoothAction('updated', $booth, $booth->userid);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send booth update notification: ' . $e->getMessage());
        }

        // Return JSON if requested (for AJAX)
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Booth updated successfully.',
                'booth' => $booth
            ]);
        }

        // Preserve floor_plan_id in redirect
        $redirectUrl = route('booths.index', ['view' => 'table']);
        if (!empty($validated['floor_plan_id'])) {
            $redirectUrl .= '&floor_plan_id=' . $validated['floor_plan_id'];
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

        // Store booth info before deletion for notification
        $boothNumber = $booth->booth_number;
        $boothUserId = $booth->userid;
        
        $booth->delete();

        // Send notification about booth deletion
        try {
            // Create a temporary booth object for notification
            $tempBooth = new Booth();
            $tempBooth->booth_number = $boothNumber;
            $tempBooth->userid = $boothUserId;
            \App\Services\NotificationService::notifyBoothAction('deleted', $tempBooth, $boothUserId);
        } catch (\Exception $e) {
            \Log::error('Failed to send booth deletion notification: ' . $e->getMessage());
        }

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
                $oldStatus = $booth->status;
                $booth->update(['status' => Booth::STATUS_CONFIRMED]);
                
                // Send notification about status change
                try {
                    \App\Services\NotificationService::notifyBoothStatusChange($booth, $oldStatus, Booth::STATUS_CONFIRMED);
                } catch (\Exception $e) {
                    \Log::error('Failed to send status change notification: ' . $e->getMessage());
                }
                
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
                $oldStatus = $booth->status;
                $booth->update(['status' => Booth::STATUS_PAID]);
                
                // Send notification about payment and status change
                try {
                    \App\Services\NotificationService::notifyPaymentReceived($booth, $booth->price ?? 0);
                    \App\Services\NotificationService::notifyBoothStatusChange($booth, $oldStatus, Booth::STATUS_PAID);
                } catch (\Exception $e) {
                    \Log::error('Failed to send payment notification: ' . $e->getMessage());
                }
                
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
            // Clean up request data - convert empty strings to null and ensure proper types
            $data = $request->all();
            foreach ($data as $key => $value) {
                if ($value === '' || $value === 'null' || $value === 'undefined') {
                    $data[$key] = null;
                }
            }
            $request->merge($data);
            
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
                // Allow empty strings to be converted to null
            ], [
                'z_index.integer' => 'Z-index must be an integer between 1 and 1000.',
                'z_index.min' => 'Z-index must be at least 1.',
                'z_index.max' => 'Z-index must not exceed 1000.',
                'font_size.integer' => 'Font size must be an integer between 8 and 48.',
                'font_size.min' => 'Font size must be at least 8.',
                'font_size.max' => 'Font size must not exceed 48.',
                'border_width.integer' => 'Border width must be an integer between 0 and 10.',
                'border_width.min' => 'Border width must be at least 0.',
                'border_width.max' => 'Border width must not exceed 10.',
                'border_radius.integer' => 'Border radius must be an integer between 0 and 50.',
                'border_radius.min' => 'Border radius must be at least 0.',
                'border_radius.max' => 'Border radius must not exceed 50.',
                'opacity.numeric' => 'Opacity must be a number between 0 and 1.',
                'opacity.min' => 'Opacity must be at least 0.',
                'opacity.max' => 'Opacity must not exceed 1.',
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
            // Ensure numeric values are properly cast
            $booth->position_x = isset($validated['position_x']) && $validated['position_x'] !== null ? (float)$validated['position_x'] : null;
            $booth->position_y = isset($validated['position_y']) && $validated['position_y'] !== null ? (float)$validated['position_y'] : null;
            $booth->width = isset($validated['width']) && $validated['width'] !== null ? (float)$validated['width'] : null;
            $booth->height = isset($validated['height']) && $validated['height'] !== null ? (float)$validated['height'] : null;
            $booth->rotation = isset($validated['rotation']) && $validated['rotation'] !== null ? (float)$validated['rotation'] : 0;
            $booth->z_index = isset($validated['z_index']) && $validated['z_index'] !== null ? (int)$validated['z_index'] : 10;
            $booth->font_size = isset($validated['font_size']) && $validated['font_size'] !== null ? (int)$validated['font_size'] : 14;
            $booth->border_width = isset($validated['border_width']) && $validated['border_width'] !== null ? (int)$validated['border_width'] : 2;
            $booth->border_radius = isset($validated['border_radius']) && $validated['border_radius'] !== null ? (int)$validated['border_radius'] : 6;
            $booth->opacity = isset($validated['opacity']) && $validated['opacity'] !== null ? (float)$validated['opacity'] : 1.00;
            
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
                'zone_about' => 'nullable|string|max:500',
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

                        // Get zone appearance settings (floor-plan-specific)
                        $zoneAppearance = [
                            'background_color' => $zoneSettings['background_color'] ?? null,
                            'border_color' => $zoneSettings['border_color'] ?? null,
                            'text_color' => $zoneSettings['text_color'] ?? null,
                            'font_weight' => $zoneSettings['font_weight'] ?? null,
                            'font_family' => $zoneSettings['font_family'] ?? null,
                            'text_align' => $zoneSettings['text_align'] ?? null,
                            'box_shadow' => $zoneSettings['box_shadow'] ?? null,
                        ];
                        
                        $booth = Booth::create([
                            'booth_number' => $boothNumber,
                            'type' => 2, // Default type
                            'price' => $zonePrice, // Use zone price
                            'status' => Booth::STATUS_AVAILABLE,
                            'floor_plan_id' => $floorPlanId, // Assign to current floor plan
                            // Apply zone appearance settings (overwrite defaults)
                            'background_color' => $zoneAppearance['background_color'],
                            'border_color' => $zoneAppearance['border_color'],
                            'text_color' => $zoneAppearance['text_color'],
                            'font_weight' => $zoneAppearance['font_weight'],
                            'font_family' => $zoneAppearance['font_family'],
                            'text_align' => $zoneAppearance['text_align'],
                            'box_shadow' => $zoneAppearance['box_shadow'],
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

                        // Get zone appearance settings (floor-plan-specific)
                        $zoneAppearance = [
                            'background_color' => $zoneSettings['background_color'] ?? null,
                            'border_color' => $zoneSettings['border_color'] ?? null,
                            'text_color' => $zoneSettings['text_color'] ?? null,
                            'font_weight' => $zoneSettings['font_weight'] ?? null,
                            'font_family' => $zoneSettings['font_family'] ?? null,
                            'text_align' => $zoneSettings['text_align'] ?? null,
                            'box_shadow' => $zoneSettings['box_shadow'] ?? null,
                        ];
                        
                        $booth = Booth::create([
                            'booth_number' => $boothNumber,
                            'type' => 2, // Default type
                            'price' => $zonePrice, // Use zone price
                            'status' => Booth::STATUS_AVAILABLE,
                            'floor_plan_id' => $floorPlanId, // Assign to current floor plan
                            // Apply zone appearance settings (overwrite defaults)
                            'background_color' => $zoneAppearance['background_color'],
                            'border_color' => $zoneAppearance['border_color'],
                            'text_color' => $zoneAppearance['text_color'],
                            'font_weight' => $zoneAppearance['font_weight'],
                            'font_family' => $zoneAppearance['font_family'],
                            'text_align' => $zoneAppearance['text_align'],
                            'box_shadow' => $zoneAppearance['box_shadow'],
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

            // Save zone_about if provided (even if empty, to allow clearing it)
            if ($request->has('zone_about')) {
                $zoneAbout = $request->input('zone_about');
                $existingSettings = ZoneSetting::getZoneDefaults($zoneName, $floorPlanId);
                $settingsToSave = array_merge($existingSettings, [
                    'zone_about' => $zoneAbout ?: null
                ]);
                ZoneSetting::saveZoneSettings($zoneName, $settingsToSave, $floorPlanId);
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
                'floor_plan_id' => 'required|exists:floor_plans,id',
                'force_delete_booked' => 'nullable|boolean', // Allow deletion of booked booths if true
            ]);

            $deletedBooths = [];
            $bookedBooths = [];
            $errors = [];
            $mode = $validated['mode'];
            $floorPlanId = $validated['floor_plan_id'];
            $forceDelete = $validated['force_delete_booked'] ?? false;

            if ($mode === 'all') {
                // Delete all booths in zone (ONLY in the specified floor plan)
                $zoneBooths = Booth::where('booth_number', 'LIKE', $zoneName . '%')
                    ->where('floor_plan_id', $floorPlanId)
                    ->get();
                
                foreach ($zoneBooths as $booth) {
                    try {
                        $boothNumber = $booth->booth_number;
                        
                        // CRITICAL: Check if booth has an active booking
                        if ($booth->bookid && !$forceDelete) {
                            // Booth is booked - skip deletion unless forced
                            $bookedBooths[] = [
                                'booth_number' => $boothNumber,
                                'status' => $booth->getStatusLabel(),
                                'client' => $booth->client ? $booth->client->company : 'Unknown',
                            ];
                            continue; // Skip this booth
                        }
                        
                        // CRITICAL: Handle bookings before deleting booth
                        // If booth has a booking (bookid), update the booking's booth list
                        if ($booth->bookid) {
                            $book = Book::find($booth->bookid);
                            if ($book) {
                                $boothIds = json_decode($book->boothid, true) ?? [];
                                $boothIds = array_filter($boothIds, function($id) use ($booth) {
                                    return $id != $booth->id;
                                });
                                
                                if (count($boothIds) > 0) {
                                    // Update booking with remaining booths
                                    $book->boothid = json_encode(array_values($boothIds));
                                    $book->save();
                                } else {
                                    // No booths left in booking, delete the booking
                                    $book->delete();
                                }
                            }
                        }
                        
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
                // Delete specific booths by ID (ONLY if they belong to the specified floor plan)
                $boothIds = $validated['booth_ids'] ?? [];
                
                foreach ($boothIds as $boothId) {
                    try {
                        $booth = Booth::where('id', $boothId)
                            ->where('floor_plan_id', $floorPlanId)
                            ->firstOrFail();
                        $boothNumber = $booth->booth_number;
                        
                        // CRITICAL: Check if booth has an active booking
                        if ($booth->bookid && !$forceDelete) {
                            $bookedBooths[] = [
                                'booth_number' => $boothNumber,
                                'status' => $booth->getStatusLabel(),
                                'client' => $booth->client ? $booth->client->company : 'Unknown',
                            ];
                            continue; // Skip this booth
                        }
                        
                        // CRITICAL: Handle bookings before deleting booth
                        if ($booth->bookid) {
                            $book = Book::find($booth->bookid);
                            if ($book) {
                                $bookBoothIds = json_decode($book->boothid, true) ?? [];
                                $bookBoothIds = array_filter($bookBoothIds, function($id) use ($booth) {
                                    return $id != $booth->id;
                                });
                                
                                if (count($bookBoothIds) > 0) {
                                    $book->boothid = json_encode(array_values($bookBoothIds));
                                    $book->save();
                                } else {
                                    $book->delete();
                                }
                            }
                        }
                        
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
                // Delete booths in range (ONLY in the specified floor plan)
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
                        $booth = Booth::where('booth_number', $boothNumber)
                            ->where('floor_plan_id', $floorPlanId)
                            ->first();
                        
                        if ($booth) {
                            try {
                                // CRITICAL: Check if booth has an active booking
                                if ($booth->bookid && !$forceDelete) {
                                    $bookedBooths[] = [
                                        'booth_number' => $boothNumber,
                                        'status' => $booth->getStatusLabel(),
                                        'client' => $booth->client ? $booth->client->company : 'Unknown',
                                    ];
                                    break; // Skip this booth
                                }
                                
                                // CRITICAL: Handle bookings before deleting booth
                                if ($booth->bookid) {
                                    $book = Book::find($booth->bookid);
                                    if ($book) {
                                        $bookBoothIds = json_decode($book->boothid, true) ?? [];
                                        $bookBoothIds = array_filter($bookBoothIds, function($id) use ($booth) {
                                            return $id != $booth->id;
                                        });
                                        
                                        if (count($bookBoothIds) > 0) {
                                            $book->boothid = json_encode(array_values($bookBoothIds));
                                            $book->save();
                                        } else {
                                            $book->delete();
                                        }
                                    }
                                }
                                
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

            $floorPlan = FloorPlan::find($floorPlanId);
            $floorPlanName = $floorPlan ? $floorPlan->name : 'Floor Plan #' . $floorPlanId;
            
            $message = count($deletedBooths) . ' booth(s) deleted successfully from Zone ' . $zoneName . ' in ' . $floorPlanName;
            
            if (count($bookedBooths) > 0) {
                $message .= '. WARNING: ' . count($bookedBooths) . ' booth(s) with active bookings were SKIPPED to prevent data loss.';
            }
            
            if (count($errors) > 0) {
                $message .= '. ' . count($errors) . ' booth(s) failed to delete.';
            }

            return response()->json([
                'status' => count($bookedBooths) > 0 ? 206 : 200, // 206 = Partial Content (some skipped)
                'message' => $message,
                'deleted' => $deletedBooths,
                'booked_booths_skipped' => $bookedBooths,
                'errors' => $errors,
                'floor_plan_id' => $floorPlanId,
                'floor_plan_name' => $floorPlanName,
                'warning' => count($bookedBooths) > 0 ? 'Some booths with active bookings were not deleted to protect booking data.' : null
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
     * Delete specific booths by ID from any zone and any floor plan.
     * Same safety and booking-handling logic as deleteBoothsInZone mode=specific,
     * but does not require or filter by zone or floor_plan_id.
     */
    public function deleteBoothsByIds(Request $request)
    {
        if (!auth()->user()->hasPermission('booths.canvas.edit') && !auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'booth_ids' => 'required|array',
                'booth_ids.*' => 'exists:booth,id',
                'force_delete_booked' => 'nullable|boolean',
            ]);

            $boothIds = $validated['booth_ids'];
            $forceDelete = $validated['force_delete_booked'] ?? false;
            $deletedBooths = [];
            $bookedBooths = [];
            $errors = [];

            foreach ($boothIds as $boothId) {
                try {
                    $booth = Booth::findOrFail($boothId);
                    $boothNumber = $booth->booth_number;
                    $floorPlanId = $booth->floor_plan_id;
                    $floorPlanName = $booth->floorPlan ? $booth->floorPlan->name : ('Floor Plan #' . $floorPlanId);

                    if ($booth->bookid && !$forceDelete) {
                        $bookedBooths[] = [
                            'booth_number' => $boothNumber,
                            'booth_id' => $booth->id,
                            'floor_plan' => $floorPlanName,
                            'status' => $booth->getStatusLabel(),
                            'client' => $booth->client ? $booth->client->company : 'Unknown',
                        ];
                        continue;
                    }

                    if ($booth->bookid) {
                        $book = Book::find($booth->bookid);
                        if ($book) {
                            $bookBoothIds = json_decode($book->boothid, true) ?? [];
                            $bookBoothIds = array_filter($bookBoothIds, function ($id) use ($booth) {
                                return (int) $id !== (int) $booth->id;
                            });
                            if (count($bookBoothIds) > 0) {
                                $book->boothid = json_encode(array_values($bookBoothIds));
                                $book->save();
                            } else {
                                $book->delete();
                            }
                        }
                    }

                    $booth->delete();
                    $deletedBooths[] = ['booth_number' => $boothNumber, 'floor_plan' => $floorPlanName];
                } catch (\Exception $e) {
                    $errors[] = [
                        'booth_id' => $boothId,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            $message = count($deletedBooths) . ' booth(s) deleted successfully from any zone/floor plan.';
            if (count($bookedBooths) > 0) {
                $message .= ' WARNING: ' . count($bookedBooths) . ' booth(s) with active bookings were SKIPPED.';
            }
            if (count($errors) > 0) {
                $message .= ' ' . count($errors) . ' booth(s) failed to delete.';
            }

            return response()->json([
                'status' => count($bookedBooths) > 0 ? 206 : 200,
                'message' => $message,
                'deleted' => array_column($deletedBooths, 'booth_number'),
                'deleted_details' => $deletedBooths,
                'booked_booths_skipped' => $bookedBooths,
                'errors' => $errors,
                'warning' => count($bookedBooths) > 0 ? 'Some booths with active bookings were not deleted to protect booking data.' : null,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error deleting booths by IDs: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status' => 500,
                'message' => 'Error deleting booths: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save zone settings (floor-plan-specific)
     */
    public function saveZoneSettings(Request $request, $zoneName)
    {
        try {
            // Get existing zone settings to merge with new ones
            $floorPlanId = $request->input('floor_plan_id');
            if (!$floorPlanId) {
                $defaultFloorPlan = FloorPlan::where('is_default', true)->first();
                $floorPlanId = $defaultFloorPlan ? $defaultFloorPlan->id : null;
            }
            
            if (!$floorPlanId) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Floor plan ID is required'
                ], 400);
            }
            
            $existingSettings = ZoneSetting::getZoneDefaults($zoneName, $floorPlanId);
            
            // Build validation rules - make fields optional to allow partial updates
            $rules = [
                'floor_plan_id' => 'required|exists:floor_plans,id',
                // Shape/Layout fields (optional - can be saved separately)
                'width' => 'nullable|integer|min:5',
                'height' => 'nullable|integer|min:5',
                'rotation' => 'nullable|numeric',
                'zIndex' => 'nullable|integer|min:1|max:1000',
                // Appearance/Style fields (optional - can be saved separately)
                'borderRadius' => 'nullable|numeric|min:0|max:50',
                'borderWidth' => 'nullable|numeric|min:0|max:10',
                'opacity' => 'nullable|numeric|min:0|max:1',
                'price' => 'nullable|numeric|min:0',
                'zone_about' => 'nullable|string|max:1000',
                'background_color' => 'nullable|string|max:50',
                'border_color' => 'nullable|string|max:50',
                'text_color' => 'nullable|string|max:50',
                'font_weight' => 'nullable|string|max:20',
                'font_family' => 'nullable|string|max:255',
                'text_align' => 'nullable|string|max:20',
                'box_shadow' => 'nullable|string|max:255',
            ];
            
            $validated = $request->validate($rules);
            
            // Merge with existing settings to preserve values not being updated
            $settingsToSave = array_merge($existingSettings, $validated);
            
            // Convert camelCase to snake_case for database
            $dbSettings = [
                'width' => $settingsToSave['width'] ?? $existingSettings['width'] ?? 80,
                'height' => $settingsToSave['height'] ?? $existingSettings['height'] ?? 50,
                'rotation' => $settingsToSave['rotation'] ?? $existingSettings['rotation'] ?? 0,
                'z_index' => $settingsToSave['zIndex'] ?? $settingsToSave['z_index'] ?? $existingSettings['zIndex'] ?? $existingSettings['z_index'] ?? 10,
                'border_radius' => $settingsToSave['borderRadius'] ?? $settingsToSave['border_radius'] ?? $existingSettings['borderRadius'] ?? $existingSettings['border_radius'] ?? 6,
                'border_width' => $settingsToSave['borderWidth'] ?? $settingsToSave['border_width'] ?? $existingSettings['borderWidth'] ?? $existingSettings['border_width'] ?? 2,
                'opacity' => $settingsToSave['opacity'] ?? $existingSettings['opacity'] ?? 1.0,
                'price' => $settingsToSave['price'] ?? $existingSettings['price'] ?? 500,
                'zone_about' => $settingsToSave['zone_about'] ?? $existingSettings['zone_about'] ?? null,
                'background_color' => $settingsToSave['background_color'] ?? $existingSettings['background_color'] ?? null,
                'border_color' => $settingsToSave['border_color'] ?? $existingSettings['border_color'] ?? null,
                'text_color' => $settingsToSave['text_color'] ?? $existingSettings['text_color'] ?? null,
                'font_weight' => $settingsToSave['font_weight'] ?? $existingSettings['font_weight'] ?? null,
                'font_family' => $settingsToSave['font_family'] ?? $existingSettings['font_family'] ?? null,
                'text_align' => $settingsToSave['text_align'] ?? $existingSettings['text_align'] ?? null,
                'box_shadow' => $settingsToSave['box_shadow'] ?? $existingSettings['box_shadow'] ?? null,
            ];
            
            ZoneSetting::saveZoneSettings($zoneName, $dbSettings, $floorPlanId);
            
            return response()->json([
                'status' => 200,
                'message' => 'Zone settings saved successfully.',
                'zone_name' => $zoneName,
                'floor_plan_id' => $floorPlanId,
                'settings' => $dbSettings
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

            // Get affiliate user ID from cookie or session (first-touch wins)
            $affiliateUserId = null;
            $cookieName = 'affiliate_fp_' . $floorPlanId;

            $cookieData = $request->cookie($cookieName);
            if ($cookieData) {
                $decoded = json_decode($cookieData, true);
                $cookieFloorPlanId = $decoded['floor_plan_id'] ?? null;
                $cookieExpiresAt = $decoded['expires_at'] ?? null;
                if ($cookieFloorPlanId == $floorPlanId && $cookieExpiresAt && time() < (int) $cookieExpiresAt) {
                    $affiliateUserId = (int) ($decoded['affiliate_user_id'] ?? 0);
                }
            }

            // Fallback to existing session if cookie missing but still valid
            if (!$affiliateUserId && session()->has('affiliate_user_id') && session('affiliate_floor_plan_id') == $floorPlanId) {
                if (session()->has('affiliate_expires_at') && now()->lt(session('affiliate_expires_at'))) {
                    $affiliateUserId = (int) session('affiliate_user_id');
                }
            }
            
            // Create Book record to link booking with floor plan and event
            $book = Book::create([
                'event_id' => $eventId,
                'floor_plan_id' => $floorPlanId,
                'clientid' => $client->id,
                'boothid' => json_encode([$booth->id]),
                'date_book' => now(),
                'userid' => $userId,
                'affiliate_user_id' => $affiliateUserId, // Track which sales person's link was used
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

    /**
     * Public view of floor plan (no authentication required, no editing tools)
     * Also handles affiliate tracking via ref parameter
     */
    public function publicView($id, Request $request)
    {
        // Get floor plan
        $floorPlan = FloorPlan::where('is_active', true)->findOrFail($id);
        
        // Handle affiliate tracking from referral parameter (first-touch wins)
        $ref = $request->query('ref');
        $cookieName = 'affiliate_fp_' . $id;
        $trackingApplied = false;

        // Respect existing valid cookie (first sender wins)
        $existingCookie = $request->cookie($cookieName);
        $hasValidCookie = false;
        if ($existingCookie) {
            $decodedCookie = json_decode($existingCookie, true);
            $cookieExpires = $decodedCookie['expires_at'] ?? null;
            if (($decodedCookie['floor_plan_id'] ?? null) == $id && $cookieExpires && time() < (int) $cookieExpires) {
                $hasValidCookie = true;
                // Refresh session for downstream flows
                session([
                    'affiliate_user_id' => (int) ($decodedCookie['affiliate_user_id'] ?? 0),
                    'affiliate_floor_plan_id' => $id,
                    'affiliate_expires_at' => now()->setTimestamp((int) $cookieExpires),
                ]);
            }
        }

        // Process ref token if present (log every valid click; only set cookie if no valid cookie exists)
        if ($ref) {
            try {
                $decoded = base64_decode($ref);
                $parts = explode('|', $decoded);
                if (count($parts) >= 5) {
                    [$affiliateUserId, $affiliateFloorPlanId, $expiryDays, $issuedAt, $signature] = $parts;
                    $affiliateUserId = (int) $affiliateUserId;
                    $affiliateFloorPlanId = (int) $affiliateFloorPlanId;
                    $expiryDays = (int) $expiryDays;
                    $issuedAt = (int) $issuedAt;

                    $payload = implode('|', array_slice($parts, 0, 4));
                    $expectedSignature = hash_hmac('sha256', $payload, config('app.key'));

                    // Validate signature and bounds
                    $allowedDurations = [7, 14, 21, 28, 60, 90];
                    $isValidRef = $signature && hash_equals($expectedSignature, $signature)
                        && $affiliateFloorPlanId === (int) $id
                        && in_array($expiryDays, $allowedDurations, true)
                        && $issuedAt > 0
                        && time() <= ($issuedAt + ($expiryDays * 24 * 60 * 60));

                    if ($isValidRef) {

                        $expiresAt = time() + ($expiryDays * 24 * 60 * 60);

                        // Set tracking only if not already held by valid cookie (first-touch wins)
                        if (!$hasValidCookie) {
                            $cookiePayload = [
                                'affiliate_user_id' => $affiliateUserId,
                                'floor_plan_id' => $id,
                                'expires_at' => $expiresAt,
                                'issued_at' => $issuedAt,
                            ];

                            // Queue cookie (minutes)
                            cookie()->queue(cookie(
                                $cookieName,
                                json_encode($cookiePayload),
                                $expiryDays * 24 * 60,
                                '/',
                                null,
                                false,
                                false,
                                false,
                                'Lax'
                            ));

                            // Mirror to session for server-side flows
                            session([
                                'affiliate_user_id' => $affiliateUserId,
                                'affiliate_floor_plan_id' => $id,
                                'affiliate_expires_at' => now()->addDays($expiryDays),
                            ]);

                            $trackingApplied = true;
                        }

                        // Log click for reporting
                        try {
                            AffiliateClick::create([
                                'affiliate_user_id' => $affiliateUserId,
                                'floor_plan_id' => $id,
                                'ref_code' => $ref,
                                'ip_address' => $request->ip(),
                                'user_agent' => $request->userAgent(),
                                'expires_at' => now()->addSeconds($expiryDays * 24 * 60 * 60),
                            ]);
                        } catch (\Exception $e) {
                            \Log::warning('Failed to log affiliate click', [
                                'error' => $e->getMessage(),
                                'ref' => $ref,
                                'affiliate_user_id' => $affiliateUserId,
                                'floor_plan_id' => $id,
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Invalid affiliate link reference: ' . $e->getMessage(), [
                    'ref' => $ref,
                ]);
            }
        }

        // Prevent re-processing on refresh by redirecting without the ref param
        if ($ref && $trackingApplied) {
            return redirect()->route('floor-plans.public', ['id' => $id, 'tracked' => 1]);
        }
        
        // Get all booths for this floor plan
        $booths = Booth::where('floor_plan_id', $id)
            ->with(['client', 'category', 'subCategory', 'boothType'])
            ->orderBy('booth_number', 'asc')
            ->get();
        
        // Prepare booth data for JavaScript
        $boothsForJS = $booths->map(function($booth) {
            // Get client logo (avatar or cover_image)
            $clientLogo = null;
            if ($booth->client) {
                if ($booth->client->avatar) {
                    $clientLogo = asset($booth->client->avatar);
                } elseif ($booth->client->cover_image) {
                    $clientLogo = asset($booth->client->cover_image);
                }
            }
            
            return [
                'id' => $booth->id,
                'booth_number' => $booth->booth_number,
                'company' => $booth->client ? $booth->client->company : '',
                'client_logo' => $clientLogo,
                'category' => $booth->category ? $booth->category->name : '',
                'sub_category' => $booth->subCategory ? $booth->subCategory->name : '',
                'status' => $booth->status,
                'status_label' => $booth->getStatusLabel(),
                'price' => $booth->price,
                'booth_type' => $booth->boothType ? $booth->boothType->name : ($booth->type == 1 ? 'Booth' : 'Space Only'),
                'booth_image' => $booth->booth_image ? asset($booth->booth_image) : null,
                'description' => $booth->description ?? '',
                'features' => $booth->features ?? '',
                'capacity' => $booth->capacity ?? null,
                'area_sqm' => $booth->area_sqm ?? null,
                'electricity_power' => $booth->electricity_power ?? '',
                'notes' => $booth->notes ?? '',
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
                'background_color' => $booth->background_color,
                'border_color' => $booth->border_color,
                'text_color' => $booth->text_color,
                'font_weight' => $booth->font_weight,
                'font_family' => $booth->font_family,
                'text_align' => $booth->text_align,
                'box_shadow' => $booth->box_shadow,
            ];
        })->values();
        
        // Get canvas settings
        $canvasWidth = $floorPlan->canvas_width ?? 1200;
        $canvasHeight = $floorPlan->canvas_height ?? 800;
        $floorImage = $floorPlan->floor_image;
        
        // Generate absolute URL for image
        $floorImageUrl = null;
        $floorImageExists = false;
        if ($floorImage) {
            $fullPath = public_path($floorImage);
            $floorImageExists = file_exists($fullPath) && is_readable($fullPath);
            if ($floorImageExists) {
                $floorImageUrl = asset($floorImage);
                if (strpos($floorImageUrl, 'http') !== 0) {
                    $floorImageUrl = url($floorImage);
                }
            }
        }
        
        // Get booth status settings for custom colors and labels (filtered by floor plan)
        try {
            $statusSettings = \App\Models\BoothStatusSetting::getActiveStatuses($floorPlan->id);
            $statusColors = \App\Models\BoothStatusSetting::getStatusColors($floorPlan->id);
        } catch (\Exception $e) {
            // If table doesn't exist yet or error occurs, use empty collections
            \Log::warning('Error loading booth status settings: ' . $e->getMessage());
            $statusSettings = collect([]);
            $statusColors = [];
        }

        // Public view actions for logged-in users (controlled by settings)
        $authUser = auth()->user();
        $allowCreateOnPublicView = Setting::getValue('public_view_allow_create_booking', true);
        // Allow when user has Create Bookings permission, or when user's role is Sales (name/slug contains "sales")
        $isSalesRole = $authUser && $authUser->role
            && (stripos($authUser->role->name ?? '', 'sales') !== false || stripos($authUser->role->slug ?? '', 'sales') !== false);
        $canCreateBookingOnPublicView = $authUser
            && $allowCreateOnPublicView
            && ($authUser->hasPermission('bookings.create') || $isSalesRole);
        $restrictCrudToOwnBooking = Setting::getValue('public_view_restrict_crud_to_own_booking', true);
        // Only users with canvas edit permission (or admin) can see "Switch back to Canvas Design"
        $canSwitchToCanvasDesign = $authUser
            && ($authUser->hasPermission('booths.canvas.edit') || $authUser->isAdmin());

        return view('booths.public-view', compact(
            'floorPlan',
            'booths',
            'boothsForJS',
            'canvasWidth',
            'canvasHeight',
            'floorImage',
            'floorImageUrl',
            'floorImageExists',
            'statusSettings',
            'statusColors',
            'authUser',
            'canCreateBookingOnPublicView',
            'restrictCrudToOwnBooking',
            'canSwitchToCanvasDesign'
        ));
    }

    /**
     * Booth Management Table View
     */
    public function managementTable(Request $request)
    {
        // If AJAX request for lazy loading
        if (($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) && $request->has('page')) {
            return $this->lazyLoadBooths($request);
        }

        $query = Booth::with(['client', 'category', 'subCategory', 'boothType', 'floorPlan', 'user']);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booth_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('company', 'like', "%{$search}%")
                                  ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function($catQuery) use ($search) {
                      $catQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by floor plan (only if explicitly requested - default is all floor plans)
        if ($request->filled('floor_plan_id')) {
            $query->where('floor_plan_id', $request->floor_plan_id);
        }
        // Note: By default, load booths from ALL floor plans (no filter applied)
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by booth type
        if ($request->filled('booth_type_id')) {
            $query->where('booth_type_id', $request->booth_type_id);
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Sort
        $sortBy = $request->input('sort_by', 'booth_number');
        $sortDir = $request->input('sort_dir', 'asc');
        $query->orderBy($sortBy, $sortDir);
        
        // Get per-page setting (validate allowed values: 10, 25, 50, 100, 200)
        $perPage = $request->input('per_page', 50);
        $allowedPerPage = [10, 25, 50, 100, 200];
        if (!in_array((int)$perPage, $allowedPerPage)) {
            $perPage = 50; // Default fallback
        }
        
        // Get initial records for pagination
        $booths = $query->paginate($perPage)->withQueryString();
        $total = $booths->total();
        
        // Get filter options
        $floorPlans = FloorPlan::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $boothTypes = BoothType::where('status', 1)->orderBy('name')->get();
        $clients = Client::orderBy('company')->get();
        
        // Statistics
        $stats = [
            'total' => Booth::count(),
            'available' => Booth::where('status', Booth::STATUS_AVAILABLE)->count(),
            'reserved' => Booth::where('status', Booth::STATUS_RESERVED)->count(),
            'confirmed' => Booth::where('status', Booth::STATUS_CONFIRMED)->count(),
            'paid' => Booth::where('status', Booth::STATUS_PAID)->count(),
        ];
        
        // Get booth status settings for management page
        try {
            $statusSettings = \App\Models\BoothStatusSetting::orderBy('floor_plan_id')->orderBy('sort_order')->get();
        } catch (\Exception $e) {
            \Log::warning('Error loading booth status settings: ' . $e->getMessage());
            $statusSettings = collect([]);
        }
        
        return view('booths.management', compact(
            'booths',
            'floorPlans',
            'categories',
            'boothTypes',
            'clients',
            'stats',
            'sortBy',
            'sortDir',
            'statusSettings',
            'perPage'
        ));
    }

    /**
     * Lazy load booths (AJAX endpoint)
     */
    public function lazyLoadBooths(Request $request)
    {
        // Use exact same query structure as managementTable method
        $query = Booth::with(['client', 'category', 'subCategory', 'boothType', 'floorPlan', 'user']);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booth_number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('company', 'like', "%{$search}%")
                                  ->orWhere('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function($catQuery) use ($search) {
                      $catQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by floor plan (only if explicitly requested - default is all floor plans)
        if ($request->filled('floor_plan_id')) {
            $query->where('floor_plan_id', $request->floor_plan_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by booth type
        if ($request->filled('booth_type_id')) {
            $query->where('booth_type_id', $request->booth_type_id);
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Sort
        $sortBy = $request->input('sort_by', 'booth_number');
        $sortDir = $request->input('sort_dir', 'asc');
        $query->orderBy($sortBy, $sortDir);
        
        // Use same pagination as initial load
        $page = $request->input('page', 1);
        // Get per-page setting (validate allowed values: 10, 25, 50, 100, 200)
        $perPage = $request->input('per_page', 50);
        $allowedPerPage = [10, 25, 50, 100, 200];
        if (!in_array((int)$perPage, $allowedPerPage)) {
            $perPage = 50; // Default fallback
        }
        $offset = ($page - 1) * $perPage;
        
        // Get total before pagination
        $total = $query->count();
        
        // Get booths for current page
        $booths = $query->offset($offset)->limit($perPage)->get();
        $hasMore = ($offset + $booths->count()) < $total;
        
        // Render table rows
        $html = '';
        foreach ($booths as $booth) {
            $html .= view('booths.partials.table-row', ['booth' => $booth])->render();
        }
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'hasMore' => $hasMore,
            'currentPage' => $page,
            'total' => $total,
            'perPage' => $perPage,
            'loaded' => $offset + $booths->count()
        ]);
    }

    /**
     * Upload booth image
     */
    public function uploadBoothImage(Request $request, $id)
    {
        try {
            $booth = Booth::findOrFail($id);
            
            $request->validate([
                'booth_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
            ]);
            
            if ($request->hasFile('booth_image')) {
                $image = $request->file('booth_image');
                $imageName = 'booth_' . $booth->id . '_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'images/booths';
                
                // Create directory if it doesn't exist
                $fullPath = public_path($imagePath);
                if (!file_exists($fullPath)) {
                    \Illuminate\Support\Facades\File::makeDirectory($fullPath, 0755, true);
                }
                
                // Delete old image if exists
                if ($booth->booth_image && file_exists(public_path($booth->booth_image))) {
                    \Illuminate\Support\Facades\File::delete(public_path($booth->booth_image));
                }
                
                // Move uploaded file
                $image->move($fullPath, $imageName);
                $booth->booth_image = $imagePath . '/' . $imageName;
                $booth->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Booth image uploaded successfully.',
                    'image_url' => asset($booth->booth_image)
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No image file provided.'
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Error uploading booth image: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload multiple images for a booth (gallery)
     */
    public function uploadBoothGalleryImages(Request $request, $id)
    {
        try {
            $booth = Booth::findOrFail($id);

            $request->validate([
                'images' => 'required|array|min:1|max:10',
                'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max per image
                'image_type' => 'nullable|in:photo,layout,setup,teardown,facility',
                'captions' => 'nullable|array',
                'captions.*' => 'nullable|string|max:500',
            ]);

            $uploadedImages = [];
            $imageType = $request->input('image_type', 'photo');
            $captions = $request->input('captions', []);

            if ($request->hasFile('images')) {
                // Get current max sort order
                $maxSort = \App\Models\BoothImage::where('booth_id', $booth->id)->max('sort_order') ?? 0;

                foreach ($request->file('images') as $index => $image) {
                    $imageName = 'booth_' . $booth->id . '_gallery_' . time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $imagePath = 'images/booths/gallery';

                    // Create directory if it doesn't exist
                    $fullPath = public_path($imagePath);
                    if (!file_exists($fullPath)) {
                        \Illuminate\Support\Facades\File::makeDirectory($fullPath, 0755, true);
                    }

                    // Move uploaded file
                    $image->move($fullPath, $imageName);

                    // Check if this is the first image for the booth
                    $isFirst = \App\Models\BoothImage::where('booth_id', $booth->id)->count() === 0;

                    // Create database record
                    $boothImage = \App\Models\BoothImage::create([
                        'booth_id' => $booth->id,
                        'floor_plan_id' => $booth->floor_plan_id,
                        'image_path' => $imagePath . '/' . $imageName,
                        'image_type' => $imageType,
                        'caption' => $captions[$index] ?? null,
                        'sort_order' => $maxSort + $index + 1,
                        'is_primary' => $isFirst, // First image is primary
                    ]);

                    $uploadedImages[] = [
                        'id' => $boothImage->id,
                        'image_path' => $boothImage->image_path,
                        'image_url' => asset($boothImage->image_path),
                        'type' => $boothImage->image_type,
                        'caption' => $boothImage->caption,
                        'is_primary' => $boothImage->is_primary,
                    ];
                }

                return response()->json([
                    'success' => true,
                    'message' => count($uploadedImages) . ' image(s) uploaded successfully.',
                    'images' => $uploadedImages,
                    'booth_id' => $booth->id,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No images provided.'
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Error uploading booth gallery images: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all images for a booth
     */
    public function getBoothImages($id)
    {
        try {
            $booth = Booth::findOrFail($id);
            $images = \App\Models\BoothImage::where('booth_id', $id)
                ->orderBy('sort_order')
                ->get()
                ->map(function($image) {
                    return [
                        'id' => $image->id,
                        'image_path' => $image->image_path,
                        'image_url' => asset($image->image_path),
                        'type' => $image->image_type,
                        'type_label' => $image->getTypeLabel(),
                        'caption' => $image->caption,
                        'sort_order' => $image->sort_order,
                        'is_primary' => $image->is_primary,
                    ];
                });

            return response()->json([
                'success' => true,
                'images' => $images,
                'count' => $images->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a booth image
     */
    public function deleteBoothImage($boothId, $imageId)
    {
        try {
            $image = \App\Models\BoothImage::where('booth_id', $boothId)
                ->where('id', $imageId)
                ->firstOrFail();

            // Delete physical file
            if (file_exists(public_path($image->image_path))) {
                \Illuminate\Support\Facades\File::delete(public_path($image->image_path));
            }

            // If this was primary, make another image primary
            $wasPrimary = $image->is_primary;
            $image->delete();

            if ($wasPrimary) {
                $nextImage = \App\Models\BoothImage::where('booth_id', $boothId)
                    ->orderBy('sort_order')
                    ->first();
                if ($nextImage) {
                    $nextImage->is_primary = true;
                    $nextImage->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set primary image for a booth
     */
    public function setPrimaryImage($boothId, $imageId)
    {
        try {
            // Remove primary from all images
            \App\Models\BoothImage::where('booth_id', $boothId)
                ->update(['is_primary' => false]);

            // Set new primary
            $image = \App\Models\BoothImage::where('booth_id', $boothId)
                ->where('id', $imageId)
                ->firstOrFail();
            
            $image->is_primary = true;
            $image->save();

            return response()->json([
                'success' => true,
                'message' => 'Primary image updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting primary image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update image sort order
     */
    public function updateImageOrder(Request $request, $boothId)
    {
        try {
            $request->validate([
                'image_ids' => 'required|array',
                'image_ids.*' => 'exists:booth_images,id',
            ]);

            $imageIds = $request->input('image_ids');
            
            foreach ($imageIds as $index => $imageId) {
                \App\Models\BoothImage::where('id', $imageId)
                    ->where('booth_id', $boothId)
                    ->update(['sort_order' => $index + 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Image order updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: ' . $e->getMessage()
            ], 500);
        }
    }
}

