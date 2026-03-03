<?php

namespace App\Http\Requests;

use App\Helpers\UploadSettingsHelper;
use Illuminate\Foundation\Http\FormRequest;

class UploadBoothGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fileRules = UploadSettingsHelper::getRules(UploadSettingsHelper::CONTEXT_BOOTH, 'gallery_images.*', false);
        $maxKb = UploadSettingsHelper::getMaxSizeKb(UploadSettingsHelper::CONTEXT_BOOTH);
        $mimes = UploadSettingsHelper::getMimesRule(UploadSettingsHelper::CONTEXT_BOOTH);

        return [
            'gallery_images' => 'required|array|min:1|max:10',
            'gallery_images.*' => "image|{$mimes}|max:{$maxKb}",
            'floor_plan_id' => 'nullable|exists:floor_plans,id',
            'image_type' => 'nullable|string|in:photo,layout,setup,teardown,facility',
            'captions' => 'nullable|array',
            'captions.*' => 'nullable|string|max:500',
        ];
    }
}
