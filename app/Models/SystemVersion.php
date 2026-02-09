<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemVersion extends Model
{
    protected $table = 'system_versions';

    protected $fillable = [
        'version',
        'released_at',
        'summary',
        'changelog',
        'is_current',
    ];

    protected $casts = [
        'released_at' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Scope: only the current/live version.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope: order by release date descending (newest first).
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('released_at', 'desc')->orderBy('id', 'desc');
    }
}
