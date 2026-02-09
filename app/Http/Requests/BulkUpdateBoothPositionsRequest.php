<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateBoothPositionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booths' => 'required|array',
            'booths.*.id' => 'required|exists:booth,id',
            'booths.*.position_x' => 'nullable|numeric',
            'booths.*.position_y' => 'nullable|numeric',
            'booths.*.width' => 'nullable|numeric',
            'booths.*.height' => 'nullable|numeric',
            'booths.*.rotation' => 'nullable|numeric',
            'booths.*.z_index' => 'nullable|integer|min:1|max:1000',
            'booths.*.font_size' => 'nullable|integer|min:8|max:48',
            'booths.*.border_width' => 'nullable|integer|min:0|max:10',
            'booths.*.border_radius' => 'nullable|integer|min:0|max:50',
            'booths.*.opacity' => 'nullable|numeric|min:0|max:1',
            'booths.*.price' => 'nullable|numeric|min:0',
            'booths.*.background_color' => 'nullable|string|max:50',
            'booths.*.border_color' => 'nullable|string|max:50',
            'booths.*.text_color' => 'nullable|string|max:50',
            'booths.*.font_weight' => 'nullable|string|max:20',
            'booths.*.font_family' => 'nullable|string|max:255',
            'booths.*.text_align' => 'nullable|string|max:20',
            'booths.*.box_shadow' => 'nullable|string|max:255',
            'booths.*.is_locked' => 'nullable|integer|in:0,1',
        ];
    }
}
