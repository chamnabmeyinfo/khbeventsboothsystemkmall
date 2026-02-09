<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBoothsInZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mode' => 'required|in:all,specific,range',
            'booth_ids' => 'required_if:mode,specific|array',
            'booth_ids.*' => 'exists:booth,id',
            'from' => 'required_if:mode,range|nullable|integer|min:1|max:9999',
            'to' => 'required_if:mode,range|nullable|integer|min:1|max:9999',
            'floor_plan_id' => 'required|exists:floor_plans,id',
            'force_delete_booked' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'mode.required' => 'Deletion mode is required.',
            'mode.in' => 'Mode must be one of: all, specific, range.',
            'booth_ids.required_if' => 'Booth IDs are required when mode is specific.',
            'from.required_if' => 'From number is required when mode is range.',
            'to.required_if' => 'To number is required when mode is range.',
            'floor_plan_id.required' => 'Floor plan ID is required.',
            'floor_plan_id.exists' => 'Selected floor plan does not exist.',
        ];
    }
}
