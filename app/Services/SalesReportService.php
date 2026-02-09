<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    /**
     * Build the last N days sales team report including activity log data.
     *
     * @return array{date_from: string, date_to: string, summary: array, sales_team: array, activity_by_user: array}
     */
    public function getMonthlySalesTeamReport(int $days = 30): array
    {
        $dateTo = Carbon::now()->endOfDay();
        $dateFrom = Carbon::now()->subDays($days)->startOfDay();

        $dateFromStr = $dateFrom->format('Y-m-d');
        $dateToStr = $dateTo->format('Y-m-d');

        // Sales team = non-admin active users (type != 1), plus any user with booking/activity in period
        $salesUserIds = User::where('status', 1)
            ->where(function ($q) {
                $q->where('type', '!=', 1)->orWhereNull('type');
            })
            ->pluck('id')
            ->toArray();

        // Also include any user who has bookings or activity in the period (so we don't miss anyone)
        $activeInPeriod = Book::whereBetween('date_book', [$dateFromStr, $dateToStr])
            ->distinct()
            ->pluck('userid')
            ->filter()
            ->merge(
                ActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])
                    ->distinct()
                    ->pluck('user_id')
                    ->filter()
            )
            ->unique()
            ->values()
            ->toArray();

        $salesUserIds = array_unique(array_merge($salesUserIds, $activeInPeriod));
        $salesUsers = User::whereIn('id', $salesUserIds)->where('status', 1)->get();

        $salesTeam = [];
        $totalBookings = 0;
        $totalRevenue = 0.0;
        $totalPaidRevenue = 0.0;

        foreach ($salesUsers as $user) {
            $bookings = Book::where('userid', $user->id)
                ->whereBetween('date_book', [$dateFromStr, $dateToStr])
                ->get();

            $userTotalRevenue = 0.0;
            $userPaidRevenue = 0.0;

            foreach ($bookings as $book) {
                $booths = $book->booths();
                $userTotalRevenue += $booths->sum('price');
                $userPaidRevenue += $booths->sum(function ($booth) {
                    return (float) ($booth->deposit_paid ?? 0) + (float) ($booth->balance_paid ?? 0);
                });
            }

            $totalBookings += $bookings->count();
            $totalRevenue += $userTotalRevenue;
            $totalPaidRevenue += $userPaidRevenue;

            $salesTeam[] = [
                'user_id' => $user->id,
                'username' => $user->username,
                'is_admin' => $user->isAdmin(),
                'bookings_count' => $bookings->count(),
                'total_revenue' => round($userTotalRevenue, 2),
                'paid_revenue' => round($userPaidRevenue, 2),
                'conversion_rate' => $userTotalRevenue > 0
                    ? round(($userPaidRevenue / $userTotalRevenue) * 100, 1)
                    : 0,
            ];
        }

        // Sort by total revenue desc
        usort($salesTeam, fn ($a, $b) => $b['total_revenue'] <=> $a['total_revenue']);

        // Activity log data per user (last 30 days): counts by action category
        $activityByUser = $this->getActivityLogSummaryByUser($dateFrom, $dateTo, $salesUserIds);

        return [
            'date_from' => $dateFromStr,
            'date_to' => $dateToStr,
            'days' => $days,
            'summary' => [
                'total_bookings' => $totalBookings,
                'total_revenue' => round($totalRevenue, 2),
                'paid_revenue' => round($totalPaidRevenue, 2),
                'sales_team_count' => count($salesTeam),
            ],
            'sales_team' => $salesTeam,
            'activity_by_user' => $activityByUser,
        ];
    }

    /**
     * Activity log summary per user: booking.*, client.*, booth.* counts.
     */
    private function getActivityLogSummaryByUser(Carbon $dateFrom, Carbon $dateTo, array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }

        $logs = ActivityLog::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('user_id', $userIds)
            ->select('user_id', 'action', DB::raw('count(*) as count'))
            ->groupBy('user_id', 'action')
            ->get();

        $byUser = [];
        foreach ($logs as $row) {
            $uid = $row->user_id;
            if (! isset($byUser[$uid])) {
                $byUser[$uid] = [
                    'booking_actions' => 0,
                    'client_actions' => 0,
                    'booth_actions' => 0,
                    'other_actions' => 0,
                ];
            }
            $action = $row->action ?? '';
            if (str_starts_with($action, 'booking.')) {
                $byUser[$uid]['booking_actions'] += (int) $row->count;
            } elseif (str_starts_with($action, 'client.')) {
                $byUser[$uid]['client_actions'] += (int) $row->count;
            } elseif (str_starts_with($action, 'booth.')) {
                $byUser[$uid]['booth_actions'] += (int) $row->count;
            } else {
                $byUser[$uid]['other_actions'] += (int) $row->count;
            }
        }

        return $byUser;
    }

    /**
     * Format a short message for the notification body (e.g. for in-app notification).
     */
    public function formatReportMessage(array $report): string
    {
        $s = $report['summary'];
        $lines = [
            "Last {$report['days']} days ({$report['date_from']} to {$report['date_to']}).",
            "Bookings: {$s['total_bookings']} | Revenue: \$".number_format($s['total_revenue'], 2)." | Paid: \$".number_format($s['paid_revenue'], 2).".",
            "Sales team: {$s['sales_team_count']} user(s).",
        ];

        $top = array_slice($report['sales_team'], 0, 3);
        if (! empty($top)) {
            $names = array_map(fn ($u) => $u['username'].' ('.$u['bookings_count'].' bookings)', $top);
            $lines[] = 'Top: '.implode(', ', $names).'.';
        }

        return implode(' ', $lines);
    }

    /**
     * Format a longer message including activity log summary (for email or detailed view).
     */
    public function formatReportMessageWithLogs(array $report): string
    {
        $msg = $this->formatReportMessage($report);
        $activity = $report['activity_by_user'] ?? [];
        if (empty($activity)) {
            return $msg;
        }

        $userNames = User::whereIn('id', array_keys($activity))->pluck('username', 'id')->toArray();
        $lines = [$msg, '', 'Activity (logs) last '.$report['days'].' days:'];

        foreach ($activity as $userId => $counts) {
            $name = $userNames[$userId] ?? 'User #'.$userId;
            $parts = [];
            if (($counts['booking_actions'] ?? 0) > 0) {
                $parts[] = 'bookings: '.$counts['booking_actions'];
            }
            if (($counts['client_actions'] ?? 0) > 0) {
                $parts[] = 'clients: '.$counts['client_actions'];
            }
            if (($counts['booth_actions'] ?? 0) > 0) {
                $parts[] = 'booths: '.$counts['booth_actions'];
            }
            if (($counts['other_actions'] ?? 0) > 0) {
                $parts[] = 'other: '.$counts['other_actions'];
            }
            if (! empty($parts)) {
                $lines[] = '- '.$name.': '.implode(', ', $parts);
            }
        }

        return implode("\n", $lines);
    }
}
