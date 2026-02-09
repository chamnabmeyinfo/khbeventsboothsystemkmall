<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * @property-read Client|null $client
 */
class Booth extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'booth';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'floor_plan_id',
        'booth_number',
        'type',
        'price',
        'status',
        'client_id',
        'userid',
        'bookid',
        'category_id',
        'sub_category_id',
        'asset_id',
        'booth_type_id',
        'position_x',
        'position_y',
        'width',
        'height',
        'rotation',
        'z_index',
        'font_size',
        'border_width',
        'border_radius',
        'opacity',
        // Appearance properties
        'background_color',
        'border_color',
        'text_color',
        'font_weight',
        'font_family',
        'text_align',
        'box_shadow',
        // Booth information fields
        'booth_image',
        'description',
        'features',
        'capacity',
        'area_sqm',
        'electricity_power',
        'notes',
        // Payment tracking fields
        'deposit_amount',
        'deposit_paid',
        'balance_due',
        'balance_paid',
        'payment_due_date',
        'deposit_paid_date',
        'balance_paid_date',
        'payment_status',
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
        return $this->belongsTo(User::class, 'userid');
    }

    /**
     * Get the booking record
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'bookid');
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
     * Get the floor plan that owns this booth
     */
    public function floorPlan()
    {
        return $this->belongsTo(FloorPlan::class, 'floor_plan_id');
    }

    /**
     * Get all images for this booth
     */
    public function images()
    {
        return $this->hasMany(BoothImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image for this booth
     */
    public function primaryImage()
    {
        return $this->hasOne(BoothImage::class)->where('is_primary', true);
    }

    /**
     * Get booking timeline for this booth
     */
    public function timeline()
    {
        return $this->hasMany(BookingTimeline::class)->orderBy('created_at', 'desc');
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
     * Get status label (uses custom settings if available, filtered by floor plan)
     */
    public function getStatusLabel(): string
    {
        $floorPlanId = $this->floor_plan_id;
        $statusSetting = BoothStatusSetting::getByCode($this->status, $floorPlanId);
        if ($statusSetting) {
            return $statusSetting->status_name;
        }

        // Fallback to hardcoded values
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_RESERVED => 'Reserved',
            self::STATUS_HIDDEN => 'Hidden',
            self::STATUS_PAID => 'Paid',
            default => 'Unknown',
        };
    }

    /**
     * Get status badge color (uses custom settings if available, filtered by floor plan)
     */
    public function getStatusColor(): string
    {
        $floorPlanId = $this->floor_plan_id;
        $statusSetting = BoothStatusSetting::getByCode($this->status, $floorPlanId);
        if ($statusSetting && $statusSetting->badge_color) {
            return $statusSetting->badge_color;
        }

        // Fallback to hardcoded values
        return match ($this->status) {
            self::STATUS_AVAILABLE => 'success',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_RESERVED => 'warning',
            self::STATUS_HIDDEN => 'secondary',
            self::STATUS_PAID => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Get status colors array (background, border, text) - filtered by floor plan
     */
    public function getStatusColors(): array
    {
        $floorPlanId = $this->floor_plan_id;
        $statusSetting = BoothStatusSetting::getByCode($this->status, $floorPlanId);
        if ($statusSetting) {
            return [
                'background' => $statusSetting->status_color,
                'border' => $statusSetting->border_color ?? $statusSetting->status_color,
                'text' => $statusSetting->text_color,
            ];
        }

        // Fallback to hardcoded values
        return match ($this->status) {
            self::STATUS_AVAILABLE => ['background' => '#28a745', 'border' => '#28a745', 'text' => '#ffffff'],
            self::STATUS_CONFIRMED => ['background' => '#0dcaf0', 'border' => '#0dcaf0', 'text' => '#ffffff'],
            self::STATUS_RESERVED => ['background' => '#ffc107', 'border' => '#ffc107', 'text' => '#333333'],
            self::STATUS_HIDDEN => ['background' => '#6c757d', 'border' => '#6c757d', 'text' => '#ffffff'],
            self::STATUS_PAID => ['background' => '#212529', 'border' => '#212529', 'text' => '#ffffff'],
            default => ['background' => '#6c757d', 'border' => '#6c757d', 'text' => '#ffffff'],
        };
    }

    /**
     * Boot method to add model event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Before creating, check for duplicate booth_number within the same floor plan (floor-plan-specific)
        static::creating(function ($booth) {
            $query = self::where('booth_number', $booth->booth_number);

            // Filter by floor_plan_id if specified (floor-plan-specific uniqueness)
            if (! empty($booth->floor_plan_id)) {
                $query->where('floor_plan_id', $booth->floor_plan_id);
            } else {
                // If no floor_plan_id specified, check globally (backward compatibility)
                // This allows booths without floor_plan_id to still work
            }

            $existing = $query->first();
            if ($existing) {
                $message = ! empty($booth->floor_plan_id)
                    ? 'This booth number already exists in this floor plan. Please choose a different number.'
                    : 'This booth number already exists. Please choose a different number.';

                throw ValidationException::withMessages([
                    'booth_number' => $message,
                ]);
            }
        });

        // Before updating, check for duplicate booth_number within the same floor plan (excluding current record)
        static::updating(function ($booth) {
            $query = self::where('booth_number', $booth->booth_number)
                ->where('id', '!=', $booth->id);

            // Filter by floor_plan_id if specified (floor-plan-specific uniqueness)
            if (! empty($booth->floor_plan_id)) {
                $query->where('floor_plan_id', $booth->floor_plan_id);
            }

            $existing = $query->first();
            if ($existing) {
                $message = ! empty($booth->floor_plan_id)
                    ? 'This booth number already exists in this floor plan. Please choose a different number.'
                    : 'This booth number already exists. Please choose a different number.';

                throw ValidationException::withMessages([
                    'booth_number' => $message,
                ]);
            }
        });
    }

    /**
     * Check if a booth number already exists (floor-plan-specific)
     */
    public static function numberExists(string $boothNumber, ?int $excludeId = null, ?int $floorPlanId = null): bool
    {
        $query = self::where('booth_number', $boothNumber);

        // Filter by floor_plan_id if specified (floor-plan-specific uniqueness)
        if ($floorPlanId !== null) {
            $query->where('floor_plan_id', $floorPlanId);
        }

        // Exclude current booth when checking for updates
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
