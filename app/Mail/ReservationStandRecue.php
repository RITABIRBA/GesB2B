<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Stand;

class ReservationStandRecue extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Stand  $stand,
        public string $nomDestinataire,
        public string $nomEvenement,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre demande de réservation de stand — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-stand-recue',
        );
    }
}