<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $sessionId = $request->session()->getId();
        $loginKey = 'login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d';
        $hasUser = $request->session()->has($loginKey);
        $authCheck = auth()->guard('web')->check();
        $filamentCheck = filament()->auth()->check();

        Log::channel('single')->info('[DebugAuth] ' . $request->path(), [
            'session_id'      => substr($sessionId, 0, 10),
            'has_login_key'   => $hasUser,
            'web_guard_check' => $authCheck,
            'filament_check'  => $filamentCheck,
            'user_id'         => $request->session()->get($loginKey),
            'pw_hash_in_sess' => $request->session()->has('password_hash_web'),
        ]);

        return $next($request);
    }
}
