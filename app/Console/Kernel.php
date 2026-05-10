<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Payment reminders - run daily at 9:00 AM (upcoming payments)
        $schedule->command('payment:send-reminders', ['--days' => 3])
            ->dailyAt('09:00')
            ->timezone(config('app.timezone', 'Asia/Phnom_Penh'));

        // Payment reminders - run daily at 14:00 (overdue payments)
        $schedule->command('payment:send-reminders', ['--overdue' => true])
            ->dailyAt('14:00')
            ->timezone(config('app.timezone', 'Asia/Phnom_Penh'));

        // Monthly sales team report (with activity logs) – notify admins on the 1st at 8:00
        $schedule->command('report:monthly-sales', ['--days' => 30])
            ->monthlyOn(1, '08:00')
            ->timezone(config('app.timezone', 'Asia/Phnom_Penh'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
