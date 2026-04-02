<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Services\OrgMailer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function build(): self
    {
        [$fromAddr, $fromName] = OrgMailer::fromFor($this->ticket->organization);

        return $this
            ->from($fromAddr, $fromName)
            ->replyTo($fromAddr, $fromName)
            ->subject("Ticket #{$this->ticket->ticket_number} - {$this->ticket->ticket_subject}")
            ->view('emails.support-ticket');
    }
}
