<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckBoothsBookingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booth_ids' => 'required|array',
            'booth_ids.*' => 'integer|exists:booth,id',
        ];
    }

    public function messages(): array
    {
        return [
            'booth_ids.required' => 'Booth IDs are required.',
            'booth_ids.array' => 'Booth IDs must be an array.',
            'booth_ids.*.integer' => 'Each booth ID must be an integer.',
            'booth_ids.*.exists' => 'One or more booth IDs do not exist.',
        ];
    }
}
