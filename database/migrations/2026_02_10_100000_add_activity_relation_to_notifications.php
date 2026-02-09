<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_log_id')->nullable()->after('booking_id');
            $table->string('notifiable_type', 100)->nullable()->after('activity_log_id');
            $table->unsignedBigInteger('notifiable_id')->nullable()->after('notifiable_type');
            $table->unsignedBigInteger('actor_id')->nullable()->after('user_id'); // user who performed the action

            $table->index('activity_log_id');
            $table->index(['notifiable_type', 'notifiable_id']);
            $table->foreign('activity_log_id')->references('id')->on('activity_logs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['activity_log_id']);
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropColumn(['activity_log_id', 'notifiable_type', 'notifiable_id', 'actor_id']);
        });
    }
};
