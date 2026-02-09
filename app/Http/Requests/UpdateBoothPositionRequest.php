<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoothPositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'position_x' => 'nullable|numeric',
            'position_y' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'rotation' => 'nullable|numeric',
            'z_index' => 'nullable|integer|min:1|max:1000',
            'font_size' => 'nullable|integer|min:8|max:48',
            'border_width' => 'nullable|integer|min:0|max:10',
            'border_radius' => 'nullable|integer|min:0|max:50',
            'opacity' => 'nullable|numeric|min:0|max:1',
            'price' => 'nullable|numeric|min:0',
            'background_color' => 'nullable|string|max:50',
            'border_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50',
            'font_weight' => 'nullable|string|max:20',
            'font_family' => 'nullable|string|max:255',
            'text_align' => 'nullable|string|max:20',
            'box_shadow' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'z_index.integer' => 'Z-index must be an integer between 1 and 1000.',
            'z_index.min' => 'Z-index must be at least 1.',
            'z_index.max' => 'Z-index must not exceed 1000.',
            'font_size.integer' => 'Font size must be an integer between 8 and 48.',
            'font_size.min' => 'Font size must be at least 8.',
            'font_size.max' => 'Font size must not exceed 48.',
            'border_width.integer' => 'Border width must be an integer between 0 and 10.',
            'border_width.min' => 'Border width must be at least 0.',
            'border_width.max' => 'Border width must not exceed 10.',
            'border_radius.integer' => 'Border radius must be an integer between 0 and 50.',
            'border_radius.min' => 'Border radius must be at least 0.',
            'border_radius.max' => 'Border radius must not exceed 50.',
            'opacity.numeric' => 'Opacity must be a number between 0 and 1.',
            'opacity.min' => 'Opacity must be at least 0.',
            'opacity.max' => 'Opacity must not exceed 1.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convert empty strings to null
        $data = $this->all();
        foreach ($data as $key => $value) {
            if ($value === '' || $value === 'null' || $value === 'undefined') {
                $data[$key] = null;
            }
        }
        $this->merge($data);
    }
}
