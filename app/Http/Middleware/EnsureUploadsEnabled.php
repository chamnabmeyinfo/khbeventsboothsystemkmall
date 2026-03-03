<?php

namespace App\Http\Middleware;

use App\Helpers\UploadSettingsHelper;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class EnsureUploadsEnabled
{
    /** Route name => context for per-context limits */
    protected static array $routeContextMap = [
        'booths.upload-floorplan' => UploadSettingsHelper::CONTEXT_FLOOR_PLAN,
        'booths.upload-image' => UploadSettingsHelper::CONTEXT_BOOTH,
        'booths.upload-gallery' => UploadSettingsHelper::CONTEXT_BOOTH,
        'floor-plans.store' => UploadSettingsHelper::CONTEXT_FLOOR_PLAN,
        'floor-plans.update' => UploadSettingsHelper::CONTEXT_FLOOR_PLAN,
        'booths.store' => UploadSettingsHelper::CONTEXT_BOOTH,
        'booths.update' => UploadSettingsHelper::CONTEXT_BOOTH,
        'images.avatar.upload' => UploadSettingsHelper::CONTEXT_AVATAR,
        'images.cover.upload' => UploadSettingsHelper::CONTEXT_COVER,
        'employee.profile.update' => UploadSettingsHelper::CONTEXT_AVATAR,
        'settings.company.upload-logo' => UploadSettingsHelper::CONTEXT_COMPANY_LOGO,
        'settings.company.upload-favicon' => UploadSettingsHelper::CONTEXT_COMPANY_LOGO,
        'hr.training.store' => UploadSettingsHelper::CONTEXT_TRAINING_CERTIFICATE,
        'hr.training.update' => UploadSettingsHelper::CONTEXT_TRAINING_CERTIFICATE,
        'hr.documents.store' => UploadSettingsHelper::CONTEXT_DOCUMENT,
        'hr.documents.update' => UploadSettingsHelper::CONTEXT_DOCUMENT,
    ];

    /**
     * Validate file uploads against settings (enabled, max size, allowed extensions).
     * Uses per-context limits when route is mapped.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $files = $request->allFiles();
        if (empty($files)) {
            return $next($request);
        }

        if (! Setting::getUploadsEnabled()) {
            return $this->reject($request, 'File uploads are currently disabled by the administrator.');
        }

        $context = self::$routeContextMap[$request->route()?->getName() ?? ''] ?? null;
        $maxKb = $context ? UploadSettingsHelper::getMaxSizeKb($context) : (Setting::getUploadMaxSizeMb() !== null ? (int) (Setting::getUploadMaxSizeMb() * 1024) : null);
        $allowedExts = $context ? self::getExtensionsForContext($context) : Setting::getUploadAllowedExtensions();
        $maxBytes = $maxKb !== null ? $maxKb * 1024 : null;

        foreach ($this->flattenFiles($files) as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            if ($maxBytes !== null && $file->getSize() > $maxBytes) {
                $maxMbLabel = $maxKb / 1024;

                return $this->reject($request, "File \"{$file->getClientOriginalName()}\" exceeds the maximum size of {$maxMbLabel} MB.");
            }

            if (! empty($allowedExts)) {
                $ext = strtolower(ltrim($file->getClientOriginalExtension(), '.'));
                if ($ext === '' || ! in_array($ext, $allowedExts, true)) {
                    $extList = implode(', ', $allowedExts);

                    return $this->reject($request, "File type \".{$ext}\" is not allowed. Allowed: {$extList}");
                }
            }
        }

        return $next($request);
    }

    protected static function getExtensionsForContext(?string $context): array
    {
        if (! $context) {
            return Setting::getUploadAllowedExtensions();
        }

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

        return UploadSettingsHelper::DEFAULT_EXTENSIONS[$context] ?? ['jpeg', 'jpg', 'png', 'gif'];
    }

    /**
     * Flatten nested file arrays (e.g. gallery_images[])
     */
    protected function flattenFiles(array $files): array
    {
        $result = [];
        foreach ($files as $f) {
            if (is_array($f)) {
                $result = array_merge($result, $this->flattenFiles($f));
            } else {
                $result[] = $f;
            }
        }

        return $result;
    }

    protected function reject(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return back()->with('error', $message);
    }
}
