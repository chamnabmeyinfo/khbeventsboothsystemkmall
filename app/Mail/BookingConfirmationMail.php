<?php

namespace App\Mail;

use App\Models\Book;
use App\Models\Booth;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $booth;
    public $client;
    public $event;

    /**
     * Create a new message instance.
     */
    public function __construct(Book $booking)
    {
        $this->booking = $booking;
        $this->booth = $booking->booth;
     $this->client = $booking->client;
     $this->event = $booking->event;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $boothNumber = $this->booth ? $this->booth->booth_number : 'N/A';
        $eventName = $this->event ? $this->event->name : 'Event';

        return new Envelope(
            subject: "✅ Booking Confirmation - Booth {$boothNumber} at {$eventName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking,
              'booth' => $this->booth,
                'client' => $this->client,
                'event' => $this->event,
            'bookingDate' => $this->booking->date_book,
                'totalAmount' => $this->booking->total_amount ?? 0,
                'paidAmount' => $this->booking->paid_amount ?? 0,
             'balanceAmount' => $this->booking->balance_amount ?? 0,
                'paymentDueDate' => $this->booking->payment_due_date,
                'status' => $this->getStatusLabel($this->booking->status),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
      $attachments = [];

        // Generate .ics calendar file if event has dates
        if ($this->event && $this->event->start_date) {
            $icsContent = $this->generateIcsFile();
            
      $attachments[] = Attachment::fromData(fn () => $icsContent, 'booking-event.ics')
         ->withMime('text/calendar');
        }

        return $attachments;
    }

    /**
     * Generate ICS calendar file content
     */
    private function generateIcsFile(): string
    {
        $boothNumber = $this->booth ? $this->booth->booth_number : 'N/A';
        $eventName = $this->event ? $this->event->name : 'Event';
        $eventLocation = $this->event ? ($this->event->location ?? 'TBD') : 'TBD';
        
        // Format dates for ICS (YYYYMMDDTHHMMSSZ)
        $startDate = $this->event && $this->event->start_date 
            ? \Carbon\Carbon::parse($this->event->start_date)->format('Ymd\THis\Z')
            : \Carbon\Carbon::now()->format('Ymd\THis\Z');
            
        $endDate = $this->event && $this->event->end_date 
            ? \Carbon\Carbon::parse($this->event->end_date)->format('Ymd\THis\Z')
            : \Carbon\Carbon::now()->addDays(1)->format('Ymd\THis\Z');
        $timestamp = \Carbon\Carbon::now()->format('Ymd\THis\Z');
        $uid = 'booking-' . $this->booking->id . '@khbevents.com';

        $description = "Your booth booking has been confirmed!\\n\\n";
        $description .= "Booth Number: {$boothNumber}\\n";
        $description .= "Event: {$eventName}\\n";
        if ($this->booking->total_amount) {
            $description .= "Total Amount: $" . number_format($this->booking->total_amount, 2) . "\\n";
        }
        $description .= "\nThank you for booking with KHB Events!";

        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//KHB Events//Booth Booking System//EN\r\n";
        $ics .= "CALSCALE:GREGORIAN\r\n";
        $ics .= "METHOD:REQUEST\r\n";
        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "UID:{$uid}\r\n";
        $ics .= "DTSTAMP:{$timestamp}\r\n";
        $ics .= "DTSTART:{$startDate}\r\n";
        $ics .= "DTEND:{$endDate}\r\n";
        $ics .= "SUMMARY:Booth {$boothNumber} - {$eventName}\r\n";
        $ics .= "DESCRIPTION:{$description}\r\n";
        $ics .= "LOCATION:{$eventLocation}\r\n";
        $ics .= "STATUS:CONFIRMED\r\n";
        $ics .= "SEQUENCE:0\r\n";
        $ics .= "BEGIN:VALARM\r\n";
        $ics .= "TRIGGER:-P1D\r\n";
        $ics .= "ACTION:DISPLAY\r\n";
        $ics .= "DESCRIPTION:Reminder: Event tomorrow\r\n";
        $ics .= "END:VALARM\r\n";
        $ics .= "END:VEVENT\r\n";
      $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }

    /**
     * Get human-readable status label
     */
    private function getStatusLabel($status): string
    {
        return match($status) {
          Book::STATUS_PENDING => 'Pending',
            Book::STATUS_CONFIRMED => 'Confirmed',
            Book::STATUS_RESERVED => 'Reserved',
            Book::STATUS_PAID => 'Paid',
          Book::STATUS_PARTIALLY_PAID => 'Partially Paid',
            Book::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }
}
