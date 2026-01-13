<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find all booths with "-dup" suffix in booth_number
        $boothsWithDup = DB::table('booth')
            ->where('booth_number', 'LIKE', '%-dup%')
            ->get();

        $cleaned = 0;
        $skipped = 0;

        foreach ($boothsWithDup as $booth) {
            // Extract original booth number (remove "-dup1", "-dup2", etc.)
            $originalNumber = preg_replace('/-dup\d+$/', '', $booth->booth_number);
            
            if ($originalNumber === $booth->booth_number) {
                // No "-dup" pattern found, skip
                continue;
            }

            // Check if original number already exists in the same floor plan
            $conflict = DB::table('booth')
                ->where('booth_number', $originalNumber)
                ->where('floor_plan_id', $booth->floor_plan_id)
                ->where('id', '!=', $booth->id)
                ->exists();

            if (!$conflict) {
                // Safe to remove suffix - no conflict in same floor plan
                DB::table('booth')
                    ->where('id', $booth->id)
                    ->update(['booth_number' => $originalNumber]);
                $cleaned++;
            } else {
                // Conflict exists - check if we can use a different approach
                // Try to find a unique number by appending floor plan identifier
                if ($booth->floor_plan_id) {
                    $floorPlan = DB::table('floor_plans')->where('id', $booth->floor_plan_id)->first();
                    if ($floorPlan) {
                        // Use floor plan abbreviation or ID to make it unique
                        $fpAbbrev = strtoupper(substr($floorPlan->name, 0, 2));
                        $newNumber = $originalNumber . '-' . $fpAbbrev;
                        
                        // Check if this new number is available
                        $newConflict = DB::table('booth')
                            ->where('booth_number', $newNumber)
                            ->where('floor_plan_id', $booth->floor_plan_id)
                            ->where('id', '!=', $booth->id)
                            ->exists();
                        
                        if (!$newConflict) {
                            DB::table('booth')
                                ->where('id', $booth->id)
                                ->update(['booth_number' => $newNumber]);
                            $cleaned++;
                        } else {
                            $skipped++;
                        }
                    } else {
                        $skipped++;
                    }
                } else {
                    // No floor plan - just remove dup suffix if no global conflict
                    $globalConflict = DB::table('booth')
                        ->where('booth_number', $originalNumber)
                        ->whereNull('floor_plan_id')
                        ->where('id', '!=', $booth->id)
                        ->exists();
                    
                    if (!$globalConflict) {
                        DB::table('booth')
                            ->where('id', $booth->id)
                            ->update(['booth_number' => $originalNumber]);
                        $cleaned++;
                    } else {
                        $skipped++;
                    }
                }
            }
        }

        // Log results (will be shown in migration output)
        if ($cleaned > 0 || $skipped > 0) {
            \Log::info("Booth number cleanup: {$cleaned} cleaned, {$skipped} skipped (conflicts)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible
        // We don't want to add back "-dup" suffixes
    }
};
