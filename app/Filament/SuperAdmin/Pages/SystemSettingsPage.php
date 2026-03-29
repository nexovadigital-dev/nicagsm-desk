<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\SystemSetting;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class SystemSettingsPage extends Page
{
    protected string $view = 'filament.superadmin.pages.system-settings';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Sistema';
    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 50;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public function getTitle(): string|Htmlable { return ''; }

    public bool   $allowRegistrations          = true;
    public string $registrationClosedMessage   = '';

    public function mount(): void
    {
        $s = SystemSetting::instance();
        $this->allowRegistrations        = $s->allow_registrations;
        $this->registrationClosedMessage = $s->registration_closed_message;
    }

    public function save(): void
    {
        SystemSetting::instance()->update([
            'allow_registrations'         => $this->allowRegistrations,
            'registration_closed_message' => trim($this->registrationClosedMessage) ?: 'No estamos admitiendo registros nuevos en este momento.',
        ]);
        $this->dispatch('nexova-toast', type: 'success', message: 'Configuración guardada');
    }
}
