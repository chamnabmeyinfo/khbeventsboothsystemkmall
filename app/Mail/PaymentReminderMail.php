<?php

namespace App\Mail;

use App\Models\Booth;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booth;

    public $reminderType;

    /**
     * Create a new message instance.
     */
    public function __construct(Booth $booth, $reminderType = 'upcoming')
    {
        $this->booth = $booth;
        $this->reminderType = $reminderType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->reminderType) {
            'overdue' => '⚠️ OVERDUE: Payment Reminder for Booth '.$this->booth->booth_number,
            'due_today' => '🔔 Payment Due TODAY for Booth '.$this->booth->booth_number,
            default => '📅 Payment Reminder for Booth '.$this->booth->booth_number,
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
            with: [
                'booth' => $this->booth,
                'reminderType' => $this->reminderType,
                'client' => $this->booth->client,
                'dueDate' => $this->booth->payment_due_date,
                'depositAmount' => $this->booth->deposit_amount,
                'depositPaid' => $this->booth->deposit_paid,
                'balanceDue' => $this->booth->balance_due,
                'balancePaid' => $this->booth->balance_paid,
                'totalDue' => ($this->booth->deposit_amount - $this->booth->deposit_paid) +
                              ($this->booth->balance_due - $this->booth->balance_paid),
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
        return [];
    }
}
