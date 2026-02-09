<?php

namespace App\Services;

use App\Models\CanvasSetting;
use App\Models\FloorPlan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class FloorPlanService
{
    /**
     * Upload floorplan image for a floor plan
     */
    public function uploadFloorplan(int $floorPlanId, UploadedFile $image): array
    {
        $floorPlan = FloorPlan::findOrFail($floorPlanId);

        $imageExtension = $image->getClientOriginalExtension();
        $imageName = time().'_floor_plan_'.$floorPlanId.'.'.$imageExtension;

        // Ensure floor plans images directory exists
        $floorPlansPath = public_path('images/floor-plans');
        if (! file_exists($floorPlansPath)) {
            mkdir($floorPlansPath, 0755, true);
        }

        // Save new image FIRST before deleting old one (prevents data loss if save fails)
        $image->move($floorPlansPath, $imageName);

        // Verify the new file was created successfully
        $newImagePath = $floorPlansPath.'/'.$imageName;
        if (! file_exists($newImagePath)) {
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
        $floorPlan->floor_image = 'images/floor-plans/'.$imageName;
        $floorPlan->canvas_width = $imageWidth;
        $floorPlan->canvas_height = $imageHeight;

        // Save to database (CRITICAL: Save BEFORE deleting old file)
        $saved = $floorPlan->save();

        if (! $saved) {
            // Database save failed - delete the new file we just created to prevent orphaned files
            if (file_exists($newImagePath)) {
                unlink($newImagePath);
            }
            Log::error('Failed to save floor plan image path to database', [
                'floor_plan_id' => $floorPlanId,
                'image_name' => $imageName,
                'image_path' => 'images/floor-plans/'.$imageName,
            ]);
            throw new \Exception('Failed to save floor plan image path to database');
        }

        // Refresh floor plan from database to ensure we have latest values
        $floorPlan->refresh();

        // Verify the image path was saved correctly
        if ($floorPlan->floor_image !== 'images/floor-plans/'.$imageName) {
            Log::error('Floor plan image path mismatch after save - attempting fix', [
                'floor_plan_id' => $floorPlanId,
                'expected' => 'images/floor-plans/'.$imageName,
                'actual' => $floorPlan->floor_image,
            ]);
            // Try to fix it
            $floorPlan->floor_image = 'images/floor-plans/'.$imageName;
            $floorPlan->save();
            $floorPlan->refresh();
        }

        // NOW delete old image (only after successful database update)
        if ($oldImageExists && $oldImagePath !== $newImagePath) {
            try {
                unlink($oldImagePath);
                Log::info('Deleted old floor plan image', [
                    'floor_plan_id' => $floorPlanId,
                    'old_image' => $oldImagePath,
                    'new_image' => $newImagePath,
                ]);
            } catch (\Exception $e) {
                Log::warning('Could not delete old floor plan image (non-critical): '.$e->getMessage(), [
                    'floor_plan_id' => $floorPlanId,
                    'old_image' => $oldImagePath,
                ]);
            }
        }

        // Update canvas settings
        try {
            CanvasSetting::updateOrCreate(
                ['floor_plan_id' => $floorPlanId],
                [
                    'canvas_width' => $imageWidth,
                    'canvas_height' => $imageHeight,
                    'floorplan_image' => $floorPlan->floor_image,
                ]
            );

            Log::info('Canvas settings updated for floor plan '.$floorPlanId, [
                'floor_plan_id' => $floorPlanId,
                'floorplan_image' => $floorPlan->floor_image,
                'canvas_width' => $imageWidth,
                'canvas_height' => $imageHeight,
            ]);
        } catch (\Exception $e) {
            Log::error('Could not update canvas settings for floor plan: '.$e->getMessage(), [
                'floor_plan_id' => $floorPlanId,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return [
            'image_url' => asset($floorPlan->floor_image),
            'image_path' => $floorPlan->floor_image,
            'image_width' => $imageWidth,
            'image_height' => $imageHeight,
            'canvas_width' => $imageWidth,
            'canvas_height' => $imageHeight,
            'floor_plan_id' => $floorPlanId,
        ];
    }

    /**
     * Remove floorplan image for a floor plan
     */
    public function removeFloorplan(int $floorPlanId): void
    {
        $floorPlan = FloorPlan::findOrFail($floorPlanId);

        // Delete floor plan's image if exists
        if ($floorPlan->floor_image && file_exists(public_path($floorPlan->floor_image))) {
            unlink(public_path($floorPlan->floor_image));
        }

        // Clear floor image from floor plan record
        $floorPlan->floor_image = null;
        $floorPlan->save();
    }
}
