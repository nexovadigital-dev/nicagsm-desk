<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportTicketMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function build(): self
    {
        $org       = $this->ticket->organization;
        $fromName  = $org?->support_name  ?: ($org?->name ?: config('mail.from.name', 'Nexova Desk'));
        $fromAddr  = $org?->support_email ?: config('mail.from.address', 'noreply@nexovadesk.com');

        return $this
            ->from($fromAddr, $fromName)
            ->replyTo($fromAddr, $fromName)
            ->subject("Ticket #{$this->ticket->ticket_number} - {$this->ticket->ticket_subject}")
            ->view('emails.support-ticket');
    }
}
