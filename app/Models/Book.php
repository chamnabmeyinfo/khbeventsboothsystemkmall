<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'booth_ids',
        'date_book',
        'user_id',
        'type',
    ];

    protected $casts = [
        'booth_ids' => 'array',
        'date_book' => 'datetime',
    ];

    /**
     * Get the client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get the user who made the booking
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get booths in this booking
     */
    public function booths()
    {
        return Booth::whereIn('id', $this->booth_ids ?? [])->get();
    }
}
