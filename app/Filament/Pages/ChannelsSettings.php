<?php
declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Http;

class ChannelsSettings extends Page
{
    protected string $view = 'filament.pages.channels-settings';

    protected Width|string|null $maxContentWidth = 'full';

    protected static ?string $navigationLabel = 'Canales';
    protected static string|\UnitEnum|null $navigationGroup = 'Integraciones';
    protected static ?int $navigationSort = 10;

    // ── Telegram ──────────────────────────────────────────────────────────
    public string $telegramToken   = '';
    public string $telegramStatus  = '';   // 'ok' | 'error' | ''
    public string $telegramBotInfo = '';

    // ── Inline feedback message ───────────────────────────────────────────
    public string $msg     = '';
    public string $msgType = 'success';  // 'success' | 'error'

    // ──────────────────────────────────────────────────────────────────────

    public static function getNavigationIcon(): string|\BackedEnum|Htmlable|null
    {
        return 'heroicon-o-signal';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Canales';
    }

    public function mount(): void
    {
        $this->telegramToken = env('TELEGRAM_BOT_TOKEN', '');

        if ($this->telegramToken) {
            $this->telegramStatus = 'ok';
        }
    }

    public function saveTelegramToken(): void
    {
        $this->writeEnvValue('TELEGRAM_BOT_TOKEN', $this->telegramToken);

        $this->dispatch('nexova-toast', type: 'success', message: 'Token de Telegram guardado');
        $this->msg     = 'Token guardado correctamente.';
        $this->msgType = 'success';
    }

    public function testTelegram(): void
    {
        if (empty($this->telegramToken)) {
            $this->telegramStatus = 'error';
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
        if (empty($this->telegramToken)) {
            $this->msg     = 'Primero guarda o ingresa el token.';
            $this->msgType = 'error';
            return;
        }

        $webhookUrl = url('/api/webhook/telegram');

        try {
            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$this->telegramToken}/setWebhook", [
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

    // ── Helpers ───────────────────────────────────────────────────────────

    private function writeEnvValue(string $key, string $value): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            return;
        }

        $contents = file_get_contents($envPath);

        $escapedKey = preg_quote($key, '/');
        if (preg_match("/^{$escapedKey}=/m", $contents)) {
            $contents = preg_replace(
                "/^{$escapedKey}=.*/m",
                "{$key}={$value}",
                $contents
            );
        } else {
            $contents .= PHP_EOL . "{$key}={$value}";
        }

        file_put_contents($envPath, $contents);
    }
}
