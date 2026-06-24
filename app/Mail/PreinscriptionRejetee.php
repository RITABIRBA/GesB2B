<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;

class PreinscriptionRejetee extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $participant,
        public string      $nomEvenement,
        public ?string     $motif = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Concernant votre préinscription — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.preinscription-rejetee',
        );
    }
}