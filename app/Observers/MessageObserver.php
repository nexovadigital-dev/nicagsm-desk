<?php

namespace App\Observers;

use App\Mail\TicketReplyMail;
use App\Models\Message;
use App\Services\OrgMailer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MessageObserver
{
    public function created(Message $message): void
    {
        // Only notify when bot or agent replies
        if (! in_array($message->sender_type, ['bot', 'agent'])) {
            return;
        }

        $ticket = $message->ticket;
        if (! $ticket || ! $ticket->client_email) {
            return;
        }

        // ── LIVE CHAT: NO enviar emails por cada mensaje ──────────────────────
        // Los chats en vivo del widget (platform='web') son conversaciones en tiempo real.
        // Solo se envía email cuando hay un ticket formal (ticket_number asignado)
        // que implica que el cliente espera respuesta por email (canal IMAP/email).
        if ($ticket->platform === 'web' && ! $ticket->ticket_number) {
            return;
        }

        $org = $ticket->organization;
        if (! $org || ! OrgMailer::notificationsEnabled($org)) {
            return;
        }

        try {
            $mailerName = OrgMailer::mailerNameFor($org);
            $mailable   = new TicketReplyMail($ticket, $message);

            // Use ->send() (synchronous) â€” no queue worker required on shared hosting
            if ($mailerName) {
                Mail::mailer($mailerName)->to($ticket->client_email)->send($mailable);
            } else {
                Mail::to($ticket->client_email)->send($mailable);
            }
        } catch (\Throwable $e) {
            Log::error("TicketReplyMail failed for ticket #{$ticket->id}: {$e->getMessage()}");
        }
    }
}
