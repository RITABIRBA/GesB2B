<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;

class PreinscriptionRecue extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $participant,
        public string      $nomEvenement,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre préinscription a bien été reçue — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.preinscription-recue',
        );
    }
}