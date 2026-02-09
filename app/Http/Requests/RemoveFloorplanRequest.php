<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveFloorplanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'floor_plan_id' => 'required|exists:floor_plans,id',
        ];
    }

    public function messages(): array
    {
        return [
            'floor_plan_id.required' => 'Floor plan ID is required.',
            'floor_plan_id.exists' => 'Selected floor plan does not exist.',
        ];
    }
}
