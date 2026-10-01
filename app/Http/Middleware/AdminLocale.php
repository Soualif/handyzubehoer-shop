<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * The back office is in French.
 */
class AdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale('fr');

        return $next($request);
    }
}
