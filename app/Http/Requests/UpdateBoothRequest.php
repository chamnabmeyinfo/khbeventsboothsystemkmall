<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoothRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'booth_number' => 'required|string|max:45',
            'type' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'status' => 'required|integer',
            'client_id' => 'nullable|exists:client,id',
            'category_id' => 'nullable|exists:category,id',
            'asset_id' => 'nullable|exists:asset,id',
            'booth_type_id' => 'nullable|exists:booth_type,id',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'description' => 'nullable|string|max:2000',
            'features' => 'nullable|string|max:2000',
            'capacity' => 'nullable|integer|min:0',
            'area_sqm' => 'nullable|numeric|min:0',
            'electricity_power' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
            'booth_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120',
            // Position and styling fields
            'position_x' => 'nullable|numeric',
            'position_y' => 'nullable|numeric',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'rotation' => 'nullable|numeric',
            'z_index' => 'nullable|integer',
            'font_size' => 'nullable|numeric|min:0',
            'border_width' => 'nullable|numeric|min:0',
            'border_radius' => 'nullable|numeric|min:0',
            'opacity' => 'nullable|numeric|min:0|max:1',
            'background_color' => 'nullable|string|max:50',
            'border_color' => 'nullable|string|max:50',
            'text_color' => 'nullable|string|max:50',
            'font_weight' => 'nullable|string|max:50',
            'font_family' => 'nullable|string|max:100',
            'text_align' => 'nullable|string|in:left,center,right',
            'box_shadow' => 'nullable|string|max:200',
            // Payment fields
            'deposit_amount' => 'nullable|numeric|min:0',
            'deposit_paid' => 'nullable|numeric|min:0',
            'balance_due' => 'nullable|numeric|min:0',
            'balance_paid' => 'nullable|numeric|min:0',
            'payment_due_date' => 'nullable|date',
            'deposit_paid_date' => 'nullable|date',
            'balance_paid_date' => 'nullable|date',
            'payment_status' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'booth_number.required' => 'Booth number is required.',
            'booth_number.max' => 'Booth number cannot exceed 45 characters.',
            'type.required' => 'Booth type is required.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'status.required' => 'Status is required.',
            'booth_image.image' => 'Booth image must be an image file.',
            'booth_image.mimes' => 'Booth image must be a jpeg, jpg, png, or gif file.',
            'booth_image.max' => 'Booth image cannot exceed 5MB.',
        ];
    }
}
