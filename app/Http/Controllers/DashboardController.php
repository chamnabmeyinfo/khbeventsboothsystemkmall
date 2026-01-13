<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booth;
use App\Models\Client;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\DebugLogger;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     * Matches Yii DashboardController actionIndex
     */
    public function index(Request $request)
    {
        // #region agent log
        DebugLogger::log( ['method'=>$request->method(),'session_id'=>$request->session()->getId(),'csrf_token'=>csrf_token(),'session_token'=>session()->token(),'is_authenticated'=>auth()->check(),'user_id'=>auth()->id(),'all_request_data'=>array_keys($request->all())], 'DashboardController.php:19', 'Dashboard accessed');
        // #endregion
        
        // #region agent log
        DebugLogger::log(['auth_check'=>auth()->check(),'user_id'=>auth()->id()], 'DashboardController.php:27', 'Getting authenticated user');
        // #endregion
        
        if (!auth()->check()) {
            // #region agent log
            DebugLogger::log([], 'DashboardController.php:31', 'User not authenticated');
            // #endregion
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        if (!$user) {
            // #region agent log
            DebugLogger::log([], 'DashboardController.php:40', 'User object is null');
            // #endregion
            return redirect()->route('login');
        }
        
        // #region agent log
        DebugLogger::log(['user_id'=>$user->id ?? 'N/A','username'=>$user->username ?? 'N/A','type'=>($user->type ?? 'N/A'),'status'=>($user->status ?? 'N/A')], 'DashboardController.php:46', 'User retrieved, checking admin status');
        // #endregion
        
        try {
            $isAdmin = $user->isAdmin();
            // #region agent log
            DebugLogger::log(['isAdmin'=>$isAdmin], 'DashboardController.php:52', 'isAdmin() result');
            // #endregion
        } catch (\Exception $e) {
            // #region agent log
            DebugLogger::log(['error'=>$e->getMessage(),'trace'=>substr($e->getTraceAsString(), 0, 500)], 'DashboardController.php:56', 'isAdmin() failed');
            // #endregion
            $isAdmin = false;
        } catch (\Throwable $e) {
            // #region agent log
            DebugLogger::log(['error'=>$e->getMessage(),'trace'=>substr($e->getTraceAsString(), 0, 500)], 'DashboardController.php:61', 'isAdmin() failed (Throwable)');
            // #endregion
            $isAdmin = false;
        }
        
        // Check if booth table exists, if not return error message
        try {
            // #region agent log
            DebugLogger::log([], 'DashboardController.php:69', 'Starting booth queries');
            // #endregion
            
            // Optimized: Get all booth statistics in one query instead of multiple separate queries
            $boothStats = Booth::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status IN (' . Booth::STATUS_AVAILABLE . ', ' . Booth::STATUS_HIDDEN . ') THEN 1 ELSE 0 END) as available,
                SUM(CASE WHEN status = ' . Booth::STATUS_RESERVED . ' THEN 1 ELSE 0 END) as reserved,
                SUM(CASE WHEN status = ' . Booth::STATUS_CONFIRMED . ' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = ' . Booth::STATUS_PAID . ' THEN 1 ELSE 0 END) as paid
            ')->first();
            
            $totalBooths = (int) ($boothStats->total ?? 0);
            $availableBooths = (int) ($boothStats->available ?? 0);
            
            // Get statistics based on user type
            if ($isAdmin) {
                $reservedBooths = (int) ($boothStats->reserved ?? 0);
                $confirmedBooths = (int) ($boothStats->confirmed ?? 0);
                $paidBooths = (int) ($boothStats->paid ?? 0);
                
                // Get all users
                // #region agent log
                DebugLogger::log([], 'DashboardController.php:87', 'Fetching users list');
                // #endregion
                
                try {
                    $users = User::orderBy('username')->get();
                    // #region agent log
                    DebugLogger::log(['count'=>count($users)], 'DashboardController.php:93', 'Users fetched');
                    // #endregion
                } catch (\Exception $e) {
                    // #region agent log
                    DebugLogger::log(['error'=>$e->getMessage()], 'DashboardController.php:97', 'Failed to fetch users');
                    // #endregion
                    $users = collect([]);
                }
                
                // Optimized: Get all user booth statistics in one query instead of N+1 queries
                $userStats = [];
                if ($users->isNotEmpty()) {
                    try {
                        // Get all booth counts grouped by user in one query
                        $boothCountsByUser = Booth::select('userid', 'status', DB::raw('count(*) as count'))
                            ->whereIn('status', [Booth::STATUS_RESERVED, Booth::STATUS_CONFIRMED, Booth::STATUS_PAID])
                            ->whereIn('userid', $users->pluck('id'))
                            ->groupBy('userid', 'status')
                            ->get()
                            ->groupBy('userid');
                        
                        // Build user stats array with optimized data
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
                                // #region agent log
                                DebugLogger::log(['user_id'=>$usr->id ?? 'N/A','error'=>$e->getMessage()], 'DashboardController.php:124', 'Error processing user stats');
                                // #endregion
                            }
                        }
                    } catch (\Exception $e) {
                        // Fallback to original method if optimized query fails
                        // #region agent log
                        DebugLogger::log(['error'=>$e->getMessage()], 'DashboardController.php:127', 'Optimized query failed, using fallback');
                        // #endregion
                        foreach ($users as $usr) {
                            try {
                                $userStats[] = [
                                    'id' => $usr->id,
                                    'username' => $usr->username,
                                    'type' => ($usr->type ?? '2') == '1' || ($usr->type ?? '2') == 1 ? 'Admin' : 'Sale',
                                    'status' => $usr->status ?? 'N/A',
                                    'reserve' => Booth::where('status', Booth::STATUS_RESERVED)
                                        ->where('userid', $usr->id)
                                        ->count(),
                                    'booking' => Booth::where('status', Booth::STATUS_CONFIRMED)
                                        ->where('userid', $usr->id)
                                        ->count(),
                                    'paid' => Booth::where('status', Booth::STATUS_PAID)
                                        ->where('userid', $usr->id)
                                        ->count(),
                                    'last_login' => $usr->last_login ?? null,
                                ];
                            } catch (\Exception $e) {
                                // #region agent log
                                DebugLogger::log(['user_id'=>$usr->id ?? 'N/A','error'=>$e->getMessage()], 'DashboardController.php:124', 'Error processing user stats');
                                // #endregion
                            }
                        }
                    }
                }
            } else {
                $reservedBooths = Booth::where('status', Booth::STATUS_RESERVED)
                    ->where('userid', $user->id)
                    ->count();
                $confirmedBooths = Booth::where('status', Booth::STATUS_CONFIRMED)
                    ->where('userid', $user->id)
                    ->count();
                $paidBooths = Booth::where('status', Booth::STATUS_PAID)
                    ->where('userid', $user->id)
                    ->count();
                
                $userStats = [];
            }
            
            // Get booking data with client information
            // Simplified query - bookings are directly linked to clients, booths are in JSON
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
                
                if (!$isAdmin) {
                    $query->where('u.id', $user->id);
                }
                
                $bookingData = $query->orderBy('b.date_book', 'desc')->limit(50)->get();
                
                // Process booking data to include booth information
                foreach ($bookingData as $booking) {
                    try {
                        $boothIds = json_decode($booking->boothid, true) ?? [];
                        if (!empty($boothIds)) {
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
                }
            } catch (\Exception $e) {
                $bookingData = collect([]);
            }
            
            // #region agent log
            DebugLogger::log([], 'DashboardController.php:180', 'Calculating stats');
            // #endregion
            
            try {
                $stats = [
                    'total_booths' => $totalBooths,
                    'available_booths' => $availableBooths,
                    'reserved_booths' => $reservedBooths,
                    'confirmed_booths' => $confirmedBooths,
                    'paid_booths' => $paidBooths,
                    'total_clients' => \App\Models\Client::count(),
                    'total_users' => \App\Models\User::count(),
                    'total_bookings' => \App\Models\Book::count(),
                ];
                // #region agent log
                DebugLogger::log($stats, 'DashboardController.php:195', 'Stats calculated');
                // #endregion
            } catch (\Exception $e) {
                // #region agent log
                DebugLogger::log(['error'=>$e->getMessage()], 'DashboardController.php:199', 'Error calculating stats');
                // #endregion
                $stats = [
                    'total_booths' => $totalBooths,
                    'available_booths' => $availableBooths,
                    'reserved_booths' => $reservedBooths ?? 0,
                    'confirmed_booths' => $confirmedBooths ?? 0,
                    'paid_booths' => $paidBooths ?? 0,
                    'total_clients' => 0,
                    'total_users' => 0,
                    'total_bookings' => 0,
                ];
            }
            
            // Get booking trends data for chart (last 30 days by default, configurable)
            $days = (int) $request->input('days', 30);
            $days = max(7, min(90, $days)); // Between 7 and 90 days
            
            $bookingTrendDates = [];
            $bookingTrendCounts = [];
            $revenueTrendData = [];
            
            try {
                for($i = $days - 1; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $dateStr = $date->format('Y-m-d');
                    
                    // Booking counts
                    $count = Book::whereDate('date_book', $dateStr)->count();
                    
                    // Revenue calculation from booths
                    $dayRevenue = 0;
                    $dayBookings = Book::whereDate('date_book', $dateStr)->get();
                    foreach ($dayBookings as $booking) {
                        try {
                            $boothIds = json_decode($booking->boothid, true) ?? [];
                            if (!empty($boothIds)) {
                                $dayRevenue += Booth::whereIn('id', $boothIds)
                                    ->where('status', Booth::STATUS_PAID)
                                    ->sum('price');
                            }
                        } catch (\Exception $e) {
                            // Skip if error
                        }
                    }
                    
                    $bookingTrendDates[] = $date->format('M d');
                    $bookingTrendCounts[] = $count;
                    $revenueTrendData[] = $dayRevenue;
                }
            } catch (\Exception $e) {
                for($i = $days - 1; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $bookingTrendDates[] = $date->format('M d');
                    $bookingTrendCounts[] = 0;
                    $revenueTrendData[] = 0;
                }
            }
            
            // Calculate additional metrics
            try {
                $todayBookings = Book::whereDate('date_book', today())->count();
            } catch (\Exception $e) {
                $todayBookings = 0;
            }
            
            try {
                $yesterdayBookings = Book::whereDate('date_book', today()->subDay())->count();
            } catch (\Exception $e) {
                $yesterdayBookings = 0;
            }
            
            try {
                $thisMonthBookings = Book::whereMonth('date_book', now()->month)
                    ->whereYear('date_book', now()->year)
                    ->count();
            } catch (\Exception $e) {
                $thisMonthBookings = 0;
            }
            
            try {
                $lastMonthBookings = Book::whereMonth('date_book', now()->subMonth()->month)
                    ->whereYear('date_book', now()->subMonth()->year)
                    ->count();
            } catch (\Exception $e) {
                $lastMonthBookings = 0;
            }
            
            // Calculate revenue metrics
            $totalRevenue = 0;
            $todayRevenue = 0;
            $thisMonthRevenue = 0;
            
            try {
                // Total revenue from paid booths
                $totalRevenue = (float) Booth::where('status', Booth::STATUS_PAID)->sum('price');
                
                // Today's revenue
                $todayBookingsList = Book::whereDate('date_book', today())->get();
                foreach ($todayBookingsList as $booking) {
                    try {
                        $boothIds = json_decode($booking->boothid, true) ?? [];
                        if (!empty($boothIds)) {
                            $todayRevenue += (float) Booth::whereIn('id', $boothIds)
                                ->where('status', Booth::STATUS_PAID)
                                ->sum('price');
                        }
                    } catch (\Exception $e) {
                        // Skip if error
                    }
                }
                
                // This month revenue
                $monthBookings = Book::whereMonth('date_book', now()->month)
                    ->whereYear('date_book', now()->year)
                    ->get();
                foreach ($monthBookings as $booking) {
                    try {
                        $boothIds = json_decode($booking->boothid, true) ?? [];
                        if (!empty($boothIds)) {
                            $thisMonthRevenue += (float) Booth::whereIn('id', $boothIds)
                                ->where('status', Booth::STATUS_PAID)
                                ->sum('price');
                        }
                    } catch (\Exception $e) {
                        // Skip if error
                    }
                }
            } catch (\Exception $e) {
                // Revenue calculation failed
            }
            
            // Calculate growth percentages
            $bookingGrowth = $yesterdayBookings > 0 
                ? (($todayBookings - $yesterdayBookings) / $yesterdayBookings) * 100 
                : ($todayBookings > 0 ? 100 : 0);
            
            $monthBookingGrowth = $lastMonthBookings > 0
                ? (($thisMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100
                : ($thisMonthBookings > 0 ? 100 : 0);
            
            // Get recent notifications (unread)
            $recentNotifications = [];
            try {
                $recentNotifications = \App\Models\Notification::where(function($query) use ($user) {
                        $query->where('user_id', $user->id)
                              ->orWhereNull('user_id'); // System notifications
                    })
                    ->where('is_read', false)
                    ->latest()
                    ->take(5)
                    ->get();
            } catch (\Exception $e) {
                $recentNotifications = collect([]);
            }
            
            // Get recent activity logs
            $recentActivities = [];
            try {
                $recentActivities = \App\Models\ActivityLog::with('user')
                    ->latest()
                    ->take(10)
                    ->get();
            } catch (\Exception $e) {
                $recentActivities = collect([]);
            }
            
            // Calculate occupancy rate
            $occupancyRate = $totalBooths > 0
                ? (($totalBooths - $availableBooths) / $totalBooths) * 100
                : 0;
            
            // Top performing users (by booth bookings)
            $topUsers = [];
            if ($isAdmin) {
                try {
                    $topUsers = collect($userStats)
                        ->sortByDesc(function($user) {
                            return $user['reserve'] + $user['booking'] + $user['paid'];
                        })
                        ->take(5)
                        ->values()
                        ->all();
                } catch (\Exception $e) {
                    $topUsers = [];
                }
            }
            
            // Prepare client data for display (simplified format)
            $clientData = [];
            try {
                foreach ($bookingData as $data) {
                    $clientData[] = [
                        'book_id' => $data->book_id ?? null,
                        'client_id' => $data->client_id ?? null,
                        'client_name' => $data->client_name ?? 'N/A',
                        'company' => $data->client_company ?? 'N/A',
                        'phone' => $data->client_phone ?? 'N/A',
                        'user_id' => $data->user_id ?? null,
                        'user_name' => $data->user_name ?? 'N/A',
                        'booth_count' => $data->booth_count ?? 0,
                        'booth_names' => $data->booth_names ?? 'N/A',
                        'date_book' => $data->date_book ? \Carbon\Carbon::parse($data->date_book)->format('Y-m-d H:i:s') : 'N/A',
                    ];
                }
            } catch (\Exception $e) {
                $clientData = [];
            }
            
            // Add enhanced metrics to stats array
            $stats['today_bookings'] = $todayBookings ?? 0;
            $stats['this_month_bookings'] = $thisMonthBookings ?? 0;
            $stats['booking_growth'] = round($bookingGrowth, 1);
            $stats['month_booking_growth'] = round($monthBookingGrowth, 1);
            $stats['total_revenue'] = $totalRevenue;
            $stats['today_revenue'] = $todayRevenue;
            $stats['this_month_revenue'] = $thisMonthRevenue;
            $stats['occupancy_rate'] = round($occupancyRate, 1);
            $stats['available_rate'] = round(100 - $occupancyRate, 1);
            
            // #region agent log
            DebugLogger::log(['has_stats'=>!empty($stats),'has_userStats'=>!empty($userStats),'has_clientData'=>!empty($clientData)], 'DashboardController.php:320', 'Returning AdminLTE view');
            // #endregion
            
            return view('dashboard.index-adminlte', compact(
                'stats', 
                'userStats', 
                'clientData', 
                'isAdmin',
                'bookingTrendDates',
                'bookingTrendCounts',
                'revenueTrendData',
                'recentNotifications',
                'recentActivities',
                'topUsers',
                'days'
            ));
        } catch (\Illuminate\Database\QueryException $e) {
            // Table doesn't exist - need to run migrations or import SQL
            return view('dashboard.setup-required', [
                'error' => 'Database tables not found. Please run migrations or import the booth booking SQL file.',
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            // General exception - return with empty data
            $stats = [
                'total_booths' => 0,
                'available_booths' => 0,
                'reserved_booths' => 0,
                'confirmed_booths' => 0,
                'paid_booths' => 0,
                'total_clients' => 0,
                'total_users' => 0,
                'total_bookings' => 0,
            ];
            $userStats = [];
            $clientData = [];
            $isAdmin = false;
            $bookingTrendDates = [];
            $bookingTrendCounts = [];
            $revenueTrendData = [];
            $recentNotifications = collect([]);
            $recentActivities = collect([]);
            $topUsers = [];
            $days = 30;
            
            return view('dashboard.index-adminlte', compact(
                'stats', 
                'userStats', 
                'clientData', 
                'isAdmin',
                'bookingTrendDates',
                'bookingTrendCounts',
                'revenueTrendData',
                'recentNotifications',
                'recentActivities',
                'topUsers',
                'days'
            ));
        }
    }
}

