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
use Illuminate\Support\Facades\Storage;

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
        if (! $message) {
            return response()->json(['ok' => true]);
        }

        $chatId   = $message['chat']['id'];
        $fromUser = $message['from'] ?? [];
        $name     = trim(($fromUser['first_name'] ?? '') . ' ' . ($fromUser['last_name'] ?? '')) ?: 'Usuario Telegram';
        $keyboard = self::buildFaqKeyboard($org);

        // ── Ignorar multimedia entrante (fotos, stickers, docs, voz) ────────────
        if (isset($message['photo']) || isset($message['sticker']) || isset($message['document']) || isset($message['voice']) || isset($message['video'])) {
            self::sendMessage($org, $chatId,
                'Por ahora solo proceso mensajes de texto. Escribe tu consulta o selecciona una opcion del menu.'
            , $keyboard);
            return response()->json(['ok' => true]);
        }

        if (! isset($message['text'])) {
            return response()->json(['ok' => true]);
        }

        $text = trim($message['text']);

        if (str_starts_with($text, '/start')) {
            self::sendMessage($org, $chatId, "Hola {$name}, soy el asistente de {$org->name}. En que puedo ayudarte?", $keyboard);
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

        // Intercepción de FAQ inmediata
        $faqAnswer = null;
        $faqs = $org->telegram_config['faq_items'] ?? [];
        foreach ($faqs as $faq) {
            if (trim(mb_strtolower($faq['question'])) === trim(mb_strtolower($text))) {
                $faqAnswer = $faq['answer'];
                break;
            }
        }

        if ($faqAnswer) {
            // Guardar constancia de la respuesta del bot en la BD para que el admin la vea
            Message::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'bot',
                'content'     => $faqAnswer,
            ]);
            self::sendMessage($org, $chatId, $faqAnswer, $keyboard);
            return response()->json(['ok' => true]);
        }

        if ($ticket->status === 'bot') {
            ProcessBotReply::dispatch($ticket);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Construye un ReplyKeyboardMarkup a partir del FAQ configurado para Telegram.
     */
    private static function buildFaqKeyboard(Organization $org): ?array
    {
        $faqs = $org->telegram_config['faq_items'] ?? [];
        if (empty($faqs)) return null;

        $keyboard = [];
        $row = [];
        foreach ($faqs as $faq) {
            $row[] = ['text' => $faq['question']];
            if (count($row) === 2) { // 2 botones por fila
                $keyboard[] = $row;
                $row = [];
            }
        }
        if (!empty($row)) $keyboard[] = $row;

        return [
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'is_persistent' => false,
            'one_time_keyboard' => false,
        ];
    }

    /**
     * Envía un mensaje via Telegram usando el token de la org.
     */
    public static function sendMessage(Organization|int $org, string|int $chatId, string $text, ?array $replyMarkup = null): void
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

        $payload = [
            'chat_id'    => $chatId,
            'text'       => self::stripMarkdown($text), // Nos aseguramos de limpiar markdown
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }

        try {
            Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", $payload);
        } catch (\Exception $e) {
            Log::error("Telegram sendMessage error: {$e->getMessage()}");
        }
    }

    /**
     * Elimina el markdown de la respuesta de la IA antes de enviarlo a Telegram,
     * dado que parse_mode = HTML no tolera asteriscos o backticks crudos.
     */
    private static function stripMarkdown(string $text): string
    {
        $text = preg_replace('/\*{1,3}(.+?)\*{1,3}/u', '<b>$1</b>', $text); // Negritas a HTML
        $text = preg_replace('/_{1,3}(.+?)_{1,3}/u', '<i>$1</i>', $text); // Cursivas a HTML
        $text = preg_replace('/^#{1,6}\s+/mu', '', $text); // Quitar Headers
        return trim($text);
    }

    public static function sendTelegramMessage(string|int $chatId, string $text, ?int $orgId = null): void
    {
        if ($orgId) {
            $org = Organization::find($orgId);
        } else {
            $ticket = Ticket::where('telegram_id', (string) $chatId)
                ->whereIn('status', ['bot', 'human'])
                ->latest()
                ->first();
            $org = $ticket?->organization;
        }

        if (! $org) return;

        // Intentar agregar el teclado del FAQ incluso en respuestas misceláneas
        $keyboard = self::buildFaqKeyboard($org);
        self::sendMessage($org, $chatId, $text, $keyboard);
    }
    /**
     * Envía una imagen/foto al usuario de Telegram.
     * Se usa cuando el agente adjunta una imagen desde el panel.
     */
    public static function sendPhoto(
        Organization|int $org,
        string|int $chatId,
        string $attachmentPath,
        ?string $caption = null
    ): void {
        if (is_int($org)) {
            $org = Organization::find($org);
        }

        if (! $org || ! $org->telegram_bot_token) return;

        try {
            $token = decrypt($org->telegram_bot_token);
        } catch (\Throwable) {
            Log::error("Telegram sendPhoto: error desencriptando token org #{$org->id}");
            return;
        }

        // Construir URL publica de la imagen
        $photoUrl = url(Storage::url($attachmentPath));

        $payload = [
            'chat_id' => $chatId,
            'photo'   => $photoUrl,
        ];

        if ($caption) {
            $payload['caption'] = mb_substr($caption, 0, 1024); // limite Telegram
        }

        try {
            Http::timeout(30)->post("https://api.telegram.org/bot{$token}/sendPhoto", $payload);
        } catch (\Exception $e) {
            Log::error("Telegram sendPhoto error: {$e->getMessage()}");
        }
    }
}
