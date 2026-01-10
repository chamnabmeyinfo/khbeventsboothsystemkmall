<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientPortal
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('client_id')) {
            return redirect()->route('client-portal.login')
                ->with('error', 'Please login to access client portal');
        }

        return $next($request);
    }
}
