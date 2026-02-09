<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBoothsInZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booth_number' => 'nullable|string|max:45',
            'count' => 'nullable|integer|min:1|max:100',
            'from' => 'nullable|integer|min:1|max:9999',
            'to' => 'nullable|integer|min:1|max:9999',
            'format' => 'nullable|integer|min:1|max:4',
            'floor_plan_id' => 'required|exists:floor_plans,id',
            'zone_about' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'floor_plan_id.required' => 'Floor plan ID is required.',
            'floor_plan_id.exists' => 'Selected floor plan does not exist.',
            'count.max' => 'Maximum 100 booths can be created at once.',
            'from.max' => 'From number cannot exceed 9999.',
            'to.max' => 'To number cannot exceed 9999.',
        ];
    }
}
