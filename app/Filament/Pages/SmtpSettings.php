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

    protected static ?string $navigationLabel = 'Email & SMTP';
    protected static string|\UnitEnum|null $navigationGroup = 'Integraciones';
    protected static ?int $navigationSort = 20;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-envelope';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // ── Fields ────────────────────────────────────────────────────────────
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

    // ──────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        $s = SmtpSetting::forOrg($orgId);
        $org = auth()->user()->organization;

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
    }

    public function save(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

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

    public function sendTest(): void
    {
        $to = trim($this->testEmail);
        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Introduce un email válido para la prueba.');
            return;
        }

        $orgId = $this->orgId();
        if (! $orgId) {
            return;
        }

        $org  = auth()->user()->organization;
        $smtp = SmtpSetting::forOrg($orgId);

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
