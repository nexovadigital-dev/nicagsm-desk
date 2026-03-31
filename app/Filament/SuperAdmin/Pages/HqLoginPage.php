<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use Filament\Pages\Auth\Login as FilamentLogin;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HqLoginPage extends FilamentLogin
{
    // Override with simple properties instead of Filament form
    public string $email    = '';
    public string $password = '';
    public string $error    = '';

    public function authenticate(): void
    {
        $this->error = '';

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->error = 'Credenciales incorrectas.';
            return;
        }

        if (! Auth::user()->isSuperAdmin()) {
            Log::warning('Non-superadmin attempted HQ login', [
                'email' => $this->email,
                'ip'    => request()->ip(),
            ]);
            Auth::logout();
            $this->error = 'Acceso restringido. Este panel es solo para administradores del sistema.';
            return;
        }

        $this->redirect('/nx-hq', navigate: true);
    }

    public function render(): View
    {
        return view('filament.superadmin.pages.hq-login')
            ->layout('layouts.auth', ['title' => 'Panel HQ · Nexova Desk', 'hqMode' => true]);
    }
}
