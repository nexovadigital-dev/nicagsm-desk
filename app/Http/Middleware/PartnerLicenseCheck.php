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
 * Verification is by domain — no token required on the partner side.
 */
class PartnerLicenseCheck
{
    public function handle(Request $request, Closure $next)
    {
        // Skip for API and widget routes
        if ($request->is('api/*') || $request->is('widget*')) {
            return $next($request);
        }

        $cacheKey = 'partner_license_valid';
        $isValid  = Cache::get($cacheKey);

        if ($isValid === null) {
            $isValid = $this->checkLicense();
            Cache::put($cacheKey, $isValid, $isValid ? now()->addHours(24) : now()->addHour());
        }

        if (! $isValid) {
            return response()->view('partner.license-invalid', [], 503);
        }

        return $next($request);
    }

    private function checkLicense(): bool
    {
        $domain  = parse_url(config('app.url'), PHP_URL_HOST);
        $baseUrl = config('partner.license_url', 'https://nexovadesk.com');

        if (! $domain) {
            Log::error('[Partner] APP_URL not set — cannot determine domain for license check');
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$baseUrl}/api/partner/verify", ['domain' => $domain]);

            if ($response->successful() && $response->json('valid') === true) {
                return true;
            }

            Log::warning('[Partner] License check failed', [
                'domain'   => $domain,
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);
            return false;

        } catch (\Throwable $e) {
            // Network error — grant grace (don't block if nexovadesk.com is temporarily down)
            Log::warning('[Partner] License check network error — granting grace period', [
                'domain' => $domain,
                'error'  => $e->getMessage(),
            ]);
            return true;
        }
    }
}
