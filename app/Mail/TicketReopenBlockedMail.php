<?php

namespace App\Mail;

use App\Models\Organization;
use App\Models\Ticket;
use App\Services\OrgMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Se envía cuando un cliente intenta responder a un ticket ya cerrado vía IMAP.
 * Informa que el caso está cerrado e instruye a crear un ticket nuevo.
 */
class TicketReopenBlockedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Organization $org;

    public function __construct(public Ticket $ticket)
    {
        $this->org = $ticket->organization ?? new Organization();
    }

    public function build(): self
    {
        [$fromAddr, $fromName] = OrgMailer::fromFor($this->org);

        return $this
            ->from($fromAddr, $fromName)
            ->replyTo($fromAddr, $fromName)
            ->subject("Re: Ticket #{$this->ticket->ticket_number} — Este caso ya fue cerrado")
            ->view('emails.ticket-reopen-blocked')
            ->with(['ticket' => $this->ticket, 'org' => $this->org]);
    }
}
