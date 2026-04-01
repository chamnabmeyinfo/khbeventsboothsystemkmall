<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class LandingPage extends Model
{
    use HasFactory;

    /** @var list<string> */
    public const VISUAL_SHARED_KEYS = [
        'logo_image',
        'hero_background_image',
        'about_image',
        'why_image',
        'hero_cta_target',
    ];

    protected $fillable = [
        'name',
        'slug',
        'industry',
        'headline',
        'html_content',
        'css_content',
        'js_content',
        'redirect_url',
        'show_once_mode',
        'allow_inline_scripts',
        'use_visual_builder',
        'template_key',
        'visual_content',
        'default_locale',
        'enabled_locales',
        'priority',
        'is_active',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'allow_inline_scripts' => 'boolean',
        'use_visual_builder' => 'boolean',
        'visual_content' => 'array',
        'enabled_locales' => 'array',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function events(): HasMany
    {
        return $this->hasMany(LandingPageEvent::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public static function getActivePublished(): ?self
    {
        return self::query()
            ->where('is_active', true)
            ->where('is_published', true)
            ->orderBy('priority')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Locale codes allowed for landing pages (matches config/landing_locales.php).
     *
     * @return list<string>
     */
    public static function allowedLocaleCodes(): array
    {
        $fromConfig = config('landing_locales.allowed');
        if (is_array($fromConfig) && $fromConfig !== []) {
            return array_keys($fromConfig);
        }

        return ['en', 'km', 'zh'];
    }

    /**
     * @return list<string>
     */
    public function enabledLocaleList(): array
    {
        $allowed = self::allowedLocaleCodes();
        $raw = $this->enabled_locales;
        if (! is_array($raw) || $raw === []) {
            return ['en'];
        }
        $filtered = array_values(array_intersect(
            array_map(static fn ($v) => strtolower(trim((string) $v)), $raw),
            $allowed
        ));

        return $filtered !== [] ? $filtered : ['en'];
    }

    public function langCookieName(): string
    {
        $safe = preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $this->slug) ?: 'page';

        return 'landing_lang_'.$safe;
    }

    public function resolveLocaleForRequest(Request $request): string
    {
        $enabled = $this->enabledLocaleList();
        $def = strtolower(trim((string) ($this->default_locale ?: 'en')));
        if (! in_array($def, $enabled, true)) {
            $def = $enabled[0];
        }

        $q = strtolower(trim((string) $request->query('lang', '')));
        if ($q !== '' && in_array($q, $enabled, true)) {
            return $q;
        }

        $cookie = $request->cookie($this->langCookieName());
        $cookie = is_string($cookie) ? strtolower(trim($cookie)) : '';
        if ($cookie !== '' && in_array($cookie, $enabled, true)) {
            return $cookie;
        }

        return $def;
    }

    /**
     * Flattened visual fields for the active locale (shared assets + i18n block).
     *
     * @return array<string, mixed>
     */
    public function visualForLocale(string $locale): array
    {
        $v = is_array($this->visual_content) ? $this->visual_content : [];
        $enabled = $this->enabledLocaleList();
        $def = strtolower(trim((string) ($this->default_locale ?: 'en')));
        if (! in_array($def, $enabled, true)) {
            $def = $enabled[0];
        }
        $locale = strtolower(trim($locale));
        if (! in_array($locale, $enabled, true)) {
            $locale = $def;
        }

        $shared = [];
        foreach (self::VISUAL_SHARED_KEYS as $k) {
            $shared[$k] = $v[$k] ?? '';
        }

        $i18n = isset($v['i18n']) && is_array($v['i18n']) ? $v['i18n'] : [];

        if ($i18n === []) {
            $textual = $v;
            unset($textual['i18n']);
            foreach (self::VISUAL_SHARED_KEYS as $k) {
                unset($textual[$k]);
            }

            return array_merge($shared, $textual);
        }

        $block = $i18n[$locale] ?? $i18n[$def] ?? [];
        if ($block === [] && $i18n !== []) {
            $first = reset($i18n);
            $block = is_array($first) ? $first : [];
        }

        return array_merge($shared, is_array($block) ? $block : []);
    }

    /**
     * @return array<string, mixed>
     */
    public static function visualContentForAdminForm(?self $page): array
    {
        if (! $page) {
            return ['i18n' => ['en' => []]];
        }

        $v = is_array($page->visual_content) ? $page->visual_content : [];
        if (! empty($v['i18n']) && is_array($v['i18n'])) {
            return $v;
        }

        $textual = $v;
        unset($textual['i18n']);
        foreach (self::VISUAL_SHARED_KEYS as $k) {
            unset($textual[$k]);
        }

        $out = $v;
        $out['i18n'] = ['en' => $textual];

        return $out;
    }

    /**
     * Move legacy root-level copy into i18n.en and drop duplicate keys from root.
     *
     * @param  array<string, mixed>  $visual
     * @return array<string, mixed>
     */
    public static function ensureStructuredVisualContent(array $visual): array
    {
        if (! empty($visual['i18n']) && is_array($visual['i18n'])) {
            return $visual;
        }

        $textual = [];
        foreach ($visual as $k => $val) {
            if ($k === 'i18n') {
                continue;
            }
            if (in_array($k, self::VISUAL_SHARED_KEYS, true)) {
                continue;
            }
            $textual[$k] = $val;
        }

        foreach (array_keys($textual) as $k) {
            unset($visual[$k]);
        }
        $visual['i18n'] = ['en' => $textual];

        return $visual;
    }
}
