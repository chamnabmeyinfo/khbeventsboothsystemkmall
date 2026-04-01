<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageEvent;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LandingPageController extends Controller
{
    public function index(Request $request)
    {
        $query = LandingPage::query();

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
        $validated = $this->prepareVisualBuilderData($request, $validated);
        $validated = $this->applyContentSafety($validated);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        if ($validated['is_active']) {
            $this->deactivateOtherPages();
        }

        LandingPage::create($validated);

        return redirect()->route('landing-pages.index')
            ->with('success', 'Landing page created successfully.');
    }

    public function edit(LandingPage $landingPage)
    {
        return view('landing-pages.edit', compact('landingPage'));
    }

    public function update(Request $request, LandingPage $landingPage)
    {
        $validated = $this->validateLandingPage($request, $landingPage->id);
        $validated['slug'] = Str::slug($validated['slug']);
        $validated = $this->prepareVisualBuilderData($request, $validated, $landingPage);
        $validated = $this->applyContentSafety($validated);
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

        return redirect()->route('landing-pages.index')
            ->with('success', 'Landing page updated successfully.');
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
        ]);

        $allowedText = [
            'hero_title' => 255,
            'hero_subtitle' => 2000,
            'hero_cta_text' => 120,
            'hero_cta_target' => 1024,
            'about_title' => 255,
            'about_text_en' => 4000,
            'about_text_kh' => 4000,
            'package_title' => 255,
            'package_price' => 120,
            'booking_title' => 255,
            'faq_title' => 255,
        ];
        $allowedImage = [
            'logo_image',
            'hero_background_image',
            'about_image',
            'why_image',
        ];

        $visual = is_array($landingPage->visual_content) ? $landingPage->visual_content : [];
        foreach ($validated['fields'] as $key => $value) {
            if (array_key_exists($key, $allowedText)) {
                $max = $allowedText[$key];
                $text = trim((string) $value);
                $text = strip_tags($text);
                $visual[$key] = mb_substr($text, 0, $max);
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

        $landingPage->update(['visual_content' => $visual]);

        return response()->json([
            'ok' => true,
            'message' => 'Visual preview updated.',
            'visual_content' => $visual,
        ]);
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
            'html_content' => [
                Rule::requiredIf(! $request->boolean('use_visual_builder')),
                'nullable',
                'string',
            ],
            'css_content' => 'nullable|string',
            'js_content' => 'nullable|string',
            'redirect_url' => 'required|string|max:1024',
            'show_once_mode' => 'required|in:cookie_once,session_once,entry_url_once',
            'allow_inline_scripts' => 'nullable|boolean',
            'use_visual_builder' => 'nullable|boolean',
            'template_key' => 'nullable|in:canton_fair_visual',
            'visual' => 'nullable|array',
            'visual.hero_title' => 'nullable|string|max:255',
            'visual.hero_subtitle' => 'nullable|string|max:2000',
            'visual.hero_cta_text' => 'nullable|string|max:120',
            'visual.hero_cta_target' => 'nullable|string|max:1024',
            'visual.about_title' => 'nullable|string|max:255',
            'visual.about_text_en' => 'nullable|string|max:4000',
            'visual.about_text_kh' => 'nullable|string|max:4000',
            'visual.package_title' => 'nullable|string|max:255',
            'visual.package_price' => 'nullable|string|max:120',
            'visual.booking_title' => 'nullable|string|max:255',
            'visual.faq_title' => 'nullable|string|max:255',
            'visual_logo_image' => 'nullable|image|max:8192',
            'visual_hero_background_image' => 'nullable|image|max:8192',
            'visual_about_image' => 'nullable|image|max:8192',
            'visual_why_image' => 'nullable|image|max:8192',
            'priority' => 'nullable|integer|min:1|max:9999',
            'is_active' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
        ], [
            'slug.regex' => 'The slug may only contain letters, numbers, spaces, underscores, and hyphens.',
        ]);
    }

    private function applyContentSafety(array $validated): array
    {
        $validated['allow_inline_scripts'] = (bool) ($validated['allow_inline_scripts'] ?? false);
        $validated['use_visual_builder'] = (bool) ($validated['use_visual_builder'] ?? false);
        $validated['priority'] = (int) ($validated['priority'] ?? 100);
        $validated['redirect_url'] = $this->normalizeRedirectUrl((string) $validated['redirect_url']);
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

    private function normalizeRedirectUrl(string $url): string
    {
        $url = trim($url);
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
        $visual = is_array($validated['visual'] ?? null)
            ? $validated['visual']
            : (is_array($landingPage?->visual_content) ? $landingPage->visual_content : []);

        $slug = (string) ($validated['slug'] ?? $landingPage?->slug ?? Str::random(8));
        $uploads = [
            'logo_image' => $request->file('visual_logo_image'),
            'hero_background_image' => $request->file('visual_hero_background_image'),
            'about_image' => $request->file('visual_about_image'),
            'why_image' => $request->file('visual_why_image'),
        ];

        foreach ($uploads as $key => $file) {
            if ($file instanceof UploadedFile) {
                $visual[$key] = $this->storeVisualImage($file, $slug, $key);
            } elseif (! empty($landingPage?->visual_content[$key]) && empty($visual[$key])) {
                $visual[$key] = $landingPage->visual_content[$key];
            }
        }

        $validated['visual_content'] = $visual;

        if ((bool) ($validated['use_visual_builder'] ?? false)) {
            $validated['html_content'] = $validated['html_content'] ?? '<div></div>';
            $validated['css_content'] = $validated['css_content'] ?? '';
            $validated['js_content'] = $validated['js_content'] ?? '';
            $validated['allow_inline_scripts'] = true;
            $validated['template_key'] = $validated['template_key'] ?? 'canton_fair_visual';
        }

        unset(
            $validated['visual'],
            $validated['visual_logo_image'],
            $validated['visual_hero_background_image'],
            $validated['visual_about_image'],
            $validated['visual_why_image']
        );

        return $validated;
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
}
