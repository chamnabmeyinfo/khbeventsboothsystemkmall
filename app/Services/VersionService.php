<?php

namespace App\Services;

use App\Models\SystemVersion;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VersionService
{
    /**
     * Get the current application version (from config, then DB current marker).
     */
    public function getCurrentVersion(): array
    {
        $configVersion = config('app.version', '1.0.0');
        $current = SystemVersion::current()->first();

        return [
            'version' => $configVersion,
            'released_at' => $current?->released_at?->format('Y-m-d'),
            'summary' => $current?->summary,
            'changelog' => $current?->changelog,
        ];
    }

    /**
     * Get version history (newest first), paginated.
     */
    public function getVersionHistory(int $perPage = 15): LengthAwarePaginator
    {
        return SystemVersion::latestFirst()->paginate($perPage);
    }

    /**
     * Get all versions for documentation/changelog page (no pagination).
     */
    public function getAllVersionsForChangelog(): \Illuminate\Database\Eloquent\Collection
    {
        return SystemVersion::latestFirst()->get();
    }

    /**
     * Create a new system version and optionally set it as current.
     */
    public function createVersion(array $validated): SystemVersion
    {
        return DB::transaction(function () use ($validated) {
            if (! empty($validated['is_current'])) {
                SystemVersion::query()->update(['is_current' => false]);
            }

            return SystemVersion::create([
                'version' => $validated['version'],
                'released_at' => $validated['released_at'],
                'summary' => $validated['summary'] ?? null,
                'changelog' => $validated['changelog'] ?? null,
                'is_current' => (bool) ($validated['is_current'] ?? false),
            ]);
        });
    }

    /**
     * Set a version as the current one.
     */
    public function setCurrent(SystemVersion $systemVersion): void
    {
        DB::transaction(function () use ($systemVersion) {
            SystemVersion::query()->update(['is_current' => false]);
            $systemVersion->update(['is_current' => true]);
        });
    }

    /**
     * Get the current version model (the one marked is_current), or null.
     */
    public function getCurrentVersionModel(): ?SystemVersion
    {
        return SystemVersion::current()->first();
    }

    /**
     * Update a version's summary and/or changelog.
     */
    public function updateVersion(SystemVersion $version, array $data): SystemVersion
    {
        $updates = [];
        if (array_key_exists('summary', $data)) {
            $updates['summary'] = $data['summary'] ?? null;
        }
        if (array_key_exists('changelog', $data)) {
            $updates['changelog'] = $data['changelog'] ?? null;
        }
        if ($updates !== []) {
            $version->update($updates);
        }

        return $version->fresh();
    }

    /**
     * Append a line (or block) to the current version's changelog. Creates current version if none.
     */
    public function appendToCurrentChangelog(string $entry): SystemVersion
    {
        $current = $this->getCurrentVersionModel();
        $entry = trim($entry);
        if ($entry === '') {
            throw new \InvalidArgumentException('Changelog entry cannot be empty.');
        }

        if (! $current) {
            $current = SystemVersion::create([
                'version' => config('app.version', '1.0.0'),
                'released_at' => now(),
                'summary' => 'Current release',
                'changelog' => $entry,
                'is_current' => true,
            ]);
            return $current;
        }

        $existing = trim((string) $current->changelog);
        $newChangelog = $existing === '' ? $entry : $existing . "\n" . $entry;
        $current->update(['changelog' => $newChangelog]);

        return $current->fresh();
    }

    /**
     * Find version by id.
     */
    public function find(int $id): ?SystemVersion
    {
        return SystemVersion::find($id);
    }
}
