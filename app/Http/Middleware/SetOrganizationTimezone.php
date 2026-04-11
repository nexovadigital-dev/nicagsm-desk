<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonImmutable;

class SetOrganizationTimezone
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = Auth::user();

        if ($user && $user->organization_id) {
            $tz = $user->organization?->timezone;

            if ($tz && in_array($tz, \DateTimeZone::listIdentifiers(), true)) {
                // Apply to PHP runtime and Carbon for this request
                date_default_timezone_set($tz);
                config(['app.timezone' => $tz]);
                CarbonImmutable::setTestNow(); // clear any stale test now
            }
        }

        return $next($request);
    }
}
