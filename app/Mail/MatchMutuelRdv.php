<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;
use App\Models\RendezVous;

class MatchMutuelRdv extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $destinataire,
        public Participant $partenaire,
        public RendezVous  $rendezVous,
        public string      $nomEvenement,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau rendez-vous confirmé — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.match-mutuel-rdv',
        );
    }
}