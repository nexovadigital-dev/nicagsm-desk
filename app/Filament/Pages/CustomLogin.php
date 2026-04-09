<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
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

        // Partner Edition: solo usuarios con organización asignada
        $user = User::where('email', trim($this->email))->first();
        if (! $user || ! $user->organization_id) {
            $this->error = 'Acceso denegado. Este panel es exclusivo para usuarios autorizados.';
            return null;
        }

        // Autenticar usando el guard de Filament (web guard)
        if (! filament()->auth()->attempt(
            ['email' => $this->email, 'password' => $this->password],
            $this->remember,
        )) {
            $this->error = 'Email o contraseña incorrectos.';
            return null;
        }

        session()->regenerate();

        $this->showSuccess = true;
        $this->dispatch('loginSuccess');

        return null;
    }

    public function performRedirect(): LoginResponse
    {
        return app(LoginResponse::class);
    }

    public function render()
    {
        return view('filament.pages.custom-login')
            ->layout('layouts.auth');
    }
}
