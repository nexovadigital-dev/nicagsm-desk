<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Models\ApiSetting;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class AiConfigPage extends Page
{
    protected string $view = 'filament.superadmin.pages.ai-config';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Config. IA';
    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';
    protected static ?int $navigationSort = 41;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-cpu-chip';
    }

    public function getTitle(): string|Htmlable { return ''; }

    protected static array $PROVIDERS = [
        'groq'   => ['label' => 'Groq Cloud',      'desc' => 'Llama 3.3 70B — Modelo principal',  'color' => '#f97316', 'letter' => 'G'],
        'gemini' => ['label' => 'Google Gemini',   'desc' => 'Gemini 1.5 Flash — Respaldo',       'color' => '#4285f4', 'letter' => 'G'],
    ];

    public array $keys        = [];
    public array $priorities  = [];
    public array $activeFlags = [];

    public function mount(): void
    {
        foreach (array_keys(self::$PROVIDERS) as $provider) {
            $row = ApiSetting::where('provider', $provider)->first();
            $this->keys[$provider]        = '';  // never prefill for security
            $this->priorities[$provider]  = $row?->priority  ?? 1;
            $this->activeFlags[$provider] = $row ? (bool) $row->is_active : true;
        }
    }

    public function getProviders(): array
    {
        return self::$PROVIDERS;
    }

    public function hasKey(string $provider): bool
    {
        return (bool) ApiSetting::where('provider', $provider)->whereNotNull('api_key')->value('api_key');
    }

    public function save(string $provider): void
    {
        if (! isset(self::$PROVIDERS[$provider])) return;

        $key = trim($this->keys[$provider] ?? '');
        if (! $key) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Introduce una clave API');
            return;
        }

        ApiSetting::updateOrCreate(
            ['provider' => $provider],
            ['api_key' => $key, 'priority' => (int)($this->priorities[$provider] ?? 1), 'is_active' => $this->activeFlags[$provider] ?? true]
        );

        $this->keys[$provider] = '';
        $this->dispatch('nexova-toast', type: 'success', message: self::$PROVIDERS[$provider]['label'] . ' guardado');
    }

    public function toggleActive(string $provider): void
    {
        $this->activeFlags[$provider] = ! ($this->activeFlags[$provider] ?? true);
        ApiSetting::where('provider', $provider)->update(['is_active' => $this->activeFlags[$provider]]);
        $estado = $this->activeFlags[$provider] ? 'activado' : 'desactivado';
        $this->dispatch('nexova-toast', type: 'success', message: self::$PROVIDERS[$provider]['label'] . ' ' . $estado);
    }

    public function delete(string $provider): void
    {
        ApiSetting::where('provider', $provider)->delete();
        $this->keys[$provider]        = '';
        $this->priorities[$provider]  = 1;
        $this->activeFlags[$provider] = true;
        $this->dispatch('nexova-toast', type: 'success', message: 'Clave de ' . self::$PROVIDERS[$provider]['label'] . ' eliminada');
    }
}
