<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('industry')->nullable();
            $table->string('headline')->nullable();
            $table->longText('html_content');
            $table->longText('css_content')->nullable();
            $table->longText('js_content')->nullable();
            $table->string('redirect_url')->default('/login');
            $table->string('show_once_mode')->default('cookie_once');
            $table->boolean('allow_inline_scripts')->default(false);
            $table->unsignedInteger('priority')->default(100);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'is_published']);
            $table->index(['industry', 'is_published']);
            $table->index(['priority', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
