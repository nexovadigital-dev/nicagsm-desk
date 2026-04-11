<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Organization;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use PragmaRX\Google2FALaravel\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class AgentProfile extends Page
{
    use WithFileUploads;
    protected string $view = 'filament.pages.agent-profile';

    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Mi Perfil';
    protected static string|\UnitEnum|null $navigationGroup = 'Cuenta';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-user-circle';
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    // ── Perfil básico ──
    public string $profileName  = '';
    public string $profileEmail = '';
    public string $availability   = 'online';
    public ?string $currentAvatarUrl = null;
    public $avatarFile = null; // Livewire temp upload

    // ── Organización ──
    public string  $orgName         = '';
    public string  $orgWebsite      = '';
    public string  $orgSupportEmail = '';
    public string  $orgSupportName  = '';
    public string  $orgTimezone     = 'America/Managua';

    // ── Cambio de contraseña ──
    public string $currentPassword    = '';
    public string $newPassword        = '';
    public string $passwordConfirm    = '';

    // ── 2FA ──
    public bool   $showQr             = false;
    public string $qrSvg              = '';
    public string $tfaSecret          = '';
    public string $tfaCode            = '';
    public string $tfaDisableCode     = '';

    public function mount(): void
    {
        $user = Filament::auth()->user();
        $this->profileName      = $user->name;
        $this->profileEmail     = $user->email;
        $this->availability     = $user->availability ?? 'online';
        $this->currentAvatarUrl = $user->avatar_path
            ? Storage::url($user->avatar_path)
            : null;

        // Org fields (only if user belongs to an org and is owner/admin)
        if ($user->organization_id && in_array($user->role, ['owner', 'admin'])) {
            $org = $user->organization;
            $this->orgName         = $org->name          ?? '';
            $this->orgWebsite      = $org->website        ?? '';
            $this->orgSupportEmail = $org->support_email  ?? '';
            $this->orgSupportName  = $org->support_name   ?? '';
            $this->orgTimezone     = $org->timezone        ?? 'America/Managua';
        }
    }

    // -------------------------------------------------------------------------
    // Avatar auto-save on upload
    // -------------------------------------------------------------------------

    public function updatedAvatarFile(): void
    {
        if (! $this->avatarFile) {
            return;
        }

        $user = Filament::auth()->user();

        $oldPath = $user->avatar_path;
        $path    = $this->avatarFile->store('avatars', 'public');

        if (! $path) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Error al subir la imagen');
            return;
        }

        $user->update(['avatar_path' => $path]);

        // Delete old avatar only after new one is confirmed saved
        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $this->currentAvatarUrl = Storage::url($path);
        $this->avatarFile = null;

        $this->dispatch('nexova-toast', type: 'success', message: 'Foto de perfil actualizada');
    }

    // -------------------------------------------------------------------------
    // Perfil básico
    // -------------------------------------------------------------------------

    public function saveProfile(): void
    {
        $user = Filament::auth()->user();

        $name  = trim($this->profileName);
        $email = trim($this->profileEmail);

        if (! $name || ! $email) {
            $this->dispatch('nexova-toast', type: 'error', message: 'El nombre y el email son obligatorios');
            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('nexova-toast', type: 'error', message: 'El email no tiene un formato válido');
            return;
        }

        // Verificar que el email no esté en uso por otro usuario
        $exists = \App\Models\User::where('email', $email)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($exists) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Ese email ya está en uso');
            return;
        }

        $user->update(['name' => $name, 'email' => $email, 'availability' => $this->availability]);

        $this->dispatch('nexova-toast', type: 'success', message: 'Perfil actualizado');
    }

    public function removeAvatar(): void
    {
        $user = Filament::auth()->user();
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
            $this->currentAvatarUrl = null;

            $this->dispatch('nexova-toast', type: 'success', message: 'Foto eliminada');
        }
    }

    // -------------------------------------------------------------------------
    // Organización
    // -------------------------------------------------------------------------

    public function saveOrg(): void
    {
        $user = Filament::auth()->user();

        if (! $user->organization_id || ! in_array($user->role, ['owner', 'admin'])) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Sin permisos para editar la organización');
            return;
        }

        $name = trim($this->orgName);
        if (! $name) {
            $this->dispatch('nexova-toast', type: 'error', message: 'El nombre de la organización es obligatorio');
            return;
        }

        $tz = trim($this->orgTimezone);
        if (! $tz || ! in_array($tz, \DateTimeZone::listIdentifiers(), true)) {
            $tz = 'America/Managua';
        }

        Organization::where('id', $user->organization_id)->update([
            'name'          => $name,
            'website'       => trim($this->orgWebsite)      ?: null,
            'support_email' => trim($this->orgSupportEmail) ?: null,
            'support_name'  => trim($this->orgSupportName)  ?: null,
            'timezone'      => $tz,
        ]);

        $this->dispatch('nexova-toast', type: 'success', message: 'Organización actualizada');
    }

    // -------------------------------------------------------------------------
    // Cambio de contraseña
    // -------------------------------------------------------------------------

    public function savePassword(): void
    {
        $user = Filament::auth()->user();

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->dispatch('nexova-toast', type: 'error', message: 'La contraseña actual no es correcta');
            return;
        }

        if (strlen($this->newPassword) < 8) {
            $this->dispatch('nexova-toast', type: 'error', message: 'La nueva contraseña debe tener al menos 8 caracteres');
            return;
        }

        if ($this->newPassword !== $this->passwordConfirm) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Las contraseñas no coinciden');
            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword = '';
        $this->newPassword     = '';
        $this->passwordConfirm = '';

        $this->dispatch('nexova-toast', type: 'success', message: 'Contraseña cambiada correctamente');
    }

    // -------------------------------------------------------------------------
    // 2FA — Setup
    // -------------------------------------------------------------------------

    public function initTwoFactor(): void
    {
        $google2fa = app(Google2FA::class);
        $user      = Filament::auth()->user();

        $secret = $google2fa->generateSecretKey();

        // Guardar provisional (sin confirmar aún)
        $user->update(['two_factor_secret' => $secret, 'two_factor_confirmed_at' => null]);

        $otpauthUrl = $google2fa->getQRCodeUrl(
            config('app.name', 'Nexova Chat'),
            $user->email,
            $secret,
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd(),
        );

        $writer = new Writer($renderer);

        $this->qrSvg    = $writer->writeString($otpauthUrl);
        $this->tfaSecret = $secret;
        $this->showQr   = true;
        $this->tfaCode  = '';
    }

    public function confirmTwoFactor(): void
    {
        $user   = Filament::auth()->user();
        $secret = $user->two_factor_secret;

        if (! $secret) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Primero genera un código QR');
            return;
        }

        $google2fa = app(Google2FA::class);
        $valid     = $google2fa->verifyKey($secret, trim($this->tfaCode));

        if (! $valid) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Código incorrecto. Inténtalo de nuevo');
            return;
        }

        $user->update(['two_factor_confirmed_at' => now()]);

        $this->showQr  = false;
        $this->tfaCode = '';

        $this->dispatch('nexova-toast', type: 'success', message: '2FA activado correctamente');
    }

    public function disableTwoFactor(): void
    {
        $user   = Filament::auth()->user();
        $secret = $user->two_factor_secret;

        if (! $secret) {
            $this->dispatch('nexova-toast', type: 'error', message: 'El 2FA no está activado');
            return;
        }

        $google2fa = app(Google2FA::class);
        $valid     = $google2fa->verifyKey($secret, trim($this->tfaDisableCode));

        if (! $valid) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Código incorrecto. No se desactivó el 2FA');
            return;
        }

        $user->update(['two_factor_secret' => null, 'two_factor_confirmed_at' => null]);

        $this->tfaDisableCode = '';
        $this->showQr         = false;
        $this->qrSvg          = '';
        $this->tfaSecret      = '';

        $this->dispatch('nexova-toast', type: 'success', message: '2FA desactivado');
    }

    // -------------------------------------------------------------------------
    // Estado actual del 2FA
    // -------------------------------------------------------------------------

    public function getTfaEnabledProperty(): bool
    {
        return Filament::auth()->user()?->hasTwoFactorEnabled() ?? false;
    }
}
