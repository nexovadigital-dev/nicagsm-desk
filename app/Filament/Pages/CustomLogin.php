<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * Partner Edition login — opera dentro del contexto de Filament
 * usando filament()->auth()->attempt() para evitar conflictos de sesión.
 * Renderiza con nuestro layout personalizado (no el layout de Filament).
 */
class CustomLogin extends Component
{
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;
    public string $error    = '';
    public bool   $showSuccess = false;

    // URL de la ruta para que Filament sepa dónde está la página de login
    public static function getSlug(): string
    {
        return 'login';
    }

    public static function getRouteName(?string $panel = null): string
    {
        return 'filament.admin.auth.login';
    }

    public function mount(): void
    {
        // Si ya está autenticado, redirigir al panel
        if (filament()->auth()->check()) {
            redirect()->intended(filament()->getUrl());
        }
    }

    public function doLogin(): mixed
    {
        $this->error = '';
        $t0 = microtime(true);

        Log::info('[CustomLogin] doLogin START', ['email' => $this->email]);

        // Partner Edition: solo usuarios con organización asignada
        $user = User::where('email', trim($this->email))->first();
        if (! $user || ! $user->organization_id) {
            $this->error = 'Acceso denegado. Este panel es exclusivo para usuarios autorizados.';
            Log::warning('[CustomLogin] doLogin BLOCKED — no org', ['email' => $this->email]);
            return null;
        }

        // Autenticar usando el guard de Filament (web guard)
        $t1 = microtime(true);
        $attempted = filament()->auth()->attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember,
        );
        Log::info('[CustomLogin] attempt done', ['ok' => $attempted, 'ms' => round((microtime(true)-$t1)*1000)]);

        if (! $attempted) {
            $this->error = 'Email o contraseña incorrectos.';
            return null;
        }

        session()->regenerate();

        Log::info('[CustomLogin] session regenerated, dispatching loginSuccess', [
            'session_id'  => substr(session()->getId(), 0, 8) . '...',
            'filament_ok' => filament()->auth()->check(),
            'total_ms'    => round((microtime(true)-$t0)*1000),
        ]);

        $this->showSuccess = true;
        $this->dispatch('loginSuccess');

        return null;
    }

    public function performRedirect(): LoginResponse
    {
        Log::info('[CustomLogin] performRedirect called', [
            'filament_ok' => filament()->auth()->check(),
            'user_id'     => filament()->auth()->id(),
        ]);
        return app(LoginResponse::class);
    }

    public function render()
    {
        return view('filament.pages.custom-login')
            ->layout('layouts.auth');
    }
}
