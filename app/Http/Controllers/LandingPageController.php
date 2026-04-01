<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageEvent;
use Illuminate\Http\Request;
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
            'html_content' => 'required|string',
            'css_content' => 'nullable|string',
            'js_content' => 'nullable|string',
            'redirect_url' => 'required|string|max:1024',
            'show_once_mode' => 'required|in:cookie_once,session_once,entry_url_once',
            'allow_inline_scripts' => 'nullable|boolean',
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
        $validated['priority'] = (int) ($validated['priority'] ?? 100);
        $validated['redirect_url'] = $this->normalizeRedirectUrl((string) $validated['redirect_url']);

        $hasPhp = stripos($validated['html_content'], '<?php') !== false
            || stripos($validated['html_content'], '<?=') !== false;

        if ($hasPhp) {
            abort(422, 'PHP tags are not allowed in landing page content.');
        }

        if (! $validated['allow_inline_scripts']) {
            $validated['html_content'] = $this->stripInlineScripts($validated['html_content']);
            $validated['js_content'] = null;
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
}
