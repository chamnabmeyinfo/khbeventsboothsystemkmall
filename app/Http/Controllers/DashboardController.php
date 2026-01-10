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
            
            // Booth status counts in one query (instead of multiple counts)
            $statusCounts = Booth::query()
                ->select('status', DB::raw('COUNT(*) as cnt'))
                ->groupBy('status')
                ->pluck('cnt', 'status');

            $totalBooths = (int) $statusCounts->sum();
            $availableBooths = (int) (($statusCounts[Booth::STATUS_AVAILABLE] ?? 0) + ($statusCounts[Booth::STATUS_HIDDEN] ?? 0));
            
            // Get statistics based on user type
            if ($isAdmin) {
                $reservedBooths = (int) ($statusCounts[Booth::STATUS_RESERVED] ?? 0);
                $confirmedBooths = (int) ($statusCounts[Booth::STATUS_CONFIRMED] ?? 0);
                $paidBooths = (int) ($statusCounts[Booth::STATUS_PAID] ?? 0);
            
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
            
            // Get per-user booth statistics (avoid N+1 queries: 3 counts per user)
            $userBoothCounts = Booth::query()
                ->select('userid', 'status', DB::raw('COUNT(*) as cnt'))
                ->whereIn('status', [Booth::STATUS_RESERVED, Booth::STATUS_CONFIRMED, Booth::STATUS_PAID])
                ->groupBy('userid', 'status')
                ->get()
                ->groupBy('userid')
                ->map(function ($rows) {
                    return $rows->pluck('cnt', 'status')->map(fn ($v) => (int) $v)->all();
                })
                ->all();

            // Get user statistics
            $userStats = [];
            foreach ($users as $usr) {
                try {
                    $counts = $userBoothCounts[$usr->id] ?? [];
                    $userStats[] = [
                        'id' => $usr->id,
                        'username' => $usr->username,
                        'type' => ($usr->type ?? '2') == '1' || ($usr->type ?? '2') == 1 ? 'Admin' : 'Sale',
                        'status' => $usr->status ?? 'N/A',
                        'reserve' => (int) ($counts[Booth::STATUS_RESERVED] ?? 0),
                        'booking' => (int) ($counts[Booth::STATUS_CONFIRMED] ?? 0),
                        'paid' => (int) ($counts[Booth::STATUS_PAID] ?? 0),
                        'last_login' => $usr->last_login ?? null,
                    ];
                } catch (\Exception $e) {
                    // #region agent log
                    DebugLogger::log(['user_id'=>$usr->id ?? 'N/A','error'=>$e->getMessage()], 'DashboardController.php:124', 'Error processing user stats');
                    // #endregion
                }
            }
        } else {
            $myCounts = Booth::query()
                ->select('status', DB::raw('COUNT(*) as cnt'))
                ->where('userid', $user->id)
                ->whereIn('status', [Booth::STATUS_RESERVED, Booth::STATUS_CONFIRMED, Booth::STATUS_PAID])
                ->groupBy('status')
                ->pluck('cnt', 'status');

            $reservedBooths = (int) ($myCounts[Booth::STATUS_RESERVED] ?? 0);
            $confirmedBooths = (int) ($myCounts[Booth::STATUS_CONFIRMED] ?? 0);
            $paidBooths = (int) ($myCounts[Booth::STATUS_PAID] ?? 0);
            
            $userStats = [];
        }
            
            // Get booking data with client information
            // Matches the Yii query structure
            try {
                $query = DB::table('client as c')
                    ->join('booth as bth', 'bth.client_id', '=', 'c.id')
                    ->join('book as b', 'c.id', '=', 'b.clientid')
                    ->join('user as u', 'u.id', '=', 'b.userid')
                    ->leftJoin('category as cat', 'cat.id', '=', 'bth.category_id')
                    ->leftJoin('category as sub', 'sub.id', '=', 'bth.sub_category_id')
                    ->select(
                        'bth.booth_number as booth_name',
                        'bth.status as booth_status',
                        'c.id as client_id',
                        'c.name as client_name',
                        'c.company as client_company',
                        'c.phone_number as client_phone',
                        'b.id as book_id',
                        'u.id as user_id',
                        'u.username as user_name',
                        'u.type as user_type',
                        'u.status as user_status',
                        'u.last_login',
                        'b.date_book',
                        'b.type as book_type',
                        'cat.name as category_name',
                        'sub.name as sub_category_name'
                    );
                
                if (!$isAdmin) {
                    $query->where('u.id', $user->id);
                }
                
                // Protect dashboard load times on large datasets.
                $bookingData = $query->orderBy('b.date_book', 'desc')->limit(1000)->get();
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
            
            // Get recent bookings for the view
            try {
                // #region agent log
                DebugLogger::log([], 'DashboardController.php:216', 'Fetching recent bookings');
                // #endregion
                $recentBookings = \App\Models\Book::with(['client', 'user'])
                    ->latest('date_book')
                    ->take(10)
                    ->get();
                // #region agent log
                DebugLogger::log(['count'=>count($recentBookings)], 'DashboardController.php:223', 'Recent bookings fetched');
                // #endregion
            } catch (\Exception $e) {
                // #region agent log
                DebugLogger::log(['error'=>$e->getMessage()], 'DashboardController.php:227', 'Error fetching recent bookings');
                // #endregion
                $recentBookings = collect([]);
            }
            
            // Get booking trends data for chart (last 7 days)
            $bookingTrendDates = [];
            $bookingTrendCounts = [];
            try {
                $start = now()->subDays(6)->startOfDay();
                $trend = Book::query()
                    ->selectRaw('DATE(date_book) as day, COUNT(*) as cnt')
                    ->where('date_book', '>=', $start)
                    ->groupBy('day')
                    ->pluck('cnt', 'day');

                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $key = $date->toDateString();
                    $bookingTrendDates[] = $date->format('M d');
                    $bookingTrendCounts[] = (int) ($trend[$key] ?? 0);
                }
            } catch (\Exception $e) {
                for($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $bookingTrendDates[] = $date->format('M d');
                    $bookingTrendCounts[] = 0;
                }
            }
            
            // Prepare client data in Yii format (matching the exact structure from Yii dashboard)
            $clientData = [];
            try {
                foreach ($bookingData as $data) {
                    $clientData[] = [
                        'book_id' => $data->book_id,
                        'client_name' => $data->client_name,
                        'company' => $data->client_company,
                        'phone' => $data->client_phone,
                        'user_name' => $data->user_name,
                        'booth_number' => $data->booth_name,
                        'status' => $data->booth_status,
                        'category_name' => $data->category_name,
                        'sub_category_name' => $data->sub_category_name,
                        'date_book' => $data->date_book ? \Carbon\Carbon::parse($data->date_book)->format('Y-m-d H:i:s') : 'N/A',
                    ];
                }
            } catch (\Exception $e) {
                $clientData = [];
            }
            
            // #region agent log
            DebugLogger::log(['has_stats'=>!empty($stats),'has_userStats'=>!empty($userStats),'has_clientData'=>!empty($clientData)], 'DashboardController.php:272', 'Returning AdminLTE view');
            // #endregion
            
            return view('dashboard.index-adminlte', compact('stats', 'userStats', 'clientData', 'isAdmin'));
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
            
            return view('dashboard.index-adminlte', compact('stats', 'userStats', 'clientData', 'isAdmin'));
        }
    }
}
