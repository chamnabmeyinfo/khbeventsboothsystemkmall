<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'summary' => ['nullable', 'string', 'max:500'],
            'changelog' => ['nullable', 'string', 'max:65535'],
        ];
    }
}
