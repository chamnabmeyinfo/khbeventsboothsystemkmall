<?php

namespace App\Http\Controllers;

use App\Helpers\DebugLogger;
use App\Helpers\DeviceDetector;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * Display the dashboard
     * Matches Yii DashboardController actionIndex
     */
    public function index(Request $request)
    {
        // #region agent log
        DebugLogger::log(['method' => $request->method(), 'session_id' => $request->session()->getId(), 'csrf_token' => csrf_token(), 'session_token' => session()->token(), 'is_authenticated' => auth()->check(), 'user_id' => auth()->id(), 'all_request_data' => array_keys($request->all())], 'DashboardController.php:19', 'Dashboard accessed');
        // #endregion

        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // #region agent log
        DebugLogger::log(['user_id' => $user->id ?? 'N/A', 'username' => $user->username ?? 'N/A', 'type' => ($user->type ?? 'N/A'), 'status' => ($user->status ?? 'N/A')], 'DashboardController.php:46', 'User retrieved, checking admin status');
        // #endregion

        try {
            $isAdmin = $user->isAdmin();
        } catch (\Exception $e) {
            DebugLogger::log(['error' => $e->getMessage()], 'DashboardController.php:56', 'isAdmin() failed');
            $isAdmin = false;
        } catch (\Throwable $e) {
            DebugLogger::log(['error' => $e->getMessage()], 'DashboardController.php:61', 'isAdmin() failed (Throwable)');
            $isAdmin = false;
        }

        try {
            // Get days parameter for trends
            $days = (int) $request->input('days', 30);
            $days = max(7, min(90, $days)); // Between 7 and 90 days

            // Get dashboard data from service
            $dashboardData = $this->dashboardService->getDashboardData(
                $isAdmin ? null : $user->id,
                $isAdmin,
                $days
            );

            // Detect device type
            $device = DeviceDetector::detect($request);
            $viewName = DeviceDetector::getViewName('dashboard.index', $request);

            // #region agent log
            DebugLogger::log(['device' => $device, 'viewName' => $viewName, 'has_stats' => ! empty($dashboardData['stats']), 'has_userStats' => ! empty($dashboardData['userStats']), 'has_clientData' => ! empty($dashboardData['clientData'])], 'DashboardController.php:320', 'Returning view based on device');
            // #endregion

            return view($viewName, array_merge($dashboardData, [
                'isAdmin' => $isAdmin,
                'device' => $device,
            ]));

        } catch (\Illuminate\Database\QueryException $e) {
            // Table doesn't exist - need to run migrations or import SQL
            return view('dashboard.setup-required', [
                'error' => 'Database tables not found. Please run migrations or import the booth booking SQL file.',
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Return with empty data on error
            $stats = [
                'total_booths' => 0,
                'available_booths' => 0,
                'reserved_booths' => 0,
                'confirmed_booths' => 0,
                'paid_booths' => 0,
                'total_clients' => 0,
                'total_users' => 0,
                'total_bookings' => 0,
                'total_revenue' => 0,
                'today_revenue' => 0,
                'this_month_revenue' => 0,
            ];

            return view('dashboard.index-adminlte', [
                'stats' => $stats,
                'userStats' => [],
                'clientData' => [],
                'isAdmin' => false,
                'bookingTrendDates' => [],
                'bookingTrendCounts' => [],
                'revenueTrendData' => [],
                'recentNotifications' => collect([]),
                'recentActivities' => collect([]),
                'topUsers' => [],
                'days' => 30,
            ]);
        }
    }
}
