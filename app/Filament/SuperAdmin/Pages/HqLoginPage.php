<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as FilamentLogin;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class HqLoginPage extends FilamentLogin
{
    public function authenticate(): ?LoginResponse
    {
        // Run Filament's full authentication (rate limiting, credentials check, etc.)
        $response = parent::authenticate();

        // After a successful login, ensure the user is a super-admin
        if (Auth::check() && ! Auth::user()->isSuperAdmin()) {
            Log::warning('Non-superadmin attempted HQ login', [
                'email' => Auth::user()->email,
                'ip'    => request()->ip(),
            ]);

            Auth::logout();
            request()->session()->invalidate();

            throw ValidationException::withMessages([
                'data.email' => 'Acceso restringido. Este panel es solo para administradores del sistema.',
            ]);
        }

        return $response;
    }

    public function render(): View
    {
        return view('filament.superadmin.pages.hq-login')
            ->layout('layouts.auth', ['title' => 'Panel HQ · Nexova Desk', 'hqMode' => true]);
    }
}
