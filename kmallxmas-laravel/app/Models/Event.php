<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\DebugLogger;

class Event extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organizer_id',
        'category_id',
        'location_id',
        'link',
        'title',
        'slug',
        'location_address',
        'short_description',
        'description',
        'start_date',
        'end_date',
        'seats',
        'seats_booked',
        'price',
        'allow_discount',
        'market_price',
        'booking_price',
        'cover_image',
        'type',
        'is_featured',
        'step',
        'status',
        'verification_details',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date:Y-m-d',
            'end_date' => 'date:Y-m-d',
            'price' => 'float',
            'market_price' => 'float',
            'booking_price' => 'float',
            'seats' => 'integer',
            'seats_booked' => 'integer',
            'is_featured' => 'boolean',
            'allow_discount' => 'boolean',
            'status' => 'integer',
        ];
    }

    /**
     * Get a formatted start date or return null for invalid dates
     */
    public function getFormattedStartDateAttribute()
    {
        // #region agent log
        $logData = [
            'event_id' => $this->id ?? null,
            'start_date_value' => $this->start_date ?? null,
            'start_date_type' => gettype($this->start_date ?? null),
            'raw_attributes' => array_key_exists('start_date', $this->getAttributes()) ? $this->getAttributes()['start_date'] : 'NOT_IN_ATTRIBUTES',
        ];
        DebugLogger::log($logData, 'Event.php:84', 'getFormattedStartDateAttribute entry');
        // #endregion
        
        // Get raw attribute value before casting
        $rawAttributes = $this->getAttributes();
        $rawStartDate = $rawAttributes['start_date'] ?? null;
        
        // Check for invalid dates
        if (!$rawStartDate || $rawStartDate === '0000-00-00' || $rawStartDate === '' || $rawStartDate === null) {
            // #region agent log
            DebugLogger::log(['raw_start_date'=>$rawStartDate], 'Event.php:94', 'Invalid date detected, returning N/A');
            // #endregion
            return 'N/A';
        }
        
        try {
            // Try to parse as Carbon if it's a string
            if (is_string($rawStartDate)) {
                $date = \Carbon\Carbon::parse($rawStartDate);
                $formatted = $date->format('M d, Y');
                // #region agent log
                DebugLogger::log(['formatted'=>$formatted], 'Event.php:105', 'Date formatted successfully');
                // #endregion
                return $formatted;
            }
            
            // If it's already a Carbon instance
            if (is_object($this->start_date) && method_exists($this->start_date, 'format')) {
                $formatted = $this->start_date->format('M d, Y');
                // #region agent log
                DebugLogger::log(['formatted'=>$formatted], 'Event.php:114', 'Date is Carbon, formatted');
                // #endregion
                return $formatted;
            }
        } catch (\Exception $e) {
            // #region agent log
            DebugLogger::log(['error'=>$e->getMessage(),'raw_start_date'=>$rawStartDate], 'Event.php:120', 'Exception in date formatting');
            // #endregion
            return 'Invalid Date';
        }
        
        // #region agent log
        DebugLogger::log(['raw_start_date'=>$rawStartDate], 'Event.php:126', 'Falling back to N/A');
        // #endregion
        return 'N/A';
    }

    /**
     * Get a formatted end date or return null for invalid dates
     */
    public function getFormattedEndDateAttribute()
    {
        if (!$this->end_date || $this->end_date === '0000-00-00' || $this->getAttributes()['end_date'] === '0000-00-00') {
            return null;
        }
        
        try {
            if (is_string($this->getAttributes()['end_date'])) {
                $date = \Carbon\Carbon::parse($this->getAttributes()['end_date']);
                return $date->format('M d, Y');
            }
            if (is_object($this->end_date) && method_exists($this->end_date, 'format')) {
                return $this->end_date->format('M d, Y');
            }
        } catch (\Exception $e) {
            return 'Invalid Date';
        }
        
        return 'N/A';
    }

    /**
     * Get the category that owns the event.
     */
    public function category()
    {
        return $this->belongsTo(CategoryEvent::class, 'category_id');
    }

    /**
     * Get available seats
     */
    public function getAvailableSeatsAttribute()
    {
        return max(0, $this->seats - $this->seats_booked);
    }

    /**
     * Check if event is fully booked
     */
    public function isFullyBooked()
    {
        return $this->seats_booked >= $this->seats;
    }

    /**
     * Check if event is active
     */
    public function isActive()
    {
        return $this->status == 1;
    }
}
