<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;

class PreinscriptionValidee extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $participant,
        public ?string     $motDePasse,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre inscription est validée — Business Forum',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.preinscription-validee',
        );
    }
}