<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->boolean('use_visual_builder')->default(false)->after('allow_inline_scripts');
            $table->string('template_key')->nullable()->after('use_visual_builder');
            $table->json('visual_content')->nullable()->after('template_key');
        });
    }

    public function down(): void
    {
        Schema::table('landing_pages', function (Blueprint $table) {
            $table->dropColumn(['use_visual_builder', 'template_key', 'visual_content']);
        });
    }
};
