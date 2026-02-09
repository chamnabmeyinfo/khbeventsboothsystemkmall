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
