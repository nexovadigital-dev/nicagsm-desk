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

        $org = $ticket->organization;
        if (! $org || ! OrgMailer::notificationsEnabled($org)) {
            return;
        }

        try {
            $mailerName = OrgMailer::mailerNameFor($org);
            $mailable   = new TicketReplyMail($ticket, $message);

            if ($mailerName) {
                Mail::mailer($mailerName)->to($ticket->client_email)->queue($mailable);
            } else {
                Mail::to($ticket->client_email)->queue($mailable);
            }
        } catch (\Throwable $e) {
            Log::error("TicketReplyMail failed for ticket #{$ticket->id}: {$e->getMessage()}");
        }
    }
}
