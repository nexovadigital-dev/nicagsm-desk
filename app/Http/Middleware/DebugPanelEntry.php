<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs detailed auth/session/timing info for every panel request.
 * Enable in AdminPanelProvider->middleware([]) to diagnose entry hangs.
 */
class DebugPanelEntry
{
    public function handle(Request $request, Closure $next): Response
    {
        $t0 = microtime(true);

        $sessionId   = session()->getId();
        $sessionData = session()->all();

        // Auth state using standard guard (before Filament panel is fully booted)
        $webCheck     = auth('web')->check();
        $webUserId    = auth('web')->id();
        $sessionLogin = array_key_exists('login_web_' . sha1(\Illuminate\Auth\SessionGuard::class), $sessionData)
                        || isset($sessionData['login_session_key'])
                        || collect(array_keys($sessionData))->contains(fn ($k) => str_starts_with($k, 'login_'));

        Log::channel('stack')->info('[DebugPanelEntry] REQUEST START', [
            'url'          => $request->fullUrl(),
            'method'       => $request->method(),
            'session_id'   => substr($sessionId, 0, 8) . '...',
            'web_check'    => $webCheck,
            'web_user_id'  => $webUserId,
            'session_keys' => array_keys($sessionData),
            'is_livewire'  => $request->header('X-Livewire') ? true : false,
            'is_xhr'       => $request->ajax(),
        ]);

        $response = $next($request);

        $elapsed = round((microtime(true) - $t0) * 1000);

        Log::channel('stack')->info('[DebugPanelEntry] REQUEST END', [
            'url'         => $request->fullUrl(),
            'status'      => $response->getStatusCode(),
            'elapsed_ms'  => $elapsed,
            'slow'        => $elapsed > 3000,
        ]);

        return $response;
    }
}
