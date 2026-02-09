<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadBoothImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxSizeKB = env('UPLOAD_MAX_SIZE_KB', 10240); // Default 10MB

        return [
            'booth_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:'.$maxSizeKB,
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
        ];
    }
}
