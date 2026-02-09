<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'user_id',
        'actor_id',
        'client_id',
        'booking_id',
        'activity_log_id',
        'notifiable_type',
        'notifiable_id',
        'link',
        'is_read',
        'read_at',
        'email_sent',
        'email_sent_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'email_sent' => 'boolean',
        'read_at' => 'datetime',
        'email_sent_at' => 'datetime',
    ];

    /** Recipient of the notification */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** User who performed the action (actor) */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function booking()
    {
        return $this->belongsTo(Book::class, 'booking_id');
    }

    /** Related activity log entry */
    public function activityLog()
    {
        return $this->belongsTo(ActivityLog::class, 'activity_log_id');
    }

    /** Subject of the notification (Booth, Book, Client, etc.) */
    public function notifiable()
    {
        return $this->morphTo('notifiable', 'notifiable_type', 'notifiable_id');
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /** Icon class for UI (Font Awesome) */
    public function getIconAttribute(): string
    {
        return self::iconForType($this->type);
    }

    /** Label for type (e.g. "Booking", "Payment") */
    public function getTypeLabelAttribute(): string
    {
        return self::labelForType($this->type);
    }

    public static function iconForType(?string $type): string
    {
        $map = [
            'booking' => 'fa-calendar-check',
            'payment' => 'fa-money-bill-wave',
            'system' => 'fa-cog',
            'security' => 'fa-shield-alt',
            'hr.leave_request' => 'fa-calendar-times',
            'hr.leave_approved' => 'fa-check-circle',
            'hr.leave_rejected' => 'fa-times-circle',
            'hr.attendance_pending' => 'fa-clock',
            'hr.attendance_approved' => 'fa-check',
            'hr.document_expiring' => 'fa-file-alt',
            'hr.performance_review' => 'fa-star',
            'hr.birthday' => 'fa-birthday-cake',
        ];

        return $map[$type] ?? 'fa-bell';
    }

    public static function labelForType(?string $type): string
    {
        if (! $type) {
            return 'Notification';
        }
        if (str_starts_with($type, 'hr.')) {
            return str_replace(['hr.', '_'], ['', ' '], ucwords($type, '.'));
        }

        return ucfirst($type);
    }
}
