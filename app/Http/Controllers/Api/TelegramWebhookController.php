<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBotReply;
use App\Models\Message;
use App\Models\Organization;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    /**
     * Recibe eventos de Telegram para una organización específica.
     * POST /api/webhook/telegram/{orgId}
     */
    public function handle(Request $request, int $orgId): JsonResponse
    {
        $org = Organization::find($orgId);
        if (! $org || ! $org->telegram_bot_token) {
            return response()->json(['ok' => false, 'error' => 'org not found or telegram not configured'], 404);
        }

        $update  = $request->all();
        $message = $update['message'] ?? $update['edited_message'] ?? null;
        if (! $message || ! isset($message['text'])) {
            return response()->json(['ok' => true]);
        }

        $chatId   = $message['chat']['id'];
        $text     = trim($message['text']);
        $fromUser = $message['from'];
        $name     = trim(($fromUser['first_name'] ?? '') . ' ' . ($fromUser['last_name'] ?? '')) ?: 'Usuario Telegram';

        if (str_starts_with($text, '/start')) {
            self::sendMessage($org, $chatId, "Hola {$name}, soy el asistente de {$org->name}. ¿En qué puedo ayudarte?");
            return response()->json(['ok' => true]);
        }

        $ticket = Ticket::where('organization_id', $orgId)
            ->where('telegram_id', (string) $chatId)
            ->whereIn('status', ['bot', 'human'])
            ->first();

        if (! $ticket) {
            $ticket = Ticket::create([
                'organization_id'   => $orgId,
                'telegram_id'       => (string) $chatId,
                'platform'          => 'telegram',
                'status'            => 'bot',
                'client_name'       => $name,
                'conversation_name' => $name . ' (Telegram)',
            ]);
        }

        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'user',
            'content'     => $text,
        ]);

        if ($ticket->status === 'bot') {
            ProcessBotReply::dispatch($ticket);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Envía un mensaje via Telegram usando el token de la org.
     * Llamado desde ProcessBotReply cuando platform='telegram'.
     */
    public static function sendMessage(Organization|int $org, string|int $chatId, string $text): void
    {
        if (is_int($org)) {
            $org = Organization::find($org);
        }

        if (! $org || ! $org->telegram_bot_token) {
            Log::warning("Telegram sendMessage: org #{$org?->id} sin token configurado.");
            return;
        }

        try {
            $token = decrypt($org->telegram_bot_token);
        } catch (\Throwable) {
            Log::error("Telegram sendMessage: error desencriptando token org #{$org->id}");
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
     * Compatibilidad: llamada estática con solo chatId y texto (para código legacy).
     * Busca la org del ticket para obtener el token correcto.
     */
    public static function sendTelegramMessage(string|int $chatId, string $text, ?int $orgId = null): void
    {
        if ($orgId) {
            $org = Organization::find($orgId);
        } else {
            // Fallback: buscar ticket activo con este chatId
            $ticket = Ticket::where('telegram_id', (string) $chatId)
                ->whereIn('status', ['bot', 'human'])
                ->latest()
                ->first();
            $org = $ticket?->organization;
        }

        if (! $org) {
            Log::warning("Telegram sendTelegramMessage: no se encontró org para chatId {$chatId}");
            return;
        }

        self::sendMessage($org, $chatId, $text);
    }
}
