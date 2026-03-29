<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket  $ticket,
        public Message $message,
    ) {}

    public function envelope(): Envelope
    {
        // Use org support_name/support_email if available, otherwise fallback to widget or config
        $org      = $this->ticket->organization;
        $fromName = $org?->support_name  ?: ($org?->name ?: (\App\Models\WidgetSetting::instance()->bot_name ?: 'Nexova Chat'));
        $fromAddr = $org?->support_email ?: null;

        return new Envelope(
            from:    $fromAddr ? new \Illuminate\Mail\Mailables\Address($fromAddr, $fromName) : null,
            subject: "Respuesta a tu consulta — {$fromName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-reply',
        );
    }
}
