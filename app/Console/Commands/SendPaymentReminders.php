<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Booth;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:send-reminders 
                            {--days=3 : Number of days before due date to send reminder}
                            {--overdue : Send reminders for overdue payments only}
                            {--test : Test mode - only log, don\'t send emails}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminder emails to clients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $overdueOnly = $this->option('overdue');
        $testMode = $this->option('test');

        $this->info('🔔 Starting payment reminder process...');

        if ($testMode) {
            $this->warn('⚠️  TEST MODE - Emails will NOT be sent, only logged');
        }

        // Get booths with pending payments
        $booths = $this->getBoothsNeedingReminders($days, $overdueOnly);

        $this->info("📊 Found {$booths->count()} booth(s) needing payment reminders");

        $sent = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($booths as $booth) {
            if (! $booth->client || ! $booth->client->email) {
                $this->warn("⚠️  Booth {$booth->booth_number}: No client email found - SKIPPED");
                $skipped++;

                continue;
            }

            try {
                $reminderType = $this->getReminderType($booth);

                if ($testMode) {
                    $this->line("📧 [TEST] Would send {$reminderType} reminder to: {$booth->client->email} for booth {$booth->booth_number}");
                } else {
                    Mail::to($booth->client->email)
                        ->send(new PaymentReminderMail($booth, $reminderType));

                    $this->info("✅ Sent {$reminderType} reminder to: {$booth->client->email} for booth {$booth->booth_number}");

                    // Log in timeline
                    \App\Models\BookingTimeline::createEntry(
                        $booth->id,
                        'payment_reminder_sent',
                        "Payment reminder sent to {$booth->client->email}",
                        null,
                        $booth->bookid
                    );
                }

                $sent++;
            } catch (\Exception $e) {
                $this->error("❌ Failed to send reminder for booth {$booth->booth_number}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info('📈 Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Sent', $sent],
                ['❌ Failed', $failed],
                ['⚠️  Skipped', $skipped],
                ['📊 Total', $booths->count()],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Get booths needing reminders
     */
    private function getBoothsNeedingReminders($days, $overdueOnly)
    {
        $query = Booth::with(['client', 'floorPlan'])
            ->whereNotNull('bookid')
            ->whereNotNull('payment_due_date');

        if ($overdueOnly) {
            // Overdue payments
            $query->where('payment_due_date', '<', Carbon::now())
                ->where('payment_status', '!=', 'paid');
        } else {
            // Upcoming due dates
            $reminderDate = Carbon::now()->addDays($days);
            $query->where('payment_due_date', '<=', $reminderDate)
                ->where('payment_due_date', '>=', Carbon::now())
                ->where('payment_status', '!=', 'paid');
        }

        return $query->get();
    }

    /**
     * Determine reminder type
     */
    private function getReminderType($booth)
    {
        if ($booth->payment_due_date < Carbon::now()) {
            return 'overdue';
        } elseif ($booth->payment_due_date->isToday()) {
            return 'due_today';
        } else {
            return 'upcoming';
        }
    }
}
