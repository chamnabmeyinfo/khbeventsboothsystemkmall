<?php

namespace App\Console\Commands;

use App\Models\LandingPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LandingPagesExportData extends Command
{
    protected $signature = 'landing:export-data
                            {--slug= : Export only this landing page slug}
                            {--path= : Output JSON path (default: storage/app/exports/landing-pages-data.json)}';

    protected $description = 'Export landing page rows (incl. visual_content) to JSON for moving local dev data to another server';

    public function handle(): int
    {
        $query = LandingPage::query()->orderBy('id');
        if ($slug = $this->option('slug')) {
            $query->where('slug', $slug);
        }
        $pages = $query->get();
        if ($pages->isEmpty()) {
            $this->warn('No landing pages found.');

            return 0;
        }

        $defaultPath = storage_path('app/exports/landing-pages-data.json');
        $outPath = $this->option('path') ?: $defaultPath;
        $dir = dirname($outPath);
        if (! is_dir($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $publicFiles = [];
        foreach ($pages as $page) {
            $rel = 'images/landing-pages/'.$page->slug;
            $abs = public_path($rel);
            if (is_dir($abs)) {
                foreach (File::allFiles($abs) as $file) {
                    $publicFiles[] = str_replace(public_path(DIRECTORY_SEPARATOR), '', $file->getPathname());
                    $publicFiles[count($publicFiles) - 1] = str_replace('\\', '/', $publicFiles[count($publicFiles) - 1]);
                }
            }
        }
        $publicFiles = array_values(array_unique($publicFiles));

        $payload = [
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'app_url' => config('app.url'),
            'landing_pages' => $pages->map(fn (LandingPage $p) => $p->toArray())->values()->all(),
            'public_asset_paths_relative' => $publicFiles,
        ];

        File::put($outPath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->info('Exported '.$pages->count().' landing page(s) to:');
        $this->line($outPath);
        if ($publicFiles !== []) {
            $this->newLine();
            $this->comment('Copy these files to the other server (same paths under public/):');
            foreach (array_slice($publicFiles, 0, 20) as $p) {
                $this->line('  '.$p);
            }
            if (count($publicFiles) > 20) {
                $this->line('  … and '.(count($publicFiles) - 20).' more.');
            }
        } else {
            $this->comment('No files under public/images/landing-pages/ for these pages.');
        }
        $this->newLine();
        $this->comment('Import on the server: php artisan landing:import-data '.basename($outPath));

        return 0;
    }
}
