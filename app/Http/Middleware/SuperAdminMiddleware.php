<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth()->user();

        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Acceso denegado.');
        }

        return $next($request);
    }
}
