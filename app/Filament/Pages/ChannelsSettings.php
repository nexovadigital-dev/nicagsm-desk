<?php
declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Concerns\ScopedToOrganization;
use App\Models\Organization;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Http;

class ChannelsSettings extends Page
{
    use ScopedToOrganization;

    protected string $view = 'filament.pages.channels-settings';
    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Canales';
    protected static string|\UnitEnum|null $navigationGroup = 'Canales';
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-signal';
    }

    public function getTitle(): string|Htmlable { return 'Canales'; }

    // ── Telegram ──────────────────────────────────────────────────────────
    public string $telegramToken   = '';
    public string $telegramStatus  = '';   // 'ok' | 'error' | ''
    public string $telegramBotInfo = '';

    // ── Telegram Configs ──────────────────────────────────────────────────
    public bool $telegramAiEnabled = false;
    public string $telegramBotPrompt = '';
    public array $telegramFaqItems = [];
    public bool $telegramUseStoreContext = false;

    // ── Inline feedback ───────────────────────────────────────────────────
    public string $msg     = '';
    public string $msgType = 'success';

    public function mount(): void
    {
        $orgId = $this->orgId();
        if ($orgId) {
            $org = Organization::find($orgId);
            if ($org && $org->telegram_bot_token) {
                // Don't prefill token for security — just show connected status
                $this->telegramStatus = 'ok';
                $this->telegramBotInfo = 'Bot configurado';
            }
            if ($org && $org->telegram_config) {
                $this->telegramAiEnabled = $org->telegram_config['ai_enabled'] ?? false;
                $this->telegramBotPrompt = $org->telegram_config['bot_prompt'] ?? '';
                $this->telegramFaqItems  = $org->telegram_config['faq_items'] ?? [];
                $this->telegramUseStoreContext = $org->telegram_config['use_store_context'] ?? false;
            }
            
            // Ensure at least one empty FAQ item for UI if empty
            if (empty($this->telegramFaqItems)) {
                $this->telegramFaqItems = [['question' => '', 'answer' => '']];
            }
        }
    }

    public function saveTelegramConfig(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) return;

        $cleanFaqs = array_values(array_filter($this->telegramFaqItems, function ($item) {
            return !empty(trim($item['question'] ?? '')) && !empty(trim($item['answer'] ?? ''));
        }));

        $config = [
            'ai_enabled'        => $this->telegramAiEnabled,
            'bot_prompt'        => trim($this->telegramBotPrompt),
            'faq_items'         => $cleanFaqs,
            'use_store_context' => $this->telegramUseStoreContext,
        ];

        Organization::where('id', $orgId)->update([
            'telegram_config' => $config,
        ]);

        if (empty($cleanFaqs)) {
            $this->telegramFaqItems = [['question' => '', 'answer' => '']];
        } else {
            $this->telegramFaqItems = $cleanFaqs;
        }

        $this->dispatch('nexova-toast', type: 'success', message: 'Configuraciones de Telegram guardadas correctamente');
        $this->msg     = 'Configuración avanzada de Telegram guardada.';
        $this->msgType = 'success';
    }

    public function addTelegramFaq(): void
    {
        $this->telegramFaqItems[] = ['question' => '', 'answer' => ''];
    }

    public function removeTelegramFaq(int $index): void
    {
        unset($this->telegramFaqItems[$index]);
        $this->telegramFaqItems = array_values($this->telegramFaqItems);
    }

    public function saveTelegramToken(): void
    {
        $orgId = $this->orgId();
        if (! $orgId || ! trim($this->telegramToken)) {
            $this->msg     = 'Ingresa el token antes de guardar.';
            $this->msgType = 'error';
            return;
        }

        Organization::where('id', $orgId)->update([
            'telegram_bot_token' => encrypt(trim($this->telegramToken)),
        ]);

        $this->telegramStatus = 'ok';
        $this->telegramBotInfo = 'Bot configurado';
        $this->telegramToken = '';
        $this->dispatch('nexova-toast', type: 'success', message: 'Token de Telegram guardado');
        $this->msg     = 'Token guardado correctamente.';
        $this->msgType = 'success';
    }

    public function testTelegram(): void
    {
        if (empty($this->telegramToken)) {
            $this->msg     = 'Ingresa el token antes de probar.';
            $this->msgType = 'error';
            return;
        }

        try {
            $response = Http::timeout(8)
                ->get("https://api.telegram.org/bot{$this->telegramToken}/getMe");

            if ($response->successful() && $response->json('ok')) {
                $bot = $response->json('result');
                $this->telegramStatus  = 'ok';
                $this->telegramBotInfo = '@' . ($bot['username'] ?? 'bot') . ' — conectado';
                $this->msg     = "Bot conectado: @{$bot['username']}";
                $this->msgType = 'success';
                $this->dispatch('nexova-toast', type: 'success', message: 'Bot de Telegram conectado correctamente');
            } else {
                $this->telegramStatus = 'error';
                $this->msg     = 'Token inválido o bot no encontrado.';
                $this->msgType = 'error';
            }
        } catch (\Throwable $e) {
            $this->telegramStatus = 'error';
            $this->msg     = 'Error de conexión: ' . $e->getMessage();
            $this->msgType = 'error';
        }
    }

    public function registerWebhook(): void
    {
        $token = trim($this->telegramToken);

        // If no token in field, try to use saved org token
        if (! $token) {
            $orgId = $this->orgId();
            if ($orgId) {
                $org = Organization::find($orgId);
                if ($org && $org->telegram_bot_token) {
                    try {
                        $token = decrypt($org->telegram_bot_token);
                    } catch (\Throwable) {
                        $token = '';
                    }
                }
            }
        }

        if (! $token) {
            $this->msg     = 'Primero guarda o ingresa el token.';
            $this->msgType = 'error';
            return;
        }

        $orgId      = $this->orgId();
        $webhookUrl = url("/api/webhook/telegram/{$orgId}");

        try {
            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$token}/setWebhook", [
                    'url' => $webhookUrl,
                ]);

            if ($response->successful() && $response->json('ok')) {
                $this->msg     = 'Webhook registrado: ' . $webhookUrl;
                $this->msgType = 'success';
                $this->dispatch('nexova-toast', type: 'success', message: 'Webhook de Telegram registrado');
            } else {
                $description   = $response->json('description') ?? 'Error desconocido';
                $this->msg     = 'No se pudo registrar el webhook: ' . $description;
                $this->msgType = 'error';
            }
        } catch (\Throwable $e) {
            $this->msg     = 'Error al registrar webhook: ' . $e->getMessage();
            $this->msgType = 'error';
        }
    }

    public function disconnectTelegram(): void
    {
        $orgId = $this->orgId();
        if (! $orgId) return;

        Organization::where('id', $orgId)->update(['telegram_bot_token' => null]);
        $this->telegramStatus  = '';
        $this->telegramBotInfo = '';
        $this->telegramToken   = '';
        $this->dispatch('nexova-toast', type: 'success', message: 'Bot de Telegram desconectado');
        $this->msg     = 'Bot desconectado.';
        $this->msgType = 'success';
    }

    public function getWebhookUrl(): string
    {
        return url('/api/webhook/telegram/' . ($this->orgId() ?? '?'));
    }
}
