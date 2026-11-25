<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoModeEnabled
{
    /**
     * Abort with 404 when interactive demo mode is disabled.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled')) {
            abort(404);
        }

        return $next($request);
    }
}
