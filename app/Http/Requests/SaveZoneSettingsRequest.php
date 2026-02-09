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
