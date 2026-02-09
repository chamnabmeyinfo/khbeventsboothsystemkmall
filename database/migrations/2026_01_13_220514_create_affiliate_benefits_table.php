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
        if (! Schema::hasTable('affiliate_benefits')) {
            Schema::create('affiliate_benefits', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255); // Benefit name (e.g., "Standard Commission", "Performance Bonus")
                $table->string('type', 50); // 'commission', 'bonus', 'incentive', 'reward'
                $table->enum('calculation_method', ['percentage', 'fixed_amount', 'tiered_percentage', 'tiered_amount'])->default('percentage');

                // Percentage-based benefits
                $table->decimal('percentage', 5, 2)->nullable(); // e.g., 5.00 for 5%

                // Fixed amount benefits
                $table->decimal('fixed_amount', 12, 2)->nullable();

                // Target-based benefits
                $table->decimal('target_revenue', 12, 2)->nullable(); // Revenue target to achieve
                $table->decimal('target_bookings', 10, 0)->nullable(); // Number of bookings target
                $table->decimal('target_clients', 10, 0)->nullable(); // Number of clients target

                // Tiered structure (stored as JSON)
                $table->text('tier_structure')->nullable(); // JSON: [{"min": 0, "max": 10000, "percentage": 5}, ...]

                // Conditions
                $table->unsignedBigInteger('floor_plan_id')->nullable(); // Specific to floor plan
                $table->unsignedBigInteger('user_id')->nullable(); // Specific to user (null = all users)
                $table->date('start_date')->nullable(); // Benefit start date
                $table->date('end_date')->nullable(); // Benefit end date

                // Status and priority
                $table->boolean('is_active')->default(true);
                $table->integer('priority')->default(0); // Higher priority = applied first

                // Additional settings
                $table->text('description')->nullable();
                $table->text('conditions')->nullable(); // Additional conditions (JSON)
                $table->decimal('min_revenue', 12, 2)->nullable(); // Minimum revenue to qualify
                $table->decimal('max_benefit', 12, 2)->nullable(); // Maximum benefit cap

                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('type');
                $table->index('is_active');
                $table->index('user_id');
                $table->index('floor_plan_id');
                $table->index(['is_active', 'priority']);

                // Add foreign keys only if tables exist
                if (Schema::hasTable('floor_plans')) {
                    $table->foreign('floor_plan_id')->references('id')->on('floor_plans')->onDelete('set null');
                }
                // Note: user table foreign keys are added as indexes only to avoid constraint issues
                // The user table may have different structure
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_benefits');
    }
};
