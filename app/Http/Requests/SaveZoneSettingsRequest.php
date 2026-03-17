<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveZoneSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:5',
            'height' => 'nullable|numeric|min:5',
            'rotation' => 'nullable|numeric|min:-360|max:360',
            'zIndex' => 'nullable|integer|min:1',
            'z_index' => 'nullable|integer|min:1',
            'borderRadius' => 'nullable|numeric|min:0',
            'border_radius' => 'nullable|numeric|min:0',
            'borderWidth' => 'nullable|numeric|min:0',
            'border_width' => 'nullable|numeric|min:0',
            'opacity' => 'nullable|numeric|min:0|max:1',
            'background_color' => 'nullable|string|max:50',
            'border_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50',
            'font_weight' => 'nullable|string|max:20',
            'font_family' => 'nullable|string|max:255',
            'text_align' => 'nullable|string|max:20',
            'box_shadow' => 'nullable|string|max:255',
            'zone_about' => 'nullable|string|max:500',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
        ];
    }
}
