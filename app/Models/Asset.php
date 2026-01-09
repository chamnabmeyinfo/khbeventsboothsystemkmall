<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'asset';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'status',
    ];

    /**
     * Get booths that use this asset
     */
    public function booths()
    {
        return $this->hasMany(Booth::class, 'asset_id');
    }
}
