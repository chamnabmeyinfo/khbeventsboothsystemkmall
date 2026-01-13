<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use App\Models\Setting;
use App\Models\CanvasSetting;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        return view('settings.index');
    }

    /**
     * Clear application cache
     */
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            $message = 'Application cache cleared successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error clearing cache: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Clear config cache
     */
    public function clearConfig(Request $request)
    {
        try {
            Artisan::call('config:clear');
            $message = 'Configuration cache cleared successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error clearing config cache: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Clear route cache
     */
    public function clearRoute(Request $request)
    {
        try {
            Artisan::call('route:clear');
            $message = 'Route cache cleared successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error clearing route cache: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Clear view cache
     */
    public function clearView(Request $request)
    {
        try {
            Artisan::call('view:clear');
            $message = 'View cache cleared successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error clearing view cache: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Clear all caches
     */
    public function clearAll(Request $request)
    {
        $results = [];
        
        try {
            Artisan::call('cache:clear');
            $results[] = 'Application cache cleared.';
        } catch (\Exception $e) {
            $results[] = 'Error clearing application cache: ' . $e->getMessage();
        }

        try {
            Artisan::call('config:clear');
            $results[] = 'Configuration cache cleared.';
        } catch (\Exception $e) {
            $results[] = 'Error clearing config cache: ' . $e->getMessage();
        }

        try {
            Artisan::call('route:clear');
            $results[] = 'Route cache cleared.';
        } catch (\Exception $e) {
            $results[] = 'Error clearing route cache: ' . $e->getMessage();
        }

        try {
            Artisan::call('view:clear');
            $results[] = 'View cache cleared.';
        } catch (\Exception $e) {
            $results[] = 'Error clearing view cache: ' . $e->getMessage();
        }

        $message = implode(' ', $results);
        $hasErrors = strpos($message, 'Error') !== false;
        $type = $hasErrors ? 'error' : 'success';

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $hasErrors ? 500 : 200,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Optimize application
     */
    public function optimize(Request $request)
    {
        try {
            Artisan::call('optimize:clear');
            $message = 'Application optimized successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error optimizing application: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Get booth default settings
     */
    public function getBoothDefaults(Request $request)
    {
        try {
            $defaults = Setting::getBoothDefaults();

            return response()->json([
                'status' => 200,
                'data' => $defaults
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error fetching booth defaults: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save booth default settings
     */
    public function saveBoothDefaults(Request $request)
    {
        try {
            $validated = $request->validate([
                'width' => 'nullable|integer|min:5',
                'height' => 'nullable|integer|min:5',
                'rotation' => 'nullable|integer',
                'z_index' => 'nullable|integer|min:1|max:1000',
                'font_size' => 'nullable|integer|min:8|max:48',
                'border_width' => 'nullable|integer|min:0|max:10',
                'border_radius' => 'nullable|integer|min:0|max:50',
                'opacity' => 'nullable|numeric|min:0|max:1',
                // Appearance settings
                'background_color' => 'nullable|string|max:50',
                'border_color' => 'nullable|string|max:50',
                'text_color' => 'nullable|string|max:50',
                'font_weight' => 'nullable|string|max:20',
                'font_family' => 'nullable|string|max:255',
                'text_align' => 'nullable|string|max:20',
                'box_shadow' => 'nullable|string|max:255',
            ]);

            $defaults = Setting::saveBoothDefaults($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Booth default settings saved successfully.',
                'data' => $defaults
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error saving booth defaults: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get canvas settings (floor-plan-specific)
     */
    public function getCanvasSettings(Request $request)
    {
        try {
            // Check if table exists first
            if (!\Schema::hasTable('canvas_settings')) {
                // Return defaults if table doesn't exist
                return response()->json([
                    'status' => 200,
                    'data' => [
                        'floor_plan_id' => null,
                        'canvas_width' => 1200,
                        'canvas_height' => 800,
                        'canvas_resolution' => 300,
                        'grid_size' => 10,
                        'zoom_level' => 1.00,
                        'pan_x' => 0,
                        'pan_y' => 0,
                        'floorplan_image' => null,
                        'grid_enabled' => true,
                        'snap_to_grid' => false,
                    ]
                ]);
            }
            
            // Get floor_plan_id from request (query parameter or JSON body)
            $floorPlanId = $request->input('floor_plan_id');
            if ($floorPlanId) {
                $floorPlanId = (int) $floorPlanId;
            } else {
                $floorPlanId = null;
            }
            
            // Get floor-plan-specific settings
            $settings = CanvasSetting::getForFloorPlan($floorPlanId);

            // CRITICAL: ALWAYS use floor plan's image from floor_plans table (source of truth)
            // Never rely on canvas_settings.floorplan_image - it's just a cache
            // This ensures each floor plan always loads its own image correctly
            $floorplanImage = null;
            if ($floorPlanId) {
                $floorPlan = \App\Models\FloorPlan::find($floorPlanId);
                
                
                if ($floorPlan && $floorPlan->floor_image) {
                    $floorplanImage = $floorPlan->floor_image; // Relative path: 'images/floor-plans/...'
                    
                    // Clean up invalid image paths (empty strings, etc.)
                    if (trim($floorplanImage) === '' || $floorplanImage === 'null') {
                        $floorplanImage = null;
                    }
                }
            }
            
            // Always sync canvas_settings.floorplan_image with floor_plans.floor_image
            // This ensures consistency and prevents conflicts when switching floor plans
            if ($floorPlanId) {
                try {
                    // Update canvas_settings to match floor_plans.floor_image (always sync)
                    $settings->floorplan_image = $floorplanImage; // Can be null if no image
                    $settings->save();
                } catch (\Exception $e) {
                    \Log::warning('Could not sync canvas_settings.floorplan_image: ' . $e->getMessage());
                }
            }
            
            // Clean up any invalid data in canvas_settings (Blade templates, full URLs, etc.)
            if ($settings->floorplan_image) {
                $canvasImagePath = $settings->floorplan_image;
                $isInvalid = (
                    strpos($canvasImagePath, '{{') !== false || // Blade template
                    strpos($canvasImagePath, 'asset') !== false || // Blade asset() function  
                    strpos($canvasImagePath, 'http://') !== false || // Full URL
                    strpos($canvasImagePath, 'https://') !== false || // Full URL
                    trim($canvasImagePath) === '' || // Empty string
                    $canvasImagePath === 'null' // String "null"
                );
                
                if ($isInvalid) {
                    try {
                        // Use floor plan's image instead (or null if none)
                        $settings->floorplan_image = $floorplanImage;
                        $settings->save();
                    } catch (\Exception $e) {
                        \Log::warning('Could not clean invalid canvas_settings.floorplan_image: ' . $e->getMessage());
                    }
                }
            }

            $responseData = [
                'floor_plan_id' => $settings->floor_plan_id,
                'canvas_width' => $settings->canvas_width ?? 1200,
                'canvas_height' => $settings->canvas_height ?? 800,
                'canvas_resolution' => $settings->canvas_resolution ?? 300,
                'grid_size' => $settings->grid_size ?? 10,
                'zoom_level' => $settings->zoom_level ?? 1.00,
                'pan_x' => $settings->pan_x ?? 0,
                'pan_y' => $settings->pan_y ?? 0,
                'floorplan_image' => $floorplanImage,
                'grid_enabled' => $settings->grid_enabled ?? true,
                'snap_to_grid' => $settings->snap_to_grid ?? false,
            ];
            
            
            return response()->json([
                'status' => 200,
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching canvas settings: ' . $e->getMessage());
            // Return defaults on error
            return response()->json([
                'status' => 200,
                'data' => [
                    'floor_plan_id' => null,
                    'canvas_width' => 1200,
                    'canvas_height' => 800,
                    'canvas_resolution' => 300,
                    'grid_size' => 10,
                    'zoom_level' => 1.00,
                    'pan_x' => 0,
                    'pan_y' => 0,
                    'floorplan_image' => null,
                    'grid_enabled' => true,
                    'snap_to_grid' => false,
                ]
            ]);
        }
    }

    /**
     * Save canvas settings (floor-plan-specific)
     */
    public function saveCanvasSettings(Request $request)
    {
        try {
            // Check if table exists first
            if (!Schema::hasTable('canvas_settings')) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Canvas settings table not found. Please run migrations.',
                    'data' => $request->all()
                ]);
            }
            
            // Get floor_plan_id from request (required for floor-plan-specific settings)
            $floorPlanId = $request->input('floor_plan_id');
            if ($floorPlanId) {
                $floorPlanId = (int) $floorPlanId;
                // Validate floor plan exists
                if (!\App\Models\FloorPlan::find($floorPlanId)) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'Floor plan not found.',
                        'errors' => ['floor_plan_id' => ['The selected floor plan is invalid.']]
                    ], 422);
                }
            } else {
                $floorPlanId = null; // Allow null for backward compatibility
            }
            
            // Prepare data with type conversion
            $data = [];
            $input = $request->all();
            
            // Only include fields that are present and valid
            if ($floorPlanId !== null) {
                $data['floor_plan_id'] = $floorPlanId;
            }
            if (isset($input['canvas_width']) && is_numeric($input['canvas_width'])) {
                $data['canvas_width'] = max(5, (int) $input['canvas_width']);
            }
            if (isset($input['canvas_height']) && is_numeric($input['canvas_height'])) {
                $data['canvas_height'] = max(5, (int) $input['canvas_height']);
            }
            if (isset($input['canvas_resolution']) && is_numeric($input['canvas_resolution'])) {
                $data['canvas_resolution'] = max(72, min(600, (int) $input['canvas_resolution']));
            }
            if (isset($input['grid_size']) && is_numeric($input['grid_size'])) {
                $data['grid_size'] = max(5, min(100, (int) $input['grid_size']));
            }
            if (isset($input['zoom_level']) && is_numeric($input['zoom_level'])) {
                $data['zoom_level'] = max(0.1, min(10, (float) $input['zoom_level']));
            }
            if (isset($input['pan_x']) && is_numeric($input['pan_x'])) {
                $data['pan_x'] = (float) $input['pan_x'];
            }
            if (isset($input['pan_y']) && is_numeric($input['pan_y'])) {
                $data['pan_y'] = (float) $input['pan_y'];
            }
            // Don't save floorplan_image from JavaScript - it's managed in floor_plans.floor_image
            // The upload endpoint (BoothController::uploadFloorplan) handles updating canvas_settings.floorplan_image
            // This prevents conflicts when switching between floor plans
            // if (isset($input['floorplan_image']) && is_string($input['floorplan_image'])) {
            //     $data['floorplan_image'] = substr($input['floorplan_image'], 0, 255);
            // }
            if (isset($input['grid_enabled'])) {
                $data['grid_enabled'] = filter_var($input['grid_enabled'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
            }
            if (isset($input['snap_to_grid'])) {
                $data['snap_to_grid'] = filter_var($input['snap_to_grid'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
            }
            
            // If no data to save, return success
            if (empty($data)) {
                return response()->json([
                    'status' => 200,
                    'message' => 'No settings to save.',
                    'data' => []
                ]);
            }

            // Update settings for specific floor plan
            $settings = CanvasSetting::updateSettings($data, $floorPlanId);

            return response()->json([
                'status' => 200,
                'message' => 'Canvas settings saved successfully.',
                'data' => [
                    'floor_plan_id' => $settings->floor_plan_id,
                    'canvas_width' => $settings->canvas_width,
                    'canvas_height' => $settings->canvas_height,
                    'canvas_resolution' => $settings->canvas_resolution,
                    'grid_size' => $settings->grid_size,
                    'zoom_level' => $settings->zoom_level,
                    'pan_x' => $settings->pan_x,
                    'pan_y' => $settings->pan_y,
                    'floorplan_image' => $settings->floorplan_image,
                    'grid_enabled' => $settings->grid_enabled,
                    'snap_to_grid' => $settings->snap_to_grid,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error saving canvas settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company information settings
     */
    public function getCompanySettings(Request $request)
    {
        try {
            $settings = Setting::getCompanySettings();

            return response()->json([
                'status' => 200,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error fetching company settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save company information settings
     */
    public function saveCompanySettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_name' => 'nullable|string|max:255',
                'company_logo' => 'nullable|string|max:500',
                'company_favicon' => 'nullable|string|max:500',
                'company_email' => 'nullable|email|max:255',
                'company_phone' => 'nullable|string|max:50',
                'company_address' => 'nullable|string|max:500',
                'company_website' => 'nullable|url|max:255',
            ]);

            $settings = Setting::saveCompanySettings($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Company settings saved successfully.',
                'data' => $settings
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error saving company settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system appearance/color settings
     */
    public function getAppearanceSettings(Request $request)
    {
        try {
            $settings = Setting::getAppearanceSettings();

            return response()->json([
                'status' => 200,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error fetching appearance settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save system appearance/color settings
     */
    public function saveAppearanceSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'success_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'info_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'warning_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'danger_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'sidebar_bg' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'navbar_bg' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'footer_bg' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            $settings = Setting::saveAppearanceSettings($validated);

            return response()->json([
                'status' => 200,
                'message' => 'Appearance settings saved successfully.',
                'data' => $settings
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Error saving appearance settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload company logo
     */
    public function uploadLogo(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $file = $request->file('logo');
            
            // Create directory if it doesn't exist
            $directory = public_path('images/company');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Generate unique filename
            $filename = 'company_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            // Move uploaded file
            $file->move($directory, $filename);

            // Save the path relative to public directory
            $relativePath = 'images/company/' . $filename;
            
            // Delete old logo if exists
            $oldLogo = Setting::getValue('company_logo', '');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }
            
            Setting::setValue('company_logo', $relativePath, 'string', 'Company logo file path');

            return response()->json([
                'status' => 200,
                'message' => 'Logo uploaded successfully.',
                'path' => $relativePath,
                'url' => asset($relativePath)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error uploading logo: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Error uploading logo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload company favicon
     */
    public function uploadFavicon(Request $request)
    {
        try {
            $request->validate([
                'favicon' => 'required|image|mimes:ico,png,jpg|max:512',
            ]);

            $file = $request->file('favicon');
            
            // Create directory if it doesn't exist
            $directory = public_path('images/company');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Generate unique filename
            $filename = 'company_favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            // Move uploaded file
            $file->move($directory, $filename);

            // Save the path relative to public directory
            $relativePath = 'images/company/' . $filename;
            
            // Delete old favicon if exists
            $oldFavicon = Setting::getValue('company_favicon', '');
            if ($oldFavicon && File::exists(public_path($oldFavicon))) {
                File::delete(public_path($oldFavicon));
            }
            
            Setting::setValue('company_favicon', $relativePath, 'string', 'Company favicon file path');

            return response()->json([
                'status' => 200,
                'message' => 'Favicon uploaded successfully.',
                'path' => $relativePath,
                'url' => asset($relativePath)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error uploading favicon: ' . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Error uploading favicon: ' . $e->getMessage()
            ], 500);
        }
    }
}

