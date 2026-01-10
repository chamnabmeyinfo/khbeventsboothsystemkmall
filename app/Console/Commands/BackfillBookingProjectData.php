<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\Booth;
use App\Models\FloorPlan;

class BackfillBookingProjectData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:backfill-project-data {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill event_id and floor_plan_id for existing bookings from their booked booths';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Starting booking project data backfill...');
        
        // Check if columns exist
        if (!Schema::hasColumn('book', 'event_id') || !Schema::hasColumn('book', 'floor_plan_id')) {
            $this->error('Columns event_id or floor_plan_id do not exist in book table!');
            $this->info('Please run the migration first: php artisan migrate');
            return 1;
        }
        
        // Get bookings that need backfilling
        $bookings = Book::where(function($query) {
            $query->whereNull('event_id')
                  ->orWhereNull('floor_plan_id');
        })->get();
        
        if ($bookings->isEmpty()) {
            $this->info('✅ No bookings need backfilling. All bookings already have event_id and floor_plan_id.');
            return 0;
        }
        
        $this->info("Found {$bookings->count()} booking(s) that need backfilling.");
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        $updated = 0;
        $skipped = 0;
        $errors = 0;
        
        $progressBar = $this->output->createProgressBar($bookings->count());
        $progressBar->start();
        
        foreach ($bookings as $booking) {
            try {
                $boothIds = json_decode($booking->boothid, true);
                
                if (empty($boothIds) || !is_array($boothIds)) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                // Get first booth to determine floor plan and event
                $firstBoothId = $boothIds[0];
                $booth = Booth::find($firstBoothId);
                
                if (!$booth || !$booth->floor_plan_id) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                $floorPlan = FloorPlan::find($booth->floor_plan_id);
                
                if (!$floorPlan) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                if (!$dryRun) {
                    $booking->event_id = $floorPlan->event_id;
                    $booking->floor_plan_id = $floorPlan->id;
                    $booking->save();
                }
                
                $updated++;
                $progressBar->advance();
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error processing booking ID {$booking->id}: " . $e->getMessage());
                $progressBar->advance();
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        if ($dryRun) {
            $this->info("DRY RUN RESULTS:");
            $this->table(
                ['Status', 'Count'],
                [
                    ['Would Update', $updated],
                    ['Would Skip', $skipped],
                    ['Errors', $errors],
                ]
            );
            $this->info('Run without --dry-run to apply changes.');
        } else {
            $this->info("✅ Backfill completed!");
            $this->table(
                ['Status', 'Count'],
                [
                    ['Updated', $updated],
                    ['Skipped', $skipped],
                    ['Errors', $errors],
                ]
            );
        }
        
        return 0;
    }
}
