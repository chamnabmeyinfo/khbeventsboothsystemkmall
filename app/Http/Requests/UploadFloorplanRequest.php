<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFloorplanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxSizeKB = env('UPLOAD_MAX_SIZE_KB', 102400); // Default 100MB in KB

        return [
            'floorplan_image' => 'required|image|mimes:jpeg,jpg,png,gif|max:'.$maxSizeKB,
            'floor_plan_id' => 'required|exists:floor_plans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'floorplan_image.required' => 'Floorplan image is required.',
            'floorplan_image.image' => 'The file must be an image.',
            'floorplan_image.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif.',
            'floorplan_image.max' => 'The image size must not exceed '.(env('UPLOAD_MAX_SIZE_KB', 102400) / 1024).'MB.',
            'floor_plan_id.required' => 'Floor plan ID is required.',
            'floor_plan_id.exists' => 'Selected floor plan does not exist.',
        ];
    }
}
