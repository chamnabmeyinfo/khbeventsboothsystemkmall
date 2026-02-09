<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('booking_status_settings')) {
            Schema::create('booking_status_settings', function (Blueprint $table) {
                $table->id();
                $table->integer('status_code')->unique()->comment('Status code (1, 2, 3, 4, 5, etc.)');
                $table->string('status_name', 100)->comment('Display name (e.g., Pending, Confirmed, Paid)');
                $table->string('status_color', 50)->default('#6c757d')->comment('Background color (hex)');
                $table->string('border_color', 50)->nullable()->comment('Border color (hex)');
                $table->string('text_color', 50)->default('#ffffff')->comment('Text color (hex)');
                $table->string('badge_color', 50)->nullable()->comment('Bootstrap badge color class');
                $table->text('description')->nullable()->comment('Status description');
                $table->boolean('is_active')->default(true)->comment('Whether this status is active');
                $table->integer('sort_order')->default(0)->comment('Display order');
                $table->boolean('is_default')->default(false)->comment('Is this the default status for new bookings');
                $table->timestamps();

                $table->index('status_code');
                $table->index('is_active');
                $table->index('sort_order');
            });

            // Insert default booking statuses
            DB::table('booking_status_settings')->insert([
                [
                    'status_code' => 1,
                    'status_name' => 'Pending',
                    'status_color' => '#6c757d',
                    'border_color' => '#6c757d',
                    'text_color' => '#ffffff',
                    'badge_color' => 'secondary',
                    'description' => 'Booking is pending confirmation',
                    'is_active' => true,
                    'sort_order' => 1,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'status_code' => 2,
                    'status_name' => 'Confirmed',
                    'status_color' => '#0dcaf0',
                    'border_color' => '#0dcaf0',
                    'text_color' => '#ffffff',
                    'badge_color' => 'info',
                    'description' => 'Booking has been confirmed',
                    'is_active' => true,
                    'sort_order' => 2,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'status_code' => 3,
                    'status_name' => 'Reserved',
                    'status_color' => '#ffc107',
                    'border_color' => '#ffc107',
                    'text_color' => '#333333',
                    'badge_color' => 'warning',
                    'description' => 'Booking is reserved',
                    'is_active' => true,
                    'sort_order' => 3,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'status_code' => 4,
                    'status_name' => 'Paid',
                    'status_color' => '#28a745',
                    'border_color' => '#28a745',
                    'text_color' => '#ffffff',
                    'badge_color' => 'success',
                    'description' => 'Payment has been received',
                    'is_active' => true,
                    'sort_order' => 4,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'status_code' => 5,
                    'status_name' => 'Partially Paid',
                    'status_color' => '#17a2b8',
                    'border_color' => '#17a2b8',
                    'text_color' => '#ffffff',
                    'badge_color' => 'info',
                    'description' => 'Partial payment received',
                    'is_active' => true,
                    'sort_order' => 5,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'status_code' => 6,
                    'status_name' => 'Cancelled',
                    'status_color' => '#dc3545',
                    'border_color' => '#dc3545',
                    'text_color' => '#ffffff',
                    'badge_color' => 'danger',
                    'description' => 'Booking has been cancelled',
                    'is_active' => true,
                    'sort_order' => 6,
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_status_settings');
    }
};
