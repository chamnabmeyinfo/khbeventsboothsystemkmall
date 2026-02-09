<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use App\Services\SalesReportService;
use Illuminate\Console\Command;

class SendMonthlySalesReport extends Command
{
    protected $signature = 'report:monthly-sales 
                            {--days=30 : Number of days to include in the report}
                            {--notify=admins : Who to notify: admins, all}';

    protected $description = 'Build 1-month sales team report (with activity logs) and send as notification';

    public function handle(SalesReportService $reportService): int
    {
        $days = (int) $this->option('days');
        $notify = $this->option('notify');

        $this->info("Building {$days}-day sales team report (with activity logs)...");

        $report = $reportService->getMonthlySalesTeamReport($days);

        $title = 'Monthly Sales Team Report ('.$report['date_from'].' to '.$report['date_to'].')';
        $messageWithLogs = $reportService->formatReportMessageWithLogs($report);

        $link = route('reports.user-performance', [
            'date_from' => $report['date_from'],
            'date_to' => $report['date_to'],
        ]);

        $recipients = $notify === 'all'
            ? User::where('status', 1)->get()
            : User::where('status', 1)->get()->filter(fn ($u) => $u->isAdmin());

        $created = 0;
        foreach ($recipients as $user) {
            try {
                NotificationService::create(
                    'system',
                    $title,
                    $messageWithLogs,
                    $user->id,
                    null,
                    null,
                    $link
                );
                $created++;
            } catch (\Throwable $e) {
                $this->warn("Failed to create notification for user {$user->username}: ".$e->getMessage());
            }
        }

        $this->info("Report created. Notifications sent: {$created}.");
        $this->line('Summary: '.$reportService->formatReportMessage($report));

        return self::SUCCESS;
    }
}
