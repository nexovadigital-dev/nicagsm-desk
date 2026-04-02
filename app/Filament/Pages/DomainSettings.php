<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class DomainSettings extends Page
{
    use ScopedToOrganization;

    protected string $view = 'filament.pages.domain-settings';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Dominio Propio';
    protected static string|\UnitEnum|null $navigationGroup = 'Integraciones';
    protected static ?int $navigationSort = 21;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-globe-alt';
    }

    public function getTitle(): string|Htmlable { return ''; }

    public string $domain          = '';
    public bool   $domainVerified  = false;
    public string $verifyToken     = '';
    public string $verifyError     = '';
    public bool   $verifySuccess   = false;

    public function mount(): void
    {
        $org = auth()->user()?->organization;
        if (! $org) return;

        $this->domain         = $org->domain         ?? '';
        $this->domainVerified = (bool) $org->domain_verified;
        $this->verifyToken    = $org->domain_verify_token ?? '';
    }

    public function saveDomain(): void
    {
        $this->verifyError   = '';
        $this->verifySuccess = false;

        $domain = strtolower(trim($this->domain));

        // Strip protocol/www
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        $domain = rtrim($domain, '/');

        if (empty($domain)) {
            $this->verifyError = 'Introduce un dominio válido (ej: tuempresa.com).';
            return;
        }

        if (! preg_match('/^[a-z0-9]([a-z0-9\-]{0,61}[a-z0-9])?(\.[a-z]{2,})+$/', $domain)) {
            $this->verifyError = 'Dominio inválido. Ejemplo correcto: tuempresa.com';
            return;
        }

        $org   = auth()->user()->organization;
        $token = $org->domain_verify_token;

        // Generate token if domain changed or no token exists
        if ($domain !== $org->domain || empty($token)) {
            $token = 'nexovadesk-verify=' . Str::random(32);
        }

        $org->update([
            'domain'               => $domain,
            'domain_verified'      => false,
            'domain_verify_token'  => $token,
        ]);

        $this->domain         = $domain;
        $this->domainVerified = false;
        $this->verifyToken    = $token;

        $this->dispatch('nexova-toast', type: 'success', message: 'Dominio guardado. Agrega el registro TXT y luego verifica.');
    }

    public function verifyDomain(): void
    {
        $this->verifyError   = '';
        $this->verifySuccess = false;

        $org = auth()->user()->organization;

        if (empty($org->domain) || empty($org->domain_verify_token)) {
            $this->verifyError = 'Primero guarda el dominio para obtener el registro TXT.';
            return;
        }

        $domain = $org->domain;
        $token  = $org->domain_verify_token;

        try {
            $records = @dns_get_record($domain, DNS_TXT);
        } catch (\Throwable) {
            $records = [];
        }

        $found = false;
        if (is_array($records)) {
            foreach ($records as $record) {
                $txt = $record['txt'] ?? $record['entries'][0] ?? '';
                if (str_contains((string) $txt, $token)) {
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            $org->update(['domain_verified' => true]);
            $this->domainVerified = true;
            $this->verifySuccess  = true;
            $this->dispatch('nexova-toast', type: 'success', message: '¡Dominio verificado correctamente!');
        } else {
            $this->verifyError = "No se encontró el registro TXT en {$domain}. Los cambios DNS pueden tardar hasta 48 horas en propagarse.";
        }
    }

    public function removeDomain(): void
    {
        $org = auth()->user()->organization;
        $org->update([
            'domain'              => null,
            'domain_verified'     => false,
            'domain_verify_token' => null,
        ]);

        $this->domain         = '';
        $this->domainVerified = false;
        $this->verifyToken    = '';
        $this->verifyError    = '';
        $this->verifySuccess  = false;

        $this->dispatch('nexova-toast', type: 'success', message: 'Dominio eliminado.');
    }
}
