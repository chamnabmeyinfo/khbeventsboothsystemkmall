<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DeveloperToolsController extends Controller
{
    // #region agent log helper
    private function dbg(string $hypothesisId, string $location, string $message, array $data = [], string $runId = 'run1'): void
    {
        $payload = [
            'sessionId' => 'debug-session',
            'runId' => $runId,
            'hypothesisId' => $hypothesisId,
            'location' => $location,
            'message' => $message,
            'data' => $data,
            'timestamp' => (int) (microtime(true) * 1000),
        ];
        $line = json_encode($payload) . PHP_EOL;
        // primary (workspace path from instructions)
        @file_put_contents('c:\\xampp\\htdocs\\KHB\\khbevents\\boothsystemv1\\.cursor\\debug.log', $line, FILE_APPEND);
        // fallback for server path
        @file_put_contents(storage_path('logs/debug.log'), $line, FILE_APPEND);
    }
    // #endregion

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
            $this->dbg('H3', 'DeveloperToolsController@migrate', 'unauthorized', ['user_id' => auth()->id()]);
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $this->dbg('H1', 'DeveloperToolsController@migrate', 'migrate_start', [
                'php_version' => phpversion(),
                'user_id' => auth()->id(),
            ]);

            $result = Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            $this->dbg('H1', 'DeveloperToolsController@migrate', 'migrate_artisan_result', [
                'result' => $result,
                'output_type' => gettype($output),
                'output' => $output,
            ]);

            $this->dbg('H2', 'DeveloperToolsController@migrate', 'migrate_done', [
                'output' => $output,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Migrations executed.',
                'output' => (string) $output,
            ]);
        } catch (\Throwable $e) {
            $this->dbg('H2', 'DeveloperToolsController@migrate', 'migrate_error', [
                'error' => $e->getMessage(),
                'type' => get_class($e),
            ]);
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
