<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveDuplicateClients extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:remove-duplicates 
                            {--field= : Field to check for duplicates (phone_2, email_1, email_2, all)}
                            {--dry-run : Preview changes without deleting}
                            {--keep-oldest : Keep the oldest record (by ID), otherwise keeps newest}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate clients based on phone_2, email_1, or email_2 fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $field = $this->option('field') ?: 'all';
        $dryRun = $this->option('dry-run');
        $keepOldest = $this->option('keep-oldest');

        $this->info('🔍 Searching for duplicate clients...');
        $this->newLine();

        $fieldsToCheck = [];
        if ($field === 'all') {
            $fieldsToCheck = ['phone_2', 'email_1', 'email_2'];
        } else {
            $fieldsToCheck = [$field];
        }

        $totalDeleted = 0;
        $totalMerged = 0;

        foreach ($fieldsToCheck as $checkField) {
            $this->info("📋 Checking duplicates for field: <fg=cyan>{$checkField}</>");

            // Find duplicates - groups of clients with the same non-null value
            $duplicates = Client::select($checkField, DB::raw('COUNT(*) as count'))
                ->whereNotNull($checkField)
                ->where($checkField, '!=', '')
                ->groupBy($checkField)
                ->having('count', '>', 1)
                ->get();

            if ($duplicates->isEmpty()) {
                $this->comment("   ✅ No duplicates found for {$checkField}");
                $this->newLine();

                continue;
            }

            $this->comment('   Found '.$duplicates->count().' duplicate groups');

            foreach ($duplicates as $duplicate) {
                $value = $duplicate->$checkField;
                $count = $duplicate->count;

                // Get all clients with this duplicate value
                $clients = Client::where($checkField, $value)
                    ->orderBy('id', $keepOldest ? 'asc' : 'desc')
                    ->get();

                // Keep the first one (oldest or newest based on option)
                $keepClient = $clients->first();
                $clientsToDelete = $clients->skip(1);

                $this->line("   📌 Value: <fg=yellow>{$value}</> - Found {$count} clients");
                $this->line("      Keeping: Client ID {$keepClient->id} ({$keepClient->name})");

                foreach ($clientsToDelete as $clientToDelete) {
                    if ($dryRun) {
                        $this->warn("      [DRY RUN] Would delete: Client ID {$clientToDelete->id} ({$clientToDelete->name})");
                    } else {
                        // Before deleting, merge any important data that might be missing in the kept client
                        $this->mergeClientData($keepClient, $clientToDelete);

                        // Delete the duplicate
                        $clientToDelete->delete();
                        $this->comment("      ✅ Deleted: Client ID {$clientToDelete->id} ({$clientToDelete->name})");
                        $totalDeleted++;
                    }
                }

                if (! $dryRun) {
                    $totalMerged++;
                }
            }

            $this->newLine();
        }

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No changes were made');
            $this->info('💡 Run without --dry-run to apply changes');
        } else {
            $this->info('✅ Process completed!');
            $this->info("   - Duplicate groups merged: {$totalMerged}");
            $this->info("   - Clients deleted: {$totalDeleted}");
        }

        return 0;
    }

    /**
     * Merge data from duplicate client into the kept client
     * Fills in missing fields in the kept client with data from the duplicate
     */
    private function mergeClientData(Client $keepClient, Client $duplicateClient)
    {
        $fieldsToMerge = [
            'name', 'sex', 'position', 'company', 'company_name_khmer',
            'phone_number', 'phone_1', 'phone_2',
            'email', 'email_1', 'email_2',
            'address', 'tax_id', 'website', 'notes',
        ];

        $updated = false;
        foreach ($fieldsToMerge as $field) {
            // If kept client field is empty/null and duplicate has a value, use duplicate's value
            if (empty($keepClient->$field) && ! empty($duplicateClient->$field)) {
                $keepClient->$field = $duplicateClient->$field;
                $updated = true;
            }
        }

        if ($updated) {
            $keepClient->save();
        }
    }
}
