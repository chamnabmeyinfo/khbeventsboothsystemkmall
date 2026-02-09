<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Upload avatar image
     */
    public function uploadAvatar(Request $request)
    {
        try {
            // Check if GD extension is available
            if (! extension_loaded('gd')) {
                return response()->json([
                    'success' => false,
                    'message' => 'GD extension is not available. Please enable it in php.ini',
                ], 500);
            }

            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
                'entity_type' => 'required|string|in:user,client,category',
                'entity_id' => 'required|integer',
            ]);

            $file = $request->file('avatar');
            $entityType = $request->entity_type;
            $entityId = $request->entity_id;

            // Create directory structure: storage/app/public/avatars/{entity_type}/
            $directory = public_path('images/avatars/'.$entityType);
            if (! File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid().'_'.time().'.'.$extension;
            $path = $directory.'/'.$filename;

            // Create image using GD library
            $sourceImage = null;
            $imageInfo = getimagesize($file->getRealPath());

            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($file->getRealPath());
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($file->getRealPath());
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($file->getRealPath());
                    break;
                default:
                    throw new \Exception('Unsupported image type');
            }

            // Resize and save original (max 512x512 for avatar)
            $maxSize = 512;
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);

            if ($width > $maxSize || $height > $maxSize) {
                $ratio = min($maxSize / $width, $maxSize / $height);
                $newWidth = (int) ($width * $ratio);
                $newHeight = (int) ($height * $ratio);

                $resized = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency for PNG/GIF
                if ($imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF) {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Save resized image
                if ($extension === 'png') {
                    imagepng($resized, $path);
                } elseif ($extension === 'gif') {
                    imagegif($resized, $path);
                } else {
                    imagejpeg($resized, $path, 90);
                }
                imagedestroy($resized);
            } else {
                // Copy original if smaller than max size
                copy($file->getRealPath(), $path);
            }

            imagedestroy($sourceImage);

            // Generate thumbnail sizes: 40x40, 64x64, 96x96, 128x128
            $sizes = [
                'xs' => 40,
                'sm' => 64,
                'md' => 96,
                'lg' => 128,
            ];

            foreach ($sizes as $size => $dimension) {
                $this->createThumbnail($file->getRealPath(), $directory.'/'.pathinfo($filename, PATHINFO_FILENAME).'_'.$size.'.'.$extension, $dimension, $dimension, $extension);
            }

            // Get relative URL
            $relativePath = 'images/avatars/'.$entityType.'/'.$filename;

            // Update entity model
            $model = $this->getModel($entityType, $entityId);
            if ($model) {
                // Delete old avatar if exists
                if ($model->avatar) {
                    $oldPath = public_path($model->avatar);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                        // Delete thumbnails
                        $oldFilename = pathinfo($model->avatar, PATHINFO_FILENAME);
                        $oldExtension = pathinfo($model->avatar, PATHINFO_EXTENSION);
                        foreach ($sizes as $size => $dimension) {
                            $oldThumbPath = public_path('images/avatars/'.$entityType.'/'.$oldFilename.'_'.$size.'.'.$oldExtension);
                            if (File::exists($oldThumbPath)) {
                                File::delete($oldThumbPath);
                            }
                        }
                    }
                }

                $model->avatar = $relativePath;
                $model->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'image_url' => asset($relativePath),
                'image_path' => $relativePath,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error uploading avatar: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error uploading avatar: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload cover image
     */
    public function uploadCover(Request $request)
    {
        try {
            // Check if GD extension is available
            if (! extension_loaded('gd')) {
                return response()->json([
                    'success' => false,
                    'message' => 'GD extension is not available. Please enable it in php.ini',
                ], 500);
            }

            $request->validate([
                'cover' => 'required|image|mimes:jpeg,jpg,png,gif|max:10240', // 10MB max
                'entity_type' => 'required|string|in:user,client,category',
                'entity_id' => 'required|integer',
            ]);

            $file = $request->file('cover');
            $entityType = $request->entity_type;
            $entityId = $request->entity_id;

            // Create directory structure
            $directory = public_path('images/covers/'.$entityType);
            if (! File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid().'_'.time().'.'.$extension;
            $path = $directory.'/'.$filename;

            // Resize cover image (recommended: 1600x400)
            $sourceImage = null;
            $imageInfo = getimagesize($file->getRealPath());

            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $sourceImage = imagecreatefromjpeg($file->getRealPath());
                    break;
                case IMAGETYPE_PNG:
                    $sourceImage = imagecreatefrompng($file->getRealPath());
                    break;
                case IMAGETYPE_GIF:
                    $sourceImage = imagecreatefromgif($file->getRealPath());
                    break;
                default:
                    throw new \Exception('Unsupported image type');
            }

            $targetWidth = 1600;
            $targetHeight = 400;
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);

            // Calculate crop position (center crop)
            $ratio = max($targetWidth / $width, $targetHeight / $height);
            $newWidth = (int) ($width * $ratio);
            $newHeight = (int) ($height * $ratio);
            $x = (int) (($newWidth - $targetWidth) / 2);
            $y = (int) (($newHeight - $targetHeight) / 2);

            // Create resized image
            $resized = imagecreatetruecolor($targetWidth, $targetHeight);

            // Preserve transparency
            if ($imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
                $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $transparent);
            }

            imagecopyresampled($resized, $sourceImage, -$x, -$y, 0, 0, $newWidth, $newHeight, $width, $height);

            // Save cover image
            if ($extension === 'png') {
                imagepng($resized, $path);
            } elseif ($extension === 'gif') {
                imagegif($resized, $path);
            } else {
                imagejpeg($resized, $path, 85);
            }

            imagedestroy($resized);
            imagedestroy($sourceImage);

            // Get relative URL
            $relativePath = 'images/covers/'.$entityType.'/'.$filename;

            // Update entity model
            $model = $this->getModel($entityType, $entityId);
            if ($model) {
                // Delete old cover if exists
                if ($model->cover_image) {
                    $oldPath = public_path($model->cover_image);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $model->cover_image = $relativePath;
                $model->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cover image uploaded successfully',
                'image_url' => asset($relativePath),
                'image_path' => $relativePath,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error uploading cover: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error uploading cover: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove avatar
     */
    public function removeAvatar(Request $request)
    {
        try {
            $request->validate([
                'entity_type' => 'required|string|in:user,client,category',
                'entity_id' => 'required|integer',
            ]);

            $entityType = $request->entity_type;
            $entityId = $request->entity_id;

            $model = $this->getModel($entityType, $entityId);
            if ($model && $model->avatar) {
                $oldPath = public_path($model->avatar);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                    // Delete thumbnails
                    $oldFilename = pathinfo($model->avatar, PATHINFO_FILENAME);
                    $oldExtension = pathinfo($model->avatar, PATHINFO_EXTENSION);
                    $sizes = ['xs', 'sm', 'md', 'lg'];
                    foreach ($sizes as $size) {
                        $oldThumbPath = public_path('images/avatars/'.$entityType.'/'.$oldFilename.'_'.$size.'.'.$oldExtension);
                        if (File::exists($oldThumbPath)) {
                            File::delete($oldThumbPath);
                        }
                    }
                }

                $model->avatar = null;
                $model->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Avatar removed successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error removing avatar: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error removing avatar: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove cover image
     */
    public function removeCover(Request $request)
    {
        try {
            $request->validate([
                'entity_type' => 'required|string|in:user,client,category',
                'entity_id' => 'required|integer',
            ]);

            $entityType = $request->entity_type;
            $entityId = $request->entity_id;

            $model = $this->getModel($entityType, $entityId);
            if ($model && $model->cover_image) {
                $oldPath = public_path($model->cover_image);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }

                $model->cover_image = null;
                $model->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cover image removed successfully',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error removing cover: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error removing cover: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create thumbnail
     */
    private function createThumbnail($sourcePath, $destinationPath, $targetWidth, $targetHeight, $extension)
    {
        $sourceImage = null;
        $imageInfo = getimagesize($sourcePath);

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        // Create square thumbnail (crop center)
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency
        if ($imageInfo[2] == IMAGETYPE_PNG || $imageInfo[2] == IMAGETYPE_GIF) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $targetWidth, $targetHeight, $transparent);
        }

        // Calculate crop position (center)
        $size = min($width, $height);
        $x = ($width - $size) / 2;
        $y = ($height - $size) / 2;

        imagecopyresampled($thumbnail, $sourceImage, 0, 0, $x, $y, $targetWidth, $targetHeight, $size, $size);

        // Save thumbnail
        if ($extension === 'png') {
            imagepng($thumbnail, $destinationPath);
        } elseif ($extension === 'gif') {
            imagegif($thumbnail, $destinationPath);
        } else {
            imagejpeg($thumbnail, $destinationPath, 90);
        }

        imagedestroy($thumbnail);
        imagedestroy($sourceImage);

        return true;
    }

    /**
     * Get model instance
     */
    private function getModel($entityType, $entityId)
    {
        switch ($entityType) {
            case 'user':
                return \App\Models\User::find($entityId);
            case 'client':
                return \App\Models\Client::find($entityId);
            case 'category':
                return \App\Models\Category::find($entityId);
            default:
                return null;
        }
    }
}
