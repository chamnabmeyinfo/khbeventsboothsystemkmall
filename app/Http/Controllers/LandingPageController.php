<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageLead;
use App\Models\LandingPageSectionTemplate;
use App\Models\LandingPageEvent;
use App\Services\LandingTextTranslationService;
use App\Services\LandingTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LandingPageController extends Controller
{
    public function index(Request $request)
    {
        $query = LandingPage::query()->withCount('leads');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('industry', 'like', "%{$search}%")
                    ->orWhere('headline', 'like', "%{$search}%");
            });
        }

        if ($request->filled('industry')) {
            $query->where('industry', $request->input('industry'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'published') {
                $query->where('is_published', true);
            } elseif ($request->input('status') === 'draft') {
                $query->where('is_published', false);
            }
        }

        $landingPages = $query
            ->orderByDesc('is_active')
            ->orderByDesc('is_published')
            ->orderBy('priority')
            ->orderByDesc('updated_at')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_pages' => LandingPage::count(),
            'published_pages' => LandingPage::where('is_published', true)->count(),
            'active_page' => LandingPage::where('is_active', true)->where('is_published', true)->count(),
            'total_events' => LandingPageEvent::count(),
            'total_leads' => LandingPageLead::count(),
        ];

        $industries = LandingPage::query()
            ->whereNotNull('industry')
            ->where('industry', '!=', '')
            ->distinct()
            ->orderBy('industry')
            ->pluck('industry');

        return view('landing-pages.index', compact('landingPages', 'stats', 'industries'));
    }

    public function create()
    {
        return view('landing-pages.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateLandingPage($request);
        $validated['slug'] = Str::slug($validated['slug']);
        $validated = $this->forceMarketingVisualMode($validated);
        $validated = $this->applyContentSafety($validated);
        $validated = $this->prepareVisualBuilderData($request, $validated);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        if ($validated['is_active']) {
            $this->deactivateOtherPages();
        }

        $page = LandingPage::create($validated);

        return redirect()->route('landing-pages.edit', $page)
            ->with('success', 'Landing page created successfully.');
    }

    public function edit(LandingPage $landingPage)
    {
        $landingPage->loadCount('leads');

        return view('landing-pages.edit', compact('landingPage'));
    }

    /**
     * Machine-translate all visual fields from English into multiple target locales (admin).
     */
    public function translateFromEnglish(Request $request, LandingPage $landingPage)
    {
        set_time_limit(0);

        $allowedLocales = LandingPage::allowedLocaleCodes();
        $allowedFieldKeys = LandingTextTranslationService::visualI18nFieldKeys();

        $validated = $request->validate([
            'fields' => ['required', 'array'],
            'fields.*' => ['nullable', 'string', 'max:50000'],
            'target_locales' => ['required', 'array', 'min:1'],
            'target_locales.*' => ['required', 'string', Rule::in($allowedLocales)],
        ]);

        $fields = $validated['fields'];
        foreach (array_keys($fields) as $key) {
            if (! in_array($key, $allowedFieldKeys, true)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Invalid field key: '.$key,
                ], 422);
            }
        }

        $targets = array_values(array_unique($validated['target_locales']));
        foreach ($targets as $loc) {
            if ($loc === 'en') {
                return response()->json([
                    'ok' => false,
                    'message' => 'Remove English from target languages. English is the source.',
                ], 422);
            }
        }

        $service = app(LandingTextTranslationService::class);
        $locales = [];

        try {
            foreach ($targets as $target) {
                $locales[$target] = [];
                foreach ($allowedFieldKeys as $key) {
                    if (! array_key_exists($key, $fields)) {
                        continue;
                    }
                    $text = (string) ($fields[$key] ?? '');
                    $locales[$target][$key] = $service->translateVisualField(
                        $text,
                        $key,
                        'en',
                        $target
                    );
                }
            }
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => 'Translation is temporarily unavailable. Please try again with fewer languages or shorter text.',
            ], 503);
        }

        return response()->json(['ok' => true, 'locales' => $locales]);
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $validated = $this->validateLandingPage($request, $landingPage->id);
        $validated['slug'] = Str::slug($validated['slug']);
        $validated = $this->forceMarketingVisualMode($validated);
        $validated = $this->applyContentSafety($validated);
        $validated = $this->prepareVisualBuilderData($request, $validated, $landingPage);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = $validated['is_published'] && ! $landingPage->published_at ? now() : $landingPage->published_at;

        if (! $validated['is_published']) {
            $validated['published_at'] = null;
        }

        if ($validated['is_active']) {
            $this->deactivateOtherPages($landingPage->id);
        }

        $landingPage->update($validated);

        return redirect()->route('landing-pages.edit', $landingPage->fresh())
            ->with('success', 'Landing page updated successfully.');
    }

    /**
     * Merge a reusable section template’s i18n payload into this page (same parsing as full form save).
     */
    public function applySectionTemplate(Request $request, LandingPage $landingPage)
    {
        $validated = $request->validate([
            'landing_page_section_template_id' => ['required', 'integer', 'exists:landing_page_section_templates,id'],
            'layout_key' => ['required', 'string', 'max:64', Rule::in(LandingPage::SECTION_LAYOUT_KEYS)],
        ]);

        if (! $landingPage->use_visual_builder) {
            return back()->with('error', 'This page does not use the visual builder.');
        }

        $template = LandingPageSectionTemplate::query()->findOrFail((int) $validated['landing_page_section_template_id']);
        if ($template->layout_key !== $validated['layout_key']) {
            return back()->with('error', 'Template does not match the selected layout.');
        }

        $visual = is_array($landingPage->visual_content) ? $landingPage->visual_content : [];
        $visual = LandingPage::ensureStructuredVisualContent($visual);

        $content = is_array($template->content) ? $template->content : [];
        $i18nSlice = isset($content['i18n']) && is_array($content['i18n']) ? $content['i18n'] : [];

        $adminLocales = LandingPage::allowedLocaleCodes();
        foreach ($adminLocales as $loc) {
            if (! isset($i18nSlice[$loc]) || ! is_array($i18nSlice[$loc])) {
                continue;
            }
            $prev = is_array($visual['i18n'][$loc] ?? null) ? $visual['i18n'][$loc] : [];
            $visual['i18n'][$loc] = $this->buildLocaleVisualBlock($loc, $i18nSlice[$loc], $prev);
        }

        $landingPage->update(['visual_content' => $visual]);

        return back()->with('success', 'Applied template “'.$template->name.'”. Review each language tab; submit the main form if you have other unsaved changes.');
    }

    public function destroy(LandingPage $landingPage)
    {
        $landingPage->delete();

        return redirect()->route('landing-pages.index')
            ->with('success', 'Landing page deleted successfully.');
    }

    public function preview(LandingPage $landingPage)
    {
        return view('landing-pages.preview', compact('landingPage'));
    }

    /**
     * Admin: all marketing leads across landing pages (under /landing-pages/reporting).
     */
    public function reportingIndex(Request $request)
    {
        $query = LandingPageLead::query()
            ->with(['landingPage:id,name,slug'])
            ->orderByDesc('created_at');

        if ($request->filled('landing_page_id')) {
            $query->where('landing_page_id', (int) $request->input('landing_page_id'));
        }

        if ($request->filled('search')) {
            $searchLike = '%'.trim((string) $request->input('search')).'%';
            $query->where(function ($q) use ($searchLike) {
                $q->where('name', 'like', $searchLike)
                    ->orWhere('email', 'like', $searchLike)
                    ->orWhere('phone', 'like', $searchLike);
            });
        }

        $leads = $query->paginate(40)->withQueryString();

        $landingPageOptions = LandingPage::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('landing-pages.reporting-index', compact('leads', 'landingPageOptions'));
    }

    public function reportingCreate(Request $request)
    {
        $landingPageOptions = LandingPage::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $prefillLandingPageId = $request->query('landing_page_id');

        return view('landing-pages.reporting-form', [
            'lead' => null,
            'landingPageOptions' => $landingPageOptions,
            'mode' => 'create',
            'prefillLandingPageId' => $prefillLandingPageId,
        ]);
    }

    public function reportingStore(Request $request)
    {
        $validated = $this->validateReportingLeadRequest($request);

        LandingPageLead::create([
            'landing_page_id' => $validated['landing_page_id'],
            'landing_tracking_event_id' => null,
            'visitor_id' => null,
            'session_uuid' => null,
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'preferred_trip_date' => $validated['preferred_trip_date'] ?? null,
            'locale' => $validated['locale'] ?? null,
            'source' => $validated['source'] ?? null,
            'meta' => $validated['meta'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 65000, ''),
            'referrer_url' => null,
        ]);

        return redirect()->route('landing-pages.reporting.index')
            ->with('success', 'Lead created.');
    }

    public function reportingShow(LandingPageLead $lead)
    {
        $lead->load(['landingPage:id,name,slug', 'trackingEvent', 'visitor']);

        return view('landing-pages.reporting-show', compact('lead'));
    }

    public function reportingEdit(LandingPageLead $lead)
    {
        $landingPageOptions = LandingPage::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('landing-pages.reporting-form', [
            'lead' => $lead,
            'landingPageOptions' => $landingPageOptions,
            'mode' => 'edit',
            'prefillLandingPageId' => null,
        ]);
    }

    public function reportingUpdate(Request $request, LandingPageLead $lead)
    {
        $validated = $this->validateReportingLeadRequest($request);

        $lead->update([
            'landing_page_id' => $validated['landing_page_id'],
            'name' => $validated['name'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'preferred_trip_date' => $validated['preferred_trip_date'] ?? null,
            'locale' => $validated['locale'] ?? null,
            'source' => $validated['source'] ?? null,
            'meta' => $validated['meta'] ?? null,
        ]);

        return redirect()->route('landing-pages.reporting.show', $lead)
            ->with('success', 'Lead updated.');
    }

    public function reportingDestroy(LandingPageLead $lead)
    {
        $lead->delete();

        return redirect()->back(302, [], route('landing-pages.reporting.index'))
            ->with('success', 'Lead deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateReportingLeadRequest(Request $request): array
    {
        $metaRaw = trim((string) $request->input('meta', ''));

        $rules = [
            'landing_page_id' => 'required|exists:landing_pages,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'preferred_trip_date' => 'nullable|string|max:500',
            'locale' => 'nullable|string|max:32',
            'source' => 'nullable|string|max:255',
        ];

        if ($metaRaw !== '') {
            $rules['meta'] = 'json';
        }

        $validated = $request->validate($rules);
        $validated['meta'] = $metaRaw !== '' ? json_decode($metaRaw, true) : null;

        return $validated;
    }

    /**
     * Admin: visitor & engagement analytics across all landing pages (under /landing-pages/analytics).
     */
    public function analyticsIndex(LandingTrackingService $tracking)
    {
        $pages = LandingPage::query()->orderBy('name')->get(['id', 'name', 'slug', 'is_published']);
        $rows = [];
        foreach ($pages as $page) {
            $rows[] = [
                'page' => $page,
                'summary' => $tracking->summary($page),
            ];
        }

        $landingPageOptions = LandingPage::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('landing-pages.analytics-index', compact('rows', 'landingPageOptions'));
    }

    /**
     * Admin: delete tracking events (not leads) for a chosen day, month, or year — optionally one landing page or all.
     */
    public function analyticsClear(Request $request, LandingTrackingService $tracking)
    {
        if (! $request->filled('landing_page_id')) {
            $request->merge(['landing_page_id' => null]);
        }

        $validated = $request->validate([
            'scope' => 'required|in:day,month,year',
            'day' => 'required_if:scope,day|nullable|date_format:Y-m-d',
            'month' => 'required_if:scope,month|nullable|date_format:Y-m',
            'year' => 'required_if:scope,year|nullable|integer|min:2000|max:2100',
            'landing_page_id' => 'nullable|integer|exists:landing_pages,id',
            'confirm' => 'required|accepted',
        ]);

        $value = match ($validated['scope']) {
            'day' => $validated['day'],
            'month' => $validated['month'],
            'year' => (string) $validated['year'],
        };

        $pageId = $validated['landing_page_id'] ?? null;
        $pageId = $pageId !== null ? (int) $pageId : null;

        $deleted = $tracking->purgeTrackingEvents($pageId, $validated['scope'], $value);

        return redirect()->route('landing-pages.analytics.index')
            ->with('success', "Deleted {$deleted} tracking event(s) for the selected period. Marketing lead records were not removed.");
    }

    /**
     * Admin: marketing leads / form submissions for this landing page (not floor-plan booth bookings).
     */
    public function leads(LandingPage $landingPage)
    {
        $leads = LandingPageLead::query()
            ->where('landing_page_id', $landingPage->id)
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('landing-pages.leads', compact('landingPage', 'leads'));
    }

    public function updateVisualInline(Request $request, LandingPage $landingPage)
    {
        if (! $landingPage->use_visual_builder) {
            return response()->json([
                'ok' => false,
                'message' => 'Inline visual editing is not enabled for this landing page.',
            ], 422);
        }

        $validated = $request->validate([
            'fields' => 'required|array',
            'locale' => 'nullable|string|max:12',
        ]);

        $allowedText = [
            'hero_title' => 255,
            'hero_subtitle' => 2000,
            'hero_cta_text' => 120,
            'about_title' => 255,
            'about_text_en' => 4000,
            'about_text_kh' => 4000,
            'package_title' => 255,
            'package_price' => 120,
            'promotion_section_title' => 255,
            'booking_title' => 255,
            'faq_title' => 255,
            'terms_title' => 255,
            'terms_text' => 8000,
            'agenda_title' => 255,
            'agenda_hdr_slot' => 120,
            'agenda_hdr_activity' => 120,
            'agenda_hdr_detail' => 120,
            'agenda_items_text' => 12000,
            'trip_section_title' => 255,
            'trip_activity_gallery_title' => 255,
            'trip_activity_gallery_slides_text' => 8000,
            'per_person_label' => 120,
            'seats_left_suffix' => 120,
            'booking_name_placeholder' => 120,
            'booking_email_placeholder' => 120,
            'booking_phone_placeholder' => 120,
            'booking_trip_placeholder' => 120,
            'booking_submit_text' => 120,
        ];
        $allowedImage = [
            'logo_image',
            'hero_background_image',
            'about_image',
            'why_image',
        ];

        $locale = strtolower(trim((string) ($validated['locale'] ?? '')));
        $enabled = $landingPage->enabledLocaleList();
        if ($locale === '' || ! in_array($locale, $enabled, true)) {
            $locale = strtolower(trim((string) ($landingPage->default_locale ?: 'en')));
            if (! in_array($locale, $enabled, true)) {
                $locale = $enabled[0];
            }
        }

        $visual = is_array($landingPage->visual_content) ? $landingPage->visual_content : [];
        $visual = LandingPage::ensureStructuredVisualContent($visual);

        foreach ($validated['fields'] as $key => $value) {
            if ($key === 'hero_cta_target') {
                $text = trim((string) $value);
                $text = strip_tags($text);
                $visual['hero_cta_target'] = mb_substr($text, 0, 1024);

                continue;
            }

            if (array_key_exists($key, $allowedText)) {
                $max = $allowedText[$key];
                $text = trim((string) $value);
                $text = strip_tags($text);
                if (! isset($visual['i18n'][$locale]) || ! is_array($visual['i18n'][$locale])) {
                    $visual['i18n'][$locale] = [];
                }
                $visual['i18n'][$locale][$key] = mb_substr($text, 0, $max);

                continue;
            }

            if (in_array($key, $allowedImage, true)) {
                $url = trim((string) $value);
                if ($url === '') {
                    $visual[$key] = '';

                    continue;
                }
                if (Str::startsWith($url, ['javascript:', 'data:text/html'])) {
                    continue;
                }
                if (! Str::startsWith($url, ['http://', 'https://', '/']) && ! Str::startsWith($url, 'images/')) {
                    continue;
                }
                $visual[$key] = mb_substr($url, 0, 2048);
            }
        }

        if (array_key_exists('agenda_items_text', $validated['fields'] ?? [])) {
            if (! isset($visual['i18n'][$locale]) || ! is_array($visual['i18n'][$locale])) {
                $visual['i18n'][$locale] = [];
            }
            $text = (string) ($visual['i18n'][$locale]['agenda_items_text'] ?? '');
            $items = LandingPage::parseAgendaItemsFromText($text);
            if ($items === []) {
                unset($visual['i18n'][$locale]['agenda_items'], $visual['i18n'][$locale]['agenda_days']);
            } else {
                $visual['i18n'][$locale]['agenda_items'] = $items;
                $visual['i18n'][$locale]['agenda_days'] = LandingPage::buildAgendaDaysFromItems($items, $locale);
            }
        }

        $landingPage->update(['visual_content' => $visual]);

        return response()->json([
            'ok' => true,
            'message' => 'Visual preview updated.',
            'visual_content' => $visual,
        ]);
    }

    /**
     * Rebuild agenda day groups from merged pipe text (admin UI + translate-from-English).
     */
    public function parseAgendaDays(Request $request)
    {
        $validated = $request->validate([
            'text' => 'nullable|string|max:12000',
            'locale' => 'required|string|max:12',
        ]);
        $locale = strtolower(trim($validated['locale']));
        $rows = LandingPage::parseAgendaItemsFromText((string) ($validated['text'] ?? ''));
        $groups = LandingPage::buildAgendaDaysFromItems($rows, $locale);
        if ($groups === []) {
            $groups = [['day' => 1, 'label' => $locale === 'zh' ? '第1天' : 'Day 1', 'rows' => []]];
        }

        return response()->json(['groups' => $groups]);
    }

    public function setActive(LandingPage $landingPage)
    {
        if (! $landingPage->is_published) {
            return back()->with('error', 'Publish the landing page before setting it active.');
        }

        $this->deactivateOtherPages($landingPage->id);
        $landingPage->update(['is_active' => true]);

        return back()->with('success', 'Landing page is now active.');
    }

    public function publish(LandingPage $landingPage)
    {
        $landingPage->update([
            'is_published' => true,
            'published_at' => $landingPage->published_at ?: now(),
        ]);

        return back()->with('success', 'Landing page published.');
    }

    public function unpublish(LandingPage $landingPage)
    {
        $landingPage->update([
            'is_published' => false,
            'is_active' => false,
            'published_at' => null,
        ]);

        return back()->with('success', 'Landing page unpublished.');
    }

    private function validateLandingPage(Request $request, ?int $landingPageId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9\\-\\s_]+$/i',
                Rule::unique('landing_pages', 'slug')->ignore($landingPageId),
            ],
            'industry' => 'nullable|string|max:255',
            'headline' => 'nullable|string|max:255',
            'html_content' => 'nullable|string',
            'css_content' => 'nullable|string',
            'js_content' => 'nullable|string',
            'redirect_url' => 'nullable|string|max:1024',
            'show_once_mode' => 'required|in:cookie_once,session_once,entry_url_once',
            'default_locale' => ['required', 'string', 'max:12', Rule::in(LandingPage::allowedLocaleCodes())],
            'enabled_locales' => ['required', 'array', 'min:1'],
            'enabled_locales.*' => ['string', 'max:12', Rule::in(LandingPage::allowedLocaleCodes())],
            'allow_inline_scripts' => 'nullable|boolean',
            'use_visual_builder' => 'nullable|boolean',
            'template_key' => 'nullable|in:canton_fair_visual',
            'visual' => 'nullable|array',
            'visual.hero_cta_target' => 'nullable|string|max:1024',
            'visual.logo_max_width_px' => 'nullable|integer|min:80|max:420',
            'visual.logo_max_height_px' => 'nullable|integer|min:40|max:200',
            'visual.logo_padding_px' => 'nullable|integer|min:0|max:40',
            'visual.logo_border_radius_px' => 'nullable|integer|min:0|max:48',
            'visual.logo_shadow_preset' => 'nullable|string|in:default,none,soft,strong,glow',
            'visual.logo_backdrop' => 'nullable|string|in:none,white,dark',
            'visual.logo_effect' => 'nullable|string|in:none,gentle_pulse,soft_ring',
            'visual.hero_background_images' => 'nullable|array',
            'visual.hero_background_images.*' => 'nullable|string|max:512',
            'visual.hero_background_video' => 'nullable|string|max:2048',
            'clear_hero_background_video' => 'nullable|boolean',
            'visual.i18n' => 'nullable|array',
            'visual.i18n.*' => 'nullable|array',
            'visual.i18n.*.hero_title' => 'nullable|string|max:255',
            'visual.i18n.*.hero_subtitle' => 'nullable|string|max:2000',
            'visual.i18n.*.hero_cta_text' => 'nullable|string|max:120',
            'visual.i18n.*.about_title' => 'nullable|string|max:255',
            'visual.i18n.*.about_text_en' => 'nullable|string|max:4000',
            'visual.i18n.*.about_text_kh' => 'nullable|string|max:4000',
            'visual.i18n.*.package_title' => 'nullable|string|max:255',
            'visual.i18n.*.package_price' => 'nullable|string|max:120',
            'visual.i18n.*.promotion_section_title' => 'nullable|string|max:255',
            'visual.i18n.*.booking_title' => 'nullable|string|max:255',
            'visual.i18n.*.faq_title' => 'nullable|string|max:255',
            'visual.i18n.*.terms_title' => 'nullable|string|max:255',
            'visual.i18n.*.terms_text' => 'nullable|string|max:8000',
            'visual.i18n.*.agenda_title' => 'nullable|string|max:255',
            'visual.i18n.*.agenda_hdr_slot' => 'nullable|string|max:120',
            'visual.i18n.*.agenda_hdr_activity' => 'nullable|string|max:120',
            'visual.i18n.*.agenda_hdr_detail' => 'nullable|string|max:120',
            'visual.i18n.*.agenda_items_text' => 'nullable|string|max:12000',
            'visual.i18n.*.trip_activity_gallery_title' => 'nullable|string|max:255',
            'visual.i18n.*.trip_activity_gallery_slides_text' => 'nullable|string|max:8000',
            'visual.i18n.*.trip_activity_slides_rebuild' => 'nullable|in:1',
            'visual.i18n.*.trip_activity_from_textarea' => 'nullable|in:0,1',
            'visual.i18n.*.trip_activity_slide_keep' => 'nullable|array',
            'visual.i18n.*.trip_activity_slide_keep.*.path' => 'nullable|string|max:512',
            'visual.i18n.*.trip_activity_slide_keep.*.caption' => 'nullable|string|max:500',
            'visual.i18n.*.trip_activity_slide_keep.*.remove' => 'nullable|boolean',
            'visual_trip_activity_new_slides' => 'nullable|array',
            'visual_trip_activity_new_slides.*' => 'nullable',
            'visual.i18n.*.trip_section_title' => 'nullable|string|max:255',
            'visual.i18n.*.per_person_label' => 'nullable|string|max:120',
            'visual.i18n.*.seats_left_suffix' => 'nullable|string|max:120',
            'visual.i18n.*.booking_name_placeholder' => 'nullable|string|max:120',
            'visual.i18n.*.booking_email_placeholder' => 'nullable|string|max:120',
            'visual.i18n.*.booking_phone_placeholder' => 'nullable|string|max:120',
            'visual.i18n.*.booking_trip_placeholder' => 'nullable|string|max:120',
            'visual.i18n.*.booking_submit_text' => 'nullable|string|max:120',
            'visual.i18n.*.trip_phase_register_cta' => 'nullable|string|max:120',
            'visual.i18n.*.trip_phase_modal_title' => 'nullable|string|max:255',
            'visual.i18n.*.hero_stats_text' => 'nullable|string|max:8000',
            'visual.i18n.*.package_items_text' => 'nullable|string|max:12000',
            'visual.i18n.*.trip_dates_text' => 'nullable|string|max:12000',
            'visual.i18n.*.trip_phases_json' => 'nullable|string|max:50000',
            'visual.i18n.*.promotion_discounts_json' => 'nullable|string|max:20000',
            'visual.i18n.*.faq_items_text' => 'nullable|string|max:16000',
            'visual.i18n.*.contact_phones_text' => 'nullable|string|max:4000',
            'visual.section_blueprint_json' => 'nullable|string|max:12000',
            'visual_logo_image' => 'nullable|image|max:8192',
            'visual_hero_background_image' => 'nullable|image|max:8192',
            'visual_hero_background_images' => 'nullable|array',
            'visual_hero_background_images.*' => 'nullable|image|max:8192',
            'visual_hero_background_video' => 'nullable|file|mimes:mp4,webm|max:51200',
            'visual_about_image' => 'nullable|image|max:8192',
            'visual_why_image' => 'nullable|image|max:8192',
            'priority' => 'nullable|integer|min:1|max:9999',
            'is_active' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ], [
            'slug.regex' => 'The slug may only contain letters, numbers, spaces, underscores, and hyphens.',
        ]);
    }

    /**
     * Marketing workflow: visual template only (no raw HTML/CSS/JS in admin).
     */
    private function forceMarketingVisualMode(array $validated): array
    {
        $validated['use_visual_builder'] = true;
        $validated['allow_inline_scripts'] = true;
        $validated['template_key'] = $validated['template_key'] ?? 'canton_fair_visual';

        return $validated;
    }

    private function applyContentSafety(array $validated): array
    {
        $validated['allow_inline_scripts'] = (bool) ($validated['allow_inline_scripts'] ?? false);
        $validated['use_visual_builder'] = (bool) ($validated['use_visual_builder'] ?? false);
        $validated['priority'] = (int) ($validated['priority'] ?? 100);
        $allowedLocaleCodes = LandingPage::allowedLocaleCodes();
        $enabled = array_values(array_unique(array_filter(
            array_map(static fn ($v) => strtolower(trim((string) $v)), $validated['enabled_locales'] ?? []),
            static fn ($v) => in_array($v, $allowedLocaleCodes, true)
        )));
        if ($enabled === []) {
            $enabled = ['en'];
        }
        $validated['enabled_locales'] = $enabled;
        $def = strtolower(trim((string) ($validated['default_locale'] ?? 'en')));
        if (! in_array($def, $enabled, true)) {
            $def = $enabled[0];
        }
        $validated['default_locale'] = $def;
        $validated['redirect_url'] = $this->normalizeRedirectUrl((string) ($validated['redirect_url'] ?? ''), (string) ($validated['slug'] ?? ''));
        $validated['template_key'] = $validated['use_visual_builder']
            ? ($validated['template_key'] ?? 'canton_fair_visual')
            : null;

        $htmlContent = (string) ($validated['html_content'] ?? '');
        if (! $validated['use_visual_builder']) {
            $hasPhp = stripos($htmlContent, '<?php') !== false
                || stripos($htmlContent, '<?=') !== false;

            if ($hasPhp) {
                abort(422, 'PHP tags are not allowed in landing page content.');
            }

            if (! $validated['allow_inline_scripts']) {
                $validated['html_content'] = $this->stripInlineScripts($htmlContent);
                $validated['js_content'] = null;
            }
        }

        return $validated;
    }

    private function stripInlineScripts(string $html): string
    {
        $html = preg_replace('/<script\\b[^>]*>(.*?)<\\/script>/is', '', $html) ?? $html;
        $html = preg_replace('/\\son[a-z]+\\s*=\\s*"[^"]*"/i', '', $html) ?? $html;
        $html = preg_replace("/\\son[a-z]+\\s*=\\s*'[^']*'/i", '', $html) ?? $html;
        $html = preg_replace('/\\son[a-z]+\\s*=\\s*[^\\s>]+/i', '', $html) ?? $html;

        return $html;
    }

    private function normalizeRedirectUrl(string $url, string $landingSlug = ''): string
    {
        $url = trim($url);
        $landingSlug = trim($landingSlug);
        if ($url === '' && $landingSlug !== '') {
            return '/l/'.Str::slug($landingSlug).'/thank-you';
        }
        if ($url === '') {
            return '/login';
        }

        if (Str::startsWith($url, ['http://', 'https://', '/'])) {
            return $url;
        }

        return '/'.ltrim($url, '/');
    }

    private function deactivateOtherPages(?int $excludeId = null): void
    {
        LandingPage::query()
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    private function prepareVisualBuilderData(Request $request, array $validated, ?LandingPage $landingPage = null): array
    {
        $existing = is_array($landingPage?->visual_content) ? $landingPage->visual_content : [];
        $incoming = is_array($validated['visual'] ?? null) ? $validated['visual'] : [];
        $slug = (string) ($validated['slug'] ?? $landingPage?->slug ?? Str::random(8));

        $visual = $existing;

        $uploads = [
            'logo_image' => $request->file('visual_logo_image'),
            'hero_background_image' => $request->file('visual_hero_background_image'),
            'about_image' => $request->file('visual_about_image'),
            'why_image' => $request->file('visual_why_image'),
        ];

        foreach ($uploads as $key => $file) {
            if ($file instanceof UploadedFile) {
                $visual[$key] = $this->storeVisualImage($file, $slug, $key);
            } elseif (! empty($existing[$key])) {
                $visual[$key] = $existing[$key];
            }
        }

        $primaryHero = $visual['hero_background_image'] ?? ($existing['hero_background_image'] ?? null);
        $gallery = [];
        if (array_key_exists('hero_background_images', $incoming)) {
            $gallery = $this->sanitizeHeroGalleryPaths(is_array($incoming['hero_background_images']) ? $incoming['hero_background_images'] : []);
        } elseif (! empty($existing['hero_background_images']) && is_array($existing['hero_background_images'])) {
            $gallery = $this->sanitizeHeroGalleryPaths($existing['hero_background_images']);
        }
        $multiHeroFiles = $request->file('visual_hero_background_images');
        if (is_array($multiHeroFiles)) {
            foreach ($multiHeroFiles as $mf) {
                if ($mf instanceof UploadedFile) {
                    $gallery[] = $this->storeVisualImage($mf, $slug, 'hero_background_image');
                }
            }
        }
        if ($primaryHero) {
            $ph = (string) $primaryHero;
            $gallery = array_values(array_unique(array_merge([$ph], array_values(array_filter($gallery, static fn ($p) => (string) $p !== $ph)))));
        }
        $gallery = array_slice($this->sanitizeHeroGalleryPaths($gallery), 0, 10);
        if ($gallery !== []) {
            $visual['hero_background_images'] = $gallery;
            $visual['hero_background_image'] = $gallery[0];
        } else {
            unset($visual['hero_background_images']);
        }

        $heroVideoFile = $request->file('visual_hero_background_video');
        if ($heroVideoFile instanceof UploadedFile) {
            $visual['hero_background_video'] = $this->storeVisualMedia($heroVideoFile, $slug, 'hero_background_video', ['mp4', 'webm']);
        } elseif ($request->boolean('clear_hero_background_video')) {
            unset($visual['hero_background_video']);
        } elseif (isset($incoming['hero_background_video'])) {
            $hv = trim((string) $incoming['hero_background_video']);
            if ($hv !== '' && LandingPage::isValidHeroBackgroundVideoReference($hv)) {
                $visual['hero_background_video'] = $hv;
            }
        } elseif (! empty($existing['hero_background_video'])) {
            $visual['hero_background_video'] = $existing['hero_background_video'];
        }

        if (array_key_exists('hero_cta_target', $incoming)) {
            $visual['hero_cta_target'] = trim((string) $incoming['hero_cta_target']);
        } elseif (! isset($visual['hero_cta_target']) || $visual['hero_cta_target'] === '') {
            $visual['hero_cta_target'] = $existing['hero_cta_target'] ?? $this->normalizeRedirectUrl((string) ($validated['redirect_url'] ?? ''), (string) ($validated['slug'] ?? ''));
        }

        $mergedPriorForLogo = array_merge($existing, $visual);
        foreach (LandingPage::mergeIncomingLogoAppearance($incoming, $mergedPriorForLogo) as $logoKey => $logoVal) {
            $visual[$logoKey] = $logoVal;
        }

        $adminLocales = LandingPage::allowedLocaleCodes();
        $reqI18n = isset($incoming['i18n']) && is_array($incoming['i18n']) ? $incoming['i18n'] : [];

        $prevI18n = [];
        if (isset($existing['i18n']) && is_array($existing['i18n'])) {
            $prevI18n = $existing['i18n'];
        } else {
            $legacyTextual = [];
            foreach ($existing as $k => $val) {
                if ($k === 'i18n') {
                    continue;
                }
                if (in_array($k, LandingPage::VISUAL_SHARED_KEYS, true)) {
                    continue;
                }
                $legacyTextual[$k] = $val;
            }
            if ($legacyTextual !== []) {
                $prevI18n['en'] = $legacyTextual;
            }
        }

        $visual['i18n'] = [];
        foreach ($adminLocales as $loc) {
            $locIn = is_array($reqI18n[$loc] ?? null) ? $reqI18n[$loc] : [];
            $prevBlock = is_array($prevI18n[$loc] ?? null) ? $prevI18n[$loc] : [];
            $block = $this->buildLocaleVisualBlock($loc, $locIn, $prevBlock);
            $visual['i18n'][$loc] = $this->mergeTripActivityGalleryFromRequest($request, $slug, $loc, $block, $locIn);
        }

        $bpJson = trim((string) ($incoming['section_blueprint_json'] ?? ''));
        if ($bpJson !== '') {
            $decoded = json_decode($bpJson, true);
            $visual['section_blueprint'] = LandingPage::sanitizeSectionBlueprint(is_array($decoded) ? $decoded : []);
        } elseif (! empty($existing['section_blueprint'])) {
            $visual['section_blueprint'] = LandingPage::sanitizeSectionBlueprint($existing['section_blueprint']);
        } else {
            $visual['section_blueprint'] = LandingPage::defaultSectionBlueprint();
        }

        foreach (array_keys($visual) as $k) {
            if ($k === 'i18n' || in_array($k, LandingPage::VISUAL_SHARED_KEYS, true)) {
                continue;
            }
            unset($visual[$k]);
        }

        $validated['visual_content'] = $visual;

        if ((bool) ($validated['use_visual_builder'] ?? false)) {
            $html = trim((string) ($validated['html_content'] ?? ''));
            $validated['html_content'] = $html !== '' ? $html : ($landingPage?->html_content ?? '<div></div>');
            $css = trim((string) ($validated['css_content'] ?? ''));
            $validated['css_content'] = $css !== '' ? $css : ($landingPage?->css_content ?? '');
            $js = trim((string) ($validated['js_content'] ?? ''));
            $validated['js_content'] = $js !== '' ? $js : ($landingPage?->js_content ?? '');
            $validated['allow_inline_scripts'] = true;
            $validated['template_key'] = $validated['template_key'] ?? 'canton_fair_visual';
        }

        unset(
            $validated['visual'],
            $validated['visual_logo_image'],
            $validated['visual_hero_background_image'],
            $validated['visual_hero_background_video'],
            $validated['visual_about_image'],
            $validated['visual_why_image']
        );

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $locIn
     * @param  array<string, mixed>  $prevBlock
     * @return array<string, mixed>
     */
    private function buildLocaleVisualBlock(string $locale, array $locIn, array $prevBlock): array
    {
        $block = $prevBlock;

        $multiline = ['hero_subtitle', 'about_text_en', 'about_text_kh', 'terms_text', 'trip_activity_gallery_slides_text'];
        $stringFields = [
            'hero_title', 'hero_subtitle', 'hero_cta_text',
            'about_title', 'about_text_en', 'about_text_kh',
            'package_title', 'package_price', 'promotion_section_title', 'booking_title', 'faq_title', 'terms_title', 'terms_text', 'agenda_title',
            'agenda_hdr_slot', 'agenda_hdr_activity', 'agenda_hdr_detail',
            'trip_section_title', 'per_person_label', 'seats_left_suffix',
            'booking_name_placeholder', 'booking_email_placeholder', 'booking_phone_placeholder',
            'booking_trip_placeholder', 'booking_submit_text',
            'trip_phase_register_cta', 'trip_phase_modal_title',
            'trip_activity_gallery_title', 'trip_activity_gallery_slides_text',
        ];

        foreach ($stringFields as $f) {
            if (! array_key_exists($f, $locIn)) {
                continue;
            }
            if ($f === 'trip_activity_gallery_slides_text' && (($locIn['trip_activity_slides_rebuild'] ?? '') === '1')) {
                continue;
            }
            $val = trim((string) $locIn[$f]);
            $block[$f] = in_array($f, $multiline, true) ? $val : strip_tags($val);
        }

        $heroRaw = array_key_exists('hero_stats_text', $locIn) ? (string) $locIn['hero_stats_text'] : null;
        $block['hero_stats'] = $this->parsePipeList(
            $heroRaw,
            ['value', 'label', 'icon'],
            is_array($prevBlock['hero_stats'] ?? null) ? $prevBlock['hero_stats'] : []
        );

        $pkgRaw = array_key_exists('package_items_text', $locIn) ? (string) $locIn['package_items_text'] : null;
        $block['package_items'] = $this->parsePackageItemsList(
            $pkgRaw,
            is_array($prevBlock['package_items'] ?? null) ? $prevBlock['package_items'] : []
        );

        $tripRaw = array_key_exists('trip_dates_text', $locIn) ? (string) $locIn['trip_dates_text'] : null;
        $block['trip_dates'] = LandingPage::parseTripDateRowsFromText(
            $tripRaw,
            is_array($prevBlock['trip_dates'] ?? null) ? $prevBlock['trip_dates'] : []
        );

        if (array_key_exists('trip_phases_json', $locIn)) {
            $jsonRaw = trim((string) $locIn['trip_phases_json']);
            if ($jsonRaw === '') {
                unset($block['trip_phases']);
            } else {
                $decoded = json_decode($jsonRaw, true);
                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                    throw ValidationException::withMessages([
                        'visual.i18n.'.$locale.'.trip_phases_json' => ['Trip phases must be valid JSON (array of phases).'],
                    ]);
                }
                $phases = LandingPage::sanitizeTripPhases($decoded);
                $nonEmpty = array_filter(
                    $phases,
                    static fn (array $p) => trim((string) ($p['label'] ?? '')) !== '' || trim((string) ($p['date'] ?? '')) !== ''
                );
                if ($nonEmpty !== []) {
                    $block['trip_phases'] = array_values($nonEmpty);
                    $block['trip_dates'] = LandingPage::tripPhasesToFlatRows($block['trip_phases']);
                } else {
                    unset($block['trip_phases']);
                }
            }
        }

        if (array_key_exists('promotion_discounts_json', $locIn)) {
            $promoRaw = trim((string) $locIn['promotion_discounts_json']);
            if ($promoRaw === '') {
                unset($block['promotion_discounts']);
                $block['promotion_discounts_show'] = false;
            } else {
                $decoded = json_decode($promoRaw, true);
                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                    throw ValidationException::withMessages([
                        'visual.i18n.'.$locale.'.promotion_discounts_json' => ['Promotion discounts must be valid JSON (object with base_price_text and tiers, or an array of tier objects).'],
                    ]);
                }
                $block['promotion_discounts'] = LandingPage::sanitizePromotionDiscounts($decoded);
                $block['promotion_discounts_show'] = true;
            }
        }

        $agendaRaw = array_key_exists('agenda_items_text', $locIn) ? (string) $locIn['agenda_items_text'] : null;
        $block['agenda_items'] = $this->parsePipeList(
            $agendaRaw,
            ['slot', 'activity', 'detail'],
            is_array($prevBlock['agenda_items'] ?? null) ? $prevBlock['agenda_items'] : []
        );
        if ($block['agenda_items'] === []) {
            unset($block['agenda_days']);
        } else {
            $block['agenda_days'] = LandingPage::buildAgendaDaysFromItems($block['agenda_items'], $locale);
        }

        $faqRaw = array_key_exists('faq_items_text', $locIn) ? (string) $locIn['faq_items_text'] : null;
        $block['faq_items'] = $this->parsePipeList(
            $faqRaw,
            ['question', 'answer'],
            is_array($prevBlock['faq_items'] ?? null) ? $prevBlock['faq_items'] : []
        );

        $phonesRaw = array_key_exists('contact_phones_text', $locIn) ? (string) $locIn['contact_phones_text'] : null;
        $block['contact_phones'] = $this->parseSimpleList(
            $phonesRaw,
            is_array($prevBlock['contact_phones'] ?? null) ? $prevBlock['contact_phones'] : []
        );

        return $block;
    }

    /**
     * @param  array<string, mixed>  $block
     * @param  array<string, mixed>  $locIn
     * @return array<string, mixed>
     */
    private function mergeTripActivityGalleryFromRequest(Request $request, string $slug, string $locale, array $block, array $locIn): array
    {
        if (($locIn['trip_activity_slides_rebuild'] ?? '') !== '1') {
            return $block;
        }

        $fromTextareaOnly = (($locIn['trip_activity_from_textarea'] ?? '') === '1');

        $lines = [];
        if ($fromTextareaOnly) {
            foreach (LandingPage::parseTripActivityGallerySlideRows(trim((string) ($locIn['trip_activity_gallery_slides_text'] ?? ''))) as $r) {
                $lines[] = $r['caption'] !== '' ? $r['path'].'|'.$r['caption'] : $r['path'];
            }
        } else {
            $keep = $locIn['trip_activity_slide_keep'] ?? null;
            if (is_array($keep)) {
                ksort($keep, SORT_NATURAL);
                foreach ($keep as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    if (! empty($row['remove'])) {
                        continue;
                    }
                    $path = LandingPage::sanitizeTripPhaseFeatureImagePath((string) ($row['path'] ?? ''));
                    if ($path === '') {
                        continue;
                    }
                    $cap = trim(strip_tags((string) ($row['caption'] ?? '')));
                    $lines[] = $cap !== '' ? $path.'|'.$cap : $path;
                }
            }
        }

        $allNew = $request->file('visual_trip_activity_new_slides');
        $localeFiles = null;
        if (is_array($allNew) && array_key_exists($locale, $allNew)) {
            $localeFiles = $allNew[$locale];
        }
        if ($localeFiles instanceof UploadedFile) {
            $localeFiles = [$localeFiles];
        }
        if (is_array($localeFiles)) {
            $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            foreach ($localeFiles as $file) {
                if (! $file instanceof UploadedFile || ! $file->isValid()) {
                    continue;
                }
                $ext = strtolower((string) $file->getClientOriginalExtension());
                if (! in_array($ext, $allowedExt, true)) {
                    throw ValidationException::withMessages([
                        'visual_trip_activity_new_slides.'.$locale => ['Each new trip activity image must be JPG, PNG, GIF, or WebP.'],
                    ]);
                }
                if ($file->getSize() > 8192 * 1024) {
                    throw ValidationException::withMessages([
                        'visual_trip_activity_new_slides.'.$locale => ['Each trip activity image must be 8 MB or smaller.'],
                    ]);
                }
                $lines[] = $this->storeVisualImage($file, $slug, 'trip_activity_slide');
            }
        }

        if (! $fromTextareaOnly && $lines === []) {
            $paste = trim((string) ($locIn['trip_activity_gallery_slides_text'] ?? ''));
            if ($paste !== '') {
                foreach (LandingPage::parseTripActivityGallerySlideRows($paste) as $r) {
                    $lines[] = $r['caption'] !== '' ? $r['path'].'|'.$r['caption'] : $r['path'];
                }
            }
        }

        if (count($lines) > 30) {
            throw ValidationException::withMessages([
                'visual.i18n.'.$locale.'.trip_activity_slide_keep' => ['You can add at most 30 trip activity slides per language.'],
            ]);
        }

        $text = implode("\n", $lines);
        if (strlen($text) > 8000) {
            throw ValidationException::withMessages([
                'visual.i18n.'.$locale.'.trip_activity_gallery_slides_text' => ['Trip activity slides data is too long. Remove some slides or shorten captions.'],
            ]);
        }

        $block['trip_activity_gallery_slides_text'] = $text;
        unset($block['trip_activity_slides_rebuild'], $block['trip_activity_from_textarea'], $block['trip_activity_slide_keep']);

        return $block;
    }

    private function storeVisualImage(UploadedFile $file, string $slug, string $key): string
    {
        $safeSlug = Str::slug($slug);
        $dir = public_path('images/landing-pages/'.$safeSlug);
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $ext = strtolower((string) $file->getClientOriginalExtension());
        $filename = $key.'-'.time().'-'.Str::random(6).'.'.$ext;
        $file->move($dir, $filename);

        return 'images/landing-pages/'.$safeSlug.'/'.$filename;
    }

    /**
     * @param  array<int, mixed>  $paths
     * @return list<string>
     */
    private function sanitizeHeroGalleryPaths(array $paths): array
    {
        $out = [];
        foreach ($paths as $p) {
            $p = trim((string) $p);
            if ($p === '' || str_contains($p, '..')) {
                continue;
            }
            if (! preg_match('#^images/landing-pages/[\w\-]+/[\w\-.]+\.(jpg|jpeg|png|gif|webp)$#i', $p)) {
                continue;
            }
            $out[] = $p;
        }

        return array_values(array_unique($out));
    }

    /**
     * Store hero background video next to landing images (public disk path returned).
     *
     * @param  list<string>  $allowedExt
     */
    private function storeVisualMedia(UploadedFile $file, string $slug, string $key, array $allowedExt): string
    {
        $ext = strtolower((string) $file->getClientOriginalExtension());
        if (! in_array($ext, $allowedExt, true)) {
            throw ValidationException::withMessages([
                'visual_hero_background_video' => ['Video must be MP4 or WebM.'],
            ]);
        }

        $safeSlug = Str::slug($slug);
        $dir = public_path('images/landing-pages/'.$safeSlug);
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = $key.'-'.time().'-'.Str::random(6).'.'.$ext;
        $file->move($dir, $filename);

        return 'images/landing-pages/'.$safeSlug.'/'.$filename;
    }

    /**
     * @param  array<int, mixed>  $fallback
     * @return array<int, string>
     */
    /**
     * One line: "Description text|icon_key" (icon_key is optional, e.g. plane, hotel).
     *
     * @param  array<int, mixed>  $fallback
     * @return array<int, array{text: string, icon: string}>
     */
    private function parsePackageItemsList(?string $raw, array $fallback = []): array
    {
        if ($raw === null) {
            $out = [];
            foreach ($fallback as $row) {
                if (is_string($row)) {
                    $out[] = ['text' => trim($row), 'icon' => ''];
                } elseif (is_array($row)) {
                    $out[] = [
                        'text' => trim((string) ($row['text'] ?? '')),
                        'icon' => trim((string) ($row['icon'] ?? '')),
                    ];
                }
            }

            return $out;
        }
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $items = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            if (str_contains($line, '|')) {
                [$textPart, $icon] = explode('|', $line, 2);
                $items[] = [
                    'text' => strip_tags(trim((string) $textPart)),
                    'icon' => strip_tags(trim((string) $icon)),
                ];
            } else {
                $items[] = ['text' => strip_tags($line), 'icon' => ''];
            }
        }

        return $items;
    }

    private function parseSimpleList(?string $raw, array $fallback = []): array
    {
        if ($raw === null) {
            return array_values(array_filter(array_map(fn ($v) => trim((string) $v), $fallback)));
        }
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $items = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            $items[] = strip_tags($line);
        }

        return $items;
    }

    /**
     * Parse "a|b|c" rows into structured arrays.
     *
     * @param  array<int, string>  $columns
     * @param  array<int, mixed>  $fallback
     * @return array<int, array<string, string>>
     */
    private function parsePipeList(?string $raw, array $columns, array $fallback = []): array
    {
        if ($raw === null) {
            $normalized = [];
            foreach ($fallback as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $item = [];
                foreach ($columns as $col) {
                    $item[$col] = trim((string) ($row[$col] ?? ''));
                }
                $normalized[] = $item;
            }

            return $normalized;
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $items = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            $parts = array_map(fn ($v) => trim(strip_tags((string) $v)), explode('|', $line));
            $item = [];
            foreach ($columns as $idx => $col) {
                $item[$col] = $parts[$idx] ?? '';
            }
            $items[] = $item;
        }

        return $items;
    }
}
