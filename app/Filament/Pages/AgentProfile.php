<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\Organization;
use App\Models\SmtpSetting;
use App\Services\OrgMailer;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use PragmaRX\Google2FALaravel\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class AgentProfile extends Page
{
    use WithFileUploads, ScopedToOrganization;

    protected string $view = 'filament.pages.agent-profile';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Configuración Avanzada';
    protected static string|\UnitEnum|null $navigationGroup = 'Cuenta';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    // ── Perfil básico ─────────────────────────────────────────────────────────
    public string $profileName      = '';
    public string $profileEmail     = '';
    public string $availability     = 'online';
    public ?string $currentAvatarUrl = null;
    public $avatarFile = null;

    // ── Organización ──────────────────────────────────────────────────────────
    public string $orgName         = '';
    public string $orgWebsite      = '';
    public string $orgSupportEmail = '';
    public string $orgSupportName  = '';
    public string $orgTimezone     = 'America/Managua';

    // ── Redes sociales ────────────────────────────────────────────────────────
    public string $socialFacebook  = '';
    public string $socialInstagram = '';
    public string $socialX         = '';
    public string $socialWhatsapp  = '';
    public string $socialTelegram  = '';
    public string $socialYoutube   = '';

    // ── Seguridad (contraseña + 2FA) ──────────────────────────────────────────
    public string $currentPassword = '';
    public string $newPassword     = '';
    public string $passwordConfirm = '';
    public bool   $showQr          = false;
    public string $qrSvg           = '';
    public string $tfaSecret       = '';
    public string $tfaCode         = '';
    public string $tfaDisableCode  = '';

    // ── SMTP (envío) ──────────────────────────────────────────────────────────
    public bool   $smtpNotificationsEnabled = false;
    public bool   $smtpEnabled              = false;
    public string $smtpHost        = '';
    public int    $smtpPort        = 587;
    public string $smtpEncryption  = 'tls';
    public string $smtpUsername    = '';
    public string $smtpPassword    = '';
    public string $smtpFromAddress = '';
    public string $smtpFromName    = '';
    public string $smtpTestEmail   = '';
    public string $smtpGenericEmail = '';

    // ── IMAP (recepción) ──────────────────────────────────────────────────────
    public bool   $imapEnabled    = false;
    public string $imapHost       = '';
    public int    $imapPort       = 993;
    public string $imapEncryption = 'ssl';
    public string $imapUsername   = '';
    public string $imapPassword   = '';
    public string $imapFolder     = 'INBOX';

    // ── Inteligencia Artificial ───────────────────────────────────────────────
    public string $orgGroqKey    = '';
    public string $orgGroqKey2   = '';
    public string $orgGroqKey3   = '';
    public string $orgGeminiKey  = '';
    public bool   $groqKey1Set   = false;
    public bool   $groqKey2Set   = false;
    public bool   $groqKey3Set   = false;
    public bool   $geminiKeySet  = false;
    public int    $maxMsgPerSession  = 30;
    public int    $maxSessionsPerDay = 100;

    // ── Licencia ──────────────────────────────────────────────────────────────
    public ?string $licenseStatus    = null;
    public ?string $licenseCheckedAt = null;
    public string  $installedDomain  = '';
    public string  $platformUrl      = 'nexovadesk.com';
    public bool    $licenseValid     = false;

    // ─────────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $user = Filament::auth()->user();

        // Perfil
        $this->profileName      = $user->name;
        $this->profileEmail     = $user->email;
        $this->availability     = $user->availability ?? 'online';
        $this->currentAvatarUrl = $user->avatar_path
            ? Storage::url($user->avatar_path)
            : null;

        // Organización (solo owner/admin)
        if ($user->organization_id && in_array($user->role, ['owner', 'admin'])) {
            $org = $user->organization;
            $this->orgName         = $org->name          ?? '';
            $this->orgWebsite      = $org->website        ?? '';
            $this->orgSupportEmail = $org->support_email  ?? '';
            $this->orgSupportName  = $org->support_name   ?? '';
            $this->orgTimezone     = $org->timezone        ?? 'America/Managua';

            // Redes sociales
            $social = $org->social_links ?? [];
            $this->socialFacebook  = $social['facebook']  ?? '';
            $this->socialInstagram = $social['instagram'] ?? '';
            $this->socialX         = $social['x']         ?? '';
            $this->socialWhatsapp  = $social['whatsapp']  ?? '';
            $this->socialTelegram  = $social['telegram']  ?? '';
            $this->socialYoutube   = $social['youtube']   ?? '';

            // SMTP / IMAP
            if ($orgId = $this->orgId()) {
                $s = SmtpSetting::forOrg($orgId);
                $this->smtpNotificationsEnabled = (bool) $s->notifications_enabled;
                $this->smtpEnabled              = (bool) $s->enabled;
                $this->smtpHost                 = $s->host         ?? '';
                $this->smtpPort                 = (int) ($s->port  ?? 587);
                $this->smtpEncryption           = $s->encryption   ?? 'tls';
                $this->smtpUsername             = $s->username      ?? '';
                $this->smtpFromAddress          = $s->from_address  ?? '';
                $this->smtpFromName             = $s->from_name     ?? ($org?->support_name ?: $org?->name ?? '');
                $this->smtpTestEmail            = $user->email ?? '';
                $this->smtpGenericEmail         = OrgMailer::genericEmail($org);

                $this->imapEnabled    = (bool) ($s->imap_enabled  ?? false);
                $this->imapHost       = $s->imap_host       ?? '';
                $this->imapPort       = (int) ($s->imap_port ?? 993);
                $this->imapEncryption = $s->imap_encryption  ?? 'ssl';
                $this->imapUsername   = $s->imap_username    ?? '';
                $this->imapFolder     = $s->imap_folder      ?? 'INBOX';
            }

            // IA
            if ($this->isOrgAdmin() && $orgId = $this->orgId()) {
                $orgModel = Organization::find($orgId);
                if ($orgModel) {
                    $this->maxMsgPerSession  = $orgModel->max_messages_per_session ?: 30;
                    $this->maxSessionsPerDay = $orgModel->max_bot_sessions_per_day ?: 100;
                    $this->groqKey1Set  = ! empty($orgModel->getRawOriginal('ai_groq_key'));
                    $this->groqKey2Set  = ! empty($orgModel->getRawOriginal('ai_groq_key_2'));
                    $this->groqKey3Set  = ! empty($orgModel->getRawOriginal('ai_groq_key_3'));
                    $this->geminiKeySet = ! empty($orgModel->getRawOriginal('ai_gemini_key'));
                }
            }
        }

        // Licencia
        $this->installedDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? config('app.url');
        $this->checkLicense();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Avatar
    // ─────────────────────────────────────────────────────────────────────────

    public function updatedAvatarFile(): void
    {
        if (! $this->avatarFile) return;

        $user    = Filament::auth()->user();
        $oldPath = $user->avatar_path;
        $path    = $this->avatarFile->store('avatars', 'public');

        if (! $path) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Error al subir la imagen');
            return;
        }

        $user->update(['avatar_path' => $path]);
        if ($oldPath) Storage::disk('public')->delete($oldPath);

        $this->currentAvatarUrl = Storage::url($path);
        $this->avatarFile = null;
        $this->dispatch('nexova-toast', type: 'success', message: 'Foto de perfil actualizada');
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

    // ─────────────────────────────────────────────────────────────────────────
    // Perfil
    // ─────────────────────────────────────────────────────────────────────────

    public function saveProfile(): void
    {
        $user  = Filament::auth()->user();
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
        if (\App\Models\User::where('email', $email)->where('id', '!=', $user->id)->exists()) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Ese email ya está en uso');
            return;
        }

        $user->update(['name' => $name, 'email' => $email, 'availability' => $this->availability]);
        $this->dispatch('nexova-toast', type: 'success', message: 'Perfil actualizado');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Organización
    // ─────────────────────────────────────────────────────────────────────────

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

    public function saveSocialLinks(): void
    {
        $user = Filament::auth()->user();
        if (! $user->organization_id || ! in_array($user->role, ['owner', 'admin'])) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Sin permisos');
            return;
        }

        Organization::where('id', $user->organization_id)->update([
            'social_links' => [
                'facebook'  => trim($this->socialFacebook)  ?: null,
                'instagram' => trim($this->socialInstagram) ?: null,
                'x'         => trim($this->socialX)         ?: null,
                'whatsapp'  => trim($this->socialWhatsapp)  ?: null,
                'telegram'  => trim($this->socialTelegram)  ?: null,
                'youtube'   => trim($this->socialYoutube)   ?: null,
            ],
        ]);
        $this->dispatch('nexova-toast', type: 'success', message: 'Redes sociales guardadas');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Seguridad — Contraseña
    // ─────────────────────────────────────────────────────────────────────────

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
        $this->currentPassword = $this->newPassword = $this->passwordConfirm = '';
        $this->dispatch('nexova-toast', type: 'success', message: 'Contraseña cambiada correctamente');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Seguridad — 2FA
    // ─────────────────────────────────────────────────────────────────────────

    public function initTwoFactor(): void
    {
        $google2fa = app(Google2FA::class);
        $user      = Filament::auth()->user();
        $secret    = $google2fa->generateSecretKey();

        $user->update(['two_factor_secret' => $secret, 'two_factor_confirmed_at' => null]);

        $otpauthUrl = $google2fa->getQRCodeUrl(config('app.name', 'Nexova Desk'), $user->email, $secret);

        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $writer   = new Writer($renderer);

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
        $valid = app(Google2FA::class)->verifyKey($secret, trim($this->tfaCode));
        if (! $valid) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Código incorrecto. Inténtalo de nuevo');
            return;
        }
        $user->update(['two_factor_confirmed_at' => now()]);
        $this->showQr = false;
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
        $valid = app(Google2FA::class)->verifyKey($secret, trim($this->tfaDisableCode));
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

    public function getTfaEnabledProperty(): bool
    {
        return Filament::auth()->user()?->hasTwoFactorEnabled() ?? false;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SMTP
    // ─────────────────────────────────────────────────────────────────────────

    public function saveSmtp(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) return;

        $s = SmtpSetting::forOrg($orgId);
        $s->update([
            'notifications_enabled' => $this->smtpNotificationsEnabled,
            'enabled'               => $this->smtpEnabled,
            'host'                  => trim($this->smtpHost),
            'port'                  => (int) $this->smtpPort ?: 587,
            'encryption'            => $this->smtpEncryption,
            'username'              => trim($this->smtpUsername),
            'from_address'          => trim($this->smtpFromAddress),
            'from_name'             => trim($this->smtpFromName) ?: (auth()->user()->organization?->name ?? ''),
        ]);

        if (! empty($this->smtpPassword)) {
            $s->update(['password' => trim($this->smtpPassword)]);
            $this->smtpPassword = '';
        }

        $this->dispatch('nexova-toast', type: 'success', message: 'Configuración SMTP guardada');
    }

    public function sendSmtpTest(): void
    {
        $to = trim($this->smtpTestEmail);
        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Introduce un email válido para la prueba.');
            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) return;

        $org  = auth()->user()->organization;
        $smtp = SmtpSetting::forOrg($orgId);

        if ($smtp->enabled && (empty($smtp->host) || empty($smtp->username) || empty($smtp->from_address))) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Completa y guarda la configuración SMTP antes de hacer una prueba.');
            return;
        }

        try {
            $mailerName = OrgMailer::mailerNameFor($org);
            [$fromAddr, $fromName] = OrgMailer::fromFor($org);

            $send = fn($m) => $m->to($to)->from($fromAddr, $fromName)->subject("Prueba SMTP — {$fromName}");

            $mailerName
                ? Mail::mailer($mailerName)->raw("Prueba de envío SMTP desde Nexova Desk.\n\nSMTP configurado correctamente.", $send)
                : Mail::raw("Prueba de envío SMTP desde Nexova Desk.\n\nUsando servidor de la plataforma.", $send);

            $this->dispatch('nexova-toast', type: 'success', message: "Correo enviado a {$to}");
        } catch (\Throwable $e) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Error SMTP: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // IMAP
    // ─────────────────────────────────────────────────────────────────────────

    public function saveImap(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) return;

        $s = SmtpSetting::forOrg($orgId);
        $s->update([
            'imap_enabled'    => $this->imapEnabled,
            'imap_host'       => trim($this->imapHost),
            'imap_port'       => (int) $this->imapPort ?: 993,
            'imap_encryption' => $this->imapEncryption,
            'imap_username'   => trim($this->imapUsername),
            'imap_folder'     => trim($this->imapFolder) ?: 'INBOX',
        ]);

        if (! empty($this->imapPassword)) {
            $s->update(['imap_password' => trim($this->imapPassword)]);
            $this->imapPassword = '';
        }

        $this->dispatch('nexova-toast', type: 'success', message: 'Configuración IMAP guardada');
    }

    public function testImapConnection(): void
    {
        if (! function_exists('imap_open')) {
            $this->dispatch('nexova-toast', type: 'error', message: 'La extensión PHP IMAP no está instalada en el servidor.');
            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) return;

        $s = SmtpSetting::forOrg($orgId);

        if (empty($s->imap_host) || empty($s->imap_username) || empty($s->imap_password)) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Completa y guarda la configuración IMAP antes de probar.');
            return;
        }

        $enc  = match ($s->imap_encryption) { 'ssl' => '/ssl', 'tls' => '/tls', 'none' => '/novalidate-cert', default => '/ssl' };
        $dsn  = "{{$s->imap_host}:{$s->imap_port}/imap{$enc}}{$s->imap_folder}";
        $conn = @imap_open($dsn, $s->imap_username, $s->imap_password, 0, 1);

        if ($conn) {
            $count = imap_num_msg($conn);
            imap_close($conn);
            $this->dispatch('nexova-toast', type: 'success', message: "Conexión IMAP exitosa. {$count} mensaje(s) en {$s->imap_folder}.");
        } else {
            $err = imap_last_error() ?: 'Error desconocido';
            $this->dispatch('nexova-toast', type: 'error', message: "Error IMAP: {$err}");
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Inteligencia Artificial
    // ─────────────────────────────────────────────────────────────────────────

    public function saveOrgKeys(): void
    {
        if (! $this->isOrgAdmin() || ! $orgId = $this->orgId()) return;

        $data = ['ai_use_own_keys' => true];
        if (trim($this->orgGroqKey))   $data['ai_groq_key']   = encrypt(trim($this->orgGroqKey));
        if (trim($this->orgGroqKey2))  $data['ai_groq_key_2'] = encrypt(trim($this->orgGroqKey2));
        if (trim($this->orgGroqKey3))  $data['ai_groq_key_3'] = encrypt(trim($this->orgGroqKey3));
        if (trim($this->orgGeminiKey)) $data['ai_gemini_key'] = encrypt(trim($this->orgGeminiKey));

        Organization::where('id', $orgId)->update($data);

        $org = Organization::find($orgId);
        $this->groqKey1Set  = ! empty($org?->getRawOriginal('ai_groq_key'));
        $this->groqKey2Set  = ! empty($org?->getRawOriginal('ai_groq_key_2'));
        $this->groqKey3Set  = ! empty($org?->getRawOriginal('ai_groq_key_3'));
        $this->geminiKeySet = ! empty($org?->getRawOriginal('ai_gemini_key'));
        $this->orgGroqKey = $this->orgGroqKey2 = $this->orgGroqKey3 = $this->orgGeminiKey = '';

        $this->dispatch('nexova-toast', type: 'success', message: 'Claves API guardadas correctamente');
    }

    public function saveLimits(): void
    {
        if (! $this->isOrgAdmin() || ! $orgId = $this->orgId()) return;

        Organization::where('id', $orgId)->update([
            'max_messages_per_session' => max(5, min(200, $this->maxMsgPerSession)),
            'max_bot_sessions_per_day' => max(10, min(10000, $this->maxSessionsPerDay)),
        ]);
        $this->dispatch('nexova-toast', type: 'success', message: 'Límites actualizados');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Licencia
    // ─────────────────────────────────────────────────────────────────────────

    public function checkLicense(): void
    {
        // ── Edge Mode: licencia local, sin dependencia del servidor principal ──
        // Esta instancia opera de forma autónoma en el servidor de NicaGSM.
        // La verificación remota se conectará cuando se active el módulo de soporte.
        $this->licenseValid  = true;
        $this->licenseStatus = 'active';

        $tz = $this->orgTimezone ?: 'America/Managua';
        $this->licenseCheckedAt = now()->setTimezone($tz)->format('d/m/Y H:i T');
    }
}


