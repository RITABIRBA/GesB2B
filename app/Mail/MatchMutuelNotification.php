<?php

namespace App\Mail;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MatchMutuelNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Participant $destinataire,
        public Participant $partenaire,
        public string      $nomEvenement,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Match mutuel ! — ' . $this->nomEvenement,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.match-mutuel',
            //  Passer explicitement les variables à la vue
            with: [
                'destinataire' => $this->destinataire,
                'partenaire'   => $this->partenaire->load('entreprise'),
                'nomEvenement' => $this->nomEvenement,
            ],
        );
    }
}