<?php

namespace App\Http\Controllers;

use App\Helpers\DebugLogger;
use App\Http\Requests\BookBoothRequest;
use App\Http\Requests\CheckBoothsBookingsRequest;
use App\Http\Requests\CreateBoothRequest;
use App\Http\Requests\CreateBoothsInZoneRequest;
use App\Http\Requests\DeleteBoothsInZoneRequest;
use App\Http\Requests\RemoveFloorplanRequest;
use App\Http\Requests\SaveZoneSettingsRequest;
use App\Http\Requests\UpdateBoothPositionRequest;
use App\Http\Requests\UpdateBoothRequest;
use App\Http\Requests\UpdateImageOrderRequest;
use App\Http\Requests\UploadBoothGalleryRequest;
use App\Http\Requests\UploadBoothImageRequest;
use App\Http\Requests\UploadFloorplanRequest;
use App\Models\AffiliateClick;
use App\Models\Asset;
use App\Models\Book;
use App\Models\Booth;
use App\Models\BoothType;
use App\Models\Category;
use App\Models\Client;
use App\Models\FloorPlan;
use App\Models\FloorPlanTickSetting;
use App\Models\Setting;
use App\Models\ZoneSetting;
use App\Services\BookingService;
use App\Services\BoothImageService;
use App\Services\BoothService;
use App\Services\ClientService;
use App\Services\FloorPlanService;
use App\Services\ZoneService;
use Illuminate\Http\Request;

class BoothController extends Controller
{
    public function __construct(
        private BoothService $boothService,
        private BoothImageService $imageService,
        private ZoneService $zoneService,
        private FloorPlanService $floorPlanService,
        private BookingService $bookingService,
        private ClientService $clientService
    ) {}

    /**
     * Display a listing of booths (Floor Plan or Management Table)
     */
    public function index(Request $request)
    {
        // #region agent log
        DebugLogger::log(['request_method' => $request->method(), 'user_authenticated' => auth()->check()], 'BoothController.php:22', 'BoothController::index() called');
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
        if (! $floorPlanId) {
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
            if ($currentFloorPlan->floor_image && ! file_exists(public_path($currentFloorPlan->floor_image))) {
                \Log::warning('Floor plan image file not found, but path exists in database', [
                    'floor_plan_id' => $floorPlanId,
                    'floor_plan_name' => $currentFloorPlan->name,
                    'floor_image_path' => $currentFloorPlan->floor_image,
                    'full_path' => public_path($currentFloorPlan->floor_image),
                ]);
            }

            \Log::info('Loading floor plan for canvas (automatic image load)', [
                'floor_plan_id' => $floorPlanId,
                'floor_plan_name' => $currentFloorPlan->name,
                'floor_image' => $currentFloorPlan->floor_image,
                'image_exists' => $currentFloorPlan->floor_image && file_exists(public_path($currentFloorPlan->floor_image)),
                'canvas_width' => $currentFloorPlan->canvas_width,
                'canvas_height' => $currentFloorPlan->canvas_height,
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

            if (! $floorImageExists) {
                \Log::warning('Floor plan image file not found or not readable', [
                    'floor_plan_id' => $floorPlanId,
                    'floor_image_path' => $floorImage,
                    'full_path' => $fullPath,
                    'file_exists' => file_exists($fullPath),
                    'is_readable' => file_exists($fullPath) ? is_readable($fullPath) : false,
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
        DebugLogger::log(['booth_count' => $booths->count(), 'first_booth_id' => $booths->first()?->id ?? null], 'BoothController.php:31', 'Booths fetched from database');
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
        DebugLogger::log(['totalBooths' => $totalBooths, 'availableBooths' => $availableBooths, 'bookedBooths' => $bookedBooths, 'reservedBoothsCount' => $reservedBoothsCount, 'paidBooths' => $paidBooths, 'totalRevenue' => $totalRevenue, 'paidRevenue' => $paidRevenue], 'BoothController.php:48', 'Booth statistics calculated');
        // #endregion

        // Prepare booth data for JavaScript (to avoid parsing issues in Blade)
        $boothsForJS = $booths->map(function ($booth) {
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
        DebugLogger::log(['boothsForJS_count' => count($boothsForJS), 'first_booth_js' => ($boothsForJS->first() ?? null)], 'BoothController.php:65', 'Booth data prepared for JavaScript');
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
                if (! isset($reserveMap[$company])) {
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
                    if (! isset($companyMap[$company])) {
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
                    if (! isset($categoryMap[$categoryName])) {
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
                    if (! isset($subCategoryMap[$subCategoryName])) {
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
                    if (! isset($assetMap[$assetName])) {
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
                    if (! isset($boothTypeMap[$boothTypeName])) {
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
            \Log::warning('Error loading booth status settings: '.$e->getMessage());
            $statusSettings = collect([]);
            $statusColors = [];
        }

        $tickSettings = FloorPlanTickSetting::getForFloorPlan($floorPlanId ? (int) $floorPlanId : null);
        $showBookedTick = $tickSettings['show_tick'];
        $bookedTickColor = $tickSettings['color'];
        $bookedTickSize = $tickSettings['size'];
        $bookedTickShape = $tickSettings['shape'];
        $bookedTickPosition = $tickSettings['position'];
        $bookedTickAnimation = $tickSettings['animation'];
        $bookedTickBgColor = $tickSettings['bg_color'];
        $bookedTickBorderWidth = $tickSettings['border_width'];
        $bookedTickBorderColor = $tickSettings['border_color'];
        $bookedTickFontSize = $tickSettings['font_size'];
        $bookedTickSizeMode = $tickSettings['size_mode'];
        $bookedTickRelativePercent = $tickSettings['relative_percent'];

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
            'canEditCanvas',
            'showBookedTick',
            'bookedTickColor',
            'bookedTickSize',
            'bookedTickShape',
            'bookedTickPosition',
            'bookedTickAnimation',
            'bookedTickBgColor',
            'bookedTickBorderWidth',
            'bookedTickBorderColor',
            'bookedTickFontSize',
            'bookedTickSizeMode',
            'bookedTickRelativePercent'
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
        if (! $floorPlanId) {
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
    public function store(CreateBoothRequest $request)
    {
        try {
            $validated = $request->validated();
            $image = $request->hasFile('booth_image') ? $request->file('booth_image') : null;

            $booth = $this->boothService->createBooth($validated, $image);

            // Return JSON if requested (for AJAX)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booth created successfully.',
                    'booth' => $booth,
                ]);
            }

            // Preserve floor_plan_id in redirect if specified
            $redirectUrl = route('booths.index', ['view' => 'table']);
            if (! empty($booth->floor_plan_id)) {
                $redirectUrl .= '&floor_plan_id='.$booth->floor_plan_id;
            }

            return redirect($redirectUrl)
                ->with('success', 'Booth created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return JSON if requested (for AJAX)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Booth creation failed: '.$e->getMessage());

            // Return JSON if requested (for AJAX)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create booth: '.$e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create booth. Please try again.']);
        }
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
            \Log::error('Error in BoothController@show: '.$e->getMessage());

            // If JSON was requested, return JSON error
            if ($request->expectsJson() ||
                $request->wantsJson() ||
                $request->ajax() ||
                str_contains($request->header('Accept', ''), 'application/json')) {
                return response()->json([
                    'error' => 'Failed to load booth data',
                    'message' => $e->getMessage(),
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
    public function update(UpdateBoothRequest $request, Booth $booth)
    {
        try {
            $validated = $request->validated();
            $image = $request->hasFile('booth_image') ? $request->file('booth_image') : null;

            $booth = $this->boothService->updateBooth($booth, $validated, $image);

            // Return JSON if requested (for AJAX)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booth updated successfully.',
                    'booth' => $booth,
                ]);
            }

            return redirect()
                ->route('booths.index', ['view' => 'table'])
                ->with('success', 'Booth updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return JSON if requested (for AJAX)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Booth update failed: '.$e->getMessage());

            // Return JSON if requested (for AJAX)
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update booth: '.$e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update booth. Please try again.']);
        }
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
        try {
            $this->boothService->deleteBooth($booth);

            return redirect()->route('booths.index')
                ->with('success', 'Booth deleted successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            \Log::error('Booth deletion failed: '.$e->getMessage());

            return back()
                ->withErrors(['error' => 'Failed to delete booth: '.$e->getMessage()]);
        }
    }

    /**
     * Confirm reservation (confirmR)
     * Changes status from 3 (reserved) to 2 (confirmed)
     */
    public function confirmReservation($id)
    {
        try {
            $booth = Booth::findOrFail($id);
            $user = auth()->user();

            $this->boothService->confirmReservation($booth, auth()->id(), $user->isAdmin());

            return response()->json([
                'status' => 200,
                'message' => 'Successful.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 403,
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Exception $e) {
            \Log::error('Failed to confirm reservation: '.$e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while confirming reservation.',
            ], 500);
        }
    }

    /**
     * Clear reservation (clearR)
     * Only works if booth status is 3 (reserved) and user owns it
     */
    public function clearReservation($id)
    {
        try {
            $booth = Booth::findOrFail($id);
            $this->boothService->clearReservation($booth, auth()->id());

            return response()->json([
                'status' => 200,
                'message' => 'Successful.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 403,
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Exception $e) {
            \Log::error('Failed to clear reservation: '.$e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while clearing reservation.',
            ], 500);
        }
    }

    /**
     * Mark booth as paid (bookPaid)
     * Changes status from 2 (confirmed) to 5 (paid)
     */
    public function markPaid($id)
    {
        try {
            $booth = Booth::findOrFail($id);
            $user = auth()->user();

            $this->boothService->markPaid($booth, auth()->id(), $user->isAdmin());

            return response()->json([
                'status' => 200,
                'message' => 'Successful.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 403,
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Exception $e) {
            \Log::error('Failed to mark booth as paid: '.$e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while marking booth as paid.',
            ], 500);
        }
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
        try {
            $booth = Booth::findOrFail($id);
            $user = auth()->user();

            $this->boothService->removeBoothFromBooking($booth, auth()->id(), $user->isAdmin());

            return response()->json([
                'status' => 200,
                'message' => 'Successful.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 403,
                'message' => $e->getMessage(),
            ], 403);
        } catch (\Exception $e) {
            \Log::error('Failed to remove booth from booking: '.$e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while removing booth from booking.',
            ], 500);
        }
    }

    /**
     * Update external view status (toggle between available and hidden)
     * Toggles status between 1 (available) and 4 (hidden)
     */
    public function updateExternalView(Request $request)
    {
        // Check permission for canvas editing
        if (! auth()->user()->hasPermission('booths.canvas.edit') && ! auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.',
            ], 403);
        }

        $data = $request->input('data');

        if (! isset($data)) {
            return response()->json([
                'status' => 403,
                'message' => 'Please Check Data Before Submit',
            ], 403);
        }

        $data = json_decode($data, true);

        if (! is_array($data)) {
            return response()->json([
                'status' => 403,
                'message' => 'Invalid data format',
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
            'message' => 'Successful.',
        ]);
    }

    /**
     * Save booth position on floorplan
     */
    public function savePosition(UpdateBoothPositionRequest $request, $id)
    {
        try {
            $booth = Booth::findOrFail($id);
            $booth = $this->boothService->updatePosition($booth, $request->validated());

            return response()->json([
                'status' => 200,
                'message' => 'Position saved successfully.',
                'booth' => [
                    'id' => $booth->id,
                    'booth_number' => $booth->booth_number,
                    'position_x' => $booth->position_x,
                    'position_y' => $booth->position_y,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving booth position: '.$e->getMessage(), [
                'booth_id' => $id,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error saving position: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save all booth positions in bulk
     */
    public function saveAllPositions(Request $request)
    {
        // Check permission for canvas editing
        if (! auth()->user()->hasPermission('booths.canvas.edit') && ! auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.',
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
                            'error' => 'Save operation returned false',
                        ];
                    }
                } catch (\Exception $e) {
                    \Log::error('Error saving booth', [
                        'booth_id' => $boothData['id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    $errors[] = [
                        'booth_id' => $boothData['id'],
                        'error' => $e->getMessage(),
                    ];
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Positions saved successfully.',
                'saved' => $saved,
                'total' => count($validated['booths']),
                'errors' => $errors,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving all booth positions: '.$e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error saving positions: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload floorplan image (floor-plan-specific)
     */
    public function uploadFloorplan(UploadFloorplanRequest $request)
    {
        // Check permission for canvas editing
        if (! auth()->user()->hasPermission('booths.canvas.edit') && ! auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.',
            ], 403);
        }

        try {
            $floorPlanId = $request->input('floor_plan_id');
            $image = $request->file('floorplan_image');

            $result = $this->floorPlanService->uploadFloorplan($floorPlanId, $image);

            return response()->json([
                'status' => 200,
                'message' => 'Floorplan uploaded successfully.',
                'image_url' => $result['image_url'],
                'image_path' => $result['image_path'],
                'image_width' => $result['image_width'],
                'image_height' => $result['image_height'],
                'canvas_width' => $result['canvas_width'],
                'canvas_height' => $result['canvas_height'],
                'floor_plan_id' => $result['floor_plan_id'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error uploading floorplan: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error uploading floorplan: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the current floorplan image (floor-plan-specific).
     */
    public function removeFloorplan(RemoveFloorplanRequest $request)
    {
        // Check permission for canvas editing
        if (! auth()->user()->hasPermission('booths.canvas.edit') && ! auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.',
            ], 403);
        }

        try {
            $floorPlanId = $request->input('floor_plan_id');

            $this->floorPlanService->removeFloorplan($floorPlanId);

            return response()->json([
                'status' => 200,
                'message' => 'Floorplan removed successfully.',
                'image_removed' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error removing floorplan: '.$e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'Error removing floorplan: '.$e->getMessage(),
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

        if (! $boothNumber) {
            return response()->json([
                'status' => 400,
                'message' => 'Booth number is required',
                'is_duplicate' => false,
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
            'message' => $message,
        ]);
    }

    /**
     * Get zone settings (floor-plan-specific)
     */
    public function getZoneSettings(Request $request, $zoneName)
    {
        try {
            $floorPlanId = $request->input('floor_plan_id');
            if (! $floorPlanId) {
                $defaultFloorPlan = FloorPlan::where('is_default', true)->first();
                $floorPlanId = $defaultFloorPlan ? $defaultFloorPlan->id : null;
            }

            $result = $this->zoneService->getZoneSettings($zoneName, $floorPlanId);

            return response()->json([
                'status' => 200,
                'zone_name' => $result['zone_name'],
                'floor_plan_id' => $result['floor_plan_id'],
                'settings' => $result['settings'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting zone settings: '.$e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'Error getting zone settings: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new booth in a specific zone
     */
    public function createBoothInZone(CreateBoothsInZoneRequest $request, $zoneName)
    {
        // Check permission for canvas editing
        if (! auth()->user()->hasPermission('booths.canvas.edit') && ! auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.',
            ], 403);
        }

        try {
            $validated = $request->validated();
            $floorPlanId = $validated['floor_plan_id'];
            $floorPlan = FloorPlan::findOrFail($floorPlanId);

            $result = $this->zoneService->createBoothsInZone($zoneName, $floorPlanId, $validated);

            // Check if any booths were created
            if (count($result['created']) === 0) {
                if (count($result['skipped']) > 0) {
                    return response()->json([
                        'status' => 409,
                        'message' => 'Zone '.$zoneName.' already exists in this floor plan. '.count($result['skipped']).' booth(s) already exist: '.implode(', ', array_slice($result['skipped'], 0, 5)).(count($result['skipped']) > 5 ? '...' : ''),
                        'created' => [],
                        'skipped' => $result['skipped'],
                        'errors' => $result['errors'],
                    ], 409);
                } elseif (count($result['errors']) > 0) {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Failed to create booth(s) in Zone '.$zoneName.'. Errors: '.implode('; ', array_column($result['errors'], 'error')),
                        'created' => [],
                        'skipped' => $result['skipped'],
                        'errors' => $result['errors'],
                    ], 500);
                } else {
                    return response()->json([
                        'status' => 500,
                        'message' => 'Failed to create booth(s) in Zone '.$zoneName.'. No booths were created.',
                        'created' => [],
                        'skipped' => $result['skipped'],
                        'errors' => $result['errors'],
                    ], 500);
                }
            }

            // Success - at least one booth was created
            $message = count($result['created']).' booth(s) created successfully in Zone '.$zoneName.' (Floor Plan: '.$floorPlan->name.')';
            if (count($result['skipped']) > 0) {
                $message .= '. '.count($result['skipped']).' booth(s) skipped (already exist).';
            }
            if (count($result['errors']) > 0) {
                $message .= '. '.count($result['errors']).' error(s) occurred: '.implode('; ', array_column($result['errors'], 'error'));
            }

            return response()->json([
                'status' => 200,
                'message' => $message,
                'created' => $result['created'],
                'skipped' => $result['skipped'],
                'errors' => $result['errors'],
                'floor_plan_id' => $floorPlanId,
                'floor_plan_name' => $floorPlan->name,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating booth in zone: '.$e->getMessage(), [
                'zone' => $zoneName,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error creating booth: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete booths from a specific zone
     */
    public function deleteBoothsInZone(DeleteBoothsInZoneRequest $request, $zoneName)
    {
        // Check permission for canvas editing
        if (! auth()->user()->hasPermission('booths.canvas.edit') && ! auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.',
            ], 403);
        }

        try {
            $validated = $request->validated();
            $mode = $validated['mode'];
            $floorPlanId = $validated['floor_plan_id'];
            $forceDelete = $validated['force_delete_booked'] ?? false;

            $result = $this->zoneService->deleteBoothsInZone($zoneName, $floorPlanId, $mode, $validated);

            $floorPlan = FloorPlan::find($floorPlanId);
            $floorPlanName = $floorPlan ? $floorPlan->name : 'Floor Plan #'.$floorPlanId;

            $message = count($result['deleted']).' booth(s) deleted successfully from Zone '.$zoneName.' in '.$floorPlanName;

            if (count($result['booked_booths_skipped']) > 0) {
                $message .= '. WARNING: '.count($result['booked_booths_skipped']).' booth(s) with active bookings were SKIPPED to prevent data loss.';
            }

            if (count($result['errors']) > 0) {
                $message .= '. '.count($result['errors']).' booth(s) failed to delete.';
            }

            return response()->json([
                'status' => count($result['booked_booths_skipped']) > 0 ? 206 : 200,
                'message' => $message,
                'deleted' => $result['deleted'],
                'booked_booths_skipped' => $result['booked_booths_skipped'],
                'errors' => $result['errors'],
                'floor_plan_id' => $floorPlanId,
                'floor_plan_name' => $floorPlanName,
                'warning' => count($result['booked_booths_skipped']) > 0 ? 'Some booths with active bookings were not deleted to protect booking data.' : null,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error deleting booths in zone: '.$e->getMessage(), [
                'zone' => $zoneName,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error deleting booths: '.$e->getMessage(),
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
        if (! auth()->user()->hasPermission('booths.canvas.edit') && ! auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas design.',
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
                    $floorPlanName = $booth->floorPlan ? $booth->floorPlan->name : ('Floor Plan #'.$floorPlanId);

                    if ($booth->bookid && ! $forceDelete) {
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

            $message = count($deletedBooths).' booth(s) deleted successfully from any zone/floor plan.';
            if (count($bookedBooths) > 0) {
                $message .= ' WARNING: '.count($bookedBooths).' booth(s) with active bookings were SKIPPED.';
            }
            if (count($errors) > 0) {
                $message .= ' '.count($errors).' booth(s) failed to delete.';
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
            \Log::error('Error deleting booths by IDs: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error deleting booths: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check which booths have active bookings (before allowing delete from canvas).
     * Returns booths_with_bookings so the UI can require "resolve booking first" before delete.
     */
    public function checkBoothsBookings(CheckBoothsBookingsRequest $request)
    {
        if (! auth()->user()->hasPermission('booths.canvas.edit') && ! auth()->user()->isAdmin()) {
            return response()->json([
                'status' => 403,
                'message' => 'You do not have permission to edit canvas.',
            ], 403);
        }

        try {
            $validated = $request->validated();
            $boothIds = array_unique(array_map('intval', $validated['booth_ids']));

            $result = $this->bookingService->checkBoothsBookings($boothIds);

            return response()->json([
                'status' => 200,
                'all_clear' => $result['all_clear'],
                'booths_with_bookings' => $result['booths_with_bookings'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error checking booth bookings: '.$e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'Error checking bookings.',
            ], 500);
        }
    }

    /**
     * Save zone settings (floor-plan-specific)
     */
    public function saveZoneSettings(SaveZoneSettingsRequest $request, $zoneName)
    {
        try {
            $validated = $request->validated();
            $floorPlanId = $request->input('floor_plan_id');
            if (! $floorPlanId) {
                $defaultFloorPlan = FloorPlan::where('is_default', true)->first();
                $floorPlanId = $defaultFloorPlan ? $defaultFloorPlan->id : null;
            }

            if (! $floorPlanId) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Floor plan ID is required',
                ], 400);
            }

            // Merge with existing settings to preserve values not being updated
            $existingSettings = ZoneSetting::getZoneDefaults($zoneName, $floorPlanId);
            $settingsToSave = array_merge($existingSettings, $validated);

            $this->zoneService->saveZoneSettings($zoneName, $settingsToSave, $floorPlanId);

            return response()->json([
                'status' => 200,
                'message' => 'Zone settings saved successfully.',
                'zone_name' => $zoneName,
                'floor_plan_id' => $floorPlanId,
                'settings' => $settingsToSave,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error saving zone settings: '.$e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error saving zone settings: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Book a booth with client information
     */
    public function bookBooth(BookBoothRequest $request)
    {
        try {
            $validated = $request->validated();
            $result = $this->bookingService->bookSingleBooth($validated, $this->clientService, $request);

            return response()->json([
                'status' => 200,
                'message' => 'Booth booked successfully!',
                'booth_id' => $result['booth_id'],
                'booth_number' => $result['booth_number'],
                'book_id' => $result['book_id'],
                'client_id' => $result['client_id'],
                'client_name' => $result['client_name'],
                'client_company' => $result['client_company'],
                'status' => $result['status'],
                'booking_type' => $result['booking_type'],
                'floor_plan_id' => $result['floor_plan_id'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error booking booth: '.$e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error booking booth: '.$e->getMessage(),
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
        $cookieName = 'affiliate_fp_'.$id;
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
                        if (! $hasValidCookie) {
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
                \Log::warning('Invalid affiliate link reference: '.$e->getMessage(), [
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
        $boothsForJS = $booths->map(function ($booth) {
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
                'client_name' => $booth->client ? ($booth->client->name ?? '') : '',
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
            \Log::warning('Error loading booth status settings: '.$e->getMessage());
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

        $tickSettings = FloorPlanTickSetting::getForFloorPlan((int) $id);
        $showBookedTick = $tickSettings['show_tick'];
        $bookedTickColor = $tickSettings['color'];
        $bookedTickSize = $tickSettings['size'];
        $bookedTickShape = $tickSettings['shape'];
        $bookedTickPosition = $tickSettings['position'];
        $bookedTickAnimation = $tickSettings['animation'];
        $bookedTickBgColor = $tickSettings['bg_color'];
        $bookedTickBorderWidth = $tickSettings['border_width'];
        $bookedTickBorderColor = $tickSettings['border_color'];
        $bookedTickFontSize = $tickSettings['font_size'];
        $bookedTickSizeMode = $tickSettings['size_mode'];
        $bookedTickRelativePercent = $tickSettings['relative_percent'];

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
            'canSwitchToCanvasDesign',
            'showBookedTick',
            'bookedTickColor',
            'bookedTickSize',
            'bookedTickShape',
            'bookedTickPosition',
            'bookedTickAnimation',
            'bookedTickBgColor',
            'bookedTickBorderWidth',
            'bookedTickBorderColor',
            'bookedTickFontSize',
            'bookedTickSizeMode',
            'bookedTickRelativePercent'
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
            $query->where(function ($q) use ($search) {
                $q->where('booth_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('company', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($catQuery) use ($search) {
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
        if (! in_array((int) $perPage, $allowedPerPage)) {
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
            \Log::warning('Error loading booth status settings: '.$e->getMessage());
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
            $query->where(function ($q) use ($search) {
                $q->where('booth_number', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('company', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($catQuery) use ($search) {
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
        if (! in_array((int) $perPage, $allowedPerPage)) {
            $perPage = 50; // Default fallback
        }
        $offset = ($page - 1) * $perPage;

        // Get total before pagination
        $total = $query->count();

        // Get booths for current page
        $booths = $query->offset($offset)->limit($perPage)->get();
        $hasMore = ($offset + $booths->count()) < $total;

        // Render table rows with row numbers
        $html = '';
        $rowNumber = $offset + 1; // Start row number from offset + 1
        foreach ($booths as $booth) {
            $html .= view('booths.partials.table-row', [
                'booth' => $booth,
                'rowNumber' => $rowNumber++,
            ])->render();
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'hasMore' => $hasMore,
            'currentPage' => $page,
            'total' => $total,
            'perPage' => $perPage,
            'loaded' => $offset + $booths->count(),
        ]);
    }

    /**
     * Upload booth image (main booth image field)
     */
    public function uploadBoothImage(UploadBoothImageRequest $request, $id)
    {
        try {
            $booth = Booth::findOrFail($id);

            if ($request->hasFile('booth_image')) {
                $image = $request->file('booth_image');
                $booth = $this->boothService->updateBooth($booth, [], $image);

                return response()->json([
                    'success' => true,
                    'message' => 'Booth image uploaded successfully.',
                    'image_url' => asset($booth->booth_image),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided.',
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Error uploading booth image: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload multiple images for a booth (gallery)
     */
    public function uploadBoothGalleryImages(UploadBoothGalleryRequest $request, $id)
    {
        try {
            $booth = Booth::findOrFail($id);
            $floorPlanId = $request->input('floor_plan_id', $booth->floor_plan_id);
            $imageType = $request->input('image_type', 'photo');
            $captions = $request->input('captions', []);

            if (! $request->hasFile('gallery_images')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No images provided.',
                ], 400);
            }

            $uploadedImages = $this->imageService->uploadGalleryImages(
                $booth,
                $request->file('gallery_images'),
                $floorPlanId,
                $imageType,
                $captions
            );

            return response()->json([
                'success' => true,
                'message' => count($uploadedImages).' image(s) uploaded successfully.',
                'images' => $uploadedImages,
                'booth_id' => $booth->id,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error uploading booth gallery images: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error uploading images: '.$e->getMessage(),
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
            $images = $this->imageService->getBoothImages($id);

            return response()->json([
                'success' => true,
                'images' => $images,
                'count' => count($images),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching images: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a booth image
     */
    public function deleteBoothImage($boothId, $imageId)
    {
        try {
            $this->imageService->deleteBoothImage($boothId, $imageId);

            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting image: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set primary image for a booth
     */
    public function setPrimaryImage($boothId, $imageId)
    {
        try {
            $this->imageService->setPrimaryImage($boothId, $imageId);

            return response()->json([
                'success' => true,
                'message' => 'Primary image updated successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting primary image: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update image sort order
     */
    public function updateImageOrder(UpdateImageOrderRequest $request, $boothId)
    {
        try {
            $this->imageService->updateImageOrder($boothId, $request->input('image_ids'));

            return response()->json([
                'success' => true,
                'message' => 'Image order updated successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: '.$e->getMessage(),
            ], 500);
        }
    }
}
