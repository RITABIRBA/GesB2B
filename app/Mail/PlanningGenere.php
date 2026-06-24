<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;
use Illuminate\Support\Collection;

class PlanningGenere extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $participant,
        public string      $nomEvenement,
        public string      $dateEvenement,
        public Collection  $rendezVous,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre planning est disponible — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.planning-genere',
        );
    }
}