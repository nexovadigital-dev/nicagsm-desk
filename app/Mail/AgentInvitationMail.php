<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\AgentInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AgentInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly AgentInvitation $invitation
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Te han invitado a unirte a ' . ($this->invitation->organization->name ?? 'Nexova Desk'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.agent-invitation',
        );
    }
}
