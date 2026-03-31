<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
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

    public array $webhooks    = [];

    // Org-own API keys
    public string $orgGroqKey    = '';
    public string $orgGeminiKey  = '';
    public bool   $orgUseOwnKeys = false;

    // Usage limits
    public int  $maxMsgPerSession  = 30;
    public int  $maxSessionsPerDay = 100;

    public function mount(): void
    {
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

}
