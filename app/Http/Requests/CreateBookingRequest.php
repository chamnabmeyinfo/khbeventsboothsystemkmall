<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
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
            'clientid' => 'required|exists:client,id',
            'booth_ids' => 'required|array|min:1',
            'booth_ids.*' => 'exists:booth,id',
            'date_book' => 'nullable|date',
            'type' => 'nullable|integer|in:1,2,3',
            'notes' => 'nullable|string|max:2000',
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'event_id' => 'nullable|exists:events,id',
            'affiliate_user_id' => 'nullable|exists:user,id',
            'payment_due_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'clientid.required' => 'Client is required.',
            'clientid.exists' => 'Selected client does not exist.',
            'booth_ids.required' => 'At least one booth must be selected.',
            'booth_ids.array' => 'Booth IDs must be an array.',
            'booth_ids.min' => 'At least one booth must be selected.',
            'booth_ids.*.exists' => 'One or more selected booths do not exist.',
            'date_book.date' => 'Booking date must be a valid date.',
            'type.in' => 'Booking type must be 1 (Regular), 2 (Special), or 3 (Temporary).',
            'notes.max' => 'Notes cannot exceed 2000 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-set date_book to current date/time if not provided
        if (empty($this->date_book)) {
            $this->merge(['date_book' => now()]);
        }
    }
}
