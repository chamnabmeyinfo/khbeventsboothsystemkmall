<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_page_section_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('layout_key', 64);
            $table->string('description', 512)->nullable();
            $table->json('content');
            $table->timestamps();

            $table->index('layout_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_page_section_templates');
    }
};
