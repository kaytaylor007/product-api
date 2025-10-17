<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequestHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('product-api.LogRequestHeaders.handle: Incoming Request Url =>', $request->fullUrl());

        // Log all incoming request headers
        Log::info('product-api.LogRequestHeaders.handle: Incoming Request Headers =>', $request->headers->all());

        // Check for Authorization header
        if ($request->hasHeader('Authorization')) {
            $authorizationHeader = $request->header('Authorization');
            Log::info('product-api.LogRequestHeaders.handle: Authorization Header Found =>', ['header' => $authorizationHeader]);

            // Check if it's a Bearer token
            if (str_starts_with($authorizationHeader, 'Bearer ')) {
                $token = substr($authorizationHeader, 7); // Extract the token
                Log::info('product-api.LogRequestHeaders.handle: Bearer Token Found =>', ['token' => $token]);
            } else {
                Log::warning('product-api.LogRequestHeaders.handle: Authorization header found but not in Bearer format.');
            }
        } else {
            Log::warning('product-api.LogRequestHeaders.handle: Authorization Header Not Found in Request.');
        }

        return $next($request);
    }
}