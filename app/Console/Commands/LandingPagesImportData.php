<?php

namespace App\Console\Commands;

use App\Models\LandingPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LandingPagesImportData extends Command
{
    protected $signature = 'landing:import-data
                            {file? : Path to JSON (default: storage/app/exports/landing-pages-data.json)}
                            {--force : Skip confirmation}';

    protected $description = 'Import landing pages from JSON (upsert by slug). Copy public/images/landing-pages first if the page uses uploads.';

    public function handle(): int
    {
        $path = $this->resolveJsonPath($this->argument('file'));

        if (! File::exists($path)) {
            $this->error('File not found: '.$path);

            return 1;
        }

        $raw = File::get($path);
        $payload = json_decode($raw, true);
        if (! is_array($payload) || empty($payload['landing_pages'])) {
            $this->error('Invalid JSON: expected landing_pages array.');

            return 1;
        }

        $count = count($payload['landing_pages']);
        $this->warn("This will upsert {$count} landing page(s) by slug.");
        if (! $this->option('force') && ! $this->confirm('Continue?', true)) {
            return 0;
        }

        $fillable = (new LandingPage)->getFillable();
        $imported = 0;
        foreach ($payload['landing_pages'] as $row) {
            if (empty($row['slug'])) {
                continue;
            }
            $data = [];
            foreach ($fillable as $key) {
                if (array_key_exists($key, $row)) {
                    $data[$key] = $row[$key];
                }
            }
            if (isset($data['visual_content']) && is_string($data['visual_content'])) {
                $decoded = json_decode($data['visual_content'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['visual_content'] = $decoded;
                }
            }
            LandingPage::updateOrCreate(
                ['slug' => $row['slug']],
                $data
            );
            $imported++;
        }

        $this->info("Imported/updated {$imported} landing page(s).");
        if (! empty($payload['public_asset_paths_relative'])) {
            $this->newLine();
            $this->comment('Ensure these exist under public/ on this server (copy from local if missing):');
            foreach (array_slice($payload['public_asset_paths_relative'], 0, 15) as $p) {
                $this->line('  '.$p);
            }
            if (count($payload['public_asset_paths_relative']) > 15) {
                $this->line('  … and '.(count($payload['public_asset_paths_relative']) - 15).' more.');
            }
        }

        return 0;
    }

    private function resolveJsonPath(?string $file): string
    {
        if ($file === null || $file === '') {
            return storage_path('app/exports/landing-pages-data.json');
        }
        if (File::exists($file)) {
            return $file;
        }
        $base = base_path($file);
        if (File::exists($base)) {
            return $base;
        }
        $inExports = storage_path('app/exports/'.$file);
        if (File::exists($inExports)) {
            return $inExports;
        }

        return $file;
    }
}
