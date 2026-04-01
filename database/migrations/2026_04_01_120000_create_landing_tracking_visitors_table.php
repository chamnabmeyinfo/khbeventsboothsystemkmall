<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_tracking_visitors', function (Blueprint $table) {
            $table->id();
            $table->uuid('visitor_uuid')->unique();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->ipAddress('first_ip_address')->nullable();
            $table->ipAddress('last_ip_address')->nullable();
            $table->text('first_user_agent')->nullable();
            $table->text('last_user_agent')->nullable();
            $table->text('first_referrer_url')->nullable();
            $table->text('last_referrer_url')->nullable();
            $table->string('first_utm_source')->nullable();
            $table->string('first_utm_medium')->nullable();
            $table->string('first_utm_campaign')->nullable();
            $table->string('first_utm_term')->nullable();
            $table->string('first_utm_content')->nullable();
            $table->string('last_utm_source')->nullable();
            $table->string('last_utm_medium')->nullable();
            $table->string('last_utm_campaign')->nullable();
            $table->string('last_utm_term')->nullable();
            $table->string('last_utm_content')->nullable();
            $table->timestamps();

            $table->index('last_seen_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_tracking_visitors');
    }
};
