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

    public function leads(): HasMany
    {
        return $this->hasMany(LandingPageLead::class);
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

    /**
     * Sample agenda rows for the Canton Fair visual template (table: slot | activity | detail).
     * Used for new-page form defaults and optional seeders.
     *
     * @return list<array{slot: string, activity: string, detail: string}>
     */
    public static function defaultDemoAgendaItems(): array
    {
        return [
            ['slot' => 'Day 1 · 06:00', 'activity' => 'Meet at Phnom Penh International Airport', 'detail' => ''],
            ['slot' => 'Day 1 · 08:20', 'activity' => 'Flight departure', 'detail' => ''],
            ['slot' => 'Day 1 · 12:15', 'activity' => 'Arrive at Guangzhou International Airport', 'detail' => 'Lunch, then transfer to hotel'],
            ['slot' => 'Day 1 · Evening', 'activity' => 'City walk (Canton Tower)', 'detail' => ''],
            ['slot' => 'Day 1 · 21:00', 'activity' => 'Return to hotel', 'detail' => ''],
            ['slot' => 'Day 2 · 7:30–8:30', 'activity' => 'Breakfast at hotel', 'detail' => ''],
            ['slot' => 'Day 2 · 8:30', 'activity' => 'Depart hotel to Canton Fair venue', 'detail' => ''],
            ['slot' => 'Day 2 · 10:00–17:00', 'activity' => 'Canton Fair program', 'detail' => ''],
            ['slot' => 'Day 2 · Evening', 'activity' => 'City walk (Beijing Road)', 'detail' => ''],
            ['slot' => 'Day 2 · 21:00', 'activity' => 'Return to hotel', 'detail' => ''],
        ];
    }

    /**
     * Multiline agenda text for admin create form defaults (one locale per tab).
     */
    public static function defaultDemoAgendaItemsText(string $locale = 'en'): string
    {
        $loc = strtolower(trim($locale));

        if ($loc === 'km') {
            return <<<'TXT'
Day 1 · 06:00|ជួបជុំគ្នានៅ ព្រលានយន្តហោះអន្តរជាតិតាខ្មៅ|
Day 1 · 08:20|យន្តហោះចេញដំណើរ|
Day 1 · 12:15|មកដល់ព្រលានយន្តហោះអន្តរជាតិ Guangzhou|ញាំបាយថ្ងៃត្រង់ រួចចេញដំណើរមកសណ្ឋាគារ
Day 1 · ល្ងាច-យប់|ដើរកំសាន្តនៅក្នុងទីក្រុង (Canton Tower)|
Day 1 · 21:00|ត្រលប់មកសណ្ឋាគារវិញ|
Day 2 · 7:30-8:30|ញាំអាហារពេលព្រឹកនៅសណ្ឋាគារ|
Day 2 · 8:30|ចេញពីសណ្ឋាគារមកទីតាំងពិព័រណ៍ Canton Fair|
Day 2 · 10:00-17:00|ចូលរួមកម្មវិធីពិព័រណ៍ Canton Fair|
Day 2 · ល្ងាច-យប់|ដើរកំសាន្តនៅក្នុងទីក្រុង (Beijing Leu)|
Day 2 · 21:00|ត្រលប់មកសណ្ឋាគារវិញ|
TXT;
        }

        if ($loc === 'zh') {
            return <<<'TXT'
第一天 · 06:00|金边国际机场集合|
第一天 · 08:20|航班起飞|
第一天 · 12:15|抵达广州国际机场|午餐后前往酒店
第一天 · 傍晚至晚上|市区观光（广州塔）|
第一天 · 21:00|返回酒店|
第二天 · 7:30-8:30|酒店早餐|
第二天 · 8:30|离开酒店前往广交会展馆|
第二天 · 10:00-17:00|参加广交会活动|
第二天 · 傍晚至晚上|市区观光（北京路）|
第二天 · 21:00|返回酒店|
TXT;
        }

        return collect(self::defaultDemoAgendaItems())
            ->map(fn (array $row) => $row['slot'].'|'.$row['activity'].'|'.$row['detail'])
            ->implode("\n");
    }

    /**
     * Parse admin textarea format into agenda rows (slot|activity|detail per line).
     *
     * @return list<array{slot: string, activity: string, detail: string}>
     */
    public static function parseAgendaItemsFromText(string $multiline): array
    {
        $out = [];
        foreach (preg_split('/\r\n|\r|\n/', trim($multiline)) as $line) {
            if ($line === '') {
                continue;
            }
            $p = explode('|', $line, 3);
            $out[] = [
                'slot' => trim($p[0] ?? ''),
                'activity' => trim($p[1] ?? ''),
                'detail' => trim($p[2] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * Default agenda rows for a locale (matches Section 5 public defaults).
     *
     * @return list<array{slot: string, activity: string, detail: string}>
     */
    public static function defaultDemoAgendaItemsForLocale(string $locale): array
    {
        $loc = strtolower(trim($locale));

        if ($loc === 'en') {
            return self::defaultDemoAgendaItems();
        }

        return self::parseAgendaItemsFromText(self::defaultDemoAgendaItemsText($loc));
    }

    /**
     * Use saved agenda rows when present; otherwise the canonical Day 1–2 demo schedule for the locale.
     *
     * @param  array<int, mixed>  $rows
     * @return list<array{slot: string, activity: string, detail: string}>
     */
    public static function resolveAgendaItemsForDisplay(array $rows, string $locale): array
    {
        $filtered = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $slot = trim((string) ($row['slot'] ?? ''));
            $act = trim((string) ($row['activity'] ?? ''));
            $det = trim((string) ($row['detail'] ?? ''));
            if ($slot !== '' || $act !== '' || $det !== '') {
                $filtered[] = ['slot' => $slot, 'activity' => $act, 'detail' => $det];
            }
        }

        if ($filtered !== []) {
            return $filtered;
        }

        return self::defaultDemoAgendaItemsForLocale($locale);
    }
}
