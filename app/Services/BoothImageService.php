<?php

namespace App\Services;

use App\Models\Booth;
use App\Models\BoothImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BoothImageService
{
    /**
     * Upload a single booth image
     */
    public function uploadBoothImage(Booth $booth, UploadedFile $image, ?int $floorPlanId = null): BoothImage
    {
        $floorPlanId = $floorPlanId ?? $booth->floor_plan_id;

        if (! $floorPlanId) {
            throw ValidationException::withMessages([
                'image' => ['Floor plan ID is required for image upload.'],
            ]);
        }

        // Generate unique filename
        $imageExtension = $image->getClientOriginalExtension();
        $imageName = 'booth_'.$booth->id.'_'.time().'_'.uniqid().'.'.$imageExtension;
        $imagePath = 'images/booths/gallery';
        $fullPath = public_path($imagePath);

        // Create directory if it doesn't exist
        if (! file_exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }

        // Move uploaded file
        $image->move($fullPath, $imageName);
        $relativePath = $imagePath.'/'.$imageName;

        // Get current max sort order
        $maxSortOrder = BoothImage::where('booth_id', $booth->id)->max('sort_order') ?? 0;

        // Check if this is the first image (make it primary)
        $isFirstImage = BoothImage::where('booth_id', $booth->id)->count() === 0;

        // Create image record
        $boothImage = BoothImage::create([
            'booth_id' => $booth->id,
            'floor_plan_id' => $floorPlanId,
            'image_path' => $relativePath,
            'image_type' => 'photo',
            'sort_order' => $maxSortOrder + 1,
            'is_primary' => $isFirstImage,
        ]);

        // Log activity
        try {
            \App\Helpers\ActivityLogger::log('booth.image_uploaded', $booth,
                'Booth image uploaded: '.$booth->booth_number);
        } catch (\Exception $e) {
            Log::error('Failed to log image upload activity: '.$e->getMessage());
        }

        return $boothImage;
    }

    /**
     * Upload multiple gallery images
     */
    public function uploadGalleryImages(Booth $booth, array $images, ?int $floorPlanId = null, ?string $imageType = 'photo', ?array $captions = []): array
    {
        $floorPlanId = $floorPlanId ?? $booth->floor_plan_id;

        if (! $floorPlanId) {
            throw ValidationException::withMessages([
                'images' => ['Floor plan ID is required for image upload.'],
            ]);
        }

        $uploadedImages = [];
        $maxSortOrder = BoothImage::where('booth_id', $booth->id)->max('sort_order') ?? 0;
        $isFirstImage = BoothImage::where('booth_id', $booth->id)->count() === 0;

        foreach ($images as $index => $image) {
            if (! $image instanceof UploadedFile) {
                continue;
            }

            // Generate unique filename
            $imageExtension = $image->getClientOriginalExtension();
            $imageName = 'booth_'.$booth->id.'_gallery_'.time().'_'.$index.'.'.$imageExtension;
            $imagePath = 'images/booths/gallery';
            $fullPath = public_path($imagePath);

            // Create directory if it doesn't exist
            if (! file_exists($fullPath)) {
                File::makeDirectory($fullPath, 0755, true);
            }

            // Move uploaded file
            $image->move($fullPath, $imageName);
            $relativePath = $imagePath.'/'.$imageName;

            // Create image record
            $boothImage = BoothImage::create([
                'booth_id' => $booth->id,
                'floor_plan_id' => $floorPlanId,
                'image_path' => $relativePath,
                'image_type' => $imageType ?? 'photo',
                'caption' => $captions[$index] ?? null,
                'sort_order' => $maxSortOrder + $index + 1,
                'is_primary' => $isFirstImage && $index === 0,
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

        // Log activity
        try {
            \App\Helpers\ActivityLogger::log('booth.images_uploaded', $booth,
                'Multiple booth images uploaded: '.$booth->booth_number);
        } catch (\Exception $e) {
            Log::error('Failed to log gallery upload activity: '.$e->getMessage());
        }

        return $uploadedImages;
    }

    /**
     * Get all images for a booth
     */
    public function getBoothImages(int $boothId): array
    {
        $images = BoothImage::where('booth_id', $boothId)
            ->orderBy('sort_order')
            ->get();

        return $images->map(function ($image) {
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
        })->toArray();
    }

    /**
     * Delete a booth image
     */
    public function deleteBoothImage(int $boothId, int $imageId): void
    {
        $image = BoothImage::where('booth_id', $boothId)
            ->where('id', $imageId)
            ->firstOrFail();

        // Delete physical file
        if (file_exists(public_path($image->image_path))) {
            File::delete(public_path($image->image_path));
        }

        $wasPrimary = $image->is_primary;
        $booth = $image->booth;

        // Delete image record
        $image->delete();

        // If this was primary, make another image primary
        if ($wasPrimary) {
            $nextImage = BoothImage::where('booth_id', $boothId)
                ->orderBy('sort_order')
                ->first();

            if ($nextImage) {
                $nextImage->is_primary = true;
                $nextImage->save();
            }
        }

        // Log activity
        try {
            \App\Helpers\ActivityLogger::log('booth.image_deleted', $booth,
                'Booth image deleted: '.$booth->booth_number);
        } catch (\Exception $e) {
            Log::error('Failed to log image deletion activity: '.$e->getMessage());
        }
    }

    /**
     * Set primary image for a booth
     */
    public function setPrimaryImage(int $boothId, int $imageId): BoothImage
    {
        // Remove primary from all images
        BoothImage::where('booth_id', $boothId)
            ->update(['is_primary' => false]);

        // Set new primary
        $image = BoothImage::where('booth_id', $boothId)
            ->where('id', $imageId)
            ->firstOrFail();

        $image->is_primary = true;
        $image->save();

        // Log activity
        try {
            \App\Helpers\ActivityLogger::log('booth.primary_image_set', $image->booth,
                'Primary image set for booth: '.$image->booth->booth_number);
        } catch (\Exception $e) {
            Log::error('Failed to log primary image set activity: '.$e->getMessage());
        }

        return $image;
    }

    /**
     * Update image sort order
     */
    public function updateImageOrder(int $boothId, array $imageIds): void
    {
        foreach ($imageIds as $index => $imageId) {
            BoothImage::where('id', $imageId)
                ->where('booth_id', $boothId)
                ->update(['sort_order' => $index + 1]);
        }

        // Log activity
        try {
            $booth = Booth::find($boothId);
            if ($booth) {
                \App\Helpers\ActivityLogger::log('booth.image_order_updated', $booth,
                    'Image order updated for booth: '.$booth->booth_number);
            }
        } catch (\Exception $e) {
            Log::error('Failed to log image order update activity: '.$e->getMessage());
        }
    }
}
