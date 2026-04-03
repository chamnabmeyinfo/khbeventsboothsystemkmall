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
        'hero_background_images',
        'hero_background_video',
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
    /**
     * Accepts https? URLs or a site-relative path under public images (uploaded MP4/WebM).
     */
    public static function isValidHeroBackgroundVideoReference(string $v): bool
    {
        $v = trim($v);
        if ($v === '') {
            return true;
        }
        if (preg_match('#^https?://#i', $v)) {
            return filter_var($v, FILTER_VALIDATE_URL) !== false;
        }
        if (str_contains($v, '..')) {
            return false;
        }

        return (bool) preg_match('#^images/landing-pages/[\w\-]+/[\w\-.]+\.(mp4|webm)$#i', $v);
    }

    /**
     * Extract YouTube video id from watch, youtu.be, embed, or shorts URLs (for hero iframe embed).
     * The HTML5 video element cannot play YouTube page URLs; use iframe embed for those.
     */
    public static function parseYouTubeVideoId(string $url): ?string
    {
        $url = trim($url);
        if ($url === '' || ! preg_match('#^https?://#i', $url)) {
            return null;
        }

        if (preg_match('#youtu\.be/([a-zA-Z0-9_-]{11})#i', $url, $m)) {
            return $m[1];
        }
        if (preg_match('#[?&]v=([a-zA-Z0-9_-]{11})#', $url, $m)) {
            return $m[1];
        }
        if (preg_match('#/embed/([a-zA-Z0-9_-]{11})#i', $url, $m)) {
            return $m[1];
        }
        if (preg_match('#/shorts/([a-zA-Z0-9_-]{11})#i', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Ordered hero slideshow paths (shared visual). Falls back to single hero_background_image.
     *
     * @param  array<string, mixed>  $visual
     * @return list<string>
     */
    public static function heroBackgroundImagePathsForDisplay(array $visual): array
    {
        $raw = $visual['hero_background_images'] ?? null;
        $paths = [];
        if (is_array($raw)) {
            foreach ($raw as $p) {
                $p = trim((string) $p);
                if ($p !== '') {
                    $paths[] = $p;
                }
            }
        }
        $paths = array_values(array_unique($paths));
        if ($paths === [] && ! empty($visual['hero_background_image'])) {
            $paths = [(string) $visual['hero_background_image']];
        }

        return $paths;
    }

    /**
     * Build a single 45s hero rotation timeline: each image once (6s each), then optional video (10s),
     * then cycle images (6s each) until 45s total (last image segment may be shorter).
     *
     * @param  list<string>  $imagePaths  Relative paths under public (e.g. images/landing-pages/...)
     * @return list<array<string, mixed>>
     */
    public static function buildHeroRotationSegments(
        array $imagePaths,
        ?string $youtubeEmbedUrl,
        ?string $html5VideoSrc,
        string $html5Mime = 'video/mp4'
    ): array {
        $cycleMs = 45000;
        $imgMs = 6000;
        $vidMs = 10000;
        $segments = [];
        $paths = [];
        foreach ($imagePaths as $p) {
            $p = trim((string) $p);
            if ($p !== '') {
                $paths[] = $p;
            }
        }
        $paths = array_values(array_unique($paths));
        if ($paths === []) {
            return [];
        }
        $imageUrls = array_map(static fn (string $p) => asset($p), $paths);
        $hasVideo = ($youtubeEmbedUrl !== null && trim($youtubeEmbedUrl) !== '')
            || ($html5VideoSrc !== null && trim((string) $html5VideoSrc) !== '');
        $elapsed = 0;

        $addImage = function (string $url) use (&$segments, &$elapsed, $cycleMs, $imgMs): void {
            if ($elapsed >= $cycleMs) {
                return;
            }
            $dur = min($imgMs, $cycleMs - $elapsed);
            if ($dur > 0) {
                $segments[] = [
                    'type' => 'image',
                    'url' => $url,
                    'ms' => $dur,
                ];
                $elapsed += $dur;
            }
        };

        foreach ($imageUrls as $url) {
            $addImage($url);
        }

        if ($hasVideo && $elapsed < $cycleMs) {
            $dur = min($vidMs, $cycleMs - $elapsed);
            if ($dur > 0) {
                if ($youtubeEmbedUrl !== null && trim($youtubeEmbedUrl) !== '') {
                    $segments[] = [
                        'type' => 'video_youtube',
                        'ms' => $dur,
                        'embedUrl' => $youtubeEmbedUrl,
                    ];
                } else {
                    $segments[] = [
                        'type' => 'video_html5',
                        'ms' => $dur,
                        'src' => (string) $html5VideoSrc,
                        'mime' => $html5Mime,
                    ];
                }
                $elapsed += $dur;
            }
        }

        $n = count($imageUrls);
        $imgIdx = 0;
        while ($elapsed < $cycleMs && $n > 0) {
            $addImage($imageUrls[$imgIdx % $n]);
            $imgIdx++;
        }

        return $segments;
    }

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

    /**
     * Label for agenda rows that do not start with a recognized day prefix (slot column).
     */
    public static function agendaFallbackGroupLabel(string $locale): string
    {
        $loc = strtolower(trim($locale));

        return match ($loc) {
            'zh' => '其他',
            'km' => 'កាលវិភាគ',
            default => 'Schedule',
        };
    }

    /**
     * Detect "Day 1 · …", "Day1", "Day 2-…", "第一天 · …", "第2天 …" in the slot column for tabbed itinerary.
     * Accepts flexible spacing and common separators so admin edits still map to Day 1 / Day 2 tabs.
     *
     * @return array{day: int, label: string, slot_display: string}|null
     */
    public static function parseAgendaSlotDayPrefix(string $slot): ?array
    {
        $s = trim($slot);
        if ($s === '') {
            return null;
        }

        // English: Day 1 … / Day1… / DAY 2-… (anything after the day number becomes the time/slot cell)
        if (preg_match('/^day\s*(\d+)\s*(.*)$/iu', $s, $m)) {
            $d = (int) $m[1];
            if ($d < 1) {
                return null;
            }
            $rest = trim((string) ($m[2] ?? ''));
            // Strip a leading punctuation dash or middle dot so "Day 1 · 06:00" and "Day 1-06:00" show cleanly
            $rest = preg_replace('/^[·•,，\-\–\—:：]\s*/u', '', $rest) ?? $rest;
            $rest = trim($rest);

            return [
                'day' => $d,
                'label' => 'Day '.$d,
                'slot_display' => $rest !== '' ? $rest : '—',
            ];
        }

        // Chinese digits: 第1天 … / 第2天…
        if (preg_match('/^第(\d+)天\s*(.*)$/u', $s, $m)) {
            $d = (int) $m[1];
            if ($d < 1) {
                return null;
            }
            $rest = trim((string) ($m[2] ?? ''));
            $rest = preg_replace('/^[·•,，\-\–\—:：]\s*/u', '', $rest) ?? $rest;
            $rest = trim($rest);

            return [
                'day' => $d,
                'label' => '第'.$m[1].'天',
                'slot_display' => $rest !== '' ? $rest : '—',
            ];
        }

        $hanToInt = [
            '一' => 1, '二' => 2, '三' => 3, '四' => 4, '五' => 5,
            '六' => 6, '七' => 7, '八' => 8, '九' => 9, '十' => 10,
        ];

        // Chinese han: 第一天 …
        if (preg_match('/^(第[一二三四五六七八九十]+天)\s*(.*)$/u', $s, $m)) {
            $han = preg_replace('/^第|天$/u', '', $m[1]);
            $d = $hanToInt[$han] ?? null;
            if ($d === null || $d < 1) {
                return null;
            }
            $rest = trim((string) ($m[2] ?? ''));
            $rest = preg_replace('/^[·•,，\-\–\—:：]\s*/u', '', $rest) ?? $rest;
            $rest = trim($rest);

            return [
                'day' => $d,
                'label' => $m[1],
                'slot_display' => $rest !== '' ? $rest : '—',
            ];
        }

        return null;
    }

    /**
     * Group agenda rows by day for public tab UI. Uses slot prefixes (Day 1, 第一天, …).
     *
     * @param  list<array{slot: string, activity: string, detail: string}>  $rows
     * @return array{use_tabs: bool, groups: list<array{day: int, label: string, rows: list<array{slot: string, activity: string, detail: string}>}>}
     */
    public static function groupAgendaItemsByDayForDisplay(array $rows, string $locale): array
    {
        $buckets = [];

        foreach ($rows as $row) {
            $slot = trim((string) ($row['slot'] ?? ''));
            $act = trim((string) ($row['activity'] ?? ''));
            $det = trim((string) ($row['detail'] ?? ''));
            if ($slot === '' && $act === '' && $det === '') {
                continue;
            }

            $parsed = self::parseAgendaSlotDayPrefix($slot);
            $fromActivity = false;
            if ($parsed === null && $act !== '') {
                $parsed = self::parseAgendaSlotDayPrefix($act);
                $fromActivity = $parsed !== null;
            }

            if ($parsed === null) {
                $day = 0;
                if (! isset($buckets[$day])) {
                    $buckets[$day] = ['day' => $day, 'label' => self::agendaFallbackGroupLabel($locale), 'rows' => []];
                }
                $buckets[$day]['rows'][] = ['slot' => $slot, 'activity' => $act, 'detail' => $det];
            } else {
                $day = $parsed['day'];
                if (! isset($buckets[$day])) {
                    $buckets[$day] = ['day' => $day, 'label' => $parsed['label'], 'rows' => []];
                }
                if ($fromActivity) {
                    // Day marker was in the activity column; keep time/slot in first column, remainder as activity
                    $buckets[$day]['rows'][] = [
                        'slot' => $slot !== '' ? $slot : '—',
                        'activity' => $parsed['slot_display'],
                        'detail' => $det,
                    ];
                } else {
                    $buckets[$day]['rows'][] = [
                        'slot' => $parsed['slot_display'],
                        'activity' => $act,
                        'detail' => $det,
                    ];
                }
            }
        }

        $groups = array_values($buckets);
        usort($groups, function ($a, $b) {
            if ($a['day'] === 0) {
                return 1;
            }
            if ($b['day'] === 0) {
                return -1;
            }

            return $a['day'] <=> $b['day'];
        });

        $useTabs = count($groups) >= 2;

        return ['use_tabs' => $useTabs, 'groups' => $groups];
    }

    /**
     * Rich trip phases: each phase has intro + subsections (sub-categories with detail).
     *
     * @return list<array{label: string, date: string, status: string, seats_left: string, intro: string, subsections: list<array{title: string, detail: string}>}>
     */
    public static function defaultTripPhases(): array
    {
        return [
            [
                'label' => 'Phase I',
                'date' => '15 – 19 April 2026',
                'status' => 'Available',
                'seats_left' => '18',
                'intro' => 'Opening phase: electronics, lighting, machinery, hardware, and tools—ideal for sourcing and factory connections.',
                'subsections' => [
                    ['title' => 'Electronics & lighting', 'detail' => 'Consumer electronics, LED lighting, smart devices, and electrical components across main halls.'],
                    ['title' => 'Machinery, hardware & tools', 'detail' => 'Industrial machinery, power tools, hardware, and building materials—meet manufacturers directly.'],
                ],
            ],
            [
                'label' => 'Phase II',
                'date' => '23 – 27 April 2026',
                'status' => 'Available',
                'seats_left' => '22',
                'intro' => 'Consumer goods, gifts, home décor, and houseware—strong for retail and wholesale buyers.',
                'subsections' => [
                    ['title' => 'Home & gifts', 'detail' => 'Houseware, décor, seasonal gifts, and daily-use products for retail assortments.'],
                    ['title' => 'Toys, recreation & personal care', 'detail' => 'Toys, sports, outdoor items, and health & beauty suppliers.'],
                ],
            ],
            [
                'label' => 'Phase III',
                'date' => '1 – 5 May 2026',
                'status' => 'Available',
                'seats_left' => '25',
                'intro' => 'Textiles, garments, footwear, office supplies, medical devices, and chemicals—closing phase for fashion and B2B essentials.',
                'subsections' => [
                    ['title' => 'Textiles, apparel & footwear', 'detail' => 'Fabrics, fashion, bags, and footwear—sampling and MOQ discussions with exhibitors.'],
                    ['title' => 'Office, medical & chemical', 'detail' => 'Stationery, medical equipment, and chemical/industrial materials for specialized buyers.'],
                ],
            ],
        ];
    }

    /**
     * Pretty JSON for admin create / prefill.
     */
    public static function defaultTripPhasesJson(): string
    {
        $json = json_encode(self::defaultTripPhases(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $json !== false ? $json : '[]';
    }

    /**
     * Flat rows for booking dropdown & legacy trip_dates (phase key matches existing templates).
     *
     * @param  list<array{label?: string, date?: string, status?: string, seats_left?: string}>  $phases
     * @return list<array{phase: string, date: string, status: string, seats_left: string}>
     */
    public static function tripPhasesToFlatRows(array $phases): array
    {
        $out = [];
        foreach ($phases as $p) {
            if (! is_array($p)) {
                continue;
            }
            $label = trim((string) ($p['label'] ?? $p['phase'] ?? ''));
            $out[] = [
                'phase' => $label,
                'date' => trim((string) ($p['date'] ?? '')),
                'status' => trim((string) ($p['status'] ?? '')),
                'seats_left' => trim((string) ($p['seats_left'] ?? '')),
            ];
        }

        return $out;
    }

    /**
     * @param  list<array{phase?: string, date?: string, status?: string, seats_left?: string}>  $rows
     * @return list<array{label: string, date: string, status: string, seats_left: string, intro: string, subsections: list<array{title: string, detail: string}>}>
     */
    public static function tripPhasesFromFlatTripDates(array $rows): array
    {
        $out = [];
        foreach ($rows as $r) {
            if (! is_array($r)) {
                continue;
            }
            $out[] = [
                'label' => trim((string) ($r['phase'] ?? '')),
                'date' => trim((string) ($r['date'] ?? '')),
                'status' => trim((string) ($r['status'] ?? '')),
                'seats_left' => trim((string) ($r['seats_left'] ?? '')),
                'intro' => '',
                'subsections' => [],
            ];
        }

        return $out;
    }

    /**
     * Allow only site-relative paths under public images/landing-pages/ (same rule as hero gallery).
     */
    public static function sanitizeTripPhaseFeatureImagePath(string $path): string
    {
        $p = trim($path);
        if ($p === '' || str_contains($p, '..')) {
            return '';
        }
        if (! preg_match('#^images/landing-pages/[\w\-]+/[\w\-.]+\.(jpg|jpeg|png|gif|webp)$#i', $p)) {
            return '';
        }

        return $p;
    }

    /**
     * @param  array<int, mixed>  $input
     * @return list<array{label: string, date: string, status: string, seats_left: string, intro: string, subsections: list<array{title: string, detail: string}>, feature_image?: string}>
     */
    public static function sanitizeTripPhases(array $input): array
    {
        $out = [];
        foreach ($input as $ph) {
            if (! is_array($ph)) {
                continue;
            }
            $subs = [];
            $subsectionsRaw = is_array($ph['subsections'] ?? null) ? $ph['subsections'] : [];
            foreach ($subsectionsRaw as $s) {
                if (! is_array($s)) {
                    continue;
                }
                $t = trim((string) ($s['title'] ?? ''));
                $d = trim((string) ($s['detail'] ?? ''));
                if ($t === '' && $d === '') {
                    continue;
                }
                $subs[] = ['title' => $t, 'detail' => $d];
            }
            $fi = self::sanitizeTripPhaseFeatureImagePath((string) ($ph['feature_image'] ?? ''));
            $row = [
                'label' => trim((string) ($ph['label'] ?? $ph['phase'] ?? '')),
                'date' => trim((string) ($ph['date'] ?? '')),
                'status' => trim((string) ($ph['status'] ?? '')),
                'seats_left' => trim((string) ($ph['seats_left'] ?? '')),
                'intro' => trim((string) ($ph['intro'] ?? '')),
                'subsections' => $subs,
            ];
            if ($fi !== '') {
                $row['feature_image'] = $fi;
            }
            $out[] = $row;
        }

        return $out;
    }

    /**
     * Merge trip_phases when present; else flat trip_dates; else built-in Phase I–III demo.
     *
     * @param  array<string, mixed>  $visual
     * @return list<array{label: string, date: string, status: string, seats_left: string, intro: string, subsections: list<array{title: string, detail: string}>, feature_image?: string}>
     */
    public static function resolveTripPhasesForDisplay(array $visual): array
    {
        $tp = $visual['trip_phases'] ?? null;
        if (is_array($tp) && $tp !== []) {
            $san = self::sanitizeTripPhases($tp);
            $nonEmpty = array_filter($san, static fn (array $p) => ($p['label'] ?? '') !== '' || ($p['date'] ?? '') !== '');
            if ($nonEmpty !== []) {
                return array_values($nonEmpty);
            }
        }

        $flat = self::resolveTripDatesForDisplay(is_array($visual['trip_dates'] ?? null) ? $visual['trip_dates'] : []);

        $defaultFlat = self::defaultTripDateRows();
        if (json_encode($flat) === json_encode($defaultFlat)) {
            return self::defaultTripPhases();
        }

        return self::tripPhasesFromFlatTripDates($flat);
    }

    /**
     * Canton Fair trip rows (Phase I / II / III) for Section 4 when none saved — derived from defaultTripPhases().
     *
     * @return list<array{phase: string, date: string, status: string, seats_left: string}>
     */
    public static function defaultTripDateRows(): array
    {
        return self::tripPhasesToFlatRows(self::defaultTripPhases());
    }

    /**
     * Admin textarea default (phase|date|status|seats_left per line).
     */
    public static function defaultTripDatesText(): string
    {
        return collect(self::defaultTripDateRows())
            ->map(fn (array $r) => $r['phase'].'|'.$r['date'].'|'.$r['status'].'|'.$r['seats_left'])
            ->implode("\n");
    }

    /**
     * Parse trip_dates_text: 4 columns (phase|date|status|seats_left) or legacy 3 (date|status|seats_left).
     *
     * @param  array<int, mixed>  $fallback
     * @return list<array{phase: string, date: string, status: string, seats_left: string}>
     */
    public static function parseTripDateRowsFromText(?string $raw, array $fallback = []): array
    {
        $normalize = static function (array $rows): array {
            $out = [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $out[] = [
                    'phase' => trim((string) ($row['phase'] ?? '')),
                    'date' => trim((string) ($row['date'] ?? '')),
                    'status' => trim((string) ($row['status'] ?? '')),
                    'seats_left' => trim((string) ($row['seats_left'] ?? '')),
                ];
            }

            return $out;
        };

        if ($raw === null) {
            return $normalize($fallback);
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $items = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            $parts = array_map(static fn ($v) => trim(strip_tags((string) $v)), explode('|', $line));
            if (count($parts) >= 4) {
                $items[] = [
                    'phase' => $parts[0] ?? '',
                    'date' => $parts[1] ?? '',
                    'status' => $parts[2] ?? '',
                    'seats_left' => $parts[3] ?? '',
                ];
            } elseif (count($parts) === 3) {
                $items[] = [
                    'phase' => '',
                    'date' => $parts[0] ?? '',
                    'status' => $parts[1] ?? '',
                    'seats_left' => $parts[2] ?? '',
                ];
            }
        }

        return $items;
    }

    /**
     * Use saved trip rows when present; otherwise Phase I–III defaults.
     *
     * @param  array<int, mixed>  $rows
     * @return list<array{phase: string, date: string, status: string, seats_left: string}>
     */
    public static function resolveTripDatesForDisplay(array $rows): array
    {
        $filtered = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $phase = trim((string) ($row['phase'] ?? ''));
            $date = trim((string) ($row['date'] ?? ''));
            $status = trim((string) ($row['status'] ?? ''));
            $seats = trim((string) ($row['seats_left'] ?? ''));
            if ($phase !== '' || $date !== '' || $status !== '' || $seats !== '') {
                $filtered[] = [
                    'phase' => $phase,
                    'date' => $date,
                    'status' => $status,
                    'seats_left' => $seats,
                ];
            }
        }

        if ($filtered !== []) {
            return $filtered;
        }

        return self::defaultTripDateRows();
    }

    /**
     * Default group discount tiers (below package section). Editable via promotion_discounts_json.
     *
     * @return array{base_price_text: string, intro_text: string, tiers: list<array{participants: int, off_each: int, label: string}>}
     */
    public static function defaultPromotionDiscountsData(): array
    {
        return [
            'base_price_text' => '$499/person',
            'intro_text' => '',
            'tiers' => [
                ['participants' => 3, 'off_each' => 10, 'label' => ''],
                ['participants' => 5, 'off_each' => 15, 'label' => ''],
                ['participants' => 10, 'off_each' => 25, 'label' => ''],
            ],
        ];
    }

    public static function defaultPromotionDiscountsJson(): string
    {
        $json = json_encode(self::defaultPromotionDiscountsData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $json !== false ? $json : '{}';
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return list<array{participants: int, off_each: int, label: string}>
     */
    public static function sanitizePromotionTiers(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $n = (int) ($row['participants'] ?? $row['min_participants'] ?? 0);
            $off = (int) ($row['off_each'] ?? $row['discount_off_each'] ?? $row['off'] ?? 0);
            $label = trim((string) ($row['label'] ?? ''));
            if ($n < 1 || $off < 1) {
                continue;
            }
            $out[] = [
                'participants' => $n,
                'off_each' => $off,
                'label' => $label,
            ];
        }
        usort($out, static fn (array $a, array $b) => $a['participants'] <=> $b['participants']);

        return $out;
    }

    /**
     * @param  array<string, mixed>|list<mixed>  $input
     * @return array{base_price_text: string, intro_text: string, tiers: list<array{participants: int, off_each: int, label: string}>}
     */
    public static function sanitizePromotionDiscounts(array $input): array
    {
        $defaults = self::defaultPromotionDiscountsData();
        if ($input === []) {
            return $defaults;
        }
        if (array_is_list($input)) {
            $tiers = self::sanitizePromotionTiers($input);

            return array_merge($defaults, ['tiers' => $tiers !== [] ? $tiers : $defaults['tiers']]);
        }
        $base = trim((string) ($input['base_price_text'] ?? ''));
        $intro = trim((string) ($input['intro_text'] ?? ''));
        $tiersRaw = $input['tiers'] ?? [];
        $tiers = is_array($tiersRaw) ? self::sanitizePromotionTiers($tiersRaw) : [];
        $out = $defaults;
        if ($base !== '') {
            $out['base_price_text'] = $base;
        }
        if ($intro !== '') {
            $out['intro_text'] = $intro;
        }
        if ($tiers !== []) {
            $out['tiers'] = $tiers;
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $visual
     * @return array{base_price_text: string, intro_text: string, tiers: list<array{participants: int, off_each: int, label: string}>}
     */
    public static function resolvePromotionDiscountsForDisplay(array $visual): array
    {
        $raw = $visual['promotion_discounts'] ?? null;
        if (is_array($raw) && $raw !== []) {
            return self::sanitizePromotionDiscounts($raw);
        }

        return self::defaultPromotionDiscountsData();
    }
}
