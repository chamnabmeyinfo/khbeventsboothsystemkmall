<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookBoothRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booth_id' => 'required|integer|exists:booth,id',
            'client_id' => 'nullable|integer|exists:client,id',
            'name' => 'required|string|max:255',
            'sex' => 'nullable|integer|in:1,2,3',
            'company' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:50',
            'email' => 'required|email|max:191',
            'address' => 'required|string',
            'tax_id' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|integer|in:2,3,5', // 2=Confirmed, 3=Reserved, 5=Paid
            'type' => 'nullable|integer|in:1,2,3', // 1=Regular, 2=Special, 3=Temporary
        ];
    }

    public function messages(): array
    {
        return [
            'booth_id.required' => 'Booth ID is required.',
            'booth_id.exists' => 'Selected booth does not exist.',
            'name.required' => 'Client name is required.',
            'company.required' => 'Company name is required.',
            'phone_number.required' => 'Phone number is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'address.required' => 'Address is required.',
            'status.required' => 'Booking status is required.',
            'status.in' => 'Invalid booking status.',
        ];
    }
}
