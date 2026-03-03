<?php

namespace App\Helpers;

use App\Models\Setting;

class UploadSettingsHelper
{
    public const CONTEXT_FLOOR_PLAN = 'floor_plan';

    public const CONTEXT_BOOTH = 'booth';

    public const CONTEXT_AVATAR = 'avatar';

    public const CONTEXT_COVER = 'cover';

    public const CONTEXT_DOCUMENT = 'document';

    public const CONTEXT_TRAINING_CERTIFICATE = 'training_certificate';

    public const CONTEXT_COMPANY_LOGO = 'company_logo';

    /** Default max size in KB when no setting (Laravel validation uses KB) */
    protected const DEFAULT_MAX_KB = 10240; // 10MB

    /**
     * Get validation rules for a file input in the given context.
     * Returns Laravel rules array, e.g. ['floor_image' => 'required|image|mimes:jpg,png|max:5120']
     */
    public static function getRules(string $context, string $fieldName, bool $required = true): array
    {
        $maxKb = self::getMaxSizeKb($context);
        $mimes = self::getMimesRule($context);
        $type = in_array($context, [self::CONTEXT_FLOOR_PLAN, self::CONTEXT_BOOTH, self::CONTEXT_AVATAR, self::CONTEXT_COVER, self::CONTEXT_COMPANY_LOGO], true) ? 'image' : 'file';

        $rule = ($required ? 'required|' : 'nullable|')."{$type}|{$mimes}|max:{$maxKb}";

        return [$fieldName => $rule];
    }

    /**
     * Get max file size in KB for context (falls back to global, then default).
     */
    public static function getMaxSizeKb(string $context): int
    {
        $key = "uploads_{$context}_max_size_mb";
        $mb = Setting::getValue($key, null);
        if ($mb !== null && $mb !== '' && (float) $mb > 0) {
            return (int) ((float) $mb * 1024);
        }

        $globalMb = Setting::getUploadMaxSizeMb();

        return $globalMb !== null ? (int) ($globalMb * 1024) : self::DEFAULT_MAX_KB;
    }

    /** Default extensions per context when no setting */
    public const DEFAULT_EXTENSIONS = [
        'floor_plan' => ['jpeg', 'jpg', 'png', 'gif'],
        'booth' => ['jpeg', 'jpg', 'png', 'gif'],
        'avatar' => ['jpeg', 'jpg', 'png', 'gif'],
        'cover' => ['jpeg', 'jpg', 'png', 'gif'],
        'document' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'],
        'training_certificate' => ['pdf', 'jpg', 'jpeg', 'png'],
        'company_logo' => ['jpeg', 'jpg', 'png', 'gif', 'ico'],
    ];

    /**
     * Get mimes rule string for context, e.g. 'mimes:jpg,png,gif'
     */
    public static function getMimesRule(string $context): string
    {
        $exts = self::getExtensionsForContext($context);

        return 'mimes:'.implode(',', $exts);
    }

    /**
     * Get human-readable hint for upload forms, e.g. "Max 5 MB, JPG, PNG, GIF"
     */
    public static function getHint(string $context): string
    {
        $maxMb = self::getMaxSizeKb($context) / 1024;
        $exts = self::getExtensionsForContext($context);

        $parts = ["Max {$maxMb} MB"];
        if (! empty($exts)) {
            $parts[] = implode(', ', array_map('strtoupper', $exts));
        }

        return implode(', ', $parts);
    }

    protected static function getExtensionsForContext(string $context): array
    {
        $key = "uploads_{$context}_allowed_extensions";
        $val = Setting::getValue($key, '');
        if ($val !== null && trim((string) $val) !== '') {
            $arr = array_map('trim', explode(',', (string) $val));

            return array_values(array_filter(array_map(fn ($e) => strtolower(ltrim($e, '.')), $arr)));
        }

        $global = Setting::getUploadAllowedExtensions();
        if (! empty($global)) {
            return $global;
        }

        return self::DEFAULT_EXTENSIONS[$context] ?? ['jpeg', 'jpg', 'png', 'gif'];
    }

    /**
     * Get accept attribute for file input, e.g. "image/*" or ".jpg,.png,.pdf"
     */
    public static function getAcceptAttribute(string $context): string
    {
        $exts = self::getExtensionsForContext($context);
        if (empty($exts)) {
            return 'image/*';
        }

        $imageOnly = [self::CONTEXT_FLOOR_PLAN, self::CONTEXT_BOOTH, self::CONTEXT_AVATAR, self::CONTEXT_COVER, self::CONTEXT_COMPANY_LOGO];
        if (in_array($context, $imageOnly, true)) {
            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (empty(array_diff($exts, $imageExts))) {
                return 'image/*';
            }
        }

        return '.'.implode(',.', $exts);
    }
}
