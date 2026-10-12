<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UseOptionalSanctum
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->bearerToken()) {
            return $next($request);
        }

        $previousGuard = Auth::getDefaultDriver();
        Auth::guard('sanctum')->forgetUser();
        Auth::shouldUse('sanctum');

        try {
            return $next($request);
        } finally {
            Auth::guard('sanctum')->forgetUser();
            Auth::shouldUse($previousGuard);
        }
    }
}
