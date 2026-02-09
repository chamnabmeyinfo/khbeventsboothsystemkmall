<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\Booth;
use App\Models\Client;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\User;
use App\Repositories\BoothRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    public function __construct(
        private BoothRepository $boothRepository
    ) {}

    /**
     * Get booth statistics
     */
    public function getBoothStatistics(?int $userId = null): array
    {
        try {
            $boothStats = $this->boothRepository->getStatistics();

            $totalBooths = (int) ($boothStats->total ?? 0);
            $availableBooths = (int) ($boothStats->available ?? 0);
            $reservedBooths = (int) ($boothStats->reserved ?? 0);
            $confirmedBooths = (int) ($boothStats->confirmed ?? 0);
            $paidBooths = (int) ($boothStats->paid ?? 0);

            // If user is specified, filter by user
            if ($userId) {
                $reservedBooths = Booth::where('status', Booth::STATUS_RESERVED)
                    ->where('userid', $userId)
                    ->count();
                $confirmedBooths = Booth::where('status', Booth::STATUS_CONFIRMED)
                    ->where('userid', $userId)
                    ->count();
                $paidBooths = Booth::where('status', Booth::STATUS_PAID)
                    ->where('userid', $userId)
                    ->count();
            }

            return [
                'total_booths' => $totalBooths,
                'available_booths' => $availableBooths,
                'reserved_booths' => $reservedBooths,
                'confirmed_booths' => $confirmedBooths,
                'paid_booths' => $paidBooths,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get booth statistics: '.$e->getMessage());

            return [
                'total_booths' => 0,
                'available_booths' => 0,
                'reserved_booths' => 0,
                'confirmed_booths' => 0,
                'paid_booths' => 0,
            ];
        }
    }

    /**
     * Get user statistics (for admin)
     */
    public function getUserStatistics(): array
    {
        try {
            $users = User::orderBy('username')->get();

            if ($users->isEmpty()) {
                return [];
            }

            // Optimized: Get all booth counts grouped by user in one query
            $boothCountsByUser = Booth::select('userid', 'status', DB::raw('count(*) as count'))
                ->whereIn('status', [Booth::STATUS_RESERVED, Booth::STATUS_CONFIRMED, Booth::STATUS_PAID])
                ->whereIn('userid', $users->pluck('id'))
                ->groupBy('userid', 'status')
                ->get()
                ->groupBy('userid');

            $userStats = [];
            foreach ($users as $usr) {
                try {
                    $userBoothCounts = $boothCountsByUser->get($usr->id, collect());

                    $reserveCount = $userBoothCounts->firstWhere('status', Booth::STATUS_RESERVED);
                    $bookingCount = $userBoothCounts->firstWhere('status', Booth::STATUS_CONFIRMED);
                    $paidCount = $userBoothCounts->firstWhere('status', Booth::STATUS_PAID);

                    $userStats[] = [
                        'id' => $usr->id,
                        'username' => $usr->username,
                        'type' => ($usr->type ?? '2') == '1' || ($usr->type ?? '2') == 1 ? 'Admin' : 'Sale',
                        'status' => $usr->status ?? 'N/A',
                        'reserve' => $reserveCount ? (int) $reserveCount->count : 0,
                        'booking' => $bookingCount ? (int) $bookingCount->count : 0,
                        'paid' => $paidCount ? (int) $paidCount->count : 0,
                        'last_login' => $usr->last_login ?? null,
                    ];
                } catch (\Exception $e) {
                    Log::error('Error processing user stats for user '.($usr->id ?? 'N/A').': '.$e->getMessage());
                }
            }

            // Sort by total performance (reserve + booking + paid) descending
            usort($userStats, function ($a, $b) {
                $totalA = ($a['reserve'] ?? 0) + ($a['booking'] ?? 0) + ($a['paid'] ?? 0);
                $totalB = ($b['reserve'] ?? 0) + ($b['booking'] ?? 0) + ($b['paid'] ?? 0);

                return $totalB <=> $totalA;
            });

            return $userStats;
        } catch (\Exception $e) {
            Log::error('Failed to get user statistics: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Get booking data with client information
     */
    public function getRecentBookings(?int $userId = null, int $limit = 50): array
    {
        try {
            $query = DB::table('book as b')
                ->join('client as c', 'c.id', '=', 'b.clientid')
                ->join('user as u', 'u.id', '=', 'b.userid')
                ->select(
                    'b.id as book_id',
                    'b.clientid as client_id',
                    'b.boothid',
                    'b.date_book',
                    'b.type as book_type',
                    'b.floor_plan_id',
                    'c.id as client_id',
                    'c.name as client_name',
                    'c.company as client_company',
                    'c.phone_number as client_phone',
                    'u.id as user_id',
                    'u.username as user_name',
                    'u.type as user_type',
                    'u.status as user_status',
                    'u.last_login'
                );

            if ($userId) {
                $query->where('u.id', $userId);
            }

            $bookingData = $query->orderBy('b.date_book', 'desc')->limit($limit)->get();

            // Process booking data to include booth information
            $processedBookings = [];
            foreach ($bookingData as $booking) {
                try {
                    $boothIds = json_decode($booking->boothid, true) ?? [];
                    if (! empty($boothIds)) {
                        $booths = Booth::whereIn('id', $boothIds)
                            ->leftJoin('category as cat', 'cat.id', '=', 'booth.category_id')
                            ->leftJoin('category as sub', 'sub.id', '=', 'booth.sub_category_id')
                            ->select(
                                'booth.id',
                                'booth.booth_number',
                                'booth.status',
                                'cat.name as category_name',
                                'sub.name as sub_category_name'
                            )
                            ->get();

                        $booking->booths = $booths;
                        $booking->booth_count = $booths->count();
                        $booking->booth_names = $booths->pluck('booth_number')->implode(', ');
                    } else {
                        $booking->booths = collect([]);
                        $booking->booth_count = 0;
                        $booking->booth_names = 'N/A';
                    }
                } catch (\Exception $e) {
                    $booking->booths = collect([]);
                    $booking->booth_count = 0;
                    $booking->booth_names = 'N/A';
                }

                $processedBookings[] = [
                    'book_id' => $booking->book_id ?? null,
                    'client_id' => $booking->client_id ?? null,
                    'client_name' => $booking->client_name ?? 'N/A',
                    'company' => $booking->client_company ?? 'N/A',
                    'phone' => $booking->client_phone ?? 'N/A',
                    'user_id' => $booking->user_id ?? null,
                    'user_name' => $booking->user_name ?? 'N/A',
                    'booth_count' => $booking->booth_count ?? 0,
                    'booth_names' => $booking->booth_names ?? 'N/A',
                    'date_book' => $booking->date_book ? \Carbon\Carbon::parse($booking->date_book)->format('Y-m-d H:i:s') : 'N/A',
                ];
            }

            return $processedBookings;
        } catch (\Exception $e) {
            Log::error('Failed to get recent bookings: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Get revenue statistics
     */
    public function getRevenueStatistics(): array
    {
        try {
            // Total revenue - use actual payment amounts from payments table
            $totalRevenue = (float) Payment::where('status', Payment::STATUS_COMPLETED)
                ->sum('amount');

            // Alternative: Calculate from booking paid amounts if payments table is empty
            if ($totalRevenue == 0) {
                $totalRevenue = (float) Book::sum('paid_amount');
            }

            // Fallback: Calculate from paid booths if both above are 0
            if ($totalRevenue == 0) {
                $totalRevenue = (float) Booth::where('status', Booth::STATUS_PAID)
                    ->sum('price');
            }

            // Today's revenue
            $todayRevenue = (float) Payment::where('status', Payment::STATUS_COMPLETED)
                ->whereDate('paid_at', today())
                ->sum('amount');

            // This month revenue
            $thisMonthRevenue = (float) Payment::where('status', Payment::STATUS_COMPLETED)
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount');

            return [
                'total_revenue' => $totalRevenue,
                'today_revenue' => $todayRevenue,
                'this_month_revenue' => $thisMonthRevenue,
            ];
        } catch (\Exception $e) {
            Log::error('Revenue calculation error: '.$e->getMessage());

            return [
                'total_revenue' => 0,
                'today_revenue' => 0,
                'this_month_revenue' => 0,
            ];
        }
    }

    /**
     * Get booking trends data for charts
     */
    public function getBookingTrends(int $days = 30): array
    {
        try {
            $days = max(7, min(90, $days)); // Between 7 and 90 days

            // Get booking counts per day
            $bookingsByDate = Book::select(
                DB::raw('DATE(date_book) as booking_date'),
                DB::raw('COUNT(*) as booking_count')
            )
                ->whereDate('date_book', '>=', now()->subDays($days))
                ->groupBy(DB::raw('DATE(date_book)'))
                ->pluck('booking_count', 'booking_date')
                ->toArray();

            $bookingTrendDates = [];
            $bookingTrendCounts = [];
            $revenueTrendData = [];

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $dateFormatted = $date->format('M d');

                // Get booking count from pre-fetched data
                $count = isset($bookingsByDate[$dateStr]) ? (int) $bookingsByDate[$dateStr] : 0;

                // Revenue calculation - use actual payments made on this date
                $dayRevenue = 0;
                try {
                    $dayRevenue = (float) Payment::where('status', Payment::STATUS_COMPLETED)
                        ->whereDate('paid_at', $dateStr)
                        ->sum('amount');
                } catch (\Exception $e) {
                    // Fallback: use booking paid amounts if payments table query fails
                    try {
                        $dayBookings = Book::whereDate('date_book', $dateStr)->get();
                        foreach ($dayBookings as $booking) {
                            $dayRevenue += (float) ($booking->paid_amount ?? 0);
                        }
                    } catch (\Exception $e2) {
                        // Skip revenue calculation if error
                    }
                }

                $bookingTrendDates[] = $dateFormatted;
                $bookingTrendCounts[] = $count;
                $revenueTrendData[] = round($dayRevenue, 2);
            }

            return [
                'dates' => $bookingTrendDates,
                'booking_counts' => $bookingTrendCounts,
                'revenue_data' => $revenueTrendData,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get booking trends: '.$e->getMessage());

            return [
                'dates' => [],
                'booking_counts' => [],
                'revenue_data' => [],
            ];
        }
    }

    /**
     * Get booking metrics (counts and growth)
     */
    public function getBookingMetrics(): array
    {
        try {
            $todayBookings = Book::whereDate('date_book', today())->count();
            $yesterdayBookings = Book::whereDate('date_book', today()->subDay())->count();
            $thisMonthBookings = Book::whereMonth('date_book', now()->month)
                ->whereYear('date_book', now()->year)
                ->count();
            $lastMonthBookings = Book::whereMonth('date_book', now()->subMonth()->month)
                ->whereYear('date_book', now()->subMonth()->year)
                ->count();

            // Calculate growth percentages
            $bookingGrowth = $yesterdayBookings > 0
                ? (($todayBookings - $yesterdayBookings) / $yesterdayBookings) * 100
                : ($todayBookings > 0 ? 100 : 0);

            $monthBookingGrowth = $lastMonthBookings > 0
                ? (($thisMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100
                : ($thisMonthBookings > 0 ? 100 : 0);

            return [
                'today_bookings' => $todayBookings,
                'this_month_bookings' => $thisMonthBookings,
                'booking_growth' => round($bookingGrowth, 1),
                'month_booking_growth' => round($monthBookingGrowth, 1),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get booking metrics: '.$e->getMessage());

            return [
                'today_bookings' => 0,
                'this_month_bookings' => 0,
                'booking_growth' => 0,
                'month_booking_growth' => 0,
            ];
        }
    }

    /**
     * Get general statistics
     */
    public function getGeneralStatistics(): array
    {
        try {
            return [
                'total_clients' => Client::count(),
                'total_users' => User::count(),
                'total_bookings' => Book::count(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get general statistics: '.$e->getMessage());

            return [
                'total_clients' => 0,
                'total_users' => 0,
                'total_bookings' => 0,
            ];
        }
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotifications(?int $userId = null, int $limit = 5)
    {
        try {
            return Notification::where(function ($query) use ($userId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                }
                $query->orWhereNull('user_id'); // System notifications
            })
                ->where('is_read', false)
                ->latest()
                ->take($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to get recent notifications: '.$e->getMessage());

            return collect([]);
        }
    }

    /**
     * Get recent activity logs
     */
    public function getRecentActivities(int $limit = 10)
    {
        try {
            return ActivityLog::with('user')
                ->latest()
                ->take($limit)
                ->get();
        } catch (\Exception $e) {
            Log::error('Failed to get recent activities: '.$e->getMessage());

            return collect([]);
        }
    }

    /**
     * Get top performing users
     */
    public function getTopUsers(array $userStats, int $limit = 5): array
    {
        try {
            return collect($userStats)
                ->sortByDesc(function ($user) {
                    return ($user['reserve'] ?? 0) + ($user['booking'] ?? 0) + ($user['paid'] ?? 0);
                })
                ->take($limit)
                ->values()
                ->all();
        } catch (\Exception $e) {
            Log::error('Failed to get top users: '.$e->getMessage());

            return [];
        }
    }

    /**
     * Calculate occupancy rate
     */
    public function calculateOccupancyRate(int $totalBooths, int $availableBooths): float
    {
        if ($totalBooths <= 0) {
            return 0;
        }

        return round((($totalBooths - $availableBooths) / $totalBooths) * 100, 1);
    }

    /**
     * Get complete dashboard data
     */
    public function getDashboardData(?int $userId = null, bool $isAdmin = false, int $days = 30): array
    {
        // Get booth statistics
        $boothStats = $this->getBoothStatistics($userId);

        // Get user statistics (admin only)
        $userStats = $isAdmin ? $this->getUserStatistics() : [];

        // Get recent bookings
        $clientData = $this->getRecentBookings($userId);

        // Get revenue statistics
        $revenueStats = $this->getRevenueStatistics();

        // Get booking trends
        $trends = $this->getBookingTrends($days);

        // Get booking metrics
        $bookingMetrics = $this->getBookingMetrics();

        // Get general statistics
        $generalStats = $this->getGeneralStatistics();

        // Calculate occupancy rate
        $occupancyRate = $this->calculateOccupancyRate(
            $boothStats['total_booths'],
            $boothStats['available_booths']
        );

        // Combine all statistics
        $stats = array_merge($boothStats, $revenueStats, $generalStats, $bookingMetrics, [
            'occupancy_rate' => $occupancyRate,
            'available_rate' => round(100 - $occupancyRate, 1),
        ]);

        // Get recent notifications
        $recentNotifications = $this->getRecentNotifications($userId);

        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        // Get top users (admin only)
        $topUsers = $isAdmin ? $this->getTopUsers($userStats) : [];

        // Get recent bookings as Book models
        $recentBookings = collect([]);
        try {
            if (! empty($clientData)) {
                $bookingIds = collect($clientData)->pluck('book_id')->filter()->unique()->toArray();
                if (! empty($bookingIds)) {
                    $recentBookings = Book::with(['client', 'user'])
                        ->whereIn('id', $bookingIds)
                        ->orderBy('date_book', 'desc')
                        ->limit(10)
                        ->get();
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to get recent bookings models: '.$e->getMessage());
        }

        return [
            'stats' => $stats,
            'userStats' => $userStats,
            'clientData' => $clientData,
            'bookingTrendDates' => $trends['dates'],
            'bookingTrendCounts' => $trends['booking_counts'],
            'revenueTrendData' => $trends['revenue_data'],
            'recentNotifications' => $recentNotifications,
            'recentActivities' => $recentActivities,
            'topUsers' => $topUsers,
            'recentBookings' => $recentBookings,
            'days' => $days,
        ];
    }
}
