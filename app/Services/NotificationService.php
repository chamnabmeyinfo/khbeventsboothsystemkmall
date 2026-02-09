<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a notification for a specific user or all admins.
     *
     * @param  int|null  $activityLogId  Related activity_logs.id
     * @param  Model|null  $notifiable  Subject (Booth, Book, Client, etc.)
     * @param  int|null  $actorId  User who performed the action
     */
    public static function create(
        $type,
        $title,
        $message,
        $userId = null,
        $clientId = null,
        $bookingId = null,
        $link = null,
        $activityLogId = null,
        $notifiable = null,
        $actorId = null
    ) {
        try {
            $actorId = $actorId ?? Auth::id();
            $notifiableType = null;
            $notifiableId = null;
            if ($notifiable instanceof Model) {
                $notifiableType = get_class($notifiable);
                $notifiableId = $notifiable->getKey();
            }

            return Notification::create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'user_id' => $userId,
                'actor_id' => $actorId,
                'client_id' => $clientId,
                'booking_id' => $bookingId,
                'activity_log_id' => $activityLogId,
                'notifiable_type' => $notifiableType,
                'notifiable_id' => $notifiableId,
                'link' => $link,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Notify all admins about an action
     */
    public static function notifyAdmins(
        $type,
        $title,
        $message,
        $clientId = null,
        $bookingId = null,
        $link = null,
        $activityLogId = null,
        $notifiable = null,
        $actorId = null
    ) {
        try {
            $admins = User::where('type', 1)->where('status', 1)->get();

            foreach ($admins as $admin) {
                self::create($type, $title, $message, $admin->id, $clientId, $bookingId, $link, $activityLogId, $notifiable, $actorId);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to notify admins: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Notify user about booth action (with optional activity log and notifiable link)
     */
    public static function notifyBoothAction($action, $booth, $userId = null, $activityLogId = null)
    {
        $actionUser = Auth::user();
        $user = $userId ? User::find($userId) : $actionUser;
        $link = $booth ? route('booths.show', $booth->id) : null;

        $messages = [
            'created' => "Booth #{$booth->booth_number} has been created by {$actionUser->username}.",
            'updated' => "Booth #{$booth->booth_number} has been updated by {$actionUser->username}.",
            'deleted' => "Booth #{$booth->booth_number} has been deleted by {$actionUser->username}.",
            'status_changed' => "Booth #{$booth->booth_number} status changed to {$booth->getStatusLabel()} by {$actionUser->username}.",
        ];

        $title = 'Booth '.ucfirst(str_replace('_', ' ', $action));
        $message = $messages[$action] ?? "Booth #{$booth->booth_number} action: {$action}";

        if ($user && $user->id !== $actionUser->id) {
            self::create('booking', $title, $message, $user->id, null, null, $link, $activityLogId, $booth, $actionUser->id);
        }

        self::notifyAdmins('booking', $title, $message, null, null, $link, $activityLogId, $booth, $actionUser->id);
    }

    /**
     * Notify user about client action (with optional activity log and notifiable link)
     */
    public static function notifyClientAction($action, $client, $userId = null, $activityLogId = null)
    {
        $actionUser = Auth::user();
        $link = route('clients.show', $client->id);

        $messages = [
            'created' => "Client '{$client->name}' has been created by {$actionUser->username}.",
            'updated' => "Client '{$client->name}' has been updated by {$actionUser->username}.",
            'deleted' => "Client '{$client->name}' has been deleted by {$actionUser->username}.",
        ];

        $title = 'Client '.ucfirst($action);
        $message = $messages[$action] ?? "Client '{$client->name}' action: {$action}";

        if ($userId) {
            self::create('system', $title, $message, $userId, $client->id, null, $link, $activityLogId, $client, $actionUser->id);
        }

        self::notifyAdmins('system', $title, $message, $client->id, null, $link, $activityLogId, $client, $actionUser->id);
    }

    /**
     * Notify user about booking action (with optional activity log and notifiable link)
     */
    public static function notifyBookingAction($action, $booking, $userId = null, $activityLogId = null)
    {
        $actionUser = Auth::user();
        $targetUser = $userId ? User::find($userId) : $booking->user;
        $link = route('books.show', $booking->id);

        $messages = [
            'created' => "New booking #{$booking->id} has been created by {$actionUser->username}.",
            'updated' => "Booking #{$booking->id} has been updated by {$actionUser->username}.",
            'deleted' => "Booking #{$booking->id} has been deleted by {$actionUser->username}.",
            'status_changed' => "Booking #{$booking->id} status changed by {$actionUser->username}.",
            'confirmed' => "Booking #{$booking->id} has been confirmed by {$actionUser->username}.",
            'cancelled' => "Booking #{$booking->id} has been cancelled by {$actionUser->username}.",
        ];

        $title = 'Booking '.ucfirst(str_replace('_', ' ', $action));
        $message = $messages[$action] ?? "Booking #{$booking->id} action: {$action}";

        if ($targetUser) {
            self::create('booking', $title, $message, $targetUser->id, $booking->client_id, $booking->id, $link, $activityLogId, $booking, $actionUser->id);
        }

        self::notifyAdmins('booking', $title, $message, $booking->client_id, $booking->id, $link, $activityLogId, $booking, $actionUser->id);
    }

    /**
     * Notify about booth status change (with optional activity link)
     */
    public static function notifyBoothStatusChange($booth, $oldStatus, $newStatus, $activityLogId = null)
    {
        $actionUser = Auth::user();
        $boothOwner = $booth->user;
        $link = route('booths.show', $booth->id);

        $statusLabels = [
            1 => 'Available',
            2 => 'Confirmed',
            3 => 'Reserved',
            4 => 'Hidden',
            5 => 'Paid',
        ];

        $oldLabel = $statusLabels[$oldStatus] ?? 'Unknown';
        $newLabel = $statusLabels[$newStatus] ?? 'Unknown';

        $title = 'Booth Status Changed';
        $message = "Booth #{$booth->booth_number} status changed from {$oldLabel} to {$newLabel} by {$actionUser->username}.";

        if ($boothOwner && $boothOwner->id !== $actionUser->id) {
            self::create('booking', $title, $message, $boothOwner->id, $booth->client_id, null, $link, $activityLogId, $booth, $actionUser->id);
        }

        self::notifyAdmins('booking', $title, $message, $booth->client_id, null, $link, $activityLogId, $booth, $actionUser->id);
    }

    /**
     * Notify about payment received (with optional activity link)
     */
    public static function notifyPaymentReceived($booth, $amount, $activityLogId = null)
    {
        $actionUser = Auth::user();
        $boothOwner = $booth->user;
        $link = route('booths.show', $booth->id);

        $title = 'Payment Received';
        $message = 'Payment of $'.number_format($amount, 2)." received for Booth #{$booth->booth_number} by {$actionUser->username}.";

        if ($boothOwner) {
            self::create('payment', $title, $message, $boothOwner->id, $booth->client_id, null, $link, $activityLogId, $booth, $actionUser->id);
        }

        self::notifyAdmins('payment', $title, $message, $booth->client_id, null, $link, $activityLogId, $booth, $actionUser->id);
    }
}
