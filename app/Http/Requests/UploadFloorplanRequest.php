<?php

namespace App\Http\Requests;

use App\Helpers\UploadSettingsHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UploadFloorplanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Check for PHP upload errors (e.g. upload_max_filesize exceeded) before validation
        if ($this->hasFile('floorplan_image') && ! $this->file('floorplan_image')->isValid()) {
            $file = $this->file('floorplan_image');
            $phpMax = ini_get('upload_max_filesize');
            $errMsg = $file->getErrorMessage();
            if ($file->getError() === UPLOAD_ERR_INI_SIZE) {
                $errMsg = "File exceeds PHP upload limit ({$phpMax}). Use: composer run serve-uploads (or increase upload_max_filesize in php.ini).";
            }

            $this->merge(['_upload_error' => $errMsg]);
        }
    }

    public function rules(): array
    {
        $rules = UploadSettingsHelper::getRules(UploadSettingsHelper::CONTEXT_FLOOR_PLAN, 'floorplan_image', true);

        return array_merge($rules, [
            'floor_plan_id' => 'required|exists:floor_plans,id',
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->has('_upload_error')) {
                $validator->errors()->add('floorplan_image', $this->get('_upload_error'));
            }
        });
    }

    public function messages(): array
    {
        $maxMb = UploadSettingsHelper::getMaxSizeKb(UploadSettingsHelper::CONTEXT_FLOOR_PLAN) / 1024;

        return [
            'floorplan_image.required' => 'Floorplan image is required.',
            'floorplan_image.image' => 'The file must be an image.',
            'floorplan_image.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif.',
            'floorplan_image.max' => "The image size must not exceed {$maxMb} MB.",
            'floor_plan_id.required' => 'Floor plan ID is required.',
            'floor_plan_id.exists' => 'Selected floor plan does not exist.',
        ];
    }
}
