<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRockAdsConfig
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.rockads.api_key');
        $apiSecret = config('services.rockads.api_secret');

        if (!$apiKey || !$apiSecret) {
            return redirect()->back()
                ->with('error', 'RockAds API configuration is missing. Please check your environment variables.');
        }

        return $next($request);
    }
} 