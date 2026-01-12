<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'model'])->latest();

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('route', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        // Statistics
        $stats = [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week_logs' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'unique_users' => ActivityLog::distinct('user_id')->count('user_id'),
        ];

        // Get unique users for filter
        $users = \App\Models\User::whereIn('id', ActivityLog::distinct()->pluck('user_id'))->orderBy('username')->get();

        // Get unique actions
        $actions = ActivityLog::distinct()->pluck('action')->filter()->sort()->values();

        // Get unique model types
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->filter()->sort()->values();

        return view('activity-logs.index', compact('logs', 'stats', 'users', 'actions', 'modelTypes'));
    }

    /**
     * Show specific activity log
     */
    public function show(ActivityLog $activityLog)
    {
        $activityLog->load('user');
        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Apply same filters as index
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        // ... add other filters

        $logs = $query->get();

        $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Date', 'User', 'Action', 'Model', 'Description', 'IP Address']);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->username : 'System',
                    $log->action,
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->description ?? 'N/A',
                    $log->ip_address ?? 'N/A',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

