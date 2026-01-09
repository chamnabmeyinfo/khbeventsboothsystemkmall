<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoothType extends Model
{
    use HasFactory;

    protected $table = 'booth_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * Get booths of this type
     */
    public function booths()
    {
        return $this->hasMany(Booth::class, 'booth_type_id');
    }
}
