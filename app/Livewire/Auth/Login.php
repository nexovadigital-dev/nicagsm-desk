<?php

namespace App\Livewire\Auth;

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

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->error = 'Email o contraseña incorrectos.';
            return;
        }

        $redirect = request()->query('redirect');
        if ($redirect && str_starts_with($redirect, '/')) {
            $this->redirect($redirect);
            return;
        }

        $this->redirect(Auth::user()->isSuperAdmin() ? '/nx-hq' : '/app');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.auth');
    }
}
