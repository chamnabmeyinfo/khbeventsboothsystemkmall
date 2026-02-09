<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookingTimeline;
use App\Models\Booth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Sales Report - Revenue by date range
     */
    public function salesReport(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));
        $groupBy = $request->input('group_by', 'day'); // day, week, month

        // Get bookings in date range
        $bookings = Book::whereBetween('date_book', [$dateFrom, $dateTo])
            ->with(['client', 'user'])
            ->get();

        // Calculate revenue
        $totalRevenue = 0;
        $paidRevenue = 0;
        $bookingData = [];

        foreach ($bookings as $book) {
            $booths = $book->booths();
            $bookingTotal = $booths->sum('price');
            $totalRevenue += $bookingTotal;

            // Calculate actual paid revenue - sum of deposit_paid + balance_paid for all booths
            // This gives accurate paid amount regardless of status
            $bookingPaid = $booths->sum(function ($booth) {
                $depositPaid = (float) ($booth->deposit_paid ?? 0);
                $balancePaid = (float) ($booth->balance_paid ?? 0);

                return $depositPaid + $balancePaid;
            });

            $paidRevenue += $bookingPaid;

            // Check if all booths in booking are fully paid (status = PAID)
            $allPaid = $booths->count() > 0 && $booths->every(function ($booth) {
                return $booth->status === Booth::STATUS_PAID;
            });

            // Determine booking status based on payment
            $bookingStatus = 'Pending';
            if ($allPaid) {
                $bookingStatus = 'Fully Paid';
            } elseif ($bookingPaid > 0) {
                $bookingStatus = 'Partially Paid';
            }

            $bookingData[] = [
                'id' => $book->id,
                'date' => $book->date_book->format('Y-m-d'),
                'client' => $book->client ? $book->client->company : 'N/A',
                'booths_count' => $booths->count(),
                'total' => $bookingTotal,
                'paid' => $bookingPaid,
                'status' => $bookingStatus,
                'user' => $book->user ? $book->user->username : 'N/A',
            ];
        }

        // Group by period for chart
        $chartData = $this->groupRevenueByPeriod($bookings, $groupBy);

        return view('reports.sales', compact(
            'dateFrom',
            'dateTo',
            'groupBy',
            'totalRevenue',
            'paidRevenue',
            'bookingData',
            'chartData'
        ));
    }

    /**
     * Booking Trends Report
     */
    public function bookingTrends(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $trends = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            $bookings = Book::whereDate('date_book', $dateStr)->count();

            // Count confirmed booths - use booking timeline to find when status changed to CONFIRMED
            $confirmed = BookingTimeline::whereDate('created_at', $dateStr)
                ->where('new_status', Booth::STATUS_CONFIRMED)
                ->distinct('booth_id')
                ->count('booth_id');

            // Count paid booths - use balance_paid_date or check timeline for fully_paid action
            $paid = Booth::where(function ($query) use ($dateStr) {
                $query->whereDate('balance_paid_date', $dateStr)
                    ->orWhereDate('deposit_paid_date', $dateStr);
            })
                ->where('status', Booth::STATUS_PAID)
                ->count();

            // Alternative: Also count from timeline if payment dates are not set
            $paidFromTimeline = BookingTimeline::whereDate('created_at', $dateStr)
                ->where(function ($query) {
                    $query->where('action', 'fully_paid')
                        ->orWhere('action', 'balance_paid')
                        ->orWhere('new_status', Booth::STATUS_PAID);
                })
                ->distinct('booth_id')
                ->count('booth_id');

            // Use the higher count (either from payment dates or timeline)
            $paid = max($paid, $paidFromTimeline);

            $trends[] = [
                'date' => $date->format('M d'),
                'date_full' => $dateStr,
                'bookings' => $bookings,
                'confirmed' => $confirmed,
                'paid' => $paid,
            ];
        }

        return view('reports.trends', compact('trends', 'days'));
    }

    /**
     * User Performance Report
     */
    public function userPerformance(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->format('Y-m-d'));

        $users = User::where('status', 1)->get();
        $performance = [];

        foreach ($users as $user) {
            // Get bookings in date range
            $bookings = Book::where('userid', $user->id)
                ->whereBetween('date_book', [$dateFrom, $dateTo])
                ->get();

            $totalBookings = $bookings->count();
            $totalRevenue = 0;
            $paidRevenue = 0;

            foreach ($bookings as $book) {
                $booths = $book->booths();
                $bookingTotal = $booths->sum('price');
                $totalRevenue += $bookingTotal;

                // Calculate actual paid revenue - sum of deposit_paid + balance_paid for all booths
                $bookingPaid = $booths->sum(function ($booth) {
                    $depositPaid = (float) ($booth->deposit_paid ?? 0);
                    $balancePaid = (float) ($booth->balance_paid ?? 0);

                    return $depositPaid + $balancePaid;
                });

                $paidRevenue += $bookingPaid;
            }

            $performance[] = [
                'user_id' => $user->id,
                'username' => $user->username,
                'type' => $user->isAdmin() ? 'Admin' : 'Sale',
                'total_bookings' => $totalBookings,
                'total_revenue' => $totalRevenue,
                'paid_revenue' => $paidRevenue,
                'conversion_rate' => ($totalRevenue > 0) ? round(($paidRevenue / $totalRevenue) * 100, 2) : 0,
            ];
        }

        // Sort by total revenue descending
        usort($performance, function ($a, $b) {
            return $b['total_revenue'] <=> $a['total_revenue'];
        });

        return view('reports.user-performance', compact('performance', 'dateFrom', 'dateTo'));
    }

    /**
     * Revenue Chart Data (API endpoint)
     */
    public function revenueChart(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $data = $this->getRevenueChartData($days);

        return response()->json($data);
    }

    /**
     * Helper: Group revenue by period
     */
    private function groupRevenueByPeriod($bookings, $groupBy)
    {
        $grouped = [];

        foreach ($bookings as $book) {
            $date = Carbon::parse($book->date_book);

            switch ($groupBy) {
                case 'week':
                    $key = $date->format('Y-W'); // Year-Week
                    $label = 'Week '.$date->format('W, Y');
                    break;
                case 'month':
                    $key = $date->format('Y-m');
                    $label = $date->format('M Y');
                    break;
                default: // day
                    $key = $date->format('Y-m-d');
                    $label = $date->format('M d');
            }

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'label' => $label,
                    'total' => 0,
                    'paid' => 0,
                    'count' => 0,
                ];
            }

            $booths = $book->booths();
            $total = $booths->sum('price');
            $grouped[$key]['total'] += $total;
            $grouped[$key]['count']++;

            // Calculate actual paid revenue - sum of deposit_paid + balance_paid for all booths
            $paid = $booths->sum(function ($booth) {
                $depositPaid = (float) ($booth->deposit_paid ?? 0);
                $balancePaid = (float) ($booth->balance_paid ?? 0);

                return $depositPaid + $balancePaid;
            });

            $grouped[$key]['paid'] += $paid;
        }

        return array_values($grouped);
    }

    /**
     * Helper: Get revenue chart data
     */
    private function getRevenueChartData($days)
    {
        $labels = [];
        $totalData = [];
        $paidData = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            $bookings = Book::whereDate('date_book', $dateStr)->get();

            $total = 0;
            $paid = 0;

            foreach ($bookings as $book) {
                $booths = $book->booths();
                $bookingTotal = $booths->sum('price');
                $total += $bookingTotal;

                // Calculate actual paid revenue - sum of deposit_paid + balance_paid for all booths
                $bookingPaid = $booths->sum(function ($booth) {
                    $depositPaid = (float) ($booth->deposit_paid ?? 0);
                    $balancePaid = (float) ($booth->balance_paid ?? 0);

                    return $depositPaid + $balancePaid;
                });

                $paid += $bookingPaid;
            }

            $labels[] = $date->format('M d');
            $totalData[] = $total;
            $paidData[] = $paid;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Revenue',
                    'data' => $totalData,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                ],
                [
                    'label' => 'Paid Revenue',
                    'data' => $paidData,
                    'borderColor' => 'rgb(54, 162, 235)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                ],
            ],
        ];
    }
}
