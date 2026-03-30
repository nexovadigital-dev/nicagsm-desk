<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\PaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CryptoPaymentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly PaymentTransaction $tx
    ) {}

    public function envelope(): Envelope
    {
        $org = $this->tx->organization?->name ?? 'Organización';
        return new Envelope(
            subject: "💰 Pago cripto recibido — {$org}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.crypto-payment-received',
        );
    }
}
