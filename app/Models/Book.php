<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

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
        'event_id',
        'floor_plan_id',
        'clientid',
        'boothid',
        'date_book',
        'userid',
        'affiliate_user_id',
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
     * Get the affiliate user (sales person whose link was used)
     */
    public function affiliateUser()
    {
        return $this->belongsTo(User::class, 'affiliate_user_id');
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
    
    /**
     * Get the floor plan this booking belongs to
     */
    public function floorPlan()
    {
        return $this->belongsTo(FloorPlan::class, 'floor_plan_id');
    }
    
    /**
     * Get the event/project this booking belongs to
     */
    public function event()
    {
        // Check if events table exists before trying to use relationship
        if (\Schema::hasTable('events') && $this->event_id) {
            try {
                return $this->belongsTo(Event::class, 'event_id');
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
}
