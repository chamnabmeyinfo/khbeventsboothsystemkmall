<?php

namespace App\Http\Middleware;

use App\Helpers\DebugLogger;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // #region agent log
        if ($request->method() !== 'GET') {
            DebugLogger::log(
                [
                    'method' => $request->method(),
                    'uri' => $request->path(),
                    'session_id' => $request->session()->getId(),
                    'token_from_request' => $request->input('_token'),
                    'token_from_header' => $request->header('X-CSRF-TOKEN'),
                    'token_from_session' => session()->token(),
                    'all_post_data' => array_keys($request->all()),
                    'content_type' => $request->header('Content-Type'),
                ],
                'VerifyCsrfToken.php:29',
                'CSRF check started'
            );
        }
        // #endregion

        try {
            $response = parent::handle($request, $next);

            // #region agent log
            if ($request->method() !== 'GET') {
                DebugLogger::log(
                    [
                        'method' => $request->method(),
                        'uri' => $request->path(),
                    ],
                    'VerifyCsrfToken.php:38',
                    'CSRF check passed'
                );
            }
            // #endregion

            return $response;
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            // #region agent log
            DebugLogger::log(
                [
                    'method' => $request->method(),
                    'uri' => $request->path(),
                    'session_id' => $request->session()->getId(),
                    'token_from_request' => $request->input('_token'),
                    'token_from_header' => $request->header('X-CSRF-TOKEN'),
                    'token_from_session' => session()->token(),
                    'error' => $e->getMessage(),
                ],
                'VerifyCsrfToken.php:45',
                'CSRF token mismatch'
            );
            // #endregion

            throw $e;
        }
    }
}
