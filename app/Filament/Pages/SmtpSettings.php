<?php
declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\SmtpSetting;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Mail;

class SmtpSettings extends Page
{
    protected string $view = 'filament.pages.smtp-settings';

    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Email & SMTP';
    protected static string|\UnitEnum|null $navigationGroup = 'Integraciones';
    protected static ?int $navigationSort = 20;

    // ── Fields ────────────────────────────────────────────────────────────
    public bool   $notificationsEnabled = false;

    public string $host        = '';
    public int    $port        = 587;
    public string $encryption  = 'tls';
    public string $username    = '';
    public string $password    = '';
    public string $fromAddress = '';
    public string $fromName    = 'Nexova Chat';

    // ── Test send ─────────────────────────────────────────────────────────
    public string $testEmail = '';

    // ──────────────────────────────────────────────────────────────────────

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-envelope';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Email & SMTP';
    }

    public function mount(): void
    {
        $s = SmtpSetting::instance();

        $this->notificationsEnabled = (bool) $s->notifications_enabled;
        $this->host                 = $s->host         ?? '';
        $this->port                 = (int) ($s->port  ?? 587);
        $this->encryption           = $s->encryption   ?? 'tls';
        $this->username             = $s->username      ?? '';
        $this->password             = $s->password      ?? '';
        $this->fromAddress          = $s->from_address  ?? '';
        $this->fromName             = $s->from_name     ?? 'Nexova Chat';
    }

    public function save(): void
    {
        $s = SmtpSetting::instance();

        $s->update([
            'notifications_enabled' => $this->notificationsEnabled,
            'host'                  => $this->host,
            'port'                  => $this->port,
            'encryption'            => $this->encryption,
            'username'              => $this->username,
            'from_address'          => $this->fromAddress,
            'from_name'             => $this->fromName,
        ]);

        // Only update password when a new one was provided
        if (! empty($this->password)) {
            $s->update(['password' => $this->password]);
        }

        $this->dispatch('nexova-toast', type: 'success', message: 'Configuración SMTP guardada');
    }

    public function sendTest(): void
    {
        if (empty($this->testEmail)) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Ingresa un email de destino');
            return;
        }

        $s = SmtpSetting::instance();
        $s->applyToConfig();

        try {
            Mail::raw(
                'Este es un email de prueba enviado desde Nexova Chat para verificar la configuración SMTP.',
                function ($message) {
                    $message->to($this->testEmail)
                            ->subject('Prueba de configuración SMTP — Nexova Chat');
                }
            );

            $this->dispatch('nexova-toast', type: 'success', message: 'Email de prueba enviado a ' . $this->testEmail);
        } catch (\Throwable $e) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Error al enviar: ' . $e->getMessage());
        }
    }

    /**
     * Alias kept for blade compatibility (wire:click="testConnection")
     */
    public function testConnection(): void
    {
        $this->sendTest();
    }
}
