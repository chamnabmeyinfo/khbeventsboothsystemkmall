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
        Schema::table('booth', function (Blueprint $table) {
            // Add unique index to booth_number if it doesn't exist
            // First, check if there are any duplicates and handle them
            $duplicates = DB::table('booth')
                ->select('booth_number', DB::raw('COUNT(*) as count'))
                ->groupBy('booth_number')
                ->having('count', '>', 1)
                ->get();
            
            // If duplicates exist, append a suffix to make them unique
            foreach ($duplicates as $duplicate) {
                $booths = DB::table('booth')
                    ->where('booth_number', $duplicate->booth_number)
                    ->orderBy('id')
                    ->get();
                
                // Keep the first one, rename the rest
                $counter = 1;
                foreach ($booths as $index => $booth) {
                    if ($index > 0) {
                        $newNumber = $duplicate->booth_number . '-dup' . $counter;
                        // Make sure the new number is also unique
                        while (DB::table('booth')->where('booth_number', $newNumber)->exists()) {
                            $counter++;
                            $newNumber = $duplicate->booth_number . '-dup' . $counter;
                        }
                        DB::table('booth')
                            ->where('id', $booth->id)
                            ->update(['booth_number' => $newNumber]);
                        $counter++;
                    }
                }
            }
            
            // Now add the unique index
            $table->unique('booth_number', 'booth_booth_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            $table->dropUnique('booth_booth_number_unique');
        });
    }
};
