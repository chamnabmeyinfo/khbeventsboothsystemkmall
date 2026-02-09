<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSystemVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'version' => ['required', 'string', 'max:45', 'unique:system_versions,version'],
            'released_at' => ['required', 'date'],
            'summary' => ['nullable', 'string', 'max:500'],
            'changelog' => ['nullable', 'string', 'max:65535'],
            'is_current' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'version.required' => 'Version number is required.',
            'version.unique' => 'This version number already exists.',
            'released_at.required' => 'Release date is required.',
        ];
    }
}
