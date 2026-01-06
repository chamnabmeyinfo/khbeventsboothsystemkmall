<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        return view('settings.index');
    }

    /**
     * Clear application cache
     */
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            $message = 'Application cache cleared successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error clearing cache: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Clear config cache
     */
    public function clearConfig(Request $request)
    {
        try {
            Artisan::call('config:clear');
            $message = 'Configuration cache cleared successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error clearing config cache: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Clear route cache
     */
    public function clearRoute(Request $request)
    {
        try {
            Artisan::call('route:clear');
            $message = 'Route cache cleared successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error clearing route cache: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Clear view cache
     */
    public function clearView(Request $request)
    {
        try {
            Artisan::call('view:clear');
            $message = 'View cache cleared successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error clearing view cache: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Clear all caches
     */
    public function clearAll(Request $request)
    {
        $results = [];
        
        try {
            Artisan::call('cache:clear');
            $results[] = 'Application cache cleared.';
        } catch (\Exception $e) {
            $results[] = 'Error clearing application cache: ' . $e->getMessage();
        }

        try {
            Artisan::call('config:clear');
            $results[] = 'Configuration cache cleared.';
        } catch (\Exception $e) {
            $results[] = 'Error clearing config cache: ' . $e->getMessage();
        }

        try {
            Artisan::call('route:clear');
            $results[] = 'Route cache cleared.';
        } catch (\Exception $e) {
            $results[] = 'Error clearing route cache: ' . $e->getMessage();
        }

        try {
            Artisan::call('view:clear');
            $results[] = 'View cache cleared.';
        } catch (\Exception $e) {
            $results[] = 'Error clearing view cache: ' . $e->getMessage();
        }

        $message = implode(' ', $results);
        $hasErrors = strpos($message, 'Error') !== false;
        $type = $hasErrors ? 'error' : 'success';

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $hasErrors ? 500 : 200,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }

    /**
     * Optimize application
     */
    public function optimize(Request $request)
    {
        try {
            Artisan::call('optimize:clear');
            $message = 'Application optimized successfully.';
            $type = 'success';
        } catch (\Exception $e) {
            $message = 'Error optimizing application: ' . $e->getMessage();
            $type = 'error';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => $type === 'success' ? 200 : 500,
                'message' => $message
            ]);
        }

        return back()->with($type, $message);
    }
}
