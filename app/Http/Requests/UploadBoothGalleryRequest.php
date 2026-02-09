<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadBoothGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxSizeKB = env('UPLOAD_MAX_SIZE_KB', 10240); // Default 10MB

        return [
            'gallery_images' => 'required|array|min:1|max:10',
            'gallery_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:'.$maxSizeKB,
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'image_type' => 'nullable|string|in:photo,layout,setup,teardown,facility',
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:500',
        ];
    }
}
