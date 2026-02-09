<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('client')->id ?? null;

        return [
            'name' => 'nullable|string|max:45',
            'sex' => 'nullable|integer|in:1,2,3',
            'position' => 'nullable|string|max:191',
            'company' => 'nullable|string|max:191',
            'company_name_khmer' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'phone_1' => 'nullable|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'email' => [
                'nullable',
                'string',
                'max:191',
                'email:rfc,dns',
                Rule::unique('client', 'email')->ignore($clientId),
            ],
            'email_1' => [
                'nullable',
                'string',
                'max:191',
                'email:rfc,dns',
            ],
            'email_2' => [
                'nullable',
                'string',
                'max:191',
                'email:rfc,dns',
            ],
            'website' => [
                'nullable',
                'string',
                'max:255',
                'url',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Name cannot exceed 45 characters.',
            'sex.in' => 'Sex must be 1, 2, or 3.',
            'position.max' => 'Position cannot exceed 191 characters.',
            'company.max' => 'Company name cannot exceed 191 characters.',
            'company_name_khmer.max' => 'Company name (Khmer) cannot exceed 255 characters.',
            'phone_number.max' => 'Phone number cannot exceed 20 characters.',
            'phone_1.max' => 'Phone 1 cannot exceed 20 characters.',
            'phone_2.max' => 'Phone 2 cannot exceed 20 characters.',
            'tax_id.max' => 'Tax ID cannot exceed 50 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'Email cannot exceed 191 characters.',
            'email.unique' => 'The email has already been taken. Please use a different email or update the existing client.',
            'email_1.email' => 'The email 1 must be a valid email address.',
            'email_1.max' => 'Email 1 cannot exceed 191 characters.',
            'email_2.email' => 'The email 2 must be a valid email address.',
            'email_2.max' => 'Email 2 cannot exceed 191 characters.',
            'website.url' => 'The website must be a valid URL.',
            'website.max' => 'Website cannot exceed 255 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Filter out empty string values and convert to null
        $data = $this->all();
        foreach ($data as $key => $value) {
            if (is_string($value) && trim($value) === '') {
                $data[$key] = null;
            }
        }
        $this->merge($data);
    }
}
