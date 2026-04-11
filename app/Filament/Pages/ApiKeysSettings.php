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

    protected static ?string $navigationLabel = 'Acceso por API';
    protected static string|\UnitEnum|null $navigationGroup = 'Desarrolladores';
    protected static ?int    $navigationSort  = 10;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-key';
    }

    public function getTitle(): string|Htmlable { return ''; }

    // Org-own API keys (input fields — always empty on load for security)
    public string $orgGroqKey    = '';
    public string $orgGroqKey2   = '';
    public string $orgGroqKey3   = '';
    public string $orgGeminiKey  = '';

    // Whether each key is currently stored (for UI indicators)
    public bool $groqKey1Set  = false;
    public bool $groqKey2Set  = false;
    public bool $groqKey3Set  = false;
    public bool $geminiKeySet = false;

    // Usage limits
    public int $maxMsgPerSession  = 30;
    public int $maxSessionsPerDay = 100;

    public function mount(): void
    {
        if ($this->isOrgAdmin() && $orgId = $this->orgId()) {
            $org = Organization::find($orgId);
            if ($org) {
                $this->maxMsgPerSession  = $org->max_messages_per_session ?: 30;
                $this->maxSessionsPerDay = $org->max_bot_sessions_per_day ?: 100;
                // Show whether each key is stored (without revealing the value)
                $this->groqKey1Set  = ! empty($org->getRawOriginal('ai_groq_key'));
                $this->groqKey2Set  = ! empty($org->getRawOriginal('ai_groq_key_2'));
                $this->groqKey3Set  = ! empty($org->getRawOriginal('ai_groq_key_3'));
                $this->geminiKeySet = ! empty($org->getRawOriginal('ai_gemini_key'));
            }
        }
    }

    public function saveOrgKeys(): void
    {
        if (! $this->isOrgAdmin() || ! $orgId = $this->orgId()) return;

        $data = ['ai_use_own_keys' => true];

        if (trim($this->orgGroqKey))   $data['ai_groq_key']   = encrypt(trim($this->orgGroqKey));
        if (trim($this->orgGroqKey2))  $data['ai_groq_key_2'] = encrypt(trim($this->orgGroqKey2));
        if (trim($this->orgGroqKey3))  $data['ai_groq_key_3'] = encrypt(trim($this->orgGroqKey3));
        if (trim($this->orgGeminiKey)) $data['ai_gemini_key'] = encrypt(trim($this->orgGeminiKey));

        Organization::where('id', $orgId)->update($data);

        // Refresh indicators
        $org = Organization::find($orgId);
        $this->groqKey1Set  = ! empty($org?->getRawOriginal('ai_groq_key'));
        $this->groqKey2Set  = ! empty($org?->getRawOriginal('ai_groq_key_2'));
        $this->groqKey3Set  = ! empty($org?->getRawOriginal('ai_groq_key_3'));
        $this->geminiKeySet = ! empty($org?->getRawOriginal('ai_gemini_key'));

        $this->orgGroqKey   = '';
        $this->orgGroqKey2  = '';
        $this->orgGroqKey3  = '';
        $this->orgGeminiKey = '';

        $this->dispatch('nexova-toast', type: 'success', message: 'Claves API guardadas correctamente');
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
