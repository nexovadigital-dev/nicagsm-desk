<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\SmtpSetting;
use App\Services\OrgMailer;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;

class SmtpSettings extends Page
{
    use ScopedToOrganization;

    protected string $view = 'filament.pages.smtp-settings';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Correo electrónico';
    protected static string|\UnitEnum|null $navigationGroup = 'Correo y Notificaciones';
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-envelope';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // ── SMTP (envío) ─────────────────────────────────────────────────────────
    public bool   $notificationsEnabled = false;
    public bool   $enabled              = false;

    public string $host        = '';
    public int    $port        = 587;
    public string $encryption  = 'tls';
    public string $username    = '';
    public string $password    = '';
    public string $fromAddress = '';
    public string $fromName    = '';

    public string $testEmail    = '';
    public string $genericEmail = '';

    // ── IMAP (recepción) ─────────────────────────────────────────────────────
    public bool   $imapEnabled    = false;
    public string $imapHost       = '';
    public int    $imapPort       = 993;
    public string $imapEncryption = 'ssl';
    public string $imapUsername   = '';
    public string $imapPassword   = '';
    public string $imapFolder     = 'INBOX';

    // ─────────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        $s   = SmtpSetting::forOrg($orgId);
        $org = auth()->user()->organization;

        // SMTP
        $this->notificationsEnabled = (bool) $s->notifications_enabled;
        $this->enabled              = (bool) $s->enabled;
        $this->host                 = $s->host         ?? '';
        $this->port                 = (int) ($s->port  ?? 587);
        $this->encryption           = $s->encryption   ?? 'tls';
        $this->username             = $s->username      ?? '';
        $this->fromAddress          = $s->from_address  ?? '';
        $this->fromName             = $s->from_name     ?? ($org?->support_name ?: $org?->name ?? '');
        $this->testEmail            = auth()->user()?->email ?? '';
        $this->genericEmail         = $org ? OrgMailer::genericEmail($org) : '';

        // IMAP
        $this->imapEnabled    = (bool) ($s->imap_enabled  ?? false);
        $this->imapHost       = $s->imap_host       ?? '';
        $this->imapPort       = (int) ($s->imap_port ?? 993);
        $this->imapEncryption = $s->imap_encryption  ?? 'ssl';
        $this->imapUsername   = $s->imap_username    ?? '';
        $this->imapFolder     = $s->imap_folder      ?? 'INBOX';
    }

    // ── Guardar SMTP ─────────────────────────────────────────────────────────
    public function save(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) return;

        $s = SmtpSetting::forOrg($orgId);

        $s->update([
            'notifications_enabled' => $this->notificationsEnabled,
            'enabled'               => $this->enabled,
            'host'                  => trim($this->host),
            'port'                  => (int) $this->port ?: 587,
            'encryption'            => $this->encryption,
            'username'              => trim($this->username),
            'from_address'          => trim($this->fromAddress),
            'from_name'             => trim($this->fromName) ?: (auth()->user()->organization?->name ?? ''),
        ]);

        if (! empty($this->password)) {
            $s->update(['password' => trim($this->password)]);
            $this->password = '';
        }

        $this->dispatch('nexova-toast', type: 'success', message: 'Configuración SMTP guardada');
    }

    // ── Guardar IMAP ─────────────────────────────────────────────────────────
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

    // ── Probar conexión IMAP ─────────────────────────────────────────────────
    public function testImap(): void
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

        $enc = match ($s->imap_encryption) {
            'ssl'  => '/ssl',
            'tls'  => '/tls',
            'none' => '/novalidate-cert',
            default => '/ssl',
        };
        $port   = $s->imap_port ?: 993;
        $folder = $s->imap_folder ?: 'INBOX';
        $dsn    = "{{$s->imap_host}:{$port}/imap{$enc}}{$folder}";

        $conn = @imap_open($dsn, $s->imap_username, $s->imap_password, 0, 1);

        if ($conn) {
            $count = imap_num_msg($conn);
            imap_close($conn);
            $this->dispatch('nexova-toast', type: 'success', message: "Conexión IMAP exitosa. {$count} mensaje(s) en {$folder}.");
        } else {
            $err = imap_last_error() ?: 'Error desconocido';
            $this->dispatch('nexova-toast', type: 'error', message: "Error IMAP: {$err}");
        }
    }

    // ── Probar SMTP ──────────────────────────────────────────────────────────
    public function sendTest(): void
    {
        $to = trim($this->testEmail);
        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Introduce un email válido para la prueba.');
            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) return;

        $org  = auth()->user()->organization;
        $smtp = SmtpSetting::forOrg($orgId);

        if ($smtp->enabled) {
            if (empty($smtp->host) || empty($smtp->username) || empty($smtp->from_address)) {
                $this->dispatch('nexova-toast', type: 'error', message: 'Completa y guarda la configuración SMTP antes de hacer una prueba.');
                return;
            }
        }

        try {
            $mailerName = OrgMailer::mailerNameFor($org);
            [$fromAddr, $fromName] = OrgMailer::fromFor($org);

            $send = function ($m) use ($to, $fromAddr, $fromName) {
                $m->to($to)
                  ->from($fromAddr, $fromName)
                  ->subject('Prueba SMTP — ' . $fromName);
            };

            if ($mailerName) {
                Mail::mailer($mailerName)->raw("Prueba de envío SMTP desde Nexova Desk.\n\nSMTP configurado correctamente.", $send);
            } else {
                Mail::raw("Prueba de envío SMTP desde Nexova Desk.\n\nUsando servidor de la plataforma (FROM genérico).", $send);
            }

            $this->dispatch('nexova-toast', type: 'success', message: "Correo enviado a {$to}");
        } catch (\Throwable $e) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Error SMTP: ' . $e->getMessage());
        }
    }

    /** Alias for blade compatibility */
    public function testConnection(): void
    {
        $this->sendTest();
    }
}
