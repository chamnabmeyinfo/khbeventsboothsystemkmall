<?php

namespace App\Http\Requests;

use App\Helpers\UploadSettingsHelper;
use Illuminate\Foundation\Http\FormRequest;

class UploadBoothImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = UploadSettingsHelper::getRules(UploadSettingsHelper::CONTEXT_BOOTH, 'booth_image', true);

        return array_merge($rules, [
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
        ]);
    }
}
