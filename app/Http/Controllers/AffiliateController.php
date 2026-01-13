<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use App\Models\FloorPlan;
use App\Models\AffiliateClick;
use App\Models\AffiliateBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AffiliateController extends Controller
{
    /**
     * Display affiliate management dashboard
     */
    public function index(Request $request)
    {
        // Check permissions - only admins or users with affiliate management permission
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('affiliates.view'))) {
            abort(403, 'Unauthorized access');
        }

        // Get filter parameters
        $userId = $request->input('user_id');
        $floorPlanId = $request->input('floor_plan_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        // Get all users who have generated affiliate bookings (or all users if admin)
        $query = User::query();
        
        if ($search) {
            $query->where('username', 'like', "%{$search}%");
        }

        // Get users with affiliate statistics
        $users = $query->get()->map(function($user) use ($userId, $floorPlanId, $dateFrom, $dateTo) {
            // Get affiliate bookings for this user
            $affiliateBookingsQuery = Book::where('affiliate_user_id', $user->id);
            
            // Apply filters
            if ($floorPlanId) {
                $affiliateBookingsQuery->where('floor_plan_id', $floorPlanId);
            }
            
            if ($dateFrom) {
                $affiliateBookingsQuery->where('date_book', '>=', $dateFrom);
            }
            
            if ($dateTo) {
                $affiliateBookingsQuery->where('date_book', '<=', $dateTo . ' 23:59:59');
            }
            
            $affiliateBookings = $affiliateBookingsQuery->get();
            
            // Calculate statistics
            $totalBookings = $affiliateBookings->count();
            $totalRevenue = $affiliateBookings->sum(function($booking) {
                $booths = $booking->booths();
                return $booths->sum('price') ?? 0;
            });
            
            // Get unique clients
            $uniqueClients = $affiliateBookings->pluck('clientid')->unique()->count();
            $uniqueFloorPlans = $affiliateBookings->pluck('floor_plan_id')->unique()->count();
            $avgBookingValue = $totalBookings > 0 ? round($totalRevenue / $totalBookings, 2) : 0;
            $lastBookingAt = $affiliateBookings->max('date_book');
            $firstBookingAt = $affiliateBookings->min('date_book');
            
            // Get bookings by floor plan
            $bookingsByFloorPlan = $affiliateBookings->groupBy('floor_plan_id')->map(function($bookings) {
                return [
                    'count' => $bookings->count(),
                    'revenue' => $bookings->sum(function($booking) {
                        $booths = $booking->booths();
                        return $booths->sum('price') ?? 0;
                    })
                ];
            });
            
            // Get affiliate clicks
            $affiliateClicksQuery = AffiliateClick::where('affiliate_user_id', $user->id);
            if ($floorPlanId) {
                $affiliateClicksQuery->where('floor_plan_id', $floorPlanId);
            }
            if ($dateFrom) {
                $affiliateClicksQuery->where('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $affiliateClicksQuery->where('created_at', '<=', $dateTo . ' 23:59:59');
            }
            $totalClicks = $affiliateClicksQuery->count();
            
            // Calculate conversion rate
            $conversionRate = $totalClicks > 0 ? round(($totalBookings / $totalClicks) * 100, 2) : 0;
            
            // Get activity timeline (bookings and clicks combined, sorted by date)
            $activities = collect();
            
            // Add bookings as activities
            foreach ($affiliateBookings as $booking) {
                $activities->push([
                    'type' => 'booking',
                    'date' => $booking->date_book,
                    'booking' => $booking,
                    'revenue' => $booking->booths()->sum('price') ?? 0,
                ]);
            }
            
            // Add clicks as activities
            $clicks = $affiliateClicksQuery->orderBy('created_at', 'desc')->get();
            foreach ($clicks as $click) {
                $activities->push([
                    'type' => 'click',
                    'date' => $click->created_at,
                    'click' => $click,
                ]);
            }
            
            // Sort by date descending
            $activities = $activities->sortByDesc(function($activity) {
                return $activity['date'];
            })->values();
            
            // Calculate total benefits based on benefit settings
            $totalBenefits = $this->calculateTotalBenefits($user->id, $totalRevenue, $totalBookings, $uniqueClients, $floorPlanId);
            
            return [
                'user' => $user,
                'total_bookings' => $totalBookings,
                'total_revenue' => $totalRevenue,
                'unique_clients' => $uniqueClients,
                'unique_floor_plans' => $uniqueFloorPlans,
                'avg_booking_value' => $avgBookingValue,
                'last_booking_at' => $lastBookingAt,
                'first_booking_at' => $firstBookingAt,
                'bookings_by_floor_plan' => $bookingsByFloorPlan,
                'recent_bookings' => $affiliateBookings->sortByDesc('date_book')->take(5),
                'total_clicks' => $totalClicks,
                'conversion_rate' => $conversionRate,
                'activities' => $activities->take(20), // Last 20 activities
                'total_benefits' => $totalBenefits,
            ];
        })->filter(function($data) use ($userId) {
            // Filter by user if specified
            if ($userId) {
                return $data['user']->id == $userId;
            }
            // Only show users with affiliate bookings
            return $data['total_bookings'] > 0;
        })->sortByDesc('total_bookings');

        // Get all floor plans for filter
        $floorPlans = FloorPlan::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // Get all users for filter
        $allUsers = User::where('status', 1)
            ->orderBy('username', 'asc')
            ->get();

        return view('affiliates.index', compact(
            'users',
            'floorPlans',
            'allUsers',
            'userId',
            'floorPlanId',
            'dateFrom',
            'dateTo',
            'search'
        ));
    }

    /**
     * Show detailed affiliate statistics for a specific user
     */
    public function show($id, Request $request)
    {
        $user = User::findOrFail($id);
        
        // Check permissions
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('affiliates.view') && Auth::user()->id != $id)) {
            abort(403, 'Unauthorized access');
        }

        // Get filter parameters
        $floorPlanId = $request->input('floor_plan_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Get affiliate bookings
        $bookingsQuery = Book::where('affiliate_user_id', $user->id)
            ->with(['client', 'floorPlan', 'user', 'affiliateUser']);
        
        if ($floorPlanId) {
            $bookingsQuery->where('floor_plan_id', $floorPlanId);
        }
        
        if ($dateFrom) {
            $bookingsQuery->where('date_book', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $bookingsQuery->where('date_book', '<=', $dateTo . ' 23:59:59');
        }
        
        $bookings = $bookingsQuery->orderBy('date_book', 'desc')->paginate(20);

        // Calculate statistics
        $totalBookings = Book::where('affiliate_user_id', $user->id)->count();
        $totalRevenue = Book::where('affiliate_user_id', $user->id)->get()->sum(function($booking) {
            $booths = $booking->booths();
            return $booths->sum('price') ?? 0;
        });
        $uniqueClients = Book::where('affiliate_user_id', $user->id)
            ->distinct('clientid')
            ->count('clientid');
        $uniqueFloorPlans = Book::where('affiliate_user_id', $user->id)
            ->distinct('floor_plan_id')
            ->count('floor_plan_id');
        $avgBookingValue = $totalBookings > 0 ? round($totalRevenue / $totalBookings, 2) : 0;
        $lastBookingAt = Book::where('affiliate_user_id', $user->id)->max('date_book');
        $firstBookingAt = Book::where('affiliate_user_id', $user->id)->min('date_book');

        // Get bookings by floor plan
        $bookingsByFloorPlan = Book::where('affiliate_user_id', $user->id)
            ->select('floor_plan_id', DB::raw('count(*) as count'))
            ->groupBy('floor_plan_id')
            ->with('floorPlan')
            ->get();

        // Get bookings by month (last 12 months)
        $bookingsByMonth = Book::where('affiliate_user_id', $user->id)
            ->where('date_book', '>=', now()->subMonths(12))
            ->select(DB::raw('DATE_FORMAT(date_book, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Get all floor plans for filter
        $floorPlans = FloorPlan::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        // Calculate total benefits
        $totalBenefits = $this->calculateTotalBenefits($user->id, $totalRevenue, $totalBookings, $uniqueClients, $floorPlanId);

        return view('affiliates.show', compact(
            'user',
            'bookings',
            'totalBookings',
            'totalRevenue',
            'uniqueClients',
            'uniqueFloorPlans',
            'avgBookingValue',
            'lastBookingAt',
            'firstBookingAt',
            'bookingsByFloorPlan',
            'bookingsByMonth',
            'floorPlans',
            'floorPlanId',
            'dateFrom',
            'dateTo',
            'totalBenefits'
        ));
    }

    /**
     * Get affiliate statistics API endpoint
     */
    public function statistics(Request $request)
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('affiliates.view'))) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userId = $request->input('user_id');
        $floorPlanId = $request->input('floor_plan_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = Book::whereNotNull('affiliate_user_id');
        
        if ($userId) {
            $query->where('affiliate_user_id', $userId);
        }
        
        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        }
        
        if ($dateFrom) {
            $query->where('date_book', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->where('date_book', '<=', $dateTo . ' 23:59:59');
        }

        $bookings = $query->get();
        
        $statistics = [
            'total_bookings' => $bookings->count(),
            'total_revenue' => $bookings->sum(function($booking) {
                $booths = $booking->booths();
                return $booths->sum('price') ?? 0;
            }),
            'unique_clients' => $bookings->pluck('clientid')->unique()->count(),
            'unique_affiliates' => $bookings->pluck('affiliate_user_id')->unique()->count(),
        ];

        return response()->json($statistics);
    }

    /**
     * Export affiliate performance to CSV
     */
    public function export(Request $request)
    {
        if (!Auth::check() || (!Auth::user()->isAdmin() && !Auth::user()->hasPermission('affiliates.view'))) {
            abort(403, 'Unauthorized access');
        }

        $floorPlanId = $request->input('floor_plan_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('search');

        $query = User::query();
        if ($search) {
            $query->where('username', 'like', "%{$search}%");
        }

        $users = $query->get()->map(function($user) use ($floorPlanId, $dateFrom, $dateTo) {
            $affiliateBookingsQuery = Book::where('affiliate_user_id', $user->id);

            if ($floorPlanId) {
                $affiliateBookingsQuery->where('floor_plan_id', $floorPlanId);
            }
            if ($dateFrom) {
                $affiliateBookingsQuery->where('date_book', '>=', $dateFrom);
            }
            if ($dateTo) {
                $affiliateBookingsQuery->where('date_book', '<=', $dateTo . ' 23:59:59');
            }

            $affiliateBookings = $affiliateBookingsQuery->get();
            $totalBookings = $affiliateBookings->count();
            $totalRevenue = $affiliateBookings->sum(function($booking) {
                $booths = $booking->booths();
                return $booths->sum('price') ?? 0;
            });
            $uniqueClients = $affiliateBookings->pluck('clientid')->unique()->count();
            $uniqueFloorPlans = $affiliateBookings->pluck('floor_plan_id')->unique()->count();
            $avgBookingValue = $totalBookings > 0 ? round($totalRevenue / $totalBookings, 2) : 0;
            $lastBookingAt = $affiliateBookings->max('date_book');

            return [
                'user' => $user,
                'total_bookings' => $totalBookings,
                'total_revenue' => $totalRevenue,
                'unique_clients' => $uniqueClients,
                'unique_floor_plans' => $uniqueFloorPlans,
                'avg_booking_value' => $avgBookingValue,
                'last_booking_at' => $lastBookingAt,
            ];
        })->filter(function($data) {
            return $data['total_bookings'] > 0;
        });

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="affiliate_performance.csv"',
        ];

        $callback = function() use ($users) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['User', 'Bookings', 'Revenue', 'Unique Clients', 'Unique Floor Plans', 'Avg/Booking', 'Last Booking']);
            foreach ($users as $row) {
                fputcsv($output, [
                    $row['user']->username,
                    $row['total_bookings'],
                    number_format($row['total_revenue'], 2, '.', ''),
                    $row['unique_clients'],
                    $row['unique_floor_plans'],
                    number_format($row['avg_booking_value'], 2, '.', ''),
                    $row['last_booking_at'] ? \Carbon\Carbon::parse($row['last_booking_at'])->toDateString() : '',
                ]);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Calculate total benefits for a user based on benefit settings
     */
    private function calculateTotalBenefits($userId, $totalRevenue, $totalBookings, $uniqueClients, $floorPlanId = null)
    {
        // Get all active benefits applicable to this user
        $benefits = AffiliateBenefit::active()
            ->forUser($userId)
            ->when($floorPlanId, function($query) use ($floorPlanId) {
                return $query->forFloorPlan($floorPlanId);
            })
            ->orderBy('priority', 'desc')
            ->get();

        $totalBenefits = 0;
        $benefitBreakdown = [];

        foreach ($benefits as $benefit) {
            $benefitAmount = $benefit->calculateBenefit(
                $totalRevenue,
                $totalBookings,
                $uniqueClients,
                $floorPlanId,
                $userId
            );

            if ($benefitAmount > 0) {
                $totalBenefits += $benefitAmount;
                $benefitBreakdown[] = [
                    'benefit' => $benefit,
                    'amount' => $benefitAmount,
                ];
            }
        }

        return [
            'total' => round($totalBenefits, 2),
            'breakdown' => $benefitBreakdown,
        ];
    }
}
