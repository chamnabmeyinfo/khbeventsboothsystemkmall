<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_page_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained('landing_pages')->cascadeOnDelete();
            $table->foreignId('landing_tracking_event_id')->nullable()->constrained('landing_tracking_events')->nullOnDelete();
            $table->foreignId('visitor_id')->nullable()->constrained('landing_tracking_visitors')->nullOnDelete();
            $table->uuid('session_uuid')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('preferred_trip_date', 500)->nullable();
            $table->string('locale', 32)->nullable();
            $table->string('source')->nullable();
            $table->json('meta')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referrer_url')->nullable();
            $table->timestamps();

            $table->index(['landing_page_id', 'created_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_bookings');
    }
};
