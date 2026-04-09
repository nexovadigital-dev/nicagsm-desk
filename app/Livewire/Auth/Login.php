<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email       = '';
    public string $password   = '';
    public bool   $remember   = false;
    public string $error      = '';
    public bool   $showSuccess = false;

    public function submit(): void
    {
        $this->error = '';

        // Partner Edition — verificar que el usuario pertenece a esta instalación
        $user = User::where('email', trim($this->email))->first();
        if (! $user || ! $user->organization_id) {
            $this->error = 'Acceso denegado. Este panel es exclusivo para usuarios autorizados de esta instalación.';
            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->error = 'Email o contraseña incorrectos.';
            return;
        }

        $redirectUrl = '/app';
        $redirect    = request()->query('redirect');
        if ($redirect && str_starts_with($redirect, '/')) {
            $redirectUrl = $redirect;
        }

        session()->regenerate();
        $this->showSuccess = true;
        session(['login_redirect' => $redirectUrl]);
        $this->dispatch('loginSuccess');
    }

    public function performRedirect(): mixed
    {
        $url = session('login_redirect', '/app');
        session()->forget('login_redirect');
        return $this->redirect($url, navigate: false);
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.auth');
    }
}
