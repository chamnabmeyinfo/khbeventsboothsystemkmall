<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\Client;
use App\Models\Category;
use App\Models\Asset;
use App\Models\BoothType;
use App\Models\Book;
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
        
        // Get all booths ordered by booth number, including positions
        $booths = Booth::with(['client', 'category', 'subCategory', 'asset', 'boothType', 'user'])
            ->orderBy('booth_number', 'asc')
            ->get();

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
            'boothsForJS'
        ));
    }

    /**
     * Show the form for creating a new booth
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $assets = Asset::where('status', 1)->get();
        $boothTypes = BoothType::where('status', 1)->get();
        
        return view('booths.create', compact('categories', 'assets', 'boothTypes'));
    }

    /**
     * Store a newly created booth
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booth_number' => 'required|string|max:45|unique:booth,booth_number',
            'type' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:category,id',
            'asset_id' => 'nullable|exists:asset,id',
            'booth_type_id' => 'nullable|exists:booth_type,id',
        ]);

        Booth::create($validated);

        return redirect()->route('booths.index')
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
    public function edit(Booth $booth)
    {
        $categories = Category::where('status', 1)->get();
        $assets = Asset::where('status', 1)->get();
        $boothTypes = BoothType::where('status', 1)->get();
        $clients = Client::orderBy('company')->get();

        return view('booths.edit', compact('booth', 'categories', 'assets', 'boothTypes', 'clients'));
    }

    /**
     * Update the specified booth
     */
    public function update(Request $request, Booth $booth)
    {
        $validated = $request->validate([
            'booth_number' => 'required|string|max:45|unique:booth,booth_number,' . $booth->id,
            'type' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'status' => 'required|integer',
            'client_id' => 'nullable|exists:client,id',
            'category_id' => 'nullable|exists:category,id',
            'asset_id' => 'nullable|exists:asset,id',
            'booth_type_id' => 'nullable|exists:booth_type,id',
        ]);

        $booth->update($validated);

        return redirect()->route('booths.index')
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
        try {
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
            ]);

            $saved = 0;
            $errors = [];

            foreach ($validated['booths'] as $boothData) {
                try {
                    $booth = Booth::findOrFail($boothData['id']);
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
                    $booth->save();
                    $saved++;
                } catch (\Exception $e) {
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
     * Upload floorplan image
     */
    public function uploadFloorplan(Request $request)
    {
        try {
            // Get upload size limit from environment or use default (100MB = 102400 KB)
            // You can set UPLOAD_MAX_SIZE_KB in .env file to customize (value in KB)
            $maxSizeKB = env('UPLOAD_MAX_SIZE_KB', 102400); // Default 100MB in KB
            
            $request->validate([
                'floorplan_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:' . $maxSizeKB,
            ]);

            $image = $request->file('floorplan_image');
            $imageName = 'map.jpg'; // Always save as map.jpg
            
            // Ensure images directory exists
            $imagesPath = public_path('images');
            if (!file_exists($imagesPath)) {
                mkdir($imagesPath, 0755, true);
            }
            
            // Delete old floorplan if exists
            $oldImagePath = $imagesPath . '/' . $imageName;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
            
            // Move uploaded file
            $image->move($imagesPath, $imageName);
            
            // Get image dimensions
            $imagePath = $imagesPath . '/' . $imageName;
            $imageInfo = getimagesize($imagePath);
            $imageWidth = $imageInfo[0] ?? null;
            $imageHeight = $imageInfo[1] ?? null;
            
            // Get the URL for the image
            $imageUrl = asset('images/' . $imageName);
            
            return response()->json([
                'status' => 200,
                'message' => 'Floorplan uploaded successfully.',
                'image_url' => $imageUrl,
                'image_path' => 'images/' . $imageName,
                'image_width' => $imageWidth,
                'image_height' => $imageHeight
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
     * Remove the current floorplan image.
     */
    public function removeFloorplan(Request $request)
    {
        try {
            $imagesPath = public_path('images');
            $imageName = 'map.jpg';
            $imagePath = $imagesPath . '/' . $imageName;

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Floorplan removed successfully.',
                'image_removed' => true,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error removing floorplan: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Error removing floorplan: ' . $e->getMessage()
            ], 500);
        }
    }
}
