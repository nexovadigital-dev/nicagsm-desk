<?php

namespace App\Http\Middleware;

use App\Filament\Pages\TwoFactorChallenge;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si ya está autenticado con Filament, comprobar si necesita 2FA
        $guard = filament()->auth();

        if ($guard->check()) {
            /** @var User $user */
            $user = $guard->user();

            if (
                $user->hasTwoFactorEnabled()
                && ! session('2fa.verified')
                && ! $request->routeIs('filament.admin.pages.two-factor-challenge')
            ) {
                // Guardar el user en sesión, desloguear temporalmente
                session(['2fa.user_id' => $user->id]);
                $guard->logout();

                return redirect()->to(TwoFactorChallenge::getUrl());
            }
        }

        // Si hay sesión 2fa.user_id pero no está autenticado, dirigir al challenge
        if (
            session()->has('2fa.user_id')
            && ! $guard->check()
            && ! $request->routeIs('filament.admin.pages.two-factor-challenge')
        ) {
            return redirect()->to(TwoFactorChallenge::getUrl());
        }

        return $next($request);
    }
}
