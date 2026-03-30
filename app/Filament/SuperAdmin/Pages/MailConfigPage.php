<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\SystemSetting;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;

class MailConfigPage extends Page
{
    protected string $view = 'filament.superadmin.pages.mail-config';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Servidor Mail';
    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 60;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-envelope';
    }

    public function getTitle(): string|Htmlable { return ''; }

    public string $smtpHost        = '';
    public string $smtpPort        = '587';
    public string $smtpUsername    = '';
    public string $smtpPassword    = '';
    public string $smtpEncryption  = 'tls';
    public string $smtpFromAddress = '';
    public string $smtpFromName    = 'Nexova Desk';
    public string $testEmailTo     = '';

    public function mount(): void
    {
        $s = SystemSetting::instance();
        $this->smtpHost        = $s->smtp_host        ?? '';
        $this->smtpPort        = $s->smtp_port        ?? '587';
        $this->smtpUsername    = $s->smtp_username    ?? '';
        $this->smtpEncryption  = $s->smtp_encryption  ?? 'tls';
        $this->smtpFromAddress = $s->smtp_from_address ?? '';
        $this->smtpFromName    = $s->smtp_from_name   ?? 'Nexova Desk';
        // Pre-fill test email with the logged-in super-admin's email
        $this->testEmailTo     = auth()->user()?->email ?? '';
    }

    public function save(): void
    {
        $s = SystemSetting::instance();
        $s->update([
            'smtp_host'         => trim($this->smtpHost),
            'smtp_port'         => trim($this->smtpPort) ?: '587',
            'smtp_username'     => trim($this->smtpUsername),
            'smtp_password'     => $this->smtpPassword !== ''
                                    ? encrypt(trim($this->smtpPassword))
                                    : $s->smtp_password,
            'smtp_encryption'   => $this->smtpEncryption,
            'smtp_from_address' => trim($this->smtpFromAddress),
            'smtp_from_name'    => trim($this->smtpFromName) ?: 'Nexova Desk',
        ]);

        $this->smtpPassword = '';
        $this->dispatch('nexova-toast', type: 'success', message: 'Configuración SMTP guardada');
    }

    public function testEmail(): void
    {
        $to = trim($this->testEmailTo);
        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Introduce un email válido para la prueba.');
            return;
        }

        $s = SystemSetting::instance();
        if ($s->smtp_host) {
            config([
                'mail.default'                 => 'smtp',
                'mail.mailers.smtp.host'       => $s->smtp_host,
                'mail.mailers.smtp.port'       => (int) ($s->smtp_port ?: 587),
                'mail.mailers.smtp.encryption' => $s->smtp_encryption ?: 'tls',
                'mail.mailers.smtp.username'   => $s->smtp_username,
                'mail.mailers.smtp.password'   => $s->smtp_password ? decrypt($s->smtp_password) : null,
                'mail.from.address'            => $s->smtp_from_address ?: 'no-reply@nexovadesk.com',
                'mail.from.name'               => $s->smtp_from_name ?: 'Nexova Desk',
            ]);
        }

        try {
            Mail::raw(
                "Este es un correo de prueba desde Nexova Desk.\n\n¡Tu configuración SMTP funciona correctamente!",
                fn ($m) => $m->to($to)->subject('Prueba SMTP — Nexova Desk')
            );
            $this->dispatch('nexova-toast', type: 'success', message: "Correo enviado a {$to}");
        } catch (\Throwable $e) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Error SMTP: '.$e->getMessage());
        }
    }
}
