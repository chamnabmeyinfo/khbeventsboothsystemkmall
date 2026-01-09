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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45)->default('N/A');
            $table->tinyInteger('sex')->nullable()->comment('1=Male, 2=Female');
            $table->string('position', 191)->default('N/A');
            $table->string('company', 191)->default('N/A');
            $table->string('phone_number', 15)->default('N/A');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
