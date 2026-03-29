<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\ApiSetting;
use App\Models\Organization;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;

class ApiKeysSettings extends Page
{
    use ScopedToOrganization;
    protected string $view = 'filament.pages.api-keys-settings';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Llaves API';
    protected static string|\UnitEnum|null $navigationGroup = 'Integraciones';
    protected static ?int    $navigationSort  = 30;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-key';
    }

    public function getTitle(): string|Htmlable { return ''; }

    public array $providers = [
        'groq'   => ['label' => 'Groq Cloud', 'desc' => 'Llama 3.3 70B · Modelo principal de IA', 'color' => '#f97316', 'letter' => 'G'],
        'gemini' => ['label' => 'Google Gemini', 'desc' => 'Gemini 1.5 Flash · Modelo de respaldo', 'color' => '#4285f4', 'letter' => 'G'],
    ];

    public array $keys        = [];
    public array $priorities  = [];
    public array $activeFlags = [];

    // Org-own API keys
    public string $orgGroqKey    = '';
    public string $orgGeminiKey  = '';
    public bool   $orgUseOwnKeys = false;

    // Usage limits
    public int  $maxMsgPerSession  = 30;
    public int  $maxSessionsPerDay = 100;

    public function mount(): void
    {
        foreach (array_keys($this->providers) as $provider) {
            $row = ApiSetting::where('provider', $provider)->first();
            $this->keys[$provider]        = $row ? $row->api_key : '';
            $this->priorities[$provider]  = $row ? $row->priority : 1;
            $this->activeFlags[$provider] = $row ? (bool) $row->is_active : true;
        }

        // Load org settings (owner/admin only)
        if ($this->isOrgAdmin() && $orgId = $this->orgId()) {
            $org = Organization::find($orgId);
            if ($org) {
                $this->orgUseOwnKeys    = (bool) $org->ai_use_own_keys;
                $this->maxMsgPerSession = $org->max_messages_per_session ?: 30;
                $this->maxSessionsPerDay= $org->max_bot_sessions_per_day ?: 100;
                // Don't pre-fill encrypted keys — security
            }
        }
    }

    public function saveOrgKeys(): void
    {
        if (! $this->isOrgAdmin() || ! $orgId = $this->orgId()) return;

        $data = ['ai_use_own_keys' => $this->orgUseOwnKeys];

        if (trim($this->orgGroqKey)) {
            $data['ai_groq_key'] = encrypt(trim($this->orgGroqKey));
        }
        if (trim($this->orgGeminiKey)) {
            $data['ai_gemini_key'] = encrypt(trim($this->orgGeminiKey));
        }

        Organization::where('id', $orgId)->update($data);
        $this->orgGroqKey   = '';
        $this->orgGeminiKey = '';
        $this->dispatch('nexova-toast', type: 'success', message: 'Configuración de IA guardada');
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

    public function save(string $provider): void
    {
        $key = trim($this->keys[$provider] ?? '');
        if (! $key) {
            $this->dispatch('nexova-toast', type: 'error', message: 'Introduce una clave API para guardar');
            return;
        }
        ApiSetting::updateOrCreate(
            ['provider' => $provider],
            ['api_key' => $key, 'priority' => (int)($this->priorities[$provider] ?? 1), 'is_active' => $this->activeFlags[$provider] ?? true]
        );
        $this->dispatch('nexova-toast', type: 'success', message: "Configuración de {$this->providers[$provider]['label']} guardada");
    }

    public function toggleActive(string $provider): void
    {
        $this->activeFlags[$provider] = !($this->activeFlags[$provider] ?? true);
        ApiSetting::where('provider', $provider)->update(['is_active' => $this->activeFlags[$provider]]);
        $estado = $this->activeFlags[$provider] ? 'activada' : 'desactivada';
        $this->dispatch('nexova-toast', type: 'success', message: "{$this->providers[$provider]['label']} {$estado}");
    }

    public function delete(string $provider): void
    {
        ApiSetting::where('provider', $provider)->delete();
        $this->keys[$provider] = ''; $this->priorities[$provider] = 1; $this->activeFlags[$provider] = true;
        $this->dispatch('nexova-toast', type: 'success', message: "Clave de {$this->providers[$provider]['label']} eliminada");
    }
}
