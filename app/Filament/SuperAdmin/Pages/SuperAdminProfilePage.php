<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class SuperAdminProfilePage extends Page
{
    protected string $view = 'filament.superadmin.pages.superadmin-profile';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Mi Perfil';
    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 98;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-user-circle';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // Profile
    public string $name            = '';
    public string $email           = '';
    public string $currentPassword = '';
    public string $newPassword     = '';
    public string $confirmPassword = '';

    // 2FA
    public string $twoFactorCode   = '';
    public string $pendingSecret   = '';
    public string $qrSvg           = '';
    public bool   $showSetup2fa    = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $this->name  = $user->name;
        $this->email = $user->email;
    }

    // ── Profile ──────────────────────────────────────────────────────────

    public function saveProfile(): void
    {
        /** @var User $user */
        $user = auth()->user();

        if (strlen(trim($this->name)) < 2) {
            $this->dispatch('nexova-toast', type: 'error', message: 'El nombre debe tener al menos 2 caracteres.');
            return;
        }

        $user->name = trim($this->name);

        if ($this->newPassword !== '') {
            if (! Hash::check($this->currentPassword, $user->password)) {
                $this->dispatch('nexova-toast', type: 'error', message: 'Contraseña actual incorrecta.');
                return;
            }
            if (strlen($this->newPassword) < 8) {
                $this->dispatch('nexova-toast', type: 'error', message: 'La nueva contraseña debe tener mínimo 8 caracteres.');
                return;
            }
            if ($this->newPassword !== $this->confirmPassword) {
                $this->dispatch('nexova-toast', type: 'error', message: 'Las contraseñas no coinciden.');
                return;
            }
            $user->password = Hash::make($this->newPassword);
            $this->currentPassword = $this->newPassword = $this->confirmPassword = '';
        }

        $user->save();
        $this->dispatch('nexova-toast', type: 'success', message: 'Perfil actualizado');
    }

    // ── 2FA ──────────────────────────────────────────────────────────────

    public function begin2faSetup(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $g2fa = new Google2FA();
        $secret = $g2fa->generateSecretKey();

        $qrUrl = $g2fa->getQRCodeUrl('Nexova Desk', $user->email, $secret);

        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $writer   = new Writer($renderer);
        $svg      = $writer->writeString($qrUrl);

        $this->pendingSecret = $secret;
        $this->qrSvg         = $svg;
        $this->showSetup2fa  = true;
        $this->twoFactorCode = '';
    }

    public function confirm2fa(): void
    {
        if (! $this->pendingSecret) return;

        $g2fa = new Google2FA();
        if (! $g2fa->verifyKey($this->pendingSecret, $this->twoFactorCode)) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Código incorrecto. Intenta de nuevo.');
            return;
        }

        /** @var User $user */
        $user = auth()->user();
        $user->two_factor_secret       = encrypt($this->pendingSecret);
        $user->two_factor_confirmed_at = now();
        $user->save();

        $this->showSetup2fa  = false;
        $this->pendingSecret = '';
        $this->twoFactorCode = '';
        $this->qrSvg         = '';
        $this->dispatch('nexova-toast', type: 'success', message: '2FA activado correctamente');
    }

    public function disable2fa(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user->two_factor_secret       = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        $this->showSetup2fa = false;
        $this->dispatch('nexova-toast', type: 'success', message: '2FA desactivado');
    }

    public function get2faActiveProperty(): bool
    {
        return auth()->user()->hasTwoFactorEnabled();
    }
}
