<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booth;
use App\Models\Client;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     * OPTIMIZED: Uses aggregated queries and caching for better performance
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        $isAdmin = false;
        try {
            $isAdmin = $user->isAdmin();
        } catch (\Exception $e) {
            $isAdmin = false;
        }
        
        try {
            // OPTIMIZED: Use a single aggregated query for booth statistics
            // Instead of 5+ separate COUNT queries
            $boothStats = Booth::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status IN (1, 4) THEN 1 ELSE 0 END) as available,
                SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as reserved,
                SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 5 THEN 1 ELSE 0 END) as paid
            ');
            
            // If not admin, filter by user
            if (!$isAdmin) {
                $boothStats->where('userid', $user->id);
            }
            
            $boothStats = $boothStats->first();
            
            $totalBooths = Booth::count(); // Always show total across all users
            $availableBooths = Booth::whereIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])->count();
            $reservedBooths = $boothStats->reserved ?? 0;
            $confirmedBooths = $boothStats->confirmed ?? 0;
            $paidBooths = $boothStats->paid ?? 0;
            
            // OPTIMIZED: Use single aggregated query for user stats instead of N queries
            $userStats = [];
            if ($isAdmin) {
                // Get user stats with a single optimized query using subqueries
                $userStats = User::select('user.*')
                    ->selectRaw('(SELECT COUNT(*) FROM booth WHERE booth.userid = user.id AND booth.status = 3) as reserve')
                    ->selectRaw('(SELECT COUNT(*) FROM booth WHERE booth.userid = user.id AND booth.status = 2) as booking')
                    ->selectRaw('(SELECT COUNT(*) FROM booth WHERE booth.userid = user.id AND booth.status = 5) as paid')
                    ->orderBy('username')
                    ->get()
                    ->map(function($usr) {
                        return [
                            'id' => $usr->id,
                            'username' => $usr->username,
                            'type' => ($usr->type ?? '2') == '1' || ($usr->type ?? '2') == 1 ? 'Admin' : 'Sale',
                            'status' => $usr->status ?? 'N/A',
                            'reserve' => $usr->reserve,
                            'booking' => $usr->booking,
                            'paid' => $usr->paid,
                            'last_login' => $usr->last_login ?? null,
                        ];
                    })->toArray();
            }

            // OPTIMIZED: Use cached counts for relatively static data
            $totalClients = Cache::remember('dashboard_total_clients', 60, function() {
                return Client::count();
            });
            $totalUsers = Cache::remember('dashboard_total_users', 60, function() {
                return User::count();
            });
            $totalBookings = Cache::remember('dashboard_total_bookings', 60, function() {
                return Book::count();
            });
            
            $stats = [
                'total_booths' => $totalBooths,
                'available_booths' => $availableBooths,
                'reserved_booths' => $reservedBooths,
                'confirmed_booths' => $confirmedBooths,
                'paid_booths' => $paidBooths,
                'total_clients' => $totalClients,
                'total_users' => $totalUsers,
                'total_bookings' => $totalBookings,
            ];

            // OPTIMIZED: Booking data query with limits
            $bookingData = collect();
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
                
                // Limit results for performance
                $bookingData = $query->orderBy('b.date_book', 'desc')->limit(100)->get();
            } catch (\Exception $e) {
                $bookingData = collect([]);
            }
            
            // Transform booking data
            $clientData = $bookingData->map(function($data) {
                return [
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
            })->toArray();
            
            return view('dashboard.index-adminlte', compact('stats', 'userStats', 'clientData', 'isAdmin'));
            
        } catch (\Illuminate\Database\QueryException $e) {
            return view('dashboard.setup-required', [
                'error' => 'Database tables not found. Please run migrations or import the booth booking SQL file.',
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
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
