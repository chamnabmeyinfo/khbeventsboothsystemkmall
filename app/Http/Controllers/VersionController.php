<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSystemVersionRequest;
use App\Http\Requests\UpdateSystemVersionRequest;
use App\Models\SystemVersion;
use App\Services\VersionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VersionController extends Controller
{
    public function __construct(
        private VersionService $versionService
    ) {}

    /**
     * Admin: list all system versions.
     */
    public function index(Request $request): View
    {
        $versions = $this->versionService->getVersionHistory(
            (int) $request->input('per_page', 15)
        );

        return view('versions.index', compact('versions'));
    }

    /**
     * Admin: show create form.
     */
    public function create(): View
    {
        return view('versions.create');
    }

    /**
     * Admin: store a new version.
     */
    public function store(StoreSystemVersionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_current'] = $request->boolean('is_current');

        $this->versionService->createVersion($validated);

        return redirect()
            ->route('versions.index')
            ->with('success', 'Version created successfully.');
    }

    /**
     * Admin: show a single version.
     */
    public function show(SystemVersion $version): View
    {
        return view('versions.show', compact('version'));
    }

    /**
     * Admin: set version as current.
     */
    public function setCurrent(SystemVersion $version): RedirectResponse
    {
        $this->versionService->setCurrent($version);

        return redirect()
            ->route('versions.index')
            ->with('success', 'Current version updated.');
    }

    /**
     * Documentation & changelog page (authenticated users).
     */
    public function documentation(): View
    {
        $current = $this->versionService->getCurrentVersion();
        $allVersions = $this->versionService->getAllVersionsForChangelog();
        $appVersion = config('app.version', '1.0.0');

        return view('documentation.index', compact('current', 'allVersions', 'appVersion'));
    }

    /**
     * Admin: single page to update changelog (edit current version, quick-add entry, or create new version).
     */
    public function updateChangelogPage(): View
    {
        $currentVersion = $this->versionService->getCurrentVersionModel();
        $recentVersions = SystemVersion::latestFirst()->limit(10)->get();

        return view('changelog.update', compact('currentVersion', 'recentVersions'));
    }

    /**
     * Admin: update current version's summary and changelog.
     */
    public function updateCurrentVersion(UpdateSystemVersionRequest $request): RedirectResponse
    {
        $current = $this->versionService->getCurrentVersionModel();
        if (! $current) {
            return redirect()
                ->route('changelog.update')
                ->with('error', 'No current version set. Create a version or set one as current first.');
        }

        $this->versionService->updateVersion($current, $request->validated());

        return redirect()
            ->route('changelog.update')
            ->with('success', 'Changelog updated.');
    }

    /**
     * Admin: append a changelog entry to the current version.
     */
    public function appendChangelogEntry(Request $request): RedirectResponse
    {
        $request->validate(['entry' => 'required|string|max:65535']);

        try {
            $this->versionService->appendToCurrentChangelog($request->input('entry'));
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->route('changelog.update')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('changelog.update')
            ->with('success', 'Entry added to changelog.');
    }
}
