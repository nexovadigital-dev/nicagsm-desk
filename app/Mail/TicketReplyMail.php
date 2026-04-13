<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\Organization;
use App\Models\Ticket;
use App\Services\OrgMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public Organization $org;

    public function __construct(
        public Ticket  $ticket,
        public Message $message,
    ) {
        $this->org = $ticket->organization ?? new Organization();
    }

    public function envelope(): Envelope
    {
        [$fromAddr, $fromName] = OrgMailer::fromFor($this->org);

        return new Envelope(
            from:    new Address($fromAddr, $fromName),
            replyTo: [new Address($fromAddr, $fromName)],
            subject: "Respuesta a tu consulta — {$fromName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-reply',
            with: ['ticket' => $this->ticket, 'message' => $this->message, 'org' => $this->org],
        );
    }
}
