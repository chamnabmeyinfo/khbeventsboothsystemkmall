<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DeveloperToolsController extends Controller
{
    /**
     * Show the developer tools page.
     */
    public function index()
    {
        return view('developer.tools');
    }

    /**
     * Run database migrations (pull/update DB schema).
     * Restricted to admins.
     */
    public function migrate(Request $request)
    {
        // Safety: ensure only admins
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = (string) Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Migrations executed.',
                'output' => $output,
            ]);
        } catch (\Throwable $e) {
            \Log::error('DeveloperTools migrate failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
