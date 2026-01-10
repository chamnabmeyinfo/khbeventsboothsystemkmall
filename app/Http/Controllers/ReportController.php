<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\Book;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
            ->with(['client', 'user', 'booths'])
            ->get();

        // Calculate revenue
        $totalRevenue = 0;
        $paidRevenue = 0;
        $bookingData = [];

        foreach ($bookings as $book) {
            $booths = $book->booths();
            $bookingTotal = $booths->sum('price');
            $totalRevenue += $bookingTotal;

            // Check if all booths in booking are paid
            $allPaid = $booths->count() > 0 && $booths->every(function ($booth) {
                return $booth->status === Booth::STATUS_PAID;
            });

            if ($allPaid) {
                $paidRevenue += $bookingTotal;
            }

            $bookingData[] = [
                'id' => $book->id,
                'date' => $book->date_book->format('Y-m-d'),
                'client' => $book->client ? $book->client->company : 'N/A',
                'booths_count' => $booths->count(),
                'total' => $bookingTotal,
                'status' => $allPaid ? 'Paid' : 'Pending',
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
            $confirmed = Booth::whereDate('create_time', $dateStr)
                ->where('status', Booth::STATUS_CONFIRMED)
                ->count();
            $paid = Booth::whereDate('create_time', $dateStr)
                ->where('status', Booth::STATUS_PAID)
                ->count();

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

                $allPaid = $booths->count() > 0 && $booths->every(function ($booth) {
                    return $booth->status === Booth::STATUS_PAID;
                });

                if ($allPaid) {
                    $paidRevenue += $bookingTotal;
                }
            }

            $performance[] = [
                'user_id' => $user->id,
                'username' => $user->username,
                'type' => $user->isAdmin() ? 'Admin' : 'Sale',
                'total_bookings' => $totalBookings,
                'total_revenue' => $totalRevenue,
                'paid_revenue' => $paidRevenue,
                'conversion_rate' => $totalBookings > 0 ? round(($paidRevenue / $totalRevenue) * 100, 2) : 0,
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
                    $label = 'Week ' . $date->format('W, Y');
                    break;
                case 'month':
                    $key = $date->format('Y-m');
                    $label = $date->format('M Y');
                    break;
                default: // day
                    $key = $date->format('Y-m-d');
                    $label = $date->format('M d');
            }

            if (!isset($grouped[$key])) {
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

            $allPaid = $booths->count() > 0 && $booths->every(function ($booth) {
                return $booth->status === Booth::STATUS_PAID;
            });

            if ($allPaid) {
                $grouped[$key]['paid'] += $total;
            }
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

                $allPaid = $booths->count() > 0 && $booths->every(function ($booth) {
                    return $booth->status === Booth::STATUS_PAID;
                });

                if ($allPaid) {
                    $paid += $bookingTotal;
                }
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
