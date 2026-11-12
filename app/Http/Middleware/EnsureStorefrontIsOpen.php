<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStorefrontIsOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! SiteSetting::storefrontIsOpen()) {
            return response()->view('welcome.closed', status: 503);
        }

        return $next($request);
    }
}
