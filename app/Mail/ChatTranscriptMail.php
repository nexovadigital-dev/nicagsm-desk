<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Services\OrgMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ChatTranscriptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket     $ticket,
        public Collection $messages,
    ) {}

    public function envelope(): Envelope
    {
        [$fromAddr, $fromName] = OrgMailer::fromFor($this->ticket->organization);

        return new Envelope(
            from:    new Address($fromAddr, $fromName),
            replyTo: [new Address($fromAddr, $fromName)],
            subject: "Transcripción de tu conversación — {$fromName}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.chat-transcript');
    }
}