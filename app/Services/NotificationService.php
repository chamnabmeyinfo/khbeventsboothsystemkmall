<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a notification for a specific user or all admins
     */
    public static function create($type, $title, $message, $userId = null, $clientId = null, $bookingId = null, $link = null)
    {
        try {
            return Notification::create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'user_id' => $userId,
                'client_id' => $clientId,
                'booking_id' => $bookingId,
                'link' => $link,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Notify all admins about an action
     */
    public static function notifyAdmins($type, $title, $message, $clientId = null, $bookingId = null, $link = null)
    {
        try {
            $admins = User::where('type', 1)->where('status', 1)->get();
            
            foreach ($admins as $admin) {
                self::create($type, $title, $message, $admin->id, $clientId, $bookingId, $link);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to notify admins: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify user about booth action
     */
    public static function notifyBoothAction($action, $booth, $userId = null)
    {
        $user = $userId ? User::find($userId) : Auth::user();
        $actionUser = Auth::user();
        
        $messages = [
            'created' => "Booth #{$booth->booth_number} has been created by {$actionUser->username}.",
            'updated' => "Booth #{$booth->booth_number} has been updated by {$actionUser->username}.",
            'deleted' => "Booth #{$booth->booth_number} has been deleted by {$actionUser->username}.",
            'status_changed' => "Booth #{$booth->booth_number} status changed to {$booth->getStatusLabel()} by {$actionUser->username}.",
        ];

        $title = "Booth " . ucfirst($action);
        $message = $messages[$action] ?? "Booth #{$booth->booth_number} action: {$action}";
        $link = route('booths.show', $booth->id);

        // Notify the user who owns the booth (if different from action user)
        if ($user && $user->id !== $actionUser->id) {
            self::create('booking', $title, $message, $user->id, null, null, $link);
        }

        // Notify admins
        self::notifyAdmins('booking', $title, $message, null, null, $link);
    }

    /**
     * Notify user about client action
     */
    public static function notifyClientAction($action, $client, $userId = null)
    {
        $actionUser = Auth::user();
        
        $messages = [
            'created' => "Client '{$client->name}' has been created by {$actionUser->username}.",
            'updated' => "Client '{$client->name}' has been updated by {$actionUser->username}.",
            'deleted' => "Client '{$client->name}' has been deleted by {$actionUser->username}.",
        ];

        $title = "Client " . ucfirst($action);
        $message = $messages[$action] ?? "Client '{$client->name}' action: {$action}";
        $link = route('clients.show', $client->id);

        // Notify the user who created/updated the client
        if ($userId) {
            self::create('system', $title, $message, $userId, $client->id, null, $link);
        }

        // Notify admins
        self::notifyAdmins('system', $title, $message, $client->id, null, $link);
    }

    /**
     * Notify user about booking action
     */
    public static function notifyBookingAction($action, $booking, $userId = null)
    {
        $actionUser = Auth::user();
        $targetUser = $userId ? User::find($userId) : $booking->user;
        
        $messages = [
            'created' => "New booking #{$booking->id} has been created by {$actionUser->username}.",
            'updated' => "Booking #{$booking->id} has been updated by {$actionUser->username}.",
            'deleted' => "Booking #{$booking->id} has been deleted by {$actionUser->username}.",
            'confirmed' => "Booking #{$booking->id} has been confirmed by {$actionUser->username}.",
            'cancelled' => "Booking #{$booking->id} has been cancelled by {$actionUser->username}.",
        ];

        $title = "Booking " . ucfirst($action);
        $message = $messages[$action] ?? "Booking #{$booking->id} action: {$action}";
        $link = route('books.show', $booking->id);

        // Notify the booking owner
        if ($targetUser) {
            self::create('booking', $title, $message, $targetUser->id, $booking->client_id, $booking->id, $link);
        }

        // Notify admins
        self::notifyAdmins('booking', $title, $message, $booking->client_id, $booking->id, $link);
    }

    /**
     * Notify about booth status change
     */
    public static function notifyBoothStatusChange($booth, $oldStatus, $newStatus)
    {
        $actionUser = Auth::user();
        $boothOwner = $booth->user;
        
        $statusLabels = [
            1 => 'Available',
            2 => 'Confirmed',
            3 => 'Reserved',
            4 => 'Hidden',
            5 => 'Paid',
        ];

        $oldLabel = $statusLabels[$oldStatus] ?? 'Unknown';
        $newLabel = $statusLabels[$newStatus] ?? 'Unknown';

        $title = "Booth Status Changed";
        $message = "Booth #{$booth->booth_number} status changed from {$oldLabel} to {$newLabel} by {$actionUser->username}.";
        $link = route('booths.show', $booth->id);

        // Notify the booth owner
        if ($boothOwner && $boothOwner->id !== $actionUser->id) {
            self::create('booking', $title, $message, $boothOwner->id, $booth->client_id, null, $link);
        }

        // Notify admins
        self::notifyAdmins('booking', $title, $message, $booth->client_id, null, $link);
    }

    /**
     * Notify about payment received
     */
    public static function notifyPaymentReceived($booth, $amount)
    {
        $actionUser = Auth::user();
        $boothOwner = $booth->user;
        
        $title = "Payment Received";
        $message = "Payment of $" . number_format($amount, 2) . " received for Booth #{$booth->booth_number} by {$actionUser->username}.";
        $link = route('booths.show', $booth->id);

        // Notify the booth owner
        if ($boothOwner) {
            self::create('payment', $title, $message, $boothOwner->id, $booth->client_id, null, $link);
        }

        // Notify admins
        self::notifyAdmins('payment', $title, $message, $booth->client_id, null, $link);
    }
}
