<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Panel;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Google2FA;

class TwoFactorChallenge extends Page
{
    protected string $view = 'filament.pages.two-factor-challenge';

    protected Width|string|null $maxContentWidth = 'sm';

    protected static bool $shouldRegisterNavigation = false;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'two-factor-challenge';
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public string $code = '';
    public ?string $errorMsg = null;

    public function mount(): void
    {
        // Si no hay usuario pendiente de 2FA, redirigir al dashboard
        if (! session()->has('2fa.user_id')) {
            $this->redirect(filament()->getUrl());
        }
    }

    public function verify(): void
    {
        $userId = session('2fa.user_id');
        $user   = \App\Models\User::find($userId);

        if (! $user) {
            $this->errorMsg = 'Sesión expirada. Inicia sesión de nuevo.';
            return;
        }

        $google2fa = app(Google2FA::class);
        $valid     = $google2fa->verifyKey($user->two_factor_secret, trim($this->code));

        if (! $valid) {
            $this->errorMsg = 'Código incorrecto. Inténtalo de nuevo.';
            $this->code     = '';
            return;
        }

        // Autenticar al usuario de verdad
        Auth::loginUsingId($userId);
        session()->forget('2fa.user_id');
        session(['2fa.verified' => true]);

        $this->redirect(filament()->getUrl());
    }
}
