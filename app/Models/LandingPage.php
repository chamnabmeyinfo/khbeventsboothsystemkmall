<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'industry',
        'headline',
        'html_content',
        'css_content',
        'js_content',
        'redirect_url',
        'show_once_mode',
        'allow_inline_scripts',
        'use_visual_builder',
        'template_key',
        'visual_content',
        'priority',
        'is_active',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'allow_inline_scripts' => 'boolean',
        'use_visual_builder' => 'boolean',
        'visual_content' => 'array',
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function events(): HasMany
    {
        return $this->hasMany(LandingPageEvent::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public static function getActivePublished(): ?self
    {
        return self::query()
            ->where('is_active', true)
            ->where('is_published', true)
            ->orderBy('priority')
            ->orderByDesc('id')
            ->first();
    }
}
