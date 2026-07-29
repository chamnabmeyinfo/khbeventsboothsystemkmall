<?php

namespace App\Console\Commands;

use App\Models\FloorPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixFloorPlanImagePaths extends Command
{
    protected $signature = 'floor-plans:fix-image-paths {--dry-run : Show what would be changed without updating DB}';

    protected $description = 'Point floor_plans.floor_image to existing files on disk when current path is missing (e.g. after migration)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $baseDir = public_path('images/floor-plans');

        if (! File::isDirectory($baseDir)) {
            $this->error('Directory not found: '.$baseDir);

            return 1;
        }

        $floorPlans = FloorPlan::all();
        $fixed = 0;

        foreach ($floorPlans as $floorPlan) {
            $currentPath = $floorPlan->floor_image;
            $fullPath = $currentPath ? public_path($currentPath) : null;

            if ($fullPath && file_exists($fullPath)) {
                continue;
            }

            // Current file missing: look for any file matching *_floor_plan_{id}.*
            $pattern = $baseDir.DIRECTORY_SEPARATOR.'*_floor_plan_'.$floorPlan->id.'.*';
            $matches = glob($pattern);

            if (empty($matches)) {
                if ($currentPath) {
                    $this->line("  [skip] FP {$floorPlan->id} ({$floorPlan->name}): no file on disk for current path, no alternate found.");
                }
                continue;
            }

            // Use the most recently modified file if multiple
            usort($matches, function ($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            $chosen = $matches[0];
            $relativePath = 'images/floor-plans/'.basename($chosen);

            if ($dryRun) {
                $this->line("  [would fix] FP {$floorPlan->id} ({$floorPlan->name}): {$currentPath} → {$relativePath}");
                $fixed++;
                continue;
            }

            $floorPlan->update(['floor_image' => $relativePath]);
            $this->line("  [fixed] FP {$floorPlan->id} ({$floorPlan->name}): → {$relativePath}");
            $fixed++;
        }

        if ($dryRun) {
            $this->newLine();
            $this->info("Dry run: would update {$fixed} floor plan(s). Run without --dry-run to apply.");
        } else {
            $this->newLine();
            $this->info("Updated {$fixed} floor plan image path(s).");
        }

        return 0;
    }
}
