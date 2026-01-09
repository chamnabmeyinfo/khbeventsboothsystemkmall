<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sex',
        'position',
        'company',
        'phone_number',
    ];

    /**
     * Get booths assigned to this client
     */
    public function booths()
    {
        return $this->hasMany(Booth::class, 'client_id');
    }

    /**
     * Get bookings for this client
     */
    public function books()
    {
        return $this->hasMany(Book::class, 'client_id');
    }
}
