<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSectionTemplate extends Model
{
    protected $fillable = [
        'name',
        'layout_key',
        'description',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];
}
