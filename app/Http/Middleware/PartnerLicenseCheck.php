<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verifies the partner license against nexovadesk.com daily.
 * Caches the result for 24 hours so it doesn't call on every request.
 */
class PartnerLicenseCheck
{
    public function handle(Request $request, Closure $next)
    {
        // Skip check for non-web routes (API, widget, etc.)
        if ($request->is('api/*') || $request->is('widget*')) {
            return $next($request);
        }

        $cacheKey = 'partner_license_valid';
        $isValid  = Cache::get($cacheKey);

        if ($isValid === null) {
            $isValid = $this->checkLicense();
            // Cache 24h if valid, 1h if invalid (retry sooner on failure)
            Cache::put($cacheKey, $isValid, $isValid ? now()->addHours(24) : now()->addHour());
        }

        if (! $isValid) {
            return response()->view('partner.license-invalid', [], 503);
        }

        return $next($request);
    }

    private function checkLicense(): bool
    {
        $token   = config('partner.token');
        $baseUrl = config('partner.license_url', 'https://nexovadesk.com');

        if (! $token) {
            Log::error('[Partner] PARTNER_TOKEN not set in .env');
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$baseUrl}/api/partner/verify/{$token}");

            if ($response->successful() && $response->json('valid') === true) {
                return true;
            }

            Log::warning('[Partner] License check failed', [
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);
            return false;
        } catch (\Throwable $e) {
            // Network error — grant grace period (don't block if nexovadesk.com is down)
            Log::warning('[Partner] License check network error — granting grace', [
                'error' => $e->getMessage(),
            ]);
            return true;
        }
    }
}
