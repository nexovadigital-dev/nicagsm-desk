<?php

namespace App\Observers;

use App\Mail\TicketReplyMail;
use App\Models\Message;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MessageObserver
{
    public function created(Message $message): void
    {
        // Only notify when bot or agent replies
        if (!in_array($message->sender_type, ['bot', 'agent'])) {
            return;
        }

        $smtp = SmtpSetting::instance();
        if (!$smtp->notifications_enabled) {
            return;
        }

        $ticket = $message->ticket;
        if (!$ticket || !$ticket->client_email) {
            return;
        }

        try {
            $smtp->applyToConfig();
            Mail::to($ticket->client_email)->queue(new TicketReplyMail($ticket, $message));
        } catch (\Exception $e) {
            Log::error("TicketReplyMail failed: {$e->getMessage()}");
        }
    }
}
