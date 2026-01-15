<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTimeline extends Model
{
    use HasFactory;

    protected $table = 'booking_timeline';

    protected $fillable = [
        'booking_id',
        'booth_id',
        'action',
        'details',
        'user_id',
        'amount',
        'old_status',
        'new_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the booking
     */
    public function booking()
    {
        return $this->belongsTo(Book::class, 'booking_id');
    }

    /**
     * Get the booth
     */
    public function booth()
    {
        return $this->belongsTo(Booth::class);
    }

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get action label
     */
    public function getActionLabelAttribute()
    {
        $labels = [
            'created' => 'Booking Created',
            'reserved' => 'Reserved',
            'confirmed' => 'Confirmed',
            'deposit_paid' => 'Deposit Paid',
            'balance_paid' => 'Balance Paid',
            'fully_paid' => 'Fully Paid',
            'cancelled' => 'Cancelled',
            'modified' => 'Modified',
            'status_changed' => 'Status Changed',
        ];
        
        return $labels[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    /**
     * Get icon for action
     */
    public function getActionIconAttribute()
    {
        $icons = [
            'created' => 'fa-plus-circle',
            'reserved' => 'fa-bookmark',
            'confirmed' => 'fa-check-circle',
            'deposit_paid' => 'fa-dollar-sign',
            'balance_paid' => 'fa-money-bill-wave',
            'fully_paid' => 'fa-check-double',
            'cancelled' => 'fa-times-circle',
            'modified' => 'fa-edit',
            'status_changed' => 'fa-exchange-alt',
        ];
        
        return $icons[$this->action] ?? 'fa-circle';
    }

    /**
     * Get color for action
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'created' => 'info',
            'reserved' => 'warning',
            'confirmed' => 'primary',
            'deposit_paid' => 'success',
            'balance_paid' => 'success',
            'fully_paid' => 'success',
            'cancelled' => 'danger',
            'modified' => 'secondary',
            'status_changed' => 'info',
        ];
        
        return $colors[$this->action] ?? 'secondary';
    }

    /**
     * Create a timeline entry
     */
    public static function createEntry($boothId, $action, $details = null, $amount = null, $bookingId = null, $oldStatus = null, $newStatus = null)
    {
        return self::create([
            'booking_id' => $bookingId,
            'booth_id' => $boothId,
            'action' => $action,
            'details' => $details,
            'user_id' => auth()->id(),
            'amount' => $amount,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);
    }
}
