<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\CategoryEvent;
use App\Models\UserEvent;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\DebugLogger;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // #region agent log
        DebugLogger::log(['session_id'=>session()->getId()], 'AdminDashboardController.php:21', 'Dashboard index method entry');
        // #endregion
        
        $stats = [
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 1)->count(),
            'total_categories' => CategoryEvent::where('status', 1)->count(),
            'total_users' => UserEvent::count(),
            'total_admins' => Admin::count(),
            'upcoming_events' => Event::where('start_date', '>=', now())->count(),
            'past_events' => Event::where('end_date', '<', now())->count(),
        ];

        // #region agent log
        DebugLogger::log(['stats'=>$stats], 'AdminDashboardController.php:35', 'Stats calculated');
        // #endregion

        // Recent events
        $recentEvents = Event::with('category')
            ->latest('created_at')
            ->take(10)
            ->get();

        // #region agent log
        $eventSample = $recentEvents->first();
        DebugLogger::log(['count'=>$recentEvents->count(),'first_event_id'=>$eventSample?->id,'first_start_date'=>($eventSample?->start_date ?? null),'first_start_date_type'=>gettype($eventSample?->start_date ?? null),'first_start_date_is_carbon'=>(is_object($eventSample?->start_date) && method_exists($eventSample?->start_date, 'format') ? 'YES' : 'NO')], 'AdminDashboardController.php:47', 'Recent events loaded');
        // #endregion

        // Events by status
        $eventsByStatus = [
            'active' => Event::where('status', 1)->count(),
            'inactive' => Event::where('status', 0)->count(),
        ];

        // Top categories
        $topCategories = CategoryEvent::withCount('events')
            ->orderBy('events_count', 'desc')
            ->take(5)
            ->get();

        // #region agent log
        DebugLogger::log(['recent_events_count'=>$recentEvents->count(),'top_categories_count'=>$topCategories->count()], 'AdminDashboardController.php:62', 'Returning view with data');
        // #endregion

        return view('admin.dashboard', compact('stats', 'recentEvents', 'eventsByStatus', 'topCategories'));
    }
}

