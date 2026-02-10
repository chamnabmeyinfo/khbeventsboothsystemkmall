<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores browser push subscriptions (Web Push) per user for sending notifications.
     */
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('endpoint', 1024);
            $table->string('endpoint_hash', 64)->index(); // for unique key (endpoint can exceed index length)
            $table->string('public_key', 256)->nullable(); // p256dh
            $table->string('auth_token', 256)->nullable(); // auth
            $table->string('content_encoding', 32)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'endpoint_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
