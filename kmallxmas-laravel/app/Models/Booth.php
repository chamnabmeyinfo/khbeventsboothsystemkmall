<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booth extends Model
{
    use HasFactory;

    protected $fillable = [
        'booth_number',
        'type',
        'price',
        'status',
        'client_id',
        'user_id',
        'book_id',
        'category_id',
        'sub_category_id',
        'asset_id',
        'booth_type_id',
    ];

    // Status constants
    const STATUS_AVAILABLE = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_RESERVED = 3;
    const STATUS_HIDDEN = 4;
    const STATUS_PAID = 5;

    /**
     * Get the client that owns this booth
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get the user who booked this booth
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the booking record
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the sub-category
     */
    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    /**
     * Get the asset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Get the booth type
     */
    public function boothType()
    {
        return $this->belongsTo(BoothType::class, 'booth_type_id');
    }

    /**
     * Check if booth is available
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Check if booth is reserved
     */
    public function isReserved(): bool
    {
        return $this->status === self::STATUS_RESERVED;
    }

    /**
     * Check if booth is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_HIDDEN => 'Hidden',
            self::STATUS_PAID => 'Paid',
            default => 'Unknown',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'success',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_RESERVED => 'warning',
            self::STATUS_HIDDEN => 'secondary',
            self::STATUS_PAID => 'primary',
            default => 'secondary',
        };
    }
}
