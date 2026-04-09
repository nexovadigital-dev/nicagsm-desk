<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\Organization;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Http;

class SubscriptionPage extends Page
{
    use ScopedToOrganization;

    protected string $view = 'filament.pages.subscription';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Licencia';
    protected static ?int    $navigationSort  = 90;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-shield-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Cuenta';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // ── State ─────────────────────────────────────────────────────────────────
    public ?string $licenseStatus      = null;
    public ?string $licenseCheckedAt   = null;
    public string  $installedDomain    = '';
    public string  $platformUrl        = 'nexovadesk.com';
    public bool    $licenseValid       = false;

    public function mount(): void
    {
        $this->installedDomain = parse_url(config('app.url'), PHP_URL_HOST) ?? config('app.url');
        $this->checkLicense();
    }

    public function checkLicense(): void
    {
        try {
            $response = Http::timeout(6)->get('https://nexovadesk.com/api/partner/verify', [
                'domain' => $this->installedDomain,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->licenseValid      = (bool) ($data['active'] ?? false);
                $this->licenseStatus     = $this->licenseValid ? 'active' : 'inactive';
            } else {
                $this->licenseStatus = 'error';
                $this->licenseValid  = false;
            }
        } catch (\Throwable) {
            $this->licenseStatus = 'unreachable';
            $this->licenseValid  = false;
        }

        $this->licenseCheckedAt = now()->format('d/m/Y H:i');
    }
}
