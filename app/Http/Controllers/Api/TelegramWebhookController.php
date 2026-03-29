<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBotReply;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Recibe eventos de Telegram (mensajes, actualizaciones).
     * POST /api/webhook/telegram
     */
    public function handle(Request $request): JsonResponse
    {
        $update = $request->all();

        // Solo procesamos mensajes de texto
        $message = $update['message'] ?? $update['edited_message'] ?? null;
        if (! $message || ! isset($message['text'])) {
            return response()->json(['ok' => true]);
        }

        $chatId   = $message['chat']['id'];
        $text     = trim($message['text']);
        $fromUser = $message['from'];
        $name     = trim(($fromUser['first_name'] ?? '') . ' ' . ($fromUser['last_name'] ?? '')) ?: 'Usuario Telegram';

        // Ignorar comandos del bot (ej: /start)
        if (str_starts_with($text, '/start')) {
            $this->sendTelegramMessage($chatId, "Hola {$name}, soy el asistente de Nexova. ¿En qué puedo ayudarte?");
            return response()->json(['ok' => true]);
        }

        // Buscar o crear ticket para este chat de Telegram
        $ticket = Ticket::where('telegram_id', (string) $chatId)
            ->whereIn('status', ['bot', 'human'])
            ->first();

        if (! $ticket) {
            $ticket = Ticket::create([
                'telegram_id'       => (string) $chatId,
                'platform'          => 'telegram',
                'status'            => 'bot',
                'client_name'       => $name,
                'conversation_name' => $name . ' (Telegram)',
            ]);
        }

        // Guardar mensaje del usuario
        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'user',
            'content'     => $text,
        ]);

        // Despachar bot si el ticket está en modo bot
        if ($ticket->status === 'bot') {
            ProcessBotReply::dispatch($ticket);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Envía un mensaje a Telegram (usado por el bot al responder).
     * Se llama desde ProcessBotReply cuando platform='telegram'.
     */
    public static function sendTelegramMessage(string|int $chatId, string $text): void
    {
        $token = config('services.telegram.token');
        if (! $token) {
            Log::warning('Telegram: TELEGRAM_BOT_TOKEN no configurado.');
            return;
        }

        try {
            Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Exception $e) {
            Log::error("Telegram sendMessage error: {$e->getMessage()}");
        }
    }

    /**
     * Registra el webhook de Telegram (ejecutar una vez al configurar).
     * GET /api/webhook/telegram/register
     */
    public function register(Request $request): JsonResponse
    {
        $token      = config('services.telegram.token');
        $webhookUrl = url('/api/webhook/telegram');

        if (! $token) {
            return response()->json(['error' => 'TELEGRAM_BOT_TOKEN no configurado'], 500);
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/setWebhook", [
            'url' => $webhookUrl,
        ]);

        return response()->json($response->json());
    }
}
