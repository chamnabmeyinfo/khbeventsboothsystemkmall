<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'book';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'clientid',
        'boothid',
        'date_book',
        'userid',
        'type',
    ];

    protected $casts = [
        'date_book' => 'datetime',
    ];

    /**
     * Get the client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'clientid');
    }

    /**
     * Get the user who made the booking
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    /**
     * Get booths in this booking (boothid is stored as JSON string)
     */
    public function booths()
    {
        $boothIds = json_decode($this->boothid, true) ?? [];
        if (empty($boothIds)) {
            return collect([]);
        }
        return Booth::whereIn('id', $boothIds)->get();
    }
}
