<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email    = '';
    public string $password = '';
    public bool   $remember = false;
    public string $error    = '';

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

        $redirect = request()->query('redirect');
        if ($redirect && str_starts_with($redirect, '/')) {
            $this->redirect($redirect);
            return;
        }

        $this->redirect('/app');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.auth');
    }
}
