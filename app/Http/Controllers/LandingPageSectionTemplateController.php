<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageSectionTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LandingPageSectionTemplateController extends Controller
{
    public function index()
    {
        $templates = LandingPageSectionTemplate::query()
            ->orderBy('layout_key')
            ->orderBy('name')
            ->paginate(30);

        return view('landing-pages.section-templates.index', compact('templates'));
    }

    public function create()
    {
        $layoutLabels = LandingPage::sectionLayoutLabels();
        $exampleJson = <<<'JSON'
{
  "i18n": {
    "en": {
      "hero_title": "Your headline",
      "hero_subtitle": "Supporting line",
      "hero_cta_text": "Reserve now"
    }
  }
}
JSON;

        return view('landing-pages.section-templates.create', compact('layoutLabels', 'exampleJson'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTemplate($request);
        $decoded = $this->decodeContentJson($validated['content_json']);
        if ($decoded === null) {
            return back()->withInput()->withErrors(['content_json' => 'Content must be valid JSON.']);
        }

        LandingPageSectionTemplate::create([
            'name' => $validated['name'],
            'layout_key' => $validated['layout_key'],
            'description' => $validated['description'] ?? null,
            'content' => $decoded,
        ]);

        return redirect()->route('landing-pages.section-templates.index')
            ->with('success', 'Section template created.');
    }

    public function edit(LandingPageSectionTemplate $section_template)
    {
        $layoutLabels = LandingPage::sectionLayoutLabels();
        $sectionTemplate = $section_template;

        return view('landing-pages.section-templates.edit', compact('sectionTemplate', 'layoutLabels'));
    }

    public function update(Request $request, LandingPageSectionTemplate $section_template)
    {
        $validated = $this->validateTemplate($request);
        $decoded = $this->decodeContentJson($validated['content_json']);
        if ($decoded === null) {
            return back()->withInput()->withErrors(['content_json' => 'Content must be valid JSON.']);
        }

        $section_template->update([
            'name' => $validated['name'],
            'layout_key' => $validated['layout_key'],
            'description' => $validated['description'] ?? null,
            'content' => $decoded,
        ]);

        return redirect()->route('landing-pages.section-templates.index')
            ->with('success', 'Section template updated.');
    }

    public function destroy(LandingPageSectionTemplate $section_template)
    {
        $section_template->delete();

        return redirect()->route('landing-pages.section-templates.index')
            ->with('success', 'Section template deleted.');
    }

    /**
     * @return array{name: string, layout_key: string, description?: string|null, content_json: string}
     */
    private function validateTemplate(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'layout_key' => ['required', 'string', 'max:64', Rule::in(LandingPage::SECTION_LAYOUT_KEYS)],
            'description' => ['nullable', 'string', 'max:512'],
            'content_json' => ['required', 'string', 'max:100000'],
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeContentJson(string $raw): ?array
    {
        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return null;
        }

        return $decoded;
    }
}
