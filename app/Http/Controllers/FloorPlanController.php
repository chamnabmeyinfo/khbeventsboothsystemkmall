<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\CanvasSetting;
use App\Models\Event;
use App\Models\FloorPlan;
use App\Models\ZoneSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('project_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $floorPlans = $query->latest('created_at')->paginate(20)->withQueryString();

            // Load additional data for each floor plan (canvas settings, zone count)
            $floorPlans->getCollection()->transform(function ($floorPlan) {
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
                if (! empty($tableCheck)) {
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
            if (! empty($tableCheck)) {
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
            'floor_image' => \App\Helpers\UploadSettingsHelper::getRules(\App\Helpers\UploadSettingsHelper::CONTEXT_FLOOR_PLAN, 'floor_image', false)['floor_image'],
            'feature_image' => \App\Helpers\UploadSettingsHelper::getRules(\App\Helpers\UploadSettingsHelper::CONTEXT_FLOOR_PLAN, 'feature_image', false)['feature_image'],
            'google_map_location' => 'nullable|string',
            'proposal' => 'nullable|string',
            'event_start_date' => 'nullable|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'event_start_time' => 'nullable|date_format:H:i',
            'event_end_time' => 'nullable|date_format:H:i',
            'event_location' => 'nullable|string|max:255',
            'event_venue' => 'nullable|string|max:255',
            'canvas_width' => 'nullable|integer|min:100|max:5000',
            'canvas_height' => 'nullable|integer|min:100|max:5000',
            'is_active' => 'nullable|boolean',
        ];

        // Only validate event_id if events table exists (safe check - avoid exists rule if table doesn't exist)
        try {
            // Try to check if table exists - if query fails, table doesn't exist
            $tables = DB::select("SHOW TABLES LIKE 'events'");
            if (! empty($tables)) {
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

        // Normalize optional text/date/time fields to null (avoid SQL errors on empty string for DATE/TIME columns)
        foreach ([
            'event_id',
            'description',
            'project_name',
            'google_map_location',
            'proposal',
            'event_start_date',
            'event_end_date',
            'event_start_time',
            'event_end_time',
            'event_location',
            'event_venue',
        ] as $nullableField) {
            if (array_key_exists($nullableField, $validated) && $validated[$nullableField] === '') {
                $validated[$nullableField] = null;
            }
        }

        // Handle floor image upload (store with unique name including floor_plan_id will be added after creation)
        if ($request->hasFile('floor_image')) {
            $image = $request->file('floor_image');
            $imageExtension = $image->getClientOriginalExtension();
            $imageName = time().'_floor_plan_temp.'.$imageExtension; // Temporary name, will update after creation
            $imagePath = public_path('images/floor-plans');
            if (! file_exists($imagePath)) {
                if (! @mkdir($imagePath, 0755, true)) {
                    return redirect()->route('floor-plans.create')
                        ->withInput()
                        ->with('error', 'Could not create upload directory. Check folder permissions.');
                }
            }
            if (! is_writable($imagePath)) {
                return redirect()->route('floor-plans.create')
                    ->withInput()
                    ->with('error', 'Upload directory is not writable. Check folder permissions.');
            }
            $image->move($imagePath, $imageName);
            $validated['floor_image'] = 'images/floor-plans/'.$imageName;

            // Auto-update canvas dimensions from image if uploaded
            $fullImagePath = $imagePath.'/'.$imageName;
            if (file_exists($fullImagePath)) {
                $imageInfo = getimagesize($fullImagePath);
                if ($imageInfo) {
                    $validated['canvas_width'] = $imageInfo[0];
                    $validated['canvas_height'] = $imageInfo[1];
                }
            }
        }

        // Handle feature image upload
        if ($request->hasFile('feature_image')) {
            $image = $request->file('feature_image');
            $imageExtension = $image->getClientOriginalExtension();
            $imageName = time().'_feature_temp.'.$imageExtension; // Temporary name, will update after creation
            $imagePath = public_path('images/floor-plans/features');
            if (! file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            $image->move($imagePath, $imageName);
            $validated['feature_image'] = 'images/floor-plans/features/'.$imageName;
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

        // Update floor image name to include floor_plan_id for uniqueness
        if ($floorPlan->floor_image && strpos($floorPlan->floor_image, '_floor_plan_temp') !== false) {
            $oldPath = public_path($floorPlan->floor_image);
            $imageExtension = pathinfo($floorPlan->floor_image, PATHINFO_EXTENSION);
            $newImageName = time().'_floor_plan_'.$floorPlan->id.'.'.$imageExtension;
            $newPath = public_path('images/floor-plans/'.$newImageName);

            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
                $floorPlan->floor_image = 'images/floor-plans/'.$newImageName;
                $floorPlan->save();
            }
        }

        // Update feature image name to include floor_plan_id for uniqueness
        if ($floorPlan->feature_image && strpos($floorPlan->feature_image, '_feature_temp') !== false) {
            $oldPath = public_path($floorPlan->feature_image);
            $imageExtension = pathinfo($floorPlan->feature_image, PATHINFO_EXTENSION);
            $newImageName = time().'_feature_'.$floorPlan->id.'.'.$imageExtension;
            $newPath = public_path('images/floor-plans/features/'.$newImageName);

            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
                $floorPlan->feature_image = 'images/floor-plans/features/'.$newImageName;
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
                    'grid_size' => 1,
                    'zoom_level' => 1.00,
                    'pan_x' => 0,
                    'pan_y' => 0,
                    'floorplan_image' => $floorPlan->floor_image,
                    'grid_enabled' => true,
                    'snap_to_grid' => false,
                ]
            );
        } catch (\Exception $e) {
            \Log::warning('Could not create canvas settings for floor plan: '.$e->getMessage());
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
            if (! empty($tableCheck)) {
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
        // Detect post_max_size exceeded: PHP empties $_POST/$_FILES silently
        if ($request->isMethod('PUT') || $request->isMethod('POST')) {
            $contentLength = $request->server('CONTENT_LENGTH', 0);
            $postMaxBytes = $this->parsePhpSize(ini_get('post_max_size') ?: '8M');
            if ($contentLength > 0 && $postMaxBytes > 0 && $contentLength > $postMaxBytes) {
                $postMaxMb = round($postMaxBytes / 1024 / 1024, 1);
                $sentMb = round($contentLength / 1024 / 1024, 1);
                \Log::error("Floor plan update: post_max_size exceeded ({$sentMb}MB sent, limit {$postMaxMb}MB)", [
                    'floor_plan_id' => $floorPlan->id,
                ]);

                return redirect()->route('floor-plans.edit', $floorPlan)
                    ->with('error', "Upload failed: the request size ({$sentMb} MB) exceeds the server limit ({$postMaxMb} MB). Please upload a smaller image or contact the administrator to increase post_max_size.");
            }
        }

        try {
            return $this->performUpdate($request, $floorPlan);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('Floor plan update failed with unhandled error', [
                'floor_plan_id' => $floorPlan->id,
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'file' => $e->getFile().':'.$e->getLine(),
                'trace' => $e->getTraceAsString(),
                'php_version' => PHP_VERSION,
                'post_max_size' => ini_get('post_max_size'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'has_floor_image' => $request->hasFile('floor_image'),
                'request_keys' => array_keys($request->all()),
            ]);

            return redirect()->route('floor-plans.edit', $floorPlan)
                ->withInput()
                ->with('error', 'An unexpected error occurred while updating the floor plan: '.$e->getMessage());
        }
    }

    /**
     * Parse PHP size string (e.g. '8M', '128K', '1G') to bytes.
     */
    private function parsePhpSize(string $size): int
    {
        $size = trim($size);
        $value = (int) $size;
        $unit = strtoupper(substr($size, -1));

        return match ($unit) {
            'G' => $value * 1024 * 1024 * 1024,
            'M' => $value * 1024 * 1024,
            'K' => $value * 1024,
            default => $value,
        };
    }

    /**
     * Internal: perform the actual floor plan update logic.
     */
    private function performUpdate(Request $request, FloorPlan $floorPlan)
    {
        // --- 1. Build validation rules ---
        $uploadRule = 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240';
        try {
            $uploadRule = \App\Helpers\UploadSettingsHelper::getRules(
                \App\Helpers\UploadSettingsHelper::CONTEXT_FLOOR_PLAN, 'floor_image', false
            )['floor_image'];
        } catch (\Throwable $e) {
            \Log::warning('Could not load upload rules, using defaults: '.$e->getMessage());
        }

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_name' => 'nullable|string|max:255',
            'floor_image' => $uploadRule,
            'feature_image' => $uploadRule,
            'google_map_location' => 'nullable|string',
            'proposal' => 'nullable|string',
            'event_start_date' => 'nullable|date',
            'event_end_date' => 'nullable|date|after_or_equal:event_start_date',
            'event_start_time' => ['nullable', function ($attribute, $value, $fail) {
                if ($value !== null && ! preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
                    $fail('The '.$attribute.' must be a valid time (HH:MM or HH:MM:SS).');
                }
            }],
            'event_end_time' => ['nullable', function ($attribute, $value, $fail) {
                if ($value !== null && ! preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
                    $fail('The '.$attribute.' must be a valid time (HH:MM or HH:MM:SS).');
                }
            }],
            'event_location' => 'nullable|string|max:255',
            'event_venue' => 'nullable|string|max:255',
            'canvas_width' => 'nullable|integer|min:100|max:5000',
            'canvas_height' => 'nullable|integer|min:100|max:5000',
            'is_active' => 'nullable',
        ];

        try {
            $tables = DB::select("SHOW TABLES LIKE 'events'");
            $rules['event_id'] = ! empty($tables) ? 'nullable|exists:events,id' : 'nullable';
        } catch (\Throwable $e) {
            $rules['event_id'] = 'nullable';
        }

        // --- 2. Check PHP upload errors before validation ---
        if ($request->hasFile('floor_image')) {
            $file = $request->file('floor_image');
            if (! $file->isValid()) {
                $phpMax = ini_get('upload_max_filesize');
                $errMsg = match ($file->getError()) {
                    UPLOAD_ERR_INI_SIZE => "File exceeds PHP upload limit ({$phpMax}). Increase upload_max_filesize in php.ini.",
                    UPLOAD_ERR_FORM_SIZE => 'File exceeds form size limit.',
                    default => $file->getErrorMessage(),
                };

                return redirect()->route('floor-plans.edit', $floorPlan)
                    ->withInput()
                    ->with('error', 'The floor image failed to upload. '.$errMsg);
            }
        }

        // --- 3. Validate ---
        $validated = $request->validate($rules);

        // Normalize empty strings to null for nullable DB columns
        $nullableFields = [
            'event_id', 'description', 'project_name', 'google_map_location',
            'proposal', 'event_start_date', 'event_end_date', 'event_start_time',
            'event_end_time', 'event_location', 'event_venue',
        ];
        foreach ($nullableFields as $field) {
            if (array_key_exists($field, $validated) && ($validated[$field] === '' || $validated[$field] === null)) {
                $validated[$field] = null;
            }
        }

        // Normalize is_active from select value
        $validated['is_active'] = (int) $request->input('is_active', 0) === 1;

        // --- 4. Handle floor plan image upload ---
        if ($request->hasFile('floor_image')) {
            $image = $request->file('floor_image');
            $imageExtension = strtolower($image->getClientOriginalExtension());
            $imageName = time().'_floor_plan_'.$floorPlan->id.'.'.$imageExtension;
            $imagePath = public_path('images/floor-plans');

            if (! file_exists($imagePath) && ! @mkdir($imagePath, 0755, true)) {
                return redirect()->route('floor-plans.edit', $floorPlan)
                    ->withInput()
                    ->with('error', 'Could not create upload directory. Check server folder permissions.');
            }

            if (! is_writable($imagePath)) {
                return redirect()->route('floor-plans.edit', $floorPlan)
                    ->withInput()
                    ->with('error', 'Upload directory is not writable. Check server folder permissions.');
            }

            $newImageFullPath = $imagePath.DIRECTORY_SEPARATOR.$imageName;

            try {
                $image->move($imagePath, $imageName);
            } catch (\Throwable $e) {
                \Log::error('Floor plan image move() failed', [
                    'floor_plan_id' => $floorPlan->id,
                    'error' => $e->getMessage(),
                ]);

                return redirect()->route('floor-plans.edit', $floorPlan)
                    ->withInput()
                    ->with('error', 'Failed to save uploaded image: '.$e->getMessage());
            }

            if (! file_exists($newImageFullPath)) {
                return redirect()->route('floor-plans.edit', $floorPlan)
                    ->withInput()
                    ->with('error', 'Image file was not found after upload. Please try again.');
            }

            $imageInfo = @getimagesize($newImageFullPath);
            if ($imageInfo === false || ! isset($imageInfo[0], $imageInfo[1])) {
                @unlink($newImageFullPath);

                return redirect()->route('floor-plans.edit', $floorPlan)
                    ->withInput()
                    ->with('error', 'The uploaded file is not a valid image. Please use a JPG, PNG, or GIF file.');
            }

            $validated['floor_image'] = 'images/floor-plans/'.$imageName;
            $validated['canvas_width'] = (int) $imageInfo[0];
            $validated['canvas_height'] = (int) $imageInfo[1];

            \Log::info('Floor plan image uploaded', [
                'floor_plan_id' => $floorPlan->id,
                'new_image' => $validated['floor_image'],
                'dimensions' => $validated['canvas_width'].'x'.$validated['canvas_height'],
            ]);
        }

        // --- 5. Handle feature image upload ---
        if ($request->hasFile('feature_image')) {
            try {
                $fImage = $request->file('feature_image');
                $fName = time().'_feature_'.$floorPlan->id.'.'.strtolower($fImage->getClientOriginalExtension());
                $fPath = public_path('images/floor-plans/features');

                if (! file_exists($fPath)) {
                    @mkdir($fPath, 0755, true);
                }

                $fImage->move($fPath, $fName);

                if (file_exists($fPath.DIRECTORY_SEPARATOR.$fName)) {
                    $validated['feature_image'] = 'images/floor-plans/features/'.$fName;
                }
            } catch (\Throwable $e) {
                \Log::error('Feature image upload failed: '.$e->getMessage());
            }
        }

        // --- 6. Preserve existing values for fields not being updated ---
        if (! isset($validated['floor_image'])) {
            unset($validated['floor_image']);
        }
        if (! isset($validated['feature_image'])) {
            unset($validated['feature_image']);
        }
        if (! $request->hasFile('floor_image')) {
            unset($validated['canvas_width'], $validated['canvas_height']);
        }

        // --- 7. Store old image paths before DB update ---
        $oldFloorImage = $floorPlan->floor_image;
        $oldFeatureImage = $floorPlan->feature_image;
        $isUploadingNewImage = isset($validated['floor_image']);
        $isUploadingNewFeature = isset($validated['feature_image']);

        // --- 8. Update database ---
        $floorPlan->update($validated);
        $floorPlan->refresh();

        // --- 9. Cleanup old images (non-critical) ---
        if ($isUploadingNewImage && $oldFloorImage) {
            $this->safeDeleteOldImage($oldFloorImage, $floorPlan->floor_image, $floorPlan->id);
        }
        if ($isUploadingNewFeature && $oldFeatureImage) {
            $this->safeDeleteOldImage($oldFeatureImage, $floorPlan->feature_image, $floorPlan->id);
        }

        // --- 10. Sync canvas settings (non-critical) ---
        try {
            CanvasSetting::updateOrCreate(
                ['floor_plan_id' => $floorPlan->id],
                [
                    'canvas_width' => $floorPlan->canvas_width ?? 1200,
                    'canvas_height' => $floorPlan->canvas_height ?? 800,
                    'floorplan_image' => $floorPlan->floor_image,
                ]
            );
        } catch (\Throwable $e) {
            \Log::warning('Could not sync canvas settings: '.$e->getMessage());
        }

        // --- 11. Redirect with success ---
        $message = 'Floor plan updated successfully.';
        if ($isUploadingNewImage) {
            $message .= ' Floor plan image uploaded. The canvas will automatically load this image.';
        }

        return redirect()->route('floor-plans.index')->with('success', $message);
    }

    /**
     * Safely delete an old image file after a new one has been saved.
     */
    private function safeDeleteOldImage(string $oldRelativePath, ?string $newRelativePath, int $floorPlanId): void
    {
        try {
            if ($oldRelativePath === $newRelativePath) {
                return;
            }
            $oldFullPath = public_path($oldRelativePath);
            if (file_exists($oldFullPath)) {
                @unlink($oldFullPath);
            }
        } catch (\Throwable $e) {
            \Log::warning('Could not delete old image (non-critical)', [
                'floor_plan_id' => $floorPlanId,
                'old_image' => $oldRelativePath,
                'error' => $e->getMessage(),
            ]);
        }
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

                    if (! $targetFloorPlanId) {
                        return back()->with('error', 'Please select a target floor plan to move booths to.');
                    }

                    $targetFloorPlan = FloorPlan::find($targetFloorPlanId);
                    if (! $targetFloorPlan) {
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
                        'booth_count' => $boothCount,
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
                        'booth_count' => $boothCount,
                    ]);
                }
            }

            // Delete canvas settings for this floor plan
            try {
                CanvasSetting::where('floor_plan_id', $floorPlan->id)->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete canvas settings for floor plan: '.$e->getMessage());
            }

            // Delete zone settings for this floor plan
            try {
                ZoneSetting::where('floor_plan_id', $floorPlan->id)->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete zone settings for floor plan: '.$e->getMessage());
            }

            // Delete floor image if exists
            if ($floorPlan->floor_image && file_exists(public_path($floorPlan->floor_image))) {
                try {
                    unlink(public_path($floorPlan->floor_image));
                } catch (\Exception $e) {
                    \Log::warning('Could not delete floor plan image: '.$e->getMessage());
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
            \Log::error('Error deleting floor plan: '.$e->getMessage(), [
                'floor_plan_id' => $floorPlan->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Error deleting floor plan: '.$e->getMessage());
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
            $newFloorPlan->name = $floorPlan->name.' (Copy)';
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

            return back()->with('error', 'Error duplicating floor plan: '.$e->getMessage());
        }
    }

    /**
     * Generate affiliate link for a floor plan
     * This creates a unique public view link that tracks which sales person shared it
     */
    public function generateAffiliateLink(Request $request, $id)
    {
        if (! Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to generate affiliate links',
            ], 401);
        }

        $user = Auth::user();

        // Only Owner/Admin/Sales Manager can generate links (not regular sales)
        $roleSlug = optional($user->role)->slug;
        $allowedRoleSlugs = ['owner', 'administrator', 'sales-manager'];
        $canGenerate = $user->isAdmin() || in_array($roleSlug, $allowedRoleSlugs);

        if (! $canGenerate) {
            return response()->json([
                'success' => false,
                'message' => 'Only owners, admins, or sales managers can generate tracking links.',
            ], 403);
        }

        $validated = $request->validate([
            'expiry_days' => 'nullable|integer|min:7|max:90', // 1-12 weeks (max 3 months)
        ]);

        // Allowed durations: 1,2,3,4 weeks or 3 months (~90 days)
        $allowedDurations = [7, 14, 21, 28, 60, 90];
        $expiryDays = $validated['expiry_days'] ?? 28;
        if (! in_array($expiryDays, $allowedDurations, true)) {
            $expiryDays = 28;
        }

        $floorPlan = FloorPlan::findOrFail($id);
        $userId = (int) $user->id;

        // Build signed payload to prevent tampering
        $issuedAt = time();
        $payload = implode('|', [$userId, $floorPlan->id, $expiryDays, $issuedAt]);
        $signature = hash_hmac('sha256', $payload, config('app.key'));
        $refCode = base64_encode($payload.'|'.$signature);

        $affiliateLink = url('/floor-plans/'.$floorPlan->id.'/public?ref='.urlencode($refCode));

        return response()->json([
            'success' => true,
            'link' => $affiliateLink,
            'message' => 'Affiliate link generated successfully',
            'expires_in_days' => $expiryDays,
        ]);
    }
}
