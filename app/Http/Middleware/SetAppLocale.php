<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAppLocale
{
    /**
     * Resolve and apply the active bilingual application locale ('id' default or 'en').
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['id', 'en'];
        $locale = $request->session()->get('locale');

        if (! in_array($locale, $supported, true)) {
            $userLocale = $request->user()?->preferred_locale;
            $locale = in_array($userLocale, $supported, true) ? $userLocale : config('app.locale', 'id');
        }

        if (! in_array($locale, $supported, true)) {
            $locale = 'id';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
