<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'client';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'name',
        'sex',
        'position',
        'company',
        'phone_number',
        'email',
        'address',
        'tax_id',
        'website',
        'notes',
        'avatar',
        'cover_image',
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
        return $this->hasMany(Book::class, 'clientid');
    }
}
