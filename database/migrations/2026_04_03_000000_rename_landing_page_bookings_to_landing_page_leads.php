<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Landing-page marketing leads are stored separately from floor-plan / booth "bookings" (Book model).
     */
    public function up(): void
    {
        if (Schema::hasTable('landing_page_bookings') && ! Schema::hasTable('landing_page_leads')) {
            Schema::rename('landing_page_bookings', 'landing_page_leads');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('landing_page_leads') && ! Schema::hasTable('landing_page_bookings')) {
            Schema::rename('landing_page_leads', 'landing_page_bookings');
        }
    }
};
