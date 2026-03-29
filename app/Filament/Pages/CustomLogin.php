<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Auth\Pages\Login;

/**
 * Overrides Filament's built-in login page to redirect to
 * our custom Livewire login at /login.
 */
class CustomLogin extends Login
{
    public function mount(): void
    {
        // If already authenticated, let Filament handle redirect to /admin
        if (auth()->check()) {
            $this->redirect(filament()->getUrl());
            return;
        }

        // Redirect unauthenticated visitors to our custom login page
        $this->redirect(route('auth.login'));
    }
}
