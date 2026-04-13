<?php

namespace App\Mail;

use App\Models\Organization;
use App\Models\Ticket;
use App\Services\OrgMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketClosedMail extends Mailable
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
            ->subject("Ticket #{$this->ticket->ticket_number} resuelto — cuéntanos cómo te fue")
            ->view('emails.ticket-closed')
            ->with(['ticket' => $this->ticket, 'org' => $this->org]);
    }
}
