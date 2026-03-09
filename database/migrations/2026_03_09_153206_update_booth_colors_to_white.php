<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update available booths to use system default (null colors)
        \App\Models\Booth::where('status', 1)
            ->whereNull('client_id')
            ->update([
                'background_color' => null,
                'border_color' => null,
                'text_color' => null,
            ]);

        // Update 'Available' status setting to white
        \App\Models\BoothStatusSetting::where('status_code', 1)
            ->update([
                'status_color' => '#ffffff',
                'text_color' => '#000000',
                'border_color' => '#007bff',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
