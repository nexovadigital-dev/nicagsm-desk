<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Http\Controllers\Api\TelegramWebhookController;
use App\Models\Message;
use App\Models\Ticket;
use App\Services\NexovaAiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBotReply implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Sin reintentos: NexovaAiService ya gestiona el fallback entre proveedores
     * internamente. Si todos fallan, el método failed() guarda un mensaje de error.
     */
    public int $tries   = 1;
    public int $timeout = 90; // segundos máximos para que la IA responda

    public function __construct(public readonly Ticket $ticket) {}

    public function handle(NexovaAiService $aiService): void
    {
        // Re-cargamos el ticket desde la DB para evitar condiciones de carrera:
        // si un agente tomó el ticket mientras el job esperaba en la cola, no respondemos.
        $ticket = $this->ticket->fresh();

        if ($ticket === null || $ticket->status !== 'bot') {
            Log::info("[NexovaBot] Job omitido: ticket #{$this->ticket->id} ya no está en modo bot.");
            return;
        }

        $rawReply = $aiService->generateReply($ticket);

        // Detect WOO_VERIFY flag (identity verification needed)
        $needsWooVerify = str_contains($rawReply, \App\Services\NexovaAiService::WOO_VERIFY_FLAG);
        if ($needsWooVerify) {
            $rawReply = str_replace(\App\Services\NexovaAiService::WOO_VERIFY_FLAG, '', $rawReply);
            $rawReply = rtrim($rawReply);
        }

        // Detect escalation flag and strip it from the visible reply
        $needsEscalation = str_ends_with($rawReply, \App\Services\NexovaAiService::ESCALATE_FLAG);
        $reply = $needsEscalation
            ? rtrim(substr($rawReply, 0, -strlen(\App\Services\NexovaAiService::ESCALATE_FLAG)))
            : $rawReply;

        Message::create([
            'ticket_id'   => $ticket->id,
            'sender_type' => 'bot',
            'content'     => $reply,
        ]);

        // WooCommerce identity verification CTA
        if ($needsWooVerify) {
            Message::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'system',
                'content'     => '__WOO_IDENTITY__',
            ]);
        }

        // Insert escalation offer as a special system message the widget can detect
        if ($needsEscalation) {
            Message::create([
                'ticket_id'   => $ticket->id,
                'sender_type' => 'system',
                'content'     => '__AGENT_CTA__',
            ]);
        }

        // Si el ticket es de Telegram, enviar la respuesta al usuario
        if ($ticket->platform === 'telegram' && $ticket->telegram_id) {
            TelegramWebhookController::sendTelegramMessage($ticket->telegram_id, $reply, $ticket->organization_id);
            if ($needsEscalation) {
                TelegramWebhookController::sendTelegramMessage($ticket->telegram_id, '¿Te gustaría hablar con un agente humano? Responde "agente" o "si" para conectarte.', $ticket->organization_id);
            }
        }
    }

    /**
     * Solo se ejecuta si el job lanza una excepción no capturada
     * (error de infraestructura, no de la IA).
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("[NexovaBot] Job falló inesperadamente para ticket #{$this->ticket->id}: {$exception->getMessage()}");

        Message::create([
            'ticket_id'   => $this->ticket->id,
            'sender_type' => 'bot',
            'content'     => 'Lo siento, ocurrió un error inesperado. Un agente revisará tu consulta.',
        ]);
    }
}
